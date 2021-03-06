<?php
// error_log("\r\nOlympia Europe - Policies\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\I18n\Translator\Translator;
use Laminas\Http\Client;
use Laminas\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$total = 0;
// error_log("\r\n COMECOU POLICIES \r\n", 3, "/srv/www/htdocs/error_log");
try {
    $db = new \Laminas\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_olympiaeurope where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $lang = $searchsettings['lang'];
    $currency = $searchsettings['currency'];
    $from = $searchsettings['from'];
    $to = $searchsettings['to'];
    $destination = $searchsettings['destination'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    $index = $searchsettings['index'];
    $ipaddress = $searchsettings['ipaddress'];
    $nationality = $searchsettings['nationality'];
    $residency = $searchsettings['residency'];
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$affiliate_id = 0;
$sql = "select value from settings where name='enableolympiaeurope' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_olympia = $affiliate_id;
} else {
    $affiliate_id_olympia = 0;
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
    $sql = "select value from settings where name='olympiaeuropeDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_olympia" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='olympiaeuropelogin' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropelogin = $row_settings['value'];
}
$sql = "select value from settings where name='olympiaeuropepassword' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropepassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='olympiaeuropeContextDatabase' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropeContextDatabase = $row_settings['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelAvailRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropeOTAHotelAvailRQ = $row_settings['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTABookingListRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTABookingListRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelCancelRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelCancelRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelDescInfoRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelDescInfoRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelReadRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelReadRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelResRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelResRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelSearchRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelSearchRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeMarkup' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropeMarkup = (double) $row_settings['value'];
} else {
    $olympiaeuropeMarkup = 0;
}
$sql = "select value from settings where name='olympiaeuropeb2cMarkup' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeb2cMarkup = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeaffiliates_id' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropebranches_id' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropebranches_id = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeParallelSearch' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeParallelSearch = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeSearchSortorder' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeCurrencyCode' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeCurrencyCode = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeTimeout' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeTimeout = (int) $row['value'];
}

$breakdown = array();
for ($w = 0; $w < count($quoteid); $w ++) {
    $outputArray = array();
    $arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
    foreach ($arrIt as $sub) {
        $subArray = $arrIt->getSubIterator();
        if (isset($quoteid[$w])) {
            if (isset($subArray['quoteid'])) {
                if ($subArray['quoteid'] === $quoteid[$w]) {
                    $outputArray[] = iterator_to_array($subArray);
                    $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                        ->key();
                }
            }
        }
    }
    if (! is_array($outputArray)) {
        $response['error'] = "Unable to handle request #3";
        return false;
    } else {
        array_push($breakdown, $outputArray);
    }
}

$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');

