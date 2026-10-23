<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * Limit public student queries to non-sensitive columns.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->select([
            'id',
            'nim',
            'name',
            'nickname',
            'class_name',
            'religion',
            'birth_place',
            'birth_date',
            'origin_city',
            'instagram_username',
            'hobbies',
            'bio_quote',
            'favorite_food_place',
            'spotify_song_url',
            'formal_photo_url',
            'casual_photo_url',
            'created_at',
            'updated_at',
        ]);
    }

    // Pilihan dari Google Form (bisa dipakai di Select Filament)
    public const CLASSES = ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'];

    public const RELIGIONS = [
        'Islam' => 'Islam',
        'Katolik' => 'Katolik',
        'Kristen Protestan' => 'Kristen Protestan',
        'Hindu' => 'Hindu',
        'Buddha' => 'Buddha',
        'Konghucu' => 'Konghucu',
    ];

    protected $fillable = [
        'nim',
        'name',
        'nickname',
        'class_name',
        'religion',
        'birth_place',
        'birth_date',
        'origin_city',
        'address',
        'whatsapp_no',
        'instagram_username',
        'hobbies',
        'bio_quote',
        'favorite_food_place',
        'spotify_song_url',
        'formal_photo_url',
        'casual_photo_url',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Otomatis ubah nomor WhatsApp ke format 628... saat disimpan.
     * 0821-4563-2896 / +62 821... / 62821... / 821...  =>  62821...
     */
    protected function whatsappNo(): Attribute
    {
        return Attribute::make(
            set: function (?string $value) {
                $digits = preg_replace('/\D+/', '', (string) $value);

                if ($digits === '') {
                    return '';
                }
                if (str_starts_with($digits, '620')) {
                    return '62'.substr($digits, 3);        // 62 0821... -> 62821...
                }
                if (str_starts_with($digits, '62')) {
                    return $digits;
                }
                if (str_starts_with($digits, '0')) {
                    return '62'.substr($digits, 1);
                }

                return '62'.$digits;
            }
        );
    }

    /**
     * Username Instagram disimpan bersih: tanpa @, tanpa spasi.
     * Jika yang ditempel adalah link lengkap, ambil username-nya saja.
     * Huruf besar/kecil dibiarkan seperti yang diketik.
     */
    protected function instagramUsername(): Attribute
    {
        return Attribute::make(
            set: function (?string $value) {
                $value = trim((string) $value);

                // https://www.instagram.com/noahjynh/?igsh=abc  =>  noahjynh
                if (preg_match('~instagram\.com/([^/?#\s]+)~i', $value, $m)) {
                    $value = $m[1];
                }

                return ltrim($value, '@');
            }
        );
    }
}
