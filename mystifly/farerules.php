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
echo "COMECOU FARERULES<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://restapidemo.myfarebox.com/api/v1/FlightFareRules';
$session_id = 'C65B61B4-1CDE-4512-A7BF-E02DB2D40AB8-365';
$FareSourceCode = 'dEpQUmwrZ2UzOUhzWjIya1pEVk5LMWN1TlJRMWl0Tmh5Vk14cEZKU3RtbHVKazdweDZyN0VqaTIyQmI1WExlMFpUMTl6Mjd3aFBLWkF0QVJqeG9SSVF3RFFiS0FRK3k4d3NvaW5XY2dZSm12L1ZaeDJMcUJ5QkxSNjBDcVFLTklBT1llNVZaUGRpOWRwOE5obC9EaFlBPT0=';
$uniqueid = 'MF13835020';

$raw = '{
    "FareSourceCode": "' . $FareSourceCode . '",
    "UniqueID": "' . $uniqueid . '",
    "Target": "Test",
    "ConversationId": "AAA2"
  }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept' => 'application/json',
    'Content-type' => 'application/json',
    'Authorization' => 'Bearer ' . $session_id,
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
echo "Response- " . $response;

$response = json_decode($response, true);

$Success = $response['Success'];
if ($Success === "true") {
    $Data = $response['Data'];
    $ConversationId = $Data['ConversationId'];
    $Success2 = $Data['Success'];
    $Target = $Data['Target'];
    $BaggageInfos = $Data['BaggageInfos'];
    if (count($BaggageInfos) > 0) {
        for ($i=0; $i < count($BaggageInfos); $i++) { 
            $Arrival = $BaggageInfos[$i]['Arrival'];
            $Baggage = $BaggageInfos[$i]['Baggage'];
            $Departure = $BaggageInfos[$i]['Departure'];
            $FlightNo = $BaggageInfos[$i]['FlightNo'];
        }
    }
    $FareRules = $Data['FareRules'];
    if (count($FareRules) > 0) {
        for ($j=0; $j < count($FareRules); $j++) { 
            $Airline = $FareRules[$j]['Airline'];
            $CityPair = $FareRules[$j]['CityPair'];
            $RuleDetails = $FareRules[$j]['RuleDetails'];
            if (count($RuleDetails) > 0) {
                for ($jAux=0; $jAux < count($RuleDetails); $jAux++) { 
                    $Category = $RuleDetails[$jAux]['Category'];
                    $Rules = $RuleDetails[$jAux]['Rules'];
                }
            }
        }
    }
}

?>