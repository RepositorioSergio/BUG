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
echo "COMECOU AMEND<br/>";
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

$raw = '<AmendRQ version="1.1" prefcurr="GBP" preflang="en"> 
<Credentials username="club1hot944" password="club1hot944"/>
<Email>test@rentalcars.com</Email>
<Booking id="326406737">
    <PickUp>
        <Date year="2016" month="05" day="8" hour="8" minute="40"/>
    </PickUp>
    <DropOff>
        <Date year="2016" month="05" day="10" hour="20" minute="50"/> 
    </DropOff>
    <DriverInfo>
        <Address country="Test" city="test" street="Bissenbrook nr. 24" postcode="24623"/> 
        <Telephone>26367899584</Telephone>
    </DriverInfo>
    <AdditionalInfo>
        <Comments>Test Amend Comments</Comments>
        <PickUpService/>
        <DropOffService/>
    </AdditionalInfo>
    <AirlineInfo flightNo="AP 871"/> 
</Booking>
</AmendRQ>';

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
$AmendRS = $inputDoc->getElementsByTagName("AmendRS");
if ($AmendRS->length > 0) {
    $Status = $AmendRS->item(0)->getElementsByTagName("Status");
    if ($Status->length > 0) {
        $Status = $Status->item(0)->nodeValue;
    } else {
        $Status = "";
    }
    $Message = $AmendRS->item(0)->getElementsByTagName("Message");
    if ($Message->length > 0) {
        $Message = $Message->item(0)->nodeValue;
    } else {
        $Message = "";
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>