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
$sql = "select value from settings where name='enableCarnectCars' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_carnect = $affiliate_id;
} else {
    $affiliate_id_carnect = 0;
}
$sql = "select value from settings where name='CarnectLogin' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectLogin = $row_settings['value'];
}
$sql = "select value from settings where name='CarnectCarspassword' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectCarspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='CarnectCarswebservicesURL' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $CarnectCarswebservicesURL = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Header />
          <soapenv:Body>
 <VehRateRuleRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0"
    ReqRespVersion="large">
        <POS>
              <Source ISOCountry="US">
                <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
              </Source>
            </POS>
<Reference URL="http://www.carhiremarket.com/upsell_parameter.aspx?reference_number=hWbyvQY1&amp;live=true" Type="16" ID_Context="hWbyvQY1" />
        </VehRateRuleRQ>    
      </soapenv:Body>
        </soapenv:Envelope>';
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Accept-Encoding: gzip",
            "Content-length: " . strlen($xml_post_string)
        );
echo $return;
echo $xml_post_string;
echo $return;
        //
        // PHP CURL for https connection with auth
        //
error_reporting(E_ALL);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $CarnectCarswebservicesURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $xmlresult = curl_exec($ch);
        curl_close($ch);
echo '<xmp>';
        echo $xmlresult;
echo "</xmp>";
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$VehRateRuleRS = $Body->item(0)->getElementsByTagName('VehRateRuleRS');

//VehRentalCore
$VehRentalCore = $VehRateRuleRS->item(0)->getElementsByTagName('VehRentalCore');
$CodeContext = $VehRentalCore->item(0)->getAttribute('CodeContext');
$Code = $VehRentalCore->item(0)->getAttribute('Code');
$TravelSector = $VehRentalCore->item(0)->getAttribute('TravelSector');
$CompanyShortName = $VehRentalCore->item(0)->getAttribute('CompanyShortName');
$ReturnDateTime = $VehRentalCore->item(0)->getAttribute('ReturnDateTime');
$PickUpDateTime = $VehRentalCore->item(0)->getAttribute('PickUpDateTime');

//Vehicle
$Vehicle = $VehRateRuleRS->item(0)->getElementsByTagName('Vehicle');
$VehicleCodeContext = $Vehicle->item(0)->getAttribute('CodeContext');
$VehicleCode = $Vehicle->item(0)->getAttribute('Code');
$VendorCarType = $Vehicle->item(0)->getAttribute('VendorCarType');
$BaggageQuantity = $Vehicle->item(0)->getAttribute('BaggageQuantity');
$PassengerQuantity = $Vehicle->item(0)->getAttribute('PassengerQuantity');
$DriveType = $Vehicle->item(0)->getAttribute('DriveType');
$FuelType = $Vehicle->item(0)->getAttribute('FuelType');
$TransmissionType = $Vehicle->item(0)->getAttribute('TransmissionType');
$AirConditionInd = $Vehicle->item(0)->getAttribute('AirConditionInd');
$PictureURL = $Vehicle->item(0)->getElementsByTagName('PictureURL');
if ($PictureURL->length > 0) {
    $PictureURL = $PictureURL->item(0)->nodeValue;
} else {
    $PictureURL = "";
}

$VehType = $Vehicle->item(0)->getElementsByTagName('VehType');
if ($VehType->length > 0) {
    $DoorCount = $VehType->item(0)->getAttribute('DoorCount');
    $VehicleCategory = $VehType->item(0)->getAttribute('VehicleCategory');
}
$VehClass = $Vehicle->item(0)->getElementsByTagName('VehClass');
if ($VehClass->length > 0) {
    $Size = $VehClass->item(0)->getAttribute('Size');
}
$VehMakeModel = $Vehicle->item(0)->getElementsByTagName('VehMakeModel');
if ($VehMakeModel->length > 0) {
    $VehMakeModelCode = $VehMakeModel->item(0)->getAttribute('Code');
    $VehMakeModelName = $VehMakeModel->item(0)->getAttribute('Name');
}

//TotalCharge
$TotalCharge = $VehRateRuleRS->item(0)->getElementsByTagName('TotalCharge');
$CurrencyCode = $TotalCharge->item(0)->getAttribute('CurrencyCode');
$EstimatedTotalAmount = $TotalCharge->item(0)->getAttribute('EstimatedTotalAmount');

