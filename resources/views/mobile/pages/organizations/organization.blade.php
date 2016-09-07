@extends('header')
@section('content')
{!! HTML::script('js/donate-mobile.js') !!}
<div data-role='page' id='org-page'>
	@include('mobile.headers.generic')

	<div data-role='main' class='ui-content' style='padding:0;' id="organization-page">
		<input type='hidden' name='pagetype' value='organization' />
		<input type='hidden' name='entityName' value='{!! $org->org_name !!}' />
		<input type='hidden' name='id' value='{!! $org->org_id !!}' />
		<input type='hidden' name='type' value='organization' />
		<input type='hidden' name='paypalUn' value='{!! $paypal_un !!}' />
		<input type='hidden' name='payment_gateway' value='{!! $org->gateway !!}' />
		<div class='org-top'>
			<div class='coverphoto' style='background: url( {!! $coverphoto !!} ) no-repeat center center; background-size: cover;'></div>
			<div class='logo-container'>
				<img src='{!! $logo !!}' />
				<div class='org-name'>{!! stripslashes( $org->org_name ) !!}</div>
			</div>
			<div class='actions'>
				@if( $hasGateway )
				<div class='donate'>
					<a href='#donate-page-one'>donate</a>
				</div>
				<div class='follow' data-id='{!! $org->org_id !!}' data-type='org'>
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
				@else
				<div class='follow top' data-id='{!! $org->org_id !!}' data-type='org' style='width: 50%;'>
					<a href='#follow'>
					@if( $following == 1 )
					unfollow
					@else
					follow
					@endif
					</a>
				</div>
				<div class='share top' style='width: 50%;'>
					<a href='#share'>share</a>
				</div>	
				@endif
			</div>
		</div>
		
		<div class='about'>
			<div class='title'>Mission Statement</div>
			<div class='content'>{!! $mission !!}</div>
			<div class='title'>About Us</div>
			<div class='content'>{!! $aboutUs !!}</div>
		</div>

		<div class='generalInfo'>
			<ul data-role="listview" style='margin: 0;'>
				<li><a href='#contactInfo'>General Information</a></li>
			</ul>
		</div>

		<div class='impacts-causes'>
			@if( sizeof( $causes ) > 0 )
			<div class='title'>Causes</div>
				<ul data-role="listview" data-inset='true' data-shadow='false'>
				@foreach( $causes as $cause )
					<li data-role='collapsible' data-inset='false'>
						<h2>
							<i class='pwi-cause-{!! $cause["icon"] !!}-stroke pwi-icon-2em pull-left'></i> <span>{!! $cause["name"] !!}</span>
						</h2>
						<p class='cause-desc'>
						{!! $cause["desc"] !!}
						</p>
					</li>
				@endforeach
				</ul>
			@endif
			@if( sizeof( $countries ) )
			<div class='title'>Areas of Impact</div>
				<ul data-role="listview" data-inset="true" data-shadow="false">
				@foreach( $countries as $country )
					<li>
						<a href='/country/{!! $country["country_alias"] !!}' rel="external">
							<span class='flag-icon flag flag-background flag-icon-{!! strtolower( $country["country_iso_code"] ) !!}'></span>
							<span class='country-name'>{!! $country["country_name"] !!}</span>
						</a>
					</li>
				@endforeach
				</ul>
			@endif
		</div>

		@if( sizeof( $projects ) > 0 )
		<div class='org-projects'>
			
			@include("mobile.modules.crowdfunding")
			
			<!--<div class='search-more'><a href=''>See more crowdfunding projects</a></div>-->
		</div>
		@endif

		@if( sizeof( $products ) > 0 )
		<div class='org-products'>

			@include("mobile.modules.products")

			<!--<div class='search-more'><a href=''>See more products</a></div>-->
		</div>
		@endif

		<div class='org-photos'>
			<div class='section-header'> 
				<span class='left' >Photos</span>
				<span class='right'><a>See All</a></span>
			</div>
			<div class='photo-list-container' style='overflow-x:auto; overflow-y: hidden;'> 
				<ul class='photo-list' style='width: {!! sizeof( $photos ) * 99 !!}px;'>
					@for($i = 0 ; $i <  sizeof( $photos ) ; $i++ )
						@if( isset( $photos[$i] ) )
		                	<li><a class='org-photos' href='/images/organization/{!! $photos[$i]->file_path !!}'><img class='img-responsive' src='/images/organization/{!! $photos[$i]->file_path !!}' /></a></li>
		                @endif
		            @endfor
				</ul>
			</div>
		</div>

		<div class='org-videos'>
			<div class='section-header'> 
				<span class='left' >Videos</span>
				<span class='right'><a>See All</a></span>
			</div>
			<div class='video-list-container' style='overflow-x:auto; overflow-y: hidden;'>
				<ul class='video-list' style='width: {!! sizeof( $videos ) * 176 !!}px;'>
					@for( $i = 0 ; $i < sizeof( $videos) ; $i++ )
						@if( isset( $videos[$i] ) )
							<li><img src='{!! $videos[$i]->video_id !!}' /></a></li>
						@endif
					@endfor
				</ul>
			</div>
		</div>

		<div class='org-reviews'>
			<div class='section-header' style='border-bottom: 1px solid #e5e5e5;'> 
				<span class='left' >Reviews</span>
				<span class='right'><a href='#allReviews'>See All</a></span>
			</div>
			<div class='review-container'>
			
			@for( $i = 0 ; $i < sizeof( $reviews ) ; $i++ )
				@if( $i < 3 )
				<div class='review'>
					<div class='top'>
						<span class='username'>{!! $reviews[$i]->comment_username !!}</span>
						<span class='rating'>
						@for($r = 1; $r < 6; $r++ )
	                        @if( $r <= $reviews[$i]->comment_rating )
	                            <span class="star fill" >
	                                <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
	                            </span>          
	                        @else
	                            <span class="star" >
	                                <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
	                            </span>
	                        @endif
	                    @endfor
						</span>
					</div>
					<div class='desc'>
						<?php $tmp = explode(" ", $reviews[$i]->comment_text ); ?>

						@if( sizeof( $tmp ) > 100 )
							@for( $j = 0 ; $j < sizeof( $tmp ) ; $j++ )
	                            @if( $j < 100 || $j > 100 )
	                                {!! $tmp[$j] !!}
	                            @else( $j == 100 )
	                                <a href='' class='readmore'>...See More</a>
	                                <span class='more'>{!! $tmp[$j] !!}
	                            @endif
	                        @endfor
	                        </span><a href='#' class='readless'>Show Less</a>
						@else
							{!! $reviews[$i]->comment_text !!}
						@endif
					</div>
				</div>
				@endif
			@endfor
			</div>
			<div class='search-more' style='border-top: none; '><a href='#review' style='font-weight: 300;'>Write a review</a></div>
		</div>

		@if( isset( $feeds["twitter"] ) )
		<div class='org-tweets'>
			<div class='section-header' style='border-bottom: 1px solid #e5e5e5;'> 
				<span class='left' style='padding-left: 32px;'><i class='icon pwi-social-twitter'></i>Twitter Feed</span>
				<span class='right'><a>Go to Twitter</a></span>
			</div>
			@foreach( $feeds["twitter"]["tweets"] as $tweet )
	    		<div class='post-item'>
	        		{!! $tweet["tweet"] !!}
	        		<div class='posted'>Posted: {!! $tweet['date'] !!}</div>
	    		</div>
	    	@endforeach
		</div>
		@endif

		<div class='backToTop' > <div>Back to top</div></div>

		<div style='width: 100%; height: 10px;'></div>
	</div>
	@include('mobile.footer')
