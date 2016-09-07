<?php

namespace App\Http;

use App\Repositories\UserRepository as UserRepository;
use Folklore\Image\Facades\Image;
use Illuminate\Http\Request;
use App\ProductCategories;
use App\Organizations;
use App\UserAddress;
use Carbon\Carbon;
use App\MetaData;
use App\Products;
use App\Projects;
use App\Causes;
use App\Files;
use App\Stats;
use Config;
use Auth;
use DB;



setlocale(LC_MONETARY, 'en_US');

class Helper {

    /** Set up the regEx pattern for Meta Data **/
    private $metaPattern = "/%%[A-Z]+%%/";  

    /** 
    * buildProjectsTileArray function
    *
    * Function to build the Array for Project being displayed
    * on the front page
    *
    * @param $projects - Collection
    *
    * @return Array
     **/
    static function buildProjectsTileArray($projects) {

        /** Initiate return array **/
        $list = array();

        /** Loop through projects Collection and extract data **/
        foreach ($projects as $project) {

            $projectId          = $project->project_id;
            $projectOrgName     = $project->org_name;
            $projectOrgAlias    = $project->org_alias;
            $projectTitle       = $project->project_title;
            $projectAlias       = $project->project_alias;
            $fundGoal           = $project->project_fund_goal;
            $amtRaised          = $project->project_amout_raised;
            $causeNames         = "";
            $countryNames       = "";

            /** Get the amount of days left from the project **/
            $daysLeft = Carbon::createFromTimeStamp(strtotime($project->project_end_date))->diffInDays();

            /** Find this individual Project **/
            $thisProject = Projects::find($projectId);

            /** Get project Causes **/
            $causes = $thisProject->causes;

            /** Get project impact countries **/
            $countries = $thisProject->countries;

            $icon = "";
            $header = "";

            /** See if the project thumbnail is null **/
            if( ! is_null( $thisProject->icon ) ){
                /** If not set the front facing url for the image **/
                $icon = Config::get('globals.prjImgPath') . $thisProject->icon->file_path;
            }
            
            /** See if the header image is null **/
            if( ! is_null( $thisProject->header ) ){
                /** If not set the front facing url for the image **/
                $header = Config::get('globals.prjImgPath' ) . $thisProject->header->file_path;
            }
            
            /** Get the end date/time for project **/
            list( $date, $time) = explode(" ", $thisProject->project_end_date);

            /** Get date components from extracted date above **/
            list( $year, $month, $day) = explode("-", $date);

            /** If the icon doesn't exist get the place holder image **/
            if (!file_exists(public_path() . $icon)) {
                $icon = "/images/cfPlaceholder.png";
            }

            /** Loop through each causes and create a comma delimited list **/
            foreach ($causes as $cause) {
                if (empty($causeNames)) {
                    $causeNames = $cause->cause_name;
                } else {
                    $causeNames .= ", " . $cause->cause_name;
                }
            }

            /** Loop through each country and create a comma delimited list **/
            foreach ($countries as $country) {
                if (empty($countryNames)) {
                    $countryNames = $country->country_name;
                } else {
                    $countryNames .= ", " . $country->country_name;
                }
            }

            /** Check if Amount Raised equals 0 **/
            if ((int) $amtRaised == 0) {
                /** If so, set percentage complete = 0 **/
                $projectPercentageComplete = 0;
            } else {
                /** Otheerwise, calculate percentage complete **/
                $projectPercentageComplete = (((int) $amtRaised / (int) str_replace(",", "", $fundGoal)) * 100);
            }

            /** Add this project data to array **/
            $list[] = array(
                "id"                => $projectId,
                "org_name"          => $projectOrgName,
                "org_alias"         => $projectOrgAlias,
                "title"             => $projectTitle,
                "alias"             => $projectAlias,
                "fundGoal"          => money_format('%(#10n', (int) $fundGoal),
                "goalInt"           => $fundGoal,
                "amtRaised"         => money_format('%(#10n', (int) $amtRaised),
                "causes"            => $causeNames,
                "countries"         => $countryNames,
                "icon"              => $icon,
                "header"            => $header,
                "percentage"        => $projectPercentageComplete,
                "daysleft"          => $daysLeft,
                "desc"              => $thisProject->project_story,
                "videoUrl"          => $thisProject->project_video_url,
                "origVideoUrl"      => $thisProject->project_orig_video_url,
                "incentives"        => $thisProject->incentives,
                "projectCauses"     => $thisProject->causes,
                "updates"           => $thisProject->updates,
                "endMonth"          => $month,
                "endDay"            => $day,
                "endYear"           => $year
            );
        }

        /** Return List **/
        return $list;
    }/** end buildProjectsTileArray **/

