<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $r)
    {
        $q = \App\Models\Coupon::query()
            ->withCount('redemptions')
            ->when(
                $r->filled('q'),
                fn($qq) =>
                $qq->where('code', 'like', '%' . $r->q . '%')
            )
            ->when($r->filled('status'), function ($qq) use ($r) {
                $now = now();
                if ($r->status === 'active') {
                    $qq->where(function ($q) use ($now) {
                        $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
                    })->where(function ($q) use ($now) {
                        $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
                    });
                } elseif ($r->status === 'expired') {
                    $qq->whereNotNull('valid_until')->where('valid_until', '<', $now);
                } elseif ($r->status === 'scheduled') {
                    $qq->whereNotNull('valid_from')->where('valid_from', '>', $now);
                }
            })
            ->latest('id');

        $coupons = $q->paginate(12)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }


    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'code'            => 'required|string|max:50|unique:coupons,code',
            'discount_percent' => 'required|integer|min:0|max:100',
            'valid_from'      => 'nullable|date',
            'valid_until'     => 'nullable|date|after_or_equal:valid_from',
            'usage_limit'     => 'nullable|integer|min:1',
        ]);

        $coupon = Coupon::create($data);
        return redirect()->route('admin.coupons.edit', $coupon)->with('ok', 'Coupon dibuat');
    }

    public function edit(Coupon $coupon)
    {
        $coupon->loadCount('redemptions');
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $r, Coupon $coupon)
    {
        $data = $r->validate([
            'discount_percent' => 'required|integer|min:0|max:100',
            'valid_from'      => 'nullable|date',
            'valid_until'     => 'nullable|date|after_or_equal:valid_from',
            'usage_limit'     => 'nullable|integer|min:1',
        ]);

        $coupon->update($data);
        return back()->with('ok', 'Coupon diupdate');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('ok', 'Coupon dihapus');
    }
}
