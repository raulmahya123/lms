<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// class PsychTestSeeder extends Seeder
// {
//     public function run(): void
//     {
//         // -----------------------------
//         // 1) Deteksi kolom yang tersedia
//         // -----------------------------
//         $cols = Schema::getColumnListing('psy_tests');

//         // kandidat kolom judul tes
//         $titleCol = collect(['title', 'label', 'display_name'])->first(fn($c) => in_array($c, $cols));

//         // kandidat kolom pengenal unik
//         $slugCol  = collect(['slug', 'code'])->first(fn($c) => in_array($c, $cols));

//         // kolom opsional lain
//         $descCol  = in_array('description', $cols) ? 'description' : (in_array('desc', $cols) ? 'desc' : null);
//         $durCol   = in_array('duration', $cols) ? 'duration' :
//                     (in_array('duration_minutes', $cols) ? 'duration_minutes' : null);
//         $premCol  = in_array('is_premium', $cols) ? 'is_premium' : null;
//         $nameCol  = in_array('name', $cols) ? 'name' : null; // kamu pakai utk nama user → isi dummy agar lolos

//         // nilai yang akan diisi hanya untuk kolom yang ada
//         $base = [];
//         if ($titleCol) $base[$titleCol] = 'Personality Test Dasar';
//         if ($slugCol)  $base[$slugCol]  = 'personality-basic';
//         if ($descCol)  $base[$descCol]  = 'Tes dasar untuk memetakan kecenderungan kepribadian';
//         if ($durCol)   $base[$durCol]   = 8;
//         if ($premCol)  $base[$premCol]  = false;
//         if ($nameCol)  $base[$nameCol]  = 'Seeder Dummy'; // dummy nama user pada psy_tests

//         // ---------------------------------------------------
//         // 2) Buat/ambil record psy_tests secara idempotent
//         // ---------------------------------------------------
//         $test = null;

//         if ($titleCol) {
//             // aman: WHERE <titleCol> = ?
//             $test = \App\Models\PsyTest::firstOrCreate([$titleCol => $base[$titleCol]], $base);
//         } elseif ($slugCol) {
//             $test = \App\Models\PsyTest::firstOrCreate([$slugCol => $base[$slugCol]], $base);
//         } else {
//             // fallback keras: pakai id=1 (atau id tertinggi + 1 jika sudah ada)
//             $exists = DB::table('psy_tests')->count();
//             if ($exists == 0) {
//                 DB::table('psy_tests')->updateOrInsert(['id' => 1], array_merge($base, [
//                     'created_at' => now(), 'updated_at' => now(),
//                 ]));
//                 $test = \App\Models\PsyTest::query()->first();
//             } else {
//                 // sudah ada data: ambil record pertama
//                 $test = \App\Models\PsyTest::query()->first();
//             }
//         }

//         // Safety net
//         if (!$test) {
//             // terakhir banget: create biasa (potensi duplikat jika di-run berulang)
//             $test = \App\Models\PsyTest::create($base);
//         }

//         // -----------------------------------
//         // 3) Pertanyaan & opsi contoh (2 item)
//         // -----------------------------------
//         if (method_exists($test, 'questions') && $test->questions()->count() === 0) {
//             $q1 = $test->questions()->create(['text' => 'Saya merasa nyaman bekerja sendiri.']);
//             if (method_exists($q1, 'options')) {
//                 $q1->options()->createMany([
//                     ['text' => 'Sangat Tidak Setuju', 'score' => 0],
//                     ['text' => 'Tidak Setuju',        'score' => 1],
//                     ['text' => 'Setuju',              'score' => 2],
//                     ['text' => 'Sangat Setuju',       'score' => 3],
//                 ]);
//             }

//             $q2 = $test->questions()->create(['text' => 'Saya mudah bergaul di lingkungan baru.']);
//             if (method_exists($q2, 'options')) {
//                 $q2->options()->createMany([
//                     ['text' => 'Sangat Tidak Setuju', 'score' => 3], // dibalik utk variasi
//                     ['text' => 'Tidak Setuju',        'score' => 2],
//                     ['text' => 'Setuju',              'score' => 1],
//                     ['text' => 'Sangat Setuju',       'score' => 0],
//                 ]);
//             }
//         }

//         // -----------------------------------
//         // 4) Profil (range skor) untuk mapping
//         // -----------------------------------
//         if (Schema::hasTable('psy_profiles') && method_exists($test, 'profiles') && $test->profiles()->count() === 0) {
//             \App\Models\PsyProfile::insert([
//                 [
//                     'test_id'     => $test->id,
//                     'key'         => 'intro',
//                     'name'        => 'Introvert',
//                     'min_total'   => 0,
//                     'max_total'   => 2,
//                     'description' => 'Reflektif, fokus pada ide dan kedalaman.',
//                     'created_at'  => now(),
//                     'updated_at'  => now(),
//                 ],
//                 [
//                     'test_id'     => $test->id,
//                     'key'         => 'ambi',
//                     'name'        => 'Ambivert',
//                     'min_total'   => 3,
//                     'max_total'   => 4,
//                     'description' => 'Seimbang antara reflektif & ekspresif.',
//                     'created_at'  => now(),
//                     'updated_at'  => now(),
//                 ],
//                 [
//                     'test_id'     => $test->id,
//                     'key'         => 'extro',
//                     'name'        => 'Extrovert',
//                     'min_total'   => 5,
//                     'max_total'   => 6,
//                     'description' => 'Energik, ekspresif, suka interaksi.',
//                     'created_at'  => now(),
//                     'updated_at'  => now(),
//                 ],
//             ]);
//         }
//     }
// }
