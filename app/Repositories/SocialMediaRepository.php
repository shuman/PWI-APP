<?php
namespace App\Repositories;

use Facebook\FacebookResponse;
use Facebook\FacebookRequest;
use Illuminate\Http\Request;
use Facebook\FacebookApp;
use Facebook\Facebook;
use Carbon\Carbon;
use App\Follow;
use App\Files;
use App\User;
use Config;
use DB;

class SocialMediaRepository{
	
	protected $feedCount = 3;
	protected $igUrl = "https://api.instagram.com/v1/tags/##TAG##/media/recent?client_id=##CLIENTID##&count=##COUNT##";
	
	public function getFeeds( $socialMedia ){
		
		$feeds = array( );
		
		foreach( $socialMedia as $item ){
            
			if( $item->social_media_name == "Twitter" ){
				
				$tmp = $this->getTwitterFeed( $item->org_sm_pageid );
                
                if( sizeof( $tmp ) > 0 ){
	                $feeds["twitter"] = $tmp;
                }
            }
            
            if( $item->social_media_name == "Facebook" ){
	            
	            $tmp = $this->getFacebookFeed( $item->org_sm_pageid );
	            
	            if( sizeof( $tmp ) > 0 ){
		            $feeds["facebook"] = $tmp;
	            }
            }
        }
        
        return $feeds;
	}
	
	private function getFacebookFeed( $pageid ){
		
		$facebook = array( );
		
		$fbApp = new FacebookApp(env("FACEBOOK_APP_ID"), env("FACEBOOK_APP_SECRET") );
        $fb = new Facebook([
           'app_id' =>  env("FACEBOOK_APP_ID"),
           'app_secret' => env("FACEBOOK_APP_SECRET"),
           'default_graph_version' => 'v2.5',
           'default_access_token' => $fbApp->getAccessToken( )
        ]);
        
        try{
            $response = $fb->get('/' . $pageid . '/feed');
            
            $user = $fb->get('/' . $pageid);
            
            $userData = $user->getGraphObject( )->asArray( );
            
            $facebook["name"] = $userData["name"];
            $facebook["id"] = $userData["id"];
            
            $graphEdge = $response->getGraphEdge( );
            
            $count = 0;
            
            foreach( $graphEdge as $graphNode ){
	            
	            if( $count < $this->feedCount ){
	            
		            $item = $graphNode->asArray( );
		            
		            $post = "";
		            
		            if( isset( $item["message"] ) ){
			            $post = $item["message"];
		            }else if( isset( $item["story"] ) ){
			            $post = $item["story"];
		            }
	
					$facebook["items"][] = array( "message" => $post, "posted" => Carbon::createFromTimestamp( $item["created_time"]->getTimeStamp( ) )->diffForHumans( ), "id" => $item["id"] );
				}
				
				$count++;
            }
        }catch( \Exception $e){
           /* echo 'fb sdk returned error: ' . $e->getMessage( );
            die; */ 
        }
        
        return $facebook;
	}
	
	private function getTwitterFeed( $pageid ){
		
		$twitter = array( );
		
		if( ! empty( $pageid ) ){
		
			try{
	        	$twitterFeed = \Twitter::getUserTimeLine(['screen_name' => $pageid, 'count' => $this->feedCount, 'format' => 'json']);
	        	$tweets = json_decode($twitterFeed, TRUE);
	        	
				$twitter["profile_image"] = $tweets[0]["user"]["profile_image_url"];
	        	
	        	$twitter["screen_name"] = $tweets[0]["user"]["screen_name"];
	        	
				foreach( $tweets as $tweet ){
					
					$tweetData = ["tweet" => $this->parseTweet( $tweet ), "date" => Carbon::createFromTimestamp( strtotime( $tweet["created_at"]) )->diffForHumans( )];
	                
					$twitter["tweets"][] = $tweetData;
				}
	        }catch ( \Exception $e){
	             
	        }
	    }
        
        return $twitter;
	}

