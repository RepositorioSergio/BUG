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
echo "COMECOU RESERVATION<br/>";
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

$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );

$raw = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
  <ServiceReservationRead xmlns="http://tempuri.org/">
<OTA_ReadRQ PrimaryLangID="en-us" Target="Test" TimeStamp="' . $timestamp . '" Version="3.0" xmlns="http://www.opentravel.org/OTA/2003/05">
<POS>
  <Source PseudoCityCode="NONE">
    <RequestorID ID="TESTID" Type="TD"/>
    <TPA_Extensions>
      <Provider>
        <System>' . $citytourspackagesSystem . '</System>
        <Userid>' . $citytourspackagesuser . '</Userid>
        <Password>' . $citytourspackagespassword . '</Password>         
      </Provider>
    </TPA_Extensions>
  </Source>
</POS>
<ReadRequests>
  <ReadRequest>
    <UniqueID ID="1121" Type="14" BrokerCode="7" />
  </ReadRequest>
</ReadRequests>
</OTA_ReadRQ>
</ServiceReservationRead>
  </soap:Body>
</soap:Envelope>';
echo $raw;

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
    "SOAPAction: http://tempuri.org/ServiceReservationRead",
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
die();
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
$OTA_TourActivityBookRS = $inputDoc->getElementsByTagName("OTA_TourActivityBookRS");
$ReservationDetails = $OTA_TourActivityBookRS->item(0)->getElementsByTagName("ReservationDetails");


//TourActivityRooms
$TourActivityRooms = $ReservationDetails->item(0)->getElementsByTagName("TourActivityRooms");
if ($TourActivityRooms->length > 0) {
    $TourActivityRoom = $TourActivityRooms->item(0)->getElementsByTagName("TourActivityRoom");
    if ($TourActivityRoom->length > 0) {
        $TourActivityRoomTypeID = $TourActivityRoom->item(0)->getAttribute("TourActivityRoomTypeID");
    }else {
        $TourActivityRoomTypeID = "";
    }
}

//BasicInfo
$BasicInfo = $ReservationDetails->item(0)->getElementsByTagName("BasicInfo");
if ($BasicInfo->length > 0) {
    $Name = $BasicInfo->item(0)->getAttribute("Name");
    $TourActivityID = $BasicInfo->item(0)->getAttribute("TourActivityID");
    $TourActivityContractPriceID = $BasicInfo->item(0)->getAttribute("TourActivityContractPriceID");
    $TourActivityContractAvailID = $BasicInfo->item(0)->getAttribute("TourActivityContractAvailID");
}else {
    $Name = "";
    $TourActivityID = "";
    $TourActivityContractPriceID = "";
    $TourActivityContractAvailID = "";
}

//Confirmation
$Confirmation = $ReservationDetails->item(0)->getElementsByTagName("Confirmation");
if ($Confirmation->length > 0) {
    $ID = $Confirmation->item(0)->getAttribute("ID");
}else {
    $ID = "";
}

//Schedule
$Schedule = $ReservationDetails->item(0)->getElementsByTagName("Schedule");
if ($Schedule->length > 0) {
    $Start = $Schedule->item(0)->getAttribute("Start");
    $Duration = $Schedule->item(0)->getAttribute("Duration");
}else {
    $Start = "";
    $Duration = "";
}

//Location
$Location = $ReservationDetails->item(0)->getElementsByTagName("Location");
if ($Location->length > 0) {
    $Address = $Location->item(0)->getElementsByTagName("Address");
    if ($Address->length > 0) {
        $CityCode = $Address->item(0)->getElementsByTagName("CityCode");
        if ($CityCode->length > 0) {
            $CityCode = $CityCode->item(0)->nodeValue;
        } else {
            $CityCode = "";
        }   
    }
}

//Pricing
$Pricing = $ReservationDetails->item(0)->getElementsByTagName("Pricing");
if ($Pricing->length > 0) {
    $Summary = $Pricing->item(0)->getElementsByTagName("Summary");
    if ($Summary->length > 0) {
        $CurrencyCode = $Summary->item(0)->getAttribute("CurrencyCode");
        $DecimalPlaces = $Summary->item(0)->getAttribute("DecimalPlaces");
        $Amount = $Summary->item(0)->getAttribute("Amount");
    }else {
        $CurrencyCode = "";
        $DecimalPlaces = "";
        $Amount = "";
    }
}


