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
echo "COMECOU SAVE QUOTE<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.infinitastravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: application/xml",
    "Content-length: ".strlen($raw)
));

$url = "https://xmlsandbox.rentalcars.com/service/ServiceRequest.do";

$raw = '<SaveQuoteRQ version="1.1" insuranceVersion="2.0">
<Credentials username="club1hot944" password="club1hot944" remoteIp="91.151.7.6"/>  
<Booking>
    <PickUp>
        <Location id="4219175"/>
        <Date year="2020" month="11" day="21" hour="12" minute="30"/> 
    </PickUp>
    <DropOff>
        <Location id="4219175"/>
        <Date year="2020" month="11" day="28" hour="12" minute="30"/>
    </DropOff>
    <ExtraList></ExtraList> 
    <Vehicle id="109433298"/> 
    <DriverInfo>
        <DriverName title="mr" firstname="tester" lastname="test"/> 
        <Address country="UK" city="London" street="Street name" postcode="L1 2AB"/> 
        <Email>example@exampleemail.com</Email>
        <Telephone>+44 (0)20 123 1234</Telephone>
        <DriverAge>32</DriverAge>
    </DriverInfo>
    <AdditionalInfo>
        <Comments>test1</Comments>
        <PickUpService>test2</PickUpService>
        <DropOffService>test3</DropOffService>
    </AdditionalInfo>
    <AirlineInfo flightNo="ABC 1234"></AirlineInfo> 
</Booking>
 </SaveQuoteRQ>';

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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.infinitastravel.php');
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
$SaveQuoteRS = $inputDoc->getElementsByTagName("SaveQuoteRS");
$Booking = $SearchRS->item(0)->getElementsByTagName("Booking");
if ($Booking->length > 0) {
    $id = $Booking->item(0)->getAttribute("id");
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>