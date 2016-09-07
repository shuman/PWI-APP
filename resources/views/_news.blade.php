@if( ! empty( $news ) )
<div class='news'>
    @if( isset( $cause ) )
    <p>{!! $cause->cause_name !!} News</p>
    @elseif( isset( $country ) )
    <p>{!! $country->country_name !!} News</p>
    @endif
    @for( $i = 0 ; $i < sizeof( $news ) ; $i++ )
    
    <div class='news-item row'>
        <div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'>
            <img src="{!! $news[$i]['image'] !!}" class='img-responsive' align='left' />
        </div>
        <div class='col-lg-10 col-md-10 col-sm-10 col-xs-10 padding-0'>
            <div class='news-item-title'>{!! $news[$i]["title"] !!}</div>
            <div class='news-item-data'>
                <span class='news-item-source'>{!! $news[$i]["source"] !!}</span>&nbsp;
                <span class='news-item-postDate'>{!! $news[$i]["date"] !!}</span>
            </div>
            <div class='news-item-desc padding-right-10'>{!! $news[$i]["text"] !!} <a href='{!! $news[$i]['link'][0] !!}'>See more..</a></div>
        </div>
    </div>
    

    @if( $i == 2 )
    <a href='' class='readmore'>See More News</a>
    <div class='more'>
    @endif

    @endfor
    @if( $i > 3 )
    </div><a href='#' class='readless'>Show Less News</a>
    @endif
    
</div>
@endif