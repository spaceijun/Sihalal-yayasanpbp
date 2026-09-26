<?php

namespace App\Services\Integrasi\Wrgroup;

use App\Jobs\SendWrgroupEvent;
use App\Models\DataLapangan;
use App\Models\Superadmin\WrgroupOutbox;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Mencatat event ke outbox WRGROUP (belum mengirim — itu tugas
 * WrgroupDeliveryService). Dipanggil dari observer model.
 *
 * Prinsip (identik dgn reference swaraningcode-be, lihat .agent/workflows/wrgroup-integrasi.md):
 *  - Best-effort: kegagalan integrasi TIDAK PERNAH membatalkan alur bisnis.
 *  - Versi naik hanya bila isi data berubah (hash) → pemanggilan berulang aman.
 *  - Event lama tidak pernah diubah; koreksi = versi baru dengan event_id baru.
 */
class WrgroupEventService
{
    /** false = baris outbox dicatat saja; pengirimannya diserahkan ke `wrgroup:process` (mis. saat backfill). */
    private bool $dispatchImmediately = true;

    public function __construct(
        private WrgroupPayloadBuilder $builder,
        private WrgroupClient $client,
    ) {}

    /**
     * Jalankan $callback tanpa mengantrikan job kirim per event. Untuk pencatatan massal (backfill data
     * lama) agar ribuan event tidak serentak menghantam batas 300 request/menit WRGROUP; `wrgroup:process`
     * mengirimnya bertahap (batch_size per menit).
     */
    public function withoutImmediateDispatch(callable $callback): mixed
    {
        $previous = $this->dispatchImmediately;
        $this->dispatchImmediately = false;

        try {
            return $callback();
        } finally {
            $this->dispatchImmediately = $previous;
        }
    }

    // --- Kasus sertifikasi (DataLapangan) -------------------------------------

    /**
     * Kasus sertifikasi lunas (status_pembayaran → DIBAYAR) → invoice + pembayaran WRGROUP,
     * dicatat bersamaan dari satu titik pemicu (meniru recordSalePaid() di reference — Kawulo
     * Halal tidak punya invoice yang terbit terpisah dari payment, jadi tidak ada syncInvoice()/
     * recordPayment() terpisah seperti InvoiceObserver/PaymentObserver reference).
     */
    public function recordDataLapanganPaid(DataLapangan $dataLapangan): void
    {
        $this->guard(function () use ($dataLapangan) {
            $this->record('invoice', $this->builder->invoiceDoc($dataLapangan), $dataLapangan);
            $this->record('payment', $this->builder->paymentDoc($dataLapangan), $dataLapangan);
        });
    }

    // --- Laporan nihil -------------------------------------------------------

    /**
     * Catat laporan nihil untuk satu triwulan.
     *
     * $since (opsional) = Tanggal Operasional pilar di WRGROUP: pada triwulan yang memuat tanggal itu, hanya
     * transaksi pada/setelah $since yang dihitung (yang lebih awal memang tidak dilaporkan).
     *
     * @return array{ok:bool, message:string, row:?WrgroupOutbox}
     */
    public function reportNihil(string $period, ?CarbonInterface $since = null): array
    {
        if (! $this->enabled()) {
            return $this->nihilResult(false, 'Integrasi WRGROUP tidak aktif (WRGROUP_ENABLED).');
        }
        if (! $this->builder->isValidPeriod($period)) {
            return $this->nihilResult(false, 'Format periode harus YYYY-Qn, mis. 2026-Q2.');
        }

        [, $end] = $this->builder->periodRange($period);
        if ($end->isFuture()) {
            return $this->nihilResult(false, "Triwulan {$period} belum berakhir — laporan nihil dikirim setelah triwulan berakhir.");
        }
        if ($this->hasTransactions($period, $since)) {
            return $this->nihilResult(false, "Triwulan {$period} memiliki transaksi — tidak boleh dilaporkan nihil.");
        }

        $existing = WrgroupOutbox::where('endpoint', 'nihil')->where('period', $period)->first();
        if ($existing) {
            return $this->nihilResult(false, "Laporan nihil {$period} sudah tercatat (status: {$existing->status_label}).", $existing);
        }

        $payload = ['periode' => $period, 'catatan' => 'Tidak ada kasus sertifikasi yang lunas pada periode ini'];

        $row = $this->createRow('nihil', 'NIHIL-'.$period, 1, 'report.nihil', $period, null, $payload, $payload);

        return $this->nihilResult((bool) $row, $row ? "Laporan nihil {$period} masuk antrian pengiriman." : 'Gagal mencatat laporan nihil.', $row);
    }