//RateRules
$RateRules = $VehRateRuleRS->item(0)->getElementsByTagName('RateRules');
if ($RateRules->length > 0) {
    $PaymentRules = $RateRules->item(0)->getElementsByTagName('PaymentRules');
    if ($PaymentRules->length > 0) {
        $PaymentRule = $RateRules->item(0)->getElementsByTagName('PaymentRule');
        if ($PaymentRule->length > 0) {
            $PaymentType = $PaymentRule->item(0)->getAttribute('PaymentType');
            $PaymentRule = $PaymentRule->item(0)->nodeValue;
        } else {
            $PaymentType = "";
            $PaymentRule = "";
        }
    }
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('raterules');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'CodeContext' => $CodeContext,
        'Code' => $Code,
        'TravelSector' => $TravelSector,
        'CompanyShortName' => $CompanyShortName,
        'ReturnDateTime' => $ReturnDateTime,
        'PickUpDateTime' => $PickUpDateTime,
        'VehicleCodeContext' => $VehicleCodeContext,
        'VehicleCode' => $VehicleCode,
        'VendorCarType' => $VendorCarType,
        'BaggageQuantity' => $BaggageQuantity,
        'PassengerQuantity' => $PassengerQuantity,
        'DriveType' => $DriveType,
        'FuelType' => $FuelType,
        'TransmissionType' => $TransmissionType,
        'AirConditionInd' => $AirConditionInd,
        'PictureURL' => $PictureURL,
        'DoorCount' => $DoorCount,
        'VehicleCategory' => $VehicleCategory,
        'Size' => $Size,
        'VehMakeModelCode' => $VehMakeModelCode,
        'VehMakeModelName' => $VehMakeModelName,
        'CurrencyCode' => $CurrencyCode,
        'EstimatedTotalAmount' => $EstimatedTotalAmount,
        'PaymentType' => $PaymentType,
        'PaymentRule' => $PaymentRule
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "Exception RE: " . $e;
    echo $return;
}

//Fees
$Fees = $VehRateRuleRS->item(0)->getElementsByTagName('Fees');
if ($Fees->length > 0) {
    $Fee = $Fees->item(0)->getElementsByTagName('Fee');
    if ($Fee->length > 0) {
        for ($i=0; $i < $Fee->length; $i++) { 
            $TaxInclusive = $Fee->item($i)->getAttribute('TaxInclusive');
            $Amount = $Fee->item($i)->getAttribute('Amount');
            $CurrencyCode = $Fee->item($i)->getAttribute('CurrencyCode');
            $IncludedInEstTotalInd = $Fee->item($i)->getAttribute('IncludedInEstTotalInd');
            $Description = $Fee->item($i)->getAttribute('Description');
            $IncludedInRate = $Fee->item($i)->getAttribute('IncludedInRate');

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('raterules_Fee');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'TaxInclusive' => $TaxInclusive,
                    'Amount' => $Amount,
                    'CurrencyCode' => $CurrencyCode,
                    'IncludedInEstTotalInd' => $IncludedInEstTotalInd,
                    'Description' => $Description,
                    'IncludedInRate' => $IncludedInRate
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Exception: " . $e;
                echo $return;
            }
        }
    }
}

