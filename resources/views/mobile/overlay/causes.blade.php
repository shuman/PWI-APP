<div data-role='page' id='causes'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>Select A Cause</h1>
	</div><!-- /header -->

	<div data-role='main'>
		<ul data-role="listview" class='cause-list'>
		@foreach( $causes as $cause )
			<li><a href='/cause/{!! $cause->cause_alias !!}' rel='external'><div class='icon {!! $iconmap[$cause->cause_id] !!}'></div><div>{!! $cause->cause_name !!}</div></a></li>
		@endforeach
		</ul>
	</div>
</div>

