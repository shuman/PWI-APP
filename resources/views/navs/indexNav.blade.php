<nav class='navbar navbar-default index-navbar'>
    <div class='container-fluid'>
        <div class='navbar-header'>
            <div class='pull-right'>
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

        <div class='collapse navbar-collapse hidden-xs' style='background-color: #fff;'>
            <form class='navbar-form navbar-left' role='search'>
                <div class='form-group home-nav-search hidden-xs'>
                    <input type='text' name='search' placeholder='Search for Causes, Organizations, Countries...' autocomplete='off'  onkeypress="if (event.keyCode == 13) {
                                event.preventDefault();
                                document.getElementById('headersearch').click();
                            }"/>
                    <!--input type='text' name='search' placeholder='Search for Causes, Organizations, Countries...' autocomplete='off'/-->
                    <button type='button' id='headersearch' class='btn btn-default btn-lg search'>
                        <span class="icon pwi-icon-search"></span>
                    </button>
                </div>
                <div class='form-group whatis-container'>
                    <button name='whatis' class='whatis'>What is Project World Impact?</button>
                </div>
            </form>
            <div class='navbar-text navbar-right hidden-xs'>
                @if( is_null( $user ) )
                <ul class='navbar-actions padding-top-20'>
                    <li class='padding-right-15'><a href='#register' class='register join-action'>create account</a></li>&nbsp;
                    <li><a href='#signin' class='signin-action'>sign in</a></li>
                </ul>
                @else
                <ul class='navbar-actions padding-top-11'>
                    <li>
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
                    <li><a href='/auth/logout' class='margin-left-10'>log out</a></li>
                </ul>
                @endif
            </div>
        </div>
    </div>
</nav>


