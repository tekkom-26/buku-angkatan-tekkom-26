<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MbtiType extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'temperament_code',
        'temperament_name',
        'title',
        'description',
    ];

    /**
     * @return HasMany<StudentMbtiProfile, $this>
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(StudentMbtiProfile::class);
    }
}
