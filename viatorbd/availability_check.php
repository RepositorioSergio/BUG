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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = 'https://api.viator.com/partner/availability/check';

$raw = '{
    "productCode": "5010SYDNEY",
    "travelDate": "2020-11-28",
    "currency": "AUD",
    "paxMix": [
      {
        "ageBand": "ADULT",
        "numberOfTravelers": 2
      },
      {
        "ageBand": "CHILD",
        "numberOfTravelers": 2
      }
    ]
  }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'exp-api-key' => '5364bbaf-e4f7-4727-9e91-317e794dfbaa',
    'Accept-Language' => 'en-US',
    'Accept' => 'application/json;version=2.0'
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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$currency = $response['currency'];
$productCode = $response['productCode'];
$travelDate = $response['travelDate'];
$bookableItems = $response['bookableItems'];
if (count($bookableItems) > 0) {
    for ($i=0; $i < count($bookableItems); $i++) { 
        $productOptionCode = $bookableItems[$i]['productOptionCode'];
        $available = $bookableItems[$i]['available'];
        $totalPrice = $bookableItems[$i]['totalPrice'];
        $price = $totalPrice['price'];
        $recommendedRetailPrice = $price['recommendedRetailPrice'];
        $partnerNetPrice = $price['partnerNetPrice'];
        $bookingFee = $price['bookingFee'];
        $partnerTotalPrice = $price['partnerTotalPrice'];
        $lineItems = $bookableItems[$i]['lineItems'];
        if (count($lineItems) > 0) {
            for ($iAux=0; $iAux < count($lineItems); $iAux++) { 
                $ageBand = $lineItems[$iAux]['ageBand'];
                $numberOfTravelers = $lineItems[$iAux]['numberOfTravelers'];
                $subtotalPrice = $lineItems[$iAux]['subtotalPrice'];
                $price = $subtotalPrice['price'];
                $recommendedRetailPrice = $price['recommendedRetailPrice'];
                $partnerNetPrice = $price['partnerNetPrice'];
            }
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>