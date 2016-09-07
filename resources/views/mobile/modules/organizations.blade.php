@if( sizeof( $orgs ) > 0 )
<div class='module-header'> 
	<span class='left' >Organizations</span>
	<!--<span class='right'><a>See More</a></span>-->
</div>
<div class='org-module-items'>
@foreach( $orgs as $org )
	<div class='org-module'>
		<div class='org-module-img-container margin-right-15 margin-bottom-5 padding-0 '>
            <a href='/organization/{!! $org["alias"] !!}' rel='external'><img src='{!! $org["logo"] !!}' align='left'/></a>
        </div><!-- end .org-module-img-container -->
        <div class='org-module-data'>
            <div class='org-module-name'><a href='/organization/{!! $org["alias"] !!}' rel='external'>{!! stripslashes( $org["name"] ) !!}</a></div>
            
            <!-- Organization Impact Countries-->
            @if( ! empty( $org["impactCountries"] ) )
            <div class='org-impacts-causes'>
                <span class='title'>Locations</span>&nbsp;
                <span class='list'>{!! $org["impactCountries"] !!}</span>
            </div><!-- end .impact-causes -->
            @endif
            
            <!-- Organization Impact Causes -->
            @if( ! empty( $org["causes"] ) )
            <div class='org-impacts-causes'>
                <span class='title'>Causes</span>&nbsp;
                <span class='list'>{!! $org["causes"] !!}</span>
            </div><!-- end .impacts-causes -->
            @endif

        </div><!-- end .pull-left -->
        <div style='clear:both;'></div>
	</div>
@endforeach
</div>
@endif