<?php

namespace App\Http\Controllers;

use App\Repositories\SocialMediaRepository as sMRepository;
use App\Repositories\NewsRepository as NewsRepository;
use App\Repositories\UserRepository as UserRepository;
use App\Repositories\PaymentRepository as Payments;
use Illuminate\Support\Facades\Response;
use App\ProductModifierOptionInventer;
use App\Http\Controllers\Controller;
use App\ProductModifierOptions;
use App\OrgSubCauseCountries;
use App\OrganizationContent;
use Illuminate\Http\Request;
use App\ProjectCauseDetails;
use App\ProductShipMethods;
use App\ProjectIncentives;
use App\ProductModifiers;
use App\ProjectUpdates;
use App\ProductImages;
use App\ProductCauses;
use App\Http\Requests;
use App\Organizations;
use App\ProjectMaster;
use App\OrgGradeList;
use App\Http\Helper;
use App\SocialMedia;
use App\Donations;
use App\OrgPhotos;
use Carbon\Carbon;
use App\OrgCauses;
use App\Products;
use App\Projects;
use App\Country;
use App\Follow;
use App\States;
use App\Videos;
use App\Files;
use Config;
use Agent;
use Input;
use DB;

class OrgController extends Controller
{
    /**
     * Javascript file name to be loaded
     *
     * @var string
     */
    private $scriptPage;
    /**
     * Object of Helper Class 
     *
     * @var object
     */
    private $helper;
    /**
     * Object of User Class - Used for Authentication
     *
     * @var object
     */
    private $user       = null;
    /**
    * Object of UserRepository 
    *
    * @var object
    */
    private $userObj    = null;
    
    /**
    * Object of SocialMediaRepository 
    *
    * @var object
    */
    private $smRepo		= null;
    
    /**
     * Org __construct function
     *
     * @param UserRepository object          $userObj
     *
     * @param Request object                 $request
     *
     * @param Helper object                  $helper
     *
     * @return SocialmediaRepository object   $sMRepository
     */
    public function __construct(UserRepository $userObj, Request $request, Helper $helper, sMRepository $smRepo){
        $this->scriptPage 	= "scripts.org";
        $this->helper 		= $helper;
        
        $this->user         = $request->instance( )->query('user');
        
        $this->userObj = $userObj;
		$this->smRepo  = $smRepo;
    }
    
    /**
     * Organization index function - Return the View for Organization Index Page
     *
     * @return View
     */
    public function index( ){
        $list = array( );
        
        //Grab the initial eight organizations from DB
        $orgs = Organizations::where("org_featured","=","Y")
              ->where("org_status","=","active")
              ->where("org_logo",">","0")
              ->leftJoin("pwi_files AS LOGO", "LOGO.file_id", "=", "pwi_organization.org_logo")
              ->select("org_id","org_name","org_desc","org_alias", "LOGO.file_path AS logoImg")
              ->groupBy("org_id")
              ->orderBy("org_grade", "DESC")
              ->orderBy("RandomSort")
              ->take( 8 )
              ->get( );
        

        $list = $this->iterateOverOrgs( $orgs );
        
        //Grab generic meta data for organization index page
		$meta = $this->helper->getMetaData("general", "organizations")->toArray( );
        
        /**
            These variables are used for the Javascript on the front end.
            The initial pull is the amount that is initially given
            The payload is how many will be returned on each 
            subsequent pull.
        */
        $initialPull = 8;
        $payload = 8;

        $view = "pages.organizations.index";

        if( Agent::isMobile( ) && ! Agent::isTablet( ) ){
            $view = "mobile.pages.organizations.index";
        }
        
        return view( $view )->with([
            "orgs"          => $list,
            "initialPull"   => $initialPull,
            "payload"       => $payload,
            "logoPath"      => Config::get("globals.orgImgPath"),
            "meta"			=> $meta[0]
        ]);
    }
    
    /**
     * Organization view function - Return the View for an Indivdual Organization Page
     *
     * @param $alias:string
     *
     * @return View
     */
    public function view( $alias ){

        //DB::connection( )->enableQueryLog( );

        //set up initial arrays
        $causeList = array( );
        $projectList = array( );
        $productList = array( );
        $org;
       
		try{
	        //Grab organization data
	        $org = Organizations::where("org_alias", "=", $alias)
                    ->leftJoin("pwi_state AS STATE", "STATE.state_id","=", "pwi_organization.org_state")
                    ->leftJoin("pwi_files AS LOGO", "LOGO.file_id", "=", "pwi_organization.org_logo")
                    ->leftJoin("pwi_files AS CP", "CP.file_id", "=", "pwi_organization.org_cover_image")
                    ->leftJoin("pwi_org_settings as ORGSET", "ORGSET.org_id", "=", "pwi_organization.org_id")
                    ->select("pwi_organization.org_id","org_name", "org_email", "org_weburl", "org_mobile_number", "org_alias", "org_title", "org_addressline1", "org_addressline2", "org_city", "org_state", "org_zip", "org_country", "org_amt_raised", "org_revenue", "STATE.state_code", "LOGO.file_path AS logoImg", "CP.file_path AS coverphotoImg", "ORGSET.paypal_username as paypal", "ORGSET.fk_payment_gateway as gateway")
                    ->firstOrFail( );
	    }catch( \Exception $e){
		    abort( 404 );
	    }

        $iAmAnAdmin = FALSE;
        $contactInfoStates = array( );

        if( !empty( $this->user->user_org_id ) && $this->user->user_org_id == $org->org_id ){
            $iAmAnAdmin = TRUE;

            //getStates for initial country for the contactInfo View

            $contactInfoStates = States::where("country_id", $org->org_country)->get( );
        }

        $aboutUs = "";
        $mission = "";

        /**
        Grab the contens of the current organization
        Loop through and extract the data that 
        will be displayed on the view
        */

        $contents = $org->content;

        foreach( $contents as $content ){

            switch( $content->org_content_type ){
                case "Financials":
                break;
                case "About Us":
                    $aboutUs = $content->org_content_description;
                break;
                case "Mission":
                    $mission = $content->org_content_description;
                break;
                case "Testimonial":
                break;
                case "Contact Us":
                break;
            }
        }

        $subCauses = OrgSubCauseCountries::where("org_id", $org->org_id)
                    ->where("org_sc_type","subcause")
                    ->select("CSE.cause_name", "pwi_org_subcause_countries.*")
                    ->join("pwi_causes AS CSE", "CSE.cause_id", "=", "pwi_org_subcause_countries.org_sc_item_id")
                    ->get( );

        $coverPhotoExists = TRUE;
        
        if( ! file_exists( public_path( ) . Config::get('globals.orgImgPath') . $org->coverphotoImg ) ){
	        $coverPhotoExists = FALSE;
        }
        
        $logo = "";
        
        if( file_exists( public_path( ) . Config::get("globals.orgImgPath") . $org->logoImg) ){
            $logo = Config::get("globals.orgImgPath") . $org->logoImg;
        }else{
            $logo = "/images/orgPlaceHolder.jpg";
        }
        
        /**
        Check if the organization has a gateway set up.
        If they do, donations will be allowed for 
        the organiztion and any subsequent 
        projects they've set up.
        */

		$paypal_un = "";
        $hasGateway = TRUE;

        if( empty( $org->gateway ) ){
            $hasGateway = FALSE;
        }else{
            $paypal_un = $org->paypal;
        }
        
        //Grab the social media available for the organization
        $socialMedia = $org->socialMedia;

        $feeds = array( );
        
        //Retrieve relevant social media feeds.
        $feeds = $this->smRepo->getFeeds( $socialMedia );
        
        $isFollowing = 0;
        
        /**
        Check to see if the user is logged in.
        If so, check to see if they are
        following this organization
        */
        if( ! is_null( $this->user) ){
	        $isFollowing = $this->userObj->isFollowing( $this->user, "org", $org->org_id);
	    }
        
        //Grab metadata for this specific organization
		$meta = $this->helper->getMetaData("individual", "organizations");

        $view = "pages.organizations.organization";

        if( Agent::isMobile( ) && ! Agent::isTablet( ) ){
            $view = "mobile.pages.organizations.organization";
        }
        
        return view( $view )->with([
            "org"           => $org,
            "aboutUs"       => $aboutUs,
            "mission"       => $mission,
            "logo"          => $logo,
            "hasCoverPhoto"	=> $coverPhotoExists,
            "coverphoto"    => Config::get('globals.orgImgPath') . $org->coverphotoImg,
            "causes"        => $this->helper->parseCauses( $org->causes ),
            "countries"     => $org->impactCountries,
            "photos"        => $org->photos,
            "videos"        => $org->videos,
            "projects"      => Helper::buildProjectsTileArray( $org->crowdfunding ),
            "products"      => Helper::buildProductsTileArray( $org->products ),
            "reviews"       => $org->reviews,
            "rating"        => $org->rating->avg( "comment_rating" ),
            "socialmedia"   => $socialMedia,
            "scriptPage"    => $this->scriptPage,
            "prjPath"       => Config::get("globals.prjImgPath"),
            "prdPath"       => Config::get("globals.prdImgPath"),
            "prjViewAll"    => "/organization/" . $org->org_alias . "/project",
            "prdViewAll"    => "/country/" . $org->org_alias . "/products",
            "paypal_un"     => $paypal_un,
            "meta"			=> $this->helper->parseIndMetaData( $meta[0], stripslashes( $org->org_name ) ),
            "following"		=> $isFollowing,
            "feeds"			=> $feeds,
            "hasGateway"    => $hasGateway,
            "isAdmin"       => $iAmAnAdmin,
            "orgSubCauses"  => $subCauses,
            "ciStates"      => $contactInfoStates
        ]);
    }

    /**
     * Organization donate function - Return the View for an Indivdual Organization Donation Page
     *
     * @param $alias:string
     *
     * @param $request:Request
     *
     * @return View
     */
    public function donate( $alias, Request $request ){

        //DB::connection( )->enableQueryLog( );
        
        $org;
        
        // try to fetch this org - if Exception abort to 404
        try{
            $org = Organizations::where("org_alias", "=", $alias)
                            ->select("org_id", "org_name")
                            ->firstOrFail( );
        } catch( \Exception $e){
            abort( 404 );
        }
        
        $donationAmt = 0.00;

        //Run query to see if user has an active gateway
        $gateway = DB::table("pwi_org_settings")
                      ->where("org_id", "=", $org->org_id)
                      ->get( );

        $paypal_un = "";
        $paymentGateway = 0;

        /**
         * Check if sizeof $gateway is greater than 0 and if paypal username is not empty
         * If conditional is true, redirect user to Crowdfunding view function
         * If conditional is false, assign $paypal_un the paypal username 
         * from the above $gateway query
        */
        if( sizeof( $gateway ) == 0 ){
            return redirect( )->action('OrgCongroller@view', $alias);    
        }else{
            $paypal_un = $gateway[0]->paypal_username;
            $paymentGateway = $gateway[0]->fk_payment_gateway;
        }

        
        //check if there is a session variable that holds donation amount
        if( $request->session()->has('donationAmt') ){

            //retieve donation amount
            $donationAmt = $request->session( )->get('donationAmt');
            //erase donation amount from session storage
            //$request->session->forget('donationAmt');
        }else{
            $donationAmt = "";
        }

        $years = array( );

        for( $i = Carbon::now( )->year ; $i < ( Carbon::now( )->year + 8 ) ; $i++ ){

            $yearAbbr = substr($i, -2);
            $years[$yearAbbr] = $i; 
        }

        //get meta data for page
        $meta = $this->helper->getMetaData("individual", "donations");

        return view("donations")->with([
            "org"               => $org,
            "amount"            => $donationAmt,
            "paypal_un"         => $paypal_un,
            "payment_gateway"   => $paymentGateway,
            "years"             => $years,
            "scriptPage"        => $this->scriptPage,
            "meta"              => $this->helper->parseIndMetaData( $meta[0], $org->org_name),
        ]); 
    }
    
    /**
     * Organization more function - Returns JSON object for additional organizations on index page
     *
     * @param $request:Request
     *
     * @return JSON object
     */
    public function more( Request $request){
        
        // Check if request is an ajax request
        if( $request->ajax( ) ){

            //Get the amount of organizations to return
            $count =  Input::get("payload");

            //Get the amount that has already been returned
            $skip  =  Input::get("next");
            
            $list = array( );
        
            $orgs = Organizations::where("org_featured","=","Y")
                  ->where("org_status","=","active")
                  ->where("org_logo",">","0")
                  ->leftJoin("pwi_files AS LOGO", "LOGO.file_id", "=", "pwi_organization.org_logo")
                  ->select("org_id","org_name","org_desc","org_alias", "LOGO.file_path AS logoImg")
                  ->groupBy("org_id")
                  ->orderBy("org_grade", "DESC")
                  ->orderBy("RandomSort")
                  ->take( $count )
                  ->skip( $skip )
                  ->get( );
            
            $list = $this->iterateOverOrgs( $orgs );
            
            echo json_encode( $list );
            die;
        }
    }
    
