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
echo "COMECOU HOTEL LIST<br/>";
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
// Start
$affiliate_id = 0;
$branch_filter = "";

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://b2b-api-staging.snaptravel.com/b2b';

$raw = '{
    "arrivalDate": "06/28/2020",
    "departureDate": "06/30/2020",
    "room1": "2",
    "hotelIdList": [108540,112915,118583,118903,119566,122212],
    "locale": "en_US",
    "currencyCode": "USD",
    "timeout": 2
  }';

/* $client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
    "x-api-key: 1Yr3v5xEXGqwB8MD2g1n3oma0r3blov3Exgo0r86",
    "locale: en_US",
    "currencyCode: USD",
    'Content-Length: 0'
));

$client->setUri($url);
$client->setMethod('GET');
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
}   */

$headers = array(
    "x-api-key: 1Yr3v5xEXGqwB8MD2g1n3oma0r3blov3Exgo0r86",
    "Content-Type: application/json",
    "version: 3",
    "Content-Length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $response;

$response = json_decode($response, true);
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$HotelListResponse = $response['HotelListResponse'];
$customerSessionId = $HotelListResponse['customerSessionId'];
$HotelList = $HotelListResponse['HotelList'];
if (count($HotelList) > 0) {
    $size = $HotelList['size'];
    $HotelSummary = $HotelList['HotelSummary'];
    if (count($HotelSummary) > 0) {
        for ($i=0; $i < count($HotelSummary); $i++) { 
            $hotelId = $HotelSummary[$i]['hotelId'];
            $RoomRateDetailsList = $HotelSummary[$i]['RoomRateDetailsList'];
            $RoomRateDetails = $RoomRateDetailsList['RoomRateDetails'];
            $RateInfos = $RoomRateDetails['RateInfos'];
            $RateInfo = $RateInfos['RateInfo'];
            $ChargeableRateInfo = $RateInfo['ChargeableRateInfo'];
            $currencyCode = $ChargeableRateInfo['currencyCode'];
            $total = $ChargeableRateInfo['total'];
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>