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
echo "COMECOU RULE<br/>";
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
echo "<br/>PASSOU 1";

$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );

$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<soap:Body>
    <HotelBookingRule xmlns="http://services.enginexml.rumbonet.riu.com">
        <in0 xmlns="http://services.enginexml.rumbonet.riu.com">
            <AdultsCount xmlns="http://dtos.enginexml.rumbonet.riu.com">2</AdultsCount>
            <ChildCount xmlns="http://dtos.enginexml.rumbonet.riu.com">0</ChildCount>
            <CountryCode xmlns="http://dtos.enginexml.rumbonet.riu.com">PE</CountryCode>
            <HotelID xmlns="http://dtos.enginexml.rumbonet.riu.com">216</HotelID>
            <HotelReservationCode xmlns="http://dtos.enginexml.rumbonet.riu.com" xsi:nil="true" />
            <HotelReservationID xmlns="http://dtos.enginexml.rumbonet.riu.com">0</HotelReservationID>
            <InfantsCount xmlns="http://dtos.enginexml.rumbonet.riu.com">0</InfantsCount>
            <Language xmlns="http://dtos.enginexml.rumbonet.riu.com">PT</Language>
            <MealPlan xmlns="http://dtos.enginexml.rumbonet.riu.com">AI</MealPlan>
            <promocode xmlns="http://dtos.enginexml.rumbonet.riu.com"/>
            <quoteType xmlns="http://dtos.enginexml.rumbonet.riu.com">AGE</quoteType>
            <RateHotel xmlns="http://dtos.enginexml.rumbonet.riu.com">216</RateHotel>
            <rateReference xmlns="http://dtos.enginexml.rumbonet.riu.com" xsi:nil="true" />
            <RoomList xmlns="http://dtos.enginexml.rumbonet.riu.com">
                <RoomConfig>
                    <RoomStayCandidateRule>
                        <AdultsCount>2</AdultsCount>
                        <Ages xsi:nil="true" />
                        <ChildCount>0</ChildCount>
                        <InfantsCount>0</InfantsCount>
                        <RoomTypeCode>DDJB</RoomTypeCode>
                    </RoomStayCandidateRule>
                </RoomConfig>
            </RoomList>
            <RoomsCount xmlns="http://dtos.enginexml.rumbonet.riu.com">1</RoomsCount>
            <StayDateEnd xmlns="http://dtos.enginexml.rumbonet.riu.com">20191209</StayDateEnd>
            <StayDateStart xmlns="http://dtos.enginexml.rumbonet.riu.com">20191206</StayDateStart>
        </in0>
    </HotelBookingRule>
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
    'JSESSIONID' => '28B5AE4BD091D21F9CC6E58BD86AAF88'
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
$HotelBookingRuleResponse = $Body->item(0)->getElementsByTagName("HotelBookingRuleResponse");
$HotelBookingRuleResponse2 = $HotelBookingRuleResponse->item(0)->getElementsByTagName("HotelBookingRuleResponse");
$bookingRule = $HotelBookingRuleResponse2->item(0)->getElementsByTagName("bookingRule");
if ($bookingRule->length > 0) {
    $adultsCount = $bookingRule->item(0)->getElementsByTagName("adultsCount");
    if ($adultsCount->length > 0) {
        $adultsCount = $adultsCount->item(0)->nodeValue;
    } else {
        $adultsCount = "";
    }
    $bookingAmount = $bookingRule->item(0)->getElementsByTagName("bookingAmount");
    if ($bookingAmount->length > 0) {
        $bookingAmount = $bookingAmount->item(0)->nodeValue;
    } else {
        $bookingAmount = "";
    }
    $childCount = $bookingRule->item(0)->getElementsByTagName("childCount");
    if ($childCount->length > 0) {
        $childCount = $childCount->item(0)->nodeValue;
    } else {
        $childCount = "";
    }
    $currencyCode = $bookingRule->item(0)->getElementsByTagName("currencyCode");
    if ($currencyCode->length > 0) {
        $currencyCode = $currencyCode->item(0)->nodeValue;
    } else {
        $currencyCode = "";
    }
    $hotelID = $bookingRule->item(0)->getElementsByTagName("hotelID");
    if ($hotelID->length > 0) {
        $hotelID = $hotelID->item(0)->nodeValue;
    } else {
        $hotelID = "";
    }
    $impPromocode = $bookingRule->item(0)->getElementsByTagName("impPromocode");
    if ($impPromocode->length > 0) {
        $impPromocode = $impPromocode->item(0)->nodeValue;
    } else {
        $impPromocode = "";
    }
    $infantsCount = $bookingRule->item(0)->getElementsByTagName("infantsCount");
    if ($infantsCount->length > 0) {
        $infantsCount = $infantsCount->item(0)->nodeValue;
    } else {
        $infantsCount = "";
    }
    $mealPlan = $bookingRule->item(0)->getElementsByTagName("mealPlan");
    if ($mealPlan->length > 0) {
        $mealPlan = $mealPlan->item(0)->nodeValue;
    } else {
        $mealPlan = "";
    }
    $numDays = $bookingRule->item(0)->getElementsByTagName("numDays");
    if ($numDays->length > 0) {
        $numDays = $numDays->item(0)->nodeValue;
    } else {
        $numDays = "";
    }
    $promocode = $bookingRule->item(0)->getElementsByTagName("promocode");
    if ($promocode->length > 0) {
        $promocode = $promocode->item(0)->nodeValue;
    } else {
        $promocode = "";
    }
    $quoteType = $bookingRule->item(0)->getElementsByTagName("quoteType");
    if ($quoteType->length > 0) {
        $quoteType = $quoteType->item(0)->nodeValue;
    } else {
        $quoteType = "";
    }
    $rateHotel = $bookingRule->item(0)->getElementsByTagName("rateHotel");
    if ($rateHotel->length > 0) {
        $rateHotel = $rateHotel->item(0)->nodeValue;
    } else {
        $rateHotel = "";
    }
    $rateReference = $bookingRule->item(0)->getElementsByTagName("rateReference");
    if ($rateReference->length > 0) {
        $rateReference = $rateReference->item(0)->nodeValue;
    } else {
        $rateReference = "";
    }
    $rateType = $bookingRule->item(0)->getElementsByTagName("rateType");
    if ($rateType->length > 0) {
        $rateType = $rateType->item(0)->nodeValue;
    } else {
        $rateType = "";
    }
    $roomsCount = $bookingRule->item(0)->getElementsByTagName("roomsCount");
    if ($roomsCount->length > 0) {
        $roomsCount = $roomsCount->item(0)->nodeValue;
    } else {
        $roomsCount = "";
    }
    $stayDateEnd = $bookingRule->item(0)->getElementsByTagName("stayDateEnd");
    if ($stayDateEnd->length > 0) {
        $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
    } else {
        $stayDateEnd = "";
    }
    $stayDateStart = $bookingRule->item(0)->getElementsByTagName("stayDateStart");
    if ($stayDateStart->length > 0) {
        $stayDateStart = $stayDateStart->item(0)->nodeValue;
    } else {
        $stayDateStart = "";
    }
    $typePrice = $bookingRule->item(0)->getElementsByTagName("typePrice");
    if ($typePrice->length > 0) {
        $typePrice = $typePrice->item(0)->nodeValue;
    } else {
        $typePrice = "";
    }
    $bookingRulePenalties = $bookingRule->item(0)->getElementsByTagName("bookingRulePenalties");
    if ($bookingRulePenalties->length > 0) {
        //cancelPenalties
        $cancelPenalties = $bookingRulePenalties->item(0)->getElementsByTagName("cancelPenalties");
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
        $modificationPenalties = $bookingRulePenalties->item(0)->getElementsByTagName("modificationPenalties");
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
        $noShowPenalties = $bookingRulePenalties->item(0)->getElementsByTagName("noShowPenalties");
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

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('hotelrule');
        $insert->values(array(
            'hotelID' => $hotelID,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'bookingAmount' => $bookingAmount,
            'currencyCode' => $currencyCode,
            'typePrice' => $typePrice,
            'adultsCount' => $adultsCount,
            'childCount' => $childCount,
            'infantsCount' => $infantsCount,
            'impPromocode' => $impPromocode,
            'mealPlan' => $mealPlan,
            'numDays' => $numDays,
            'promocode' => $promocode,
            'quoteType' => $quoteType,
            'rateHotel' => $rateHotel,
            'rateReference' => $rateReference,
            'rateType' => $rateType,
            'roomsCount' => $roomsCount,
            'stayDateEnd' => $stayDateEnd,
            'stayDateStart' => $stayDateStart,
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
        echo "ERRO RULE: " . $e;
        echo $return;
    }

    $roomStayList = $bookingRule->item(0)->getElementsByTagName("roomStayList");
    if ($roomStayList->length > 0) {
        $RoomStay = $roomStayList->item(0)->getElementsByTagName("RoomStay");
        if ($RoomStay->length > 0) {
            $mealPlanSupplementPeriodsList = $RoomStay->item(0)->getElementsByTagName("mealPlanSupplementPeriodsList");
            if ($mealPlanSupplementPeriodsList->length > 0) {
                $mealPlanSupplementPeriodsList = $mealPlanSupplementPeriodsList->item(0)->nodeValue;
            } else {
                $mealPlanSupplementPeriodsList = "";
            }
            $pricesPeriodsList = $RoomStay->item(0)->getElementsByTagName("pricesPeriodsList");
            if ($pricesPeriodsList->length > 0) {
                $pricesPeriodsList = $pricesPeriodsList->item(0)->nodeValue;
            } else {
                $pricesPeriodsList = "";
            }
            $roomAmount = $RoomStay->item(0)->getElementsByTagName("roomAmount");
            if ($roomAmount->length > 0) {
                $roomAmount = $roomAmount->item(0)->nodeValue;
            } else {
                $roomAmount = "";
            }
            $roomNumber = $RoomStay->item(0)->getElementsByTagName("roomNumber");
            if ($roomNumber->length > 0) {
                $roomNumber = $roomNumber->item(0)->nodeValue;
            } else {
                $roomNumber = "";
            }
            $roomTypeCode = $RoomStay->item(0)->getElementsByTagName("roomTypeCode");
            if ($roomTypeCode->length > 0) {
                $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
            } else {
                $roomTypeCode = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotelrule_roomStayList');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'mealPlanSupplementPeriodsList' => $mealPlanSupplementPeriodsList,
                    'pricesPeriodsList' => $pricesPeriodsList,
                    'roomAmount' => $roomAmount,
                    'roomNumber' => $roomNumber,
                    'roomTypeCode' => $roomTypeCode,
                    'hotelID' => $hotelID
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO STAY: " . $e;
                echo $return;
            }

            $roomGuestsList = $RoomStay->item(0)->getElementsByTagName("roomGuestsList");
            if ($roomGuestsList->length > 0) {
                $RoomGuests = $roomGuestsList->item(0)->getElementsByTagName("RoomGuests");
                if ($RoomGuests->length > 0) {
                    for ($k=0; $k < $RoomGuests->length; $k++) { 
                        $age = $RoomGuests->item($k)->getElementsByTagName("age");
                        if ($age->length > 0) {
                            $age = $age->item(0)->nodeValue;
                        } else {
                            $age = "";
                        }
                        $guestNumber = $RoomGuests->item($k)->getElementsByTagName("guestNumber");
                        if ($guestNumber->length > 0) {
                            $guestNumber = $guestNumber->item(0)->nodeValue;
                        } else {
                            $guestNumber = "";
                        }
                        $typeGuestCode = $RoomGuests->item($k)->getElementsByTagName("typeGuestCode");
                        if ($typeGuestCode->length > 0) {
                            $typeGuestCode = $typeGuestCode->item(0)->nodeValue;
                        } else {
                            $typeGuestCode = "";
                        }
                        $pricePeriodsList = $RoomGuests->item($k)->getElementsByTagName("pricePeriodsList");
                        if ($pricePeriodsList->length > 0) {
                            $PricePeriods = $pricePeriodsList->item(0)->getElementsByTagName("PricePeriods");
                            if ($PricePeriods->length > 0) {
                                $amount = $PricePeriods->item(0)->getElementsByTagName("amount");
                                if ($amount->length > 0) {
                                    $amount = $amount->item(0)->nodeValue;
                                } else {
                                    $amount = "";
                                }
                                $amountPerNight = $PricePeriods->item(0)->getElementsByTagName("amountPerNight");
                                if ($amountPerNight->length > 0) {
                                    $amountPerNight = $amountPerNight->item(0)->nodeValue;
                                } else {
                                    $amountPerNight = "";
                                }
                                $stayDateEnd = $PricePeriods->item(0)->getElementsByTagName("stayDateEnd");
                                if ($stayDateEnd->length > 0) {
                                    $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
                                } else {
                                    $stayDateEnd = "";
                                }
                                $stayDateStart = $PricePeriods->item(0)->getElementsByTagName("stayDateStart");
                                if ($stayDateStart->length > 0) {
                                    $stayDateStart = $stayDateStart->item(0)->nodeValue;
                                } else {
                                    $stayDateStart = "";
                                }
                                $value = $PricePeriods->item(0)->getElementsByTagName("value");
                                if ($value->length > 0) {
                                    $value = $value->item(0)->nodeValue;
                                } else {
                                    $value = "";
                                }
                            }
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hotelrule_roomGuestsList');
                            $insert->values(array(
                                'guestNumber' => $guestNumber,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'age' => $age,
                                'typeGuestCode' => $typeGuestCode,
                                'amount' => $amount,
                                'amountPerNight' => $amountPerNight,
                                'stayDateEnd' => $stayDateEnd,
                                'stayDateStart' => $stayDateStart,
                                'value' => $value,
                                'hotelID' => $hotelID
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO GUEST: " . $e;
                            echo $return;
                        }
                    }
                }
            }
        }
    }

    $amountsList = $bookingRule->item(0)->getElementsByTagName("amountsList");
    if ($amountsList->length > 0) {
        $Amounts = $amountsList->item(0)->getElementsByTagName("Amounts");
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
                    $insert->into('hotelrule_amountsList');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'concept' => $concept,
                        'netAmount' => $netAmount,
                        'quote' => $quote,
                        'taxesList' => $taxesList,
                        'totalAmount' => $totalAmount,
                        'hotelID' => $hotelID
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
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>