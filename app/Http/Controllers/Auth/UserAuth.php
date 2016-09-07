<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Auth;

/**
 * Description of UserAuth
 *
 * @author PWI
 */
use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\AuthenticateUser;
use App\Listeners\AuthenticateUserListener;
use Illuminate\Support\Facades\Session;
use Auth;
use Hash;

class UserAuth extends Controller {

    public function __construct() {
        $this->middleware('guest');
    }

    public function login(Request $request) {
        if ($request->isMethod('post')) {
         
            $rules = [
                'user_email' => 'required',
                'password' => 'required'
            ];
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation)->withInput();
            } else {
                $email = $request->input('user_email');
                $password = $request->input('password');
                if (Auth::attempt(["user_email" => $email, "password" => md5($password), "user_status" => "active"])) {
                    return redirect()->intended('/user/dashboard');
                } elseif (Auth::attempt(["user_email" => $email, "password" => $password, "user_status" => "active"])) {
                    return redirect()->intended('/user/dashboard');
                } else {
                    Session::flash('flash_message', 'We cannot find a user matching these credentials. Please check your email and password.');
                    Session::flash('flash_type', 'alert alert-danger');
                    return redirect()->back();
                }
            }
        } else {
            return view('user/login')->with(["meta" => array('title' => 'Login | Project World Impact', 'description' => '')]);
        }
    }

}
