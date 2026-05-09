<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * CpanelEmailController
 *
 * .env:
 *   CPANEL_HOST      = https://localhost:2083
 *   CPANEL_USERNAME  = kawulohalal
 *   CPANEL_API_TOKEN = R9W6NSZUM1YAWDW2QZV9Q70377ZZZA3Q
 *   CPANEL_DOMAIN    = kawulohalal.id
 */
class CpanelEmailController extends Controller
{
    private string $host;
    private int    $port;
    private bool   $ssl;
    private string $username;
    private string $token;
    private string $domain;

    public function __construct()
    {
        $rawHost = trim(env('CPANEL_HOST', '127.0.0.1'));

        if (str_contains($rawHost, '://')) {
            $parsed     = parse_url($rawHost);
            $this->host = $parsed['host'] ?? 'localhost';
            $this->port = (int) ($parsed['port'] ?? (($parsed['scheme'] ?? 'https') === 'https' ? 2083 : 2082));
            $this->ssl  = ($parsed['scheme'] ?? 'https') === 'https';
        } else {
            $parts      = explode(':', $rawHost);
            $this->host = $parts[0];
            $this->port = isset($parts[1]) ? (int) $parts[1] : 2083;
            $this->ssl  = $this->port !== 2082;
        }

        if (env('CPANEL_PORT')) {
            $this->port = (int) env('CPANEL_PORT');
            $this->ssl  = $this->port !== 2082;
        }

        $this->username = env('CPANEL_USERNAME', '');
        $this->token    = env('CPANEL_API_TOKEN', '');
        $this->domain   = env('CPANEL_DOMAIN', '');
    }

    // ─── Index ────────────────────────────────────────────────────────
    public function index()
    {
        $result   = $this->fetchEmailAccounts();
        $emails   = $result['emails']  ?? [];
        $diskInfo = $result['disk']    ?? [];
        $error    = $result['error']   ?? null;

        return view('superadmin.home.email-manager', compact('emails', 'diskInfo', 'error'));
    }

    // ─── AJAX refresh ─────────────────────────────────────────────────
    public function apiEmails()
    {
        return response()->json($this->fetchEmailAccounts());
    }

