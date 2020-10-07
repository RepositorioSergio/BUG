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

$url = 'http://parsystest.olympia.it/NewAvailabilityServlet/reservationsread/OTA2014Compact';

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
        <OTA_ReadRQ xmlns="http://parsec.es/hotelapi/OTA2014Compact" DetailLevel="2" RateDetails="1">
            <UniqueID Type="Locator" ID="964805" />
        </OTA_ReadRQ> 
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
$OTA_BookingListRQ = $inputDoc->getElementsByTagName('OTA_BookingListRQ');
$BookingSearch = $OTA_BookingListRQ->item(0)->getElementsByTagName('BookingSearch');



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>