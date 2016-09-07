<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository as UserRepository;
use App\Repositories\PaymentRepository as Payments;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProjectIncentives;
use App\ProjectMaster;
use App\Http\Requests;
use App\Http\Helper;
use App\EmailQueue;
use Carbon\Carbon;
use App\Projects;
use App\Country;
use App\States;
use App\Follow;
use App\Files;
use Config;
use Agent;
use Input;
use Mail;
use Log;
use DB;

setlocale(LC_MONETARY, 'en_US');

class CrowdFundingController extends Controller {

    //

    private $scriptPage;
    private $user = null;
    private $userImage = "";
    private $helper;
    private $request;
    private $userObj;

    public function __construct(UserRepository $userObj, Request $request, Helper $helper) {
        $this->scriptPage = "scripts.crowdfunding";
        $this->helper = $helper;

        $this->user = $request->instance()->query('user');
        $this->request = $request;
        $this->userObj = $userObj;
    }

    public function index() {

        $list = array();

        $projects = Projects::where("project_featured", "=", "Y")
                ->where("project_status", "=", "active")
                ->where("project_icon", ">", "0")
                ->leftJoin("pwi_files AS ICON", "ICON.file_id", "=", "pwi_projects.project_icon")
                ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_projects.org_id")
                ->select("pwi_projects.project_id", "pwi_projects.project_title AS title", "pwi_projects.project_alias", "pwi_projects.project_fund_goal as fundGoal", "pwi_projects.project_amout_raised as amtRaised", "pwi_projects.project_end_date", "ICON.file_path AS icon", "ORG.org_name")
                ->take(8)
                ->get();

        foreach ($projects as $project) {
            $causes = $project->causes;
            $daysLeft = Carbon::createFromTimeStamp(strtotime($project->project_end_date))->diffInDays();

            $projectPercentageComplete = 0;

            if ((int) $project->amtRaised > 0) {
                $projectPercentageComplete = (((int) $project->amtRaised / (int) str_replace(",", "", $project->fundGoal)) * 100);
            }




            $countries = $project->countries;

            $tmp = $project->toArray();

            if (file_exists(public_path() . Config::get("globals.prjImgPath") . $tmp["icon"])) {
                $tmp["icon"] = Config::get("globals.prjImgPath") . $tmp["icon"];
            } else {
                $tmp["icon"] = "/images/cfPlaceholder.png";
            }

            $tmp["fundGoal"] = money_format('%(#5n', (int) $tmp["fundGoal"]);
            $tmp["amtRaised"] = money_format('%(#5n', (int) $tmp["amtRaised"]);
//            $tmp["fundGoal"] = (int) $tmp["fundGoal"];
//            $tmp["amtRaised"] = (int) $tmp["amtRaised"];

            $tmp["causes"] = $causes;
            $tmp["countries"] = $countries;
            $tmp["daysleft"] = $daysLeft;
            $tmp["percentage"] = $projectPercentageComplete;

            $list[] = $tmp;
        }
        $meta = $this->helper->getMetaData("general", "crowdfunding")->toArray();

        $initialPull = 8;
        $payload = 8;

        $view = "pages.crowdfunding.index";

        if (Agent::isMobile() && ! Agent::isTablet( ) ) {
            $view = "mobile.pages.crowdfunding.index";
        }

        return view($view)->with([
                    "projects"      => $list,
                    "initialPull"   => $initialPull,
                    "payload"       => $payload,
                    "scriptPage"    => $this->scriptPage,
                    "meta"          => $meta[0]
        ]);
    }