$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['hotelid'];
            $scode = $value['shid'];
            $hotel_code = $value['shid'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $adults = $value['adults'];
        $children = $value['children'];
        $room = $value['room'];
        $MealPlanCode = $value['MealPlanCode'];
        $roomid = $value['roomid'];
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        $raw = '<?xml version="1.0" encoding="utf-8"?><soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/"><soap-env:Header><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><wsse:Username>' . $olympiaeuropelogin . '</wsse:Username><wsse:Password>' . $olympiaeuropepassword . '</wsse:Password><Context>' . $olympiaeuropeContextDatabase . '</Context></wsse:Security></soap-env:Header><soap-env:Body><OTA_HotelAvailRQ xmlns="http://parsec.es/hotelapi/OTA2014Compact"><HotelSearch><Currency Code="' . $olympiaeuropeCurrencyCode . '"/><HotelRef HotelCode="' . $hotel_code . '"/><MealPlan Code="' . $MealPlanCode . '" /><DateRange Start="' . $from_date . '" End="' . $to_date . '"/><RoomCandidates> <RoomCandidate RPH="1" RoomTypeCode="' . $roomid . '"><Guests><Guest AgeCode="A" Count="' . $adults . '" />';
        if ($children > 0) {
            for ($z = 0; $z < $children; $z ++) {
                $raw .= '<Guest AgeCode="C" Count="1" Age="' . $children_ages[$z] . '"';
            }
        }
        $raw .= '</Guests></RoomCandidate></RoomCandidates></HotelSearch></OTA_HotelAvailRQ></soap-env:Body></soap-env:Envelope>';
        error_log("\r\nOlynmpia Europe RAW: $raw\r\n", 3, "/srv/www/htdocs/error_log");
        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Accept: application/xml',
            'Content-Length: ' . strlen($raw)
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $olympiaeuropeOTAHotelAvailRQ);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, $olympiaeuropeTimeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        error_log("\r\nResponse: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_olympiaeurope');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $olympiaeuropeOTAHotelAvailRQ,
                'sqlcontext' => $response2,
                'errcontext' => ''
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        if ($response2 != "") {
            $inputDoc = new DOMDocument();
            $inputDoc->loadXML($response2);
            $OTA_HotelAvailRS = $inputDoc->getElementsByTagName('OTA_HotelAvailRS');
            $Hotelsb = $OTA_HotelAvailRS->item(0)->getElementsByTagName('Hotels');
            if ($Hotelsb->length > 0) {
                $DateRange = $Hotelsb->item(0)->getElementsByTagName('DateRange');
                if ($DateRange->length > 0) {
                    $Start = $DateRange->item(0)->getAttribute('Start');
                    $End = $DateRange->item(0)->getAttribute('End');
                }
                $RoomCandidates = $Hotelsb->item(0)->getElementsByTagName('RoomCandidates');
                if ($RoomCandidates->length > 0) {
                    $RoomCandidate = $RoomCandidates->item(0)->getElementsByTagName('RoomCandidate');
                    if ($RoomCandidate->length > 0) {
                        $RPH = $RoomCandidate->item(0)->getAttribute('RPH');
                        $Guests = $RoomCandidate->item(0)->getElementsByTagName('Guests');
                        if ($Guests->length > 0) {
                            $Guest = $Guests->item(0)->getElementsByTagName('Guest');
                            if ($Guest->length > 0) {
                                $AgeCode = $Guest->item(0)->getAttribute('AgeCode');
                                $Count = $Guest->item(0)->getAttribute('Count');
                            }
                        }
                    }
                }
                $Hotelb = $Hotelsb->item(0)->getElementsByTagName('Hotel');
                if ($Hotelb->length > 0) {
                    for ($i = 0; $i < $Hotelb->length; $i ++) {
                        $Info = $Hotelb->item($i)->getElementsByTagName('Info');
                        if ($Info->length > 0) {
                            $HotelCode = $Info->item(0)->getAttribute('HotelCode');
                            $shid = $HotelCode;
                            $HotelName = $Info->item(0)->getAttribute('HotelName');
                            $HotelCityCode = $Info->item(0)->getAttribute('HotelCityCode');
                            $Rating = $Info->item(0)->getAttribute('Rating');
                            $MasterCode = $Info->item(0)->getAttribute('MasterCode');
                            $Recommended = $Info->item(0)->getAttribute('Recommended');
                            $HotelProvider = $Info->item(0)->getElementsByTagName('HotelProvider');
                            if ($HotelProvider->length > 0) {
                                $HotelProvider = $HotelProvider->item(0)->nodeValue;
                            } else {
                                $HotelProvider = "";
                            }
                            $HotelIdent = $Info->item(0)->getElementsByTagName('HotelIdent');
                            if ($HotelIdent->length > 0) {
                                $HotelIdent = $HotelIdent->item(0)->nodeValue;
                            } else {
                                $HotelIdent = "";
                            }
                        }
                        $BestPrice = $Hotelb->item($i)->getElementsByTagName('BestPrice');
                        if ($BestPrice->length > 0) {
                            $Amount = $BestPrice->item(0)->getAttribute('Amount');
                            $Currency = $BestPrice->item(0)->getAttribute('Currency');
                        }
                        $Rooms = $Hotelb->item($i)->getElementsByTagName('Rooms');
                        if ($Rooms->length > 0) {
                            $Room = $Rooms->item(0)->getElementsByTagName('Room');
                            if ($Room->length > 0) {
                                for ($iAux = 0; $iAux < $Room->length; $iAux ++) {
                                    $RPH = $Room->item($iAux)->getAttribute('RPH');
                                    $Best = $Room->item($iAux)->getAttribute('Best');
                                    $Status = $Room->item($iAux)->getAttribute('Status');
                                    $RoomType = $Room->item($iAux)->getElementsByTagName('RoomType');
                                    if ($RoomType->length > 0) {
                                        $RoomTypeCode = $RoomType->item(0)->getAttribute('Code');
                                        $RoomTypeName = $RoomType->item(0)->getAttribute('Name');
                                    }
                                    $RoomRates = $Room->item($iAux)->getElementsByTagName('RoomRates');
                                    if ($RoomRates->length > 0) {
                                        $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
                                        if ($RoomRate->length > 0) {
                                            $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');
                                            $BookingCode = $RoomRate->item(0)->getAttribute('BookingCode');
                                            $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                                            if ($Total->length > 0) {
                                                $Amount = $Total->item(0)->getAttribute('Amount');
                                                $Commission = $Total->item(0)->getAttribute('Commission');
                                                $Currency = $Total->item(0)->getAttribute('Currency');
                                            }
                                            if ($RoomTypeCode == $roomid) {
                                                $total = $Amount;
                                                $nettotal = $total;
                                                $CancelPenaltyArray = array();
                                                $count = 0;
                                                $CancelPenalties = $RoomRate->item(0)->getElementsByTagName('CancelPenalties');
                                                if ($CancelPenalties->length > 0) {
                                                    $CancellationCostsToday = $CancelPenalties->item(0)->getAttribute('CancellationCostsToday');
                                                    $NonRefundable = $CancelPenalties->item(0)->getAttribute('NonRefundable');
                                                    $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName('CancelPenalty');
                                                    if ($CancelPenalty->length > 0) {
                                                        for ($iAux2 = 0; $iAux2 < $CancelPenalty->length; $iAux2 ++) {
                                                            $Deadline = $CancelPenalty->item($iAux2)->getElementsByTagName('Deadline');
                                                            if ($Deadline->length > 0) {
                                                                $TimeUnit = $Deadline->item(0)->getAttribute('TimeUnit');
                                                                $Units = $Deadline->item(0)->getAttribute('Units');
                                                            }
                                                            $Charge = $CancelPenalty->item($iAux2)->getElementsByTagName('Charge');
                                                            if ($Charge->length > 0) {
                                                                $ChargeAmount = $Charge->item(0)->getAttribute('Amount');
                                                                $ChargeCurrency = $Charge->item(0)->getAttribute('Currency');
                                                            }
                                                            $CancelPenaltyArray[$count]['Units'] = $Units;
                                                            $cancelpolicy .= $translator->translate("If you cancel booking ") . $Units . " " . $translator->translate($TimeUnit) . "(s) " . $translator->translate(" before checkin cost ") . $ChargeCurrency . $ChargeAmount . " .<br>";
                                                            $count = $count + 1;
                                                        }
                                                    }
                                                }
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //
        // Policies
        //
        $item['code'] = $value['shid'];
        $item['name'] = $value['name'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['nettotal'];
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['RoomTypeCode'] = $value['room_type'];
        $item['RoomType'] = $value['room_type'];
        $item['RoomDescription'] = $value['room_description'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        $days = "- " . $CancelPenaltyArray[0]['Units'] . " days";
        $date = strftime("%a, %e %b %Y", strtotime($from_date . $days));
        if ($NonRefundable == 0) {
            $item['nonrefundable'] = false;
        } else {
            $item['nonrefundable'] = true;
        }
        $item['cancelpolicy'] = $cancelpolicy;
        $item['cancelpolicy_details'] = $$item['nonrefundable'] = true;
        ;
        $item['cancelpolicy_deadline'] = $date;
        $item['cancelpolicy_deadlinetimestamp'] = $date;
        
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Laminas\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_molympiaeurope where sid='" . $shid . "' and hid=" . $hid;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$db = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $db2 = new \Laminas\Db\Adapter\Adapter($config);
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db2->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $db2->getDriver()
        ->getConnection()
        ->disconnect();
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$images = array();
try {
    $db = new \Laminas\Db\Adapter\Adapter($config);
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    if ($result instanceof ResultInterface && $result->isQueryResult()) {
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        foreach ($resultSet as $row) {
            $item = array();
            $item['url'] = "//world-wide-web-servers.com/static/hotels/" . $row->url;
            $item['description'] = $row->description;
            array_push($images, $item);
        }
    }
    $db->getDriver()
        ->getConnection()
        ->disconnect();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
$response['code'] = $vector['code'];
?>