    // ─── Tambah Email ─────────────────────────────────────────────────
    public function addEmail(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|max:64|regex:/^[a-zA-Z0-9._\-]+$/',
            'password' => 'required|string|min:8|max:72',
            'quota'    => 'nullable|integer|min:0|max:51200',
        ]);

        $login    = $request->input('email');
        $password = $request->input('password');
        $quota    = $request->input('quota', 250); // MB, 0 = unlimited

        $resp = $this->uapiCall('Email', 'add_pop', [
            'email'    => $login,
            'password' => $password,
            'quota'    => $quota,
            'domain'   => $this->domain,
        ]);

        $payload = $resp['result'] ?? $resp;

        if (($payload['status'] ?? 1) === 0) {
            $errMsg = implode('; ', $payload['errors'] ?? ['Gagal menambah email']);
            return response()->json(['success' => false, 'message' => $errMsg], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Email {$login}@{$this->domain} berhasil dibuat.",
        ]);
    }

    // ─── Reset Password ───────────────────────────────────────────────
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string|min:8|max:72',
        ]);

        $email    = $request->input('email');   // format: user@domain
        $password = $request->input('password');

        // Pisahkan login dan domain
        [$login, $domain] = str_contains($email, '@')
            ? explode('@', $email, 2)
            : [$email, $this->domain];

        $resp = $this->uapiCall('Email', 'passwd_pop', [
            'email'    => $login,
            'password' => $password,
            'domain'   => $domain,
        ]);

        $payload = $resp['result'] ?? $resp;

        if (($payload['status'] ?? 1) === 0) {
            $errMsg = implode('; ', $payload['errors'] ?? ['Gagal reset password']);
            return response()->json(['success' => false, 'message' => $errMsg], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Password untuk {$email} berhasil diubah.",
        ]);
    }

    // ─── Debug ────────────────────────────────────────────────────────
    public function debug()
    {
        $scheme = $this->ssl ? 'https' : 'http';
        $url    = "{$scheme}://{$this->host}:{$this->port}/execute/Email/list_pops_with_disk";
        $info   = ['parsed_config' => [
            'host' => $this->host,
            'port' => $this->port,
            'ssl' => $this->ssl,
            'username' => $this->username,
            'token'    => $this->token ? substr($this->token, 0, 6) . '...' : '(kosong)',
            'domain'   => $this->domain,
            'final_url' => $url,
        ]];
        try {
            $resp = $this->httpClient()->get($url, ['domain' => $this->domain]);
            $info['http_status'] = $resp->status();
            $info['response']    = $resp->json() ?? $resp->body();
        } catch (\Throwable $e) {
            $info['exception'] = $e->getMessage();
        }
        return response()->json($info, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    // ─── Core fetch ───────────────────────────────────────────────────
    private function fetchEmailAccounts(): array
    {
        if (empty($this->token))    return ['error' => 'CPANEL_API_TOKEN belum diisi di .env'];
        if (empty($this->username)) return ['error' => 'CPANEL_USERNAME belum diisi di .env'];
        if (empty($this->host))     return ['error' => 'CPANEL_HOST belum diisi di .env'];

        try {
            $emailResp = $this->uapiCall('Email', 'list_pops_with_disk', [
                'domain' => $this->domain,
            ]);

            $payload = $emailResp['result'] ?? $emailResp;

            if (($payload['status'] ?? 1) === 0) {
                $errMsg = implode('; ', $payload['errors'] ?? ['Unknown cPanel error']);
                return ['error' => "cPanel menolak request: {$errMsg}"];
            }

            $rawEmails = $payload['data'] ?? null;
            if ($rawEmails === null) {
                return ['error' => 'Respons cPanel tidak terduga: ' . json_encode($emailResp)];
            }

            $emails = collect($rawEmails)->map(function ($item) {
                $diskUsedBytes  = (int) ($item['_diskused'] ?? 0);
                $diskQuotaBytes = (int) ($item['_diskquota'] ?? 0);

                if ($diskUsedBytes === 0 && isset($item['diskused']) && $item['diskused'] > 0) {
                    $diskUsedBytes = (int) ((float) $item['diskused'] * 1024 * 1024);
                }
                if ($diskQuotaBytes === 0 && isset($item['diskquota']) && $item['diskquota'] > 0) {
                    $diskQuotaBytes = (int) ((float) $item['diskquota'] * 1024 * 1024);
                }

                $pct = $diskQuotaBytes > 0
                    ? round(($diskUsedBytes / $diskQuotaBytes) * 100, 1)
                    : 0;

                $rawLogin = $item['login'] ?? $item['email'] ?? $item['user'] ?? '';
                $domain   = $item['domain'] ?? $this->domain;
                $email    = str_contains($rawLogin, '@') ? $rawLogin : "{$rawLogin}@{$domain}";
                $login    = str_contains($rawLogin, '@') ? explode('@', $rawLogin)[0] : $rawLogin;

                return [
                    'email'      => $email,
                    'login'      => $login,
                    'domain'     => $domain,
                    'disk_used'  => $this->bytesToHuman($diskUsedBytes),
                    'disk_quota' => $diskQuotaBytes > 0
                        ? $this->bytesToHuman($diskQuotaBytes)
                        : 'Unlimited',
                    'disk_pct'   => min($pct, 100),
                    'suspended'  => (bool) ($item['suspended_login'] ?? false),
                    'password'   => '',
                ];
            })->values()->all();

            $diskInfo = [];
            $diskResp = $this->uapiCall('Quota', 'get_quota_info');
            $dPayload = $diskResp['result'] ?? $diskResp;

            if (isset($dPayload['data'])) {
                $d     = $dPayload['data'];
                $used  = (float) ($d['megabytes_used'] ?? 0);
                $limit = (float) ($d['megabyte_limit'] ?? 0);
                $diskInfo = [
                    'used'      => $this->bytesToHuman((int) ($used  * 1024 * 1024)),
                    'limit'     => $limit > 0
                        ? $this->bytesToHuman((int) ($limit * 1024 * 1024))
                        : 'Unlimited',
                    'percent'   => $limit > 0 ? round(($used / $limit) * 100, 1) : 0,
                    'raw_used'  => $used,
                    'raw_limit' => $limit,
                ];
            }

            return [
                'emails' => $emails,
                'disk'   => $diskInfo,
                'total'  => count($emails),
                'error'  => null,
            ];
        } catch (\Throwable $e) {
            return ['error' => 'Exception: ' . $e->getMessage()];
        }
    }

    // ─── UAPI Helper ──────────────────────────────────────────────────
    private function uapiCall(string $module, string $function, array $params = []): array
    {
        $scheme   = $this->ssl ? 'https' : 'http';
        $url      = "{$scheme}://{$this->host}:{$this->port}/execute/{$module}/{$function}";
        $response = $this->httpClient()->get($url, $params);
        return $response->json() ?? [];
    }

    private function httpClient()
    {
        return Http::withHeaders([
            'Authorization' => "cpanel {$this->username}:{$this->token}",
        ])->withOptions([
            'verify'          => false,
            'connect_timeout' => 10,
        ])->timeout(20);
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
