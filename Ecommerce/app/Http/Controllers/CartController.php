<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->productId);
        if ($product ==  null) {
          return response()->json([
            'status' => false,
            'message'=> 'Record not found',
          ]);
        }

        //cart already exist hai ya nhi jaha humn add kia hai
        if (Cart::count() > 0) {
            //agr zero sa ziayad hai cart means ka cart already add hai
            //cart is not empty
            //cart found in cart
            //if product cart already in the cart
            //return a message product cart already in the cart
            //if not found product crt , then add the cart

            $cartContent = Cart::content();
            $productAlreadyExist = false;
            foreach ($cartContent as $cartItem) {
                //cartitem ma hoga product ka id
                if ($cartItem->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }

            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImages' => (!empty($product->product_images) ? $product->product_images->first() : '')]);
                $status = true;
                $message = $product->title." added to cart successfully";
            } else {
                $status = false;
                $message = $product->title." already added in cart";
            }
        } else {
            // cart is empty
            Cart::add($product->id, $product->title, 1, $product->price, ['productImages' => (!empty($product->product_images) ? $product->product_images->first() : '')]);
            $status = true;
            $message = $product->title." added to cart successfully";
        }

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
    
            // Get the cart item info
            $iteminfo = Cart::get($rowId);
    
            //check qty avalable in stock
            $product = Product::find($iteminfo->id);
            if ($product->track_qty == 'Yes'){
                if ($qty <= $product->qty){
                    Cart::update($rowId, $qty);
                    $message = 'Cart updated successfully';
                    $status = true;
                } else {
                    $message = 'Request qty ('.$qty.') not available in stock.';
                    $status = false;
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
}