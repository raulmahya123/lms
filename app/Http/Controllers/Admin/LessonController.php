<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Lesson, Module, User};
use Illuminate\Http\Request;

class LessonController extends Controller
{
    // =====================
    // LIST
    // =====================
    public function index(Request $r)
    {
        $lessons = Lesson::query()
            ->with([
                'module' => fn($q) => $q->select(['id','course_id','title']),
                'module.course' => fn($q) => $q->select(['id','title']),
                // preload whitelist (ringkas; ambil status untuk badge)
                'driveWhitelists:id,lesson_id,status',
            ])
            // NOTE: kalau module_id kamu UUID, pakai input biasa (bukan ->integer())
            ->when($r->filled('module_id'), fn($q) => $q->where('module_id', $r->input('module_id')))
            ->when($r->filled('q'), fn($q) => $q->where('title', 'like', '%'.$r->q.'%'))
            ->orderBy('module_id')->orderBy('ordering')
            ->paginate(20)
            ->withQueryString();

        return view('admin.lessons.index', compact('lessons'));
    }

    // =====================
    // CREATE FORM
    // =====================
    public function create()
    {
        $modules = Module::with('course:id,title')
            ->orderBy('course_id')->orderBy('ordering')->get();

        $users = User::select('id','name','email')->orderBy('name')->get();

        return view('admin.lessons.create', compact('modules','users'));
    }

    // =====================
    // STORE
    // =====================
    public function store(Request $r)
    {
        $data = $r->validate([
            'module_id'              => 'required|exists:modules,id',
            'title'                  => 'required|string|max:255',

            // content bisa string JSON (textarea) atau array
            'content'                => 'nullable',

            'content_url'            => 'nullable|array',
            'content_url.*.title'    => 'required_with:content_url|string|max:255',
            'content_url.*.url'      => 'required_with:content_url|url',

            'ordering'               => 'nullable|integer|min:1',
            'is_free'                => 'boolean',

            // whitelist by user_id (maks 4)
            'drive_user_ids'         => 'nullable|array|max:4',
            'drive_user_ids.*'       => 'required_with:drive_user_ids|integer|exists:users,id',

            // kolom opsional di lessons
            'drive_link'             => 'nullable|url',
            // ⛔️ HAPUS drive_status — tidak dipakai
        ]);

        $data['ordering']    = $data['ordering'] ?? 1;
        $data['is_free']     = $r->boolean('is_free');
        $data['content']     = $this->coerceJsonToArray($data['content'] ?? null);
        $data['content_url'] = $data['content_url'] ?? [];

        $lesson = Lesson::create($data);

        // Map user_id -> email (lowercase, unik, max 4)
        $emails = User::whereIn('id', $r->input('drive_user_ids', []))
            ->pluck('email')
            ->filter()
            ->map(fn($e) => mb_strtolower(trim($e)))
            ->unique()
            ->take(4)
            ->values()
            ->all();

        // Sinkron ke tabel whitelist & cache email ke kolom lessons (opsional)
        $lesson->syncDriveEmails($emails);
        $lesson->forceFill(['drive_emails' => $emails])->save();

        return redirect()->route('admin.lessons.edit', $lesson)->with('ok', 'Lesson dibuat');
    }

    // =====================
    // EDIT FORM
    // =====================
    public function edit(Lesson $lesson)
    {
        $modules = Module::orderBy('course_id')->orderBy('ordering')->get();

        // preload relasi + whitelist beserta user
        $lesson->load(['resources','quiz','driveWhitelists.user']);

        $users = User::select('id','name','email')->orderBy('name')->get();

        return view('admin.lessons.edit', compact('lesson','modules','users'));
    }

    // =====================
    // UPDATE
    // =====================
    public function update(Request $r, Lesson $lesson)
    {
        $data = $r->validate([
            'module_id'              => 'required|exists:modules,id',
            'title'                  => 'required|string|max:255',

            'content'                => 'nullable',

            'content_url'            => 'nullable|array',
            'content_url.*.title'    => 'required_with:content_url|string|max:255',
            'content_url.*.url'      => 'required_with:content_url|url',

            'ordering'               => 'nullable|integer|min:1',
            'is_free'                => 'boolean',

            'drive_user_ids'         => 'nullable|array|max:4',
            'drive_user_ids.*'       => 'required_with:drive_user_ids|integer|exists:users,id',

            'drive_link'             => 'nullable|url',
            // ⛔️ HAPUS drive_status — tidak dipakai
        ]);

        $data['is_free']     = $r->boolean('is_free');
        $data['content']     = $this->coerceJsonToArray($data['content'] ?? null);
        $data['content']     = $this->coerceJsonToArray($data['content'] ?? null);
        $data['content_url'] = $data['content_url'] ?? [];

        $lesson->update($data);

        // Map user_id -> email (lowercase, unik, max 4)
        $emails = User::whereIn('id', $r->input('drive_user_ids', []))
            ->pluck('email')
            ->filter()
            ->map(fn($e) => mb_strtolower(trim($e)))
            ->unique()
            ->take(4)
            ->values()
            ->all();

        $lesson->syncDriveEmails($emails);
        $lesson->forceFill(['drive_emails' => $emails])->save();

        return back()->with('ok', 'Lesson diupdate');
    }

    // =====================
    // DELETE
    // =====================
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('admin.lessons.index')->with('ok', 'Lesson dihapus');
    }

    // =====================
    // SHOW (viewer/admin)
    // =====================
    public function show(Lesson $lesson)
    {
        $lesson->load('module.course');

        // pastikan content_url array (handle jika masih string JSON di DB lawas)
        $videos = $lesson->content_url;
        if (is_string($videos)) {
            $decoded = json_decode($videos, true);
            $videos  = is_array($decoded) ? $decoded : [];
        }

        $active = request()->integer('v', 0);
        if ($active < 0 || $active >= count($videos)) $active = 0;

        return view('admin.lessons.show', compact('lesson','videos','active'));
    }

    // =====================
    // Helpers
    // =====================
    protected function coerceJsonToArray($value): array
    {
        if (is_array($value)) return $value;
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
}
