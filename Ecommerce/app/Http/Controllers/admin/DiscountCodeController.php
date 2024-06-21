<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\DiscountCoupon;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DiscountCodeController extends Controller
{
    public function index(Request $request) {
        $discountCoupons = DiscountCoupon::latest();
        if (!empty($request->get('keyword'))) {
          $discountCoupons = $discountCoupons->where('name', 'like', '%' . $request->get('keyword') . '%');
          $discountCoupons = $discountCoupons->orWhere('code', 'like', '%' . $request->get('keyword') . '%');
        }
        $discountCoupons = $discountCoupons->paginate(10);
        return view('coupon.list',compact('discountCoupons'));
    }

    public function create() {
       return view('coupon.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);

        if($validator->passes()) {

            //starting date must be greater then current date
            if (!empty($request->starts_at)) {
                $now = Carbon::now(); // current datetime object bun gia hai
                $starAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                               //lte mean less the equal
                               //startAt less than $now
                if ($starAt->lte($now) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date can not be less than current date time']
                        ]);
                }
            }

            //expiry date must be greater then starting date
            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $expireAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $starAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                               //gt mean greater than
                if ($expireAt->gt($starAt) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date must be greater than start date']
                        ]);
                }
            }

            $discountCode = new DiscountCoupon();
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount    ;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at; //starting date current date sa ziyada hona chaye aisa logic bnaye ga
            $discountCode->expire_at = $request->expires_at;// or expiry date humesha starting date sa ziyada hona chaye or store krna sa phela check kra ga
            $discountCode->save();

            $request->session()->flash('success', 'Discount code created successfully');

            return response()->json([
              'status' => true
              ]);

        } else {
        return response()->json([
          'status' => false,
          'errors' => $validator->errors()
          ]);
        }
    }

    public function edit(Request $request, $id) {
        $coupon = DiscountCoupon::find($id);
        if ($coupon == null) {
                session()->flash('error', 'Coupon not found');
            return redirect()->route('coupon.list')->with('error', 'Coupon not found');
        }
        $data['coupon'] =$coupon;
        return view('coupon.edit',$data);
    }

    public function update(Request $request, $id) {
        $discountCode = DiscountCoupon::find($id);

        if ($discountCode == null) {
            session()->flash('error', 'Coupon not found');
            return response()->json([
              'status' => true,
            ]);
        }

        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);

        if($validator->passes()) {

        

            //expiry date must be greater then starting date
            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $expireAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $starAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                               //gt mean greater than
                if ($expireAt->gt($starAt) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date must be greater than start date']
                        ]);
                }
            }

            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount    ;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expire_at = $request->expires_at;
            $discountCode->save();

            $request->session()->flash('success', 'Discount code updated     successfully');

            return response()->json([
              'status' => true
              ]);

        } else {
        return response()->json([
          'status' => false,
          'errors' => $validator->errors()
          ]);
        }

    }

    public function destroy(Request $request, $id) {
        $discountCode = DiscountCoupon::find($id);

        if ($discountCode == null) {
                session()->flash('error', 'Coupon not found');
            return response()->json([
              'status' => true,
            ]);
        }

        $discountCode->delete();
        session()->flash('success', 'Coupon deleted successfully');
        return response()->json([
          'status' => true,
        ]);
    }
}
