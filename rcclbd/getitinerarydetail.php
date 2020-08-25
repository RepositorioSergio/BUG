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

$config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT id, duration, start, status, shipcode, vendorcode, inclusiveindicator  FROM cruisesailavail";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $CruisePackageCode = $row["id"];
        $Duration = $row["duration"];
        $Start = $row["start"];
        $Status = $row["status"];
        $ShipCode = $row["shipcode"];
        $VendorCode = $row["vendorcode"];
        $InclusiveIndicator = $row["inclusiveindicator"];

        $username = 'CONSTGCOSTAMAR';
        $password = '3MDQV5F5BzdvcX9';

        $url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/ItineraryDetail";

        $raw = '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:itin="http://services.rccl.com/Interfaces/ItineraryDetail" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
        <soapenv:Header/>
        <soapenv:Body>
            <itin:getItineraryDetail>
                <OTA_CruiseItineraryDescRQ RetransmissionIndicator="false" SequenceNmbr="1" TimeStamp="2008-12-29T18:25:50.1Z" TransactionIdentifier="106597" Version="1.0" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
                    <POS>
                        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                            <RequestorID ID="369567" ID_Context="AGENCY1" Type="11"/>
                            <BookingChannel Type="7">
                                <CompanyName CompanyShortName="COSTAMAR"/>
                            </BookingChannel>
                        </Source>
                        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                            <RequestorID ID="369567" ID_Context="AGENCY2" Type="11"/>
                            <BookingChannel Type="7">
                                <CompanyName CompanyShortName="COSTAMAR"/>
                            </BookingChannel>
                        </Source>
                        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                            <RequestorID ID="369567" ID_Context="AGENT1" Type="11"/>
                            <BookingChannel Type="7">
                                <CompanyName CompanyShortName="COSTAMAR"/>
                            </BookingChannel>
                        </Source>
                    </POS>
                    <!--Optional:-->
                    <SelectedSailing Start="' . $Start . '" Duration="' . $Duration . '" VendorCode="' . $VendorCode . '" ShipCode="' . $ShipCode . '" Status="' . $Status . '"/>
                    <!--Optional:-->
                    <PackageOption CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="false"/>
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

        $config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
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
                                'datetime_updated' => time(),
                                'portcode' => $PortCode,
                                'portname' => $PortName,
                                'information' => $Text,
                                'cruisepackagecode' => $CruisePackageCode
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
                                        'datetime_updated' => time(),
                                        'datetimedetails' => $DateTimeDetails,
                                        'datetimequalifier' => $DateTimeQualifier,
                                        'dayofweek' => $DayOfWeek,
                                        'portcode' => $PortCode,
                                        'cruisepackagecode' => $CruisePackageCode
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
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>