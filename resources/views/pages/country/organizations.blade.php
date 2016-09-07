@extends("header")

@section("content")
<script> setHeight( ); </script>
{!! Form::hidden('page', 'org') !!}

<div class='row'>
    <div class='col-lg-6 col-md-6'>
        <div class='list-header'>Organizations in <b><a href='/country/{!! $alias !!}'>{!! $country_name !!}</a></b></div>
        <div class='org-module-list no-outline'>
            <div class='orgs-module'>
            @foreach( $orgs as $org )
            <div class='org-module margin-top-10'>
                <div class='org-module-top'>
                    <div class='org-module-img-container margin-right-10 margin-bottom-5 padding-0 pull-left'>
                        <a href='/organization/{!! $org["org_alias"] !!}'>
                            <img src='{!! $org["logoImg"] !!}' align='left'/>
                        </a>
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
                        </div>
                    </div>
                    <div class='pull-left'>
                        <div class='org-module-name pull-left'>
                            {!! HTML::link( '/organization/' . $org["org_alias"], stripslashes( $org["org_name"] )) !!}
                        </div>

                        <div class='impacts-causes'>
                            <span class='title'>Locations</span><br />
                            <span class='list'>
                            @if( sizeof( $org["countries"] ) > 10 )
                                @for( $i = 0 ; $i < sizeof( $org["countries"] ) ; $i++ )
                                    @if( $i == 0 )
                                        {!! $org["countries"][$i]->country_name !!}
                                    @else
                                        @if( $i < 10 || $i > 10 )
                                            , {!! $org["countries"][$i]->country_name !!}
                                        @else( $i == 10 )
                                            <a href='' class='readmore'>...See More Countries</a>
                                            <span class='more'>, {!! $org["countries"][$i]->country_name !!}
                                        @endif
                                    @endif
                                @endfor
                                </span><a href='#' class='readless'>Show Less Countries</a>
                            @else
                                @for( $i = 0 ; $i < sizeof( $org["countries"] ) ; $i++ )
                                    @if( $i == 0 )
                                        {!! $org["countries"][$i]->country_name !!}   
                                    @else
                                        , {!! $org["countries"][$i]->country_name !!}
                                    @endif
                                @endfor
                            @endif
                            </span>
                        </div>

                        <div class='impacts-causes'>
                            <span class='title'>Causes</span><br />
                            <span class='list'>
                            @for( $i = 0 ; $i < sizeof( $org["causes"] ) ; $i++ )
                                @if( $i == 0 )
                                    {!! $org["causes"][$i]->cause_name !!}   
                                @else
                                    , {!! $org["causes"][$i]->cause_name !!}
                                @endif
                            @endfor
                            </span>
                        </div>
                    </div>
                </div>
                <div style='clear:both;'></div>
                <div class='org-module-bottom'>
                    <div class='org-module-desc'>{!! $org["org_desc"] !!}</div>
                </div>
            </div>
            @endforeach
            </div>
        </div>
    </div>
    <div class='col-lg-3 col-md-e'>
        <div class='browse-by'>
            <div class='list-header margin-bottom-15'>Also Browse By</div>
            <div class='margin-top-10 margin-bottom-10'>
                <a href='' class='browseCause'>Causes</a>
            </div>
            <div class='margin-top-10 margin-bottom-10'>
                <a href='' class='openCountryModal'>Countries</a>
            </div>
            <div class='margin-top-10 margin-bottom-10'>
                <a href='/crowdfunding' >Crowdfunding</a>
            </div>
            <div class='margin-top-10 margin-bottom-10'>
                <a href='/products'>Products</a>
            </div>
        </div>
        <hr />
    </div>
</div>
@stop