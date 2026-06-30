<?php

namespace App\Models\Superadmin;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * WaDevice Model
 *
 * Model untuk menyimpan data perangkat WhatsApp Gateway.
 * Menggunakan Baileys library untuk koneksi WhatsApp.
 */
class WaDevice extends Model
{
    use HasHashedId;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_devices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'qr_code',
        'credentials',
        'status',
        'last_connected_at',
        'device_info',
        'reject_call',
        'available',
        'typing',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'credentials' => 'encrypted',
        'device_info' => 'array',
        'qr_code' => 'array',
        'last_connected_at' => 'datetime',
        'reject_call' => 'boolean',
        'available' => 'boolean',
        'typing' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'credentials',
    ];

    /**
     * Status constants
     */
    public const STATUS_DISCONNECTED = 'disconnected';
    public const STATUS_CONNECTING = 'connecting';
    public const STATUS_CONNECTED = 'connected';

    /**
     * Get all status options
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DISCONNECTED,
            self::STATUS_CONNECTING,
            self::STATUS_CONNECTED,
        ];
    }

    /**
     * Check if device is connected
     */
    public function isConnected(): bool
    {
        return $this->status === self::STATUS_CONNECTED;
    }

    /**
     * Check if device is connecting
     */
    public function isConnecting(): bool
    {
        return $this->status === self::STATUS_CONNECTING;
    }

    /**
     * Check if device is disconnected
     */
    public function isDisconnected(): bool
    {
        return $this->status === self::STATUS_DISCONNECTED;
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_CONNECTED => 'bg-success',
            self::STATUS_CONNECTING => 'bg-warning',
            self::STATUS_DISCONNECTED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status text for UI
     */
    public function getStatusText(): string
    {
        return match ($this->status) {
            self::STATUS_CONNECTED => 'Connected',
            self::STATUS_CONNECTING => 'Connecting',
            self::STATUS_DISCONNECTED => 'Disconnected',
            default => 'Unknown',
        };
    }

    /**
     * Get messages relationship
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WaMessage::class, 'wa_device_id');
    }

    /**
     * Check if reject call is enabled
     */
    public function isRejectCallEnabled(): bool
    {
        return $this->reject_call ?? false;
    }

    /**
     * Check if device is available
     */
    public function isAvailable(): bool
    {
        return $this->available ?? true;
    }

    /**
     * Check if typing indicator is enabled
     */
    public function isTypingEnabled(): bool
    {
        return $this->typing ?? true;
    }

    /**
     * Get all feature settings as array
     */
    public function getFeatures(): array
    {
        return [
            'reject_call' => $this->isRejectCallEnabled(),
            'available' => $this->isAvailable(),
            'typing' => $this->isTypingEnabled(),
        ];
    }

    /**
     * Get config payload for Baileys service
     */
    public function getBaileysConfig(): array
    {
        return [
            'reject_call' => $this->isRejectCallEnabled(),
            'available' => $this->isAvailable(),
            'typing' => $this->isTypingEnabled(),
        ];
    }
}
