<div class='cc-info'>
    <p class='group-header'>
        Credit Card Information
    </p>
    <div class='form-group'>
        {!! Form::text('cc_number', '', array('placeholder'=>'Card number', 'class'=>'form-control', 'id'=>'cc_number', 'maxLength'=>'20')) !!}
        <div class='error cc_number-error bg-danger '></div>
    </div>
    <div class='form-group'>
        {!! Form::text('name_on_card', '', array('placeholder'=>'Name on card', 'class'=>'form-control', 'id'=>'name_on_card')) !!}
        <div class='error name_on_card-error bg-danger '></div>
    </div>
    <div class='form-group' style='height: 40px;'>
        <div class='half-form-group-left margin-bottom-15'>
            <div style='display: inline-block; width: 100%;'>
            {!! Form::select('exp_date_month', array( '01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12'), 0,  array('class'=>'form-control padding-left-5 pull-left', 'style'=>'height:45px; width: 49%;') ) !!}
            {!! Form::select('exp_date_year', $years, 0, array('class'=>'form-control padding-left-5 pull-right', 'style'=>'height:45px; width: 49%;') ) !!}
            </div>
            <div class='error exp_date_month-error bg-danger '></div>
        </div>
        <div class='half-form-group-right margin-bottom-15'>
            {!! Form::text('ccv', '', array('placeholder'=>'CCV', 'class'=>'form-control padding-left-5', 'id'=>'cvv', 'maxLength'=>'20')) !!}
            <div class='error ccv-error bg-danger '></div>
        </div>
    </div>
</div>