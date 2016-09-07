<div data-role='page' id='donate-page-one' class='donate'>
	<div data-role='header' class='donate-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-nodisc-icon ui-btn-icon-left ui-btn-icon-notext
">home</a>
		<h1>Donate</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<div class='donateToName'></div>
		<div class='donationAmountError'></div>
		<div class='donateContainer'>
			<div class='title'>Donation Amount</div>
			<div class='amtContainer'>
				<input name='donationAmt' type='number' />
			</div>
		</div>
		<div style='width: 100%;'>
			@if( is_null( $user ) )
			<div class='sign-in-button'>
				<a href='#donate-login'>Sign in </a>
			</div>
			<div class='cont-as-guest'>
				<a href='#donate-page-two' class='continue'>Continue as guest</a>
			</div>
			@else
			<div class='sign-in-button'>
				<a href='#donate-page-two' class='continue'>Continue </a>
			</div>
			@endif
		</div>
	</div>
</div>

<div data-role='page' id='donate-page-two' class='donate'>
	<div data-role='header' class='donate-header'>
		<a data-rel='#donate-page-one' class="ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>Donate</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<div class='donateToName'></div>
		<div class='donateContainer'>
			<div class='header'>Donation Amount</div>
			<div style='height: 45px;'>
				<div class='donationAmount'></div>
				<div class='donationEdit'><a href='#donate-page-one'>Edit</a></div>
			</div>
		</div>
		{!! Form::open( array( 'url' => '/organization/validateFunding', 'name' => 'donationCheckoutForm')) !!}
	    @if( ! is_null( $user ) )
	    	{!! Form::hidden('user_id', $user->user_id) !!}
	    @else 
	    	{!! Form::hidden('user_id') !!}
	    @endif
		<div class='donateContainer' style='margin-top: 10px;'>
			<div class='header'>Enter Personal</div>
			<div class='infoContainer' style='border-bottom: 1px solid #e5e5e5;'>
				@if( ! is_null( $user ) )
				{!! Form::text('first_name', $user->user_firstname, array('placeholder'=>'First Name')) !!}
				<div class='error first_name-error bg-danger '></div>
				{!! Form::text('last_name', $user->user_lastname, array('placeholder'=>'Last Name')) !!}
				<div class='error last_name-error bg-danger '></div>
				{!! Form::email('email', $user->user_email, array('placeholder'=>'Email') ) !!}
				<div class='error email-error bg-danger '></div>
				@else
                {!! Form::text('first_name', '', array('placeholder'=>'First Name')  ) !!}
                <div class='error first_name-error bg-danger '></div>
                {!! Form::text('last_name', '', array('placeholder'=>'Last Name')  ) !!}
                <div class='error last_name-error bg-danger '></div>
                {!! Form::email('email', '', array('placeholder'=>'Email' ) ) !!}
                <div class='error email-error bg-danger '></div>
                @endif
			</div>
			<div class='header'>Credit Card Information</div>
			<div class='infoContainer' style='border-bottom: 1px solid #e5e5e5;'>
				{!! Form::text('cc_number', '', array('placeholder'=>'Card number', 'class'=>'form-control', 'id'=>'cc_number')) !!}
				<div class='error cc_number-error bg-danger '></div>
				{!! Form::text('name_on_card', '', array('placeholder'=>'Name on card', 'class'=>'form-control', 'id'=>'name_on_card')) !!}
			     <div class='error name_on_card-error bg-danger '></div>
			     <fieldset class='ui-grid-a'>
			     
					<div class='ui-block-a'>
						{!! Form::text('exp_date_month', '', array('placeholder'=>'Exp Month', 'class'=>'form-control padding-left-5', 'id'=>'cvv')) !!}		

                		<div class='error exp_date_month-error bg-danger '></div>
                	</div>
                	<div class='ui-block-b'>
	                	{!! Form::text('exp_date_year', '', array('placeholder'=>'Exp Year', 'class'=>'form-control padding-left-5', 'id'=>'cvv')) !!}
                	</div>
                </fieldset>
                {!! Form::text('ccv', '', array('placeholder'=>'CCV', 'class'=>'form-control padding-left-5', 'id'=>'cvv')) !!}
			    <div class='error ccv-error bg-danger '></div>
	        </div>
			
			<div class='header'>Billing Address</div>
			<div class='infoContainer' style='border-bottom: 1px solid #e5e5e5;'>
				@if( ! is_null( $user ) )
                    @if( sizeof( $userData) > 0 )
                    <div class='form-group'>
                        <select name='userAddresses' class='form-control'>
                            <option value='0'>Select an Address...</option>
                            @foreach( $userData as $key => $value )
                            	@if( $value["type"] == "billing" )
                            		<option value='{!! $key !!}'>{!! $value["address"] !!}</option>
                            	@endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                @endif

                {!! Form::text('billingAddress1', '6471 Berkshire Ct', array('placeholder'=>'Billing Address Line 1' ) ) !!}
                <div class='error billingAddress1-error bg-danger '></div>
                {!! Form::text('billingAddress2', '', array('placeholder'=>'Billing Address Line 2' ) ) !!}
                <fieldset class='ui-grid-a'>
                	<div class='ui-block-a'>
                		{!! Form::text('billingCity', 'Lisle', array('placeholder'=>'City' , 'class'=>'half') ) !!}
                		<div class='error billingCity-error bg-danger '></div>
                	</div>
                	<div class='ui-block-b'>
                		{!! Form::select('billingState', array('0' => 'Select a Country to Retrieve States'), 0, array('placeholder'=>'State/Province', 'class'=>'form-control padding-left-5', 'style'=>'height:45px;') ) !!}
                		<div class='error billingState-error bg-danger '></div>
                	</div>
                </fieldset>

                <fieldset class='ui-grid-a'>
                	<div class='ui-block-a'>
                		{!! Form::text('billingZip', '60532', array('placeholder'=>'Zip' ) ) !!}
                		<div class='error billingZip-error bg-danger '></div>
                	</div>
                	<div class='ui-block-b'>
                		{!! Form::select('billingCountry', $countries, 0, array('placeholder'=>'Country', 'class'=>'half' ) ) !!}
                		<div class='error billingCountry-error bg-danger '></div>
                	</div>
                </fieldset>

                @if( isset( $incentive ) && $project->project_donor_shipping_address == "Y" )
					
					{!! Form::text('shippingAddress1', '', array('placeholder'=>'Shipping Address Line 1' ) ) !!}
					<div class='error shippingAddress1-error bg-danger '></div>
	                {!! Form::text('shippingAddress2', '', array('placeholder'=>'Shipping Address Line 2' ) ) !!}
	                <fieldset class='ui-grid-a'>
                		<div class='ui-block-a'>
    	            		{!! Form::text('shippingCity', '', array('placeholder'=>'City' ) ) !!}
    	            		<div class='error shippingCity-error bg-danger '></div>
    	            	</div>
    	            	<div class='ui-block-b'>
        	        		{!! Form::text('shippingState', '', array('placeholder'=>'State' ) ) !!}
        	        		<div class='error shippingState-error bg-danger '></div>
        	        	</div>
        	        </fieldset>
        	        <fieldset class='ui-grid-a'>
        	        	<div class='ui-block-a'>
        	        		{!! Form::text('shippingZip', '', array('placeholder'=>'Zip' ) ) !!}
        	        		<div class='error shippingZip-error bg-danger '></div>
        	        	</div>
        	        	<div class='ui-block-b'>
        	        		{!! Form::text('shippingCountry', '', array('placeholder'=>'Country' ) ) !!}
        	        		<div class='error shippingCountry-error bg-danger '></div>
        	        	</div>
        	        </fieldset>
                @endif
			</div>
			<div class='ui-btn continue-button' id='continue-to-review'>
            	review order
            </div>
            {!! Form::close( ) !!}
		</div>
	</div>
