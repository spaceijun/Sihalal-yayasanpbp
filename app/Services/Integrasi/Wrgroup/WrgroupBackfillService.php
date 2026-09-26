<?php

namespace App\Services\Integrasi\Wrgroup;

use App\Models\DataLapangan;
use App\Models\Superadmin\WrgroupOutbox;
use Illuminate\Support\Carbon;

/**
 * Backfill kasus sertifikasi LAMA yang sudah lunas ke WRGROUP: semua `DataLapangan` yang
 * tanggal lunasnya pada/setelah Tanggal Operasional pilar (dibaca dari WRGROUP: GET /profil →
 * data_historis.kirim_sejak), bukan hanya yang lunas setelah terhubung.
 *
 * - Hanya MENCATAT ke outbox lewat WrgroupEventService (idempotent: dokumen yang isinya tidak berubah tidak
 *   membuat versi baru); pengirimannya oleh `wrgroup:process` bertahap agar batas 300 request/menit WRGROUP aman.
 * - Hanya jalan bila kredensial production. Kredensial testing dijawab `persisted:false` oleh WRGROUP, sehingga
 *   event yang tercatat "terkirim" tidak benar-benar dibukukan dan tidak akan dikirim ulang nanti.
 * - Triwulan tanpa transaksi sejak triwulan Tanggal Operasional sampai triwulan lalu dilaporkan nihil.
 */
class WrgroupBackfillService
{
    public function __construct(
        private WrgroupClient $client,
        private WrgroupEventService $events,
        private WrgroupPayloadBuilder $builder,
        private WrgroupDeliveryService $delivery,
    ) {}

    /**
     * @return array{skipped:?string, since:?string, dry_run:bool, kasus:array{kandidat:int, dicatat:int}, nihil:list<string>}
     */
    public function run(?Carbon $since = null, bool $dryRun = false): array
    {
        $summary = [
            'skipped' => null,
            'since' => null,
            'dry_run' => $dryRun,
            'kasus' => ['kandidat' => 0, 'dicatat' => 0],
            'nihil' => [],
        ];

        if (! $this->client->enabled()) {
            return ['skipped' => 'Integrasi WRGROUP tidak aktif.'] + $summary;
        }
        if (! $this->client->configured()) {
            return ['skipped' => 'Kredensial WRGROUP belum lengkap di .env.'] + $summary;
        }
        if ($this->delivery->halted()) {
            return ['skipped' => 'Pengiriman dihentikan (401). Perbaiki kredensial lalu lanjutkan.'] + $summary;
        }

        $profil = $this->readProfile();
        if ($profil['error'] !== null) {
            return ['skipped' => $profil['error']] + $summary;
        }
        if ($profil['environment'] !== 'production') {
            return ['skipped' => "Kredensial berlingkungan \"{$profil['environment']}\": data tidak dibukukan WRGROUP, backfill ditunda sampai kredensial production dipakai."] + $summary;
        }

        $since ??= $profil['since'];
        if ($since === null) {
            return ['skipped' => 'Tanggal Operasional pilar belum ditentukan di WRGROUP — data lama belum dikirim.'] + $summary;
        }

        $since = $since->copy()->timezone(config('wrgroup.timezone'))->startOfDay();
        $summary['since'] = $since->toDateString();

        $this->events->withoutImmediateDispatch(function () use (&$summary, $since, $dryRun) {
            $summary['kasus'] = $this->backfillKasus($since, $dryRun);
            $summary['nihil'] = $this->backfillNihil($since, $dryRun);
        });

        return $summary;
    }

    /**
     * @return array{error:?string, environment:?string, since:?Carbon}
     */
    private function readProfile(): array
    {
        $result = $this->client->get('/profil');
        $http = $result['status'];

        if ($http !== 200 || ! is_array($result['body'])) {
            $detail = $http === 0 ? ($result['error'] ?? 'tanpa respons') : "HTTP {$http}";

            return ['error' => "Gagal membaca profil pilar dari WRGROUP ({$detail}).", 'environment' => null, 'since' => null];
        }

        $date = $result['body']['data_historis']['kirim_sejak'] ?? null;

        return [
            'error' => null,
            'environment' => (string) ($result['body']['environment'] ?? ''),
            'since' => is_string($date) && $date !== '' ? Carbon::parse($date, config('wrgroup.timezone')) : null,
        ];
    }

    /** @return array{kandidat:int, dicatat:int} */
    private function backfillKasus(Carbon $since, bool $dryRun): array
    {
        $stats = ['kandidat' => 0, 'dicatat' => 0];
        $before = $this->outboxCount(['invoice', 'payment']);

        DataLapangan::query()
            ->whereRaw('UPPER(status_pembayaran) = ?', ['DIBAYAR'])
            ->whereDate('updated_at', '>=', $since->toDateString())
            ->orderBy('id')
            ->chunkById(200, function ($kasusList) use (&$stats, $dryRun) {
                foreach ($kasusList as $dataLapangan) {
                    $stats['kandidat']++;
                    if ($dryRun) {
                        $stats['dicatat'] += $this->hasOutbox('invoice', $this->builder->transactionId($dataLapangan)) ? 0 : 1;
                    } else {
                        $this->events->recordDataLapanganPaid($dataLapangan);
                    }
                }
            });

        return $this->finish($stats, ['invoice', 'payment'], $before, $dryRun);
    }

    /**
     * Laporan nihil untuk triwulan tanpa transaksi, dari triwulan Tanggal Operasional sampai triwulan lalu.
     *
     * @return list<string> triwulan yang dicatat (dry-run: yang akan dicatat)
     */
    private function backfillNihil(Carbon $since, bool $dryRun): array
    {
        $periods = [];
        $last = $this->builder->previousPeriod();

        for ($cursor = $this->builder->periodRange($this->builder->period($since))[0]; $this->builder->period($cursor) <= $last; $cursor = $cursor->copy()->addMonths(3)) {
            $period = $this->builder->period($cursor);

            if ($dryRun) {
                $exists = WrgroupOutbox::where('endpoint', 'nihil')->where('period', $period)->exists();
                if (! $exists && ! $this->events->hasTransactions($period, $since)) {
                    $periods[] = $period;
                }

                continue;
            }

            if ($this->events->reportNihil($period, $since)['ok']) {
                $periods[] = $period;
            }
        }

        return $periods;
    }

    // --- internal ------------------------------------------------------------

    /**
     * @param  array{kandidat:int, dicatat:int}  $stats
     * @param  list<string>  $endpoints
     * @return array{kandidat:int, dicatat:int}
     */
    private function finish(array $stats, array $endpoints, int $before, bool $dryRun): array
    {
        if (! $dryRun) {
            $stats['dicatat'] = $this->outboxCount($endpoints) - $before;
        }

        return $stats;
    }

    /** @param  list<string>  $endpoints */
    private function outboxCount(array $endpoints): int
    {
        return WrgroupOutbox::whereIn('endpoint', $endpoints)->count();
    }

    private function hasOutbox(string $endpoint, string $transactionId): bool
    {
        return WrgroupOutbox::where('endpoint', $endpoint)->where('transaction_id', $transactionId)->exists();
    }
}
