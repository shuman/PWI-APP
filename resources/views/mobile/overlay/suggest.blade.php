<div data-role='page' id='suggest'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-icon-notext
"></a>
		<h1>Suggest a Nonprofit</h1>
	</div><!-- /header -->

	<div data-role='main'>
		{!! Form::open( array( 'name' => 'suggest', 'data-ajax' => 'false')) !!}
			{!! Form::text('np-website','', array('placeholder' => 'Nonprofit Website') ) !!}
			<div class='error error-0'></div>
			<br />
			{!! Form::text('np-contact','', array('placeholder' => 'Point of Contact Email' ) ) !!}
			<div class='error error-1'></div>
			<br />
			{!! Form::text('your-name', '', array('placeholder' => 'Your Name' ) ) !!}
			<div class='error error-2'></div>
			<br />
			{!! Form::email('your-email', '', array('placeholder' => 'Your Email') ) !!}
			<div class='error error-3'></div>
			<br />
			{!! Form::submit('SUBMIT') !!}
		{!! Form::close( ) !!}
	</div>
</div>