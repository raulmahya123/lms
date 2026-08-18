<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PsyProfile;
use App\Models\PsyTest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PsyProfileController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'test_id' => ['nullable', 'uuid', 'exists:psy_tests,id'],
            'track' => ['nullable', Rule::in(PsyTest::TRACKS)],
        ]);

        $term = trim((string) ($filters['q'] ?? ''));
        $testId = (string) ($filters['test_id'] ?? '');
        $track = (string) ($filters['track'] ?? '');

        $profiles = PsyProfile::query()
            ->select(['id', 'test_id', 'key', 'name', 'min_total', 'max_total', 'description', 'created_at'])
            ->with(['test:id,name,track'])
            ->when($testId !== '', fn ($q) => $q->where('test_id', $testId))
            ->when($track !== '', fn ($q) => $q->whereHas('test', fn ($test) => $test->where('track', $track)))
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($search) use ($term) {
                    $search->where('key', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $tests = PsyTest::query()->orderBy('name')->get(['id', 'name', 'track']);
        $tracks = PsyTest::TRACKS;

        return view('admin.psy_profiles.index', compact('profiles', 'tests', 'tracks', 'term', 'testId'));
    }

    public function create()
    {
        $tests = PsyTest::query()->orderBy('name')->get(['id', 'name', 'track']);

        return view('admin.psy_profiles.create', compact('tests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'test_id' => ['required', 'uuid', 'exists:psy_tests,id'],
            'key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('psy_profiles')->where(fn ($q) => $q->where('test_id', $request->input('test_id'))),
            ],
            'name' => ['required', 'string', 'max:150'],
            'min_total' => ['required', 'integer', 'min:0'],
            'max_total' => ['required', 'integer', 'gte:min_total'],
            'description' => ['nullable', 'string'],
        ]);

        $data['user_id'] = auth()->id();

        PsyProfile::create($data);

        return redirect()
            ->route('admin.psy-profiles.index', ['test_id' => $data['test_id']])
            ->with('ok', 'Profile berhasil dibuat.');
    }

    public function edit(PsyProfile $psyProfile)
    {
        $tests = PsyTest::query()->orderBy('name')->get(['id', 'name', 'track']);
        $profile = $psyProfile;

        return view('admin.psy_profiles.edit', compact('profile', 'tests'));
    }

    public function update(Request $request, PsyProfile $psyProfile)
    {
        $data = $request->validate([
            'test_id' => ['required', 'uuid', 'exists:psy_tests,id'],
            'key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('psy_profiles')
                    ->ignore($psyProfile->id)
                    ->where(fn ($q) => $q->where('test_id', $request->input('test_id'))),
            ],
            'name' => ['required', 'string', 'max:150'],
            'min_total' => ['required', 'integer', 'min:0'],
            'max_total' => ['required', 'integer', 'gte:min_total'],
            'description' => ['nullable', 'string'],
        ]);

        $psyProfile->update($data);

        return redirect()
            ->route('admin.psy-profiles.index', ['test_id' => $data['test_id']])
            ->with('ok', 'Profile diperbarui.');
    }

    public function destroy(PsyProfile $psyProfile)
    {
        $psyProfile->delete();

        return back()->with('ok', 'Profile dihapus.');
    }
}

