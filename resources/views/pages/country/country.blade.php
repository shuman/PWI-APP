@extends('header')

@section('content')

{!! Form::hidden('paypalUn', $paypal_un) !!}
    
<div class='modal white' id='countryCheckout'>
    <div class='checkout overlay-content'>
        <div class='exit-country-checkout exit pull-right'>
            <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
        </div>
        <div class='title'>{!! $country->country_name !!}</div>
        @include("partials._donation")
    </div>
</div>

<input type='hidden' name='latitude' value='{!! $country->latitude !!}' />
<input type='hidden' name='longitude' value='{!! $country->longitude !!}' />
<!-- 
* Country Header Content: includes: coverphoto, flag, name, follow and donate buttons.
-->
<div class='country-header'>
	@if( ! is_null( $coverphoto ) )
    <img class='cover_photo' src='{!! $coverphoto !!}' />
    @endif
    <div class='country-header-content'>
        <div class='row'>
            <div class='col-lg-1 col-md-1 col-sm-2 col-xs-4'>
                <div class='img-thumbnail flag-wrapper' style='height: 45px; width: 68px; margin-top: 8px; margin-left: 5px;'>
                    <span class='flag-icon flag flag-background flag-icon-{!! strtolower( $country->country_iso_code ) !!}'></span>    
                </div>    
            </div>
            <div class='col-lg-4 col-md-4 col-sm-4 col-xs-8 padding-left-5'>
                <p class='country_name margin-top-12'>{!! $country->country_name !!}</p>
            </div>
            <div class='col-lg-7 col-md-7 col-sm-6 col-xs-12 text-right padding-right-25 hidden-sm hidden-xs'>
	            <button type='button' class='btn share margin-top-10 margin-right-10' >share</button>
                <button type='button' class='btn btn-follow margin-top-10 margin-right-10 follow' aria-label='Favorite' data-type="country" data-id='{!! $country->country_id !!}'>
                	@if( $following )
                	unfollow
                	@else
                    follow
                    @endif
                </button>
                <button type='button' class='btn btn-donate margin-top-10 padding-left-50 padding-right-50 donate-button' aria-label='Favorite' style='max-width: 225px;' data-alias='{!! $country->country_alias !!}'>
                    donate now
                </button>
            </div>
        </div>
    </div><!-- end .country-header-content -->
</div><!-- end .country-header -->

<div class='top-button hidden-lg hidden-md visible-sm visible-xs'>
    <div class='row'>
        <div class='col-sm-4 col-xs-4 padding-right-0'>
            <div class='donate-button' data-id="{!! $country->country_id !!}">
                donate 
                @if( ! Agent::isMobile( ) ) 
                    now 
                @endif
            </div>
        </div>

        <div class='col-sm-4 col-xs-4 padding-left-0 padding-right-0'>
            
            <button class='follow' data-id='{!! $country->country_id !!}' data-type='country' style='width: 100%; max-width: 100%; font-size: 18px;'>
            @if( $following == 1 )
            unfollow
            @else
            follow
            @endif
            </button>
        </div>
        <div class='col-sm-4 col-xs-4 padding-left-0 '>
            <button class='share pull-right' style='width: 100%; max-width: 100%; font-size: 18px;'>share</button>
        </div>
    </div>
</div>

