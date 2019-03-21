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
echo "COMECOU DETAILS<br/>";
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
    <HotelBookingDetails xmlns="http://services.enginexml.rumbonet.riu.com">
        <in0 xmlns="http://services.enginexml.rumbonet.riu.com">
            <CountryCode xmlns="http://dtos.enginexml.rumbonet.riu.com">PE</CountryCode>
            <CustomerReservationCode xmlns="http://dtos.enginexml.rumbonet.riu.com" xsi:nil="true" />
            <hotelReservationCode xmlns="http://dtos.enginexml.rumbonet.riu.com">RNT2XPQM</hotelReservationCode>
            <hotelReservationID xmlns="http://dtos.enginexml.rumbonet.riu.com">1</hotelReservationID>
            <Language xmlns="http://dtos.enginexml.rumbonet.riu.com">E</Language>
        </in0>
    </HotelBookingDetails>
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
    'JSESSIONID' => 'AC6CEBC3B597371376EBCD9F3E33820A'
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
$HotelBookingDetailsResponse = $Body->item(0)->getElementsByTagName("HotelBookingDetailsResponse");
$HotelBookingDetailsRS = $HotelBookingDetailsResponse->item(0)->getElementsByTagName("HotelBookingDetailsRS");
if ($HotelBookingDetailsRS->length > 0) {
    $RPCAccountCode = $HotelBookingDetailsRS->item(0)->getElementsByTagName("RPCAccountCode");
    if ($RPCAccountCode->length > 0) {
        $RPCAccountCode = $RPCAccountCode->item(0)->nodeValue;
    } else {
        $RPCAccountCode = "";
    }
    $adultsCount = $HotelBookingDetailsRS->item(0)->getElementsByTagName("adultsCount");
    if ($adultsCount->length > 0) {
        $adultsCount = $adultsCount->item(0)->nodeValue;
    } else {
        $adultsCount = "";
    }
    $bookingAmount = $HotelBookingDetailsRS->item(0)->getElementsByTagName("bookingAmount");
    if ($bookingAmount->length > 0) {
        $bookingAmount = $bookingAmount->item(0)->nodeValue;
    } else {
        $bookingAmount = "";
    }
    $bookingState = $HotelBookingDetailsRS->item(0)->getElementsByTagName("bookingState");
    if ($bookingState->length > 0) {
        $bookingState = $bookingState->item(0)->nodeValue;
    } else {
        $bookingState = "";
    }
    $childCount = $HotelBookingDetailsRS->item(0)->getElementsByTagName("childCount");
    if ($childCount->length > 0) {
        $childCount = $childCount->item(0)->nodeValue;
    } else {
        $childCount = "";
    }
    $creationDate = $HotelBookingDetailsRS->item(0)->getElementsByTagName("creationDate");
    if ($creationDate->length > 0) {
        $creationDate = $creationDate->item(0)->nodeValue;
    } else {
        $creationDate = "";
    }
    $currencyCode = $HotelBookingDetailsRS->item(0)->getElementsByTagName("currencyCode");
    if ($currencyCode->length > 0) {
        $currencyCode = $currencyCode->item(0)->nodeValue;
    } else {
        $currencyCode = "";
    }
    $currentAccount = $HotelBookingDetailsRS->item(0)->getElementsByTagName("currentAccount");
    if ($currentAccount->length > 0) {
        $currentAccount = $currentAccount->item(0)->nodeValue;
    } else {
        $currentAccount = "";
    }
    $CustomerReservationCode = $HotelBookingDetailsRS->item(0)->getElementsByTagName("CustomerReservationCode");
    if ($CustomerReservationCode->length > 0) {
        $CustomerReservationCode = $CustomerReservationCode->item(0)->nodeValue;
    } else {
        $CustomerReservationCode = "";
    }
    $errors = $HotelBookingDetailsRS->item(0)->getElementsByTagName("errors");
    if ($errors->length > 0) {
        $errors = $errors->item(0)->nodeValue;
    } else {
        $errors = "";
    }
    $hotelID = $HotelBookingDetailsRS->item(0)->getElementsByTagName("hotelID");
    if ($hotelID->length > 0) {
        $hotelID = $hotelID->item(0)->nodeValue;
    } else {
        $hotelID = "";
    }
    $hotelReservationCode = $HotelBookingDetailsRS->item(0)->getElementsByTagName("hotelReservationCode");
    if ($hotelReservationCode->length > 0) {
        $hotelReservationCode = $hotelReservationCode->item(0)->nodeValue;
    } else {
        $hotelReservationCode = "";
    }
    $hotelReservationID = $HotelBookingDetailsRS->item(0)->getElementsByTagName("hotelReservationID");
    if ($hotelReservationID->length > 0) {
        $hotelReservationID = $hotelReservationID->item(0)->nodeValue;
    } else {
        $hotelReservationID = "";
    }
    $impPromocode = $HotelBookingDetailsRS->item(0)->getElementsByTagName("impPromocode");
    if ($impPromocode->length > 0) {
        $impPromocode = $impPromocode->item(0)->nodeValue;
    } else {
        $impPromocode = "";
    }
    $infantsCount = $HotelBookingDetailsRS->item(0)->getElementsByTagName("infantsCount");
    if ($infantsCount->length > 0) {
        $infantsCount = $infantsCount->item(0)->nodeValue;
    } else {
        $infantsCount = "";
    }
    $mealPlan = $HotelBookingDetailsRS->item(0)->getElementsByTagName("mealPlan");
    if ($mealPlan->length > 0) {
        $mealPlan = $mealPlan->item(0)->nodeValue;
    } else {
        $mealPlan = "";
    }
    $numDays = $HotelBookingDetailsRS->item(0)->getElementsByTagName("numDays");
    if ($numDays->length > 0) {
        $numDays = $numDays->item(0)->nodeValue;
    } else {
        $numDays = "";
    }
    $Observations = $HotelBookingDetailsRS->item(0)->getElementsByTagName("Observations");
    if ($Observations->length > 0) {
        $Observations = $Observations->item(0)->nodeValue;
    } else {
        $Observations = "";
    }
    $promocode = $HotelBookingDetailsRS->item(0)->getElementsByTagName("promocode");
    if ($promocode->length > 0) {
        $promocode = $promocode->item(0)->nodeValue;
    } else {
        $promocode = "";
    }
    $RateHotel = $HotelBookingDetailsRS->item(0)->getElementsByTagName("RateHotel");
    if ($RateHotel->length > 0) {
        $RateHotel = $RateHotel->item(0)->nodeValue;
    } else {
        $RateHotel = "";
    }
    $rateReference = $HotelBookingDetailsRS->item(0)->getElementsByTagName("rateReference");
    if ($rateReference->length > 0) {
        $rateReference = $rateReference->item(0)->nodeValue;
    } else {
        $rateReference = "";
    }
    $rateType = $HotelBookingDetailsRS->item(0)->getElementsByTagName("rateType");
    if ($rateType->length > 0) {
        $rateType = $rateType->item(0)->nodeValue;
    } else {
        $rateType = "";
    }
    $roomsCount = $HotelBookingDetailsRS->item(0)->getElementsByTagName("roomsCount");
    if ($roomsCount->length > 0) {
        $roomsCount = $roomsCount->item(0)->nodeValue;
    } else {
        $roomsCount = "";
    }
    $state = $HotelBookingDetailsRS->item(0)->getElementsByTagName("state");
    if ($state->length > 0) {
        $state = $state->item(0)->nodeValue;
    } else {
        $state = "";
    }
    $stayDateEnd = $HotelBookingDetailsRS->item(0)->getElementsByTagName("stayDateEnd");
    if ($stayDateEnd->length > 0) {
        $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
    } else {
        $stayDateEnd = "";
    }
    $stayDateStart = $HotelBookingDetailsRS->item(0)->getElementsByTagName("stayDateStart");
    if ($stayDateStart->length > 0) {
        $stayDateStart = $stayDateStart->item(0)->nodeValue;
    } else {
        $stayDateStart = "";
    }

    $bookingPenalties = $HotelBookingDetailsRS->item(0)->getElementsByTagName("bookingPenalties");
    if ($bookingPenalties->length > 0) {
        //cancelPenalties
        $cancelPenalties = $bookingPenalties->item(0)->getElementsByTagName("cancelPenalties");
        if ($cancelPenalties->length > 0) {
            $CPamount = $cancelPenalties->item(0)->getElementsByTagName("amount");
            if ($CPamount->length > 0) {
                $CPamount = $CPamount->item(0)->nodeValue;
            } else {
                $CPamount = "";
            }
            $CPdays = $cancelPenalties->item(0)->getElementsByTagName("days");
            if ($CPdays->length > 0) {
                $CPdays = $CPdays->item(0)->nodeValue;
            } else {
                $CPdays = "";
            }
            $CPpercent = $cancelPenalties->item(0)->getElementsByTagName("percent");
            if ($CPpercent->length > 0) {
                $CPpercent = $CPpercent->item(0)->nodeValue;
            } else {
                $CPpercent = "";
            }
            $CPreleaseDays = $cancelPenalties->item(0)->getElementsByTagName("releaseDays");
            if ($CPreleaseDays->length > 0) {
                $CPreleaseDays = $CPreleaseDays->item(0)->nodeValue;
            } else {
                $CPreleaseDays = "";
            }
            $CPtotalAmount = $cancelPenalties->item(0)->getElementsByTagName("totalAmount");
            if ($CPtotalAmount->length > 0) {
                $CPtotalAmount = $CPtotalAmount->item(0)->nodeValue;
            } else {
                $CPtotalAmount = "";
            }
        }

        //modificationPenalties
        $modificationPenalties = $bookingPenalties->item(0)->getElementsByTagName("modificationPenalties");
        if ($modificationPenalties->length > 0) {
            $MPamount = $modificationPenalties->item(0)->getElementsByTagName("amount");
            if ($MPamount->length > 0) {
                $MPamount = $MPamount->item(0)->nodeValue;
            } else {
                $MPamount = "";
            }
            $MPdays = $modificationPenalties->item(0)->getElementsByTagName("days");
            if ($MPdays->length > 0) {
                $MPdays = $MPdays->item(0)->nodeValue;
            } else {
                $MPdays = "";
            }
            $MPpercent = $modificationPenalties->item(0)->getElementsByTagName("percent");
            if ($MPpercent->length > 0) {
                $MPpercent = $MPpercent->item(0)->nodeValue;
            } else {
                $MPpercent = "";
            }
            $MPreleaseDays = $modificationPenalties->item(0)->getElementsByTagName("releaseDays");
            if ($MPreleaseDays->length > 0) {
                $MPreleaseDays = $MPreleaseDays->item(0)->nodeValue;
            } else {
                $MPreleaseDays = "";
            }
            $MPtotalAmount = $modificationPenalties->item(0)->getElementsByTagName("totalAmount");
            if ($MPtotalAmount->length > 0) {
                $MPtotalAmount = $MPtotalAmount->item(0)->nodeValue;
            } else {
                $MPtotalAmount = "";
            }
        }

        //noShowPenalties
        $noShowPenalties = $bookingPenalties->item(0)->getElementsByTagName("noShowPenalties");
        if ($noShowPenalties->length > 0) {
            $SPamount = $noShowPenalties->item(0)->getElementsByTagName("amount");
            if ($SPamount->length > 0) {
                $SPamount = $SPamount->item(0)->nodeValue;
            } else {
                $SPamount = "";
            }
            $SPdays = $noShowPenalties->item(0)->getElementsByTagName("days");
            if ($SPdays->length > 0) {
                $SPdays = $SPdays->item(0)->nodeValue;
            } else {
                $SPdays = "";
            }
            $SPpercent = $noShowPenalties->item(0)->getElementsByTagName("percent");
            if ($SPpercent->length > 0) {
                $SPpercent = $SPpercent->item(0)->nodeValue;
            } else {
                $SPpercent = "";
            }
            $SPreleaseDays = $noShowPenalties->item(0)->getElementsByTagName("releaseDays");
            if ($SPreleaseDays->length > 0) {
                $SPreleaseDays = $SPreleaseDays->item(0)->nodeValue;
            } else {
                $SPreleaseDays = "";
            }
            $SPtotalAmount = $noShowPenalties->item(0)->getElementsByTagName("totalAmount");
            if ($SPtotalAmount->length > 0) {
                $SPtotalAmount = $SPtotalAmount->item(0)->nodeValue;
            } else {
                $SPtotalAmount = "";
            }
        }
    }

    $ContactGuest = $HotelBookingDetailsRS->item(0)->getElementsByTagName("ContactGuest");
    if ($ContactGuest->length > 0) {
        $SexCode = $ContactGuest->item(0)->getElementsByTagName("SexCode");
        if ($SexCode->length > 0) {
            $SexCode = $SexCode->item(0)->nodeValue;
        } else {
            $SexCode = "";
        }
        $TreatmentCode = $ContactGuest->item(0)->getElementsByTagName("TreatmentCode");
        if ($TreatmentCode->length > 0) {
            $TreatmentCode = $TreatmentCode->item(0)->nodeValue;
        } else {
            $TreatmentCode = "";
        }
        $UserCountryPrefix = $ContactGuest->item(0)->getElementsByTagName("UserCountryPrefix");
        if ($UserCountryPrefix->length > 0) {
            $UserCountryPrefix = $UserCountryPrefix->item(0)->nodeValue;
        } else {
            $UserCountryPrefix = "";
        }
        $UserEmail = $ContactGuest->item(0)->getElementsByTagName("UserEmail");
        if ($UserEmail->length > 0) {
            $UserEmail = $UserEmail->item(0)->nodeValue;
        } else {
            $UserEmail = "";
        }
        $UserName = $ContactGuest->item(0)->getElementsByTagName("UserName");
        if ($UserName->length > 0) {
            $UserName = $UserName->item(0)->nodeValue;
        } else {
            $UserName = "";
        }
        $UserPhoneNumber = $ContactGuest->item(0)->getElementsByTagName("UserPhoneNumber");
        if ($UserPhoneNumber->length > 0) {
            $UserPhoneNumber = $UserPhoneNumber->item(0)->nodeValue;
        } else {
            $UserPhoneNumber = "";
        }
        $UserProvincePrefix = $ContactGuest->item(0)->getElementsByTagName("UserProvincePrefix");
        if ($UserProvincePrefix->length > 0) {
            $UserProvincePrefix = $UserProvincePrefix->item(0)->nodeValue;
        } else {
            $UserProvincePrefix = "";
        }
        $UserSurname = $ContactGuest->item(0)->getElementsByTagName("UserSurname");
        if ($UserSurname->length > 0) {
            $UserSurname = $UserSurname->item(0)->nodeValue;
        } else {
            $UserSurname = "";
        }
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('hoteldetails');
        $insert->values(array(
            'CustomerReservationCode' => $CustomerReservationCode,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'hotelID' => $hotelID,
            'RPCAccountCode' => $RPCAccountCode,
            'bookingAmount' => $bookingAmount,
            'bookingState' => $bookingState,
            'creationDate' => $creationDate,
            'currencyCode' => $currencyCode,
            'currentAccount' => $currentAccount,
            'errors' => $errors,
            'hotelReservationCode' => $hotelReservationCode,
            'hotelReservationID' => $hotelReservationID,
            'impPromocode' => $impPromocode,
            'mealPlan' => $mealPlan,
            'numDays' => $numDays,
            'Observations' => $Observations,
            'promocode' => $promocode,
            'RateHotel' => $RateHotel,
            'rateReference' => $rateReference,
            'rateType' => $rateType,
            'AdultsCount' => $AdultsCount,
            'ChildCount' => $ChildCount,
            'InfantsCount' => $InfantsCount,
            'roomsCount' => $roomsCount,
            'state' => $state,
            'stayDateEnd' => $stayDateEnd,
            'stayDateStart' => $stayDateStart,
            'SexCode' => $SexCode,
            'TreatmentCode' => $TreatmentCode,
            'UserCountryPrefix' => $UserCountryPrefix,
            'UserEmail' => $UserEmail,
            'UserName' => $UserName,
            'UserPhoneNumber' => $UserPhoneNumber,
            'UserProvincePrefix' => $UserProvincePrefix,
            'UserSurname' => $UserSurname,
            'CPamount' => $CPamount,
            'CPdays' => $CPdays,
            'CPpercent' => $CPpercent,
            'CPreleaseDays' => $CPreleaseDays,
            'CPtotalAmount' => $CPtotalAmount,
            'MPamount' => $MPamount,
            'MPdays' => $MPdays,
            'MPpercent' => $MPpercent,
            'MPreleaseDays' => $MPreleaseDays,
            'MPtotalAmount' => $MPtotalAmount,
            'SPamount' => $SPamount,
            'SPdays' => $SPdays,
            'SPpercent' => $SPpercent,
            'SPreleaseDays' => $SPreleaseDays,
            'SPtotalAmount' => $SPtotalAmount
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

    $BookingRoomsList = $HotelBookingDetailsRS->item(0)->getElementsByTagName("BookingRoomsList");
    if ($BookingRoomsList->length > 0) {
        $BookingRoomsRes = $BookingRoomsList->item(0)->getElementsByTagName("BookingRoomsRes");
        if ($BookingRoomsRes->length > 0) {
            $adultsCount = $BookingRoomsRes->item(0)->getElementsByTagName("adultsCount");
            if ($adultsCount->length > 0) {
                $adultsCount = $adultsCount->item(0)->nodeValue;
            } else {
                $adultsCount = "";
            }
            $childsCount = $BookingRoomsRes->item(0)->getElementsByTagName("childsCount");
            if ($childsCount->length > 0) {
                $childsCount = $childsCount->item(0)->nodeValue;
            } else {
                $childsCount = "";
            }
            $infantsCount = $BookingRoomsRes->item(0)->getElementsByTagName("infantsCount");
            if ($infantsCount->length > 0) {
                $infantsCount = $infantsCount->item(0)->nodeValue;
            } else {
                $infantsCount = "";
            }
            $mealPlanPeriodsList = $BookingRoomsRes->item(0)->getElementsByTagName("mealPlanPeriodsList");
            if ($mealPlanPeriodsList->length > 0) {
                $mealPlanPeriodsList = $mealPlanPeriodsList->item(0)->nodeValue;
            } else {
                $mealPlanPeriodsList = "";
            }
            $pricesPeriodsList = $BookingRoomsRes->item(0)->getElementsByTagName("pricesPeriodsList");
            if ($pricesPeriodsList->length > 0) {
                $pricesPeriodsList = $pricesPeriodsList->item(0)->nodeValue;
            } else {
                $pricesPeriodsList = "";
            }
            $roomAmount = $BookingRoomsRes->item(0)->getElementsByTagName("roomAmount");
            if ($roomAmount->length > 0) {
                $roomAmount = $roomAmount->item(0)->nodeValue;
            } else {
                $roomAmount = "";
            }
            $roomNumber = $BookingRoomsRes->item(0)->getElementsByTagName("roomNumber");
            if ($roomNumber->length > 0) {
                $roomNumber = $roomNumber->item(0)->nodeValue;
            } else {
                $roomNumber = "";
            }
            $roomTypeCode = $BookingRoomsRes->item(0)->getElementsByTagName("roomTypeCode");
            if ($roomTypeCode->length > 0) {
                $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
            } else {
                $roomTypeCode = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hoteldetails_BookingRoomsList');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'adultsCount' => $adultsCount,
                    'childsCount' => $childsCount,
                    'infantsCount' => $infantsCount,
                    'mealPlanPeriodsList' => $mealPlanPeriodsList,
                    'pricesPeriodsList' => $pricesPeriodsList,
                    'roomAmount' => $roomAmount,
                    'roomNumber' => $roomNumber,
                    'roomTypeCode' => $roomTypeCode,
                    'CustomerReservationCode' => $CustomerReservationCode
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO HOTELGUEST: " . $e;
                echo $return;
            }

            $ages = $BookingRoomsRes->item(0)->getElementsByTagName("ages");
            if ($ages->length > 0) {
                $int = $ages->item(0)->getElementsByTagName("int");
                if ($int->length > 0) {
                    $int2 = "";
                   for ($i=0; $i < $int->length; $i++) { 
                        $int2 = $int->item($i)->nodeValue;

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hoteldetails_ages');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'age' => $int2,
                                'CustomerReservationCode' => $CustomerReservationCode
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO AGES: " . $e;
                            echo $return;
                        }
                   }
                }
            }

            $bookingRoomGuestList = $BookingRoomsRes->item(0)->getElementsByTagName("bookingRoomGuestList");
            if ($bookingRoomGuestList->length > 0) {
                $BookingRoomGuests = $bookingRoomGuestList->item(0)->getElementsByTagName("BookingRoomGuests");
                if ($BookingRoomGuests->length > 0) {
                    for ($j=0; $j < $BookingRoomGuests->length; $j++) { 
                        $guestAge = $BookingRoomGuests->item(0)->getElementsByTagName("guestAge");
                        if ($guestAge->length > 0) {
                            $guestAge = $guestAge->item(0)->nodeValue;
                        } else {
                            $guestAge = "";
                        }
                        $guestName = $BookingRoomGuests->item(0)->getElementsByTagName("guestName");
                        if ($guestName->length > 0) {
                            $guestName = $guestName->item(0)->nodeValue;
                        } else {
                            $guestName = "";
                        }
                        $guestNumber = $BookingRoomGuests->item(0)->getElementsByTagName("guestNumber");
                        if ($guestNumber->length > 0) {
                            $guestNumber = $guestNumber->item(0)->nodeValue;
                        } else {
                            $guestNumber = "";
                        }
                        $guestSurname = $BookingRoomGuests->item(0)->getElementsByTagName("guestSurname");
                        if ($guestSurname->length > 0) {
                            $guestSurname = $guestSurname->item(0)->nodeValue;
                        } else {
                            $guestSurname = "";
                        }
                        $personTypeCode = $BookingRoomGuests->item(0)->getElementsByTagName("personTypeCode");
                        if ($personTypeCode->length > 0) {
                            $personTypeCode = $personTypeCode->item(0)->nodeValue;
                        } else {
                            $personTypeCode = "";
                        }
                        $riuClassAccount = $BookingRoomGuests->item(0)->getElementsByTagName("riuClassAccount");
                        if ($riuClassAccount->length > 0) {
                            $riuClassAccount = $riuClassAccount->item(0)->nodeValue;
                        } else {
                            $riuClassAccount = "";
                        }
                        $sexCode = $BookingRoomGuests->item(0)->getElementsByTagName("sexCode");
                        if ($sexCode->length > 0) {
                            $sexCode = $sexCode->item(0)->nodeValue;
                        } else {
                            $sexCode = "";
                        }
                        $treatmentCode = $BookingRoomGuests->item(0)->getElementsByTagName("treatmentCode");
                        if ($treatmentCode->length > 0) {
                            $treatmentCode = $treatmentCode->item(0)->nodeValue;
                        } else {
                            $treatmentCode = "";
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hoteldetails_bookingRoomGuestList');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'guestAge' => $guestAge,
                                'guestName' => $guestName,
                                'guestNumber' => $guestNumber,
                                'guestSurname' => $guestSurname,
                                'personTypeCode' => $personTypeCode,
                                'riuClassAccount' => $riuClassAccount,
                                'sexCode' => $sexCode,
                                'treatmentCode' => $treatmentCode,
                                'CustomerReservationCode' => $CustomerReservationCode
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO HOTELGUEST: " . $e;
                            echo $return;
                        }
                    }
                }
            }
        }
    }


    $amountList = $HotelBookingDetailsRS->item(0)->getElementsByTagName("amountList");
    if ($amountList->length > 0) {
        $Amounts = $amountList->item(0)->getElementsByTagName("Amounts");
        if ($Amounts->length > 0) {
            for ($j=0; $j < $Amounts->length; $j++) { 
                $concept = $Amounts->item($j)->getElementsByTagName("concept");
                if ($concept->length > 0) {
                    $concept = $concept->item(0)->nodeValue;
                } else {
                    $concept = "";
                }
                $netAmount = $Amounts->item($j)->getElementsByTagName("netAmount");
                if ($netAmount->length > 0) {
                    $netAmount = $netAmount->item(0)->nodeValue;
                } else {
                    $netAmount = "";
                }
                $quote = $Amounts->item($j)->getElementsByTagName("quote");
                if ($quote->length > 0) {
                    $quote = $quote->item(0)->nodeValue;
                } else {
                    $quote = "";
                }
                $taxesList = $Amounts->item($j)->getElementsByTagName("taxesList");
                if ($taxesList->length > 0) {
                    $taxesList = $taxesList->item(0)->nodeValue;
                } else {
                    $taxesList = "";
                }
                $totalAmount = $Amounts->item($j)->getElementsByTagName("totalAmount");
                if ($totalAmount->length > 0) {
                    $totalAmount = $totalAmount->item(0)->nodeValue;
                } else {
                    $totalAmount = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hoteldetails_amountsList');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'concept' => $concept,
                        'netAmount' => $netAmount,
                        'quote' => $quote,
                        'taxesList' => $taxesList,
                        'totalAmount' => $totalAmount,
                        'CustomerReservationCode' => $CustomerReservationCode
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO AMOUNT: " . $e;
                    echo $return;
                }
            }
        }
    }

    $translationTHabs = $HotelBookingDetailsRS->item(0)->getElementsByTagName("translationTHabs");
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
                    $insert->into('hoteldetails_listTHabs');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'codTha' => $codTha,
                        'hotel' => $hotel,
                        'CustomerReservationCode' => $CustomerReservationCode
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
                                $insert->into('hoteldetails_listTranslation');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'description' => $description,
                                    'language' => $language,
                                    'CustomerReservationCode' => $CustomerReservationCode
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