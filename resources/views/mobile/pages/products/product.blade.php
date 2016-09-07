@extends('header')
@section('content')

{!! HTML::style('/css/fotorama.css') !!}
{!! HTML::script('/js/fotorama.js') !!}
{!! HTML::script('/js/purchase-mobile.js') !!}
<div data-role='page' id='product-page'>
	@include('mobile.headers.generic')

	<div data-role='main' class='ui-content' style='padding:0;' >
		{!! Form::hidden('paypalUn', $paypal_un) !!}
		{!! Form::hidden('product_name', $product->product_name) !!}
		{!! Form::hidden('org_name', $product->org_name) !!}
		{!! Form::hidden('shipping', $product->product_shipping_fee) !!}
		{!! Form::hidden('alias', $product->product_alias) !!}
		<div class='product-top'>
			<div class='product-name'>{!! $product->product_name !!}</div>
			<div class='org-name'>By {!! $product->org_name !!}</div>
		</div>

		@if( sizeof( $images) > 0 )
		<div class='product-images fotorama'>
			@foreach( $images as $image )
				<img src='{!! $prdPath !!}{!! $image->file_path !!}' />
			@endforeach
		</div>
		@else
		<div class='product-images'>
			<img src='/images/prodPlaceholder.jpg'>
		</div>
		@endif
		<div class='price-and-rating'>
            <div class='price ' data-price='{!! $product->product_sales_price !!}'>{!! money_format('%(#10n', $product->product_sales_price ) !!}</div>
            <div class='rating'>
            @for($i = 1; $i < 6; $i++ )
                @if( $i <= $rating )
                    <span class="star fill" >
                        <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
                    </span>          
                @else
                    <span class="star" >
                        <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
                    </span>
                @endif
            @endfor
            </div>
        </div><!-- end .price-and-rating -->

		<div class='actions margin-top-10'>
			<div class='follow top' data-id='{!! $product->product_id !!}' data-type='product'>
				<a href='#follow'>
				@if( $following == 1 )
				unfollow
				@else
				follow
				@endif
				</a>
			</div>
			<div class='share top'>
				<a href='#share'>share</a>
			</div>
		</div>

		<div class='product-options'>
			<div class='product-modifiers'>
				@foreach( $modifiers as $modifier )
	                <select name='modifier-{!! $modifier["modifier_id"] !!}' class='form-control ui-nodisc-icon' data-mod-id='{!! $modifier["modifier_id"] !!}'>
	                    <option value='0'>Select {!! $modifier["modifier_name"] !!}</option>
	                    @foreach( $modifier["modifier_options"] as $option )
	                    <option value='{!! $option["option_id"] !!}' data-product-info='{!! $option["option_price"] !!}|{!! $option["option_quantity"] !!}|{!! $option["option_shipping_fee"] !!}'>{!! $option["option_name"] !!}</option>
	                    @endforeach
	                </select>
	                <div class='error modifier-{!! $modifier["modifier_id"] !!}-error'></div>
	            @endforeach
            </div>
			<select name='quantity' class='form-control'>
		        <option value='0'>Select Modifier Above for Available Quantity</option>
		    </select>
		</div>

		<div class='actions margin-top-10'>
			<div class='donate bottom'>
				<a href='#' class='add-to-cart' data-id='{!! $product->product_id !!}'>buy</a>
			</div>
		</div>

		<div class='impacts-causes'>
			@if( sizeof( $causes ) > 0 )
			<div class='title'>Causes</div>
				<ul data-role="listview" data-inset='true' data-shadow='false'>
				@foreach( $causes as $cause )
					<li data-role='collapsible' data-inset='false'>
						<h2>
							<i class='pwi-cause-{!! $cause["icon"] !!}-stroke pwi-icon-2em pull-left'></i> <span>{!! $cause["name"] !!}</span>
						</h2>
						<p class='cause-desc'>
						{!! $cause["desc"] !!}
						</p>
					</li>
				@endforeach
				</ul>
			@endif
			@if( sizeof( $impacts ) )
			<div class='title'>Areas of Impact</div>
				<ul data-role="listview" data-inset="true" data-shadow="false" style='margin: 0px;'>
				@foreach( $impacts as $impact )
					<li>
						<a href='/country/{!! $impact["country_alias"] !!}' rel="external">
							<span class='flag-icon flag flag-background flag-icon-{!! strtolower( $impact["country_iso_code"] ) !!}'></span>
							<span class='country-name'>{!! $impact["country_name"] !!}</span>
						</a>
					</li>
				@endforeach
				</ul>
			@endif
		</div>

		@if( ! empty( $product->product_full_desc) )
		<div class='product-description margin-top-10'>
			<div class='title'>Description</div>
			<div class='content'>{!! $product->product_full_desc !!}</div>
		</div>
		@endif

		@if( sizeof( $reviews) > 0 )
		<div class='product-reviews'>
			<div class='title'>Reviews</div>
			<div class='reviews'>
            @foreach( $reviews as $review )
            	<div class='review'>
                    <div class='review-top'>
                        <div class='name'>{!! $review->comment_username !!}</div>
                        <div class='rating'>
                        @for($i = 1; $i < 6; $i++ )
                            @if( $i <= $review->comment_rating )
                                <span class="star fill" >
                                    <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
                                </span>          
                            @else
                                <span class="star" >
                                    <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
                                </span>
                            @endif
                        @endfor
                        </div>
                    </div>
                    <div class='content'>{!! $review->comment_text !!}</div>
                </div>
            @endforeach
            </div>
		</div>
		@else
		<div class='writeReview margin-top-10'><a href='#review'>Write a Review</a></div>
		@endif

		@if( $relatedProductsCount > 0 )
        <!-- Related Products -->
        <div class='product-list'>
            <div class='title'>Related Products</div>
            @for( $i = 0; $i < 3 ; $i++ )
	            @if( isset( $relatedProducts[$i] ) )
	            <div class='product-item'>
	                <a href='/product/{!! $relatedProducts[$i]["product_alias"] !!}'>
	                    <img src='{!! $relatedProducts[$i]["image"] !!}' align='left' />
	                </a>
	                <div class='item-title'><a href='/product/{!! $relatedProducts[$i]["product_alias"] !!}'>{!! $relatedProducts[$i]["name"] !!}</a></div>
	                <div class='org-name'>
	                     {!! stripSlashes( $relatedProducts[$i]["org_name"] ) !!}
	                </div>
	                <div class='price'>
	                	{!! $relatedProducts[$i]["price"] !!}
	                </div>
	            </div>
	            @endif
            @endfor

            @if( $relatedProductsCount > 3 )
            <div class='more-products'><a href='#moreproducts'>See more products</a></div>
            @endif
        </div><!-- end .product-related-products -->
        @endif
		
		<div class='backToTop' > <div>Back to top</div></div>

		<div style='width: 100%; height: 10px;'></div>
	</div>
	@include('mobile.footer')
</div>
@include("mobile.overlay.purchase")
@stop