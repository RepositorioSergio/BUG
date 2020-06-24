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
echo "COMECOU OBTAIN API KEY ";
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

$url = "https://www.saveatrain.com/api/sales_agent_sessions";
$username = "api.xl";
$password = "JNpWAfo%3d&";

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
//$client->setRawBody($raw);
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
$complete = $response['complete'];
$departure_datetime = $response['departure_datetime'];
$return_departure_datetime = $response['return_departure_datetime'];
$expiration_time_left = $response['expiration_time_left'];
$route = $response['route'];
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
$results = $response['results'];
for ($k=0; $k < count($results); $k++) { 
    $id = $results[$k]['id'];
    $departure_datetime = $results[$k]['departure_datetime'];
    $arrival_datetime = $results[$k]['arrival_datetime'];
    $duration = $results[$k]['duration'];
    $best_price = $results[$k]['best_price'];
    $kind = $results[$k]['kind'];
    $changes_count = $results[$k]['changes_count'];
    $route = $results[$k]['route'];
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
}
?>