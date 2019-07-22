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
echo "COMECOU VEHMODIFY<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.enterprise.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$username = "OTA_APTMSTST1";
$password = "fWQBzb4L";
$host = 'https://cis1-xmldirect.ehi.com/services30/OTA30SOAP';
$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.opentravel.org/OTA/2003/05">
​<soapenv:Header/>
​<soapenv:Body>
    <OTA_VehModifyRQ TimeStamp="2019-07-10T09:54:48" Target="Production" Version="3.0" TransactionIdentifier="100000001" SequenceNmbr="1" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 file:///C:/Users/Documents/XML/2012b%20Updated/OTA_VehModifyRQ.xsd">
        <POS>
            <Source ISOCountry="US">
                <RequestorID Type="4" ID="XMLRTA">
                    <CompanyName Code="EX" CompanyShortName="EHIXMLTEST"/>
                </RequestorID>
            </Source>
            <Source>
                <RequestorID Type="4" ID="00000000" ID_Context="IATA"/>
            </Source>
        </POS>
        <VehModifyRQCore ModifyType="Modify" Status="Available">
            <UniqueID ID="1702167138COUNT" Type="14"/>
            <VehRentalCore PickUpDateTime="2019-12-01T09:30:00" ReturnDateTime="2019-12-14T09:30:00">
                <PickUpLocation LocationCode="TULT71"/>
                <ReturnLocation LocationCode="TULT71"/>
            </VehRentalCore>
            <Customer>
                <Primary>
                    <PersonName>
                        <NamePrefix>Mr</NamePrefix>
                        <GivenName>XML</GivenName>
                        <Surname>TEST</Surname>
                        <NameSuffix>Sr</NameSuffix>
                    </PersonName>
                    <Telephone PhoneUseType="3" AreaCityCode="918" PhoneNumber="4016170" CountryAccessCode="1"/>
                    <Email>linksupport@ehi.com</Email>
                    <Address>
                        <AddressLine>600 Clayton Corporate Park</AddressLine>
                        <CityName>Clayton</CityName>
                        <PostalCode>63105</PostalCode>
                        <StateProv StateCode="MO"/>
                        <CountryName Code="US"/>
                    </Address>
                    <CustLoyalty ProgramID="AL" MembershipID="778125810" TravelSector="2"/>
                    <CustLoyalty ProgramID="AA" MembershipID="987654332" TravelSector="1"/>
                    </Primary>
                </Customer>
                <VendorPref Code="AL"/>
                <VehPref AirConditionInd="true" TransmissionType="Automatic">
                    <VehType VehicleCategory="1"/>
                    <VehClass Size="5"/>
                </VehPref>
                <DriverType Age="25"/>
                <RateQualifier RateQualifier="ARTEST"/>
                <SpecialEquipPrefs>
                    <SpecialEquipPref EquipType="7" Quantity="1" Action="Add"/>
                </SpecialEquipPrefs>
                <TPA_Extensions>
                    <TPA_Extension_Flags EnhancedTotalPrice="true"/>
                </TPA_Extensions>
        </VehModifyRQCore>
        <VehModifyRQInfo>
            <ArrivalDetails TransportationCode="14" Number="1234">
                <OperatingCompany Code="AA"/>
            </ArrivalDetails>
            <Reference ID="ARTEST" DateTime="2019-07-17T12:08:00" Type="16"/>
            <TPA_Extensions>
            </TPA_Extensions>
        </VehModifyRQInfo>
    </OTA_VehModifyRQ>                   
​</soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init($host);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: text/xml;charset=UTF-8',
    'Accept-Encoding: gzip,deflate',
    'SOAPAction: "OTA_VehModifyRQ"',
    'Host: cis1-xmldirect.ehi.com',
    'User-Agent: Jakarta Commons-HttpClient/3.1',
    'Content-Length: ' . strlen($raw)
));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>";
//die();
$config = new \Zend\Config\Config(include '../config/autoload/global.enterprise.php');
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

