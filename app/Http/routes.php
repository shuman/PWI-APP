<?php

/*
  |--------------------------------------------------------------------------
  | Routes File
  |--------------------------------------------------------------------------
  |
  | Here is where you will register all of the routes in an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */


//Index Routes

/* Home Page Route */

Route::group(['middleware' => 'web'], function( ) {

    Route::get('/', ['as' => 'home', 'uses' => 'IndexController@index']);

    Route::get('/auth/facebook/callback', 'Auth\AuthController@facebookCallback');

    Route::get('/auth/social/{type}', 'Auth\AuthController@socialLogin');

    Route::get('/emailtest', "IndexController@email");

    Route::post('/auth/login', ['as' => 'login', 'uses' => 'Auth\AuthController@userLoginAjax']);

    Route::get('/info', function( ){

          phpinfo( );
    });

    Route::get('/auth/logout', function(Request $request) {

        \Auth::logout();

        return redirect()->intended(\URL::previous());
    });

    Route::get('/getStates', 'IndexController@getStates');

    Route::post('/follow', 'IndexController@follow');

    Route::any('/passthru', ['middleware' => 'auth', 'uses' => 'IndexController@passThru']);

    Route::post('/setPendingDonation', 'IndexController@pendingDonation');

    Route::get('/thankyou', ['as' => 'indexPage', 'uses' => 'IndexController@thankYou']);

    Route::post('/ipn', 'IndexController@ipn');

    Route::post('/validateDonation', 'IndexController@validateDonation');

    Route::post('/saveAddress', 'IndexController@saveAddress');

    Route::get('/notify', 'IndexController@notifyUser');

    Route::post('/comment', [
        'as' => 'comment',
        'before' => 'csrf',
        'uses' => 'IndexController@comment'
    ]);

    Route::post('/suggest-nonprofit', 'IndexController@suggestNonProfit');

    Route::post('/storeDonation', function(Request $request) {

        session(['donationAmt' => Input::get('amount')]);
        session(['donationType' => Input::get('type')]);

        return response()->json(['status' => true]);
    });

    //Search Routes

    /* Search Home Route */
    Route::get('/search', 'SearchController@index');

    Route::get('/findCountry/{term}', 'SearchController@findCountry');

    Route::get('/search/organizations/{term}', ['as' => 'searchResults', 'uses' => 'SearchController@searchOrganizations']);

    /* Search Item Route */
    Route::get('/search/{term}', ['as' => 'searchResults', 'uses' => 'SearchController@search']);

    //Crowdfunding Routes

    /* Crowdfunding Home Page */
    Route::get('/crowdfunding', ['as' => 'indexPage', 'uses' => 'CrowdFundingController@index']);

    Route::post('/crowdfunding/setPendingDonation', 'CrowdFundingController@pendingDonation');

    /* Crowdfunding Individual Project Page */

    Route::get('/crowdfunding/{alias}', ['as' => 'crowdfundView', 'uses' => 'CrowdFundingController@view']);

    Route::get('/crowdfunding/{alias}/fund', ['as' => 'donation', 'uses' => 'CrowdFundingController@fund']);

    Route::post('/crowdfunding/validateFunding', [
        'as' => 'fundingCheckout',
        'before' => 'csrf',
        'uses' => 'CrowdFundingController@validateFundProject']
    );

    Route::post('/project/ipn', 'CrowdFundingController@ipn');

    /* Crowdfunding More Route */
    Route::post('/crowdfunding/more', 'CrowdFundingController@more');

    Route::post('/crowdfunding/storeFund', 'CrowdFundingController@storeFund');


    //Country Routes

    /* Country Home Route */
    Route::get('/country', 'CountryController@index');

    /* Country Item Route */
    Route::get('/country/{country}', 'CountryController@view');

    /* Country Item - Orgs Route */
    Route::get('/country/{country}/organizations', [
        'as' => 'searchResults', 
        'uses' => 'OrgController@getOrgsForCountry']);

    /* Country Item - Projects Route */

    Route::get('/country/{country}/projects', [
        'as' => 'searchResults', 
        'uses' => 'CrowdFundingController@getProjectsForCountry']);

    /* Country Item - Products Route */

    Route::get('/country/{country}/products', [
        'as' => 'searchResults', 
        'uses' => 'ProductController@getProductsForCountry']);

    /* Country Donation Route */
    Route::get('/country/{country}/donation', [
        'as' => 'donation', 
        'uses' => 'CountryController@donate']);

    Route::post('/country/validateDonation', [
        'as' => 'donationCheckout',
        'before' => 'csrf',
        'uses' => 'IndexController@validateDonation']
    );

    //Organization Routes

    /* Organization Home Route */
    Route::get('/organizations', ['as' => 'indexPage', 'uses' => 'OrgController@index']);

    /* Organization More Route */
    Route::post('/organization/more', 'OrgController@more');

    /* Organization Dashboard */
    Route::get('/organization/dashboard', 'OrgController@dashboard');

    /* Set Gateway for Org */
    Route::post('/organization/setGateway', 'OrgController@saveGateway');

    /* Set Org File Upload Path */
    Route::post('/organization/uploadimage', 'OrgController@uploadImage');

    /* Set Org File Remove Path */
    Route::post('/organization/removePhoto', 'OrgController@removeImage');    

    /* Set Org Video Remove Path */
    Route::post('/organization/removeVideo', 'OrgController@removeVideo');

    /* Set Org Video Save Path */
    Route::post('/organization/saveVideo', 'OrgController@saveVideo');

    /* Set Org General Information Update Route */
    Route::post('/organization/updateGeneralInfo', 'OrgController@updateGeneralInfo');

    /* Set Org Cause update Route */
    Route::post('/organization/updateCause', 'OrgController@updateCause');

    /* Set Org Cause Add Route */
    Route::post('/organization/addCause', 'OrgController@addCause');

    /* Set org Cause Remove Route */
    Route::post('/organization/removeCause', 'OrgController@removeCause');

    /* Set org Update Contact Information Route */
    Route::post('/organization/updateContactInfo', 'OrgController@updateContactInfo');

    /* Set org Add Project Route */
    Route::post('/organization/addProject', 'OrgController@addProject');

    /* Set org Update Project Route */
    Route::post('/organization/updateProject', 'OrgController@updateProject');

    /* Set org Delete Project Route */
    Route::post('/organization/deleteProject', 'OrgController@deleteProject');

    /* Set org Save Social Media Route */
    Route::post('/organization/saveSocialMedia', 'OrgController@saveSocialMedia');

    /* Set org Save Subscription Route */
    Route::post('/organization/saveSubscriptionData', 'OrgController@saveSubscriptionData');

    /* Set org getPaymentData Route */
    Route::post('/organization/getPaymentCredentials', 'OrgController@getPaymentCredentials');

    /* Set org addProduct Route */
    Route::post('/organization/addProduct', 'OrgController@addProduct');

    /* Set org removeProduct Route */
    Route::post('/organization/removeProduct', 'OrgController@removeProduct');

    /* Organization Item Route */
    Route::get('/organization/{org}', ['as' => 'orgView', 'uses' => 'OrgController@view']);

    /* Organzation Donation Route */
    Route::get('/organization/{org}/donation', ['as' => 'donation', 'uses' => 'OrgController@donate']);

    Route::post('/organization/validateDonation', [
        'as' => 'donationCheckout',
        'before' => 'csrf',
        'uses' => 'IndexController@validateDonation']
    );

    //Cause Routes

    /* Cause Home Route */
    Route::get('/causes', 'CauseController@index');

    /* Individual Cause Route */
    Route::get('/cause/{cause}', 'CauseController@view');

    /* Cause Item - Orgs Route */

    Route::get('/cause/{cause}/organizations', ['as' => 'searchResults', 'uses' => 'OrgController@getOrgsForCause']);

    /* Cause Item - Projects Route */

    Route::get('/cause/{cause}/projects', ['as' => 'searchResults', 'uses' => 'CrowdFundingController@getProjectsForCause']);

    /* Cause Item - Products Route */

    Route::get('/cause/{cause}/products', ['as' => 'searchResults', 'uses' => 'ProductController@getProductsForCause']);

    /* Cause Donation Route */
    Route::get('/cause/{cause}/donation', ['as' => 'donation', 'uses' => 'CauseController@donate']);

    Route::post('/cause/validateDonation', [
        'as' => 'donationCheckout',
        'before' => 'csrf',
        'uses' => 'IndexController@validateDonation']
    );



    //Product Routes

    /* Product Home Route */
    Route::get('/products', ['as' => 'indexPage', 'uses' => 'ProductController@index']);

    Route::get('/product/mail', 'ProductController@mailTest');

    /* Individual Project Page */
    Route::get('/product/{alias}', ['as' => 'productView', 'uses' => 'ProductController@view']);

    /* Individual Product Purchase Page */
    Route::get('/product/{alias}/purchase', ['as' => 'productPurchase', 'uses' => 'ProductController@purchase']);

    /* Product Filter Route */
    Route::post('/products/filter', 'ProductController@filter');

    /* Product Get Quantity - AJAX */

    Route::get('/products/getQuantity', 'ProductController@getQuantity');

    Route::post('/products/setPendingTransaction', 'ProductController@pendingTransaction');

    Route::post('/products/ipn', 'ProductController@ipn');

    /* Product validate checkout */
    Route::post('/product/validateCheckout', 'ProductController@validateCheckout');

    Route::get('/{page}', 'PagesController@page');

    /*
     * User Registration action
     */

    // Password reset link request routes...
    Route::get('password/email', 'Auth\PasswordController@getEmail');
    Route::post('password/email', 'Auth\PasswordController@postEmail');

    // Password reset routes...
    Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
    Route::get('password/reset', 'Auth\PasswordController@getReset');
    Route::post('password/reset', 'Auth\PasswordController@postReset');
});



