@include("pull-down")
@if( Route::is( "indexPage" ) || Route::is("searchResults") || isset( $isError ) || Route::is("home") )
<div id='wrapper' style='height: 100%; width: 100%; margin: 0; padding:0; position:relative; background-color: #fff;'>
@else
<div id='wrapper' style='height: auto; width: 100%; margin: 0; padding:0; position:relative; background-color: #f9f9f9;'>
@endif

    @if( Agent::isMobile( ) )
      @include('navs.indexNav')
    @else
      @if( Route::is('home') )
          @include('navs.indexNav')
      @else
          @include('navs.stdNav')
      @endif
    @endif
    

    @include("mobile-nav")
    <div class='modals'>
    	@include("modal")
    </div>
    @include("login")

    <div class='container'>
        @yield('content')
    </div>

    @if( isset( $isError ) )
    	<style>
      	footer{
        	position:fixed !important;
        	bottom: 0 !important;
      	}	
      </style>
    @endif
    @include('footer')
    @include('social-links')
    @include('join')
</div>