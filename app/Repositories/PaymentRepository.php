<?php
namespace App\Repositories;

/** Include Paypal Api **/

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Api\CreditCard;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Item;

/** Include Authorize.NET libraries **/


use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

/** Load other dependencies **/

use Illuminate\Http\Request;
use App\Organizations;
use App\Http\Helper;
use Carbon\Carbon;
use App\Products;
use App\Projects;
use App\Country;
use App\Follow;
use App\Causes;
use App\Files;
use App\User;
use Config;
use Log;
use DB;

class PaymentRepository {

	private $transNationalAPIKey = "";
	private $transNationalUrl    = "";
	private $transResultTable 	 = array( );
	
	private $helper;
	private $request;
    
    function __construct( $request ){
    	$this->helper  = new Helper( );
    	$this->request = $request;
    	$this->transResultTable = Config::get("globals.transNationalResultTable");

    	$this->transNationalUrl 	= Config::get('globals.transnationalUrl');

    }

    public function processTransnational( $data, $type, $id){

		$xml = new \DOMDocument('1.0', 'UTF-8');

		$xml->formatOutput = true;
		$xmlSale = $xml->createElement('sale');

		if( $data["cc_number"] == "4111111111111111" ){
			$this->transNationalAPIKey = Config::get("globals.transnationalAPIKeyTest");
		}else{
			switch( $data["item_type"] ){
				case "organization":
					$this->setTransnationalData( $data["item_id"] );
				break;
				case "crowdfunding":
					$this->setTransnationalData( $data["project_id"] );				
				break;
				case "cause":
				case "country":
					$this->transNationalAPIKey = Config::get("globals.transnationalAPIKeyFoundation");
					Log::info( "USING: " . $this->transNationalAPIKey . " API Key\n\n");
				break;
				case "product":
					$this->transNationalAPIKey =  Config::get("globals.transnationalAPIKeyLLC");
				break;
			}	
		}

		/** Append XML Nodes **/

		//Sale Node
		$this->helper->appendXmlNode($xml, $xmlSale, 'api-key', $this->transNationalAPIKey );
		$this->helper->appendXmlNode($xml, $xmlSale, 'redirect-url', $this->request->headers->get('referer') );
		$this->helper->appendXmlNode($xml, $xmlSale, 'ip-address', $this->request->ip( ) );
		$this->helper->appendXmlNode($xml, $xmlSale, 'currency', 'USD');

		$this->helper->appendXmlNode($xml, $xmlSale, 'order-id', $id);

		$orderDescription = "";

		switch( $type ){

			case "country":
				$tmp = Country::find( $data["item_id"] );
				$orderDescription = "Donation to " . $tmp->country_name;
				$this->helper->appendXmlNode($xml, $xmlSale, 'order-description', $orderDescription );
				$this->helper->appendXmlNode($xml, $xmlSale, 'amount', $data['donation_amount'] );
			break;
			case "cause":
				$tmp = Causes::find( $data["item_id"] );
				$orderDescription = "Donation to " . $tmp->cause_name;
				$this->helper->appendXmlNode($xml, $xmlSale, 'order-description', $orderDescription );
				$this->helper->appendXmlNode($xml, $xmlSale, 'amount', $data['donation_amount'] );
			break;
			case "organization":
				$tmp = Organizations::find( $data["item_id"] );
				$orderDescription = "Donation to " . $tmp->org_name;
				$this->helper->appendXmlNode($xml, $xmlSale, 'order-description', $orderDescription );
				$this->helper->appendXmlNode($xml, $xmlSale, 'amount', $data['donation_amount'] );
			break;
			case "project":
				$tmp = Projects::find( $data["project_id"] );
				$orderDescription = "Fund Project: " . $tmp->project_title;
				$this->helper->appendXmlNode($xml, $xmlSale, 'order-description', $orderDescription );
				$this->helper->appendXmlNode($xml, $xmlSale, 'amount', $data['donation_amount'] );
			break;
			case "product":
				$this->helper->appendXmlNode($xml, $xmlSale, 'order-description', $data["description"] );
				$this->helper->appendXmlNode($xml, $xmlSale, 'tax-amount', $data['tax'] );
				$this->helper->appendXmlNode($xml, $xmlSale, 'shipping-amount', $data['shipping'] );
				$this->helper->appendXmlNode($xml, $xmlSale, 'amount', ($data["price"] * $data["quantity"]) + $data["shipping"] + $data["tax"] );

				if( ! empty( $data['product_modifiers'] ) ){

					$modifiers = explode(",", $data['product_modifiers'] );

					if( sizeof( $modifiers ) == 1 ){
						$this->helper->appendXmlNode($xml, $xmlSale, 'merchant-defined-field-1', $data['product_modifiers'] );
					}else{
						for( $i = 1 ; $i <= sizeof( $modifiers ) ; $i++ ){
							$this->helper->appendXmlNode($xml, $xmlSale, 'merchant-defined-field-' . $i, $modifiers[($i-1)] );
						}
					}
				}
			break;
		}

		// Billing Node

		$xmlBillingAddress = $xml->createElement('billing');

		$this->helper->appendXmlNode($xml, $xmlBillingAddress, 'first-name', $data['first_name'] );
		$this->helper->appendXmlNode($xml, $xmlBillingAddress, 'last-name', $data['last_name'] );
		$this->helper->appendXmlNode($xml, $xmlBillingAddress, 'address1', $data['billing_address_line1'] );
		$this->helper->appendXmlNode($xml, $xmlBillingAddress, 'address2', $data['billing_address_line2'] );
		$this->helper->appendXmlNode($xml, $xmlBillingAddress, 'city', $data['billing_city'] );
		$this->helper->appendXmlNode($xml, $xmlBillingAddress, 'state', $data["stateAbbr_billing"] );
		$this->helper->appendXmlNode($xml, $xmlBillingAddress, 'postal', $data["billing_zip"] );
		$this->helper->appendXmlNode($xml, $xmlBillingAddress, 'country', $data["countryCode_billing"] );
		$this->helper->appendXmlNode($xml, $xmlBillingAddress, 'email', $data["email"] );

		$xmlSale->appendChild( $xmlBillingAddress );

		if( isset( $data["hasShippingData"] ) && $data["hasShippingData"] == 1 ){
			$xmlShippingAddress = $xml->createElement('shipping');

			$this->helper->appendXmlNode($xml, $xmlShippingAddress, 'first-name', $data['first_name']);
			$this->helper->appendXmlNode($xml, $xmlShippingAddress, 'last-name', $data['last_name']);
			$this->helper->appendXmlNode($xml, $xmlShippingAddress, 'address1', $data['shipping_address_line1']);
			$this->helper->appendXmlNode($xml, $xmlShippingAddress, 'address2', $data['shipping_address_line2']);
			$this->helper->appendXmlNode($xml, $xmlShippingAddress, 'city', $data['shipping_city']);
			$this->helper->appendXmlNode($xml, $xmlShippingAddress, 'state', $data['stateAbbr_shipping']);
			$this->helper->appendXmlNode($xml, $xmlShippingAddress, 'country', $data['countryCode_shipping']);

			$xmlSale->appendChild( $xmlShippingAddress );
		}

		if( $type == "purchase" ){
			$xmlProduct = $xml->createElement('product');

			$this->helper->appendXmlNode($xml, $xmlProduct, 'product-code', $data['product_sku']);
			$this->helper->appendXmlNode($xml, $xmlProduct, 'description', $data['description']);
			$this->helper->appendXmlNode($xml, $xmlProduct, 'unit-cost', $data['price']);
			$this->helper->appendXmlNode($xml, $xmlProduct, 'quantity', $data['quantity']);
			$this->helper->appendXmlNode($xml, $xmlProduct, 'tax-amount', $data['tax']);
			
			$xmlSale->appendChild( $xmlProduct );
		}

		$xml->appendChild( $xmlSale );

		$xmlResp = $this->helper->sendXMLviaCurl( $xml, $this->transNationalUrl );

		Log::info( "XML RESPONSE: " . $xmlResp . "\n");

		$response = @new \SimpleXmlElement( $xmlResp );
		Log::info( "RESPONSE: " . $response . "\n");
		if( (string)$response->result == 1 ){
			$formUrl = (string)$response->{'form-url'};

			$fields = array(
		        "billing-cc-number" => $data["cc_number"],
		        "billing-cc-exp"	=> $data["exp_date"],
		        "cvv"				=> $data["ccv"]
		    );

		    $ch = curl_init( );

		    curl_setopt( $ch, CURLOPT_URL, $formUrl );
	        curl_setopt( $ch, CURLOPT_POST, TRUE );
	        curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields );
	        curl_setopt( $ch, CURLOPT_HEADER, true);
	        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);

