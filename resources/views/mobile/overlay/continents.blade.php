<div data-role='page' id='continents'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>Select A Continent</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<ul data-role="listview">
		@for( $i = 0 ; $i < sizeof( $zones ) ; $i++ )
			<li><a href='#{!! str_replace(" ", "_", strtolower( $zones[$i]->zone_name) ) !!}'>{!! $zones[$i]->zone_name !!}</a></li>
		@endfor
		</ul>
	</div>
</div>


