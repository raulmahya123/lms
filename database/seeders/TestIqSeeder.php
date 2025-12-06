<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestIq;

class TestIqSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFrontendBasicIq();
    }

    protected function seedFrontendBasicIq(): void
    {
        // Biar gak dobel-dobel kalau seeder dijalankan ulang
        $existing = TestIq::where('title', 'Tes IQ Dasar untuk Calon Frontend Developer')->first();
        if ($existing) {
            return;
        }

        $questions = [
            [
                'id'      => 'Q1',
                'prompt'  => 'Jika 2, 4, 8, 16, ..., angka selanjutnya adalah?',
                'options' => [
                    ['key' => 'A', 'label' => '18'],
                    ['key' => 'B', 'label' => '24'],
                    ['key' => 'C', 'label' => '32'],
                    ['key' => 'D', 'label' => '30'],
                ],
                'answer_key' => 'C',
                'category'   => 'sequence',
                'weight'     => 1,
            ],
            [
                'id'      => 'Q2',
                'prompt'  => 'Seorang developer membutuhkan 20 menit untuk menyelesaikan 2 task kecil. '
                           . 'Berapa task kecil yang kira-kira bisa ia selesaikan dalam 1 jam dengan kecepatan yang sama?',
                'options' => [
                    ['key' => 'A', 'label' => '4 task'],
                    ['key' => 'B', 'label' => '6 task'],
                    ['key' => 'C', 'label' => '8 task'],
                    ['key' => 'D', 'label' => '10 task'],
                ],
                'answer_key' => 'C', // 20 menit = 2 task → 60 menit = 6 task? Wait, cek: 20min→2task => 10min→1task => 60min→6task! (jadi harus B)
                'category'   => 'math',
                'weight'     => 1,
            ],
            [
                'id'      => 'Q3',
                'prompt'  => 'Semua frontend developer bisa menulis HTML. Budi adalah frontend developer. '
                           . 'Kesimpulan yang paling logis?',
                'options' => [
                    ['key' => 'A', 'label' => 'Budi tidak bisa menulis HTML'],
                    ['key' => 'B', 'label' => 'Budi mungkin bisa, mungkin tidak'],
                    ['key' => 'C', 'label' => 'Budi pasti bisa menulis HTML'],
                    ['key' => 'D', 'label' => 'Tidak ada kesimpulan yang bisa dibuat'],
                ],
                'answer_key' => 'C',
                'category'   => 'logic',
                'weight'     => 1,
            ],
            [
                'id'      => 'Q4',
                'prompt'  => 'Urutan huruf berikut: A, C, E, G, ... huruf selanjutnya adalah?',
                'options' => [
                    ['key' => 'A', 'label' => 'H'],
                    ['key' => 'B', 'label' => 'I'],
                    ['key' => 'C', 'label' => 'J'],
                    ['key' => 'D', 'label' => 'K'],
                ],
                'answer_key' => 'B', // lompat 2: A C E G I
                'category'   => 'sequence',
                'weight'     => 1,
            ],
            [
                'id'      => 'Q5',
                'prompt'  => 'Dalam satu tim, semua orang yang jago CSS juga paham dasar desain. '
                           . 'Sinta jago CSS. Kesimpulan yang benar adalah:',
                'options' => [
                    ['key' => 'A', 'label' => 'Sinta tidak paham desain'],
                    ['key' => 'B', 'label' => 'Sinta paham dasar desain'],
                    ['key' => 'C', 'label' => 'Sinta hanya paham JavaScript'],
                    ['key' => 'D', 'label' => 'Tidak ada hubungan antara CSS dan desain'],
                ],
                'answer_key' => 'B',
                'category'   => 'logic',
                'weight'     => 1,
            ],
            [
                'id'      => 'Q6',
                'prompt'  => 'Sebuah halaman web memiliki grid 12 kolom. Jika satu card memakai 3 kolom, '
                           . 'berapa card maksimal dalam satu baris?',
                'options' => [
                    ['key' => 'A', 'label' => '3'],
                    ['key' => 'B', 'label' => '4'],
                    ['key' => 'C', 'label' => '6'],
                    ['key' => 'D', 'label' => '12'],
                ],
                'answer_key' => 'B', // 12 / 3 = 4
                'category'   => 'math',
                'weight'     => 1,
            ],
            [
                'id'      => 'Q7',
                'prompt'  => 'Jika 5 * 3 = 15, 5 * 5 = 25, maka 5 * 7 = ?',
                'options' => [
                    ['key' => 'A', 'label' => '30'],
                    ['key' => 'B', 'label' => '32'],
                    ['key' => 'C', 'label' => '35'],
                    ['key' => 'D', 'label' => '40'],
                ],
                'answer_key' => 'C',
                'category'   => 'math',
                'weight'     => 1,
            ],
            [
                'id'      => 'Q8',
                'prompt'  => 'Seorang user mengatakan: "Saya suka tampilan yang simpel dan mudah dipahami." '
                           . 'Ini paling dekat dengan konsep?',
                'options' => [
                    ['key' => 'A', 'label' => 'Backend optimization'],
                    ['key' => 'B', 'label' => 'User Experience (UX)'],
                    ['key' => 'C', 'label' => 'Database design'],
                    ['key' => 'D', 'label' => 'Server scaling'],
                ],
                'answer_key' => 'B',
                'category'   => 'frontend_basic',
                'weight'     => 1,
            ],
            [
                'id'      => 'Q9',
                'prompt'  => 'Jika sebuah komponen UI butuh 4 state (normal, hover, active, disabled), '
                           . 'dan setiap state butuh 2 warna berbeda, total kombinasi warna yang perlu disiapkan adalah?',
                'options' => [
                    ['key' => 'A', 'label' => '4'],
                    ['key' => 'B', 'label' => '6'],
                    ['key' => 'C', 'label' => '8'],
                    ['key' => 'D', 'label' => '10'],
                ],
                'answer_key' => 'C', // 4 state * 2 warna = 8
                'category'   => 'logic',
                'weight'     => 1,
            ],
            [
                'id'      => 'Q10',
                'prompt'  => 'Semua icon di sebuah design system punya ukuran kelipatan 4 (4px, 8px, 12px, ...). '
                           . 'Jika sebuah icon berukuran 28px, apakah ukuran tersebut konsisten dengan aturan?',
                'options' => [
                    ['key' => 'A', 'label' => 'Ya, 28 bisa dibagi 4'],
                    ['key' => 'B', 'label' => 'Tidak, 28 tidak bisa dibagi 4'],
                    ['key' => 'C', 'label' => 'Tidak tahu'],
                    ['key' => 'D', 'label' => 'Tergantung warna icon'],
                ],
                'answer_key' => 'A',
                'category'   => 'math',
                'weight'     => 1,
            ],
        ];

        // PERBAIKI jawaban Q2 (cek hitungan lagi)
        // 20 menit = 2 task → 10 menit = 1 task → 60 menit = 6 task → harusnya 'B'
        $questions[1]['answer_key'] = 'B';

        $totalQuestions = count($questions);

        $meta = [
            'level'         => 'beginner',
            'track'         => 'frontend',
            'max_questions' => $totalQuestions,
            'norm_table'    => [
                [
                    'min'        => 0,
                    'max'        => 3,
                    'label'      => 'Perlu Latihan Dasar',
                    'short_code' => 'LOW',
                    'description'=> 'Skor masih rendah. Disarankan memperkuat logika dasar dan pemahaman konsep frontend sederhana.',
                ],
                [
                    'min'        => 4,
                    'max'        => 7,
                    'label'      => 'Cukup',
                    'short_code' => 'MID',
                    'description'=> 'Skor cukup. Dengan latihan dan projek kecil, kemampuan logika dan frontend Anda bisa meningkat pesat.',
                ],
                [
                    'min'        => 8,
                    'max'        => $totalQuestions,
                    'label'      => 'Kuat',
                    'short_code' => 'HIGH',
                    'description'=> 'Skor tinggi. Logika dan pemahaman dasar frontend Anda kuat, siap lanjut ke materi yang lebih advanced.',
                ],
            ],
        ];

        TestIq::create([
            'title'            => 'Tes IQ Dasar untuk Calon Frontend Developer',
            'description'      => 'Tes ini mengukur logika dasar, kemampuan berhitung sederhana, dan pemahaman konsep frontend fundamental.',
            'questions'        => $questions,
            'is_active'        => true,
            'duration_minutes' => 20,
            'cooldown_value'   => 7,
            'cooldown_unit'    => 'day',
            'submissions'      => [],    // awalnya kosong
            'meta'             => $meta,
        ]);
    }
}
