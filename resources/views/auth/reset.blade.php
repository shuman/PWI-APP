@extends('header')
@section('content')

<!-- resources/views/auth/reset.blade.php -->

<style>
    .normal-footer{
        position: absolute !important;
        bottom: 0px;
    }
</style>

<div class='container text-center' >

    <div class='forgotEmailWrapper' >
        <h3>
            Reset your password below.
        </h3>

    <form method="POST" action="/password/reset">
        {!! csrf_field() !!}
        <input type="hidden" name="token" value="{{ $token }}">

        @if (count($errors) > 0)
            <p class='bg-danger'>
                @foreach ($errors->all() as $error)
                    <div class='text-danger'>{{ $error }}</div>
                @endforeach
            </p>
        @endif

        <div style='display: table;' class='center margin-top-10'>
            <div style='display: table-row'>
                <div style='display: table-cell;' class='text-left padding-10'>
                    Email
                </div>
                <div style='display: table-cell;' class='padding-10'>
                    <input type="email" name="user_email" value="{{ old('email') }}" size='35'>
                </div>
            </div>
            <div style='display: table-row'>
                <div style='display: table-cell;' class='text-left padding-10'>
                    Password
                </div>
                <div style='display: table-cell;' class='padding-10'>
                    <input type="password" name="password" size='35'>
                </div>
            </div>
            <div style='display: table-row'>
                <div style='display: table-cell;' class='text-left padding-10'>
                    Confirm Password
                </div>
                <div style='display: table-cell;' class='padding-10'>
                    <input type="password" name="password_confirmation" size='35'>
                </div>
            </div>
            <div style='position: absolute; width: 100%; max-width: 275px;'>
                <div >
                    <button type="submit" class='resetPassword'>
                        Reset Password
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@stop