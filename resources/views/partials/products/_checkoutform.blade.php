@if( env("APP_ENV") == "live" )
{!! Form::open( array('url' => 'https://www.paypal.com/cgi-bin/webscr', 'method' => 'post', 'name' => 'paypalStdCheckout', 'class' => 'hidden') ) !!}
@else
{!! Form::open( array('url' => 'https://www.sandbox.paypal.com/cgi-bin/webscr', 'method' => 'post', 'name' => 'paypalStdCheckout', 'class' => 'hidden') ) !!}
@endif
{!! Form::hidden('cmd') !!}
{!! Form::hidden('return') !!}
{!! Form::hidden('notify_url') !!}
{!! Form::hidden('amount') !!}
{!! Form::hidden('quantity') !!}
{!! Form::hidden('shipping') !!}
{!! Form::hidden('tax') !!}
{!! Form::hidden('custom') !!}
{!! Form::hidden('business') !!}
{!! Form::hidden('first_name') !!}
{!! Form::hidden('last_name') !!}
{!! Form::hidden('email') !!}
{!! Form::hidden('address1') !!}
{!! Form::hidden('address2') !!}
{!! Form::hidden('city') !!}
{!! Form::hidden('state') !!}
{!! Form::hidden('zip') !!}
{!! Form::hidden('country') !!}
{!! Form::submit('submit') !!}
{!! Form::close( ) !!}