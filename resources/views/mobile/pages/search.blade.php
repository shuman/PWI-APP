@extends('header')
@section('content')

<div data-role='page' id='search'>
	@include('mobile.headers.generic')

	<div data-role='main' class='ui-content' style='padding:0;' id="indexPage">
		<div class='top-search'>
			<div class='mobile-icon pwi-icon-search'></div>
	        <input type='text' name='mobile-menu-search' placeholder='search' data-enhance='false'/>
	        <span class='pwi-icon-close right-icon exit-search'></span>
	        <div style='clear:both;'></div>
		</div>
		<div class='filters'>
			<div class='items'>
				@if( $filter == "all" )
				<span class='filter-item active'><a>All</a></span>
				@else
				<span class='filter-item'><a>All</a></span>
				@endif
				
				@if( $filter == "products")
				<span class='filter-item active'><a>Products</a></span>
				@else
				<span class='filter-item'><a>Products</a></span>
				@endif

				@if( $filter == "crowdfunding")
				<span class='filter-item active'><a>Crowdfunding</a></span>
				@else
				<span class='filter-item'><a>Crowdfunding</a></span>
				@endif

				@if( $filter == "organizations" )
				<span class='filter-item active'><a>Organizations</a></span>
				@else
				<span class='filter-item'><a>Organizations</a></span>
				@endif
			</div>
		</div>
		<div style='clear: both;'></div>
		@if( $filter == "all" )
        	@include("mobile.modules.organizations")
        	<div class='search-more'><a href='/search/organizations/{!! $term !!}'>See more organizations</a></div>
        @elseif( $filter == "organizations" )
        	@include("modules.organizations")
        @elseif( $filter == "crowdfunding" )
        	
        @elseif( $filter == "products" )
        	
        @endif
    </div>
    <div style='width: 100%; height: 10px;'></div>
	@include('mobile.footer')

</div>
@stop