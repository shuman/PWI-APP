@extends('header')
@section('content')
    <div class='home-page-bg' style='background: url({!! $path !!}) no-repeat center center; background-size: cover;'>
        {!! Form::hidden('page', 'homepage') !!}
        <input type='hidden' name='page' value='homepage' />
        <div class='home-page-content-wrapper'>
            <div class='home-page-content'>
                <div class='home-page-title'>
                    project world impact 
                </div>
                <div class='home-page-sub-title'>Learn about countries, causes and nonprofits around the world</div>
                <div class='buttons margin-top-25'>
                    {!! Form::button("Countries", array('class' => 'home-page-button openCountryModal margin-right-5') ) !!}
                    
                    {!! Form::button("Causes", array('class' => 'home-page-button openCauseModal margin-left-5 margin-right-5') ) !!}
                    
                    {!! Form::button("Organizations", array(
                                    'class' => 'home-page-button margin-left-5 margin-right-5',
                                    'onclick' => "javascript: location.href = '/organizations';"
                                    )) !!}
{{--
                    {!! Form::button("Crowdfunding", array(
                                    'class' => 'home-page-button margin-left-5 margin-right-5',
                                    'onclick' => "javascript: location.href = '/crowdfunding';"
                                    )) !!}
                    
                    {!! Form::button("Products", array(
                                    'class' => 'home-page-button margin-left-5',
                                    'onclick' => "javascript: location.href = '/products';"
                                    )) !!}
--}}
                </div>
            </div>
			<div class='background-description hidden-xs'>
				<div class='container-fluid'>
				<span>{!! $desc !!}</span>
				</div>
			</div>
            
        </div>
        
    </div>
    
    <style>
        footer{
            position:absolute;
        }
    </style>
@stop