//PricedCoverages
$PricedCoverages = $VehRateRuleRS->item(0)->getElementsByTagName('PricedCoverages');
if ($PricedCoverages->length > 0) {
    $PricedCoverage = $PricedCoverages->item(0)->getElementsByTagName('PricedCoverage');
    if ($PricedCoverage->length > 0) {
        for ($j=0; $j < $PricedCoverage->length; $j++) { 
            $Coverage = $PricedCoverage->item($j)->getElementsByTagName('Coverage');
            if ($Coverage->length > 0) {
                $CoverageCode = $Coverage->item(0)->getAttribute('Code');
                $CoverageType = $Coverage->item(0)->getAttribute('CoverageType');

                $Details = $Coverage->item(0)->getElementsByTagName('Details');
                if ($Details->length > 0) {
                    $Charge = $Details->item(0)->getElementsByTagName('Charge');
                    if ($Charge->length > 0) {
                        $Amount = $Charge->item(0)->getAttribute('Amount');
                        $IncludedInRate = $Charge->item(0)->getAttribute('IncludedInRate');
                    }
                }
            }

            $Charge = $PricedCoverage->item($j)->getElementsByTagName('Charge');
            if ($Charge->length > 0) {
                $TaxInclusive = $Charge->item(0)->getAttribute('TaxInclusive');
                $ChargeAmount = $Charge->item(0)->getAttribute('Amount');
                $ChargeCurrencyCode = $Charge->item(0)->getAttribute('CurrencyCode');
                $IncludedInEstTotalInd = $Charge->item(0)->getAttribute('IncludedInEstTotalInd');
                $Description = $Charge->item(0)->getAttribute('Description');
                $ChargeIncludedInRate = $Charge->item(0)->getAttribute('IncludedInRate');
                $GuaranteedInd = $Charge->item(0)->getAttribute('GuaranteedInd');
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('raterules_PricedCoverage');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'CoverageCode' => $CoverageCode,
                    'CoverageType' => $CoverageType,
                    'Amount' => $Amount,
                    'IncludedInRate' => $IncludedInRate,
                    'TaxInclusive' => $TaxInclusive,
                    'ChargeAmount' => $ChargeAmount,
                    'ChargeCurrencyCode' => $ChargeCurrencyCode,
                    'IncludedInEstTotalInd' => $IncludedInEstTotalInd,
                    'Description' => $Description,
                    'ChargeIncludedInRate' => $ChargeIncludedInRate,
                    'GuaranteedInd' => $GuaranteedInd
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Exception PRICE: " . $e;
                echo $return;
            }
        }
    }
}

