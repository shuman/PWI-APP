@if(count($causes_news)>0)
<?php $cn = 0; ?>
@foreach($causes_news as $causes_news_value)
@if($cn<3)
<div class="news-feed">
    <img src="{{$causes_news_value['image']}}" alt="{{$causes_news_value['image']}}" class="propic-sm">
    <h2 class="news-title">{{$causes_news_value['title']}}</h2>
    <div class="news-meta"><span class="source">{{$causes_news_value['source']}}</span> <span class="timeago"> {{$causes_news_value['date']}}</span></div>
    <div class="news-desc truncate"><p>{{$causes_news_value['text']}}</p><a class="read-more" href="{{$causes_news_value['link']}}">Read More</a></div>
</div>
@endif

<?php $cn++; ?>
@endforeach
@endif