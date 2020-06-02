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
echo "COMECOU ITINERARY DESC<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
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
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));

$username = 'CONCTMM';
$password = 'u73ecKBu73ecKB!';

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/ItineraryDetail";

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:itin="http://services.rccl.com/Interfaces/ItineraryDetail" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
   <soapenv:Header/>
   <soapenv:Body>
      <itin:getItineraryDetail>
         <OTA_CruiseItineraryDescRQ RetransmissionIndicator="false" SequenceNmbr="1" TimeStamp="2008-12-29T18:25:50.1Z" TransactionIdentifier="106597" Version="1.0" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
            <POS>
                <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <RequestorID ID="313917" ID_Context="AGENCY1" Type="5"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="PULLMANTUR"/>
                    </BookingChannel>
                </Source>
                <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <RequestorID ID="313917" ID_Context="AGENCY2" Type="5"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="PULLMANTUR"/>
                    </BookingChannel>
                </Source>
                <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <RequestorID ID="313917" ID_Context="AGENT1" Type="5"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="PULLMANTUR"/>
                    </BookingChannel>
                </Source>
            </POS>
            <!--Optional:-->
            <SelectedSailing Start="2020-09-04" Duration="P7N" VendorCode="PUL" ShipCode="SO" Status="36"/>
            <!--Optional:-->
            <PackageOption CruisePackageCode="SOPD0757" InclusiveIndicator="false"/>
         </OTA_CruiseItineraryDescRQ>
      </itin:getItineraryDetail>
   </soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
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
$getItineraryDetailResponse = $Body->item(0)->getElementsByTagName("getItineraryDetailResponse");
if ($getItineraryDetailResponse->length > 0) {
    $CruiseItinInfos = $getItineraryDetailResponse->item(0)->getElementsByTagName("CruiseItinInfos");
    if ($CruiseItinInfos->length > 0) {
        $CruiseItinInfo = $CruiseItinInfos->item(0)->getElementsByTagName("CruiseItinInfo");
        if ($CruiseItinInfo->length > 0) {
            for ($i=0; $i < $CruiseItinInfo->length; $i++) { 
                $PortCode = $CruiseItinInfo->item($i)->getAttribute("PortCode");
                $PortName = $CruiseItinInfo->item($i)->getAttribute("PortName");

                $Information = $CruiseItinInfo->item($i)->getElementsByTagName("Information");
                if ($Information->length > 0) {
                    $Text = $Information->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        $Text = $Text->item(0)->nodeValue;
                    } else {
                        $Text = "";
                    }
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('cruiseitineraries');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'portcode' => $PortCode,
                        'portname' => $PortName,
                        'information' => $Text
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 1: " . $e;
                    echo $return;
                }

                $DateTimeDescription = $CruiseItinInfo->item($i)->getElementsByTagName("DateTimeDescription");
                if ($DateTimeDescription->length > 0) {
                    for ($iAux=0; $iAux < $DateTimeDescription->length; $iAux++) { 
                        $DateTimeDetails = $DateTimeDescription->item($iAux)->getAttribute("DateTimeDetails");
                        $DateTimeQualifier = $DateTimeDescription->item($iAux)->getAttribute("DateTimeQualifier");
                        $DayOfWeek = $DateTimeDescription->item($iAux)->getAttribute("DayOfWeek");

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('cruiseitineraries_datetimedescription');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'datetimedetails' => $DateTimeDetails,
                                'datetimequalifier' => $DateTimeQualifier,
                                'dayofweek' => $DayOfWeek
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 2: " . $e;
                            echo $return;
                        }
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