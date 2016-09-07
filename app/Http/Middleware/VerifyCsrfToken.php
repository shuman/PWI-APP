<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
	    '/ipn',
        '/follow',
        '/comment',
        '/passthru',
        '/product/filter',
        '/crowdfunding/more',
        '/organization/more',
        '/products/filter',
        '/product/validateCheckout',
        '/products/setPendingTransaction',
        '/products/ipn',
        '/project/ipn',
        '/storeDonation',
        '/crowdfunding/storeFund',
        '/saveAddress',
        '/user/profile-picture',
        '/setPendingDonation',
        '/crowdfunding/setPendingDonation',
        '/validateDonation',
        '/organization/setGateway',
        '/organization/updateGeneralInfo',
        '/organization/addProduct'
    ];
}
