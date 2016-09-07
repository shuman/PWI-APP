<div class='form-group' style='height: 40px;'>
    <div class='half-form-group-left'>
        @if( ! is_null( $user ) )
        {!! Form::text('first_name', $user->user_firstname, array('placeholder'=>'First Name', 'class'=>'form-control padding-right-5')) !!}
        @else
        {!! Form::text('first_name', '', array('placeholder'=>'First Name', 'class'=>'form-control padding-right-5')) !!}
        @endif
        <div class='error first_name-error bg-danger '></div>
    </div>
    <div class='half-form-group-right'>
        @if( ! is_null( $user ) )
        {!! Form::text('last_name', $user->user_lastname, array('placeholder'=>'Last Name', 'class'=>'form-control padding-left-5 margin-bottom-15')) !!}
        @else
        {!! Form::text('last_name', '', array('placeholder'=>'Last Name', 'class'=>'form-control padding-left-5 margin-bottom-15')) !!}
        @endif
        <div class='error last_name-error bg-danger '></div>
    </div>
</div>
<div class='form-group'>
    @if( ! is_null( $user ) )
        @if( ! preg_match("/_twitter@pwi.com$/", $user->user_email ) )
           {!! Form::email('email', $user->user_email, array('placeholder'=>'Email', 'class'=>'form-control')) !!}
        @else
            {!! Form::email('email', '', array('placeholder'=>'Email', 'class'=>'form-control')) !!}
        @endif
    @else
    {!! Form::email('email', '', array('placeholder'=>'Email', 'class'=>'form-control')) !!}
    @endif
    <div class='error email-error bg-danger '></div>
</div>