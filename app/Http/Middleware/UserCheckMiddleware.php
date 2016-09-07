<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository as UserRepository;
use Closure;
use Auth;
use View;

class UserCheckMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!$request->ajax()) {
            $userObj = new UserRepository( );
            $user = null;
            $userImage = "";
            $userData = null;
            if (\Auth::check()) {
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
            }

            $request->merge(array("user" => $user));

            View::share('user', $user);
            View::share('userImg', $userImage);
            View::share('userData', $userData);
        }
        return $next($request);
    }

}
