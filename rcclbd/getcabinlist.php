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
echo "COMECOU CABIN LIST<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.rccl.php');
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

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/CabinList";

$raw2 = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cab="http://services.rccl.com/Interfaces/CabinList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
<soapenv:Header/>
<soapenv:Body>
    <cab:getCabinList>
        <OTA_CruiseCabinAvailRQ MaxResponses="50" MoreDataEchoToken="01" Target="Test" RetransmissionIndicator="false" SequenceNmbr="1" TimeStamp="2008-11-25T10:08:12.204-05:00" TransactionIdentifier="106597" Version="1.0" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
            <POS>
                <Source TerminalID="3MDQV5F5BzdvcX9" ISOCurrency="USD">
                    <RequestorID ID="313917" ID_Context="AGENCY1" Type="11"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="CONSTGCOSTAMAR"/>
                    </BookingChannel>
                </Source>
                <Source TerminalID="3MDQV5F5BzdvcX9" ISOCurrency="USD">
                    <RequestorID ID="313917" ID_Context="AGENCY2" Type="11"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="CONSTGCOSTAMAR"/>
                    </BookingChannel>
                </Source>
                <Source TerminalID="3MDQV5F5BzdvcX9" ISOCurrency="USD">
                    <RequestorID ID="313917" ID_Context="AGENT1" Type="11"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="CONSTGCOSTAMAR"/>
                    </BookingChannel>
                </Source>
            </POS>
            <Guest Code="10" Age="30">
                <GuestTransportation Mode="29" Status="36">
                    <GatewayCity LocationCode="C/O"/>
                </GuestTransportation>
            </Guest>
            <GuestCounts>
                <GuestCount Age="30" Quantity="1"/>
            </GuestCounts>
            <SailingInfo>
                <SelectedSailing ListOfSailingDescriptionCode="6" Start="2021-02-07" Duration="P7N" Status="36" PortsOfCallQuantity="5">
                    <CruiseLine VendorCode="PUL" ShipCode="HR"/>
                    <!--Optional:-->
                    <Region RegionCode="PDYDO" SubRegionCode="PGS"/>
                    <!--Optional:-->
                    <DeparturePort LocationCode="DXB"/>
                    <!--Optional:-->
                    <ArrivalPort LocationCode="DXB"/>
                </SelectedSailing>
                <InclusivePackageOption CruisePackageCode="HRPO0720"/>
                <Currency CurrencyCode="USD" DecimalPlaces="2"/>
                <SelectedCategory BerthedCategoryCode="GS" PricedCategoryCode="GS" WaitlistIndicator="false">
                </SelectedCategory>
            </SailingInfo>
            <SearchQualifiers BerthedCategoryCode="GS" FareCode="F9368551" GroupCode="1" CategoryLocation="Deluxe">
                <Status Status="36"/>
            </SearchQualifiers>
            <SelectedFare GroupCode="1"/>
        </OTA_CruiseCabinAvailRQ>
    </cab:getCabinList>
</soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
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
$config = new \Zend\Config\Config(include '../config/autoload/global.rccl.php');
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
$getCabinListResponse = $Body->item(0)->getElementsByTagName("getCabinListResponse");
if ($getCabinListResponse->length > 0) {
    $OTA_CruiseCabinAvailRS = $getCabinListResponse->item(0)->getElementsByTagName("OTA_CruiseCabinAvailRS");
    if ($OTA_CruiseCabinAvailRS->length > 0) {
        $SailingInfo = $OTA_CruiseCabinAvailRS->item(0)->getElementsByTagName("SailingInfo");
        if ($SailingInfo->length > 0) {
            $SelectedSailing = $SailingInfo->item(0)->getElementsByTagName("SelectedSailing");
            if ($SelectedSailing->length > 0) {
                $Start = $SelectedSailing->item(0)->getAttribute("Start");
                $Duration = $SelectedSailing->item(0)->getAttribute("Duration");

                $CruiseLine = $SelectedSailing->item(0)->getElementsByTagName("CruiseLine");
                if ($CruiseLine->length > 0) {
                    $ShipCode = $CruiseLine->item(0)->getAttribute("ShipCode");
                    $VendorCode = $CruiseLine->item(0)->getAttribute("VendorCode");
                }
                $Region = $SelectedSailing->item(0)->getElementsByTagName("Region");
                if ($Region->length > 0) {
                    $RegionCode = $Region->item(0)->getAttribute("RegionCode");
                    $SubRegionCode = $Region->item(0)->getAttribute("SubRegionCode");
                }
            }
            $InclusivePackageOption = $SailingInfo->item(0)->getElementsByTagName("InclusivePackageOption");
            if ($InclusivePackageOption->length > 0) {
                $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
                $InclusiveIndicator = $InclusivePackageOption->item(0)->getAttribute("InclusiveIndicator");
            }
        }
        //SelectedFare
        $SelectedFare = $OTA_CruiseCabinAvailRS->item(0)->getElementsByTagName("SelectedFare");
        if ($SelectedFare->length > 0) {
            $FareCode = $SelectedFare->item(0)->getAttribute("FareCode");
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cabinlist');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'start' => $Start,
                'duration' => $Duration,
                'shipcode' => $ShipCode,
                'vendorcode' => $VendorCode,
                'regioncode' => $RegionCode,
                'subregioncode' => $SubRegionCode,
                'cruisepackagecode' => $CruisePackageCode,
                'inclusiveindicator' => $InclusiveIndicator,
                'farecode' => $FareCode
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

        //CabinOptions
        $CabinOptions = $OTA_CruiseCabinAvailRS->item(0)->getElementsByTagName("CabinOptions");
        if ($CabinOptions->length > 0) {
            $CabinOption = $CabinOptions->item(0)->getElementsByTagName("CabinOption");
            if ($CabinOption->length > 0) {
                for ($i=0; $i < $CabinOption->length; $i++) { 
                    $CabinCategoryCode = $CabinOption->item($i)->getAttribute("CabinCategoryCode");
                    $CabinNumber = $CabinOption->item($i)->getAttribute("CabinNumber");
                    $CabinRanking = $CabinOption->item($i)->getAttribute("CabinRanking");
                    $DeckName = $CabinOption->item($i)->getAttribute("DeckName");
                    $DeckNumber = $CabinOption->item($i)->getAttribute("DeckNumber");
                    $MaxOccupancy = $CabinOption->item($i)->getAttribute("MaxOccupancy");
                    $PositionInShip = $CabinOption->item($i)->getAttribute("PositionInShip");
                    $Status = $CabinOption->item($i)->getAttribute("Status");
                    $Remark = $CabinOption->item($i)->getElementsByTagName("Remark");
                    if ($Remark->length > 0) {
                        $Remark = $Remark->item(0)->nodeValue;
                    } else {
                        $Remark = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('cabinlist_cabinoptions');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'cabincategorycode' => $CabinCategoryCode,
                            'cabinnumber' => $CabinNumber,
                            'cabinranking' => $CabinRanking,
                            'deckname' => $DeckName,
                            'decknumber' => $DeckNumber,
                            'maxoccupancy' => $MaxOccupancy,
                            'positioninship' => $PositionInShip,
                            'status' => $Status,
                            'remark' => $Remark
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

                    $CabinConfiguration = $CabinOption->item($i)->getElementsByTagName("CabinConfiguration");
                    if ($CabinConfiguration->length > 0) {
                        for ($iAux3=0; $iAux3 < $CabinConfiguration->length; $iAux3++) { 
                            $BedConfigurationCode = $CabinConfiguration->item($iAux3)->getAttribute("BedConfigurationCode");
                            if ($iAux3 == 1) {
                                $TPA_ViewObstruction = $CabinConfiguration->item($iAux3)->getAttribute("TPA_ViewObstruction");
                            }

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('cabinlist_cabinoption_cabinconfiguration');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'bedconfigurationcode' => $BedConfigurationCode,
                                    'tpa_viewobstruction' => $TPA_ViewObstruction
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO 3: " . $e;
                                echo $return;
                            }
                        }
                    }
                    $MeasurementInfo = $CabinOption->item($i)->getElementsByTagName("MeasurementInfo");
                    if ($MeasurementInfo->length > 0) {
                        for ($iAux=0; $iAux < $MeasurementInfo->length; $iAux++) { 
                            $Name = $MeasurementInfo->item($iAux)->getAttribute("Name");
                            $UnitOfMeasure = $MeasurementInfo->item($iAux)->getAttribute("UnitOfMeasure");
                            $UnitOfMeasureCode = $MeasurementInfo->item($iAux)->getAttribute("UnitOfMeasureCode");
                            $UnitOfMeasureQuantity = $MeasurementInfo->item($iAux)->getAttribute("UnitOfMeasureQuantity");

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('cabinlist_cabinoption_measurementinfo');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name,
                                    'unitofmeasure' => $UnitOfMeasure,
                                    'unitofmeasurecode' => $UnitOfMeasureCode,
                                    'unitofmeasurequantity' => $UnitOfMeasureQuantity
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO 4: " . $e;
                                echo $return;
                            }
                        }
                    }
                    $CabinFilters = $CabinOption->item($i)->getElementsByTagName("CabinFilters");
                    if ($CabinFilters->length > 0) {
                        $CabinFilter = $CabinFilters->item(0)->getElementsByTagName("CabinFilter");
                        if ($CabinFilter->length > 0) {
                            for ($iAux2=0; $iAux2 < $CabinFilter->length; $iAux2++) { 
                                $CabinFilterCode = $CabinFilter->item($iAux2)->getAttribute("CabinFilterCode");

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('cabinlist_cabinoption_cabinfilters');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'cabinfiltercode' => $CabinFilterCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 5: " . $e;
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