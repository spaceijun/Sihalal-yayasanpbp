<?php

namespace App\Models\Superadmin;

use Illuminate\Database\Eloquent\Model;

/**
 * WaGatewayConfig Model
 *
 * Model untuk menyimpan konfigurasi WhatsApp Gateway.
 * Menggunakan key-value storage pattern.
 */
class WaGatewayConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_gateway_configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Default configuration values
     */
    public static function defaults(): array
    {
        return [
            'wa_gateway_url' => 'http://localhost:3000',
            'base_url' => 'http://kawalakugateway.test',
            'api_key' => '',
            'enabled' => true,
            'default_media_url' => 'https://kawulohalal.id/assets/logo.png',
            'bypass_ssl' => false,
        ];
    }

    /**
     * Get a configuration value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $config = self::where('key', $key)->first();
        return $config ? $config->value : ($default ?? ($config = self::defaults()[$key] ?? null));
    }

    /**
     * Set a configuration value
     */
    public static function setValue(string $key, $value, ?string $description = null): self
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
            ]
        );
    }

    /**
     * Get all configurations as array
     */
    public static function getAll(): array
    {
        $configs = self::all()->pluck('value', 'key')->toArray();
        $defaults = self::defaults();

        return array_merge($defaults, $configs);
    }

    /**
     * Get enabled status
     */
    public static function isEnabled(): bool
    {
        return (bool) self::getValue('enabled', true);
    }
}
