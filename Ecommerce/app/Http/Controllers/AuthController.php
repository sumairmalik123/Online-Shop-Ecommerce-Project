<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\Order;
use App\Models\Country;
use App\Models\Wishlist;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\CustomerAddress;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login() {
      return view('Front.account.login');
    }
    public function register() {
        return view('Front.account.register');
    }
    public function processRegister(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed',
        ]);
        if ($validator->passes()){
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = bcrypt($request->password);
            $user->save();
            session()->flash('success' , 'You have been register successfully');
            return response()->json([
                'status' => true,
                'message' => 'You have been register successfully',    
            ]);
        } else {
            return response()->json([
                'status' => false,
                 'error' => $validator->errors()]);
        }
      
}
public function authenticate(Request $request) {
    $validator = Validator::make($request->all(),[
        'email' => 'required|email',
        'password' => 'required|min:5',
    ]);
    if ($validator->passes()){
       if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))){
        if(session()->has('url.intended')) {
            return redirect(session()->get('url.intended'));
           // ->with('success', 'You have been login successfully');
        }
        
           return redirect()->route('user.dashboard');
          } else {
           // session()->flash('error', 'Either email/password is incorrect.');
            return redirect()->route('user.login')
            ->withInput($request->only('email'))
            ->with('error', 'Either email/password is incorrect.');
          }


        } else {
            session()->flash('error', 'Either email/password is incorrect.');
            return redirect()->route('user.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));
        }

}  
public function dashboard() {
    $userId = Auth::user()->id;
    $countries = Country::orderBy('name','ASC')->get();
    $user = User::where('id',Auth::user()->id)->first();
   $address = CustomerAddress::where('user_id',$userId)->first();
    return view('Front.account.profile',[
        'user' => $user,
        'countries' => $countries,
        'address' => $address
    ]);
    }

    public function updateProfile(Request $request) {
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$userId.',id',
            'phone' => 'required'
        ]);
        if ($validator->passes()){
            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();
            session()->flash('success' , 'You have been update successfully');
            return response()->json([
                'status' => true,
                'message' => 'You have been update successfully',    
            ]);
        } else {
            return response()->json([
                'status' => false,
                 'error' => $validator->errors()]);
        }
    }
    
    public function updateAddress(Request $request) {
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(),[
          'first_name' => 'required|min:7',
            'last_name' => 'required',
            'email' => 'required|email',
            'country_id' => 'required',
            'address' => 'required|min:20',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required'
        ]);
        if ($validator->passes()){
            $customerAddress = CustomerAddress::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_id' => $userId,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country_id    ,
                    'address' => $request->address,
                    'apartment' => $request->appartment,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip,
                ]
            );
            session()->flash('success' , 'Address update successfully');
            return response()->json([
                'status' => true,
                'message' => 'Address update successfully',    
            ]);
        } else {
            return response()->json([
                'status' => false,
                 'error' => $validator->errors()]);
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect()->route('user.login')
        ->with('success', 'You have been logout successfully');
        
        }
        public function orders() {
            $data = [];
            $user = Auth::user();
          $orders = Order::where('user_id',$user->id)->orderBy('created_at','DESC')->get();
            $data['orders'] = $orders;
            return view('Front.account.order',$data);
            }
            public function orderDetail($id) {
                $data = [];
                $user = Auth::user();
                $orders = Order::where('user_id',$user->id)->where('id',$id)->first();
                $orderItems = OrderItem::where('order_id',$id)->get();
                $orderItemsCount = OrderItem::where('order_id',$id)->count();

                $order = Order::find($id);
                $data['order'] = $order;
                $data['orderItems'] = $orderItems;
                $data['orderItemsCount'] = $orderItemsCount;
                return view('Front.account.order-details',$data);
                }
                public function wishlist(){
                    $wishlist = Wishlist::where('user_id', Auth::user()->id)->with('product')->get();
                    $data = [];
                    $data['wishlist'] = $wishlist;
                     return view('Front.account.wishlist',$data);
                }
                public function removeProductFromWishlist(Request $request){
                    $wishlist = Wishlist::where('user_id', Auth::user()->id)->where('product_id', $request->id)->first();
                    if ($wishlist == null) {
                        session()->flash('error','Product Already removed');
                        return response()->json([
                            'status' => false,
                            'message' => 'Product Already removed',
                        ]);
                    } else {
                      //  $wishlist->delete();
                      $wishlist = Wishlist::where('user_id', Auth::user()->id)->where('product_id', $request->id)->delete();
                      session()->flash('success','Product removed successfully');
                        return response()->json([
                            'status' => true,
                            'message' => 'Product removed successfully',
                        ]);

                    }
                }
                public function showChangePasswordForm() {
                    return view('Front.change-password');
                }

                public function changePassword(Request $request) {
                    $validator = Validator::make($request->all(), [
                        'old_password' => 'required',
                        'new_password' => 'required|min:6',
                        'confirm_password' => 'required|min:6|same:new_password',
                    ]);
                    if ($validator->passes()) { 
                        $user = User::select('id','password')->where('id',Auth::user()->id)->first();
                        if (!Hash::check($request->old_password, $user->password)) {
                            session()->flash('error','Your old passowrd is incorrect, please try again.');
                            return response()->json([
                                'status' => true,
                                'message' => 'Your old passowrd is incorrect, please try again.',
                            ]);
                        } 
                        User::where('id',$user->id)->update([
                            'password' => Hash::make($request->new_password)
                        ]);
                        session()->flash('success','Password Changed Successfully');
                        return response()->json([
                            'status' => true,
                            'message' => 'Password Changed Successfully',
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'error' => $validator->errors(),
                        ]);
                    }
                }
                public function forgotPasword() {
                    return view('Front.forgot-password');
                }
                public function sendForgotPasswordLink(Request $request) {
                    $validator = Validator::make($request->all(), [
                        'email' => 'required|email|exists:users,email',
                    ]);
                    if ($validator->fails()) {
                        return redirect()->route('front.forgotPasword')->withInput()->withErrors($validator);
                    }
                      $token = Str::random(60);
                      DB::table('password_reset_tokens')->where('email',$request->email)->delete();

                      DB::table('password_reset_tokens')->insert([
                          'email' => $request->email,
                          'token' => $token,
                          'created_at' => now()
                      ]);
                       $user = User::where('email', $request->email)->first();
                       $mailData = [
                           'user' => $user,
                           'token' => $token,
                           'mail_subject' => 'You have requested for forgot password'
                       ];

                        Mail::to($request->email)->send(new ResetPasswordEmail($mailData));
                     //   session()->flash('success','Password Reset Link has been sent to your email.');
                        return redirect()->route('front.forgotPasword')->with('success','Please check your inbox to reset your password.');
                    }

                   // if ($validator->passes()) {
                      //  $user = User::where('email', $request->email)->first();
                      //  if ($user == null) {
                      //      session()->flash('error', 'User not found');
                      //      return response()->json([
                         //       'status' => false,
                           //     'message' => 'User not found',
                         //   ]);
                       // } else {
                       //     $token = Str::random(32);
                        //    User::where('id', $user->id)->update([
                       //         'forgot_password_token' => $token
                        //    ]);
                        //    $mailData = [
                          //      'name' => $user->name,
                         //       'email' => $user->email,
                           //     'token' => $token,
                           //     'mail_subject' => 'You have requested for forgot password'
                          //  ];
                           // Mail::to($user->email)->send(new ForgotPassword($mailData));
                           // session()->flash('success', 'Password reset link has been sent to your email');
                          //  return response()->json([
                            //    'status' => true,
                           //     'message' => 'Password reset link has been sent to your email',
                           // ]);
                       // }
                  //  } else {
                        //return response()->json([
                       //     'status' => false,
                      //      'error' => $validator->errors(),
                      //  ]);
                   // }

                   public function resetpassword($token) {
                       $tokenExist = DB::table('password_reset_tokens')->where('token', $token)->first();
                       if ($tokenExist == null) {
                           return redirect()->route('front.forgotPasword')->with('error','Invalid request');
                       }
                       return view('Front.reset-password',[
                           'token' => $token
                       ]);
                   }

                   public function processResetPassword(Request $request) {
                    $token = $request->token;
                    $data = DB::table('password_reset_tokens')->where('token', $token)->first();
                       if ($data == null) {
                           return redirect()->route('front.forgotPasword')->with('error','Invalid request');
                       }
                       $user = User::where('email',$data->email)->first();
                       if ($user == null) {
                           return redirect()->route('front.forgotPasword')->with('error','Invalid request');
                       }
                       $validator = Validator::make($request->all(), [
                        'new_password' => 'required|min:6',
                        'confirm_password' => 'required|min:6|same:new_password',
                    ]);
                    if ($validator->fails()) {
                        return redirect()->route('front.resetpassword', $token)->withErrors($validator);   
                    }

                    User::where('id', $user->id)->update([
                        'password' => Hash::make($request->new_password)
                    ]);
                    DB::table('password_reset_tokens')->where('email', $user->email)->delete();
                    return redirect()->route('front.forgotPasword')->with('success','Password Changed Successfully');
                }

}