<div class='country-content margin-top-5'>
    <div class='row'>
        @if( Agent::isMobile( ) )
        <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>
        @else
        <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-right-5'>
        @endif
            <!-- include org module -->
            @include("modules.organizations")
            <!-- end org module -->
            @if( ! empty( $twitter ) || ! empty( $instagram) )
            <div class='col-lg-12 col-md-6 col-sm-6 margin-top-10 padding-0' >
                @if( ! empty( $twitter ) && ! empty( $instagram ) )
                    <div class='col-lg-6 col-md-6 col-sm-6 padding-right-5 padding-left-0'>
                        <div class='feed twitter-feed p'>
                            <p>Twitter <span class='hashtag'>{!! $hashtags !!}</span></p>
                            <div style='overflow: auto;'>
                                @foreach( $twitter as $tweet )
                                    <div class='post-item'>
                                        {!! $tweet["tweet"] !!}
                                        <div class='posted'>Posted: {!! $tweet['date'] !!}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class='col-lg-6 col-md-6 col-sm-6 padding-left-5 padding-right-0'>
                        <div class='feed instagram-feed'>
                            <p>Instagram <span class='hashtag'>{!! $hashtags !!}</span></p>
                            <div class='row'>
                            @foreach( $instagram as $post )
                                <div class='col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-0'>
                                    <img src='{!! $post["image"] !!}' class='img-responsive' />
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                @elseif( ! empty( $twitter ) && empty( $instagram ) )
                    <div class='col-lg-12 col-md-12 col-sm-12 padding-0'>
                        <div class='feed twitter-feed'>
                            <p>Twitter <span class='hashtag'>{!! $hashtags !!}</span></p>
                            <div style='overflow: auto;'>
                                @for( $i = 0 ; $i < sizeof( $twitter ) ; $i++ )
                                    <div class='post-item'>
                                        {!! $twitter[$i]["tweet"] !!}
                                        <div class='posted'>Posted: {!! $twitter[$i]['date'] !!}</div>
                                    </div>

                                    @if( $i == 3 )
                                    <a href='' class='readmore'>See More Tweets</a>
                                    <div class='more'>
                                    @endif
                                @endfor

                                @if( $i > 3 )
                                </div><a href='#' class='readless'>Show Less Tweets</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            @if( sizeof( $causes ) > 0 )
	        <div class='col-lg-12 causes-we-support margin-top-10' >
                <div class='row'>
                    <div class='col-lg-5 col-md-5 col-sm-5 col-xs-5 cause-list padding-right-0'>
                        <p>Causes We Support</p>
                        @for( $i = 0 ; $i < sizeof( $causes ) ; $i++ )
                            @if( $i == 0 )
                            <div class='countryCauseName active' data-cause='{!! $causes[$i]["icon"] !!}'>
                            @else
                            <div class='countryCauseName' data-cause='{!! $causes[$i]["icon"] !!}'>
                            @endif
                                <div class='countryCauseIcon'>
                                    <i class='pwi-cause-{!! $causes[$i]["icon"] !!}-stroke pwi-icon-2em pull-left'></i>    
                                    <div class='countryCauseNameText pull-left'>{!! $causes[$i]["name"] !!}</div>
                                </div>    
                            </div>
                        @endfor
                    </div>
                    <div class='col-lg-7 col-md-7 col-sm-7 col-xs-7'>
                        @for( $j = 0 ; $j < sizeof( $causes) ; $j++ )
                            @if( $j == 0 )
                            <div class='country-cause-description {!! $causes[$j]["icon"] !!}-description'>                 
                            @else
                            <div class='country-cause-description {!! $causes[$j]["icon"] !!}-description hidden'>                       
                            @endif
                                <div class='country-cause-description-header'>{!! $causes[$j]["name"] !!}</div>
                                @if( sizeof( $causes[$j]["descExp"] ) < 125 )
									{!! $causes[$j]["desc"] !!}
								@else
									@for( $i = 0 ; $i < sizeof( $causes[$j]["descExp"] ) ; $i++ )
			                            @if( $i < 125 || $i > 125 )
			                                {!! $causes[$j]["descExp"][$i] !!}
			                            @else( $i == 125 )
			                                <a href='' class='readmore'>...See More</a>
			                                <span class='more'>{!! $causes[$j]["descExp"][$i] !!}
			                            @endif
			                        @endfor
	                           </span><a href='#' class='readless'>Show Less</a>
								@endif
                                <br />
                                <br />
                                <div style='word-wrap: break-word;'>
                                @if( ! empty( $causes[$j]["reference"] ) )
                                    <a href='' class='readmore'>Show References</a>
                                    <div class='more'>
                                    {!! str_replace("\n", "<br />", $causes[$j]["reference"] ) !!}
                                    </div><a href='#' class='readless'>Hide References</a>
                                @endif
                                </div>
                            </div>
                        @endfor                          
                    </div>
                </div>
            </div><!-- end of .causes-we-support -->
            @endif
            <!-- include project module -->
            @include("modules.project")
            <!-- end project module -->

            <!-- include product module -->
            @include("modules.product")
            <!-- end product module -->
        </div>
        <div class='col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-left-5 margin-top-10'>
           <!-- include news module -->
            @include("_news")
            <!-- end news module -->

            <div class='col-lg-12 col-md-12 col-sm-12 padding-0'>
                <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-0'>
                    <div class='country-map'>
                        <p >Map</p>
                        <div id="map" class='margin-0'></div>
                    </div>
                    @if( isset( $geography) )
                    <div class='country-geography'>
                        <p>Geography</p>

                        <div class='geography-data'>
                        @foreach( $geography as $key => $value )
                            @if( $key == "location" )
                                <div class='geography-item'>
	                                @if( isset( $value["text"]) )
	                                	<?php $textSize = explode(" ", $value["text"]); ?>
	                                	@if( sizeof( $textSize ) < 100 )
	                                    	{!! $value["text"] !!}
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
	                                    @endif
                                    @endif
                                </div>
                            @elseif( $key == "area")
                                <div class='geography-item'>
                                    <b>Area</b>
                                    <br />
                                    <br />
                                    <table>
                                    @if( isset( $value["total"] ) )
                                        <tr>
                                            <td>Total</td>     
                                            <td style='padding-left: 5px;'>{!! $value["total"] !!}</td>
                                        </tr>
                                    @endif
                                    @if( isset( $value["land"] ) )
                                        <tr>
                                            <td>Land</td>
                                            <td style='padding-left: 5px;'>{!! $value["land"] !!}</td>
                                        </tr>      
                                    @endif
                                    @if( isset( $value["water"] ) )
                                        <tr>
                                            <td>Water</td>
                                            <td style='padding-left: 5px;'>{!! $value["water"] !!}</td>
                                        </tr>
                                    @endif
                                    </table>
                                    <br />
                                    @if( isset( $value["note"] ) )
                                        ***NOTE*** {!! $value["note"] !!}
                                    @endif
                                </div>
                            @elseif( $key == "climate" )
                            	@if( isset( $value["text"] ) )
                                <div class='geography-item'>
                                    <b>Climate</b>
                                    <br />
                                    <br />
                                    <?php $textSize = explode(" ", $value["text"]); ?>
                                	@if( sizeof( $textSize ) < 100 )
                                    	{!! $value["text"] !!}
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
								@endif
                            @elseif( $key == "natural_resources" )
                            	@if( isset( $value["text"] ) )
                                <div class='geography-item'>
                                    <b>Natural Resources</b>
                                    <br />
                                    <br />
                                    <?php $textSize = explode(" ", $value["text"]); ?>
                                	@if( sizeof( $textSize ) < 100 )
                                    	{!! $value["text"] !!}
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

                                    @if( isset( $value["note"] ) )
                                        <br />
                                        <br />
                                        ***NOTE*** {!! $value["note"] !!}
                                    @endif
                                </div>
                                @endif
                            @elseif( $key == "natural_hazards")
                                <div class='geography-item'>
                                    <b>Natural Hazards</b>
                                    <br />
                                    <br />
                                    @foreach( $value as $k => $v )
                                        @if( $k == "text" )
                                            {!! $v !!}
                                        @else
                                            <br />
                                            <br />
                                            <b>{!! $k !!}</b>: {!! $v !!}
                                            <?php $textSize = explode(" ", $v); ?>
		                                	@if( sizeof( $textSize ) < 100 )
		                                    	{!! $v !!}
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
										@endif
                                    @endforeach
                                </div>
                            @elseif( $key == "environment_current_issues" )
                                <div class='geography-item'>
                                    <b>Current Environmental Issues</b>
                                    <br />
                                    <br />
                                    <?php $textSize = explode(" ", $value["text"]); ?>
                                	@if( sizeof( $textSize ) < 100 )
                                    	{!! $value["text"] !!}
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
                            @endif
                        @endforeach
                        </div>
                    </div>
                </div>
                @endif
                <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-0'>
                    @if( sizeof( $demographics ) > 0 )
                    <div class='country-demographics'>
                        <p>Demographics</p>

                        <div class='demographics-data padding-left-5'>
                        @foreach( $demographics as $demographic )

                            @if( $demographic["type"] == "text" )
                                <b>{!! $demographic["name"] !!}</b>
                                <div class='data-text'>
                                {!! $demographic["data"] !!}
                                </div>
                            @else
                                <div class='charts'>
                                    <b>{!! $demographic["name"] !!}</b>
                                    @foreach( $demographic["data"] as $data )
                                    <input type='hidden' name='item_name[]' value='{!! $data["name"] !!}' />
                                    <input type='hidden' name='item_percentage[]' value='{!! $data["percentage"] !!}'/>    

                                    @if( ! empty( $data["date"] ) )
                                        <input type='hidden' name='item_date' value='{!! $data["date"] !!}' />
                                    @endif

                                    @endforeach
                                    <div class='chart-legend'></div>
                                     <canvas width="95%" height="95%"/>
                                </div>
							@endif
						@endforeach
                        </div>
                    </div>
                    @endif
                    @if( isset( $finances) )
                    <div class='country-finances'>
                        <p>Finances</p>
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
        </div>
    </div>
</div>
@stop


                
                