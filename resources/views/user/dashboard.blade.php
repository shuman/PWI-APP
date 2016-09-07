@extends('header')
@section('content')
{!! HTML::Script( 'js/handlebars.js') !!}
{!! HTML::Script( 'js/user.js') !!}
<div class="user" id="dashboard">
    <div class="user-wrpper">
        <div id="sidebar-left">
            @include('navs.sidebarLeft')
        </div>
        <div class="main-content">
            <div class="dashboard-wrapper" id="dashboardContent">
                <div class="widget">
                    <div class="user-bio">
                        <form enctype="multipart/form-data">
                            <a href="javascript:void(0)" class="profileImage">
                                @if( ! empty( $userImg ) )
                                <img src='{!! $userImg !!}' class='user-image img-rounded'/>
                                @else
                                <img src="{{asset('images/fallback-img.png')}}" class="user-image img-rounded" alt="">
                                @endif
                            </a>
                            <input type="file" class="upload" id="imgInp" style="display: none;">
                        </form>
                        <h4>{{Auth::user()->user_firstname.' '.Auth::user()->user_lastname}}</h4>
                        @if(!empty(Auth::user()->user_bio))
                        <p>{!! nl2br(e(Auth::user()->user_bio)) !!}</p>
                        @else
                        <p style="font-weight:bold; font-style:italic;">TIP: You can change your profile picture, bio, and other preferences by selecting <a href="/user/settings">"Settings"</a> from the left menu!</p>
                        @endif
                    </div>
                    <strong class="image-msg text-success"></strong>
                </div>
                <div class="widget">
                    <h1>Welcome to your dashboard!</h1>
                    <p>This page is your personal portal to PWI.  It allows you to see - in one place - the countries, organizations, and causes that mean the most to you!</p>
                    <p>Follow additional organizations, countries, or causes from the sidebar ... or, <a href="/">click here</a> to discover something new. </p>
                </div>
                @if(count($latest_video)>0)
                @foreach($latest_video as $video)
                <div class="widget">
                    <div class="company-info">
                        <img src="{{$video['logoImgPath']}}" alt="{{$video['org_name']}}" class="propic-sm">
                        <a href="/organization/{{$video['org_alias']}}"> <h2 class="company-title">{{$video['org_name']}}</h2></a>
                        <div class="company-desc">
                            <p class="min-text">{!! nl2br($video['description']) !!}</p>
                        </div>
                    </div>
                    <div class="post">
                        <script id="video-iframe" type="text/html">
                            {!! Helper::url2embed($video['video_url']) !!}
                        </script>
                        <div class="video_wrapper videos latestVideo">
                            <img src="{{$video['video_id']}}" alt="{{$video['org_name']}}" style="width:100%;border-radius: 4px;">
                            <a href="javascript:void(0)" class="video videoPlay" data-id="{{$video['org_video_id']}}">
                                <span class="play-btn"></span>  
                            </a>
                        </div>
                        <div class="post-meta">
                            <a href="javascript:void(0)" class="sharer text-blue margin-right-40">Share
                                <div class="share-pop arrow-box">
                                    <ul>
                                        <li class="fb">
                                            <span class="icon pwi-social-facebook shareLink" data-index="facebook" data-title="{{url('/organization/',$video['org_alias'])}}"></span>
                                        </li>
                                        <li class="tw">
                                            <span class="icon pwi-social-twitter shareLink" data-index="twitter" data-title="{{url('/organization/',$video['org_alias'])}}"></span>
                                        </li>
                                        <li class="in">
                                            <span class="icon pwi-social-instagram shareLink" data-inex="instagram" data-title="{{url('/organization/',$video['org_alias'])}}"></span>
                                        </li>
                                    </ul>
                                </div>
                            </a>
                            <a href="{{url('/organizations')}}" class="read_more text-right">See More</a>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
                @if(count($user_news_feed)>0)
                @foreach($user_news_feed as $feed_news_value)
                <div class="widget">
                    <div class="company-info">
                        <img src="{{$feed_news_value['logoImgPath']}}" alt="{{$feed_news_value['org_name']}}" class="propic-sm">
                        <h2 class="company-title">{!! stripslashes($feed_news_value['org_name'])!!}
                            @if(!empty($feed_news_value['causes']))
                            <a href="#">Becasue You Follow <?php
                                $causes = json_decode($feed_news_value['causes'], TRUE);
                                foreach ($causes as $value) {
                                    echo $value['cause_name'];
                                }
                                ?>
                            </a>
                            @endif
                        </h2>
                        <div class="company-desc">
                            <p class="min-text">{!! nl2br($feed_news_value['description'])!!}</p>
                        </div>
                    </div>
                    <div class="post">
                        <div class="cf-project">

                            <a class="project-img" href="#"><img src="{{$feed_news_value['projectImgPath']}}" alt=""></a>

                            <h2>{{$feed_news_value['project_title']}}</h2>
                            <ul>
                                <li>
                                    <h5>Locations</h5>
                                    <p>{{$feed_news_value['address']}} {{$feed_news_value['org_state']}}</p>
                                </li>
                                @if(!empty($feed_news_value['causes']))
                                <li>
                                    <h5>Causes</h5>
                                    <p> 
                                        <?php
                                        $causes = json_decode($feed_news_value['causes'], TRUE);
                                        foreach ($causes as $value) {
                                            echo $value['cause_name'];
                                        }
                                        ?>
                                    </p>
                                </li>
                                @endif
                            </ul>
                        </div>
                        <div>
                            <?php echo $feed_news_value['project_story']; ?>
                        </div>
                        <div class="post-meta">
                            <a href="{{url('/organizations')}}" class="read_more text-right">See More</a>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
                @if(count($latest_photo)>0)
                @foreach($latest_photo as $photo_data)
                @if(!empty($photo_data['photo']))
                <div class="widget">
                    <div class="company-info">
                        <img src="{{$photo_data['logoImgPath']}}" alt="{{$photo_data['org_name']}}" class="propic-sm">
                        <a href="/organization/{{$photo_data['org_alias']}}"> <h2 class="company-title">{!! stripslashes($photo_data['org_name']) !!}</h2></a>
                        <div class="company-desc">
                            <p class="min-text">{!! nl2br($photo_data['org_desc']) !!}</p>
                        </div>
                    </div>
                    <div class="post">
                        <?php
                        $photos = json_decode($photo_data['photo'], TRUE);
                        $length = count($photos);
                        $tmp = array();
                        if ($length > 0) {
                            foreach ($photos as $key => $photo) {
                                $tmp[$key] = $photo['orgImg'];
                            }
                        }
                        $count = count($tmp);
                        ?>
                        @if($count>1)
                        <div class="latest_photo">
                            @if ($count >= 5)
                            <div class="photo-left">
                                <img src="{{$tmp[0]}}" alt="latest-photo">
                            </div>
                            <div class="photo-right">
                                <div class="up-image"> 
                                    <img src="{{$tmp[1]}}" class="first-img" alt="latest-photos">
                                    <img src="{{$tmp[2]}}" class="second-img" alt="latest-pgotos">
                                </div>

                                <div class="down-image"> 
                                    <img src="{{$tmp[3]}}" class="first-img" alt="latest-photos">
                                    <img src="{{$tmp[4]}}" class="second-img" alt="latest-pgotos">
                                </div>
                            </div>
                            @elseif ($count < 5 && $count > 2) 
                            <div class="photo-left">
                                <img src="{{$tmp[0]}}" alt="latest-photo">
                            </div>
                            <div class="photo-right">
                                <div class="up-image-3"> 
                                    <img src="{{$tmp[1]}}" alt="latest-photo">
                                </div>
                                <div class="down-image-3"> 
                                    <img src="{{$tmp[1]}}" alt="latest-photo">
                                </div>
                            </div>
                            @elseif ($count < 3 && $count > 1) 
                            <div class="photo-left">
                                <img src="{{$tmp[0]}}" alt="latest-photo">
                            </div>
                            <div class="photo-right-2">
                                <img src="{{$tmp[1]}}" alt="latest-photo">
                            </div>
                            @else 
                            <img src="{{$tmp[0]}}" alt="latest-photo">
                            @endif
                            @endif
                        </div>

                        <div class="post-meta">
                            <a href="javascript:void(0)" class="sharer text-blue margin-right-40">Share
                                <div class="share-pop arrow-box">
                                    <ul>
                                        <li class="fb">
                                            <span class="icon pwi-social-facebook shareLink" data-index="facebook" data-title="{{url('/organization/',$photo_data['org_alias'])}}"></span>
                                        </li>
                                        <li class="tw">
                                            <span class="icon pwi-social-twitter shareLink" data-index="twitter" data-title="{{url('/organization/',$photo_data['org_alias'])}}"></span>
                                        </li>
                                        <li class="in">
                                            <span class="icon pwi-social-instagram shareLink" data-inex="instagram" data-title="{{url('/organization/',$photo_data['org_alias'])}}"></span>
                                        </li>
                                    </ul>
                                </div>
                            </a>
                            <a href="{{url('/organizations')}}" class="read_more text-right">See More</a>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
                @endif

                <!-- News -->
                @if(count($country_news)>0)
                <?php
                $i = 0;
                $j = 0;
                ?>
                <div class="widget showMore countryNews">
                    <div class="widget-title">
                        <h2>{{$country_name}} News <span><a href="javascript:void(0)" class="text-center moreCountryNews" >See More</a></span></h2>
                    </div>
                    <div id="showMore">
                        @foreach($country_news as $news)
                        @if($i<3)
                        <div class="news-feed">
                            <img src="{{$news['image']}}" alt="{{$news['image']}}" class="propic-sm">
                            <h2 class="news-title newsTitle" data-index="{{$i}}">{{$news['title']}}</h2>
                            <div class="news-meta">
                                <span class="source">{{$news['source']}}</span> <span class="timeago"> {{$news['date']}}</span>
                            </div>
                            <div class="news-desc truncate">
                                <p>{{$news['text']}}</p>
                                <a class="read-more" href="{{$news['link'][0]}}">Read More</a>
                            </div>
                        </div>
                        <?php $j++ ?>
                        @endif
                        <?php $i++; ?>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="more_country_news" value="{{$j}}">
                @endif

                <!-- New Organization -->
                @if(count($new_organization)>0)
                <div class="widget">
                    <div class="widget-title">
                        <h2>New Organizations <span><a href="{{url('/organizations')}}" class="text-center">See More</a></span>
                            @foreach($new_organization as $key=> $organization)
                            @if($key==0)
                            <a href="/cause/{{$organization['cause_alias']}}">
                                <span class="widget-title-right">{!! stripslashes($organization['cause_name']) !!} <i class="icon {{$organization['icon_class']}}"></i></span>
                            </a>
                            @endif
                            @endforeach
                        </h2>
                    </div>
                    <div class="row org_logos">
                        @foreach($new_organization as $organization)
                        <div class="col-md-4">
                            <div class="org-logo">
                                <img src="{{$organization['logoImgPath'] }}" alt="{{$organization['org_name']}}">
                            </div>
                            <div class="text-blue margin-top-5">
                                <a href="/organization/{{$organization['org_alias']}}">{!! stripslashes($organization['org_name']) !!}</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="post-meta">
                        <!--                        <a href="#" class="sharer text-blue margin-right-40">Share
                                                    <div class="share-pop arrow-box">
                                                        <ul>
                                                            <li class="fb"><span class="icon pwi-social-facebook"></span></li>
                                                            <li class="tw"><span class="icon pwi-social-twitter"></span></li>
                                                            <li class="in"><span class="icon pwi-social-instagram"></span></li>
                                                        </ul>
                                                    </div>
                                                </a>-->
                        <a href="{{url('/organizations')}}" class="read_more text-right">See More</a>
                    </div>
                </div>
                @else
                @endif

                <!-- Water News -->
                @if(count($causes_news)>0)
                <?php
                $cn = 0;
                $last_id = 0;
                ?>
                <div class="widget showCausesMore" id="causesNews">
                    <div class="widget-title">
                        <h2>{{$causes_name}} News <span><a href="javascript:void(0)" class="text-center moreCauses">See More</a></span></h2>
                    </div>
                    <div id="showCausesMore">
                        @foreach($causes_news as $news)
                        @if($cn<3)
                        <div class="news-feed">
                            <img src="{{$news['image']}}" alt="{{$news['image']}}" class="propic-sm">
                            <h2 class="news-title causeNewsTitle" data-index="{{$cn}}">{{$news['title']}}</h2>
                            <div class="news-meta"><span class="source">{{$news['source']}}</span> <span class="timeago"> {{$news['date']}}</span></div>
                            <div class="news-desc">
                                <p>{{$news['text']}}</p>
                                <a class="read-more" href="{{$news['link'][0]}}">Read More</a>
                            </div>
                        </div>
                        <?php $last_id = $last_id + 1 ?>
                        @endif

                        <?php $cn++; ?>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="newslimit" class="causes_news_last_id" value="{{$last_id}}">
                @endif
            </div>

            <div id="sidebar-right">
                @include('navs.sidebarRight')
            </div>
        </div>
    </div>
</div>
@endsection