    public function view($alias) {

        DB::connection()->enableQueryLog();

        $project;

        try {

            $project = Projects::where("project_alias", "=", $alias)
                    ->leftJoin("pwi_files AS FILE", "FILE.file_id", "=", "pwi_projects.project_icon")
                    ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_projects.org_id")
                    ->leftJoin("pwi_org_settings as ORGSET", "ORGSET.org_id", "=", "ORG.org_id")
                    ->leftJoin("pwi_project_donation_master AS DON", "DON.project_id", "=", "pwi_projects.project_id")
                    ->select(DB::raw("pwi_projects.*, FILE.file_path as coverphoto, ORG.org_name, ORG.org_alias, ORGSET.paypal_username as paypal, ORGSET.fk_payment_gateway as payment_gateway, COUNT( DISTINCT( DON.project_id) ) as funders"))
                    ->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }

        $incentives = $project->incentives;

        $raisedAmt = $project->project_amout_raised;

        $fundGoal = $project->project_fund_goal;

        $hasGateway = TRUE;

        $paypal_un = "";

        $payment_gateway = 0;

        if (empty($project->payment_gateway) && ( $project->payment_gateway != 3 ) ) {
            $hasGateway = FALSE;
        } else {
            $paypal_un = $project->paypal;

            $payment_gateway = $project->payment_gateway;
        }

        $percentage = 0;

        if ((int) $raisedAmt > 0) {
            $percentage = ( ( (int) $raisedAmt / (int) str_replace(",", "", $fundGoal) ) * 100 );
        }

        $countries = $project->countries;

        $reviews = $project->reviews;

        $userData = null;
        $isFollowing = FALSE;

        if (!is_null($this->user)) {
            $isFollowing = $this->userObj->isFollowing($this->user, "project", $project->project_id);
        }

        $meta = $this->helper->getMetaData("individual", "crowdfunding");

        $view = "pages.crowdfunding.project";

        if (Agent::isMobile() && ! Agent::isTablet( ) ) {
            $view = "mobile.pages.crowdfunding.project";
        }

        return view($view)->with([
            "project"           => $project,
            "incentives"        => $incentives,
            "causes"            => $this->helper->parseCauses($project->causes),
            "impactCountries"   => $countries,
            "reviews"           => $reviews,
            "imgPath"           => Config::get("globals.prjImgPath"),
            "percentage"        => $percentage,
            "daysLeft"          => Carbon::createFromTimeStamp(strtotime($project->project_end_date))->diffInDays(),
            "scriptPage"        => $this->scriptPage,
            "raised"            => money_format('%(#10n', (int) $raisedAmt),
            "goal"              => money_format('%(#10n', (int) $fundGoal),
            "paypal_un"         => $paypal_un,
            "meta"              => $this->helper->parseIndMetaData($meta[0], $project->project_title),
            "following"         => $isFollowing,
            "hasGateway"        => $hasGateway,
            "payment_gateway"   => $payment_gateway
        ]);
    }

    public function fund($alias, Request $request) {

        //DB::connection( )->enableQueryLog( );

        $project;

        // try to fetch this org - if Exception abort to 404
        try {
            $project = Projects::where("project_alias", "=", $alias)
                    ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_projects.org_id")
                    ->select("project_id", "project_title", "ORG.org_name", "ORG.org_id")
                    ->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }

        $donationAmt = 0.00;

        $chosenIncentive = "";

        $gateway = DB::table("pwi_org_settings")
                ->where("org_id", "=", $project->org_id)
                ->get();

        $paypal_un = "";
        $payment_gateway = 0;

        if ( sizeof( $gateway ) == 0 ) {
            return redirect()->action("CrowdFundingController@view", $alias);
        } else {
            $paypal_un      = $gateway[0]->paypal_username;
            $payment_gateway = $gateway[0]->fk_payment_gateway;
        }

        if ($request->session()->has('incentiveId')) {
            $chosenIncentive = $request->session()->get('incentiveId');
            //erase incentive id from session storage
            //$request->session->forget('incentiveId');
        }

        //check if there is a session variable that holds donation amount
        if ($request->session()->has('donationAmt')) {

            //retieve donation amount
            $donationAmt = $request->session()->get('donationAmt');
            //erase donation amount from session storage
            //$request->session->forget('donationAmt');
        } else {
            $donationAmt = "";
        }

        //get meta data for page
        $meta = $this->helper->getMetaData("individual", "donations");

        $years = array( );

        for( $i = Carbon::now( )->year ; $i < ( Carbon::now( )->year + 8 ) ; $i++ ){

            $yearAbbr = substr($i, -2);
            $years[$yearAbbr] = $i; 
        }

        return view("pages.crowdfunding.fund")->with([
            "project"           => $project,
            "chosenIncentive"   => $chosenIncentive,
            "incentives"        => $project->incentives,
            "amount"            => $donationAmt,
            "paypal_un"         => $paypal_un,
            "scriptPage"        => $this->scriptPage,
            "meta"              => $this->helper->parseIndMetaData($meta[0], $project->project_title),
            "years"             => $years,
            "payment_gateway"   => $payment_gateway,
        ]);
    }

