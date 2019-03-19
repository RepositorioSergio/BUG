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
echo "COMECOU VEHRES";
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

$config = new \Zend\Config\Config(include '../config/autoload/globlal.avisbudget.php');
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
    <OTA_VehCancelRQ xmlns:xsi="http://www.w3.org/2008/XMLSchema-instance" Version="1.0">
    <POS>
      <Source>
        <RequestorID Type="1" ID="CTMTours"/>
      </Source>
      <Source>
        <RequestorID Type="5" ID="11063888US3"/>
      </Source>
    </POS>
    <VehCancelRQCore CancelType="Commit">
      <UniqueID Type="14" ID="11063888US3"/>
      <PersonName>
        <Surname>Cebola</Surname>
      </PersonName>
    </VehCancelRQCore>
    <VehCancelRQInfo>
      <Vendor CompanyShortName="Avis"/>
    </VehCancelRQInfo>
  </OTA_VehCancelRQ>
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

$config = new \Zend\Config\Config(include '../config/autoload/globlal.avisbudget.php');
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
$OTA_VehCancelRS = $Response->item(0)->getElementsByTagName("OTA_VehCancelRS");
$TID = $OTA_VehCancelRS->item(0)->getAttribute("TID");
$VehResRSCore = $OTA_VehCancelRS->item(0)->getElementsByTagName("VehResRSCore");
if ($VehResRSCore->length > 0) {
    $CancelStatus = $VehResRSCore->item(0)->getAttribute("CancelStatus");
}

$VehCancelRSInfo = $OTA_VehCancelRS->item(0)->getElementsByTagName("VehCancelRSInfo");
$VehReservation = $VehCancelRSInfo->item(0)->getElementsByTagName("VehReservation");
if ($VehReservation->length > 0) {
    $Customer = $VehReservation->item(0)->getElementsByTagName("Customer");
    if ($Customer->length > 0) {
        $Primary = $Customer->item(0)->getElementsByTagName("Primary");
        if ($Primary->length > 0) {
            $Primary = $Primary->item(0)->nodeValue;
        } else {
          $Primary = "";
        }  
    }

    $VehSegmentCore = $VehReservation->item(0)->getElementsByTagName("VehSegmentCore");
    if ($VehSegmentCore->length > 0) {
        $ConfID = $VehSegmentCore->item(0)->getElementsByTagName("ConfID");
        if ($ConfID->length > 0) {
            $ID = $ConfID->item(0)->getAttribute("ID");
            $Type = $ConfID->item(0)->getAttribute("Type");
        } else {
            $ID = "";
            $Type = "";
        } 
        $Vendor = $VehSegmentCore->item(0)->getElementsByTagName("Vendor");
        if ($Vendor->length > 0) {
            $Vendor = $Vendor->item(0)->nodeValue;
        } else {
            $Vendor = "";
        }
        if ($Vehicle->length > 0) {
            $Vehicle = $Vehicle->item(0)->nodeValue;
        } else {
            $Vehicle = "";
        }
        if ($RentalRate->length > 0) {
            $RentalRate = $RentalRate->item(0)->nodeValue;
        } else {
            $RentalRate = "";
        }

        //VehRentalCore
        $VehRentalCore = $VehSegmentCore->item(0)->getElementsByTagName("VehRentalCore");
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

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('vehCancel');
                $insert->values(array(
                    'ID' => $ID,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Type' => $Type,
                    'Vendor' => $Vendor,
                    'Vehicle' => $Vehicle,
                    'RentalRate' => $RentalRate,
                    'ReturnDateTime' => $ReturnDateTime,
                    'PickUpDateTime' => $PickUpDateTime,
                    'PickUpLocationCodeContext' => $PickUpLocationCodeContext,
                    'PickUpLocationLocationCode' => $PickUpLocationLocationCode,
                    'ReturnLocationCodeContext' => $ReturnLocationCodeContext,
                    'ReturnLocationLocationCode' => $ReturnLocationLocationCode,
                    'TID' => $TID,
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO: " . $e;
                echo $return;
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>