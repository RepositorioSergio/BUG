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
echo "COMECOU INBOUND SEARCH TRANSFERS AND FARES";
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

$url = "https://www.saveatrain.com/api/searches/e9yzCV/results/72/sub_routes";
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

$result_id = $response['result_id'];
$search_identifier = $response['search_identifier'];
$transfers = $response['transfers'];
for ($i=0; $i < count($transfers); $i++) { 
    $id = $transfers[$i]['id'];
    $changes = $transfers[$i]['changes'];
    if (count($changes) > 0) {
        for ($j=0; $j < count($changes); $j++) { 
            $departure_datetime = $changes[$j]['departure_datetime'];
            $arrival_datetime = $changes[$j]['arrival_datetime'];
            $train = $changes[$j]['train'];
            $category = $train['category'];
            $number = $train['number'];
            $origin_station_names = $changes[$j]['origin_station_names'];
            if (count($origin_station_names) > 0) {
                for ($jAux=0; $jAux < count($origin_station_names); $jAux++) { 
                    $name = $origin_station_names[$jAux]['name'];
                    $language = $origin_station_names[$jAux]['language'];
                    $languagename = $language['name'];
                    $languagevalue = $language['value'];
                }
            }
            $destination_station_names = $changes[$j]['destination_station_names'];
            if (count($destination_station_names) > 0) {
                for ($jAux2=0; $jAux2 < count($destination_station_names); $jAux2++) { 
                    $name = $destination_station_names[$jAux2]['name'];
                    $language = $destination_station_names[$jAux2]['language'];
                    $languagename = $language['name'];
                    $languagevalue = $language['value'];
                }
            }
        }
    }
    $fares = $transfers[$i]['fares'];
    if (count($fares) > 0) {
        for ($k=0; $k < count($fares); $k++) { 
            $id = $fares[$k]['id'];
            $name = $fares[$k]['name'];
            $price = $fares[$k]['price'];
        }
    }
}
?>