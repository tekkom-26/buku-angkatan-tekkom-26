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
        Schema::table('storage_files', function (Blueprint $table) {
            $table->dropIndex(['is_directory']);
            $table->dropColumn('is_directory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_files', function (Blueprint $table) {
            $table->boolean('is_directory')->default(false)->index();
        });
    }
};