    /**
     * Apakah triwulan punya transaksi apa pun (outbox ATAU data sumber) — dicek ke tabel
     * data_lapangans juga agar data sebelum integrasi aktif ikut terhitung.
     */
    public function hasTransactions(string $period, ?CarbonInterface $since = null): bool
    {
        [$start, $end] = $this->builder->periodRange($period);

        if ($since !== null && $since->copy()->startOfDay()->gt($start)) {
            $start = $since->copy()->timezone(config('wrgroup.timezone'))->startOfDay();
        }

        if (WrgroupOutbox::where('endpoint', 'invoice')->where('period', $period)->exists()) {
            return true;
        }

        // Tidak ada kolom paid_at khusus di data_lapangans — updated_at dipakai sebagai
        // proksi "kapan lunas" (sama seperti WrgroupPayloadBuilder::paidAt()). Batasan
        // yang diketahui: edit lain pada baris yang SUDAH DIBAYAR di masa lalu bisa
        // menggeser updated_at tanpa mengubah status_pembayaran — lihat
        // .agent/workflows/wrgroup-integrasi.md § Batasan.
        return DataLapangan::query()
            ->whereRaw('UPPER(status_pembayaran) = ?', ['DIBAYAR'])
            ->whereBetween('updated_at', [$start, $end])
            ->exists();
    }

    // --- internal ------------------------------------------------------------

    /**
     * @param  array{transaction_id:string, period:string, cancelled:bool, data:array<string, mixed>}|null  $doc
     */
    private function record(string $endpoint, ?array $doc, Model $source): ?WrgroupOutbox
    {
        if ($doc === null || ! $this->enabled()) {
            return null;
        }

        $hash = $this->hashData($doc['data']);

        try {
            return DB::transaction(function () use ($endpoint, $doc, $source, $hash) {
                $last = WrgroupOutbox::where('endpoint', $endpoint)
                    ->where('transaction_id', $doc['transaction_id'])
                    ->orderByDesc('version')
                    ->lockForUpdate()
                    ->first();

                // Tidak ada perubahan berarti → tidak ada versi baru.
                if ($last && $last->payload_hash === $hash) {
                    return null;
                }

                $version = ($last?->version ?? 0) + 1;
                $type = $this->builder->typeFor($endpoint, $version, $doc['cancelled']);
                $eventId = (string) Str::uuid();
                $eventTime = $this->builder->now();

                $payload = $this->builder->envelope($eventId, $doc['transaction_id'], $version, $type, $eventTime, $doc['data']);

                return $this->createRow(
                    $endpoint, $doc['transaction_id'], $version, $type, $doc['period'],
                    $source, $payload, $doc['data'], $eventId, $eventTime,
                );
            });
        } catch (UniqueConstraintViolationException) {
            // Proses paralel sudah mencatat versi yang sama — aman diabaikan.
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $payload  body request persis
     * @param  array<string, mixed>  $hashOf  bagian yang di-hash untuk deteksi perubahan
     */
    private function createRow(
        string $endpoint,
        string $transactionId,
        int $version,
        string $type,
        ?string $period,
        ?Model $source,
        array $payload,
        array $hashOf,
        ?string $eventId = null,
        ?CarbonInterface $eventTime = null,
    ): ?WrgroupOutbox {
        $row = WrgroupOutbox::create([
            'event_id' => $eventId ?? (string) Str::uuid(),
            'endpoint' => $endpoint,
            'transaction_id' => $transactionId,
            'version' => $version,
            'type' => $type,
            'period' => $period,
            'source_type' => $source ? class_basename($source) : null,
            'source_id' => $source?->getKey(),
            'payload' => $payload,
            'payload_hash' => $this->hashData($hashOf),
            'status' => 'pending',
            'next_attempt_at' => now(),
            'event_time' => $eventTime ?? $this->builder->now(),
        ]);

        // afterCommit: job baru jalan setelah transaksi bisnis + outbox ter-commit.
        if ($this->dispatchImmediately) {
            SendWrgroupEvent::dispatch($row->id);
        }

        return $row;
    }

    private function enabled(): bool
    {
        return $this->client->enabled();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function hashData(array $data): string
    {
        return hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Best-effort: exception integrasi tidak boleh merusak alur bisnis.
     */
    private function guard(callable $callback): mixed
    {
        // Nonaktif = no-op murni: jangan bangun payload / query relasi sama sekali.
        // (Argumen $callback dievaluasi lazily di dalam closure, jadi aman.)
        if (! $this->enabled()) {
            return null;
        }

        try {
            return $callback();
        } catch (\Throwable $e) {
            Log::warning('WRGROUP: gagal mencatat event — '.$e->getMessage(), ['exception' => $e::class]);

            return null;
        }
    }

    /**
     * @return array{ok:bool, message:string, row:?WrgroupOutbox}
     */
    private function nihilResult(bool $ok, string $message, ?WrgroupOutbox $row = null): array
    {
        return ['ok' => $ok, 'message' => $message, 'row' => $row];
    }
}
