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
        <OTA_VehResRQ xmlns:xsi="http://www.w3.org/2008/XMLSchema-instance" Version="1.0">
        <POS> 
<Source> 
<RequestorID Type="1" ID="CTMTours"/> 
</Source> 
<Source> 
<RequestorID Type="5" ID="91502994"/> 
</Source> 
</POS> 
<VehResRQCore Status="Available"> 
<VehRentalCore PickUpDateTime="2019-11-24T12:00:00" ReturnDateTime="2019-11-25T14:00:00"> 
<PickUpLocation LocationCode="JFK"></PickUpLocation> 
<ReturnLocation LocationCode="JFK"></ReturnLocation> 
</VehRentalCore> 
<Customer> 
<Primary> 
<PersonName> 
<GivenName>Andre</GivenName> 
<Surname>Castro</Surname> 
</PersonName> 
<CitizenCountryName Code="US"></CitizenCountryName> 
</Primary> 
</Customer> 
<VendorPref CompanyShortName="Avis">Avis Rent-A-Car</VendorPref> 
<VehPref AirConditionPref="Preferred" TransmissionPref="Preferred" TransmissionType="Automatic" ClassPref="Preferred" TypePref="Preferred"> 
<VehType VehicleCategory="1"></VehType> 
<VehClass Size="4"></VehClass> 
<VehGroup GroupType="SIPP" GroupValue="CCAR"></VehGroup> 
</VehPref> 
<RateQualifier RateCategory="3" RateQualifier="8A"/> 
</VehResRQCore> 
<VehResRQInfo> 
<RentalPaymentPref> 
<Voucher Identifier="12345678" ValueType="FixedValue" ElectronicIndicator="true"/> 
<PaymentAmount Amount="237.65" CurrencyCode="USD"/> 
</RentalPaymentPref> 
</VehResRQInfo> 
      </OTA_VehResRQ>
</ns:Request>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

echo "<xmp>";
var_dump($raw);
echo "</xmp>";

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
$VehCancelRSCore = $OTA_VehCancelRS->item(0)->getElementsByTagName("VehCancelRSCore");

