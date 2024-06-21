<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    return view('Front.account.profile');
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
                $order = Order::find($id);
                $data['order'] = $order;
                $data['orderItems'] = $orderItems;
                return view('Front.account.order-details',$data);
                }
}