<?php

namespace App\Http\Controllers;

use App\Repositories\SocialMediaRepository as sMRepository;
use App\Repositories\NewsRepository as NewsRepository;
use App\Repositories\UserRepository as UserRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Organizations;
use App\Http\Helper;
use Carbon\Carbon;
use App\Products;
use App\Projects;
use App\Country;
use App\Follow;
use App\Files;
use Config;
use Agent;
use DB;

class CountryController extends Controller {

    /**
     * Prefix for mysql table
     *
     * @var string
     */
    private $mysqlPrefix;

    /**
     * Javascript file name to be loaded
     *
     * @var string
     */
    private $scriptPage;

    /**
     * List of demographics to be loaded for country
     *
     * @var array
     */
    private $demographicsList;

    /**
     * List of finance statistics
     *
     * @var array
     */
    private $financeList;

    /**
     * User object
     *
     * @var object
     */
    private $user = null;

    /**
     * Userimage path
     *
     * @var string
     */
    private $userImage = "";

    /**
     * userObj object
     *
     * @var object
     */
    private $userObj = null;

    /**
     * Helper object
     *
     * @var object
     */
    private $helper;

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
     * Country __construct function
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
        $this->mysqlPrefix = Config::get('globals.mysql_prefix');
        $this->scriptPage = "scripts.country";
        $this->demographicsList = array(
            "people:nationality:adjective|text",
            "people:population:text|text",
            "people:ethnic_groups:text|chart",
            "people:languages:text|chart",
            "people:religions:text|chart"
        );

        $this->helper = $helper;

        $this->userObj = $userObj;

        $this->user = $request->instance()->query('user');

        $this->smRepo = $sMRepository;