    public function more() {

        if ($this->request->ajax()) {
            $count = Input::get("payload");
            $skip = Input::get("next");

            $list = array();

            $projects = Projects::where("project_featured", "=", "Y")
                    ->where("project_status", "=", "active")
                    ->where("project_icon", ">", "0")
                    ->leftJoin("pwi_files AS ICON", "ICON.file_id", "=", "pwi_projects.project_icon")
                    ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_projects.org_id")
                    ->select("pwi_projects.project_id", "pwi_projects.project_title AS title", "pwi_projects.project_alias", "pwi_projects.project_fund_goal as fundGoal", "pwi_projects.project_amout_raised as amtRaised", "pwi_projects.project_end_date", "ICON.file_path AS icon", "ORG.org_name")
                    ->take($count)
                    ->skip($skip)
                    ->get();

            foreach ($projects as $project) {

                $daysLeft = Carbon::createFromTimeStamp(strtotime($project->project_end_date))->diffInDays();

                $projectPercentageComplete = 0;

                if ((int) $project->amtRaised > 0) {
                    $projectPercentageComplete = (((int) $project->amtRaised / (int) str_replace(",", "", $project->fundGoal)) * 100);
                }

                $causes = $project->causes;

                $countries = $project->countries;

                $tmp = $project->toArray();

                if (file_exists(public_path() . Config::get("globals.prjImgPath") . $tmp["icon"])) {
                    $tmp["icon"] = Config::get("globals.prjImgPath") . $tmp["icon"];
                } else {
                    $tmp["icon"] = "/images/cfPlaceholder.png";
                }

                $tmp["fundGoal"] = money_format('%(#10n', (int) $tmp["fundGoal"]);
                $tmp["amtRaised"] = money_format('%(#10n', (int) $tmp["amtRaised"]);

                $tmp["causes"] = $causes;
                $tmp["countries"] = $countries;
                $tmp["daysleft"] = $daysLeft;
                $tmp["percentage"] = $projectPercentageComplete;

                $list[] = $tmp;
            }

            $count = sizeof($list);

            $list["count"] = $count;
            $list["path"] = Config::get("globals.prjImgPath");


            echo json_encode($list);
            die;
        }
    }

    public function storeFund(Request $request) {

        $donationAmt = 0.00;

        if (Input::has("amount")) {
            $donationAmt = Input::get("amount");
        }

        if (Input::has("incentive")) {
            $request->session()->put('incentiveId', Input::get('incentive'));
        }

        $request->session()->put('donationAmt', $donationAmt);
    }

