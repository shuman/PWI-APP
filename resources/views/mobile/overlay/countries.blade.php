@foreach( $zones as $zone )
<!-- Country List for {!! $zone->zone_name !!} -->
<div data-role='page' id='{!! str_replace(" ", "_", strtolower( $zone->zone_name) ) !!}'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-icon-notext
"></a>
		<h1>{!! $zone->zone_name !!}</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<ul data-role="listview" data-inset='true' class='country-list'> 
			{!! $countries[$zone->zone_id] !!}
		</ul>
	</div>
</div>
<!-- End Country List for {!! $zone->zone_name !!} --> 		
@endforeach