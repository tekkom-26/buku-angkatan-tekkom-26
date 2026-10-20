<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    // Laravel biasanya menebak nama tabel "app_configs",
    // jadi kita beri tahu nama yang benar.
    protected $table = 'app_config';

    protected $fillable = ['key', 'value', 'description'];
}
