<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Option, Question};
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * List semua opsi (dengan relasi question).
     */
    public function index()
    {
        $options = Option::with('question')->latest()->paginate(20);
        return view('admin.options.index', compact('options'));
    }

    /**
     * Form tambah opsi baru.
     */
    public function create()
    {
        $questions = Question::all(['id','prompt']);
        return view('admin.options.create', compact('questions'));
    }

    /**
     * Simpan opsi baru.
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'question_id' => 'required|exists:questions,id',
            'text'        => 'required|string|max:500',
            'is_correct'  => 'boolean',
        ]);
        $data['is_correct'] = $r->boolean('is_correct');

        Option::create($data);

        return redirect()->route('admin.options.index')->with('ok','Opsi ditambahkan');
    }

    /**
     * Detail opsi.
     */
    public function show(Option $option)
    {
        return view('admin.options.show', compact('option'));
    }

    /**
     * Form edit opsi.
     */
    public function edit(Option $option)
    {
        $questions = Question::all(['id','prompt']);
        return view('admin.options.edit', compact('option','questions'));
    }

    /**
     * Update opsi.
     */
    public function update(Request $r, Option $option)
    {
        $data = $r->validate([
            'question_id' => 'required|exists:questions,id',
            'text'        => 'required|string|max:500',
            'is_correct'  => 'boolean',
        ]);
        $data['is_correct'] = $r->boolean('is_correct');

        $option->update($data);

        return redirect()->route('admin.options.index')->with('ok','Opsi diupdate');
    }

    /**
     * Hapus opsi.
     */
    public function destroy(Option $option)
    {
        $option->delete();
        return redirect()->route('admin.options.index')->with('ok','Opsi dihapus');
    }
}
