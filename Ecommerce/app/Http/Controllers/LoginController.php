<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    //customer page login open
    public function index(){
        return view('login');
    }

    //authenticate user
    public function authenticate(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        if($validator->passes()){
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])){
                $admin = Auth::guard('admin')->user();
                if ($admin->role === 'admin') {
                    return redirect()->route('account.dashboard');
                } else {
                    Auth::guard('admin')->logout();
                    return redirect()->route('account.login')->with('error', 'Only admin can login.');
                }
            }else{
                return redirect()->route('account.login')->with('error', 'Either mail and password is incorrect.');
            }
        } else {
            return redirect()->route('account.login')->withErrors($validator);
        }
    }

    //Show registration form
    public function register()
    {
        return view('register');
    }
    public function processregister(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);
        if($validator->passes()){
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = 'customer';
            $user->save();
            return redirect()->route('account.login')->with('success', 'You have register suucessfully');
        } else{
            return redirect()->route('account.register')->withErrors($validator);
        }
        
    }

    //logout
    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('account.login');
    }
}
