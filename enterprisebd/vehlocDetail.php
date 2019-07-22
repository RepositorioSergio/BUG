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
echo "COMECOU VEHLOCDETAIL<br/>";
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
<OTA_VehLocDetailRQ TimeStamp="2019-07-10T09:54:48" Target="Production" Version="3.0" TransactionIdentifier="100000001" SequenceNmbr="1" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 file:///C:/Users/Documents/XML/2012b%20Updated/OTA_VehLocDetailRQ.xsd">
	<POS>
        <Source>
            <RequestorID Type="4" ID="XMLRTA">
                <CompanyName Code="EX" CompanyShortName="EHIXMLTEST"/>
            </RequestorID>
        </Source>
        <Source>
            <RequestorID Type="4" ID="00000000" ID_Context="IATA"/>
        </Source>
    </POS>
    <Location LocationCode="STLT71"/>
    <Vendor Code="AL"/>
    <Date PickUpDateTime="2019-09-09T10:00:00" ReturnDateTime="2019-09-10T10:00:00"/>
    <TPA_Extensions>
        <TPA_Extension_Flags OpScheduleIndicator="true"/>
    </TPA_Extensions>
</OTA_VehLocDetailRQ>
​</soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init($host);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: text/xml;charset=UTF-8',
    'Accept-Encoding: gzip,deflate',
    'SOAPAction: "OTA_VehLocDetailRQ"',
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