$VehReservation = $VehCancelRSCore->item(0)->getElementsByTagName("VehReservation");
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
                $insert->into('vehres_VehRentalCore');
                $insert->values(array(
                    'ID' => $ID,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Primary' => $Primary,
                    'Type' => $Type,
                    'Vendor' => $Vendor,
                    'ReturnDateTime' => $ReturnDateTime,
                    'PickUpDateTime' => $PickUpDateTime,
                    'PickUpLocationCodeContext' => $PickUpLocationCodeContext,
                    'PickUpLocationLocationCode' => $PickUpLocationLocationCode,
                    'ReturnLocationCodeContext' => $ReturnLocationCodeContext,
                    'ReturnLocationLocationCode' => $ReturnLocationLocationCode,
                    'TID' => $TID
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO VEHRENT: " . $e;
                echo $return;
            }
        }

        //Vehicle
        $Vehicle = $VehSegmentCore->item(0)->getElementsByTagName("Vehicle");
        if ($Vehicle->length > 0) {
            $TransmissionType = $Vehicle->item(0)->getAttribute("TransmissionType");
            $AirConditionInd = $Vehicle->item(0)->getAttribute("AirConditionInd");

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

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('vehres_Vehicle');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'TransmissionType' => $TransmissionType,
                    'AirConditionInd' => $AirConditionInd,
                    'VehicleCategory' => $VehicleCategory,
                    'Size' => $Size,
                    'Code' => $Code,
                    'Name' => $Name
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO VEH: " . $e;
                echo $return;
            }
        }

        //RentalRate
        $RentalRate = $VehSegmentCore->item(0)->getElementsByTagName("RentalRate");
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
            $RateQualifier2 = "";
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
                $insert->into('vehres_RentalRate');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
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
                echo "ERRO RENTAL: " . $e;
                echo $return;
            }

            $VehicleCharges = $RentalRate->item(0)->getElementsByTagName('VehicleCharges');
            if ($VehicleCharges->length > 0) {
                $VehicleCharge = $VehicleCharges->item(0)->getElementsByTagName('VehicleCharge');
                if ($VehicleCharge->length > 0) {
                    for ($i=0; $i < $VehicleCharge->length; $i++) { 
                        $Purpose = $VehicleCharge->item($i)->getAttribute("Purpose");
                        $CurrencyCode = $VehicleCharge->item($i)->getAttribute("CurrencyCode");
                        $Amount = $VehicleCharge->item($i)->getAttribute("Amount");
                        $IncludedInRate = $VehicleCharge->item($i)->getAttribute("IncludedInRate");
                        $GuaranteedInd = $VehicleCharge->item($i)->getAttribute("GuaranteedInd");
                        $Description = $VehicleCharge->item($i)->getAttribute("Description");
                        $TaxInclusive = $VehicleCharge->item($i)->getAttribute("TaxInclusive");

                        $TaxAmounts = $VehicleCharge->item($i)->getElementsByTagName('TaxAmounts');
                        if ($TaxAmounts->length > 0) {
                            $TaxAmount = $TaxAmounts->item(0)->getElementsByTagName('TaxAmount');
                            if ($TaxAmount->length > 0) {
                                $TaxAmountCurrencyCode = $TaxAmount->item(0)->getAttribute("CurrencyCode");
                                $TaxAmountDescription = $TaxAmount->item(0)->getAttribute("Description");
                                $Total = $TaxAmount->item(0)->getAttribute("Total");
                            }
                        }
                        $Calculation = $VehicleCharge->item($i)->getElementsByTagName('Calculation');
                        if ($Calculation->length > 0) {
                            $Quantity = $Calculation->item(0)->getAttribute("Quantity");
                            $UnitName = $Calculation->item(0)->getAttribute("UnitName");
                            $UnitCharge = $Calculation->item(0)->getAttribute("UnitCharge");
                        } else {
                            $Quantity = "";
                            $UnitName = "";
                            $UnitCharge = "";
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('vehres_VehicleCharge');
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
                                'TaxAmountCurrencyCode' => $TaxAmountCurrencyCode,
                                'TaxAmountDescription' => $TaxAmountDescription,
                                'Total' => $Total,
                                'Quantity' => $Quantity,
                                'UnitName' => $UnitName,
                                'UnitCharge' => $UnitCharge
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO CHARGE: " . $e;
                            echo $return;
                        }

                    }
                }
            }
        }

        //TotalCharge
        $TotalCharge = $VehSegmentCore->item(0)->getElementsByTagName("TotalCharge");
        if ($TotalCharge->length > 0) {
            $CurrencyCode = $TotalCharge->item(0)->getAttribute("CurrencyCode");
            $EstimatedTotalAmount = $TotalCharge->item(0)->getAttribute("EstimatedTotalAmount");
            $RateTotalAmount = $TotalCharge->item(0)->getAttribute("RateTotalAmount");
        } else {
            $CurrencyCode = "";
            $EstimatedTotalAmount = "";
            $RateTotalAmount = "";
        }

        try {
          $sql = new Sql($db);
          $insert = $sql->insert();
          $insert->into('vehres');
          $insert->values(array(
              'datetime_created' => time(),
              'datetime_updated' => 0,
              'CurrencyCode' => $CurrencyCode,
              'EstimatedTotalAmount' => $EstimatedTotalAmount,
              'RateTotalAmount' => $RateTotalAmount
          ), $insert::VALUES_MERGE);
          $statement = $sql->prepareStatementForSqlObject($insert);
          $results = $statement->execute();
          $db->getDriver()
          ->getConnection()
          ->disconnect();
      } catch (\Exception $e) {
          echo $return;
          echo "ERRO CHARGE: " . $e;
          echo $return;
      }
    }

    //VehSegmentInfo
    $VehSegmentInfo = $VehAvailRSCore->item(0)->getElementsByTagName("VehSegmentInfo");
    if ($VehSegmentInfo->length > 0) {

        $RentalPaymentAmount = $VehSegmentInfo->item(0)->getElementsByTagName("RentalPaymentAmount");
        if ($RentalPaymentAmount->length > 0) {
            $Voucher = $RentalPaymentAmount->item(0)->getElementsByTagName("Voucher");
            if ($Voucher->length > 0) {
                $Identifier = $Voucher->item(0)->getAttribute("Identifier");
            }
            $PaymentAmount = $RentalPaymentAmount->item(0)->getElementsByTagName("PaymentAmount");
            if ($PaymentAmount->length > 0) {
                $CurrencyCode = $PaymentAmount->item(0)->getAttribute("CurrencyCode");
                $Amount = $PaymentAmount->item(0)->getAttribute("Amount");
            }
        }

        $VendorMessages = $VehSegmentInfo->item(0)->getElementsByTagName("VendorMessages");
        if ($VendorMessages->length > 0) {
            $VendorMessage = $VendorMessages->item(0)->getElementsByTagName("VendorMessage");
            if ($VendorMessage->length > 0) {
                $SubSection = $VendorMessage->item(0)->getElementsByTagName("SubSection");
                if ($SubSection->length > 0) {
                    $Paragraph = $SubSection->item(0)->getElementsByTagName("Paragraph");
                    if ($Paragraph->length > 0) {
                        $Text2 = "";
                        $Text = $Paragraph->item(0)->getElementsByTagName("Text");
                        for ($s=0; $s < $Text->length; $s++) { 
                            $Text2 = $Text->item($s)->nodeValue;
                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('vehres_VendorMessage');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'Identifier' => $Identifier,
                                    'CurrencyCode' => $CurrencyCode,
                                    'Amount' => $Amount,
                                    'Text' => $Text2
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO LOC2: " . $e;
                                echo $return;
                            }
                        }
                    }
                }
            }
        }

      $LocationDetails = $VehSegmentInfo->item(0)->getElementsByTagName("LocationDetails");
      if ($LocationDetails->length > 0) {
        for ($r=0; $r < $LocationDetails->length; $r++) { 
          $Code = $LocationDetails->item($r)->getAttribute("Code");
          $Name = $LocationDetails->item($r)->getAttribute("Name");
          $AtAirport = $LocationDetails->item($r)->getAttribute("AtAirport");
          $CodeContext = $LocationDetails->item($r)->getAttribute("CodeContext");
          
          $Address = $LocationDetails->item($r)->getElementsByTagName('Address');
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
              $StateProv2 = "";
              $StateProv = $Address->item(0)->getElementsByTagName('StateProv');
              if ($StateProv->length > 0) {
                  $StateCode = $StateProv->item(0)->getAttribute("StateCode");
                  $StateProv2 = $StateProv->item(0)->nodeValue;
              } else {
                  $StateCode = "";
                  $StateProv2 = "";
              }
              $CountryName2 = "";
              $CountryName = $Address->item(0)->getElementsByTagName('CountryName');
              if ($CountryName->length > 0) {
                  $CountryCode = $CountryName->item(0)->getAttribute("Code");
                  $CountryName2 = $CountryName->item(0)->nodeValue;
              } else {
                  $CountryCode = "";
                  $CountryName2 = "";
              }
              $Telephone = $Address->item(0)->getElementsByTagName('Telephone');
              if ($Telephone->length > 0) {
                  $PhoneNumber = $Telephone->item(0)->getAttribute("PhoneNumber");
              } else {
                  $PhoneNumber = "";
              }
          }

          try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('vehres_Location');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'CurrencyCode' => $CurrencyCode,
                'EstimatedTotalAmount' => $EstimatedTotalAmount,
                'RateTotalAmount' => $RateTotalAmount,
                'Code' => $Code,
                'Name' => $Name,
                'AtAirport' => $AtAirport,
                'CodeContext' => $CodeContext,
                'StreetNmbr' => $StreetNmbr,
                'AddressLine' => $AddressLine,
                'CityName' => $CityName,
                'PostalCode' => $PostalCode,
                'StateCode' => $StateCode,
                'StateProv' => $StateProv2,
                'CountryCode' => $CountryCode,
                'CountryName' => $CountryName2,
                'PhoneNumber' => $PhoneNumber
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