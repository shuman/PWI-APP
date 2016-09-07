Product Progress
<ol class='breadcrumb'>
	<li data-breadcrumb-item='1'><a href='/breadcrumb-product-info'>Product Info</a></li>
</ol>

<div class='new-product-content'>
	<div class='new-product-info' data-product-step='1'>
		@include('partials.products._newProductInfo')
	</div>
	<div class='new-product-modifiers' data-product-step='2'>
		@include('partials.products._newProductModifiers')
	</div>
	<div class='new-product-inventory padding-top-10 padding-bottom-10' data-product-step='3'>
		@include('partials.products._newProductInventory')
	</div>
	<div class='new-product-shipping' data-product-step='4'>
		@include('partials.products._newProductShipping')
	</div>
	<div class='new-product-causes-impacts' data-product-step='5'>
		@include('partials.products._newProductCausesAndImpacts')
	</div>
</div>
<div class='new-product-status row margin-0 padding-10' style='width: 100%;'>
	<div class='col-lg-6 col-md-6 col-sm-6 bg-success text-success text-center hidden'>Your Product has been Uploaded!</div>
	<div class='col-lg-6 col-md-6 col-sm-6 bg-danger text-danger text-center hidden'>There was an error uploading your product.</div>
</div>
<div class='new-product-actions margin-top-10 row'>
	<div class='col-lg-6 col-md-6 col-sm-6 left-action pull-left'>
		<button type="button" class="btn btn-info pull-left prev-step hidden" data-product-prev-step=""></button>
	</div>
	<div class='col-lg-6 col-md-6 col-sm-6 right-action'>
		<button type="button" class="btn btn-info pull-right next-step" data-product-next-step='2'>Modifiers >></button>
	</div>
</div>