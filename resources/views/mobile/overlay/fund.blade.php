<div data-role='page' id='fund-page-one' class='fund'>
	<div data-role='header' class='fund-header'>
		<a href='#crowdfunding-page' class="ui-btn ui-icon-carat-l ui-nodisc-icon ui-btn-icon-left ui-btn-icon-notext
">home</a>
		<h1>Donate</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<div class='fund-information'>
			<div class='project-title'>{!! $project->project_title !!}</div>
			<div class='org-name'>{!! $project->org_name !!}</div>
			
		</div>
		<div class='pick-incentive'>Pick an Incentive</div>
		<div class='details-fund border-top border-bottom'>
			<div class='just-contribute'>
                No thanks, just want to contribute
                <div class='donation' data-incentive-id='donate'>
                	<p>Donation Amount</p>
                    <div style='width:100%; display:table;'>
                		<div class='denomination' style='width: 45%; text-align: center; display:table-cell;'>
                			<input type="text" name="fundAmt" data-role='none' >		
                        </div>
                		<div class='action' style='width: 45%; text-align: right; display:table-cell;'>
                			<a href='#fund-page-two' class='continue-button'>Continue</a>
                		</div>
                	</div>
                </div>
            </div>
        </div>

        <div class='fund-incentives-list'>
            @foreach( $incentives as $incentive )
             <div class='incentive border-bottom' data-incentive-id='{!! $incentive->project_incentive_id !!}'>
             	<div style='padding: 10px 20px;'>
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
	                <div class='donation'>
	                	<p>Donation Amount</p>

	                	<div style='width:100%; display:table;'>
	                		<div class='denomination' style='width: 45%; text-align: center; display:table-cell;'>
	                			<input type="text" name="fundAmt" data-role='none' value='{{ $incentive->project_incentive_amount }}'>		
                            	<input type='hidden' name='shippingRequired' value='{!! $incentive->project_donor_shipping_address !!}' >
	                		</div>
	                		<div class='action' style='width: 45%; text-align: right; display:table-cell;'>
	                			<a href='#fund-page-two' class='continue-button'>Continue</a>
	                		</div>
	                	</div>
	                </div>
	            </div>
            </div>
            @endforeach
        </div>
	</div>
</div>

<div data-role='page' id='fund-page-two' class='donate'>
	<div data-role='header' class='donate-header'>
		<a href='#fund-page-one' class="ui-btn ui-icon-carat-l ui-nodisc-icon ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>Donate</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<div class='fund-information'>
			<div class='project-title'>{!! $project->project_title !!}</div>
			<div class='org-name'>{!! $project->org_name !!}</div>
		</div>
		<div class='fund-type'></div>
		<div class='fund-review'>
			<div class='incentive-data' style='display: none;'>
				<div style='display: table-row;'>
					<div class='incentive-name' style='display: table-cell;'></div>
					<div class='incentive-action' style='display: table-cell;'><a href='#fund-page-one'>Edit</a></div>
				</div>
				<div style='display: table-row;'>
					<div class='incentive-desc' style='display: table-cell;'></div>
				</div>
			</div>
			<p>Donation Amount <span class='donation-edit' style='float: right;'><a href='#donate-page-one'>Edit</a></span></p>
			<span class='donation-amount'></span>
		</div>
		{!! Form::open( array( 'url' => '/organization/validateFunding', 'name' => 'fundCheckoutForm')) !!}
	    @if( ! is_null( $user ) )
	    	{!! Form::hidden('user_id', $user->user_id) !!}
	    @else 
	    	{!! Form::hidden('user_id') !!}
	    @endif
		<div class='fundContainer padding-0' style='margin-top: 10px;'>
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

			{!! Form::hidden('showShipping', 'N') !!}
			<div class='shipping-info hidden'>
				<div class='header'>Shipping Address</div>
				<div class='infoContainer' style='border-bottom: 1px solid #e5e5e5;'>
					{!! Form::text('shippingAddress1', '', array('placeholder'=>'Shipping Address Line 1' ) ) !!}
					<div class='error shippingAddress1-error bg-danger '></div>
	                {!! Form::text('shippingAddress2', '', array('placeholder'=>'Shipping Address Line 2' ) ) !!}
	                <fieldset class='ui-grid-a'>
	            		<div class='ui-block-a'>
		            		{!! Form::text('shippingCity', '', array('placeholder'=>'City' ) ) !!}
		            		<div class='error shippingCity-error bg-danger '></div>
		            	</div>
		            	<div class='ui-block-b'>
	    	        		{!! Form::select('shippingState', array('0' => 'Select a Country to Retrieve States'), 0, array('placeholder'=>'State/Province', 'class'=>'form-control padding-left-5', 'style'=>'height:45px;') ) !!}
	    	        		<div class='error shippingState-error bg-danger '></div>
	    	        	</div>
	    	        </fieldset>
	    	        <fieldset class='ui-grid-a'>
	    	        	<div class='ui-block-a'>
	    	        		{!! Form::text('shippingZip', '', array('placeholder'=>'Zip' ) ) !!}
	    	        		<div class='error shippingZip-error bg-danger '></div>
	    	        	</div>
	    	        	<div class='ui-block-b'>
	    	        		{!! Form::select('shippingCountry', $countries, 0, array('placeholder'=>'Country', 'class'=>'half' ) ) !!}
	    	        		<div class='error shippingCountry-error bg-danger '></div>
	    	        	</div>
	    	        </fieldset>
	    	    </div>
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
                            <option value='{!! $key !!}'>{!! $value !!}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                @endif
				{!! Form::text('billingAddress1', '', array('placeholder'=>'Billing Address Line 1' ) ) !!}
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
                		{!! Form::text('billingZip', '', array('placeholder'=>'Zip' ) ) !!}
                		<div class='error billingZip-error bg-danger '></div>
                	</div>
                	<div class='ui-block-b'>
                		{!! Form::select('billingCountry', $countries, 0, array('placeholder'=>'Country', 'class'=>'half' ) ) !!}
                		<div class='error billingCountry-error bg-danger '></div>
                	</div>
                </fieldset>
			</div>
			<div class='ui-btn continue-button' id='continue-to-review'>
            	review order
            </div>
            {!! Form::close( ) !!}
		</div>
	</div>
