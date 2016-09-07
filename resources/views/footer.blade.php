<footer class='margin-top-20 normal-footer'>
    <div class='container-fluid'>
        <div class='row'>
            <div class='col-lg-3 col-md-3 col-sm-12 col-xs-12 left-side pwi-footer-action'>
                <span class='project'>project</span>
                <span class='world'>world</span>
                <span class='impact'>impact</span>
            </div>
            <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 footer-links' >
                <div style='text-align: center;'>
                        {!! HTML::link('/terms-of-use', 'Terms') !!}
                        <!--<a href='#terms'>Terms</a>-->
                        {!! HTML::link('/privacy-policy', 'Privacy') !!}
                        <!--<a href='#privacy'>Privacy</a>-->
                        {!! HTML::link('/faq', 'FAQ') !!}
                        <!--<a href='#faq'>FAQ</a>-->
                        {!! HTML::link('http://marketing.projectworldimpact.com', 'Marketing') !!}
                        <!--<a href='#marketing'>Marketing</a>-->
                        {!! HTML::link('/press-release', 'Press') !!}
                        <!--<a href='#press'>Press</a>-->
                        {!! HTML::link('http://imstuck.projectworldimpact.com', 'Support') !!}
                        <!--<a href='#support'>Support Center</a>-->
                        {!! HTML::link('/suggest', 'Suggest a Nonprofit', array("class" => "openSuggestNP")) !!}
                        <!--<a href='#suggust'>Suggest a Nonprofit</a>-->
                </div>
            </div>
            <div class='col-lg-3 col-md-3 col-sm-12 col-xs-12 social-links'>
                <a href='https://www.facebook.com/ProjectWorldImpact/' targe='_blank'><i class='pwi-social-facebook'></i></a>
                &nbsp;
                <a href='https://www.twitter.com/prjworldimpact'><i class='pwi-social-twitter'></i></a>
                &nbsp;
                <a href='https://www.instagram.com/projectworldimpact'><i class='pwi-social-instagram'></i></a>
                &nbsp;
                <a href='https://www.pinterest.com/prjworldimpact'><i class='pwi-social-pinterest'></i></a>
            </div>
        </div>
    </div>
</footer>

<footer class='mobile-footer'>
    <div class='container-fluid'>
        <div class='row'>
            <div class='col-sm-12 col-xs-12 margin-top-2 padding-0' >
                <div class='whatis whatis_mobile'>
                What is Project World Impact?
                </div>
            </div>
	        <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 footer-links' >
                <div style='text-align: center;'>
                        {!! HTML::link('/terms-of-use', 'Terms') !!}
                        <!--<a href='#terms'>Terms</a>-->
                        {!! HTML::link('/privacy-policy', 'Privacy') !!}
                        <!--<a href='#privacy'>Privacy</a>-->
                        {!! HTML::link('/faq', 'FAQ') !!}
                        <!--<a href='#faq'>FAQ</a>-->
                        {!! HTML::link('http://marketing.projectworldimpact.com', 'Marketing') !!}
                        <!--<a href='#marketing'>Marketing</a>-->
                        {!! HTML::link('/press-release', 'Press') !!}
                        <!--<a href='#press'>Press</a>-->
                        {!! HTML::link('http://imstuck.projectworldimpact.com', 'Support') !!}
                        <!--<a href='#support'>Support Center</a>-->
                        {!! HTML::link('/suggest', 'Suggest a Nonprofit', array("class" => "openSuggestNP")) !!}
                        <!--<a href='#suggust'>Suggest a Nonprofit</a>-->
                        @if( Route::is('home') )
                        <div style='width: 100%; color: #000; margin-top: 3px; margin-bottom: 3px;'>
                            <span>{!! $desc !!}</span>
                        </div>
                        @endif
                </div>
            </div>
            <div class='col-lg-3 col-md-3 col-sm-12 col-xs-12 social-links'>
                <a href='#facebook'><i class='pwi-social-facebook'></i></a>
                &nbsp;
                <a href='#twitter'><i class='pwi-social-twitter'></i></a>
                &nbsp;
                <a href='#instagram'><i class='pwi-social-instagram'></i></a>
                &nbsp;
                <a href='#pinterest'><i class='pwi-social-pinterest'></i></a>
            </div>
            <div class='col-lg-3 col-md-3 col-sm-12 col-xs-12 pwi-text padding-top-2 paddding-bottom-2'>
                <span class='project'>project</span>
                <span class='world'>world</span>
                <span class='impact'>impact</span>
            </div>
        </div>
    </div>
</footer>
