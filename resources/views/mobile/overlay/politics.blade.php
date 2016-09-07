<div data-role='page' id='politics'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-nodisc-icon ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>
			<div class='img-thumbnail flag-wrapper' style='height: 45px; width: 68px; margin-top: 8px; margin-left: 5px;'>
                <span class='flag-icon flag flag-background flag-icon-{!! strtolower( $country->country_iso_code ) !!}'></span>    
            </div> 
            <p>{!! stripslashes( $country->country_name ) !!}</p>
        </h1>
	</div><!-- /header -->

	<div data-role='main'>
		<ul data-role="listview" class='cause-list'>
		@foreach( $causes as $cause )
			<li><a href='/cause/{!! $cause->cause_alias !!}' rel='external'><div class='icon {!! $iconmap[$cause->cause_id] !!}'></div><div>{!! $cause->cause_name !!}</div></a></li>
		@endforeach
		</ul>
	</div>
</div>

