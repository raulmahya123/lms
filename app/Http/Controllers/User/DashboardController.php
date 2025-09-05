<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Enrollment, Membership, QuizAttempt};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = [
            'courses_count' => Enrollment::where('user_id',$user->id)->count(),
            'active_membership' => Membership::where('user_id',$user->id)
                ->where('status','active')
                ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>',now()); })
                ->first(),
            'last_attempt' => QuizAttempt::where('user_id',$user->id)->latest('id')->first(),
        ];
        return view('app.dashboard', compact('user','stats'));
    }
}
