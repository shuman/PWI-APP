<div class='mobile-pull-down-menu'>
    <div class='mobile-menu-item search_mobile'>
        <span class='mobile-menu-icon pwi-icon-search'></span>
        <input type='text' name='search' size='30' placeholder='Search' />
        <span class='mobile-menu-icon pwi-icon-close' style='right: 0;'></span>
    </div>
	@if( is_null( $user ) )
	<div class='mobile-menu-item signin_mobile'>
        <span class='mobile-menu-icon pwi-icon-login'></span>
        <div>
            <a href='#login_wrapper' class='signin'>sign in</a>
        </div>
    </div>
    <div class='mobile-menu-item join_mobile'>
        <span class='mobile-menu-icon pwi-icon-join'></span>
        <div>
            <a role='button' data-toggle='collapse' data-parent='join_mobile' href='#registerList' aria-expanded='false'>create account</a>
        </div>
        <span class='mobile-menu-icon glyphicon glyphicon-chevron-down' style='right: 0; font-size: 1.5em; padding-top: 15px; text-align:center;'></span>
    </div>
    <div  id='registerList' class='panel-collapse collapse' role='tab-panel' aria-labeledby=''>
        <div id='mobile_menu_join_indivdual' class='mobile-submenu'>
            <span class='mobile-menu-icon pwi-icon-individual'></span>
            <div><a href='https://portal.projectworldimpact.com/register/0'>join as individual</a></div>
        </div>
        <div id='mobile_menu_join_organization' class='mobile-submenu'>
            <span class='mobile-menu-icon pwi-icon-organization'></span>
            <div><a href='http://join.projectworldimpact.com'>join as organization</a></div>
        </div>
    </div>
	@else
	<div class='mobile-menu-item user_mobile'>
        <img src='{!! $userImg !!}' class='img-rounded' align='left'/>
        <a href='#' class='toUserPortal'>
            @if( empty( $user->user_firstname ) && empty( $user->user_lastname) )
            	{!! $user->user_username !!}
            @else
            	{!! $user->user_firstname !!} {!! $user->user_lastname !!}
            @endif
            </a>
    </div>
    <div class='mobile-menu-item userLogout_mobile'>
        <a href='/auth/logout' class='margin-left-10'>log out</a>
    </div>
    @endif

    @if( ! Route::is('home') )
    <div class='mobile-menu-item browseby_mobile' id='browseby_mobile'>
        <span class='mobile-menu-icon pwi-icon-browse'></span>
        <div>
	       <a role='button' data-toggle='collapse' data-parent='browseby_mobile' href='#browseByList' aria-expanded='false'>browse by</a>
        </div>
        <span class='mobile-menu-icon glyphicon glyphicon-chevron-down' style='right: 0; font-size: 1.5em; padding-top: 15px; text-align:center;'></span>
	</div>
	<div  id='browseByList' class='panel-collapse collapse' role='tab-panel' aria-labeledby=''>
	    <div id='mobile_menu_organizations' class='mobile-submenu'><a href='/organizations'>organizations</a></div>
	    <div id='mobile_menu_causes' class='openCauseModal mobile-submenu'>causes</div>
	    <div id='mobile_menu_countries' class='openCountryModal mobile-submenu'>countries</div>
	    <div id='mobile_menu_crowdfunding' class='mobile-submenu'><a href='/crowdfunding'>crowdfunding</a></div>
	    <div id='mobile_menu_products' class='mobile-submenu'><a href='/products'>products</a></div>
    </div>
    @endif
</div>