<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PsyTest;
use App\Models\PsyQuestion;
use App\Models\PsyOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PsyQuestionController extends Controller
{
    /* =========================
     * GLOBAL LIST / CREATE
     * ========================= */
    public function globalIndex(Request $r)
    {
        $q = PsyQuestion::query()->with(['test:id,name']);

        if ($r->filled('psy_test_id')) {
            $q->where('test_id', $r->string('psy_test_id'));
        }
        if ($r->filled('q')) {
            $q->where('prompt', 'like', '%' . $r->q . '%');
        }

        $questions = $q->orderBy('ordering')->paginate(50)->withQueryString();
        $tests     = PsyTest::select('id', 'name')->orderBy('name')->get();

        $currentTest = $r->filled('psy_test_id')
            ? $tests->firstWhere('id', $r->psy_test_id)
            : null;

        return view('admin.psy_questions.index', compact('questions', 'tests', 'currentTest'));
    }

    public function globalCreate(Request $r)
    {
        $tests       = PsyTest::select('id', 'name')->orderBy('name')->get();
        $selected    = $r->psy_test_id;
        $currentTest = $tests->firstWhere('id', $selected);

        return view('admin.psy_questions.create', compact('tests', 'selected', 'currentTest'));
    }

    public function globalStore(Request $r)
    {
        $data = $r->validate([
            'psy_test_id'        => ['required', Rule::exists('psy_tests', 'id')],
            'prompt'             => ['required', 'string'],
            'trait_key'          => ['nullable', 'string', 'max:50'],
            'qtype'              => ['required', Rule::in(['likert', 'mcq'])],
            'ordering'           => ['nullable', 'integer'],
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

    /* =========================
     * NESTED (per {psy_test})
     * ========================= */
    public function index(PsyTest $psy_test)
    {
        $questions = PsyQuestion::where('test_id', $psy_test->id)
            ->with('options')
            ->orderBy('ordering')
            ->paginate(50);

        return view('admin.psy_questions.index', [
            'questions'   => $questions,
            'tests'       => PsyTest::select('id', 'name')->orderBy('name')->get(),
            'currentTest' => $psy_test,
        ]);
    }

    public function create(PsyTest $psy_test)
    {
        $tests       = PsyTest::select('id', 'name')->orderBy('name')->get();
        $selected    = $psy_test->id;

        return view('admin.psy_questions.create', compact('tests', 'selected', 'psy_test'));
    }

    public function store(Request $r, PsyTest $psy_test)
    {
        $data = $r->validate([
            'prompt'   => ['required', 'string'],
            'trait_key' => ['nullable', 'string', 'max:50'],
            'qtype'    => ['required', Rule::in(['likert', 'mcq'])],
            'ordering' => ['nullable', 'integer'],
            'options'  => ['array'],
            'options.*.label' => ['required_with:options', 'string'],
        ]);

        $q = PsyQuestion::create([
            'test_id'   => $psy_test->id,
            'prompt'    => $data['prompt'],
            'trait_key' => $data['trait_key'] ?? null,
            'qtype'     => $data['qtype'],
            'ordering'  => $data['ordering'] ?? 0,
        ]);

        if (!empty($data['options'])) {
            foreach ($data['options'] as $i => $opt) {
                $q->options()->create([
                    'label' => $opt['label'],
                    'value' => $opt['value'] ?? null,
                    'ordering' => $i + 1,
                ]);
            }
        }

        return redirect()->route('admin.psy-tests.questions.index', $psy_test);
    }

    public function show(PsyTest $psy_test, PsyQuestion $psy_question)
    {
        abort_if($psy_question->test_id !== $psy_test->id, 404);

        $psy_question->load('test:id,name', 'options');

        return view('admin.psy_questions.show', [
            'question'    => $psy_question,
            'currentTest' => $psy_test,
        ]);
    }


    public function showFlat(PsyQuestion $psy_question)
    {
        $psy_question->load('test:id,name', 'options');

        return view('admin.psy_questions.show', [
            'question'    => $psy_question,
            'currentTest' => $psy_question->test,
        ]);
    }

    public function destroy(PsyTest $psy_test, PsyQuestion $psy_question)
    {
        abort_if($psy_question->test_id !== $psy_test->id, 404);

        $psy_question->delete();

        return redirect()->route('admin.psy-tests.questions.index', $psy_test);
    }

    /* =========================
     * FLAT (global akses)
     * ========================= */
    public function edit(PsyQuestion $psy_question)
    {
        $psy_question->load('test:id,name', 'options');

        return view('admin.psy_questions.edit', [
            'question' => $psy_question,
        ]);
    }

    public function update(Request $r, PsyQuestion $psy_question)
    {
        $data = $r->validate([
            'prompt'   => ['required', 'string'],
            'trait_key' => ['nullable', 'string', 'max:50'],
            'qtype'    => ['required', Rule::in(['likert', 'mcq'])],
            'ordering' => ['nullable', 'integer'],
            'options'  => ['array'],
            'options.*.id'    => ['nullable', 'uuid', Rule::exists('psy_options', 'id')],
            'options.*.label' => ['required_with:options', 'string'],
            'options.*.value' => ['nullable', 'integer'],
            'options_delete'  => ['array'],
            'options_delete.*' => ['uuid', Rule::exists('psy_options', 'id')],
        ]);

        $psy_question->update([
            'prompt' => $data['prompt'],
            'trait_key' => $data['trait_key'] ?? null,
            'qtype' => $data['qtype'],
            'ordering' => $data['ordering'] ?? 0,
        ]);

        if (!empty($data['options_delete'])) {
            PsyOption::whereIn('id', $data['options_delete'])
                ->where('question_id', $psy_question->id)
                ->delete();
        }

        if (!empty($data['options'])) {
            $i = 1;
            foreach ($data['options'] as $opt) {
                if (!empty($opt['id'])) {
                    PsyOption::where('id', $opt['id'])
                        ->where('question_id', $psy_question->id)
                        ->update([
                            'label' => $opt['label'],
                            'value' => $opt['value'] ?? null,
                            'ordering' => $i,
                        ]);
                } else {
                    $psy_question->options()->create([
                        'label' => $opt['label'],
                        'value' => $opt['value'] ?? null,
                        'ordering' => $i,
                    ]);
                }
                $i++;
            }
        }

        return redirect()->route(
            'admin.psy-tests.questions.show',
            ['psy_test' => $psy_question->test_id, 'psy_question' => $psy_question->id]
        );
    }

    public function destroyFlat(PsyQuestion $psy_question)
    {
        $testId = $psy_question->test_id;
        $psy_question->delete();

        return redirect()->route('admin.psy-questions.index', ['psy_test_id' => $testId]);
    }
}
