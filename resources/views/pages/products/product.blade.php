@extends('header')

@section('content')

{!! Form::hidden('paypalUn', $paypal_un) !!}
{!! Form::hidden('product_name', $product->product_name) !!}
{!! Form::hidden('org_name', $product->org_name) !!}
{!! Form::hidden('shipping', $product->product_shipping_fee) !!}

<div class='modal white' id='productCheckout'>
    <div class='checkout'>
        <div class='exit-crowdfunding-checkout exit pull-right' data-control="#productCheckout">
            <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
        </div>
        <div class='title'>{!! $product->product_name !!}</div>
        <div class='organization'>{!! $product->org_name !!}</div>
	</div>
</div>

<div class='row'>
    <div class='col-lg-4 col-md-4 ' >
        <!-- First Product Image -->
        <div class='product-current-image'>
            @if( isset( $images[0] ) )
                <img src='{!! $prdPath !!}{!! $images[0]->file_path !!}' class='img-responsive'/>
            @else
                <img src='/images/prodPlaceholder.png' class='img-responsive'/>
            @endif 
            
        </div><!-- end .product-current-image -->
        
        <!-- All product images -->
        @if( sizeof( $images ) > 1  )
        <div class='product-images'>
            <div class='row'>
            @foreach( $images as $image )
                <div class='col-lg-3 col-md-3 product-image'><img src='{!! $prdPath !!}{!! $image->file_path !!}' class='img-responsive'/></div>
            @endforeach
            </div>
        </div><!-- end .product-images -->
        @endif

        <div class='add-to-cart-container margin-top-10'>
            {!! Form::button("buy now", array("class"=>"add-to-cart", "data-alias" => $product->product_alias, "data-id" => $product->product_id))!!}
        </div>
        <div class='left-actions'>
            <div class='button-container'>
                {!! Form::button("follow", array("class"=>"follow"))!!}
                {!! Form::button("share", array("class"=>"share margin-left-35"))!!}
            </div>
        </div>
    </div>
    <!-- Main Product Body/Description -->
    <div class='col-lg-6 col-md-6 margin-left-10 ' >
        <div class='product-title'>{!! $product->product_name !!}</div>
        <div class='product-org'>By {!! $product->org_name !!} <a href='/organization/{!! $product->org_alias !!}'>Visit Organization Profile</a></div>
        <!-- Price and Rating for Product -->
        <div class='price-and-rating'>
            <div class='price pull-left' data-price='{!! $product->product_sales_price !!}'>{!! money_format('%(#10n', $product->product_sales_price ) !!}</div>
            <div class='rating pull-right padding-top-5'>
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
                <span class='padding-left-10'>
                ( {!! sizeof( $reviews ) !!} Reviews )
                </span>
            </div>
        </div><!-- end .price-and-rating -->
        <hr />
        <!-- Causes and Impacts for products -->
        <div class='impacts-and-causes'>
            <div class='causes pull-left product-section'>
	            @if( sizeof( $causes) > 0 )
                <p class='title'>Causes</p>
                @foreach( $causes as $cause )
                    <div class='cause-name'>
                        <div class='cause-icon' data-link="{!! $cause['alias'] !!}">
                            <i class='pwi-cause-{!! $cause["icon"] !!}-stroke pwi-icon-2em pull-left'></i>
                            <div class='cause-name-text pull-left padding-top-5 padding-left-5'>{!! $cause["name"] !!}</div>
                        </div>
                    </div>
                @endforeach
                @endif
            </div>
            <div class='impacts pull-right product-section'>
                <p class='title'>Areas of Impact</p>
                @foreach( $impacts as $country )
                <div class='row'>
                    <div class='col-lg-3 col-md-3'>
                        <div class='img-thumbnail flag-wrapper margin-bottom-5' style='height: 33px; width: 50px;  margin-left: 5px;'>
                            <span class='flag-icon flag flag-background flag-icon-{!! strtolower( $country["country_iso_code"] ) !!}'></span>    
                        </div> 
                    </div>
                    <div class='col-lg-9 col-md-9 country-name padding-0 padding-top-15'><a href='/country/{!! $country["country_alias"] !!}'>{!! $country["country_name"] !!}</a></div>
                </div>
                @endforeach
            </div><!-- end .impacts -->
        </div><!-- end .impacts-and-causes -->
        <hr />
        <!-- Product Modifiers -->
        <div class='product-modifiers'>
            @foreach( $modifiers as $modifier )
                <select name='modifier-{!! $modifier["modifier_id"] !!}' class='form-control'>
                    <option value='0'>Select {!! $modifier["modifier_name"] !!}</option>
                    @foreach( $modifier["modifier_options"] as $option )
                    <option value='{!! $option["option_id"] !!}' data-product-info='{!! $option["option_price"] !!}|{!! $option["option_quantity"] !!}|{!! $option["option_shipping_fee"] !!}'>{!! $option["option_name"] !!}</option>
                    @endforeach
                </select>
                <div class='error modifier-{!! $modifier["modifier_id"] !!}-error'></div>
            @endforeach
        </div><!-- end .product-modifiers -->
        
        <div class='inline-group margin-top-10'>
	        <select name='quantity' class='form-control'>
		        <option value='0'>Select Modifier Above for Available Quantity</option>
		    </select>
		</div>
        <div class='error quantity-error'></div>
        <hr />
        <!-- Product Description -->
        <div class='product-description product-section'>
            <p class='title'>Description</p>
            {!! $product->product_full_desc !!}
        </div><!-- end .product-description -->
        
        
        <!-- Product Impacts -->
        <!--<div class='product-impacts product-section'>
            <p class='title'>Product Impacts</p>
        </div>--><!-- end .product-impacts -->
        
        @if( $relatedProductsCount > 0 )
        <!-- Related Products -->
        <div class='product-related-products product-section'>
            <p class='title'>Related Products</p>
            @foreach( $relatedProducts as $relatedProduct )
            <div class='product-module-list no-box-outline' style='background-color: transparent;'>
                <div class="product-module">
                    <div class='product-module-top'>
                        <a href='/product/{!! $relatedProduct["product_alias"] !!}'>
                            <img src='{!! $relatedProduct["image"] !!}' align='left' />
                        </a>
                        <div class='product-module-name'><a href='/product/{!! $relatedProduct["product_alias"] !!}'>{!! $relatedProduct["name"] !!}</a></div>
                            <div class='product-module-org-name'>
                                <span class='product-price'>{!! $relatedProduct["price"] !!}</span> from {!! stripSlashes( $relatedProduct["org_name"] ) !!}
                            </div>
                            <div class='rating'>
                                @for($i = 1; $i < 6; $i++ )
                                    @if( $i <= $relatedProduct["rating"] )
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
                            <div class='product-module-desc'>
                                @if( sizeof( $relatedProduct["descExp"] ) < 50 )
                                    {!! $product["sdesc"] !!}
                                @else
                                    @for( $i = 0 ; $i < sizeof( $relatedProduct["descExp"] ) ; $i++ )
                                        @if( $i < 50 || $i > 50 )
                                            {!! $relatedProduct["descExp"][$i] !!}
                                        @else( $i == 50 )
                                            <a href='' class='readmore'>...See More</a>
                                            <span class='more'>{!! $relatedProduct["descExp"][$i] !!}
                                        @endif
                                    @endfor
                                    </span><a href='#' class='readless'>Show Less</a>
                                @endif
                            </div>
                        </div>
                    <div style='clear:both;'></div>
                </div>        
            </div>
            @endforeach
        </div><!-- end .product-related-products -->
        @endif
        
        <!-- Reviews -->
        <div class='product-reviews product-section'>
            <div class='review-list'>
                <p class="title">Reviews</p>
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
            <hr />
            <div class='post-review'>
                @include("modules.postcomment")
            </div>
        </div>
        
    </div>
</div>
@stop