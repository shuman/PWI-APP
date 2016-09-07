@extends('header')
@section('content')
{!! HTML::script('js/donate-mobile.js') !!}
<div data-role='page' id='country-page'>
	@include('mobile.headers.generic')

	<div data-role='main' class='ui-content' style='padding:0;' id="cause-page">
		<input type='hidden' name='pagetype' value='country' />
		<input type='hidden' name='entityName' value='{!! $country->country_name !!}' />
		<input type='hidden' name='id' value='{!! $country->country_id !!}' />
		<input type='hidden' name='paypalUn' value='{!! $paypal_un !!}' />
		<input type='hidden' name='payment_gateway' value='2' />

		<input type='hidden' name='latitude' value='{!! $country->latitude !!}' />
		<input type='hidden' name='longitude' value='{!! $country->longitude !!}' />
		
		<div class='country-top'>
			<div class='coverphoto' style='background: url( {!! $coverphoto !!} ) no-repeat center center; background-size: cover;'></div>
			
			<div class='country-name'>
				<div class='img-thumbnail flag-wrapper' style='height: 45px; width: 68px; margin-top: 8px; margin-left: 5px;'>
                    <span class='flag-icon flag flag-background flag-icon-{!! strtolower( $country->country_iso_code ) !!}'></span>    
                </div> 
                <p>{!! stripslashes( $country->country_name ) !!}</p>
			</div>
			<div class='actions'>
				<div class='donate'>
					<a href='#donate-page-one'>donate</a>
				</div>
				<div class='follow' data-type="country" data-id='{!! $country->country_id !!}'>
					<a href='#follow'>
					@if( $following )
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
		<div class='country-news'>
			<div class='header'>{!! $country->country_name !!} News</div>
			<div class='news-content'>
				@for( $i = 0 ; $i < 3 ; $i++ )
					@include("mobile.partials._news")
				@endfor
			</div>
			<div class='search-more' style='margin-bottom: 0px;'><a href='#news'>See more news</a></div>
		</div>

		@if( sizeof( $twitter ) > 0 )
		<div class='country-tweets'>
			<div class='section-header' style='border-bottom: 1px solid #e5e5e5;'> 
				<span class='left' style='padding-left: 32px;'><i class='icon pwi-social-twitter'></i>Twitter <span class='hashtag'>{!! $country->hashtags !!}</span></span>
			</div>
			@foreach( $twitter as $tweet )
	    		<div class='post-item'>
	        		{!! $tweet["tweet"] !!}
	        		<div class='posted'>Posted: {!! $tweet['date'] !!}</div>
	    		</div>
	    	@endforeach
	    	<div class='more-tweets'><a href='https://twitter.com/hashtag/{!! str_replace("#", "", $country->hashtags) !!}' target='_blank' rel='external'>See more tweets</a></div>
		</div>
		@endif

		<div class='country-causes'>
			@if( sizeof( $causes ) > 0 )
			<div class='section-header red'>Causes We Support</div>
				<ul data-role="listview" data-inset='true' data-shadow='false' >
				@foreach( $causes as $cnt_cause )
					<li data-role='collapsible' data-iconpos="right" data-collapsed-icon="carat-d">
						<h2>
							<i class='pwi-cause-{!! $cnt_cause["icon"] !!}-stroke pwi-icon-2em pull-left'></i> <span>{!! $cnt_cause["name"] !!}</span>
						</h2>
						<p class='cause-desc'>
						{!! $cnt_cause["desc"] !!}
						</p>
					</li>
				@endforeach
				</ul>
			@endif
		</div>

		<div class='country-map'>
			<div class='header'>Map</div>
			<div id="map" class='margin-0'></div>
		</div>

		@if( isset( $geography ) && sizeof( $geography ) > 0 )
		<div class='country-geography country-stat-item'>
			<ul data-role='listview'>
				<li><a href='#geography'>Geography</a></li>
			</ul>
		</div>
		@endif

		@if( sizeof( $demographics ) > 0 )
		<div class='country-demographics country-stat-item'>
			<ul data-role='listview'>
				<li><a href='#demographics'>Demographics</a></li>
			</ul>
		</div>
		@endif

		@if( isset( $finances ) )
		<div class='country-finance country-stat-item'>
			<ul data-role='listview'>
				<li><a href='#finance'>Finance</a></li>
			</ul>
		</div>
		@endif
		<!--
		<div class='country-politics'>
			<ul data-role='listview'>
				<li><a href='#politics'>Politics</a></li>
			</ul>
		</div>
	-->
		@if( sizeof( $orgs ) > 0 )
		<div class='country-orgs'>
			
			@include("mobile.modules.organizations")
			
			<div class='search-more'><a href='/country/{!! $country->country_name !!}/organizations'>See more organizations</a></div>
		</div>
		@endif

		@if( sizeof( $projects ) > 0 )
		<div class='country-projects'>
			
			@include("mobile.modules.crowdfunding")
			
			<div class='search-more'><a href=''>See more crowdfunding projects</a></div>
		</div>
		@endif

		@if( sizeof( $products ) > 0 )
		<div class='country-products'>

			@include("mobile.modules.products")

			<div class='search-more'><a href=''>See more products</a></div>
		</div>
		@endif
		
		<div class='backToTop' > 
			<a href='#' >Back to top</a>
		</div>

		<div style='width: 100%; height: 10px;'></div>
	</div>
	@include("mobile.partials._share")
	@include('mobile.footer')
</div>

@include("mobile.overlay.donate")
@include("mobile.overlay.news")
@if( isset( $geography ) && sizeof( $geography ) > 0 )
	@include("mobile.overlay.geography")
@endif
@include("mobile.overlay.demographics")
@include("mobile.overlay.finance")


{!! HTML::script('js/map-style.js') !!}
{!! HTML::script('https://maps.googleapis.com/maps/api/js') !!}
{!! HTML::script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js') !!}
<script>
	
/* Start Country Map Script */

    if( $("input[name=latitude]").length > 0 ){

    	var bounds = new google.maps.LatLngBounds( );
        var map;
        
        var initialZoom = 0;
        var zoomFactor = 3;
        
        var lat = $("input[name=latitude]").val( );
        var lng = $("input[name=longitude]").val( );
        
        var mapCanvas = document.getElementById('map');

        var mapOptions = {
            zoom: 3,
            center: new google.maps.LatLng( lat, lng),
            myTypeid: google.maps.MapTypeId.ROADMAP,
            styles: styles,
            animatedZoom: true,
            navigationControl: false,
    		mapTypeControl: false,
    		scaleControl: false,
    		draggable: false,
            disableDefaultUI: true
        };
        
        map = new google.maps.Map(mapCanvas, mapOptions);

        var myLatLng = {lat: parseFloat( lat ), lng: parseFloat( lng )};

        var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map,
                        animation: google.maps.Animation.DROP,
                    });
    }
    
    /* End Country Map Script */

    
</script>

@stop

