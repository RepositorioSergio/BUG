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
echo "COMECOU PRE CHECK";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://sandbox-affiliateapiservices.agoda.com/api/v1/prebooking/precheck';

$searchid = 604183064886580000;
$tag = "00000000-0000-0000-0000-000000000000";
$checkin = "2020-01-25";
$checkout = "2020-01-27";
$hotelID = 12133;
$roomId = 3094730;
$promotionid = 164998561;
$name = "Studio";
$lineitemid = 1;
$rateplan = "B2B";
$ratetype = "B2B";
$currency = "USD";
$model = "Merchant";
$ratecategoryid = 377586;
$blockid = "548d30b1-7a9d-d62e-1efe-32d229c618c9";
$count = 1;
$adults = 2;
$children = 0;
$rateExclusive = 53.20;
$rateInclusive = 62.62;
$rateTax = 4.63;
$rateFees = 4.79;

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<PrecheckRequest siteid="1831338" apikey="b57a754c-5e06-4cdd-ac0d-2ea58c48ef74" 
    xmlns="http://xml.agoda.com" 
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <PrecheckDetails searchid="' . $searchid . '" tag="' . $tag . '" AllowDuplication="true" CheckIn="' . $checkin . '" CheckOut="' . $checkout . '">
        <Hotel id="' . $hotelID . '">
            <Rooms>
                <Room id="' . $roomId . '" promotionid="' . $promotionid . '" name="' . $name . '" lineitemid="' . $lineitemid . '" rateplan="' . $rateplan . '" ratetype="' . $ratetype . '" currency="' . $currency . '" model="' . $model . '" ratecategoryid="' . $ratecategoryid . '" blockid="' . $blockid . '" count="' . $count . '" adults="' . $adults . '" children="' . $children . '">
                    <Rate exclusive="' . $rateExclusive . '" tax="' . $rateTax . '" fees="' . $rateFees . '" inclusive="' . $rateInclusive . '"/>
                </Room>
            </Rooms>
        </Hotel>
    </PrecheckDetails>
</PrecheckRequest>';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'text/xml;charset=utf-8',
    'Authorization' => '1831338:b57a754c-5e06-4cdd-ac0d-2ea58c48ef74'
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

echo '<xmp>';
var_dump($response);
echo '</xmp>';


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$PrecheckResponse = $inputDoc->getElementsByTagName("PrecheckResponse");
$status = $PrecheckResponse->item(0)->getAttribute('status');

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>