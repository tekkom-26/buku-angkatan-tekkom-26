<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_mbti_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->foreignId('mbti_type_id')->constrained('mbti_types')->cascadeOnDelete();
            $table->unsignedTinyInteger('pct_e');
            $table->unsignedTinyInteger('pct_i');
            $table->unsignedTinyInteger('pct_n');
            $table->unsignedTinyInteger('pct_s');
            $table->unsignedTinyInteger('pct_t');
            $table->unsignedTinyInteger('pct_f');
            $table->unsignedTinyInteger('pct_j');
            $table->unsignedTinyInteger('pct_p');
            $table->text('screenshot_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_mbti_profiles');
    }
};
