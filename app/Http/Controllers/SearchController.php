<?php
namespace App\Http\Controllers;

use App\Repositories\UserRepository as UserRepository;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\SearchOrgController;
use Illuminate\Http\Request;
use App\Organizations;
use App\Http\Requests;
use App\Http\Helper;
use App\Country;
use Config;
use Agent;
use Input;
use DB;

class SearchController extends Controller
{
	/**
	* @var scriptPage	
	*/
	private $scriptPage;
	
	/**
	* @var user	
	*/
    private $user       			= null;
    
    /**
	* @var userImage
	*/
    private $userImage  			= "";
    
    /**
	* @var relatedProductCount	
	*/
    private $searchLimit 			= 0;

    /**
    *@var helper
    */
    private $helper;
    
    public function __construct(UserRepository $userObj, Request $request, Helper $helper){
        $this->scriptPage = "scripts.products";
        if( Agent::isMobile( ) && ! Agent::isTablet( ) ){
        	$this->searchLimit = 3;
        }else{
        	$this->searchLimit = 2;	
        }
        
        if( \Auth::check( ) ){
            $this->user = \Auth::user( );
            
            $this->userImage = $userObj->getUserIcon( $this->user->user_id, $this->user->user_photo_id, $request );
        }

        $this->helper = $helper;
    }

    public function index( ){
        
    }
    
    public function search( $term, Request $request ){
        
        DB::connection( )->enableQueryLog( );
        $page   = Input::get('page');
        $rt     = Input::get('returntype');
        $term 	= addslashes( $term );

        if( $request->ajax( ) ){
	        
	        $results = DB::select("SELECT hp.*, (MATCH(name,keywords) AGAINST('" . $term . "') * 2) + grade AS Sequence FROM pwi_search_homepage hp WHERE (MATCH(name, keywords) AGAINST ('" . $term . "')) OR name LIKE '" . $term . "%' ORDER BY Sequence DESC LIMIT 8");
	        
            echo json_encode( $results );
            die;
        }else{
	        
	        $countries 	= array( );
	        $causes 	= array( );
	        
	        $orgResults = array( );

	        $term 		= addslashes( $term );
	        
	        $orgs 		= SearchOrgController::whereRaw("MATCH(org_name, keywords) AGAINST ('" . $term . "')" )
	    								->orWhereRaw("org_name LIKE '" . $term . "%'" )
										->groupBy(DB::raw("MATCH(org_name, keywords) AGAINST ('" . $term . "') + org_grade DESC"))
										->select("org_id", "org_name", "org_country_string as impactCountries", "org_cause_string as causes", "org_alias", "org_rating as rating", DB::raw("CONCAT('" . Config::get("globals.orgImgPath") . "', file_path) as logo"), "org_desc")
										->take( $this->searchLimit )
										->get( );

			$orgs = $this->helper->buildOrgTileArray( $orgs, "search", "");

			$view = "pages.search";
			/*
		    if( Agent::isMobile( ) ){
		    	$view = "mobile.pages.search";
		    }
		    */

			return view( $view )->with([
		        "user" 			=> $this->user,
		        "userImg" 		=> $this->userImage,
		        "orgs" 			=> $orgs,
		        "orgViewAll" 	=> "/search/organizations/" . stripslashes( $term ),
		        "countries" 	=> $countries,
		        "causes" 		=> $causes,
		        "filter" 		=> "all",
		        "term"			=> stripcslashes( $term ),
	        ]);
	    }
    }
    
    public function searchOrganizations( $term, Request $request ){
	    
    	$countries 	= array( );
        $causes 	= array( );
        
        $orgResults = array( );
        
        $orgs = SearchOrgController::whereRaw("MATCH(org_name, keywords) AGAINST ('" . $term . "')" )
    								->orWhereRaw("org_name LIKE '" . $term . "%'" )
									->groupBy(DB::raw("MATCH(org_name, keywords) AGAINST ('" . $term . "') + org_grade DESC"))
									->select("org_id", "org_name as name", "org_country_string as impactCountries", "org_cause_string as causes", "org_alias as alias", "org_rating as rating", DB::raw("CONCAT('" . Config::get("globals.orgImgPath") . "', file_path) as logo"), "org_desc as desc")
									->get( );
        
        foreach( $orgs as $org ){
	        
	        $orgCountries =  explode(",", $org->impactCountries);
	        
	        $org["descExp"] = explode(" ", $org["desc"]);
	        
	        foreach($orgCountries as $country ){
		        if( ! empty( $country ) ){
			        if( ! in_array( $country, $countries) ){
				        /*$countries[] = array(
					        "name" => $country,
					        "alias" => str_replace(" ", "-", strtolower( $country ) );
				        )*/
				        
				        $countries[] = $country;
			        }
			    }
	        }
	        
	        $orgCauses = explode(",", $org->causes );
	        
	        foreach( $orgCauses as $cause ){
		        if( ! empty( $cause ) ){
			        if( ! in_array( $cause, $causes) ){
				        $causes[] = $cause;
			        }
			    }
	        }
	    }
        
        return view('pages.search')->with([
	        "user" 			=> $this->user,
	        "userImg" 		=> $this->userImage,
	        "orgs" 			=> $orgs,
	        "orgViewAll" 	=> "/search/organizations/" . $term,
	        "countries" 	=> $countries,
	        "causes" 		=> $causes,
	        "filter" 		=> "organizations",
	        "term"			=> $term,
        ]);
	}

	public function findCountry( $query ){
     	
     	$countries = Country::where( "country_name", "like", $query . "%")
     						  ->take( 5 )
     						  ->get( );

     	return Response::json( $countries );
	}
}
