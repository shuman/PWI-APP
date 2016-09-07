<h3>General Product Shipping Information<br />
	<small>Please fill out general Shipping information for your product</small>
</h3>

<div class='row margin-top-10' id='productGeneralShippingLine1'>
	<div class='col-lg-6 col-md-6 col-sm-6'>
		<div class="form-group">
			<label for='product_price'>Product Flat Rate Shipping Fee<sup>*</sup></label>
		    <div class="input-group">
		    	<div class="input-group-addon">$</div>
		      	<input type="text" class="form-control" name='product_flat_rate_shipping_fee' placeholder="0">
		      	<div class="input-group-addon">.00</div>
		    </div>
		</div>
		<div class='error product_flat_rate_shipping_fee-error'></div>
	</div>
	<div class='col-lg-6 col-md-6 col-sm-6'>
		<div class='input-group'>
			<label for='project_quantity'>Product Estimated Shipping Time<sup>*</sup></label>
			<input type='text' class='form-control' name='product_shipping_time' placeholder='Time ( in days )'/>
			<div class='error product_shipping_time-error margin-top-15' ></div>
		</div>
	</div>
</div>

<div class='row margin-top-10' id='productGeneralShippingLine2'>
	<div class='col-lg-6 col-md-6 col-sm-6'>
		<div class="form-group">
			<label for='product_price'>Product Weight (lbs)<sup>*</sup></label>
		    <div class="input-group">
		    		<input type="text" class="form-control" name='product_weight' placeholder="0">
		      	<div class="input-group-addon">lbs</div>
		    </div>
		</div>
		<div class='error product_weight-error'></div>
	</div>
	<div class='col-lg-6 col-md-6 col-sm-6'>
		<div class='input-group'>
			<label for='project_quantity'>Product Dimensions (LxWxH - Inches) <sup>*</sup></label>
			<div class='row'>
				<div class='col-lg-4'>
					<input type='text' class='form-control' name='product_dimensions_length' placeholder='Length' />
				</div>
				<div class='col-lg-4'>
					<input type='text' class='form-control' name='product_dimensions_width' placeholder="Width" />
				</div>
				<div class='col-lg-4'>
					<input type='text' class='form-control' name='product_dimensions_height' placeholder="Height" />
				</div>
			</div>
			<div class='error product_dimensions-error margin-top-15' ></div>
		</div>
	</div>
</div>

<h3>Available Shipping Methods<br />
	<small>Please choose a shipping method for this product</small>
</h3>

<div class='product-shipping-methods-container'>
	<div class="btn-group" data-toggle="buttons" style='width: 100%;'>
	  <label class="btn btn-primary" style='width: 33%;'>
	    <input type="checkbox" autocomplete="off" value='usps'> USPS
	  </label>
	  <label class="btn btn-primary" style='width: 33%;'>
	    <input type="checkbox" autocomplete="off" value='ups'> UPS
	  </label>
	  <label class="btn btn-primary" style='width: 33%;'>
	    <input type="checkbox" autocomplete="off" value='fedex'> FedEx
	  </label>
	</div>
	
	<div class='error product-shipping-method-errors'></div>
</div>

<div class='modifier-shipping-items'></div>