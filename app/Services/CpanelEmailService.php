<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class CpanelEmailService
{
    private string $host;
    private string $username;
    private string $apiToken;
    private string $domain;

    public function __construct()
    {
        $this->host      = config('services.cpanel.host');
        $this->username  = config('services.cpanel.username');
        $this->apiToken  = config('services.cpanel.api_token');
        $this->domain    = config('services.cpanel.domain');
    }

    /**
     * Buat akun email di cPanel.
     * Password di-generate otomatis, TIDAK disimpan ke DB.
     */
    public function createEmailAccount(string $emailPrefix, string $password, int $quotaMb = 250): void
    {
        $response = Http::withHeaders([
            'Authorization' => "cpanel {$this->username}:{$this->apiToken}",
        ])
            ->withoutVerifying()
            ->post("{$this->host}/execute/Email/add_pop", [
                'email'    => $emailPrefix,
                'password' => $password,   // password dari user, tidak disimpan di DB
                'quota'    => $quotaMb,
                'domain'   => $this->domain,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Gagal menghubungi cPanel: ' . $response->status());
        }

        $data = $response->json();

        if (!empty($data['errors'])) {
            throw new \Exception('cPanel error: ' . implode(', ', $data['errors']));
        }
        // return void — password tidak dikembalikan, tidak disimpan
    }

    /**
     * Cek apakah akun email sudah ada di cPanel.
     */
    public function emailExists(string $emailPrefix): bool
    {
        $response = Http::withHeaders([
            'Authorization' => "cpanel {$this->username}:{$this->apiToken}",
        ])
            ->withoutVerifying()
            ->get("{$this->host}/execute/Email/list_pops", [
                'domain' => $this->domain,
                'regex'  => "^{$emailPrefix}$",
            ]);

        if (!$response->successful()) {
            return false;
        }

        $data = $response->json();
        return !empty($data['data']);
    }
}
