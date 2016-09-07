<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository as UserRepository;
use App\Repositories\PaymentRepository as Payments;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SuggestNonProfit;
use App\Http\Requests;
use App\Organizations;
use App\Http\Helper;
use App\UserAddress;
use App\Background;
use App\EmailQueue;
use Carbon\Carbon;
use App\Donations;
use App\Country;
use App\Causes;
use App\Follow;
use App\Rating;
use App\States;
use App\Files;
use Config;
use Agent;
use Input;
use Auth;
use Mail;
use Log;
use DB;

class IndexController extends Controller {

    /**
     * @var $user	
     */
    private $user = null;

    /**
     * @var $userImage   
     */
    private $userImage = "";

    /**
     * @var $request
     */
    private $request;

    /**
     * __construct
     *
     * @param UserRepository object
     *
     * @param Request $request
     *
     */
    function __construct(UserRepository $userObj, Request $request, Helper $helper) {


        $this->user = $request->instance()->query('user');

        $this->helper = $helper;

        $this->request = $request;
    }

    /**
     * index
     *
     * @return View - pages.index
     *
     */
    public function index() {

        $homepageImagePath = Config::get("globals.hpBgPath");

        /**
        * Get a random image from the background table
        * that is supposed to show between two dates
        */
        $background = Background::where('active', '=', 1)
                ->whereRaw('start_date < NOW( )')
                ->whereRaw('end_date > NOW( )')
                ->orderByRaw("RAND( )")
                ->limit(1)
                ->get();

        /**
        * Check if the background return has any rows
        * If not, get random background image
        */
        if ($background->isEmpty()) {

            $background = Background::where('active', '=', 1)
                    ->whereRaw('start_date IS NULL')
                    ->orderByRaw("RAND( )")
                    ->limit(1)
                    ->get();
        }

        /**
        * Check to see if the background pulled has a link associated with it
        * If so, replace the pattern ##URL## in the description with the link
        * If not, just show the description
        */
        if (!is_null($background[0]["link"])) {
            $backgroundDescription = str_replace("##URL##", $background[0]["link"], $background[0]["description"]);
        } else {
            $backgroundDescription = $background[0]["description"];
        }

        $backgroundImagePath = $homepageImagePath . $background[0]["file_path"];

        $metaData = $this->helper->getMetaData("general", "home")->toArray();

        $view = "pages.index";

        if (Agent::isMobile() && ! Agent::isTablet( ) ) {
            $view = "mobile.pages.index";
        }

        return view($view)->with([
                    "desc" => $backgroundDescription,
                    "path" => $backgroundImagePath,
                    "meta" => $metaData[0],
        ]);
    }

