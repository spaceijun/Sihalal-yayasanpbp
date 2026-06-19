<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\Superadmin\Settingwebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingwebsiteController extends Controller
{
    use HasRoutePrefix;
    public function index()
    {
        $setting     = Settingwebsite::first() ?? new Settingwebsite();
        $envContent  = $this->getEnv();
        $routePrefix = $this->routePrefix();

        return view('superadmin.settingwebsite.index', compact('setting', 'envContent', 'routePrefix'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg,gif|max:2048',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048',
        ]);

        $setting = Settingwebsite::first();
        if (!$setting) {
            $setting = new Settingwebsite();
        }

        $setting->title = $request->title;
        $setting->description = $request->description;

        // Mengelola upload favicon
        if ($request->hasFile('favicon')) {
            // Hapus favicon lama jika ada
            if ($setting->favicon) {
                Storage::disk('public')->delete($setting->favicon);
            }

            $faviconPath = $request->file('favicon')->store('settings', 'public');
            $setting->favicon = $faviconPath;
        }

        // Mengelola upload logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }

            $logoPath = $request->file('logo')->store('settings', 'public');
            $setting->logo = $logoPath;
        }

        $setting->save();

        return redirect()->back()->with('success', 'Pengaturan website berhasil diperbarui');
    }


    public function getEnv()
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
                        'type'  => 'variable',
                        'key'   => trim($key),
                        'value' => trim($value),
                    ];
                }
            }
        }

        return $envContent;
    }

    public function updateEnv(Request $request)
    {
        $envPath = base_path('.env');
        $envData = $request->input('env', []);

        if (!file_exists($envPath)) {
            return redirect()->back()->with('error', 'File .env tidak ditemukan');
        }

        $protectedKeys = [
            'APP_KEY',           // Jika berubah, semua session/enkripsi rusak
            'SESSION_DRIVER',    // Jika berubah, session aktif hilang
            'SESSION_DOMAIN',    // Jika berubah, cookie tidak terbaca
            'DB_CONNECTION',     // Jika berubah, koneksi DB putus
            'DB_HOST',
            'DB_PORT',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
        ];

        foreach ($protectedKeys as $pk) {
            unset($envData[$pk]);
        }
        $lines  = file($envPath, FILE_IGNORE_NEW_LINES);
        $output = [];

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#') || trim($line) === '') {
                $output[] = $line;
                continue;
            }
            if (str_contains($line, '=')) {
                [$key] = explode('=', $line, 2);
                $key   = trim($key);
                if (array_key_exists($key, $envData)) {
                    $value = $envData[$key];
                    if ($value !== '' && str_contains($value, ' ') && !str_starts_with($value, '"')) {
                        $value = '"' . $value . '"';
                    }
                    $output[] = $key . '=' . $value;
                    unset($envData[$key]);
                    continue;
                }
            }
            $output[] = $line;
        }

        file_put_contents($envPath, implode("\n", $output) . "\n");

        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // Abaikan jika gagal (production mungkin restrict artisan)
        }

        return redirect()->route('superadmin.settings.index')
            ->with('success', 'Konfigurasi .env berhasil diperbarui');
    }
}
