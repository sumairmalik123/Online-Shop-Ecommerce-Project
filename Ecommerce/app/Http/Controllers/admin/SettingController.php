<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function showChangeAdminPasswordForm() {
        return view('change-password');

    }
    public function processChangePassword(Request $request) {
        $validator = Validator::make($request->all(),[
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:new_password',
        ]);
        $admin = User::where('id',Auth::guard('admin')->user()->id)->first();
        if($validator->passes()) {
            if(!Hash::check($request->old_password, $admin->password)) {
                session()->flash('error','Old password is not correct, please try again');
                return response()->json([
                    'status' => true,
                    'message' => 'Old password is not correct, please try again'
                ]);
            } 
            User::where('id',Auth::guard('admin')->user()->id)->update([
                'password' => Hash::make($request->new_password)
            ]);
            session()->flash('success','Password Changed Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Password Changed Successfully'
            ]);
        } else {
              return response()->json([
                   'status' => false,
                    'error' => $validator->errors()
                ]);
            }
        }

    }   