    /**
     * passThru
     *
     * @param Reqeust $request
     *
     * @return Redirect
     *
     */
    public function passThru(Request $request) {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric|exists:pwi_users,user_id'
        ]);

        if ($validator->fails()) {
            //redirect to home page.
            \Auth::logout();
            return redirect('/');
        } else {

            $id = Input::get('id');
            $token = Input::get('_token');

            $hashed = \Hash::make($token);

            DB::table("pwi_portal")->insert(["session_id" => $token, "user_id" => $id]);

            return redirect("https://portal.projectworldimpact.com/portal/pass/?id=" . $id . "&hash=" . $hashed);
        }
    }

    /**
    * pending Donation function
    *
    * this will be used for country, cause and organization donations
    *
    * @param Request object
    *
    * @return JSON object
    */
    public function pendingDonation(Request $request) {

        //Get all data from request
        $data = $request->all();

        //Check if the user is saving their billing address
        if (Input::get('saveAddress') == "true") {
            $this->helper->saveBillingAddress( $data );
        }

        $donation_record = new Donations;

        $donation_record->user_id               = $data["user_id"];
        $donation_record->item_id               = $data["item_id"];
        $donation_record->email                 = $data["email"];
        $donation_record->item_type             = $data["item_type"];
        $donation_record->donation_amount       = $data["donation_amount"];
        $donation_record->billing_full_name     = $data["billing_full_name"];
        $donation_record->billing_address_line1 = $data["billing_address_line1"];
        $donation_record->billing_address_line2 = $data["billing_address_line2"];
        $donation_record->billing_city          = $data["billing_city"];
        $donation_record->billing_state         = $data["billing_state"];
        $donation_record->billing_zip           = $data["billing_zip"];
        $donation_record->billing_country       = $data["billing_country"];
        $donation_record->donated_date          = Carbon::now( );
        $donation_record->payment_gateway       = $data["payment_gateway"];
        $donation_record->donation_status       = "P";

        $donation_record->save( );

        $donation_id = $donation_record->donation_id;

        $state      = States::find( $data['billing_state'] );
        $country    = Country::find( $data['billing_country'] );

        $data["stateAbbr_billing"]  = $state->state_code;
        $data["countryCode_billing"]= $country->country_iso_code;

        switch( $data["payment_gateway"] ){
            case 1:
                $payments = new Payments( $this->request );

                $transactionData = $payments->processPayPalPro( $data, $data["item_type"], $donation_id);
                
            break;
            case 2:
                $payments = new Payments( $this->request );

                $transactionData = $payments->processTransnational($data, $data["item_type"], $donation_id);
            break;
            case 3:
                return response( )->json(['id' => $donation_id]);
            break;
            case 4:
                $payments = new Payments( $this->request );

                $transactionData = $payments->processAuthorizeDotNET( $data, $data["item_type"], $donation_id );
            break;
        }

        if( $transactionData["status"] == 1 ){
            $donation = Donations::find( $donation_id );

            $item_id    = $donation->item_id;
            $item_type  = $donation->item_type;

            //set donation to 1
            $donation->donation_status = 1;
            //set transaction id
            $donation->transaction_id  = $transactionData["transactionId"];
            
            //dave donation
            $donation->save( );

            /**
            * Find which Item type and increment the amount donated. 
            */
            switch( $item_type ){
                case "country":
                    $country    = Country::find( $item_id );

                    $country->country_raised_amt += (int)$data["donation_amount"];

                    $country->save( );
                break;

                case "cause":
                    $cause      = Causes::find( $item_id );

                    $cause->cause_raised_amt += (int)$data["donation_amount"];

                    $cause->save( );
                break;

                case "organization":
                    $org        = Organizations::find( $item_id );

                    $org->org_amt_raised += (int)$data["donation_amount"];

                    $org->save( );
                break;
            }

             //Add donation to email queue.
            $emailQueue = array(
                "type" => "donation",
                "type_id" => $donation_id
            );

            try {
                EmailQueue::create($emailQueue);
            } catch (\Exception $e) {
                Log::info("catch for EmailQueue");
                Log::info($e);
            }

            return response()->json(['status' => true, 'txnId' => $transactionData["transactionId"], 'donationId' => $donation_id]); 
        }else{

            $donation = Donations::find( $donation_id );

            $item_id    = $donation->item_id;
            $item_type  = $donation->item_type;

            //set donation to 1
            $donation->donation_status = 1;
            //set transaction id
            $donation->transaction_id  = $transactionData["transactionId"];
            
            //save donation
            $donation->save( );

            return response( )->json(['status' => false, 'text' => $transactionData["result-text"]]);
        }
    }

    /**
    * thankYou function
    *
    * @return View
    */
    public function thankYou( ) {

        //get meta data
        $metaData = $this->helper->getMetaData("general", "home")->toArray();

        return view("partials.thankyou")->with([
                    "meta" => $metaData[0],
        ]);
    }

    /**
    * ipn function
    */
    public function ipn() {

        //get all data from request object
        $data = $this->request->all();

        //find donation associated with the donation id passed back as 'custom'
        $donation = Donations::find( $data["custom"] );

        $item_id    = $donation->item_id;
        $item_type  = $donation->item_type;

        //set donation to 1
        $donation->donation_status = 1;
        //set transaction id
        $donation->transaction_id  = $data["txn_id"];
        //set the amount
        $donation->donation_amount = $data["mc_gross"];

        //dave donation
        $donation->save( );

        /**
        * Find which Item type and increment the amount donated. 
        */
        switch( $item_type ){
            case "country":
                $country    = Country::find( $item_id );

                $country->country_raised_amt += (int)$data["mc_gross"];

                $country->save( );
            break;

            case "cause":
                $cause      = Causes::find( $item_id );

                $cause->cause_raised_amt += (int)$data["mc_gross"];

                $cause->save( );
            break;

            case "organization":
                $org        = Organizations::find( $item_id );

                $org->org_amt_raised += (int)$data["mc_gross"];

                $org->save( );
            break;
        }

        //Add donation to email queue.
        $emailQueue = array(
            "type" => "donation",
            "type_id" => $data["custom"]
        );

        try {
            EmailQueue::create($emailQueue);
        } catch (\Exception $e) {
            Log::info("catch for EmailQueue");
            Log::info($e);
        }
    }

    public function notifyUser( ){

        
    }

    public function follow() {

        if (!\Auth::check()) {
            return response()->json(['status' => false, 'action' => 'signin']);
        } else {

            $user = \Auth::user();

            $follow = Follow::firstOrNew([
                        'follow_type' => Input::get('follow_item'),
                        'follow_type_id' => Input::get('follow_item_id'),
                        'follow_user_id' => $user->user_id
            ]);

            if ($follow->exists) {

                $action = "";

                if ($follow->follow_status == 'inactive') {
                    $follow->follow_status = 'active';
                    $action = "unfollow";
                } else {
                    $follow->follow_status = 'inactive';
                    $action = "follow";
                }

                $follow->save();

                return response()->json(['status' => true, 'action' => $action]);
            } else {

                $follow->follow_status = 'active';

                $follow->save();

                return response()->json(['status' => true, 'action' => 'unfollow']);
            }
        }
    }

    public function comment() {

        if (!\Auth::check()) {
            return response()->json(['status' => false, 'action' => 'signin']);
        } else {

            $user = \Auth::user();

            $comment = Rating::create([
                        "comment_user_id" => $user->user_id,
                        "comment_username" => $user->user_username,
                        "comment_item" => Input::get('type'),
                        "comment_item_id" => Input::get('id'),
                        "comment_text" => Input::get('comment'),
                        "comment_rating" => Input::get('rating'),
                        "comment_date" => Carbon::now(),
                        "comment_status" => "Y",
                        "comment_org_type" => "person_impacted"
            ]);

            return response()->json(['status' => true, 'username' => $user->user_username]);
        }
    }

    public function suggestNonProfit(Request $request) {

        $validator = \Validator::make($request->all(), [
                    'np-website' => 'required|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
                    'np-contact' => 'required|email',
                    'your-name' => 'required',
                    'your-email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()->all()]);
        } else {

            $suggestion = SuggestNonProfit::create([
                        "nonprofit_website" => Input::get('np-website'),
                        "nonprofit_poc" => Input::get('np-contact'),
                        "suggestors_name" => Input::get('your-name'),
                        "suggestors_email" => Input::get('your-email')
            ]);

            Mail::send("email.suggestOrg", [
                "website"   => Input::get('np-website'),
                "poc"       => Input::get('np-contact'),
                "name"      => Input::get('your-name'),
                "email"     => Input::get('your-email')
            ], function( $m ) {
                $m->to("info@projectworldimpact.com")->subject("Suggested NonProfit");
            });

            return response()->json(['status' => true]);
        }
    }

    public function email() {

        $test = Mail::send('email.mail', [], function($message) {
                    $message->to('michael.realmuto@gmail.com')->subject('your mom');
                });

        dd($test->getReasonPhrase());
    }

    public function getStates() {

        $countryId = Input::get('id');

        $states = States::where("country_id", "=", $countryId)->orderBy('state_name')->get();

        echo json_encode($states);
    }

    public function storeDonation(Request $request) {

        $donationAmt = 0.00;

        if (Input::has("amount")) {
            $donationAmt = Input::get("amount");
        }

        $request->session()->put('donationAmt', $donationAmt);
    }

    public function validateDonation(Request $request) {

        $messages = array(
            'first_name.required'           => "Your First Name is Required.",
            'last_name.required'            => "Your Last Name is Required.",
            'email.required'                => "Your Email is Required.",
            'email.email'                   => "Your Email is not formatted properly.",
            'billingAddress1.required'      => 'Billing Address is required.',
            'billingCity.required'          => 'Billing City is required.',
            'billingState.required'         => 'Billing State is required',
            'billingState.exists'           => 'Invalid Billing State',
            'billingZip:required'           => 'Billing Zip is required.',
            'billingZip:max'                => 'Billing Zip must be five numbers.',
            'billingCountry:required'       => 'Billing Country is required.',
            'billingCountry:exists'         => 'Invalid Billing Country.',
            'cc_number.required'            => 'Credit Card Number is required.',
            'cc_number.CreditCardNumber'    => 'Credit Card Number is Invalid.',
            'ccv.required'                  => 'Credit Card CCV is required.',
            'ccv.CreditCardCvc'             => 'Invalid CVC',
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

        if( Input::get('payment_gateway') != 3 ){
            $validationList['cc_number']        = 'required|CreditCardNumber';
            $validationList['ccv']              = 'required|CreditCardCvc:' . Input::get('cc_number');
            $validationList['name_on_card']     = 'required';
            $validationList['exp_date_month']   = 'CreditCardDate:' . Input::get('exp_date_year');
        } 

        $validator = \Validator::make($request->all(), $validationList, $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        } else {
            return response()->json(['status' => true]);
        }
    }

    
}
