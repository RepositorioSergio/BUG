<?php
$scurrency = strtoupper($currency);
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$travelplan = false;
$sql = "select city_xml50, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml50 = $row_settings["city_xml50"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml50 = 0;
}
if ($city_xml50 != "") {
    $city_xml50 = explode(":", $city_xml50);
    $x50_0 = $city_xml50[0];
    $x50_1 = $city_xml50[1];
    $x50_2 = $city_xml50[2];
}
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
if ((int) $residency > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $residency;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $residenceMarket = $row_settings["iso_code_2"];
    } else {
        $residenceMarket = "";
    }
} else {
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $residenceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='TravelPlanuser' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanuser = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanpassword' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='TravelPlanMarkup' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanMarkup = (double) $row_settings['value'];
} else {
    $TravelPlanMarkup = 0;
}
$sql = "select value from settings where name='TravelPlanserviceURL' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanserviceURL = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSystem' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSystem = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSalesChannel' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSalesChannel = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanlanguage' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanlanguage = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanConnectionString' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanConnectionString = $row_settings['value'];
}

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');

$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">  <soap:Body>   <HotelBookingRule xmlns="http://services.enginexml.rumbonet.riu.com">    <in0 xmlns="http://services.enginexml.rumbonet.riu.com">     <AdultsCount xmlns="http://dtos.enginexml.rumbonet.riu.com">2</AdultsCount>     <ChildCount xmlns="http://dtos.enginexml.rumbonet.riu.com">0</ChildCount>     <CountryCode xmlns="http://dtos.enginexml.rumbonet.riu.com">ES</CountryCode>     <HotelID xmlns="http://dtos.enginexml.rumbonet.riu.com">102</HotelID>     <HotelReservationCode xmlns="http://dtos.enginexml.rumbonet.riu.com" xsi:nil="true" />     <HotelReservationID xmlns="http://dtos.enginexml.rumbonet.riu.com">0</HotelReservationID>     <InfantsCount xmlns="http://dtos.enginexml.rumbonet.riu.com">0</InfantsCount>     <Language xmlns="http://dtos.enginexml.rumbonet.riu.com">PT</Language>     <MealPlan xmlns="http://dtos.enginexml.rumbonet.riu.com">AI</MealPlan>     <promocode xmlns="http://dtos.enginexml.rumbonet.riu.com">TESTXML</promocode>     <quoteType xmlns="http://dtos.enginexml.rumbonet.riu.com">AGE</quoteType>     <RateHotel xmlns="http://dtos.enginexml.rumbonet.riu.com">102</RateHotel>     <rateReference xmlns="http://dtos.enginexml.rumbonet.riu.com" xsi:nil="true" />     <RoomList xmlns="http://dtos.enginexml.rumbonet.riu.com">      <RoomConfig>       <RoomStayCandidateRule>        <AdultsCount>2</AdultsCount>        <Ages xsi:nil="true" />        <ChildCount>0</ChildCount>        <InfantsCount>0</InfantsCount>        <RoomTypeCode>DBSB</RoomTypeCode>       </RoomStayCandidateRule>      </RoomConfig>     </RoomList>     <RoomsCount xmlns="http://dtos.enginexml.rumbonet.riu.com">1</RoomsCount>     <StayDateEnd xmlns="http://dtos.enginexml.rumbonet.riu.com">20151209</StayDateEnd>     <StayDateStart xmlns="http://dtos.enginexml.rumbonet.riu.com">20151206</StayDateStart>    </in0>   </HotelBookingRule>  </soap:Body> </soap:Envelope>';
if ($TravelPlanserviceURL != "" and $TravelPlanuser != "" and $TravelPlanpassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $TravelPlanserviceURL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept-Encoding: gzip, deflate",
        "Accept: application/xml",
        "Content-type: application/xml",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // $response = curl_exec($ch);
    include "/srv/www/htdocs/ages.xml/src/App/Action/Travelplan/debug.php";
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    // if ($response === false) {
    // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    // } else {
    // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    // }
    // error_log("\r\n RESPONSE2: $response \r\n", 3, "/srv/www/htdocs/error_log");
    curl_close($ch);
    // Descomentar para ver o resultado do provider
    // Nao esquecer de alterar o session id para testar por causa de cache
    // echo $response;
    // die();
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_travelplan');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $TravelPlanserviceURL . $raw,
            'sqlcontext' => $response,
            'errcontext' => ''
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    // echo $response;
    // die();
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName('Envelope');
    $Body = $Envelope->item(0)->getElementsByTagName('Body');
    $HotelAvailResponse = $Body->item(0)->getElementsByTagName('HotelAvailResponse');
    $HotelAvailResponse = $HotelAvailResponse->item(0)->getElementsByTagName('HotelAvailResponse');
    $availabilityList = $HotelAvailResponse->item(0)->getElementsByTagName('availabilityList');
    $node = $availabilityList->item(0)->getElementsByTagName('AvailabilityGroup');
    for ($r = 0; $r < $node->length; $r++) {
        $bookingAmount = $node->item($r)->getElementsByTagName('bookingAmount');
        if ($bookingAmount->length > 0) {
            $bookingAmount = $bookingAmount->item(0)->nodeValue;
        } else {
            $bookingAmount = "";
        }
        $currencyCode = $node->item($r)->getElementsByTagName('currencyCode');
        if ($currencyCode->length > 0) {
            $currencyCode = $currencyCode->item(0)->nodeValue;
        } else {
            $currencyCode = "";
        }
        $hotelID = $node->item($r)->getElementsByTagName('hotelID');
        if ($hotelID->length > 0) {
            $hotelID = $hotelID->item(0)->nodeValue;
        } else {
            $hotelID = "";
        }
        $shid = $hotelID;
        $sfilter[] = " sid='$shid' ";
        $AdultsCount = $node->item($r)->getElementsByTagName('AdultsCount');
        if ($AdultsCount->length > 0) {
            $AdultsCount = $AdultsCount->item(0)->nodeValue;
        } else {
            $AdultsCount = "";
        }
        $ChildCount = $node->item($r)->getElementsByTagName('ChildCount');
        if ($ChildCount->length > 0) {
            $ChildCount = $ChildCount->item(0)->nodeValue;
        } else {
            $ChildCount = "";
        }
        $InfantsCount = $node->item($r)->getElementsByTagName('InfantsCount');
        if ($InfantsCount->length > 0) {
            $InfantsCount = $InfantsCount->item(0)->nodeValue;
        } else {
            $InfantsCount = "";
        }
        $impPromocode = $node->item($r)->getElementsByTagName('impPromocode');
        if ($impPromocode->length > 0) {
            $impPromocode = $impPromocode->item(0)->nodeValue;
        } else {
            $impPromocode = "";
        }
        $promocode = $node->item($r)->getElementsByTagName('promocode');
        if ($promocode->length > 0) {
            $promocode = $promocode->item(0)->nodeValue;
        } else {
            $promocode = "";
        }
        $quoteType = $node->item($r)->getElementsByTagName('quoteType');
        if ($quoteType->length > 0) {
            $quoteType = $quoteType->item(0)->nodeValue;
        } else {
            $quoteType = "";
        }
        $rateHotel = $node->item($r)->getElementsByTagName('rateHotel');
        if ($rateHotel->length > 0) {
            $rateHotel = $rateHotel->item(0)->nodeValue;
        } else {
            $rateHotel = "";
        }
        $rateType = $node->item($r)->getElementsByTagName('rateType');
        if ($rateType->length > 0) {
            $rateType = $rateType->item(0)->nodeValue;
        } else {
            $rateType = "";
        }
        $mealPlan = $node->item($r)->getElementsByTagName('mealPlan');
        if ($mealPlan->length > 0) {
            $mealPlan = $mealPlan->item(0)->nodeValue;
        } else {
            $mealPlan = "";
        }
        //rateReference
        $rateReference = $node->item($r)->getElementsByTagName('rateReference');
        if ($rateReference->length > 0) {
            $rateReference = $rateReference->item(0)->nodeValue;
        } else {
            $rateReference = "";
        }

        //amountsList
        $amountsList = $node->item($r)->getElementsByTagName('amountsList');
        $Amounts = $amountsList->item(0)->getElementsByTagName('Amounts');
        if ($Amounts->length > 0) {
            for ($i=0; $i < $Amounts->length; $i++) { 
                $concept = $Amounts->item($i)->getElementsByTagName('concept');
                if ($concept->length > 0) {
                    $concept = $concept->item(0)->nodeValue;
                } else {
                    $concept = "";
                }
                $netAmount = $Amounts->item($i)->getElementsByTagName('netAmount');
                if ($netAmount->length > 0) {
                    $netAmount = $netAmount->item(0)->nodeValue;
                } else {
                    $netAmount = "";
                }
                $quote = $Amounts->item($i)->getElementsByTagName('quote');
                if ($quote->length > 0) {
                    $quote = $quote->item(0)->nodeValue;
                } else {
                    $quote = "";
                }
                $taxesList = $Amounts->item($i)->getElementsByTagName('taxesList');
                if ($taxesList->length > 0) {
                    $taxesList = $taxesList->item(0)->nodeValue;
                } else {
                    $taxesList = "";
                }
                $totalAmount = $Amounts->item($i)->getElementsByTagName('totalAmount');
                if ($totalAmount->length > 0) {
                    $totalAmount = $totalAmount->item(0)->nodeValue;
                } else {
                    $totalAmount = "";
                }
            }
        }

        //bookingRulePenalties
        $bookingRulePenalties = $node->item($r)->getElementsByTagName('bookingRulePenalties');
        if ($bookingRulePenalties->length > 0) {
            $cancelPenalties = $bookingRulePenalties->item(0)->getElementsByTagName('cancelPenalties');
            if ($cancelPenalties->length > 0) {
                $amount = $cancelPenalties->item(0)->getElementsByTagName('amount');
                if ($amount->length > 0) {
                    $amount = $amount->item(0)->nodeValue;
                } else {
                    $amount = "";
                }
                $days = $cancelPenalties->item(0)->getElementsByTagName('days');
                if ($days->length > 0) {
                    $days = $days->item(0)->nodeValue;
                } else {
                    $days = "";
                }
                $percent = $cancelPenalties->item(0)->getElementsByTagName('percent');
                if ($percent->length > 0) {
                    $percent = $percent->item(0)->nodeValue;
                } else {
                    $percent = "";
                }
                $releaseDays = $cancelPenalties->item(0)->getElementsByTagName('releaseDays');
                if ($releaseDays->length > 0) {
                    $releaseDays = $releaseDays->item(0)->nodeValue;
                } else {
                    $releaseDays = "";
                }
                $totalAmount = $cancelPenalties->item(0)->getElementsByTagName('totalAmount');
                if ($totalAmount->length > 0) {
                    $totalAmount = $totalAmount->item(0)->nodeValue;
                } else {
                    $totalAmount = "";
                }
            }
            $modificationPenalties = $bookingRulePenalties->item(0)->getElementsByTagName('modificationPenalties');
            if ($modificationPenalties->length > 0) {
                $amount = $modificationPenalties->item(0)->getElementsByTagName('amount');
                if ($amount->length > 0) {
                    $amount = $amount->item(0)->nodeValue;
                } else {
                    $amount = "";
                }
                $days = $modificationPenalties->item(0)->getElementsByTagName('days');
                if ($days->length > 0) {
                    $days = $days->item(0)->nodeValue;
                } else {
                    $days = "";
                }
                $percent = $modificationPenalties->item(0)->getElementsByTagName('percent');
                if ($percent->length > 0) {
                    $percent = $percent->item(0)->nodeValue;
                } else {
                    $percent = "";
                }
                $releaseDays = $modificationPenalties->item(0)->getElementsByTagName('releaseDays');
                if ($releaseDays->length > 0) {
                    $releaseDays = $releaseDays->item(0)->nodeValue;
                } else {
                    $releaseDays = "";
                }
                $totalAmount = $modificationPenalties->item(0)->getElementsByTagName('totalAmount');
                if ($totalAmount->length > 0) {
                    $totalAmount = $totalAmount->item(0)->nodeValue;
                } else {
                    $totalAmount = "";
                }
            }
            $noShowPenalties = $bookingRulePenalties->item(0)->getElementsByTagName('noShowPenalties');
            if ($noShowPenalties->length > 0) {
                $amount = $noShowPenalties->item(0)->getElementsByTagName('amount');
                if ($amount->length > 0) {
                    $amount = $amount->item(0)->nodeValue;
                } else {
                    $amount = "";
                }
                $days = $noShowPenalties->item(0)->getElementsByTagName('days');
                if ($days->length > 0) {
                    $days = $days->item(0)->nodeValue;
                } else {
                    $days = "";
                }
                $percent = $noShowPenalties->item(0)->getElementsByTagName('percent');
                if ($percent->length > 0) {
                    $percent = $percent->item(0)->nodeValue;
                } else {
                    $percent = "";
                }
                $releaseDays = $noShowPenalties->item(0)->getElementsByTagName('releaseDays');
                if ($releaseDays->length > 0) {
                    $releaseDays = $releaseDays->item(0)->nodeValue;
                } else {
                    $releaseDays = "";
                }
                $totalAmount = $noShowPenalties->item(0)->getElementsByTagName('totalAmount');
                if ($totalAmount->length > 0) {
                    $totalAmount = $totalAmount->item(0)->nodeValue;
                } else {
                    $totalAmount = "";
                }
            }
        }

        $roomsCount = $node->item($r)->getElementsByTagName('roomsCount');
        if ($roomsCount->length > 0) {
            $roomsCount = $roomsCount->item(0)->nodeValue;
        } else {
            $roomsCount = "";
        }
        $stayDateEnd = $node->item($r)->getElementsByTagName('stayDateEnd');
        if ($stayDateEnd->length > 0) {
            $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
        } else {
            $stayDateEnd = "";
        }
        $stayDateStart = $node->item($r)->getElementsByTagName('stayDateStart');
        if ($stayDateStart->length > 0) {
            $stayDateStart = $stayDateStart->item(0)->nodeValue;
        } else {
            $stayDateStart = "";
        }

        $typePrice = $node->item($r)->getElementsByTagName('typePrice');
        if ($typePrice->length > 0) {
            $typePrice = $typePrice->item(0)->nodeValue;
        } else {
            $typePrice = "";
        }

        //roomStayList
        $roomStayList = $node->item($r)->getElementsByTagName('roomStayList');
        $RoomStay = $roomStayList->item(0)->getElementsByTagName('RoomStay');
        if ($RoomStay->length > 0) {
            $mealPlanSupplementPeriodsList = $RoomStay->item(0)->getElementsByTagName('mealPlanSupplementPeriodsList');
            if ($mealPlanSupplementPeriodsList->length > 0) {
                $mealPlanSupplementPeriodsList = $mealPlanSupplementPeriodsList->item(0)->nodeValue;
            } else {
                $mealPlanSupplementPeriodsList = "";
            }
            $pricesPeriodsList = $RoomStay->item(0)->getElementsByTagName('pricesPeriodsList');
            if ($pricesPeriodsList->length > 0) {
                $pricesPeriodsList = $pricesPeriodsList->item(0)->nodeValue;
            } else {
                $pricesPeriodsList = "";
            }
            $roomAmount = $RoomStay->item(0)->getElementsByTagName('roomAmount');
            if ($roomAmount->length > 0) {
                $roomAmount = $roomAmount->item(0)->nodeValue;
            } else {
                $roomAmount = "";
            }
            $roomNumber = $RoomStay->item(0)->getElementsByTagName('roomNumber');
            if ($roomNumber->length > 0) {
                $roomNumber = $roomNumber->item(0)->nodeValue;
            } else {
                $roomNumber = "";
            }
            $roomTypeCode = $RoomStay->item(0)->getElementsByTagName('roomTypeCode');
            if ($roomTypeCode->length > 0) {
                $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
            } else {
                $roomTypeCode = "";
            }
            $roomGuestsList = $RoomStay->item(0)->getElementsByTagName('roomGuestsList');
            if ($roomGuestsList->length > 0) {
                $RoomGuests = $roomGuestsList->item(0)->getElementsByTagName('RoomGuests');
                for ($k=0; $k < $RoomGuests->length; $k++) { 
                    $age = $RoomGuests->item($k)->getElementsByTagName('age');
                    if ($age->length > 0) {
                        $age = $age->item(0)->nodeValue;
                    } else {
                        $age = "";
                    }
                    $guestNumber = $RoomGuests->item($k)->getElementsByTagName('guestNumber');
                    if ($guestNumber->length > 0) {
                        $guestNumber = $guestNumber->item(0)->nodeValue;
                    } else {
                        $guestNumber = "";
                    }
                    $typeGuestCode = $RoomGuests->item($k)->getElementsByTagName('typeGuestCode');
                    if ($typeGuestCode->length > 0) {
                        $typeGuestCode = $typeGuestCode->item(0)->nodeValue;
                    } else {
                        $typeGuestCode = "";
                    }
                    $pricePeriodsList = $RoomGuests->item($k)->getElementsByTagName('pricePeriodsList');
                    if ($pricePeriodsList->length > 0) {
                        $PricePeriods = $pricePeriodsList->item(0)->getElementsByTagName('PricePeriods');
                        if ($PricePeriods->length > 0) {
                            $amount = $PricePeriods->item(0)->getElementsByTagName('amount');
                            if ($amount->length > 0) {
                                $amount = $amount->item(0)->nodeValue;
                            } else {
                                $amount = "";
                            }
                            $amountPerNight = $PricePeriods->item(0)->getElementsByTagName('amountPerNight');
                            if ($amountPerNight->length > 0) {
                                $amountPerNight = $amountPerNight->item(0)->nodeValue;
                            } else {
                                $amountPerNight = "";
                            }
                            $stayDateEnd = $PricePeriods->item(0)->getElementsByTagName('stayDateEnd');
                            if ($stayDateEnd->length > 0) {
                                $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
                            } else {
                                $stayDateEnd = "";
                            }
                            $stayDateStart = $PricePeriods->item(0)->getElementsByTagName('stayDateStart');
                            if ($stayDateStart->length > 0) {
                                $stayDateStart = $stayDateStart->item(0)->nodeValue;
                            } else {
                                $stayDateStart = "";
                            }
                            $value = $PricePeriods->item(0)->getElementsByTagName('value');
                            if ($value->length > 0) {
                                $value = $value->item(0)->nodeValue;
                            } else {
                                $value = "";
                            }
                        }
                    }
                }
            }
        }
        

        for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
            if (is_array($tmp[$shid])) {
                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
            } else {
                $baseCounterDetails = 0;
            }
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $hotelID;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $hotelID;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-50";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $AdultsCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $ChildCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['infants'] = $InfantsCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $amount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $amountWithoutOffer;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($mealPlan);
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                $amount = $noOfNights * $AmountAfterTax;
                if ($TravelPlanMarkup != 0) {
                    $amount = $amount + (($amount * $TravelPlanMarkup) / 100);
                }
                // Geo target markup
                if ($internalmarkup != 0) {
                    $amount = $amount + (($amount * $internalmarkup) / 100);
                }
                // Agent markup
                if ($agent_markup != 0) {
                    $amount = $amount + (($amount * $agent_markup) / 100);
                }
                // Fallback Markup
                if ($TravelPlanMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                    $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                }
                // Agent discount
                if ($agent_discount != 0) {
                    $amount = $amount - (($amount * $agent_discount) / 100);
                }
                if ($scurrency != "" and $currency != $scurrency) {
                    $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                }
                $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                $pricebreakdownCount = $pricebreakdownCount + 1;
            }
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currencyCode;
            if ($promocode != "") {
                $tmp[$shid]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $translator->translate($promocode);
            } else {
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
            }
            // policyDescription nao existe
            // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $policyDescription;
            $travelplan = true;
        }
    }
}
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($travelplan == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mglobalia where " . $sfilter;
        error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        $result2->buffer();
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            foreach ($resultSet2 as $row2) {
                // $sidfilter[] = "id=" . $row2->hid;
                $sidfilter[] = $row2->hid;
                if (is_array($hotels_array[$row2->hid])) {
                    // Append to original details
                    $tmph = $hotels_array[$row2->hid]['details'];
                    $tmps = $tmp[$row2->sid]['details'];
                    foreach ($tmph as $key => $value) {
                        $last = count($tmph[$key]);
                        foreach ($tmps[$key] as $keyd => $valued) {
                            $tmph[$key][$last] = $valued;
                            $last ++;
                        }
                    }
                    $hotels_array[$row2->hid]['details'] = $tmph;
                } else {
                    $hotels_array[$row2->hid] = $tmp[$row2->sid];
                }
            }
        }
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 50;
        error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_travelplan');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_travelplan');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $response,
                'data' => base64_encode(serialize($hotels_array)),
                'searchsettings' => base64_encode(serialize($requestdata))
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
?>