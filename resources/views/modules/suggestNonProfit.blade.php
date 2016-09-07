<div class='exit-suggestNP exit pull-right' data-control="#suggestNonProfitModal">
    <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
</div>
<h3>Suggest a NonProfit</h3>
{!! Form::open( array( 'url' => '/suggest-non-profit', 'name' => 'suggestNonProfitForm')) !!}
<div class='form-group'>
	{!! Form::text('np-website','', array('placeholder' => 'Nonprofit Website', 'class' => 'form-control') ) !!}
	<div class='error error-0'></div>
</div>
<div class='form-group'>
	{!! Form::text('np-contact','', array('placeholder' => 'Point of Contact Email', 'class' => 'form-control')) !!}
	<div class='error error-1'></div>
</div>
<div class='form-group'>
	{!! Form::text('your-name', '', array('placeholder' => 'Your Name', 'class' => 'form-control')) !!}
	<div class='error error-2'></div>
</div>
<div class='form-group'>
	{!! Form::email('your-email', '', array('placeholder' => 'Your Email', 'class' => 'form-control')) !!}
	<div class='error error-3'></div>
</div>
<div class='form-group sub-suggestNP'>
	{!! Form::submit('submit') !!}
</div>

{!! Form::close( ) !!}

<div class='bg-success suggestThankYou'>Thank you for suggesting this NonProfit!</div>