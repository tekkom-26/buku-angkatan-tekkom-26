<?php

namespace App\Models;

use App\Services\AppConfigService;
use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    // Laravel biasanya menebak nama tabel "app_configs",
    // jadi kita beri tahu nama yang benar.
    protected $table = 'app_config';

    protected $fillable = ['key', 'value', 'description'];

    protected static function booted(): void
    {
        static::saved(function (self $config): void {
            app(AppConfigService::class)->forget($config->key);
        });

        static::deleted(function (self $config): void {
            app(AppConfigService::class)->forget($config->key);
        });
    }
}
