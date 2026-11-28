<?php

namespace Database\Seeders;

use App\Models\MbtiType;
use Illuminate\Database\Seeder;

class MbtiTypeSeeder extends Seeder
{
    /**
     * Seed the 16 canonical MBTI personality types grouped by temperament.
     */
    public function run(): void
    {
        $types = [
            // Analysts (NT)
            [
                'code' => 'INTJ',
                'temperament_code' => 'NT',
                'temperament_name' => 'Analysts',
                'title' => 'Architect',
                'description' => 'Pemikir strategis dengan rancangan sistematis dan visi jangka panjang.',
            ],
            [
                'code' => 'INTP',
                'temperament_code' => 'NT',
                'temperament_name' => 'Analysts',
                'title' => 'Logician',
                'description' => 'Analis logis yang gemar membedah pola, logika, dan sistem kompleks.',
            ],
            [
                'code' => 'ENTJ',
                'temperament_code' => 'NT',
                'temperament_name' => 'Analysts',
                'title' => 'Commander',
                'description' => 'Pemimpin tegas yang berorientasi eksekusi, struktur, dan target besar.',
            ],
            [
                'code' => 'ENTP',
                'temperament_code' => 'NT',
                'temperament_name' => 'Analysts',
                'title' => 'Debater',
                'description' => 'Eksplorator ide tajam yang cepat menemukan sudut pandang dan terobosan baru.',
            ],

            // Diplomats (NF)
            [
                'code' => 'INFJ',
                'temperament_code' => 'NF',
                'temperament_name' => 'Diplomats',
                'title' => 'Advocate',
                'description' => 'Idealis reflektif yang memadukan intuisi mendalam dengan empati dan prinsip.',
            ],
            [
                'code' => 'INFP',
                'temperament_code' => 'NF',
                'temperament_name' => 'Diplomats',
                'title' => 'Mediator',
                'description' => 'Pribadi kreatif yang hangat, autentik, dan peka terhadap nilai kemanusiaan.',
            ],
            [
                'code' => 'ENFJ',
                'temperament_code' => 'NF',
                'temperament_name' => 'Diplomats',
                'title' => 'Protagonist',
                'description' => 'Penggerak kolaborasi yang inspiratif dan mampu menyatukan potensi tim.',
            ],
            [
                'code' => 'ENFP',
                'temperament_code' => 'NF',
                'temperament_name' => 'Diplomats',
                'title' => 'Campaigner',
                'description' => 'Konektor antusias yang penuh imajinasi, semangat sosial, dan keterbukaan.',
            ],

            // Sentinels (SJ)
            [
                'code' => 'ISTJ',
                'temperament_code' => 'SJ',
                'temperament_name' => 'Sentinels',
                'title' => 'Logistician',
                'description' => 'Pribadi andal yang teliti, teratur, dan memegang teguh fakta serta tanggung jawab.',
            ],
            [
                'code' => 'ISFJ',
                'temperament_code' => 'SJ',
                'temperament_name' => 'Sentinels',
                'title' => 'Defender',
                'description' => 'Pendukung setia yang cermat menjaga keharmonisan dan detail kebutuhan tim.',
            ],
            [
                'code' => 'ESTJ',
                'temperament_code' => 'SJ',
                'temperament_name' => 'Sentinels',
                'title' => 'Executive',
                'description' => 'Pengelola tangguh yang menjaga ketertiban, prosedur, dan penyelesaian tepat waktu.',
            ],
            [
                'code' => 'ESFJ',
                'temperament_code' => 'SJ',
                'temperament_name' => 'Sentinels',
                'title' => 'Consul',
                'description' => 'Koordinator ramah yang aktif merawat kebersamaan dan solidaritas komunitas.',
            ],

            // Explorers (SP)
            [
                'code' => 'ISTP',
                'temperament_code' => 'SP',
                'temperament_name' => 'Explorers',
                'title' => 'Virtuoso',
                'description' => 'Pemecah masalah praktis yang cekatan bereksperimen dengan perangkat dan teknis.',
            ],
            [
                'code' => 'ISFP',
                'temperament_code' => 'SP',
                'temperament_name' => 'Explorers',
                'title' => 'Adventurer',
                'description' => 'Kreator fleksibel dengan kepekaan estetika dan ekspresi personal yang tenang.',
            ],
            [
                'code' => 'ESTP',
                'temperament_code' => 'SP',
                'temperament_name' => 'Explorers',
                'title' => 'Entrepreneur',
                'description' => 'Eksekutor responsif yang sigap membaca situasi nyata dan bertindak cepat.',
            ],
            [
                'code' => 'ESFP',
                'temperament_code' => 'SP',
                'temperament_name' => 'Explorers',
                'title' => 'Entertainer',
                'description' => 'Pembawa energi positif yang spontan, ekspresif, dan mencairkan suasana.',
            ],
        ];

        foreach ($types as $type) {
            MbtiType::query()->updateOrCreate(
                ['code' => $type['code']],
                [
                    'temperament_code' => $type['temperament_code'],
                    'temperament_name' => $type['temperament_name'],
                    'title' => $type['title'],
                    'description' => $type['description'],
                ],
            );
        }
    }
}