    public function validateFundProject(Request $request) {

        $messages = array(
            'first_name.required'           => "Your First Name is Required.",
            'last_name.required'            => "Your Last Name is Required.",
            'email.required'                => "Your Email is Required.",
            'email.email'                   => "Your Email is not formatted properly.",
            'shippingAddress1.required_if'  => 'Shipping Address is required',
            'shippingCity.required_if'      => 'Shipping City is required',
            'shippingState.required_if'     => 'Shipping State is required',
            'shippingState.exists'          => 'Invalid Shipping State',
            'shippingZip.required_if'       => 'Shipping Zip is required',
            'shippingZip.max'               => 'Shipping Zip must be five numbers',
            'shippingZip.min'               => 'Shipping Zip must be five numbers',
            'shippingCountry.required_if'   => 'Invalid Shipping State',
            'shippingCountry.exists'        => 'Country is invalid',
            'billingAddress1.required'      => 'Billing Address is required.',
            'billingCity.required'          => 'Billing City is required.',
            'billingState.required'         => 'Billing State is required',
            'billingState.exists'           => 'Invalid Billing State',
            'billingZip:required'           => 'Billing Zip is required.',
            'billingZip:max'                => 'Billing Zip must be five numbers.',
            'billingCountry:required'       => 'Billing Country is required.',
            'billingCountry:exists'         => 'Invalid Billing Country.',
            'cc_number.required'            => 'Credit Card Number is required.',
            'cc_number.credit_card_number'  => 'Credit Card Number is Invalid.',
            'ccv.required'                  => 'Credit Card CCV is required.',
            'ccv.credit_card_cvc'           => 'Invalid CVC',
            'exp_date_month.CreditCardDate' => 'Invalid Expiration Date.'
        );

        $validationList = array(
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => 'required|email',
            'billingAddress1'   => 'required',
            'billingCity'       => 'required',
            'billingState'      => 'required|exists:pwi_state,state_id',
            'billingZip'        => 'required|max:5',
            'billingCountry'    => 'required|exists:pwi_country,country_id'
        );

        if(Input::get('showShipping') == 'Y') {
            $validationList['showShipping']     = 'required';
            $validationList['shippingAddress1'] = 'required_if:showShipping,Y';
            $validationList['shippingCity']     = 'required_if:showShipping,Y';
            $validationList['shippingState']    = 'required_if:showShipping,Y|exists:pwi_state,state_id';
            $validationList['shippingZip']      = 'required_if:showShipping,Y|max:5|min:5';
            $validationlist['shippingCountry']  = 'required_if:showShipping,Y|exists:pwi_country,country_id';
        }

        if( Input::get('payment_gateway') != 3 ){
            $validationList['cc_number']        = 'required|CreditCardNumber';
            $validationList['ccv']              = 'required|CreditCardCvc:' . Input::get('cc_number');
            $validationList['name_on_card']     = 'required';
            $validationList['exp_date_month']   = 'CreditCardDate:' . Input::get('exp_date_year');
        } 

        $this->validate($request, $validationList, $messages);
    }

    public function getProjectsForCountry($alias) {

        $country = \App\Country::where("country_alias", "=", $alias)->firstOrFail();

        $list = array();

        $projects = Projects::where("project_status", "=", "active")
                ->where("PRJCD.project_cause_type", "=", "country")
                ->where("PRJCD.project_cause_status", "=", "active")
                ->where("CTRY.country_alias", "=", $alias)
                ->leftJoin("pwi_files AS ICON", "ICON.file_id", "=", "pwi_projects.project_icon")
                ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_projects.org_id")
                ->leftJoin("pwi_project_cause_details AS PRJCD", "PRJCD.project_id", "=", "pwi_projects.project_id")
                ->leftJoin("pwi_country AS CTRY", "CTRY.country_id", "=", "PRJCD.project_cause_item_id")
                ->select("pwi_projects.project_id", "pwi_projects.project_title AS title", "pwi_projects.project_alias", "pwi_projects.project_fund_goal as fundGoal", "pwi_projects.project_amout_raised as amtRaised", "pwi_projects.project_end_date", "ICON.file_path AS icon", "ORG.org_name", "CTRY.country_name")
                ->get();

        foreach ($projects as $project) {

            $daysLeft = Carbon::createFromTimeStamp(strtotime($project->project_end_date))->diffInDays();

            $projectPercentageComplete = 0;

            if ((int) $project->amtRaised > 0) {
                $projectPercentageComplete = (((int) $project->amtRaised / (int) str_replace(",", "", $project->fundGoal)) * 100);
            }

            $causes = $project->causes;

            $countries = $project->countries;

            $tmp = $project->toArray();

            $tmp["fundGoal"] = money_format('%(#10n', (int) $tmp["fundGoal"]);
            $tmp["amtRaised"] = money_format('%(#10n', (int) $tmp["amtRaised"]);

            $tmp["causes"] = $causes;
            $tmp["countries"] = $countries;
            $tmp["daysleft"] = $daysLeft;
            $tmp["percentage"] = $projectPercentageComplete;

            $list[] = $tmp;
        }

        $meta = $this->helper->getMetaData("individual", "search_results_page");

        return view("pages.country.crowdfunding")->with([
                    "projects" => $list,
                    "alias" => $alias,
                    "country_name" => $country->country_name,
                    "iconPath" => Config::get("globals.prjImgPath"),
                    "scriptPage" => $this->scriptPage,
                    "user" => $this->user,
                    "userImg" => $this->userImage,
                    "meta" => $this->helper->parseSearchMetaData($meta[0], "Crowdfunding", $country->country_name),
        ]);
    }

