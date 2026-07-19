<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FeatureFlag extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
        'metadata' => 'array',
    ];

    public static function isEnabled(string $key): bool
    {
        // Default to true or database value
        return Cache::rememberForever("feature_flag:{$key}", function () use ($key) {
            $flag = self::where('key', $key)->first();
            return $flag ? (bool) $flag->is_enabled : true;
        });
    }

    public static function isDisabled(string $key): bool
    {
        return ! self::isEnabled($key);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget("feature_flag:{$model->key}");
        });

        static::deleted(function ($model) {
            Cache::forget("feature_flag:{$model->key}");
        });
    }
}
