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
echo "COMECOU HOTELBOOK<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$user = 'wingstest';
$pass = 'Win@59491374';

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <hot:Credentials UserName="' . $user . '" Password="' . $pass . '"> </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/HotelBookingDetailBasedOnDate</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelBookingDetailBasedOnDateRequest>
        <hot:FromDate>2018-12-27</hot:FromDate>
        <hot:ToDate>2019-01-27</hot:ToDate>
    </hot:HotelBookingDetailBasedOnDateRequest>
</soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Content-length: ".strlen($raw)
));
$url =  "https://api.tbotechnology.in/HotelAPI_V7/HotelService.svc";

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
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$hotelFacil = '';
$Attr = '';

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$HotelBookingDetailBasedOnDateResponse = $Body->item(0)->getElementsByTagName("HotelBookingDetailBasedOnDateResponse");

$BookingDetail = $HotelRoomAvailabilityResponse->item(0)->getElementsByTagName("BookingDetail");
if ($BookingDetail->length > 0) {
    $Booking = $BookingDetail->item(0)->getElementsByTagName("Booking");
    if ($Booking->length > 0) {
        for ($i=0; $i < $Booking->length; $i++) { 
            $Index = $Booking->item($i)->getElementsByTagName("Index");
            if ($Index->length > 0) {
                $Index = $Index->item(0)->nodeValue;
            } else {
                $Index = "";
            }
            $BookingId = $Booking->item($i)->getElementsByTagName("BookingId");
            if ($BookingId->length > 0) {
                $BookingId = $BookingId->item(0)->nodeValue;
            } else {
                $BookingId = "";
            }
            $ConfirmationNo = $Booking->item($i)->getElementsByTagName("ConfirmationNo");
            if ($ConfirmationNo->length > 0) {
                $ConfirmationNo = $ConfirmationNo->item(0)->nodeValue;
            } else {
                $ConfirmationNo = "";
            }
            $BookingDate = $Booking->item($i)->getElementsByTagName("BookingDate");
            if ($BookingDate->length > 0) {
                $BookingDate = $BookingDate->item(0)->nodeValue;
            } else {
                $BookingDate = "";
            }
            $Currency = $Booking->item($i)->getElementsByTagName("Currency");
            if ($Currency->length > 0) {
                $Currency = $Currency->item(0)->nodeValue;
            } else {
                $Currency = "";
            }
            $AgentMarkup = $Booking->item($i)->getElementsByTagName("AgentMarkup");
            if ($AgentMarkup->length > 0) {
                $AgentMarkup = $AgentMarkup->item(0)->nodeValue;
            } else {
                $AgentMarkup = "";
            }
            $AgencyName = $Booking->item($i)->getElementsByTagName("AgencyName");
            if ($AgencyName->length > 0) {
                $AgencyName = $AgencyName->item(0)->nodeValue;
            } else {
                $AgencyName = "";
            }
            $BookingStatus = $Booking->item($i)->getElementsByTagName("BookingStatus");
            if ($BookingStatus->length > 0) {
                $BookingStatus = $BookingStatus->item(0)->nodeValue;
            } else {
                $BookingStatus = "";
            }
            $BookingPrice = $Booking->item($i)->getElementsByTagName("BookingPrice");
            if ($BookingPrice->length > 0) {
                $BookingPrice = $BookingPrice->item(0)->nodeValue;
            } else {
                $BookingPrice = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('bookingDetailDate');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Index' => $Index,
                    'BookingId' => $BookingId,
                    'ConfirmationNo' => $ConfirmationNo,
                    'BookingDate' => $BookingDate,
                    'Currency' => $Currency,
                    'AgentMarkup' => $AgentMarkup,
                    'AgencyName' => $AgencyName,
                    'BookingStatus' => $BookingStatus,
                    'BookingPrice' => $BookingPrice
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
        }
    }
}


$HotelRooms = $HotelRoomAvailabilityResponse->item(0)->getElementsByTagName("HotelRooms");
if ($HotelRooms->length > 0) {
    $HotelRoom = $HotelRooms->item(0)->getElementsByTagName("HotelRoom");
    if ($HotelRoom->length > 0) {
        for ($i=0; $i < $HotelRoom->length; $i++) { 
            $RoomIndex = $HotelRoom->item($i)->getElementsByTagName("RoomIndex");
            if ($RoomIndex->length > 0) {
                $RoomIndex = $RoomIndex->item(0)->nodeValue;
            } else {
                $RoomIndex = "";
            }
            $RoomTypeName = $HotelRoom->item($i)->getElementsByTagName("RoomTypeName");
            if ($RoomTypeName->length > 0) {
                $RoomTypeName = $RoomTypeName->item(0)->nodeValue;
            } else {
                $RoomTypeName = "";
            }
            $Inclusion = $HotelRoom->item($i)->getElementsByTagName("Inclusion");
            if ($Inclusion->length > 0) {
                $Inclusion = $Inclusion->item(0)->nodeValue;
            } else {
                $Inclusion = "";
            }
            $RoomTypeCode = $HotelRoom->item($i)->getElementsByTagName("RoomTypeCode");
            if ($RoomTypeCode->length > 0) {
                $RoomTypeCode = $RoomTypeCode->item(0)->nodeValue;
            } else {
                $RoomTypeCode = "";
            }
            $RatePlanCode = $HotelRoom->item($i)->getElementsByTagName("RatePlanCode");
            if ($RatePlanCode->length > 0) {
                $RatePlanCode = $RatePlanCode->item(0)->nodeValue;
            } else {
                $RatePlanCode = "";
            }
            $RoomPromtion = $HotelRoom->item($i)->getElementsByTagName("RoomPromtion");
            if ($RoomPromtion->length > 0) {
                $RoomPromtion = $RoomPromtion->item(0)->nodeValue;
            } else {
                $RoomPromtion = "";
            }
            $Amenities = $HotelRoom->item($i)->getElementsByTagName("Amenities");
            if ($Amenities->length > 0) {
                $Amenities = $Amenities->item(0)->nodeValue;
            } else {
                $Amenities = "";
            }


            try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('HotelRoomAvailability');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'RoomIndex' => $RoomIndex,
                        'RoomTypeName' => $RoomTypeName,
                        'Inclusion' => $Inclusion,
                        'RoomTypeCode' => $RoomTypeCode,
                        'RatePlanCode' => $RatePlanCode,
                        'RoomPromtion' => $RoomPromtion,
                        'Amenities' => $Amenities
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

            $RoomRate = $HotelRoom->item($i)->getElementsByTagName("RoomRate");
            if ($RoomRate->length > 0) {
                $B2CRates = $RoomRate->item(0)->getAttribute("B2CRates");
                $PrefCurrency = $RoomRate->item(0)->getAttribute("PrefCurrency");
                $TotalFare = $RoomRate->item(0)->getAttribute("TotalFare");
                $PrefPrice = $RoomRate->item(0)->getAttribute("PrefPrice");
                $RoomTax = $RoomRate->item(0)->getAttribute("RoomTax");
                $AgentMarkUp = $RoomRate->item(0)->getAttribute("AgentMarkUp");
                $Currency = $RoomRate->item(0)->getAttribute("Currency");
                $RoomFare = $RoomRate->item(0)->getAttribute("RoomFare");
                $IsPackageRate = $RoomRate->item(0)->getAttribute("IsPackageRate");

                $ExtraGuestCharges = $RoomRate->item(0)->getElementsByTagName("ExtraGuestCharges");
                if ($ExtraGuestCharges->length > 0) {
                    $ExtraGuestCharges = $ExtraGuestCharges->item(0)->nodeValue;
                } else {
                    $ExtraGuestCharges = "";
                }
                $ChildCharges = $RoomRate->item(0)->getElementsByTagName("ChildCharges");
                if ($ChildCharges->length > 0) {
                    $ChildCharges = $ChildCharges->item(0)->nodeValue;
                } else {
                    $ChildCharges = "";
                }
                $Discount = $RoomRate->item(0)->getElementsByTagName("Discount");
                if ($Discount->length > 0) {
                    $Discount = $Discount->item(0)->nodeValue;
                } else {
                    $Discount = "";
                }
                $OtherCharges = $RoomRate->item(0)->getElementsByTagName("OtherCharges");
                if ($OtherCharges->length > 0) {
                    $OtherCharges = $OtherCharges->item(0)->nodeValue;
                } else {
                    $OtherCharges = "";
                }
                $ServiceTax = $RoomRate->item(0)->getElementsByTagName("ServiceTax");
                if ($ServiceTax->length > 0) {
                    $ServiceTax = $ServiceTax->item(0)->nodeValue;
                } else {
                    $ServiceTax = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('HotelRoomAvailability_RoomRate');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'ExtraGuestCharges' => $ExtraGuestCharges,
                        'ChildCharges' => $ChildCharges,
                        'Discount' => $Discount,
                        'OtherCharges' => $OtherCharges,
                        'ServiceTax' => $ServiceTax,
                        'B2CRates' => $B2CRates,
                        'PrefCurrency' => $PrefCurrency,
                        'TotalFare' => $TotalFare,
                        'PrefPrice' => $PrefPrice,
                        'RoomTax' => $RoomTax,
                        'AgentMarkUp' => $AgentMarkUp,
                        'Currency' => $Currency,
                        'RoomFare' => $RoomFare,
                        'IsPackageRate' => $IsPackageRate,
                        'RoomIndex' => $RoomIndex
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


                $DayRates = $RoomRate->item(0)->getElementsByTagName("DayRates");
                if ($DayRates->length > 0) {
                    $DayRates2 = $DayRates->item(0)->getElementsByTagName("DayRates");
                    if ($DayRates2->length > 0) {
                        for ($j=0; $j < $DayRates2->length; $j++) { 
                            $BaseFare = $DayRates2->item($j)->getAttribute("BaseFare");
                            $Date = $DayRates2->item($j)->getAttribute("Date");

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('HotelRoomAvailability_DayRates');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'BaseFare' => $BaseFare,
                                    'Date' => $Date,
                                    'RoomIndex' => $RoomIndex
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

            $Supplements = $HotelRoom->item($i)->getElementsByTagName("Supplements");
            if ($Supplements->length > 0) {
                $Supplement = $Supplements->item(0)->getElementsByTagName("Supplement");
                if ($Supplement->length > 0) {
                    for ($k=0; $k < $Supplement->length; $k++) { 
                        $SuppID = $Supplement->item($k)->getAttribute("SuppID");
                        $SuppName = $Supplement->item($k)->getAttribute("SuppName");
                        $Type = $Supplement->item($k)->getAttribute("Type");
                        $SuppIsMandatory = $Supplement->item($k)->getAttribute("SuppIsMandatory");
                        $SuppChargeType = $Supplement->item($k)->getAttribute("SuppChargeType");
                        $Price = $Supplement->item($k)->getAttribute("Price");
                        $CurrencyCode = $Supplement->item($k)->getAttribute("CurrencyCode");

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('HotelRoomAvailability_Supplements');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'SuppID' => $SuppID,
                                'SuppName' => $SuppName,
                                'Type' => $Type,
                                'SuppIsMandatory' => $SuppIsMandatory,
                                'SuppChargeType' => $SuppChargeType,
                                'Price' => $Price,
                                'CurrencyCode' => $CurrencyCode
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
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>