<?php

namespace App\Services\Integrasi\Wrgroup;

use App\Models\Superadmin\WrgroupOutbox;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Data untuk panel monitoring integrasi WRGROUP (superadmin). Read-only.
 */
class WrgroupMonitorService
{
    public function __construct(
        private WrgroupClient $client,
        private WrgroupDeliveryService $delivery,
        private WrgroupPayloadBuilder $builder,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function dashboard(): array
    {
        $counts = WrgroupOutbox::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $apiKey = (string) config('wrgroup.api_key');

        return [
            'enabled' => $this->client->enabled(),
            'configured' => $this->client->configured(),
            'halt' => $this->delivery->halted(),
            'heartbeat' => Cache::get(WrgroupDeliveryService::HEARTBEAT_KEY),
            'counts' => [
                'pending' => (int) ($counts['pending'] ?? 0),
                'sent' => (int) ($counts['sent'] ?? 0),
                'rejected' => (int) ($counts['rejected'] ?? 0),
                'failed' => (int) ($counts['failed'] ?? 0),
            ],
            'last_sent_at' => WrgroupOutbox::where('status', 'sent')->max('sent_at'),
            'connection' => [
                'api_url' => config('wrgroup.api_url') ?: '—',
                'business_id' => config('wrgroup.business_id') ?: '—',
                // API Key hanya ditampilkan prefix-nya; API Secret TIDAK PERNAH ditampilkan.
                'api_key' => $apiKey !== '' ? Str::limit($apiKey, 8, '…') : '—',
                'has_secret' => filled(config('wrgroup.api_secret')),
                'start_date' => config('wrgroup.start_date') ?: '—',
            ],
            'nihil_periods' => $this->nihilPeriods(),
        ];
    }

    /**
     * Query builder listing event untuk DataTables server-side (bukan Collection —
     * lihat WrgroupController::data()).
     *
     * @param  array<string, mixed>  $filters
     */
    public function eventsQuery(array $filters = []): Builder
    {
        return WrgroupOutbox::query()
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['endpoint'] ?? null, fn ($q, $v) => $q->where('endpoint', $v))
            ->latest('id');
    }

    /**
     * Empat triwulan terakhir yang sudah berakhir (kandidat laporan nihil manual).
     *
     * @return list<string>
     */
    public function nihilPeriods(): array
    {
        $periods = [];
        $cursor = $this->builder->now();

        for ($i = 0; $i < 4; $i++) {
            $period = $this->builder->previousPeriod($cursor);
            $periods[] = $period;
            $cursor = $cursor->copy()->firstOfQuarter()->subDay();
        }

        return $periods;
    }
}
