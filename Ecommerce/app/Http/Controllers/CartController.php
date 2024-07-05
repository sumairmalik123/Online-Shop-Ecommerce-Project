<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\DiscountCoupon;
use App\Models\ShippingCharge;
use Illuminate\Support\Carbon;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;


class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->productId);
    
        if ($product === null) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ]);
        }
    
        $cartContent = Cart::content();
        $productAlreadyExist = false;
    
        foreach ($cartContent as $cartItem) {
            if ($cartItem->id === $product->id) {
                $productAlreadyExist = true;
                break; // Exit the loop once product is found
            }
        }
    
        if ($productAlreadyExist) {
            $status = false;
            $message = $product->title . "is already in your cart.";
        } else {
            Cart::add($product->id, $product->title, 1, $product->price, [
                'productImages' => (!empty($product->product_images) ? $product->product_images->first() : '')      
            ]);
            $status = true;
            $message = "<strong>$product->title </strong>". " added to cart successfully!";
            session()->flash('success', $message);  

        }
    
        session()->flash('success', $message); // Store message for subsequent view
    
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
    
    public function cart()
    {
        $cartcontent = Cart::content();
       // dd($cartcontent);
        return view('Front.cart', compact('cartcontent'));
    }
    public function updateCart(Request $request)
    {
        $status = false;
        try {
            $rowId = $request->rowId;
            $qty = $request->qty;
    
            // Get the cart item info esma product ki id hai    
            $iteminfo = Cart::get($rowId);
    
            //check qty avalable in stock
            $product = Product::find($iteminfo->id);
            if ($product->track_qty == 'Yes'){
                if ($qty <= $product->qty){
                    Cart::update($rowId, $qty);
                    $message = 'Cart updated successfully';
                        session()->flash('success', $message);
                    $status = true;
                } else {
                    $message = 'Requested qty ('.$qty.') not available in stock.';
                    $status = false;
                    session()->flash('error', $message);
                }
            } else {
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
            }
            
            session()->flash('success', $message);
            return response()->json([
                'status' => $status,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            $message = 'Error updating cart: ' . $e->getMessage();
            session()->flash('error', $message);
            return response()->json([
                'status' => $status,
                'message' => $message
            ]);
        }
    }
    public function removeCart(Request $request)
    {
        $iteminfo = Cart::get($request->rowId);
    
        if ($iteminfo === null) {
            $errormessage = 'Record not found';
            session()->flash('error', $errormessage); // Use session flash for error messages (if necessary)
            return response()->json([
                'status' => false,
                'message' => 'Record not found',
            ]);
        }
    
        $message = 'Item removed from cart successfully';
        $rowId = $request->rowId;
        Cart::remove($rowId);
    
        session()->flash('success', $message); // Use session flash for success messages (if necessary)
    
        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }
    

    public function checkout() {
        $discount = 0;
//if cart is empty then redirect cart page
        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }
//if user not logged in then redirect to loogin page
       // if (!auth()->check()) {
        if (Auth::check() == false) {
                    if(!session()->has('url.intended')) {
                        session(['url.intended' => url()->current()]);
                    }
            return redirect()->route('user.login');
        }
        
        $customerAddress = CustomerAddress::where('user_id',Auth::user()->id)->first();
        
        session()->forget('url.intended');
        $countries = Country::orderBy('name','ASC')->get();
        $subTotal = Cart::subtotal(2,'.','');   
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
               $discount = ($code->discount_amount/100)*$subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        if ($customerAddress != '') {
             // Retrieve user's country ID
         $usercountry = $customerAddress->country_id;

         // Fetch shipping information for the user's country
         $shippinginfo = ShippingCharge::where('country_id', $usercountry)->first();
 
         // Calculate total quantity from cart items
         //$totalQty = Cart::content()->sum('qty'); // Using Laravel's collection helper
          $totalQty = 0;
          $totalShippingCharge = 0;
          $grandTotal = 0;
         foreach (Cart::content() as $item){
              $totalQty += $item->qty;
            }
           // Initialize total shipping charge (default to 0)
           $totalShippingCharge = 0;
 
         // Calculate total shipping charge only if shipping information is found
         if ($shippinginfo) {
             $totalShippingCharge = $totalQty * $shippinginfo->amount;
          } 
          //grandTotal calculation
            $grandTotal = ($subTotal-$discount) + $totalShippingCharge;
 

        } else {
            $grandTotal = ($subTotal-$discount) ;
            $totalShippingCharge = 0;

        }

        return view('Front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'grandTotal' => $grandTotal,
            'discount' => $discount,        
        ]);
    }

    public function processCheckout(Request $request) {
        //step.1 aplly validator
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:7',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:20',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Pleasefix the errors',
                'errors' => $validator->errors()
            ]);
    }

