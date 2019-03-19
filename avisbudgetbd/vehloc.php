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
echo "COMECOU VEHLOC";
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
        <OTA_VehLocSearchRQ xmlns:xsi="http://www.w3.org/2008/XMLSchema-instance" MaxResponses="1" Version="1.0">
        <POS>
            <Source/>
        </POS>
        <VehLocSearchCriterion>
            <Address>
                <AddressLine>305 Federal Circle</AddressLine>
                <CityName>Jamaica</CityName>
                <PostalCode>11430</PostalCode>
                <County>USA</County>
                <StateProv StateCode="NY"/>
                <CountryName Code="US"/>
            </Address>
            <Radius DistanceMax="40" DistanceMeasure="Miles"/>
        </VehLocSearchCriterion>
        <Vendor Code="Avis"/>
        <TPA_Extensions>
            <SortOrderType>DESCENDING</SortOrderType>
            <TestLocationType>NO</TestLocationType>
            <LocationStatusType>OPEN</LocationStatusType>
            <LocationType>RENTAL</LocationType>
        </TPA_Extensions>
        </OTA_VehLocSearchRQ>
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
$OTA_VehLocSearchRS = $Response->item(0)->getElementsByTagName("OTA_VehLocSearchRS");
$VehMatchedLocs = $OTA_VehLocSearchRS->item(0)->getElementsByTagName("VehMatchedLocs");
$VehMatchedLoc = $VehMatchedLocs->item(0)->getElementsByTagName("VehMatchedLoc");
//VehRentalCore
$VehLocSearchCriterion = $VehMatchedLoc->item(0)->getElementsByTagName("VehLocSearchCriterion");
if ($VehLocSearchCriterion->length > 0) {
    $Position = $VehLocSearchCriterion->item(0)->getElementsByTagName("Position");
    if ($Position->length > 0) {
        $Longitude = $Position->item(0)->getAttribute("Longitude");
        $Latitude = $Position->item(0)->getAttribute("Latitude");
    }

    $Radius = $VehLocSearchCriterion->item(0)->getElementsByTagName("Radius");
    if ($Radius->length > 0) {
        $DistanceMax = $Radius->item(0)->getAttribute("DistanceMax");
        $DistanceMeasure = $Radius->item(0)->getAttribute("DistanceMeasure");
        $Distance = $Radius->item(0)->getAttribute("Distance");
    }
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('vehloc_searchCriterion');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'Longitude' => $Longitude,
            'Latitude' => $Latitude,
            'DistanceMax' => $DistanceMax,
            'DistanceMeasure' => $DistanceMeasure,
            'Distance' => $Distance
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO SEARCH: " . $e;
        echo $return;
    }
}

