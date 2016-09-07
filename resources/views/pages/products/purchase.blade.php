@extends('header')

@section('content')

<style>
    #wrapper{
        background-color:#fff !important;
    }
</style>
{!! HTML::Script( 'js/purchase.js') !!}
{!! Form::hidden('productId', $product->product_id) !!}
{!! Form::hidden('paypal_un', $paypal_un ) !!}
{!! Form::hidden('shipping_cost', $product->product_shipping_fee) !!}
{!! Form::hidden('modifierIds', $passedModIds) !!}
{!! Form::hidden('payment_gateway', '2') !!}
<div class='product-wrapper' style='width: 100%; height: 100%;'>
    <div class='product-checkout row'>
    	<div class='col-lg-6 col-md-6 col-sm-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-3 product-title'>
    		<div class='product-purchase-title'>Purchase: {!! $product->product_name !!}</div>
    		<div class='product-purchase-organization'>{!! $product->org_name !!}</div>
    	</div>

    	<div class='col-lg-6 col-md-6 col-sm-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-3 product-information'>
    		<div class='row'>
    			<div class='col-lg-3 col-md-3 col-sm-3'>
    				@if( file_exists( public_path( ) . $prdPath . $product["image"] ) )
    					<img src='{!! $prdPath !!}{!! $product["image"] !!}' />
    				@else
    					<img src='/images/prodPlaceholder.png' />
    				@endif
    			</div>
    			<div class='col-lg-6 col-md-6 col-sm-6 '>
    				<div class='product-modifiers'>
    					@foreach( $modifiers as $modifier )
    						<div class='margin-top-10'>
    							<b>{!! $modifier["modifier_name"] !!}</b><br />
    			                <select name='modifier-{!! $modifier["modifier_id"] !!}' class='form-control'>
    			                    <option value='0'>Select {!! $modifier["modifier_name"] !!}</option>
    			                    @foreach( $modifier["modifier_options"] as $option )
    			                    	@if( sizeof( $chosenMods ) > 0 )
    			                    		@if( in_array( $option["option_id"] . "|" . $option["option_name"], $chosenMods ) )
    		                    				<option value='{!! $option["option_id"] !!}' data-product-info='{!! $option["option_price"] !!}|{!! $option["option_quantity"] !!}|{!! $option["option_shipping_fee"] !!}' selected>{!! $option["option_name"] !!}</option>
    		                    			@else
    		                    				<option value='{!! $option["option_id"] !!}' data-product-info='{!! $option["option_price"] !!}|{!! $option["option_quantity"] !!}|{!! $option["option_shipping_fee"] !!}'>{!! $option["option_name"] !!}</option>
    		                    			@endif
    			                    	@else
    			                    		<option value='{!! $option["option_id"] !!}' data-product-info='{!! $option["option_price"] !!}|{!! $option["option_quantity"] !!}|{!! $option["option_shipping_fee"] !!}' >{!! $option["option_name"] !!}</option>
    			                    	@endif
    			                    @endforeach
    			                </select>
    			                <div class='error modifier-{!! $modifier["modifier_id"] !!}-error'></div>
    		                </div>
    		            @endforeach
    		        </div>
    	            <div class='margin-top-10'>
    	            	<b>Quantity</b><br />
    	            	<select name='quantity' class='form-control margin-top-10'>
    			        	@if( $quantity == 0 && sizeof( $chosenMods) == 0 )
    				        	<option value='0'>Select Above for Available Quantity</option>
    				        @else
    				        	@for( $i = 1 ; $i <= $maxQuantity ; $i++ )
    				        		@if( $quantity == $i )
    				        			<option value='{!! $i !!}' selected>{!! $i !!}</option>
    				        		@else
    				        			<option value='{!! $i !!}'>{!! $i !!}</option>
    				        		@endif
    				        	@endfor
    				        @endif
    				    </select>
    				</div>
    				<div class='error quantity-error'></div>
    			</div>
    			<div class='col-lg-3 col-md-3 col-sm-3'>
    				<div class='price' data-price='{!! $price !!}'>{!! money_format('%(#0n', $price) !!}</div>
    			</div>

    		</div>
    	</div>

    	<div class='col-lg-6 col-md-6 col-sm-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-3 product-personal'>
    		<div class='details-personal'>
                {!! Form::open( array( 'url' => '/validateDonation', 'name' => 'purchaseCheckoutForm')) !!}
                @if( ! is_null( $user ) )
                    {!! Form::hidden('user_id', $user->user_id) !!}
                @else 
                    {!! Form::hidden('user_id') !!}
                @endif
                <p class='margin-top-15' style='font-size: 24px;'>Enter Personal</p>
                
                <div class='information'>
                    @include('partials.checkout._details')
                    <hr />
                    @include('partials.checkout._shipping')
                    <hr />
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

    <div class='purchase-review row'>
    	<div class='col-lg-6 col-md-6 col-sm-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-3 review'>
            <div class='order-error hidden'></div>
            <p class='header margin-top-15'>Review Order</p>
    	    <div class='order-review'>
    	        <div class='order-review-incentive product-review'>
    				<div class='row'>
    				    <div class='col-lg-3 col-md-3 col-sm-3 col-xs-3'>
    	                    @if( file_exists( public_path( ) . $prdPath . $product["image"] ) )
    							<img src='{!! $prdPath !!}{!! $product["image"] !!}' />
    						@else
    							<img src='/images/prodPlaceholder.png' />
    						@endif
    				    </div>
    				    <div class='col-lg-9 col-md-6 col-sm-6 col-xs-6'>
    						<div class='prod-title'>{!! $product->product_name !!}</div>
    						<div class='prod-org'>from {!! $product->org_name !!}</div>
    						<div class='modifiers'></div>
    						<div class='product-amount'><b>Price</b>: <span></span></div>
    						<div class='quantity'><b>Quantity</b>: <span></span></div>
    						<div class='total'><b>Total</b>: <span></span></div>
    					</div>
    				</div>
    	        </div>
    	        <hr />
    	        <div class='row'>
    	        	<div class='col-lg-6 col-md-6 col-sm-6'>
    			        @include( 'partials.checkout.review._shipping')
    			    </div>
    			</div>
    			<div class='row'>
    	        	<div class='col-lg-6 col-md-6 col-sm-6'>
    			        <div class='order-review-cc-billing'>
    			            <div class='order-review-total'>
    			                @include('partials.checkout.review._ccInfo')
    							
    			                @include('partials.checkout.review._billing')
    			            </div>
    			        </div>
    			    </div>
    			    <div class='col-lg-6 col-md-6 col-sm-6 padding-left-0' style='vertical-align: bottom; height:auto; min-height: 238px;'>
    			    	{!! Form::button('edit information', array('class'=>'edit-button', 'style'=>'position:absolute; bottom: 0px;')) !!}
    			    </div>
    			</div>
    	        <hr />
    	        <div class='place-order row'>
    	        	<div class='col-lg-12 col-md-12 col-sm-12'>
    			        <div class='row'>
    				        <div class='col-lg-12 col-md-12 col-sm-12 '>
    					        <div class='row'>
    						        <div class='col-lg-3 col-md-3 col-sm-3 quantity'>Item (<span></span>):</div>
    							    <div class='col-lg-3 col-md-3 col-sm-3 item-price text-right'></div> 
    					        </div>
    					        <div class='row'>
    						        <div class='col-lg-3 col-md-3 col-sm-3'>Shipping:</div>
    							    <div class='col-lg-3 col-md-3 col-sm-3 item-shipping text-right'></div> 
    					        </div>
    					        <div class='row'>
    						        <div class='col-lg-3 col-md-3 col-sm-3 '>Tax:</div>
    							    <div class='col-lg-3 col-md-3 col-sm-3 item-tax text-right'></div> 
    					        </div>
    					        <hr />
    					        <div class='row'>
    						        <div class='col-lg-3 col-md-3 col-sm-3 product-total'>Total:</div>
    							    <div class='col-lg-3 col-md-3 col-sm-3 item-total text-right product-total-amount'></div> 
    							    <div class='col-lg-6 col-md-6 col-sm-6 col-xs-6' >
    	                				{!! Form::button('place order', array('class'=>'checkout-button')) !!}
    	            				</div>
    					    	</div>
    				        </div>
    				    </div>
    				</div>
    			</div>
    	    </div>
    	</div>
    </div>
</div>
@stop