    public function getProjectsForCause($alias) {

        $cause = \App\Causes::where("cause_alias", "=", $alias)->firstOrFail();

        $list = array();

        $projects = Projects::where("project_status", "=", "active")
                ->where(function( $query ) {
                    $query->where("PRJCD.project_cause_type", "=", "cause")
                    ->orWhere("PRJCD.project_cause_type", "=", "subcause");
                })
                ->where("PRJCD.project_cause_status", "=", "active")
                ->where("CSE.cause_alias", "=", $alias)
                ->leftJoin("pwi_files AS ICON", "ICON.file_id", "=", "pwi_projects.project_icon")
                ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_projects.org_id")
                ->leftJoin("pwi_project_cause_details AS PRJCD", "PRJCD.project_id", "=", "pwi_projects.project_id")
                ->leftJoin("pwi_causes AS CSE", "CSE.cause_id", "=", "PRJCD.project_cause_item_id")
                ->select("pwi_projects.project_id", "pwi_projects.project_title AS title", "pwi_projects.project_alias", "pwi_projects.project_fund_goal as fundGoal", "pwi_projects.project_amout_raised as amtRaised", "pwi_projects.project_end_date", "ICON.file_path AS icon", "ORG.org_name", "CSE.cause_name")
                ->get();

        foreach ($projects as $project) {

            $daysLeft = Carbon::createFromTimeStamp(strtotime($project->project_end_date))->diffInDays();

            $projectPercentageComplete = 0;

            if ((int) $project->amtRaised > 0) {
                $projectPercentageComplete = (((int) $project->amtRaised / (int) str_replace(",", "", $project->fundGoal)) * 100);
            }

            $causes = $project->causes;

            $countries = $project->countries;

            $tmp = $project->toArray();

            $tmp["fundGoal"] = money_format('%(#10n', (int) $tmp["fundGoal"]);
            $tmp["amtRaised"] = money_format('%(#10n', (int) $tmp["amtRaised"]);

            $tmp["causes"] = $causes;
            $tmp["countries"] = $countries;
            $tmp["daysleft"] = $daysLeft;
            $tmp["percentage"] = $projectPercentageComplete;

            $list[] = $tmp;
        }

        $meta = $this->helper->getMetaData("individual", "search_results_page");

        return view("pages.causes.crowdfunding")->with([
                    "projects" => $list,
                    "alias" => $alias,
                    "cause_name" => $cause->cause_name,
                    "iconPath" => Config::get("globals.prjImgPath"),
                    "scriptPage" => $this->scriptPage,
                    "user" => $this->user,
                    "userImg" => $this->userImage,
                    "meta" => $this->helper->parseSearchMetaData($meta[0], "Crowdfunding", $cause->cause_name),
        ]);
    }

