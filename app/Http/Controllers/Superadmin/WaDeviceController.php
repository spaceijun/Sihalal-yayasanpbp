<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Superadmin\WaDeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * WaDeviceController
 *
 * Controller untuk manajemen perangkat WhatsApp Gateway di dashboard superadmin.
 * Pattern: RESTful Controller dengan Service Layer
 */
class WaDeviceController extends Controller
{
    public function __construct(
        protected WaDeviceService $waDeviceService
    ) {}

    /**
     * Display a listing of devices.
     */
    public function index(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'search' => $request->get('search'),
        ];

        $devices = $this->waDeviceService->getAllDevices($filters, 15);
        $statistics = $this->waDeviceService->getStatistics();

        $pageTitle = 'Kelola Perangkat WhatsApp';
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => url('superadmin')],
            ['title' => 'WA Gateway', 'url' => '#'],
            ['title' => 'Perangkat', 'url' => null],
        ];

        return view('superadmin.wa-devices.index', compact(
            'devices',
            'statistics',
            'pageTitle',
            'breadcrumbs',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new device.
     */
    public function create()
    {
        $pageTitle = 'Tambah Perangkat WhatsApp';
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => url('superadmin')],
            ['title' => 'WA Gateway', 'url' => '#'],
            ['title' => 'Perangkat', 'url' => route('superadmin.wa-devices.index')],
            ['title' => 'Tambah', 'url' => null],
        ];

        return view('superadmin.wa-devices.create', compact('pageTitle', 'breadcrumbs'));
    }

    /**
     * Store a newly created device in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $device = $this->waDeviceService->createDevice([
                'name' => $validated['name'],
            ]);

            return redirect()
                ->route('superadmin.wa-devices.show', $device->hashed_id)
                ->with('success', 'Perangkat berhasil ditambahkan. Silakan scan QR code untuk menghubungkan.');
        } catch (\Exception $e) {
            Log::error('WaDeviceController: Failed to create device', [
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan perangkat: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified device.
     */
    public function show(string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return redirect()
                ->route('superadmin.wa-devices.index')
                ->with('error', 'Perangkat tidak ditemukan');
        }

        $status = $this->waDeviceService->getDeviceStatus($device);
        $features = $this->waDeviceService->getDeviceFeatures($device);

        $pageTitle = 'Detail Perangkat: ' . $device->name;
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => url('superadmin')],
            ['title' => 'WA Gateway', 'url' => '#'],
            ['title' => 'Perangkat', 'url' => route('superadmin.wa-devices.index')],
            ['title' => $device->name, 'url' => null],
        ];

        return view('superadmin.wa-devices.show', compact(
            'device',
            'status',
            'features',
            'pageTitle',
            'breadcrumbs'
        ));
    }

    /**
     * Show the form for editing the specified device.
     */
    public function edit(string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return redirect()
                ->route('superadmin.wa-devices.index')
                ->with('error', 'Perangkat tidak ditemukan');
        }

        $pageTitle = 'Edit Perangkat: ' . $device->name;
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => url('superadmin')],
            ['title' => 'WA Gateway', 'url' => '#'],
            ['title' => 'Perangkat', 'url' => route('superadmin.wa-devices.index')],
            ['title' => $device->name, 'url' => route('superadmin.wa-devices.show', $device->hashed_id)],
            ['title' => 'Edit', 'url' => null],
        ];

        return view('superadmin.wa-devices.edit', compact(
            'device',
            'pageTitle',
            'breadcrumbs'
        ));
    }

    /**
     * Update the specified device in storage.
     */
    public function update(Request $request, string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return redirect()
                ->route('superadmin.wa-devices.index')
                ->with('error', 'Perangkat tidak ditemukan');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $this->waDeviceService->updateDevice($device, [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
            ]);

            return redirect()
                ->route('superadmin.wa-devices.show', $device->hashed_id)
                ->with('success', 'Perangkat berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('WaDeviceController: Failed to update device', [
                'device_id' => $hashedId,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui perangkat: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified device from storage.
     */
    public function destroy(string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan',
            ], 404);
        }

        try {
            $this->waDeviceService->deleteDevice($device);

            return response()->json([
                'success' => true,
                'message' => 'Perangkat berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('WaDeviceController: Failed to delete device', [
                'device_id' => $hashedId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus perangkat: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API Actions
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Generate QR code for device connection.
     * Returns QR code as base64 data URL.
     */
    public function generateQr(string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan',
            ], 404);
        }

        $result = $this->waDeviceService->generateQrCode($device);

        // Generate QR code as base64 data URL
        if ($result['success'] && isset($result['qr_code'])) {
            try {
                $qrCodeDataUrl = \App\Services\Superadmin\QrCodeService::generateDataUrl($result['qr_code']);
                $result['qr_code_data_url'] = $qrCodeDataUrl;
            } catch (\Exception $e) {
                // If QR generation fails, still return success with raw QR code
                Log::warning('Failed to generate QR code image: ' . $e->getMessage());
            }
        }

        return response()->json($result);
    }

    /**
     * Connect device (initiate QR code generation).
     */
    public function connect(string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan',
            ], 404);
        }

        $result = $this->waDeviceService->connectDevice($device);

        return response()->json($result);
    }

    /**
     * Disconnect device.
     */
    public function disconnect(string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan',
            ], 404);
        }

        $result = $this->waDeviceService->disconnectDevice($device);

        return response()->json($result);
    }

    /**
     * Force reconnect device.
     */
    public function forceReconnect(string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan',
            ], 404);
        }

        $result = $this->waDeviceService->forceReconnect($device);

        return response()->json($result);
    }

    /**
     * Get device status.
     */
    public function getStatus(string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan',
            ], 404);
        }

        $result = $this->waDeviceService->getDeviceStatus($device);

        return response()->json($result);
    }

    /**
     * Get QR status.
     */
    public function getQrStatus(string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan',
            ], 404);
        }

        $result = $this->waDeviceService->getQrStatus($device);

        return response()->json($result);
    }

    /**
     * Update device features.
     */
    public function updateFeatures(Request $request, string $hashedId)
    {
        $device = $this->waDeviceService->getDeviceByHashedId($hashedId);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'reject_call' => 'sometimes|boolean',
            'available' => 'sometimes|boolean',
            'typing' => 'sometimes|boolean',
        ]);

        $result = $this->waDeviceService->updateDeviceFeatures($device, $validated);

        return response()->json($result);
    }

    /**
     * Get statistics.
     */
    public function statistics()
    {
        $statistics = $this->waDeviceService->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }
}
