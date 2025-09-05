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
            ->when($r->filled('q'), fn($q) => $q->where('title', 'like', '%'.$r->q.'%'))
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
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // ≤2MB
           'cover_url'   => [
        'nullable',
        'regex:/^(https?:\/\/.+|\/[A-Za-z0-9_\-\/\.]+)$/'
    ],
            'is_published' => 'nullable',     // dinormalkan di bawah
        ]);

        // Tentukan sumber cover: file upload > url manual > null
        $finalCoverUrl = null;

        if ($r->hasFile('cover')) {
            $path = $r->file('cover')->store('covers', 'public');
            $finalCoverUrl = Storage::disk('public')->url($path); // biasanya "/storage/covers/xxx.webp"
        } elseif (!empty($data['cover_url'])) {
            $finalCoverUrl = $data['cover_url'];
        }

        $course = Course::create([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'cover_url'    => $finalCoverUrl,
            'is_published' => $r->boolean('is_published'),
            'created_by'   => Auth::id(),
        ]);

        return redirect()
            ->route('admin.courses.index')
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
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_url'    => 'nullable|url', // ends_with DIHAPUS
            'is_published' => 'nullable',
        ]);

        $finalCoverUrl = $course->cover_url;

        // PRIORITAS 1: upload file -> timpa cover lama
        if ($r->hasFile('cover')) {
            $this->deleteOldLocalCoverIfAny($course->cover_url);

            $path = $r->file('cover')->store('covers', 'public');
            $finalCoverUrl = Storage::disk('public')->url($path);
        }
        // PRIORITAS 2: tidak upload file, tapi form mengirim cover_url (bisa kosong atau diisi)
        elseif (array_key_exists('cover_url', $data)) {
            if (empty($data['cover_url'])) {
                // user mengosongkan -> hapus file lokal lama bila ada
                $this->deleteOldLocalCoverIfAny($course->cover_url);
                $finalCoverUrl = null;
            } else {
                // user ganti ke URL baru -> hapus file lokal lama bila ada
                $this->deleteOldLocalCoverIfAny($course->cover_url);
                $finalCoverUrl = $data['cover_url'];
            }
        }
        // PRIORITAS 3: tidak upload file & tidak kirim cover_url -> biarkan yang lama

        $course->update([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'cover_url'    => $finalCoverUrl,
            'is_published' => $r->boolean('is_published'),
        ]);

        return redirect()
            ->route('admin.courses.index')
            ->with('ok', 'Course berhasil diupdate');
    }

    public function destroy(Course $course)
    {
        // Hapus file cover lokal kalau ada
        $this->deleteOldLocalCoverIfAny($course->cover_url);

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('ok', 'Course dihapus');
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
     * Hapus file lama jika cover_url menunjuk ke berkas lokal di disk 'public' (/storage/...).
     * Mendukung path absolut atau full URL (https://domainmu/storage/...).
     */
    protected function deleteOldLocalCoverIfAny(?string $coverUrl): void
    {
        if (!$coverUrl) return;

        // Ambil path dari URL (jika full URL), atau pakai apa adanya
        $pathPart = parse_url($coverUrl, PHP_URL_PATH) ?: $coverUrl;

        // Hanya kalau memang mengarah ke /storage/...
        if (Str::startsWith($pathPart, ['/storage/', 'storage/'])) {
            // Normalisasi ke path relatif di disk 'public'
            $relative = ltrim(Str::after($pathPart, '/storage/'), '/'); // ex: "covers/xxx.webp"

            if ($relative && Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            }
        }
    }
}
