@if( count( $orgs) > 0 )
@if( Route::is('searchResults') )
<div class='org-module-list padding-left-5 no-border'>
@else
<div class='org-module-list padding-left-5 margin-top-10 col-lg-12'>
@endif
	<div class='org-module-list-header'>
        @if( isset( $country->country_name ) )
            Organizations in {!! $country->country_name !!} 
        @else
            Organizations
        @endif
        
        @if( ! Route::is('searchResults') )
		<span class=' margin-left-5 margin-right-5' ></span>
		{!! HTML::link($orgViewAll, 'View all') !!}
		@endif
        <!--<a href='{!! $orgViewAll !!}'>View all</a>-->
    </div><!-- end .org-module-list-header -->
    <div class='orgs-module'>
    @foreach( $orgs as $org )
        <div class='org-module margin-top-15 margin-bottom-15'>
            <div class='org-module-top'>
                <div class='org-module-img-container margin-right-15 margin-bottom-5 padding-0 pull-left'>
                    <a href='/organization/{!! $org["alias"] !!}'><img src='{!! $org["logo"] !!}' align='left'/></a>
                    
                    <br />
                    <div class='rating below-image'>
                        @for($i = 1; $i < 6; $i++ )
                            @if( $i <= $org["rating"] )
                                <span class="star fill" >
                                    <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
                                </span>          
                            @else
                                <span class="star" >
                                    <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
                                </span>
                            @endif
                        @endfor
                    </div><!-- end .rating -->
                </div><!-- end .org-module-img-container -->
                <div class='pull-left'>
                    <div class='org-module-name pull-left'><a href='/organization/{!! $org["alias"] !!}'>{!! stripslashes( $org["name"] ) !!}</a></div>
                    
                    <!-- Organization Impact Countries-->
                    <div class='impacts-causes'>
                        <span class='title'>Locations</span><br />
                        <?php
                            $locations = explode(",", $org["impactCountries"] );
                        ?>
                        <span class='list'>
                        @if( sizeof( $locations) > 10 )
                            @for( $i = 0 ; $i < sizeof( $locations ) ; $i++ )
                                @if( $i == 0 )
                                    {!! $locations[$i] !!}
                                @else
                                    @if( $i < 10 || $i > 10 )
                                        , {!! $locations[$i] !!}
                                    @else( $i == 10 )
                                        <a href='' class='readmore'>...See More Countries</a>
                                        <span class='more'>, {!! $locations[$i] !!}
                                    @endif
                                @endif
                            @endfor
                            </span><a href='#' class='readless'>Show Less Countries</a>
                        @else
                        {!! $org["impactCountries"] !!}
                        @endif
                        </span>
                    </div><!-- end .impact-causes -->
                    
                    <!-- Organization Impact Causes -->
                    <div class='impacts-causes'>
                        <span class='title'>Causes</span><br />
                        <span class='list'>{!! $org["causes"] !!}</span>
                    </div><!-- end .impacts-causes -->
                </div><!-- end .pull-left -->
                
            </div><!-- end .org-module-top -->
            <div style='clear:both;'></div>
            <div class='org-module-bottom'>
                <!-- Organization description -->
                <div class='org-module-desc'>

	            	@if( sizeof( $org["descExp"] ) < 50 )
                        {!! $org["desc"] !!}
                    @else
                        @for( $i = 0 ; $i < sizeof( $org["descExp"] ) ; $i++ )
                            @if( $i < 50 || $i > 50 )
                                {!! $org["descExp"][$i] !!}
                            @else( $i == 50 )
                                <a href='' class='readmore'>...See More</a>
                                <span class='more'>{!! $org["descExp"][$i] !!}
                            @endif
                        @endfor
                        </span><a href='#' class='readless'>Show Less</a>
                    @endif    
				</div>
            </div><!-- end .org-module-bottom -->
        </div><!-- end .org-module -->
    @endforeach
    </div><!-- end .orgs-module -->
	@if( ! Route::is('searchResults') )
		{!! HTML::link($orgViewAll, 'View all') !!}
	@endif  
</div><!-- end .org-module-list -->
@endif