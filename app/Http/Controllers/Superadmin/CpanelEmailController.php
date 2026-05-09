<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CpanelEmailController extends Controller
{
    private string $host;
    private string $username;
    private string $token;
    private string $domain;

    public function __construct()
    {
        $this->host     = env('CPANEL_HOST', 'localhost');
        $this->username = env('CPANEL_USERNAME', '');
        $this->token    = env('CPANEL_API_TOKEN', '');
        $this->domain   = env('CPANEL_DOMAIN', '');
    }

    // ─── Index: tampilkan halaman ─────────────────────────────────────
    public function index()
    {
        $result = $this->fetchEmailAccounts();

        $emails    = $result['emails']  ?? [];
        $diskInfo  = $result['disk']    ?? [];
        $error     = $result['error']   ?? null;

        return view('superadmin.home.email-manager', compact('emails', 'diskInfo', 'error'));
    }

    // ─── API: refresh via AJAX ────────────────────────────────────────
    public function apiEmails()
    {
        return response()->json($this->fetchEmailAccounts());
    }

    // ─── Core fetch ───────────────────────────────────────────────────
    private function fetchEmailAccounts(): array
    {
        if (empty($this->token) || empty($this->username)) {
            return ['error' => 'CPANEL_API_TOKEN atau CPANEL_USERNAME belum dikonfigurasi di .env'];
        }

        try {
            // ── 1. Ambil daftar email accounts ──────────────────────
            $emailResp = $this->uapiCall('Email', 'list_pops_with_disk', [
                'domain' => $this->domain,
            ]);

            if (!isset($emailResp['result']['data'])) {
                return ['error' => 'Gagal mengambil data email: ' . ($emailResp['result']['errors'][0] ?? 'Unknown error')];
            }

            $rawEmails = $emailResp['result']['data'];

            // ── 2. Format data email ─────────────────────────────────
            $emails = collect($rawEmails)->map(function ($item) {
                $diskUsed  = (int) ($item['_diskused']  ?? $item['diskused']  ?? 0);
                $diskQuota = (int) ($item['_diskquota'] ?? $item['diskquota'] ?? 0);
                $pct       = ($diskQuota > 0) ? round(($diskUsed / $diskQuota) * 100, 1) : 0;

                return [
                    'email'       => ($item['login'] ?? $item['email'] ?? '?') . '@' . ($item['domain'] ?? $this->domain),
                    'login'       => $item['login'] ?? '',
                    'domain'      => $item['domain'] ?? $this->domain,
                    'disk_used'   => $this->bytesToHuman($diskUsed * 1024 * 1024),   // MB → bytes
                    'disk_quota'  => $diskQuota > 0 ? $this->bytesToHuman($diskQuota * 1024 * 1024) : 'Unlimited',
                    'disk_pct'    => min($pct, 100),
                    'suspended'   => (bool) ($item['suspended_login'] ?? false),
                    // Password tidak bisa diambil via API (cPanel tidak expose plaintext password)
                    // — password ditampilkan dari env atau dikosongkan
                    'password'    => '',
                ];
            })->values()->all();

            // ── 3. Disk usage keseluruhan domain ─────────────────────
            $diskResp = $this->uapiCall('Quota', 'get_quota_info');
            $diskInfo = [];
            if (isset($diskResp['result']['data'])) {
                $d = $diskResp['result']['data'];
                $used  = (int) ($d['megabytes_used'] ?? 0);
                $limit = (int) ($d['megabyte_limit']  ?? 0);
                $diskInfo = [
                    'used'    => $this->bytesToHuman($used * 1024 * 1024),
                    'limit'   => $limit > 0 ? $this->bytesToHuman($limit * 1024 * 1024) : 'Unlimited',
                    'percent' => $limit > 0 ? round(($used / $limit) * 100, 1) : 0,
                    'raw_used'  => $used,
                    'raw_limit' => $limit,
                ];
            }

            return [
                'emails'   => $emails,
                'disk'     => $diskInfo,
                'total'    => count($emails),
                'error'    => null,
            ];
        } catch (\Throwable $e) {
            return ['error' => 'Exception: ' . $e->getMessage()];
        }
    }

    // ─── UAPI Helper ──────────────────────────────────────────────────
    private function uapiCall(string $module, string $function, array $params = []): array
    {
        // cPanel UAPI: port 2083 (SSL) atau 2082 (non-SSL)
        $port     = 2083;
        $scheme   = 'https';
        $base     = "{$scheme}://{$this->host}:{$port}/execute/{$module}/{$function}";

        $response = Http::withHeaders([
            'Authorization' => "cpanel {$this->username}:{$this->token}",
        ])
            ->withOptions(['verify' => false])   // skip SSL verify untuk localhost
            ->timeout(15)
            ->get($base, $params);

        return $response->json() ?? [];
    }

    // ─── Helpers ──────────────────────────────────────────────────────
    private function bytesToHuman(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i     = (int) floor(log($bytes, 1024));
        $i     = min($i, count($units) - 1);
        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }
}
