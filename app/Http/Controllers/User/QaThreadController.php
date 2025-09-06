<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{QaThread, Course, Lesson};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QaThreadController extends Controller
{

    /**
     * List semua thread (atau hanya milik saya jika ?mine=1).
     * Filter: q (judul), status (open|resolved|closed)
     */
    public function index(Request $r)
    {
        $threads = QaThread::query()
            ->with(['user:id,name', 'course:id,title', 'lesson:id,title'])
            ->withCount('replies')
            ->when($r->boolean('mine'), fn($q) => $q->where('user_id', Auth::id()))
            ->when($r->filled('q'), fn($q) => $q->where('title', 'like', '%'.$r->q.'%'))
            ->when($r->filled('status'), fn($q) => $q->where('status', $r->status))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('app.qa_threads.index', compact('threads'));
    }

    /**
     * Form buat thread baru.
     */
    public function create()
    {
        return view('app.qa_threads.create', [
            'courses' => Course::select('id','title')->orderBy('title')->get(),
            'lessons' => Lesson::select('id','title')->orderBy('title')->get(),
        ]);
    }

    /**
     * Simpan thread baru (user_id diambil dari Auth, bukan input).
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'course_id' => ['nullable','exists:courses,id'],
            'lesson_id' => ['nullable','exists:lessons,id'],
            'title'     => ['required','string','max:255'],
            'body'      => ['required','string'],
        ]);

        $data['user_id'] = Auth::id();
        $data['status']  = 'open';

        $thread = QaThread::create($data);

        return redirect()
            ->route('app.qa-threads.show', $thread)
            ->with('ok', 'Thread berhasil dibuat.');
    }

    /**
     * Detail thread + replies.
     */
    public function show(QaThread $qa_thread)
    {
        $qa_thread->load([
            'user:id,name',
            'course:id,title',
            'lesson:id,title',
            'replies.user:id,name',
        ]);

        // urutkan: jawaban yang ditandai (is_answer=1) ditaruh atas
        $qa_thread->setRelation('replies',
            $qa_thread->replies->sortByDesc('is_answer')->values()
        );

        return view('app.qa_threads.show', ['thread' => $qa_thread]);
    }

    /**
     * Edit thread (hanya pemilik).
     */
    public function edit(QaThread $qa_thread)
    {
        abort_unless($qa_thread->user_id === Auth::id(), 403);

        return view('app.qa_threads.edit', [
            'thread'  => $qa_thread,
            'courses' => Course::select('id','title')->orderBy('title')->get(),
            'lessons' => Lesson::select('id','title')->orderBy('title')->get(),
        ]);
    }

    /**
     * Update thread (hanya pemilik).
     */
    public function update(Request $r, QaThread $qa_thread)
    {
        abort_unless($qa_thread->user_id === Auth::id(), 403);

        $data = $r->validate([
            'course_id' => ['nullable','exists:courses,id'],
            'lesson_id' => ['nullable','exists:lessons,id'],
            'title'     => ['required','string','max:255'],
            'body'      => ['required','string'],
        ]);

        $qa_thread->update($data);

        return redirect()
            ->route('app.qa-threads.show', $qa_thread)
            ->with('ok', 'Thread diperbarui.');
    }

    /**
     * Hapus thread (hanya pemilik).
     */
    public function destroy(QaThread $qa_thread)
    {
        abort_unless($qa_thread->user_id === Auth::id(), 403);

        $qa_thread->delete();

        return redirect()
            ->route('app.qa-threads.index', ['mine' => 1])
            ->with('ok', 'Thread dihapus.');
    }
}
