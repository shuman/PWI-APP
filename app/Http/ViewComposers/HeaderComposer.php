<?php 

namespace App\Http\ViewComposers;

use App\Repositories\UserRepository as UserRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Files;
use Config;
use Input;
use Auth;
use DB;

class HeaderComposer{

    public function compose(View $view){
        
        $userObj = new UserRepository( );

        $user       = null;
        $userImage  = "";
        $userData   = null;
        
        if( \Auth::check( ) ){
            $user = \Auth::user( );

            $userImage = $userObj->getUserIcon( $user->user_id, $user->user_photo_id, $request );
            /*
            $file = Files::find( $user->user_photo_id );
            
            if( sizeof( $file ) > 0 ){
                $userImage = Config::get("globals.usrImgPath").$file->file_path;
            }*/
        }

        $view->share('user', $user);
        $view->share('userImg', $userImage);
    }
}