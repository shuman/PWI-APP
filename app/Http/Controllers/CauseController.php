<?php

namespace App\Http\Controllers;

use App\Repositories\SocialMediaRepository as sMRepository;
use App\Repositories\NewsRepository as NewsRepository;
use App\Repositories\UserRepository as UserRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Helper;
use Carbon\Carbon;
use App\Causes;
use App\Follow;
use App\Files;
use Config;
use Agent;
use DB;

class CauseController extends Controller {

    /**
     * Javascript file name to be loaded
     *
     * @var string
     */
    private $scriptPage;

    /**
     * object for the Helper Class
     *
     * @var object
     */
    private $helper;

    /**
     * Associative Array to store the mapping of cause ids
     * to their respective icons
     *
     * @var array (associative)
     */
    private $causeIconMap = array();

    /**
     * object for the User Class
     *
     * @var object
     */
    private $user = null;

    /**
     * object for the UserRepository Class
     *
     * @var object
     */
    private $userObj;

    /**
     * object for the SocialMediaRepository Class
     *
     * @var object
     */
    private $smRepo = null;

    /**
     * object for the newsrepository class
     *
     * @var object
     */
    private $newsRepo = null;

    /**
     * Cause __construct function
     *
     * @param UserRepository object          $userObj
     *
     * @param Request object                 $request
     *
     * @param Helper object                  $helper
     *
     * @param SocialmediaRepository object   $sMRepository
     */
    public function __construct(UserRepository $userObj, Request $request, Helper $helper, sMRepository $sMRepository, NewsRepository $newsRepo) {
        $this->scriptPage = "scripts.causes";
        $this->helper = $helper;

        $this->causeIconMap = array(
            "1" => "pwi-cause-environment-solid",
            "20" => "pwi-cause-family-solid",
            "21" => "pwi-cause-humanrights-solid",
            "22" => "pwi-cause-education-solid",
            "23" => "pwi-cause-poverty-solid",
            "24" => "pwi-cause-religion-solid",
            "43" => "pwi-cause-water-solid",
            "44" => "pwi-cause-economy-solid",
            "45" => "pwi-cause-government-solid",
            "46" => "pwi-cause-health-solid",
            "47" => "pwi-cause-children-solid"
        );

        $this->user = $request->instance()->query('user');

        $this->userObj = $userObj;

        $this->smRepo = $sMRepository;

        $this->newsRepo = $newsRepo;
    }

    /**
     * Cause index function
     */
    public function index() {
        
    }

