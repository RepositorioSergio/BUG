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
echo "COMECOU VEHAVAIL";
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
        <OTA_VehAvailRateRQ MaxResponses="100" ReqRespVersion="small" Version="1.0" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_VehAvailRateRQ.xsd">
            <POS>
                <Source>
                    <RequestorID ID="CTMTours" Type="1"/>
                </Source>
                <Source>
                    <RequestorID ID="00000000" Type="5"/>
                </Source>
            </POS>
        <VehAvailRQCore Status="Available">
            <VehRentalCore PickUpDateTime="2019-04-24T12:00:00" ReturnDateTime="2019-04-25T14:00:00">
                <PickUpLocation LocationCode="JFK"/>
                <ReturnLocation LocationCode="JFK"/>
            </VehRentalCore>
            <VendorPrefs>
                <VendorPref CompanyShortName="Avis"/>
            </VendorPrefs>
            <VehPrefs>
               <VehPref AirConditionPref="Preferred" ClassPref="Preferred" TransmissionPref="Preferred" TransmissionType="Automatic" TypePref="Preferred">
                   <VehType VehicleCategory="1" />
                   <VehClass Size="1"/>
               </VehPref>
            </VehPrefs>
          <RateQualifier RateCategory="3" />         
</VehAvailRQCore>
        <VehAvailRQInfo>
            <Customer>
                <Primary>
                    <CitizenCountryName Code="US"/>
                </Primary>
            </Customer>
        </VehAvailRQInfo>
    </OTA_VehAvailRateRQ>
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
    $Vendor = $VehVendorAvails->item(0)->getElementsByTagName('Vendor');
    if ($Vendor->length > 0) {
        $Vendor = $Vendor->item(0)->nodeValue;
    } else {
        $Vendor = "";
    }

    $Info = $VehVendorAvails->item(0)->getElementsByTagName('Info');
    if ($Info->length > 0) {
        $LocationDetails = $Info->item(0)->getElementsByTagName('LocationDetails');
        if ($LocationDetails->length > 0) {
            $Code = $LocationDetails->item(0)->getAttribute("Code");
            $Name = $LocationDetails->item(0)->getAttribute("Name");
            $CodeContext = $LocationDetails->item(0)->getAttribute("CodeContext");
            $ExtendedLocationCode = $LocationDetails->item(0)->getAttribute("ExtendedLocationCode");
            $AtAirport = $LocationDetails->item(0)->getAttribute("AtAirport");

            $Telephone = $LocationDetails->item(0)->getElementsByTagName('Telephone');
            if ($Telephone->length > 0) {
                $PhoneNumber = $Telephone->item(0)->getAttribute("PhoneNumber");
            } else {
                $PhoneNumber = "";
            }

            $Address = $LocationDetails->item(0)->getElementsByTagName('Address');
            if ($Address->length > 0) {
                $StreetNmbr = $Address->item(0)->getElementsByTagName('StreetNmbr');
                if ($StreetNmbr->length > 0) {
                    $StreetNmbr = $StreetNmbr->item(0)->nodeValue;
                } else {
                    $StreetNmbr = "";
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
                    $StateCode = $StateProv->item(0)->getAttribute("StateCode");
                    $StateProv = $StateProv->item(0)->nodeValue;
                } else {
                    $StateCode = "";
                    $StateProv = "";
                }
                $CountryName = $Address->item(0)->getElementsByTagName('CountryName');
                if ($CountryName->length > 0) {
                    $CountryCode = $CountryName->item(0)->getAttribute("Code");
                    $CountryName = $CountryName->item(0)->nodeValue;
                } else {
                    $CountryCode = "";
                    $CountryName = "";
                }
            }
        }
    }

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

    $VehAvails = $VehVendorAvails->item(0)->getElementsByTagName('VehAvails');
    if ($VehAvails->length > 0) {
        $VehAvail = $VehAvails->item(0)->getElementsByTagName('VehAvail');
        if ($VehAvail->length > 0) {
            for ($i=0; $i < $VehAvail->length; $i++) { 
                $VehAvailCore = $VehAvail->item($i)->getElementsByTagName('VehAvailCore');
                if ($VehAvailCore->length > 0) {
                    $Status = $VehAvailCore->item(0)->getAttribute("Status");

                    $Vehicle = $VehAvailCore->item(0)->getElementsByTagName('Vehicle');
                    if ($Vehicle->length > 0) {
                        $TransmissionType = $Vehicle->item(0)->getAttribute("TransmissionType");
                        $AirConditionInd = $Vehicle->item(0)->getAttribute("AirConditionInd");
                        $PictureURL = $Vehicle->item(0)->getElementsByTagName('PictureURL');
                        if ($PictureURL->length > 0) {
                            $PictureURL = $PictureURL->item(0)->nodeValue;
                        } else {
                            $PictureURL = "";
                        }
                        $VehType = $Vehicle->item(0)->getElementsByTagName('VehType');
                        if ($VehType->length > 0) {
                            $VehicleCategory = $VehType->item(0)->getAttribute("VehicleCategory");
                        } else {
                            $VehicleCategory = "";
                        }
                        $VehClass = $Vehicle->item(0)->getElementsByTagName('VehClass');
                        if ($VehClass->length > 0) {
                            $Size = $VehClass->item(0)->getAttribute("Size");
                        } else {
                            $Size = "";
                        }
                        $VehMakeModel = $Vehicle->item(0)->getElementsByTagName('VehMakeModel');
                        if ($VehMakeModel->length > 0) {
                            $Code = $VehMakeModel->item(0)->getAttribute("Code");
                            $Name = $VehMakeModel->item(0)->getAttribute("Name");
                        } else {
                            $Code = "";
                            $Name = "";
                        }
                    } else {
                        $TransmissionType = "";
                        $AirConditionInd = "";
                    }
                } else {
                    $Status = "";
                }

                $TotalCharge = $VehAvailCore->item(0)->getElementsByTagName('TotalCharge');
                if ($TotalCharge->length > 0) {
                    $CurrencyCode = $TotalCharge->item(0)->getAttribute("CurrencyCode");
                    $EstimatedTotalAmount = $TotalCharge->item(0)->getAttribute("EstimatedTotalAmount");
                    $RateTotalAmount = $TotalCharge->item(0)->getAttribute("RateTotalAmount");
                } else {
                    $CurrencyCode = "";
                    $EstimatedTotalAmount = "";
                    $RateTotalAmount = "";
                }

                $RentalRate = $VehAvailCore->item(0)->getElementsByTagName('RentalRate');
                if ($RentalRate->length > 0) {
                    $RateDistance = $RentalRate->item(0)->getElementsByTagName('RateDistance');
                    if ($RateDistance->length > 0) {
                        $VehiclePeriodUnitName = $RateDistance->item(0)->getAttribute("VehiclePeriodUnitName");
                        $DistUnitName = $RateDistance->item(0)->getAttribute("DistUnitName");
                        $Unlimited = $RateDistance->item(0)->getAttribute("Unlimited");
                    } else {
                        $VehiclePeriodUnitName = "";
                        $DistUnitName = "";
                        $Unlimited = "";
                    }
                    $RateQualifier = $RentalRate->item(0)->getElementsByTagName('RateQualifier');
                    if ($RateQualifier->length > 0) {
                        $RateQualifier2 = $RateQualifier->item(0)->getAttribute("RateQualifier");
                        $RateCategory = $RateQualifier->item(0)->getAttribute("RateCategory");
                    } else {
                        $RateQualifier2 = "";
                        $RateCategory = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('vehavail_VehAvailCore');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Status' => $Status,
                            'TransmissionType' => $TransmissionType,
                            'AirConditionInd' => $AirConditionInd,
                            'PictureURL' => $PictureURL,
                            'VehicleCategory' => $VehicleCategory,
                            'Size' => $Size,
                            'Code' => $Code,
                            'Name' => $Name,
                            'CurrencyCode' => $CurrencyCode,
                            'EstimatedTotalAmount' => $EstimatedTotalAmount,
                            'RateTotalAmount' => $RateTotalAmount,
                            'VehiclePeriodUnitName' => $VehiclePeriodUnitName,
                            'DistUnitName' => $DistUnitName,
                            'Unlimited' => $Unlimited,
                            'RateQualifier' => $RateQualifier2,
                            'RateCategory' => $RateCategory
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

                    $VehicleCharges = $RentalRate->item(0)->getElementsByTagName('VehicleCharges');
                    if ($VehicleCharges->length > 0) {
                        $VehicleCharge = $VehicleCharges->item(0)->getElementsByTagName('VehicleCharge');
                        if ($VehicleCharge->length > 0) {
                            for ($j=0; $j < $VehicleCharge->length; $j++) { 
                                $Purpose = $VehicleCharge->item($j)->getAttribute("Purpose");
                                $CurrencyCode = $VehicleCharge->item($j)->getAttribute("CurrencyCode");
                                $Amount = $VehicleCharge->item($j)->getAttribute("Amount");
                                $IncludedInRate = $VehicleCharge->item($j)->getAttribute("IncludedInRate");
                                $GuaranteedInd = $VehicleCharge->item($j)->getAttribute("GuaranteedInd");
                                $Description = $VehicleCharge->item($j)->getAttribute("Description");
                                $TaxInclusive = $VehicleCharge->item($j)->getAttribute("TaxInclusive");

                                $Calculation = $VehicleCharge->item($j)->getElementsByTagName('Calculation');
                                if ($Calculation->length > 0) {
                                    $Quantity = $Calculation->item(0)->getAttribute("Quantity");
                                    $UnitName = $Calculation->item(0)->getAttribute("UnitName");
                                } else {
                                    $Quantity = "";
                                    $UnitName = "";
                                }

                                $TaxAmounts = $VehicleCharge->item($j)->getElementsByTagName('TaxAmounts');
                                if ($TaxAmounts->length > 0) {
                                    $TaxAmount = $TaxAmounts->item(0)->getElementsByTagName('TaxAmount');
                                    if ($TaxAmount->length > 0) {
                                        $TaxAmountCurrencyCode = $TaxAmount->item(0)->getAttribute("CurrencyCode");
                                        $TaxAmountDescription = $TaxAmount->item(0)->getAttribute("Description");
                                        $Total = $TaxAmount->item(0)->getAttribute("Total");
                                    } else {
                                        $TaxAmountCurrencyCode = "";
                                        $TaxAmountDescription = "";
                                        $Total = "";
                                    }
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('vehavail_VehicleCharge');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'Purpose' => $Purpose,
                                        'CurrencyCode' => $CurrencyCode,
                                        'Amount' => $Amount,
                                        'IncludedInRate' => $IncludedInRate,
                                        'GuaranteedInd' => $GuaranteedInd,
                                        'Description' => $Description,
                                        'TaxInclusive' => $TaxInclusive,
                                        'Quantity' => $Quantity,
                                        'UnitName' => $UnitName,
                                        'TaxAmountCurrencyCode' => $TaxAmountCurrencyCode,
                                        'TaxAmountDescription' => $TaxAmountDescription,
                                        'Total' => $Total
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