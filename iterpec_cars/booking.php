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
echo "COMECOU BOOKING<br/>";
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

$url = "https://ws-iterpec.cangooroo.net/ws/Rest/RentACar.svc/Booking";
$username = "api.xl";
$password = "JNpWAfo%3d&";

$raw = '{
    "Credential": {
       "Username": "' . $username . '",
       "Password": "' . $password . '"
    },
    "Token": "dca28fe1-7f51-4aab-9faa-c7a990da8451",
    "CarId": 1408,
    "Driver":{
       "Name": "Caique",
       "Surname": "NoBook",
       "Age": 25,
       "Cpf": "40028922080",
       "Title": "Mr",
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

$search_identifier = $response['search_identifier'];
$issuing_info = $response['issuing_info'];
?>