//ParticipantInfo
$ParticipantInfo = $ReservationDetails->item(0)->getElementsByTagName("ParticipantInfo");
if ($ParticipantInfo->length > 0) {
    $Individual = $ParticipantInfo->item(0)->getElementsByTagName("Individual");
    if ($Individual->length > 0) {
        $GivenName = $Individual->item(0)->getElementsByTagName("GivenName");
        if ($GivenName->length > 0) {
            $GivenName = $GivenName->item(0)->nodeValue;
        } else {
            $GivenName = "";
        } 
        $Surname = $Individual->item(0)->getElementsByTagName("Surname");
        if ($Surname->length > 0) {
            $Surname = $Surname->item(0)->nodeValue;
        } else {
            $Surname = "";
        } 
    }

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('reservation_info');
    $insert->values(array(
        'ID' => $ID,
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'TourActivityRoomTypeID' => $TourActivityRoomTypeID,
        'Name' => $Name,
        'TourActivityID' => $TourActivityID,
        'TourActivityContractPriceID' => $TourActivityContractPriceID,
        'TourActivityContractAvailID' => $TourActivityContractAvailID,
        'Start' => $Start,
        'Duration' => $Duration,
        'CityCode' => $CityCode,
        'CurrencyCode' => $CurrencyCode,
        'DecimalPlaces' => $DecimalPlaces,
        'Amount' => $Amount,
        'GivenName' => $GivenName,
        'Surname' => $Surname
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();

    $Categories = $ParticipantInfo->item(0)->getElementsByTagName("Categories");
    if ($Categories->length > 0) {
        $Category = $Categories->item(0)->getElementsByTagName("Category");
        if ($Category->length > 0) {
            for ($l=0; $l < $Category->length; $l++) { 
                $Quantity = $Category->item($l)->getAttribute("Quantity");
                $QualifierInfo = $Category->item($l)->getElementsByTagName("QualifierInfo");
                if ($QualifierInfo->length > 0) {
                    $QualifierInfo = $QualifierInfo->item(0)->nodeValue;
                } else {
                    $QualifierInfo = "";
                }

                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('reservation_category');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Quantity' => $Quantity,
                    'QualifierInfo' => $QualifierInfo,
                    'IDConfirmation' => $ID
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            }
        } else {
            $Quantity = "";
        }
        
    }
}




$TourActivityAdditionalServices = $ReservationDetails->item(0)->getElementsByTagName("TourActivityAdditionalServices");
$node = $TourActivityAdditionalServices->item(0)->getElementsByTagName("TourActivityAdditionalService");
echo $return;
echo "TAM: " . $node->length;
echo $return;
for ($i = 0; $i < $node->length; $i++) {
    $TourActivityAdditionalServiceID = $node->item($i)->getAttribute("TourActivityAdditionalServiceID");
    $TourActivityDay = $node->item($i)->getAttribute("TourActivityDay");
    $TourActivityAdditionalServiceServiceID = $node->item($i)->getAttribute("TourActivityAdditionalServiceServiceID");
    $AdditionalServiceDate = $node->item($i)->getAttribute("AdditionalServiceDate");
    $TourActivityAdditionalServiceTypeID = $node->item($i)->getElementsByTagName("TourActivityAdditionalServiceTypeID");
    if ($TourActivityAdditionalServiceTypeID->length > 0) {
        $TourActivityAdditionalServiceTypeID = $TourActivityAdditionalServiceTypeID->item(0)->nodeValue;
    } else {
        $TourActivityAdditionalServiceTypeID = "";
    }
    $TourActivityAdditionalServiceName = $node->item($i)->getElementsByTagName("TourActivityAdditionalServiceName");
    if ($TourActivityAdditionalServiceName->length > 0) {
        $TourActivityAdditionalServiceName = $TourActivityAdditionalServiceName->item(0)->nodeValue;
    } else {
        $TourActivityAdditionalServiceName = "";
    }
    //TourActivityAdditionalServiceAirSegment
    $TourActivityAdditionalServiceAirSegment = $node->item($i)->getElementsByTagName("TourActivityAdditionalServiceAirSegment");
    if ($TourActivityAdditionalServiceAirSegment->length > 0) {
        $ArrivalHour = $TourActivityAdditionalServiceAirSegment->item(0)->getAttribute("ArrivalHour");
        $DepartureHour = $TourActivityAdditionalServiceAirSegment->item(0)->getAttribute("DepartureHour");

        $DepartureAirport = $TourActivityAdditionalServiceAirSegment->item(0)->getElementsByTagName("DepartureAirport");
        if ($DepartureAirport->length > 0) {
            $LocationCodeDeparture = $DepartureAirport->item(0)->getAttribute("LocationCode");
        } else {
            $LocationCodeDeparture = "";
        }
        $ArrivalAirport = $TourActivityAdditionalServiceAirSegment->item(0)->getElementsByTagName("ArrivalAirport");
        if ($ArrivalAirport->length > 0) {
            $LocationCodeArrival = $ArrivalAirport->item(0)->getAttribute("LocationCode");
        } else {
            $LocationCodeArrival = "";
        }
        $OperatingAirline = $TourActivityAdditionalServiceAirSegment->item(0)->getElementsByTagName("OperatingAirline");
        if ($OperatingAirline->length > 0) {
            $CompanyShortName = $OperatingAirline->item(0)->getAttribute("CompanyShortName");
            $Code = $OperatingAirline->item(0)->getAttribute("Code");
            $FlightNumber = $OperatingAirline->item(0)->getAttribute("FlightNumber");
        } else {
            $CompanyShortName = "";
            $Code = "";
            $FlightNumber = "";
        }

        $ArrivalLocation = $TourActivityAdditionalServiceAirSegment->item(0)->getElementsByTagName("ArrivalLocation");
        if ($ArrivalLocation->length > 0) {
            $Address = $ArrivalLocation->item(0)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $CityNameArrival = $Address->item(0)->getElementsByTagName("CityName");
                if ($CityNameArrival->length > 0) {
                    $CityNameArrival = $CityNameArrival->item(0)->nodeValue;
                } else {
                    $CityNameArrival = "";
                }
                $CityCodeArrival = $Address->item(0)->getElementsByTagName("CityCode");
                if ($CityCodeArrival->length > 0) {
                    $CityCodeArrival = $CityCodeArrival->item(0)->nodeValue;
                } else {
                    $CityCodeArrival = "";
                }
                $StateProvArrival = $Address->item(0)->getElementsByTagName("StateProv");
                if ($StateProvArrival->length > 0) {
                    $StateProvArrival = $StateProvArrival->item(0)->nodeValue;
                } else {
                    $StateProvArrival = "";
                }
                $CountryNameArrival = $Address->item(0)->getElementsByTagName("CountryName");
                if ($CountryNameArrival->length > 0) {
                    $CodeArrival = $CountryNameArrival->item(0)->getAttribute("Code");
                    $CountryNameArrival = $CountryNameArrival->item(0)->nodeValue;
                } else {
                    $CountryNameArrival = "";
                }
            }
        }

        $DepartureLocation = $TourActivityAdditionalServiceAirSegment->item(0)->getElementsByTagName("DepartureLocation");
        if ($DepartureLocation->length > 0) {
            $Address = $DepartureLocation->item(0)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $CityNameDeparture = $Address->item(0)->getElementsByTagName("CityName");
                if ($CityNameDeparture->length > 0) {
                    $CityNameDeparture = $CityNameDeparture->item(0)->nodeValue;
                } else {
                    $CityNameDeparture = "";
                }
                $CityCodeDeparture = $Address->item(0)->getElementsByTagName("CityCode");
                if ($CityCodeDeparture->length > 0) {
                    $CityCodeDeparture = $CityCodeDeparture->item(0)->nodeValue;
                } else {
                    $CityCodeDeparture = "";
                }
                $StateProvDeparture = $Address->item(0)->getElementsByTagName("StateProv");
                if ($StateProvDeparture->length > 0) {
                    $StateProvDeparture = $StateProvDeparture->item(0)->nodeValue;
                } else {
                    $StateProvDeparture = "";
                }
                $CountryNameDeparture = $Address->item(0)->getElementsByTagName("CountryName");
                if ($CountryNameDeparture->length > 0) {
                    $CodeDeparture = $CountryNameDeparture->item(0)->getAttribute("Code");
                    $CountryNameDeparture = $CountryNameDeparture->item(0)->nodeValue;
                } else {
                    $CountryNameDeparture = "";
                }
            }
        }
    }

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('reservation');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'TourActivityDay' => $TourActivityDay,
        'TourActivityAdditionalServiceID' => $TourActivityAdditionalServiceID,
        'TourActivityAdditionalServiceServiceID' => $TourActivityAdditionalServiceServiceID,
        'AdditionalServiceDate' => $AdditionalServiceDate,
        'TourActivityAdditionalServiceTypeID' => $TourActivityAdditionalServiceTypeID,
        'TourActivityAdditionalServiceName' => $TourActivityAdditionalServiceName,
        'ArrivalHour' => $ArrivalHour,
        'DepartureHour' => $DepartureHour,
        'LocationCodeDeparture' => $LocationCodeDeparture,
        'LocationCodeArrival' => $LocationCodeArrival,
        'CompanyShortName' => $CompanyShortName,
        'Code' => $Code,
        'FlightNumber' => $FlightNumber,
        'CityNameArrival' => $CityNameArrival,
        'CityCodeArrival' => $CityCodeArrival,
        'StateProvArrival' => $StateProvArrival,
        'CodeArrival' => $CodeArrival,
        'CountryNameArrival' => $CountryNameArrival,
        'CityNameDeparture' => $CityNameDeparture,
        'CityCodeDeparture' => $CityCodeDeparture,
        'StateProvDeparture' => $StateProvDeparture,
        'CodeDeparture' => $CodeDeparture,
        'CountryNameDeparture' => $CountryNameDeparture,
        'IDConfirmation' => $ID
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>