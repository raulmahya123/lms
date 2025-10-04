<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            ->when(!$this->isAdminOrMentor(), fn($q) => $q->where('created_by', $user->id))
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
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'is_published' => 'nullable',       // checkbox
            'is_free'      => 'nullable|boolean',
            'price'        => 'nullable|numeric|min:0',
        ]);

        $isFree = $r->boolean('is_free');
        if (!$isFree && !isset($data['price'])) {
            return back()->withErrors(['price' => 'Harga wajib diisi untuk kursus berbayar.'])->withInput();
        }

        // Simpan file cover (jika ada) → path relatif, contoh: covers/abc.webp
        $coverRelPath = null;
        if ($r->hasFile('cover')) {
            $coverRelPath = $r->file('cover')->store('covers', 'public'); // storage/app/public/covers
        }

        Course::create([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'cover'        => $coverRelPath,                 // ← kolom tunggal `cover`
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
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'is_published' => 'nullable',
            'is_free'      => 'nullable|boolean',
            'price'        => 'nullable|numeric|min:0',
        ]);

        $isFree = $r->boolean('is_free');
        if (!$isFree && !isset($data['price'])) {
            return back()->withErrors(['price' => 'Harga wajib diisi untuk kursus berbayar.'])->withInput();
        }

        $coverRelPath = $course->cover;

        // Upload cover baru → hapus file lama jika ada
        if ($r->hasFile('cover')) {
            if ($coverRelPath && Storage::disk('public')->exists($coverRelPath)) {
                Storage::disk('public')->delete($coverRelPath);
            }
            $coverRelPath = $r->file('cover')->store('covers', 'public');
        }

        // Optional: tombol “hapus cover” di form (name="remove_cover" value="1")
        if ($r->boolean('remove_cover') && $coverRelPath) {
            if (Storage::disk('public')->exists($coverRelPath)) {
                Storage::disk('public')->delete($coverRelPath);
            }
            $coverRelPath = null;
        }

        $course->update([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'cover'        => $coverRelPath,
            'is_published' => $r->boolean('is_published'),
            'is_free'      => $isFree,
            'price'        => $isFree ? null : ($data['price'] ?? null),
        ]);

        return redirect()->route('admin.courses.index')->with('ok', 'Course berhasil diupdate');
    }

    public function destroy(Request $r, Course $course)
    {
        $this->authorizeCourse($course, $r->user());

        if ($course->cover && Storage::disk('public')->exists($course->cover)) {
            Storage::disk('public')->delete($course->cover);
        }

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

    /** ========================= Helpers (Akses) ========================= */
    protected function authorizeCourse(Course $course, $user): void
    {
        if (!$user) abort(403);

        if ($this->isAdminOrMentor()) {
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
}
