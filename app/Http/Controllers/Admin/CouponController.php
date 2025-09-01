<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Coupon, CouponRedemption};
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $r)
    {
        $coupons = Coupon::withCount('redemptions')
            ->when($r->filled('q'), fn($q)=>$q->where('code','like','%'.$r->q.'%'))
            ->latest('id')->paginate(20);

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
            'discount_percent'=> 'required|integer|min:0|max:100',
            'valid_from'      => 'nullable|date',
            'valid_until'     => 'nullable|date|after_or_equal:valid_from',
            'usage_limit'     => 'nullable|integer|min:1',
        ]);

        $coupon = Coupon::create($data);
        return redirect()->route('admin.coupons.edit',$coupon)->with('ok','Coupon dibuat');
    }

    public function edit(Coupon $coupon)
    {
        $coupon->loadCount('redemptions');
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $r, Coupon $coupon)
    {
        $data = $r->validate([
            'discount_percent'=> 'required|integer|min:0|max:100',
            'valid_from'      => 'nullable|date',
            'valid_until'     => 'nullable|date|after_or_equal:valid_from',
            'usage_limit'     => 'nullable|integer|min:1',
        ]);

        $coupon->update($data);
        return back()->with('ok','Coupon diupdate');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('ok','Coupon dihapus');
    }
}
