<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

/**
 * Description of RegistrationController
 *
 * @author PWI
 */
use App\Http\Controllers\Controller;
use Validator;
use Session;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use DB;
use App\RefferedUs;
use App\User;

class RegistrationController extends Controller {

    public function __construct() {
        $this->middleware('guest');
    }

    public function register(Request $request) {
        if ($request->isMethod('post')) {
            $rules = [
                'first_name' => 'required|max:255|alpha',
                'last_name' => 'required|max:100|alpha',
                'user_email' => 'required|email|max:255|unique:pwi_users',
                'user_username' => 'required|alpha_num|max:50|unique:pwi_users',
                'password' => 'required|min:6|confirmed',
                'birthdaypicker_birthDay' => 'required',
                'gender' => 'required',
                'project_impact' => 'required'
            ];
            $validation = Validator::make($request->all(), $rules);
            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation)->withInput();
            } else {
//                echo $password=Hash::make($request->input('password')).'<br>';
//                echo strlen($password);
//                exit();
                $saveData = new User();
                $saveData->user_firstname = $request->input('first_name');
                $saveData->user_lastname = $request->input('last_name');
                $saveData->user_email = $request->input('user_email');
                $saveData->user_username = $request->input('user_username');
                $saveData->password = Hash::make($request->input('password'));
//                $saveData->password = md5($request->input('password'));
                $saveData->user_dob = $request->input('birthdaypicker_birthDay');
                $saveData->user_gender = $request->input('gender');
                $saveData->referrer = $request->input('project_impact');
                $saveData->user_type = "user";
                $saveData->user_status = "active";
                if ($saveData->save()) {
                    $email = $request->input('user_email');
                    $password = $request->input('password');
                    if (Auth::attempt(['user_email' => $email, 'password' => $password])) {
                        return redirect()->intended('/user/dashboard');
                    } else {
                        die("Echo Login Failed");
                    }
                } else {
                    die("saved Failed");
                }
            }
        } else {
            $hearaboutas = RefferedUs::where('hearabout_status', 'active')->get();
            return view('user.register')->with([
                        "meta" => array('title' => 'Registration | Project World Impact', 'description' => ''),
                        'data' => $hearaboutas,
            ]);
        }
    }

}
