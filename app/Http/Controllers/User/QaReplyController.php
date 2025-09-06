<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{QaReply, QaThread};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QaReplyController extends Controller
{

    /**
     * Tambah reply ke sebuah thread.
     */
    public function store(Request $r, QaThread $thread)
    {
        $data = $r->validate([
            'body' => ['required','string'],
        ]);

        QaReply::create([
            'thread_id' => $thread->id,
            'user_id'   => Auth::id(),
            'body'      => $data['body'],
            'is_answer' => 0,
            'upvotes'   => 0,
        ]);

        return redirect()
            ->route('app.qa-threads.show', $thread)
            ->with('ok', 'Balasan ditambahkan.');
    }

    /**
     * Tandai sebuah reply sebagai jawaban (hanya pemilik thread).
     */
    public function markAnswer(QaReply $reply)
    {
        $thread = $reply->thread;

        // hanya pemilik thread yang boleh menandai jawaban
        abort_unless($thread && $thread->user_id === Auth::id(), 403);

        DB::transaction(function () use ($reply, $thread) {
            QaReply::where('thread_id', $thread->id)->where('is_answer', 1)->update(['is_answer' => 0]);
            $reply->update(['is_answer' => 1]);

            // opsional: auto set status thread menjadi resolved
            $thread->update(['status' => 'resolved']);
        });

        return redirect()
            ->route('app.qa-threads.show', $thread)
            ->with('ok', 'Jawaban ditandai.');
    }

    /**
     * (Opsional) Hapus reply (hanya pemilik reply).
     */
    public function destroy(QaReply $reply)
    {
        abort_unless($reply->user_id === Auth::id(), 403);

        $threadId = $reply->thread_id;
        $reply->delete();

        return redirect()
            ->route('app.qa-threads.show', $threadId)
            ->with('ok', 'Balasan dihapus.');
    }
}
