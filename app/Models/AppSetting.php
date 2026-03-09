<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'name',
        'value',
        'description',
        'type',
        'options',
        'is_public'
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean'
    ];

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        // Cache for 5 minutes (300 seconds) instead of 1 hour
        return Cache::remember("app_setting.{$key}", 300, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value by key
     */
    public static function set(string $key, $value): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            static::create([
                'key' => $key,
                'name' => ucwords(str_replace('_', ' ', $key)),
                'value' => $value,
                'type' => 'text'
            ]);
        }

        // Clear cache
        Cache::forget("app_setting.{$key}");
    }

    /**
     * Delete setting and clear cache
     */
    public function delete()
    {
        Cache::forget("app_setting.{$this->key}");
        return parent::delete();
    }

    /**
     * Update setting and clear cache
     */
    public function update(array $attributes = [], array $options = [])
    {
        $result = parent::update($attributes, $options);
        Cache::forget("app_setting.{$this->key}");
        return $result;
    }

    /**
     * Get WhatsApp admin phone number
     */
    public static function getAdminWhatsApp(): string
    {
        return static::get('admin_whatsapp_number', '6281234567890');
    }

    /**
     * Get company/app name
     */
    public static function getAppName(): string
    {
        return static::get('company_name', 'Lumin Park Housing');
    }

    /**
     * Get all public settings (accessible by users)
     */
    public static function getPublicSettings(): array
    {
        // Cache for 5 minutes (300 seconds) instead of 1 hour
        return Cache::remember('public_settings', 300, function () {
            return static::where('is_public', true)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("app_setting.{$key}");
        }
        Cache::forget('public_settings');
    }

    /**
     * Boot method to handle cache clearing
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when a setting is saved (created or updated)
        static::saved(function ($setting) {
            Cache::forget("app_setting.{$setting->key}");
            Cache::forget('public_settings');
        });

        // Clear cache when a setting is deleted
        static::deleted(function ($setting) {
            Cache::forget("app_setting.{$setting->key}");
            Cache::forget('public_settings');
        });
    }
}