    /**
     * Cause view function
     *
     * @param string                     $alias
     * 
     * @return View      
     */
    public function view($alias) {
//         echo $alias;
//         die();
        //DB::connection( )->enableQueryLog( );

        $cause;

        // try to fetch this org - if Exception abort to 404
        try {
            $cause = Causes::where("cause_alias", "=", $alias)
                    ->leftJoin("pwi_files AS LOGO", "LOGO.file_id", "=", "pwi_causes.cause_icon_img")
                    ->leftJoin("pwi_files AS CP", "CP.file_id", "=", "pwi_causes.cause_cover_img")
                    ->select("cause_id", "cause_name", "cause_content", "cause_raised_amt", "cause_parent_id", "cause_instagram_hashtag as hashtags", "cause_content_reference as reference", "LOGO.file_path AS icon", "CP.file_path as coverphoto")
                    ->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }

        $coverphoto = "";
        $causeIcon = "";
        //set default paypal username
        $paypal_un = "paypal@pwifoundation.org";

        /*         * *
         * if the cause does not have a parent assign the values for
         * the cause icon and coverphoto from the variables in the
         * database. If the cause does have a parent map the
         * cause icon and find the coverphoto that 
         * corresponds to the parent cause
         */

        $orgs;

        if ($cause->cause_parent_id == 0) {
            $causeIcon  = $this->causeIconMap[$cause->cause_id];
            $coverphoto = $cause->coverphoto;
            $orgs       = $cause->orgs;
        } else {
            $causeIcon  = $this->causeIconMap[$cause->cause_parent_id];
            $coverphoto = Files::find(( Causes::find($cause->cause_parent_id)->cause_cover_img))->file_path;
            $orgs       = $cause->getSubCauseOrgs;
        }

        if (!empty($cause->hashtags)) {
            //Retrieve the hashtag feed from Twitter
            $twitterHashtagFeed = $this->smRepo->getTwitterHashtags($cause->hashtags);

            //Retrieve the hashtag feed from instagram
            $instagramHashtagFeed = $this->smRepo->getInstagramHashtags($cause->hashtags);
        } else {
            $twitterHashtagFeed = [];
            $instagramHashtagFeed = [];
        }


        $isFollowing = FALSE;

        //if the user object is not null, see if the user is following this cause
        if (!is_null($this->user)) {

            $isFollowing = $this->userObj->isFollowing($this->user, "cause", $cause->cause_id);
        }

        //get meta data for page
        $meta = $this->helper->getMetaData("individual", "causes");

        $view = "";

        //check if the user agent is a mobile device
        if (Agent::isMobile() && ! Agent::isTablet( ) ) {
            $view = "mobile.pages.causes.cause";
        } else {
            $view = "pages.causes.cause";
        }

        //$view = "pages.causes.cause";
        //return view with variables
        return view($view)->with([
            "cause"         => $cause,
            "alias"         => $alias,
            "coverphoto"    => $coverphoto,
            "orgs"          => Helper::buildOrgTileArray($orgs, "cause", $cause->cause_name),
            "projects"      => Helper::buildProjectsTileArray($cause->crowdfunding),
            "products"      => Helper::buildProductsTileArray($cause->products),
            "subcauses"     => $cause->subcauses,
            "scriptPage"    => $this->scriptPage,
            "causeImgPath"  => Config::get("globals.cseImgPath"),
            "prjViewAll"    => "/cause/" . $alias . "/projects",
            "prdViewAll"    => "/cause/" . $alias . "/products",
            "orgViewAll"    => "/cause/" . $alias . "/organizations",
            "paypal_un"     => $paypal_un,
            "icon"          => $causeIcon,
            "meta"          => $this->helper->parseIndMetaData($meta[0], $cause->cause_name),
            "following"     => $isFollowing,
            "hashtags"      => explode(" ", $cause->hashtags),
            "twitter"       => $twitterHashtagFeed,
            "instagram"     => $instagramHashtagFeed,
            "news"          => $this->newsRepo->getNews($cause->cause_name)
        ]);
    }

    public function donate($alias, Request $request) {

        //DB::connection( )->enableQueryLog( );

        $cause;

        // try to fetch this org - if Exception abort to 404
        try {
            $cause = Causes::where("cause_alias", "=", $alias)
                    ->select("cause_id", "cause_name")
                    ->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }

        $donationAmt = 0.00;
        //set default paypal username
        $paypal_un = "paypal@pwifoundation.org";

        //check if there is a session variable that holds donation amount
        if ($request->session()->has('donationAmt')) {

            //retieve donation amount
            $donationAmt = $request->session()->get('donationAmt');
            //erase donation amount from session storage
            //$request->session->forget('donationAmt');
        } else {
            $donationAmt = "";
        }

        $payment_gateway = 2;

        $years = array( );

        for( $i = Carbon::now( )->year ; $i < ( Carbon::now( )->year + 8 ) ; $i++ ){

            $yearAbbr = substr($i, -2);
            $years[$yearAbbr] = $i; 
        }

        //get meta data for page
        $meta = $this->helper->getMetaData("individual", "donations");

        return view("donations")->with([
            "cause"             => $cause,
            "amount"            => $donationAmt,
            "paypal_un"         => $paypal_un,
            "scriptPage"        => $this->scriptPage,
            "meta"              => $this->helper->parseIndMetaData($meta[0], $cause->cause_name),
            "payment_gateway"   => $payment_gateway,
            "years"             => $years,
        ]);
    }

    public function validateDonation(Request $request) {

        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            //'cc_number'         => 'required|digits:16',
            //'name_on_card'      => 'required',
            //'exp_date'          => 'required|date_format:m/Y',
            //'ccv'               => 'required|regex:/^([0-9]{3,4})$/',
            'billingAddress1' => 'required',
            'billingCity' => 'required',
            'billingState' => 'required|exists:pwi_state,state_code',
            'billingZip' => 'required|max:5',
            'billingCountry' => 'required|exists:pwi_country,country_iso_code'
        ]);
    }

}
