<div class='billing-info martin-top-15' style='clear:both;'>
    <p class='group-header'>
        Billing Information
    </p>
    @if( isset( $product ) || isset( $project ) )
    <div class='sameAsShipping-wrapper'>
        {!! Form::checkbox('sameAsShipping', '1') !!} <span class='saveShippingAddress'>Same As Shipping</span>
        <br />
        <br />
    </div>
    @endif
    @if( ! is_null( $user ) )
        @if( sizeof( $userData) > 0 )
        <div class='form-group'>
            <select name='userAddresses' class='form-control'>
                <option value='0'>Select an Address...</option>
                @foreach( $userData as $key => $value )
                    @if( $value["type"] == "billing" )
                        <option value='{!! $key !!}'>{!! $value['address'] !!}</option>
                    @endif
                @endforeach
            </select>
        </div>
        @endif
    @endif
    <div class='form-group'>
        {!! Form::text('billingAddress1', '', array('placeholder'=>'Billing Address Line 1', 'class'=>'form-control')) !!}
        <div class='error billingAddress1-error bg-danger '></div>
    </div>
    <div class='form-group'>
        {!! Form::text('billingAddress2', '', array('placeholder'=>'Billing Address Line 2', 'class'=>'form-control')) !!}
    </div>
    <div class='form-group' style='height: 40px;'>
        <div class='half-form-group-left'>
            {!! Form::text('billingCity', '', array('placeholder'=>'City', 'class'=>'form-control padding-right-5')) !!}
            <div class='error billingCity-error bg-danger '></div>
        </div>
        <div class='half-form-group-right'>
            {!! Form::select('billingState', array('0' => 'Select a Country to Retrieve States'), 0, array('placeholder'=>'State/Province', 'class'=>'form-control padding-left-5', 'style'=>'height:45px;') ) !!}
            <div class='error billingState-error bg-danger '></div>
        </div>
    </div>
    <div class='form-group' style='height: 40px;'>
        <div class='half-form-group-left'>
            {!! Form::text('billingZip', '', array('placeholder'=>'Zip', 'class'=>'form-control padding-right-5')) !!}
            <div class='error billingZip-error bg-danger '></div>
        </div>
        <div class='half-form-group-right'>
            {!! Form::select('billingCountry', $countries, 0, array('placeholder'=>'Country', 'class'=>'form-control padding-left-5', 'style' => 'height: 45px;', ) ) !!}
            
            <div class='error billingCountry-error bg-danger '></div>
        </div>
    </div>
    @if( ! is_null( $user ) )
        {!! Form::checkbox('saveAddress', '1', false, array('data-type'=>'billing')) !!} <span class='saveAddress'>Save Billing Address</span>
    @endif
</div>