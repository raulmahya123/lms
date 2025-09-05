<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('price')->get();
        return view('app.plans.index', compact('plans'));
    }
}
