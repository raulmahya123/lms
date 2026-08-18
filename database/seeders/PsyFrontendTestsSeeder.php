<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PsyTest;
use App\Models\PsyQuestion;
use App\Models\PsyOption;
use App\Models\PsyProfile;

class PsyFrontendTestsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFrontendInterestTest();
        $this->seedFrontendIqTest();
    }

    /**
     * TES 1:
     * Tes Minat & Bakat Frontend (Likert 1–5)
     * Track: frontend
     */
    protected function seedFrontendInterestTest(): void
    {
        // Cegah duplikasi test
        $test = PsyTest::firstOrCreate(
            ['slug' => 'frontend-minat-bakat'],
            [
                'name'           => 'Tes Minat & Bakat Frontend Dasar',
                'track'          => 'frontend',
                'type'           => 'likert', // sesuai TYPES di PsyTest
                'time_limit_min' => 0,        // tanpa batas waktu
                'is_active'      => true,
            ]
        );

        // Kalau sudah pernah di-seed (ada questions), skip
        if ($test->questions()->exists()) {
            return;
        }

        $questions = [
            [
                'prompt'    => 'Saya menikmati mengatur layout halaman web agar terlihat rapi dan seimbang.',
                'trait_key' => 'ui_layout',
            ],
            [
                'prompt'    => 'Saya tertarik mengutak-atik CSS untuk membuat tampilan yang lebih menarik.',
                'trait_key' => 'css_styling',
            ],
            [
                'prompt'    => 'Saya suka memperhatikan detail kecil (jarak, warna, ukuran font) pada tampilan website.',
                'trait_key' => 'detail_oriented',
            ],
            [
                'prompt'    => 'Saya senang jika tampilan website yang saya buat responsif di berbagai ukuran layar.',
                'trait_key' => 'responsive',
            ],
            [
                'prompt'    => 'Saya tertarik mempelajari komponen UI modern seperti Tailwind, Bootstrap, atau UI library lainnya.',
                'trait_key' => 'ui_library',
            ],
            [
                'prompt'    => 'Saya nyaman membaca dokumentasi HTML/CSS/JavaScript untuk menyelesaikan masalah.',
                'trait_key' => 'docs_reading',
            ],
            [
                'prompt'    => 'Saya suka mencoba animasi kecil (hover, transition, micro interaction) pada UI.',
                'trait_key' => 'micro_interaction',
            ],
            [
                'prompt'    => 'Saya tertarik mempelajari cara kerja browser merender halaman (DOM, CSSOM, dsb).',
                'trait_key' => 'browser_understanding',
            ],
            [
                'prompt'    => 'Saya menikmati ketika user merasa tampilan dan alur penggunaan aplikasi yang saya buat mudah dipahami.',
                'trait_key' => 'ux_mindset',
            ],
            [
                'prompt'    => 'Saya merasa nyaman berkolaborasi dengan designer untuk menerjemahkan desain ke kode.',
                'trait_key' => 'collab_designer',
            ],
        ];

        // Likert 1–5 yang sama untuk semua pertanyaan
        $likertOptions = [
            1 => 'Sangat tidak setuju',
            2 => 'Tidak setuju',
            3 => 'Netral',
            4 => 'Setuju',
            5 => 'Sangat setuju',
        ];

        foreach ($questions as $idx => $qData) {
            $question = PsyQuestion::create([
                'test_id'   => $test->id,
                'prompt'    => $qData['prompt'],
                'trait_key' => $qData['trait_key'],
                'qtype'     => 'likert',    // <-- penting: konsisten
                'ordering'  => $idx + 1,
            ]);

            $ordering = 1;
            foreach ($likertOptions as $value => $label) {
                PsyOption::create([
                    'question_id' => $question->id,
                    'label'       => $label,
                    'value'       => $value,
                    'ordering'    => $ordering++,
                ]);
            }
        }

        // Total skor teoritis: 10 pertanyaan * 5 = 50
        // Kita buat 4 profil hasil
        $profiles = [
            [
                'key'         => 'low_fit',
                'name'        => 'Kecocokan Rendah dengan Frontend',
                'min_total'   => 0,
                'max_total'   => 20,
                'description' => 'Saat ini minat dan bakat Anda terhadap dunia frontend masih rendah. '
                    . 'Mungkin Anda lebih cocok di area lain, atau butuh eksplorasi lebih lanjut sebelum fokus ke frontend.',
            ],
            [
                'key'         => 'medium_fit',
                'name'        => 'Kecocokan Cukup dengan Frontend',
                'min_total'   => 21,
                'max_total'   => 34,
                'description' => 'Anda memiliki ketertarikan dan potensi yang cukup terhadap frontend. '
                    . 'Dengan latihan terarah (HTML, CSS dasar, dan sedikit JavaScript), Anda bisa berkembang cukup baik.',
            ],
            [
                'key'         => 'high_fit',
                'name'        => 'Kecocokan Tinggi dengan Frontend',
                'min_total'   => 35,
                'max_total'   => 42,
                'description' => 'Minat dan bakat Anda terhadap frontend sudah tinggi. '
                    . 'Anda kemungkinan akan enjoy ketika mendalami UI/UX, CSS framework, dan JavaScript di sisi tampilan.',
            ],
            [
                'key'         => 'top_fit',
                'name'        => 'Sangat Cocok Menjadi Frontend Developer',
                'min_total'   => 43,
                'max_total'   => 50,
                'description' => 'Anda sangat cocok di jalur frontend. Minat dan bakat Anda kuat di area tampilan, '
                    . 'interaksi, dan pengalaman pengguna. Disarankan untuk serius mendalami frontend modern (React/Vue, Tailwind, dsb).',
            ],
        ];

        foreach ($profiles as $p) {
            PsyProfile::create([
                'test_id'     => $test->id,
                'key'         => $p['key'],
                'name'        => $p['name'],
                'min_total'   => $p['min_total'],
                'max_total'   => $p['max_total'],
                'description' => $p['description'],
                'user_id'     => null, // profil global
            ]);
        }
    }

    /**
     * TES 2:
     * Tes Logika & Kecerdasan Dasar Frontend
     * Track: frontend, Type: iq (pakai pilihan ganda dengan value 1 = benar, 0 = salah)
     */
    protected function seedFrontendIqTest(): void
    {
        $test = PsyTest::firstOrCreate(
            ['slug' => 'frontend-iq-dasar'],
            [
                'name'           => 'Tes Logika & Kecerdasan Dasar Frontend',
                'track'          => 'frontend',
                'type'           => 'iq',   // sesuai TYPES di PsyTest
                'time_limit_min' => 20,
                'is_active'      => true,
            ]
        );

        if ($test->questions()->exists()) {
            return;
        }

        /**
         * Struktur:
         * - prompt
         * - trait_key (opsional, untuk grouping)
         * - options: [ ['label' => '...', 'correct' => true/false], ... ]
         *
         * Kita simpan value:
         *  - 1 untuk jawaban benar
         *  - 0 untuk jawaban salah
         * total_score = jumlah jawaban benar
         */
        $questions = [
            [
                'prompt'    => "Apa output dari ekspresi JavaScript berikut?\n\nconsole.log(2 + '2');",
                'trait_key' => 'js_basic',
                'options'   => [
                    ['label' => '4',          'correct' => false],
                    ['label' => "'22'",       'correct' => true],
                    ['label' => 'Error',      'correct' => false],
                    ['label' => 'NaN',        'correct' => false],
                ],
            ],
            [
                'prompt'    => "CSS mana yang membuat teks menjadi tebal?",
                'trait_key' => 'css_basic',
                'options'   => [
                    ['label' => 'font-style: bold;',     'correct' => false],
                    ['label' => 'font-weight: bold;',    'correct' => true],
                    ['label' => 'text-style: bold;',     'correct' => false],
                    ['label' => 'font-bold: true;',      'correct' => false],
                ],
            ],
            [
                'prompt'    => "Tag HTML mana yang digunakan untuk menampilkan teks paragraf?",
                'trait_key' => 'html_basic',
                'options'   => [
                    ['label' => '<h1>',  'correct' => false],
                    ['label' => '<p>',   'correct' => true],
                    ['label' => '<span>','correct' => false],
                    ['label' => '<div>', 'correct' => false],
                ],
            ],
            [
                'prompt'    => "Dalam layout, sistem grid 12 kolom sering dipakai. "
                    . "Jika satu elemen memakai 6 kolom, berapa banyak elemen serupa yang muat dalam satu baris?",
                'trait_key' => 'logic_numeric',
                'options'   => [
                    ['label' => '1 elemen',  'correct' => false],
                    ['label' => '2 elemen',  'correct' => true],
                    ['label' => '3 elemen',  'correct' => false],
                    ['label' => '6 elemen',  'correct' => false],
                ],
            ],
            [
                'prompt'    => "Manakah yang merupakan contoh selector CSS yang lebih spesifik?",
                'trait_key' => 'css_specificity',
                'options'   => [
                    ['label' => '.button',                  'correct' => false],
                    ['label' => 'button',                   'correct' => false],
                    ['label' => '#primary-button',          'correct' => true],
                    ['label' => '*',                        'correct' => false],
                ],
            ],
            [
                'prompt'    => "Jika sebuah array JavaScript berisi [1, 2, 3, 4], "
                    . "berapa panjang array tersebut?",
                'trait_key' => 'js_array',
                'options'   => [
                    ['label' => '3', 'correct' => false],
                    ['label' => '4', 'correct' => true],
                    ['label' => '5', 'correct' => false],
                    ['label' => 'Tidak bisa ditentukan', 'correct' => false],
                ],
            ],
            [
                'prompt'    => "Konsep \"responsive design\" paling dekat dengan pernyataan mana?",
                'trait_key' => 'responsive_concept',
                'options'   => [
                    ['label' => 'Website hanya terlihat bagus di satu resolusi.',             'correct' => false],
                    ['label' => 'Website menyesuaikan tampilan di berbagai ukuran layar.',    'correct' => true],
                    ['label' => 'Website hanya untuk mobile.',                                'correct' => false],
                    ['label' => 'Website hanya untuk desktop.',                               'correct' => false],
                ],
            ],
            [
                'prompt'    => "Di CSS, properti mana yang mengatur jarak di luar border sebuah elemen?",
                'trait_key' => 'css_box_model',
                'options'   => [
                    ['label' => 'padding', 'correct' => false],
                    ['label' => 'margin',  'correct' => true],
                    ['label' => 'border',  'correct' => false],
                    ['label' => 'gap',     'correct' => false],
                ],
            ],
            [
                'prompt'    => "Jika sebuah fungsi JavaScript dipanggil dengan argumen yang kurang (dibanding parameternya), "
                    . "apa yang terjadi pada parameter yang tidak diisi?",
                'trait_key' => 'js_function',
                'options'   => [
                    ['label' => 'Akan error.',                                       'correct' => false],
                    ['label' => 'Secara otomatis menjadi 0.',                        'correct' => false],
                    ['label' => 'Secara otomatis menjadi string kosong.',            'correct' => false],
                    ['label' => 'Bernilai undefined.',                               'correct' => true],
                ],
            ],
            [
                'prompt'    => "Dalam logika dasar, jika semua frontend developer suka CSS, "
                    . "dan Budi adalah frontend developer, maka:",
                'trait_key' => 'logic_basic',
                'options'   => [
                    ['label' => 'Budi pasti tidak suka CSS.',             'correct' => false],
                    ['label' => 'Budi mungkin suka, mungkin tidak.',      'correct' => false],
                    ['label' => 'Budi pasti suka CSS.',                   'correct' => true],
                    ['label' => 'Kesimpulan tidak bisa dibuat.',          'correct' => false],
                ],
            ],
        ];

        foreach ($questions as $idx => $qData) {
            $question = PsyQuestion::create([
                'test_id'   => $test->id,
                'prompt'    => $qData['prompt'],
                'trait_key' => $qData['trait_key'],
                'qtype'     => 'mcq',       // <-- ganti dari 'single' ke 'mcq'
                'ordering'  => $idx + 1,
            ]);

            $ordering = 1;
            foreach ($qData['options'] as $opt) {
                PsyOption::create([
                    'question_id' => $question->id,
                    'label'       => $opt['label'],
                    'value'       => $opt['correct'] ? 1 : 0,
                    'ordering'    => $ordering++,
                ]);
            }
        }

        // 10 soal, nilai maksimum 10
        $profiles = [
            [
                'key'         => 'low_iq_frontend',
                'name'        => 'Perlu Banyak Latihan Logika & Dasar Frontend',
                'min_total'   => 0,
                'max_total'   => 3,
                'description' => 'Skor Anda masih cukup rendah. Disarankan memperkuat dasar logika, HTML/CSS dasar, '
                    . 'serta konsep JavaScript fundamental sebelum terjun lebih jauh ke frontend.',
            ],
            [
                'key'         => 'medium_iq_frontend',
                'name'        => 'Pemahaman Dasar Sudah Cukup',
                'min_total'   => 4,
                'max_total'   => 7,
                'description' => 'Anda memiliki pemahaman dasar yang cukup. Dengan latihan dan praktik membangun projek kecil, '
                    . 'kemampuan Anda akan meningkat signifikan.',
            ],
            [
                'key'         => 'high_iq_frontend',
                'name'        => 'Pemahaman Logika & Dasar Frontend Kuat',
                'min_total'   => 8,
                'max_total'   => 10,
                'description' => 'Skor Anda tinggi. Logika dan pemahaman dasar frontend Anda kuat. '
                    . 'Anda siap untuk materi yang lebih advanced (framework JS, state management, dan optimasi performa).',
            ],
        ];

        foreach ($profiles as $p) {
            PsyProfile::create([
                'test_id'     => $test->id,
                'key'         => $p['key'],
                'name'        => $p['name'],
                'min_total'   => $p['min_total'],
                'max_total'   => $p['max_total'],
                'description' => $p['description'],
                'user_id'     => null,
            ]);
        }
    }
}
