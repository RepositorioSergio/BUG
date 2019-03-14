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
echo "COMECOU CANCEL ITEM RESERVA SIATAR<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xnet="http://xnetinfo.org/">
<soap:Header/>
<soap:Body>
   <xnet:getRes>
      <xnet:aRequest EchoToken="123" TimeStamp="2019-02-28T17:43:25.315" Version="1.0">
         <xnet:POS>
            <xnet:Source>
               <xnet:RequestorID ID="a6dge3!tnsf2or" PartnerID="TEST" Username="xnet" Password="pctnx!!!"/>
            </xnet:Source>
         </xnet:POS>
         <xnet:UniqueID>     
            <xnet:ID>398</xnet:ID>     
            <xnet:Type>Reservation</xnet:Type>   
         </xnet:UniqueID>
    </xnet:aRequest>
    </xnet:getRes>
</soap:Body>
</soap:Envelope>';
echo "<br/> RAW:" . $raw;
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
    "Content-length: ".strlen($raw)
));
$url = "http://xnetinfo.redirectme.net:8080/homologacao_webservice/Integration/ServerIntegration.asmx";
echo "<br/> PASSOU URL";
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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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
$getResResponse = $Body->item(0)->getElementsByTagName("getResResponse");
$getResResult = $getResResponse->item(0)->getElementsByTagName("getResResult");
$ReservationsType = $getResResult->item(0)->getElementsByTagName("ReservationsType");
$Reservations = $ReservationsType->item(0)->getElementsByTagName("Reservations");
$node = $Reservations->item(0)->getElementsByTagName("Reservation");
for ($i=0; $i < $node->length; $i++) { 
    $ID = $node->item($i)->getAttribute("ID");
    $UserPartnerID = $node->item($i)->getAttribute("UserPartnerID");
    $CancelCost = $node->item($i)->getAttribute("CancelCost");
    $Status = $node->item($i)->getAttribute("Status");
    $CreateDateTime = $node->item($i)->getAttribute("CreateDateTime");

    $ServiceReservations = $node->item($i)->getElementsByTagName("ServiceReservations");
    if ($ServiceReservations->length > 0) {
        $ServiceReservations = $ServiceReservations->item(0)->nodeValue;
    } else {
        $ServiceReservations = "";
    }

    $CancelCostCurrency = $node->item($i)->getElementsByTagName("CancelCostCurrency");
    if ($CancelCostCurrency->length > 0) {
        $Code = $CancelCostCurrency->item(0)->getAttribute("Code");
        $Name = $CancelCostCurrency->item(0)->getAttribute("Name");
    }

    $HotelReservations = $node->item($i)->getElementsByTagName("HotelReservations");
    if ($HotelReservations->length > 0) {
        $HotelReservation = $HotelReservations->item(0)->getElementsByTagName("HotelReservation");
        if ($HotelReservation->length > 0) {
            $HotelReservationID = $HotelReservation->item(0)->getAttribute("ID");
            $HotelReservationStatus = $HotelReservation->item(0)->getAttribute("Status");

            $RoomStay = $HotelReservation->item(0)->getElementsByTagName("RoomStay");
            $Hotel = $RoomStay->item(0)->getElementsByTagName("Hotel");
            if ($Hotel->length > 0) {
                $HotelCode = $Hotel->item(0)->getAttribute("Code");
                $HotelName = $Hotel->item(0)->getAttribute("Name");
                $HotelStarRating = $Hotel->item(0)->getAttribute("StarRating");
                $MinAccommodationRate = $Hotel->item(0)->getAttribute("MinAccommodationRate");
                $MaxAccommodationRate = $Hotel->item(0)->getAttribute("MaxAccommodationRate");
            } else {
                $HotelCode = "";
                $HotelName = "";
                $HotelStarRating = "";
                $MinAccommodationRate = "";
                $MaxAccommodationRate = "";
            }

            $RoomRates = $RoomStay->item(0)->getElementsByTagName("RoomRates");
            if ($RoomRates->length > 0) {
                $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                if ($RoomRate->length > 0) {
                    $HasAllIncluded = $RoomRate->item(0)->getAttribute("HasAllIncluded");
                    $HasBkftIncluded = $RoomRate->item(0)->getAttribute("HasBkftIncluded");
                    $HasFapIncluded = $RoomRate->item(0)->getAttribute("HasFapIncluded");
                    $HasMapIncluded = $RoomRate->item(0)->getAttribute("HasMapIncluded");
                    $CancelCost = $RoomRate->item(0)->getAttribute("CancelCost");
                    $DailyCostCancel = $RoomRate->item(0)->getAttribute("DailyCostCancel");
                    $DeadLineCancel = $RoomRate->item(0)->getAttribute("DeadLineCancel");
                    $ChargingUnit = $RoomRate->item(0)->getAttribute("ChargingUnit");
                    $TotalValue = $RoomRate->item(0)->getAttribute("TotalValue");

                    $Currency = $RoomRate->item(0)->getElementsByTagName("Currency");
                    if ($Currency->length > 0) {
                        $CurrencyCode = $Currency->item(0)->getAttribute("Code");
                        $CurrencyName = $Currency->item(0)->getAttribute("Name");
                    } else {
                        $CurrencyCode = "";
                        $CurrencyName = "";
                    }
                    $Market = $RoomRate->item(0)->getElementsByTagName("Market");
                    if ($Market->length > 0) {
                        $MarketCode = $Market->item(0)->getAttribute("Code");
                    } else {
                        $MarketCode = "";
                    }

                    //RoomType
                    $RoomType = $RoomRate->item($k)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $RoomTypeCode = $RoomType->item(0)->getAttribute("Code");
                        $RoomTypeName = $RoomType->item(0)->getAttribute("Name");

                        $RoomsOccupants = $RoomType->item(0)->getElementsByTagName("RoomsOccupants");
                        $RoomOccupants = $RoomsOccupants->item(0)->getElementsByTagName("RoomOccupants");
                        $RoomRateOccupants = $RoomOccupants->item(0)->getElementsByTagName("RoomRateOccupants");
                        if ($RoomRateOccupants->length > 0) {
                            $OccupantsID = $RoomRateOccupants->item(0)->getAttribute("OccupantsID");
                            $IsImmediateConfirmation = $RoomRateOccupants->item(0)->getAttribute("IsImmediateConfirmation");
                            $TotalValueRate = $RoomRateOccupants->item(0)->getAttribute("TotalValue");

                            $AccommodationRate = $RoomRateOccupants->item(0)->getElementsByTagName("AccommodationRate");
                            if ($AccommodationRate->length > 0) {
                                $Occupation = $AccommodationRate->item(0)->getAttribute("Occupation");

                                $DailyRate = $AccommodationRate->item(0)->getElementsByTagName("DailyRate");
                                if ($DailyRate->length > 0) {
                                    $TotalValueAcc = $DailyRate->item(0)->getAttribute("TotalValue");
                                    $DailyValue = $DailyRate->item(0)->getAttribute("DailyValue");
                                    $End = $DailyRate->item(0)->getAttribute("End");
                                    $Start = $DailyRate->item(0)->getAttribute("Start");
                                }

                            } else {
                                $Occupation = "";
                            }      
                        } else {
                            $OccupantsID = "";
                            $IsImmediateConfirmation = "";
                            $TotalValueRate = "";
                        }
                    } else {
                        $RoomTypeCode = "";
                        $RoomTypeName = "";
                    }
                }
            }
        }
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('consultar_reserva');
        $insert->values(array(
            'ID' => $ID,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'UserPartnerID' => $UserPartnerID,
            'CancelCost' => $CancelCost,
            'Status' => $Status,
            'ServiceReservations' => $ServiceReservations,
            'Code' => $Code,
            'Name' => $Name,
            'HotelReservationID' => $HotelReservationID,
            'HotelReservationStatus' => $HotelReservationStatus,
            'HotelCode' => $HotelCode,
            'HotelName' => $HotelName,
            'HotelStarRating' => $HotelStarRating,
            'MinAccommodationRate' => $MinAccommodationRate,
            'MaxAccommodationRate' => $MaxAccommodationRate,
            'HasAllIncluded' => $HasAllIncluded,
            'HasBkftIncluded' => $HasBkftIncluded,
            'HasFapIncluded' => $HasFapIncluded,
            'HasMapIncluded' => $HasMapIncluded,
            'DailyCostCancel' => $DailyCostCancel,
            'DeadLineCancel' => $DeadLineCancel,
            'ChargingUnit' => $ChargingUnit,
            'TotalValue' => $TotalValue,
            'CurrencyCode' => $CurrencyCode,
            'CurrencyName' => $CurrencyName,
            'RoomTypeCode' => $RoomTypeCode,
            'RoomTypeName' => $RoomTypeName,
            'OccupantsID' => $OccupantsID,
            'IsImmediateConfirmation' => $IsImmediateConfirmation,
            'TotalValueRate' => $TotalValueRate,
            'Occupation' => $Occupation,
            'TotalValueAcc' => $TotalValueAcc,
            'DailyValue' => $DailyValue,
            'End' => $End,
            'Start' => $Start
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO CONSULTA: " . $e;
        echo $return;
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>