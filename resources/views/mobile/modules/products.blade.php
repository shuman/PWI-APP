@if( sizeof( $products ) ) 
<div class='products'>
	<div class='module-header'>
		<span class='left'>Products</span>
		<!--<span class='right'><a>See More</a></span>-->
	</div>
	<div class='product-module-items'>
		@foreach( $products as $product )
			<div class='product-module'>
				<div class='product-module-img-container '>
		            <a href='/product/{!! $product["alias"] !!}' rel='external'><img src='{!! $product["image"] !!}' align='left'/></a>
		        </div><!-- end .product-module-img-container -->
		        <div class='product-module-data'>
		            <div class='product-module-name pull-left'><a href='/product/{!! $product["alias"] !!}' rel='external'>{!! stripslashes( $product["name"] ) !!}</a></div>
		            
		            <div class='product-org-name'>{!! $product["org_name"] !!}</div>

		            <div class='product-price'>{!! $product["price"] !!}</div>
				</div><!-- end .pull-left -->
		        <div style='clear:both;'></div>
			</div>
		@endforeach
	</div>
</div>
@endif