<?php

namespace App\Providers;

use App\Repositories\UserRepository as UserRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use App\Country;
use App\Causes;
use View;
use DB;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        View::composer('modal', 'App\Http\ViewComposers\ModalComposer');

        View::composer(['partials.modals.causes', 
                        'partials.modals._addCrowdFundingProject',
                        'partials.modals._editCrowdFundingProject',
                        'partials.products._newProductCausesAndImpacts'], 
        function( $view ){

            $causeIconMap = array( );
        
            $causeIconMap = array( 
                "1" => "pwi-cause-environment-stroke",
                "20" => "pwi-cause-family-stroke",
                "21" => "pwi-cause-humanrights-stroke",
                "22" => "pwi-cause-education-stroke",
                "23" => "pwi-cause-poverty-stroke",
                "24" => "pwi-cause-religion-stroke",
                "43" => "pwi-cause-water-stroke",
                "44" => "pwi-cause-economy-stroke",
                "45" => "pwi-cause-government-stroke",
                "46" => "pwi-cause-health-stroke",
                "47" => "pwi-cause-children-stroke"
            );

            $causes = Causes::where("cause_parent_id", "=", "0")
                    ->select("cause_id", "cause_name", "cause_alias")
                    ->get( );

            $subCauses = Causes::where("cause_parent_id", "<>", "0")
                    ->select("cause_id", "cause_name", "cause_alias", "cause_parent_id")
                    ->orderBy("cause_parent_id")
                    ->get( );

            $view->with([
                'causeList'     => $causes,
                'subCauseList'  => $subCauses,
                'iconMap'       => $causeIconMap,
            ]);
        });

        View::composer('donations', 'App\Http\ViewComposers\DetailsComposer');

        View::composer('pages.crowdfunding.fund', 'App\Http\ViewComposers\DetailsComposer');

        View::composer('pages.products.purchase', 'App\Http\ViewComposers\DetailsComposer');

        View::composer('mobile.overlay.donate', 'App\Http\ViewComposers\DetailsComposer');

        View::composer('mobile.overlay.fund', 'App\Http\ViewComposers\DetailsComposer');

        View::composer('mobile.overlay.purchase', 'App\Http\ViewComposers\DetailsComposer');

        View::composer('partials.modals.contactInfo', function( $view ){

            /** 
                Not using 'DetailsComposer' because details composer is used in the checkout 
                area and the checkout area does not have conflicting $countries 
                variables like the org view page does.
            **/

            $countries = Country::all( )->sortBy("country_name");

            $list = array( );
            $list[0] = "Country";

            foreach( $countries as $country ){
                $list[$country->country_id] = $country->country_name;
            }

            $view->with([
                "countryDropDownList" => $list,
            ]);
        });

        View::composer('partials.crowdfunding.details', 'App\Http\ViewComposers\DetailsComposer');

        View::composer('partials.products.details', 'App\Http\ViewComposers\DetailsComposer');

        View::composer('mobile.overlay.continents', 'App\Http\ViewComposers\ModalComposer');

        View::composer('mobile.overlay.countries', 'App\Http\ViewComposers\ModalComposer');

        View::composer('mobile.overlay.causes', 'App\Http\ViewComposers\ModalComposer');

        View::composer("partials.modals._payment_gateway", function( $view ){

            $gateways = DB::table("pwi_payment_gateways")
                        ->select("pk", "payment_gateway_name")
                        ->where("gateway_status", "=", "active")
                        ->get( );

            $view->with([
                "gateways" => $gateways
            ]);
        });
        
        View::composer('errors/404', function( $view ) use( $request ){
	        
	        $userObj = new UserRepository( );
            
            $user = null;
            
            $userImg = null;
            
            if( \Auth::check( ) ){
                $user = \Auth::user( );
                
                $userImage = $userObj->getUserIcon( $user->user_id, $user->user_photo_id, $request );
            }
            
            $view->with([
            'user' => $user,
            'userImg' => $userImg,
            'isError' => true,
            ]);
	        
        });
        
        //View::composer('header', 'App\Http\ViewComposers\HeaderComposer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
