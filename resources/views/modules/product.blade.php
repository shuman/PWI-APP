@if( count( $products ) > 0 )
<div class='product-module-list padding-left-5 margin-top-10 col-lg-12'>
    @if( isset( $isAdmin ) )
        @if( $isAdmin )
            <div class='edit-cog' style='color: #e5e5e5;' data-toggle='modal' data-target='#productAdminModal'>
                <i class='fa fa-cog cog'></i>
            </div>
            @include("partials.modals._productAdminModal")
        @endif
    @endif
    <div class='product-module-list-header'>
        @if( isset( $country->country_name ) )
            Products in {!! $country->country_name !!} 
        @else
            Products
        @endif
        
        @if( ! isset( $org ) )
        <span class='margin-left-5 margin-right-5'></span>
        {!! HTML::link($prdViewAll, 'View all') !!}
        @endif
        <!--<a href='{!! $prdViewAll !!}'>View all</a>-->
    </div><!-- end .product-module-list-header --> 
    <div class='products-module'>
        @foreach( $products as $product )
        <div class="product-module">
            <div class='product-module-top'>
                <a href='/product/{!! $product["alias"] !!}'><img src='{!! $product['image'] !!}' align='left' /></a>
                <!-- Product Name -->
                <div class='product-module-name'><a href='/product/{!! $product["alias"] !!}'>{!! $product["name"] !!}</a></div>
                
                <!-- Product Price / Organization Name -->
                <div class='product-module-org-name'>
                    <span class='product-price'>{!! $product["price"] !!}</span>{!! $product["org_name"] !!}
                </div><!-- end .product-module-org-name -->
                
                <div class='rating'>
                    @for($i = 1; $i < 6; $i++ )
                        @if( $i <= $product["rating"] )
                            <span class="star fill" >
                                <i class="icon pwi-icon-star pwi-icon-2em"></i>
                            </span>          
                        @else
                            <span class="star" >
                                <i class="icon pwi-icon-star pwi-icon-2em"></i>
                            </span>
                        @endif
                    @endfor
                </div><!-- .rating -->
                
                <!-- Product Description -->
                <div class='product-module-desc'>
                    {!! $product["sdesc"] !!}
                </div><!-- end .product-module-desc -->
            </div>
            <div style='clear:both;'></div>
        </div>
        @endforeach
    </div><!-- end .product-module -->
</div><!-- end .product-module-list -->
@else
    @if( isset( $isAdmin ) )
        @if( $isAdmin )
            <div class='btn btn-primary margin-bottom-10' style='width: 100%;' data-toggle='modal' data-target='#productAdminModal' >
            Add New Product
            </div>  
            @include("partials.modals._productAdminModal")
        @endif
    @endif
@endif