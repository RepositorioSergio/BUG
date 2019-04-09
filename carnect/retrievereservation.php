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
          <VehRetResRQ xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" EchoToken="1.0" Version="0" xmlns="http://www.opentravel.org/OTA/2003/05">
            <POS>
                <Source>
                    <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '"/>
                </Source>
            </POS>
            <VehRetResRQCore>
                <UniqueID ID_Context="TES404842991024" />
                <PersonName>
                    <Surname>Andrade</Surname>
                </PersonName>
            </VehRetResRQCore>
        </VehRetResRQ>
        </soapenv:Body>
        </soapenv:Envelope>';

        //41z7uAY1

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Accept-Encoding: gzip",
            "Content-length: " . strlen($xml_post_string)
        );

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
$VehRetResRS = $Body->item(0)->getElementsByTagName('VehRetResRS');

//VehRetResRSInfo
$VehRetResRSInfo = $VehRetResRS->item(0)->getElementsByTagName('VehRetResRSInfo');
if ($VehRetResRSInfo->length > 0) {
  $TPA_Extensions = $VehRetResRSInfo->item(0)->getElementsByTagName("TPA_Extensions");
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
        $insert->into('retrieve_TPA_Extensions');
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
}

//VehResRSCore
$VehRetResRSCore = $VehRetResRS->item(0)->getElementsByTagName('VehRetResRSCore');
if ($VehRetResRSCore->length > 0) {
    $ReservationStatus = $VehRetResRSCore->item(0)->getAttribute("ReservationStatus");
    $VehReservation = $VehRetResRSCore->item(0)->getElementsByTagName('VehReservation');
    if ($VehReservation->length > 0) {
        //Customer
        $Customer = $VehReservation->item(0)->getElementsByTagName('Customer');
        if ($Customer->length > 0) {
            $Primary = $Customer->item(0)->getElementsByTagName('Primary');
            if ($Primary->length > 0) {
                $Language = $Primary->item(0)->getAttribute("Language");
                $BirthDate = $Primary->item(0)->getAttribute("BirthDate");
                $Gender = $Primary->item(0)->getAttribute("Gender");
                $Email = $Primary->item(0)->getElementsByTagName("Email");
                if ($Email->length > 0) {
                    $Email = $Email->item(0)->nodeValue;
                } else {
                    $Email = "";
                }
                $PersonName = $Primary->item(0)->getElementsByTagName("PersonName");
                if ($PersonName->length > 0) {
                    $GivenName = $PersonName->item(0)->getElementsByTagName("GivenName");
                    if ($GivenName->length > 0) {
                        $GivenName = $GivenName->item(0)->nodeValue;
                    } else {
                        $GivenName = "";
                    }
                    $Surname = $PersonName->item(0)->getElementsByTagName("Surname");
                    if ($Surname->length > 0) {
                        $Surname = $Surname->item(0)->nodeValue;
                    } else {
                        $Surname = "";
                    }
                }
                $Telephone = $Primary->item(0)->getElementsByTagName("Telephone");
                if ($Telephone->length > 0) {
                    $PhoneNumber = $Telephone->item(0)->getAttribute("PhoneNumber");
                    $PhoneTechType = $Telephone->item(0)->getAttribute("PhoneTechType");
                }
                $Address = $Primary->item(0)->getElementsByTagName('Address');
                if ($Address->length > 0) {
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
                }
                $CitizenCountryName = $Primary->item(0)->getElementsByTagName('CitizenCountryName');
                if ($CitizenCountryName->length > 0) {
                    $Code = $CitizenCountryName->item(0)->getAttribute("Code");
                }
                $PaymentForm = $Primary->item(0)->getElementsByTagName('PaymentForm');
                if ($PaymentForm->length > 0) {
                    $Voucher = $PaymentForm->item(0)->getElementsByTagName('Voucher');
                    if ($Voucher->length > 0) {
                        $ValueType = $Voucher->item(0)->getAttribute("ValueType");
                        $Identifier = $Voucher->item(0)->getAttribute("Identifier");
                        $SupplierIdentifier = $Voucher->item(0)->getAttribute("SupplierIdentifier");
                    }
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('retrieve');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Language' => $Language,
                        'BirthDate' => $BirthDate,
                        'Gender' => $Gender,
                        'Email' => $Email,
                        'GivenName' => $GivenName,
                        'Surname' => $Surname,
                        'PhoneNumber' => $PhoneNumber,
                        'PhoneTechType' => $PhoneTechType,
                        'StreetNmbr' => $StreetNmbr,
                        'AddressLine' => $AddressLine,
                        'CityName' => $CityName,
                        'PostalCode' => $PostalCode,
                        'Code' => $Code,
                        'ValueType' => $ValueType,
                        'Identifier' => $Identifier,
                        'SupplierIdentifier' => $SupplierIdentifier
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Exception VEHSEG: " . $e;
                    echo $return;
                }
            }
        }

        //VehSegmentCore
        $VehSegmentCore = $VehReservation->item(0)->getElementsByTagName('VehSegmentCore');
        if ($VehSegmentCore->length > 0) {
            $ConfID = $VehSegmentCore->item(0)->getElementsByTagName('ConfID');
            if ($ConfID->length > 0) {
                for ($i=0; $i < $ConfID->length; $i++) { 
                    $ID_Context = $ConfID->item($i)->getAttribute("ID_Context");
                    $Type = $ConfID->item($i)->getAttribute("Type");
                }
            }
            $Vendor = $VehSegmentCore->item(0)->getElementsByTagName('Vendor');
            if ($Vendor->length > 0) {
                $VendorCode = $Vendor->item(0)->getAttribute("Code");
                $TravelSector = $Vendor->item(0)->getAttribute("TravelSector");
                $CompanyShortName = $Vendor->item(0)->getAttribute("CompanyShortName");
            }
            $VehRentalCore = $VehSegmentCore->item(0)->getElementsByTagName('VehRentalCore');
            if ($VehRentalCore->length > 0) {
                $ReturnDateTime = $VehRentalCore->item(0)->getAttribute("ReturnDateTime");
                $PickUpDateTime = $VehRentalCore->item(0)->getAttribute("PickUpDateTime");
                $PickUpLocation = $VehRentalCore->item(0)->getElementsByTagName('PickUpLocation');
                if ($PickUpLocation->length > 0) {
                    $ExtendedLocationCode = $PickUpLocation->item(0)->getAttribute("ExtendedLocationCode");
                    $CodeContext = $PickUpLocation->item(0)->getAttribute("CodeContext");
                    $LocationCode = $PickUpLocation->item(0)->getAttribute("LocationCode");
                    $PickUpLocation = $PickUpLocation->item(0)->nodeValue;
                } else {
                    $PickUpLocation = "";
                }
                $ReturnLocation = $VehRentalCore->item(0)->getElementsByTagName('ReturnLocation');
                if ($ReturnLocation->length > 0) {
                    $ExtendedLocationCodeRL = $ReturnLocation->item(0)->getAttribute("ExtendedLocationCode");
                    $CodeContextRL = $ReturnLocation->item(0)->getAttribute("CodeContext");
                    $LocationCodeRL = $ReturnLocation->item(0)->getAttribute("LocationCode");
                    $ReturnLocation = $ReturnLocation->item(0)->nodeValue;
                } else {
                    $ReturnLocation = "";
                }
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('retrieve_VehSegment');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'ID_Context' => $ID_Context,
                    'Type' => $Type,
                    'VendorCode' => $VendorCode,
                    'TravelSector' => $TravelSector,
                    'CompanyShortName' => $CompanyShortName,
                    'ReturnDateTime' => $ReturnDateTime,
                    'PickUpDateTime' => $PickUpDateTime,
                    'ExtendedLocationCode' => $ExtendedLocationCode,
                    'CodeContext' => $CodeContext,
                    'LocationCode' => $LocationCode,
                    'PickUpLocation' => $PickUpLocation,
                    'ExtendedLocationCodeRL' => $ExtendedLocationCodeRL,
                    'CodeContextRL' => $CodeContextRL,
                    'LocationCodeRL' => $LocationCodeRL,
                    'ReturnLocation' => $ReturnLocation
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Exception VEHSEG: " . $e;
                echo $return;
            }
            

            //Vehicle
            $Vehicle = $VehSegmentCore->item(0)->getElementsByTagName('Vehicle');
            $CodeContext = $Vehicle->item(0)->getAttribute('CodeContext');
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

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('retrieve_Vehicle');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'CodeContext' => $CodeContext,
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
                    'Size' => $Size,
                    'VehMakeModelCode' => $VehMakeModelCode,
                    'VehMakeModelName' => $VehMakeModelName
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Exception VEH: " . $e;
                echo $return;
            }

            //RentalRate
            $RentalRate = $VehSegmentCore->item(0)->getElementsByTagName("RentalRate");
            if ($RentalRate->length > 0) {
                $RateDistance = $RentalRate->item(0)->getElementsByTagName("RateDistance");
                $DistUnitName = $RateDistance->item(0)->getAttribute("DistUnitName");
                $Unlimited = $RateDistance->item(0)->getAttribute("Unlimited");
                
                $RateQualifier = $RentalRate->item(0)->getElementsByTagName("RateQualifier");
                if ($RateQualifier->length > 0) {
                    $ArriveByFlight = $RateQualifier->item(0)->getAttribute("ArriveByFlight");
                    $RateQualifier2 = $RateQualifier->item(0)->getAttribute("RateQualifier");
                    $RateCategory = $RateQualifier->item(0)->getAttribute("RateCategory");
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('retrieve_RentalRate');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'RateDistance' => $RateDistance,
                        'DistUnitName' => $DistUnitName,
                        'Unlimited' => $Unlimited,
                        'ArriveByFlight' => $ArriveByFlight,
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
                    echo "Exception DIST: " . $e;
                    echo $return;
                }
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
                                $insert->into('retrieve_VehicleCharge');
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
            //Fees
            $Fees = $VehSegmentCore->item(0)->getElementsByTagName('Fees');
            if ($Fees->length > 0) {
                $Fee = $Fees->item(0)->getElementsByTagName('Fee');
                if ($Fee->length > 0) {
                    $TaxInclusive = $Fee->item(0)->getAttribute('TaxInclusive');
                    $Amount = $Fee->item(0)->getAttribute('Amount');
                    $CurrencyCode = $Fee->item(0)->getAttribute('CurrencyCode');
                    $IncludedInEstTotalInd = $Fee->item(0)->getAttribute('IncludedInEstTotalInd');
                    $Description = $Fee->item(0)->getAttribute('Description');
                    $IncludedInRate = $Fee->item(0)->getAttribute('IncludedInRate');
                }
            }
            //TotalCharge
            $TotalCharge = $VehSegmentCore->item(0)->getElementsByTagName('TotalCharge');
            if ($TotalCharge->length > 0) {
                $TotalChargeCurrencyCode = $TotalCharge->item(0)->getAttribute('CurrencyCode');
                $TotalChargeEstimatedTotalAmount = $TotalCharge->item(0)->getAttribute('EstimatedTotalAmount');
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('retrieve_TotalFees');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'TaxInclusive' => $TaxInclusive,
                    'Amount' => $Amount,
                    'CurrencyCode' => $CurrencyCode,
                    'IncludedInEstTotalInd' => $IncludedInEstTotalInd,
                    'Description' => $Description,
                    'IncludedInRate' => $IncludedInRate,
                    'TotalChargeCurrencyCode' => $TotalChargeCurrencyCode,
                    'TotalChargeEstimatedTotalAmount' => $TotalChargeEstimatedTotalAmount
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Exception FT: " . $e;
                echo $return;
            }
        }

        //VehSegmentInfo
        $VehSegmentInfo = $VehReservation->item(0)->getElementsByTagName('VehSegmentInfo');
        if ($VehSegmentInfo->length > 0) {
            $PricedCoverages = $VehSegmentInfo->item(0)->getElementsByTagName("PricedCoverages");
            if ($PricedCoverages->length > 0) {
                $PricedCoverage = $PricedCoverages->item(0)->getElementsByTagName("PricedCoverage");
                for ($k=0; $k < $PricedCoverage->length; $k++) { 
                    $Coverage = $PricedCoverage->item($k)->getElementsByTagName("Coverage");
                    $Details = $Coverage->item(0)->getElementsByTagName("Details");
                    $CodeDetails = $Details->item(0)->getAttribute("Code");
                    $CoverageTypeDetails = $Details->item(0)->getAttribute("CoverageType");
                    $ChargeDetails = $Details->item(0)->getElementsByTagName("Charge");
                    $AmountCD = $ChargeDetails->item(0)->getAttribute("Amount");
                    $IncludedInRateCD = $ChargeDetails->item(0)->getAttribute("IncludedInRate");
                    $Charge = $PricedCoverage->item($k)->getElementsByTagName("Charge");
                    $TaxInclusiveC = $Charge->item(0)->getAttribute("TaxInclusive");
                    $AmountC = $Charge->item(0)->getAttribute("Amount");
                    $CurrencyCodeC = $Charge->item(0)->getAttribute("CurrencyCode");
                    $IncludedInEstTotalIndC = $Charge->item(0)->getAttribute("IncludedInEstTotalInd");
                    $IncludedInRateC = $Charge->item(0)->getAttribute("IncludedInRate");
                    $GuaranteedIndC = $Charge->item(0)->getAttribute("GuaranteedInd");

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('retrieve_PricedCoverage');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Details' => $Details,
                            'CodeDetails' => $CodeDetails,
                            'CoverageTypeDetails' => $CoverageTypeDetails,
                            'ChargeDetails' => $ChargeDetails,
                            'AmountCD' => $AmountCD,
                            'IncludedInRateCD' => $IncludedInRateCD,
                            'TaxInclusiveC' => $TaxInclusiveC,
                            'AmountC' => $AmountC,
                            'CurrencyCodeC' => $CurrencyCodeC,
                            'IncludedInEstTotalIndC' => $IncludedInEstTotalIndC,
                            'IncludedInRateC' => $IncludedInRateC,
                            'GuaranteedIndC' => $GuaranteedIndC
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
            $PaymentRules = $VehSegmentInfo->item(0)->getElementsByTagName("PaymentRules");
            $PaymentRule = $PaymentRules->item(0)->getElementsByTagName("PaymentRule");
            if ($PaymentRule->length > 0) {
                for ($x=0; $x < $PaymentRule->length; $x++) { 
                    $PaymentType = $PaymentRule->item($x)->getAttribute("PaymentType");
                    $PaymentRule = $PaymentRule->item($x)->nodeValue;

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('retrieve_PaymentRule');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
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
                        echo "Exception PAY: " . $e;
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
