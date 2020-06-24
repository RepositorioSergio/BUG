<?php
require '../vendor/autoload.php';
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Config;
use Zend\Log\Logger;
use Zend\Log\Writer;
echo "COMECOU CONFIRM SELECTION<br/>";
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}
$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = "https://www.saveatrain.com/api/searches/e9yzCV/confirm_selection";
$username = "api.xl";
$password = "JNpWAfo%3d&";

$raw = '{
    "select_results_attributes": {
      "search_identifier": "e9yzCV",
      "result_id": 7161,
      "transfers_attributes": {
        "0": {
          "id": 2264,
          "fare_id": 28802
        }
      }
    }
  }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'X-Agent-Email' => 'gzip,deflate',
    'X-Agent-Token' => 'Zend Framework',
    'Content-Length' => strlen($raw)
));
$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
    $response = $response->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}
echo $return;
echo $response;
echo $return;
$response = json_decode($response, true);
echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$identifier = $response['identifier'];
$outbound_selected_result = $response['outbound_selected_result'];
$id = $outbound_selected_result['id'];
$departure_datetime = $outbound_selected_result['departure_datetime'];
$arrival_datetime = $outbound_selected_result['arrival_datetime'];
$duration = $outbound_selected_result['duration'];
$best_price = $outbound_selected_result['best_price'];
$kind = $outbound_selected_result['kind'];
$changes_count = $outbound_selected_result['changes_count'];
$route = $outbound_selected_result['route'];
$origin_station = $route['origin_station'];
$origin_station_uid = $origin_station['uid'];
$names = $origin_station['names'];
for ($i=0; $i < count($names); $i++) { 
    $name = $names[$i]['name'];
    $language = $names[$i]['language'];
    $languagename = $language['name'];
    $languagevalue = $language['value'];
}
$destination_station = $route['destination_station'];
$destination_station_uid = $destination_station['uid'];
$names = $destination_station['names'];
for ($j=0; $j < count($names); $j++) { 
    $name = $names[$j]['name'];
    $language = $names[$j]['language'];
    $languagename = $language['name'];
    $languagevalue = $language['value'];
}
$seat_preference = $outbound_selected_result['seat_preference'];
$currency = $seat_preference['currency'];
$amount = $seat_preference['amount'];
$status = $seat_preference['status'];
$options = $seat_preference['options'];
if (count($options) > 0) {
    for ($k=0; $k < count($options); $k++) { 
        $code = $options[$k]['code'];
        $name = $options[$k]['name'];
        $description = $options[$k]['description'];
    }
}
?>