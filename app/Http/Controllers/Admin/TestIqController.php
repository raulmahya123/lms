<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestIq;
use Illuminate\Http\Request;

class TestIqController extends Controller
{
    /** Daftar test IQ (pencarian + paginate) */
    public function index(Request $r)
    {
        $q = TestIq::query()
            ->when($r->filled('q'), fn($qq) =>
                $qq->where('title', 'like', '%'.$r->q.'%')
                   ->orWhere('description', 'like', '%'.$r->q.'%')
            )
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
            // dikirim dari builder sebagai JSON string
            'questions_json'    => ['nullable','string'],
        ]);

        $payload = [
            'title'             => $data['title'],
            'description'       => $data['description'] ?? null,
            'is_active'         => (bool)($data['is_active'] ?? false),
            'duration_minutes'  => (int)($data['duration_minutes'] ?? 0),
            // simpan sebagai ARRAY terstruktur (bukan string JSON)
            'questions'         => $this->decodeQuestions($data['questions_json'] ?? null),
        ];

        TestIq::create($payload);

        return redirect()->route('admin.test-iq.index')
            ->with('success','Test IQ berhasil dibuat.');
    }

    /** Form edit */
    public function edit(TestIq $testIq)
    {
        // View builder akan baca $testIq->questions (array)
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
            'questions_json'    => ['nullable','string'],
        ]);

        $payload = [
            'title'             => $data['title'],
            'description'       => $data['description'] ?? null,
            'is_active'         => (bool)($data['is_active'] ?? false),
            'duration_minutes'  => (int)($data['duration_minutes'] ?? 0),
            'questions'         => $this->decodeQuestions($data['questions_json'] ?? null),
        ];

        $testIq->update($payload);

        return redirect()->route('admin.test-iq.index')
            ->with('success','Test IQ berhasil diperbarui.');
    }

    /** Hapus */
    public function destroy(TestIq $testIq)
    {
        $testIq->delete();
        return redirect()->route('admin.test-iq.index')
            ->with('success','Test IQ dihapus.');
    }

    /** Toggle aktif/nonaktif (opsional) */
    public function toggle(TestIq $testIq)
    {
        $testIq->update(['is_active' => ! $testIq->is_active]);
        return back()->with('success','Status test diubah.');
    }

    /**
     * Decode & normalisasi struktur questions dari JSON string builder.
     * Standar disimpan: [
     *   { id:int, text:string, options:string[], answer_index:int|null }
     * ]
     */
    private function decodeQuestions(?string $json): ?array
    {
        if (!$json || !is_string($json)) {
            return null; // biarkan null kalau kosong
        }

        try {
            $arr = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            // kembalikan HTTP 422 agar error tampil rapi di form
            abort(422, 'Format pertanyaan tidak valid: '.$e->getMessage());
        }

        if (!is_array($arr)) return null;

        $norm = [];
        $i = 1;
        foreach ($arr as $item) {
            $text    = (string)($item['text'] ?? '');
            $options = array_values(array_map('strval', $item['options'] ?? []));
            // dukung keduanya: answer_index (prefer) atau answer (string)
            $answerIndex = null;

            if (array_key_exists('answer_index', $item) && $item['answer_index'] !== null) {
                $ai = $item['answer_index'];
                if (is_int($ai) && $ai >= 0 && $ai < count($options)) {
                    $answerIndex = $ai;
                }
            } elseif (array_key_exists('answer', $item) && $item['answer'] !== null) {
                $ans = (string)$item['answer'];
                $idx = array_search($ans, $options, true);
                if ($idx !== false) $answerIndex = (int) $idx;
            }

            $norm[] = [
                'id'            => (int)($item['id'] ?? $i),
                'text'          => $text,
                'options'       => $options,
                'answer_index'  => $answerIndex,
            ];
            $i++;
        }

        return $norm;
    }
}
