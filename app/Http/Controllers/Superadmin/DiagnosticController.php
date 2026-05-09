<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class DiagnosticController extends Controller
{
    // ─── Show the diagnostic page ───────────────────────────────────────
    public function index()
    {
        return view('superadmin.home.troubleshoot');
    }

    // ─── Run all checks and return JSON ────────────────────────────────
    public function run(Request $request)
    {
        $start = microtime(true);

        $result = [
            'cronjobs'        => $this->checkCronjobs(),
            'php'             => $this->checkPhp(),
            'filesystem'      => $this->checkFilesystem(),
            'storage_link'    => $this->checkStorageLink(),
            'ssl'             => $this->checkSsl(),
            'env'             => $this->checkEnv(),
            'database'        => $this->checkDatabase(),
            'network'         => $this->checkNetwork(),
            'scheduled_tasks' => $this->checkScheduledTasks(),
            'elapsed'         => round(microtime(true) - $start, 2),
        ];

        return response()->json($result);
    }

    // ── 1. Cronjobs (via cache timestamps) ─────────────────────────────
    // In each artisan command, call:  Cache::put('cron_last_run_<name>', now(), 60 * 5);
    private function checkCronjobs(): array
    {
        $jobs = [
            ['key' => 'cron_last_run_subscribe_history', 'name' => 'Subscribes & History Messages'],
            ['key' => 'cron_last_run_blast',             'name' => 'Blast'],
            ['key' => 'cron_last_run_schedule_run',      'name' => 'Schedule-run'],
        ];

        $result = [];
        foreach ($jobs as $job) {
            $lastRun = Cache::get($job['key']);
            if ($lastRun) {
                $minutesAgo = (int) now()->diffInMinutes($lastRun);
                $result[] = [
                    'name'     => $job['name'],
                    'working'  => $minutesAgo <= 10,
                    'last_run' => $minutesAgo,
                    'note'     => '',
                ];
            } else {
                $result[] = [
                    'name'    => $job['name'],
                    'working' => false,
                    'last_run' => null,
                    'note'    => 'Belum pernah berjalan atau cache expired',
                ];
            }
        }
        return $result;
    }

    // ── 2. PHP version & extensions ────────────────────────────────────
    private function checkPhp(): array
    {
        $required = '8.2';
        $extensions = [
            'curl',
            'fileinfo',
            'intl',
            'json',
            'mbstring',
            'openssl',
            'mysqli',
            'zip',
            'ctype',
            'dom',
        ];

        $extStatus = [];
        foreach ($extensions as $ext) {
            $extStatus[$ext] = extension_loaded($ext);
        }

        return [
            'version'    => PHP_VERSION,
            'required'   => $required,
            'valid'      => version_compare(PHP_VERSION, $required, '>='),
            'extensions' => $extStatus,
        ];
    }

    // ── 3. Filesystem permissions ───────────────────────────────────────
    private function checkFilesystem(): array
    {
        $paths = [
            storage_path()                  => 'storage',
            base_path('bootstrap/cache')    => 'bootstrap/cache',
        ];

        $result = [];
        foreach ($paths as $abs => $label) {
            $result[] = [
                'path'     => $label,
                'exists'   => is_dir($abs),
                'writable' => is_writable($abs),
                'note'     => !is_dir($abs) ? 'Directory tidak ditemukan' : (!is_writable($abs) ? 'Tidak writable' : ''),
            ];
        }
        return $result;
    }

    // ── 4. Storage symlink ──────────────────────────────────────────────
    private function checkStorageLink(): bool
    {
        return is_link(public_path('storage'));
    }

    // ── 5. SSL Certificate (cPanel API) ────────────────────────────────
    private function checkSsl(): array
    {
        $host   = config('cpanel.host',     env('CPANEL_HOST'));
        $user   = config('cpanel.username', env('CPANEL_USERNAME'));
        $token  = config('cpanel.token',    env('CPANEL_API_TOKEN'));
        $domain = config('cpanel.domain',   env('CPANEL_DOMAIN'));

        try {
            // Try cPanel UAPI first
            $response = Http::withHeaders([
                'Authorization' => 'cpanel ' . $user . ':' . $token,
            ])->withOptions(['verify' => false])
                ->timeout(10)
                ->get("{$host}/execute/SSL/list_certs", ['domain' => $domain]);

            if ($response->successful()) {
                $certs = $response->json('data') ?? [];
                if (!empty($certs)) {
                    $cert = $certs[0];
                    $validTo  = \Carbon\Carbon::parse($cert['not_after'] ?? null);
                    $validFrom = \Carbon\Carbon::parse($cert['not_before'] ?? null);
                    $daysLeft = (int) now()->diffInDays($validTo, false);

                    return [
                        'valid'          => $daysLeft > 0,
                        'common_name'    => $cert['domains'][0] ?? $domain,
                        'organization'   => $cert['subject']['O'] ?? 'N/A',
                        'issuer'         => $cert['issuer']['O'] ?? 'N/A',
                        'valid_from'     => $validFrom->format('Y-m-d H:i:s'),
                        'valid_to'       => $validTo->format('Y-m-d H:i:s'),
                        'days_remaining' => $daysLeft,
                        'source'         => 'cpanel_api',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Fall through to socket check
        }

        // Fallback: direct socket check
        return $this->checkSslSocket($domain);
    }

    private function checkSslSocket(string $domain): array
    {
        try {
            $ctx = stream_context_create(['ssl' => ['capture_peer_cert' => true, 'verify_peer' => false]]);
            $sock = @stream_socket_client("ssl://{$domain}:443", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx);

            if (!$sock) {
                return ['valid' => false, 'error' => "Tidak dapat konek ke {$domain}:443"];
            }

            $params = stream_context_get_params($sock);
            $cert   = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            fclose($sock);

            $validTo  = \Carbon\Carbon::createFromTimestamp($cert['validTo_time_t']);
            $validFrom = \Carbon\Carbon::createFromTimestamp($cert['validFrom_time_t']);
            $daysLeft = (int) now()->diffInDays($validTo, false);

            return [
                'valid'          => $daysLeft > 0,
                'common_name'    => $cert['subject']['CN'] ?? $domain,
                'organization'   => $cert['subject']['O'] ?? 'N/A',
                'issuer'         => $cert['issuer']['O'] ?? 'N/A',
                'valid_from'     => $validFrom->format('Y-m-d H:i:s'),
                'valid_to'       => $validTo->format('Y-m-d H:i:s'),
                'days_remaining' => $daysLeft,
                'source'         => 'socket',
            ];
        } catch (\Throwable $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    // ── 6. .env keys ───────────────────────────────────────────────────
    private function checkEnv(): array
    {
        $keys = ['APP_KEY', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
        $result = [];
        foreach ($keys as $key) {
            $val = env($key);
            $result[$key] = ['ok' => !empty($val)];
        }
        return $result;
    }

    // ── 7. Database connection ──────────────────────────────────────────
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return [
                'connected' => true,
                'name'      => DB::connection()->getDatabaseName(),
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'error'     => $e->getMessage(),
            ];
        }
    }

    // ── 8. CURL / Network ───────────────────────────────────────────────
    private function checkNetwork(): array
    {
        $targets = ['https://www.google.com'];
        $result  = [];

        foreach ($targets as $url) {
            try {
                $response = Http::timeout(8)->get($url);
                $result[] = [
                    'url'     => $url,
                    'success' => true,
                    'code'    => $response->status(),
                ];
            } catch (\Throwable $e) {
                $result[] = [
                    'url'     => $url,
                    'success' => false,
                    'error'   => $e->getMessage(),
                ];
            }
        }
        return $result;
    }

    // ── 9. Scheduled tasks list ────────────────────────────────────────
    private function checkScheduledTasks(): array
    {
        try {
            Artisan::call('schedule:list', ['--json' => true]);
            $output = Artisan::output();
            $tasks  = json_decode($output, true);

            if (is_array($tasks)) {
                return array_map(fn($t) => [
                    'command'    => $t['command']  ?? $t['description'] ?? '—',
                    'expression' => $t['expression'] ?? '—',
                    'next_due'   => $t['next_due_at'] ?? $t['next_due'] ?? '—',
                ], $tasks);
            }
        } catch (\Throwable $e) {
            // schedule:list --json not available in older Laravel
        }

        // Fallback: parse text output
        try {
            Artisan::call('schedule:list');
            $raw   = Artisan::output();
            $lines = array_filter(explode("\n", $raw));
            $tasks = [];
            foreach ($lines as $line) {
                if (preg_match('/(\S+\s+\S+\s+\S+\s+\S+\s+\S+)\s+(php artisan \S+).*?Next Due:\s*(.+)/i', $line, $m)) {
                    $tasks[] = [
                        'expression' => trim($m[1]),
                        'command'    => trim($m[2]),
                        'next_due'   => trim($m[3]),
                    ];
                }
            }
            return $tasks;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
