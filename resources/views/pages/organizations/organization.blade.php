@extends("header")

@section("content")

{!! Form::hidden('paypalUn', $paypal_un) !!}
{!! Form::hidden('orgId', $org->org_id) !!}
@if( isset( $user ) )
    {!! Form::hidden('userId', $user->user_id) !!}
@endif

@if( $isAdmin )
    {!! HTML::style( 'css/jquery-te.css') !!}
    {!! HTML::script('js/jquery-te-1.4.0.min.js') !!}
    {!! HTML::script('js/org-admin.js') !!}
    
@endif

<div class='modal white' id='organizationCheckout' style='background-color: rgba(255, 255, 255, 1);'>
    <div class='checkout overlay-content'>
        <div class='exit-organization-checkout exit pull-right'>
            <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
        </div>
        <div class='title'>{!! stripslashes( $org->org_name ) !!}</div>
        @include("partials._donation")
    </div>
</div>
<div class='alert alert-info upload-info upload-dialog'>
    Your file is currently being uploaded...
</div>
<div class='alert alert-success upload-success upload-dialog' >
    <strong>Success!</strong> Your file has been uploaded!
</div>
<div class='alert alert-danger upload-error upload-dialog' >
    <strong>Error!</strong> There was a problem uploading your file!
</div>
<div class='row'>
    @if( Agent::isMobile( ) )
    <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>
    @else
    <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-left-0 padding-right-5'>
    @endif
        <div class='org-header'>
            @if( $hasCoverPhoto )
            <div class='coverphoto' style='background: url( {!! $coverphoto !!} ) no-repeat center center; background-size: cover;' >
               @if( $isAdmin )
                <div class='edit-cog edit-general-info' style='color: #fff;' >
                    <div class="fileUpload btn btn-blue btn-sm-sw ">
                        <form enctype="multipart/form-data">
                            {!! Form::token( ) !!}
                            <span class='glyphicon glyphicon-camera upload-icon-cover'></span>
                            <input type='file' name="logo" class="upload" id="imgCoverPic" data-parent="coverphoto" />
                        </form>
                    </div>
                </div>
               @endif 
            </div>
            @else
            <div class='coverphotoPH'>
                @if( $isAdmin )
                <div class='edit-cog edit-general-info' style='color: #fff;' >
                    <div class="fileUpload btn btn-blue btn-sm-sw ">
                        <form enctype="multipart/form-data">
                            {!! Form::token( ) !!}
                            <span class='glyphicon glyphicon-camera upload-icon-cover'></span>
                            <input type='file' name="logo" class="upload" id="imgCoverPic" data-parent="coverphotoPH" />
                        </form>
                    </div>
                </div>
               @endif 
            </div>	
            @endif
            <div >
                <img class='logo' src='{!! $logo !!}' />
                @if( $isAdmin )
                <div class="fileUpload btn btn-blue btn-sm-sw" style='position: absolute; left: 70px; bottom: 15px;'>
                    <form enctype="multipart/form-data">
                        {!! Form::token( ) !!}
                        <span class='glyphicon glyphicon-camera upload-icon-profile'></span>
                        <input type='file' name="logo" class="upload" id="imgProfilePic" />
                    </form>
                </div>
                @endif
            </div>
            <div class='header-content'>
                <div class='org-name'>{!! stripslashes( $org->org_name ) !!}</div>
                <div class='rating below-image'>
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
                <div class='org-social-media'>
                    <?php
                    $hasSocialMedia = 0;
                    ?>
                    @foreach( $socialmedia as $item)
                    @if( strtolower( $item->social_media_name ) == "facebook" )
                    <div class='social-icon-facebook'>
                        <a href='{!! Helper::getSocialUrl( $item->org_sm_url, "facebook") !!}' target='_blank'>
                            <i class='icon pwi-social-facebook'></i>
                        </a>
                    </div>
                    <?php
                    $hasSocialMedia++;
                    ?>
                    @endif

                    @if( strtolower( $item->social_media_name) == "twitter" )
                    <div class='social-icon-twitter'>
                        <a href='{!! Helper::getSocialUrl( $item->org_sm_url, "twitter") !!}' target='_blank'>
                            <i class='icon pwi-social-twitter'></i>
                        </a>
                    </div>
                    <?php
                    $hasSocialMedia++;
                    ?>
                    @endif

                    @if( strtolower( $item->social_media_name) == "instagram" )
                    <div class='social-icon-instagram'>
                        <a href='{!! Helper::getSocialUrl( $item->org_sm_url, "instagram") !!}' target='_blank'>
                            <i class='icon pwi-social-instagram'></i>
                        </a>
                    </div>
                    <?php
                    $hasSocialMedia++;
                    ?>
                    @endif

                    @if( strtolower( $item->social_media_name) == "pinterest" )
                    <div class='social-icon-pinterest'>
                        <a href='{!! Helper::getSocialUrl( $item->org_sm_url, "pinterest") !!}' target='_blank'>
                            <i class='icon pwi-social-pinterest'></i>
                        </a>
                    </div>
                    <?php
                    $hasSocialMedia++;
                    ?>
                    @endif

                   @endforeach
                    @if( $isAdmin )
                        @if( $hasSocialMedia == 0 )
                        <div class='edit-cog margin-right-5' style='color: #e5e5e5; position: relative; float:left; top: -5px; right: 0px;' data-toggle='modal' data-target='#socialMediaModal'>
                        @else
                        <div class='edit-cog margin-right-5' style='color: #e5e5e5; position: relative; float:left; top: 3px; right: 0px;' data-toggle='modal' data-target='#socialMediaModal'>
                        @endif
                            <i class='fa fa-cog cog'></i>
                        </div>
                        @include("partials.modals._socialMediaModal")
                    @endif
                </div>
            </div>
        </div>
            <div class='top-button hidden-lg hidden-md visible-sm visible-xs'>
                <div class='row'>
                    <div class='col-sm-4 col-xs-4 padding-right-0'>
                        <div class='donate-button' data-alias="{!! $org->org_alias !!}">
                            donate 
                            @if( ! Agent::isMobile( ) ) 
                            now 
                            @endif
                        </div>
                    </div>

                    <div class='col-sm-4 col-xs-4 padding-left-0 padding-right-0'>

                        <button class='follow' data-id='{!! $org->org_id !!}' data-type='org' style='width: 100%; max-width: 100%; font-size: 18px;'>
                            @if( $following == 1 )
                            unfollow
                            @else
                            follow
                            @endif
                        </button>
                    </div>
                    <div class='col-sm-4 col-xs-4 padding-left-0 '>
                        <button class='share pull-right' style='width: 100%; max-width: 100%; font-size: 18px;'>share</button>
                    </div>
                </div>
            </div> 
            @if( ! empty( $aboutUs) || ! empty( $mission) )
            <div class='about-us margin-bottom-10 margin-top-10' >
                @if( $isAdmin )
                    <div class='edit-cog' style='color: #e5e5e5;' data-toggle='modal' data-target='#generalInfoModal'>
                        <i class='fa fa-cog cog'></i>
                    </div>
                    @include("partials.modals._generalInfo")
                @endif 

                @if( ! empty( $mission ) )
                <div class='title'>Mission Statement</div>
                <div class='content mission-content margin-top-5'>{!! nl2br( $mission ) !!}</div>
                <hr />
                @endif

                @if( ! empty( $aboutUs) )
                <div class='title'>About Us</div>
                <div class='content aboutUs-content margin-top-5'>{!! nl2br( $aboutUs ) !!}</div>
                @endif
            </div>
            @else
                @if( $isAdmin )
                <div class='btn btn-primary margin-top-10' style='width: 100%;' data-toggle='modal' data-target='#generalInfoModal'>
                Add About Us/Mission Statement Information
                </div>  
                
                @include("partials.modals._generalInfo")
                @endif
            @endif

            @if( sizeof( $causes ) > 0 )
            <div class='org-causes margin-bottom-10'>
                @if( $isAdmin )
                    <div class='edit-cog' style='color: #e5e5e5; z-index: 10;' data-toggle='modal' data-target='#orgCauseModal'>
                        <i class='fa fa-cog cog'></i>
                    </div>
                    @include("partials.modals.causes")
                @endif 
                <div class='row'>
                    <div class='col-lg-5 col-md-5 col-sm-5 col-xs-5 cause-list padding-right-0' style='padding: 20px 15px;'>
                        <p>Causes</p>
                        @for( $i = 0 ; $i < sizeof( $causes ) ; $i++ )
                        @if( $i == 0 )
                        <div class='cause-name active' data-cause='{!! $causes[$i]["id"] !!}'>
                            @else
                            <div class='cause-name' data-cause='{!! $causes[$i]["id"] !!}'>
                                @endif
                                <div class='cause-icon'>
                                    <i class='pwi-cause-{!! $causes[$i]["icon"] !!}-stroke pwi-icon-2em pull-left'></i>    
                                    <div class='cause-name-text pull-left'>{!! $causes[$i]["name"] !!}</div>
                                </div>    
                            </div>
                            @endfor
                        </div>
                        <div class='col-lg-7 col-md-7 col-sm-7 col-xs-7' >
                            @for( $j = 0 ; $j < sizeof( $causes) ; $j++ )
                            @if( $j == 0 )
                            <div class='cause-description {!! $causes[$j]["id"] !!}-description' style='padding: 20px 15px;'>                 
                                @else
                                <div class='cause-description {!! $causes[$j]["id"] !!}-description hidden' style='padding: 20px 15px;'>                       
                                    @endif
                                    <div class='cause-description-header'>{!! $causes[$j]["name"] !!} - <a href='/cause/{!! $causes[$j]["alias"] !!}'>Learn more</a></div>
                                    @if( sizeof( $causes[$j]["descExp"] ) < 250 )
                                    {!! $causes[$j]["desc"] !!}
                                    @else
                                    @for( $i = 0 ; $i < sizeof( $causes[$j]["descExp"] ) ; $i++ )
                                    @if( $i < 250 || $i > 250 )
                                    {!! $causes[$j]["descExp"][$i] !!}
                                    @else( $i == 250 )
                                    <a href='' class='readmore'>...See More</a>
                                    <span class='more'>{!! $causes[$j]["descExp"][$i] !!}
                                        @endif
                                        @endfor
                                    </span><a href='#' class='readless'>Show Less</a>
                                    @endif

                                </div>
                                @endfor                          
                            </div>
                        </div>
                    </div><!-- end of .org-causes -->
                    @else
                        @if( $isAdmin )
                            <div class='btn btn-primary margin-bottom-10' style='width: 100%;' data-toggle='modal' data-target='#orgCauseModal'>
                                Add Causes
                            </div> 
                            @include("partials.modals.causes")
                        @endif 
                    @endif

                    <!-- include project module -->
                    @include("modules.project")
                    <!-- end project module -->

                    <!-- include product module -->
                    @include("modules.product")
                    <!-- end product module -->

                </div><!-- end of left side - orgs -->
                @if( Agent::isMobile( ) )
                <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>
                    @else
                    <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-left-0 padding-right-5'>
                        @endif
                        <div class='col-lg-12 col-md-12 padding-0'>
                            <!-- start left side -->
                            <div class='col-lg-6 col-md-6 col-md-6 col-xs-12 padding-0'>
                                @if( ! $countries->isEmpty( ) )
                                <!-- Start Areas of Impact -->
                                <div class='org-areas-of-impact'>
                                    @if( isset( $isAdmin ) )
                                        @if( $isAdmin )
                                            <!--<div class='edit-cog' style='color: #e5e5e5;'>
                                                <i class='fa fa-cog cog'></i>
                                            </div>-->
                                        @endif
                                    @endif
                                    <p>Areas of Impact</p>
                                    <div class='area-list'>
                                        @for( $i = 0 ; $i < sizeof( $countries ) ; $i++ )
                                        <div class='row'>
                                            <div class='col-lg-3 col-md-3 col-sm-3 col-xs-3'>
                                                <div class='img-thumbnail flag-wrapper margin-bottom-5' style='height: 28px; width: 40px;  margin-left: 5px;'>
                                                    <span class='flag-icon flag flag-background flag-icon-{!! strtolower( $countries[$i]["country_iso_code"] ) !!}'></span>    
                                                </div> 
                                            </div>
                                            <div class='col-lg-9 col-md-9 col-sm-9 col-xs-9 country-name padding-0'><a href='/country/{!! $countries[$i]["country_alias"] !!}'>{!! $countries[$i]["country_name"] !!}</a></div>
                                            <input type='hidden' name='lat' value='{!! $countries[$i]["latitude"] !!}' />
                                            <input type='hidden' name='lng' value='{!! $countries[$i]["longitude"] !!}' />
                                            <input type='hidden' name='code' value='{!! $countries[$i]["country_iso_code"] !!}' />
                                            <input type='hidden' name='alias' value='{!! $countries[$i]["country_alias"] !!}' />
                                        </div>

                                        @if( $i == 5 )
                                        <br />
                                        <a href='' class='readmore'>...See More Countries</a>
                                        <span class='more'>
                                            @endif

                                        @endfor

                                        @if( $i > 5 )
                                        </span><a href='#' class='readless'>Show Less Countries</a>
                                        @endif
                                    </div>
                                    <div id="map" class='margin-0'></div>
                                </div>
                                @endif

                                @if( $isAdmin )
                                    @include("partials.modals.mediaModal")
                                @endif

                                @if( count( $photos ) > 0)
                                <!-- start photo-tiles -->
                                <div class='photo-tiles margin-bottom-10'>
                                    @if( $isAdmin )
                                        <div class='edit-cog open-photos' style='color: #e5e5e5;' data-toggle='modal' data-target='#mediaModal'>
                                            <i class='fa fa-cog cog'></i>
                                        </div>
                                    @endif
                                    <p>Photos</p>
                                    <div class='row margin-0'>
                                        @for($i = 0 ; $i <  sizeof( $photos ) ; $i++ )
                                        @if( isset( $photos[$i] ) )
                                        @if( $i < 9 )
                                        <div class='col-lg-4 col-md-4 col-sm-4 col-xs-3 padding-0 margin-0'>
                                            <a class='org-photos' href='/images/organization/{!! $photos[$i]->file_path !!}'><img class='img-responsive' src='/images/organization/{!! $photos[$i]->file_path !!}' /></a>
                                            @else
                                            <div class='col-lg-4 col-md-4 col-sm-4 col-xs-3 padding-0 margin-0 hidden'>
                                                <a class='org-photos' href='/images/organization/{!! $photos[$i]->file_path !!}'><img class='img-responsive' src='/images/organization/{!! $photos[$i]->file_path !!}' /></a>
                                                @endif
                                            </div>
                                            @endif
                                            @endfor
                                        </div>
                                    </div><!-- end photo-tiles -->
                                @else
                                    @if( $isAdmin )
                                        <div class='btn btn-primary margin-bottom-10 open-photos' style='width: 100%;' data-toggle='modal' data-target='#mediaModal'>
                                            Add Photos
                                        </div>  
                                    @endif
                                @endif

                                    @if( count( $videos ) > 0 )
                                    <!-- start video-tiles -->
                                    <div class='video-tiles margin-bottom-10'>
                                        @if( $isAdmin )
                                            <div class='edit-cog open-videos' style='color: #e5e5e5;' data-toggle='modal' data-target='#mediaModal'>
                                                <i class='fa fa-cog cog'></i>
                                            </div>
                                        @endif
                                        <p>Videos</p>
                                        <div class='row margin-0'>
                                            @for( $i = 0 ; $i < sizeof( $videos) ; $i++ )
                                            @if( isset( $videos[$i] ) )
                                            @if( $i < 9 )
                                            <div class='col-lg-4 col-md-4 col-sm-4 col-xs-3 padding-0 margin-0'>
                                                @if( preg_match("/^https/", $videos[$i]->video_url) )
                                                <a class='org-videos' href='{!! $videos[$i]->video_url !!}'><img class='img-responsive' src='{!! $videos[$i]->video_id !!}' /></a>
                                                @else
                                                <a class='org-videos' href='{!! str_replace("http", "https", $videos[$i]->video_url)  !!}'><img class='img-responsive' src='{!! $videos[$i]->video_id !!}' /></a>
                                                @endif
                                            </div>
                                            @else
                                            <div class='col-lg-4 col-md-4 col-sm-4 col-xs-3 padding-0 margin-0 hidden'>
                                                @if( preg_match("/^https/", $videos[$i]->video_url) )
                                                <a class='org-videos' href='{!! $videos[$i]->video_url !!}'><img class='img-responsive' src='{!! $videos[$i]->video_id !!}' /></a>
                                                @else
                                                <a class='org-videos' href='{!! str_replace("http", "https", $videos[$i]->video_url)  !!}'><img class='img-responsive' src='{!! $videos[$i]->video_id !!}' /></a>
                                                @endif
                                            </div>
                                            @endif
                                            @endif
                                            @endfor                     
                                        </div>
                                    </div><!-- end video-tiles -->
                                    @else
                                        @if( $isAdmin )
                                            <div class='btn btn-primary margin-bottom-10 open-videos' style='width: 100%;' data-toggle='modal' data-target='#mediaModal'>
                                                Add Videos
                                            </div> 
                                        @endif
                                    @endif
                                    @if( isset( $feeds["twitter"] ) )
                                    <div class='feed twitter-feed margin-bottom-10'>
                                        <p>Twitter Feed</p>
                                        <div style='height: 50px;'>
                                            <img src='{!! $feeds["twitter"]["profile_image"] !!}'  align='left'/>
                                            <div class='screen-name'><a href='https://www.twitter.com/{!! $feeds["twitter"]["screen_name"] !!}' target='_blank'> &#64;{!! $feeds["twitter"]["screen_name"] !!}</a></div>
                                        </div>
                                        @foreach( $feeds["twitter"]["tweets"] as $tweet )
                                        <div class='post-item'>
                                            {!! $tweet["tweet"] !!}
                                            <div class='posted'>Posted: {!! $tweet['date'] !!}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif

                                    @if( isset( $feeds["facebook"] ) )
                                    <div class='feed facebook-feed margin-bottom-10'>
                                        <p>Facebook Feed</p>
                                        <div style='min-height: 55px; height: auto;'>
                                            <img src='//graph.facebook.com/{!! $feeds["facebook"]["id"] !!}/picture' align='left'/>
                                            <div class='screen-name margin-top-0'>{!! $feeds["facebook"]["name"] !!}</div>
                                        </div>
                                        @foreach( $feeds["facebook"]["items"] as $item )
                                        <div class='post-item'>
                                            {!! $item["message"] !!} &nbsp; <a href='https://www.facebook.com/{!! $item["id"] !!}' target='_blank'>View Full Post</a>
                                            <div class='posted'>{!! $item["posted"] !!} </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div><!-- end left side -->
                                <!-- start right side -->
                                <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-right-0 padding-left-5'>
                                    <div class='contact-info' id='orgContactInfo'>
                                        <div style='padding: 20px 15px;'>
                                            <div class='hidden-sm hidden-xs'>
                                                @if( $hasGateway )
                                                <div class='donate-button' data-alias="{!! $org->org_alias !!}">
                                                    donate now
                                                </div>
                                                @endif
                                                <div class='org-actions'>
                                                    <div class='action pull-left'>
                                                        <button class='follow' data-id='{!! $org->org_id !!}' data-type='org'>
                                                            @if( $following == 1)
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
                                            </div>
                                            <div style='position: relative;'>
                                                @if( $isAdmin )
                                                    <div class='edit-cog' style='color: #e5e5e5; top: 0px;' data-toggle='modal' data-target='#contactInfoModal'>
                                                        <i class='fa fa-cog cog'></i>
                                                    </div>
                                                    @include("partials.modals.contactInfo")
                                                @endif
                                                <p >Contact Information</p>
                                                <table>
                                                    @if( ! empty( $org->org_weburl ) )
                                                    <tr>
                                                    @else
                                                    <tr class='hidden'>
                                                    @endif
                                                        @if( strlen( $org->org_weburl ) > 30 )
                                                        <td><i data-icon="'" class="icon pwi-icon-2em"></i></td>
                                                        <td style='padding-left: 5px;' id='orgWebUrl'><a href='{!! $org->org_weburl !!}' target='_blank'> {!! substr( $org->org_weburl, 0, 30 ) !!}...</a></td>
                                                        @else
                                                        <td><i data-icon="'" class="icon pwi-icon-2em"></i></td>
                                                        <td style='padding-left: 5px;' id='orgWebUrl'><a href='{!! $org->org_weburl !!}' target='_blank'>{!! $org->org_weburl !!}</a></td>
                                                        @endif
                                                    </tr>
                                                    
                                                    @if( ! empty( $org->org_mobile_number ) )
                                                    <tr>
                                                    @else
                                                    <tr class='hidden'>
                                                    @endif
                                                        <td><i data-icon='"' class="icon pwi-icon-2em"></i></td>
                                                        <td style='padding-left: 5px;' id='orgPhone'>{!! $org->org_mobile_number !!}</td>
                                                    </tr>
                                                    

                                                    @if( ! empty( $org->org_email ) )
                                                    <tr>
                                                    @else
                                                    <tr class='hidden'>
                                                    @endif
                                                        <td><i data-icon="3" class="icon pwi-icon-2em"></i></td>
                                                        <td style='padding-left: 5px;' id='orgEmail'>{!! $org->org_email !!}</td>
                                                    </tr>
                                                    
                                                    @if( empty( $org->org_addressline1 ) && empty( $org->org_city) && empty( $org->state_code ) && empty( $org->org_zip ) )
                                                    <tr class='hidden'>
                                                    @else
                                                    <tr >
                                                    @endif
                                                        <td valign='top'>
                                                            <i data-icon="Y" class="icon pwi-icon-2em"></i>
                                                        </td>
                                                        <td style='padding-left: 5px;' id='orgStreetAddress'>
                                                            {!! $org->org_addressline1 !!}<br />
                                                            @if( ! empty( $org->org_addressline2 ) )
                                                            {!! $org->org_addressline2 !!}<br />
                                                            @endif
                                                            {!! $org->org_city !!}, {!! $org->state_code !!} {!! $org->org_zip !!}
                                                        </td>
                                                    </tr>
                                                    
                                                </table>
                                            </div>
                                            @if( (int)$org->org_revenue > 0 && ! empty( $org->org_revenue ) )
                                            <hr />
                                            <p>Revenue</p>
                                            <div class='revenue'>{!! money_format('%(#10n', $org->org_revenue ) !!}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class='reviews-container margin-top-10'>
                                        <p>Reviews &nbsp;  <a href='/' class='openCommentModal'>Write Review</a></p>
                                        <div class='reviews'>
                                            @if( count( $reviews ) > 0 )
                                            @for( $i = 0 ; $i < sizeof( $reviews ) ; $i++ )

                                            <div class='review'>
                                                <div class='review-top'>
                                                    <div class='name'>
                                                        {!! $reviews[$i]->comment_username !!} 
                                                    </div>

                                                    <div class='rating'>
                                                        &nbsp;
                                                        @for($j = 1; $j < 6; $j++ )
                                                        @if( $j <= $reviews[$i]->comment_rating )
                                                        <span class="star fill" >
                                                            <i class="icon pwi-icon-star pwi-icon-2em"></i>
                                                        </span>          
                                                        @else
                                                        <span class="star" >
                                                            <i class="icon pwi-icon-star pwi-icon-2em"></i>
                                                        </span>
                                                        @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                                <div class='content'>
                                                    <?php $tmp = explode(" ", $reviews[$i]->comment_text); ?>

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

                                            @endfor
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    @stop