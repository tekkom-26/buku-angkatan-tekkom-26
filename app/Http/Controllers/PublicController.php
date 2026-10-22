<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Display the landing page (Buku Angkatan).
     */
    public function index(Request $request): View|JsonResponse
    {
        $data=[
            // ── Hero ───────────────────────────────────────────────
            'hero_title'       => 'Ascent',
            'hero_subtitle'    => 'Teknik Komputer 2026',

            // ── Statistics ─────────────────────────────────────────
            'total_students'   => 207,
            'per_class'        => [
                ['class_name' => 'A', 'total' => 52],
                ['class_name' => 'B', 'total' => 51],
                ['class_name' => 'C', 'total' => 52],
                ['class_name' => 'D', 'total' => 52],
            ],

            // ── Ascent Philosophy ──────────────────────────────────
            'ascent_narrative' => [
                'tagline'  => 'Naik Bersama, Capai Bersama',
                'body'     => '"Ascent" bukan sekadar nama — ia adalah semangat. Setiap langkah kita ke atas adalah langkah yang kita ambil bersama. Dari titik nol di kampus Tembalang hingga puncak pencapaian tertinggi, angkatan Teknik Komputer 2026 berkomitmen untuk saling menarik ke atas, bukan saling menarik ke bawah.',
                'vision'   => 'Menjadi angkatan yang dikenal tidak hanya karena prestasi akademik, tetapi juga karena kekuatan karakter, kebersamaan yang autentik, dan dampak positif bagi lingkungan sekitar.',
            ],

            // ── Sejarah / Timeline ────────────────────────────────
            'timeline'         => [
                [
                    'period'    => 'Agustus 2024',
                    'title'     => 'Awal Mula',
                    'desc'      => '207 mahasiswa dari berbagai daerah berkumpul di kampus UNDIP Tembalang, memulai perjalanan sebagai bagian dari keluarga besar Teknik Komputer 2026.',
                ],
                [
                    'period'    => 'September – Desember 2024',
                    'title'     => 'Pembentukan Karakter',
                    'desc'      => 'Masa orientasi dan pengenalan kampus yang membentuk ikatan awal antar anggota angkatan. Identitas "Ascent" mulai digaungkan sebagai semangat bersama.',
                ],
                [
                    'period'    => 'Januari – Juni 2025',
                    'title'     => 'Eksplorasi & Kolaborasi',
                    'desc'      => 'Organisasi kemahasiswaan, komunitas belajar, dan proyek-proyek pertama yang menunjukkan sinergi antar kelas A, B, C, dan D.',
                ],
                [
                    'period'    => '2025 – Sekarang',
                    'title'     => 'Momentum Ascent',
                    'desc'      => 'Perjalanan terus berlanjut. Buku angkatan digital ini menjadi wujud nyata dokumentasi jejak dan kenangan kita bersama.',
                ],
            ],

            // ── Nilai-Nilai Ascent ─────────────────────────────────
            'values'           => [
                [
                    'title'     => 'Resiliensi Adaptif',
                    'desc'      => 'Ketangguhan & rasa aman saat menghadapi tantangan.',
                    'icon'      => 'shield-check',
                    'color'     => 'teal',
                ],
                [
                    'title'     => 'Kesetaraan Inklusif',
                    'desc'      => 'Kebersamaan & keseimbangan tanpa sekat antaranggota.',
                    'icon'      => 'user-group',
                    'color'     => 'cyan',
                ],
                [
                    'title'     => 'Proteksi Mutual',
                    'desc'      => 'Transparansi & konektivitas aliran informasi yang terbuka.',
                    'icon'      => 'lock-open',
                    'color'     => 'teal',
                ],
                [
                    'title'     => 'Sinergi Spektrum',
                    'desc'      => 'Harmoni & keberagaman yang saling melengkapi.',
                    'icon'      => 'sparkles',
                    'color'     => 'cyan',
                ],
                [
                    'title'     => 'Kualitas Berkelanjutan',
                    'desc'      => 'Kejernihan & konsistensi evaluasi berkesinambungan.',
                    'icon'      => 'arrow-path',
                    'color'     => 'teal',
                ],
            ],

            // ── Contact Info ───────────────────────────────────────
            'contact'          => [
                'address'    => 'Jl. Prof. Soedarto, S.H., Tembalang, Semarang 50275',
                'email'      => 'Tekkom26@gmail.com',
                'phone'      => '+62 812-3456-7890',
                'instagram'  => '@tekkom.26',
            ],
        ];
        if ($request->expectsJson()) {
            return response()->json([
                'framework' => 'Laravel',
                'message' => 'Hello from Laravel and FrankenPHP on Vercel',
                'data' => $data,
            ]);
        }

        return view('landing', $data);
    }
}
