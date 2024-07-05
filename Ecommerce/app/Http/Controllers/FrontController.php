<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use App\Mail\ContactEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function index()
    {
       $products = Product::where('is_featured', 'Yes')
       ->orderBy('id', 'DESC')
       ->where('status',1)
       ->take(8)
       ->get();
       $data['featuredproducts'] = $products;

       $latestproducts = Product::orderBy('id', 'DESC')
       ->where('status',1)
       ->take(8)
       ->get();
        $data['latestproduct'] = $latestproducts;
        return view('Front.home', $data);
    }
    public function addToWishlist(Request $request){
        if (Auth::check() == false) {

            session(['url.intended' => url()->previous()]);

            return response()->json([
                'status' => false
            ]);
        }
        $product = Product::find($request->productId);
        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => '<div class="alert alert-danger">product not found</div>'
            ]);
        }
        
        Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->productId
            ],
            [
                
            ]
            );

    
        return response()->json([
            'status' => true,
            'message' => '<div class="alert alert-success"><strong>"'.$product->title.'"</strong>added in your wishlist</div>'
        ]);
    }
    public function showfrontpage($slug){
        $page = Page::where('slug',$slug)->first();
        if ($page == null) {
            abort(404);
        }
        return view('Front.page',[
            'page' => $page
        ]);

    }
    public function sendContactEmail(Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required|min:10',
        ]);
        if($validator->passes()) {
            $mailData = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'mail_subject' => 'You have received a contact email'
            ];
            $admin = User::where('id',1)->first();
            Mail::to($admin->email)->send(new ContactEmail($mailData));
            session()->flash('success','Thanks for contacting us, we will get back to you soon.');
            return response()->json([
                'status' => true,
            ]);

    } else {
        return response()->json([
            'status' => false,
            'error' => $validator->errors()
        ]);
    }
}
}