<?php

namespace App\Services\Superadmin;

use App\Models\Superadmin\WaDevice;
use App\Services\KawuloHalalService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * WaDeviceService - Service layer untuk WhatsApp Device Management
 *
 * Pattern: Service Layer dengan Dependency Injection
 * Handles all business logic untuk WhatsApp Gateway menggunakan Baileys
 */
class WaDeviceService
{
    /**
     * Cache prefix untuk device status
     */
    protected const CACHE_PREFIX = 'wa_device_';
    protected const CACHE_TTL = 300; // 5 minutes

    public function __construct(
        protected KawuloHalalService $kawuloHalal
    ) {}

    /**
     * Get all devices with pagination and filters
     */
    public function getAllDevices(array $filters = [], int $perPage = 15)
    {
        $query = WaDevice::query()->select([
            'id', 'name', 'phone', 'status',
            'last_connected_at', 'created_at', 'updated_at'
        ]);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get device by hashed ID
     */
    public function getDeviceByHashedId(string $hashedId): ?WaDevice
    {
        return WaDevice::findByHashedId($hashedId);
    }

    /**
     * Get device by ID
     */
    public function getDeviceById(int $id): ?WaDevice
    {
        return WaDevice::find($id);
    }

    /**
     * Create new device
     */
    public function createDevice(array $data): WaDevice
    {
        $device = WaDevice::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'status' => WaDevice::STATUS_DISCONNECTED,
            'device_info' => null,
            'reject_call' => $data['reject_call'] ?? false,
            'available' => $data['available'] ?? true,
            'typing' => $data['typing'] ?? true,
        ]);

