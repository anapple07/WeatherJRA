<?php

require_once './crawler/vendor/autoload.php';
require_once './config.php';
require_once './twitteroauth/autoload.php';

use Goutte\Client;
use Abraham\TwitterOAuth\TwitterOAuth;

$client = new Client();
$crawler = $client->request('GET', 'http://www.jra.go.jp/');

$dom = $crawler->filter('#kaisai_area .kaisai_unit .kaisai_icon');

$dom->each(function($node) {
    $list = $node->filter('img')->attr('alt');
    $place_data = get_place_id($list);

    tweet($place_data);

});


function get_place_id($list) {
    $place_list = array(
        '中山'  => 1863905,
        '阪神'  => 1851012,
        '中京'  => 1856057,
        '京都'  => 1857910,
        '札幌'  => 2128295,
        '函館'  => 2130188,
        '福島'  => 2112923,
        '新潟'  => 1855431,
        '東京'  => 1864154,
        '小倉'  => 1859307
    );

    foreach ( $place_list as $key => $value ) {
        if ( $list == $key ) {
            $data = array( $key => $value);
        }
    }
    return  $data;
}

function tweet($place_data) {

$consumer_key = CONSUMER_KEY;
$consumer_secret = CONSUMER_SECRET;
$access_token = ACCESS_TOKEN;
$access_token_secret = ACCESS_TOKEN_SECRET;
$api_key = API_KEY;

foreach ( $place_data as $key => $value ) {
    $place = $key;
    $place_id = $value;

}

$url = 'http://api.openweathermap.org/data/2.5/weather?id='. $place_id. '&units=metric&appid='. $api_key;
$json = file_get_contents($url);
$array = json_decode($json, true);


$weather = $array['weather'][0]['description'];
$temp = $array['main']['temp'];
$wind = $array['wind']['speed'];

$text = "【☀️お天気情報☁️】\n"."現在の". $place ."競馬場のお天気は".$weather. "\n気温". $temp . "℃" . " 風速" . $wind . "m/s です。"; 

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
$res = $connection->post("statuses/update", array("status" => $text ));

}

?>
