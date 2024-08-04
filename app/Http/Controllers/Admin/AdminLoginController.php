<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('Admin.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'password'=> 'required'
        ]);

        if($validator->passes())
        {
            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password],$request->get('remember')))
            {
                $admin = Auth::guard('admin')->user();
                if($admin->role == 2)
                {
                    return redirect()->route('admin.dashboard');
                }
                else
                {
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'Unauthorized Accesse');
                }
            }
            else
            {
                return redirect()->route('admin.login')->with('error','Invalid Credentials');
            }
        }
        else
        {
            $inputs = $request->only('email');
            return redirect()->route('admin.login')->withErrors($validator)->withInput($inputs);
        }
    }
}