        return $device;
    }

    /**
     * Update device
     */
    public function updateDevice(WaDevice $device, array $data): WaDevice
    {
        $device->update($data);
        $this->clearDeviceCache($device->hashed_id);
        return $device;
    }

    /**
     * Delete device
     */
    public function deleteDevice(WaDevice $device): bool
    {
        // Disconnect first if connected
        if ($device->isConnected()) {
            $this->disconnectDevice($device);
        }

        $this->clearDeviceCache($device->hashed_id);
        return $device->delete();
    }

    /**
     * Generate QR Code for device connection
     * Mengirim request ke Baileys service untuk generate QR code
     */
    public function generateQrCode(WaDevice $device): array
    {
        try {
            // Update status to connecting
            $device->update(['status' => WaDevice::STATUS_CONNECTING]);

            // Call Baileys service API to start pairing
            $response = $this->kawuloHalal->connectDevice($device->hashed_id);

            if ($response['status']) {
                $data = $response['data'] ?? [];

                Log::info('WaDeviceService: QR code generation response', [
                    'device_id' => $device->hashed_id,
                    'response' => $data,
                ]);

                // Save QR code if returned
                if (isset($data['qr_code'])) {
                    $device->update(['qr_code' => ['qr' => $data['qr_code']]]);
                }

                return [
                    'success' => true,
                    'qr_code' => $data['qr_code'] ?? null,
                    'message' => $data['message'] ?? 'QR Code generated. Scan with WhatsApp.',
                ];
            }

            // If failed, return the error
            return [
                'success' => false,
                'message' => $response['error'] ?? 'Failed to generate QR code',
            ];

        } catch (\Exception $e) {
            Log::error('WaDeviceService: Failed to generate QR code', [
                'device_id' => $device->hashed_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Connect device to WhatsApp
     */
    public function connectDevice(WaDevice $device): array
    {
        try {
            $device->update(['status' => WaDevice::STATUS_CONNECTING]);

            $response = $this->kawuloHalal->connectDevice($device->hashed_id);

            if ($response['status']) {
                $device->update([
                    'status' => WaDevice::STATUS_CONNECTING,
                    'last_connected_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Device connection initiated. Please scan QR code.',
                ];
            }

            return [
                'success' => false,
                'message' => $response['error'] ?? 'Failed to connect device',
            ];

        } catch (\Exception $e) {
            Log::error('WaDeviceService: Connect device error', [
                'device_id' => $device->hashed_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Disconnect device from WhatsApp
     */
    public function disconnectDevice(WaDevice $device): array
    {
        try {
            $this->kawuloHalal->disconnectDevice($device->hashed_id);

            $device->update([
                'status' => WaDevice::STATUS_DISCONNECTED,
                'qr_code' => null,
            ]);

            $this->clearDeviceCache($device->hashed_id);

            return [
                'success' => true,
                'message' => 'Device disconnected successfully',
            ];

        } catch (\Exception $e) {
            Log::error('WaDeviceService: Disconnect device error', [
                'device_id' => $device->hashed_id,
                'error' => $e->getMessage(),
            ]);

            // Still update local status even if remote fails
            $device->update([
                'status' => WaDevice::STATUS_DISCONNECTED,
                'qr_code' => null,
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Force reconnect (delete session and reconnect)
     */
    public function forceReconnect(WaDevice $device): array
    {
        try {
            $response = $this->kawuloHalal->forceReconnect($device->hashed_id);

            if ($response['status']) {
                $device->update([
                    'status' => WaDevice::STATUS_CONNECTING,
                    'qr_code' => null,
                ]);

                return [
                    'success' => true,
                    'message' => 'Device reconnection initiated. Please scan QR code again.',
                ];
            }

            return [
                'success' => false,
                'message' => $response['error'] ?? 'Failed to reconnect device',
            ];

        } catch (\Exception $e) {
            Log::error('WaDeviceService: Force reconnect error', [
                'device_id' => $device->hashed_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get device status
     */
    public function getDeviceStatus(WaDevice $device): array
    {
        // Check cache first
        $cacheKey = self::CACHE_PREFIX . $device->hashed_id;
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        try {
            $response = $this->kawuloHalal->getDeviceStatus($device->hashed_id);

            if ($response['status']) {
                $data = $response['data'] ?? [];

                $status = [
                    'success' => true,
                    'status' => $data['status'] ?? $device->status,
                    'connected' => $data['connected'] ?? false,
                    'phone' => $data['user']['phone'] ?? $device->phone,
                    'user' => $data['user'] ?? null,
                ];

                // Cache the status
                Cache::put($cacheKey, $status, self::CACHE_TTL);

                // Update local device if status changed
                if (isset($data['status'])) {
                    $updateData = ['status' => $data['status']];
                    if (isset($data['user']['phone'])) {
                        $updateData['phone'] = $data['user']['phone'];
                    }
                    $device->update($updateData);
                }

                return $status;
            }

        } catch (\Exception $e) {
            Log::warning('WaDeviceService: Failed to get device status', [
                'device_id' => $device->hashed_id,
                'error' => $e->getMessage(),
            ]);
        }

        // Return current database status
        return [
            'success' => true,
            'status' => $device->status,
            'connected' => $device->isConnected(),
            'phone' => $device->phone,
        ];
    }

    /**
     * Get QR code status
     */
    public function getQrStatus(WaDevice $device): array
    {
        try {
            $response = $this->kawuloHalal->getQrStatus($device->hashed_id);

            if ($response['status']) {
                $data = $response['data'] ?? [];

                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'waiting',
                    'qr_code' => $data['qr_code'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $response['error'] ?? 'Failed to get QR status',
            ];

        } catch (\Exception $e) {
            Log::error('WaDeviceService: Get QR status error', [
                'device_id' => $device->hashed_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update device features (reject_call, available, typing)
     */
    public function updateDeviceFeatures(WaDevice $device, array $features): array
    {
        try {
            $response = $this->kawuloHalal->updateDeviceFeatures($device->hashed_id, $features);

            if ($response['status']) {
                // Update local device
                $device->update([
                    'reject_call' => $features['reject_call'] ?? $device->reject_call,
                    'available' => $features['available'] ?? $device->available,
                    'typing' => $features['typing'] ?? $device->typing,
                ]);

                return [
                    'success' => true,
                    'message' => 'Features updated successfully',
                    'features' => $device->getFeatures(),
                ];
            }

            return [
                'success' => false,
                'message' => $response['error'] ?? 'Failed to update features',
            ];

        } catch (\Exception $e) {
            Log::error('WaDeviceService: Update features error', [
                'device_id' => $device->hashed_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get device features
     */
    public function getDeviceFeatures(WaDevice $device): array
    {
        try {
            $response = $this->kawuloHalal->getDeviceFeatures($device->hashed_id);

            if ($response['status']) {
                return [
                    'success' => true,
                    'features' => $response['data']['data'] ?? $device->getFeatures(),
                ];
            }

        } catch (\Exception $e) {
            Log::warning('WaDeviceService: Get features error', [
                'device_id' => $device->hashed_id,
                'error' => $e->getMessage(),
            ]);
        }

        // Return local features
        return [
            'success' => true,
            'features' => $device->getFeatures(),
        ];
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_devices' => WaDevice::count(),
            'connected' => WaDevice::where('status', WaDevice::STATUS_CONNECTED)->count(),
            'connecting' => WaDevice::where('status', WaDevice::STATUS_CONNECTING)->count(),
            'disconnected' => WaDevice::where('status', WaDevice::STATUS_DISCONNECTED)->count(),
        ];
    }

    /**
     * Clear device cache
     */
    protected function clearDeviceCache(string $hashedId): void
    {
        Cache::forget(self::CACHE_PREFIX . $hashedId);
    }

    /**
     * Sync device status with Baileys service
     */
    public function syncDeviceStatus(WaDevice $device): void
    {
        $status = $this->getDeviceStatus($device);

        if ($status['success']) {
            $device->update([
                'status' => $status['status'] ?? $device->status,
                'phone' => $status['phone'] ?? $device->phone,
            ]);

            $this->clearDeviceCache($device->hashed_id);
        }
    }
}
