@extends('header')
@section('content')

{!! HTML::Script( 'js/handlebars.js') !!}
{!! HTML::Script( 'js/user.js') !!}

<div class="user">
    <div class="user-wrpper">
        <div id="sidebar-left">
            @include('navs.sidebarLeft')
        </div>
        <div class="main-content">
            <div class="settings">
                <div class="widget">
                    <div class="setting-content user-preference-init">
                        <div class="widget-title"><h2>User Preferences<a href="javascript:void(0)" class="config user-set-pref"><i class="fa fa-cog cog"></i></a></h2></div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label text-blue">First Name</label>
                                <p class="font-18 text-grey">{{$user_data->user_firstname}}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label text-blue">Date of Birth</label>
                                <p class="font-18 text-grey dateOfBirth" data-index="{{$user_data->user_dob}}">
                                    @if(!empty(strtotime($user_data->user_dob)))
                                    {{$user_data->user_dob}}
                                    @else
                                    yyyy-mm-dd
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row margin-top-10">
                            <div class="col-md-6">
                                <label class="control-label text-blue">Last Name</label>
                                <p class="font-18 text-grey">{{$user_data->user_lastname}}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label text-blue">Gender</label>
                                <p class="font-18 text-grey text-capitalize">{{$user_data->user_gender}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label text-blue">Username</label>
                                @if(empty($user_data->user_username))
                                <?php
                                $username_alias = !empty($user_data->user_firstname . $user_data->user_lastname) ? $user_data->user_firstname . $user_data->user_lastname : $user_data->user_email;
                                $user_name = Helper::generateUsername($username_alias);
                                ?>
                                <p class="font-18 text-grey">{{$user_name}}</p>
                                @else 
                                <p class="font-18 text-grey">{{$user_data->user_username}}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="control-label text-blue">Password</label>
                                <p class="font-18 text-grey">************</p>
                            </div>
                        </div>
                        <div class="row margin-top-10">
                            <div class="col-md-6">
                                <label class="control-label text-blue">Email</label>
                                <p class="font-18 text-grey">{{$user_data->user_email}}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label"><a class="text-blue user-set-pref" href="javascript:void(0)">Change Profile Picture</a></label><br>
                                <div class="col-md-6">
                                    @if( ! empty( $userImg ) )
                                    <img src="{!! $userImg !!}" alt="profile-image" class="img-responsive img-circle user-image">
                                    @else 
                                    <img src="{{asset('images/fallback-img.png')}}" alt="profile-image" class="img-responsive img-circle user-image">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row margin-top-10">
                            <div class="col-md-12">
                                <label class="text-blue">Biography</label>
                                <p class="text-grey">{!! nl2br(e($user_data->user_bio)) !!}</p>
                            </div>
                        </div>
                    </div>
                    <div class="setting-content edit-settings-content" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="widget-title"><h2>User Preferences</h2></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label text-blue">First Name</label>
                                <input type="text" class="form-control user_firstname" value="{{$user_data->user_firstname}}">
                                <span class="user_firstname_msg"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label text-blue">Date of Birth</label>
                                <input type="text" class="form-control birthDay datepicker" name="birthday" placeholder="yyyy-mm-dd" value="{{$user_data->user_dob}}">
                                <span class="user_dob_msg"></span>
                            </div>
                        </div>
                        <div class="row margin-top-10">
                            <div class="col-md-6">
                                <label class="control-label text-blue">Last Name</label>
                                <input type="text" class="form-control user_lastname" value="{{$user_data->user_lastname}}">
                                <span class="user_lastname_msg"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label text-blue">Gender</label>
                                <select  class="form-control gender user-gender" name="gender">
                                    <option value="">Select</option>
                                    <option value="Male" <?php echo $user_data->user_gender == 'male' ? 'selected="selected"' : '' ?>>Male</option>
                                    <option value="Female" <?php echo $user_data->user_gender == 'female' ? 'selected="selected"' : '' ?>>Female</option>
                                    <option value="Other" <?php echo $user_data->user_gender == 'other' ? 'selected="selected"' : '' ?>>Other</option>
                                </select>
                                <span class="user_gender_msg"></span>
                            </div>
                        </div>
                        <div class="row margin-top-10">
                            <input type="hidden" name="url" class="base_url" value="{{url('/')}}">
                            <div class="col-md-6">
                                <label class="control-label text-blue">Username</label>
                                <input type="text" class="form-control curr-username" value="{!! !empty($user_data->user_username)? $user_data->user_username :$user_name!!}" disabled="">
                                <input type="text" class="form-control margin-top-10 new-username" value="" placeholder="New Username">
                                <span class="username_message"></span>
                                <span class="user_name_confirm"></span>
                                <input type="text" class="form-control margin-top-10 confirm-username" value="" placeholder="Confirm Username">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label text-blue">Password</label>
                                <input type="password" class="form-control curr-password" value="*********" disabled="">
                                <input type="password" class="form-control margin-top-10 new-password" value="" placeholder="New Password">
                                <span class="user_pass_confirm"></span>
                                <input type="password" class="form-control margin-top-10 confirm-password" value="" placeholder="Confirm Password">
                            </div>
                        </div>
                        <div class="row margin-top-20">
                            <div class="col-md-6">
                                <label class="control-label text-blue">Email</label>
                                <input type="email" class="form-control curr-email" value="{{$user_data->user_email}}" disabled="">
                                <input type="email" class="form-control margin-top-10 new-email" value="" placeholder="New Email">
                                <span class="email_message"></span>
                                <span class="user_email_confirm"></span>
                                <input type="email" class="form-control margin-top-10 confirm-email" value="" placeholder="Confirm Email">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label text-blue">Change Profile Picture</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        @if( ! empty( $userImg ) )
                                        <img src="{!! $userImg !!}" alt="profile-image" class="img-responsive img-circle user-image">
                                        @else 
                                        <img src="{{asset('images/fallback-img.png')}}" alt="profile-image" class="img-responsive img-circle user-image">
                                        @endif
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <div class="fileUpload btn btn-blue btn-sm-sw margin-top-50">
                                            <form enctype="multipart/form-data">
                                                <span class="upload-text">Upload</span>
                                                <input type='file' name="logo" class="upload" id="imgInp" />
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <br>
                                        <strong class="image-msg text-success"></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row margin-top-20">
                            <div class="col-md-12">
                                <label class="text-blue">Biography</label>
                                <textarea class="form-control user-bio-info" id="user-bio-text" rows="6">{{$user_data->user_bio}}</textarea>
                            </div>
                        </div>
                        <div class="row margin-top-20">
                            <div class="col-md-7 text-right">
                                <span class="success-msg font-18"></span>
                            </div>
                            <div class="col-md-5">
                                <div class="text-right">
                                    <button type="button" class="btn btn-grey btn-sm-sw text-uppercase cancel-btn">Cancel</button>
                                    <button type="button" class="btn btn-green btn-sm-sw text-uppercase save-user-pref">Save Settings</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sidebar-right">
                @include('navs.sidebarRight')
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