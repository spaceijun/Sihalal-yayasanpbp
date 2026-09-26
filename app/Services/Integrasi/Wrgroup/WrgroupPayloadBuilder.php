<?php

namespace App\Services\Integrasi\Wrgroup;

use App\Models\DataLapangan;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Pemetaan data Kawulo Halal → format event WRGROUP. Murni (tanpa efek samping).
 *
 * Aturan format WRGROUP: IDR, Asia/Jakarta, angka bulat tanpa pemisah ribuan,
 * tanggal YYYY-MM-DD, waktu ISO 8601 dengan offset. transaction_id berbasis
 * `no_registrasi` (mis. KH2026-00001) — sudah unik permanen per kasus sertifikasi
 * sejak dibuat (lihat DataLapangan::generateNoRegistrasi()), tidak pernah dipakai
 * ulang, jadi aman dipakai langsung (beda dari reference swaraningcode-be yang
 * memakai primary key karena nomor dokumennya BISA dipakai ulang setelah hapus).
 *
 * Kawulo Halal tidak mengenal invoice terpisah dari payment — satu `DataLapangan`
 * berpindah status_pembayaran langsung ke DIBAYAR dalam satu langkah (mirip `Sale`
 * di reference, bukan `Invoice` bertahap). Karena itu hanya ada SATU pasangan method
 * doc (invoiceDoc + paymentDoc) yang dipanggil bersama dari
 * WrgroupEventService::recordDataLapanganPaid() — meniru pola recordSalePaid().
 * Tidak ada refundDoc(): DataLapangan tidak memiliki alur pembatalan pembayaran.
 *
 * @phpstan-type Doc array{transaction_id:string, period:string, cancelled:bool, data:array<string, mixed>}
 */
class WrgroupPayloadBuilder
{
    // --- ID dokumen ----------------------------------------------------------

    public function transactionId(DataLapangan $dataLapangan): string
    {
        return (string) $dataLapangan->no_registrasi;
    }

    public function paymentTransactionId(DataLapangan $dataLapangan): string
    {
        return 'PAY-'.$this->transactionId($dataLapangan);
    }

    // --- Dokumen -------------------------------------------------------------

    /**
     * Kasus sertifikasi halal yang lunas — dilaporkan sebagai invoice. Null bila belum
     * (atau tidak lagi) DIBAYAR, atau fee-nya 0 (tidak ada dokumen tagihan untuk itu).
     */
    public function invoiceDoc(DataLapangan $dataLapangan): ?array
    {
        $fee = $this->fee($dataLapangan);
        if ($fee === null) {
            return null;
        }

        $date = $this->paidAt($dataLapangan)->toDateString();
        $reference = $dataLapangan->nama_usaha ?: ($dataLapangan->nama_produk ?: $dataLapangan->nama_pu);

        return [
            'transaction_id' => $this->transactionId($dataLapangan),
            'period' => $this->period($this->paidAt($dataLapangan)),
            'cancelled' => false,
            'data' => $this->withoutNulls([
                'nomor_invoice' => $dataLapangan->no_registrasi,
                'tanggal_invoice' => $date,
                'tanggal_transaksi' => $date,
                'nilai_dasar' => $fee,
                // Fee sertifikasi Kawulo Halal tidak mengenal diskon/pajak/penyesuaian
                // terpisah (satu angka flat per fee-schedule) — selalu 0, sama seperti
                // saleDoc() di reference untuk alasan yang sama (bukan invoiceDoc()
                // yang punya kolom tax_amount sungguhan).
                'diskon' => 0,
                'pajak' => 0,
                'penyesuaian' => 0,
                'mata_uang' => config('wrgroup.currency'),
                'status' => 'terbit',
                'referensi_dokumen' => $reference ? Str::limit((string) $reference, 255, '') : null,
            ]),
        ];
    }

