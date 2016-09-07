@extends('header')

@section('content')

<style>
    #wrapper{
        background-color:#fff !important;
    }
</style>
{!! HTML::script('js/fund.js') !!}
{!! Form::hidden('donationAmt', $amount) !!}
{!! Form::hidden('incentiveId', $chosenIncentive) !!}
{!! Form::hidden('projectId', $project->project_id) !!}
{!! Form::hidden('paypal_un', $paypal_un ) !!}
{!! Form::hidden('title', $project->project_title) !!}
{!! Form::hidden('payment_gateway', $payment_gateway) !!}

<div class='project-fund-title'>Donate To {!! $project->project_title !!}</div>
<div class='project-fund-organization'>{!! $project->org_name !!}</div>

<div class='donation-checkout row'>
    <div class='col-lg-6 col-md-6 col-sm-6 fund-options'>
    
        <p class='margin-top-15' style='font-size: 24px;'>Fund/Incentives</p>
        <div class='error fund-error bg-danger margin-bottom-10'></div>
        @if( empty( $chosenIncentive ) && ! empty( $amount ) )
        <div class='details-fund selected'>
        @else
        <div class='details-fund'>
        @endif
            <div class='just-contribute'>
                No thanks, just want to contribute
                @if( empty( $chosenIncentive ) && ! empty( $amount ) )
                <div class='donation' data-incentive-id='donate' style='display:block;'>
                @else
                <div class='donation' data-incentive-id='donate'>
                @endif
                    <p>Donation Amount</p>
                    <div class='denomination pull-left'>
                        @if( empty( $chosenIncentive ) && ! empty( $amount ) )
                            <input type='text' name='donationAmt' value='{!! $amount !!}'/>
                        @else
                            <input type='text' name='donationAmt' />
                        @endif
                    </div>
                </div>
            </div>
        </div>

         <div class='fund-incentives-list'>
            @foreach( $incentives as $incentive )
                @if( $chosenIncentive == $incentive->project_incentive_id )
                <div class='incentive margin-top-10 selected' data-incentive-id='{!! $incentive->project_incentive_id !!}'>
                @else
                <div class='incentive margin-top-10' data-incentive-id='{!! $incentive->project_incentive_id !!}'>
                @endif
                    @if( $incentive->project_available_incentive_count == $incentive->project_incentive_purchasedcount )
                    <div class='zero-left-overlay'></div>
                    @endif
                    <div class='title'>{!! $incentive->project_incentive_title !!}</div>    
                    <div class='info'>
                        <span class='price pull-left'>{!! money_format('%(#10n', $incentive->project_incentive_amount ) !!}</span>
                        @if( $incentive->project_available_incentive_count == $incentive->project_incentive_purchasedcount )
                        <span class='left no-more pull-right'>0 Left</span>
                        @else
                        <span class='left pull-right'>{!! ( $incentive->project_available_incentive_count - $incentive->project_incentive_purchasedcount ) !!} Left</span>
                        @endif
                    </div>
                    <div class='description'>{!! $incentive->project_incentive_description !!}</div>
                    @if( $chosenIncentive == $incentive->project_incentive_id )
                    <div class='donation' style='display:block;'>
                    @else
                    <div class='donation'>
                    @endif
                        <p>Donation Amount</p>
                        <div class='denomination pull-left'>
                            <input type='text' name='donationAmt' value='{!! $incentive->project_incentive_amount !!}' />
                            <input type='hidden' name='shippingRequired' value='{!! $incentive->project_donor_shipping_address !!}' />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
    <div class='col-lg-6 col-md-6 col-sm-6 fund-information'>
        <div class='details-personal'>
            {!! Form::open( array( 'url' => '/validateDonation', 'name' => 'fundCheckoutForm')) !!}
            @if( ! is_null( $user ) )
                {!! Form::hidden('user_id', $user->user_id) !!}
            @else 
                {!! Form::hidden('user_id') !!}
            @endif
            {!! Form::hidden('payment_gateway', $payment_gateway) !!}
            <p class='margin-top-15' style='font-size: 24px;'>Enter Personal</p>
            
            <div class='information'>
                @include('partials.checkout._details')
                <hr />
                @if( $payment_gateway != 3 )
                    @include('partials.checkout._shipping')
                @endif
                <hr class='shippingHR'/>
                @include('partials.checkout._ccInfo')
                <hr />
                @include('partials.checkout._billing')
                <hr />
                <div class='form-group text-right'>
                    {!! Form::button('review order', array('class'=>'continueCheckout')) !!}
                </div>
            </div>
            {!! Form::close( ) !!}
        </div>
    </div>
</div>

<div class='fund-review row'>
    <div class='col-lg-6 col-md-6 col-sm-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-3'>
        <p class='header margin-top-15'>Review Order</p>
        <div class='order-review'>
            <div class='order-review-incentive'>
                <p class='header'></p>
                <div class='incentive-title'></div>
                <div class='incentive-description'></div>
                <div class='incentive-amount row margin-top-10'>
                    <div class='col-lg-6 col-md-6 col-sm-6'>
                        <p>Donation amount</p>
                        <div class='margin-top-10'>
                            <span class='donation-amount'></span>
                        </div>
                    </div>
                    <div class='col-lg-6 col-md-6 col-sm-6'>
                        {!! Form::button('edit incentive', array('class'=>'edit-button')) !!}
                    </div>
                </div>
            </div>
            <hr />
            @include('partials.checkout.review._shipping')
            <hr />
            <div class='order-review-cc-billing'>
                <div class='order-review-total'>
                   @include('partials.checkout.review._ccInfo')
                   @include('partials.checkout.review._billing')
                </div>
            </div>
            <hr />
            <div class='place-order row'>
                <div class='col-lg-6 col-md-6 col-sm-6'>
                    <p class='header'>Total:</p>
                    <div class='donation-amount'></div>
                </div>
                <div class='col-lg-6 col-md-6 col-sm-6'>
                    {!! Form::button('place order', array('class'=>'checkout-button')) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@if( $payment_gateway == 3 )
    @include("partials._checkoutform")
@endif
@stop