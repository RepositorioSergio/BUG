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
echo "COMECOU RES<br/>";
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
$sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_riu = $affiliate_id;
} else {
    $affiliate_id_riu = 0;
}
$sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $riuServiceURL = $row['value'];
}
echo $return;
echo $riuServiceURL;
echo $return;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <soap:Body>
        <HotelRes xmlns="http://services.enginexml.rumbonet.riu.com">
            <in0 xmlns="http://services.enginexml.rumbonet.riu.com">
                <BookingDetails xmlns="http://dtos.enginexml.rumbonet.riu.com">
                    <RPCAccountCode xsi:nil="true" />
                    <adultsCount>2</adultsCount>
                    <bookingAmount>759.18</bookingAmount>
                    <BookingRoomsList>
                        <BookingRooms>
                            <adultsCount>2</adultsCount>
                            <ages />
                            <bookingRoomGuestList>
                                <BookingRoomGuests>
                                    <guestAge>30</guestAge>
                                    <guestName>Guest 0</guestName>
                                    <guestNumber>0</guestNumber>
                                    <guestSurname>Guest 0</guestSurname>
                                    <personTypeCode>AD</personTypeCode>
                                    <riuClassAccount/>
                                    <sexCode>H</sexCode>
                                    <treatmentCode>SR</treatmentCode>
                                </BookingRoomGuests>
                                <BookingRoomGuests>
                                    <guestAge>30</guestAge>
                                    <guestName>Guest 1</guestName>
                                    <guestNumber>1</guestNumber>
                                    <guestSurname>Guest 1</guestSurname>
                                    <personTypeCode>AD</personTypeCode>
                                    <riuClassAccount/>
                                    <sexCode>H</sexCode>
                                    <treatmentCode>SR</treatmentCode>
                                </BookingRoomGuests>
                            </bookingRoomGuestList>
                            <childsCount>0</childsCount>
                            <infantsCount>0</infantsCount>
                            <roomNumber>1</roomNumber>
                            <roomTypeCode>DDJB</roomTypeCode>
                        </BookingRooms>
                    </BookingRoomsList>
                    <childCount>0</childCount>
                    <ContactGuest>
                        <SexCode>H</SexCode>
                        <TreatmentCode>SR</TreatmentCode>
                        <UserCountryPrefix>0</UserCountryPrefix>
                        <UserEmail>guedesv@gmail.com</UserEmail>
                        <UserName>Antonio</UserName>
                        <UserPhoneNumber>0</UserPhoneNumber>
                        <UserProvincePrefix>0</UserProvincePrefix>
                        <UserSurname>Guedes</UserSurname>
                    </ContactGuest>
                    <currencyCode>EUR</currencyCode>
                    <CustomerReservationCode>r928</CustomerReservationCode>
                    <hotelID>216</hotelID>
                    <impPromocode>0.0</impPromocode>
                    <infantsCount>0</infantsCount>
                    <mealPlan>AI</mealPlan>
                    <numDays>3</numDays>
                    <Observations>prueba XML Guedes</Observations>
                    <promocode xsi:nil="true"/>
                    <RateHotel>216</RateHotel>
                    <rateReference xsi:nil="true"/>
                    <roomsCount>1</roomsCount>
                    <stayDateEnd>20191209</stayDateEnd>
                    <stayDateStart>20191206</stayDateStart>
                </BookingDetails>
                <CountryCode xmlns="http://dtos.enginexml.rumbonet.riu.com">PE</CountryCode>
                <Language xmlns="http://dtos.enginexml.rumbonet.riu.com">PT</Language>
                <offLinePrice xmlns="http://dtos.enginexml.rumbonet.riu.com">0.0</offLinePrice>
                <quoteType xmlns="http://dtos.enginexml.rumbonet.riu.com">AGE</quoteType>
            </in0>
        </HotelRes>
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
    "Content-length: ".strlen($raw)
));