</div>

<div data-role='page' id='donate-page-three' class='donate'>
	<div data-role='header' class='donate-header'>
		<a href='#donate-page-two' class="ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>Donate</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<div class='donateToName'></div>
		<div class='donateContainer' style='border-bottom: 1px solid #e5e5e5;'>
			<div class='header'>Review Donation</div>
			<div >
				<div class='ui-grid-a' style='padding-top: 10px;'>
					<div class='ui-block-a'>
						<b style='padding-left: 20px;'>Donation Amount</b>
					</div>
					<div class='ui-block-b' >
						<div class='donationEdit' style='padding: 0px; padding-right: 20px;'><a href='#donate-page-one'>Edit</a></div>
					</div>
				</div>
				<div class='donationAmount' style='clear:both; float: none; padding: 20px;'></div>
			</div>
		</div>

		<div class='donateContainer' style='border-bottom: 1px solid #e5e5e5;'>
			<div class='ui-grid-a'>
				<div class='ui-block-a' style='width: 70%;'>
					<div class='header' style='border:none;'>Credit Card Information</div>
				</div>
				<div class='ui-block-b' style='width: 30%;'>
					<div class='donationEdit' style='padding: 0px; padding-right: 20px;'><a href='#donate-page-two'>Edit</a></div>
				</div>
			</div>
			<div class='creditCardInformation' style='padding: 10px 20px;'></div>
			<div class='billingInformationReview' style='padding: 10px 20px;'></div>
		</div>
		<div class='donateTotal'>
			<div class='ui-grid-a'>
				<div class='ui-block-a' style='font-weight: bold; font-size: 18px;'>
					TOTAL:
				</div>
				<div class='ui-block-b billingTotalReview' ></div>
			</div>
		</div>
		<div class='ui-btn continue-button' id='continue-to-paypal'>
        	checkout
        </div>
	</div>
</div>

<div data-role='page' id='thank-you' class='donate'>
	<div class='thank-you-message'>
		<h1>Thank you for your donation!</h1>
		<p>We'll send you an email confirmation shortly.</p>
	</div>
	<div class='thankYouContainer'>Order Summary</div>
	<div class='order-reference thankYouContainer'>
		<b>Reference Number</b>
		<br />
		<div class='transaction-id'></div>
	</div>
	<div class='thankYouContainer'>
		<p style='font-weight: bold;'>Donation Amount</p>
		<div class='donationAmount' style='clear:both; float: none; '></div>
	</div>
	<div class='donateContainer' style='border-bottom: 1px solid #e5e5e5;'>
		<div class='header' style='border:none;'>Credit Card Information</div>
			
		<div class='creditCardInformation' style='padding: 10px 20px;'></div>
		<div class='billingInformationReview' style='padding: 10px 20px;'></div>
	</div>
	<div class='donateTotal'>
		<div class='ui-grid-a'>
			<div class='ui-block-a' style='font-weight: bold; font-size: 18px;'>
				TOTAL:
			</div>
			<div class='ui-block-b billingTotalReview' ></div>
		</div>
	</div>

	<div class='ui-btn ui-icon-carat-r ui-btn-icon-right continue-button' id='back-to-site'>
    	Back to Site
    </div>
</div>

<div data-role='page' id='donate-login' class='donate-login'>
	<div data-role='header' class='donate-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>Donate</h1>
	</div><!-- /header -->
	<div data-role='main' >
		@include("mobile.pages._login")
	</div>
</div>

