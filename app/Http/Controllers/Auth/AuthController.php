<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Listeners\AuthenticateUserListener;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AuthenticateUser;
use Socialite;
use Validator;
use App\User;
use App\Organizations;
use Log;
use URL;
use DB;





class AuthController extends Controller implements AuthenticateUserListener {
    /*
      |--------------------------------------------------------------------------
      | Registration & Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users, as well as the
      | authentication of existing users. By default, this controller uses
      | a simple trait to add these behaviors. Why don't you explore it?
      |
     */

use AuthenticatesAndRegistersUsers,
    ThrottlesLogins;


    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Authenticate facebook login
     *
     *
     */
    public function socialLogin(AuthenticateUser $authenticateUser, Request $request, $loginType) {


        //authenticate user

        $hasCode = false;

        if (strtolower($loginType) == "twitter") {
            $hasCode = $request->has('oauth_token');
        } else {
            $hasCode = $request->has('code');
        }

        return $authenticateUser->execute($hasCode, $loginType, $this, $request);
    }

    public function userLoginAjax(Request $request) {


        $validator = Validator::make($request->all(), [
                    'email' => 'required|email|exists:pwi_users,user_email',
                    'password' => 'required'
        ]);

        if ($validator->fails()) {
            echo json_encode(array("status" => false, "errors" => $validator->errors()));
        } else {
            $user = array(
                "user_email" => \Input::get('email'),
                "password" => md5(\Input::get('password')),
                "user_status" => "active"
            );

            if (\Auth::attempt($user, 1)) {
                return response()->json([
                    'status' => true,
                    'intended' => '/user/dashboard'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "The email/password combination was not valid"
                ]);
            }
        }
    }

    public function userHasLoggedIn($user) {

        $url = session('login-redirect-url');

        return redirect($url);
    }

    

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
                    //'name' => 'required|max:255',
                    'user_email' => 'required|email|max:255|unique:users',
                    'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        return User::create([
                    'name' => $data['name'],
                    'user_email' => $data['email'],
                    'password' => bcrypt($data['password']),
        ]);
    }

}
