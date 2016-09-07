@if( Route::is( "indexPage" ) || Route::is("searchResults") )
<nav class='navbar navbar-default std-navbar' style='background-color: #f9f9f9;'>
    @else
    <nav class='navbar navbar-default std-navbar' style='background-color: #ffffff;'>
        @endif
        <div class='container'>
            <div class='navbar-header'>
                <div class='pull-right'>
                    <button type='button' class='mobile-search-button'>
                        <span class="icon pwi-icon-search"></span>
                    </button>

                    <button type="button" class="navbar-toggle" data-toggle="collapse" aria-expanded="false">
                        <span class='sr-only'>Toggle Navigation</span>
                        <span class='icon-bar'></span>
                        <span class='icon-bar'></span>
                        <span class='icon-bar'></span>
                    </button>
                </div>
                <a class="navbar-brand" href='/'>
                    <!-- {!! HTML::image('images/pwi_logo.png', 'PWI') !!} -->
                    <span class="icon pwi-icon-pwi-logo"></span> 
                </a>
            </div>
            <div class="collapse hidden-xs navbar-collapse" id="std-navbar-collapse">
                <form class='navbar-form navbar-left' role='search'>
                    <div class='form-group'>
                        <input class="margin-right-0" type='text' name='search' placeholder='Search' autocomplete='off' onkeypress="if (event.keyCode == 13) {
                                    event.preventDefault();
                                    document.getElementById('headersearch').click();
                                }"/>
                        <!--input class="margin-right-0" type='text' name='search' placeholder='Search' autocomplete='off'/-->
                        <button type='button' id='headersearch' class='search'><span class="icon pwi-icon-search"></span></button>
                    </div>
                </form>

                <ul class='nav navbar-nav navbar-right'>
                    
                    @if(!is_null( $user))
                    <li class='user-data pull-left'>
                        <a href="{{ url('/user/dashboard') }}" class=" text-grey">
                            @if( ! empty( $userImg ) )
                            <img src='{!! $userImg !!}' class='user-image img-rounded' />
                            @else
                            <img src="{{asset('images/fallback-img.png')}}" class="user-image img-rounded" alt="">
                            @endif

                            @if( empty( $user->user_firstname ) && empty( $user->user_lastname) )
                            <span class='user-name'>{!! $user->user_username !!}</span>
                            @else
                            <span class='user-name'>{!! $user->user_firstname !!} {!! $user->user_lastname !!}</span>
                            @endif
                        </a>
                    </li>    
                    <li class='dropdown margin-top-10 border-left'>
                        <a href='#' class='dropdown-toggle nav-browseby' data-toggle='dropdown' rold='button' aria-haspopup='true' aria-expanded='false' style='color: #000; font-weight: bold;'>browse by</a>
                        <ul class='dropdown-menu'>
                            <li><a href='#' class='openCauseModal'>Causes</a></li>
                            <li><a href='#' class='openCountryModal'>Country</a></li>
                            <li><a href='/organizations'>Organization</a></li>
                            <li><a href='/crowdfunding'>Crowdfunding</a></li>
                            <li><a href='/products'>Products</a></li>
                        </ul>
                    </li>
                    <li class='margin-top-10 border-left'>
                        <a href='/auth/logout' class='margin-left-11 logout text-grey' style='color: #000; font-weight: bold;'>log out</a>
                    </li>
                    @endif

                    @if( is_null( $user ) )
                    <li class='join-std-nav margin-top-11'>
                        <a href='#register' style='color: #f1657f; font-weight: bold;' class='join-action'>create account</a>
                    </li>
                    <li class='signin-std-nav margin-top-11 border-left'>
                        <a href='#signin' class='signin-action' style='color: #33aef4; font-weight: bold;'>sign in</a>
                    </li>
                    <li class='dropdown margin-top-10 border-left'>
                        <a href='#' class='dropdown-toggle nav-browseby' data-toggle='dropdown' rold='button' aria-haspopup='true' aria-expanded='false' style='color: #000; font-weight: bold;'>browse by</a>
                        <ul class='dropdown-menu'>
                            <li><a href='' class='openCauseModal'>Causes</a></li>
                            <li><a href='' class='openCountryModal'>Country</a></li>
                            <li><a href='/organizations'>Organization</a></li>
                            <li><a href='/crowdfunding'>Crowdfunding</a></li>
                            <li><a href='/products'>Products</a></li>
                        </ul>
                    </li>
                    @endif

                </ul>
            </div>
        </div>
    </nav>
