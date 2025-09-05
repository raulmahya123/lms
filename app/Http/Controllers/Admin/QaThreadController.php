<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{QaThread, Course, Lesson};
use Illuminate\Http\Request;

class QaThreadController extends Controller
{
    public function index(Request $r)
    {
        $threads = QaThread::query()
            ->with(['user','course','lesson'])
            ->withCount('replies')
            ->when($r->filled('q'), fn($q)=>$q->where('title','like','%'.$r->q.'%'))
            ->when($r->filled('status'), fn($q)=>$q->where('status',$r->status))
            ->latest('id')->paginate(20)->withQueryString();

        return view('admin.qa_threads.index', compact('threads'));
    }

    public function create()
    {
        return view('admin.qa_threads.create', [
            'courses' => Course::select('id','title')->orderBy('title')->get(),
            'lessons' => Lesson::select('id','title')->orderBy('title')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'user_id'   => ['required','exists:users,id'],
            'course_id' => ['nullable','exists:courses,id'],
            'lesson_id' => ['nullable','exists:lessons,id'],
            'title'     => ['required','string','max:255'],
            'body'      => ['required','string'],
            'status'    => ['nullable','in:open,resolved,closed'],
        ]);
        $data['status'] = $data['status'] ?? 'open';

        $thread = QaThread::create($data);
        return redirect()->route('admin.qa-threads.show', $thread)->with('success','Thread created');
    }

    public function show(QaThread $qa_thread)
    {
        $qa_thread->load(['user','course','lesson','replies.user']);
        return view('admin.qa_threads.show', ['thread'=>$qa_thread]);
    }

    public function edit(QaThread $qa_thread)
    {
        return view('admin.qa_threads.edit', [
            'thread'  => $qa_thread,
            'courses' => Course::select('id','title')->orderBy('title')->get(),
            'lessons' => Lesson::select('id','title')->orderBy('title')->get(),
        ]);
    }

    public function update(Request $r, QaThread $qa_thread)
    {
        $data = $r->validate([
            'user_id'   => ['required','exists:users,id'],
            'course_id' => ['nullable','exists:courses,id'],
            'lesson_id' => ['nullable','exists:lessons,id'],
            'title'     => ['required','string','max:255'],
            'body'      => ['required','string'],
            'status'    => ['required','in:open,resolved,closed'],
        ]);

        $qa_thread->update($data);
        return redirect()->route('admin.qa-threads.show', $qa_thread)->with('success','Thread updated');
    }

    public function destroy(QaThread $qa_thread)
    {
        $qa_thread->delete();
        return back()->with('success','Thread deleted');
    }
}