$responseElement = $inputDoc->documentElement;
$xpath = new DOMXPath($inputDoc);
$search = "";
$search = $xpath->query('/env:Envelope/env:Body', $responseElement);

$OTA_VehModifyRS = $search->item(0)->getElementsByTagName("OTA_VehModifyRS");
//LocationDetail
$VehModifyRSCore = $OTA_VehModifyRS->item(0)->getElementsByTagName("VehModifyRSCore");
if ($VehModifyRSCore->length > 0) {
    $ModifyStatus = $VehModifyRSCore->item(0)->getAttribute("ModifyStatus");
    $VehReservation = $VehModifyRSCore->item(0)->getElementsByTagName("VehReservation");
    if ($VehReservation->length > 0) {
        $Customer = $VehReservation->item(0)->getElementsByTagName("Customer");
        if ($Customer->length > 0) {
            $Primary = $Customer->item(0)->getElementsByTagName("Primary");
            if ($Primary->length > 0) {
                $Email = $Primary->item(0)->getElementsByTagName("Email");
                if ($Email->length > 0) {
                    $Email = $Email->item(0)->nodeValue;
                } else {
                    $Email = "";
                }
                echo $return;
                echo $Email;
                echo $return;
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
                } else {
                    $PhoneNumber = "";
                }
                
                $addr = "";
                $Address = $Primary->item(0)->getElementsByTagName("Address");
                if ($Address->length > 0) {
                    $AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
                    if ($AddressLine->length > 0) {
                        for ($a=0; $a < $AddressLine->length; $a++) { 
                            $addr = $AddressLine->item(0)->nodeValue;

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('addressLine_vehmodify');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'AddressLine' => $addr
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO 0: " . $e;
                                echo $return;
                            }
                        }
                        
                    }
                    $CityName = $Address->item(0)->getElementsByTagName("CityName");
                    if ($CityName->length > 0) {
                        $CityName = $CityName->item(0)->nodeValue;
                    } else {
                        $CityName = "";
                    }
                    $PostalCode = $Address->item(0)->getElementsByTagName("PostalCode");
                    if ($PostalCode->length > 0) {
                        $PostalCode = $PostalCode->item(0)->nodeValue;
                    } else {
                        $PostalCode = "";
                    }
                    $StateProv = $Address->item(0)->getElementsByTagName("StateProv");
                    if ($StateProv->length > 0) {
                        $StateCode = $StateProv->item(0)->getAttribute("StateCode");
                    } else {
                        $StateCode = "";
                    }
                    $CountryName = $Address->item(0)->getElementsByTagName("CountryName");
                    if ($CountryName->length > 0) {
                        $CodeCountryName = $CountryName->item(0)->getAttribute("Code");
                    } else {
                        $CodeCountryName = "";
                    }
                }

                $CustLoyalty = $Primary->item(0)->getElementsByTagName("CustLoyalty");
                if ($CustLoyalty->length > 0) {
                    $LoyalLevel = $CustLoyalty->item(0)->getAttribute("LoyalLevel");
                    $ProgramID = $CustLoyalty->item(0)->getAttribute("ProgramID");
                    $TravelSector = $CustLoyalty->item(0)->getAttribute("TravelSector");
                    $MembershipID = $CustLoyalty->item(0)->getAttribute("MembershipID");
                } else {
                    $LoyalLevel = "";
                    $ProgramID = "";
                    $TravelSector = "";
                    $MembershipID = "";
                }
                
                $TPA_Extensions = $Primary->item(0)->getElementsByTagName("TPA_Extensions");
                if ($TPA_Extensions->length > 0) {
                    $TPA_Extensions_Inf = $TPA_Extensions->item(0)->getElementsByTagName("TPA_Extensions_Inf");
                    if ($TPA_Extensions_Inf->length > 0) {
                        $RentalDuration = $TPA_Extensions_Inf->item(0)->getAttribute("RentalDuration");
                    } else {
                        $RentalDuration = "";
                    }
                    $ArrivalDetails = $TPA_Extensions->item(0)->getElementsByTagName("ArrivalDetails");
                    if ($ArrivalDetails->length > 0) {
                        $Number = $ArrivalDetails->item(0)->getAttribute("Number");
                        $TransportationCode = $ArrivalDetails->item(0)->getAttribute("TransportationCode");
                        $OperatingCompany = $ArrivalDetails->item(0)->getElementsByTagName("OperatingCompany");
                        if ($OperatingCompany->length > 0) {
                            $CodeOperatingCompany = $OperatingCompany->item(0)->getAttribute("Code");
                        } else {
                            $CodeOperatingCompany = "";
                        }
                        
                    } else {
                        $Number = "";
                        $TransportationCode = "";
                    }
                }
            }
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('vehmodify');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Email' => $Email,
                'GivenName' => $GivenName,
                'Surname' => $Surname,
                'PhoneNumber' => $PhoneNumber,
                'CityName' => $CityName,
                'PostalCode' => $PostalCode,
                'StateCode' => $StateCode,
                'CodeCountryName' => $CodeCountryName,
                'LoyalLevel' => $LoyalLevel,
                'ProgramID' => $ProgramID,
                'TravelSector' => $TravelSector,
                'MembershipID' => $MembershipID,
                'RentalDuration' => $RentalDuration,
                'Number' => $Number,
                'TransportationCode' => $TransportationCode,
                'CodeOperatingCompany' => $CodeOperatingCompany,
                'ModifyStatus' => $ModifyStatus
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

        $VehSegmentCore = $VehReservation->item(0)->getElementsByTagName("VehSegmentCore");
        if ($VehSegmentCore->length > 0) {
            $ConfID = $VehSegmentCore->item(0)->getElementsByTagName("ConfID");
            if ($ConfID->length > 0) {
                $IDConfID = $ConfID->item(0)->getAttribute("ID");
                $TypeConfID = $ConfID->item(0)->getAttribute("Type");
            } else {
                $IDConfID = "";
                $TypeConfID = "";
            }
            
            $Vendor = $VehSegmentCore->item(0)->getElementsByTagName("Vendor");
            if ($Vendor->length > 0) {
                $CodeVendor = $Vendor->item(0)->getAttribute("Code");
                $CompanyShortName = $Vendor->item(0)->getAttribute("CompanyShortName");
            } else {
                $CodeVendor = "";
                $CompanyShortName = "";
            }

            $VehRentalCore = $VehSegmentCore->item(0)->getElementsByTagName("VehRentalCore");
            if ($VehRentalCore->length > 0) {
                $PickUpDateTime = $VehRentalCore->item(0)->getAttribute("PickUpDateTime");
                $ReturnDateTime = $VehRentalCore->item(0)->getAttribute("ReturnDateTime");
                if ($PickUpLocation->length > 0) {
                    $LocationCodePickup = $PickUpLocation->item(0)->getAttribute("LocationCode");
                }
                $ReturnLocation = $VehRentalCore->item(0)->getElementsByTagName("ReturnLocation");
                if ($ReturnLocation->length > 0) {
                    $LocationCodeReturn = $ReturnLocation->item(0)->getAttribute("LocationCode");
                    echo $return;
                    echo $LocationCodeReturn;
                    echo $return;
                }
            }

            $Vehicle = $VehSegmentCore->item(0)->getElementsByTagName("Vehicle");
            if ($Vehicle->length > 0) {
                $TransmissionType = $Vehicle->item(0)->getAttribute("TransmissionType");
                $AirConditionInd = $Vehicle->item(0)->getAttribute("AirConditionInd");
                $BaggageQuantity = $Vehicle->item(0)->getAttribute("BaggageQuantity");
                $PassengerQuantity = $Vehicle->item(0)->getAttribute("PassengerQuantity");
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
                    $CodeVehMakeModel = $VehMakeModel->item(0)->getAttribute("Code");
                    $NameVehMakeModel = $VehMakeModel->item(0)->getAttribute("Name");
                } else {
                    $CodeVehMakeModel = "";
                    $NameVehMakeModel = "";
                }
            }

            // TotalCharge
            $TotalCharge = $VehSegmentCore->item(0)->getElementsByTagName('TotalCharge');
            if ($TotalCharge->length > 0) {
                $CurrencyCode = $TotalCharge->item(0)->getAttribute("CurrencyCode");
                $RateTotalAmount = $TotalCharge->item(0)->getAttribute("RateTotalAmount");
                $EstimatedTotalAmount = $TotalCharge->item(0)->getAttribute("EstimatedTotalAmount");
            } else {
                $CurrencyCode = "";
                $RateTotalAmount = "";
                $EstimatedTotalAmount = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('vehSegmentCore_vehmodify');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'IDConfID' => $IDConfID,
                    'TypeConfID' => $TypeConfID,
                    'CodeVendor' => $CodeVendor,
                    'CompanyShortName' => $CompanyShortName,
                    'PickUpDateTime' => $PickUpDateTime,
                    'ReturnDateTime' => $ReturnDateTime,
                    'LocationCodePickup' => $LocationCodePickup,
                    'LocationCodeReturn' => $LocationCodeReturn,
                    'TransmissionType' => $TransmissionType,
                    'AirConditionInd' => $AirConditionInd,
                    'BaggageQuantity' => $BaggageQuantity,
                    'PassengerQuantity' => $PassengerQuantity,
                    'PictureURL' => $PictureURL,
                    'VehicleCategory' => $VehicleCategory,
                    'Size' => $Size,
                    'CodeVehMakeModel' => $CodeVehMakeModel,
                    'NameVehMakeModel' => $NameVehMakeModel,
                    'CurrencyCode' => $CurrencyCode,
                    'RateTotalAmount' => $RateTotalAmount,
                    'EstimatedTotalAmount' => $EstimatedTotalAmount
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO 11: " . $e;
                echo $return;
            }

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
                $RateQualifier = $RentalRate->item(0)->getElementsByTagName('RateQualifier');
                if ($RateQualifier->length > 0) {
                    $CorpDiscountNmbr = $RateQualifier->item(0)->getAttribute("CorpDiscountNmbr");
                    $RateQualifier2 = $RateQualifier->item(0)->getAttribute("RateQualifier");
                    $RatePeriod = $RateQualifier->item(0)->getAttribute("RatePeriod");
                } else {
                    $CorpDiscountNmbr = "";
                    $RateQualifier2 = "";
                    $RatePeriod = "";
                }
                
                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('rentalrate_vehmodify');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'VehiclePeriodUnitName' => $VehiclePeriodUnitName,
                        'DistUnitName' => $DistUnitName,
                        'Unlimited' => $Unlimited,
                        'CorpDiscountNmbr' => $CorpDiscountNmbr,
                        'RateQualifier' => $RateQualifier2,
                        'RatePeriod' => $RatePeriod
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
                
                $VehicleCharges = $RentalRate->item(0)->getElementsByTagName('VehicleCharges');
                if ($VehicleCharges->length > 0) {
                    $VehicleCharge = $VehicleCharges->item(0)->getElementsByTagName('VehicleCharge');
                    if ($VehicleCharge->length > 0) {
                        for ($i = 0; $i < $VehicleCharge->length; $i ++) {
                            $Amount = $VehicleCharge->item($i)->getAttribute("Amount");
                            $CurrencyCode = $VehicleCharge->item($i)->getAttribute("CurrencyCode");
                            $TaxInclusive = $VehicleCharge->item($i)->getAttribute("TaxInclusive");
                            $Purpose = $VehicleCharge->item($i)->getAttribute("Purpose");
                            $Description = $VehicleCharge->item($i)->getAttribute("Description");
                            $Calculation = $VehicleCharge->item($i)->getElementsByTagName('Calculation');
                            if ($Calculation->length > 0) {
                                $UnitCharge = $Calculation->item(0)->getAttribute("UnitCharge");
                                $UnitName = $Calculation->item(0)->getAttribute("UnitName");
                                $Quantity = $Calculation->item(0)->getAttribute("Quantity");
                            } else {
                                $UnitCharge = "";
                                $UnitName = "";
                                $Quantity = "";
                            }
                            
                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('vehiclecharge_vehmodify');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'Amount' => $Amount,
                                    'CurrencyCode' => $CurrencyCode,
                                    'TaxInclusive' => $TaxInclusive,
                                    'Purpose' => $Purpose,
                                    'Description' => $Description,
                                    'UnitCharge' => $UnitCharge,
                                    'UnitName' => $UnitName,
                                    'Quantity' => $Quantity
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

            $PricedEquips = $VehSegmentCore->item(0)->getElementsByTagName('PricedEquips');
            if ($PricedEquips->length > 0) {
                $PricedEquip = $PricedEquips->item(0)->getElementsByTagName('PricedEquip');
                if ($PricedEquip->length > 0) {
                    $Required = $PricedEquip->item(0)->getAttribute("Required");
                    $Equipment = $PricedEquip->item(0)->getElementsByTagName('Equipment');
                    if ($Equipment->length > 0) {
                        $Quantity = $Equipment->item(0)->getAttribute("Quantity");
                        $EquipType = $Equipment->item(0)->getAttribute("EquipType");
                    } else {
                        $Quantity = "";
                        $EquipType = "";
                    }
                    $Charge = $PricedEquip->item(0)->getElementsByTagName('Charge');
                    if ($Charge->length > 0) {
                        $CurrencyCodeCharge = $Charge->item(0)->getAttribute("CurrencyCode");
                        $Amount = $Charge->item(0)->getAttribute("Amount");
                        $IncludedInEstTotalInd = $Charge->item(0)->getAttribute("IncludedInEstTotalInd");
                        $IncludedInRate = $Charge->item(0)->getAttribute("IncludedInRate");
                        $GuaranteedInd = $Charge->item(0)->getAttribute("GuaranteedInd");
                    } else {
                        $CurrencyCode = "";
                        $Amount = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('pricedEquips_vehmodify');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Required' => $Required,
                            'Quantity' => $Quantity,
                            'EquipType' => $EquipType,
                            'CurrencyCodeCharge' => $CurrencyCodeCharge,
                            'Amount' => $Amount,
                            'IncludedInEstTotalInd' => $IncludedInEstTotalInd,
                            'IncludedInRate' => $IncludedInRate,
                            'GuaranteedInd' => $GuaranteedInd
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 10: " . $e;
                        echo $return;
                    }
                }
            }

            $Fees = $VehSegmentCore->item(0)->getElementsByTagName('Fees');
            if ($Fees->length > 0) {
                $Fee = $Fees->item(0)->getElementsByTagName('Fee');
                if ($Fee->length > 0) {
                    for ($j = 0; $j < $Fee->length; $j ++) {
                        $Amount = $Fee->item($j)->getAttribute("Amount");
                        $CurrencyCode = $Fee->item($j)->getAttribute("CurrencyCode");
                        $Purpose = $Fee->item($j)->getAttribute("Purpose");
                        $Description = $Fee->item($j)->getAttribute("Description");
                        $IncludedInRate = $Fee->item($j)->getAttribute("IncludedInRate");
                        $IncludedInEstTotalInd = $Fee->item($j)->getAttribute("IncludedInEstTotalInd");
                        
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('fees_vehmodify');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'Amount' => $Amount,
                                'CurrencyCode' => $CurrencyCode,
                                'Purpose' => $Purpose,
                                'Description' => $Description,
                                'IncludedInRate' => $IncludedInRate,
                                'IncludedInEstTotalInd' => $IncludedInEstTotalInd
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

        $detail = "";
        $VehSegmentInfo = $VehReservation->item(0)->getElementsByTagName("VehSegmentInfo");
        if ($VehSegmentInfo->length > 0) {
            $PricedCoverages = $VehSegmentInfo->item(0)->getElementsByTagName("PricedCoverages");
            if ($PricedCoverages->length > 0) {
                $PricedCoverage = $PricedCoverages->item(0)->getElementsByTagName("PricedCoverage");
                if ($PricedCoverage->length > 0) {
                    $Required = $PricedCoverage->item(0)->getAttribute("Required");

                    $Coverage = $PricedCoverage->item(0)->getElementsByTagName("Coverage");
                    if ($Coverage->length > 0) {
                        $CodeCoverage = $Coverage->item(0)->getAttribute("Code");
                        $CoverageType = $Coverage->item(0)->getAttribute("CoverageType");
                        $Details = $Coverage->item(0)->getElementsByTagName("Details");
                        if ($Details->length > 0) {
                            $CoverageTextType = $Details->item(0)->getAttribute("CoverageTextType");
                            $detail = $Details->item(0)->nodeValue;
                        } else {
                            $CoverageTextType = "";
                        }  
                    }

                    $Deductible = $PricedCoverage->item(0)->getElementsByTagName("Deductible");
                    if ($Deductible->length > 0) {
                        $CurrencyCodeDeductible = $Deductible->item(0)->getAttribute("CurrencyCode");
                        $AmountDeductible = $Deductible->item(0)->getAttribute("Amount");
                    } else {
                        $CurrencyCodeDeductible = "";
                        $AmountDeductible = "";
                    }

                    $Charge = $PricedCoverage->item(0)->getElementsByTagName("Charge");
                    if ($Charge->length > 0) {
                        $Description = $Charge->item(0)->getAttribute("Description");
                        $CurrencyCode = $Charge->item(0)->getAttribute("CurrencyCode");
                        $Amount = $Charge->item(0)->getAttribute("Amount");
                        $IncludedInEstTotalInd = $Charge->item(0)->getAttribute("IncludedInEstTotalInd");
                        $IncludedInRate = $Charge->item(0)->getAttribute("IncludedInRate");
                        $GuaranteedInd = $Charge->item(0)->getAttribute("GuaranteedInd");

                        $Calculation = $Charge->item(0)->getElementsByTagName("Calculation");
                        if ($Calculation->length > 0) {
                            $Quantity = $Calculation->item(0)->getAttribute("Quantity");
                            $UnitName = $Calculation->item(0)->getAttribute("UnitName");
                            $UnitCharge = $Calculation->item(0)->getAttribute("UnitCharge");
                            $Total = $Calculation->item(0)->getAttribute("Total");
                        }

                        $TaxAmounts = $Charge->item(0)->getElementsByTagName("TaxAmounts");
                        if ($TaxAmounts->length > 0) {
                            $TaxAmount = $TaxAmounts->item(0)->getElementsByTagName("TaxAmount");
                            if ($TaxAmount->length > 0) {
                                for ($l=0; $l < $TaxAmount->length; $l++) { 
                                    $Description = $TaxAmount->item($l)->getAttribute("Description");
                                    $CurrencyCode = $TaxAmount->item($l)->getAttribute("CurrencyCode");
                                    $Total = $TaxAmount->item($l)->getAttribute("Total");

                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('taxAmount_vehmodify');
                                        $insert->values(array(
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'Description' => $Description,
                                            'CurrencyCode' => $CurrencyCode,
                                            'Total' => $Total
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "ERRO 14: " . $e;
                                        echo $return;
                                    }
                                }
                            }
                        }
                    } else {
                        $Description = "";
                        $CurrencyCode = "";
                        $Amount = "";
                        $IncludedInEstTotalInd = "";
                        $IncludedInRate = "";
                        $GuaranteedInd = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('vehSegmentInfo_vehmodify');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Required' => $Required,
                            'CodeCoverage' => $CodeCoverage,
                            'CoverageType' => $CoverageType,
                            'CoverageTextType' => $CoverageTextType,
                            'details' => $detail,
                            'CurrencyCodeDeductible' => $CurrencyCodeDeductible,
                            'AmountDeductible' => $AmountDeductible,
                            'Description' => $Description,
                            'CurrencyCode' => $CurrencyCode,
                            'Amount' => $Amount,
                            'IncludedInEstTotalInd' => $IncludedInEstTotalInd,
                            'IncludedInRate' => $IncludedInRate,
                            'GuaranteedInd' => $GuaranteedInd,
                            'Quantity' => $Quantity,
                            'UnitName' => $UnitName,
                            'UnitCharge' => $UnitCharge,
                            'Total' => $Total
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 15: " . $e;
                        echo $return;
                    }

                }
            }

            $VendorMessages = $VehSegmentInfo->item(0)->getElementsByTagName("VendorMessages");
            if ($VendorMessages->length > 0) {
                $VendorMessage = $VendorMessages->item(0)->getElementsByTagName("VendorMessage");
                if ($VendorMessage->length > 0) {
                    for ($t=0; $t < $VendorMessage->length; $t++) { 
                        $Title = $VendorMessage->item($t)->getAttribute("Title");
                        $SubSection = $VendorMessage->item($t)->getElementsByTagName("SubSection");
                        if ($SubSection->length > 0) {
                            $Paragraph = $SubSection->item(0)->getElementsByTagName("Paragraph");
                            if ($Paragraph->length > 0) {
                                $Text = $Paragraph->item(0)->getElementsByTagName("Text");
                                if ($Text->length > 0) {
                                    $Text = $Text->item(0)->nodeValue;
                                } else {
                                    $Text = "";
                                }
                            }
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('vendormessages_vehmodify');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'Title' => $Title,
                                'Text' => $Text
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 20: " . $e;
                            echo $return;
                        }
                    }
                }
            }

            $LocationDetails = $VehSegmentInfo->item(0)->getElementsByTagName("LocationDetails");
            if ($LocationDetails->length > 0) {
                $AdditionalInfo = $LocationDetails->item(0)->getElementsByTagName("AdditionalInfo");
                if ($AdditionalInfo->length > 0) {
                    $VehRentLocInfos = $AdditionalInfo->item(0)->getElementsByTagName("VehRentLocInfos");
                    if ($VehRentLocInfos->length > 0) {
                        $VehRentLocInfo = $VehRentLocInfos->item(0)->getElementsByTagName("VehRentLocInfo");
                        if ($VehRentLocInfo->length > 0) {
                            for ($v=0; $v < $VehRentLocInfo->length; $v++) { 
                                $Title = $VehRentLocInfo->item($v)->getAttribute("Title");
                                $Type = $VehRentLocInfo->item($v)->getAttribute("Type");

                                $SubSection = $VehRentLocInfo->item($v)->getElementsByTagName("SubSection");
                                if ($SubSection->length > 0) {
                                    $SubCode = $SubSection->item(0)->getAttribute("SubCode");
                                    $Paragraph = $SubSection->item(0)->getElementsByTagName("Paragraph");
                                    if ($Paragraph->length > 0) {
                                        $Text = $Paragraph->item(0)->getElementsByTagName("Text");
                                        if ($Text->length > 0) {
                                            $Text = $Text->item(0)->nodeValue;
                                        } else {
                                            $Text = "";
                                        }
                                    }
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('locationdetails_vehmodify');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'Title' => $Title,
                                        'Type' => $Type,
                                        'SubCode' => $SubCode,
                                        'Text' => $Text
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 20: " . $e;
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