</div>

<!-- Start Company General Information -->
<div data-role='page' id='contactInfo'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>{!! stripslashes( $org->org_name ) !!}</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<div class='blueButton left-align'>
			<div>General Information</div>
		</div>
		<div class='information-header'>
			<div>Contact</div>
		</div>
		@if( ! empty( $org->org_weburl ) )
        	<div class='information-item'>
        		<div class='item-col' style='width:15%; text-align: center;'>
        			<i data-icon="'" class="icon pwi-icon-2em"></i>
        		</div>
        		<div class='item-col' style='width: 25%; text-align: left;'>
        			<span>Website</span>
        		</div>
        		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px;'>
        			<a href='tel:{!! $org->org_mobile_number !!}' class='blue'>
        			@if( strlen( $org->org_weburl ) > 20 )
        				{!! substr( $org->org_weburl, 0, 20 ) !!}...
        			@else
        				{!! $org->org_weburl !!}
        			@endif
        			</a>
        		</div>
        	</div>
        @endif

        @if( ! empty( $org->org_mobile_number ) )
        <div class='information-item'>
    		<div class='item-col' style='width:15%; text-align: center;'>
    			<i data-icon='"' class="icon pwi-icon-2em"></i>
    		</div>
    		<div class='item-col' style='width: 25%; text-align: left;'>
    			<span>Phone</span>
    		</div>
    		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px;'>
    			<a href='tel:{!! $org->org_mobile_number !!}' class='grey'>{!! $org->org_mobile_number !!}</a>
    		</div>
    	</div>
        @endif

        @if( ! empty( $org->org_email ) )
        <div class='information-item'>
    		<div class='item-col' style='width:15%; text-align: center;'>
    			<i data-icon='3' class="icon pwi-icon-2em"></i>
    		</div>
    		<div class='item-col' style='width: 25%; text-align: left;'>
    			<span>Email</span>
    		</div>
    		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px;'>
    			<a href='tel:{!! $org->org_mobile_number !!}' class='grey'>
    			@if( strlen( $org->org_email ) > 20 )
    				{!! substr( $org->org_email, 0, 20 ) !!}...
    			@else
    				{!! $org->org_email !!}
    			@endif
    			</a>
    		</div>
    	</div>
        @endif

        @if( ! empty( $org->org_addressline1 ) )
        <div class='information-item'>
    		<div class='item-col' style='width:15%; text-align: center;'>
    			<i data-icon='Y' class="icon pwi-icon-2em"></i>
    		</div>
    		<div class='item-col' style='width: 25%; text-align: left;'>
    			<span>Address</span>
    		</div>
    		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px; font-size: 12px;' class='grey' >
    			{!! $org->org_addressline1 !!}<br />
	            {!! $org->org_addressline2 !!}<br />
	            {!! $org->org_city !!}, {!! $org->state_code !!} {!! $org->org_zip !!}
    		</div>
    	</div>
        @endif
        <div class='information-header'>
			<div>Social</div>
		</div>
		@foreach( $socialmedia as $item)
            @if( strtolower( $item->social_media_name ) == "facebook" )
            	<div class='information-item'>
		    		<div class='item-col' style='width:15%; text-align: center;'>
		    			<i class='icon pwi-social-facebook pwi-icon-2em'></i>
		    		</div>
		    		<div class='item-col' style='width: 25%; text-align: left;'>
		    			<span>Facebook</span>
		    		</div>
		    		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px; font-size: 12px;'  >
		    			{!! Helper::cleanSocialLink( $item->org_sm_url, "facebook") !!}
		    		</div>
		    	</div>
            @endif
            @if( strtolower( $item->social_media_name ) == "twitter" )
            	<div class='information-item'>
		    		<div class='item-col' style='width:15%; text-align: center;'>
		    			<i class='icon pwi-social-twitter pwi-icon-2em'></i>
		    		</div>
		    		<div class='item-col' style='width: 25%; text-align: left;'>
		    			<span>Twitter</span>
		    		</div>
		    		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px; font-size: 12px;'  >
		    			{!! Helper::cleanSocialLink( $item->org_sm_url, "twitter") !!}
		    		</div>
		    	</div>
            @endif
            @if( strtolower( $item->social_media_name ) == "instagram" )
            	<div class='information-item'>
		    		<div class='item-col' style='width:15%; text-align: center;'>
		    			<i class='icon pwi-social-instagram pwi-icon-2em'></i>
		    		</div>
		    		<div class='item-col' style='width: 25%; text-align: left;'>
		    			<span>Instagram</span>
		    		</div>
		    		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px; font-size: 12px;'  >
		    			{!! Helper::cleanSocialLink( $item->org_sm_url, "instagram") !!}
		    		</div>
		    	</div>
            @endif
            @if( strtolower( $item->social_media_name ) == "pinterest" )
            	<div class='information-item'>
		    		<div class='item-col' style='width:15%; text-align: center;'>
		    			<i class='icon pwi-social-pinterest pwi-icon-2em'></i>
		    		</div>
		    		<div class='item-col' style='width: 25%; text-align: left;'>
		    			<span>Pinterest</span>
		    		</div>
		    		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px; font-size: 12px;'  >
		    			{!! Helper::cleanSocialLink( $item->org_sm_url, "pintrest") !!}
		    		</div>
		    	</div>
            @endif
        @endforeach
        <div class='information-header'>
			<div>Other Information</div>
		</div>
		<div class='information-item'>
    		<div class='item-col' style='width:15%; text-align: center;'>
    			<i class='icon pwi-icon-dashboard-payment pwi-icon-2em grey'></i>
    		</div>
    		<div class='item-col' style='width: 25%; text-align: left;'>
    			<span>Revenue</span>
    		</div>
    		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px; font-size: 12px;' class='grey' >
    			{!! money_format('%(#10n', $org->org_revenue ) !!}
    		</div>
    	</div>
    	<div class='information-item'>
    		<div class='item-col' style='width:15%; text-align: center;' >
    			<i class='icon pwi-icon-dashboard-venderlist pwi-icon-2em grey'></i>
    		</div>
    		<div class='item-col' style='width: 25%; text-align: left;'>
    			<span>Rating</span>
    		</div>
    		<div class='item-col' style='width: 60%; text-align: right; padding-right: 20px; font-size: 12px;'  >
    			<div class='rating'>
	                @for($i = 1; $i < 6; $i++ )
                        @if( $i <= $rating )
                            <span class="star fill" >
                                <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
                            </span>          
                        @else
                            <span class="star" >
                                <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
                            </span>
                        @endif
                    @endfor
                </div>
                <div class='review-count' class'grey'>
                 {{ sizeof( $reviews ) }} Reviews
                </div>
    		</div>
    	</div>
	</div>
</div>

@if( $hasGateway )
	@include("mobile.overlay.donate")
@endif
@include("mobile.overlay.review")
@include("mobile.overlay.allReviews")

@stop