@if(!empty($news))
<?php $i = 3 ?>
@foreach($news as $news_value)
@if($i<=5 && $i>2)
<div class="news-feed">
    <img src="{{$news_value['image']}}" alt="{{$news_value['image']}}" class="propic-sm">
    <h2 class="news-title">{{$news_value['title']}}</h2>
    <div class="news-meta"><span class="source">{{$news_value['source']}}</span> <span class="timeago"> {{$news_value['date']}}</span></div>
    <div class="news-desc"><p>{{$news_value['text']}}</p><a class="read-more" href="{{$news_value['link']}}">Read More</a></div>
</div>
@endif
<?php $i++; ?>
@endforeach
@endif