    public function pendingDonation(Request $request) {

        $data = $request->all();

        if (Input::get('saveShippingAddress') == "true") {
            $this->helper->saveShippingAddress( $data );
        }

        if (Input::get('saveBillingAddress') == "true") {
            $this->helper->saveBillingAddress( $data );
        }

        unset( $data["saveBillingAddress"] );
        unset( $data["saveShippingAddress"] );
        unset( $data["hasShippingData"] );

        $data["donation_status"] = "1";
        $data["donated_date"] = Carbon::now();

        $master = ProjectMaster::create( $data );

        $masterId = $master->donation_id;

        $projectId = $data["project_id"];

        $donationAmt = $data["donation_amount"];

        $state      = States::find( $data['billing_state'] );
        $country    = Country::find( $data['billing_country'] );

        $data["stateAbbr_billing"]  = $state->state_code;
        $data["countryCode_billing"]= $country->country_iso_code;

        $transactionData;

        switch( $data["payment_gateway"] ){
            case 1:
                $payments = new Payments( $this->request );

                $transactionData = $payments->processPayPalPro( $data, "project", $masterId);
                
            break;
            case 2:
                $payments = new Payments( $this->request );

                $transactionData = $payments->processTransnational($data, "project", $masterId);

            break;
            case 3:
                return response()->json(['id' => $masterId]);
            break;
            case 4:
                $payments = new Payments( $this->request );

                $transactionData = $payments->processAuthorizeDotNET( $data, "project", $masterId);
            break;
        }
        
        if( $transactionData["status"] == 1 ){
            $donation = ProjectMaster::find( $masterId );

            //set donation to 1
            $donation->donation_status = 1;
            //set transaction id
            $donation->transaction_id  = $transactionData["transactionId"];
            
            //dave donation
            $donation->save( );

            $project = Projects::where("project_id", "=", $projectId )
                        ->leftJoin('pwi_organization as ORG', 'ORG.org_id', '=', 'pwi_projects.org_id')
                        ->select('pwi_projects.*', 'ORG.org_email')
                        ->first( );

            $fundGoal   = $project->project_fund_goal;
            $amtRaised  = $project->project_amout_raised;

            $amtRaised  = $amtRaised + $donationAmt;

            $project->project_amout_raised = $amtRaised;

            if ( ( (int) $amtRaised >= (int) $fundGoal ) && $project->project_fund_met == 0 ) {
                $project->project_fund_met = 1;

                $mail = Mail::send("email.project-target-met", [
                    "projectName" => $project->project_title,
                    "projectGoal" => $project->project_fund_goal,
                    "amtRaised" => $amtRaised
                        ], function( $m ) use( $project ) {
                    $m->to($project->org_email)->subject("Your Project: " . $project->project_title . " has met it's Funding Goal!");
                });
            }

            $project->save( );

            if( ! empty( $data["incentive_id"] ) && $data["incentive_id"] > 0 ){

                $incentive = ProjectIncentives::find( $data["incentive_id"] );

                $incentive->project_available_incentive_count--;

                $incentive->save( );
            }

             //Add donation to email queue.
            $emailQueue = array(
                "type" => "donation",
                "type_id" => $masterId
            );

            try {
                EmailQueue::create($emailQueue);
            } catch (\Exception $e) {
                Log::info("catch for EmailQueue");
                Log::info($e);
            }

            return response()->json(['status' => true, 'txnId' => $transactionData["transactionId"]]); 
        }else{
            return response( )->json(['status' => false, 'text' => $transactionData["result-text"]]);
        }
        
    }

    public function ipn() {

        //DB::connection()->enableQueryLog();

        $data = $this->request->all();
        $donatedAmt = (int) $data["mc_gross"];
        $project;

        //Log::info("in Crowdfunding IPN");

        DB::table("pwi_project_donation_master")
                ->where("donation_id", $data["custom"])
                ->update(["donation_status" => 1, "transaction_id" => $data["txn_id"], "donation_amount" => (int) $data["mc_gross"]]);

        $project = DB::table("pwi_project_donation_master")
                ->leftJoin("pwi_projects as PRJ", "PRJ.project_id", "=", "pwi_project_donation_master.project_id")
                ->leftJoin("pwi_organization as ORG", "ORG.org_id", "=", "PRJ.org_id")
                ->where("donation_id", "=", $data["custom"])
                ->select("PRJ.project_id", "PRJ.project_title", "project_amout_raised", "project_fund_goal", "PRJ.project_fund_met", "ORG.org_email")
                ->get();

        $project = $project[0];

        if (!is_null($project)) {

            $fundGoal = $project->project_fund_goal;
            $amtRaised = $project->project_amout_raised;

            $amtRaised += $donatedAmt;

            $project->project_amout_raised = $amtRaised;

            $tmp = Projects::find($project->project_id);

            $tmp->project_amout_raised = $amtRaised;

            if (( (int) $amtRaised >= (int) $fundGoal ) && $project->project_fund_met == 0) {

                $tmp->project_fund_met = 1;

                $mail = Mail::send("email.project-target-met", [
                    "projectName" => $project->project_title,
                    "projectGoal" => $project->project_fund_goal,
                    "amtRaised" => $amtRaised
                        ], function( $m ) use( $project ) {
                    $m->to($project->org_email)->subject("Your Project: " . $project->project_title . " has met it's Funding Goal!");
                });
            }

            $tmp->save();

            $emailQueue = array(
                "type" => "crowdfunding",
                "type_id" => $data["custom"]
            );

            try {
                EmailQueue::create($emailQueue);
            } catch (\Exception $e) {
                Log::info("catch for EmailQueue");
                Log::info($e);
            }
        }
    }

    private function checkFundGoalMet( $amtRaised, $goal, $project ){


    }

}
