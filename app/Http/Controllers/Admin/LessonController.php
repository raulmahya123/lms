<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Lesson, Module, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LessonController extends Controller
{
    // =====================
    // LIST
    // =====================
    public function index(Request $r)
    {
        $user = $r->user();
        if (!$user) abort(403);

        $lessons = Lesson::query()
            ->with([
                'module' => fn($q) => $q->select(['id', 'course_id', 'title']),
                'module.course' => fn($q) => $q->select(['id', 'title', 'created_by']),
                'driveWhitelists:id,lesson_id,status',
            ])
            ->when(!$this->isAdminOrMentor(), function ($q) use ($user) {
                $q->whereHas('module.course', fn($qc) => $qc->where('created_by', $user->id));
            })
            // === aktifkan ini kalau mentor hanya boleh lihat course ditugaskan
            // ->when($this->isMentorOnly(), function ($q) use ($user) {
            //     $q->whereHas('module.course.mentors', fn($qm) => $qm->whereKey($user->id))
            //       ->orWhereHas('module.course', fn($qc) => $qc->where('created_by', $user->id));
            // })
            ->when($r->filled('module_id'), fn($q) => $q->where('module_id', $r->input('module_id')))
            ->when($r->filled('q'), fn($q) => $q->where('title', 'like', '%' . $r->q . '%'))
            ->orderBy('module_id')->orderBy('ordering')
            ->paginate(20)
            ->withQueryString();

        return view('admin.lessons.index', compact('lessons'));
    }

    // =====================
    // CREATE FORM
    // =====================
    public function create(Request $r)
    {
        $user = $r->user();
        if (!$user) abort(403);

        $modules = Module::with('course:id,title,created_by')
            ->when(!$this->isAdminOrMentor(), function ($q) use ($user) {
                $q->whereHas('course', fn($qc) => $qc->where('created_by', $user->id));
            })
            ->orderBy('course_id')->orderBy('ordering')->get();

        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('admin.lessons.create', compact('modules', 'users'));
    }

    // =====================
    // STORE
    // =====================
    public function store(Request $r)
    {
        $user = $r->user();
        if (!$user) abort(403);

        $data = $r->validate([
            'module_id'              => 'required|exists:modules,id',
            'title'                  => 'required|string|max:255',

            // ===== Field baru
            'about'                  => 'nullable|string',
            'syllabus'               => 'nullable|string',
            'reviews'                => 'nullable|string',
            'tools'                  => 'nullable|string',
            'benefits'               => 'nullable|string',

            'content'                => 'nullable',
            'content_url'            => 'nullable|array',
            'content_url.*.title'    => 'required_with:content_url|string|max:255',
            'content_url.*.url'      => 'required_with:content_url|url',
            'ordering'               => 'nullable|integer|min:1',
            'is_free'                => 'boolean',
            'drive_user_ids'         => 'nullable|array|max:4',
            'drive_user_ids.*'       => 'required_with:drive_user_ids|integer|exists:users,id',
            'drive_link'             => 'nullable|url',
        ]);

        $data['ordering']    = $data['ordering'] ?? 1;
        $data['is_free']     = $r->boolean('is_free');
        $data['content']     = $this->coerceJsonToArray($data['content'] ?? null);
        $data['content_url'] = $data['content_url'] ?? [];

        $module = Module::with('course')->findOrFail($data['module_id']);
        if (!$this->isAdminOrMentor() && $module->course->created_by !== $user->id) {
            abort(403, 'Anda tidak boleh membuat lesson di course ini.');
        }

        $lesson = Lesson::create($data);

        // Drive whitelist (ambil email dari user id)
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

        return redirect()->route('admin.lessons.edit', $lesson)->with('ok', 'Lesson dibuat');
    }

    // =====================
    // EDIT FORM
    // =====================
    public function edit(Request $r, Lesson $lesson)
    {
        $this->authorizeLesson($lesson, $r->user());

        $modules = Module::with('course:id,title,created_by')
            ->when(!$this->isAdminOrMentor(), function ($q) use ($r) {
                $q->whereHas('course', fn($qc) => $qc->where('created_by', $r->user()->id));
            })
            ->orderBy('course_id')->orderBy('ordering')->get();

        $lesson->load(['resources', 'quiz', 'driveWhitelists.user']);
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('admin.lessons.edit', compact('lesson', 'modules', 'users'));
    }

    // =====================
    // UPDATE
    // =====================
    public function update(Request $r, Lesson $lesson)
    {
        $this->authorizeLesson($lesson, $r->user());

        $data = $r->validate([
            'module_id'              => 'required|exists:modules,id',
            'title'                  => 'required|string|max:255',

            // ===== Field baru
            'about'                  => 'nullable|string',
            'syllabus'               => 'nullable|string',
            'reviews'                => 'nullable|string',
            'tools'                  => 'nullable|string',
            'benefits'               => 'nullable|string',

            'content'                => 'nullable',
            'content_url'            => 'nullable|array',
            'content_url.*.title'    => 'required_with:content_url|string|max:255',
            'content_url.*.url'      => 'required_with:content_url|url',
            'ordering'               => 'nullable|integer|min:1',
            'is_free'                => 'boolean',
            'drive_user_ids'         => 'nullable|array|max:4',
            'drive_user_ids.*'       => 'required_with:drive_user_ids|integer|exists:users,id',
            'drive_link'             => 'nullable|url',
        ]);

        $data['is_free']     = $r->boolean('is_free');
        $data['content']     = $this->coerceJsonToArray($data['content'] ?? null);
        $data['content_url'] = $data['content_url'] ?? [];

        DB::transaction(function () use ($lesson, $r, $data) {
            $lesson->update($data);

            // Sinkron whitelist
            $emails = User::whereIn('id', $r->input('drive_user_ids', []))
                ->pluck('email')
                ->filter()
                ->map(fn($e) => mb_strtolower(trim($e)))
                ->unique()
                ->take(4)
                ->values()
                ->all();

            $lesson->syncDriveEmails($emails);
            $lesson->refresh();

            // Update status whitelist (opsional)
            $statuses = collect($r->input('whitelist_status', []))
                ->mapWithKeys(function ($status, $email) {
                    $email  = mb_strtolower(trim((string) $email));
                    $status = mb_strtolower(trim((string) $status));
                    if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
                        $status = 'pending';
                    }
                    return [$email => $status];
                });

            if ($statuses->isNotEmpty()) {
                $lesson->loadMissing('driveWhitelists');
                foreach ($lesson->driveWhitelists as $w) {
                    $key = mb_strtolower($w->email);
                    if ($statuses->has($key)) {
                        $old = $w->status;
                        $new = $statuses->get($key);

                        $w->status = $new;
                        $w->verified_at = ($new === 'approved' && $old !== 'approved') ? now()
                            : ($new !== 'approved' ? null : $w->verified_at);
                        $w->save();
                    }
                }
            }

            $lesson->forceFill(['drive_emails' => $emails])->save();
        });

        return back()->with('ok', 'Lesson diupdate');
    }

    // =====================
    // DELETE
    // =====================
    public function destroy(Request $r, Lesson $lesson)
    {
        $this->authorizeLesson($lesson, $r->user());

        $lesson->delete();
        return redirect()->route('admin.lessons.index')->with('ok', 'Lesson dihapus');
    }

    // =====================
    // SHOW
    // =====================
    public function show(Request $r, Lesson $lesson)
    {
        $this->authorizeLesson($lesson, $r->user());

        $lesson->load('module.course');

        // Normalisasi content_url -> array
        $videos = $lesson->content_url;
        if (is_string($videos)) {
            $decoded = json_decode($videos, true);
            $videos  = is_array($decoded) ? $decoded : [];
        }

        $active = request()->integer('v', 0);
        if ($active < 0 || $active >= count($videos)) $active = 0;

        // ==== Tambahan: siapkan versi string untuk field yang bisa array/JSON
        $about    = $this->stringifyForDisplay($lesson->about ?? null);
        $syllabus = $this->stringifyForDisplay($lesson->syllabus ?? null);
        $reviews  = $this->stringifyForDisplay($lesson->reviews ?? null);

        // tools & benefits biarkan apa adanya; ditangani di Blade sebagai list
        return view('admin.lessons.show', compact('lesson', 'videos', 'active', 'about', 'syllabus', 'reviews'));
    }

    /**
     * Ubah nilai (array/object/string/null) menjadi string aman untuk ditampilkan.
     */
    protected function stringifyForDisplay($value): string
    {
        if (is_null($value)) return '';
        if (is_string($value)) return $value;

        // Jika array/object: tampilkan sebagai JSON pretty agar tidak melempar error
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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

    protected function authorizeLesson(Lesson $lesson, User $user)
    {
        if ($this->isAdminOrMentor()) {
            return true;
        }

        $lesson->loadMissing('module.course');
        if ($lesson->module->course->created_by !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses lesson ini.');
        }
    }

    protected function isAdminOrMentor(): bool
    {
        return Gate::allows('admin') || Gate::allows('mentor');
    }

    protected function isMentorOnly(): bool
    {
        return Gate::allows('mentor') && !Gate::allows('admin');
    }
}