    /** Pembayaran fee sertifikasi (lunas) — dipasangkan dengan invoiceDoc() di atas. */
    public function paymentDoc(DataLapangan $dataLapangan): ?array
    {
        $fee = $this->fee($dataLapangan);
        if ($fee === null) {
            return null;
        }

        $paidAt = $this->paidAt($dataLapangan);

        return [
            'transaction_id' => $this->paymentTransactionId($dataLapangan),
            'period' => $this->period($paidAt),
            'cancelled' => false,
            'data' => $this->withoutNulls([
                'invoice_transaction_id' => $this->transactionId($dataLapangan),
                'nilai' => $fee,
                'tanggal' => $paidAt->toDateString(),
                // keterangan_pembayaran adalah catatan bebas staf saat menandai DIBAYAR
                // (mis. referensi transfer) — cocok dipetakan ke `referensi`, bukan
                // `metode` (Kawulo Halal tidak mencatat metode pembayaran terstruktur).
                'referensi' => $dataLapangan->keterangan_pembayaran
                    ? Str::limit((string) $dataLapangan->keterangan_pembayaran, 255, '')
                    : $dataLapangan->no_registrasi,
                'metode' => null,
            ]),
        ];
    }

    // --- Envelope ------------------------------------------------------------

    /**
     * Envelope event WRGROUP. version dikirim sebagai string (mengikuti contoh
     * dokumentasi WRGROUP).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function envelope(string $eventId, string $transactionId, int $version, string $type, CarbonInterface $eventTime, array $data): array
    {
        return [
            'event_id' => $eventId,
            'transaction_id' => $transactionId,
            'version' => (string) $version,
            'event_time' => $eventTime->copy()->timezone(config('wrgroup.timezone'))->toIso8601String(),
            'type' => $type,
            'data' => $data,
        ];
    }

    public function typeFor(string $endpoint, int $version, bool $cancelled): string
    {
        return match ($endpoint) {
            'invoice' => $cancelled ? 'invoice.cancelled' : ($version === 1 ? 'invoice.created' : 'invoice.updated'),
            'refund' => 'refund.created',
            'payment' => 'payment.received',
            default => $endpoint,
        };
    }

    // --- Triwulan ------------------------------------------------------------

    /** Triwulan (Jan–Mar = Q1, dst.) dari tanggal transaksi. Format YYYY-Qn. */
    public function period(CarbonInterface $date): string
    {
        return $date->year.'-Q'.$date->quarter;
    }

    /**
     * @return array{0: Carbon, 1: Carbon} awal & akhir triwulan (Asia/Jakarta)
     */
    public function periodRange(string $period): array
    {
        [$year, $quarter] = explode('-Q', $period);
        $start = Carbon::create((int) $year, ((int) $quarter - 1) * 3 + 1, 1, 0, 0, 0, config('wrgroup.timezone'));

        return [$start, $start->copy()->addMonths(3)->subSecond()];
    }

    public function isValidPeriod(string $period): bool
    {
        return (bool) preg_match('/^\d{4}-Q[1-4]$/', $period);
    }

    public function previousPeriod(?CarbonInterface $now = null): string
    {
        return $this->period(($now ?? $this->now())->copy()->firstOfQuarter()->subDay());
    }

    public function now(): Carbon
    {
        return Carbon::now(config('wrgroup.timezone'));
    }

    // --- internal ------------------------------------------------------------

    /**
     * Fee yang sudah dibukukan untuk kasus ini, atau null bila belum DIBAYAR / fee-nya 0
     * (tidak ada dokumen tagihan untuk fee 0). Satu sumber kebenaran dengan cashflow lokal:
     * DataLapangan::resolveFee() adalah method yang sama persis yang dipakai
     * DataLapangan::booted() untuk membukukan CashflowsKoordinator/Cashflow.
     */
    private function fee(DataLapangan $dataLapangan): ?int
    {
        if (strtoupper(trim((string) $dataLapangan->status_pembayaran)) !== 'DIBAYAR') {
            return null;
        }

        $fee = (int) DataLapangan::resolveFee($dataLapangan);

        return $fee > 0 ? $fee : null;
    }

    /**
     * Waktu pembayaran dikonfirmasi. DataLapangan tidak memiliki kolom paid_at
     * khusus (beda dari Sale di reference) — updated_at pada saat status_pembayaran
     * berpindah ke DIBAYAR adalah proksi terbaik yang tersedia, dan ini adalah
     * timestamp yang sama yang dipakai catatan Cashflow/CashflowsKoordinator lokal.
     */
    private function paidAt(DataLapangan $dataLapangan): Carbon
    {
        return $dataLapangan->updated_at ? Carbon::parse($dataLapangan->updated_at) : $this->now();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withoutNulls(array $data): array
    {
        return array_filter($data, fn ($v) => $v !== null);
    }
}
