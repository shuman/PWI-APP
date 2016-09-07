@extends('header')
@section('content')

{!! HTML::Script( 'js/user.js') !!}

<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if(Session::has('flash_message'))
            <div class="alert {{Session::get('flash_type')}}">
                <strong>{{Session::get('flash_message')}}</strong>
            </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Create Your Profile</strong></div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/user/register') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">First Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}">
                                @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Last Name</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}">
                                @if ($errors->has('last_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('user_email') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Email</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="user_email" value="{{ old('user_email') }}">
                                @if ($errors->has('user_email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('user_email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('user_username') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Username</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="user_username" value="{{ old('user_username') }}">
                                @if ($errors->has('user_username'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('user_username') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">
                                @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirmation">
                                @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('birthdaypicker_birthDay') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Date of birth</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control datepicker" name="birthdaypicker_birthDay" value="{{ old('birthdaypicker_birthDay') }}">
                                @if ($errors->has('birthdaypicker_birthDay'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('birthdaypicker_birthDay') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Gender</label>
                            <div class="col-md-6">
                                <select  class="form-control" name="gender">
                                    <option value="">Select</option>
                                    <option value="Male" <?php echo old('gender') == 'Male' ? 'selected="selected"' : '' ?>>Male</option>
                                    <option value="Female" <?php echo old('gender') == 'Female' ? 'selected="selected"' : '' ?>>Female</option>
                                    <option value="Other" <?php echo old('gender') == 'Other' ? 'selected="selected"' : '' ?>>Other</option>
                                </select>

                                @if ($errors->has('gender'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('gender') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('project_impact') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">How did you hear about Project World Impact?</label>
                            <div class="col-md-6">
                                <select class="form-control" name="project_impact" value="{{ old('project_impact') }}">
                                    <option value="">Select</option>
                                    @if(count($data)>0)
                                    @foreach($data as $hearValue)
                                    <option value="{{$hearValue->hearabout_title}}">{{$hearValue->hearabout_title}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('project_impact'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('project_impact') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user fa-fw"></i> Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
        });
    });

</script>
@endsection

