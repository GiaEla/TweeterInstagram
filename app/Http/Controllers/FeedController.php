<?php

namespace App\Http\Controllers;

use App\Twitter;
use Illuminate\Http\Request;

use Abraham\TwitterOAuth\TwitterOAuth;

class FeedController extends Controller
{
    public function getFeed(Request $request){
        $username = $request->input('username');
        $username = '@' . $username;
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

        return view('feed', ['username' => $username, 'tweets' => $user_tweets]);

    }

    private function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
        $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
        return $connection;
    }
}
