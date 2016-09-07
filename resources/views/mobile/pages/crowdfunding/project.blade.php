@extends('header')
@section('content')
{!! HTML::script('js/fund-mobile.js') !!}
<div data-role='page' id='crowdfunding-page'>
	@include('mobile.headers.generic')

	<div data-role='main' class='ui-content' style='padding:0;' id="project-page">
		<input type='hidden' name='pagetype' value='crowdfunding' />
		<input type='hidden' name='entityName' value='{!! $project->project_title !!}' />
		<input type='hidden' name='id' value='{!! $project->project_id !!}' />
		<input type='hidden' name='paypalUn' value='{!! $paypal_un !!}' />
		<input type='hidden' name='payment_gateway' value='{!! $project->payment_gateway !!}' />

		<div class='crowdfunding-top'>
			<div class='coverphoto' style='background: url( {!! $imgPath !!}{!! $project->coverphoto !!} ) no-repeat center center; background-size: cover;'></div>
			
			<div class='crowdfunding-title-data'>
				<div class='project-title'>
					{!! $project->project_title !!}
				</div>
				<div class='org-name'>
					{!! $project->org_name !!}
				</div>
			</div>
			
			<div class='actions'>
				<div class='follow top' data-id='{!! $project->project_id !!}' data-type='project'>
					<a href='#follow'>
					@if( $following == 1 )
					unfollow
					@else
					follow
					@endif</a>
				</div>
				<div class='share top'>
					<a href='#share'>share</a>
				</div>
				@if( $hasGateway )
				<div class='donate bottom'>
					<a href='#fund-page-one'>donate</a>
				</div>
				@endif
			</div>
			<div class='crowdfunding-status'>
				<div class='stats'>
					<div class='stat' style='text-align: left;'>
						<span class='number'>{!! $raised !!}</span> given
					</div>
					<div class='stat' style='text-align: center;'>
						<span class='number'>{!! $percentage !!}%</span> funded
					</div>
					<div class='stat' style='text-align: right;'>
						<span class='number'>{!! $daysLeft !!} days</span> left
					</div>
				</div>
				<div class='progress'>
					<div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: {!! $percentage !!}%;">
                    </div>
				</div>
			</div>
		</div>

		<!-- Project Description -->
        <div class='project-description margin-top-10'>
            <div class='title'>Project</div>
            <div class='content'>
                {!! $project->project_story !!}
            </div>
            @if( ! empty( $project->project_video_url ) )
                <div class='video'>
	                @if( preg_match("/^https/", $project->project_video_url) )
                    <iframe src='{!! $project->project_video_url !!}' frameborder='0' ></iframe>
                    @else
                    <iframe src='{!! str_replace("http", "https", $project->project_video_url) !!}' frameborder='0' ></iframe>
                    @endif
                </div>
            @endif
        </div><!-- end .project-description -->

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
			@if( sizeof( $impactCountries ) )
			<div class='title'>Areas of Impact</div>
				<ul data-role="listview" data-inset="true" data-shadow="false" style='margin: 0px;'>
				@foreach( $impactCountries as $country )
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

		<div class='org-reviews'>
			<div class='section-header' style='border-bottom: 1px solid #e5e5e5;'> 
				<span class='left' >Comments</span>
				<span class='right'><a>See All</a></span>
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
			<div class='search-more' style='border-top: none; text-align: center; '><a href='#review' styel='font-weight: 300;'>Write a Comment</a></div>
		</div>
			
		<div class='backToTop' > <div>Back to top</div></div>

		<div style='width: 100%; height: 10px;'></div>

		@include("mobile.partials._share")
	</div>

	@include('mobile.footer')
</div>
@if( $hasGateway )
	@include("mobile.overlay.fund")
@endif

<script>
	$(document).on("pagecreate", "#crowdfunding-page", function( ){

		var aspectRatio = 315/560;

		var windowWidth = $(window).width( );

		$(".video").find("iframe").attr({
			width: windowWidth + "px",
			height: parseInt( windowWidth * aspectRatio ) + "px"
		});
	});
</script>
@stop