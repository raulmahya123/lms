<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PsyTest, PsyQuestion, PsyOption};
use Illuminate\Http\Request;

class PsyQuestionController extends Controller
{
    // INDEX nested: /admin/psy-tests/{psy_test}/questions
    public function index(PsyTest $psy_test)
    {
        $questions = $psy_test->questions()->with('options')->orderBy('ordering')->paginate(50);
        return view('admin.psy_questions.index', compact('psy_test','questions'));
    }

    public function create(PsyTest $psy_test)
    {
        return view('admin.psy_questions.create', compact('psy_test'));
    }

    public function store(Request $r, PsyTest $psy_test)
    {
        $data = $r->validate([
            'prompt'   => ['required','string'],
            'trait_key'=> ['nullable','string','max:50'],
            'qtype'    => ['required','in:likert,mcq'],
            'ordering' => ['nullable','integer','min:0','max:65535'],
            'options'  => ['array'],      // untuk mcq / likert seeds
            'options.*.label' => ['required_with:options','string','max:120'],
            'options.*.value' => ['nullable','integer'],
        ]);

        $q = $psy_test->questions()->create([
            'prompt'    => $data['prompt'],
            'trait_key' => $data['trait_key'] ?? null,
            'qtype'     => $data['qtype'],
            'ordering'  => $data['ordering'] ?? 0,
        ]);

        if (!empty($data['options'])) {
            foreach ($data['options'] as $i => $opt) {
                $q->options()->create([
                    'label'    => $opt['label'],
                    'value'    => $opt['value'] ?? null,
                    'ordering' => $i + 1,
                ]);
            }
        }

        return redirect()->route('admin.psy-tests.questions.index', $psy_test)->with('success','Question created');
    }

    // SHALLOW routes: /admin/questions/{question}
    public function show(PsyQuestion $question)
    {
        $question->load('test','options');
        return view('admin.psy_questions.show', compact('question'));
    }

    public function edit(PsyQuestion $question)
    {
        $question->load('test','options');
        return view('admin.psy_questions.edit', compact('question'));
    }

    public function update(Request $r, PsyQuestion $question)
    {
        $data = $r->validate([
            'prompt'   => ['required','string'],
            'trait_key'=> ['nullable','string','max:50'],
            'qtype'    => ['required','in:likert,mcq'],
            'ordering' => ['nullable','integer','min:0','max:65535'],
            'options'  => ['array'],
            'options.*.id'    => ['nullable','integer','exists:psy_options,id'],
            'options.*.label' => ['required_with:options','string','max:120'],
            'options.*.value' => ['nullable','integer'],
            'options_delete'  => ['array'],
            'options_delete.*'=> ['integer','exists:psy_options,id'],
        ]);

        $question->update([
            'prompt'    => $data['prompt'],
            'trait_key' => $data['trait_key'] ?? null,
            'qtype'     => $data['qtype'],
            'ordering'  => $data['ordering'] ?? 0,
        ]);

        // Delete options
        if (!empty($data['options_delete'])) {
            PsyOption::whereIn('id', $data['options_delete'])->where('question_id',$question->id)->delete();
        }
        // Upsert options
        if (!empty($data['options'])) {
            foreach ($data['options'] as $i => $opt) {
                PsyOption::updateOrCreate(
                    ['id'=>$opt['id'] ?? 0, 'question_id'=>$question->id],
                    ['label'=>$opt['label'], 'value'=>$opt['value'] ?? null, 'ordering'=>$i+1]
                );
            }
        }

        return redirect()->route('admin.questions.show', $question)->with('success','Question updated');
    }

    public function destroy(PsyQuestion $question)
    {
        $testId = $question->test_id;
        $question->delete();
        return redirect()->route('admin.psy-tests.questions.index', $testId)->with('success','Question deleted');
    }
}
