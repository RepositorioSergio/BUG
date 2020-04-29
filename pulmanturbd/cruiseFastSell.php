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
echo "COMECOU CRUISE FAST SELL<br/>";
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

$url = "https://stage.services.rccl.com/";

$raw ='<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<m:fastSell xmlns:m="http://services.rccl.com/Interfaces/FastSell">
    <OTA_CruiseFastSellRQ TimeStamp="2011-06-29T07:48:43" Target="Test" Version="2.0" SequenceNmbr="1" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
        <POS>
            <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
                <RequestorID ID="u73ecKBu73ecKB!" ID_Context="CONCTMM" Type="5"/>
                <BookingChannel Type="7">
                    <CompanyName CompanyShortName="IST"/>
                </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
                <RequestorID ID="u73ecKBu73ecKB!" ID_Context="CONCTMM" Type="5"/>
                <BookingChannel Type="7">
                    <CompanyName CompanyShortName="IST"/>
                </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
                <RequestorID ID="u73ecKBu73ecKB!" ID_Context="CONCTMM" Type="5"/>
                <BookingChannel Type="7">
                    <CompanyName CompanyShortName="IST"/>
                </BookingChannel>
            </Source>
        </POS>
        <GuestCounts>
            <GuestCount Quantity="1"/>
        </GuestCounts>
        <SelectedSailing Start=”2011-09-02" Duration="P7N" VendorCode="RCC" ShipCode="RH">
            <SelectedFare FareCode="BRKA01" />
            <SelectedCategory BerthedCategoryCode="M" PricedCategoryCode="M">
            <SelectedCabin CabinNumber="7126"/>
            <SelectedCabin CabinNumber="7127"/>
            <SelectedCabin CabinNumber="7128"/>
            <SelectedCabin CabinNumber="7129"/>
            </SelectedCategory>
        </SelectedSailing>
        <Guest>
            <GuestTransportation Mode="29" Status="36">
            <GuestCity LocationCode="DFW"/>
            </GuestTransportation>
        </Guest>
        <Guest>
            <GuestTransportation Mode="29" Status="36">
            <GuestCity LocationCode="DFW"/>
            </GuestTransportation>
        </Guest>
    </OTA_CruiseFastSellRQ>
</m:fastSell>
</soap:Body>
</soap:Envelope>';

/* $client->setUri($url);
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
} */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
if ($response === false) {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
} else {
    echo $return;
    echo "Operation completed without any errors";
    echo $return;
}
curl_close($ch);

echo "<br/>RESPONSE";
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
$fastSellResponse = $Body->item(0)->getElementsByTagName("fastSellResponse");
$OTA_CruiseCabinAvailRS = $fastSellResponse->item(0)->getElementsByTagName("OTA_CruiseCabinAvailRS");

$SailingInfo = $OTA_CruiseCabinAvailRS->item(0)->getElementsByTagName("SailingInfo");
if ($SailingInfo->length > 0) {
    $SelectedSailing = $SailingInfo->item(0)->getElementsByTagName("SelectedSailing");
    if ($SelectedSailing->length > 0) {
        $Start = $SelectedSailing->item(0)->getAttribute("Start");
        $Duration = $SelectedSailing->item(0)->getAttribute("Duration");

        $CruiseLine = $SelectedSailing->item(0)->getElementsByTagName("CruiseLine");
        if ($CruiseLine->length > 0) {
            $ShipCode = $CruiseLine->item(0)->getAttribute("ShipCode");
        } else {
            $ShipCode = "";
        }
        
        $Region = $SelectedSailing->item(0)->getElementsByTagName("Region");
        if ($Region->length > 0) {
            $RegionCode = $Region->item(0)->getAttribute("RegionCode");
        } else {
            $RegionCode = "";
        }
    }

    $InclusivePackageOption = $SailingInfo->item(0)->getElementsByTagName("InclusivePackageOption");
    if ($InclusivePackageOption->length > 0) {
        $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
    }
}

$SelectedFare = $OTA_CruiseCabinAvailRS->item(0)->getElementsByTagName("SelectedFare");
if ($SelectedFare->length > 0) {
    $FareCode = $SelectedFare->item(0)->getAttribute("FareCode");
}

