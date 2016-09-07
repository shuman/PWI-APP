<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository as UserRepository;
use App\Repositories\PaymentRepository as Payments;
use App\Http\Controllers\Controller;
use App\ProductModifierOptions;
use Illuminate\Http\Request;
use App\ProductDetail;
use App\ProductMaster;
use App\Http\Requests;
use App\Http\Helper;
use App\UserAddress;
use App\Categories;
use App\EmailQueue;
use Carbon\Carbon;
use App\Products;
use App\Country;
use App\Follow;
use App\States;
use Response;
use Config;
use Agent;
use Input;
use Mail;
use Log;
use DB;

setlocale(LC_MONETARY, 'en_US');

class ProductController extends Controller {

    /**
     * Javascript file name to be loaded
     *
     * @var string
     */
    private $scriptPage;

    /**
     * variable for amount of related product to show
     *
     * @var int
     */
    private $relatedProductCount = 2;

    /**
     * Object for the helper
     *
     * @var helper
     */
    private $helper;

    /**
     * Request object
     *
     * @var request
     */
    private $request;

    /**
     * construct for Products
     *
     * @param App\Repositories\UserRepository $userObj
     * @param Illuminate\Http\Request $request
     * @param App\Http\Helper $helper
     */
    public function __construct(UserRepository $userObj, Request $request, Helper $helper) {
        $this->scriptPage = "scripts.products";
        $this->helper = $helper;

        $this->user = $request->instance()->query('user');

        $this->request = $request;

        $this->userObj = $userObj;
    }

