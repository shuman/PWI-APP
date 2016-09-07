<div data-role='page' id='purchase-page-one' class='purchase'>
	<div data-role='header' class='fund-header'>
		<a href='#product-page' class="ui-btn ui-icon-carat-l ui-nodisc-icon ui-btn-icon-left ui-btn-icon-notext
">home</a>
		<h1>Purchase</h1>
	</div><!-- /header -->
	<div data-role='main'>
		<div class='product-purchase-desc'>
			@if( sizeof( $images ) > 0 )
				<img src='{!! $prdPath !!}{!! $images[0]->file_path !!}' />
			@else
				<img src='/images/prodPlaceholder.jpg'>
			@endif

			<div class='product-name'>{!! $product->product_name !!}</div>
			<div class='org-name'>By {!! $product->org_name !!}</div>

			<div class='purchase-info'>
				<div class='left'>
			 	@foreach( $modifiers as $modifier )
	                <div>
	                	<span class='name' data-id='{!! $modifier["modifier_id"] !!}'>{!! $modifier["modifier_name"] !!}</span>
	                	<span class='value' data-id='{!! $modifier["modifier_id"] !!}'></span>
	                </div>
	            @endforeach
	            	<div>
	            		<span class='name' >Quantity</span>
	            		<span class='quantity'></span>
	            	</div>
	            </div>
	            <div class='right'>
	            	<span class='price'></span>
	            </div>
			</div>
		</div>

		{!! Form::open( array( 'url' => '/organization/validateFunding', 'name' => 'purchaseCheckoutForm')) !!}
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

			<div class='shipping-info'>
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
            <div class='header'>
            	Billing Address 
            	<div class='sameAsShipping'>
	            	<input type='radio' name='sameAsShipping' data-role='none' value='1' /> Billing same as Shipping
	            </div>
            </div>

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
                		{!! Form::text('billingCity', '', array('placeholder'=>'City' , 'class'=>'half') ) !!}
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

<div data-role='page' id='purchase-page-two' class='purchase'>
	<div data-role='header' class='fund-header'>
		<a href='#product-page-one' class="ui-btn ui-icon-carat-l ui-nodisc-icon ui-btn-icon-left ui-btn-icon-notext
">home</a>
		<h1>Purchase</h1>
	</div><!-- /header -->
	<div data-role='main'>
		<div class='product-purchase-desc'>
			@if( sizeof( $images ) > 0 )
				<img src='{!! $prdPath !!}{!! $images[0]->file_path !!}' />
			@else
				<img src='/images/prodPlaceholder.jpg'>
			@endif

			<div class='product-name'>{!! $product->product_name !!}</div>
			<div class='org-name'>By {!! $product->org_name !!}</div>

			<div class='purchase-info'>
				<div class='left'>
			 	@foreach( $modifiers as $modifier )
	                <div>
	                	<span class='name' data-id='{!! $modifier["modifier_id"] !!}'><b>{!! $modifier["modifier_name"] !!}</b></span>
	                	<span class='value' data-id='{!! $modifier["modifier_id"] !!}'></span>
	                </div>
	            @endforeach
	            	<div>
	            		<span class='name' >Quantity</span>
	            		<span class='quantity'></span>
	            	</div>
	            </div>
	            <div class='right'>
	            	<span class='price'></span>
	            </div>
			</div>
		</div>
		<div class='order-review-shipping-address'>
		    <p class='header'>Shipping Address<span><a href='#fund-page-two'>Edit</a></span></p>
		        <div class='shipping-address'></div>
		    </div>
		    
		    <div class='order-review-cc-billing'>
		        <!--<p class='header'>Credit Card Information</p>-->
		    
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
		    
		    <div class='place-order'>
		        <div>
		        	<p>Item(<span class='quantity' style='float: none;'></span>): <span class='right item-price'></span></p>

		        	<p>Shipping: <span class='right item-shipping'></span></p>

		        	<p>Tax: <span class='right item-tax'></span></p>

		            <p class='header'>Total:<span class='product-total-amount'></span></p>
		        </div>
		    </div>
		    <div> 
	            {!! Form::button('place order', array('class'=>'checkout-button')) !!}
	        </div>
	    </div>
	</div>
</div>

<div data-role='page' id='thank-you' class='purchase'>
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
		<div class='left'>
	 	@foreach( $modifiers as $modifier )
            <div>
            	<span class='name' data-id='{!! $modifier["modifier_id"] !!}'><b>{!! $modifier["modifier_name"] !!}</b></span>
            	<span class='value' data-id='{!! $modifier["modifier_id"] !!}'></span>
            </div>
        @endforeach
        </div>
    </div>
	<div class='donateContainer' style='border-bottom: 1px solid #e5e5e5;'>
		<div class='shipping-container' style='border: 1px solid #e5e5e5;'>
			<p class='header' style='border: none;'>Shipping Information </p>
			<div class='shippingInformationReview' style='padding: 10px 20px;'></div>
		</div>
		<div class='header' style='border:none;'>Credit Card Information</div>
		<div class='creditCardInformation' style='padding: 10px 20px;'></div>
		<p class='header' style='border: none;'>Billing Information </p>
		<div class='billingInformationReview' style='padding: 10px 20px; border: none;'></div>
	</div>
	<div class='donateTotal border-bottom' style='height: auto;'>
		<div class='ui-grid-a'>
			<div>
	        	<p>Item(<span class='quantity' style='float: none;'></span>): <span class='right item-price' style='float: right;'></span></p>

	        	<p>Shipping: <span class='right item-shipping' style='float: right;'></span></p>

	        	<p>Tax: <span class='right item-tax' style='float: right;'></span></p>

	            <p class='header'>Total:<span class='product-total-amount' style='float: right;'></span></p>
	        </div>
		</div>
	</div>

	<div class='ui-btn ui-icon-carat-r ui-btn-icon-right continue-button' data-alias='{!! $product->product_alias !!}' id='back-to-site'>
    	Back to Site
    </div>
</div>
