<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PsyProfile;
use App\Models\PsyTest;
use Illuminate\Http\Request;

class PsyProfileController extends Controller
{
    /**
     * Daftar semua profile berdasarkan test_id (opsional).
     */
    public function index(Request $request)
    {
        $testId = $request->get('test_id');
        $query  = PsyProfile::with('test');

        if ($testId) {
            $query->where('test_id', $testId);
        }
        $profiles = $query->orderBy('id','desc')->paginate(20);
        $tests = PsyTest::pluck('name','id');
        return view('admin.psy-profiles.index', compact('profiles','tests','testId'));
    }

    /**
     * Form tambah.
     */
    public function create()
    {
        $tests = PsyTest::pluck('name','id');
        return view('admin.psy-profiles.create', compact('tests'));
    }

    /**
     * Simpan ke DB.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'test_id'     => 'required|exists:psy_tests,id',
            'key'         => 'required|string|max:50',
            'name'        => 'required|string|max:100',
            'min_total'   => 'required|integer',
            'max_total'   => 'required|integer',
            'description' => 'nullable|string',
        ]);

        PsyProfile::create($data);

        return redirect()->route('admin.psy-profiles.index')
            ->with('success','Profil berhasil ditambahkan');
    }

    /**
     * Form edit.
     */
    public function edit(PsyProfile $psyProfile)
    {
        $tests = PsyTest::pluck('name','id');
        return view('admin.psy-profiles.edit', compact('psyProfile','tests'));
    }

    /**
     * Update data.
     */
    public function update(Request $request, PsyProfile $psyProfile)
    {
        $data = $request->validate([
            'test_id'     => 'required|exists:psy_tests,id',
            'key'         => 'required|string|max:50',
            'name'        => 'required|string|max:100',
            'min_total'   => 'required|integer',
            'max_total'   => 'required|integer',
            'description' => 'nullable|string',
        ]);

        $psyProfile->update($data);

        return redirect()->route('admin.psy-profiles.index')
            ->with('success','Profil berhasil diupdate');
    }

    /**
     * Hapus data.
     */
    public function destroy(PsyProfile $psyProfile)
    {
        $psyProfile->delete();
        return redirect()->route('admin.psy-profiles.index')
            ->with('success','Profil berhasil dihapus');
    }
}