    /**
     * index page for Products
     *
     * @return view
     */
    public function index() {

        $list = array();

        $priceFilterList = array(
            array("value" => "<10", "text" => "Under $10", "count" => 0),
            array("value" => "10-25", "text" => "$10-$25", "count" => 0),
            array("value" => "25-50", "text" => "$25-$50", "count" => 0),
            array("value" => "50-100", "text" => "$50-$100", "count" => 0),
            array("value" => ">100", "text" => "$100 & Above", "count" => 0)
        );

        $ratingFilterList = array(
            array("value" => 1, "count" => 0),
            array("value" => 2, "count" => 0),
            array("value" => 3, "count" => 0),
            array("value" => 4, "count" => 0),
            array("value" => 5, "count" => 0)
        );

        $products = Products::where("product_status", "=", "active")
                ->where("product_featured", "=", "Y")
                ->where("product_image_id", ">", "0")
                ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_products.org_id")
                ->leftJoin("pwi_files AS FILE", "FILE.file_id", "=", "pwi_products.product_image_id")
                ->select("product_id", "product_name AS name", "product_alias", "product_sales_price AS price", "product_short_desc AS sdesc", "FILE.file_path AS image", "ORG.org_name")
                ->get();

        $list = $this->createProductList($products);

        $categories = Categories::all();

        $categoryCnts = DB::table("pwi_categories AS CAT")
                ->leftJoin("pwi_product_categories AS PRDCAT", "PRDCAT.category_id", "=", "CAT.category_id")
                ->leftJoin("pwi_products AS PRD", "PRD.product_id", "=", "PRDCAT.product_id")
                ->where("PRD.product_status", "=", "active")
                ->select(DB::raw("COUNT(CAT.category_id) AS CNT, CAT.category_name, CAT.category_id"))
                ->groupBy("CAT.category_name")
                ->get();

        $categoryFilterList = array();

        foreach ($categories as $category) {

            $matches = array_filter($categoryCnts, function( $x ) use ($category) {

                return( strtolower($x->category_name) == strtolower($category["category_name"]) );
            });

            if (sizeof($matches) > 0) {

                $match = array_pop($matches);

                $categoryFilterList[] = array(
                    "name" => $category["category_name"],
                    "id" => $category["category_id"],
                    "cnt" => $match->CNT
                );
            }
        }

        $priceCnts = DB::table("pwi_products AS PRD")
                ->select(DB::raw("CASE 
                                WHEN product_sales_price > 0 AND product_sales_price <= 10 THEN '<10'
                                WHEN product_sales_price > 10 AND product_sales_price <= 25 THEN '10-25'
                                WHEN product_sales_price > 25 AND product_sales_price <= 50 THEN '25-50'
                                WHEN product_sales_price > 50 AND product_sales_price <= 100 THEN '50-100'
                                WHEN product_sales_price > 100 THEN '>100 & Above'
                            END AS PriceRange, COUNT(*) AS RangeAmt"))
                ->where("PRD.product_status", "=", "active")
                ->groupBy("PriceRange")
                ->get();

        foreach ($priceFilterList as &$filter) {

            $matches = array_filter($priceCnts, function( $x ) use( $filter ) {

                return( $x->PriceRange == $filter["value"] );
            });

            if (sizeof($matches) > 0) {

                $match = array_pop($matches);
                $filter["count"] = $match->RangeAmt;
            }
        }

        $ratingCnts = DB::table(DB::raw("( SELECT product_id, AVG(COM.comment_rating) AS Rating 
                                FROM pwi_comments as COM
                                INNER JOIN pwi_products AS PRD ON PRD.product_id = COM.comment_item_id ) as sq"))
                ->select(DB::raw("CASE 
                                WHEN Rating > 0 AND Rating <= 1 THEN '1'
                                WHEN Rating > 1 AND Rating <= 2 THEN '2'
                                WHEN Rating > 2 AND Rating <= 3 THEN '3'
                                WHEN Rating > 3 AND Rating <= 4 THEN '4'
                                WHEN Rating > 4 AND Rating <= 5 THEN '5'
                            END AS RatingRange, COUNT(*) AS Num"))
                ->groupBy("RatingRange")
                ->get();

        foreach ($ratingFilterList as &$filter) {

            $matches = array_filter($ratingCnts, function( $x ) use( $filter ) {
                return( $x->RatingRange == $filter["value"] );
            });

            if (sizeof($matches) > 0) {
                $match = array_pop($matches);
                $filter["count"] = $match->RatingRange;
            }
        }

        $meta = $this->helper->getMetaData("general", "products")->toArray();

        $view = "pages.products.index";

        if (Agent::isMobile() && ! Agent::isTablet( ) ) {
            $view = "mobile.pages.products.index";
        }

        return view($view)->with([
                "products" => $list,
                "categories" => $categoryFilterList,
                "priceFilters" => $priceFilterList,
                "ratingFilters" => $ratingFilterList,
                "path" => Config::get("globals.prdImgPath"),
                "scriptPage" => $this->scriptPage,
                "meta" => $meta[0],
        ]);
    }

    /**
     * individual view page for Products
     *
     * @return view
     */
    public function view($alias) {

        //DB::connection( )->enableQueryLog( );
        $product;

        try {
            $product = Products::where("product_alias", "=", $alias)
                    ->leftJoin("pwi_organization as ORG", "pwi_products.org_id", "=", "ORG.org_id")
                    ->leftJoin("pwi_files as ICON", "pwi_products.product_image_id", "=", "ICON.file_id")
                    ->leftJoin("pwi_product_categories AS PRDCAT", "pwi_products.product_id", "=", "PRDCAT.product_id")
                    ->leftJoin("pwi_categories AS CAT", "PRDCAT.category_id", "=", "CAT.category_id")
                    ->leftJoin("pwi_org_settings AS ORGSET", "ORG.org_id", "=", "ORGSET.org_id")
                    ->select("pwi_products.*", "ORG.org_name", "ORG.org_alias", "ORG.org_id", "ICON.file_path", "CAT.category_id", "ORGSET.paypal_username as paypal_un", "product_viewcount")
                    ->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }

        $relatedProducts = Products::where("pwi_products.product_id", "<>", $product->product_id)
                ->leftJoin("pwi_product_categories AS PRDCAT", "pwi_products.product_id", "=", "PRDCAT.product_id")
                ->leftJoin("pwi_categories AS CAT", "PRDCAT.category_id", "=", "CAT.category_id")
                ->leftJoin("pwi_organization AS ORG", "pwi_products.org_id", "=", "ORG.org_id")
                ->leftJoin("pwi_files AS FILE", "pwi_products.product_image_id", "=", "FILE.file_id")
                ->where(function( $query ) use ($product) {
                    $query->where("pwi_products.org_id", "=", $product->org_id)
                    ->orWhere("CAT.category_id", "=", $product->category_id);
                })
                ->where("product_status", "=", "active")
                ->select("pwi_products.product_id", "product_name AS name", "product_alias", "product_sales_price AS price", "product_short_desc AS sdesc", "FILE.file_path AS image", "ORG.org_name")
                ->get();

        $paypal_un = "";

        if (empty($product->paypal_un) || is_null($product->paypal_un)) {
            $paypal_un = "chris@projectworldimpact.com";
        } else {
            $paypal_un = $product->paypal_un;
        }

        $relatedProductsCount = $relatedProducts->count();

        $rPrds = array();

        $rPrds = $this->createProductList($relatedProducts, 2);

        $images = $product->images;

        $impacts = $product->impacts;

        $reviews = $product->rating;

        $reviewCnt = 0;

        $reviewAggregate = 0;

        $rating = 0;

        foreach ($reviews as $review) {
            $reviewAggregate += (int) $review->comment_rating;
            $reviewCnt++;
        }

        if ($reviewCnt > 0) {
            $rating = ceil($reviewAggregate / $reviewCnt);
        }

        $productModifiers = array();

        $modifiers = $product->modifiers;

        //dd( $this->helper->parseCauses( $product->causes ) );

        foreach ($modifiers as $modifier) {

            $tmp = array();

            $tmp = array(
                "modifier_name" => $modifier->product_modifier_title,
                "modifier_id" => $modifier->product_modifier_id,
                "modifier_options" => array()
            );

            $options = ProductModifierOptions::where("product_modifier_id", "=", $modifier->product_modifier_id)
                    ->where("product_id", "=", $product->product_id)
                    ->get();

            $i = 0;

            foreach ($options as $option) {
                $tmp["modifier_options"][$i]["option_id"] = $option->pm_option_id;
                $tmp["modifier_options"][$i]["option_name"] = $option->pm_option_name;
                $tmp["modifier_options"][$i]["option_price"] = $option->pm_option_price;
                $tmp["modifier_options"][$i]["option_quantity"] = $option->pm_option_quantity;
                $tmp["modifier_options"][$i]["option_shipping_fee"] = $option->pm_option_shippingfee;

                $i++;
            }

            $productModifiers[] = $tmp;
        }

        $userData = null;
        $isFollowing = FALSE;

        if (!is_null($this->user)) {

            $isFollowing = $this->userObj->isFollowing($this->user, "product", $product->product_id);
        }

        $meta = $this->helper->getMetaData("individual", "products");

        $view = "pages.products.product";

        if (Agent::isMobile() && ! Agent::isTablet( ) ) {
            $view = "mobile.pages.products.product";
        }

        /** Update view count for product **/

        Products::where('product_id', '=', $product->product_id)
                ->update(['product_viewcount' => $product->product_viewcount++]);

        return view($view)->with([
            "product" => $product,
            "images" => $images,
            "impacts" => $impacts,
            "causes" => $this->helper->parseCauses($product->causes),
            "impacts" => $impacts,
            "prdPath" => Config::get("globals.prdImgPath"),
            "rating" => $rating,
            "reviews" => $reviews,
            "modifiers" => $productModifiers,
            "relatedProductsCount" => $relatedProductsCount,
            "relatedProducts" => $rPrds,
            "meta" => $this->helper->parseIndMetaData($meta[0], $product->product_name),
            "following" => $isFollowing,
            "paypal_un" => $paypal_un,
            "scriptPage" => $this->scriptPage,
        ]);
    }

    public function more() {
        
    }

    public function filter(Request $request) {
        //DB::connection( )->enableQueryLog( );
        $list = array();

        if ($request->ajax()) {

            $catId = "";
            $price = "";
            $rating = "";
            $noFilter = FALSE;

            if (Input::has("cat_id")) {
                $catId = Input::get("cat_id");
            }

            if (Input::has("price")) {
                $price = Input::get("price");
            }

            if (Input::has("rating")) {
                $rating = Input::get("rating");
            }

            if (empty($catId) && empty($price) && empty($rating)) {
                $noFilter = TRUE;
            }

            if ($noFilter) {
                $products = Products::where("product_status", "=", "active")
                        ->where("product_featured", "=", "Y")
                        ->where("product_image_id", ">", "0")
                        ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_products.org_id")
                        ->leftJoin("pwi_files AS FILE", "FILE.file_id", "=", "pwi_products.product_image_id")
                        ->select("product_id", "product_name AS name", "product_alias", "product_sales_price AS price", "product_short_desc AS sdesc", "FILE.file_path AS image", "ORG.org_name")
                        ->get();

                $list = $this->createProductList($products);

                echo json_encode(array("status" => 1, "data" => $list));
            } else {
                $products = Products::where("product_status", "=", "active")
                        ->select("pwi_products.product_id", "product_name AS name", "product_alias", "product_sales_price AS price", "product_short_desc AS sdesc", "FILE.file_path AS image", "ORG.org_name")
                        ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_products.org_id")
                        ->leftJoin("pwi_files AS FILE", "FILE.file_id", "=", "pwi_products.product_image_id");

                if (!empty($catId)) {
                    $products->leftJoin("pwi_product_categories AS PRDCAT", "PRDCAT.product_id", "=", "pwi_products.product_id")
                            ->where("PRDCAT.category_id", "=", $catId);
                }

                if (!empty($price)) {

                    if (preg_match('/^</', $price)) {
                        $price = str_replace("<", "", $price);
                        $products->where("product_sales_price", "<", $price);
                    } else if (preg_match('/^>/', $price)) {
                        $price = str_replace(">", $price);
                        $products->where("product_sales_price", ">", $price);
                    } else {
                        list($low, $high) = explode("-", $price);
                        $products->whereBetween("product_sales_price", [$low, $high]);
                    }
                }

                if (!empty($rating)) {

                    $subQuery = DB::table('pwi_comments')
                            ->selectRaw("AVG(comment_rating)")
                            ->where("comment_id", "=", "product");

                    $products->whereRaw($rating . " >= ( " . $subQuery->toSql() . " )")
                            ->addBinding($subQuery->getBindings());
                }

                $productslist = $products->get();

                $list = $this->createProductList($productslist);

                echo json_encode(array("status" => 1, "data" => $list));

                die;
            }
        }
    }

    public function purchase(Request $request, $alias) {

        $product;

        try {
            $product = Products::where("product_alias", "=", $alias)
                    ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_products.org_id")
                    ->leftJoin("pwi_files AS FILE", "pwi_products.product_image_id", "=", "FILE.file_id")
                    ->select("product_id", "product_name", "product_sales_price", "product_shipping_fee", "ORG.org_name", "ORG.org_id", "FILE.file_path as image")
                    ->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }

        $modifiers = $product->modifiers;

        $productModifiers = array();

        foreach ($modifiers as $modifier) {

            $tmp = array();

            $tmp = array(
                "modifier_name" => $modifier->product_modifier_title,
                "modifier_id" => $modifier->product_modifier_id,
                "modifier_options" => array()
            );

            $options = ProductModifierOptions::where("product_modifier_id", "=", $modifier->product_modifier_id)
                    ->where("product_id", "=", $product->product_id)
                    ->get();

            $i = 0;

            foreach ($options as $option) {
                $tmp["modifier_options"][$i]["option_id"] = $option->pm_option_id;
                $tmp["modifier_options"][$i]["option_name"] = $option->pm_option_name;
                $tmp["modifier_options"][$i]["option_price"] = $option->pm_option_price;
                $tmp["modifier_options"][$i]["option_quantity"] = $option->pm_option_quantity;
                $tmp["modifier_options"][$i]["option_shipping_fee"] = $option->pm_option_shippingfee;

                $i++;
            }

            $productModifiers[] = $tmp;
        }

        //Run query t osee if user has an active gateway
        $gateway = DB::table("pwi_org_settings")
                      ->where("org_id", "=", $product->org_id)
                      ->get( );

        $paypal_un = "";
        $paymentGateway = 0;

        if( sizeof( $gateway ) == 0 ){
            return redirect( )->action('ProductController@view', $alias);    
        }else{
            $paypal_un = $gateway[0]->paypal_username;
            $paymentGateway = $gateway[0]->fk_payment_gateway;
        }


        $optionInventors = array();
        $modInventorTable = array();

        $price = $product->product_sales_price;
        $maxQuantity = 0;

        $modifierIds = "";

        if (Input::has('modifiers')) {

            try {
                $optionInventor = DB::table("pwi_product_modifier_option_inventers")
                        ->where("pm_option_inventory", "=", Input::get('modifiers'))
                        ->where("product_id", "=", $product->product_id)
                        ->select("pm_option_price", "pm_option_inventory_names", "pm_option_quantity")
                        ->get();

                $price = $optionInventor[0]->pm_option_price;

                $maxQuantity = $optionInventor[0]->pm_option_quantity;

                $inventorNames = explode(",", $optionInventor[0]->pm_option_inventory_names);
                $inventorOptionIds = explode(",", Input::get('modifiers'));

                if (sizeof($inventorNames) == sizeof($inventorOptionIds)) {
                    for ($i = 0; $i < sizeof($inventorNames); $i++) {
                        $modInventorTable[] = $inventorOptionIds[$i] . "|" . $inventorNames[$i];
                    }
                }

                $modifierIds = Input::get("modifiers");
            } catch (\Exception $e) {
                dd($e);
            }
        }

        $quantity = 0;

        if (Input::has("quantity")) {
            $quantity = Input::get("quantity");

            $price = $price * $quantity;
        }

        $years = array( );

        for( $i = Carbon::now( )->year ; $i < ( Carbon::now( )->year + 8 ) ; $i++ ){

            $yearAbbr = substr($i, -2);
            $years[$yearAbbr] = $i; 
        }

        $meta = $this->helper->getMetaData("individual", "purchase");

        return view("pages.products.purchase")->with([
                "product"           => $product,
                "prdPath"           => Config::get("globals.prdImgPath"),
                "modifiers"         => $productModifiers,
                "chosenMods"        => $modInventorTable,
                "quantity"          => $quantity,
                "maxQuantity"       => $maxQuantity,
                "meta"              => $this->helper->parseProductPurchaseData($meta[0], $product->product_name, $product->org_name),
                "paypal_un"         => "paypal@pwifoundation.org",
                "price"             => $price,
                "passedModIds"      => $modifierIds,
                "years"             => $years,
                "payment_gateway"   => $paymentGateway,

        ]);
    }

    private function createProductList($products, $limit = 0) {

        $list = array();

        $count = 0;

        foreach ($products as $product) {

            if ($limit > 0 && $count < $limit) {

                $tmp = array();

                $rating = $product->rating->avg("comment_rating");

                $tmp = $product->toArray();

                if (file_exists(public_path() . Config::get("globals.prdImgPath") . $tmp["image"]) && !is_null($tmp["image"])) {
                    $tmp["image"] = Config::get("globals.prdImgPath") . $tmp["image"];
                } else {
                    $tmp["image"] = "/images/prodPlaceholder.png";
                }

                $tmp["rating"] = $rating;

                $tmp["price"] = money_format('%(#0n', (int) $tmp["price"]);

                $tmp["descExp"] = explode(" ", $tmp["sdesc"]);

                $list[] = $tmp;
            } else {
                $tmp = array();

                $rating = $product->rating->avg("comment_rating");

                $tmp = $product->toArray();

                if (file_exists(public_path() . Config::get("globals.prdImgPath") . $tmp["image"]) && !is_null($tmp["image"])) {
                    $tmp["image"] = Config::get("globals.prdImgPath") . $tmp["image"];
                } else {
                    $tmp["image"] = "/images/prodPlaceholder.png";
                }

                $tmp["rating"] = $rating;

//                $tmp["price"] = money_format('%(#10n', (int) $tmp["price"]);
                $tmp["price"] =  money_format('%(#0n', (int)$tmp["price"]);

                $tmp["descExp"] = explode(" ", $tmp["sdesc"]);

                $list[] = $tmp;
            }
            $count++;
        }
        return $list;
    }

    public function getProductsForCountry($alias) {

        $country = \App\Country::where("country_alias", "=", $alias)->firstOrFail();

        $list = array();

        $products = Products::where("product_status", "=", "active")
                ->where("product_cause_type", "=", "country")
                ->where("product_cause_status", "=", "active")
                ->where("product_cause_item_id", "=", $country->country_id)
                ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_products.org_id")
                ->leftJoin("pwi_files AS FILE", "FILE.file_id", "=", "pwi_products.product_image_id")
                ->leftJoin("pwi_product_causes AS PRDC", "PRDC.product_id", "=", "pwi_products.product_id")
                ->select("pwi_products.product_id", "product_name AS name", "product_alias", "product_sales_price AS price", "product_short_desc AS sdesc", "FILE.file_path AS image", "ORG.org_name")
                ->get();

        $list = $this->createProductList($products);

        $meta = $this->helper->getMetaData("individual", "search_results_page");

        return view("pages.country.products")->with([
                    "products" => $list,
                    "alias" => $alias,
                    "country_name" => $country->country_name,
                    "path" => Config::get("globals.prdImgPath"),
                    "scriptPage" => $this->scriptPage,
                    "meta" => $this->helper->parseSearchMetaData($meta[0], "Products", $country->country_name),
        ]);
    }

    public function getProductsForCause($alias) {

        $list = array();

        DB::connection()->enableQueryLog();

        $cause = \App\Causes::where("cause_alias", "=", $alias)->firstOrFail();

        $products = Products::where("product_status", "=", "active")
                ->where("product_cause_type", "=", "cause")
                ->where("product_cause_status", "=", "active")
                ->where("product_cause_item_id", "=", $cause->cause_id)
                ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_products.org_id")
                ->leftJoin("pwi_files AS FILE", "FILE.file_id", "=", "pwi_products.product_image_id")
                ->leftJoin("pwi_product_causes AS PRDC", "PRDC.product_id", "=", "pwi_products.product_id")
                ->select("pwi_products.product_id", "product_name AS name", "product_alias", "product_sales_price AS price", "product_short_desc AS sdesc", "FILE.file_path AS image", "ORG.org_name")
                ->get();

        $list = $this->createProductList($products);


        $meta = $this->helper->getMetaData("individual", "search_results_page");

        return view("pages.causes.products")->with([
                    "products" => $list,
                    "alias" => $alias,
                    "cause_name" => $cause->cause_name,
                    "path" => Config::get("globals.prdImgPath"),
                    "scriptPage" => $this->scriptPage,
                    "meta" => $this->helper->parseSearchMetaData($meta[0], "Products", $cause->cause_name),
        ]);
    }

    public function validateCheckout(Request $request) {

        $messages = array(
            'first_name.required'       => "Your First Name is Required.",
            'last_name.required'        => "Your Last Name is Required.",
            'email.required'            => "Your Email is Required.",
            'email.email'               => "Your Email is not formatted properly.",
            'shippingAddress1.required' => 'Shipping Address is required',
            'shippingCity.required'     => 'Shipping City is required',
            'shippingState.required'    => 'Shipping State is required',
            'shippingState.exists'      => 'Invalid Shipping State',
            'shippingZip.required'      => 'Shipping Zip is required',
            'shippingZip.max'           => 'Shipping Zip must be five numbers',
            'shippingZip.min'           => 'Shipping Zip must be five numbers',
            'shippingCountry.required'  => 'Invalid Shipping State',
            'shippingCountry.exists'    => 'Country is invalid',
            'billingAddress1.required'  => 'Billing Address is required.',
            'billingCity.required'      => 'Billing City is required.',
            'billingState.required'     => 'Billing State is required',
            'billingState.exists'       => 'Invalid Billing State',
            'billingZip.required'       => 'Billing Zip is required.',
            'billingZip.max'            => 'Billing Zip must be five numbers.',
            'billingCountry.required'   => 'Billing Country is required.',
            'billingCountry.exists'     => 'Invalid Billing Country.',
            'cc_number.required'            => 'Credit Card Number is required.',
            'cc_number.CreditCardNumber'    => 'Credit Card Number is Invalid.',
            'ccv.required'                  => 'Credit Card CCV is required.',
            'ccv.CreditCardCvc'             => 'Invalid CVC',
            'exp_date_month.CreditCardDate' => 'Invalid Expiration Date.'
        );

        $validator = \Validator::make($request->all(), [
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => 'required|email',
            'shippingAddress1'  => 'required',
            'shippingCity'      => 'required',
            'shippingState'     => 'required|exists:pwi_state,state_id',
            'shippingZip'       => 'required|max:5|min:5',
            'shippingCountry'   => 'required|exists:pwi_country,country_id',
            'cc_number'         => 'required|CreditCardNumber',
            'ccv'               => 'required|CreditCardCvc:' . Input::get('cc_number'),
            'name_on_card'      => 'required',
            'exp_date_month'    => 'CreditCardDate:' . Input::get('exp_date_year'),
            'billingAddress1'   => 'required',
            'billingCity'       => 'required',
            'billingState'      => 'required|exists:pwi_state,state_id',
            'billingZip'        => 'required|max:5',
            'billingCountry'    => 'required|exists:pwi_country,country_id'
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        } else {

            $taxCode = DB::table("pwi_tax_settings as TAX")
                    ->where("state_id", "=", Input::get("shippingState"))
                    ->where("country_id", "=", Input::get("shippingCountry"))
                    ->get();

            return response()->json(['status' => true, 'taxCode' => $taxCode[0]]);
        }
    }

    public function getQuantity() {

        $data = DB::table("pwi_product_modifier_option_inventers")
                ->where("pm_option_inventory", "=", Input::get('options'))
                ->where("product_id", "=", Input::get('id'))
                ->select("pm_option_inventer_id", "pm_option_quantity", "pm_option_price")
                ->get();

        $modifierId = $data[0]->pm_option_inventer_id;
        $quantity   = $data[0]->pm_option_quantity;
        $price      = $data[0]->pm_option_price;

        if( $quantity == 0 ){
            $product = Products::find( Input::get('id') );

            $quantity   = $product->product_quantity;
            $price      = $product->product_sales_price;
        }


        return response()->json(['modifier_id' => $modifierId, 'count' => $quantity, 'price' => $price]);
    }

    public function pendingTransaction(Request $request) {

        $payload = $request->all();

        /** Check if the user has requested to save the shipping and/or billing address **/

        if ($payload["saveShipping"] == "true") {
            $this->helper->saveShippingAddress( $payload );
        }

        if ($payload["saveBilling"] == "true") {
            $this->helper->saveBillingAddress( $payload );
        }

        $master = new ProductMaster;

        $master->user_id                  = $payload["user_id"];
        $master->billing_full_name        = $payload["first_name"] . " " . $payload["last_name"];
        $master->shipping_full_name       = $payload["first_name"] . " " . $payload["last_name"];
        $master->billing_email            = $payload["email"];
        $master->shipping_email           = $payload["email"];
        $master->billing_address_line1    = $payload["billing_address_line1"];
        $master->billing_address_line2    = $payload["billing_address_line2"];
        $master->billing_city             = $payload["billing_city"];
        $master->billing_state            = $payload["billingstateId"];
        $master->billing_zip              = $payload["billing_zip"];
        $master->billing_country          = $payload["billingcountryId"];
        $master->shipping_address_line1   = $payload["shipping_address_line1"];
        $master->shipping_address_line2   = $payload["shipping_address_line2"];
        $master->shipping_city            = $payload["shipping_city"];
        $master->shipping_state           = $payload["shippingstateId"];
        $master->shipping_zip             = $payload["shipping_zip"];
        $master->shipping_country         = $payload["shippingcountryId"];
        $master->product_count            = $payload["quantity"];
        $master->order_date               = Carbon::now();
        $master->payment_gateway          = $payload["payment_gateway"];
        $master->order_item_total         = ($payload["price"] * $payload["quantity"]);
        $master->order_shipping_cost      = $payload["shipping"];
        $master->order_tax                = $payload["tax"];
        $master->order_cost               = ($payload["price"] * $payload["quantity"]) + $payload["shipping"] + $payload["tax"];

        $master->save( );

        $masterId = $master->order_id;

        //Get Product record
        $product = Products::find($payload["id"]);

        //Retrieve modifiers for product
        $productModifier = DB::table("pwi_product_modifier_option_inventers")
                ->where("pm_option_inventory", "=", $payload["modifierId"])
                ->select("pm_option_inventory_names as names")
                ->get();

        $detail = new ProductDetail;

        $detail->product_id         = $payload["id"];
        $detail->product_name       = $product->product_name;
        $detail->product_sku        = $product->product_sku;
        $detail->modifier_id        = $payload["modifierId"];
        $detail->modifier_name      = $productModifier[0]->names;
        $detail->quantity           = $payload["quantity"];
        $detail->status             = 1;
        $detail->product_price      = $payload["price"];
        $detail->product_shipping   = $payload["shipping"];
        $detail->org_id             = $product->org_id;
        $detail->order_id           = $master->order_id;

        $detail->save( );

        $payload['product_modifiers']   = $productModifier[0]->names;
        $payload['product_sku']         = $product->product_sku;
        $payload['description']         = $product->product_short_desc;

        $state      = States::find( $payload['billingstateId'] );
        $country    = Country::find( $payload['billingcountryId'] );

        $payload["stateAbbr_billing"]      = $state->state_code;
        $payload["countryCode_billing"]    = $country->country_iso_code;

        $state      = States::find( $payload['shippingstateId'] );
        $country    = Country::find( $payload['billingcountryId'] );

        $payload["stateAbbr_shipping"]     = $state->state_code;
        $payload["countryCode_shipping"]   = $country->country_iso_code;

        $payments = new Payments( $this->request );

        $payload["item_type"] = "product";

        $transactionData = $payments->processTransnational($payload, "product", $master->order_id);

        if( $transactionData["status"] == 1 ){
            
            $master = ProductMaster::find( $masterId );

            $master->order_status = 1;
            $master->transaction_id = $transactionData["transactionId"];

            $master->save( );

            //Add donation to email queue.
            $emailQueue = array(
                "type" => "order",
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
            return response( )->json(['status' => false, 'text' => $transactionData["result-text"], 'reason' => $transactionData["reason"]]);
        }
    }

    public function ipn() {
        $data = $this->request->all();

        DB::table("pwi_order_master")
                ->where("order_id", $data["custom"])
                ->update(["order_status" => 1, "transaction_id" => $data["txn_id"]]);

        $emailQueue = array(
            "type" => "order",
            "type_id" => $data["custom"]
        );
    }

}
