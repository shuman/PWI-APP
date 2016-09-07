@extends('header')

@section('content')

<input type='hidden' name='filepath' value='{!! $path !!}' />

<div class='row'>
    <div class='col-lg-3 col-md-3 col-sm-3 col-xs-3 product-filters'>
        <div class='product-filter'>
            <div class='title'>Categories</div>
            <div class='filter-items'>
                <ul>
                    <li data-filter-type='category' data-cat-id='' data-amount='100'>All Categories</li>
                    @foreach( $categories as $category )
                       <li data-filter-type='category' data-cat-id='{!! $category["id"] !!}' data-amount='{!! $category["cnt"] !!}'>{!! $category["name"] !!} ( {!! $category["cnt"] !!} )</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class='product-filter'>
            <div class='title'>Price</div>
            <div class='filter-items'>
                <ul >
                    @foreach( $priceFilters as $priceFilter)
                        @if( (int)$priceFilter['count'] > 0 )
                        <li data-filter-type='price' data-price='{!! $priceFilter['value'] !!}' data-amount='{!! $priceFilter['count'] !!}'>{!! $priceFilter['text'] !!} ( {!! $priceFilter['count'] !!} )</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        @if( 1 == 0 )
        <div class='product-filter rating-filter'>
            <div class='title'>Rating</div>
            <div class='filter-items'>
                <ul class='no-list-style'>
                    @foreach( $ratingFilters as $ratingFilter)
                        @if( (int)$ratingFilter["count"] > 0 )
                        <li data-filter-type='rating' data-rating='{!! $ratingFilter["value"] !!}' data-amount='{!! $ratingFilter["count"] !!}'>
                            @for( $r = 1 ; $r <= 5 ; $r++ )
                                @if( $r <= $ratingFilter["value"] )
                                    <span class="star fill" >
                                        <i class="pwi-icon-star"></i>
                                    </span>
                                @else
                                    <span class="star" >
                                        <i class="pwi-icon-star"></i>
                                    </span>
                                @endif
                            @endfor
                            & Up ( {!! $ratingFilter["count"] !!} )
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        <hr />
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
                <a href='/organizations'>Organizations</a>
            </div>
        </div>
    </div>
    <div class='col-lg-9 col-md-9 col-sm-9 col-xs-9'>
        <div class='product-loading-overlay' >
            <div style='display:table-cell; vertical-align: middle; text-align: center;'>
                <h1> Filtering Products </h1>
                <img src='/images/loading1.gif' />
            </div>
        </div>
        <div class='row product-module-list no-box-outline'>
        @foreach( $products as $product )
            <div class='col-lg-6 col-md-6 margin-top-10'>
                <div class="product-module">
                    <div class='product-module-top row'>
                        <div class='product-module-left col-lg-4 col-md-4 col-sm-4'>
                            <a href='/product/{!! $product["product_alias"] !!}'>
                                <img src='{!! $product["image"] !!}' align='left' />
                            </a>
                            <br />
                            {!! Form::button('Buy', array('class'=>'donate-button buy-now', 'data-alias'=>$product["product_alias"], 'style'=>'height:auto;padding: 5px 0px; border: none;' ) ) !!}
                        </div>
                        <div class='product-module-right col-lg-8 col-md-8 col-sm-8 padding-left-0'>
                            <div class='product-module-name'><a href='/product/{!! $product["product_alias"] !!}'>{!! $product["name"] !!}</a></div>
                                <div class='product-module-org-name'>
                                    <span class='product-price'>{!! $product["price"] !!}</span> from {!! stripSlashes( $product["org_name"] ) !!}
                                </div>
                                <div class='rating'>
                                    @for($i = 1; $i < 6; $i++ )
                                        @if( $i <= $product["rating"] )
                                            <span class="star fill" >
                                                <i class="icon pwi-icon-star pwi-icon-2em"></i>
                                            </span>          
                                        @else
                                            <span class="star" >
                                                <i class="icon pwi-icon-star pwi-icon-2em"></i>
                                            </span>
                                        @endif
                                    @endfor
                                </div>
                                <div class='product-module-desc'>
                                    @if( sizeof( $product["descExp"] ) < 25 )
                                        {!! $product["sdesc"] !!}
                                    @else
                                        @for( $i = 0 ; $i < sizeof( $product["descExp"] ) ; $i++ )
                                            @if( $i < 25 || $i > 25 )
                                                {!! $product["descExp"][$i] !!}
                                            @else( $i == 25 )
                                                <a href='' class='readmore'>...See More</a>
                                                <span class='more'>{!! $product["descExp"][$i] !!}
                                            @endif
                                        @endfor
                                        </span><a href='#' class='readless'>Show Less</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    <div style='clear:both;'></div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>
@stop