        $this->newsRepo = $newsRepo;
    }

    public function index() {

        return view('pages.countryIndex');
    }

    /*
     * @params -> takes in country alias
     *
     * --gets information for individual country
     *
     * returns array of data to country view 
     */

    public function view($alias) {

        //DB::connection( )->enableQueryLog( );

        $demographics = array();
        $finances = array();
        $countryCauses = array();
        $orgs = array();
        $projectList = array();
        $productList = array();
        setlocale(LC_MONETARY, 'en_US');

        $country;

        //Grab country data
        try {
            $country = Country::where("country_alias", "=", $alias)->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }

        //Decode JSON object of country Overview to A_ARRAY 
        $countryOverview = json_decode($country->country_overview, TRUE);

        //Decode JSON object of country statistics to A_ARRAY
        $statistics = json_decode($country->country_statistics, TRUE);

        //Call 'getDemographics' to retrieve readable version of displayed visual demographics
        $demoGraphics = $this->getDemographics($statistics);

        $noDemoGrapicsCount = 0;

        foreach ($demoGraphics as $item) {

            if (!is_array($item["data"])) {
                if (is_null($item["data"])) {
                    $noDemoGrapicsCount++;
                }
            } else {
                if (( sizeof($item["data"]) == 1 ) && ( empty($item["data"][0]["name"]) )) {
                    $noDemoGrapicsCount++;
                }
            }
        }

        if (sizeof($demoGraphics) == $noDemoGrapicsCount) {
            $demoGraphics = [];
        }

        $countryId = $country->country_id;
        $countryName = $country->country_name;

        $coverphoto = $country->coverphoto;

        if (!is_null($coverphoto)) {
            //Get coverphoto for country
            $coverphoto = Config::get('globals.countryImgPath') . $country->coverphoto->file_path;
        }

        //Retrieve projects for country
        $projectList = $this->helper->buildProjectsTileArray($country->crowdfunding);

        //Retrieve impact orgs for country
        $impactOrgs = $country->impactOrgs;


        //Retrieve array of Produts that have impacts in this country
        $productList = $this->helper->buildProductsTileArray($country->products);

        //Set paypal username for donations
        $paypal_un = "paypal@pwifoundation.org";

        $isFollowing = FALSE;

        //If the user is not null, see if the current user is following this country
        if (!is_null($this->user)) {

            $isFollowing = $this->userObj->isFollowing($this->user, "country", $country->country_id);
        }

        //Retrive Metadata for the indivdual country
        $meta = $this->helper->getMetaData("individual", "countries");

        $view = "pages.country.country";

        if (Agent::isMobile() && ! Agent::isTable( ) ) {
            $view = "mobile.pages.country.country";
        }

        //return view
        return view($view)->with([
            "orgs"          => $this->helper->buildOrgTileArray($impactOrgs, "country", $countryName),
            "coverphoto"    => $coverphoto,
            "country"       => $country,
            "causes"        => $this->helper->parseCauses($country->causeData),
            "demographics"  => $demoGraphics,
            "geography"     => $statistics["geo"],
            "finances"      => $statistics["econ"],
            "projects"      => $projectList,
            "products"      => $productList,
            "scriptPage"    => $this->scriptPage,
            "prjViewAll"    => "/country/" . $country->country_alias . "/projects",
            "prdViewAll"    => "/country/" . $country->country_alias . "/products",
            "orgViewAll"    => "/country/" . $country->country_alias . "/organizations",
            "paypal_un"     => $paypal_un,
            "meta"          => $this->helper->parseIndMetaData($meta[0], $country->country_name),
            "following"     => $isFollowing,
            "hashtags"      => str_replace(" ", ", ", $country->hashtags),
            "twitter"       => $this->smRepo->getTwitterHashtags($country->hashtags, FALSE),
            "instagram"     => $this->smRepo->getInstagramHashtags($country->hashtags, FALSE),
            "news"          => $this->newsRepo->getNews($country->country_name)
        ]);
    }

    public function donate($alias, Request $request) {

        //DB::connection( )->enableQueryLog( );

        $country;

        // try to fetch this org - if Exception abort to 404
        try {
            $country = Country::where("country_alias", "=", $alias)
                    ->select("country_id", "country_name")
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
            "country"           => $country,
            "amount"            => $donationAmt,
            "paypal_un"         => $paypal_un,
            "scriptPage"        => $this->scriptPage,
            "meta"              => $this->helper->parseIndMetaData($meta[0], $country->country_name),
            "payment_gateway"   => $payment_gateway,
            "years"             => $years
        ]);
    }

    private function getDemographics($stats) {

        $tmp = array();

        foreach ($this->demographicsList as $demographic) {

            $d = "";
            $n = "";
            $t = "";

            list($path, $type) = explode("|", $demographic);

            $t = $type;

            $trail = explode(":", $path);

            $n = str_replace("_", " ", $trail[1]);

            $words = explode(" ", $n);

            if (sizeof($words) > 1) {
                $n = "";
                foreach ($words as $word) {
                    if (empty($n)) {
                        $n = ucfirst($word);
                    } else {
                        $n .= " " . ucfirst($word);
                    }
                }
            } else {
                $n = ucfirst($n);
            }

            $value;

            for ($i = 0; $i < sizeof($trail); $i++) {

                if ($i == 0) {
                    $value = $stats[$trail[$i]];
                } else {
                    $value = $value[$trail[$i]];
                }
            }

            if ($type == "text") {

                $tmp[] = array(
                    "type" => $t,
                    "name" => $n,
                    "data" => $value
                );
            } else {
                $data = explode(", ", $value);
                $percentage_pattern = "/^\d*\.?\d*%$/";
                $parentheses_pattern = "/\(([^)]+)\)/";

                for ($j = 0; $j < sizeof($data); $j++) {
                    $percentage = "";
                    $date = "";
                    $name = "";
                    $items = explode(" ", $data[$j]);

                    foreach ($items as $item) {
                        if (preg_match($percentage_pattern, $item)) {
                            $percentage = str_replace("%", "", $item);
                        } else {
                            if (empty($name)) {
                                $name = $item;
                            } else {
                                $name .= " " . $item;
                            }
                        }
                    }

                    if ($j == ( sizeof($data) - 1 )) {
                        preg_match($parentheses_pattern, $name, $matches);

                        $date = array_pop($matches);
                    }
                    $d[] = array(
                        "name" => $name,
                        "percentage" => $percentage,
                        "date" => $date
                    );
                }

                $tmp[] = array(
                    "type" => $t,
                    "name" => $n,
                    "data" => $d
                );
            }
        }

        return $tmp;
    }

    public function organziations($alias) {
        
    }

}