    /** 
    * buidProductsTileArray Function
    *
    * Function to build the array for products to be 
    * displayed on the front end
    *
    * @param $products - Collection
    *
    * @return Array
     **/
    static function buildProductsTileArray($products) {

        /** Initiate return array **/
        $list = array();

        /** Loop through Products **/
        foreach ($products as $product) {

            /** Extract data from current product **/
            $product_id     = $product->product_id;
            $product_name   = $product->product_name;
            $product_alias  = $product->product_alias;
            $short_desc     = $product->product_short_desc;
            $org_name       = $product->org_name;
            $sales_price    = $product->product_sales_price;
            $image          = Config::get("globals.prdImgPath") . $product->file_path;

            /** Check if image for product exists **/
            if (!file_exists(public_path() . $image) || is_dir(public_path() . $image)) {
                /** If not set placholder **/
                $image = "/images/prodPlaceholder.png";
            }

            /** Get this product **/
            $thisProduct = Products::find($product_id);

            /** Get the product rating **/
            $rating = $thisProduct->rating->avg("comment_rating");

            /** Append Data to list **/
            $list[] = array(
                "name"      => $product_name,
                "alias"     => $product_alias,
                "sdesc"     => $short_desc,
                "org_name"  => stripslashes($org_name),
                "image"     => $image,
                "price"     => money_format('%(#10n', $sales_price),
                "rating"    => $rating
            );
        }
        /** Return list **/
        return $list;
    }/** End buildProductsTileArray **/

    /** 
    * buildOrgTileArray Function
    *
    * Function to build the array for Organizations to
    * be displayed on the front end
    *
    * @param $orgs - Collection
    * 
    * @param $from - String 
    *
    * @param $name - String
    * 
    * @return Array
     **/
    static function buildOrgTileArray($orgs, $from, $name) {

        /** Initiate Array to return **/
        $list = array();

        /** Loop through each Organization **/
        foreach ($orgs as $org) {

            //Extract/Declare variables for organization
            $id                 = $org->org_id;
            $org_name           = $org->org_name;
            $desc               = $org->org_desc;
            $alias              = $org->org_alias;
            $impactCountries    = "";
            $orgCauses          = "";
            $orgRatings         = 0;

            /** Get the current organization **/
            $tmp = Organizations::find($id);    

            /** Get impact Countries for Org **/
            $impactCountries    = $tmp->impactCountries;
            $impacts            = "";

            /** Loop through impactCountries and create a comma delimited list **/
            foreach ($impactCountries as $impactCountry) {
                $impactCountryName = $impactCountry->country_name;
                if (empty($impacts)) {
                    $impacts = $impactCountryName;
                } else {
                    $impacts .= ", " . $impactCountryName;
                }
            }

            /** See if the request is coming from the Country Controller **/
            if ($from == "country") {
                /** Add Bold Country name to impacts **/
                $impacts = str_replace("<b>" . $name . "</b>", $name, $impacts);
            }

            /** Get Causes for Organization **/
            $causes = $tmp->causes;

            /** Loop through causes and create a comma delmited list **/
            foreach ($causes as $cause) {
                if (empty($orgCauses)) {
                    $orgCauses = $cause->cause_name;
                } else {
                    $orgCauses .= ", " . $cause->cause_name;
                }
            }

            /** See if this function call is from the Cause Controller **/
            if ($from == "cause") {
                /** Replace Cause name with bold cause name **/
                $orgCauses = str_replace("<b>" . $name . "</b>", $name, $orgCauses);
            }

            /** Get Org Rating **/
            $orgRatings = $tmp->rating->avg("comment_rating");

            /** Get Org Logo **/
            $orgLogo = $tmp->logo;

            /** See if the file path for the orgLogo is set and if the image exists **/
            if (isset($orgLogo->file_path) && file_exists(public_path() . Config::get("globals.orgImgPath") . $orgLogo->file_path)) {
                /** If so set the public url for the image **/
                $orgLogo = Config::get("globals.orgImgPath") . $orgLogo->file_path;
            }else{
                /** If not, set org placeholder **/
                $orgLogo = "/images/orgPlaceHolder.jpg";
            }

            //Build Org array
            $list[] = array(
                "id"                => $id,
                "name"              => $org_name,
                "alias"             => $alias,
                "desc"              => $desc,
                "impactCountries"   => $impacts,
                "causes"            => $orgCauses,
                "rating"            => $orgRatings,
                "logo"              => $orgLogo,
                "descExp"           => explode(" ", $desc),
            );
        }
        /** Return Org Array **/
        return $list;
    }/** End buildOrgTileArray **/

    /** 
    * getMetaData Function
    *
    * Searches for the Meta Data of a page and returns data
    *
    * @param $type - String ( general, individual )
    *
    * @param $page - String ( organization, project, general, etc )
     **/
    public function getMetaData($type, $page) {

        /** Return Data from MetaData table **/
        return MetaData::where("type", "=", $type)
                        ->where("page", "=", $page)
                        ->select("title", "description")
                        ->get();
    }/** End getMetaData **/

