<?php

namespace App\Services\Integrasi\Wrgroup;

use App\Jobs\SendWrgroupEvent;
use App\Models\Superadmin\WrgroupOutbox;
use Illuminate\Support\Facades\Cache;

/**
 * Pengirim event outbox → WRGROUP. Satu-satunya tempat yang memanggil API WRGROUP.
 * Porting 1:1 dari swaraningcode-be (mekanisme ini tidak spesifik domain bisnis).
 *
 * Aturan respons:
 *   200            → terkirim
 *   409            → duplikat = anggap sukses
 *   422 retry:true → invoice asal belum diterima → ulangi dengan backoff
 *   422 lainnya    → ditolak, TIDAK diulang otomatis (perlu perbaikan data)
 *   401            → hentikan SEMUA pengiriman (circuit breaker) sampai dilanjutkan
 *   403            → ditolak (cakupan salah / pilar diarsipkan)
 *   429            → tunggu Retry-After, tidak menghabiskan jatah percobaan
 *   timeout / 5xx  → ulangi dengan exponential backoff, event_id SAMA
 */
class WrgroupDeliveryService
{
    public const HALT_KEY = 'wrgroup:halt';

    public const HEARTBEAT_KEY = 'wrgroup:heartbeat';

    private const PATHS = [
        'invoice' => '/events/invoice',
        'refund' => '/events/refund',
        'payment' => '/events/payment',
        'nihil' => '/reports/nihil',
        'pendapatan_bersih' => '/reports/pendapatan-bersih',
    ];

    /** Klaim yang lebih tua dari ini dianggap milik proses yang mati. */
    private const LOCK_TTL_MINUTES = 5;

    public function __construct(private WrgroupClient $client) {}

    // --- Pengiriman event ----------------------------------------------------

    /**
     * Kirim satu event bila memang sudah waktunya. Aman dipanggil paralel
     * (job + scheduler): hanya satu pemanggil yang berhasil mengklaim.
     */
    public function deliver(WrgroupOutbox $row): void
    {
        if (! $this->canSend()) {
            return;
        }

        if (! $this->claim($row)) {
            return;
        }

        $row->refresh();

        $result = $this->client->post(self::PATHS[$row->endpoint], $row->payload);

        $this->applyResult($row, $result);
    }

    /**
     * Proses event pending yang sudah jatuh tempo (scheduler tiap menit).
     *
     * @return array{processed:int, sent:int, pending:int, rejected:int, failed:int, skipped:?string}
     */
    public function processDue(?int $limit = null): array
    {
        $summary = ['processed' => 0, 'sent' => 0, 'pending' => 0, 'rejected' => 0, 'failed' => 0, 'skipped' => null];

        if (! $this->client->enabled()) {
            return ['skipped' => 'Integrasi WRGROUP tidak aktif.'] + $summary;
        }
        if (! $this->client->configured()) {
            return ['skipped' => 'Kredensial WRGROUP belum lengkap di .env.'] + $summary;
        }
        if ($this->halted()) {
            return ['skipped' => 'Pengiriman dihentikan (401). Perbaiki kredensial lalu lanjutkan.'] + $summary;
        }

        $rows = WrgroupOutbox::due()->orderBy('id')->limit($limit ?? (int) config('wrgroup.batch_size'))->get();

        foreach ($rows as $row) {
            if ($this->halted()) {
                break;
            }

            $this->deliver($row);
            $row->refresh();

            $summary['processed']++;
            $summary[$row->status] = ($summary[$row->status] ?? 0) + 1;
        }

        return $summary;
    }

    /** Kirim ulang event rejected/failed setelah penyebabnya diperbaiki. */
    public function retry(WrgroupOutbox $row): bool
    {
        if (! $row->isRetryable()) {
            return false;
        }

        $row->update([
            'status' => 'pending',
            'attempts' => 0,
            'next_attempt_at' => now(),
            'locked_at' => null,
            'last_error' => null,
        ]);

        SendWrgroupEvent::dispatch($row->id);

        return true;
    }

    // --- Heartbeat -----------------------------------------------------------

    /**
     * Heartbeat berkala. Hasil terakhir disimpan di cache untuk panel monitoring.
     *
     * @return array{ok:bool, message:string, http:?int}
     */
    public function heartbeat(): array
    {
        if (! $this->client->enabled()) {
            return ['ok' => false, 'message' => 'Integrasi WRGROUP tidak aktif.', 'http' => null];
        }
        if (! $this->client->configured()) {
            return ['ok' => false, 'message' => 'Kredensial WRGROUP belum lengkap di .env.', 'http' => null];
        }
        if ($this->halted()) {
            return ['ok' => false, 'message' => 'Pengiriman dihentikan (401). Perbaiki kredensial lalu lanjutkan.', 'http' => null];
        }

        $body = ['status' => config('wrgroup.heartbeat_status', 'online')];

        $lastTransaction = WrgroupOutbox::whereIn('endpoint', ['invoice', 'refund', 'payment'])
            ->latest('event_time')->value('event_time');
        if ($lastTransaction) {
            $body['last_transaction_at'] = $lastTransaction->copy()->timezone(config('wrgroup.timezone'))->toIso8601String();
        }

        $result = $this->client->post('/heartbeat', $body);
        $http = $result['status'];
        $ok = $http >= 200 && $http < 300;

        if ($http === 401) {
            $this->halt('Kredensial ditolak WRGROUP (401) saat heartbeat.', 401);
        }

        $message = $ok ? 'Heartbeat diterima.' : $this->describe($result);

        Cache::forever(self::HEARTBEAT_KEY, [
            'at' => now()->toIso8601String(),
            'ok' => $ok,
            'http' => $http ?: null,
            'message' => $message,
            'environment' => $result['body']['environment'] ?? null,
            'persisted' => $result['body']['persisted'] ?? null,
        ]);

        // http = 0 → gagal koneksi/timeout; null hanya untuk "dilewati" (nonaktif/belum dikonfigurasi).
        return ['ok' => $ok, 'message' => $message, 'http' => $http];
    }