    /**
     * Organization getOrgsForCountry function 
     * Returns View of Organizations which have impact in a specific country
     *
     * @param $alias:string
     *
     * @return View
     */
    public function getOrgsForCountry( $alias ){
        
        $list = array( );
        
        //Get country information of passed country
        $country = \App\Country::where("country_alias","=",$alias)->firstOrFail( );
        
        //Get all organizations with impacts in the country that was passed
        $orgs = Organizations::where("org_status","=","active")
                  ->where("SC.org_sc_type","=", "country")
                  ->where("SC.org_sc_status", "=", "active")
                  ->where("CTRY.country_alias","=", $alias)
                  ->leftJoin("pwi_files AS LOGO", "LOGO.file_id", "=", "pwi_organization.org_logo")
                  ->leftJoin("pwi_org_subcause_countries AS SC", "SC.org_id", "=", "pwi_organization.org_id")
                  ->leftJoin("pwi_country AS CTRY", "SC.org_sc_item_id", "=", "CTRY.country_id")
                  ->select("pwi_organization.org_id","org_name","org_desc","org_alias", "LOGO.file_path AS logoImg")
                  ->groupBy("pwi_organization.org_id")
                  ->orderBy("pwi_organization.org_grade", "DESC")
                  ->orderBy("pwi_organization.RandomSort")
                  ->get( );

        $list = $this->iterateOverOrgs( $orgs );
        
        $initialPull = 8;
        $payload = 8;

        $meta = $this->helper->getMetaData("individual", "search_results_page");
        
        return view("pages.country.organizations")->with([
            "orgs"          => $list,
            "country_name"  => $country->country_name,
            "alias"         => $alias,
            "initialPull"   => $initialPull,
            "payload"       => $payload,
            "logoPath"      => Config::get("globals.orgImgPath"),
            "meta"          => $this->helper->parseSearchMetaData( $meta[0], "Organizations", $country->country_name),
        ]);
    }

    /**
     * Organization getOrgsForCause function 
     * Returns View of Organizations which have impact in a specific cause
     *
     * @param $alias:string
     *
     * @return View
     */
    public function getOrgsForCause( $alias ){
        
        //DB::connection( )->enableQueryLog( );
        
        $list = array( );
        
        //Get country information of passed cause
        $cause = \App\Causes::where("cause_alias","=",$alias)->firstOrFail( );

        $orgs = Organizations::where("org_status","=","active");
        
        if( $cause->cause_parent_id == 0 ){
            $orgs = $orgs->where("SC.cause_id", $cause->cause_id);
        }else{
            $orgs = $orgs->where("SC.org_sc_type", "=", "subcause")
                ->where("SC.org_sc_item_id", $cause->cause_id);
        }

        $orgs = $orgs->where("SC.org_sc_status", "=", "active")
          ->leftJoin("pwi_files AS LOGO", "LOGO.file_id", "=", "pwi_organization.org_logo")
          ->leftJoin("pwi_org_subcause_countries AS SC", "SC.org_id", "=", "pwi_organization.org_id")
          ->leftJoin("pwi_causes AS CSE", "SC.org_sc_item_id", "=", "CSE.cause_id")
          ->select("pwi_organization.org_id","org_name","org_desc","org_alias", "LOGO.file_path AS logoImg")
          ->groupBy("pwi_organization.org_id")
          ->get( );
        
        $list = $this->iterateOverOrgs( $orgs );
        
        $meta = $this->helper->getMetaData("individual", "search_results_page");
        
        return view("pages.causes.organizations")->with([
            "orgs"          => $list,
            "alias"         => $alias,
            "cause_name"    => $cause->cause_name,
            "logoPath"      => Config::get("globals.orgImgPath"),
            "meta"          => $this->helper->parseSearchMetaData( $meta[0], "Organizations", $cause->cause_name),
        ]);
    }

    /**  
    * Org iterateOverOrgs Function - private
    *
    * Iterate over collection of orgs and build array 
    * with relevant information
    *
    * @param $orgs:Collection
    *
    * @return $list:array
    */
    private function iterateOverOrgs( $orgs ){

        /** Instantiate array **/
        $list = array( );

        //Iterate through orgs and grab relevant informationt to be displayed
        foreach( $orgs as $org ){

            /** Get causes for this Org **/
            $causes = $org->causes;

            /** Get countries for this Org **/
            $countries = $org->impactCountries;

            /** Get this Orgs Rating **/
            $rating = $org->rating->sum( "comment_rating" );

            /** Assign the current collection to tmp as an Array **/
            $tmp = $org->toArray( );
            
            /** 
            * Check if image path exists
            * If true, then assign the image path to the "logoImg" tmp index
            * else, assign a placeholder to the "logoImg" tmp index
             **/
            if( file_exists( public_path( ) . Config::get("globals.orgImgPath") . $tmp["logoImg"]) ){
                $tmp["logoImg"] = Config::get("globals.orgImgPath") . $tmp["logoImg"];
            }else{
                $tmp["logoImg"] = "/images/orgPlaceHolder.jpg";
            }

            /** Assign causes, rating and countries to the tmp array **/
            $tmp["causes"] = $causes;
            $tmp["rating"] = $rating;
            $tmp["countries"] = $countries;

            /** assign tmp array to next index of $list **/
            $list[] = $tmp;
        }

        /** Return list **/
        return $list;
    }

    /**
     * Org dashboard function
     *
     * @param Request object                 $request
     *
     * @return View   
     */
    public function dashboard( Request $request ){

        /** Grab the Org Id from user Object  **/
        $orgId = "";
        
        if( isset( $this->user ) ){
	        $orgId = $this->user->user_org_id;
        }else{
	        return redirect( )->route('home');
        }
        
		/** 
         * Check if orgId from the user object is null or empty 
         * If true, redirect /home    
         **/
        if( is_null( $orgId ) || empty( $orgId ) ){
            return redirect( )->route('home');
        }

        /** Grab Org **/
        $org = Organizations::find( $orgId );

        $orgGateway = null;

        /** Grab Org Settings **/
        $orgSettings = DB::table("pwi_org_settings")
                    ->where("org_id", "=", $org->org_id)
                    ->get( );

        if( sizeof( $orgSettings ) > 0 ){
            $orgGateway = $orgSettings[0];
        }

        /** Grab the QuickView Information **/
        $quickViews = $this->helper->getQuickViews( $orgId );

        /** Get Profile Score Information **/
        list($score, $maxGrade, $percentage) = $this->getOrgProfileScore( $orgId );

        /** Get the GradeList to Display Object from The database **/
        $gradeList = OrgGradeList::orderBy('sequence')->get( );

        /** Get the GradeList and calculated Score for each grade **/
        list($gradeList, $calculatedScore) = $this->getOrgGradeList( $gradeList->toArray( ), $org );

        /** Recalculate Score if scores to do match **/

        if( $calculatedScore != $score ){
            $score = $calculatedScore;

            $percentage = ($score/$maxGrade) * 100;
        }

        /** 
        * Start Retrieving the Donation/Product Information
        * to display on the organization dashboard
        **/

        //Funding & Donations
        $crowdFundingDonations  = 0;
        $donations              = 0;

        /** Grab Crowdfunding Donations **/
        $crowdFundingDonationQuery = ProjectMaster::select(DB::raw("IFNULL( SUM( donation_amount ), 0 ) AS TotalProjects"))
                                    ->join("pwi_projects AS pp", "pp.project_id", "=", "pwi_project_donation_master.project_id")
                                    ->where("pp.org_id", "=", $org->org_id)
                                    ->where("donation_status", "=", "1")
                                    ->get( );

        $crowdFundingDonations = $crowdFundingDonationQuery[0]->TotalProjects;

        /** End Crowdfunding Donations **/

        /** Grab Organization Donations **/
        $donationQuery = Donations::select(DB::raw("IFNULL( SUM( donation_amount ), 0 ) AS TotalDonations"))
                        ->where("donation_status", "=", "1")
                        ->where("item_type", "=", "organization")
                        ->where("item_id", "=", $org->org_id)
                        ->get( );

        $donations = $donationQuery[0]->TotalDonations;

        /** End Organization Donations **/

        /** Start Prouct Information **/
        $productTotalAmount = 0;
        $numberSold         = 0;
        $avgPrice           = 0;
        $mostPopular        = "";

        /** Retrieve Product Total **/
        $productTotalQuery = Products::select( DB::raw( "IFNULL( SUM( order_item_total ), 0) AS TotalAmountSold") )
                            ->join("pwi_order_details AS od", "od.product_id", "=", "pwi_products.product_id")
                            ->join("pwi_order_master AS om", function( $join ){
                                    $join->on("om.order_id", "=", "od.order_id")
                                    ->where("om.order_status", "=", "1");
                            })
                            ->where("pwi_products.org_id", "=", $org->org_id)
                            ->get( );

        $productTotalAmount = $productTotalQuery[0]->TotalAmountSold;

        
        /** Retreive the Number of Products Sold **/
        $numberSoldQuery = Products::select( DB::raw( "IFNULL( SUM( product_count ), 0 ) AS TotalProductsSold" ) )
                            ->join("pwi_order_details AS od", "od.product_id", "=", "pwi_products.product_id")
                            ->join("pwi_order_master AS om", function( $join ){
                                $join->on("om.order_id", "=", "od.order_id")
                                ->where("om.order_status", "=", "1");
                            })
                            ->where("pwi_products.org_id", "=", $org->org_id)
                            ->get( );

        $numberSold     = $numberSoldQuery[0]->TotalProductsSold;


        /** Retrieve the Average Price for Products Sold **/
        $avgPriceQuery = Products::select( DB::raw( "IFNULL( SUM( order_item_total ), 0 ) AS AverageAmount" ) )
                        ->join("pwi_order_details AS od", "od.product_id", "=", "pwi_products.product_id")
                        ->join("pwi_order_master AS om", function( $join){
                            $join->on("om.order_id", "=", "od.order_id")
                            ->where("om.order_status", "=", "1");
                        })
                        ->where("pwi_products.org_id", "=", $org->org_id)
                        ->get( );

        $avgPrice       = $avgPriceQuery[0]->AverageAmount;



        /** Retrieve the Most Popular Products **/
        $mostPopularQuery = Products::select( DB::raw( "pwi_products.product_sku, SUM( product_count ) AS TotalSold" ))
                        ->join("pwi_order_details AS od", "od.product_id", "=", "pwi_products.product_id")
                        ->join("pwi_order_master AS om", function( $join ){
                            $join->on("om.order_id", "=", "od.order_id")
                            ->where("om.order_status", "=", "1");
                        })
                        ->where("pwi_products.org_id", "=", $org->org_id)
                        ->groupBy("pwi_products.product_sku")
                        ->orderBy("TotalSold", "desc")
                        ->take( 1 )
                        ->get( );

        $mostPopular = "";

        if( sizeof( $mostPopularQuery ) > 0 ){
            $mostPopular = $mostPopularQuery[0]->product_sku;    
        }

        /** 
        * End retrieving Donation/Products Information
        **/

        //Featured Profile

        /** Query for Featured Org Here **/

        /** Begin Retrieving Causes and Twitter Information to display on Dashbard **/
        $causes         = $this->helper->parseCauses( $org->causes );
        $causeNews      = array( );
        $countryNews    = array( );
        $causeTwitter   = array( );

        $newsRepository = new NewsRepository( );

        /** Loop through Causes and retrieve News/Twitter for each Cause **/
        foreach( $causes as $cause ){
            $causeNews[ $cause["name"] ] = $newsRepository->getNews( $cause["name"] );
            $causeTwitter[ $cause["name"] ] = $this->smRepo->getTwitterHashTags( $cause["hashtags"] );    
        }

        /** End Cause Retrieval **/

        /*
        if( sizeof( $causeNews ) > 2 ){
            $causeNews = $this->helper->getRandomArray( $causeNews, 2);
        }

        if( sizeof( $causeTwitter ) > 2 ){
            $causeTwitter = $this->helper->getRandomArray( $causeTwitter, 2 );
        }
        */



        /** Begin Retrieving Country Information for display on Dashboard **/

        $countries = $org->impactCountries;

        /** Loop through each country and retrieve News for each country **/
        foreach( $countries as $country ){
            $countryNews[ $country->country_name] = $newsRepository->getNews( $country->country_name );
        }

        /*
        if( sizeof( $countryNews ) > 2 ){
            $countryNews = $this->helper->getRandomArray( $countryNews, 2 );
        }
        */

        /** End Country Retrieval **/



        /** Check if the org has a subscription with PWI **/
        $hasSubscription = FALSE;

        $subscription = DB::table("pwi_org_subscription")
                        ->leftJoin("pwi_subscription_fee as subFee", "subFee.subscription_id", "=", "pwi_org_subscription.subscription_id")
                        ->where("org_id", $orgId)
                        ->get( );
        if( sizeof( $subscription ) > 0 ){
            $hasSubscription = TRUE;
        }

        //Grab metadata for this specific organization
        $meta = $this->helper->getMetaData("individual", "organizations");

        /** Pass data to the View **/
        return view("pages.organizations.dashboard")->with([
            "org"               => $org,
            "logo"              => $org->logo,
            "causes"            => $causes,
            "projects"          => Helper::buildProjectsTileArray( $org->crowdfunding ),
            "products"          => Helper::buildProductsTileArray( $org->products ),
            "meta"              => $this->helper->parseIndMetaData( $meta[0], stripslashes( "Dashboard for " . $org->org_name ) ),
            "quickView"         => $quickViews,
            "profileScore"      => $score,
            "maxGrade"          => $maxGrade,
            "percentage"        => $percentage,
            "gradeList"         => $gradeList,
            "projectDonations"  => $crowdFundingDonations,
            "donations"         => $donations,
            "productTotalAmount"=> $productTotalAmount,
            "numberSold"        => $numberSold,
            "avgPrice"          => $avgPrice,
            "mostPopular"       => $mostPopular,
            "causeNews"         => $causeNews,
            "countryNews"       => $countryNews,
            "causeTwitter"      => $causeTwitter,
            "orgGateway"        => $orgGateway,
            "hasSubscription"   => $hasSubscription,
            "subscription"      => $subscription,
        ]); 
    }

