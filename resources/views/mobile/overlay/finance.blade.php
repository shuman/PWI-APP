<div data-role='page' id='finance'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-nodisc-icon ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>
			<div class='img-thumbnail flag-wrapper' style='height: 27px; width: 40px; margin-top: 8px; margin-left: 5px;'>
                <span class='flag-icon flag flag-background flag-icon-{!! strtolower( $country->country_iso_code ) !!}'></span>    
            </div> 
            <span>{!! stripslashes( $country->country_name ) !!}</span>
        </h1>
	</div><!-- /header -->

	<div data-role='main'>
		<div class='stat-header'>Finance</div>
		@if( isset( $finances) )
        <div class='country-finances stat-content'>
            <div class='finance-data'>
            @foreach( $finances as $f_key => $f_value )
                @if( $f_key == "economy_overview")
                    <div class='finance-item'>
                        <?php $textSize = explode(" ", $f_value["text"]); ?>
                    	@if( sizeof( $textSize ) < 100 )
                        	{!! $f_value["text"] !!}
                        @else
                        	@for( $i = 0 ; $i < sizeof( $textSize ) ; $i++ )
	                            @if( $i < 100 || $i > 100 )
	                                {!! $textSize[$i] !!}
	                            @else( $i == 100 )
	                                <a href='' class='readmore'>...See More</a>
	                                <span class='more'>{!! $textSize[$i] !!}
	                            @endif
	                        @endfor
	                        </span><a href='#' class='readless'>Show Less</a>
                        @endif;

                    </div>
                @elseif( $f_key == "gdp_purchasing_power_parity" )
                    <div class='finance-item'>
                        <b>GDP Purchasing Power Parity</b>
                        <br />
                        <br />
                        {!! $f_value["text"] !!}
                    </div>
                @elseif( $f_key == "gross_national_saving")
                    <div class='finance-item'>
                        <b>Gross National Saving</b>
                        <br />
                        <br />
                        {!! $f_value["text"] !!}
                    </div>
                @elseif( $f_key == "agriculture_products")
                    <div class='finance-item'>
                        <b>Agriculture Products</b>
                        <br />
                        <br />
                        {!! $f_value["text"] !!}
                    </div>
                @elseif( $f_key == "industries" )
                    <div class='finance-item'>
                        <b>Industries</b>
                        <br />
                        <br />
                        {!! $f_value["text"] !!}
                    </div>
                @elseif( $f_key == "unemployement_rate" )
                    <div class='finance-item'>
                        <b>Unemployement Rate</b>
                        <br />
                        <br />
                        {!! $f_value["text"] !!}
                    </div>
                @endif
            @endforeach
            </div>
        </div>
        @endif
	</div>
</div>