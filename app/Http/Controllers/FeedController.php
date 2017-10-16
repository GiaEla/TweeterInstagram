<?php

namespace App\Http\Controllers;

use App\Twitter;
use Illuminate\Http\Request;

use Abraham\TwitterOAuth\TwitterOAuth;
use MetzWeb\Instagram\Instagram;


class FeedController extends Controller
{
    public function getFeed(Request $request){
        $username = $request->input('username');
        $inst_username = $username;
        $username = '@' . $username;

        // Twitter
        $user_tweets = Twitter::where('user', $username)
            ->orderBy('tweet_posted_at', 'desc')
            ->take(10)
            ->get();
        if ($user_tweets->count() > 0){
            //pass
        }
        else{
            $twitteruser = $username;
            $notweets = 10;
            $consumerkey = "1lAANUIy38sU5CDl1mUHTchpv";
            $consumersecret = "mTsTybv5WVIYq8Y4AAf4ICaDjnbUSABu46bdzykZDF7j0T7boX";
            $accesstoken = "918880075805753344-2jGmrD6Z8snG3JQsRhkchNJ23weE7vI";
            $accesstokensecret = "tIAFqywupO7H1MUu37NOYltJE5PgpNwntsMLBDdmxlQux";

            $connection = $this->getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);

            $tweets = $connection->get('statuses/user_timeline', ['screen_name' => $twitteruser, 'count' => $notweets]);

            foreach ($tweets as $tweet){
                $twitter = new Twitter();
                $twitter->tweet_posted_at = date('Y-m-d H:i:s', strtotime($tweet->created_at));
                $twitter->user = $username;
                $twitter->twit_id = $tweet->id;
                $twitter->tekst = $tweet->text;

                $twitter->save();
            }

            $user_tweets = Twitter::where('user', $username)
                ->orderBy('tweet_posted_at', 'desc')
                ->take(10)
                ->get();
        }
        // Instagram

        $noinsta= 10;
        $access_token = '6219529913.b8f94c0.5f2d434920cb4c6d84dcda7049461604';
        //get user id
        $instagram_id = "https://api.instagram.com/v1/users/search?q=$username&access_token=$access_token";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $instagram_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $result = json_decode($response);

        $instagram_url = "https://api.instagram.com/v1/users/$inst_username/media/recent/?access_token=$access_token&count=$noinsta";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $instagram_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $result = json_decode($response);

//        $instagram = new Instagram(array(
//            'apiKey'      => 'b8f94c00318b4341bdb2bd2f58b39dca',
//            'apiSecret'   => 'c92ff11d867a4091b6bbec3a074143dd',
//            'apiCallback' => 'http://zabec.app/'
//        ));
//        $inst_token = $instagram->setAccessToken('6219529913.b8f94c0.5f2d434920cb4c6d84dcda7049461604');
//        $instagram_feed = $instagram->getUserFeed($noinsta);

        return view('feed', ['username' => $username, 'tweets' => $user_tweets]);

    }
     //Twitter
    private function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
        $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
        return $connection;
    }

    //Instagram
    public function getTokenUrl(){
        return view('getToken');
    }
}
