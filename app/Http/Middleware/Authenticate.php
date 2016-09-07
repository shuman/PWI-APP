<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository as UserRepository;
use View;
use Illuminate\Support\Facades\Session;

class Authenticate {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        $userObj = new UserRepository( );
        $user = null;
        $userImage = "";
        $userData = null;
        $user_all_data = array();

        if (Auth::guard($guard)->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/user/login');
            }
        } else {
            if (Auth::check()) {
                $user = \Auth::user();
                $userImage = $userObj->getUserIcon($user->user_id, $user->user_photo_id, $request);
                $addresses = $user->getAddresses;
                foreach ($addresses as $address) {
                    $key = $address->id . "|" . $address->type . "|" . $address->addrLine1 . "|" . $address->addrLine2 . "|" . $address->city . "|" . $address->stateId . "|" . $address->zip . "|" . $address->countryId;
                    $value = $address->addrLine1 . " " . $address->city . ", " . $address->state . " " . $address->zip;
                    $userData[$key] = array(
                        "address" => $value,
                        "type" => $address->type
                    );
                }
                $request->merge(array("user" => $user));
                View::share('user', $user);
                View::share('userImg', $userImage);
                View::share('userData', $userData);

                $user_all_data = array('user' => $user, 'userImg' => $userImage, 'userData' => $userData);
                Session::put('user_all_data', $user_all_data);
                return $next($request);
            }
        }
    }

}
