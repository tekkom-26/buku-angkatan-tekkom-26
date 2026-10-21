<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storage_files', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Storage location
            $table->string('disk')->default('r2');
            $table->text('path')->unique(); // full object key, e.g. "public/profile-aasokdaok/avatar.jpg"
            $table->string('prefix')->nullable()->index(); // top-level folder used for scanning, e.g. "profile-aasokdaok"
            $table->string('alias')->nullable()->unique(); // human friendly alias/slug for the file
            $table->boolean('is_directory')->default(false)->index();

            // Visibility / ownership
            $table->string('visibility')->default('private')->index(); // 'public' | 'private'
            $table->string('uploaded_by')->nullable(); // admin email, or 'public' for passcode uploads

            // File metadata
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable(); // bytes
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_files');
    }
};
