<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

/**
 * Description of AjaxController
 *
 * @author PWI
 */
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Helper;
use App\User;
use Illuminate\Support\Facades\Hash;

class AjaxController extends Controller {

    //put your code here
    public function __construct(UserRepository $userObj, Request $request, Helper $helper) {

        $this->user = $request->instance()->query('user');

        $this->helper = $helper;

        $this->request = $request;
    }

    public function registerAction(Request $request) {
      
        if ($request->ajax()) {
            
        } else {
            return view('register');
        }
    }
    
    


}
