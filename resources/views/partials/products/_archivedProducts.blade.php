@foreach( Helper::getArchivedProducts( $org->org_id) as $product )
	<li class="list-group-item" data-product-id='{{ $product->product_id }}'>
		<span class='badge remove-product' data-toggle="tooltip" data-placement="top" title="Delete">
			<span class='glyphicon glyphicon glyphicon-trash' aria-hidden='true'></span>
		</span>
		<span class='badge edit-product' data-toggle="tooltip" data-placement="top" title="Edit"  />
			<span class='glyphicon glyphicon glyphicon-pencil' aria-hidden='true'></span>
		</span>
		<div class='crowdfunding-list-project-image' data-alias='{!! $product->product_alias !!}' style='background: url({!! $product->product_image_id !!}) top left no-repeat; background-size: cover;'>
		</div>
		
		<div class='project-module-name'><a href='/product/{!! $product->product_alias !!}'>{!! $product->product_name !!}</a></div>
	</li>
@endforeach