    /** 
    * parseIndMetaData function
    *
    * Parses the Response from the individual Meta Data Call 
    *
    * @param $data - Object ( data of the page )
    *
    * @param $name - String ( name of the page )
    *
    * @return Object
     **/
    public function parseIndMetaData($data, $name) {

        /** Set Meta Title **/
        $data->title = preg_replace($this->metaPattern, $name, $data->title);

        /** Set Meta Description **/
        $data->description = preg_replace($this->metaPattern, $name, $data->description);

        /** Return Data **/
        return $data;
    }

    /** 
    * parseSearchMetaData Function
    *
    * Parses the Search Page Meta Data
    *
    * @param $data - Object
    *
    * @param $of - String
    *
    * @param $in - String
    *
    * @return Object
     **/
    public function parseSearchMetaData($data, $of, $in) {
        /** Change title **/
        $data->title = str_replace("%%OF%%", $of, $data->title);
        /** Change Title **/
        $data->title = str_replace("%%IN%%", $in, $data->title);
        /** set Description **/
        $data->description = $data->description;
        /** Return $data **/
        return $data;
    }/** End parseSearchMetaData **/

    /** 
    * parseProductPurchaseData Function
    *
    * Parses the Meta Data for Product Purchase Pages
    *
    * @param $data - Object
    *
    * @param $productName - String
    *
    * @param $orgName - String
    *
    * @return Object
     **/
    public function parseProductPurchaseData($data, $productName, $orgName) {

        /** Replace Title with Product Name **/
        $data->title = str_replace("%%PRODNAME%%", $productName, $data->title);

        /** Replace Title with Org Name **/
        $data->title = str_replace("%%ORGNAME%%", $orgName, $data->title);

        /** Replace Product name in Description **/
        $data->description = str_replace("%%PRODNAME%%", $productName, $data->description);

        /** Return Data **/
        return $data;
    }/** end parseProductPurchaseData **/

    /** 
    * parseCauses Function
    *
    * create a list of cause Data
    *
    * @param $causes - Collection
    *
    * @return Array
     **/
    public function parseCauses($causes) {

        /** Initiate return array **/
        $list = array();

        /** Loop through each Cauess **/
        foreach ($causes as $cause) {

            $icon = "";
            $desc = "";
            $ref = "";

            /** Check if this cause has a parent id **/
            if ($cause->cause_parent_id == 0) {
                /** Get Cause Icon **/
                $icon = $this->getCauseIcon($cause->cause_name);
            } else {
                /** Get Parent Cause **/
                $tmp = Causes::find($cause->cause_parent_id);

                /** Get cause Icon **/
                $icon = $this->getCauseIcon($tmp->cause_name);
            }

            /** see if cause has reference data **/
            if (isset($cause->reference)) {
                /** Get cause reference **/
                $ref = $cause->reference;
            }

            /** Appened dat to list **/
            $list[] = array(
                "name"      => $cause->cause_name,
                "alias"     => $cause->cause_alias,
                "desc"      => $cause->description,
                "descExp"   => explode(" ", $cause->description),
                "icon"      => $icon,
                "id"        => $cause->cause_id,
                "reference" => $ref,
                "hashtags"  => $cause->cause_instagram_hashtag,
                "orgCauseId"=> $cause->orgCauseId
            );
        }
        /** Return Array **/
        return $list;
    }/** End parseCauses **/

    /** 
    * cleanSocialLink function
    *
    * function cleans up the social media link so that it is linkable
    *
    * @param $link - String
    *
    * @param $platform  - String
    *
    * @return String
     **/
    public function cleanSocialLink($link, $platform) {

        /** Get the url for this social link **/
        $url = "<a href='" . $this->getSocialUrl($link, $platform) . "' target='_blank' class='blue'>";
        $handle = "";

        /** See if $link has https in the beginning of the link **/
        if (preg_match("/^https/", $link)) {

            /** See if $link has https://www in the beginning of string **/
            if (preg_match("/^https:\/\/www/", $link)) {
                /** Create handle with https://www at beginning **/
                $handle = str_replace("https://www." . $platform . ".com", "", $link);
            } else {
                /** Otherwise, create handle with https:// in front **/
                $handle = str_replace("https://" . $platform . ".com", "", $link);
            }
        /** See if $link starts with 'http' **/
        } else if (preg_match("/^http/", $link)) {
            if (preg_match("/^http:\/\/www/", $link)) {
                $handle = str_replace("http://www." . $platform . ".com", "", $link);
            } else {
                $handle = str_replace("http://" . $platform . ".com", "", $link);
            }
        /** See if $link starts with 'www' **/
        } else if (preg_match("/^www/", $link)) {
            $handle = str_replace("www." . $platform . ".com", "", $link);
        } else {
            $handle = str_replace($platform . ".com", "", $link);
        }
        /** See if length of handle is greater than 20 **/
        if (strlen($handle) > 20) {
            /** If so, truncate handle **/
            return $url . substr($handle, 0, 20) . "...</a>";
        } else {
            /** Otherwise, return handle and url **/
            return $url . $handle . "</a>";
        }
    }/** End cleanSocialLink **/

