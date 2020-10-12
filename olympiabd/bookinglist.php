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
// Start
$affiliate_id = 0;
$branch_filter = "";

$config = new \Zend\Config\Config(include '../config/autoload/global.olympia.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://parsystest.olympia.it/NewAvailabilityServlet/bookinglist/OTA2014Compact';

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/">
	<soap-env:Header>
		<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			<wsse:Username>628347</wsse:Username>
			<wsse:Password>clubtest</wsse:Password>
			<Context>olympia_europe_ts</Context>
		</wsse:Security>
	</soap-env:Header>
	<soap-env:Body>
        <OTA_BookingListRQ xmlns="http://parsec.es/hotelapi/OTA2014Compact">
            <BookingSearch>
                <DateRange Start="2021-01-03" End="2021-01-10" DateType="Arrival"/>
                <!-- <HotelRef HotelCode="364382"/> -->
            </BookingSearch>
        </OTA_BookingListRQ>
	</soap-env:Body>
</soap-env:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type: text/xml; charset=utf-8',
    'Accept: application/xml',
    'Content-Length: ' . strlen($raw)
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

$config = new \Zend\Config\Config(include '../config/autoload/global.olympia.php');
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
$OTA_BookingListRS = $inputDoc->getElementsByTagName('OTA_BookingListRS');
$BookingList = $OTA_BookingListRS->item(0)->getElementsByTagName('BookingList');
if ($BookingList->length > 0) {
    $ResGlobalInfo = $BookingList->item(0)->getElementsByTagName('ResGlobalInfo');
    if ($ResGlobalInfo->length > 0) {
        for ($i=0; $i < $ResGlobalInfo->length; $i++) { 
            $LeadGuest = $ResGlobalInfo->item($i)->getElementsByTagName('LeadGuest');
            if ($LeadGuest->length > 0) {
                $LeadGuest = $LeadGuest->item(0)->nodeValue;
            } else {
                $LeadGuest = "";
            }
            $DateRange = $ResGlobalInfo->item($i)->getElementsByTagName('DateRange');
            if ($DateRange->length > 0) {
                $Start = $DateRange->item(0)->getAttribute('Start');
                $End = $DateRange->item(0)->getAttribute('End');
            }
            $Total = $ResGlobalInfo->item($i)->getElementsByTagName('Total');
            if ($Total->length > 0) {
                $Amount = $Total->item(0)->getAttribute('Amount');
                $Commission = $Total->item(0)->getAttribute('Commission');
                $Currency = $Total->item(0)->getAttribute('Currency');
            }
            $ResIDs = $ResGlobalInfo->item($i)->getElementsByTagName('ResIDs');
            if ($ResIDs->length > 0) {
                $ResID = $ResIDs->item(0)->getElementsByTagName('ResID');
                if ($ResID->length > 0) {
                    for ($i=0; $i < $ResID->length; $i++) { 
                        $Type = $ResID->item($i)->getAttribute('Type');
                        $ID = $ResID->item($i)->getAttribute('ID');
                    }
                }
            }
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>