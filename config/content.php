<?php

return [

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
            'wordmark' => 'ASCENT',
            'sub_label' => 'As One Computer Engineering Twenty Six',
            'movement_one_title' => '// 01_THE_ASCENT',
            'movement_one_heading' => 'Pendakian Kolektif Menuju Puncak Ekselensi',
            'movement_one_body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Setiap langkah ke atas adalah komitmen bersama untuk tumbuh melampaui batas kemampuan teknis maupun karakter.',
            'movement_two_title' => '// 02_AS_ONE',
            'movement_two_heading' => 'Satu Frekuensi dalam Spektrum Keberagaman',
            'movement_two_body' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Dari berbagai latar belakang daerah, menyatu sebagai keluarga besar Teknik Komputer 2026 yang saling menguatkan.',
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
            'eyebrow' => '// 03_LOGO_ANATOMY',
            'title' => 'Bedah Simbolisme',
            'subtitle' => 'Logo ASCENT',
            'description' => 'Setiap lapisan geometri pada lambang ASCENT dirancang dengan presisi untuk merepresentasikan visi, identitas angkatan, keahlian rekayasa, dan solidaritas Teknik Komputer 2026.',
            'slides' => [
                [
                    'number' => '01',
                    'eyebrow' => 'ELEVATION LAYER',
                    'title' => 'Sayap Ascent // Elevasi & Ambisi Melambung',
                    'description' => 'Rentang sayap geometris melambangkan semangat elevasi (Ascent) dan ambisi angkatan 2026 untuk terus terbang tinggi melampaui batas kemampuan.',
                    'detail' => 'Elevasi // Ambisi Melambung',
                    'part' => 'WINGS',
                    'accent' => 'highlight',
                    'accentClass' => 'text-highlight-900',
                    'wireframeLabel' => '[X] [ASSET: LOGO_COMPONENT_1:1 — ELEMEN_SAYAP_ASCENT]',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '02',
                    'eyebrow' => 'COHORT IDENTITY',
                    'title' => 'Geometri Angka 26 // Identitas Angkatan',
                    'description' => 'Konstruksi garis tegas yang membentuk siluet angka 26 sebagai penanda abadi tahun kelahiran dan eksistensi keluarga besar Teknik Komputer 2026.',
                    'detail' => 'Identitas // Angkatan 2026',
                    'part' => 'NUMERAL 26',
                    'accent' => 'secondary',
                    'accentClass' => 'text-secondary-700',
                    'wireframeLabel' => '[X] [ASSET: LOGO_COMPONENT_1:1 — GEOMETRI_ANGKA_26]',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '03',
                    'eyebrow' => 'ENGINEERING CORE',
                    'title' => 'Sirkuit Logika Komputasi // Keahlian Rekayasa',
                    'description' => 'Jalur sirkuit terpadu dan simpul logika merepresentasikan fondasi keilmuan Teknik Komputer yang menghubungkan perangkat keras dan perangkat lunak.',
                    'detail' => 'Keahlian // Rekayasa Komputasi',
                    'part' => 'CIRCUIT',
                    'accent' => 'highlight',
                    'accentClass' => 'text-highlight-900',
                    'wireframeLabel' => '[X] [ASSET: LOGO_COMPONENT_1:1 — SIRKUIT_LOGIKA_KOMPUTASI]',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '04',
                    'eyebrow' => 'UNIFIED EMBLEM',
                    'title' => 'Lambang Utuh Terintegrasi // Solidaritas Kolektif',
                    'description' => 'Perpaduan utuh seluruh elemen membentuk perisai kesatuan yang kokoh, menjadi simbol solidaritas kolektif yang tak terpisahkan.',
                    'detail' => 'Solidaritas // Kesatuan Kolektif',
                    'part' => 'FULL EMBLEM',
                    'accent' => 'secondary',
                    'accentClass' => 'text-secondary-700',
                    'wireframeLabel' => '[X] [ASSET: LOGO_COMPONENT_1:1 — LAMBANG_UTUH_TERINTEGRASI]',
                    'image' => '/assets/Placeholder.png',
                ],
            ],
        ],
        'mascot' => [
            'eyebrow' => '// 04_MASCOT_ANATOMY',
            'title' => 'Bedah Karakter',
            'subtitle' => '& Anatomi Maskot',
            'description' => 'Setiap bagian dari maskot gajah Tekkom membawa cerita tentang cara kita berpikir, terhubung, dan tumbuh sebagai satu angkatan.',
            'slides' => [
                [
                    'number' => '01',
                    'eyebrow' => 'CORE ATTRIBUTE',
                    'title' => 'Gading Komputasi',
                    'description' => 'Gading melambangkan memori dan daya ingat kuat: kemampuan menyimpan pengalaman serta mengubahnya menjadi solusi.',
                    'detail' => 'Memori // Daya Ingat Kuat',
                    'part' => 'TUSK',
                    'accent' => 'highlight',
                    'accentClass' => 'text-highlight-900',
                    'wireframeLabel' => '[X] [ASSET: MASCOT_COMPONENT_1:1 — ANATOMI_GADING]',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '02',
                    'eyebrow' => 'CORE ATTRIBUTE',
                    'title' => 'Belalai Ramah',
                    'description' => 'Belalai menjadi simbol sinergi dan komunikasi tanpa sekat: lentur, adaptif, dan selalu siap menjangkau satu sama lain.',
                    'detail' => 'Sinergi // Komunikasi Tanpa Sekat',
                    'part' => 'TRUNK',
                    'accent' => 'secondary',
                    'accentClass' => 'text-secondary-700',
                    'wireframeLabel' => '[X] [ASSET: MASCOT_COMPONENT_1:1 — ANATOMI_BELALAI]',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '03',
                    'eyebrow' => 'IDENTITY HARDWARE',
                    'title' => 'Almamater / Hardware',
                    'description' => 'Almamater adalah hardware identitas Teknik Komputer yang menyatukan karakter personal dengan rumah besar Tekkom.',
                    'detail' => 'Identitas // Teknik Komputer',
                    'part' => 'JACKET',
                    'accent' => 'highlight',
                    'accentClass' => 'text-highlight-900',
                    'wireframeLabel' => '[X] [ASSET: MASCOT_COMPONENT_1:1 — ANATOMI_ALMAMATER]',
                    'image' => '/assets/Placeholder.png',
                ],
                [
                    'number' => '04',
                    'eyebrow' => 'FULL CHARACTER',
                    'title' => 'Pose Penuh Karakter',
                    'description' => 'Seluruh elemen bertemu dalam pose penuh karakter gajah chibi, simbol semangat angkatan Teknik Komputer 2026.',
                    'detail' => 'Semangat // Angkatan 2026',
                    'part' => 'FULL BODY',
                    'accent' => 'secondary',
                    'accentClass' => 'text-secondary-700',
                    'wireframeLabel' => '[X] [ASSET: MASCOT_COMPONENT_1:1 — POSE_LENGKAP_CHIBI]',
                    'image' => '/assets/Placeholder.png',
                ],
            ],
        ],
    ],

];
