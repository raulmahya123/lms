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

            // builder:
            'questions_json'    => ['nullable','string'],

            // norm table:
            'norm_table_json'   => ['nullable','string'],

            // meta tambahan (opsional)
            'meta_json'         => ['nullable','string'],
            'cooldown_seconds'  => ['nullable','integer','min:0'],
            'cooldown_minutes'  => ['nullable','integer','min:0'],
            'cooldown_hours'    => ['nullable','integer','min:0'],
            'cooldown_days'     => ['nullable','integer','min:0'],
            'cooldown_clear'    => ['nullable','boolean'],

            // PERIODISASI (opsional UI kamu)
            'schedule_mode'     => ['nullable','in:weekly,monthly'],
            'weekly_days'       => ['nullable','array'],
            'weekly_days.*'     => ['integer','between:0,6'],
            'monthly_day'       => ['nullable','integer','between:1,31'],

            // ⬇️ INI YANG DITAMBAH (sesuai Model kamu)
            'cooldown_value'    => ['nullable','integer','min:0'],
            'cooldown_unit'     => ['nullable','in:day,week,month'],
        ]);

        $questions = $this->decodeQuestions($data['questions_json'] ?? null) ?? [];
        $normTable = $this->decodeNormTable($data['norm_table_json'] ?? null);

        // Jika diaktifkan, wajib ada minimal 1 soal
        if (!empty($data['is_active']) && count($questions) < 1) {
            return back()
                ->withInput()
                ->withErrors(['questions_json' => 'Aktifkan test membutuhkan minimal 1 soal.']);
        }

        // Build meta dari request (baru)
        $meta = $this->buildMetaFromRequest($r, $normTable);

        $payload = [
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'is_active'        => $r->boolean('is_active'),
            'duration_minutes' => (int)($data['duration_minutes'] ?? 0),
            'questions'        => $questions,
            'meta'             => $meta,
        ];

        // ⬇️ TULIS cooldown_value & cooldown_unit KE KOLOM MODEL
        $payload['cooldown_value'] = (int) $r->input('cooldown_value', 0);
        $payload['cooldown_unit']  = $r->input('cooldown_unit', 'day');

        TestIq::create($payload);

        return redirect()
            ->route('admin.test-iq.index')
            ->with('success','Test IQ berhasil dibuat.');
    }

    /** Form edit */
    public function edit(TestIq $testIq)
    {
        // View builder akan baca $testIq->questions (array) & $testIq->meta (array)
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

            // norm table
            'norm_table_json'   => ['nullable','string'],

            // meta tambahan (opsional)
            'meta_json'         => ['nullable','string'],
            'cooldown_seconds'  => ['nullable','integer','min:0'],
            'cooldown_minutes'  => ['nullable','integer','min:0'],
            'cooldown_hours'    => ['nullable','integer','min:0'],
            'cooldown_days'     => ['nullable','integer','min:0'],
            'cooldown_clear'    => ['nullable','boolean'],

            // periodisasi (opsional)
            'schedule_mode'     => ['nullable','in:weekly,monthly'],
            'weekly_days'       => ['nullable','array'],
            'weekly_days.*'     => ['integer','between:0,6'],
            'monthly_day'       => ['nullable','integer','between:1,31'],

            // ⬇️ INI YANG DITAMBAH
            'cooldown_value'    => ['nullable','integer','min:0'],
            'cooldown_unit'     => ['nullable','in:day,week,month'],
        ]);

        // Decode keduanya (boleh null)
        $decodedQuestions = $this->decodeQuestions($data['questions_json'] ?? null);
        $decodedNorm      = $this->decodeNormTable($data['norm_table_json'] ?? null);

        // Kalau admin tidak mengirim questions_json (atau kosong), jangan timpa pertanyaan lama.
        $finalQuestions = is_array($decodedQuestions)
            ? $decodedQuestions
            : ($testIq->questions ?? []);

        // Validasi ringan saat mau aktifkan
        $targetActive = $r->boolean('is_active');
        if ($targetActive && count($finalQuestions) < 1) {
            return back()
                ->withInput()
                ->withErrors(['questions_json' => 'Aktifkan test membutuhkan minimal 1 soal.']);
        }

        // Merge meta lama + input baru (termasuk cooldown_seconds & schedule)
        $meta = $this->buildMetaFromRequest($r, $decodedNorm, $testIq->meta ?? []);

        $payload = [
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'is_active'        => $targetActive,
            'duration_minutes' => (int)($data['duration_minutes'] ?? 0),
            'questions'        => $finalQuestions,
            'meta'             => $meta,
        ];

        // ⬇️ HANYA TIMPA KALAU FIELD DIKIRIM (AMAN UNTUK FORM PARSIAL)
        if ($r->has('cooldown_value')) {
            $payload['cooldown_value'] = (int) $r->input('cooldown_value', 0);
        }
        if ($r->has('cooldown_unit')) {
            $payload['cooldown_unit']  = $r->input('cooldown_unit', 'day');
        }

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
        $testIq->update(['is_active' => ! $testIq->is_active]);
        return back()->with('success','Status test diubah.');
    }

    /**
     * ===== Helpers =====
     */

    /**
     * Build/merge meta dari Request.
     * - Bisa terima meta_json (di-merge ke base).
     * - Atur/hapus norm_table.
     * - Hitung cooldown_seconds dari seconds | minutes/hours/days.
     * - Bisa clear cooldown via cooldown_clear=1.
     * - Opsional: schedule (weekly/monthly).
     */
    private function buildMetaFromRequest(Request $r, ?array $normTable, array $base = []): array
    {
        $meta = $base;

        // 1) meta_json (prioritas merge)
        if ($r->filled('meta_json')) {
            try {
                $incoming = json_decode($r->input('meta_json'), true, 512, JSON_THROW_ON_ERROR);
                if (is_array($incoming)) {
                    $meta = array_replace($meta, $incoming);
                }
            } catch (\Throwable $e) {
                abort(422, 'Format meta_json tidak valid: '.$e->getMessage());
            }
        }

        // 2) norm_table
        if ($r->has('norm_table_json')) {
            if (is_array($normTable)) {
                $meta['norm_table'] = $normTable;
            } else {
                unset($meta['norm_table']);
            }
        } elseif ($normTable !== null) {
            $meta['norm_table'] = $normTable;
        }

        // 3) cooldown_seconds (meta): clear jika diminta
        if ($r->boolean('cooldown_clear')) {
            unset($meta['cooldown_seconds'], $meta['cooldown_raw']);
        }

        // Jika ada salah satu input cooldown, hitung & set
        if (
            $r->has('cooldown_seconds') ||
            $r->has('cooldown_minutes') ||
            $r->has('cooldown_hours')   ||
            $r->has('cooldown_days')
        ) {
            $seconds = (int) $r->input('cooldown_seconds', 0);
            $minutes = (int) $r->input('cooldown_minutes', 0);
            $hours   = (int) $r->input('cooldown_hours', 0);
            $days    = (int) $r->input('cooldown_days', 0);

            if (!$r->filled('cooldown_seconds')) {
                $seconds = ($days * 86400) + ($hours * 3600) + ($minutes * 60);
            }

            $seconds = max(0, $seconds);
            $meta['cooldown_seconds'] = $seconds;

            $meta['cooldown_raw'] = [
                'days'    => $days,
                'hours'   => $hours,
                'minutes' => $minutes,
                'seconds' => $r->input('cooldown_seconds', null) !== null ? (int)$r->input('cooldown_seconds') : null,
            ];
        }

        // 4) schedule (opsional)
        if ($r->filled('schedule_mode')) {
            $meta['schedule_mode'] = $r->input('schedule_mode'); // weekly|monthly
        }
        if ($r->has('weekly_days')) {
            $meta['weekly_days'] = array_values(array_map('intval', $r->input('weekly_days', [])));
        }
        if ($r->filled('monthly_day')) {
            $meta['monthly_day'] = (int) $r->input('monthly_day');
        }

        return $meta;
    }

    /**
     * Decode & normalisasi struktur questions dari JSON string builder.
     * Standar per item:
     *   { id:int, text:string, options:string[], answer_index:int|null }
     *
     * @return array<int, array{id:int,text:string,options:array<int,string>,answer_index:int|null}>|null
     */
    private function decodeQuestions(?string $json): ?array
    {
        if (!$json || !is_string($json)) {
            return null;
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
            return null;
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