    // --- Circuit breaker (401) -------------------------------------------------

    /** @return array{reason:string, http:int, at:string}|null */
    public function halted(): ?array
    {
        return Cache::get(self::HALT_KEY);
    }

    public function resume(): void
    {
        Cache::forget(self::HALT_KEY);
    }

    // --- internal ------------------------------------------------------------

    private function canSend(): bool
    {
        return $this->client->enabled() && $this->client->configured() && ! $this->halted();
    }

    /**
     * Klaim atomik: hanya pending + jatuh tempo + tidak sedang dikunci proses lain.
     */
    private function claim(WrgroupOutbox $row): bool
    {
        return WrgroupOutbox::whereKey($row->id)
            ->due()
            ->where(fn ($q) => $q->whereNull('locked_at')->orWhere('locked_at', '<', now()->subMinutes(self::LOCK_TTL_MINUTES)))
            ->update(['locked_at' => now()]) === 1;
    }

    /**
     * @param  array{status:int, body:?array<string, mixed>, retry_after:?int, error:?string}  $result
     */
    private function applyResult(WrgroupOutbox $row, array $result): void
    {
        $http = $result['status'];
        $body = $result['body'];

        $update = [
            'last_http_status' => $http ?: null,
            'last_response' => $body ?? ($result['error'] ? ['raw' => $result['error']] : null),
            'locked_at' => null,
        ];

        $attempts = $row->attempts + 1;

        if (($http >= 200 && $http < 300) || $http === 409) {
            $update += [
                'status' => 'sent',
                'attempts' => $attempts,
                'sent_at' => now(),
                'next_attempt_at' => null,
                'last_error' => null,
                'environment' => $body['environment'] ?? $row->environment,
                'persisted' => $body['persisted'] ?? $row->persisted,
            ];
        } elseif ($http === 401) {
            // Bukan salah event ini — hentikan semua pengiriman, jangan habiskan percobaan.
            $this->halt('Kredensial ditolak WRGROUP (401). Periksa WRGROUP_BUSINESS_ID / API_KEY / API_SECRET.', 401);
            $update += ['status' => 'pending', 'last_error' => $this->describe($result), 'next_attempt_at' => null];
        } elseif ($http === 429) {
            $wait = max(1, $result['retry_after'] ?? 60);
            $update += ['status' => 'pending', 'next_attempt_at' => now()->addSeconds($wait), 'last_error' => "Kena rate limit (429), coba lagi {$wait} dtk."];
        } elseif ($http === 0 || $http >= 500 || $http === 408 || ($http === 422 && ($body['retry'] ?? false) === true)) {
            $update += $this->scheduleRetry($attempts) + ['attempts' => $attempts, 'last_error' => $this->describe($result)];
        } else {
            $update += ['status' => 'rejected', 'attempts' => $attempts, 'next_attempt_at' => null, 'last_error' => $this->describe($result)];
        }

        $row->update($update);
    }

    /**
     * @return array{status:string, next_attempt_at:?\Illuminate\Support\Carbon}
     */
    private function scheduleRetry(int $attempts): array
    {
        $schedule = array_values((array) config('wrgroup.backoff'));

        if ($attempts >= count($schedule)) {
            return ['status' => 'failed', 'next_attempt_at' => null];
        }

        return ['status' => 'pending', 'next_attempt_at' => now()->addSeconds((int) $schedule[$attempts - 1])];
    }

    private function halt(string $reason, int $http): void
    {
        Cache::forever(self::HALT_KEY, ['reason' => $reason, 'http' => $http, 'at' => now()->toIso8601String()]);
    }

    /**
     * Ringkasan singkat penyebab kegagalan untuk kolom last_error / panel.
     *
     * @param  array{status:int, body:?array<string, mixed>, retry_after:?int, error:?string}  $result
     */
    private function describe(array $result): string
    {
        $body = $result['body'] ?? [];
        $message = $body['message'] ?? $body['error'] ?? $result['error'] ?? 'Tidak ada respons.';
        $message = is_scalar($message) ? (string) $message : json_encode($message, JSON_UNESCAPED_UNICODE);
        $label = $result['status'] ? "HTTP {$result['status']}" : 'Tanpa respons';

        $errors = $body['errors'] ?? null;
        if (is_array($errors) && $errors !== []) {
            $message .= ' — '.collect($errors)->map(fn ($v, $k) => $k.': '.(is_array($v) ? implode(', ', $v) : $v))->take(5)->implode('; ');
        }

        return mb_substr("{$label}: {$message}", 0, 1000);
    }
}
