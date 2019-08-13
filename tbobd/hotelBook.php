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
<hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
</hot:Credentials>
<wsa:Action>http://TekTravel/HotelBookingApi/HotelBook</wsa:Action>
<wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelBookRequest>
        <hot:ClientReferenceNumber>210314135855789#kuld</hot:ClientReferenceNumber>
        <hot:GuestNationality>IN</hot:GuestNationality>
        <hot:Guests>
            <hot:Guest LeadGuest="true" GuestType="Adult" GuestInRoom="1">
                <hot:Title>Mr</hot:Title>
                <hot:FirstName>Ajay</hot:FirstName>
                <hot:LastName>testgea</hot:LastName>
                <hot:Age>20</hot:Age>
            </hot:Guest>
            <hot:Guest LeadGuest="false" GuestType="Child" GuestInRoom="1">
                <hot:Title>Mr</hot:Title>
                <hot:FirstName>Mahi</hot:FirstName>
                <hot:LastName>test</hot:LastName>
                <hot:Age>5</hot:Age>
            </hot:Guest>
            <hot:Guest LeadGuest="false" GuestType="Adult" GuestInRoom="2">
                <hot:Title>Mr</hot:Title>
                <hot:FirstName>Kuld</hot:FirstName>
                <hot:LastName>adulttwo</hot:LastName>
                <hot:Age>30</hot:Age>
            </hot:Guest>
        </hot:Guests>
        <hot:AddressInfo>
            <hot:AddressLine1>testadd1</hot:AddressLine1>
            <hot:AddressLine2>testadd2</hot:AddressLine2>
            <hot:CountryCode>91</hot:CountryCode>
            <hot:AreaCode>11</hot:AreaCode>
            <hot:PhoneNo>25869696</hot:PhoneNo>
            <hot:Email>abc@gurgaon.in</hot:Email>
            <hot:City>Delhi</hot:City>
            <hot:State>Delhi</hot:State>
            <hot:Country>India</hot:Country>
            <hot:ZipCode>256525</hot:ZipCode>
        </hot:AddressInfo>
        <!-- VoucherBooking-true Booking will be Vouchered -->
        <hot:PaymentInfo VoucherBooking="true" PaymentModeType="Limit">
        </hot:PaymentInfo>
        <hot:SessionId>0d1d5959-eb82-4364-a4db-7735c2b84981</hot:SessionId>
        <hot:NoOfRooms>2</hot:NoOfRooms>
        <hot:ResultIndex>9</hot:ResultIndex>
        <hot:HotelCode>1082959</hot:HotelCode>
        <hot:HotelName>Imperial Suites Hotel</hot:HotelName>
        <hot:HotelRooms>
            <hot:HotelRoom>
                <hot:RoomIndex>1</hot:RoomIndex>
                <hot:RoomTypeName>Luxury Suite</hot:RoomTypeName>
                <hot:RoomTypeCode></hot:RoomTypeCode>
                <hot:RatePlanCode>558651|</hot:RatePlanCode>
            </hot:HotelRoom>
            <hot:HotelRoom>
                <hot:RoomIndex>2</hot:RoomIndex>
                <hot:RoomTypeName>Luxury Suite</hot:RoomTypeName>
                <hot:RoomTypeCode></hot:RoomTypeCode>
                <hot:RatePlanCode>558651|</hot:RatePlanCode>
            </hot:HotelRoom>
        </hot:HotelRooms>
    </hot:HotelBookRequest>
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
$HotelRoomAvailabilityResponse = $Body->item(0)->getElementsByTagName("HotelRoomAvailabilityResponse");

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