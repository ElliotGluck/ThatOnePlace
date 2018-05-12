<?php
session_start();
header('Content-Type: application/json');
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
      die("That method is not supported.");
}

/*
POST PARAMS:
latlong, formatted 1234,-5678
*/

# preload randomness
$repeatkey = random_int(1, 3);

# set vars
$apikey = getenv('googleapikey');;
$radius = '1609'; // 1 mile default search radius

# interpret POST payload
$payload = json_decode(file_get_contents('php://input'), true);
$latlong = $payload["latlong"];


# repeat restaurants function
function repeatRestaurant(){
  $client = new GuzzleHttp\Client(['base_uri' => 'https://maps.googleapis.com/']);
  if (isset($_SESSION['repeattoken'])) {
    if (random_int(1, 3) != 1) {
//      print_r("GO AROUND ");
      sleep(5);
      $response = $client->request('GET', '/maps/api/place/nearbysearch/json?key='.$GLOBALS["apikey"].'&pagetoken='.$_SESSION['repeattoken'].'&type=restaurant');
      $_SESSION['places'] = json_decode($response->getBody());
      $_SESSION['repeattoken'] = $_SESSION['places']->next_page_token;
      $_SESSION['results'] = $_SESSION['places']->results;
      repeatRestaurant();
    } else {
//      print_r("TOKEN PRESENT AGAIN ");
      $max = count($_SESSION['results']);
//      print_r(''.$max.' ');
      $rando = random_int(0, $max);
      print_r(json_encode($_SESSION['results'][$rando]));
      session_destroy();
    }
  } else {
//    print_r("TOKEN NOT PRESENT AGAIN ");
    $max = count($_SESSION['results']);
//    print_r(''.$max.' ');
    $rando = random_int(0, $max);
    print_r(json_encode($_SESSION['results'][$rando]));
    session_destroy();
  }
}

# get restaurants function
function getRestaurant(){
  $client = new GuzzleHttp\Client(['base_uri' => 'https://maps.googleapis.com/']);
    $response = $client->request('GET', '/maps/api/place/nearbysearch/json?key='.$GLOBALS["apikey"].'&location='.$GLOBALS["latlong"].'&radius='.$GLOBALS["radius"].'&type=restaurant&opennow');
  $_SESSION['places'] = json_decode($response->getBody());
  $_SESSION['repeattoken'] = $_SESSION['places']->next_page_token;
  $_SESSION['results'] = $_SESSION['places']->results;
  if(isset($_SESSION['repeattoken'])){
//    print_r("TOKEN IS HERE ");
//    print_r("".$GLOBALS['repeatkey']." ");
    if ($GLOBALS['repeatkey'] != 1) {
      print_r("REPEATED ");
      $_SESSION['repeattoken'] = $_SESSION['places']->next_page_token;
//      echo $_SESSION['repeattoken'];
      sleep(5);
      $response = $client->request('GET', '/maps/api/place/nearbysearch/json?key='.$GLOBALS["apikey"].'&pagetoken='.$_SESSION['repeattoken'].'&type=restaurant');
      $_SESSION['places'] = json_decode($response->getBody());
      $_SESSION['repeattoken'] = $_SESSION['places']->next_page_token;
//      print(' /maps/api/place/nearbysearch/json?key='.$GLOBALS["apikey"].'&pagetoken='.$_SESSION['repeattoken'].'&type=restaurant ');
//      var_dump($_SESSION['places']);
      $_SESSION['results'] = $_SESSION['places']->results;
      repeatRestaurant();
    } else {
//      print_r("NOT REPEATED ");
      $max = count($_SESSION['results']);
//      print_r(''.$max.' ');
      $rando = random_int(0, $max);
      print_r(json_encode($_SESSION['results'][$rando]));
      session_destroy();
    }
  } else {
//    print_r("NO TOKEN ");
    $max = count($_SESSION['results']);
//    print_r(''.$max.' ');
    $rando = random_int(0, $max);
    print_r(json_encode($_SESSION['results'][$rando]));
    session_destroy();
  }
}

getRestaurant();
