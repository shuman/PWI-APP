@extends('header')
@section('content')
<!-- resources/views/auth/password.blade.php -->
<style>
    .normal-footer{
        position: absolute !important;
        bottom: 0px;
    }
</style>
<div class='container text-center' >

    <div class='forgotEmailWrapper' >
        <h3>
            Forgot your password?<br />
            <small>Enter your email below to retrieve password. <br />Need help? <a href='http://imstuck.projectworldimpact.com' target='_blank'>Click here</a>. </small>
        </h3>
        <form method="POST" action="{{url('/password/email')}}">
            {!! csrf_field() !!}
            @if (count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <div style='display: table;' class='center text-left '>
                <div style='display: table-row;'>
                    <div style='font-size: 18px; font-weight: 700; display: table-cell;'>
                        <label for='email'>Email</label><br />
                        <input type="email" name="user_email" value="{{ old('user_email') }}" size='35'>
                    </div>
                </div>
                <div  style='display: table-row;'>
                    <div class='padding-top-10' style='display: table-cell;'>
                        <button type="submit" class='forgotPassword'>
                            Send Password Reset Link
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
