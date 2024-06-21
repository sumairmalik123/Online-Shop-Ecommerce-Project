<?php

namespace App\Http\Controllers\admin;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ShippingCharge;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create() {
        $countries = Country::get();
        $data['countries'] = $countries;
        $shippingCharge = ShippingCharge::select('shipping_charges.*','countries.name')->leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        $data['shippingCharge'] = $shippingCharge;
        return view('shipping.create', $data);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric',
        ]);

        if($validator->passes()) {
            $count = ShippingCharge::where('country_id',$request->country)->count();
            if($count > 0) {
                session()->flash('error','Country already exists');
                return response()->json([
                    'status' => true,
                    'errors' => 'Country already exists'
                ]);
            }
            $shipping = new ShippingCharge();
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success','Shipping added successfully');

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

    public function edit($id) {
        $countries = Country::get();
        $data['countries'] = $countries;
        $shippingCharge = ShippingCharge::find($id);
        $data['shippingCharge'] = $shippingCharge;
        return view('shipping.edit', $data);
    }

    public function update(Request $request, $id) {
        $shipping = ShippingCharge::find($id);
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric',
        ]);

        if($validator->passes()) {
            if($shipping == null) {
                session()->flash('error','Shipping not found');
                return response()->json([
                    'status' => true,
                    'errors' => 'Shipping not found'
                ]);
            }
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success','Shipping updated successfully');

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

    public function destroy($id) {
        $shipping = ShippingCharge::find($id);
        if($shipping == null) {
            session()->flash('error','Shipping not found');
            return response()->json([
                'status' => true,
                'errors' => 'Shipping not found'
            ]);
        }
        $shipping->delete();
        session()->flash('success','Shipping deleted successfully');
        return response()->json([
            'status' => true,
        ]);   
     }

}
