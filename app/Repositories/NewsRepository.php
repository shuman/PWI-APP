<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Follow;
use App\Files;
use App\User;
use Config;
use DB;
use Cache;

class NewsRepository {

    private $url = "https://news.google.com/news?q=##SEARCH##&output=rss";

    //private $url = "http://digg.com/search/?q=##SEARCH##&format=rss";

    public function getNews($keyword) {
        
        
        if (Cache::has($keyword)){
            $newsJSON = Cache::get($keyword);
            return json_decode($newsJSON, TRUE);
        }

        $news = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, str_replace("##SEARCH##", urlencode($keyword), $this->url));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $feed = simplexml_load_string(curl_exec($ch));
        if ($feed):
            foreach ($feed->channel->item as $entry) {

                $image = "";
                $text = "";
                $source = "";

                libxml_use_internal_errors(true);
                $doc = new \DOMDocument;
                $doc->loadHTML($entry->description);

                $cells = $doc->getElementsByTagName("td");
                $cellCnt = 0;
                foreach ($cells as $cell) {

                    if ($cellCnt == 0) {
                        $images = $cell->getElementsByTagName("img");

                        if (!is_null($images->item(0))) {
                            if (empty($image) && $images->item(0)->hasAttribute("src")) {
                                $image = $images->item(0)->getAttribute("src");
                            }
                        }
                    } else {
                        $fontTags = $cell->getElementsByTagName("font");
                        $tagCnt = 0;
                        foreach ($fontTags as $tag) {
                            if ($tag->hasAttribute("size")) {
                                if ($tagCnt == 0) {
                                    $source = $tag->textContent;
                                }

                                if ($tagCnt == 1) {
                                    $text = $tag->textContent;
                                }
                                $tagCnt++;
                            }
                        }
                    }
                    $cellCnt++;
                }

                $title = explode("-", $entry->title);

                if (empty($title)) {
                    $title = $entry->title;
                } else {
                    $title = $title[0];
                }

                $news[] = array(
                    "image" => $image,
                    "source" => $source,
                    "text" => $text,
                    "link" => $entry->link,
                    "title" => $title,
                    "date" => Carbon::createFromTimestamp(strtotime($entry->pubDate->__toString()))->diffForHumans()
                );
            }
        endif;
        
        $newsJSON = json_encode($news);

        $expiresAt = Carbon::now()->addMinutes(30);
        Cache::put($keyword, $newsJSON, $expiresAt);
        
        return json_decode($newsJSON, TRUE);
    }

}
