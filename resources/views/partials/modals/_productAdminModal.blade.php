<div class='modal fade overlay-content admin-product-modal' tabindex='-1' role='dialog' id='productAdminModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-body'>
				<ul class='nav nav-tabs' role='tablist'>
					<li role='presentation' class='active'>
						<a href='#currentProductsTab' aria-controls='currentProductsTab' role='tab' data-toggle='tab'>Current</a>
					</li>
					<li role='presentation'>
					 	<a href='#archivedProductsTab' aria-controls='archivedProductsTab' role='tab' data-toggle='tab'>Archived</a>
					</li>
					<li role='presentation'>
					 	<a href='#newProductTab' aria-controls='newProductTab' role='tab' data-toggle='tab'>Add New</a>
					</li>
				</ul>
				<div class='tab-content'>
					<div role='tabpanel' class='tab-pane active' id='currentProductsTab' style='column-count: 0;'>
						@include("partials.products._currentProducts")
					</div>
					<div role='tabpanel' class='tab-pane' id='archivedProductsTab' style='column-count: 0;'>
						@include("partials.products._archivedProducts")
					</div>
					<div role='tabpanel' class='tab-pane' id='newProductTab' style='column-count: 0;'>
						@include("partials.products._newProduct")
					</div>
				</div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
			</div>
		</div>
	</div>
</div>