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
echo "COMECOU VEHAVAIL2";
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


$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.opentravel.org/OTA/2003/05">
	<soapenv:Header/>
	<soapenv:Body>
		<OTA_VehAvailRateRQ PrimaryLangID="EN" TimeStamp="2019-07-16T09:30:00" Target="Test" Version="3.0" TransactionIdentifier="100000001" SequenceNmbr="1" MaxResponses="1" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05:\Users\e557gc\Documents\XML\2012BU~1\OTA_VehAvailRateRQ.xsd">
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
			<VehAvailRQCore Status="Available">
				<VehRentalCore PickUpDateTime="2019-10-31T12:00:00" ReturnDateTime="2019-11-10T03:45:00">
					<PickUpLocation LocationCode="DABT71"/>
					<ReturnLocation LocationCode="DABT71"/>
				</VehRentalCore>
				<VendorPrefs>
					<VendorPref Code="AL"/>
				</VendorPrefs>
				<DriverType Age="59"/>
				<RateQualifier RateQualifier="CORP"/>
				<TPA_Extensions>
					<TPA_Extension_Flags EnhancedTotalPrice="true"/>
				</TPA_Extensions>
			</VehAvailRQCore>
		</OTA_VehAvailRateRQ>
	</soapenv:Body>
</soapenv:Envelope>
';

$encrypt_method = "AES-256-CBC";
$secret_key = "fWQBzb4L";
$key = hash("sha256", $secret_key);
$auth = openssl_encrypt($encrypt_method, $key, 0);
$auth = base64_encode($auth);

$url = 'https://xmldirect.ehi.com/services30/OTA30-Vendor.wsdl';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: text/xml; charset=UTF-8",
    "Accept: text/xml",
    "Accept-Encoding: gzip,deflate",
    "User-Agent: Jakarta Commons-HttpClient/3.1",
    "Authorization: Basic " . $auth,
    "Host: cis1-xmldirect.ehi.com",
    "SOAPAction: OTA_VehAvailRateRQ",
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

