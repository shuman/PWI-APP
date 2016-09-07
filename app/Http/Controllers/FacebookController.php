<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Facebook;

use Config;

class FacebookController extends Controller
{
    //
    public function index( ){
        
        $facebook = new Facebook( Config::get("services.facebook"));
        $params = array(
            'redirect_uri' => url('/login/fb/callback'),
            'scope' => 'email',
        );
        
        return Redirect::to($facebook->getLoginUrl($params) );
        
    }
    
    public function callback( ){
        
        $code = Input::get('code');
        
        if( strlen( $code ) == 0 ){
            return Redirect::to('/')->with('message', 'there was an error');
        }
        
        $facebook = new Facebook( Config::get('services.facebook') );
        $uid = $facebook->getUser( );
        
        if( $uid == 0 ){
            return Redirect::to('/')->with('message', 'there was an error');
        }
        
        $me = $facebook->api('/me');
        
        dd( $me );
    }
}
