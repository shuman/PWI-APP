<div class='login-popover-wrapper'>
	<div class='text header'>Sign In</div>
	<div class='inputContainer'>
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		{!! Form::email('email', '', array('placeholder'=>'Email') ) !!}
		<div class='error email-error'></div>
		{!! Form::password('', array('placeholder'=>'Password') ) !!}
		<div class='error generic-error'></div>
		{!! Form::button('sign in', array("id"=>"actionSignIn")) !!}
	</div>
	<div class='text forget'>
	 Forget email or password?
	</div>
	<div class='text create'>
	Create a profile
	</div>
	<div class='social'>
		<div class='text header'>
			Or sign in with
		</div>
		<div class='buttons'>
			<div class='button-login facebook'>
				<i class='icon pwi-social-facebook pwi-font-2em'></i><a href='/auth/social/facebook' rel='external'>Facebook</a>
			</div>
			<div class='button-login twitter'>
				<i class='icon pwi-social-twitter pwi-font-2em'></i><a href='/auth/social/twitter' rel='external'>Twitter</a>
			</div>
			<div class='button-login google'>
				<i class='icon pwi-social-google pwi-font-2em'></i><a href='/auth/social/google' rel='external'>Google</a>
			</div>
		</div>


	</div>

</div>