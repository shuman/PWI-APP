@extends('header')

@section('content')

<div class='modal white' id='causeCheckout'>
    <div class='checkout overlay-content'>
        <div class='exit-cause-checkout exit pull-right'>
            <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
        </div>
        <div class='title'>{!! $cause->cause_name !!}</div>
        @include("partials._donation")
    </div>
</div>

<div class='cause-header'>
    <img class='cover_photo' src='{!! $causeImgPath !!}{!! $coverphoto !!}' />
    <div class='cause-header-content'>
        <div class='row'>
            <div class='col-lg-1 col-md-1 col-sm-2 col-xs-4'>
                @if( strlen( $icon ) == 1 )
                <i data-icon="{!! $icon !!}" class="icon pwi-icon-2em"></i>
                @else
                <i class='{!! $icon !!} pwi-icon-2em pull-left'></i>
                @endif
            </div>
            <div class='col-lg-4 col-md-4 col-sm-3 col-xs-8 padding-left-15'>
                <p class='cause_name margin-top-12'>{!! $cause->cause_name !!}</p>
            </div>
            <div class='col-lg-7 col-md-7 col-sm-7 col-xs-7 text-right padding-right-25 hidden-xs hidden-sm'>
                <button type='button' class='btn share margin-top-10 margin-right-10' >share</button>
                <button type='button' class='btn btn-follow margin-top-10 margin-right-10 follow' data-id='{!! $cause->cause_id !!}' data-type='cause' aria-label='Favorite'>
                    @if( ! $following )
                    follow
                    @else
                    unfollow
                    @endif
                </button>
                <button type='button' class='btn btn-donate margin-top-10 padding-left-50 padding-right-50 donate-button' style='max-width: 225px;' aria-label='Favorite' data-alias="{!! $alias !!}">
                    donate now
                </button>
            </div>
        </div>
    </div><!-- end .cause-header-content -->
</div><!-- end .cause-header -->

<div class='top-button hidden-lg hidden-md visible-sm visible-xs'>
    <div class='row'>
        <div class='col-sm-4 col-xs-4 padding-right-0'>
            <div class='donate-button' data-id="{!! $cause->cause_id !!}">
                donate 
                @if( ! Agent::isMobile( ) ) 
                now 
                @endif
            </div>
        </div>

        <div class='col-sm-4 col-xs-4 padding-left-0 padding-right-0'>

            <button class='follow' data-id='{!! $cause->cause_id !!}' data-type='cause' style='width: 100%; max-width: 100%; font-size: 18px;'>
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

