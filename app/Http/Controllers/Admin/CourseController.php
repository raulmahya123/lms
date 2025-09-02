<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $r)
    {
        $courses = Course::query()
            ->withCount('modules')
            ->when($r->filled('q'), fn($q) => $q->where('title','like','%'.$r->q.'%'))
            ->when($r->filled('published'), function ($q) use ($r) {
                if ($r->published === '1') $q->where('is_published', 1);
                if ($r->published === '0') $q->where('is_published', 0);
            })
            ->latest('id')
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
        $data = $r->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // ≤2MB
            'cover_url'   => 'nullable|url|ends_with:jpg,jpeg,png,webp',
            'is_published'=> 'nullable', // checkbox
        ]);

        // Tentukan sumber cover: file upload > url manual > null
        $finalCoverUrl = null;

        if ($r->hasFile('cover')) {
            $path = $r->file('cover')->store('covers', 'public'); // storage/app/public/covers
            $finalCoverUrl = Storage::disk('public')->url($path); // /storage/covers/xxxx.jpg
        } elseif (!empty($data['cover_url'])) {
            $finalCoverUrl = $data['cover_url'];
        }

        $course = Course::create([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'cover_url'    => $finalCoverUrl,
            'is_published' => $r->has('is_published') ? 1 : 0,
            'created_by'   => Auth::id(),
        ]);

        return redirect()
            ->route('admin.courses.index', $course)
            ->with('ok', 'Course dibuat');
    }

    public function edit(Course $course)
    {
        $course->load('modules');
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $r, Course $course)
    {
        $data = $r->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_url'   => 'nullable|url|ends_with:jpg,jpeg,png,webp',
            'is_published'=> 'nullable',
        ]);

        $finalCoverUrl = $course->cover_url;

        // Jika user upload file baru → simpan & (opsional) hapus file lama jika lokal
        if ($r->hasFile('cover')) {
            // Hapus file lama kalau berasal dari storage lokal
            $this->deleteOldLocalCoverIfAny($course->cover_url);

            $path = $r->file('cover')->store('covers', 'public');
            $finalCoverUrl = Storage::disk('public')->url($path);
        } elseif (array_key_exists('cover_url', $data)) {
            // User mengubah URL manual (atau mengosongkan)
            // Jika mengosongkan & sebelumnya file lokal, hapus file lokal
            if (empty($data['cover_url'])) {
                $this->deleteOldLocalCoverIfAny($course->cover_url);
                $finalCoverUrl = null;
            } else {
                // Ganti ke URL eksternal → hapus file lokal lama jika ada
                $this->deleteOldLocalCoverIfAny($course->cover_url);
                $finalCoverUrl = $data['cover_url'];
            }
        }

        $course->update([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'cover_url'    => $finalCoverUrl,
            'is_published' => $r->has('is_published') ? 1 : 0,
        ]);

         // -> balik ke index
    return redirect()->route('admin.courses.index')
        ->with('ok', 'Course berhasil diupdate');
    }

    public function destroy(Course $course)
    {
        // Hapus file cover lokal kalau ada
        $this->deleteOldLocalCoverIfAny($course->cover_url);

        $course->delete();
        return redirect()
            ->route('admin.courses.index')
            ->with('ok','Course dihapus');
    }

    // Opsional: API daftar modules untuk course tertentu
    public function modules(Course $course)
    {
        return response()->json(
            $course->modules()
                ->select('id','title','ordering')
                ->orderBy('ordering')
                ->get()
        );
    }

    /**
     * Hapus file lama jika cover_url mengarah ke storage lokal (/storage/...).
     * Supaya aman, kita hanya menghapus jika path-nya berada di disk 'public'.
     */
    protected function deleteOldLocalCoverIfAny(?string $coverUrl): void
    {
        if (!$coverUrl) return;

        // Biasanya Storage::url() memberi "/storage/..."
        // Kita terjemahkan kembali menjadi path di disk 'public'.
        if (Str::startsWith($coverUrl, ['/storage/', 'storage/'])) {
            $relative = ltrim(str_replace('/storage/', '', $coverUrl), '/'); // "covers/xxx.jpg"
            if (Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            }
        }
    }
}
