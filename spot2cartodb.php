<?php

// require_once 'cartodb.class.php';
// require_once 'config.php';

date_default_timezone_set($timezone);
$feedId = '0Ja4Ivs1BMTNaXf3R5ac3QJgWqtfIMUnt';

$cartoKey = 'c407b78029c927aeeed97462c0b6b902f3320af1';
$cartoSubdomain = 'hthompso';
$cartoTable = 'spotted';


$SPOTurl = "https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed/$feedId/message.json";
// echo $url . "?q=" . $q . $api;
// echo "\n\n";

$ch = curl_init($SPOTurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$SPOTresponseRAW = curl_exec($ch);
curl_close($ch);


$SPOTresponse = json_decode($SPOTresponseRAW, true);
$SPOTdata = $SPOTresponse['response'];

if (isset($SPOTdata['errors'])) {
  $error = $SPOTdata['errors']['error'];
  error_log($error['code'] . $error['text']);
  print $error['description'];
  exit();
}
//
$SPOTfeed = $SPOTdata['feedMessageResponse'];
if ($SPOTfeed['count'] == 1) {
  $messages = array($SPOTfeed['messages']['message']);
} else {
  $messages = $SPOTfeed['messages']['message'];
}
// print_r($messages);
// $cartodb = new CartoDBClient($cartodb_config);
//
// if (!$cartodb->authorized) {
//   error_log("uauth");
//   print 'There is a problem authenticating, check the key and secret.';
//   exit();
// }

// Find max timestamp
// $response = $cartodb->runSql("SELECT MAX(timestamp) FROM spot_2017");

$CARTOurl = "https://" . $cartoSubdomain . ".carto.com/api/v2/sql";
$MAXq = "SELECT MAX(timestamp) FROM " . $cartoTable;
$api = "&api_key=" . $cartoKey;
// echo $CARTOurl . "?q=" . $q . $api;
// echo "\n\n";

$ch = curl_init($CARTOurl . "?q=" . urlencode($MAXq) . $api);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$MAXresponseRAW = curl_exec($ch);
// $maxResponse = json_decode($response);
// // Close request to clear up some resources
curl_close($ch);


// print_r($response);

// echo "\n\n";
$maxResponse = json_decode($MAXresponseRAW);

$max = 0;
if(is_array($maxResponse->rows)) {
  $max = array_pop($maxResponse->rows)->max;
}
// echo $max;
$count = 0;
foreach ($messages as $message) {
  $message = array_pop($messages);
  if ($message['unixTime'] > $max) { // Add if newer
    $data = array(
      'feed_id'         => "'" . $feedId . "'",
      'spot_id'         => $message['id'],
      'the_geom'        => "ST_SetSRID(ST_MakePoint(" . $message['longitude'] . ", " . $message['latitude'] . "),4326)",
      'messenger_id'    => "'" . $message['messengerId'] . "'",
      'messenger_name'  => "'" . $message['messengerName'] . "'",
      'timestamp'       => $message['unixTime'],
      'message_type'    => "'" . $message['messageType'] . "'",
      'latitude'        => $message['latitude'],
      'longitude'       => $message['longitude'],
      'model_id'        => "'" . $message['modelId'] . "'",
      'show_custom_msg' => "'" . $message['showCustomMsg'] . "'",
      'datetime'        => "'" . $message['dateTime'] . "'",
      'battery_state'   => "'" . $message['batteryState'] . "'",
      'hidden'          => "'" . $message['hidden'] . "'",
      'message_content' => "'" . (isset($message['messageContent']) ? $message['messageContent'] : '') . " '"
    );

    $INSERTq = "INSERT INTO " . $cartoTable . " (" . implode(", ", array_keys($data)) . ") VALUES (" . implode(", ", $data) . ") ";

    $ch = curl_init($CARTOurl . "?q=" . urlencode($INSERTq) . $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec( $ch );
    // print_r($response);
    curl_close($ch);
    $count++;
  }

}
echo "Spot Tracker synced " . $count . " records to CartoDB table " . $cartoTable;
// require 'kartverket2cartodb.php';
// require 'yr2cartodb.php';

?>