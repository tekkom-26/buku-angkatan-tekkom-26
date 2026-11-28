<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global Cohort Official Name
    |--------------------------------------------------------------------------
    |
    | Centralized cohort name stored in app_config ('cohort_name') with this
    | baseline default fallback. Admin can update this via Filament Panel.
    |
    */

    'cohort_name' => 'TEKNIK KOMPUTER 2026',

    /*
    |--------------------------------------------------------------------------
    | Landing Page Dynamic Content Defaults
    |--------------------------------------------------------------------------
    |
    | Baseline content for the public landing page (/), serving as the
    | fail-safe fallback if database records are empty or missing.
    |
    */

    'landing' => [
        'hero' => [
            'title' => 'ANGKATAN // TEKKOM 2026',
            'motto' => 'LOREM IPSUM DOLOR!!!',
            'narrative' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.',
            'directory_url' => '/directory',
            'identity_url' => '/identity',
            'mascot_placeholder' => '/assets/Placeholder.png',
        ],
        'decoding' => [
            'wordmark' => 'TEKKOM 2026',
            'sub_label' => 'Computer Engineering Twenty Six',
            'movement_one_title' => '// 01_MOVEMENT_ONE',
            'movement_one_heading' => 'Pendakian Kolektif Menuju Puncak Ekselensi',
            'movement_one_body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Setiap langkah ke atas adalah komitmen bersama untuk tumbuh melampaui batas kemampuan teknis maupun karakter.',
            'logo_placeholder' => '/assets/Placeholder.png',
        ],
        'directory_banner' => [
            'headline' => 'Temukan Rekan Satu Angkatan di Direktori Resmi',
            'description' => 'Akses profil lengkap mahasiswa Teknik Komputer 2026 berdasarkan kelas, domisili, dan minat eksplorasi melalui sistem direktori terverifikasi.',
            'cta_label' => 'Jelajahi 100+ Profil Mahasiswa',
            'directory_url' => '/directory',
            'total_students' => '205',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Identity Page Dynamic Content Defaults
    |--------------------------------------------------------------------------
    |
    | Baseline content for the identity & philosophy page (/identity),
    | serving as the fail-safe fallback if database records are empty or missing.
    |
    */

    'identity' => [
        'header_banner' => [
            'title' => 'Identitas, Filosofi & Nilai Dasar',
            'subhead' => 'Dokumentasi komprehensif mengenai arsitektur visual, arah gerak strategis, empat pilar warna, serta pembedahan simbolisme lambang dan maskot resmi angkatan Teknik Komputer 2026.',
            'banner_placeholder' => '/assets/Placeholder.png',
        ],
        'vision_mission' => [
            'motto' => 'Integrity, Unbreakable, We Are Competent!!!',
            'vision_body' => 'Menjadi angkatan Teknik Komputer 2026 yang solid, berintegritas tinggi, dan unggul dalam kompetensi rekayasa perangkat keras maupun perangkat lunak, serta mampu memberikan kontribusi nyata bagi kemajuan teknologi dan masyarakat.',
            'missions' => [
                [
                    'number' => '01.',
                    'title' => 'Solidaritas & Inklusivitas Tanpa Sekat',
                    'description' => 'Membangun iklim kekeluargaan yang suportif lintas kelas A, B, C, dan D sehingga setiap anggota angkatan tumbuh bersama tanpa ada yang tertinggal.',
                ],
                [
                    'number' => '02.',
                    'title' => 'Akselerasi Kompetensi Akademik & Rekayasa',
                    'description' => 'Mendorong budaya kolaborasi riset, praktikum sistem komputer, dan pengembangan karya inovatif yang berdaya saing nasional maupun global.',
                ],
                [
                    'number' => '03.',
                    'title' => 'Integritas Karakter & Kontribusi Berkelanjutan',
                    'description' => 'Menjaga etika profesional, tanggung jawab sosial, serta meninggalkan jejak kontribusi positif di lingkungan Departemen Teknik Komputer FT Undip.',
                ],
            ],
        ],
        'color_pillars' => [
            'pillars' => [
                [
                    'index' => '01',
                    'name' => 'Sky Wash',
                    'role' => 'Primary Interactive',
                    'hexLabel' => '#80bab2',
                    'tokenName' => 'secondary-500',
                    'swatchClass' => 'bg-secondary-500 text-primary-900',
                    'philosophy' => 'Melambangkan kejernihan berpikir, keterbukaan kolaborasi, serta energi interaktif yang menggerakkan inovasi teknologi angkatan 2026.',
                ],
                [
                    'index' => '02',
                    'name' => 'Butter Cream',
                    'role' => 'Accent & Alert',
                    'hexLabel' => '#d4d86a',
                    'tokenName' => 'highlight-500',
                    'swatchClass' => 'bg-highlight-500 text-primary-900',
                    'philosophy' => 'Merepresentasikan optimisme hangat, penanda fokus strategis, serta semangat muda yang adaptif dalam menghadapi dinamika tantangan.',
                ],
                [
                    'index' => '03',
                    'name' => 'Deep Plum',
                    'role' => 'Structural Grounding',
                    'hexLabel' => '#514051',
                    'tokenName' => 'primary-900',
                    'swatchClass' => 'bg-primary-900 text-white',
                    'philosophy' => 'Menjadi jangkar otoritas struktural, kedalaman intelektual, dan keteguhan prinsip yang menjaga keutuhan fondasi keluarga besar angkatan.',
                ],
                [
                    'index' => '04',
                    'name' => 'Snow White',
                    'role' => 'Surface Canvas',
                    'hexLabel' => '#e6e3e8',
                    'tokenName' => 'surface-card',
                    'swatchClass' => 'bg-surface-card text-primary-900 border border-surface-border',
                    'philosophy' => 'Mewujudkan ruang kanvas yang bersih, inklusif, dan harmonis sebagai wadah tumbuhnya gagasan serta dokumentasi jejak seluruh anggota.',
                ],
            ],
        ],
        'logo_symbolism' => [
            'eyebrow' => 'Anatomi Lambang',
            'title' => 'Bedah Simbolisme',
            'subtitle' => '5 Elemen Lambang COMETS',
            'description' => 'Setiap lapisan geometri pada lambang COMETS 2026 dirancang dengan presisi untuk memadukan solidaritas koloni lebah, ketangguhan melewati masa terang dan gelap, serta identitas keilmuan Teknik Komputer.',
            'slides' => [
                [
                    'number' => '01',
                    'eyebrow' => 'Kesetaraan & Perlindungan',
                    'title' => 'Lingkaran — Kesetaraan Tanpa Awal & Akhir',
                    'description' => 'Lingkaran utuh melambangkan kesetaraan seluruh anggota angkatan tanpa awal maupun akhir, sebuah ikatan pelindung di mana setiap mahasiswa saling menjaga satu sama lain.',
                    'detail' => 'Saling Melindungi',
                    'part' => 'Lingkaran',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '02',
                    'eyebrow' => 'Solidaritas Koloni',
                    'title' => 'Lebah — Satu Koloni Membangun Sarang Kokoh',
                    'description' => 'Lebah merepresentasikan nilai inti Kebersamaan: satu koloni pekerja keras yang bersinergi secara harmonis untuk membangun sarang dan masa depan yang kokoh.',
                    'detail' => 'Kebersamaan Koloni',
                    'part' => 'Lebah',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '03',
                    'eyebrow' => 'Identitas Teknik Komputer',
                    'title' => 'Sayap Bersirkuit — Rekayasa & Daya Juang',
                    'description' => 'Rentang sayap berjalur sirkuit terpadu melambangkan identitas keilmuan Teknik Komputer (perangkat keras & perangkat lunak) sekaligus daya juang untuk terus melaju tinggi.',
                    'detail' => 'Hardware & Software',
                    'part' => 'Sayap Bersirkuit',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '04',
                    'eyebrow' => 'Ketangguhan',
                    'title' => 'Belang & Sengat — Terang, Gelap & Daya Tahan',
                    'description' => 'Perpaduan belang hitam-emas dan sengat tajam melambangkan masa terang maupun gelap yang dilewati bersama dengan penuh keberanian dan daya tahan.',
                    'detail' => 'Ketangguhan Bersama',
                    'part' => 'Belang & Sengat',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '05',
                    'eyebrow' => 'Angkatan 2026',
                    'title' => '26 Segi Enam — 26 Simpul Rumah Bersama',
                    'description' => 'Konstruksi 26 sel heksagonal menjadi penanda angkatan 2026, di mana setiap simpul mahasiswa saling terhubung membentuk satu rumah besar Teknik Komputer Undip.',
                    'detail' => '26 Simpul Hexagon',
                    'part' => '26 Segi Enam',
                    'image' => '/assets/Placeholder.png',
                ],
            ],
        ],
        'mascot' => [
            'eyebrow' => 'Karakter Maskot',
            'title' => 'Bedah Karakter',
            'subtitle' => '& Anatomi Maskot Bee-ner',
            'description' => 'Setiap detail anatomi pada maskot lebah biner Bee-ner dirancang untuk memancarkan kecerdasan komputasi, potensi diri yang menyala, kekuatan memikul tanggung jawab bersama, dan kepekaan sosial koloni COMETS 2026.',
            'slides' => [
                [
                    'number' => '01',
                    'eyebrow' => 'Logika Komputasi',
                    'title' => 'Strip Hitam-Emas — Logika Biner 0 & 1',
                    'description' => 'Belang tubuh berwarna hitam Charcoal dan Tech Gold merepresentasikan bilangan biner (0 dan 1) sebagai fondasi logika komputasi Teknik Komputer sekaligus ketangguhan melewati suka dan duka.',
                    'detail' => 'Biner 0 & 1',
                    'part' => 'Strip Hitam-Emas',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '02',
                    'eyebrow' => 'Potensi Diri',
                    'title' => 'Mata Hexagon — Potensi yang Menyala',
                    'description' => 'Mata berbentuk segi enam (hexagon) yang bersinar keemasan melambangkan fokus, ketajaman visi rekayasa, serta potensi setiap mahasiswa COMETS 2026 yang selalu menyala.',
                    'detail' => 'Visi Hexagon',
                    'part' => 'Mata Hexagon',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '03',
                    'eyebrow' => 'Tanggung Jawab Bersama',
                    'title' => 'Ring Bahu — Kuat Memikul Beban Bersama',
                    'description' => 'Konstruksi ring pelindung di kedua bahu Bee-ner melambangkan kekuatan fisik maupun mental untuk saling menopang dan memikul tanggung jawab besar secara kolektif.',
                    'detail' => 'Kekuatan Kolektif',
                    'part' => 'Ring Bahu',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '04',
                    'eyebrow' => 'Empati & Konektivitas',
                    'title' => 'Antena — Peka & Peduli Sesama',
                    'description' => 'Sepasang antena pemancar di kepala Bee-ner berfungsi sebagai sensor kepekaan sosial: selalu tanggap, peduli, dan terhubung dengan kondisi rekan satu angkatan.',
                    'detail' => 'Peka & Peduli',
                    'part' => 'Antena',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '05',
                    'eyebrow' => 'Semangat Inklusif',
                    'title' => 'Senyum Ramah — Inklusivitas Koloni',
                    'description' => 'Ekspresi senyum hangat Bee-ner menyeimbangkan ketegasan teknologi keras dengan keramahan keluarga besar Teknik Komputer 2026 yang terbuka bagi siapa saja.',
                    'detail' => 'Koloni Inklusif',
                    'part' => 'Senyum Ramah',
                    'image' => '/assets/Placeholder.png',
                ],
            ],
        ],
    ],

];
