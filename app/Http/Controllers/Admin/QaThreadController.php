<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{QaThread, Course, Lesson, User};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QaThreadController extends Controller
{
    public function index(Request $r)
    {
        $filters = $r->validate([
            'q'      => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['open', 'resolved', 'closed'])],
        ]);
        $term = trim((string) ($filters['q'] ?? ''));

        $threads = QaThread::query()
            ->select(['id', 'user_id', 'course_id', 'lesson_id', 'title', 'status', 'created_at'])
            ->with(['user:id,name,email','course:id,title','lesson:id,title'])
            ->withCount('replies')
            ->when($term !== '', fn($q) => $q->where('title','like','%'.$term.'%'))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status',$status))
            // ⚠️ UUID tidak bisa dipakai untuk sort kronologis → gunakan created_at
            ->latest() // default: created_at desc
            ->paginate(20)
            ->withQueryString();

        return view('admin.qa_threads.index', compact('threads'));
    }

    public function create()
    {
        return view('admin.qa_threads.create', [
            'courses' => Course::select('id','title')->orderBy('title')->get(),
            'lessons' => Lesson::select('id','title')->orderBy('title')->get(),
            // Tambahkan users untuk dropdown (mengatasi “user-nya kok ga muncul?”)
            'users'   => User::select('id','name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            // UUID-friendly: tetap pakai exists ke kolom id (string UUID)
            'user_id'   => ['required','exists:users,id','uuid'],
            'course_id' => ['nullable','exists:courses,id','uuid'],
            'lesson_id' => ['nullable','exists:lessons,id','uuid'],
            'title'     => ['required','string','max:255'],
            'body'      => ['required','string'],
            'status'    => ['nullable','in:open,resolved,closed'],
        ]);
        $data['status'] = $data['status'] ?? 'open';

        $thread = QaThread::create($data);

        return redirect()
            ->route('admin.qa-threads.show', $thread) // implicit binding by UUID ok
            ->with('success','Thread created');
    }

    public function show(QaThread $qa_thread)
    {
        $qa_thread->load([
            'user:id,name,email',
            'course:id,title',
            'lesson:id,title',
            'replies' => fn($q) => $q
                ->select(['id', 'thread_id', 'user_id', 'body', 'is_answer', 'created_at'])
                ->with('user:id,name,email')
                ->orderByDesc('is_answer')
                ->orderBy('created_at'),
        ]);
        return view('admin.qa_threads.show', ['thread' => $qa_thread]);
    }

    public function edit(QaThread $qa_thread)
    {
        return view('admin.qa_threads.edit', [
            'thread'  => $qa_thread,
            'courses' => Course::select('id','title')->orderBy('title')->get(),
            'lessons' => Lesson::select('id','title')->orderBy('title')->get(),
            'users'   => User::select('id','name')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $r, QaThread $qa_thread)
    {
        $data = $r->validate([
            'user_id'   => ['required','exists:users,id','uuid'],
            'course_id' => ['nullable','exists:courses,id','uuid'],
            'lesson_id' => ['nullable','exists:lessons,id','uuid'],
            'title'     => ['required','string','max:255'],
            'body'      => ['required','string'],
            'status'    => ['required','in:open,resolved,closed'],
        ]);

        $qa_thread->update($data);

        return redirect()
            ->route('admin.qa-threads.show', $qa_thread)
            ->with('success','Thread updated');
    }

    public function destroy(QaThread $qa_thread)
    {
        $qa_thread->delete();
        return back()->with('success','Thread deleted');
    }
}
