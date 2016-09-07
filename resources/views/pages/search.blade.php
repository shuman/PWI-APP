@extends('header')
@section('content')
<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12'>
		@if( $filter == "all" )
		<div class='filter all-filter pull-left active' >
		@else
		<div class='filter all-filter pull-left' >
		@endif
			<a href='/search/{!! $term !!}'>All</a>
		</div>
		@if( $filter == "organizations" )
		<div class='filter organizations-filter pull-left active'>
		@else
		<div class='filter organizations-filter pull-left'>
		@endif
			<a href='/search/organizations/{!! $term !!}'>Organizations</a>
		</div>
		@if( $filter == "crowdfunding" )
		<div class='filter crowdfunding-filter pull-left active'>
		@else
		<div class='filter crowdfunding-filter pull-left '>
		@endif
			Crowdfunding
		</div>
		@if( $filter == "products" )
		<div class='filter products-filter pull-left active'>
		@else
		<div class='filter products-filter pull-left '>
		@endif
			Products
		</div>
	</div>
</div>

<hr class='filter-divider'>
<div class='row margin-top-25'>
    <div class='col-lg-6 col-md-6'>
        @if( sizeof( $orgs) == 0 )
        
        @else
        
        	@if( $filter == "all" )
        		@include("modules.organizations")
        		<div class='search-more'><a href='/search/organizations/{!! $term !!}'>More organizations related to"{!! stripslashes( $term )!!}"</a></div>
        		<hr />
        	@elseif( $filter == "organizations" )
        		@include("modules.organizations")
        	@elseif( $filter == "crowdfunding" )
        	
        	@elseif( $filter == "products" )
        	
        	@endif
        @endif 
    </div>
    <div class='col-lg-3 col-md-3'>
        <div class='search-browse-by'>
            <div class='list-header margin-bottom-15'>Causes</div>
            <ul>
            @foreach( $causes as $cause )
	            <li><a>{!! $cause !!}</a></li>
            @endforeach
            </ul>
            <div class='list-header'>Countries</div>
            <ul>
            @foreach( $countries as $country )
            	<li><a>{!! $country !!}</a></li>
            @endforeach
            </ul>
        </div>
        <hr />
    </div>
</div>
@stop