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
echo "COMECOU RENTAL TERMS<br/>";
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
    "Content-type: text/xml",
    "Accept: text/xml",
    "Content-length: ".strlen($raw)
));

$url = "https://xml.rentalcars.com/service/ServiceRequest.do";

$raw = '<RentalTermsRQ version="1.1">
    <Credentials username="club1hot944" password="club1hot944"/> 
    <Vehicle id=""/>
    <DriverAge/>
    <PickUp>
        <Location id=""/>
        <Date year="" month="" day="" hour="" minute=""/> 
    </PickUp>
    <DropOff>
        <Location id=""/>
        <Date year="" month="" day="" hour="" minute=""/>
    </DropOff>
    <RateReference/>
 </RentalTermsRQ>';

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
$RentalTermsRS = $inputDoc->getElementsByTagName("RentalTermsRS");
$TermsList = $RentalTermsRS->item(0)->getElementsByTagName("TermsList");
if ($TermsList->length > 0) {
    $TermGroup = $TermsList->item(0)->getElementsByTagName("TermGroup");
    if ($TermGroup->length > 0) {
        for ($i=0; $i < $TermGroup->length; $i++) { 
            $type = $TermGroup->item($i)->getAttribute("type");
            $Caption = $TermGroup->item($i)->getElementsByTagName("Caption");
            if ($Caption->length > 0) {
                $Caption = $Caption->item(0)->nodeValue;
            } else {
                $Caption = "";
            }
            $Body = $TermGroup->item($i)->getElementsByTagName("Body");
            if ($Body->length > 0) {
                $Body = $Body->item(0)->nodeValue;
            } else {
                $Body = "";
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