<div class='input-group margin-top-10' style='width: 100%;'>
	<label for='project_name'>Product Name<sup>*</sup></label>
	<input type='text' name='product_name' size='45' class='form-control' />
</div>
<div class='error product_name-error'></div>

<div class='input-group margin-top-10' style='width: 100%;'>
	<label for='product_sku'>Product SKU<sup>*</sup></label>
	<input type='text' name='product_sku' size='45' class='form-control' />
</div>
<div class='error product_sku-error'></div>

<div class='input-group margin-top-10' style='width: 100%;'>
	<label for='product_category'>Product Category<sup>*</sup></label>
	<select name='product_category' class='form-control' style='height: 45px;'>
		<option value='0'>Select A Category</option>
		@foreach( Helper::getProductCategories( ) as $category )
			@if( $category->category_status == "Y" )
				<option value='{{$category->category_id}}'>{{$category->category_name}}</option>
			@endif
		@endforeach
	</select>
</div>
<div class='error product_category-error'></div>

<div class='input-group margin-top-10' style='width: 100%;'>
	<label for='short_product_description'>Short Product Description</label>
	<textarea name='short_product_description' class='form-control' ></textarea>
</div>

<div class='input-group margin-top-10' style='width: 100%;'>
	<label for='long_product_description'>Long Product Description</label>
	<textarea name='long_product_description' class='form-control' ></textarea>
</div>

<div class='row margin-top-10'>
	<div class='col-lg-6 col-md-6 col-sm-6'>
		<div class="form-group">
			<label for='product_price'>Product Price<sup>*</sup></label>
		    <div class="input-group">
		    	<div class="input-group-addon">$</div>
		      	<input type="text" class="form-control" name='product_price' >
		    </div>
		</div>
		<div class='error product_price-error'></div>
	</div>
	<div class='col-lg-6 col-md-6 col-sm-6'>
		<div class='input-group'>
			<label for='project_quantity'>Quantity<sup>*</sup></label>
			<input type='text' class='form-control' name='product_quantity'  />
			<div class='error product_quantity-error margin-top-15' ></div>
		</div>
	</div>
</div>

<p class='heading'>Photos</p>
<div class='product_photo_list' style='border-spacing: 10px;'>
	<input type='file' name="logo" class="upload" id="productPic" />
	<button type="button" class="btn btn-success upload-photo-image margin-top-10">Upload Image(s)</button>
</div>