    /** 
    * getSocialUrl Function
    *
    * Function returns the full social Media URL
    *
    * @param $link - String
    *
    * @param $platform - String
    *
    * @return string
     **/
    public function getSocialUrl($link, $platform) {

        /** See if link starts with https:// or http:// **/
        if (preg_match("/^https/", $link) || preg_match("/^http/", $link)) {
            /** If so return link **/
            return $link;
        /** See if link starts with 'www' **/
        } else if (preg_match("/^www/", $link)) {
            /** Replace $platform with link **/
            $handle = str_replace("www." . $platform . ".com", "", $link);
            /** Return link with https in front of it **/
            return "https://www." . $platform . ".com" . $handle . "'";

        } else {
            $handle = str_replace($platform . ".com", "", $link);

            return "https://www." . $platform . ".com" . $handle . "'";
        }
    }

    /** 
    * sendXMLviaCurl Function
    *
    * Function to send XML to Payment Gateway via Curl
    *
    * @param $xmlRequest - XMLRequest Object
    *
    * @param $gatewayURL - String
    *
    * @return Curl Response
     **/
    public function sendXMLviaCurl($xmlRequest, $gatewayURL) {
        
        /** helper function demonstrating how to send the xml with curl **/
        $ch = curl_init(); // Initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $gatewayURL); // Set POST URL

        $headers = array();
        $headers[] = "Content-type: text/xml";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add http headers to let it know we're sending XML
        $xmlString = $xmlRequest->saveXML();
        curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return into a variable
        curl_setopt($ch, CURLOPT_PORT, 443); // Set the port number
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Times out after 30s
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString); // Add XML directly in POST

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);


        /** 
        * This should be unset in production use. 
        * With it on, it forces the ssl cert to be valid
        * before sending info. **/
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (!($data = curl_exec($ch))) {
            print "curl error =>" . curl_error($ch) . "\n";
            throw New Exception(" CURL ERROR :" . curl_error($ch));
        }
        curl_close($ch);

        return $data;
    }/** End sendXMLviaCurl **/

    /** 
    * appendXMLNode Function   
    *
    * Function adds a node onto an existing XML Document
    *
    * @param $domDocument - XMLDocument
    *
    * @param $parentNode - XMLNode
    *
    * @param $name - String
    *
    * @param $value - String
    *
     **/
    public function appendXmlNode($domDocument, $parentNode, $name, $value) {
        /** Create New Node **/
        $childNode = $domDocument->createElement($name);
        /** Set Value for childNode **/
        $childNodeValue = $domDocument->createTextNode($value);
        /** Append value to child node **/
        $childNode->appendChild($childNodeValue);
        /** append Child Node to Parent **/
        $parentNode->appendChild($childNode);
    }/** End appendXmlNode **/

    /** 
    * getCauseIcon Function
    *   
    * Get the icon for a particular Cause
    *
    * @param $causename - string
    *
    * @return string
     **/
    private function getCauseIcon($causename) {

        switch (strtolower($causename)) {
            case "human rights":
                return "humanrights";
                break;
            case "clean water":
                return "water";
                break;
            case "children/youth":
                return "children";
                break;
            default:
                return strtolower($causename);
                break;
        }
    }/** End getCauseIcon **/

    /**  
    * getCauseIconClass Function
    *
    * Function returns Icon class based on cause id or Name
    *
    * @param $cause - String or INT
    * 
    * @return String
    **/
    public function getCauseIconClass($cause) {

        if ($cause == 1 || $cause == 'Environment' || $cause == 588) {
            return 'pwi-cause-environment-stroke';
        }
        if ($cause == 20 || $cause == 'Family' || $cause == 589) {
            return 'pwi-cause-family-stroke';
        }
        if ($cause == 23 || $cause == 'Poverty' || $cause == 582) {
            return 'pwi-cause-poverty-stroke';
        }
        if ($cause == 43 || $cause == 'Clean Water' || $cause == 585) {
            return 'pwi-cause-water-stroke';
        }
        if ($cause == 22 || $cause == 'Education' || $cause == 587) {
            return 'pwi-cause-education-stroke';
        }
        if ($cause == 45 || $cause == 'Government' || $cause == 590) {
            return 'pwi-cause-government-stroke';
        }
        if ($cause == 21 || $cause == 'Human Rights' || $cause == 592) {
            return 'pwi-cause-humanrights-stroke';
        }
        if ($cause == 44 || $cause == 'Economy' || $cause == 586) {
            return 'pwi-cause-economy-stroke';
        }
        if ($cause == 24 || $cause == 'Religion' || $cause == 583) {
            return 'pwi-cause-religion-stroke';
        }
        if ($cause == 46 || $cause == 'Health' || $cause == 591) {
            return 'pwi-cause-health-stroke';
        }
        if ($cause == 47 || $cause == 'Children/Youth' || $cause == 588) {
            return 'pwi-cause-children-stroke';
        }
        return '';
    }/** End getCauseIconClass **/

    /** 
    * url2embed Function
    *
    * Function takes a youtube url, gets the id and returns the embed tag
    *
    * @param $url - String
    *
    * @return String
     **/
    public function url2embed($url) {
        /** Get Video Id from getYoutubeIdFromUrl **/
        $video_id = $this->getYoutubeIdFromUrl($url);
        /** Chekc Iif video is truthey **/
        if ($video_id) {
            /** Return iframe **/
            return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $video_id . '?autoplay=1&showinfo=0" frameborder="0" allowfullscreen></iframe>';
        }
        return '';
    }/** End url2embed **/

    /**
     * getYoutubeIdFromUrl Function
     *
     * Function get youtube video ID from URL
     *
     * @param string $url
     *
     * @return string Youtube video id or FALSE if none found. 
     */
    public function getYoutubeIdFromUrl($url) {
        /** Match Pattern **/
        $pattern = '%^# Match any youtube URL
            (?:https?://)?  # Optional scheme. Either http or https
            (?:www\.)?      # Optional www subdomain
            (?:             # Group host alternatives
              youtu\.be/    # Either youtu.be,
            | youtube\.com  # or youtube.com
              (?:           # Group path alternatives
                /embed/     # Either /embed/
              | /v/         # or /v/
              | /watch\?v=  # or /watch\?v=
              )             # End path alternatives.
            )               # End host alternatives.
            ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
            $%x'
        ;
        /** Get Result **/
        $result = preg_match($pattern, $url, $matches);
        /** If result is truthey **/
        if ($result) {
            /** Return matches **/
            return $matches[1];
        }
        /** Otherwise return false **/
        return false;
    }/** End getYoutubeIdFromUrl **/

    /** 
    * saveShippingAddress Function
    *
    * Function takes an array and inserts data into UserAddress Table
    *
    * @param $data - Array
    * 
     **/
    public function saveShippingAddress($data) {
        UserAddress::create([
            "user_addr_user_id"         => $data["user_id"],
            "user_addr_address_type"    => "shipping",
            "user_addr_line1"           => $data["shipping_address_line1"],
            "user_addr_line2"           => $data["shipping_address_line2"],
            "user_addr_city"            => $data["shipping_city"],
            "user_addr_country_code"    => $data["shipping_country"],
            "user_addr_state"           => $data["shipping_state"],
            "user_addr_zip"             => $data["shipping_zip"],
            "user_addr_isDefault"       => "N",
            "user_addr_status"          => "active",
            "user_addr_fname"           => $data["billing_first_name"],
            "user_addr_lname"           => $data["billing_last_name"]
        ]);
    }/** End saveShippingAddress **/

    /** 
    * saveBillingAddress Function
    *
    * Function takes an array and inserts data into UserAddress Table
    *
    * @param $data - Array
    *
     **/
    public function saveBillingAddress($data) {

        UserAddress::create([
            "user_addr_user_id"         => $data["user_id"],
            "user_addr_address_type"    => "billing",
            "user_addr_line1"           => $data["billing_address_line1"],
            "user_addr_line2"           => $data["billing_address_line2"],
            "user_addr_city"            => $data["billing_city"],
            "user_addr_country_code"    => $data["billing_country"],
            "user_addr_state"           => $data["billing_state"],
            "user_addr_zip"             => $data["billing_zip"],
            "user_addr_isDefault"       => "N",
            "user_addr_status"          => "active",
            "user_addr_fname"           => $data["first_name"],
            "user_addr_lname"           => $data["last_name"]
        ]);
    }/** End saveBillingAddress **/

    /** 
    * getQuickViews Function
    *
    * Function gets QuickView Stats for Org Dashboard
    *
    * @param orgId
    *
    * @return Array
     **/
    public function getQuickViews($orgId) {

        /** Initiate Return Array **/
        $views = array();

        /** Get PWI Page View Stats **/
        $views["pwi"] = (int) Stats::where('type', '=', 'date')->sum('pageviews');

        /** Location Query **/
        $location = DB::table("pwi_org_subcause_countries AS scc")
                ->select(DB::raw("SUM(DISTINCT ga.pageviews ) AS LocationViews"))
                ->join("pwi_stats_ga AS ga", "ga.country_id", "=", "org_sc_item_id")
                ->where("scc.org_sc_status", "=", "active")
                ->where("scc.org_sc_type", "=", "country")
                ->where("scc.org_id", "=", $orgId)
                ->get();

        /** Set Location Data **/        
        $views["location"] = (int) $location[0]->LocationViews;

        /** Cause Stat Query **/
        $cause = Stats::where("scc1.org_id", "=", $orgId)
                ->leftJoin("pwi_org_subcause_countries AS scc1", function( $join) {
                    $join->on('scc1.cause_id', '=', 'pwi_stats_ga.cause_id')
                    ->where('scc1.org_sc_status', '=', 'active');
                })
                ->leftJoin("pwi_org_subcause_countries AS scc2", function( $join) {
                    $join->on("scc2.org_sc_item_id", "=", "pwi_stats_ga.cause_id")
                    ->where("scc2.org_sc_status", "=", "active")
                    ->where("scc2.org_sc_type", "=", "subcause");
                })
                ->select(DB::raw("SUM( DISTINCT pwi_stats_ga.pageviews) AS CauseViews"))
                ->get();

        /** Set Causes View Data **/
        $views["causes"] = (int) $cause[0]->CauseViews;

        /** Get Profile Views **/
        $profileViews = Stats::where("org_id", "=", $orgId)
                ->select("pageviews")
                ->get();

        /** Check if size of ProfileViews is greater than 0 **/
        if (sizeof($profileViews) > 0) {
            /** Set ProfileView Data **/
            $views["profileViews"] = $profileViews[0]->pageviews;
        } else {
            /** Set ProfileView to 0 **/
            $views["profileViews"] = 0;
        }

        /** Followers Query **/
        $followers = DB::table("pwi_follow")
                ->where("follow_type", "org")
                ->where("follow_type_id", "=", $orgId)
                ->count();

        /** Set Followers Count **/
        $views["followers"] = (int) $followers;

        /** Return Views Array **/
        return $views;
    }/** End getQuickViews **/

    /** 
    * getRandomArray Function
    *
    * Function gets X number of random indexes from array and creates new array 
    *
    * @param $arr - Array
    *
    * @param $number - INT
    *
    * @return Array
     **/
    public function getRandomArray( $arr, $number ){

        /** Get $number of random items out of $arr **/
        $tmp = array_rand( $arr, $number );

        /** Set New Array **/
        $newArray = array( );

        /** Loop through $tmp array and equate to new array **/
        foreach( $tmp as $k => $v ){
            $newArray[$k] = $v;
        }

        /** Return New Array **/
        return $newArray;
    }/** End getRandomArray **/

    /** 
    * generateAlias Function
    *
    * Function creates an alias for an Org/Project/Etc
    *
    * @param $table - String
    *
    * @param $aliasText - String
    *
    * @param $idColumn - String
    * 
    * @param $id - String
    * 
    * @param $aliasColumn - String
    *
    * @return String
     **/
    public function generateAlias( $table, $aliasText, $idColumn, $id = "", $aliasColumn = "alias" ){

        $alias = "";

        /** Replace '&' with 'and' **/
        $alias = str_replace("&amp;", "and", $aliasText);
        /** decode quotes **/
        $alias = htmlspecialchars_decode($alias, ENT_QUOTES);
        /** replace spaces with dash (-) **/
        $alias = str_replace("-", " ", $alias);
        /** Set to lower case **/
        $alias = preg_replace("/[^a-zA-Z0-9\s]/", "", $alias);
        /** Get rid of spaces and returns **/
        $alias = preg_replace('/[\r\n\s]+/xms', ' ', trim($alias));

        $alias = strtolower( str_replace(" ", "-", $alias) );

        /** Find any duplicates for the alias **/
        $duplicates = DB::table( $table )
                        ->where($aliasColumn, $alias)
                        ->count( );

        /** Check if there are duplicates **/
        if( $duplicates > 0 ){
            /** If there are get randome string and generate Alias Again **/
            $randStr = $this->rand_str( 4 );
            $alias   = $this->generateAlias( $table, $alias . "-" . $randStr, $idColumn, $id, $aliasColumn );
        }
        /** Return Alias **/
        return strtolower( $alias );
    }

    /** 
    * rand_str Function
    *
    * Get a random string that is $length long
    *
    * @param $length - Int
    *
    * @param $chars - String
    *
    * @return String
     **/
    private function rand_str( $length = 32, $chars = "abcdefghijklmnopqrstuvwxyz1234567890"){
        $chars_length = (strlen($chars) - 1);
        $string = $chars{rand(0, $chars_length)};
        for ($i = 1; $i < $length; $i = strlen($string)) {
            $r = $chars{rand(0, $chars_length)};
            if ($r != $string{$i - 1})
                $string .= $r;
        }
        return $string;
    }/** End rand_str **/

    /** 
    * checkKeyValueExists function
    *
    * Function that checks if a key/value combo exists
    *
    * @param $array - Array
    *
    * @param $key - String
    *
    * @param $value - Mixed
    *
    * @return Bool
     **/
    public static function checkKeyValueExists( $array, $key, $value ){

        $found = FALSE;

        /** Loop through array **/
        foreach( $array as $k => $v ){

            /** See if Value is an Array **/
            if( is_array( $v ) ){
                /** If so Recursively call function **/
                if( self::checkKeyValueExists( $v, $key, $value ) ){
                    $found = TRUE;
                }
            /** Otherwise, check if key and value are equal to $k & $v **/
            }else{
                if( $k == $key && $v == $value ){
                    $found = TRUE;
                }
            }
        }
        /** Return $found **/
        return $found;
    }/** End checkKeyValueExists **/

    /** 
    * getVideoFromIframe Function
    *
    * Function Extracts video URL from iframe tag
    *
    * @param $iframe - String
    *
    * @return Mixed
     **/
    public static function getVideoFromIframe( $iframe ){
        
        /** Explode $iframe on ' ' **/
        $iframeValues = explode(" ", $iframe);

        $videoLink = "";

        /** Loop through iframe Values **/
        foreach( $iframeValues as $item ){

            /** Explode $item on '=' **/
            $keyValue = explode("=", $item );

            /** See if keyValue has more than one value **/
            if( sizeof( $keyValue ) > 1 ){
                /** See if key is 'src' **/
                if( $keyValue[0] == "src" ){
                    /** Return Url **/
                    return( str_replace('"', "", $keyValue[1] ) );
                }
            }
        }
        /** Return False when not found **/
        return FALSE;
    }/** End getVideoFromIframe **/

    /** 
    * createImage function
    *
    * Function that creates a file and returns result
    *
    * @param $file - Object
    *
    * @param $params - Array
    *
    * @param $path - String
    *
    * @return Image Object
     **/
    public function createImage( $file, $params, $path){
        /** Return Image object **/
        return Image::make($file, $params)->save($path);
    }/** End createImage **/

    /** 
    * getVideoData Function
    *
    * Function retrieves Data from the video Url passed in
    *
    * @param $videoUrl - String
    *
    * @return Array
     **/
    public function getVideoData( $videoUrl ){

        /** Establish Video Patterns **/
        $youtubePattern         = "/youtube/";
        $youtubeShortPattern    = "/youtu.be/";
        $vimeoPattern           = "/vimeo/";

        $videoImg = "";
        $videoLink = "";

        /** Check to see if Youtube Patters match $videourl **/
        if( preg_match($youtubePattern, $videoUrl ) || preg_match( $youtubeShortPattern, $videoUrl ) ){
            /** Set Video Type **/
            $videoType = "youtube";

            /** Get Video Id **/
            $videoId = \Youtube::parseVidFromURL( $videoUrl );

            $video = null;

            /** Try and Grab Video Data from youtube **/
            try{
                $video = \Youtube::getVideoInfo( $videoId );
            }catch( \Exception $e ){
                dd( $e );
            }
            
            /** Chck if Video is Falsey **/
            if( ! $video ){
                /** Return Array with indicating failure **/
                return array("status"=>false,"msg"=>"Not a valid Youtube Video.");
            }
            /** Return Video Data - thumbnail, $url **/
            return array($video->snippet->thumbnails->default->url,  $this->getVideoFromIframe( $video->player->embedHtml ) );

        /** Test Vimeo Pattern against VideoUrl **/
        }else if( preg_match( $vimeoPattern, $videoUrl ) ){

            /** Set video Type to 'vimeo' **/
            $videoType = "vimeo";

            /** set up cUrl **/
            $ch = curl_init( );

            /** Vimeo API Url **/
            $vimeoOembedUrl = "https://vimeo.com/api/oembed.json?url=" . urlencode($videoUrl);

            /** Set cUrl options **/
            curl_setopt($ch, CURLOPT_URL, $vimeoOembedUrl );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            /** decode vimeo response to array **/
            $vimeoResponse = json_decode( curl_exec( $ch ) );

            /** Check if vimeoResponse is NULL **/
            if( is_null( $vimeoResponse ) ){
                /** If so return array indicating failure **/
                return array("status"=>false, "msg"=>"The Video is not validated through Vimeo.");
            }
            /** Close cUrl **/
            curl_close( $ch );

            /** Return Video Data - thumbnail, url **/
            return array( $vimeoResponse->thumbnail_url, $this->getVideoFromIframe( $vimeoResponse->html ) );
        /** Other video source **/
        }else{
            /** Return array indicating failure **/
            return array("status"=>false, "msg"=>"Not a valid video to upload. Please use: Youtube or Vimeo");
        }
    }/** end getVideoData **/

    /** 
    * user_all_data function
    * 
    * Function return all user data
    *
    * @return Array
     **/
    public function user_all_data() {
        /** Get user Object **/
        $userObj = new UserRepository( );
        $user = null;
        $userImage = "";
        $addressData = null;
        $user_all_data = array();
        /** Authenticate user **/
        $user = \Auth::user();
        /** Check if user returns not empty  **/
        if (!empty($user)) {
            /** set userData to empty Array **/
            $userData = array(
            );
        }
        /** Get userImage **/
        $userImage = $userObj->getProfileImage($user->user_id, $user->user_photo_id);
        /** Get user Address **/
        $addresses = $user->getAddresses;
        /** Loop through each address **/
        foreach ($addresses as $address) {
            $key = $address->id . "|" . $address->type . "|" . $address->addrLine1 . "|" . $address->addrLine2 . "|" . $address->city . "|" . $address->stateId . "|" . $address->zip . "|" . $address->countryId;
            $value = $address->addrLine1 . " " . $address->city . ", " . $address->state . " " . $address->zip;
            $addressData[$key] = array(
                "address" => $value,
                "type" => $address->type
            );
        }
        $user_all_data = array('user' => $user, 'userImg' => $userImage, 'userData' => $userData);
//        Session::put('user_all_data', $user_all_data);

        /** Return data  **/
        return $user_all_data;
    }/** end user_all_data **/

    /** 
    * generateUsername Function
    *
    * Function generates the user name
    *
    * @param $alias_text - string
    *
    * @return String
     **/
    function generateUsername($alias_text) {
        //format alias
        $alias = str_replace("&amp;", "and", $alias_text);
        $alias = htmlspecialchars_decode($alias, ENT_QUOTES);
        $alias = str_replace("-", " ", $alias);
        $alias = preg_replace("/[^a-zA-Z0-9\s]/", "", $alias);
        $alias = preg_replace('/[\r\n\s]+/xms', ' ', trim($alias));
        $alias = strtolower(str_replace(" ", "-", $alias));
        return strtolower($alias);
    }

    /** 
    * saveFile Function
    *
    * Function takes a file and saves it and returns file Id
    *
    * @param $file - File
    *
    * @param $type - String
    *
    * @param $width - Int
    *
    * @param $height - Int
    *
    * @param $userId - Int
    *
    * @return Mixed
     **/
    public function saveFile( $file, $type, $width = 0, $height = 0, $userId){

        /** Get File Extension **/
        $extension      = $file->getClientOriginalExtension();
        /** Get File Mime Type **/
        $mime_type      = $file->getClientMimeType();
        /** Get File Type **/
        $file_type      = $file->getType();
        /** Get File Size **/
        $file_size      = $file->getClientSize();
        /** Get original File Name **/
        $orig_file_name = $file->getClientOriginalName( );

        /** Create directory **/
        $directory = '/images/' . $type;

        /** See if directory exists **/
        if ( ! is_dir( public_path('images/' . $type ) ) ){
            /** if not, create it **/
            @mkdir( public_path('images/' . $type ) );
        }

        /** Get File Data  **/
        $fileData = getimagesize($file);

        /** Check if fileData is truthey **/
        if( $fileData ){

            /** Check if width/height equals 0 **/
            if( $width == 0 && $height == 0 ){
                /** If so set width/height equal to fileData **/
                $width      = $fileData[0];
                $height     = $fileData[1];    
            }
            
            /** Generate file Name **/
            $filename   = md5("pwi-" . $type . "-" . $orig_file_name . time( )) . "." . $extension;

            $upload;

            /** Call createImage **/
            $upload = $this->createImage( $file, array( 'width'=>$width, 'height'=>$height, 'crop'=>false, 'grayscale'=>false), 'images/' . $type . '/' . $filename);

            /** Check if upload is truthey **/
            if( $upload ){
                /** Create File **/
                $file_data = new Files( );

                /** Set File Data **/
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
                $file_data->created_by      = $userId;

                /** Check if file is saved **/
                if( $file_data->save( ) ){
                    /** Return File Id **/
                    return( $file_data->file_id ); 
                }else{
                    /** Return Array indicating Failure **/
                    return( array( "status" => FALSE, "error" => "There as a problem saving the file.") );
                }
            }else{
                /** Return Array indicating Failure **/
                return( array("status"=>FALSE, "error"=>"There was an issue uploading the file.") );
            }
        }
    }/** end saveFile **/

    public static function getCurrentProducts( $orgId ){

        $org = Organizations::find( $orgId );

        return $org->products;
    }

    public static function getCurrentProductsCount( $orgId ){

        return Organizations::find( $orgId )->products->count( );
    }

    public static function getArchivedProducts( $orgId ){

        $org = Organizations::find( $orgId );

        return $org->archivedProducts;
    }
    
    public static function getProductCategories( ){
	    
	    $categories = ProductCategories::all( );
	    
	    return $categories;
    }
}
