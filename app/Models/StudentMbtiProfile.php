<?php

namespace App\Models;

use App\Observers\StudentMbtiProfileObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([StudentMbtiProfileObserver::class])]
class StudentMbtiProfile extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'mbti_type_id',
        'pct_e',
        'pct_i',
        'pct_n',
        'pct_s',
        'pct_t',
        'pct_f',
        'pct_j',
        'pct_p',
        'screenshot_url',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'pct_e' => 'integer',
        'pct_i' => 'integer',
        'pct_n' => 'integer',
        'pct_s' => 'integer',
        'pct_t' => 'integer',
        'pct_f' => 'integer',
        'pct_j' => 'integer',
        'pct_p' => 'integer',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<MbtiType, $this>
     */
    public function mbtiType(): BelongsTo
    {
        return $this->belongsTo(MbtiType::class);
    }

    /**
     * Return all 8 axis percentages keyed by single MBTI aspect letter.
     *
     * @return array<string, int>
     */
    public function toPercentageMap(): array
    {
        return [
            'E' => (int) $this->pct_e,
            'I' => (int) $this->pct_i,
            'N' => (int) $this->pct_n,
            'S' => (int) $this->pct_s,
            'T' => (int) $this->pct_t,
            'F' => (int) $this->pct_f,
            'J' => (int) $this->pct_j,
            'P' => (int) $this->pct_p,
        ];
    }
}
