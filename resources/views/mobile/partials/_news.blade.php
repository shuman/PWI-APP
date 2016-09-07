<div class='news-item'>
    <div class='news-image'>
        <img src="{!! $news[$i]["image"] !!}" class='img-responsive' align='left' />
    </div>
    <div class='news-content'>
        <div class='news-item-title'>{!! $news[$i]["title"] !!}</div>
        <div class='news-item-data'>
            <span class='news-item-source'>{!! $news[$i]["source"] !!}</span>&nbsp;
            <span class='news-item-postDate'>{!! $news[$i]["date"] !!}</span>
        </div>
        <div class='news-item-desc'>{!! $news[$i]["text"] !!} <a href='{!! $news[$i]["link"][0] !!}'>See more..</a></div>
    </div>
</div>