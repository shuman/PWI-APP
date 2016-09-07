@extends('header')

@section('content')
<script> setHeight( ); </script>
<input type='hidden' name='filepath' value='{!! $path !!}' />
<div class='row product-module-list no-box-outline'>
    <div class='list-header'>Products in <b><a href='/country/{!! $alias !!}'>{!! $country_name !!}</a></b></div>
@foreach( $products as $product )
    <div class='col-lg-6 col-md-6 margin-top-10'>
        <div class="product-module">
            <div class='product-module-top'>
                <a href='/product/{!! $product["product_alias"] !!}'>
                    <img src='{!! $product["image"] !!}' align='left' />
                </a>
                <div class='product-module-name'><a href='/product/{!! $product["product_alias"] !!}'>{!! $product["name"] !!}</a></div>
                    <div class='product-module-org-name'>
                        <span class='product-price'>{!! $product["price"] !!}</span> from {!! stripSlashes( $product["org_name"] ) !!}
                    </div>
                    <div class='rating'>
                        @for($i = 1; $i < 6; $i++ )
                            @if( $i <= $product["rating"] )
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
                    <div class='product-module-desc'>
                        @if( sizeof( $product["descExp"] ) < 50 )
                            {!! $product["sdesc"] !!}
                        @else
                            @for( $i = 0 ; $i < sizeof( $product["descExp"] ) ; $i++ )
                                @if( $i < 50 || $i > 50 )
                                    {!! $product["descExp"][$i] !!}
                                @else( $i == 50 )
                                    <a href='' class='readmore'>...See More</a>
                                    <span class='more'>{!! $product["descExp"][$i] !!}
                                @endif
                            @endfor
                            </span><a href='#' class='readless'>Show Less</a>
                        @endif
                    </div>
                </div>
            <div style='clear:both;'></div>
        </div>
    </div>
@endforeach
</div>
@stop