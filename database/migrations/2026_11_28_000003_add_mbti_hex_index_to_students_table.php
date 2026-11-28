<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->unsignedTinyInteger('mbti_hex_index')->nullable()->after('class_name')->index();
        });

        $hexMap = [
            'ENTJ' => 0,
            'ENTP' => 1,
            'ENFJ' => 2,
            'ENFP' => 3,
            'ESTJ' => 4,
            'ESTP' => 5,
            'ESFJ' => 6,
            'ESFP' => 7,
            'INTJ' => 8,
            'INTP' => 9,
            'INFJ' => 10,
            'INFP' => 11,
            'ISTJ' => 12,
            'ISTP' => 13,
            'ISFJ' => 14,
            'ISFP' => 15,
        ];

        $profiles = DB::table('student_mbti_profiles')
            ->join('mbti_types', 'mbti_types.id', '=', 'student_mbti_profiles.mbti_type_id')
            ->select(['student_mbti_profiles.student_id', 'mbti_types.code'])
            ->get();

        foreach ($profiles as $row) {
            $code = strtoupper(trim((string) $row->code));
            if (array_key_exists($code, $hexMap)) {
                DB::table('students')
                    ->where('id', $row->student_id)
                    ->update(['mbti_hex_index' => $hexMap[$code]]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->dropIndex(['mbti_hex_index']);
            $table->dropColumn('mbti_hex_index');
        });
    }
};
