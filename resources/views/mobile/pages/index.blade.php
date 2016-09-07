@extends('header')
@section('content')

<div data-role='page' id='main'>
	@include('mobile.headers.generic')

	<div data-role='main' class='ui-content' style='padding:0;' id="indexPage">
		<div class='indexWrapper' >
			<div class='home-page-background-image' style='background: url({!! $path !!}) no-repeat center center; background-size: cover;'></div>
			<div class='home-page-content'>
				<div class='home-page-title'>
					Project World Impact
				</div>
				<div class='home-page-sub-title'>Learn about countries, causes and nonprofits around the world</div>
                <div class='buttons'>
                    <a href='#continents' data-transition="slide" class='ui-btn'><div>Countries</div></a>
                    
                    <a href='#causes' data-transition="slide" class='ui-btn'><div>Causes</div></a>
                    
                    <a href='/organizations' class='ui-btn' rel="external"><div>Organizations</div></a>
                    <!--
                    <a href='/crowdfunding' class='ui-btn' rel="external"><div>Crowdfunding</div></a>

                    <a href='/products/' class='ui-btn' rel="external"><div>Products</div></a>
                    -->
                </div>
                <div class='whatisMobile'>
                    <a href='#whatis' data-transition="slide" class='ui-btn'><div>What is Project World Impact?</div></a>
                </div>
            </div>
		</div>
	</div>

	@include('mobile.footer')
</div>

<div data-role='page' id='whatis'>
    @include('mobile.headers.generic')

    <div data-role='main' class='ui-content' style='padding:0;' id="whatIsPage">
        <div class='whatIsContent'>
            <div class='title'>
                what is<br />
                project world impact?
            </div>
            <div class='content'>
                PWI is changing the way people interact with important causes all over the world by seeking to educate and connect them with ways they can better impact the world's must fundamental needs. Begin your journey here; search for your favorite cause or non-profit and learn how you can help create a better future, today. 
            </div>
        </div>
        <div class='panel whatis-cause-panel' >
            <div class='section-icon pwi-icon-cause-solid' ></div>
            <div class='title'>Causes</div>
            <div class='content'>Search for non-profits by the causes you're interested in.</div>
            <div class='margin-top-10 padding-top-10 padding-bottom-10'><button class='searchby cause'>Search by Cause</button></div>
        </div>
        <div class='panel whatis-country-panel' >
            <div class='section-icon pwi-icon-country-solid' ></div>
            <div class='title'>Countries</div>
            <div class='content'>Search for non-profits by country they work in.</div>
            <div class='margin-top-10 padding-top-10 padding-bottom-10'><button class='searchby country'>Search by Country</button></div>
        </div>
        <div class='panel whatis-org-panel' >
            <div class='section-icon pwi-icon-organization-solid' ></div>
            <div class='title'>Are you a non-profit?</div>
            <div class='content'>Find out how PWI can benefit your organization.</div>
            <div class='margin-top-10 padding-top-10 padding-bottom-10'><button class='searchby learnmore organization'>Learn More</button></div>
        </div>

        <div class='whatis-backHome'>
            <a href='#main'>back to homepage</a>
        </div>
    </div>

    @include('mobile.footer')
</div>
@stop