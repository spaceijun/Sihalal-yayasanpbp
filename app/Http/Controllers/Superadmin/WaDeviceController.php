<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Superadmin\WaDevice;
use App\Services\Superadmin\WaDeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

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
    public function index()
    {
        $statistics = $this->waDeviceService->getStatistics();

        $pageTitle = 'Kelola Perangkat WhatsApp';
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => url('superadmin')],
            ['title' => 'WA Gateway', 'url' => '#'],
            ['title' => 'Perangkat', 'url' => null],
        ];

        return view('superadmin.wa-devices.index', compact(
            'statistics',
            'pageTitle',
            'breadcrumbs'
        ));
    }

    /**
     * Return DataTables JSON for the devices listing.
     */
    public function data(Request $request)
    {
        $query = WaDevice::query()->select([
            'id', 'name', 'phone', 'status', 'last_connected_at', 'created_at', 'updated_at',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('phone_fmt', fn ($d) => $d->phone ?? '-')
            ->addColumn('status_badge', function ($d) {
                $class = $d->isConnected() ? 'adm-badge-success' : ($d->status === 'connecting' ? 'adm-badge-warning' : 'adm-badge-danger');

                return '<span class="adm-badge '.$class.'">'.e($d->getStatusText()).'</span>';
            })
            ->addColumn('last_connected_fmt', fn ($d) => $d->last_connected_at ? $d->last_connected_at->diffForHumans() : '-')
            ->addColumn('aksi', function ($d) {
                $showUrl = route('superadmin.wa-devices.show', $d->hashed_id);
                $connectDisabled = $d->isConnected() ? 'disabled' : '';
                $disconnectDisabled = ! $d->isConnected() ? 'disabled' : '';

                return '<div style="display:inline-flex;align-items:center;gap:8px;justify-content:center;">
                    <a href="'.$showUrl.'" class="adm-btn-secondary" style="width:32px;height:32px;padding:0;justify-content:center;border-radius:50%;" title="Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <button onclick="connectDevice(\''.$d->hashed_id.'\')" class="adm-btn-secondary" style="width:32px;height:32px;padding:0;justify-content:center;border-radius:50%;color:var(--adm-green);border-color:var(--adm-green-lt);background:var(--adm-green-lt);" title="Hubungkan" '.$connectDisabled.'>
                        <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </button>
                    <button onclick="disconnectDevice(\''.$d->hashed_id.'\')" class="adm-btn-secondary" style="width:32px;height:32px;padding:0;justify-content:center;border-radius:50%;color:var(--adm-red);border-color:var(--adm-red-lt);background:var(--adm-red-lt);" title="Putuskan" '.$disconnectDisabled.'>
                        <svg viewBox="0 0 24 24"><line x1="1" y1="1" x2="23" y2="23"/><path d="M16.7 11.73a5 5 0 0 0-7.54-.54l-2.77 2.77a5 5 0 0 0 7.07 7.07l1.11-1.11"/><path d="M8 12a5 5 0 0 0 7.54.54l2.73-2.73a5 5 0 0 0-7.07-7.07l-1.07 1.07"/></svg>
                    </button>
                    <button onclick="deleteDevice(\''.$d->hashed_id.'\', \''.e(addslashes($d->name)).'\')" class="adm-btn-secondary" style="width:32px;height:32px;padding:0;justify-content:center;border-radius:50%;color:var(--adm-text-muted);" title="Hapus">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['status_badge', 'aksi'])
            ->make(true);
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
