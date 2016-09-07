<div class='login-wrapper' >
    <div class='login-window' id="login_wrapper">
        <div class='login-flash-alert'></div>
        <div class='login-credentials'>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class='form-group'>
                {!! Form::text('email', '', array('placeholder'=>'Email') ) !!}
                <div class='error email-error'></div>
            </div>
            <div class='form-group'>
                {!! Form::password('password', array('placeholder'=>'Password') ) !!}
                <div class='error generic-error'></div>
            </div>
            <!--div class='form-group'>
                    <label>Log in as:</label><br />
                    <label class='radio-inline'>
                        <input type='radio' name='loginAs' value='user'  checked/> User
                    </label>
                    <label class='radio-inline'>
                            <input type='radio' name='loginAs' value='org_users' />Organization
                    </label>
                </div-->
            <div class='row'>
                <div class='col-lg-5 col-md-5'>
                    <p>Forgot email or password? <a href='/password/email'>Click here</a></p>
                    <p><a href='https://portal.projectworldimpact.com/register/0' class='create-profile'>Create a profile</a></p>
                </div>
                <div class='col-lg-7 col-md-7'>
                    {!! Form::button('sign in', array("id"=>"actionSignIn")) !!}
                </div>
            </div>
        </div>
        <hr />
        <div class='social-media-options'>
            <p style='font-size: 12px;'>Or sign in with</p>
            <div class='social-media-buttons'>
                <div class='social-btn btn-facebook'>
                    <span class='pwi-social-facebook' aria-hidden="true"></span> <div class='btn-content'>Facebook</div>
                </div>
                <div class='social-btn btn-twitter'>
                    <span class='pwi-social-twitter' aria-hidden="true"></span> <div class='btn-content'>Twitter</div>
                </div>
                <div class='social-btn btn-google'>
                    <span class='pwi-social-google' aria-hidden="true"></span> <div class='btn-content'>Google</div>
                </div>
            </div>
        </div>
    </div>
</div>
@if( ! is_null( $user ) )
<div style='position: absolute; top:9999999; left:9999999'>
    {!! Form::open( array( 'url' => '/user/login', 'name' => 'login')) !!}
    {!! Form::hidden('id',$user->user_id) !!}
    {!! Form::close( ) !!}
</div>
@endif