$OTA_VehLocDetailRS = $search->item(0)->getElementsByTagName("OTA_VehLocDetailRS");
//LocationDetail
$LocationDetail = $OTA_VehLocDetailRS->item(0)->getElementsByTagName("LocationDetail");
if ($LocationDetail->length > 0) {
    $Code = $LocationDetail->item(0)->getAttribute("Code");
    $Name = $LocationDetail->item(0)->getAttribute("Name");
    $AtAirport = $LocationDetail->item(0)->getAttribute("AtAirport");
    echo $return;
    echo $Code;
    echo $return;

    $Address = $LocationDetail->item(0)->getElementsByTagName("Address");
    if ($Address->length > 0) {
        $AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
        if ($AddressLine->length > 0) {
            $AddressLine = $AddressLine->item(0)->nodeValue;
        } else {
            $AddressLine = "";
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

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('vehlocDetail');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'Code' => $Code,
            'Name' => $Name,
            'AtAirport' => $AtAirport,
            'AddressLine' => $AddressLine,
            'CityName' => $CityName,
            'PostalCode' => $PostalCode,
            'StateCode' => $StateCode,
            'CodeCountryName' => $CodeCountryName
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

    $Telephone = $LocationDetail->item(0)->getElementsByTagName("Telephone");
    if ($Telephone->length > 0) {
        for ($k=0; $k < $Telephone->length; $k++) { 
            $PhoneTechType = $Telephone->item($k)->getAttribute("PhoneTechType");
            $AreaCityCode = $Telephone->item($k)->getAttribute("AreaCityCode");
            $PhoneNumber = $Telephone->item($k)->getAttribute("PhoneNumber");

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('telephone_vehlocDetail');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'PhoneTechType' => $PhoneTechType,
                    'AreaCityCode' => $AreaCityCode,
                    'PhoneNumber' => $PhoneNumber
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

    $day = "";
    $count = 0;
    $AdditionalInfo = $LocationDetail->item(0)->getElementsByTagName("AdditionalInfo");
    if ($AdditionalInfo->length > 0) {
        $OperationSchedules = $AdditionalInfo->item(0)->getElementsByTagName("OperationSchedules");
        if ($OperationSchedules->length > 0) {
            $OperationSchedule = $OperationSchedules->item(0)->getElementsByTagName("OperationSchedule");
            if ($OperationSchedule->length > 0) {
                for ($j=0; $j < $OperationSchedule->length; $j++) { 
                    $OperationTimes = $OperationSchedule->item($j)->getElementsByTagName("OperationTimes");
                    if ($OperationTimes->length > 0) {
                        $OperationTime = $OperationTimes->item(0)->getElementsByTagName("OperationTime");
                        if ($OperationTime->length > 0) {
                            for ($jAux=0; $jAux < $OperationTime->length; $jAux++) { 
                                $Text = $OperationTime->item($jAux)->getAttribute("Text");
                                $End = $OperationTime->item($jAux)->getAttribute("End");
                                $Start = $OperationTime->item($jAux)->getAttribute("Start");
                                if ($count == 0) {
                                    $day = $OperationTime->item($jAux)->getAttribute("Sun");
                                } elseif ($count == 1) {
                                    $day = $OperationTime->item($jAux)->getAttribute("Mon");
                                } elseif ($count == 2) {
                                    $day = $OperationTime->item($jAux)->getAttribute("Tue");
                                } elseif ($count == 3) {
                                    $day = $OperationTime->item($jAux)->getAttribute("Weds");
                                } elseif ($count == 4) {
                                    $day = $OperationTime->item($jAux)->getAttribute("Thur");
                                } elseif ($count == 5) {
                                    $day = $OperationTime->item($jAux)->getAttribute("Fri");
                                } else {
                                    $day = $OperationTime->item($jAux)->getAttribute("Sat");
                                }
                                $count = $count + 1;

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('additionalInfo_vehlocDetail');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'Text' => $Text,
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
                                    echo "ERRO 3: " . $e;
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

//Vehicles
$Vehicles = $OTA_VehLocDetailRS->item(0)->getElementsByTagName("Vehicles");
if ($Vehicles->length > 0) {
    $Vehicle = $Vehicles->item(0)->getElementsByTagName("Vehicle");
    if ($Vehicle->length > 0) {
        for ($i=0; $i < $Vehicle->length; $i++) { 
            $FuelType = $Vehicle->item($i)->getAttribute("FuelType");
            $TransmissionType = $Vehicle->item($i)->getAttribute("TransmissionType");
            $AirConditionInd = $Vehicle->item($i)->getAttribute("AirConditionInd");
            $BaggageQuantity = $Vehicle->item($i)->getAttribute("BaggageQuantity");
            $PassengerQuantity = $Vehicle->item($i)->getAttribute("PassengerQuantity");
            $PictureURL = $Vehicle->item($i)->getElementsByTagName('PictureURL');
            if ($PictureURL->length > 0) {
                $PictureURL = $PictureURL->item(0)->nodeValue;
            } else {
                $PictureURL = "";
            }
            $VehType = $Vehicle->item($i)->getElementsByTagName('VehType');
            if ($VehType->length > 0) {
                $VehicleCategory = $VehType->item(0)->getAttribute("VehicleCategory");
            } else {
                $VehicleCategory = "";
            }
            $VehClass = $Vehicle->item($i)->getElementsByTagName('VehClass');
            if ($VehClass->length > 0) {
                $Size = $VehClass->item(0)->getAttribute("Size");
            } else {
                $Size = "";
            }
            $VehMakeModel = $Vehicle->item($i)->getElementsByTagName('VehMakeModel');
            if ($VehMakeModel->length > 0) {
                $CodeVehMakeModel = $VehMakeModel->item(0)->getAttribute("Code");
                $NameVehMakeModel = $VehMakeModel->item(0)->getAttribute("Name");
            } else {
                $CodeVehMakeModel = "";
                $NameVehMakeModel = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('vehicles_vehlocDetail');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'FuelType' => $FuelType,
                    'TransmissionType' => $TransmissionType,
                    'AirConditionInd' => $AirConditionInd,
                    'BaggageQuantity' => $BaggageQuantity,
                    'PassengerQuantity' => $PassengerQuantity,
                    'PictureURL' => $PictureURL,
                    'VehicleCategory' => $VehicleCategory,
                    'Size' => $Size,
                    'CodeVehMakeModel' => $CodeVehMakeModel,
                    'NameVehMakeModel' => $NameVehMakeModel
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


//Requirements
$Requirements = $OTA_VehLocDetailRS->item(0)->getElementsByTagName("Requirements");
if ($Requirements->length > 0) {
    $PaymentOptions = $Requirements->item(0)->getElementsByTagName("PaymentOptions");
    if ($PaymentOptions->length > 0) {
        $CreditCard = $PaymentOptions->item(0)->getElementsByTagName("CreditCard");
        if ($CreditCard->length > 0) {
            $AcceptablePaymentCards = $CreditCard->item(0)->getElementsByTagName("AcceptablePaymentCards");
            if ($AcceptablePaymentCards->length > 0) {
                $AcceptablePaymentCard = $AcceptablePaymentCards->item(0)->getElementsByTagName("AcceptablePaymentCard");
                if ($AcceptablePaymentCard->length > 0) {
                    for ($x=0; $x < $AcceptablePaymentCard->length; $x++) { 
                        $CardType = $AcceptablePaymentCard->item($x)->getAttribute("CardType");

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('creditcard_vehlocDetail');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'CardType' => $CardType
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

        $DebitCard = $PaymentOptions->item(0)->getElementsByTagName("DebitCard");
        if ($DebitCard->length > 0) {
            $Info = $DebitCard->item(0)->getElementsByTagName("Info");
            if ($Info->length > 0) {
                $SubSection = $Info->item(0)->getElementsByTagName("SubSection");
                if ($SubSection->length > 0) {
                    $Paragraph = $SubSection->item(0)->getElementsByTagName("Paragraph");
                    if ($Paragraph->length > 0) {
                        $Text = $Paragraph->item(0)->getElementsByTagName("Text");
                        if ($Text->length > 0) {
                            $Text = $Text->item(0)->nodeValue;

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('debitcard_vehlocDetail');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'Text' => $Text
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO 6: " . $e;
                                echo $return;
                            }
                        }
                    }
                }
            }
        }
    }
}


//ServicesOffered
$ServicesOffered = $OTA_VehLocDetailRS->item(0)->getElementsByTagName("ServicesOffered");
if ($ServicesOffered->length > 0) {
    $SpecialEquipments = $ServicesOffered->item(0)->getElementsByTagName("SpecialEquipments");
    if ($SpecialEquipments->length > 0) {
        $SpecialEquipment = $SpecialEquipments->item(0)->getElementsByTagName("SpecialEquipment");
        if ($SpecialEquipment->length > 0) {
            for ($y=0; $y < $SpecialEquipment->length; $y++) { 
                $Type = $SpecialEquipment->item($y)->getAttribute("Type");

                $EquipDesc = $SpecialEquipment->item($y)->getElementsByTagName("EquipDesc");
                if ($EquipDesc->length > 0) {
                    $SubSection = $EquipDesc->item(0)->getElementsByTagName("SubSection");
                    if ($SubSection->length > 0) {
                        $SubTitle = $SubSection->item(0)->getAttribute("SubTitle");
                        $Paragraph = $SubSection->item(0)->getElementsByTagName("Paragraph");
                        if ($Paragraph->length > 0) {
                            $Name = $Paragraph->item(0)->getAttribute("Name");
                            $Text = $Paragraph->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                $Text = $Text->item(0)->nodeValue;
                            } else {
                                $Text = "";
                            }
                            
                        }
                    }
                }

                $EquipCharges = $SpecialEquipment->item($y)->getElementsByTagName("EquipCharges");
                if ($EquipCharges->length > 0) {
                    $EquipCharge = $EquipCharges->item(0)->getElementsByTagName("EquipCharge");
                    if ($EquipCharge->length > 0) {
                        $CurrencyCode = $EquipCharge->item(0)->getAttribute("CurrencyCode");
                        $IncludedInRate = $EquipCharge->item(0)->getAttribute("IncludedInRate");
                        $GuaranteedInd = $EquipCharge->item(0)->getAttribute("GuaranteedInd");

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('servicesOffered_vehlocDetail');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'Type' => $Type,
                                'SubTitle' => $SubTitle,
                                'Name' => $Name,
                                'Text' => $Text,
                                'CurrencyCode' => $CurrencyCode,
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
                            echo "ERRO 7: " . $e;
                            echo $return;
                        }

                        $Calculation = $EquipCharge->item(0)->getElementsByTagName("Calculation");
                        if ($Calculation->length > 0) {
                            for ($z=0; $z < $Calculation->length; $z++) { 
                                $UnitName = $Calculation->item($z)->getAttribute("UnitName");
                                $UnitCharge = $Calculation->item($z)->getAttribute("UnitCharge");

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('calculation_vehlocDetail');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
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
                                    echo "ERRO 8: " . $e;
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