$client->setUri($riuServiceURL);
$client->setMethod('POST');
$client->setCookies(array(
    'JSESSIONID' => '59083F1E35C6619A43056D600EECEBB1'
));
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
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
$HotelResResponse = $Body->item(0)->getElementsByTagName("HotelResResponse");
$out = $HotelResResponse->item(0)->getElementsByTagName("out");
if ($out->length > 0) {
    $adultsCount = $out->item(0)->getElementsByTagName("adultsCount");
    if ($adultsCount->length > 0) {
        $adultsCount = $adultsCount->item(0)->nodeValue;
    } else {
        $adultsCount = "";
    }
    $bookingAmount = $out->item(0)->getElementsByTagName("bookingAmount");
    if ($bookingAmount->length > 0) {
        $bookingAmount = $bookingAmount->item(0)->nodeValue;
    } else {
        $bookingAmount = "";
    }
    $bookingState = $out->item(0)->getElementsByTagName("bookingState");
    if ($bookingState->length > 0) {
        $bookingState = $bookingState->item(0)->nodeValue;
    } else {
        $bookingState = "";
    }
    $childCount = $out->item(0)->getElementsByTagName("childCount");
    if ($childCount->length > 0) {
        $childCount = $childCount->item(0)->nodeValue;
    } else {
        $childCount = "";
    }
    $creationDate = $out->item(0)->getElementsByTagName("creationDate");
    if ($creationDate->length > 0) {
        $creationDate = $creationDate->item(0)->nodeValue;
    } else {
        $creationDate = "";
    }
    $currencyCode = $out->item(0)->getElementsByTagName("currencyCode");
    if ($currencyCode->length > 0) {
        $currencyCode = $currencyCode->item(0)->nodeValue;
    } else {
        $currencyCode = "";
    }
    $customerReservationCode = $out->item(0)->getElementsByTagName("customerReservationCode");
    if ($customerReservationCode->length > 0) {
        $customerReservationCode = $customerReservationCode->item(0)->nodeValue;
    } else {
        $customerReservationCode = "";
    }
    $errors = $out->item(0)->getElementsByTagName("errors");
    if ($errors->length > 0) {
        $errors = $errors->item(0)->nodeValue;
    } else {
        $errors = "";
    }
    $hotelID = $out->item(0)->getElementsByTagName("hotelID");
    if ($hotelID->length > 0) {
        $hotelID = $hotelID->item(0)->nodeValue;
    } else {
        $hotelID = "";
    }
    $hotelReservationCode = $out->item(0)->getElementsByTagName("hotelReservationCode");
    if ($hotelReservationCode->length > 0) {
        $hotelReservationCode = $hotelReservationCode->item(0)->nodeValue;
    } else {
        $hotelReservationCode = "";
    }
    $hotelReservationID = $out->item(0)->getElementsByTagName("hotelReservationID");
    if ($hotelReservationID->length > 0) {
        $hotelReservationID = $hotelReservationID->item(0)->nodeValue;
    } else {
        $hotelReservationID = "";
    }
    $hotelReservationID = $out->item(0)->getElementsByTagName("hotelReservationID");
    if ($hotelReservationID->length > 0) {
        $hotelReservationID = $hotelReservationID->item(0)->nodeValue;
    } else {
        $hotelReservationID = "";
    }
    $impPromocode = $out->item(0)->getElementsByTagName("impPromocode");
    if ($impPromocode->length > 0) {
        $impPromocode = $impPromocode->item(0)->nodeValue;
    } else {
        $impPromocode = "";
    }
    $infantsCount = $out->item(0)->getElementsByTagName("infantsCount");
    if ($infantsCount->length > 0) {
        $infantsCount = $infantsCount->item(0)->nodeValue;
    } else {
        $infantsCount = "";
    }
    $numDays = $out->item(0)->getElementsByTagName("numDays");
    if ($numDays->length > 0) {
        $numDays = $numDays->item(0)->nodeValue;
    } else {
        $numDays = "";
    }
    $promocode = $out->item(0)->getElementsByTagName("promocode");
    if ($promocode->length > 0) {
        $promocode = $promocode->item(0)->nodeValue;
    } else {
        $promocode = "";
    }
    $rateReference = $out->item(0)->getElementsByTagName("rateReference");
    if ($rateReference->length > 0) {
        $rateReference = $rateReference->item(0)->nodeValue;
    } else {
        $rateReference = "";
    }
    $rateType = $out->item(0)->getElementsByTagName("rateType");
    if ($rateType->length > 0) {
        $rateType = $rateType->item(0)->nodeValue;
    } else {
        $rateType = "";
    }
    $roomsCount = $out->item(0)->getElementsByTagName("roomsCount");
    if ($roomsCount->length > 0) {
        $roomsCount = $roomsCount->item(0)->nodeValue;
    } else {
        $roomsCount = "";
    }
    $state = $out->item(0)->getElementsByTagName("state");
    if ($state->length > 0) {
        $state = $state->item(0)->nodeValue;
    } else {
        $state = "";
    }
    $stayDateEnd = $out->item(0)->getElementsByTagName("stayDateEnd");
    if ($stayDateEnd->length > 0) {
        $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
    } else {
        $stayDateEnd = "";
    }
    $stayDateStart = $out->item(0)->getElementsByTagName("stayDateStart");
    if ($stayDateStart->length > 0) {
        $stayDateStart = $stayDateStart->item(0)->nodeValue;
    } else {
        $stayDateStart = "";
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('hotelres');
        $insert->values(array(
            'hotelReservationID' => $hotelReservationID,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'hotelReservationCode' => $hotelReservationCode,
            'hotelID' => $hotelID,
            'bookingAmount' => $bookingAmount,
            'bookingState' => $bookingState,
            'adultsCount' => $adultsCount,
            'childCount' => $childCount,
            'InfantsCount' => $InfantsCount,
            'creationDate' => $creationDate,
            'currencyCode' => $currencyCode,
            'customerReservationCode' => $customerReservationCode,
            'errors' => $errors,
            'impPromocode' => $impPromocode,
            'mealPlan' => $mealPlan,
            'numDays' => $numDays,
            'promocode' => $promocode,
            'rateReference' => $rateReference,
            'rateType' => $rateType,
            'roomsCount' => $roomsCount,
            'state' => $state,
            'stayDateEnd' => $stayDateEnd,
            'stayDateStart' => $stayDateStart
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO HOTEL: " . $e;
        echo $return;
    }

    $translationTHabs = $out->item(0)->getElementsByTagName("translationTHabs");
    if ($translationTHabs->length > 0) {
        $listTHabs = $translationTHabs->item(0)->getElementsByTagName("listTHabs");
        if ($listTHabs->length > 0) {
            $THabsDto = $listTHabs->item(0)->getElementsByTagName("THabsDto");
            if ($THabsDto->length > 0) {
                $codTha = $THabsDto->item(0)->getElementsByTagName("codTha");
                if ($codTha->length > 0) {
                    $codTha = $codTha->item(0)->nodeValue;
                } else {
                    $codTha = "";
                }
                $hotel = $THabsDto->item(0)->getElementsByTagName("hotel");
                if ($hotel->length > 0) {
                    $hotel = $hotel->item(0)->nodeValue;
                } else {
                    $hotel = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hotelres_listTHabs');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'codTha' => $codTha,
                        'hotel' => $hotel,
                        'hotelReservationID' => $hotelReservationID
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO HOTELL: " . $e;
                    echo $return;
                }

                $listTranslation = $THabsDto->item(0)->getElementsByTagName("listTranslation");
                if ($listTranslation->length > 0) {
                    $TranslationDto = $listTranslation->item(0)->getElementsByTagName("TranslationDto");
                    if ($TranslationDto->length > 0) {
                        for ($l=0; $l < $TranslationDto->length; $l++) { 
                            $description = $TranslationDto->item($l)->getElementsByTagName("description");
                            if ($description->length > 0) {
                                $description = $description->item(0)->nodeValue;
                            } else {
                                $description = "";
                            }
                            $language = $TranslationDto->item($l)->getElementsByTagName("language");
                            if ($language->length > 0) {
                                $language = $language->item(0)->nodeValue;
                            } else {
                                $language = "";
                            }

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('hotelres_listTranslation');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'description' => $description,
                                    'language' => $language,
                                    'hotelReservationID' => $hotelReservationID
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO HOTELT: " . $e;
                                echo $return;
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
echo '<br/>Done';
?>