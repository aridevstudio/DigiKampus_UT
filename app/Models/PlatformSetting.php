<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PlatformSetting extends Model
{
    use HasFactory;

    public const KEY_COURSE_SERVICE_FEE = 'course_service_fee';
    public const DEFAULT_COURSE_SERVICE_FEE = 5000;

    protected $table = 'platform_settings';

    protected $fillable = [
        'key',
        'value',
        'value_type',
        'description',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        if (!Schema::hasTable('platform_settings')) {
            return $default;
        }

        $setting = static::query()->where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->value_type) {
            'int' => (int) $setting->value,
            'float' => (float) $setting->value,
            'bool' => filter_var($setting->value, FILTER_VALIDATE_BOOL),
            default => $setting->value,
        };
    }

    public static function setValue(string $key, mixed $value, string $valueType = 'string', ?string $description = null): self
    {
        if (!Schema::hasTable('platform_settings')) {
            throw new \RuntimeException('Tabel platform_settings belum tersedia.');
        }

        return static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'value_type' => $valueType,
                'description' => $description,
            ]
        );
    }

    public static function getCourseServiceFee(): int
    {
        return max(0, (int) static::getValue(
            self::KEY_COURSE_SERVICE_FEE,
            self::DEFAULT_COURSE_SERVICE_FEE
        ));
    }
}
