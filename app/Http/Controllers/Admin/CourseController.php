<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    public function index(Request $r)
    {
        $user = $r->user();
        if (!$user) abort(403);

        $courses = Course::query()
            ->withCount('modules')
            ->when($r->filled('q'), fn($q) => $q->where('title', 'like', '%'.$r->q.'%'))
            ->when($r->filled('published'), function ($q) use ($r) {
                if ($r->published === '1') $q->where('is_published', 1);
                if ($r->published === '0') $q->where('is_published', 0);
            })
            ->when(!$this->isAdminOrMentor(), function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $r)
    {
        $user = $r->user();
        if (!$user) abort(403);

        $data = $r->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Konsisten: izinkan http/https ATAU path lokal /storage/...
            'cover_url'    => ['nullable', 'regex:/^(https?:\/\/.+|\/[A-Za-z0-9_\-\/\.]+)$/'],
            'is_published' => 'nullable',
            'is_free'      => 'nullable|boolean',
            'price'        => 'nullable|numeric|min:0',
        ]);

        $isFree = $r->boolean('is_free');
        if (!$isFree && !isset($data['price'])) {
            return back()
                ->withErrors(['price' => 'Harga wajib diisi untuk kursus berbayar.'])
                ->withInput();
        }

        // Cover handling
        $finalCoverUrl = null;
        if ($r->hasFile('cover')) {
            $path = $r->file('cover')->store('covers', 'public');
            $finalCoverUrl = Storage::disk('public')->url($path); // → /storage/covers/xxx
        } elseif (!empty($data['cover_url'])) {
            $finalCoverUrl = $data['cover_url']; // bisa http/https atau /storage/...
        }

        Course::create([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'cover_url'    => $finalCoverUrl,
            'is_published' => $r->boolean('is_published'),
            'created_by'   => Auth::id(),
            'is_free'      => $isFree,
            'price'        => $isFree ? null : ($data['price'] ?? null),
        ]);

        return redirect()->route('admin.courses.index')->with('ok', 'Course dibuat');
    }

    public function edit(Request $r, Course $course)
    {
        $this->authorizeCourse($course, $r->user());
        $course->load('modules');
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $r, Course $course)
    {
        $this->authorizeCourse($course, $r->user());

        $data = $r->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // FIX: samakan validasi dengan store, jangan pakai 'url' agar /storage/... lolos
            'cover_url'    => ['nullable', 'regex:/^(https?:\/\/.+|\/[A-Za-z0-9_\-\/\.]+)$/'],
            'is_published' => 'nullable',
            'is_free'      => 'nullable|boolean',
            'price'        => 'nullable|numeric|min:0',
        ]);

        $isFree = $r->boolean('is_free');
        if (!$isFree && !isset($data['price'])) {
            return back()
                ->withErrors(['price' => 'Harga wajib diisi untuk kursus berbayar.'])
                ->withInput();
        }

        // Cover handling
        $finalCoverUrl = $course->cover_url;

        if ($r->hasFile('cover')) {
            $this->deleteOldLocalCoverIfAny($course->cover_url);
            $path = $r->file('cover')->store('covers', 'public');
            $finalCoverUrl = Storage::disk('public')->url($path);
        } elseif (array_key_exists('cover_url', $data)) {
            // User mengosongkan field → hapus cover lama (jika lokal)
            if (empty($data['cover_url'])) {
                $this->deleteOldLocalCoverIfAny($course->cover_url);
                $finalCoverUrl = null;
            } else {
                // Ganti cover URL → hapus file lama jika lokal
                if ($data['cover_url'] !== $course->cover_url) {
                    $this->deleteOldLocalCoverIfAny($course->cover_url);
                }
                $finalCoverUrl = $data['cover_url'];
            }
        }

        $course->update([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'cover_url'    => $finalCoverUrl,
            'is_published' => $r->boolean('is_published'),
            'is_free'      => $isFree,
            'price'        => $isFree ? null : ($data['price'] ?? null),
        ]);

        return redirect()->route('admin.courses.index')->with('ok', 'Course berhasil diupdate');
    }

    public function destroy(Request $r, Course $course)
    {
        $this->authorizeCourse($course, $r->user());

        $this->deleteOldLocalCoverIfAny($course->cover_url);
        $course->delete();

        return redirect()->route('admin.courses.index')->with('ok', 'Course dihapus');
    }

    public function modules(Request $r, Course $course)
    {
        $this->authorizeCourse($course, $r->user());

        return response()->json(
            $course->modules()
                ->select('id', 'title', 'ordering')
                ->orderBy('ordering')
                ->get()
        );
    }

    /** Hapus file lama jika cover_url menunjuk ke /storage/... */
    protected function deleteOldLocalCoverIfAny(?string $coverUrl): void
    {
        if (!$coverUrl) return;

        $pathPart = parse_url($coverUrl, PHP_URL_PATH) ?: $coverUrl;

        if (Str::startsWith($pathPart, ['/storage/', 'storage/'])) {
            $relative = ltrim(Str::after($pathPart, '/storage/'), '/');
            if ($relative && Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            }
        }
    }

    /** ========================= Helpers (Akses) ========================= */
    protected function authorizeCourse(Course $course, $user): void
    {
        if (!$user) abort(403);

        if ($this->isAdminOrMentor()) {
            // (Opsional) jika ingin mentor hanya boleh course yang ditugaskan, aktifkan blok pivot di sini
            return;
        }

        if ($course->created_by !== $user->id) {
            abort(403, 'Anda tidak berhak mengelola course ini.');
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
