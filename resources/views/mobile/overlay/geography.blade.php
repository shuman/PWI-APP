<div data-role='page' id='geography'>
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
		<div class='stat-header'>Geography</div>
		<div class='country-geography stat-content'>
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
    </div>
</div>