$remark2 = "";
$CabinOptions = $OTA_CruiseCabinAvailRS->item(0)->getElementsByTagName("CabinOptions");
if ($CabinOptions->length > 0) {
    $CabinOption = $CabinOptions->item(0)->getElementsByTagName("CabinOption");
    if ($CabinOption->length > 0) {
        $Status = $CabinOption->item(0)->getAttribute("Status");
        $PositionInShip = $CabinOption->item(0)->getAttribute("PositionInShip");
        $MaxOccupancy = $CabinOption->item(0)->getAttribute("MaxOccupancy");
        $DeckNumber = $CabinOption->item(0)->getAttribute("DeckNumber");
        $DeckName = $CabinOption->item(0)->getAttribute("DeckName");
        $ConnectingCabinIndicator = $CabinOption->item(0)->getAttribute("ConnectingCabinIndicator");
        $CabinRanking = $CabinOption->item(0)->getAttribute("CabinRanking");
        $CabinNumber = $CabinOption->item(0)->getAttribute("CabinNumber");
        $CabinCategoryCode = $CabinOption->item(0)->getAttribute("CabinCategoryCode");

        $Remark = $CabinOption->item(0)->getElementsByTagName("Remark");
        if ($Remark->length > 0) {
            $remark2 = $Remark->item(0)->nodeValue;
        } else {
            $remark2 = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('fastSell');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Start' => $Start,
                'Duration' => $Duration,
                'ShipCode' => $ShipCode,
                'RegionCode' => $RegionCode,
                'CruisePackageCode' => $CruisePackageCode,
                'FareCode' => $FareCode,
                'Status' => $Status,
                'PositionInShip' => $PositionInShip,
                'MaxOccupancy' => $MaxOccupancy,
                'DeckNumber' => $DeckNumber,
                'ConnectingCabinIndicator' => $ConnectingCabinIndicator,
                'CabinRanking' => $CabinRanking,
                'CabinNumber' => $CabinNumber,
                'CabinCategoryCode' => $CabinCategoryCode,
                'Remark' => $remark2
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

        $CabinConfiguration = $CabinOption->item(0)->getElementsByTagName("CabinConfiguration");
        if ($CabinConfiguration->length > 0) {
            for ($i=0; $i < $CabinConfiguration->length; $i++) { 
                $BedConfigurationCode = $CabinConfiguration->item($i)->getAttribute("BedConfigurationCode");

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('cabinConfiguration_fastSell');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'BedConfigurationCode' => $BedConfigurationCode
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

        $MeasurementInfo = $CabinOption->item(0)->getElementsByTagName("MeasurementInfo");
        if ($MeasurementInfo->length > 0) {
            for ($j=0; $j < $MeasurementInfo->length; $j++) { 
                $UnitOfMeasureQuantity = $MeasurementInfo->item($j)->getAttribute("UnitOfMeasureQuantity");
                $UnitOfMeasureCode = $MeasurementInfo->item($j)->getAttribute("UnitOfMeasureCode");
                $UnitOfMeasure = $MeasurementInfo->item($j)->getAttribute("UnitOfMeasure");
                $Name = $MeasurementInfo->item($j)->getAttribute("Name");

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('measurementInfo_fastSell');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'UnitOfMeasureQuantity' => $UnitOfMeasureQuantity,
                        'UnitOfMeasureCode' => $UnitOfMeasureCode,
                        'UnitOfMeasure' => $UnitOfMeasure,
                        'Name' => $Name
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

        $CabinFilters = $CabinOption->item(0)->getElementsByTagName("CabinFilters");
        if ($CabinFilters->length > 0) {
            $CabinFilter = $CabinFilters->item(0)->getElementsByTagName("CabinFilter");
            if ($CabinFilter->length > 0) {
                for ($k=0; $k < $CabinFilter->length; $k++) { 
                    $CabinFilterCode = $CabinFilter->item($k)->getAttribute("CabinFilterCode");

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('cabinFilters_fastSell');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'CabinFilterCode' => $CabinFilterCode
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
        }
        
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>: