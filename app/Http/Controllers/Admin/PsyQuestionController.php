<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PsyTest, PsyQuestion, PsyOption};
use Illuminate\Http\Request;

class PsyQuestionController extends Controller
{
    /**
     * ========== GLOBAL (tanpa {psy_test}) ==========
     */
    public function globalIndex(Request $r)
    {
        $q = \App\Models\PsyQuestion::query()->with(['test:id,name']);

        if ($r->filled('psy_test_id')) {
            $q->where('test_id', $r->psy_test_id);
        }
        if ($r->filled('q')) {
            $q->where('prompt', 'like', '%' . $r->q . '%');
        }

        $questions = $q->orderBy('ordering')->paginate(50)->withQueryString();

        $tests = \App\Models\PsyTest::select('id', 'name')->orderBy('name')->get();

        // <-- tambahkan ini
        $currentTest = null;
        if ($r->filled('psy_test_id')) {
            $currentTest = $tests->firstWhere('id', (int)$r->psy_test_id);
        }

        return view('admin.psy_questions.index', compact('questions', 'tests', 'currentTest'));
    }

    public function globalCreate(Request $r)
    {
        $tests = \App\Models\PsyTest::select('id', 'name')->orderBy('name')->get();
        $selected = $r->psy_test_id;

        // opsional: untuk judul di form create
        $currentTest = $tests->firstWhere('id', (int)$selected);

        return view('admin.psy_questions.create', compact('tests', 'selected', 'currentTest'));
    }


    public function globalStore(Request $r)
    {
        $data = $r->validate([
            'psy_test_id'        => ['required', 'exists:psy_tests,id'],
            'prompt'             => ['required', 'string'],
            'trait_key'          => ['nullable', 'string', 'max:50'],
            'qtype'              => ['required', 'in:likert,mcq'],
            'ordering'           => ['nullable', 'integer', 'min:0', 'max:65535'],
            'options'            => ['array'],
            'options.*.label'    => ['required_with:options', 'string', 'max:120'],
            'options.*.value'    => ['nullable', 'integer'],
        ]);

        $q = PsyQuestion::create([
            'test_id'   => $data['psy_test_id'],
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

        return redirect()
            ->route('admin.psy-questions.index', ['psy_test_id' => $q->test_id])
            ->with('success', 'Question created');
    }

    /**
     * ========== SHALLOW ==========
     */
    public function show(PsyQuestion $question)
    {
        $question->load('test:id,name', 'options');
        return view('admin.psy_questions.show', compact('question'));
    }

    public function edit(PsyQuestion $question)
    {
        $question->load('test:id,name', 'options');
        return view('admin.psy_questions.edit', compact('question'));
    }

    public function update(Request $r, PsyQuestion $question)
    {
        $data = $r->validate([
            'prompt'             => ['required', 'string'],
            'trait_key'          => ['nullable', 'string', 'max:50'],
            'qtype'              => ['required', 'in:likert,mcq'],
            'ordering'           => ['nullable', 'integer', 'min:0', 'max:65535'],
            'options'            => ['array'],
            'options.*.id'       => ['nullable', 'integer', 'exists:psy_options,id'],
            'options.*.label'    => ['required_with:options', 'string', 'max:120'],
            'options.*.value'    => ['nullable', 'integer'],
            'options_delete'     => ['array'],
            'options_delete.*'   => ['integer', 'exists:psy_options,id'],
        ]);

        $question->update([
            'prompt'    => $data['prompt'],
            'trait_key' => $data['trait_key'] ?? null,
            'qtype'     => $data['qtype'],
            'ordering'  => $data['ordering'] ?? 0,
        ]);

        // Delete options
        if (!empty($data['options_delete'])) {
            PsyOption::whereIn('id', $data['options_delete'])
                ->where('question_id', $question->id)
                ->delete();
        }

        // Upsert options
        if (!empty($data['options'])) {
            foreach ($data['options'] as $i => $opt) {
                PsyOption::updateOrCreate(
                    ['id' => $opt['id'] ?? 0, 'question_id' => $question->id],
                    ['label' => $opt['label'], 'value' => $opt['value'] ?? null, 'ordering' => $i + 1]
                );
            }
        }

        return redirect()->route('admin.questions.show', $question)
            ->with('success', 'Question updated');
    }

    public function destroy(PsyQuestion $question)
    {
        $testId = $question->test_id;
        $question->delete();

        return redirect()
            ->route('admin.psy-questions.index', ['psy_test_id' => $testId])
            ->with('success', 'Question deleted');
    }
}
