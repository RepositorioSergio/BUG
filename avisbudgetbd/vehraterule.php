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
echo "COMECOU VEHRATERULE";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.avisbudget.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$raw = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"  xmlns:xsi="http://www.w3.org/1999/XMLSchema-instance" xmlns:xsd="http://www.w3.org/1999/XMLSchema">
<SOAP-ENV:Header>
    <ns:credentials xmlns:ns="http://wsg.avis.com/wsbang/authInAny">
        <ns:userID ns:encodingType="xsd:string">CTMTours</ns:userID>
        <ns:password ns:encodingType="xsd:string">zGkWdCXG8yrw</ns:password>
    </ns:credentials>
    <ns:WSBang-Roadmap xmlns:ns="http://wsg.avis.com/wsbang"/>
</SOAP-ENV:Header>
<SOAP-ENV:Body>
    <ns:Request xmlns:ns="http://wsg.avis.com/wsbang">
       <OTA_VehRateRuleRQ xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1.0" CompanyShortName="Avis">
        <POS>
          <Source>
            <RequestorID Type="1" ID="CTMTours"/>
          </Source>
          <Source>
            <RequestorID Type="6" ID="00000000"/>
          </Source>
        </POS>
        <RentalInfo>
          <VehRentalCore PickUpDateTime="2099-04-05T09:00:00" ReturnDateTime="2019-04-06T09:00:00">
            <PickUpLocation LocationCode="DFW" />
            <ReturnLocation LocationCode="DFW" />
          </VehRentalCore>
          <VehicleInfo TypePref="Only" TransmissionPref="Only" TransmissionType="Automatic" AirConditionPref="Only" ClassPref="Only">
            <VehType VehicleCategory="1"/>
            <VehClass Size="4"/>
          </VehicleInfo>
          <RateQualifier RateQualifier="3"/>
          <CustomerID Type="1" ID="US"/>
        </RentalInfo>
      </OTA_VehRateRuleRQ>
</ns:Request>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

$url = 'https://qaservices.carrental.com/wsbang/HTTPSOAPRouter/ws9071';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: text/xml",
    "Accept: text/xml",
    "Content-length: " . strlen($raw)
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

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.avisbudget.php');
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
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$Response = $Body->item(0)->getElementsByTagName("Response");
$OTA_VehAvailRateRS = $Response->item(0)->getElementsByTagName("OTA_VehAvailRateRS");
$VehAvailRSCore = $OTA_VehAvailRateRS->item(0)->getElementsByTagName("VehAvailRSCore");
//VehRentalCore
$VehRentalCore = $VehAvailRSCore->item(0)->getElementsByTagName("VehRentalCore");
if ($VehRentalCore->length > 0) {
    $ReturnDateTime = $VehRentalCore->item(0)->getAttribute("ReturnDateTime");
    $PickUpDateTime = $VehRentalCore->item(0)->getAttribute("PickUpDateTime");

    $PickUpLocation = $VehRentalCore->item(0)->getElementsByTagName("PickUpLocation");
    if ($PickUpLocation->length > 0) {
        $PickUpLocationCodeContext = $PickUpLocation->item(0)->getAttribute("CodeContext");
        $PickUpLocationLocationCode = $PickUpLocation->item(0)->getAttribute("LocationCode");
    }

    $ReturnLocation = $VehRentalCore->item(0)->getElementsByTagName("ReturnLocation");
    if ($ReturnLocation->length > 0) {
        $ReturnLocationCodeContext = $ReturnLocation->item(0)->getAttribute("CodeContext");
        $ReturnLocationLocationCode = $ReturnLocation->item(0)->getAttribute("LocationCode");
    }
}

//VehVendorAvails
$VehVendorAvails = $VehAvailRSCore->item(0)->getElementsByTagName("VehVendorAvails");
if ($VehVendorAvails->length > 0) {

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('vehavail');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'ReturnDateTime' => $ReturnDateTime,
            'PickUpDateTime' => $PickUpDateTime,
            'PickUpLocationCodeContext' => $PickUpLocationCodeContext,
            'PickUpLocationLocationCode' => $PickUpLocationLocationCode,
            'ReturnLocationCodeContext' => $ReturnLocationCodeContext,
            'ReturnLocationLocationCode' => $ReturnLocationLocationCode,
            'Vendor' => $Vendor,
            'Code' => $Code,
            'Name' => $Name,
            'CodeContext' => $CodeContext,
            'ExtendedLocationCode' => $ExtendedLocationCode,
            'AtAirport' => $AtAirport,
            'PhoneNumber' => $PhoneNumber,
            'StreetNmbr' => $StreetNmbr,
            'CityName' => $CityName,
            'PostalCode' => $PostalCode,
            'StateCode' => $StateCode,
            'StateProv' => $StateProv,
            'CountryCode' => $CountryCode,
            'CountryName' => $CountryName
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error: " . $e;
        echo $return;
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>