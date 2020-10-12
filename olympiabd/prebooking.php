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
$bookingcode = "Hh0nLR0Dxlf0HBrmWq4cRQ+E+jygn8k97GpsxHwOOmHWA0UzgCrkLD0rU53hh2P/JM6M3cRZOq+op0Jmmev4/gEXMninHh+Ftks6iWcx/bhRVDLc7j+WH3pZCxNPnKcj2Fp+6FQxXQCO/wX2+zkIHWydCvlsyqFrgz11Rchaon58fyqIKuefpCPZ8RDUNmDAWeKjRK1aBaMIho1RpKAAtK9gnBOLzMEfhRTbp2MYoziJQJai1roExlFaxCoVwvdU";
$bookingcode2 = "Hh0nLR0Dxlf0HBrmWq4cRaqAPPJqa0KIk6LePy4ByaHWA0UzgCrkLD0rU53hh2P/NAXklT4VPTGlHNtULc8PPnGkXLuibq9N4U36WDsUWjN63OPvZji9S9AVA9tG5lNrhSpmhtiJ27PjM00iEoUIAQ5lOYtm03IdVpNG9wECUPU6G/YYEr5Ny2gwqYsMASQm9Umt/tVcZePN7oNFuYQf02Z6sNdvWyWRSXgOKc65a2nnPTf3MHaXHmp9kDtFqJ4Y";

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