//LocationDetails
$LocationDetails = $VehRateRuleRS->item(0)->getElementsByTagName('LocationDetails');
if ($LocationDetails->length > 0) {
    for ($k=0; $k < $LocationDetails->length; $k++) { 
        $LocationDetailsCode = $LocationDetails->item($k)->getAttribute('Code');
        $LocationDetailsName = $LocationDetails->item($k)->getAttribute('Name');
        $CodeContext = $LocationDetails->item($k)->getAttribute('CodeContext');
        $ExtendedLocationCode = $LocationDetails->item($k)->getAttribute('ExtendedLocationCode');
        $AtAirport = $LocationDetails->item($k)->getAttribute('AtAirport');

        $Address = $LocationDetails->item($k)->getElementsByTagName('Address');
        if ($Address->length > 0) {
            $Type = $Address->item(0)->getAttribute('Type');

            $StreetNmbr = $Address->item(0)->getElementsByTagName('StreetNmbr');
            if ($StreetNmbr->length > 0) {
                $StreetNmbr = $StreetNmbr->item(0)->nodeValue;
            } else {
                $StreetNmbr = "";
            }
            $AddressLine = $Address->item(0)->getElementsByTagName('AddressLine');
            if ($AddressLine->length > 0) {
                $AddressLine = $AddressLine->item(0)->nodeValue;
            } else {
                $AddressLine = "";
            }
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
                $StateCode = $StateProv->item(0)->getAttribute('StateCode');
                $StateProv = $StateProv->item(0)->nodeValue;
            } else {
                $StateProv = "";
            }
            $CountryName = $Address->item(0)->getElementsByTagName('CountryName');
            if ($CountryName->length > 0) {
                $CountryCode = $CountryName->item(0)->getAttribute('Code');
                $CountryName = $CountryName->item(0)->nodeValue;
            } else {
                $CountryName = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('raterules_LocationDetails');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'LocationDetailsCode' => $LocationDetailsCode,
                    'LocationDetailsName' => $LocationDetailsName,
                    'CodeContext' => $CodeContext,
                    'ExtendedLocationCode' => $ExtendedLocationCode,
                    'AtAirport' => $AtAirport,
                    'Type' => $Type,
                    'StreetNmbr' => $StreetNmbr,
                    'AddressLine' => $AddressLine,
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
                echo "Exception LOC: " . $e;
                echo $return;
            }
        }
        //Telephone
        $Telephone = $LocationDetails->item($k)->getElementsByTagName('Telephone');
        if ($Telephone->length > 0) {
            for ($l=0; $l < $Telephone->length; $l++) { 
                $PhoneNumber = $Telephone->item($l)->getAttribute('PhoneNumber');
                $PhoneTechType = $Telephone->item($l)->getAttribute('PhoneTechType');

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('raterules_Telephone');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'PhoneNumber' => $PhoneNumber,
                        'PhoneTechType' => $PhoneTechType
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Exception TEL: " . $e;
                    echo $return;
                }
            }
        }

        //AdditionalInfo
        $AdditionalInfo = $LocationDetails->item($k)->getElementsByTagName('AdditionalInfo');
        if ($AdditionalInfo->length > 0) {
            $ParkLocation = $AdditionalInfo->item(0)->getElementsByTagName('ParkLocation');
            if ($ParkLocation->length > 0) {
                $Location = $ParkLocation->item(0)->getAttribute('Location');
            }
            //TPA_Extensions
            $TPA_Extensions = $AdditionalInfo->item(0)->getElementsByTagName('TPA_Extensions');
            if ($TPA_Extensions->length > 0) {
                $CityId = $TPA_Extensions->item(0)->getElementsByTagName('CityId');
                if ($CityId->length > 0) {
                    $CityId = $CityId->item(0)->nodeValue;
                } else {
                    $CityId = "";
                }
                $AirportId = $TPA_Extensions->item(0)->getElementsByTagName('AirportId');
                if ($AirportId->length > 0) {
                    $AirportId = $AirportId->item(0)->nodeValue;
                } else {
                    $AirportId = "";
                }
                $Position = $TPA_Extensions->item(0)->getElementsByTagName('Position');
                if ($Position->length > 0) {
                    $LongitudeTPA = $Position->item(0)->getAttribute('Longitude');
                    $LatitudeTPA = $Position->item(0)->getAttribute('Latitude');
                }
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('raterules_AdditionalInfo');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Location' => $Location,
                    'CityId' => $CityId,
                    'AirportId' => $AirportId,
                    'Longitude' => $LongitudeTPA,
                    'Latitude' => $LatitudeTPA
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Exception ADI: " . $e;
                echo $return;
            }

            $OperationSchedules = $AdditionalInfo->item(0)->getElementsByTagName('OperationSchedules');
            if ($OperationSchedules->length > 0) {
                $OperationSchedule = $OperationSchedules->item(0)->getElementsByTagName('OperationSchedule');
                if ($OperationSchedule->length > 0) {
                    $OperationTimes = $OperationSchedule->item(0)->getElementsByTagName('OperationTimes');
                    if ($OperationTimes->length > 0) {
                        $OperationTime = $OperationTimes->item(0)->getElementsByTagName('OperationTime');
                        if ($OperationTime->length > 0) {
                            for ($x=0; $x < $OperationTime->length; $x++) { 
                                $End = $OperationTime->item($x)->getAttribute('End');
                                echo $return;
                                echo "End: " . $End;
                                echo $return;
                                $Start = $OperationTime->item($x)->getAttribute('Start');
                                echo $return;
                                echo "Start: " . $Start;
                                echo $return;
                                if ($OperationTime->item($x)->getAttribute('Mon')) {
                                    $day = "Mon";
                                    echo $return;
                                    echo "day: " . $day;
                                    echo $return;
                                }elseif ($OperationTime->item($x)->getAttribute('Tue')) {
                                    $day = 'Tue';
                                    echo $return;
                                    echo "day: " . $day;
                                    echo $return;
                                }elseif ($OperationTime->item($x)->getAttribute('Weds')) {
                                    $day = 'Weds';
                                    echo $return;
                                    echo "day: " . $day;
                                    echo $return;
                                }elseif ($OperationTime->item($x)->getAttribute('Thur')) {
                                    $day = 'Thur';
                                    echo $return;
                                    echo "day: " . $day;
                                    echo $return;
                                }elseif ($OperationTime->item($x)->getAttribute('Fri')) {
                                    $day = 'Fri';
                                    echo $return;
                                    echo "day: " . $day;
                                    echo $return;
                                }elseif ($OperationTime->item($x)->getAttribute('Sat')) {
                                    $day = 'Sat';
                                    echo $return;
                                    echo "day: " . $day;
                                    echo $return;
                                }else {
                                    $day = 'Sun';
                                    echo $return;
                                    echo "day: " . $day;
                                    echo $return;
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('raterules_operationtime');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'End' => $End,
                                        'Start' => $Start,
                                        'day' => $day
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Exception: " . $e;
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

//VendorMessages
$VendorMessages = $VehRateRuleRS->item(0)->getElementsByTagName('VendorMessages');
if ($VendorMessages->length > 0) {
    $VendorMessage = $VendorMessages->item(0)->getElementsByTagName('VendorMessage');
    if ($VendorMessage->length > 0) {
        $SubSection = $VendorMessage->item(0)->getElementsByTagName('SubSection');
        if ($SubSection->length > 0) {
            $InfoType = $SubSection->item(0)->getAttribute('InfoType');
            $Title = $SubSection->item(0)->getAttribute('Title');

            $Paragraph = $SubSection->item(0)->getElementsByTagName('Paragraph');
            if ($Paragraph->length > 0) {
                $Language = $Paragraph->item(0)->getAttribute('Language');
                $Paragraph = $Paragraph->item(0)->nodeValue;
            } else {
                $Paragraph = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('raterules_VendorMessage');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'InfoType' => $InfoType,
                    'Title' => $Title,
                    'Language' => $Language,
                    'Paragraph' => $Paragraph
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Exception VE: " . $e;
                echo $return;
            }
        }
    }
}


//TPA_Extensions
$TPA_Extensions = $VehRateRuleRS->item(0)->getElementsByTagName("TPA_Extensions");
if ($TPA_Extensions->length > 0) {
    $SupplierLogo = $TPA_Extensions->item(0)->getElementsByTagName("SupplierLogo");
    if ($SupplierLogo->length > 0) {
        $urlSL = $SupplierLogo->item(0)->getAttribute("url");
    } else {
        $urlSL = "";
    }
    $ProductInformation = $TPA_Extensions->item(0)->getElementsByTagName("ProductInformation");
    if ($ProductInformation->length > 0) {
        $temp = $ProductInformation->item(0)->getAttribute("temp");
        $urlPI = $ProductInformation->item(0)->getAttribute("url");
    } else {
        $temp = "";
    }  
    $TermsConditions = $TPA_Extensions->item(0)->getElementsByTagName("TermsConditions");
    if ($TermsConditions->length > 0) {
        $urlTC = $TermsConditions->item(0)->getAttribute("url");
    } else {
        $urlTC = "";
    } 
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('raterules_TPA_Extensions');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'urlSL' => $urlSL,
            'temp' => $temp,
            'urlPI' => $urlPI,
            'urlTC' => $urlTC
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Exception TPA: " . $e;
        echo $return;
    }
}

//RentalRate
$RentalRate = $VehRateRuleRS->item(0)->getElementsByTagName("RentalRate");
if ($RentalRate->length > 0) {
    $RateDistance = $RentalRate->item(0)->getElementsByTagName("RateDistance");
    $DistUnitName = $RateDistance->item(0)->getAttribute("DistUnitName");
    $Unlimited = $RateDistance->item(0)->getAttribute("Unlimited");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('raterules_RateDistance');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'DistUnitName' => $DistUnitName,
            'Unlimited' => $Unlimited
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Exception DIST: " . $e;
        echo $return;
    }

    $VehicleCharges = $RentalRate->item(0)->getElementsByTagName("VehicleCharges");
    if ($VehicleCharges->length > 0) {
        $VehicleCharge = $VehicleCharges->item(0)->getElementsByTagName("VehicleCharge");
        if ($VehicleCharge->length > 0) {
            for ($i=0; $i < $VehicleCharge->length; $i++) { 
                $RateConvertIndVC = $VehicleCharge->item($i)->getAttribute("RateConvertInd");
                $PurposeVC = $VehicleCharge->item($i)->getAttribute("Purpose");
                $TaxInclusiveVC = $VehicleCharge->item($i)->getAttribute("TaxInclusive");
                $AmountVC = $VehicleCharge->item($i)->getAttribute("Amount");
                $CurrencyCodeVC = $VehicleCharge->item($i)->getAttribute("CurrencyCode");
                $IncludedInEstTotalIndVC = $VehicleCharge->item($i)->getAttribute("IncludedInEstTotalInd");
                $DescriptionVC = $VehicleCharge->item($i)->getAttribute("Description");

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('raterules_VehicleCharge');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'RateConvertInd' => $RateConvertIndVC,
                        'Purpose' => $PurposeVC,
                        'TaxInclusive' => $TaxInclusiveVC,
                        'Amount' => $AmountVC,
                        'CurrencyCode' => $CurrencyCodeVC,
                        'IncludedInEstTotalInd' => $IncludedInEstTotalIndVC,
                        'Description' => $DescriptionVC
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Exception DIST: " . $e;
                    echo $return;
                }
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br />Done';
?>
