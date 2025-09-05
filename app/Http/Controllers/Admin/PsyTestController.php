<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PsyTest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PsyTestController extends Controller
{
    public function index(Request $r)
    {
        $tests = PsyTest::query()
            ->when($r->filled('q'), fn($q)=>$q->where('name','like','%'.$r->q.'%')->orWhere('slug','like','%'.$r->q.'%'))
            ->when($r->filled('track'), fn($q)=>$q->where('track',$r->track))
            ->when($r->filled('type'), fn($q)=>$q->where('type',$r->type))
            ->latest('id')->paginate(20)->withQueryString();

        return view('admin.psy_tests.index', compact('tests'));
    }

    public function create()
    {
        return view('admin.psy_tests.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'           => ['required','string','max:160'],
            'slug'           => ['nullable','alpha_dash','unique:psy_tests,slug'],
            'track'          => ['required','in:backend,frontend,fullstack,qa,devops,pm,custom'],
            'type'           => ['required','in:likert,mcq,iq,disc,big5,custom'],
            'time_limit_min' => ['nullable','integer','min:1','max:600'],
            'is_active'      => ['nullable','boolean'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        $test = PsyTest::create($data);
        return redirect()->route('admin.psy-tests.show', $test)->with('success','Test created');
    }

    public function show(PsyTest $psy_test)
    {
        $psy_test->loadCount(['questions','profiles']);
        return view('admin.psy_tests.show', ['test'=>$psy_test]);
    }

    public function edit(PsyTest $psy_test)
    {
        return view('admin.psy_tests.edit', ['test'=>$psy_test]);
    }

    public function update(Request $r, PsyTest $psy_test)
    {
        $data = $r->validate([
            'name'           => ['required','string','max:160'],
            'slug'           => ['required','alpha_dash','unique:psy_tests,slug,'.$psy_test->id],
            'track'          => ['required','in:backend,frontend,fullstack,qa,devops,pm,custom'],
            'type'           => ['required','in:likert,mcq,iq,disc,big5,custom'],
            'time_limit_min' => ['nullable','integer','min:1','max:600'],
            'is_active'      => ['required','boolean'],
        ]);

        $psy_test->update($data);
        return redirect()->route('admin.psy-tests.show', $psy_test)->with('success','Test updated');
    }

    public function destroy(PsyTest $psy_test)
    {
        $psy_test->delete();
        return back()->with('success','Test deleted');
    }
}
