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
echo "COMECOU EXTRA INFO<br/>";
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

$raw = '<ExtrasListRQ version="1.1" insuranceVersion="2.0">
    <Credentials username="club1hot944" password="club1hot944" remoteIp="91.151.7.6"/> 
    <Vehicle id="674412831"/>
    <PickUp>
        <Date year="2020" month="11" day="21" hour="12" minute="30"/> 
    </PickUp>
    <DropOff>
        <Date year="2020" month="11" day="28" hour="12" minute="30"/>
    </DropOff>
    <Price>88.15</Price> 
</ExtrasListRQ>';

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
$ExtrasListRS = $inputDoc->getElementsByTagName("ExtrasListRS");
$ExtraInfoList = $ExtrasListRS->item(0)->getElementsByTagName("ExtraInfoList");
if ($ExtraInfoList->length > 0) {
    $ExtraInfo = $ExtraInfoList->item(0)->getElementsByTagName("ExtraInfo");
    if ($ExtraInfo->length > 0) {
        for ($i=0; $i < $ExtraInfo->length; $i++) { 
            $defaultOptIn = $ExtraInfo->item($i)->getAttribute("defaultOptIn");
            $Extra = $ExtraInfo->item($i)->getElementsByTagName("Extra");
            if ($Extra->length > 0) {
                $id = $Extra->item(0)->getAttribute("id");
                $available = $Extra->item(0)->getAttribute("available");
                $product = $Extra->item(0)->getAttribute("product");
                $Name = $Extra->item(0)->getElementsByTagName("Name");
                if ($Name->length > 0) {
                    $Name = $Name->item(0)->nodeValue;
                } else {
                    $Name = "";
                }
                $Comments = $Extra->item(0)->getElementsByTagName("Comments");
                if ($Comments->length > 0) {
                    $Comments = $Comments->item(0)->nodeValue;
                } else {
                    $Comments = "";
                }
            }
            $Price = $ExtraInfo->item($i)->getElementsByTagName("Price");
            if ($Price->length > 0) {
                $currency = $Price->item(0)->getAttribute("currency");
                $baseCurrency = $Price->item(0)->getAttribute("baseCurrency");
                $basePrice = $Price->item(0)->getAttribute("basePrice");
                $prePayable = $Price->item(0)->getAttribute("prePayable");
                $maxPrice = $Price->item(0)->getAttribute("maxPrice");
                $minPrice = $Price->item(0)->getAttribute("minPrice");
                $pricePerWhat = $Price->item(0)->getAttribute("pricePerWhat");
                $pricePerRental = $Price->item(0)->getAttribute("pricePerRental");
                $priceAvailable = $Price->item(0)->getAttribute("priceAvailable");
                $Price = $Price->item(0)->nodeValue;
            } else {
                $Price = "";
            }  
            $PreBookingURIs = $ExtraInfo->item($i)->getElementsByTagName("PreBookingURIs");
            if ($PreBookingURIs->length > 0) {
                $PreBookingKeyFactsURI = $Extra->item(0)->getElementsByTagName("PreBookingKeyFactsURI");
                if ($PreBookingKeyFactsURI->length > 0) {
                    for ($iAux=0; $iAux < $PreBookingKeyFactsURI->length; $iAux++) { 
                        $PreBookingKeyFactsURI = $PreBookingKeyFactsURI->item($iAux)->nodeValue;
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