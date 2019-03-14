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
echo "COMECOU SEARCH<br/>";
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
$sql = "select value from settings where name='enablecitytourspackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_citytours = $affiliate_id;
} else {
    $affiliate_id_citytours = 0;
}
$sql = "select value from settings where name='citytourspackagesuser' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagesuser = $row_settings['value'];
}
echo "<br/>citytourspackagesuser: " . $citytourspackagesuser;
$sql = "select value from settings where name='citytourspackagespassword' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagespassword = base64_decode($row_settings['value']);
}
echo "<br/>citytourspackagespassword: " . $citytourspackagespassword;
$sql = "select value from settings where name='citytourspackagesserviceURL' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagesserviceURL = $row_settings['value'];
}
echo "<br/>citytourspackagesserviceURL: " . $citytourspackagesserviceURL;
$sql = "select value from settings where name='citytourspackagesagency' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagesagency = $row_settings['value'];
}
echo "<br/>citytourspackagesagency: " . $citytourspackagesagency;
$sql = "select value from settings where name='citytourspackagesSystem' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagesSystem = $row_settings['value'];
}
echo "<br/>citytourspackagesSystem: " . $citytourspackagesSystem;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.citytours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
  <ServiceSearch xmlns="http://tempuri.org/">
  <OTA_TourActivitySearchAvailRQ AltLangID="en-us" Version="3.0" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<POS>
    <Source PseudoCityCode="NONE">
        <RequestorID ID="TESTID" Type="TD"/>
        <TPA_Extensions>
            <Provider xmlns="">
                <System>' . $citytourspackagesSystem . '</System>
                <Userid>' . $citytourspackagesuser . '</Userid>
                <Password>' . $citytourspackagespassword . '</Password>					
            </Provider>
        </TPA_Extensions>
    </Source>
</POS>
<SearchCriteria>
    <BasicInfo Name="" />
    <CategoryTypePref>
        <Type Code="3" />
    </CategoryTypePref>
    <CustomerCounts Age="35" Quantity="1">
        <QualifierInfo>Adult</QualifierInfo>
    </CustomerCounts>
    <CustomerCounts Age="7" Quantity="1">
        <QualifierInfo>Child</QualifierInfo>
    </CustomerCounts> 
    <DateTimePref Start="2019-03-26" End="2019-04-26" />
    <LocationPref>
        <Address>
            <CityName>New York City</CityName>
            <StateProv StateCode="NY" />
            <CountryName Code="US" />
        </Address>
    </LocationPref>
</SearchCriteria>
</OTA_TourActivitySearchAvailRQ>
</ServiceSearch>
  </soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "SOAPAction: http://tempuri.org/ServiceSearch",
    "Content-length: ".strlen($raw)
));
$client->setUri($citytourspackagesserviceURL);
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

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>'; 