</div>

<div data-role='page' id='fund-page-three' class='fund'>
	<div data-role='header' class='donate-header'>
		<a href='#fund-page-two' class="ui-btn ui-icon-carat-l ui-icon-nodisc ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>Donate</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<div class='fund-information'>
			<div class='project-title'>{!! $project->project_title !!}</div>
			<div class='org-name'>{!! $project->org_name !!}</div>
		</div>
		<div class='review border-bottom'>Review Order</div>
		<div class='order-review'>
		    <div class='order-review-incentive' style='border-bottom: 1px solid #e5e5e5;'>
		        <p class='header'><span><a href='#fund-page-two'>Edit</a></span></p>
		        <div class='incentive-title'></div>
		        <div class='incentive-description'></div>
		        <div class='incentive-amount margin-top-10'>
		            <div>
		                <p>Donation amount</p>
		                <div class='margin-top-10'>
		                    <span class='donation-amount'></span>
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class='order-review-shipping-address' style='border-bottom: 1px solid #e5e5e5;'>
		        <p class='header'>Shipping Address<span><a href='#fund-page-two'>Edit</a></span></p>
		        <div class='shipping-address'></div>
		    </div>
		   
		    <div class='order-review-cc-billing' style='border-bottom: 1px solid #e5e5e5;'>
		        <div class='order-review-total'>
		           <div class='cc-info' style='padding: 10px 20px; background-color: #fff;'>
		           		<p class='header'>Credit Card Information</p>
		                <div class='credit-card-information'></div>
		            </div>
		           
		            <div class='billing-info' >
		                <p class='header'>Billing Information <span><a href='#fund-page-two'>Edit</a></span></p>
		                <div class='billing-address'></div>
		            </div>
		        </div>
		    </div>
		    
		    <div class='place-order' style='border-bottom: 1px solid #e5e5e5;'>
		        <div>
		            <p class='header'>Total:<span class='donation-amount'></span></p>
		        </div>
		    </div>
		    <div> 
	            {!! Form::button('PLACE ORDER', array('class'=>'checkout-button')) !!}
	        </div>
		</div>
		

        @if( env("APP_ENV") == "live" )
		{!! Form::open( array('url' => 'https://www.paypal.com/cgi-bin/webscr', 'method' => 'post', 'name' => 'paypalStdCheckout', 'class' => 'hidden') ) !!}
		@else
		{!! Form::open( array('url' => 'https://www.sandbox.paypal.com/cgi-bin/webscr', 'method' => 'post', 'name' => 'paypalStdCheckout', 'class' => 'hidden') ) !!}
		@endif
		{!! Form::hidden('cmd') !!}
		{!! Form::hidden('return') !!}
		{!! Form::hidden('notify_url') !!}
		{!! Form::hidden('amount') !!}
		{!! Form::hidden('custom') !!}
		{!! Form::hidden('business') !!}
		{!! Form::hidden('first_name') !!}
		{!! Form::hidden('last_name') !!}
		{!! Form::hidden('email') !!}
		{!! Form::hidden('address1') !!}
		{!! Form::hidden('address2') !!}
		{!! Form::hidden('city') !!}
		{!! Form::hidden('state') !!}
		{!! Form::hidden('zip') !!}
		{!! Form::hidden('country') !!}
		{!! Form::submit('submit') !!}
		{!! Form::close( ) !!}

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
		<div class='order-review-incentive' style='padding: 0px;'>
	        <p class='header'></p>
	        <div class='incentive-title'></div>
	        <div class='incentive-description'></div>
	        <div class='incentive-amount margin-top-10'>
	            <div>
	                <p>Donation amount</p>
	                <div class='margin-top-10'>
	                    <span class='donation-amount'></span>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	<div class='donateContainer' style='border-bottom: 1px solid #e5e5e5;'>
		<div class='shipping-container'>
			<p class='header'>Shipping Information </p>
			<div class='shippingInformationReview' style='padding: 10px 20px;'></div>
		</div>
		<div class='header' style='border:none;'>Credit Card Information</div>
		<div class='creditCardInformation' style='padding: 10px 20px;'></div>
		<p class='header'>Billing Information </p>
		<div class='billingInformationReview' style='padding: 10px 20px; border: none;'></div>
	</div>
	<div class='donateTotal border-bottom'>
		<div class='ui-grid-a'>
			<div class='ui-block-a' style='font-weight: bold; font-size: 18px;'>
				TOTAL:
			</div>
			<div class='ui-block-b donation-amount billingTotalReview' ></div>
		</div>
	</div>

	<div class='ui-btn ui-icon-carat-r ui-btn-icon-right continue-button' data-alias='{!! $project->project_alias !!}' id='back-to-site'>
    	Back to Site
    </div>
</div>

<div data-role='page' id='donate-login' class='donate-login'>
	<div data-role='header' class='donate-header'>
		<a data-rel='back' class='ui-nodisc-icon ui-icon-delete ui-btn-icon-notext'>back</a>
		<h1>Donate</h1>
	</div><!-- /header -->
	<div data-role='main' >
		@include("mobile.pages._login")
	</div>
</div>




