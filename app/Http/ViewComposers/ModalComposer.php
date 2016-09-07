<?php 

namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use App\Causes;
use App\Zones;
use Agent;
use DB;


class ModalComposer{
    
    public function compose(View $view){
        
        $list = array( );
        $degrees = array( );
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
        
        $countries = DB::table("pwi_country")
                        ->select("country_name", "country_alias", "country_iso_code", "zone_id")
                        ->orderBy("country_name")
                        ->get( );
        
        foreach( $countries as $country ){
            
            if( Agent::isMobile( ) ){
                if( ! isset( $list[$country->zone_id] ) ){
                    $list[$country->zone_id] = "";
                }
                $list[$country->zone_id] .= "<li><a href='/country/" . $country->country_alias . "' rel='external'><div class='flag-wrapper'><span class='flag-icon flag flag-background flag-icon-" .  strtolower( $country->country_iso_code ) . "'></span></div><div style='padding-left: 10px;'> " . $country->country_name . "</div></a></li>";
                
            }else{
                if( ! isset( $list[$country->zone_id] ) ){
                    $list[$country->zone_id] = "<a href='/country/" . $country->country_alias . "' data-iso-code='" . $country->country_iso_code . "' class='country-overlay-item'>" . $country->country_name . "</a><br />"; 
                }else{
                    $list[$country->zone_id] .= "<a href='/country/" . $country->country_alias . "' data-iso-code='" . $country->country_iso_code . "' class='country-overlay-item'>" . $country->country_name . "</a><br />"; 
                }    
            }
        }
        
        $zones = Zones::all( )->sortBy("zone_name")->values( );

        $view->with("modal")->with([
            "causes"    => $causes,
            "subcauses" => $subCauses,
            "countries" => $list,
            "zones"     => $zones,
            "iconmap"   => $causeIconMap,
        ]);
        
    }
}