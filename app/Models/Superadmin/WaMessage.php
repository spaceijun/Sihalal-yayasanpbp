<?php

namespace App\Models\Superadmin;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WaMessage Model
 *
 * Model untuk menyimpan riwayat pesan WhatsApp.
 */
class WaMessage extends Model
{
    use HasHashedId;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wa_device_id',
        'sender_number',
        'receiver_number',
        'message_template',
        'processed_message',
        'footer_message',
        'message_type',
        'media_url',
        'media_type',
        'media_caption',
        'status',
        'error_message',
        'message_source',
        'sent_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    /**
     * Message type constants
     */
    public const TYPE_TEXT = 'text';
    public const TYPE_MEDIA = 'media';

    /**
     * Source constants
     */
    public const SOURCE_WEBSITE = 'website';
    public const SOURCE_API = 'api';

    /**
     * Get all status options
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_SENT,
            self::STATUS_FAILED,
        ];
    }

    /**
     * Get all message types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_TEXT,
            self::TYPE_MEDIA,
        ];
    }

    /**
     * Get all sources
     */
    public static function getSources(): array
    {
        return [
            self::SOURCE_WEBSITE,
            self::SOURCE_API,
        ];
    }

    /**
     * Get the device that owns the message.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(WaDevice::class, 'wa_device_id');
    }

    /**
     * Check if message is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if message is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if message is sent
     */
    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * Check if message is failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-secondary',
            self::STATUS_PROCESSING => 'bg-warning',
            self::STATUS_SENT => 'bg-success',
            self::STATUS_FAILED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status text for UI
     */
    public function getStatusText(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SENT => 'Sent',
            self::STATUS_FAILED => 'Failed',
            default => 'Unknown',
        };
    }
}
