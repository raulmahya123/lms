<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Membership, Plan};
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    public function index()
    {
        $active = Membership::with('plan')
            ->where('user_id', Auth::id())
            ->where('status','active')
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>',now()); })
            ->get();

        $plans = Plan::orderBy('price')->get();
        return view('app.memberships.index', compact('active','plans'));
    }
}
