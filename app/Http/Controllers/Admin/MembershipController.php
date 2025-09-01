<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Membership, Plan, User};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MembershipController extends Controller
{
    public function index(Request $r)
    {
        $items = Membership::with(['user:id,name,email','plan:id,name'])
            ->when($r->filled('status'), fn($q)=>$q->where('status',$r->status))
            ->latest('id')->paginate(20);

        return view('admin.memberships.index', compact('items'));
    }

    public function show(Membership $membership)
    {
        $membership->load(['user:id,name,email','plan:id,name']);
        return view('admin.memberships.show', compact('membership'));
    }

    public function update(Request $r, Membership $membership)
    {
        $data = $r->validate([
            'status'       => ['required', Rule::in(['pending','active','inactive'])],
            'activated_at' => 'nullable|date',
            'expires_at'   => 'nullable|date|after:activated_at',
        ]);

        $membership->update($data);
        return back()->with('ok','Membership diupdate');
    }

    public function destroy(Membership $membership)
    {
        $membership->delete();
        return redirect()->route('admin.memberships.index')->with('ok','Membership dihapus');
    }
}
