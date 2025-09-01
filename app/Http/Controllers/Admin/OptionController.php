<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Option, Question};
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate([
            'question_id' => 'required|exists:questions,id',
            'text'        => 'required|string|max:500',
            'is_correct'  => 'boolean',
        ]);
        $data['is_correct'] = $r->boolean('is_correct');

        Option::create($data);
        return back()->with('ok','Opsi ditambahkan');
    }

    public function update(Request $r, Option $option)
    {
        $data = $r->validate([
            'text'       => 'required|string|max:500',
            'is_correct' => 'boolean',
        ]);
        $data['is_correct'] = $r->boolean('is_correct');

        $option->update($data);
        return back()->with('ok','Opsi diupdate');
    }

    public function destroy(Option $option)
    {
        $option->delete();
        return back()->with('ok','Opsi dihapus');
    }
}
