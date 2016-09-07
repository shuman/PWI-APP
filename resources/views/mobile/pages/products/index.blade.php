@extends('header')
@section('content')

<div data-role='page' id='org-page'>
	<div data-role='panel' id='filterpanel' data-display="overlay" data-position="right">
		<div class='filter-page'>
			<div class='filter-page-header' style='display:table; width: 100%;'>
				<div class='filter-header-row'>
					Filter
					<span style='float: right;'>Done</span>
				</div>
			</div>
			<div class='product-filters'>
		        <div class='product-filter'>
		            <div class='title'>Categories</div>
		            <div class='filter-items'>
		                <ul>
		                    <li data-filter-type='category' data-cat-id='' data-amount='100'>All Categories</li>
		                    @foreach( $categories as $category )
		                       <li data-filter-type='category' data-cat-id='{!! $category["id"] !!}' data-amount='{!! $category["cnt"] !!}'>{!! $category["name"] !!} ( {!! $category["cnt"] !!} )</li>
		                    @endforeach
		                </ul>
		            </div>
		        </div>
		        <div class='product-filter'>
		            <div class='title'>Price</div>
		            <div class='filter-items'>
		                <ul >
		                    @foreach( $priceFilters as $priceFilter)
		                        @if( (int)$priceFilter['count'] > 0 )
		                        <li data-filter-type='price' data-price='{!! $priceFilter['value'] !!}' data-amount='{!! $priceFilter['count'] !!}'>{!! $priceFilter['text'] !!} ( {!! $priceFilter['count'] !!} )</li>
		                        @endif
		                    @endforeach
		                </ul>
		            </div>
		        </div>
		        @if( 1 == 0 )
		        <div class='product-filter rating-filter'>
		            <div class='title'>Rating</div>
		            <div class='filter-items'>
		                <ul class='no-list-style'>
		                    @foreach( $ratingFilters as $ratingFilter)
		                        @if( (int)$ratingFilter["count"] > 0 )
		                        <li data-filter-type='rating' data-rating='{!! $ratingFilter["value"] !!}' data-amount='{!! $ratingFilter["count"] !!}'>
		                            @for( $r = 1 ; $r <= 5 ; $r++ )
		                                @if( $r <= $ratingFilter["value"] )
		                                    <span class="star fill" >
		                                        <i class="pwi-icon-star"></i>
		                                    </span>
		                                @else
		                                    <span class="star" >
		                                        <i class="pwi-icon-star"></i>
		                                    </span>
		                                @endif
		                            @endfor
		                            & Up ( {!! $ratingFilter["count"] !!} )
		                        </li>
		                        @endif
		                    @endforeach
		                </ul>
		            </div>
		        </div>
		        @endif
	        </div>
		</div>
	</div><!-- /panel -->
	@include('mobile.headers.generic')
	<div data-role='main' class='ui-content' style='padding:0;' id="organization-page">
		@if( sizeof( $products ) ) 
		<div class='products'>
			<div class='module-header'>
				<span class='left'>Products</span>
				<span class='right'><a href='#filterpanel' class='filter-products'>Filter</a></span>
			</div>
			<div class='product-module-items'>
				@foreach( $products as $product )
					<div class='product-module'>
						<div class='product-module-img-container '>
				            <a href='/product/{!! $product["product_alias"] !!}' rel='external'><img src='{!! $product["image"] !!}' align='left'/></a>
				        </div><!-- end .product-module-img-container -->
				        <div class='product-module-data'>
				            <div class='product-module-name pull-left'><a href='/product/{!! $product["product_alias"] !!}' rel='external'>{!! stripslashes( $product["name"] ) !!}</a></div>
				            
				            <div class='product-org-name'>{!! $product["org_name"] !!}</div>

				            <div class='product-price'>{!! $product["price"] !!}</div>
						</div><!-- end .pull-left -->
				        <div style='clear:both;'></div>
					</div>
				@endforeach
			</div>
		</div>
		@endif

		@include("mobile.partials._browseby")
		
		<div class='backToTop' > <div>Back to top</div></div>

		<div style='width: 100%; height: 10px;'></div>

	</div>
	
	@include('mobile.footer')
</div>
<script type='text/javascript'>

	var filters = {};

	$(".filter-products").on("click", function( e ){
        $("#filterpanel").panel("open");

        e.preventDefault( );
        e.stopPropagation( ); 
	});

	$(".filter-items ul li").on("click", function( e ){

		alert(' filter item ');
        
        var removeItem = false;
        
        if( $(this).hasClass("chosen-filter") ){
            $(this).removeClass('chosen-filter');
            removeItem = true;
        }else{
            $(this).parent( ).find("li").removeClass('chosen-filter');
            $(this).addClass('chosen-filter');    
        }
        
        if( parseInt( $(this).data("amount") ) > 0 ){
        
            var type = $(this).data('filter-type');

            switch( type ){

                case "price":

                    if( removeItem ){
                        delete filters.price;
                    }else{
                        var price = $(this).data("price");

                        filters.price = price;    
                    }

                break;
                case "category":

                    if( removeItem ){
                        delete filters.cat_id;
                    }else{
                        var cat_id = $(this).data("cat-id");

                        filters.cat_id = cat_id    
                    }

                break;
                case "rating":

                    if( removeItem ){
                        delete filters.rating;
                    }else{
                        var rating = $(this).data("rating");

                        filters.rating = rating;        
                    }

                break;
            }

            var $productTemplate = $("<div class='product-module'>" + $(".product-module:first").html( ) + "</div>");

            $.ajax({
                method: "post",
                url: "/products/filter",
                data: filters,
                dataType: "json",
                success: function( resp ){

                    if( resp.status == 1 ){

                        if( resp.data.length == 0 ){
                        	$("#filterpanel").panel("close");
                        }else{

                        	$(".product-module-items").empty( );

                        	$.each(resp.data, function(count, item){

                                $productTemplate.find(".product-module-img-container a").attr("href", "/product/" + item.product_alias);

                                $productTemplate.find(".product-module-img-container img").attr("src", item.image);

                                $productTemplate.find(".product-module-name a").attr("href", "/product/" + item.product_alias);

                                $productTemplate.find('.product-module-name a').text( item.name.stripSlashes( ) );

                                $productTemplate.find('.product-org-name').text( item.org_name.stripSlashes( ) );

                                $productTemplate.find('.product-price').text( item.price );

								$(".product-module-items").append("<div class='product-module'>" + $productTemplate.html( ) + "</div>");
                            });

                            $("#filterpanel").panel("close");
                        }
                    }
                }
            });
        }
    });
</script>
@stop