	        $result = curl_exec( $ch );

	        $info = curl_getinfo( $ch );

	        foreach( $info as $k=>$v){

	        	if( ! is_array( $v) ){
	        		Log::info( $k . " -> " . $v . "\n\n");	
	        	}
	        }

	        list($url, $queryString) = explode("?", $info["redirect_url"] );

	        list($key, $value) = explode("=", $queryString);

	        $tokenId = $value;

	        $xml = new \DOMDocument('1.0', 'UTF-8');

	        $xml->formatOutput = true;

	        $xmlCompleteTransaction = $xml->createElement('complete-action');
	        $this->helper->appendXmlNode($xml, $xmlCompleteTransaction, 'api-key', $this->transNationalAPIKey);
	        $this->helper->appendXmlNode($xml, $xmlCompleteTransaction, 'token-id', $tokenId);

	        $xml->appendChild( $xmlCompleteTransaction );

	        $transactionResponse = $this->helper->sendXMLviaCurl($xml, $this->transNationalUrl);

	        Log::info( "Transaction Response: " . $transactionResponse . "\n\n");

	        $gwResponse = @new \SimpleXmlElement((string)$transactionResponse);

	        if( (string)$gwResponse->result == 1 ){
	        	return array( 
	        		"status" 		=> 1,
	        		"transactionId" => (string)$gwResponse->{'transaction-id'},
	        		"authCode"		=> (string)$gwResponse->{'authorization-code'}
	        	);
	        }else{
	        	return array( 
	        		"status" 		=> 0,
	        		"result-text" 	=> (string)$gwResponse->{'result-text'},
	        		"reason"		=> $this->getTransResultCode( (string)$gwResponse->{'result-code'} )
	        	);
	        }
		}else{
			return array( 
        		"status" 		=> 0,
        		"result-text" 	=> $response,
        		"reason"		=> $this->getTransResultCode( (string)$response->{'result-code'} )
        	);
		}
	}

	public function processPayPalPro($data, $type, $id){

		$_api_context;

		$paypal_conf = Config::get('paypal');
		$_api_context = new ApiContext( new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
		$_api_context->setConfig( $paypal_conf['settings'] );

		$cardDetails = \CreditCard::validCreditCard( $data['cc_number'] );

		$cardType = $cardDetails["type"];

		$expMonth = substr($data["exp_date"], 0, 2);
		$expYear  = "20" . substr( $data["exp_date"], -2, 2);
		
		$card = new CreditCard( );
		$card->setType( $cardType )
			 ->setNumber( $data['cc_number'] )
			 ->setExpireMonth( $expMonth )
			 ->setExpireYear( $expYear )
			 ->setCvv2( $data['ccv'] )
			 ->setFirstName( $data["first_name"] )
			 ->setLastName( $data["last_name"] );

		$fi = new FundingInstrument( );
		$fi->setCreditCard( $card );

		$payer = new Payer( );
		$payer->setPaymentMethod('credit_card')
			  ->setFundingInstruments( array( $fi ) );

		$itemList = new ItemList( );

		$details  = new Details( );

		$amount   = new Amount( );

		switch( $type ){
			/*
			case "country":
				$tmp = Country::find( $data["item_id"] );
				$item = new Item( );
				$item->setName("Donation to " . $tmp->country_name )
					 ->setDescription( "Donation to " . $tmp->country_name )
					 ->setCurrency("USD")
					 ->setQuantity( 1 )
					 ->setPrice( $data["donation_amount"] );

				$itemList->setItems( array($item) );

				$details->setSubtotal( $data["donation_amount"] );

				$amount->setCurrency( 'USD' )
					   ->setTotal( $data["donation_amount"] )
					   ->setDetails( $details );
			break;
			case "cause":
				$tmp = Cause::find( $data["item_id"] );
				$item = new Item( );
				$item->setName("Donation to " . $tmp->cause_name )
					 ->setDescription( "Donation to " . $tmp->cause_name )
					 ->setCurrency("USD")
					 ->setQuantity( 1 )
					 ->setPrice( $data["donation_amount"] );

				$itemList->setItems( array($item) );

				$details->setSubtotal( $data["donation_amount"] );

				$amount->setCurrency( 'USD' )
					   ->setTotal( $data["donation_amount"] )
					   ->setDetails( $details );
			break;
			*/
			case "organization":
				$tmp = Organizations::find( $data["item_id"] );
				$item = new Item( );
				$item->setName("Donation to " . $tmp->org_name )
					 ->setDescription( "Donation to " . $tmp->org_name )
					 ->setCurrency("USD")
					 ->setQuantity( 1 )
					 ->setPrice( $data["donation_amount"] );

				$itemList->setItems( array($item) );

				$details->setSubtotal( $data["donation_amount"] );

				$amount->setCurrency( 'USD' )
					   ->setTotal( $data["donation_amount"] )
					   ->setDetails( $details );
			break;
			case "project":
				$tmp = Projects::find( $data["project_id"] );

				$description = "";

				if( isset( $data["incentive_id"] ) && $data["incentive_id"] > 0 ){
					$incentive = ProjectIncentives::find( $data["incentive_id"] );

					$description = "Donation to " . $tmp->project_title . " - Incentive: " . $incentive->project_incentive_description;
				}else{
					$description = "Donation to " . $tmp->project_title;
				}

				$item = new Item( );
				$item->setName("Donation to " . $tmp->project_title )
					 ->setDescription( $description )
					 ->setCurrency( "USD" )
					 ->setQuantity( 1 )
					 ->setPrice( $data["donation_amount"] );

				$itemList->setItems( array( $item ) );

				$details->setSubtotal( $data["donation_amount"] );

				$amount->setCurrency( 'USD' )
					   ->setTotal( $data["donation_amount"] )
					   ->setDetails( $details );
			break;
			/*
			case "product":
				$tmp = Products::find( $data["id"] );
				$productDescription = "";

				if( ! empty( $data['product_modifiers'] ) ){
					$productDescription = $tmp->product_name . " - " . $data['product_modifiers'];
				}else{
					$productDescription = $tmp->product_name;
				}

				$item = new Item( );
				$item->setName( $tmp->product_name )
					 ->setDescription( $productDescription )
					 ->setCurrency( 'USD' )
					 ->setQuantity( $data["quantity"] )
					 ->setTax( $data["tax"] )
					 ->setPrice( $data["price"] );

				$itemList->setItems( array( $item ) ); 

				$details->setShipping( $data['shipping'] )
						->setTax( $data["tax"] )
						->setSubtotal( ( $data["donation_amount"] * $data["quantity"] ) );

				$amount->setCurrency( 'USD' )
					   ->setTotal( ($data["price"] * $data["quantity"]) + $data["shipping"] + $data["tax"] )
					   ->setDetails( $details );

			break;
			*/
		}

		$transaction = new Transaction( );
		$transaction->setAmount( $amount )
					->setItemList( $itemList )
					->setDescription(" PWI Transactions ")
					->setInvoiceNumber( uniqid( ) );

		$payment = new Payment( );
		$payment->setIntent( "sale" )
				->setPayer( $payer )
				->setTransactions( array( $transaction ) );

		$payment->create( $_api_context );

		$paymentState = $payment->getState( );

		if( $paymentState == "approved" || $paymentState == "created"){
			return array( 
        		"status" 		=> 1,
        		"transactionId" => (string)$payment->getId( )
        	);
		}else{
			return array( 
        		"status" 		=> 0,
        		"result-text" 	=> (string)$payment->getFailureReason( )
        	);
		}
	}

	public function processAuthorizeDotNET( $data, $type, $id ){

		$merchantAuthentication = new AnetAPI\MerchantAuthenticationType( );

		$orgId = "";

		if( $type == "organization" ){
			$orgId = $data["item_id"];
		}else{
			$project = Projects::find( $data["project_id"] );

			$orgId = $project->org_id;
		}


		$settings = DB::table("pwi_org_settings")
					->where( "org_id", "=", $orgId)
					->select("authorizeNET_name AS name", "authorizeNET_key AS key")
					->get( );

		$name = $settings[0]->name;
		$key  = $settings[0]->key;

		/*
		$testName = "24Xtg2SgC7";
		$testTransactionKey = "9jX6Gy5fw74LY8Wd";
		*/

		$merchantAuthentication->setName( $name );
		$merchantAuthentication->setTransactionKey( $key );
		$refId = 'ref' . time( );

		$expMonth = substr($data["exp_date"], 0, 2);
		$expYear  = "20" . substr( $data["exp_date"], -2, 2);
		
		$creditCard = new AnetAPI\CreditCardType( );
		$creditCard->setCardNumber( $data["cc_number"] );
		$creditCard->setExpirationDate( $expMonth . "-" . $expYear );
		$creditCard->setCardCode( $data["ccv"] );
		$payment = new AnetAPI\PaymentType( );
		$payment->setCreditCard( $creditCard );

		
		$order = new AnetAPI\OrderType( );
		$order->setDescription( "PWI Transaction" );

		//create transaction 
		$transactionRequestType = new AnetAPI\TransactionRequestType( );
		$transactionRequestType->setTransactionType( "authCaptureTransaction" );
		$transactionRequestType->setAmount( $data["donation_amount"] );
		$transactionRequestType->setOrder( $order );
		$transactionRequestType->setPayment( $payment );

		$request = new AnetAPI\CreateTransactionRequest( );
		$request->setMerchantAuthentication( $merchantAuthentication );
		$request->setRefId( $refId );
		$request->setTransactionRequest( $transactionRequestType );

		$controller = new AnetController\CreateTransactionController( $request );
		$authResponse;

		if( env( "APP_ENV" ) == "live" ){
			$authResponse = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION );
		}else{
			$authResponse = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX );
		}

		if( $authResponse != null ){

			$transResponse = $authResponse->getTransactionResponse( );
							
			if( $transResponse->getResponseCode( ) == 1 ){
				return array( 
	        		"status" 		=> 1,
	        		"transactionId" => $transResponse->getTransId( ),
	        		"authCode" 		=> $transResponse->getAuthCode( )
	        	);
			}else{
				return array( 
	        		"status" 		=> 0,
	        		"result-text" 	=> "Invalid Response."
	        	);
			}			
		}else{
			return array( 
        		"status" 		=> 0,
        		"result-text" 	=> "No response returned."
        	);
		}
	}

	private function setTransnationalData( $id ){

		$gateway = DB::table("pwi_org_settings")
				->where("org_id", "=", $id)
				->get( );

		$this->transNationalAPIKey  = $gateway[0]->gateway_key;
		$this->transNationalUrl  	= $gateway[0]->gateway_url;
	}

	public function getTransResultCode( $code ){

		if( isset( $this->transNationalResultTable[(int)$code] ) ){
			return $this->transNationalResultTable[(int)$code];
		}else{
			return "";
		}
	}
}