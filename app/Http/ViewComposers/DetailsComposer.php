<?php 

namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use App\Country;
use Agent;
use DB;


class DetailsComposer{
    
    public function compose(View $view){

        $countries = Country::all( )->sortBy("country_name");

        $list = array( );
        $list[0] = "Please Select a Country";

        foreach( $countries as $country ){
            $list[$country->country_id] = $country->country_name;
        }

        $view->with([
            "countries" => $list,
        ]);
        
    }
}