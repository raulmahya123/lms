<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{QaReply, QaThread};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QaReplyController extends Controller
{
    public function store(Request $r, QaThread $thread)
    {
        $data = $r->validate([
            'user_id' => ['required','exists:users,id'],
            'body'    => ['required','string'],
        ]);

        $data['thread_id'] = $thread->id;
        QaReply::create($data);

        return redirect()->route('admin.qa-threads.show', $thread)->with('success','Reply posted');
    }

    public function markAnswer(QaReply $reply)
    {
        DB::transaction(function() use ($reply) {
            // clear answer di thread yang sama
            QaReply::where('thread_id',$reply->thread_id)->where('is_answer',1)->update(['is_answer'=>0]);
            // set jawaban ini sebagai answer
            $reply->update(['is_answer'=>1]);
            // opsional: ubah status thread ke resolved
            $reply->thread()->update(['status'=>'resolved']);
        });

        return redirect()->route('admin.qa-threads.show', $reply->thread_id)->with('success','Marked as answer');
    }
}
