<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Superadmin\WaGatewayConfig;
use App\Services\KawuloHalalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * WaGatewayConfigController
 *
 * Controller untuk mengelola konfigurasi WhatsApp Gateway.
 */
class WaGatewayConfigController extends Controller
{
    /**
     * Display WA Gateway settings page.
     */
    public function index()
    {
        $configs = WaGatewayConfig::getAll();
        $healthStatus = $this->checkHealth();

        // Get devices from both sources
        $localDevices = \App\Models\Superadmin\WaDevice::where('status', 'connected')
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'hashed_id'])
            ->toArray();

        // Get devices from Kawalaku Gateway API
        $apiDevices = $this->getApiDevices();

        // Merge devices from both sources (API devices first, then local)
        $devices = array_merge($apiDevices, $localDevices);

        $pageTitle = 'Pengaturan WA Gateway';
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => url('superadmin')],
            ['title' => 'WA Gateway', 'url' => '#'],
            ['title' => 'Pengaturan', 'url' => null],
        ];

        return view('superadmin.wa-gateway-config.index', compact(
            'configs',
            'healthStatus',
            'devices',
            'pageTitle',
            'breadcrumbs'
        ));
    }

    /**
     * Get devices from Kawalaku Gateway API.
     */
    protected function getApiDevices(): array
    {
        try {
            /** @var KawuloHalalService $service */
            $service = app(KawuloHalalService::class);
            $result = $service->getDevices();

            if ($result['status'] && isset($result['data']['devices'])) {
                $apiDevices = [];
                foreach ($result['data']['devices'] as $device) {
                    $apiDevices[] = [
                        'id' => $device['id'] ?? 0,
                        'name' => ($device['name'] ?? 'Unknown') . ' (API)',
                        'phone' => $device['phone'] ?? null,
                        'hashed_id' => $device['id'] ?? $device['hashed_id'] ?? '',
                        'source' => 'api',
                    ];
                }
                return $apiDevices;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get devices from API: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Update WA Gateway settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'wa_gateway_url' => 'required|url',
            'base_url' => 'required|url',
            'api_key' => 'nullable|string',
            'default_media_url' => 'nullable|url',
            'enabled' => 'sometimes|boolean',
            'bypass_ssl' => 'sometimes|boolean',
        ]);

        try {
            // Update each configuration
            WaGatewayConfig::setValue(
                'wa_gateway_url',
                $validated['wa_gateway_url'],
                'Node.js Baileys service URL'
            );

            WaGatewayConfig::setValue(
                'base_url',
                $validated['base_url'],
                'Kawalaku Gateway Laravel API URL'
            );

            WaGatewayConfig::setValue(
                'api_key',
                $validated['api_key'] ?? '',
                'API Key untuk Kawalaku Gateway'
            );

            WaGatewayConfig::setValue(
                'default_media_url',
                $validated['default_media_url'] ?? '',
                'URL default untuk media/image notification'
            );

            WaGatewayConfig::setValue(
                'enabled',
                $request->has('enabled') ? '1' : '0',
                'Aktifkan/Nonaktifkan WhatsApp Gateway'
            );

            WaGatewayConfig::setValue(
                'bypass_ssl',
                $request->has('bypass_ssl') ? '1' : '0',
                'Bypass verifikasi SSL (untuk self-signed certificate)'
            );

            // Clear cache
            Cache::forget('wa_gateway_config');

            return redirect()
                ->back()
                ->with('success', 'Pengaturan berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('WaGatewayConfigController: Failed to update config', [
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Test connection to WA Gateway service.
     */
    public function testConnection(Request $request)
    {
        try {
            /** @var KawuloHalalService $service */
            $service = app(KawuloHalalService::class);
            $isHealthy = $service->healthCheck();

            return response()->json([
                'success' => $isHealthy,
                'message' => $isHealthy
                    ? 'Koneksi berhasil! WA Gateway service berjalan.'
                    : 'Koneksi gagal. Pastikan WA Gateway service berjalan.',
            ]);
        } catch (\Exception $e) {
            Log::error('WaGatewayConfigController: Test connection failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Check health status of WA Gateway.
     */
    protected function checkHealth(): array
    {
        try {
            /** @var KawuloHalalService $service */
            $service = app(KawuloHalalService::class);
            $isHealthy = $service->healthCheck();

            return [
                'status' => $isHealthy ? 'online' : 'offline',
                'message' => $isHealthy
                    ? 'WA Gateway service berjalan'
                    : 'WA Gateway service tidak dapat diakses',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send a test text message.
     * API: POST /api/v1/messages/send/text
     * Params: receiver (62xxx format), message
     */
    public function sendTestText(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string',
            'message' => 'required|string',
            'device_id' => 'nullable|string',
            'device_source' => 'nullable|string',
        ]);

        try {
            $deviceId = $validated['device_id'] ?? null;
            $deviceSource = $validated['device_source'] ?? 'local';

            // Get device if not provided
            if (!$deviceId) {
                if ($deviceSource === 'api') {
                    // Try to get from API devices
                    $apiDevices = $this->getApiDevices();
                    if (!empty($apiDevices)) {
                        $deviceId = $apiDevices[0]['hashed_id'] ?? null;
                    }
                } else {
                    // Get from local database
                    $localDevice = \App\Models\Superadmin\WaDevice::where('status', 'connected')->first();
                    if ($localDevice) {
                        $deviceId = $localDevice->hashed_id;
                    }
                }
            }

            /** @var KawuloHalalService $service */
            $service = app(KawuloHalalService::class);

            $result = $service->sendText(
                number: $validated['number'],
                message: $validated['message'],
                deviceId: $deviceId
            );

            if ($result['status']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan teks berhasil dikirim',
                    'data' => $result['data'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Gagal mengirim pesan',
            ], 500);

        } catch (\Exception $e) {
            Log::error('WaGatewayConfigController: Send test text failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a test media message.
     * API: POST /api/v1/messages/send/media
     * Params: receiver (62xxx format), media_type, media_url, caption (optional)
     */
    public function sendTestMedia(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string',
            'media_type' => 'required|in:image,video,audio,document',
            'media_url' => 'required|url',
            'caption' => 'nullable|string',
            'device_id' => 'nullable|string',
            'device_source' => 'nullable|string',
        ]);

        try {
            $deviceId = $validated['device_id'] ?? null;
            $deviceSource = $validated['device_source'] ?? 'local';

            // Get device if not provided
            if (!$deviceId) {
                if ($deviceSource === 'api') {
                    // Try to get from API devices
                    $apiDevices = $this->getApiDevices();
                    if (!empty($apiDevices)) {
                        $deviceId = $apiDevices[0]['hashed_id'] ?? null;
                    }
                } else {
                    // Get from local database
                    $localDevice = \App\Models\Superadmin\WaDevice::where('status', 'connected')->first();
                    if ($localDevice) {
                        $deviceId = $localDevice->hashed_id;
                    }
                }
            }

            /** @var KawuloHalalService $service */
            $service = app(KawuloHalalService::class);

            $result = $service->sendMedia(
                number: $validated['number'],
                mediaType: $validated['media_type'],
                url: $validated['media_url'],
                caption: $validated['caption'] ?? null,
                deviceId: $deviceId
            );

            if ($result['status']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan media berhasil dikirim',
                    'data' => $result['data'] ?? null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Gagal mengirim pesan',
            ], 500);

        } catch (\Exception $e) {
            Log::error('WaGatewayConfigController: Send test media failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
