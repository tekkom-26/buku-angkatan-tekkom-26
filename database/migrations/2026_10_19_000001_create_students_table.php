<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // === Dari Google Form (semua wajib) ===
            $table->string('nim')->unique();          // unique otomatis membuat index
            $table->string('name');                   // Nama Lengkap (sesuai data resmi)
            $table->string('nickname');               // Nama Panggilan
            $table->string('class_name', 1);          // Kelas: A, B, C, D
            $table->string('religion');               // Agama
            $table->string('birth_place');            // Tempat Lahir
            $table->date('birth_date');               // Tanggal Lahir
            $table->string('origin_city');            // Kota Asal / Domisili Asal
            $table->text('address');                  // Alamat Kost (Sensitif/Admin Only)
            $table->string('whatsapp_no', 20);        // Tersimpan: 628... (Sensitif/Admin Only)
            $table->string('instagram_username');     // Tersimpan: namaigkamu (tanpa @)

            // === Tambahan (semua wajib) ===
            $table->string('hobbies');                // contoh: "membaca, futsal, memasak"
            $table->text('bio_quote');                // Quotes
            $table->string('favorite_food_place');    // Tempat makan favorit
            $table->string('spotify_song_url', 500);  // Link lagu Spotify
            $table->string('formal_photo_url', 500);  // URL publik Cloudflare R2
            $table->string('casual_photo_url', 500);  // URL publik Cloudflare R2

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
