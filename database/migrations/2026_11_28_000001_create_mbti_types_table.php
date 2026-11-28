<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mbti_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 4)->unique();
            $table->string('temperament_code', 2)->index();
            $table->string('temperament_name');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mbti_types');
    }
};
