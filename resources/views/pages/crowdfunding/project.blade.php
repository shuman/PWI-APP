@extends('header')

@section('content')

{!! Form::hidden('paypalUn', $paypal_un) !!}

@if( $hasGateway )
<div class='modal white' id='crowdFundingCheckout'>
    
    <div class='checkout overlay-content'>
        <div class='exit-crowdfunding-checkout exit pull-right'>
            <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
        </div>
        <div class='title'>{!! $project->project_title !!}</div>
        <div class='organization'>{!! $project->org_name !!}</div>
        @include("partials.crowdfunding._incentives")
        
    </div>
</div>
@endif

<input type='hidden' name='project_name' value='{!! $project->project_title !!}' />
<input type='hidden' name='org_name' value='{!! $project->org_name !!}' />

<div class='row'>
    <!-- Project Left Side -->
    <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-left-0 padding-right-5'>
        <!-- Project Header -->
        <div class='project-header'>
            <img class='coverphoto' src='{!! $imgPath !!}{!! $project->coverphoto !!}' />
            <div class='header-content'>
                <div class='project-name'>{!! $project->project_title !!}</div>
                <div class='org-name'>{!! $project->org_name !!} &nbsp; 
                    <span class='org-link'>
                        <a href='/organization/{!! $project->org_alias !!}'>Visit Organization's Profile</a>
                    </span>
                </div>
            </div>
        </div><!-- end .project-header -->
        
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
        
        <!-- Project Comments -->
        <div class='project-comments margin-top-10'>
            <div class='title'>Comments</div>
            @include("modules.postcomment")
            <hr />
            <div class='comments'>
                @foreach( $reviews as $review )
                    <div class='comment'>
                        <div class='name'>{!! $review->comment_username !!}</div>
                        <div class='review'>{!! $review->comment_text !!}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div><!-- end .col-xx-6 ( left side project ) -->
    
    <!-- Project Right Side -->
    <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-left-0 padding-right-5'>
        <div class='row'>
            <div class='col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-right-5'>
                <!-- Project Info -->
                <div class='project-info'>
                    <p>Overview</p>
                    
                     <div class='info-item'>
                        {!! round( $percentage, 2 ) !!}% Funded
                    </div>
                    
                    <div class='info-item'>
                        {!! $daysLeft !!} Days Left
                    </div>
                    <div class='info-item'>
                        {!! $project->funders !!} Funders
                    </div>
                    <div class='info-item'>
                        {!! ucfirst( $project->project_fund_type ) !!} Funding
                    </div>
                </div><!-- end .project-info -->
            
				@if( sizeof( $causes) > 0 )
                <!-- Project Causes -->
                <div class='project-causes margin-top-10'>
                    <p>Causes</p>
                    @foreach( $causes as $cause )
                        <div class='cause-name'>
                            <div class='cause-icon' data-link="{!! $cause['alias'] !!}">
                                <i class='pwi-cause-{!! $cause["icon"] !!}-stroke pwi-icon-2em pull-left'></i>
                                <div class='cause-name-text pull-left'>{!! $cause["name"] !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div><!-- end .project-causes -->
                @endif
                
                @if( sizeof( $impactCountries) > 0 )
                <!-- Project Areas of Impact -->
                <div class='project-areas-of-impact margin-top-10'>
                    <p>Areas of Impact</p>
                    <div class='area-list'>
                        @foreach( $impactCountries as $country )
                        <div class='row'>
                            <div class='col-lg-3 col-md-3 col-sm-3 col-xs-3'>
                                <div class='img-thumbnail flag-wrapper margin-bottom-5' style='height: 27px; width: 40px;  margin-left: 5px;'>
                                    <span class='flag-icon flag flag-background flag-icon-{!! strtolower( $country["country_iso_code"] ) !!}'></span>    
                                </div> 
                            </div>
                            <div class='col-lg-9 col-md-9 col-sm-9 col-xs-9 country-name padding-0'><a href='/country/{!! $country["country_alias"] !!}'>{!! $country["country_name"] !!}</a></div>
                            <input type='hidden' name='lat' value='{!! $country["latitude"] !!}' />
                            <input type='hidden' name='lng' value='{!! $country["longitude"] !!}' />
                            <input type='hidden' name='code' value='{!! $country["country_iso_code"] !!}' />
                            <input type='hidden' name='alias' value='{!! $country["country_alias"] !!}' />
                        </div>
                        @endforeach
                    </div>
                    <div id="map" class='margin-0'></div>
                </div><!-- end .project-areas-of-impact -->
                @endif
                
                @if( sizeof( $incentives ) > 0 )
                    
                    <!-- put photos and org twitter feed here -->
                
                @endif
                
            </div>
            <div class='col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-left-5'>
                <div class='project-funded-status'>
	                <div style='padding: 20px 15px;'>
	                    <div class='amount-raised'>
	                        <span class='raised'>{!! $raised !!}</span><span class='goal'>/{!! $goal !!}</span>
	                    </div>
	                    <div class='progress-wrapper'>
	                        <div class='progress'>
	                            <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: {!! $percentage !!}%;">
	                            </div>
	                        </div>
	                    </div>
                        @if( $hasGateway )
	                    <div class='fund-project-wrapper'>
	                        <button class='fund-project' data-alias='{!! $project->project_alias !!}'>fund project</button>
	                    </div>
                        @endif
	                </div>
                    <hr />
                    <div class='fund-actions'>
                        <div class='action pull-left'>
                            <button class='follow' data-id='{!! $project->project_id !!}' data-type='project'>
                            @if( $following == 1 )
                            unfollow
                            @else
                            follow
                            @endif
                            </button>
                        </div>
                        <div class='action pull-right'>
                            <button class='share pull-right'>share</button>
                        </div>
                    </div>
                    @if( sizeof( $incentives ) > 0 )
                    <div class='incentives'>
                        <hr />
                        <div class='incentives-title'>Incentives</div>
                        <div class='incentive-list'>
                        @foreach( $incentives as $incentive )
                            @if( $hasGateway )
                            <div class='incentive' data-incentive-id-front='{!! $incentive->project_incentive_id !!}' data-type='{!! $project->project_alias !!}'>
                            @else
                            <div class='incentive'>
                            @endif
                                @if( $incentive->project_available_incentive_count == $incentive->project_incentive_purchasedcount )
                                <div class='zero-left-overlay'></div>
                                @endif
                                <div class='title'>{!! $incentive->project_incentive_title !!}</div>    
                                <div class='info'>
                                    <span class='price pull-left'>{!! money_format('%(#10n', $incentive->project_incentive_amount ) !!}</span>
                                    @if( $incentive->project_available_incentive_count == $incentive->project_incentive_purchasedcount )
                                    <span class='left no-more pull-right'>0 Left</span>
                                    @else
                                    <span class='left pull-right'>{!! ( $incentive->project_available_incentive_count - $incentive->project_incentive_purchasedcount ) !!} Left</span>
                                    @endif
                                </div>
                                <div class='description'>{!! $incentive->project_incentive_description !!}</div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                        
                    @else
                        <!-- put photos and twitter feed here -->
                    
                    @endif
                </div>    
            
            </div>
        </div>
    </div>
</div>
@stop