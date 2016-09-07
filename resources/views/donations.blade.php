@extends('header')

@section('content')

<style>
    #wrapper{
        background-color:#fff !important;
    }
</style>
{!! HTML::Script( 'js/donate.js') !!}
{!! Form::hidden('donationAmt', $amount) !!}
{!! Form::hidden('payment_gateway', $payment_gateway) !!}
{!! Form::hidden('paypal_un', $paypal_un ) !!}

@if( isset( $cause ) )
    {!! Form::hidden('donate_id', $cause->cause_id) !!}
    {!! Form::hidden('type', 'cause') !!}
    <h1 class='text-center'>Donate to {!! $cause->cause_name !!}</h1>
@elseif( isset( $country) )
    {!! Form::hidden('donate_id', $country->country_id) !!}
    {!! Form::hidden('type', 'country') !!}
    <h1 class='text-center'>Donate to {!! $country->country_name !!}</h1>
@elseif( isset( $org ) )
    {!! Form::hidden('donate_id', $org->org_id) !!}
    {!! Form::hidden('type', 'organization') !!}
    <h1 class='text-center'>Donate to {!! $org->org_name !!}</h1>
@endif

<div class='thank-you-message row hidden'>
    <div class='col-lg-6 col-md-6 col-sm-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-3'>
        <div class='thank-you'>Thank you for your donation!</div>
        <div class='send-email'>We'll send you an email confirmation shortly.</div>
        <div><a href='/'>Click here</a> to return to continue browsing causes, countries, and organizations.</div>
    </div>
</div>

<div class='donation-checkout row'>
    <div class='col-lg-6 col-md-6 col-sm-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-3 donation-amount'>
        <p class='margin-top-15' style='font-size: 24px;'>Donation</p>
        <div class='details-donate'>
            <div class='donation' data-incentive-id='donate'>
                <p>Donation Amount</p>
                <div class='donation-amount row'>
                    @if( empty( $amount ) )
                        <div class='col-lg-6 col-md-6 col-sm-6'>
                            {!! Form::text('donation', '', array('class'=>'form-control')) !!}
                        </div>
                    @else
                        <div class='col-lg-6 col-md-6 col-sm-6'>
                            <span>${!! $amount !!}</span>
                            {!! Form::text('donation', '', array('placholder'=>'Donation Amount', 'class'=>'form-control hidden')) !!}
                        </div>
                        <div class='col-lg-6 col-md-6 col-sm-6'>
                            {!! Form::button('change amount', array('class' => 'donate-button pull-left chg-amount') ) !!}
                            {!! Form::button('cancel', array('class' => 'pull-right cancel-amount hidden', 'style'=>'width:45%;') ) !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    
        <div class='details-personal'>
            {!! Form::open( array( 'url' => '/validateDonation', 'name' => 'donationCheckoutForm')) !!}

            @if( ! is_null( $user ) )
                {!! Form::hidden('user_id', $user->user_id) !!}
            @else 
                {!! Form::hidden('user_id') !!}
            @endif

            <p class='margin-top-15' style='font-size: 24px;'>Enter Personal</p>
            
            <div class='information'>
                @include( 'partials.checkout._details' )
                <hr />
                @if( $payment_gateway != 3 )
                    @include( 'partials.checkout._ccInfo' )
                    <hr />
                @endif
                @include( 'partials.checkout._billing' )
                <hr />
                <div class='form-group text-right'>
                    {!! Form::button('review order', array('class'=>'continueCheckout')) !!}
                </div>
            </div>
            {!! Form::close( ) !!}
        </div>
    </div>
</div>

<div class='donation-review row'>
    <div class='review col-lg-6 col-md-6 col-sm-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-3' >
        <p class='header donation-review-header margin-top-15'>Review Donation</p>
        <div class='error'></div>
        <div class='order-review'>
            <div class='order-review-cc-billing'>
                <div class='order-review-total'>
                    @if( $payment_gateway != 3 )
                        @include('partials.checkout.review._ccInfo')
                    @endif
                    @include('partials.checkout.review._billing')
                </div>
            </div>
            <hr />
            <div class='place-order'>
                <div class='row'>
                    <div class='col-lg-6 col-md-6 col-sm-6'>
                        <p class='header'>Donation Total:</p>
                        <div class='donation-amount'></div>
                    </div>
                    <div class='col-lg-6 col-md-6 col-sm-6 padding-left-0'>
                        {!! Form::button('checkout', array('class'=>'checkout-button')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if( $payment_gateway == 3 )
    @include("partials._checkoutform")
@endif
@stop