//step.2 save user address
$user = Auth::user();
$customerAddress = CustomerAddress::updateOrCreate(
    ['user_id' => $user->id],
    [
        'user_id' => $user->id,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'mobile' => $request->mobile,
        'country_id' => $request->country,
        'address' => $request->address,
        'apartment' => $request->appartment,
        'city' => $request->city,
        'state' => $request->state,
        'zip' => $request->zip,
    ]
);

//step.3 save data in order table
if($request->payment_method == 'cod') {
    //cod method here
    $discountCodeId = NULL;
    $promoCode = '';
    $shipping = 0;
    $discount = 0;
    $subTotal = Cart::subtotal(2,'.','');
    $grandTotal = $subTotal + $shipping;
//apply discount here

if (session()->has('code')) {
    $code = session()->get('code');
    if ($code->type == 'percent') {
       $discount = ($code->discount_amount/100)*$subTotal;
    } else {
        $discount = $code->discount_amount;
    }
    $discountCodeId = $code->id;
    $promoCode = $code->code;
}
    //calculate shipping
    $shippingInfo = ShippingCharge::where('country_id', $request->country)->first();
    $totalQty = 0;
    foreach (Cart::content() as $item){
        $totalQty += $item->qty;
    }

    if ($shippingInfo != null) {
        $shipping = $shippingInfo->amount;
        $grandTotal = ($subTotal-$discount) + $shipping;

        
    } else {
        $shippingInfo = ShippingCharge::where('country_id', 'rest_of_world')->first();
        if ($shippingInfo != null) {
            $shipping = $shippingInfo->amount;
            $grandTotal = ($subTotal-$discount) + $shipping;  
        } 
    }
   
    $order = new Order;
    $order->user_id = $user->id;
    $order->subtotal = $subTotal;
    $order->shipping = $shipping;
    $order->discount = $discount;
    $order->coupon_code_id = $discountCodeId;
    $order->coupon_code = $promoCode;
    $order->payment_status = 'not_paid';
    $order->status = 'pending';
    $order->grand_total = $grandTotal;
    $order->first_name = $customerAddress->first_name;
    $order->last_name = $customerAddress->last_name;
    $order->email = $customerAddress->email;
    $order->mobile = $customerAddress->mobile;
    $order->country_id = $customerAddress->country_id;
    $order->address = $customerAddress->address;
    $order->apartment = $customerAddress->apartment;
    $order->city = $customerAddress->city;
    $order->state = $customerAddress->state;
    $order->zip = $customerAddress->zip;
    $order->notes = $request->order_notes;
    $order->save();
//step.4 save data in order details table
     foreach(Cart::content() as $item){
        $orderItem = new OrderItem;
        $orderItem->product_id = $item->id;
        $orderItem->order_id = $order->id;
        $orderItem->name = $item->name;
        $orderItem->quantity = $item->qty;
        $orderItem->price = $item->price;
        $orderItem->total = $item->price*$item->qty;
        $orderItem->save();
        //update product stock
        $productData = Product::find($item->id);
        if ($productData->track_qty == 'Yes'){
            $currentQty = $productData->qty;
            $updatedQty = $currentQty-$item->qty;
            $productData->qty = $updatedQty;
            $productData->save();
        }
       
     }

     //Send Order email
       orderEmail($order->id,'customer');

        session()->flash('success','You have successfully placed your order.');
          //finally, clear the cart
     Cart::destroy();
     session()->forget('code');
         return response()->json([
        'status' => true,
        'message' => 'Order save Successfully',
        'orderId' => $order->id    
        ]);


} else {
      //stripe method here
    }
}
    public function thankyou($id)
    {
    return view('Front.thanks',[
        'id' => $id
    ]);
    }

    public function getOrdersummary(Request $request) {
        $discount = 0;
        $discountString =0;
        $subTotal = Cart::subtotal(2,'.','');
        //Apply discount here 
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
               $discount = ($code->discount_amount/100)*$subTotal;
            } else {
                $discount = $code->discount_amount;
            }
       $discountString = '<div class="mt-4" id="discount-row">
                   <strong>'.Session()->get('code')->code.'</strong>
                  <a class="btn btn-sm btn-danger" id="remove-discount">
                    <i class="fa fa-times"></i>
                  </a>
                </div>';
        }
        if ($request->country_id > 0) { 
            $subTotal = Cart::subtotal(2,'.','');
            $shippingInfo = ShippingCharge::where('country_id', $request->country_id)->first();
            $totalQty = 0;
            foreach (Cart::content() as $item){
                $totalQty += $item->qty;
            }
    
            if ($shippingInfo != null) {
                $shippingCharge = $shippingInfo->amount;
                $grandTotal = ($subTotal-$discount) + $shippingCharge;
    
                return response()->json([
                    'status' => true,
                    'shippingCharge' => number_format($shippingCharge,2),
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => $discount,
                    'discountString' => $discountString,
                ]);
            } else {
                $shippingInfo = ShippingCharge::where('country_id', 'rest_of_world')->first();
                if ($shippingInfo != null) {
                    $shippingCharge = $shippingInfo->amount;
                    $grandTotal = ($subTotal-$discount) + $shippingCharge;
    
                    return response()->json([
                        'status' => true,
                        'shippingCharge' => number_format($shippingCharge,2),
                        'grandTotal' => number_format($grandTotal,2),
                        'discount' => $discount,
                        'discountString' => $discountString,    
                    ]);
                } else {
                    $grandTotal = $subTotal;
                    return response()->json([
                        'status' => true,
                        'grandTotal' => number_format($grandTotal,2),
                        'shippingCharge' => number_format(0,2),
                        'discount' => $discount,
                    ]);
                }
            }
        } else {
            $subTotal = Cart::subtotal(2,'.','');
            $grandTotal = $subTotal;
            return response()->json([
                'status' => true,
                'grandTotal' => number_format($grandTotal-$discount,2),
                'shippingCharge' => number_format(0,2),
                'discount' => $discount,
                'discountString' => $discountString,
            ]);
        }
    }

    public function applyDiscount(Request $request) {
        $code = DiscountCoupon::where('code',$request->discount_code)->first();
        if ($code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid coupon code'
            ]);
        }
     //check if coupon start date is valid or not
        $now = Carbon::now();
        //echo $now->format('Y-m-d H:i:s');
        if ($code->starts_at != "") {
         $startDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->starts_at); //$startDate
           if ($now->lt($startDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Coupon not valid yet'
                ]);
            }
        }

        //check if coupon end date is valid or not
        if ($code->expire_at != "") {
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->expire_at);
            if ($now->gt($endDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Coupon not valid yet'
                ]);
            }
        }
          //max Uses
        // ya method huma ya return kra ga ka coupon code ko kitni baar use kia gia hai
        if ($code->max_uses > 0) {
            $couponUsed = Order::where('coupon_code_id',$code->id)->count();
            if ($couponUsed >= $code->max_uses) {
                return response()->json([
                    'status' => false,
                    'message' => 'Coupon not valid yet'
                ]);
            }
        }
    //max useses user check
    // coupon code ko ek singke user kitni baar use kr skta hai
    if ($code->max_uses_user > 0) {
        $couponUsedByUser = Order::where(['coupon_code_id'=> $code->id, 'user_id' => Auth::user()->id])->count();
        if ($couponUsedByUser >= $code->max_uses_user) {
            return response()->json([
                'status' => false,
                'message' => 'This Coupon code has already been used'
            ]);
        }
    }
    //minimum ammount check
      $subTotal = Cart::subtotal(2,'','');
    if ($code->min_amount > 0) {
        if ($subTotal < $code->min_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Your min amount must be'.$code->min_amount.'.',
            ]);
        }
    }

                session()->put('code',$code);
            return $this->getOrdersummary($request);
           }

     public function removeCoupon(Request $request) {
                session()->forget('code');
                return $this->getOrdersummary($request);

           }
    }