$OTA_VehAvailRateRS = $Response->item(0)->getElementsByTagName("OTA_VehAvailRateRS");
$VehAvailRSCore = $OTA_VehAvailRateRS->item(0)->getElementsByTagName("VehAvailRSCore");
//VehRentalCore
$VehRentalCore = $VehAvailRSCore->item(0)->getElementsByTagName("VehRentalCore");
if ($VehRentalCore->length > 0) {
    $PickUpDateTime = $VehRentalCore->item(0)->getAttribute("PickUpDateTime");
    $ReturnDateTime = $VehRentalCore->item(0)->getAttribute("ReturnDateTime");
    $PickUpLocation = $VehRentalCore->item(0)->getElementsByTagName("PickUpLocation");
    if ($PickUpLocation->length > 0) {
        $LocationCodePickup = $PickUpLocation->item(0)->getAttribute("LocationCode");
    }
    $ReturnLocation = $VehRentalCore->item(0)->getElementsByTagName("ReturnLocation");
    if ($ReturnLocation->length > 0) {
        $LocationCodeReturn = $ReturnLocation->item(0)->getAttribute("LocationCode");
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('vehavailRate');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'PickUpDateTime' => $PickUpDateTime,
            'ReturnDateTime' => $ReturnDateTime,
            'LocationCodePickup' => $LocationCodePickup,
            'LocationCodeReturn' => $LocationCodeReturn
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
}

//VehVendorAvails
$VehVendorAvails = $VehAvailRSCore->item(0)->getElementsByTagName("VehVendorAvails");
if ($VehVendorAvails->length > 0) {
    $VehVendorAvail = $VehVendorAvails->item(0)->getElementsByTagName("VehVendorAvail");
    if ($VehVendorAvail->length > 0) {
        $Vendor = $VehVendorAvail->item(0)->getElementsByTagName("Vendor");
        if ($Vendor->length > 0) {
            $Code = $Vendor->item(0)->getAttribute("Code");
            $CompanyShortName = $Vendor->item(0)->getAttribute("CompanyShortName");
        } else {
            $Code = "";
            $CompanyShortName = "";
        }

        //Info
        $Info = $VehVendorAvail->item(0)->getElementsByTagName("Info");
        if ($Info->length > 0) {
            $TPA_Extensions = $Info->item(0)->getElementsByTagName("TPA_Extensions");
            if ($TPA_Extensions->length > 0) {
                $TPA_Extensions_Inf = $TPA_Extensions->item(0)->getElementsByTagName('TPA_Extensions_Inf');
                if ($TPA_Extensions_Inf->length > 0) {
                    $RentalDuration = $TPA_Extensions_Inf->item(0)->getAttribute("RentalDuration");
                } else {
                    $RentalDuration = "";
                }
                $TPA_Extension_Flags = $TPA_Extensions->item(0)->getElementsByTagName('TPA_Extension_Flags');
                if ($TPA_Extension_Flags->length > 0) {
                    $Type = $TPA_Extension_Flags->item(0)->getAttribute("Type");
                    $CustDropOff = $TPA_Extension_Flags->item(0)->getAttribute("CustDropOff");
                    $CustPickUp = $TPA_Extension_Flags->item(0)->getAttribute("CustPickUp");
                } else {
                    $Type = "";
                    $CustDropOff = "";
                    $CustPickUp = "";
                }
            }
        }


        //VehAvails
        $VehAvails = $VehVendorAvail->item(0)->getElementsByTagName("VehAvails");
        if ($VehAvails->length > 0) {
            $VehAvail = $VehAvails->item(0)->getElementsByTagName("VehAvail");
            if ($VehAvail->length > 0) {
                $VehAvailInfo = $VehAvail->item(0)->getElementsByTagName('VehAvailInfo');
                if ($VehAvailInfo->length > 0) {
                    $VehAvailInfo = $VehAvailInfo->item(0)->nodeValue;
                } else {
                    $VehAvailInfo = "";
                }

                $VehAvailCore = $VehAvail->item(0)->getElementsByTagName('VehAvailCore');
                if ($VehAvailCore->length > 0) {
                    $Status = $VehAvailCore->item(0)->getAttribute("Status");
                    //Vehicle
                    $Vehicle = $VehAvailCore->item(0)->getElementsByTagName('Vehicle');
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

                    //TotalCharge
                    $TotalCharge = $VehAvailCore->item(0)->getElementsByTagName('TotalCharge');
                    if ($TotalCharge->length > 0) {
                        $CurrencyCode = $TotalCharge->item(0)->getAttribute("CurrencyCode");
                        $RateTotalAmount = $TotalCharge->item(0)->getAttribute("RateTotalAmount");
                        $EstimatedTotalAmount = $TotalCharge->item(0)->getAttribute("EstimatedTotalAmount");
                    } else {
                        $CurrencyCode = "";
                        $RateTotalAmount = "";
                        $EstimatedTotalAmount = "";
                    }

                    //Reference
                    $Reference = $VehAvailCore->item(0)->getElementsByTagName('Reference');
                    if ($Reference->length > 0) {
                        $TypeReference = $Reference->item(0)->getAttribute("Type");
                        $DateTime = $Reference->item(0)->getAttribute("DateTime");
                        $ID = $Reference->item(0)->getAttribute("ID");
                    } else {
                        $Type = "";
                        $DateTime = "";
                        $ID = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('vehavail_vehavailRate');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Code' => $Code,
                            'CompanyShortName' => $CompanyShortName,
                            'RentalDuration' => $RentalDuration,
                            'Type' => $Type,
                            'CustDropOff' => $CustDropOff,
                            'CustPickUp' => $CustPickUp,
                            'VehAvailInfo' => $VehAvailInfo,
                            'Status' => $Status,
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
                            'EstimatedTotalAmount' => $EstimatedTotalAmount,
                            'TypeReference' => $TypeReference,
                            'DateTime' => $DateTime,
                            'ID' => $ID
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


                    //RentalRate
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
                            $CorpDiscountNmbr = $RateQualifier->item(0)->getAttribute("CorpDiscountNmbr");
                            $RateQualifier = $RateQualifier->item(0)->getAttribute("RateQualifier");
                            $RatePeriod = $RateQualifier->item(0)->getAttribute("RatePeriod");
                        } else {
                            $CorpDiscountNmbr = "";
                            $RateQualifier = "";
                            $RatePeriod = "";
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('rentalrate_vehavailRate');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'VehiclePeriodUnitName' => $VehiclePeriodUnitName,
                                'DistUnitName' => $DistUnitName,
                                'Unlimited' => $Unlimited,
                                'CorpDiscountNmbr' => $CorpDiscountNmbr,
                                'RateQualifier' => $RateQualifier,
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
                                for ($i=0; $i < $VehicleCharge->length; $i++) { 
                                    $Amount = $RateQualifier->item($i)->getAttribute("Amount");
                                    $CurrencyCode = $RateQualifier->item($i)->getAttribute("CurrencyCode");
                                    $TaxInclusive = $RateQualifier->item($i)->getAttribute("TaxInclusive");
                                    $Purpose = $RateQualifier->item($i)->getAttribute("Purpose");
                                    $Description = $RateQualifier->item($i)->getAttribute("Description");
                                    $Calculation = $RateQualifier->item($i)->getElementsByTagName('Calculation');
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
                                        $insert->into('vehiclecharge_vehavailRate');
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

                    //Fees
                    $Fees = $VehAvailCore->item(0)->getElementsByTagName('Fees');
                    if ($Fees->length > 0) {
                        $Fee = $Fees->item(0)->getElementsByTagName('Fee');
                        if ($Fee->length > 0) {
                            for ($j=0; $j < $Fee->length; $j++) { 
                                $Amount = $Fee->item($j)->getAttribute("Amount");
                                $CurrencyCode = $Fee->item($j)->getAttribute("CurrencyCode");
                                $Purpose = $Fee->item($j)->getAttribute("Purpose");
                                $Description = $Fee->item($j)->getAttribute("Description");
                                $IncludedInRate = $Fee->item($j)->getAttribute("IncludedInRate");

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('fees_vehavailRate');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'Amount' => $Amount,
                                        'CurrencyCode' => $CurrencyCode,
                                        'Purpose' => $Purpose,
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
echo 'Done';
?>