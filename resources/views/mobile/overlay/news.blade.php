<div data-role='page' id='news'>
	<div data-role='header' class='overlay-header grey-bg'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-nodisc-icon ui-btn-icon-left ui-btn-icon-notext
"></a>
		@if( isset( $cause ) )
			<h1>{!! $cause->cause_name !!} News</h1>
		@elseif( isset( $country ) )
			<h1>{!! $country->country_name !!} News</h1>
		@endif
	</div><!-- /header -->
    <div data-role='main'>
	@for( $i = 0 ; $i < sizeof( $news ) ; $i++ )
	   @include("mobile.partials._news")
    @endfor
    </div>
</div>