<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestIq;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TestIqController extends Controller
{
    /** Daftar test IQ (pencarian + paginate) */
    public function index(Request $r)
    {
        $q = TestIq::query()
            ->when($r->filled('q'), function ($qq) use ($r) {
                $term = trim($r->q);
                $qq->where(function ($w) use ($term) {
                    $w->where('title', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->latest('id');

        $tests = $q->paginate(20)->withQueryString();

        return view('admin.test_iq.index', compact('tests'));
    }

    /** Form create */
    public function create()
    {
        return view('admin.test_iq.create');
    }

    /** Simpan data baru */
    public function store(Request $r)
    {
        $data = $r->validate([
            'title'             => ['required','string','max:160'],
            'description'       => ['nullable','string'],
            'is_active'         => ['nullable','boolean'],
            'duration_minutes'  => ['nullable','integer','min:0','max:1440'],

            // dari builder:
            'questions_json'    => ['nullable','string'],

            // opsional: norm table untuk konversi raw -> IQ
            // format contoh:
            // [
            //   {"min_raw":0, "iq":70},
            //   {"min_raw":10,"iq":90},
            //   {"min_raw":20,"iq":110}
            // ]
            'norm_table_json'   => ['nullable','string'],
        ]);

        $questions   = $this->decodeQuestions($data['questions_json'] ?? null) ?? [];
        $normTable   = $this->decodeNormTable($data['norm_table_json'] ?? null);

        // Jika diaktifkan, wajib ada minimal 1 soal
        if (!empty($data['is_active']) && count($questions) < 1) {
            return back()
                ->withInput()
                ->withErrors(['questions_json' => 'Aktifkan test membutuhkan minimal 1 soal.']);
        }

        $payload = [
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'is_active'        => (bool)($data['is_active'] ?? false),
            'duration_minutes' => (int)($data['duration_minutes'] ?? 0),
            'questions'        => $questions, // simpan array terstruktur
            'meta'             => array_filter([
                'norm_table' => $normTable,  // null kalau tidak ada
            ], fn($v) => $v !== null),
        ];

        TestIq::create($payload);

        return redirect()
            ->route('admin.test-iq.index')
            ->with('success','Test IQ berhasil dibuat.');
    }

    /** Form edit */
    public function edit(TestIq $testIq)
    {
        // View builder akan baca $testIq->questions (array) & $testIq->meta['norm_table'] (opsional)
        return view('admin.test_iq.edit', compact('testIq'));
    }

    /** Update data */
    public function update(Request $r, TestIq $testIq)
    {
        $data = $r->validate([
            'title'             => ['required','string','max:160'],
            'description'       => ['nullable','string'],
            'is_active'         => ['nullable','boolean'],
            'duration_minutes'  => ['nullable','integer','min:0','max:1440'],

            // builder kirim string (boleh kosong kalau tidak ubah soal)
            'questions_json'    => ['nullable','string'],

            // opsional norm table
            'norm_table_json'   => ['nullable','string'],
        ]);

        // Decode keduanya (boleh null)
        $decodedQuestions = $this->decodeQuestions($data['questions_json'] ?? null);
        $decodedNorm      = $this->decodeNormTable($data['norm_table_json'] ?? null);

        // Kalau admin tidak mengirim questions_json (atau kosong),
        // jangan timpa pertanyaan lama.
        $finalQuestions = is_array($decodedQuestions)
            ? $decodedQuestions
            : ($testIq->questions ?? []);

        // Validasi ringan saat mau aktifkan
        $targetActive = (bool)($data['is_active'] ?? false);
        if ($targetActive && count($finalQuestions) < 1) {
            return back()
                ->withInput()
                ->withErrors(['questions_json' => 'Aktifkan test membutuhkan minimal 1 soal.']);
        }

        // Build meta baru (merge supaya tidak hilangkan key lain)
        $meta = $testIq->meta ?? [];
        if (array_key_exists('norm_table_json', $data)) {
            // Kalau field norm_table_json ikut dikirim (meski kosong), treat sebagai update:
            // - jika decode OK (array), timpa
            // - jika kosong/null, hapus key
            if (is_array($decodedNorm)) {
                $meta['norm_table'] = $decodedNorm;
            } else {
                unset($meta['norm_table']);
            }
        }

        $payload = [
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'is_active'        => $targetActive,
            'duration_minutes' => (int)($data['duration_minutes'] ?? 0),
            'questions'        => $finalQuestions,
            'meta'             => $meta,
        ];

        $testIq->update($payload);

        return redirect()
            ->route('admin.test-iq.index')
            ->with('success','Test IQ berhasil diperbarui.');
    }

    /** Hapus */
    public function destroy(TestIq $testIq)
    {
        $testIq->delete();
        return redirect()
            ->route('admin.test-iq.index')
            ->with('success','Test IQ dihapus.');
    }

    /** Toggle aktif/nonaktif (opsional) */
    public function toggle(TestIq $testIq)
    {
        // Toggle sederhana tanpa validasi jumlah soal
        $testIq->update(['is_active' => ! $testIq->is_active]);
        return back()->with('success','Status test diubah.');
    }

    /**
     * Decode & normalisasi struktur questions dari JSON string builder.
     * Standar disimpan per item:
     *   { id:int, text:string, options:string[], answer_index:int|null }
     *
     * @return array<int, array{id:int,text:string,options:array<int,string>,answer_index:int|null}>|null
     */
    private function decodeQuestions(?string $json): ?array
    {
        if (!$json || !is_string($json)) {
            return null; // biarkan null kalau kosong; caller yang putuskan menimpa atau tidak
        }

        try {
            $arr = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            abort(422, 'Format pertanyaan tidak valid: '.$e->getMessage());
        }

        if (!is_array($arr)) return [];

        $norm = [];
        $i = 1;
        foreach ($arr as $item) {
            // Normalisasi
            $text    = (string)($item['text'] ?? '');
            $options = array_values(array_map('strval', $item['options'] ?? []));

            // prefer answer_index, fallback answer (string yang harus match salah satu options)
            $answerIndex = null;
            if (array_key_exists('answer_index', $item) && $item['answer_index'] !== null) {
                $ai = $item['answer_index'];
                if (is_int($ai) && $ai >= 0 && $ai < count($options)) {
                    $answerIndex = $ai;
                }
            } elseif (array_key_exists('answer', $item) && $item['answer'] !== null) {
                $ans = (string)$item['answer'];
                $idx = array_search($ans, $options, true);
                if ($idx !== false) $answerIndex = (int)$idx;
            }

            $norm[] = [
                'id'           => (int)($item['id'] ?? $i),
                'text'         => $text,
                'options'      => $options,
                'answer_index' => $answerIndex,
            ];
            $i++;
        }

        return $norm;
    }

    /**
     * Decode norm table (opsional) dari JSON string.
     * Format disarankan (urut menaik by min_raw):
     * [
     *   {"min_raw":0,  "iq":70},
     *   {"min_raw":10, "iq":90},
     *   {"min_raw":20, "iq":110}
     * ]
     *
     * @return array<int, array{min_raw:int,iq:int}>|null
     */
    private function decodeNormTable(?string $json): ?array
    {
        if (!$json || !is_string($json)) {
            return null; // caller yang putuskan menimpa/hapus
        }

        try {
            $arr = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            abort(422, 'Format norm table tidak valid: '.$e->getMessage());
        }

        if (!is_array($arr)) return null;

        $out = [];
        foreach ($arr as $row) {
            $minRaw = isset($row['min_raw']) ? (int)$row['min_raw'] : null;
            $iq     = isset($row['iq']) ? (int)$row['iq'] : null;
            if ($minRaw === null || $iq === null) continue;
            $out[] = ['min_raw' => $minRaw, 'iq' => $iq];
        }

        // urutkan berdasarkan min_raw
        usort($out, fn($a, $b) => $a['min_raw'] <=> $b['min_raw']);

        return $out ?: null;
    }
}
