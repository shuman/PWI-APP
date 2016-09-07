<div class='browseBy margin-top-10'>
	<div class='title'>Also Browse By</div>
	<div class='browse-item'>
		<a href='#causes' data-transition="slide" >Causes</a>
	</div>
	<div class='browse-item'>
		<a <a href='#continents' data-transition="slide">Countries</a>
	</div>

	@if( ! isset( $org ) )
	<div class='browse-item'>
		<a href='/organizations' rel="external">Organizations</a>
	</div>
	@endif

	@if( ! isset( $projects ) )
	<div class='browse-item'>
		<a href='/crowdfunding' rel="external">Causes</a>
	</div>
	@endif

	@if( ! isset( $products ) )
	<div class='browse-item'>
		<a href='/products' rel="external">Products</a>
	</div>
	@endif
</div>