    /** 
    * Org getOrgProfileScore Function - private
    *
    * Get the profile org score
    *
    * @param $orgId:Int
    *
    * @return Array
     **/
    private function getOrgProfileScore( $orgId ){

        /**  Get the MaxGrade for the Organization **/
        $maxGrade = DB::table("pwi_lookup AS p1")
                    ->select("p1.vLookUp_Value as MaxGrade")
                    ->where("p1.vLookUp_Name", "=", "max_grade")
                    ->get( );

        $maxGrade = $maxGrade[0]->MaxGrade;

        /** Get the org Score **/
        $score = Organizations::find( $orgId )->org_grade;

        /** Calculate percentage **/
        $percentage = ($score/$maxGrade) * 100;

        /** Return data to dasboard function **/
        return array( $score, $maxGrade, $percentage);
    }

    /** 
    * Org getOrgGradeList Function - private
    * 
    * Get the Grade List for the organization
    *
    * @param $grades:aArray
    *
    * @param $org:Collection Item
    *
    * @return Array
     **/
    private function getOrgGradeList( $grades, $org ){

        $score = 0;

        /** Loop Through Grades **/
        foreach( $grades as &$grade ){

            /** Switch Statement on "description" index of $grade array **/
            switch( $grade["description"] ){
                case "Description":
                    $content = OrganizationContent::where("org_id", "=", $org->org_id)
                                ->where("org_content_type", "=", "About Us")
                                ->select("org_content_description as description")
                                ->get( );

                    if( isset( $content[0] ) ){
                        if( ! empty( $content[0]->description ) || ! is_null( $content[0]->description ) ){
                            $grade["has"] = TRUE;
                            $score += $grade["points"];
                        }else{
                            $grade["has"] = FALSE;
                        }   
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Email":
                    if( ! empty( $org->org_email) || ! is_null( $org->org_email ) ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Web URL":
                    if( ! empty( $org->org_weburl ) || ! is_null( $org->org_weburl) ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "EIN":
                    if( ! empty( $org->org_ein ) || ! is_null( $org->org_ein) ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Payment Gateway":
                    $paymentGateway = DB::table("pwi_org_settings")
                                    ->where("org_id", "=", $org->org_id)
                                    ->count( );

                    if( $paymentGateway > 0 ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Social Media":
                    $socialMedia = DB::table("pwi_org_social_media")
                                ->where("org_id", "=", $org->org_id)
                                ->count( );

                    if( $socialMedia > 0 ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Photos":
                    $photos = $org->photos;

                    if( ! $photos->isEmpty( ) ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Videos":
                    $videos = $org->videos;

                    if( ! $videos->isEmpty( ) ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Products":
                    $products = $org->products;

                    if( ! $products->isEmpty( ) ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Projects":
                    $projects = $org->crowdfunding;

                    if( ! $projects->isEmpty( ) ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Causes":
                    $causes = $org->causes;

                    if( ! $causes->isEmpty( ) ){
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }else{
                        $grade["has"] = FALSE;
                    }
                break;
                case "Logo":
                    if( $org->org_logo == 0 || empty( $org->org_logo ) ){
                        $grade["has"] = FALSE;
                    }else{
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }
                break;
                case "Cover Image":
                    if( $org->org_cover_image == 0 || empty( $org->org_cover_image ) ){
                        $grade["has"] = FALSE;
                    }else{
                        $grade["has"] = TRUE;
                        $score += $grade["points"];
                    }
                break;
            }
        }
        /** Return grades and overall score **/
        return array( $grades, $score);
    }

    /** 
    * Org saveGateway function 
    *
    * Ajax function to set The Payment Gateway
    *
    * @param Request $request
    *
    * @return JSON Object
     **/
    public function saveGateway( Request $request ){

        /** Set up Variables for Validation **/
        $validationList = array( );
        $messages       = array( );

        /** Set up payload for DB **/
        $dbPayload      = array( );

        /** Switch Statement Based on Gateway ID **/
        switch( Input::get("gateway") ){
            /** PayPal Pro Case **/
            case "1":

                /** Set up validation for PayPal Pro **/
                $validationList["clientId"] = "required";
                $validationList["secret"]   = "required";

                /** Set up messages for validation **/
                $messages["clientId.required"]  = "The PayPal Client Id is Required.";
                $messages["secret.required"]    = "The PayPal Secret is Required.";

                /** Set up Array to update/insert row into DB **/
                $dbPayload = array(
                    "paypal_client_id"      => Input::get("clientId"),
                    "paypal_client_secret"  => Input::get("secret"),
                    "used_payment_gateway"  => "paypalpro",
                    "fk_payment_gateway"    => "1",
                    "org_id"                => Input::get("orgId"),
                    "paypal_username"       => "",
                    "authorizeNET_name"     => "",
                    "authorizeNET_key"      => "",
                    "gateway_key"           => "",
                );
            break;
            /** Transnational Case **/
            case "2":

                /** Set up Validation for Transnational **/
                $validationList["gatewayKey"]    = "required";

                /** Set up Message for validation **/
                $messages["gatewayKey.required"] = "The Transnational Gateway Key is required.";

                /** Set up Array to update/insert row into DB **/
                $dbPayload = array(
                    "gateway_key"           => Input::get("gatewayKey"),
                    "used_payment_gateway"  => "transnational",
                    "fk_payment_gateway"    => "2",
                    "org_id"                => Input::get("orgId"),
                    "paypal_username"       => "",
                    "authorizeNET_name"     => "",
                    "authorizeNET_key"      => "",
                    "paypal_client_id"      => "",
                    "paypal_client_secret"  => ""
                );
            break;
            /** PayPal Std Case **/
            case "3":

                /** Validation for PayPal Std **/
                $validationList["payPalUsername"] = "required|email";

                /** Messages for PayPal Std Validation **/
                $messages["payPalUsername.required"] = "The PayPal Username is Required.";
                $messages["payPalUsername.email"]    = "Not a valid email.";

                /** Set up Array to update/insert row into DB **/
                $dbPayload = array(
                    "paypal_username"       => Input::get("payPalUsername"),
                    "used_payment_gateway"  => "paypalstd",
                    "fk_payment_gateway"    => "3",
                    "org_id"                => Input::get("orgId"),
                    "authorizeNET_name"     => "",
                    "authorizeNET_key"      => "",
                    "gateway_key"           => "",
                    "paypal_client_id"      => "",
                    "paypal_client_secret"  => ""
                );
            break;
            /** Authorize.NET Case **/
            case "4":

                /** Validation for Authorize.MET **/
                $validationList["name"] = "required";
                $validationList["key"]  = "required";

                /** Messages for Validation **/
                $messages["name.required"] = "The Authenticate.NET name is required.";
                $messages["key.required"]  = "The Authenticate.NET key is required.";

                /** Set up Array to update/insert row into DB **/
                $dbPayload = array(
                    "authorizeNET_name"     => Input::get("name"),
                    "authorizeNET_key"      => Input::get("key"),
                    "used_payment_gateway"  => "authorize.net",
                    "fk_payment_gateway"    => "4",
                    "org_id"                => Input::get("orgId"),
                    "paypal_username"       => "",
                    "paypal_client_id"      => "",
                    "paypal_client_secret"  => "",
                    "gateway_key"           => "",
                );
            break;
        }

        /** Create Validator Object **/
        $validator = \Validator::make($request->all(), $validationList, $messages);

        /** 
        * Check if validator Fails 
        * If so, return JSON object with errors
        * else, continue
        **/
        if ( $validator->fails( ) ){
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }else{
            
            /** Query to see if Gateway Exists for Org **/
            $hasGateway = DB::table( "pwi_org_settings" )
                        ->where("org_id", "=", Input::get("orgId"))
                        ->get( );

            $method = "";

            /** Check size of $hasGateway has rows **/
            if( sizeof( $hasGateway ) > 0 ){
                /** Update Org Settings Row **/
                DB::table("pwi_org_settings")
                    ->where("org_id", Input::get("orgId"))
                    ->update( $dbPayload );
                $method = "update";
            }else{
                /** Insert New Org Settings Row **/
                DB::table("pwi_org_settings")
                    ->insert( $dbPayload );

                $method = "insert";
            }

            /** Return JSON Object **/
            return response()->json(['status' => true, 'method' => $method]);
        }
    }

    /**  
    * Org uploadImage function
    *
    * Function to upload image for Org
    *
    * @param Request $request
    *
    * @return JSON Object
    **/
    public function uploadImage( Request $request ){

        $output = array( );

        /** Get all Request data **/
        $data = $request->all( );

        /** Check if Request is an AJAX Request **/
        if( $request->ajax( ) ){

            /** Check if Request has a file  **/
            if( $request->hasFile('file') ){

                /** Retrieve File **/
                $file = $request->file('file');

                /** Get File Extension **/
                $extension      = $file->getClientOriginalExtension();
                /** Get File Mime Type **/
                $mime_type      = $file->getClientMimeType();
                /** Get File Type **/
                $file_type      = $file->getType();
                /** Get File Size **/
                $file_size      = $file->getClientSize();
                /** Get Original File Name **/
                $orig_file_name = $file->getClientOriginalName( );


                $directory = '/images/organization';
                /** Check if directory exists, if not: create **/
                if (!is_dir(public_path('images/organization'))) {
                    @mkdir(public_path('images/organization'));
                }

                /** Get Image Information **/
                $fileData = getimagesize($file);

                /** Check if $fileData has data **/                
                if( $fileData ){
                    /** Get Width **/
                    $width = $fileData[0];
                    /** Get Height **/
                    $height = $fileData[1];
                    /** Create New File Name **/
                    $filename = md5("pwi-org-" . $orig_file_name . time( )) . "." . $extension;
                    /** Get the url to return to page **/
                    $output['url'] = asset('images/organization/' . $filename);

                    $upload;

                    /** Check what type the uploaded file is ( cover, photo, thumbnail ) **/
                    if( $data['type'] == 'cover' || $data['type'] == 'photo'){
                        //Upload
                        $upload = $this->helper->createImage( $file, array('width'=>$width, 'height'=>$height, 'crop'=>false, 'grayscale' => false), 'images/organization/' . $filename);
                        /*$upload = Image::make($file, array(
                            'width' => $width,
                            'height' => $height,
                            'crop' => false,
                            'grayscale' => false
                        ))->save('images/organization/' . $filename);*/
                    }else{
                        $upload = $this->helper->createImage( $file, array( 'width'=>300, 'height'=>300, 'crop'=>false, 'grayscale'=>false), 'images/organization/' . $filename);
                        /*$upload = Image::make($file, array(
                            'width' => 300,
                            'height' => 300,
                            'crop' => true,
                            'grayscale' => false
                        ))->save('images/organization/' . $filename);*/

                        $width  = 300;
                        $height = 300;
                    }

                    /** 
                    * check if upload comes back truthy 
                    * If true, create file data and
                    * insert/update accordingly
                    **/
                    if( $upload ){
                        /** Create File Data **/
                        $file_data = new Files( );
                        $file_data->file_orig_name  = $orig_file_name;
                        $file_data->file_extension  = $file_type;
                        $file_data->file_mime_type  = $mime_type;
                        $file_data->file_type       = "photo";
                        $file_data->file_width      = $width;
                        $file_data->file_height     = $height;
                        $file_data->file_play_time  = 0;
                        $file_data->file_size       = $file_size;
                        $file_data->file_path       = $filename;
                        $file_data->created_on      = Carbon::now( );
                        $file_data->created_by      = $data["userId"];

                        /** Check if File is saved **/
                        if( $file_data->save( ) ){
                            /** Check if data["type"] is Photo **/
                            if( $data["type"] != "photo" ){

                                /** Get Org **/
                                $org = Organizations::find( $data["id"] );
                                /** Update Org based on data type **/
                                if( $data["type"] == "cover" ){
                                    /** Update Cover Image **/
                                    $org->org_cover_image   = $file_data->file_id;
                                }else{
                                    /** Update Org Logo **/
                                    $org->org_logo          = $file_data->file_id;
                                }
                                /** Assign the status to TRUE **/
                                $output['status'] = TRUE;
                                /** Save Org **/
                                $org->save( );
                            }else{
                                /** New Instace of OrgPhotos **/
                                $orgPhoto = new OrgPhotos;

                                /** Add data to $orgPhoto **/
                                $orgPhoto->org_id           = $data["id"];
                                $orgPhoto->file_id          = $file_data->file_id;
                                $orgPhoto->org_photo_status = "Y";
                                $orgPhoto->createdatetime   = Carbon::now( );
                                $orgPhoto->sequence         = 0;

                                /** Save $orgPhoto **/
                                $orgPhoto->save( );

                                /** Set status to TRUE **/
                                $output["status"] = TRUE;
                            }
                        /** If file data does not save **/
                        }else{
                            /** Set Status to FALSE **/
                            $output["status"]   = FALSE;
                            /** set Error Msg **/
                            $output["error"]    = "There as a problem saving the file.";
                        }
                    }else{
                        /** Set Status to FALSE **/
                        $output["status"]   = FALSE;
                        /** Set Error Message **/
                        $output["error"]    = "There was an issue uploading the file.";
                    }
                }
            }
        }
        /** Return JSON Object **/
        return Response::json( $output );
    }

    /** 
    * Org removeImage Function
    * 
    * Function to remove Image from Organization
    *
    * @param Request $request
    *
    * @return JSON Object
     **/
    public function removeImage( Request $request ){

        /** Get All Data from Request Object **/
        $data = $request->all( );

        /** Update Org Photos **/
        $update = DB::table("pwi_org_photos")
                    ->where("org_id", $data["orgId"])
                    ->where("file_id", $data["fileId"])
                    ->update(["org_photo_status" => "N", "updated_at" => Carbon::now( )]);

        /** Check if Update has returned Rows **/
        if( $update > 0 ){
             /** Return JSON Object with status == TRUE **/
            return Response::json(array("status"=>true));
        }else{
            /** Return JSON Object with status == FALSE **/
            return Response::json(array("status"=>false));
        }
    }

    /** 
    * Org updateGeneralInfo Function
    *
    * Function to update Org General Info
    *
    * @param Request $request
    *
    * @return JSON object
     **/
    public function updateGeneralInfo( Request $request ){

        /** Get all data from $request variable **/
        $data = $request->all( );

        /** Get Org **/
        $org = Organizations::find( $data["orgId"] );

        /** Set Org Name & Desc **/
        $org->org_name = $data["orgName"];
        $org->org_desc = strip_tags( $data["briefDescription"] );

        $errors = 0;

        /** Initiate Output array **/
        $output = array( );

        /** Check if $org is saved **/
        if( ! $org->save( ) ){
            /** If Org Does not save set errors = 1 and add error to output array **/
            $errors = 1;
            $output["error"] = "There was an issue updating the Org Name & Brief Description.<br />";
        }

        /** Update Org Contents with About Us **/
        $orgContentAboutUs = DB::table("pwi_org_contents")
                            ->where("org_id", $data["orgId"])
                            ->where("org_content_type", "About Us")
                            ->update(['org_content_description' => strip_tags( $data["aboutUs"] ), 'org_content_updated_date' => Carbon::now( )]);

        /** Check if the above query is falsy **/
        if( ! $orgContentAboutUs ){
            /** If Falsy, set errors = 1 and set error message in output array **/
            $errors = 1;
            $output["error"] .= "There was an error updating the About Us Secion.<br />";
        }

        /** Update Org Contents with Mission Statement **/
        $orgContentMission = DB::table("pwi_org_contents")
                            ->where("org_id", $data["orgId"])
                            ->where("org_content_type", "Mission")
                            ->update(['org_content_description' => strip_tags( $data["missionStatement"] ), 'org_content_updated_date' => Carbon::now( )]);

        /** Check if the above query is falsey **/                   
        if( ! $orgContentMission ){
            /** If Falsy, set errors = 1 adn set error message in output array **/
            $errors = 1;
            $output["error"] .= "There was an error updating the Mission Statement Section.";
        }

        /** Check value of errors and set 'status' in ouput array accordingly **/
        if( $errors == 0 ){
            $output["status"] = TRUE;
        }else{
            $output["status"] = FALSE;
        }

        /** Return JSON object **/
        return Response::json( $output );
    }

    /** 
    * Org udpateCause Function
    *
    * Function to update a Cause for an organization
    *
    * @param Request $request
    *
    * @return JSON Object
     **/
    public function updateCause( Request $request ){

        /** Get all data from $request **/
        $data = $request->all( );

        /** Get Org Cause **/
        $orgCause = OrgCauses::find( $data["org_cause_id"] );

        /** Set Org Cause Description **/
        $orgCause->org_cause_description = $data["desc"];

        /** Set errors array **/
        $errors = array( );

        /** Check if Saving Org Cause saves **/
        if( ! $orgCause->save( ) ){
            /** If not, set org_cause error message **/ 
            $errors["org_cause"] = "Could not save Description";
        }

        /** Retrieve countries related to cause **/
        $causeCountries = OrgSubCauseCountries::where("org_id", $data["orgId"])
                    ->where("cause_id", $data["cause_id"])
                    ->where("org_sc_type", "country")
                    ->get( );

        /** Get all values from the above query **/
        $tableCountryValues = array_pluck($causeCountries, "org_sc_item_id");
        /** Get Values from $data["countries"]  **/
        $dataCountryValues = array_pluck($data["countries"], "id");

        /** 
            Loop through $dataCountryValues
         **/
        foreach( $dataCountryValues as $dataItem ){
            $inTable = FALSE;
            $inPayload = FALSE;

            /** check if size of return from above DB Query  is greater than 0 **/
            if( sizeof( $tableCountryValues ) > 0 ){
                //Check to see if the item from the data array is in the table
                if( in_array( $dataItem, $tableCountryValues ) ){
                    $inTable = TRUE;
                }
                /** Loop through items in table **/
                foreach( $tableCountryValues as $tableItem ){

                    /** Check to see if the current item is in the $dataCountryValues from page **/
                    if( in_array( $tableItem, $dataCountryValues) ){
                        $inPayload = TRUE;
                    }
                }
            }else{
                $inPayload = TRUE;
            }

            /** if the Item is in the table and in the $request object: update OrgSubCauseCountries **/
            if( $inTable && ! $inPayload ){
                $updatedRows = OrgSubCauseCountries::where("org_id", $data["orgId"])
                ->where("cause_id", $data["cause_id"])
                ->where("org_sc_type", "country")
                ->where("org_sc_item_id", $dataItem)
                ->where("org_cause_id", $data["org_cause_id"])
                ->update(["org_sc_status"=>"inactive"]);

                /** If no updated Rows find country
                    and set error message
                **/
                if( ! $updatedRows ){
                    $country = Country::find( $dataItem );
                    $errors["countries"] .= "Issue deleting " . $country->country_name . ".<br />";
                }
            /** If the item is in the table but not in the $data payload **/
            }else if( ! $inTable && $inPayload){

                /** Create new OrgSubCauseCountries **/
                $orgSC = new OrgSubCauseCountries;

                /** Set Values for new OrgSubCauseCountries **/
                $orgSC->org_id          = $data["orgId"];
                $orgSC->cause_id        = $data["cause_id"];
                $orgSC->org_cause_id    = $data["org_cause_id"];
                $orgSC->org_sc_type     = "country";
                $orgSC->org_sc_item_id  = $dataItem;
                $orgSC->org_sc_status   = "active";

                /** check if there is an error saving $orgSC **/
                if( ! $orgSC->save( ) ){
                    /** If Falsey, find country and throw error for output **/
                    $country = Country::find( $dataItem );
                    $errors["countries"] .= "Issue inserting " . $country->country_name . ".<br />";
                }
            }else{
                //do nothing
            }
        }

        //Get all Subcauses for the Org from pwi_org_subcausecountries
        $causeSubCauses = OrgSubCauseCountries::where("org_id", $data["orgId"])
        ->where("cause_id", $data["cause_id"])
        ->where("org_sc_type", "subcause")
        ->get( );
        
        /** Extract all subcause values **/
        $tableSubCauseValues = array_pluck($causeSubCauses, "org_sc_item_id");

        /** Loop through all sub causes from Request **/
        foreach( $data["sub_causes"] as $subcause ){
            $inTable    = FALSE;
            $inPayload  = FALSE;

            if( sizeof( $tableSubCauseValues ) > 0 ){
                //checking if this id exists in table
                if( in_array( $subcause, $tableSubCauseValues) ){
                    $inTable = TRUE;
                }

                /** Loop through values from aboveQuery **/
                foreach( $tableSubCauseValues as $tableItem ){
                    /** Check if the item from the data payload is in the tableItem **/
                    if( ! in_array( $tableItem, $data["sub_causes"] ) ){
                        /** Mark inPayLoad as TRUE **/
                        $inPayload = TRUE;
                    }
                }
            }else{
                $inPayload = TRUE;
            }

            /** If item is in the table but not in the $request payload **/
            if( $inTable && ! $inPayload ){
                /** Update Rows in database for subcauses **/
                $updatedRows = OrgSubCauseCountries::where("org_id", $data["orgId"])
                ->where("cause_id", $data["cause_id"])
                ->where("org_cause_id", $data["org_cause_id"])
                ->where("org_sc_type", "subcause")
                ->where("org_sc_item_id", $subcause)
                ->update(["org_sc_status"=>"inactive"]);

                /** If updatedRows is Falsey, set error subcause **/
                if( ! $updatedRows ){
                    $cause = Causes::find( $subcause );
                    $errors["subcauses"] .= "Issue deleting " . $cause->cause_name . ".<br />";
                }
            /** Item is in $request payload and not in table **/
            }else if( $inPayload && ! $inTable ){ 
                /** New OrgSubCauseCountries **/
                $oscc = new OrgSubCauseCountries;

                /** Set Variables for above object **/
                $oscc->org_id           = $data["orgId"];
                $oscc->cause_id         = $data["cause_id"];
                $oscc->org_cause_id     = $data["org_cause_id"]; 
                $oscc->org_sc_type      = "subcause";
                $oscc->org_sc_item_id   = $subcause;
                $oscc->org_sc_status    = "active";

                /** If above object is falsey on save **/
                if( ! $oscc->save( ) ){
                    /** Find Cause and set error message **/
                    $causes = Causes::find( $subcause );
                    $errors["subcauses"] .= "Issue inserting " . $cause->cause_name . ".<br />";
                }
            }else{
                //do nothing
            }
        }
        /** Check if size of errors return JSON Object **/
        if( sizeof( $errors ) == 0 ){
            return Response::json( array("status"=>TRUE) );
        }else{
            $errors["status"] = FALSE;
            return Response::json( $errors );
        }
    }
    /** 
    * Org addCause Function
    * 
    * Adds a new cause to the organization
    *
    * @param Request
    *
    * @return JSON Object
    **/
    public function addCause( Request $request ){

        /** Get all data from request object **/
        $data = $request->all( );

        /** Create New OrgCause **/
        $orgCause = new OrgCauses;

        /** Add Data to New OrgCause **/
        $orgCause->org_id                   = $data["orgId"];
        $orgCause->cause_id                 = $data["cause_id"];
        $orgCause->org_cause_description    = $data["desc"];
        $orgCause->org_cause_status         = "active";

        /** Check if OrgCause saves **/
        if( ! $orgCause->save( ) ){
            /** If not, return JSON Object **/
            return Response::json( array("status" => FALSE, "cause" => "error creating cause" ) );
        }

        /** Loop through all sub_causes in $data **/
        foreach( $data["sub_causes"] as $subcause ){
            /** Create New OrgSubCauseCountries for each subcause **/
            $oscc = new OrgSubCauseCountries;
            
            /** Assign all data to new OrgSubCauseCountry **/
            $oscc->org_id           = $data["orgId"];
            $oscc->cause_id         = $data["cause_id"];
            $oscc->org_cause_id     = $orgCause->org_cause_id;
            $oscc->org_sc_type      = "subcause";
            $oscc->org_sc_item_id   = $subcause;
            $oscc->org_sc_status    = "active";

            /** Save new OrgSubCauseCountry **/
            $oscc->save( );
        }
        /** Loop through all countries in $data **/
        foreach( $data["countries"] as $country ){
            /** Create new OrgSubCauseCountries for each country **/
            $oscc = new OrgSubCauseCountries;   

            /** Assign all data to new OrgSubCauseCountry **/
            $oscc->org_id           = $data["orgId"];
            $oscc->cause_id         = $data["cause_id"];
            $oscc->org_cause_id     = $orgCause->org_cause_id;
            $oscc->org_sc_type      = "country";
            $oscc->org_sc_item_id   = $country["id"];
            $oscc->org_sc_status    = "active";

            /** Save New OrgSubCauseCountry **/
            $oscc->save( );
        }

        /** Return JSON Object informing user of status **/
        return Response::json( array("status"=>TRUE, "org_cse_id" => $orgCause->org_cause_id) );
    }

    /** 
    * Org removeCause Function
    *
    * removes cause from organization
    *
    * @param Request $request 
    *
    * @return JSON Object
     **/
    public function removeCause( Request $request ){

        /** Get causeId from Input **/
        $causeId = Input::get("causeId");
        /** Get OrgId from Input **/
        $orgId   = Input::get("orgId");

        /** Setup Error Arrays **/
        $errors = array( );

        /** set orgcause to 'inactive' **/
        $updatedRow = OrgCauses::where("cause_id", $causeId)
                    ->where("org_id", $orgId)
                    ->update(["org_cause_status" => "inactive"]);

        /** Check if rows have been updated **/            
        if( ! $updatedRow ){
            /** If fails, return JSON object informing user **/
            return Response::json(array( "status" => FALSE, "cause" => "Issue deleting Cause.") );
        }

        /** 
        * Set all subcause and countries to 'inactive' where the 
        * orgId and causeId equate to the cause and org 
        * id that are passed into the function
         **/
        $oscc = OrgSubCauseCountries::where("org_id", $orgId)
                ->where("cause_id", $causeId)
                ->update(["org_sc_status" => "inactive"]);

        /** Return JSON Object informing user  **/
        return Response::json( array( "status" => TRUE ) );
    }

    /** 
    * Org updateContactInfo function
    * 
    * function updates the contact info of organization
    *
    * @param Request
    *
    * @return JSON object
     **/
    public function updateContactInfo( Request $request ){

        /** Set validation array **/
        $validationList = array( 
            "org_email"     => "required|email",
        );

        /** Set messages for above Validation List **/
        $messages = array( 
            "org_web_url.url"       => "Must be a properly formatted URL.",
            "org_email.required"    => "The Org Email is Required.",
            "org_email.email"       => "The Email is not properly formatted.",
            "org_state.exists"      => "Invalid State.",
            "org_country.exists"    => "Invalid Country."
        );

        /** create validator object **/
        $validator = \Validator::make($request->all(), $validationList, $messages);

        /** 
        * Conditional Validation Check : org web url
        * - Add org_web_url to validation if org_web_url is not empty
        **/
        $validator->sometimes("org_web_url", "url", function( $input ){
            return $input->org_web_url != "";
        });

        /** 
        * Conditional Validation Check : org state
        * - Add org_state to validation if org_state is greater than 0
         **/
        $validator->sometimes("org_state", "exists:pwi_state,state_id", function( $input){
            return $input->org_state > 0;
        });

        /**  
        * Conditional Validation Check : org country
        * - Add org_country to validation if org_country is greater than 
        * 0
        **/
        $validator->sometimes("org_country", "exists:pwi_country,country_id", function( $input){
            return $input->org_country > 0;
        });

        /** Check if validation fails **/
        if( $validator->fails( ) ){
            /** Send back JSON Object to user **/
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }else{
            /** Get all data from request object **/
            $data = $request->all( );

            /** Retrieve all Organization information **/
            $org = Organizations::find( $data["orgId"] );
            
            /** Set data for Organization **/
            $org->org_email         = $data["org_email"];
            $org->org_weburl        = $data["org_web_url"];
            $org->org_mobile_number = $data["org_phone"];
            $org->org_addressline1  = $data["org_address1"];
            $org->org_addressline2  = $data["org_address2"];
            $org->org_city          = $data["org_city"];
            $org->org_state         = $data["org_state"];
            $org->org_zip           = $data["org_zip"];
            $org->org_country       = $data["org_country"];

            /** Check if Org is saved correctly **/
            if( ! $org->save( ) ){
                /** Return JSON Object informing user **/
                return response( )->json(['status' => false, 'error' => "There was a problem updating the Organization"]);
            }else{
                /** Org was saved correctly **/
                $stateAbbr = "";
                $countryAbbr = "";

                /** Get State Abbreviation **/
                if( $data["org_state"] > 0 ){
                    $state = States::find($data["org_state"]);    
                    $stateAbbr = $state->state_code;
                }
                
                /** Get Country Abbreviation **/
                if( $data["org_country"] > 0 ){
                    $country = Country::find( $data["org_country"] );
                    $countryAbbr = $country->country_iso_code;
                }

                /** 
                * Return JSON Object with status and 
                * state/country abbreviation  
                **/
                return response( )->json(['status' => true, 'state' => $stateAbbr, 'country' => $countryAbbr] ); 
            }
        }
    }

    /** 
    * Org saveVideo Function
    * 
    * Function saves video that is added on front end
    *
    * @param Request
    *
    * @return JSON Object
     **/
    public function saveVideo( Request $request ){

        /** Get all data from Requst Object **/
        $data = $request->all( );

        /** Retrieve Video Url from $data array **/
        $videoUrl = $data["videoUrl"];
        $videoType = "";

        /** Set the YouTube/Vimeo RegEx Patterns **/
        $youtubePattern = "/youtube/";
        $vimeoPattern   = "/vimeo/";

        $videoImg = "";
        $videoLink = "";

        /** Call getVideoData from Helper Class **/
        $videoData = $this->helper->getVideoData( $videoUrl );

        /** See if the Key 'status' exists in videoData **/
        if( array_key_exists("status", $videoData ) ){
            /** 
            * Convert videoData to JSON Object & return to user 
            **/
            return Response::json( $videoData );
        }else{
            /** Get Video Thumbnail **/
            $videoImg   = $videoData[0];
            /** Get Video Link **/
            $videoLink  = $videoData[1];
        }
        /** Check if video link is 'falsey' **/
        if( ! $videoLink ){
            /** Return JSON Object informing user videoLink errored **/
            return Response::json(array("status"=>false, "msg"=>"Could not parse video"));   
        }

        /** Create new Video Object **/
        $videoRecord = new Videos;

        /** Set new Video Data **/
        $videoRecord->org_id            = $data["orgId"];
        $videoRecord->video_url         = $videoLink;
        $videoRecord->video_id          = $videoImg;
        $videoRecord->org_video_status  = "Y";
        $videoRecord->createdatetime    = Carbon::now( );
        $videoRecord->sequence          = 0;

        /** Check if Video Saves  **/
        if( $videoRecord->save( ) ){
            /** If true, send back status and img link **/
            return Response::json(array("status"=>true, "img" => $videoImg) );
        }else{
            /** Otherwise, send back false status and error **/
            return Response::json(array("status"=>false, "msg"=>"There was an error saving your video."));
        }
    }

    /**  
    * Org removeVideo Function
    * 
    * Function to remove video from Organization
    *
    * @param Request 
    *
    * @return JSON Object
    **/
    public function removeVideo( Request $request ){

        /** Get all data from Request Object **/
        $data = $request->all( );

        /** Update org Videos - set status and updated_at **/
        $update = DB::table("pwi_org_videos")
                    ->where("org_video_id", $data["orgVideoId"])
                    ->update(["org_video_status" => "N", "updated_at" => Carbon::now( )]);

        /** Check if udpate worked **/
        if( $update > 0 ){
            /** Return TRUE **/
            return Response::json(array("status"=>true));
        }else{
            /** Return FALSE **/
            return Response::json(array("status"=>false));
        }
    }

    /** 
    * Org addProject Function
    *
    * function add a Project to Organization
    *
    * @param Request
    *
    * @return JSON Object
     **/
    public function addProject( Request $request ){

        /** Get all data from Requst Object **/
        $data = $request->all( );

        /** Set up thumbnailId **/
        $thumbnailId    = 0;
        /** Set up headerID **/
        $headerId       = 0;

        /** Set up validation Array **/
        $validationList = array( 
            "name"     => "required",
            "end_date" => "required|date_format:mdY|after:today",
            "goal"     => "required|integer"
        );

        /** set up validation messages **/
        $messages = array( 
            "name.required"         => "Please Enter a Project Name",
            "end_date.required"     => "The End Date for the Project is Required.",
            "end_date.date_format"  => "Improper Date.",
            "end_date.after"        => "The End Date must be AFTER today.",
            "goal.required"         => "The Project Goal is required.",
            "goal.integer"          => "The Project Goal must be a number."
        );

        /** Validate input **/
        $validator = \Validator::make($request->all(), $validationList, $messages);

        /** Check to see if validator fails **/
        if( $validator->fails( ) ){
            /** If so, send back JSON Object indicating failure with errors **/
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }

        /** Check to see if request is an AJAX Call **/
        if( $request->ajax( ) ){

            /** Check to see if the request object has a file called 'thumbnail' **/
            if( $request->hasFile('thumbnail') ){

                /** Call saveFile for thumbnail **/
                $fileSaveData = $this->helper->saveFile( $request->file('thumbnail'), 'projects', 300, 300, $data["userId"]);

                /** Check if the return type is INT **/
                if( is_int( $fileSaveData) ){
                    /** If INT, set thumbnailId to fileSaveData **/
                    $thumbnailId = $fileSaveData;
                }else{
                    /** Set Output array to fileSaveData **/
                    $output = $fileSaveData;
                }
            }

            /** Check to see if the request object has a file called 'header' **/
            if( $request->hasFile('header') ){

                /** Call save file for header **/
                $fileSaveData = $this->helper->saveFile( $request->file('header'), 'projects', 0, 0, $data["userId"]);

                /** Check if return type is INT **/
                if( is_int( $fileSaveData ) ){
                    /** If INT, set headerId to fileSaveData **/
                    $headerId = $fileSaveData;
                }else{
                    /** Set output array to fileSaveData **/
                    $output = $fileSaveData;
                }
            }

            /** Get Video Link from data **/
            $videoOrigUrl   = $data["video"];
            $videoLink      = "";

            /** Check if video is empty **/
            if( ! empty( $data["video"] ) ){

                /** Get Video data from getVideoData in Helper Class **/
                $videoData = $this->helper->getVideoData( $data["video"] );

                /** Check $videoData contains 'status' key **/
                if( array_key_exists("status", $videoData ) ){
                    /** Return JSON Object with video data **/
                    return Response::json( $videoData );
                }else{
                    /** Set video link to returned link **/
                    $videoLink  = $videoData[1];
                }

                /** check if videoLink is falsey **/
                if( ! $videoLink ){
                    /** return error of not being able to parse video data **/
                    return Response::json(array("status"=>false, "msg"=>"Could not parse video"));   
                }
            }

            /** Set up new Project **/
            $project = new Projects;

            /** Set up project data **/
            $project->org_id                    = $data["orgId"];
            $project->project_title             = $data["name"];
            $project->project_alias             = $this->helper->generateAlias( "pwi_projects", $data["name"], "", "", "project_alias");
            $project->project_start_date        = Carbon::now( );
            $project->project_end_date          = Carbon::createFromDate($data["year"], $data["month"], $data["day"]);
            $project->project_icon              = $thumbnailId;
            $project->project_header            = $headerId;
            $project->project_video_url         = $videoLink;
            $project->project_orig_video_url    = $videoOrigUrl;
            $project->project_story             = $data["story"];
            $project->project_fund_goal         = $data["goal"];
            $project->project_fund_type         = "independent";
            $project->project_payment_type      = "gateway";
            $project->project_status            = "active";
            $project->project_featured          = "Y";
            $project->featured_order            = 0;
            $project->project_amout_raised      = 0;
            $project->project_viewcount         = 0;
            $project->project_created_date      = Carbon::now( );

            /** Check if project is saved properly **/
            if( $project->save( ) ){

                /** Check there are project causes **/
                if( isset( $data["projectCause"] ) ){
                    /** If so, loop through causes **/
                    foreach( $data["projectCause"] as $cause ){

                        $projectCauseDetail;
                        $causeId = "";
                        
                        /** Find all sub causes and countries where cause_id equals this cause **/
                        $items = OrgSubCauseCountries::where("org_cause_id", $cause)
                                ->select("org_sc_type", "org_sc_item_id", "cause_id")
                                ->get( );

                        /** Loop through all retrieved items **/
                        foreach( $items as $item ){
                            /** Check if causeId is empty **/
                            if( empty( $causeId ) ){
                                /** If it is a cause has not been processed yet - Process Cause **/
                                $causeId = $item["cause_id"];

                                /** New Project Cause Detail - Cause **/
                                $projectCauseDetail = new ProjectCauseDetails( array("org_id"=>$data["orgId"], "project_id"=>$project->project_id, "project_cause_type"=>"cause", "project_cause_item_id"=>$causeId, "project_cause_status"=>"active") );
                            }else{
                                /** Else use each items types and insert into ProjectCause Detail **/
                                $projectCauseDetail = new ProjectCauseDetails( array("org_id"=>$data["orgId"], "project_id"=>$project->project_id, "project_cause_type"=>$item["org_sc_type"], "project_cause_item_id"=>$item["org_sc_item_id"], "project_cause_status"=>"active") );
                            }

                            /** Save Project Cause Detail **/
                            $projectCauseDetail->save( );
                        }
                    }    
                }
                
                /** Check to see $data has Incentives  **/
                if( isset( $data["projectIncentiveName"] ) ){
                    
                    /** Loop through all Incentives **/
                    for( $i = 0 ; $i < sizeof( $data["projectIncentiveName"] ) ; $i++ ){

                        /** Set default shipping = 'N' **/
                        $shipping = "N";

                        /** Check to see if this incentive needs shipping **/
                        if( $data["projectIncentiveShipping"][$i] == 1 ){
                            /** If so, set shipping to 'Y' **/
                            $shipping = "Y";
                        }

                        $newIncentive;

                        /** Set up new incentive array **/
                        $incentive = array( 
                        "project_id"                            =>$project->project_id, 
                        "project_incentive_title"               =>$data["projectIncentiveName"][$i], 
                        "project_incentive_description"         =>$data["projectIncentiveDesc"][$i],
                        "project_incentive_amount"              =>$data["projectIncentiveAmt"][$i],
                        "project_incentive_estdelivery_date"    => Carbon::now( ),
                        "project_available_incentive_count"     =>$data["projectIncentiveNumber"][$i],
                        "project_donor_shipping_address"        =>$shipping,
                        "project_incentive_status"              =>"active",
                        "project_incentive_purchasedcount"      => 0); 

                        /** Create new Incentive **/
                        $newIncentive = new ProjectIncentives( $incentive );

                        /** Save new incentive **/
                        $newIncentive->save( );
                    }
                }
                /** Return JSON Object indicating success **/
                return Response::json( array("status"=>true) );
            }else{
                /** Return JSON object indicating failure **/
                return Response::json( array("status"=>false, "msg"=>"Could not create Project") );
            }
        }
    }/** End addProject **/


    /** 
    * Org updateProject Function
    *
    * updates a project for an organization
    *
    * @param Request
    *
    * @return JSON Object
     **/
    public function updateProject( Request $request ){

        //DB::connection( )->enableQueryLog( );

        /** get data from request object **/
        $data = $request->all( );

        /** Set up thumbnail Id **/
        $thumbnailId    = 0;
        /** Set up header Id **/
        $headerId       = 0;

        /** Set up validation list **/
        $validationList = array( 
            "name"     => "required",
            "end_date" => "required|date_format:mdY|after:today",
            "goal"     => "required|integer"
        );

        /** Set up validation messages **/
        $messages = array( 
            "name.required"         => "Please Enter a Project Name",
            "end_date.required"     => "The End Date for the Project is Required.",
            "end_date.date_format"  => "Improper Date.",
            "end_date.after"        => "The End Date must be AFTER today.",
            "goal.required"         => "The Project Goal is required.",
            "goal.integer"          => "The Project Goal must be a number."
        );

        /** Create validator object **/
        $validator = \Validator::make($request->all(), $validationList, $messages);

        /** Check to see if validator fails **/
        if( $validator->fails( ) ){
            /** If so return JSON object with errors **/
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }

        /** Check to see if request type is AJAX **/
        if( $request->ajax( ) ){

            /** Get project Id from $data **/
            $projectId = $data["id"];

            /** Get intended project **/
            $project = Projects::find( $projectId );

            /** Get all project causes **/
            $projectCauses = $project->causes;

            /** Get all project incentives **/
            $projectIncentives = $project->incentives;

            if( $request->hasFile('thumbnail') ){

                /** Call save file for header **/
                $fileSaveData = $this->helper->saveFile( $request->file('thumbnail'), 'projects', 300, 300, $data["userId"]);

                /** Check if return type is INT **/
                if( is_int( $fileSaveData ) ){
                    /** If INT, set headerId to fileSaveData **/
                    $thumbnailId = $fileSaveData;
                }else{
                    /** Set output array to fileSaveData **/
                    $output = $fileSaveData;
                }
            }

            if( $request->hasFile('header') ){

                /** Call save file for header **/
                $fileSaveData = $this->helper->saveFile( $request->file('header'), 'projects', 0, 0, $data["userId"]);

                /** Check if return type is INT **/
                if( is_int( $fileSaveData ) ){
                    /** If INT, set headerId to fileSaveData **/
                    $headerId = $fileSaveData;
                }else{
                    /** Set output array to fileSaveData **/
                    $output = $fileSaveData;
                }
            }

            /** Set project title **/
            $project->project_title             = $data["name"];

            /** Set project end date **/
            $project->project_end_date          = Carbon::createFromDate($data["year"], $data["month"], $data["day"]);
            
            /** Check if thumnailId still equals 0 **/
            if( $thumbnailId != 0 ){
                /** If not, set thumbnailId **/
                $project->project_icon = $thumbnailId;
            }

            /** Check if headerid still equals 0 **/
            if( $headerId != 0 ){
                /** If not, set headerId **/
                $project->project_header = $headerId;
            }

            /** Check to see old video url equals the new video url **/
            if( $project->project_orig_video_url != $data["video"] ){

                /** If not check if empty video **/
                if( empty( $data["video"] ) ){
                    /** If so, set project video data to "" **/
                    $project->project_video_url         = "";
                    $project->project_orig_video_url    = "";
                }else{
                    /** Set video url **/
                    $videoOrigUrl   = $data["video"];
                    $videoLink       = "";

                    /** Get video data from getVideoData in Helper Class **/
                    $videoData = $this->helper->getVideoData( $data["video"] );

                    /** Check to see if the key 'status' is in videoData **/
                    if( array_key_exists("status", $videoData ) ){
                        /** If so send back video data error **/
                        return Response::json( $videoData );
                    }else{
                        /** Otherwise, set the videoLink **/
                        $videoLink  = $videoData[1];
                    }

                    /** Check if VideoLink is falsey,  **/
                    if( ! $videoLink ){
                        /** Return JSON Object indicating Video could not be parsed **/
                        return Response::json(array("status"=>false, "msg"=>"Could not parse video"));   
                    }
                    
                    /** Set video data **/
                    $project->project_video_url         = $videoLink;
                    $project->project_orig_video_url    = $data["video"];
                }
            }
            /** Set project story **/
            $project->project_story             = $data["story"];
            $project->project_fund_goal         = $data["goal"];
            
            /** Check if project data is savd **/
            if( $project->save( ) ){

                /** Check if any project Cause data is passed **/
                if( isset( $data["projectCause"] ) ){

                    /** Loop through project cause data **/
                    foreach( $data["projectCause"] as $cause ){

                        $projectCauseDetail;
                        $causeId = "";
                        
                        /** Get all sub causes and countries from org_cause_id **/
                        $items = OrgSubCauseCountries::where("org_cause_id", $cause)
                                ->select("org_sc_type", "org_sc_item_id", "cause_id")
                                ->get( );

                        /** Loop through each items **/
                        foreach( $items as $item ){

                            /** Check if CauseId is empty **/
                            if( empty( $causeId ) ){

                                /** set Cause Id to item[cause_id] **/
                                $causeId = $item["cause_id"];

                                /** 
                                * Get a count of ProjectCauseDetails where: 
                                * project_id = this project;
                                * project_cause_type = 'cause'
                                * project_cause_item_id = cause_id
                                **/
                                $count = ProjectCauseDetails::where("org_id", $data["orgId"] )
                                ->where("project_id", $data["id"])
                                ->where("project_cause_type", "cause")
                                ->where("project_cause_item_id", $causeId)
                                ->count( );

                                /** If no records returned Cause does not exist **/
                                if( $count == 0 ){
                                    /** Create new ProjectCauseDetail **/
                                    $projectCauseDetail = new ProjectCauseDetails( array("org_id"=>$data["orgId"], "project_id"=>$project->project_id, "project_cause_type"=>"cause", "project_cause_item_id"=>$causeId, "project_cause_status"=>"active") );

                                    /** Save projectCauseDetail **/
                                    $projectCauseDetail->save( );
                                }
                            /** Process countries/sub causes **/
                            }else{
                                /** 
                                * Get count of ProjectCauseDetails where: 
                                * project_id = this project
                                * project_cause_type = 'country or subcause'
                                * project_cause_item_id = this item id
                                **/
                                $count = ProjectCauseDetails::where("org_id", $data["orgId"] )
                                ->where("project_id", $data["id"])
                                ->where("project_cause_type", $item["org_sc_type"])
                                ->where("project_cause_item_id", $item["org_sc_item_id"])
                                ->count( );

                                /** If no records returned Subcause/Country does not exist **/
                                if( $count == 0 ){
                                    /** Create new ProjectCauseDetail row **/
                                    $projectCauseDetail = new ProjectCauseDetails( array("org_id"=>$data["orgId"], "project_id"=>$project->project_id, "project_cause_type"=>$item["org_sc_type"], "project_cause_item_id"=>$item["org_sc_item_id"], "project_cause_status"=>"active") );

                                    /** Save projectCauseDetail **/
                                    $projectCauseDetail->save( );
                                }
                            }
                        }
                    }    

                    /** Get all project causes for this project **/
                    $projectCauses = ProjectCauseDetails::where("project_id", $data["id"])
                        ->where("project_cause_type", "cause")
                        ->groupBy( "project_cause_item_id" )
                        ->get( );

                    /** Get active project subcauses & countries for this project **/
                    $activeProjectCauses = OrgSubCauseCountries::whereIn("org_cause_id", $data["projectCause"] )
                        ->select("cause_id")
                        ->groupBy("cause_id")
                        ->get( );

                    $projectCauseIds = array( );

                    /** 
                    * Loop through all activeProjectCauses 
                    * & add value to projectCauseIds Array
                    **/
                    foreach( $activeProjectCauses as $activeProjectCause ){
                        $projectCauseIds[] = $activeProjectCause["cause_id"];
                    }

                    /** Loop through all project Causes **/
                    foreach( $projectCauses as $projectCause ){

                        /** See if this item is in the projectCauseIds Array **/
                        if( ! in_array( $projectCause["project_cause_item_id"], $projectCauseIds ) ){

                            /** Cause has been removed **/

                            /** 
                            * If not get all subcause/countries where: 
                            * cause_id = this projectCause["project_cause_item_id"]
                            * org_id = this OrgId
                            **/
                            $items = OrgSubCauseCountries::where("cause_id", $projectCause["project_cause_item_id"])
                                ->where("org_id", $data["orgId"])
                                ->select("org_sc_type", "org_sc_item_id", "cause_id")
                                ->get( );

                            /** 
                            * Get the ProjectCauseDetails where: 
                            * project_id = this project's id
                            * org_id = this project's org id
                            * project_cause_item_id = projectCause[project_cause_item_id]
                            **/
                            ProjectCauseDetails::where("project_id", $data["id"])
                            ->where("org_id", $data["orgId"])
                            ->where("project_cause_type", "cause")
                            ->where("project_cause_item_id", $projectCause["project_cause_item_id"])
                            ->delete( );

                            /** Loop through each item and remove associated item **/
                            foreach( $items as $item ){
                                ProjectCauseDetails::where("project_id", $data["id"])
                                ->where("org_id", $data["orgId"])
                                ->where("project_cause_type", $item["org_sc_type"])
                                ->where("project_cause_item_id", $item["org_sc_item_id"])
                                ->delete( );
                            }
                        }else{
                            //echo $projectCause["project_cause_item_id"] . " in array.<br />";
                        }
                    }
                }

                /** see if an project incentives have been passed in **/
                if( isset( $data["projectIncentiveName"] ) ){
                    
                    /** loop through each incentive and add **/
                    for( $i = 0 ; $i < sizeof( $data["projectIncentiveName"] ) ; $i++ ){

                        $incentiveCnt = 0;

                        /** See if this project incentive exists **/
                        if( isset( $data["projectIncentiveId"][$i] ) ){

                            /** Find project incentive **/
                            $incentiveCheck = ProjectIncentives::find( $data["projectIncentiveId"][$i] );

                            /** Check if incentiveCheck return NULL **/
                            if( ! is_null( $incentiveCheck ) ){
                                $incentiveCnt = 1;
                            }    
                            
                            /** Incentive does not exist : create **/
                            if( $incentiveCnt == 0 ){
                                /** Initiate shiping to 'N' **/
                                $shipping = "N";

                                /** If this incentive shipping == 1 **/
                                if( $data["projectIncentiveShipping"][$i] == 1 ){
                                    /** set shipping to 'Y' **/
                                    $shipping = "Y";
                                }

                                $newIncentive;

                                /** Create new incentive data **/
                                $incentive = array( 
                                "project_id"                            =>$project->project_id, 
                                "project_incentive_title"               =>$data["projectIncentiveName"][$i], 
                                "project_incentive_description"         =>$data["projectIncentiveDesc"][$i],
                                "project_incentive_amount"              =>$data["projectIncentiveAmt"][$i],
                                "project_incentive_estdelivery_date"    => Carbon::now( ),
                                "project_available_incentive_count"     =>$data["projectIncentiveNumber"][$i],
                                "project_donor_shipping_address"        =>$shipping,
                                "project_incentive_status"              =>"active",
                                "project_incentive_purchasedcount"      => 0); 

                                /** Create new Project Incentive **/
                                $newIncentive = new ProjectIncentives( $incentive );

                                /** Save new incentive **/
                                $newIncentive->save( );

                            /** Incentive does exist update **/
                            }else{

                                /** Find incentive **/
                                $incentive = ProjectIncentives::find( $data["projectIncentiveId"][$i] );

                                /** Set shipping = 'N' **/
                                $shipping = "N";

                                /** see if incentive shipping == 1 **/
                                if( $data["projectIncentiveShipping"][$i] == 1 ){
                                    /** set shipping = 'Y' **/
                                    $shipping = "Y";
                                }

                                /** Update Incentive Data **/
                                $incentive->project_incentive_title                     = $data["projectIncentiveName"][$i];
                                $incentive->project_incentive_description               = $data["projectIncentiveDesc"][$i];
                                $incentive->project_incentive_amount                    = $data["projectIncentiveAmt"][$i];
                                $incentive->project_available_incentive_count           = $data["projectIncentiveNumber"][$i];
                                $incentive->project_donor_shipping_address              = $shipping;

                                /** Save Incentive Data **/
                                $incentive->save( );
                            }
                        }
                    }

                    /** Get projects Incentives from DB for this Project **/
                    $projectIncentiveList = ProjectIncentives::where("project_id", $data["id"] )->get( );

                    /** Loop through each Incentive List **/
                    foreach( $projectIncentiveList as $projectIncentiveListItem ){

                        $found = FALSE;

                        /** Loop through each project Incentive from Request **/
                        for( $i = 0 ; $i < sizeof( $data["projectIncentiveId"] ) ; $i++ ){

                            /** Check if this incentive id is in the projectIncentiveList **/
                            if( $projectIncentiveListItem["project_incentive_id"] == $data["projectIncentiveId"][$i] ){
                                /** Mark incentive as found **/
                                $found = TRUE; 
                            }
                        }
                        /** Check if found **/
                        if( ! $found ){
                            /** Set incentive to inactive **/
                            ProjectIncentives::where("project_incentive_id", $projectIncentiveListItem["project_incentive_id"])->update(array("project_incentive_status"=>"inactive"));
                        }
                    }
                }
                
                /** See if any Project Updates exist **/
                if( isset( $data["projectUpdateTitle"] ) ){

                    /** set array for new update rows **/
                    $newUpdateRows = array( );

                    /** Loop through Updates **/
                    for( $i = 0 ; $i < sizeof( $data["projectUpdateTitle"] ) ; $i++ ){

                        $updateCnt = 0;

                        /** Try and find update **/
                        $updateCheck = ProjectUpdates::find( $data["projectUpdateId"][$i] );

                        /** Check if $updateCheck is null **/
                        if( ! is_null( $updateCheck) ){
                            $updateCnt = 1;
                        }

                        /** Check if updateCnt is greater than 0 **/
                        if( $updateCnt > 0 ){

                            /** Update Project Update **/
                            $thisUpdate = ProjectUpdates::find( $data["projectUpdateId"][$i]);

                            $thisUpdate->title = $data["projectUpdateTitle"][$i];
                            $thisUpdate->description = $data["projectUpdate"][$i];

                            /** Save Update **/
                            $thisUpdate->save( );
                        }else{

                            /** Create new Upate **/
                            $newUpdate = new ProjectUpdates( array("project_id"=>$data["id"], "title"=>$data["projectUpdateTitle"][$i], "description"=>$data["projectUpdate"][$i]));

                            /** Save update **/
                            $newUpdate->save( );

                            /** Add update id to updaterow **/
                            $newUpdateRows[] = $newUpdate->project_update_id;
                        }
                    }

                    /** Get current project updates **/
                    $projectUpdateList = ProjectUpdates::where('project_id', $data["id"])->get( );

                    /** Loop throuugh projectUpdates **/
                    foreach( $projectUpdateList as $projectUpdatesListItem ){

                        $found = FALSE;

                        /** Loop through updates from Request **/
                        for( $i = 0 ; $i < sizeof( $data["projectUpdateId"] ) ; $i++ ){

                            /** Check if projectUpdate already exists **/
                            if( ( $projectUpdatesListItem["project_update_id"] == $data["projectUpdateId"][$i] ) || ( in_array($projectUpdatesListItem["project_update_id"], $newUpdateRows) ) ){
                                /** Mark as found **/
                                $found = TRUE; 
                            }
                        }

                        /** If not found, remove update **/
                        if( ! $found ){
                            ProjectUpdates::where("project_update_id", $projectUpdatesListItem["project_update_id"])->delete( );
                        }
                    }
                }
                /** Send JSON Object back indicating success **/
                return Response::json( array( "status" => true ) );

            }else{
                /** Send JSON Object back indicating failure **/
                return Response::json( array("status"=>false, "msg"=>"Could not update Project") );
            }
        }
    }/** end updateProject **/

    /** 
    * Org deleteProject function
    *
    * function for deleting an organization's project
    *
    * @param Request
    *
    * @return JSON Object
     **/
    public function deleteProject( Request $request ){

        /** Get Project Id **/
        $projectId  = Input::get("id");
        /** Get Org Id **/
        $orgId      = Input::get("orgId");

        /** Destroy Project **/
        Projects::destroy($projectId);

        $updateIds = array( );

        /** Get all Project Updates **/
        $updates = ProjectUpdates::where("project_id", $projectId)->get( );

        /** Loop through each update and extract Ids **/
        foreach( $updates as $update ){
            $updateIds[] = $update["project_udpate_id"];
        }

        /** Destroy All Project Updates **/
        ProjectUpdates::destroy( $updateIds );

        $incentiveIds = array( );

        /** Find all Incentive Ids **/
        $incentives = ProjectIncentives::where("project_id", $projectId)->get( );

        /** Loop through all incentives and add incentive_id to $incentiveIds **/
        foreach( $incentives as $incentive ){
            $incentiveIds[] = $incentive["project_incentive_id"];
        }

        /** Destroy All Project Incentives **/
        ProjectIncentives::destroy( $incentiveIds );

        $causeDetailIds = array( );

        /** Get all Project Cause Details for org & project **/
        $causeDetailList = ProjectCauseDetails::where("project_id", $projectId)
                            ->where("org_id", $orgId)
                            ->get( );

        /** Loop through all causeDetails and get project_cause_id **/
        foreach( $causeDetailList as $causeDetailListItem ){
            $causeDetailIds[] = $causeDetailListItem["project_cause_id"];
        }

        /** Destroy all ProjectCauseDetails for this project **/
        ProjectCauseDetails::destroy( $causeDetailIds );

        /** Return JSON Object for success **/
        return Response::json(array("status"=>true) );
    }

    /**
    * Org saveSocialMedia Function
    *
    * Function to save social Media Data for ORG
    *
    * @param Request
    *
    * @return JSON Object
    *
    **/
    public function saveSocialMedia( Request $request ){

        /** Get all data from Request Object **/
        $data = $request->all( );

        /** Get all socialMedia Ids **/
        $socialMediaIds = DB::table("pwi_social_media")->get( );

        /** get OrgId from data **/
        $orgId = $data["orgId"];

        /** Loop through all social media Ids **/
        foreach( $socialMediaIds as $socialMediaItem ){

            $fullUrl = "";
            $pageId  = "";
            /** Evaluate each social media name and see get Id and data  **/
            switch( strtolower( $socialMediaItem->social_media_name ) ){
                case "facebook":
                    $fullUrl = "https://www.facebook.com/" . $data["facebook"];
                    $pageId  = $data["facebook"];
                break;
                case "twitter":
                    $fullUrl = "https://www.twitter.com/" . $data["twitter"];
                    $pageId  = $data["twitter"];
                break;
                case "instagram":
                    $fullUrl = "https://www.instagram.com/" . $data["instagram"];
                    $pageId  = $data["instagram"];
                break;
                case "pinterest":
                    $fullUrl = "https://www.pinterest.com/" . $data["pinterest"];
                    $pageId  = $data["pinterest"];
                break;
            }

            /** Check if row exists **/
            $rowCheck = SocialMedia::where("org_id", $orgId)
                    ->where("social_media_id", $socialMediaItem->social_media_id )
                    ->get( );

            /** Check if pageId is empty  **/
            if( ! empty( $pageId ) ){

                /** Check if rowCheck has returned Rows **/
                if( sizeof( $rowCheck ) > 0 ){

                    /** Check if Row Status is 'N' **/
                    if( $rowCheck[0]->org_sm_status == "N" ){
                        /** Update only row status **/
                        $update = SocialMedia::where("org_id", $orgId)
                                ->where("social_media_id", $socialMediaItem->social_media_id )
                                ->update( array( "org_sm_status"=>"Y" ) );
                    }else{
                        /** Otherwise, update url and page id **/
                        $update = SocialMedia::where("org_id", $orgId)
                                ->where("social_media_id", $socialMediaItem->social_media_id )
                                ->where("org_sm_status", "Y")
                                ->update( array( "org_sm_url"=>$fullUrl, "org_sm_pageid"=>$pageId ) );    
                    }
                /** Create New Social Media row **/
                }else{
                    /** Create new Row **/
                    $row = new SocialMedia;

                    /** Add Data to new row **/
                    $row->org_id                = $orgId;
                    $row->org_sm_url            = $fullUrl;
                    $row->org_sm_pageid         = $pageId;
                    $row->social_media_id       = $socialMediaItem->social_media_id; 
                    $row->org_sm_status         = "Y";
                    $row->org_sm_created_date   = Carbon::now( );

                    /** Save Row **/
                    $row->save( );
                }
            }else{
                /** Set status = 'n' **/
                if( sizeof( $rowCheck ) > 0 ){
                    $update = SocialMedia::where("org_id", $orgId)
                    ->where("social_media_id", $socialMediaItem->social_media_id )
                    ->where("org_sm_status", "Y")
                    ->update( array( "org_sm_status"=>"N" ) );
                } 
            }
        }
        /** Return update status **/
        return Response::json(array("status"=>true) );
    }

    public function addProduct( Request $request ){

        $data = $request->all( );

        $fedexEnabled = "N";
        $uspsEnabled  = "N";
        $upsEnabled   = "N";

        foreach( $data["shippingMethod"] as $sMethod ){
            if( $sMethod == "fedex" ){
                $fedexEnabled = "Y";
            }else if( $sMethod == "usps" ){
                $uspsEnabled = "Y";
            }else if( $sMethod == "ups" ){
                $upsEnabled = "Y";
            }
        }
        
        $productTableData = array(
            "org_id"                => $data["orgId"],
            "product_type"          => "physical",
            "product_name"          => $data["productName"],
            "product_alias"         => $this->helper->generateAlias("pwi_products", $data["productName"], "", "", "product_alias"),
            "product_sku"           => $data["productSKU"],
            "product_regular_price" => 0,
            "product_sales_price"   => $data["productPrice"],
            "product_display_price" => 0,
            "product_short_desc"    => $data["productShortDesc"],
            "product_full_desc"     => $data["productLongDesc"],
            "product_image_id"      => 0,
            "product_quantity"      => $data["productQuantity"],
            "product_shipping_fee"  => $data["shipping_fee"],
            "product_shipping_time" => $data["shipping_time"],
            "product_weight"        => $data["weight"],
            "product_width"         => $data["width"],
            "product_height"        => $data["height"],
            "product_length"        => $data["length"],
            "product_status"        => "active",
            "product_viewcount"     => 0,
            "product_featured"      => "Y",
            "product_created_date"  => Carbon::now( ),
            "fedex_enabled"         => $fedexEnabled,
            "usps_enabled"          => $uspsEnabled,
            "ups_enabled"           => $upsEnabled
        );

        $newProduct = new Products( $productTableData );

        if( $newProduct->save( ) ){
            $productId = $newProduct->product_id;

            $isFirst = TRUE;
            
            if( isset( $data["photos"] ) ){
                /** Loop through Photos  **/
                foreach( $data["photos"] as $photo ){
                    
                    /** Call save file for header **/
                    $fileSaveData = $this->helper->saveFile( $photo, 'products', 0, 0, $data["userId"]);

                    /** Check if return type is INT **/
                    if( is_int( $fileSaveData ) ){
                        /** If INT, set headerId to fileSaveData **/
                        $product = Products::find( $productId );
                        if( $isFirst ){
                            $product->product_image_id = $fileSaveData;
                            $product->save( );
                            $isFirst = FALSE;
                        }

                        $productImage = new ProductImages( array( "product_id" => $productId, "file_id" => $fileSaveData, "product_image_status" => "Y") );

                        $productImage->save( );
                        
                    }else{
                        /** Set output array to fileSaveData **/
                        $output = $fileSaveData;
                    }
                }
            }

            if( isset( $data["orgCause"] ) ){
                /** Loop through Causes **/
                for( $i = 0 ; $i < sizeof( $data["orgCause"] ) ;  $i++  ){

                    /** Add Cause to pwi_products_causes **/
                    $x = new ProductCauses( array("product_id"=>$productId, "product_cause_type"=>"cause", "product_cause_item_id"=>$data["orgCause"][$i], "product_cause_status"=>"active"));
                    
                    $x->save( );
                    
                    $subCauseCountriesList = OrgSubCauseCountries::where("org_cause_id", $data["orgCauseId"][$i] )
                                            ->where("org_sc_status", "active")
                                            ->get( );

                    foreach( $subCauseCountriesList as $subCauseCountryItem ){
                        $x = new ProductCauses( array("product_id"=>$productId, "product_cause_type"=>$subCauseCountryItem["org_sc_type"], "product_cause_item_id"=>$subCauseCountryItem["org_sc_item_id"], "product_cause_status"=>"active"));
                        $x->save( );
                    }
                }
            }
            /** shipping methods **/

            if( $fedexEnabled ){
                $shipMethod = new ProductShipMethods( array( "product_id" => $productId, "ship_method" => "fedex", "shipmethod_status" => "Y" ) );
                $shipMethod->save( );
            }

            if( $uspsEnabled ){
                $shipMethod = new ProductShipMethods( array( "product_id" => $productId, "ship_method" => "usps", "shipmethod_status" => "Y" ) );
                $shipMethod->save( );
            }

            if( $upsEnabled ){
                $shipMethod = new ProductShipMethods( array( "product_id" => $productId, "ship_method" => "ups", "shipmethod_status" => "Y" ) );
                $shipMethod->save( );
            }

            /** Modifiers **/
            $modifierOptions = [];

            $items = [];

            if( isset( $data["modifierId"] ) ){
                for( $i = 0 ; $i < sizeof( $data["modifierId"] ) ; $i++ ){

                    $tmpModifierId = $data["modifierId"];

                    $modifier = new ProductModifiers( array( "product_id" => $productId, "product_modifier_title" => $data["modifierTitle"][$i], "product_modifier_status" => "active" ) );

                    $modifier->save( );
                    
                    for( $j = 0 ; $j < sizeof( $data["modifier_" . $i . "_item_id"] ) ; $j++ ){

                        $modifierItem = new ProductModifierOptions( array( 
                                "product_modifier_id"       => $modifier->product_modifier_id,
                                "product_id"                => $productId, 
                                "pm_option_name"            => $data["modifier_" . $i . "_item_title"][$j], 
                                "pm_option_price"           => 0, 
                                "pm_option_quantity"        => 0, 
                                "pm_option_shippingfee"     => 0, 
                                "pm_option_weight"          => 0, 
                                "pm_option_length"          => 0, 
                                "pm_option_width"           => 0, 
                                "pm_option_height"          => 0, 
                                "pm_option_parent_shipping" => "Y"));

                        $modifierItem->save( );

                        if( $i == 0 ){
                            $first[] = array( "dbId" => $modifierItem->pm_option_id, "tmpId" => $data["modifier_" . $i . "_item_id"][$j], "dbName" => $data["modifier_" . $i . "_item_title"][$j], "modifierId" => $modifier->product_modifier_id );
                        }else{
                            $rest[] = array( "dbId" => $modifierItem->pm_option_id, "tmpId" => $data["modifier_" . $i . "_item_id"][$j], "dbName" => $data["modifier_" . $i . "_item_title"][$j], "modifierId" => $modifier->product_modifier_id );
                        }
                    }
                }
                
                for( $i = 0 ; $i < sizeof( $first ) ; $i++ ){

                    $optionInventoryId    = $first[$i]["dbId"];
                    $optionInventoryName  = $first[$i]["dbName"];
                    $firstTmpId           = $first[$i]["tmpId"];

                    if( isset( $rest) && sizeof( $rest ) > 0 ){
                        for( $j = 0 ; $j < sizeof( $rest ) ; $j++ ){

                            $tmpId = $firstTmpId . "|" . $rest[$j]["tmpId"];

                            $modifierInventerData = array(
                                "product_id"                => $productId,
                                "pm_option_inventory"       => $optionInventoryId . "," . $rest[$j]["dbId"],
                                "pm_option_inventory_names" => $optionInventoryName . "," . $rest[$j]["dbName"]
                            );

                            if( in_array( $tmpId, $data["modifierInventoryIdList"] ) ){
                                $index = array_search($tmpId, $data["modifierInventoryIdList"] );

                                $modifierInventerData["pm_option_price"]            = $data["modifierInventoryPriceDiff"][$index];
                                $modifierInventerData["pm_option_quantity"]         = $data["modifierInventoryQuantity"][$index];
                                $modifierInventerData["pm_option_shippingfee"]      = $data["modifierInventoryShippingFee"][$index];
                                $modifierInventerData["pm_option_shipping_time"]    = $data["modifierInventoryShippingTime"][$index];
                                $modifierInventerData["pm_option_weight"]           = $data["modifierInventoryWeight"][$index];
                                $modifierInventerData["pm_option_length"]           = $data["modifierInventoryLength"][$index];
                                $modifierInventerData["pm_option_width"]            = $data["modifierInventoryWidth"][$index];
                                $modifierInventerData["pm_option_height"]           = $data["modifierInventoryHeight"][$index];
                                $modifierInventerData["pm_option_parent_shipping"]  = "Y";
                            }else{
                                $modifierInventerData["pm_option_price"]            = 0;
                                $modifierInventerData["pm_option_quantity"]         = 0;
                                $modifierInventerData["pm_option_shippingfee"]      = 0;
                                $modifierInventerData["pm_option_shipping_time"]    = 0;
                                $modifierInventerData["pm_option_weight"]           = 0;
                                $modifierInventerData["pm_option_length"]           = 0;
                                $modifierInventerData["pm_option_width"]            = 0;
                                $modifierInventerData["pm_option_height"]           = 0;
                                $modifierInventerData["pm_option_parent_shipping"]  = "Y";
                            }

                            $pmOptionInventer = new ProductModifierOptionInventer( $modifierInventerData );

                            $pmOptionInventer->save( );
                        }
                    }else{
                        $modifierInventerData = array(
                            "product_id"                => $productId,
                            "pm_option_inventory"       => $optionInventoryId,
                            "pm_option_inventory_names" => $optionInventoryName
                        );

                        if( in_array( $firstTmpId, $data["modifierInventoryIdList"] ) ){
                            $index = array_search($firstTmpId, $data["modifierInventoryIdList"] );

                            $modifierInventerData["pm_option_price"]            = $data["modifierInventoryPriceDiff"][$index];
                            $modifierInventerData["pm_option_quantity"]         = $data["modifierInventoryQuantity"][$index];
                            $modifierInventerData["pm_option_shippingfee"]      = $data["modifierInventoryShippingFee"][$index];
                            $modifierInventerData["pm_option_shipping_time"]    = $data["modifierInventoryShippingTime"][$index];
                            $modifierInventerData["pm_option_weight"]           = $data["modifierInventoryWeight"][$index];
                            $modifierInventerData["pm_option_length"]           = $data["modifierInventoryLength"][$index];
                            $modifierInventerData["pm_option_width"]            = $data["modifierInventoryWidth"][$index];
                            $modifierInventerData["pm_option_height"]           = $data["modifierInventoryHeight"][$index];
                            $modifierInventerData["pm_option_parent_shipping"]  = "Y";
                        }else{
                            $modifierInventerData["pm_option_price"]            = 0;
                            $modifierInventerData["pm_option_quantity"]         = 0;
                            $modifierInventerData["pm_option_shippingfee"]      = 0;
                            $modifierInventerData["pm_option_shipping_time"]    = 0;
                            $modifierInventerData["pm_option_weight"]           = 0;
                            $modifierInventerData["pm_option_length"]           = 0;
                            $modifierInventerData["pm_option_width"]            = 0;
                            $modifierInventerData["pm_option_height"]           = 0;
                            $modifierInventerData["pm_option_parent_shipping"]  = "Y";
                        }

                        $pmOptionInventer = new ProductModifierOptionInventer( $modifierInventerData );

                        $pmOptionInventer->save( );
                    }
                }
            }
            /** Return update status **/
            return Response::json(array("status"=>true) );
        }else{
            /** Return update status **/
            return Response::json(array("status"=>false) );
        }
    }

    public function removeProduct( Request $request ){

        if( $request->ajax( ) ){

            $productId = Input::get("productId");

            $product = Products::find( $productId );

            $product->product_status = "deleted";

            $product->save( );
        }
    }

    public function saveSubscriptionData( Request $request ){
        /*
        $data = $request->all( );

        $validationList = array( 
            "cc_number"     => "required|CreditCardNumber",
            "cc_ccv"        => 'required|CreditCardCvc:' . Input::get('cc_number'),
            "cc_exp_month"  => 'CreditCardDate:' . Input::get('cc_exp_year')
        );

        $messages = array( 
            'cc_number.required'            => 'Credit Card Number is required.',
            'cc_number.CreditCardNumber'    => 'Credit Card Number is Invalid.',
            'ccv.required'                  => 'Credit Card CCV is required.',
            'ccv.CreditCardCvc'             => 'Invalid CVC',
            'exp_date_month.CreditCardDate' => 'Invalid Expiration Date.'
        );

        $validator = \Validator::make($request->all(), $validationList, $messages);

        if( $validator->fails( ) ){
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }

        //Generate New Subscription 
        if( empty( $data["subscription_id"] ) ){

        }
        */
    }
}