/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */

Route::group(['middleware' => ['web']], function () {
    Route::get('/user/register', ['as' => 'register', 'uses' => 'AjaxController@registerAction']);
    Route::any('/user/dashboard', ['as' => 'dashboard', 'uses' => 'UserController@dashboard']);
    Route::any('/user/settings', ['as' => 'settings', 'uses' => 'UserController@settings']);
    Route::any('/user/order', ['as' => 'order', 'uses' => 'UserController@orders']);
    Route::any('/user/ordermessage', 'UserController@oder_message');
    Route::any('/user/register', 'RegistrationController@register');
    Route::any('/user/login', 'Auth\UserAuth@login');
    Route::any('/user/social', 'UserController@user_social_media');
    Route::any('/user/bill-account-pref', 'UserController@user_billpref_address');
    Route::any('/user/ship-account-preff', 'UserController@user_shippref_address');
    Route::any('/user/shipaddress-delete', 'UserController@user_shipp_address_delete');
    Route::any('/user/billadd-delete', 'UserController@user_bill_address_delete');

    Route::any('/user/news-letter-update', 'UserController@news_letter');
    Route::any('/user/follow-country', 'UserController@follow_country');
    Route::any('/user/profile-picture', 'UserController@change_profile_image');

    Route::any('/user/more-country-news', 'UserController@getmore_country_news');
    Route::any('/user/more-causes-news', 'UserController@getmore_causesnews');

    Route::any('/user/follow-causes', 'UserController@follow_causes');
    Route::any('/user/remove-follow', 'UserController@remove_follow');
    Route::any('/user/check_email','UserController@check_existing_email');
    Route::any('/user/check_username','UserController@check_user_name');
});


