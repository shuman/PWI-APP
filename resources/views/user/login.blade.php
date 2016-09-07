@extends('header')
@section('content')
{!! HTML::Script( 'js/user.js') !!}
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if(Session::has('flash_message'))
            <div class="alert {{Session::get('flash_type')}}">
                <strong>{{Session::get('flash_message')}}</strong>
            </div>
            @endif
            <div class="login-window">
                <label class="margin-15 font-18">Sign in</label>
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/user/login') }}">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('user_email') ? ' has-error' : '' }}">
                        <label class="col-md-2 control-label">E-Mail</label>

                        <div class="col-md-6">
                            <input type="email" class="form-control" name="user_email" value="{{ old('user_email') }}">

                            @if ($errors->has('user_email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('user_email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="col-md-2 control-label">Password</label>

                        <div class="col-md-6">
                            <input type="password" class="form-control" name="password">
                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-2">
                            <button type="submit" class="btn btn-primary text-uppercase">
                                <i class="fa fa-btn fa-sign-in fa-fw"></i> sign in
                            </button>
                            <a class="btn btn-link" href="{{ url('/password/email') }}">Forgot Your Password?</a>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-10">
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


            </div>
        </div>
    </div>
    @endsection