$config = new \Zend\Config\Config(include '../config/autoload/global.citytours.php');
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
$OTA_TourActivitySearchAvailRS = $Body->item(0)->getElementsByTagName("OTA_TourActivitySearchAvailRS");
$node = $OTA_TourActivitySearchAvailRS->item(0)->getElementsByTagName("TourActivityInfo");
echo $return;
echo "TAM: " . $node->length;
echo $return;
for ($i = 0; $i < $node->length; $i++) {
    $BrokerCode = $node->item($i)->getAttribute("BrokerCode");
    $TourActivityRooms = $node->item($i)->getElementsByTagName("TourActivityRooms");
    $TourActivityRoom = $TourActivityRooms->item(0)->getElementsByTagName("TourActivityRoom");
    if ($TourActivityRoom->length > 0) {
        $TourActivityAmountPeople = $TourActivityRoom->item(0)->getAttribute("TourActivityAmountPeople");
        $TourActivityRoomType = $TourActivityRoom->item(0)->getAttribute("TourActivityRoomType");
        $TourActivityRoomTypeID = $TourActivityRoom->item(0)->getAttribute("TourActivityRoomTypeID");
        $TourActivityContractPriceID = $TourActivityRoom->item(0)->getAttribute("TourActivityContractPriceID");
        echo $return;
        echo "TourActivityContractPriceID: " . $TourActivityContractPriceID;
        echo $return;
    } else {
        $TourActivityAmountPeople = "";
    }

    //BasicInfo
    $BasicInfo = $node->item($i)->getElementsByTagName("BasicInfo");
    if ($BasicInfo->length > 0) {
        $Name = $BasicInfo->item(0)->getAttribute("Name");
        $IssueVoucher = $BasicInfo->item(0)->getAttribute("IssueVoucher");
        $PassengerWeight = $BasicInfo->item(0)->getAttribute("PassengerWeight");
        $AwaitingConfirmationVendor = $BasicInfo->item(0)->getAttribute("AwaitingConfirmationVendor");
        $TourActivityServiceType = $BasicInfo->item(0)->getAttribute("TourActivityServiceType");
        $TourActivityContractID = $BasicInfo->item(0)->getAttribute("TourActivityContractID");
        $VendorCode = $BasicInfo->item(0)->getAttribute("VendorCode");
        $TourActivityCode = $BasicInfo->item(0)->getAttribute("TourActivityCode");
        $TourActivityID = $BasicInfo->item(0)->getAttribute("TourActivityID");
        $TourActivityContractPriceID = $BasicInfo->item(0)->getAttribute("TourActivityContractPriceID");
        $TourActivityContractAvailID = $BasicInfo->item(0)->getAttribute("TourActivityContractAvailID");
        echo $return;
        echo "Name: " . $Name;
        echo $return;
    } else {
        $Name = "";
        $TourActivityID = "";
        $TourActivityContractPriceID = "";
        $TourActivityContractAvailID = "";
    }

    $TourActivityAdditionalServices = $node->item($i)->getElementsByTagName("TourActivityAdditionalServices");
    if ($TourActivityAdditionalServices->length > 0) {
        $Service = $TourActivityAdditionalServices->item(0)->getElementsByTagName("TourActivityAdditionalService");
        if ($Service->length > 0) {
            echo $return;
            echo "Service->length: " . $Service->length;
            echo $return;
            for ($z=0; $z < $Service->length; $z++) { 
                $VendorCode = $Service->item($z)->getAttribute("VendorCode");
                $ServiceServiceID = $Service->item($z)->getAttribute("TourActivityAdditionalServiceServiceID");
                $TourActivityDay = $Service->item($z)->getAttribute("TourActivityDay");
                $ServiceID = $Service->item($z)->getAttribute("TourActivityAdditionalServiceID");

                $ServiceCode = $Service->item($z)->getElementsByTagName("TourActivityAdditionalServiceCode");
                if ($ServiceCode->length > 0) {
                    $ServiceCode = $ServiceCode->item(0)->nodeValue;
                } else {
                    $ServiceCode = "";
                }
                $ServiceDescription = $Service->item($z)->getElementsByTagName("TourActivityAdditionalServiceDescription");
                if ($ServiceDescription->length > 0) {
                    $ServiceDescription = $ServiceDescription->item(0)->nodeValue;
                } else {
                    $ServiceDescription = "";
                }
                $ServiceName = $Service->item($z)->getElementsByTagName("TourActivityAdditionalServiceName");
                if ($ServiceName->length > 0) {
                    $ServiceName = $ServiceName->item(0)->nodeValue;
                } else {
                    $ServiceName = "";
                }
                $ServiceTypeID = $Service->item($z)->getElementsByTagName("TourActivityAdditionalServiceTypeID");
                if ($ServiceTypeID->length > 0) {
                    $ServiceTypeID = $ServiceTypeID->item(0)->nodeValue;
                } else {
                    $ServiceTypeID = "";
                }
                $ServiceType = $Service->item($z)->getElementsByTagName("TourActivityAdditionalServiceType");
                if ($ServiceType->length > 0) {
                    $ServiceType = $ServiceType->item(0)->nodeValue;
                } else {
                    $ServiceType = "";
                }
                echo $return;
                echo "ServiceType: " . $ServiceType;
                echo $return;
                $ServiceOperationTimes = $Service->item($z)->getElementsByTagName("TourActivityAdditionalServiceOperationTimes");
                if ($ServiceOperationTimes->length > 0) {
                    $ServiceOperationTime = $ServiceOperationTimes->item(0)->getElementsByTagName("TourActivityAdditionalServiceOperationTime");
                    if ($ServiceOperationTime->length > 0) {
                        $Mon = $ServiceOperationTime->item(0)->getAttribute("Mon");
                        $Tue = $ServiceOperationTime->item(0)->getAttribute("Tue");
                        $Weds = $ServiceOperationTime->item(0)->getAttribute("Weds");
                        $Thur = $ServiceOperationTime->item(0)->getAttribute("Thur");
                        $Fri = $ServiceOperationTime->item(0)->getAttribute("Fri");
                        $Sat = $ServiceOperationTime->item(0)->getAttribute("Sat");
                        $Sun = $ServiceOperationTime->item(0)->getAttribute("Sun");
                    } else {
                        $Mon = "";
                        $Tue = "";
                        $Weds = "";
                        $Thur = "";
                        $Fri = "";
                        $Sat = "";
                        $Sun = "";
                    }
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('serviceOperationTimes');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'VendorCode' => $VendorCode,
                        'TourActivityDay' => $TourActivityDay,
                        'ServiceServiceID' => $ServiceServiceID,
                        'ServiceID' => $ServiceID,
                        'ServiceCode' => $ServiceCode,
                        'ServiceDescription' => $ServiceDescription,
                        'ServiceName' => $ServiceName,
                        'ServiceTypeID' => $ServiceTypeID,
                        'ServiceType' => $ServiceType,
                        'Mon' => $Mon,
                        'Tue' => $Tue,
                        'Weds' => $Weds,
                        'Thur' => $Thur,
                        'Fri' => $Fri,
                        'Sat' => $Sat,
                        'Sun' => $Sun,
                        'TourActivityID' => $TourActivityID
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO SOT: " . $e;
                    echo $return;
                }

            }
        }
    }


    //Schedule
    $Schedule = $node->item($i)->getElementsByTagName("Schedule");
    if ($Schedule->length > 0) {
        $Summary = $Schedule->item(0)->getElementsByTagName("Summary");
        if ($Summary->length > 0) {
            $DurationSummary = $Summary->item(0)->getAttribute("Duration");
            $StartSummary = $Summary->item(0)->getAttribute("Start");
        } else {
            $DurationSummary = "";
            $StartSummary = "";
        }
        echo $return;
        echo "StartSummary: " . $StartSummary;
        echo $return;
        $Detail = $Schedule->item(0)->getElementsByTagName("Detail");
        if ($Detail->length > 0) {
            $StartDetail = $Detail->item(0)->getAttribute("Start");
            $OperationTimes = $Detail->item(0)->getElementsByTagName("OperationTimes");
            if ($OperationTimes->length > 0) {
                $OperationTime = $OperationTimes->item(0)->getElementsByTagName("OperationTime");
                if ($OperationTime->length > 0) {
                    $StartOperationTime = $OperationTime->item(0)->getAttribute("Start");
                    $Mon = $OperationTime->item(0)->getAttribute("Mon");
                    $Tue = $OperationTime->item(0)->getAttribute("Tue");
                    $Weds = $OperationTime->item(0)->getAttribute("Weds");
                    $Thur = $OperationTime->item(0)->getAttribute("Thur");
                    $Fri = $OperationTime->item(0)->getAttribute("Fri");
                    $Sat = $OperationTime->item(0)->getAttribute("Sat");
                    $Sun = $OperationTime->item(0)->getAttribute("Sun");
                } else {
                    $StartOperationTime = "";
                    $Mon = "";
                    $Tue = "";
                    $Weds = "";
                    $Thur = "";
                    $Fri = "";
                    $Sat = "";
                    $Sun = "";
                }
                
            }
        } else {
            $StartDetail = "";
        }
    }

    //CategoryAndType
    $Type2 = "";
    $CategoryAndType = $node->item($i)->getElementsByTagName("CategoryAndType");
    if ($CategoryAndType->length > 0) {
        $Type = $CategoryAndType->item(0)->getElementsByTagName("Type");
        if ($Type->length > 0) {
            $Code = $Type->item(0)->getAttribute("Code");
            $Type2 = $Type->item(0)->nodeValue;
        } else {
            $Code = "";
            $Type2 = "";
        }
        echo $return;
        echo "Type2: " . $Type2;
        echo $return;
    }

    $Description = $node->item($i)->getElementsByTagName("Description");
    if ($Description->length > 0) {
        $LongDescription = $Description->item(0)->getElementsByTagName("LongDescription");
        if ($LongDescription->length > 0) {
            $LongDescription = $LongDescription->item(0)->nodeValue;
        } else {
            $LongDescription = "";
        }
    }
    echo $return;
    echo "LongDescription: " . $LongDescription;
    echo $return;

    $Location = $node->item($i)->getElementsByTagName("Location");
    if ($Location->length > 0) {
        $Address = $Location->item(0)->getElementsByTagName("Address");
        if ($Address->length > 0) {
            $CityName = $Address->item(0)->getElementsByTagName("CityName");
            if ($CityName->length > 0) {
                $CityName = $CityName->item(0)->nodeValue;
            } else {
                $CityName = "";
            }
            echo $return;
            echo "CityName: " . $CityName;
            echo $return;
            $StateProv2 = "";
            $StateProv = $Address->item(0)->getElementsByTagName("StateProv");
            if ($StateProv->length > 0) {
                $StateCode = $StateProv->item(0)->getAttribute("StateCode");
                $StateProv2 = $StateProv->item(0)->nodeValue;
            } else {
                $StateProv2 = "";
            }
            $CountryName2 = "";
            $CountryName = $Address->item(0)->getElementsByTagName("CountryName");
            if ($CountryName->length > 0) {
                $CountryCode = $CountryName->item(0)->getAttribute("Code");
                $CountryName2 = $CountryName->item(0)->nodeValue;
            } else {
                $CountryName2 = "";
            }
            echo $return;
            echo "CountryName: " . $CountryName2;
            echo $return;
        }
    }
    echo $return;
    echo "PASSOU<br/> ";
    echo $return;
    //Pricing
    $Pricing = $node->item($i)->getElementsByTagName("Pricing");
    if ($Pricing->length > 0) {
        $Summary = $Pricing->item(0)->getElementsByTagName("Summary");
        if ($Summary->length > 0) {
            $CurrencyCode = $Summary->item(0)->getAttribute("CurrencyCode");
            $DecimalPlaces = $Summary->item(0)->getAttribute("DecimalPlaces");
            $Amount = $Summary->item(0)->getAttribute("Amount");
            $AmountBeforeTax = $Summary->item(0)->getAttribute("AmountBeforeTax");
            $AmountAfterTax = $Summary->item(0)->getAttribute("AmountAfterTax");
            echo $return;
            echo "AmountAfterTax: " . $AmountAfterTax;
            echo $return;
            $PricingType = $Summary->item(0)->getElementsByTagName("PricingType");
            if ($PricingType->length > 0) {
                $PricingType = $PricingType->item(0)->nodeValue;
            } else {
                $PricingType = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('citytours_info');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'BrokerCode' => $BrokerCode,
                    'TourActivityAmountPeople' => $TourActivityAmountPeople,
                    'TourActivityContractPriceID' => $TourActivityContractPriceID,
                    'TourActivityRoomType' => $TourActivityRoomType,
                    'TourActivityRoomTypeID' => $TourActivityRoomTypeID,
                    'Name' => $Name,
                    'TourActivityID' => $TourActivityID,
                    'IssueVoucher' => $IssueVoucher,
                    'PassengerWeight' => $PassengerWeight,
                    'AwaitingConfirmationVendor' => $AwaitingConfirmationVendor,
                    'TourActivityServiceType' => $TourActivityServiceType,
                    'TourActivityContractID' => $TourActivityContractID,
                    'VendorCode' => $VendorCode,
                    'TourActivityCode' => $TourActivityCode,
                    'TourActivityContractAvailID' => $TourActivityContractAvailID,
                    'DurationSummary' => $DurationSummary,
                    'StartSummary' => $StartSummary,
                    'StartDetail' => $StartDetail,
                    'StartOperationTime' => $StartOperationTime,
                    'Mon' => $Mon,
                    'Tue' => $Tue,
                    'Weds' => $Weds,
                    'Thur' => $Thur,
                    'Fri' => $Fri,
                    'Sat' => $Sat,
                    'Sun' => $Sun,
                    'Code' => $Code,
                    'Type' => $Type2,
                    'LongDescription' => $LongDescription,
                    'CityName' => $CityName,
                    'StateCode' => $StateCode,
                    'StateProv' => $StateProv2,
                    'CountryCode' => $CountryCode,
                    'CountryName' => $CountryName2,
                    'CurrencyCode' => $CurrencyCode,
                    'DecimalPlaces' => $DecimalPlaces,
                    'Amount' => $Amount,
                    'AmountBeforeTax' => $AmountBeforeTax,
                    'AmountAfterTax' => $AmountAfterTax,
                    'PricingType' => $PricingType
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
            echo $return;
            echo "PASSOU 2<br/> ";
            echo $return;
        }

        //ParticipantCategory
        $ParticipantCategory = $Pricing->item(0)->getElementsByTagName("ParticipantCategory");
        if ($ParticipantCategory->length > 0) {
            for ($iAux=0; $iAux < $ParticipantCategory->length; $iAux++) { 
                $QualifierInfo = $ParticipantCategory->item($iAux)->getElementsByTagName("QualifierInfo");
                if ($QualifierInfo->length > 0) {
                    $QualifierInfo = $QualifierInfo->item(0)->nodeValue;
                } else {
                    $QualifierInfo = "";
                }
                echo $return;
                echo "QualifierInfo: " . $QualifierInfo;
                echo $return;
                $Price = $ParticipantCategory->item($iAux)->getElementsByTagName("Price");
                if ($Price->length > 0) {
                    $CurrencyCodeP = $Price->item(0)->getAttribute("CurrencyCode");
                    $DecimalPlacesP = $Price->item(0)->getAttribute("DecimalPlaces");
                    $AmountP = $Price->item(0)->getAttribute("Amount");
                    $AmountAfterTaxP = $Price->item(0)->getAttribute("AmountAfterTax");
                } else {
                    $CurrencyCodeP = "";
                    $DecimalPlacesP = "";
                    $AmountP = "";
                }
                echo $return;
                echo "AmountP: " . $AmountP;
                echo $return;

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('participantCategory');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'QualifierInfo' => $QualifierInfo,
                        'CurrencyCode' => $CurrencyCodeP,
                        'DecimalPlaces' => $DecimalPlacesP,
                        'Amount' => $AmountP,
                        'AmountAfterTax' => $AmountAfterTaxP,
                        'TourActivityID' => $TourActivityID
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO P: " . $e;
                    echo $return;
                }
            }
        }
    }
    echo $return;
    echo "PASSOU 3";
    echo $return;
    //Policies
    $Policies = $node->item($i)->getElementsByTagName("Policies");
    if ($Policies->length > 0) {
        $Cancel = $Policies->item(0)->getElementsByTagName("Cancel");
        if ($Cancel->length > 0) {
            $TourActivityPolicyTypeCancel = $Cancel->item(0)->getElementsByTagName("TourActivityPolicyTypeCancel");
            if ($TourActivityPolicyTypeCancel->length > 0) {
                $TourActivityPolicyTypeCancel2 = "";
                for ($t=0; $t < $TourActivityPolicyTypeCancel->length; $t++) { 
                    $NonRefundable = $TourActivityPolicyTypeCancel->item($t)->getAttribute("NonRefundable");
                    echo $return;
                    echo "NonRefundable P: " . $NonRefundable;
                    echo $return;
                    $TourActivityPolicyTypeCancel2 = $TourActivityPolicyTypeCancel->item($t)->nodeValue;
                    $CancelDeadline = $TourActivityPolicyTypeCancel->item($t)->getElementsByTagName("CancelDeadline");
                    if ($CancelDeadline->length > 0) {
                        $OffsetTimeUnit = $CancelDeadline->item(0)->getAttribute("OffsetTimeUnit");
                        $AbsoluteDeadline = $CancelDeadline->item(0)->getAttribute("AbsoluteDeadline");
                    }
                    $AmountPercent = $TourActivityPolicyTypeCancel->item($t)->getElementsByTagName("AmountPercent");
                    if ($AmountPercent->length > 0) {
                        $Amount = $AmountPercent->item(0)->getAttribute("Amount");
                    }
                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('reservation_policies');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'NonRefundable' => $NonRefundable,
                            'TourActivityPolicyTypeCancel' => $TourActivityPolicyTypeCancel2,
                            'OffsetTimeUnit' => $OffsetTimeUnit,
                            'AbsoluteDeadline' => $AbsoluteDeadline,
                            'Amount' => $Amount,
                            'TourActivityID' => $TourActivityID
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO POL: " . $e;
                        echo $return;
                    }
                }
            } else {
                $TourActivityPolicyTypeCancel = "";
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