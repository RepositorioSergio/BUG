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

$url = 'http://parsystest.olympia.it/NewAvailabilityServlet/hotelres/OTA2014Compact';
$bookingcode = "Hh0nLR0Dxlf0HBrmWq4cRfcpxEe4BuU4tz8EYLOI5Ew3mWm3Gv6befkFSDkHNU6a4hmoqtHpHMop7gD7nIvx71eHcNApbfmhRw0XiiA8cydRVDLc7j+WH3pZCxNPnKcj2Fp+6FQxXQCO/wX2+zkIHRaroxCZ2qeyJaxQfXuGB+T6gvU5O5/2KhXI0R/9GJo6dexL4psT42nbif8C8QPpCYB9CCKLJZsMDCloSU90W6SJn9RT7St/nPCdx9jgWA4a";
$bookingcode2 = "Hh0nLR0Dxlf0HBrmWq4cRTlJH5Ayv875oTG07ygLRIzYrw4r0z9AseG7cJysEy2Dly5RFNwIvVWuEloPtzvfrJa6orUgSsEGqzsGzJfS9MJRVDLc7j+WH3pZCxNPnKcj2Fp+6FQxXQCO/wX2+zkIHRaroxCZ2qeyJaxQfXuGB+SHKHKLUsuuNm/CTLXbjXUzUgIsXAwO0WR3rtNnQhlp9Bw6tzYl6nQRaZlB3AA1r5Fo+YT9IiwRU54PSWEgcydh";
$bookingcode3 = "Hh0nLR0Dxlf0HBrmWq4cRTS/YDLLmICWSuq23icQeg3Yrw4r0z9AseG7cJysEy2D4hmoqtHpHMop7gD7nIvx71eHcNApbfmhRw0XiiA8cydRVDLc7j+WH3pZCxNPnKcj2Fp+6FQxXQCO/wX2+zkIHRaroxCZ2qeyJaxQfXuGB+SvDkVo99W43o+u9ZawSX6QG+JF6OvIYsKdQUGdGQtZHxw6tzYl6nQRaZlB3AA1r5Fo+YT9IiwRU54PSWEgcydh";

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
        <OTA_HotelResRQ xmlns="http://parsec.es/hotelapi/OTA2014Compact" Transaction="PreBooking">
            <HotelRes>
                <Rooms>
                    <Room>
                        <RoomRate BookingCode="' . $bookingcode . '"/>
                    </Room>
                    <Room>
                        <RoomRate BookingCode="' . $bookingcode2 . '"/>
                    </Room>
                    <Room>
                        <RoomRate BookingCode="' . $bookingcode3 . '"/>
                    </Room>
                </Rooms>
            </HotelRes>
        </OTA_HotelResRQ>
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
$OTA_BookingInfoRS = $inputDoc->getElementsByTagName('OTA_BookingInfoRS');
$Success = $OTA_BookingInfoRS->item(0)->getElementsByTagName('Success');
if ($Success->length > 0) {
    $Success = $Success->item(0)->nodeValue;
} else {
    $Success = "";
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>