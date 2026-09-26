<?php

namespace App\Services\Superadmin;

use App\Models\Superadmin\Settingwebsite;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingwebsiteService
{
    /**
     * Key .env yang tidak boleh diubah via UI demi keamanan session & koneksi DB.
     */
    private const PROTECTED_ENV_KEYS = [
        'APP_KEY',
        'SESSION_DRIVER',
        'SESSION_DOMAIN',
        'DB_CONNECTION',
        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME',
        'DB_PASSWORD',
    ];

    /**
     * Ambil (atau bentuk baru jika belum ada) row pengaturan website.
     * Tabel settingwebsites bersifat singleton (satu row saja).
     */
    public function getSetting(): Settingwebsite
    {
        return Settingwebsite::first() ?? new Settingwebsite;
    }

    /**
     * Perbarui informasi umum website (judul, deskripsi, favicon, logo).
     */
    public function update(Settingwebsite $setting, array $data, ?UploadedFile $favicon, ?UploadedFile $logo): Settingwebsite
    {
        return DB::transaction(function () use ($setting, $data, $favicon, $logo) {
            $setting->title = $data['title'];
            $setting->description = $data['description'] ?? null;

            if ($favicon) {
                if ($setting->favicon) {
                    Storage::disk('public')->delete($setting->favicon);
                }
                $setting->favicon = $favicon->store('settings', 'public');
            }

            if ($logo) {
                if ($setting->logo) {
                    Storage::disk('public')->delete($setting->logo);
                }
                $setting->logo = $logo->store('settings', 'public');
            }

            $setting->save();

            return $setting->fresh();
        });
    }

    /**
     * Simpan API Keys (Gemini / Anthropic) ke database.
     */
    public function updateApiKeys(Settingwebsite $setting, array $data): Settingwebsite
    {
        return DB::transaction(function () use ($setting, $data) {
            $setting->gemini_api_key = $data['gemini_api_key'] ?? '';
            $setting->anthropic_api_key = $data['anthropic_api_key'] ?? '';
            $setting->save();

            return $setting->fresh();
        });
    }

    /**
     * Simpan konfigurasi Urusin Secara Online (base URL + API key) ke database.
     */
    public function updateUrusinConfig(Settingwebsite $setting, array $data): Settingwebsite
    {
        return DB::transaction(function () use ($setting, $data) {
            $setting->urusin_base_url = isset($data['urusin_base_url']) ? rtrim($data['urusin_base_url'], '/') : null;
            $setting->urusin_api_key = $data['urusin_api_key'] ?? '';
            $setting->save();

            return $setting->fresh();
        });
    }

    /**
     * Baca isi file .env dan pecah menjadi baris comment/variable untuk ditampilkan di UI.
     *
     * @return array<int, array{type: string, raw?: string, key?: string, value?: string}>
     */
    public function getEnvRows(): array
    {
        $envPath = base_path('.env');
        $envContent = [];

        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                // Simpan komentar dan baris kosong
                if (str_starts_with(trim($line), '#') || trim($line) === '') {
                    $envContent[] = ['type' => 'comment', 'raw' => $line];

                    continue;
                }
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $envContent[] = [
                        'type' => 'variable',
                        'key' => trim($key),
                        'value' => trim($value),
                    ];
                }
            }
        }

        return $envContent;
    }

    /**
     * Perbarui file .env dari input form (key protected diabaikan), lalu clear config/cache.
     */
    public function updateEnv(array $envData): void
    {
        DB::transaction(function () use ($envData) {
            $envPath = base_path('.env');

            if (! file_exists($envPath)) {
                throw new \RuntimeException('File .env tidak ditemukan');
            }

            foreach (self::PROTECTED_ENV_KEYS as $pk) {
                unset($envData[$pk]);
            }

            $lines = file($envPath, FILE_IGNORE_NEW_LINES);
            $output = [];

            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#') || trim($line) === '') {
                    $output[] = $line;

                    continue;
                }
                if (str_contains($line, '=')) {
                    [$key] = explode('=', $line, 2);
                    $key = trim($key);
                    if (array_key_exists($key, $envData)) {
                        $value = (string) ($envData[$key] ?? '');
                        if ($value !== '' && str_contains($value, ' ') && ! str_starts_with($value, '"')) {
                            $value = '"'.$value.'"';
                        }
                        $output[] = $key.'='.$value;
                        unset($envData[$key]);

                        continue;
                    }
                }
                $output[] = $line;
            }

            file_put_contents($envPath, implode("\n", $output)."\n");

            $this->clearConfigCache();
        });
    }

    /**
     * Update flag maintenance per-role langsung ke file .env.
     *
     * @param  array{maintenance_data_entry?: string|null, maintenance_admin_umum?: string|null, maintenance_enumerator_api?: string|null}  $flags
     */
    public function updateMaintenance(array $flags): void
    {
        DB::transaction(function () use ($flags) {
            $envPath = base_path('.env');

            if (! file_exists($envPath)) {
                throw new \RuntimeException('File .env tidak ditemukan.');
            }

            $updates = [
                'MAINTENANCE_DATA_ENTRY' => ($flags['maintenance_data_entry'] ?? null) === 'on' ? 'true' : 'false',
                'MAINTENANCE_ADMIN_UMUM' => ($flags['maintenance_admin_umum'] ?? null) === 'on' ? 'true' : 'false',
                'MAINTENANCE_ENUMERATOR_API' => ($flags['maintenance_enumerator_api'] ?? null) === 'on' ? 'true' : 'false',
            ];

            $lines = file($envPath, FILE_IGNORE_NEW_LINES);
            $output = [];
            $handled = [];

            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#') || trim($line) === '') {
                    $output[] = $line;

                    continue;
                }
                if (str_contains($line, '=')) {
                    [$key] = explode('=', $line, 2);
                    $key = trim($key);
                    if (array_key_exists($key, $updates)) {
                        $output[] = $key.'='.$updates[$key];
                        $handled[$key] = true;

                        continue;
                    }
                }
                $output[] = $line;
            }

            // Append keys belum ada di .env
            foreach ($updates as $key => $value) {
                if (! isset($handled[$key])) {
                    $output[] = $key.'='.$value;
                }
            }

            file_put_contents($envPath, implode("\n", $output)."\n");

            $this->clearConfigCache();
        });
    }

    /**
     * Clear config & cache setelah menulis .env. Diabaikan jika artisan dibatasi (mis. shared hosting).
     */
    private function clearConfigCache(): void
    {
        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // Abaikan jika gagal di hosting dengan restricted artisan
        }
    }
}
