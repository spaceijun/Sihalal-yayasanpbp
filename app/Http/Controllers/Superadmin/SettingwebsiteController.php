<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Integrasi\UrusinService;
use App\Services\Superadmin\SettingwebsiteService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingwebsiteController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private SettingwebsiteService $service) {}

    /**
     * Display the website settings page (tabs: website, environment, maintenance, api keys).
     */
    public function index(): View
    {
        $setting = $this->service->getSetting();
        $envContent = $this->service->getEnvRows();
        $routePrefix = $this->routePrefix();

        $maintenanceDataEntry = config('app.maintenance_data_entry', false);
        $maintenanceAdminUmum = config('app.maintenance_admin_umum', false);
        $maintenanceEnumeratorApi = config('app.maintenance_enumerator_api', false);

        // API Keys dari DB
        $geminiApiKey = $setting->gemini_api_key ?? '';
        $anthropicApiKey = $setting->anthropic_api_key ?? '';
        $urusinBaseUrl = $setting->urusin_base_url ?? '';
        $urusinApiKey = $setting->urusin_api_key ?? '';

        return view('superadmin.settingwebsite.index', compact(
            'setting',
            'envContent',
            'routePrefix',
            'maintenanceDataEntry',
            'maintenanceAdminUmum',
            'maintenanceEnumeratorApi',
            'geminiApiKey',
            'anthropicApiKey',
            'urusinBaseUrl',
            'urusinApiKey',
        ));
    }

    /**
     * Update general website info (title, description, favicon, logo).
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg,gif|max:2048',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048',
        ]);

        try {
            $setting = $this->service->getSetting();
            $this->service->update(
                $setting,
                $request->only(['title', 'description']),
                $request->file('favicon'),
                $request->file('logo'),
            );

            return redirect()->back()->with('success', 'Pengaturan website berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui pengaturan: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Update the .env file from the "Environment" tab.
     */
    public function updateEnv(Request $request): RedirectResponse
    {
        try {
            $this->service->updateEnv((array) $request->input('env', []));

            return redirect()->route($this->routePrefix().'.settings.index')
                ->with('success', 'Konfigurasi .env berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui .env: '.$e->getMessage());
        }
    }

    /**
     * Update flag maintenance per-role langsung ke file .env.
     * Dipanggil dari tab Maintenance di halaman Setting Website.
     */
    public function updateMaintenance(Request $request): RedirectResponse
    {
        $request->validate([
            'maintenance_data_entry' => 'nullable|in:on,off',
            'maintenance_admin_umum' => 'nullable|in:on,off',
            'maintenance_enumerator_api' => 'nullable|in:on,off',
        ]);

        try {
            $this->service->updateMaintenance($request->only([
                'maintenance_data_entry',
                'maintenance_admin_umum',
                'maintenance_enumerator_api',
            ]));

            return redirect()->route($this->routePrefix().'.settings.index')
                ->with('success', 'Pengaturan maintenance berhasil diperbarui.')
                ->with('_active_tab', 'maintenance');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal memperbarui maintenance: '.$e->getMessage())
                ->with('_active_tab', 'maintenance');
        }
    }

    /**
     * Simpan API Keys ke database (bukan .env).
     */
    public function updateApiKeys(Request $request): RedirectResponse
    {
        $request->validate([
            'gemini_api_key' => 'nullable|string|max:500',
            'anthropic_api_key' => 'nullable|string|max:500',
        ]);

        try {
            $setting = $this->service->getSetting();
            $this->service->updateApiKeys($setting, $request->only(['gemini_api_key', 'anthropic_api_key']));

            return redirect()->route($this->routePrefix().'.settings.index')
                ->with('success', 'API Keys berhasil disimpan ke database.')
                ->with('_active_tab', 'apikeys');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal menyimpan API Keys: '.$e->getMessage())
                ->with('_active_tab', 'apikeys');
        }
    }

    /**
     * Simpan konfigurasi Urusin Secara Online (base URL + API key) ke database.
     * Lihat .agent/workflows/data-entry-integrasi.md §3 untuk spesifikasi API-nya.
     */
    public function updateUrusinConfig(Request $request): RedirectResponse
    {
        $request->validate([
            'urusin_base_url' => 'nullable|url|max:255',
            'urusin_api_key' => 'nullable|string|max:500',
        ]);

        try {
            $setting = $this->service->getSetting();
            $this->service->updateUrusinConfig($setting, $request->only(['urusin_base_url', 'urusin_api_key']));

            return redirect()->route($this->routePrefix().'.settings.index')
                ->with('success', 'Konfigurasi Urusin Secara Online berhasil disimpan.')
                ->with('_active_tab', 'apikeys');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal menyimpan konfigurasi Urusin Secara Online: '.$e->getMessage())
                ->with('_active_tab', 'apikeys');
        }
    }

    /**
     * Test koneksi ke Urusin Secara Online (AJAX) — panggil GET /api/v1/me dengan
     * konfigurasi yang baru saja disimpan.
     */
    public function testUrusinConnection(UrusinService $urusinService): JsonResponse
    {
        if (! $urusinService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Base URL dan API Key belum diisi. Simpan konfigurasi terlebih dahulu.',
            ], 422);
        }

        $result = $urusinService->me();

        if (! $result['status']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Gagal terhubung ke Urusin Secara Online.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Koneksi berhasil.',
            'data' => $result['data'],
            'mode' => $urusinService->detectMode(),
        ]);
    }
}
