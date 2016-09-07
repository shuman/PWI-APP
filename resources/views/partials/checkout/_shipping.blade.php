{!! Form::hidden('showShipping', 'N') !!}
<div class='shipping-info padding-bottom-10'>
    <p class='group-header'>
        Shipping Address
    </p>
    @if( ! is_null( $user ) )
        @if( sizeof( $userData) > 0 )
        <div class='form-group'>
            <select name='userAddresses' class='form-control'>
                <option value='0'>Select a Shipping Address...</option>
                @foreach( $userData as $key => $value )
                    @if( $value["type"] == "shipping" )
                        <option value='{!! $key !!}'>{!! $value['address'] !!}</option>
                    @endif
                @endforeach
            </select>
        </div>
        @endif
    @endif
    <div class='form-group'>
        {!! Form::text('shippingAddress1', '', array('placeholder'=>'Shipping Address Line 1', 'class'=>'form-control')) !!}
        <div class='error shippingAddress1-error bg-danger '></div>
    </div>
    <div class='form-group'>
        {!! Form::text('shippingAddress2', '', array('placeholder'=>'Shipping Address Line 2', 'class'=>'form-control')) !!}
    </div>
    <div class='form-group' style='height: 40px;'>
        <div class='half-form-group-left'>
            {!! Form::text('shippingCity', '', array('placeholder'=>'City', 'class'=>'form-control')) !!}
            <div class='error shippingCity-error bg-danger '></div>
        </div>
        <div class='half-form-group-right'> 
            {!! Form::select('shippingState', array('0' => 'Select a Country to Retrieve States'), 0, array('placeholder'=>'State/Province', 'class'=>'form-control padding-left-5', 'style'=>'height:45px;') ) !!}
            <div class='error shippingState-error bg-danger '></div>
        </div>
    </div>
    <div class='form-group' style='height: 40px;'>
        <div class='half-form-group-left'>
            {!! Form::text('shippingZip', '', array('placeholder'=>'Zip', 'class'=>'form-control')) !!}
            <div class='error shippingZip-error bg-danger '></div>
        </div>
        <div class='half-form-group-right' >
            {!! Form::select('shippingCountry', $countries, 0, array('placeholder'=>'Country', 'class'=>'form-control padding-left-5', 'style' => 'height: 45px;', ) ) !!}
            <div class='error shippingCountry-error bg-danger '></div>
        </div>
    </div>
    @if( ! is_null( $user ) )
        {!! Form::checkbox('saveShippingAddress', '1', false, array('data-type'=>'shipping')) !!} <span class='saveAddress'>Save Shipping Address</span>
    @endif
</div>