<div class='country-content margin-top-5'>
    <div class='row'>
        @if( Agent::isMobile( ) )
        <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12' >
        @else
        <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-right-5'>
        @endif

        @if( sizeof( $orgs ) > 0 )
        <div class='row '>
            <div class='col-lg-12 col-md-12 col-sm-12 padding-right-5'>
                <!-- include org module -->
                @include("modules.organizations")
                <!-- end org module -->
            </div>
        </div>
        @endif

        @foreach( $hashtags as $hashtag )
        <div class='row margin-top-10'>
            @if( ! empty( $twitter[$hashtag] ) && ! empty( $instagram[$hashtag] ) ) 
                @if( Agent::isMobile( ) )
                <div class='col-lg-6 col-md-6 col-sm-6' >
                @else
                <div class='col-lg-6 col-md-6 col-sm-6 padding-right-5' >
                @endif
                    <div class='feed twitter-feed'>
                        <p>Twitter <span class='hashtag'>{!! $hashtag !!}</span></p>
                        @foreach( $twitter[$hashtag] as $tweet )
                        <div class='post-item'>
                            {!! $tweet["tweet"] !!}
                            <div class='posted'>Posted: {!! $tweet['date'] !!}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @if( Agent::isMobile( ) )
                <div class='col-lg-6 col-md-6 col-sm-6' >
                @else
                <div class='col-lg-6 col-md-6 col-sm-6 padding-right-5' >
                @endif
                    <div class='feed instagram-feed'>
                        <p>Instagram <span class='hashtag'>{!! $hashtag !!}</span></p>
                        <div class='row'>
                            @foreach( $instagram[$hashtag] as $post )
                            <div class='col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-0'>
                                <img src='{!! $post["image"] !!}' class='img-responsive' />
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @elseif( ! empty( $twitter[$hashtag] )  && empty( $instagram[$hashtag] ) )
                @if( Agent::isMobile( ) )
                <div class='col-lg-12 col-md-12 col-sm-12'>
                @else
                <div class='col-lg-12 col-md-12 col-sm-12 padding-right-5'>
                @endif
                    <div class='feed twitter-feed'>
                        <p>Twitter <span class='hashtag'>{!! $hashtag !!}</span></p>
                        @foreach( $twitter[$hashtag] as $tweet )
                        <div class='post-item'>
                            {!! $tweet["tweet"] !!}
                            <div class='posted'>Posted: {!! $tweet['date'] !!}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @elseif( empty( $twitter[$hashtag] ) && ! empty( $instagram[$hashtag] ) )
                @if( Agent::isMobile( ) )
                <div class='col-lg-12 col-md-12 col-sm-12'>
                @else
                <div class='col-lg-12 col-md-12 col-sm-12 padding-left-5'>
                @endif
                    <div class='feed instagram-feed'>
                        <p>Instagram <span class='hashtag'>{!! $hashtag !!}</span></p>
                        <div class='row'>
                            @foreach( $instagram[$hashtag] as $post )
                            <div class='col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-0'>
                                <img src='{!! $post["image"] !!}' class='img-responsive' />
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endforeach

        @if( sizeof( $projects ) > 0 )
        <div class='row margin-top-10'>
            <div class='col-lg-12 col-md-12 col-sm-12 padding-right-5'>
                <!-- include project module -->
                @include("modules.project")
                <!-- end project module -->
            </div>
        </div>
        @endif

        @if( sizeof( $products) > 0 )
        <div class='row margin-top-10'>
            <div class='col-lg-12 col-md-12 col-sm-12 padding-right-5'>
                <!-- include product module -->
                @include("modules.product")
                <!-- end product module -->
            </div>
        </div>
        @endif
    </div>
    <div class='col-lg-6 col-md-6 col-sm-6 margin-top-10'>
        <!-- include news module -->
        @include("_news")
        <!-- end news module -->
        <div class='cause-desc padding-10 margin-top-10'>
            <p class='desc-header'>Overview</p>
            <div>{!! $cause->cause_content !!}</div>
            @if( ! empty( $cause->reference ) )
            <a href='' class='readmore'>Show References</a>
            <div class='more'>
                {!! $cause->reference !!}
            </div><a href='#' class='readless'>Hide References</a>
            @endif
        </div>
        @if( count( $subcauses) > 0 )
        <div class='subcauses margin-top-10'>
            <div class='row'>
                <div class='col-lg-5 col-md-5 col-sm-5 col-xs-5 subcause-list padding-right-0'>
                    <p>{!! $cause->cause_name !!} Subcauses</p>
                    @for( $i = 0 ; $i < sizeof( $subcauses ) ; $i++ )
                        @if( $i == 0 )
                        <div class='subCauseName active' data-cause='{!! $i !!}'>
                        @else
                        <div class='subCauseName' data-cause='{!! $i !!}'>
                        @endif
                        <div class='subCauseNameText'>{!! $subcauses[$i]->cause_name !!}</div>
                    </div>
                    @endfor
                </div>
                <div class='col-lg-7 col-md-7 col-sm-7 col-xs-7' style='border-left: 1px solid #f1f1f1;'>
                    @for( $j = 0 ; $j < sizeof( $subcauses) ; $j++ )
                        @if( $j == 0 )
                        <div class='subcause-description subcause-description-{{$j}}'>                 
                        @else
                        <div class='subcause-description subcause-description-{{$j}} hidden'>
                        @endif
                        <div class='subcause-description-header'>{!! $subcauses[$j]->cause_name !!}</div>
                            <?php $tmp = explode(" ", strip_tags( $subcauses[$j]->cause_content, '<sup>' ) ); ?>
                            @if( sizeof( $tmp ) <= 125 )
                                {!! $subcauses[$j]->cause_content !!}
                            @else
                                @for( $i = 0 ; $i < sizeof( $tmp ) ; $i++ )
                                    @if( $i < 125 || $i > 125 )
                                        {!! $tmp[$i] !!}
                                    @else( $i == 125 )
                                        <a href='' class='readmore'>...See More</a>
                                        <span class='more'>{!! $tmp[$i] !!}
                                    @endif
                                @endfor
                                </span><a href='#' class='readless'>Show Less</a>
                            @endif
                        </div>
                    @endfor                          
                    </div>
                </div>
            </div><!-- end of .subcauses -->
            @endif
        </div>
    </div><!-- end .row -->
</div><!-- .country-content -->
@stop