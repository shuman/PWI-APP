<?php
	
namespace App; 

use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Illuminate\Contracts\Auth\Guard as Authenticator;
use App\Repositories\UserRepository as UserRepository;
use URL;
	
class AuthenticateUser{
	
	/**
	* @var UserRepository
	*/
	private $users;
	
	
	/**
	* @var Socialite
	*/
	private $socialite;
	
	/**
	* @var Authenticator
	*/
	
	private $auth;
	
	
	
	public function __construct(UserRepository $users, Socialite $socialite, Authenticator $auth )
	{
		$this->users 		= $users;
		$this->socialite 	= $socialite;
		$this->auth 		= $auth;
	}
	
	/**
	* function name - execute
	*	
	* @param $hasCode - Boolean 
	*
	* @param $type - string 
	*
	* @param $listener - object 
	*
	* @return redirect path after log in
	*/	
	
	public function execute( $hasCode, $type, $listener, Request $request ){
		
		if( ! $hasCode ){
			
			if( $request->has('mobilepage') ){
				session(['login-redirect-url' => URL::previous( ) . "#" . $request->input("mobilepage")]);
				
			}else{
				session(['login-redirect-url' => URL::previous( )]);
			}

			return $this->getAuthorizationFirst( $type );
		}

		$user = $this->users->findByUserEmailOrCreate( $this->getSocialUser( $type ), $type );
		
		$this->auth->login( $user, true);
		
		session(['social-platform' => $type]);
		
		return $listener->userHasLoggedIn( $user );
		
		//dd($user);
		
	}
	
	private function getAuthorizationFirst( $platform ){
		
		if( $platform == "facebook" ){
			return $this->socialite->driver($platform)->scopes(['email','user_posts'])->redirect( ); 
		}else{
			return $this->socialite->driver($platform)->redirect( ); 	
		}
		
	}
	
	private function getSocialUser( $platform ){
		return $this->socialite->driver($platform)->stateless( )->user( );
	}
	
	
}