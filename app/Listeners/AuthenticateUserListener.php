<?php
	
namespace App\Listeners;

interface AuthenticateUserListener {
	public function userHasLoggedIn( $user ); 
}
	
	 