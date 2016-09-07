@extends('header')
@section('content')
{!! HTML::script('js/donate-mobile.js') !!}
<div data-role='page' id='cause-page'>
	@include('mobile.headers.generic')

	<div data-role='main' class='ui-content' style='padding:0;' id="cause-page">
		<input type='hidden' name='pagetype' value='organization' />
		<input type='hidden' name='entityName' value='{!! $cause->cause_name !!}' />
		<input type='hidden' name='id' value='{!! $cause->cause_id !!}' />
		<input type='hidden' name='paypalUn' value='{!! $paypal_un !!}' />
		<input type='hidden' name='payment_gateway' value='2' />

		<div class='cause-top'>
			<div class='coverphoto' style='background: url( {!! $causeImgPath !!}{!! $coverphoto !!} ) no-repeat center center; background-size: cover;'></div>
			
			<div class='cause-name'>
				@if( strlen( $icon ) == 1 )
                    <i data-icon="{!! $icon !!}" class="icon"></i>
                @else
                    <i class='{!! $icon !!} pull-left'></i>
                @endif
                <p>{!! stripslashes( $cause->cause_name ) !!}</p>
			</div>
			<div class='actions'>
				<div class='donate'>
					<a href='#donate-page-one'>donate</a>
				</div>
				<div class='follow' data-id='{!! $cause->cause_id !!}' data-type='cause'>
					<a href='#follow'>
						@if( $following == 1 )
			            unfollow
			            @else
			            follow
			            @endif
					</a>
				</div>
				<div class='share'>
					<a href='#share'>share</a>
				</div>
			</div>
		</div>

		<div class='cause-overview'>
			<div class='header'>Overview</div>
			<div class='content'>
				<?php $overviewExp = explode(" ", strip_tags( $cause->cause_content ) ); ?>
				
				@if( sizeof( $overviewExp ) > 75 )
					@for( $i = 0 ; $i < sizeof( $overviewExp ) ; $i++ )
						@if( $i < 75 || $i > 75 )
                             {!! $overviewExp[$i] !!} 
                        @else( $i == 75 )
                            <a href='' class='readmore'>...See More</a>
                            <span class='more'>{!! $overviewExp[$i] !!}
                        @endif
					@endfor
					<br />
					@if( ! empty( $cause->reference ) )
						{!! $cause->reference !!}
					@endif
					</span><a href='#' class='readless'>Show Less</a>
				@else
					<div>{!! $cause->cause_content !!}</div>
	                @if( ! empty( $cause->reference ) )
	                    <a href='' class='readmore'>Show References</a>
	                    <div class='more'>
	                    {!! $cause->reference !!}
	                    </div><a href='#' class='readless'>Hide References</a>
	                @endif
				@endif 
			</div>
		</div>
		@if( ! empty( $news ) )
		<div class='cause-news'>
			<ul data-role='listview'>
				<li><a href='#news'>{!! $cause->cause_name !!} News</a></li>
			</ul>
		</div>
		@endif

		@foreach( $hashtags as $hashtag )
			@if( isset( $twitter[$hashtag] ) )
			<div class='cause-tweets'>
				<div class='section-header' style='border-bottom: 1px solid #e5e5e5;'> 
					<span class='left' style='padding-left: 32px;'><i class='icon pwi-social-twitter'></i>Twitter <span class='hashtag'>{!! $hashtag !!}</span></span>
				</div>
				@foreach( $twitter[$hashtag] as $tweet )
		    		<div class='post-item'>
		        		{!! $tweet["tweet"] !!}
		        		<div class='posted'>Posted: {!! $tweet['date'] !!}</div>
		    		</div>
		    	@endforeach
		    	<div class='more-tweets'><a href='https://twitter.com/hashtag/{!! str_replace("#", "", $hashtag) !!}' target='_blank' rel='external'>See more tweets</a></div>
			</div>
			@endif
		@endforeach

		<div class='sub-causes'>
		@if( sizeof( $subcauses ) > 0 )
			<div class='section-header red'>{!! $cause->cause_name !!} Subcauses</div>
				<ul data-role="listview" data-inset='true' data-shadow='false' >
				@foreach( $subcauses as $subcause )
					<li data-role='collapsible' data-iconpos="right" data-collapsed-icon="carat-d">
						<h2>
							<i class='pwi-cause-{!! $cause["icon"] !!}-stroke pwi-icon-2em pull-left'></i> <span>{!! $subcause->cause_name !!}</span>
						</h2>
						<p class='cause-desc'>
						{!! $subcause->cause_content !!}
						</p>
					</li>
				@endforeach
				</ul>
			@endif
		</div>

		@if( sizeof( $orgs ) > 0 )
		<div class='cause-orgs'>
			
			@include("mobile.modules.organizations")
			
			<div class='search-more'><a href=''>See more organizations</a></div>
		</div>
		@endif

		@if( sizeof( $projects ) > 0 )
		<div class='cause-projects'>
			
			@include("mobile.modules.crowdfunding")
			
			<div class='search-more'><a href=''>See more crowdfunding projects</a></div>
		</div>
		@endif

		@if( sizeof( $products ) > 0 )
		<div class='cause-products'>

			@include("mobile.modules.products")

			<div class='search-more'><a href=''>See more products</a></div>
		</div>
		@endif
		
		<div class='backToTop' > <div>Back to top</div></div>

		<div style='width: 100%; height: 10px;'></div>
			
	</div>
	@include("mobile.partials._share")
	@include('mobile.footer')
</div>

@include("mobile.overlay.news")
@include("mobile.overlay.donate")

@stop
