<!-- menu start -->
<div class='mobile-menu'>
	<div class='mobile-menu-item'>
        <div class='mobile-icon pwi-icon-search'></div>
        <input type='text' name='mobile-menu-search' placeholder='search' />
        <span class='pwi-icon-close right-icon exit-search'></span>
        <div style='clear:both;'></div>
    </div>
    @if( ! is_null( $user ) )
    <div class='mobile-menu-item'>
        <div class='mobile-icon pwi-icon-dashboard-general'></div>
        <div style='padding-top: 13px; float: left;'>
            <a href='#login' class='account'>Account</a>
        </div>
    </div>
    <div class='mobile-sub-menu' id='accountSubMenu'>
        <div class='sub-menu-item red'>
            <div style='padding-top: 13px; float: left;'>
                <a href="" data-rel="external">Account Dashboard</a>
            </div>
        </div>
        <div class='sub-menu-item red'>
            <div style='padding-top: 13px; float: left;'>
                <a href="" data-rel="external">Help</a>
            </div>
        </div>
        <div class='sub-menu-item red'>
            <div style='padding-top: 13px; float: left;'>
                <a href="" data-rel="external">Go To Profile Page</a>
            </div>
        </div>
    </div>
    @else
    <div class='mobile-menu-item'>
    	<div class='mobile-icon pwi-icon-join'></div>
    	<div style='padding-top: 13px; float: left;'>
    		<a href='#login' class='join'>Create Account</a>
    	</div>
    </div>
    <div class='mobile-sub-menu' id='joinSubMenu'>
    	<div class='sub-menu-item'>
    		<div class='mobile-icon pwi-icon-individual'></div>
    		<div style='padding-top: 13px; float: left;'>
    			<a href="" data-rel="external">Join as an Individual</a>
    		</div>
    	</div>
    	<div class='sub-menu-item'>
    		<div class='mobile-icon pwi-icon-organization'></div>
    		<div style='padding-top: 13px; float: left;'>
    			<a href="" data-rel="external">Join as an Organization</a>
    		</div>
    	</div>
    </div>
    <div class='mobile-menu-item'>
    	<div class='mobile-icon pwi-icon-login'></div>
    	<div style='padding-top: 13px; float: left;'>
    		<a href='/auth/logout' rel='external'>sign in</a>
    	</div>
    </div>
    @endif
    <div class='mobile-menu-item'>
    	<div class='mobile-icon pwi-icon-browse'></div>
    	<div style='padding-top: 13px; float: left;'>
    		<a href='#browseby' class='browseby'>browse by</a>
    	</div>
    </div>
    <div class='mobile-sub-menu' id='browseSubMenu'>
    	<div class='sub-menu-item' >
    		<a href="/organizations" rel="external" style='margin-left: 45px;'>Organizations</a>
    	</div>
    	<div class='sub-menu-item'>
    		<a href="#causes" style='margin-left: 45px;' class='overlay-link'>Causes</a>
    	</div>
    	<div class='sub-menu-item'>
    		<a href="#countries" style='margin-left: 45px;' class='overlay-link'>Countries</a>
    	</div>
    	<div class='sub-menu-item'>
    		<a href="/crowdfunding" rel="external" style='margin-left: 45px;'>Crowdfunding</a>
    	</div>
    	<div class='sub-menu-item'>
    		<a href="/products" rel="external" style='margin-left: 45px;'>Products</a>
    	</div>
    </div>
    @if( ! is_null( $user ) )
    <div class='mobile-menu-item'>
        <div class='mobile-icon pwi-icon-logout'></div>
        <div style='padding-top:13px; float: left;'>
            <a href='/auth/logout' rel='external'>Logout</a>
        </div>
    </div>
    @endif

</div>

<!-- /menu  -->
<!-- Start of Page -->
@yield('content')
<!-- End of Page -->

<!-- Start of Continent Overlay Page -->
@include("mobile.overlay.continents")
<!-- End of Country Overlay Page -->

<!-- Start of Country Overlay Page -->
@include("mobile.overlay.countries")
<!-- End of Country Overlay Page -->

<!-- Start of Cause Overlay Page -->
@include("mobile.overlay.causes")
<!-- End of Cause Overlay Page -->

<!-- Start Suggest Non Profit Page -->
@include("mobile.overlay.suggest")
<!-- End of Suggest Non Profit Page -->

<!-- Start Generic Login Page -->
@include("mobile.overlay.login")
<!-- End Generic Login Page -->