//LocationDetail
$LocationDetail = $VehMatchedLoc->item(0)->getElementsByTagName("LocationDetail");
if ($LocationDetail->length > 0) {
    $Code = $LocationDetail->item(0)->getAttribute("Code");
    $Name = $LocationDetail->item(0)->getAttribute("Name");
    $AtAirport = $LocationDetail->item(0)->getAttribute("AtAirport");

    $Address = $LocationDetail->item(0)->getElementsByTagName("Address");
    if ($Address->length > 0) {
        $CityName = $Address->item(0)->getElementsByTagName('CityName');
        if ($CityName->length > 0) {
            $CityName = $CityName->item(0)->nodeValue;
        } else {
            $CityName = "";
        }
        $PostalCode = $Address->item(0)->getElementsByTagName('PostalCode');
        if ($PostalCode->length > 0) {
            $PostalCode = $PostalCode->item(0)->nodeValue;
        } else {
            $PostalCode = "";
        }

        $StateProv = $Address->item(0)->getElementsByTagName('StateProv');
        if ($StateProv->length > 0) {
            $StateCode = $StateProv->item(0)->getAttribute("StateCode");
        } else {
            $StateCode = "";
        }

        $CountryName = $Address->item(0)->getElementsByTagName('CountryName');
        if ($CountryName->length > 0) {
            $CountryCode = $CountryName->item(0)->getAttribute("Code");
        } else {
            $CountryCode = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('vehloc');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Code' => $Code,
                'Name' => $Name,
                'AtAirport' => $AtAirport,
                'CityName' => $CityName,
                'PostalCode' => $PostalCode,
                'StateCode' => $StateCode,
                'CountryCode' => $CountryCode
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO LOC: " . $e;
            echo $return;
        }

        $AddressLine2 = "";
        $AddressLine = $Address->item(0)->getElementsByTagName('AddressLine');
        if ($AddressLine->length > 0) {
            for ($i=0; $i < $AddressLine->length; $i++) { 
                $AddressLine2 = $AddressLine->item($i)->nodeValue;

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('vehloc_AddressLine');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'AddressLine' => $AddressLine2,
                        'CityName' => $CityName
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO ADDRESS: " . $e;
                    echo $return;
                }
            }
        }
    }

    $Telephone = $LocationDetail->item(0)->getElementsByTagName("Telephone");
    if ($Telephone->length > 0) {
        for ($j=0; $j < $Telephone->length; $j++) { 
            $PhoneNumber = $Telephone->item($j)->getAttribute("PhoneNumber");
            $CountryAccessCode = $Telephone->item($j)->getAttribute("CountryAccessCode");
            $PhoneTechType = $Telephone->item($j)->getAttribute("PhoneTechType");
            $PhoneLocationType = $Telephone->item($j)->getAttribute("PhoneLocationType");

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('vehloc_Telephone');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'PhoneNumber' => $PhoneNumber,
                    'CountryAccessCode' => $CountryAccessCode,
                    'PhoneTechType' => $PhoneTechType,
                    'PhoneLocationType' => $PhoneLocationType
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO TELF: " . $e;
                echo $return;
            }
        }
    }

    $AdditionalInfo = $LocationDetail->item(0)->getElementsByTagName("AdditionalInfo");
    if ($AdditionalInfo->length > 0) {
        $TPA_Extensions = $AdditionalInfo->item(0)->getElementsByTagName("TPA_Extensions");
        if ($TPA_Extensions->length > 0) {
            $AirportCityCode = $TPA_Extensions->item(0)->getElementsByTagName('AirportCityCode');
            if ($AirportCityCode->length > 0) {
                $AirportCityCode = $AirportCityCode->item(0)->nodeValue;
            } else {
                $AirportCityCode = "";
            }
            $AirportCitySequenceNumber = $TPA_Extensions->item(0)->getElementsByTagName('AirportCitySequenceNumber');
            if ($AirportCitySequenceNumber->length > 0) {
                $AirportCitySequenceNumber = $AirportCitySequenceNumber->item(0)->nodeValue;
            } else {
                $AirportCitySequenceNumber = "";
            }
            $LDBMnemonic = $TPA_Extensions->item(0)->getElementsByTagName('LDBMnemonic');
            if ($LDBMnemonic->length > 0) {
                $LDBMnemonic = $LDBMnemonic->item(0)->nodeValue;
            } else {
                $LDBMnemonic = "";
            }
            $LDBNumber = $TPA_Extensions->item(0)->getElementsByTagName('LDBNumber');
            if ($LDBNumber->length > 0) {
                $LDBNumber = $LDBNumber->item(0)->nodeValue;
            } else {
                $LDBNumber = "";
            }
            $FleetOwnerLDBNumber = $TPA_Extensions->item(0)->getElementsByTagName('FleetOwnerLDBNumber');
            if ($FleetOwnerLDBNumber->length > 0) {
                $FleetOwnerLDBNumber = $FleetOwnerLDBNumber->item(0)->nodeValue;
            } else {
                $FleetOwnerLDBNumber = "";
            }
            $LocationType = $TPA_Extensions->item(0)->getElementsByTagName('LocationType');
            if ($LocationType->length > 0) {
                $LocationType = $LocationType->item(0)->nodeValue;
            } else {
                $LocationType = "";
            }
            $LicenseeType = $TPA_Extensions->item(0)->getElementsByTagName('LicenseeType');
            if ($LicenseeType->length > 0) {
                $LicenseeType = $LicenseeType->item(0)->nodeValue;
            } else {
                $LicenseeType = "";
            }
            $LocationStatusType = $TPA_Extensions->item(0)->getElementsByTagName('LocationStatusType');
            if ($LocationStatusType->length > 0) {
                $LocationStatusType = $LocationStatusType->item(0)->nodeValue;
            } else {
                $LocationStatusType = "";
            }
            $DbrLocationCode = $TPA_Extensions->item(0)->getElementsByTagName('DbrLocationCode');
            if ($DbrLocationCode->length > 0) {
                $DbrLocationCode = $DbrLocationCode->item(0)->nodeValue;
            } else {
                $DbrLocationCode = "";
            }
            $TestLocationType = $TPA_Extensions->item(0)->getElementsByTagName('TestLocationType');
            if ($TestLocationType->length > 0) {
                $TestLocationType = $TestLocationType->item(0)->nodeValue;
            } else {
                $TestLocationType = "";
            }
            $PreferredServiceType = $TPA_Extensions->item(0)->getElementsByTagName('PreferredServiceType');
            if ($PreferredServiceType->length > 0) {
                $PreferredServiceType = $PreferredServiceType->item(0)->nodeValue;
            } else {
                $PreferredServiceType = "";
            }
            $InternationalDivisionCodeType = $TPA_Extensions->item(0)->getElementsByTagName('InternationalDivisionCodeType');
            if ($InternationalDivisionCodeType->length > 0) {
                $InternationalDivisionCodeType = $InternationalDivisionCodeType->item(0)->nodeValue;
            } else {
                $InternationalDivisionCodeType = "";
            }
            $RegionalCode = $TPA_Extensions->item(0)->getElementsByTagName('RegionalCode');
            if ($RegionalCode->length > 0) {
                $RegionalCode = $RegionalCode->item(0)->nodeValue;
            } else {
                $RegionalCode = "";
            }
            $RegionNo = $TPA_Extensions->item(0)->getElementsByTagName('RegionNo');
            if ($RegionNo->length > 0) {
                $RegionNo = $RegionNo->item(0)->nodeValue;
            } else {
                $RegionNo = "";
            }
            $LatLongSourceType = $TPA_Extensions->item(0)->getElementsByTagName('LatLongSourceType');
            if ($LatLongSourceType->length > 0) {
                $LatLongSourceType = $LatLongSourceType->item(0)->nodeValue;
            } else {
                $LatLongSourceType = "";
            }
            $StationSiteCodeType = $TPA_Extensions->item(0)->getElementsByTagName('StationSiteCodeType');
            if ($StationSiteCodeType->length > 0) {
                $StationSiteCodeType = $StationSiteCodeType->item(0)->nodeValue;
            } else {
                $StationSiteCodeType = "";
            }
            $ContactName = $TPA_Extensions->item(0)->getElementsByTagName('ContactName');
            if ($ContactName->length > 0) {
                $ContactName = $ContactName->item(0)->nodeValue;
            } else {
                $ContactName = "";
            }
            $LocID = $TPA_Extensions->item(0)->getElementsByTagName('LocID');
            if ($LocID->length > 0) {
                $LocID = $LocID->item(0)->nodeValue;
            } else {
                $LocID = "";
            }
            $WireLocationType = $TPA_Extensions->item(0)->getElementsByTagName('WireLocationType');
            if ($WireLocationType->length > 0) {
                $WireLocationType = $WireLocationType->item(0)->nodeValue;
            } else {
                $WireLocationType = "";
            }
            $AutonationIndType = $TPA_Extensions->item(0)->getElementsByTagName('AutonationIndType');
            if ($AutonationIndType->length > 0) {
                $AutonationIndType = $AutonationIndType->item(0)->nodeValue;
            } else {
                $AutonationIndType = "";
            }
            $SelfServiceInd = $TPA_Extensions->item(0)->getElementsByTagName('SelfServiceInd');
            if ($SelfServiceInd->length > 0) {
                $SelfServiceInd = $SelfServiceInd->item(0)->nodeValue;
            } else {
                $SelfServiceInd = "";
            }
            $SecureLotInd = $TPA_Extensions->item(0)->getElementsByTagName('SecureLotInd');
            if ($SecureLotInd->length > 0) {
                $SecureLotInd = $SecureLotInd->item(0)->nodeValue;
            } else {
                $SecureLotInd = "";
            }
            $TruckIndicator = $TPA_Extensions->item(0)->getElementsByTagName('TruckIndicator');
            if ($TruckIndicator->length > 0) {
                $TruckIndicator = $TruckIndicator->item(0)->nodeValue;
            } else {
                $TruckIndicator = "";
            }
            $DotComLocationType = $TPA_Extensions->item(0)->getElementsByTagName('DotComLocationType');
            if ($DotComLocationType->length > 0) {
                $DotComLocationType = $DotComLocationType->item(0)->nodeValue;
            } else {
                $DotComLocationType = "";
            }
            $DistanceFromMapOrigin = $TPA_Extensions->item(0)->getElementsByTagName('DistanceFromMapOrigin');
            if ($DistanceFromMapOrigin->length > 0) {
                $DistanceFromMapOrigin = $DistanceFromMapOrigin->item(0)->nodeValue;
            } else {
                $DistanceFromMapOrigin = "";
            }
            $ConsolidatedHours = $TPA_Extensions->item(0)->getElementsByTagName('ConsolidatedHours');
            if ($ConsolidatedHours->length > 0) {
                $ConsolidatedHours = $ConsolidatedHours->item(0)->nodeValue;
            } else {
                $ConsolidatedHours = "";
            }
            $GDSLocationCode = $TPA_Extensions->item(0)->getElementsByTagName('GDSLocationCode');
            if ($GDSLocationCode->length > 0) {
                $GDSLocationCode = $GDSLocationCode->item(0)->nodeValue;
            } else {
                $GDSLocationCode = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('vehloc_TPA_Extensions');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'AirportCityCode' => $AirportCityCode,
                    'AirportCitySequenceNumber' => $AirportCitySequenceNumber,
                    'LDBMnemonic' => $LDBMnemonic,
                    'LDBNumber' => $LDBNumber,
                    'FleetOwnerLDBNumber' => $FleetOwnerLDBNumber,
                    'LocationType' => $LocationType,
                    'LicenseeType' => $LicenseeType,
                    'LocationStatusType' => $LocationStatusType,
                    'DbrLocationCode' => $DbrLocationCode,
                    'TestLocationType' => $TestLocationType,
                    'PreferredServiceType' => $PreferredServiceType,
                    'InternationalDivisionCodeType' => $InternationalDivisionCodeType,
                    'RegionalCode' => $RegionalCode,
                    'RegionNo' => $RegionNo,
                    'LatLongSourceType' => $LatLongSourceType,
                    'StationSiteCodeType' => $StationSiteCodeType,
                    'ContactName' => $ContactName,
                    'LocID' => $LocID,
                    'WireLocationType' => $WireLocationType,
                    'AutonationIndType' => $AutonationIndType,
                    'SelfServiceInd' => $SelfServiceInd,
                    'SecureLotInd' => $SecureLotInd,
                    'TruckIndicator' => $TruckIndicator,
                    'DotComLocationType' => $DotComLocationType,
                    'DistanceFromMapOrigin' => $DistanceFromMapOrigin,
                    'ConsolidatedHours' => $ConsolidatedHours,
                    'GDSLocationCode' => $GDSLocationCode
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO EXT: " . $e;
                echo $return;
            }
        }

        $OperationSchedules = $AdditionalInfo->item(0)->getElementsByTagName("OperationSchedules");
        if ($OperationSchedules->length > 0) {
            $OperationSchedule = $OperationSchedules->item(0)->getElementsByTagName("OperationSchedule");
            if ($OperationSchedule->length > 0) {
                for ($k=0; $k < $OperationSchedule->length; $k++) { 
                    $Start = $OperationSchedule->item($k)->getAttribute("Start");
                    $End = $OperationSchedule->item($k)->getAttribute("End");

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('vehloc_OperationSchedule');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Start' => $Start,
                            'End' => $End
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO SCH: " . $e;
                        echo $return;
                    }

                    $r = 0;
                    $week_day = "";
                    $OperationTimes = $OperationSchedule->item($k)->getElementsByTagName("OperationTimes");
                    if ($OperationTimes->length > 0) {
                        $OperationTime = $OperationTimes->item(0)->getElementsByTagName("OperationTime");
                        if ($OperationTime->length > 0) {
                            for ($t=0; $t < $OperationTime->length; $t++) { 
                                $Start = $OperationTime->item($t)->getAttribute("Start");
                                $End = $OperationTime->item($t)->getAttribute("End");

                                if ($r == 0) {
                                    $week_day = $OperationTime->item($t)->getAttribute("Sun");
                                } else if ($r == 1) {
                                    $week_day = $OperationTime->item($t)->getAttribute("Mon");
                                } else if ($r == 2) {
                                    $week_day = $OperationTime->item($t)->getAttribute("Tue");
                                } else if ($r == 3) {
                                    $week_day = $OperationTime->item($t)->getAttribute("Weds");
                                } else if ($r == 4) {
                                    $week_day = $OperationTime->item($t)->getAttribute("Thur");
                                } else if ($r == 5) {
                                    $week_day = $OperationTime->item($t)->getAttribute("Fri");
                                } else if ($r == 6) {
                                    $week_day = $OperationTime->item($t)->getAttribute("Sat");
                                }
                                $r = $r + 1;
                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('vehloc_OperationTime');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'Start' => $Start,
                                        'End' => $End,
                                        'week_day' => $week_day
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO TIME: " . $e;
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
echo 'Done';
?>