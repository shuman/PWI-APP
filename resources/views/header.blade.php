<!doctype html>
<html lang="en">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include("_meta")
        @if( Agent::isMobile( ) )
        {!! HTML::style( 'css/mobile/jquery.mobile.min.css') !!}
        {!! HTML::script('js/mobile.js') !!}
        <script>
            $(document).on('mobileinit', function () {
                $.mobile.ignoreContentEnabled = true;
            });
        </script>
        {!! HTML::script('js/jquery.mobile.min.js') !!}
        {!! HTML::style( 'css/mobile.css') !!}
        {!! HTML::style('css/pwi-font.css') !!}
        @if( Route::is('home') )
        {!! HTML::script('js/mobileAdjust.js') !!}
        @endif

        @else
        {!! HTML::script('js/app.js') !!}
        {!! HTML::script('js/jquery.dotdotdot.min.js') !!}
        {!! HTML::script('js/functions.js') !!}
        @if( isset( $scriptPage ) )
        @include($scriptPage)
        @endif
        {!! HTML::style('css/app.css') !!}
        {!! HTML::style('css/pwi-font.css') !!}
        {!! HTML::style('css/colorbox.css') !!}
        @endif
        {!! HTML::style('css/datepicker.css') !!}
        {!! HTML::style('css/select-css/select2.min.css') !!}
        @if( ! Route::is('home') && ! Agent::isMobile( ) )
        <style>
            footer{
                position: relative !important;
            }	
        </style>	
        @endif
        <link rel="icon" type="image/png" href="{!! URL::to('/') !!}/images/favicon.ico">
        <script>
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
            ga('create', 'UA-42851086-1', 'auto');
            ga('send', 'pageview');
        </script>
    </head>
    <body>

        <div id='fb-root'></div>
        <script>
            //Facebook Script
            window.fbAsyncInit = function () {
                FB.init({
                    appId: '{!! env("FACEBOOK_APP_ID") !!}',
                    xfbml: true,
                    version: 'v2.5'
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=1687430964862274";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>



        <script>!function (d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
              if (!d.getElementById(id)) {
                  js = d.createElement(s);
                  js.id = id;
                  js.src = p + '://platform.twitter.com/widgets.js';
                  fjs.parentNode.insertBefore(js, fjs);
              }
          }(document, 'script', 'twitter-wjs');</script>

        <script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>
        {!! HTML::script('js/custom/select-js/select2.js') !!}
        {!! HTML::script('js/birthday-picker/bootstrap-datepicker.js') !!}

        @if( Agent::isMobile( ) && ! Agent::isTablet( ) )
        	@include("content.mobile")
        @else
        	@include("content.desktop")
        @endif
    </body>
</html>