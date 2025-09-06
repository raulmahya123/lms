<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestIq;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            // questions akan diisi via textarea JSON, validasi basic dulu
            'questions_json'    => ['nullable','string'],
        ]);

        $payload = [
            'title'             => $data['title'],
            'description'       => $data['description'] ?? null,
            'is_active'         => (bool)($data['is_active'] ?? false),
            'duration_minutes'  => (int)($data['duration_minutes'] ?? 0),
            'questions'         => $this->decodeQuestions($data['questions_json'] ?? null),
        ];

        TestIq::create($payload);

        return redirect()->route('admin.test-iq.index')
            ->with('success','Test IQ berhasil dibuat.');
    }

    /** Form edit */
    public function edit(TestIq $testIq)
    {
        // stringify questions utk textarea
        $questions_json = json_encode($testIq->questions ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        return view('admin.test_iq.edit', compact('testIq','questions_json'));
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

    /** Util: decode dan normalisasi struktur questions */
    private function decodeQuestions(?string $json): ?array
    {
        if (!$json) return null;

        try {
            $arr = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            // Jika JSON invalid, lempar balik ke form dengan pesan rapi
            abort(422, 'Format JSON tidak valid: '.$e->getMessage());
        }

        // Normalisasi minimal: setiap item harus punya id, text, options[], answer
        $norm = [];
        $i = 1;
        foreach ($arr as $item) {
            $norm[] = [
                'id'      => $item['id']      ?? $i,
                'text'    => $item['text']    ?? '',
                'options' => array_values($item['options'] ?? []),
                'answer'  => $item['answer']  ?? null,
            ];
            $i++;
        }
        return $norm;
    }
}