	public function getTwitterHashtags( $hashtags, $groupByHashTag = TRUE ){

		$items = explode(" ", $hashtags );

		$feeds  = array( );

		if( sizeof( $items ) == 0 ){
			$items = array( $hashtags );
		}

		foreach( $items as $item ){

			if( ! empty( $item ) ){ 

				$tweets = \Twitter::getSearch([
					"q" => $item,
					"count" => "5",
					"result_type" => "recent"
				]);

				foreach( $tweets->statuses as $tweet ){

					if( is_object( $tweet ) ){
						$tweet = (array)$tweet;
					}

					$tweetData = ["tweet" => $this->parseTweet( $tweet ), "date" => Carbon::createFromTimestamp( strtotime( $tweet["created_at"]) )->diffForHumans( )];

					if( $groupByHashTag ){
						$feeds[$item][] = $tweetData;	
					}else{
						$feeds[] = $tweetData;
					}
				}
			}
		}
		return $feeds;
	}

	public function getInstagramHashtags( $hashtags, $groupByHashTag = TRUE ){

		$items = explode(" ", $hashtags);
		$feeds = array( );
		$badRequest = FALSE;

		foreach( $items as $item ){

			$ch = curl_init( );

			$targets = array("##TAG##", "##CLIENTID##", "##COUNT##");
			$values  = array( str_replace("#","",$item), env("INSTAGRAM_CLIENT_ID"), 9);

			$url = str_replace( $targets, $values, $this->igUrl );

			curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_SSL_VERIFYHOST => 2
			));

			
			$ig_feed = json_decode( curl_exec( $ch ), "A_ARRAY");

			if( (int)$ig_feed["meta"]["code"] == 400 ){
				//$accessToken = $this->regenerateAccessToken( );
				return array( );
			}else{
				if( isset( $ig_feed["data"] ) ){
					foreach( $ig_feed["data"] as $post ){
						
						$ig_data = ["image"=>$post["images"]["standard_resolution"]["url"], "link" => $post["link"]];

						if( $groupByHashTag ){
							$feeds[$item][] = $ig_data;	
						}else{
							$feeds[] = $ig_data;
						}
					}
				}
			}
		}

		return $feeds;
	}

	private function parseTweet( $tweet ){

		if( is_object( $tweet["entities"] ) ){
			$tweet["entities"] = (array)$tweet["entities"];
		}

		$tweet_text  = $tweet["text"];
	                
		if( sizeof( $tweet["entities"]["hashtags"] ) > 0 ){
           foreach( $tweet["entities"]["hashtags"] as $hashtag ){

           		if( is_object( $hashtag) ){
           			$hashtag = (array)$hashtag;
           		}

           		$tweet_text = str_replace( "#" . $hashtag["text"], "<a href='https://www.twitter.com/hashtag/" . $hashtag["text"] . "' target='_blank'>#" . $hashtag["text"] . "</a>", $tweet_text);
           }
        }
        
        if( sizeof( $tweet["entities"]["symbols"] ) > 0 ){
                
        }
        
        if( sizeof( $tweet["entities"]["user_mentions"] ) > 0 ){
            foreach( $tweet["entities"]["user_mentions"] as $mention ){
                
            	if( is_object( $mention) ){
           			$mention = (array)$mention;
           		}

                $sNameVariations = ["@" . strtolower($mention["screen_name"]), "@" . ucfirst($mention["screen_name"]), "@" . strtoupper($mention["screen_name"])];
                
                $tweet_text = str_replace($sNameVariations, "<a href='https://www.twitter.com/" . $mention["screen_name"] . "'>@" . $mention["screen_name"] . "</a>", $tweet_text );
            }
        }
        
		if( sizeof( $tweet["entities"]["urls"] ) > 0 ){

            foreach( $tweet["entities"]["urls"] as $url ){

            	if( is_object( $url ) ){
            		$url = (array)$url;
            	}

            	$tweet_text = str_replace($url["url"], "<a href='" . $url["expanded_url"] . "' target='_blank'>" . $url["url"] . "</a>", $tweet_text);
            }
        }

        return $tweet_text;
	}

	
}