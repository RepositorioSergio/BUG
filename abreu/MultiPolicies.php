<?php
error_log("\r\nMulti Policies ABREU\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
use Zend\Http\Client;
use Zend\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$salestaxes = 0;
$salestaxesfees = 0;
$baserate = 0;
$affiliate_id_expedia = 0;
$occupancies = "";
$sindex = $index;
$db = new \Zend\Db\Adapter\Adapter($config);
if ($details == "hoteldetails") {
    // Detail level
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_abreu where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_abreu where session_id='$session_id'";
}
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_settings->buffer();
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
    if ($details == "hoteldetails") {
        $selectedAdults = array();
        $selectedAdults[$nroom] = $adt;
        // Children + Ages
        $selectedChildrenAges = array();
        $selectedChildren = array();
        $selectedChildren[$nroom] = $chd;
        if ($chd > 0) {
            $children_ages = explode(",", $children_ages);
            for ($w = 0; $w < count($children_ages); $w ++) {
                $selectedChildrenAges[$nroom][$w] = $children_ages[$w];
            }
        }
    }
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$affiliate_id = 0;
$branch_filter = '';
$sql = "select value from settings where name='enableabreu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_abreu = $affiliate_id;
} else {
    $affiliate_id_abreu = 0;
}
$sql = "select value from settings where name='AbreuUsername' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuUsername = $row_settings['value'];
}
$sql = "select value from settings where name='Abreupassword' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreupassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='AbreuMarkup' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuMarkup = (double) $row_settings['value'];
} else {
    $AbreuMarkup = 0;
}
// URL
$sql = "select value from settings where name='AbreuHOTELAVAILABILITY' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuHOTELAVAILABILITY = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuContext' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuContext = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuCustomerID' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuCustomerID = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuOnRequest' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuOnRequest = $row_settings['value'];
}
$sql = "select value from settings where name='Abreub2cMarkup' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreub2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuCurrency' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuCurrency = $row_settings['value'];
}
if ($AbreuCurrency == "") {
    $AbreuCurrency = "USD";
}

$outputArray = array();
$arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
foreach ($arrIt as $sub) {
    $subArray = $arrIt->getSubIterator();
    if (isset($quoteid[$nroom])) {
        if (isset($subArray['quoteid'])) {
            if ($subArray['quoteid'] === $quoteid[$nroom]) {
                $outputArray[] = iterator_to_array($subArray);
                $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                    ->key();
            }
        }
    }
}
$breakdownTmp = array();
if (! is_array($outputArray)) {
    $response['error'] = "Unable to handle request #3";
    return false;
} else {
    array_push($breakdownTmp, $outputArray);
}
$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');
$c = $nroom;
$response = array();
$roombreakdown2 = array();
foreach ($breakdownTmp as $k => $v) {
    foreach ($v as $key => $value) {
        $shid = $value['shid'];
        $code = $value['hotelid'];
        $scode = $value['shid'];
        $HotelId = $value['hotelid'];
        $room_code = $value['roomid'];
        $roomid = $value['roomid'];
        $bookingcode = $value['bookingcode'];
        $sourceMarket = $value['sourceMarket'];
        $MealPlanCode = $value['MealPlanCode'];
        
        $item = array();
        $cancelation_deadline = 0;
        $cancelation_details = "";
        $nonrefundable = false;
        $from = date("Y-m-d", strtotime($from));
        $to = date("Y-m-d", strtotime($to));
        $raw2 = '<?xml version="1.0" encoding="utf-8"?><soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/"><soap-env:Header><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><wsse:Username>' . $AbreuUsername . '</wsse:Username><wsse:Password>' . $Abreupassword . '</wsse:Password><Context>' . $AbreuContext . '</Context></wsse:Security></soap-env:Header><soap-env:Body><OTA_HotelAvailRQ xmlns=" http://parsec.es/hotelapi/OTA2014Compact" ><HotelSearch><HotelLocation HotelCode="' . $HotelId . '" /><MealPlan Code="' . $MealPlanCode . '" /><Currency Code="' . $AbreuCurrency . '" /><DateRange Start="' . $from . '" End="' . $to . '" /><GuestCountry Code="' . $sourceMarket . '"/><RoomCandidates><RoomCandidate RPH="1" RoomTypeCode="' . $roomid . '"><Guests><Guest AgeCode="A" Count="' . $adt . '" />';
        for ($z = 0; $z < $chd; $z ++) {
            $raw2 .= '<Guest AgeCode="C" Count="1" Age="' . $children_ages[$z] . '" />';
        }
        $raw2 .= '</Guests></RoomCandidate></RoomCandidates></HotelSearch></OTA_HotelAvailRQ></soap-env:Body></soap-env:Envelope>';
        // error_log("\r\nAbreu Multi Request: $raw2\r\n", 3, "/srv/www/htdocs/error_log");
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $AbreuHOTELAVAILABILITY);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/xml",
            "Content-type: text/xml",
            "Content-length: " . strlen($raw2)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        // error_log("\r\nAbreu Multi Policies Response: $response2\r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\nBooking Code: $bookingcode\r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_abreu');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $$AbreuHOTELAVAILABILITY . $raw2,
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
        
        $cancel = array();
        $count = 0;
        $BookingCode = "";
        $Amount = "";
        if ($response2 != "") {
            $inputDoc = new DOMDocument();
            $inputDoc->loadXML($response2);
            $OTA_HotelAvailRS = $inputDoc->getElementsByTagName('OTA_HotelAvailRS');
            if ($OTA_HotelAvailRS->length > 0) {
                $Hotels = $OTA_HotelAvailRS->item(0)->getElementsByTagName('Hotels');
                if ($Hotels->length > 0) {
                    $HotelInfo = $Hotels->item(0)->getElementsByTagName('Hotel');
                    if ($HotelInfo->length > 0) {
                        for ($i = 0; $i < $HotelInfo->length; $i ++) {
                            $Info = $HotelInfo->item($i)->getElementsByTagName('Info');
                            if ($Info->length > 0) {
                                $Recommended = $Info->item(0)->getAttribute('Recommended');
                                $MasterCode = $Info->item(0)->getAttribute('MasterCode');
                                $Rating = $Info->item(0)->getAttribute('Rating');
                                $HotelCityCode = $Info->item(0)->getAttribute('HotelCityCode');
                                $HotelName = $Info->item(0)->getAttribute('HotelName');
                                $HotelCode = $Info->item(0)->getAttribute('HotelCode');
                            }
                            $BestPrice = $HotelInfo->item($i)->getElementsByTagName('BestPrice');
                            if ($BestPrice->length > 0) {
                                $Currency = $BestPrice->item(0)->getAttribute('Currency');
                                $Amount = $BestPrice->item(0)->getAttribute('Amount');
                            }
                            $Rooms = $HotelInfo->item($i)->getElementsByTagName('Rooms');
                            if ($Rooms->length > 0) {
                                $Room = $Rooms->item(0)->getElementsByTagName('Room');
                                // error_log("\r\nRoom Length: " . $Room->length . "\r\n", 3, "/srv/www/htdocs/error_log");
                                if ($Room->length > 0) {
                                    for ($j = 0; $j < $Room->length; $j ++) {
                                        $RPH = $Room->item($j)->getAttribute('RPH');
                                        $Status = $Room->item($j)->getAttribute('Status');
                                        $Best = $Room->item($j)->getAttribute('Best');
                                        $RoomType = $Room->item($j)->getElementsByTagName('RoomType');
                                        if ($RoomType->length > 0) {
                                            $Code = $RoomType->item(0)->getAttribute('Code');
                                            $Name = $RoomType->item(0)->getAttribute('Name');
                                            $Special = $RoomType->item(0)->getElementsByTagName('Special');
                                            if ($Special->length > 0) {
                                                $Special = $Special->item(0)->nodeValue;
                                            } else {
                                                $Special = "";
                                            }
                                        }
                                        if ($Code == $roomid) {
                                            $RoomRates = $Room->item($j)->getElementsByTagName('RoomRates');
                                            if ($RoomRates->length > 0) {
                                                $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
                                                if ($RoomRate->length > 0) {
                                                    $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');
                                                    $BookingCode = $RoomRate->item(0)->getAttribute('BookingCode');
                                                    $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                                                    if ($Total->length > 0) {
                                                        $Currency = $Total->item(0)->getAttribute('Currency');
                                                        $Amount = $Total->item(0)->getAttribute('Amount');
                                                        $MinPrice = $Total->item(0)->getAttribute('MinPrice');
                                                        $Commission = $Total->item(0)->getAttribute('Commission');
                                                    } else {
                                                        $Currency = "";
                                                        $Amount = 0;
                                                        $MinPrice = 0;
                                                        $Commission = 0;
                                                    }
                                                    $CancelPenalties = $RoomRate->item(0)->getElementsByTagName('CancelPenalties');
                                                    if ($CancelPenalties->length > 0) {
                                                        if ($CancelPenalties->item(0)->getAttribute("NonRefundable") == 1) {
                                                            $nonrefundable = true;
                                                        }
                                                        $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName('CancelPenalty');
                                                        if ($CancelPenalty->length > 0) {
                                                            for ($x = 0; $x < $CancelPenalty->length; $x ++) {
                                                                $Deadline = $CancelPenalty->item($x)->getElementsByTagName('Deadline');
                                                                if ($Deadline->length > 0) {
                                                                    $Units = $Deadline->item(0)->getAttribute('Units');
                                                                    $cancel[$count]['Units'] = $Units;
                                                                    $TimeUnit = $Deadline->item(0)->getAttribute('TimeUnit');
                                                                    $cancel[$count]['TimeUnit'] = $TimeUnit;
                                                                } else {
                                                                    $Units = "";
                                                                    $TimeUnit = "";
                                                                }
                                                                $Charge = $CancelPenalty->item($x)->getElementsByTagName('Charge');
                                                                if ($Charge->length > 0) {
                                                                    $ChargeCurrency = $Charge->item(0)->getAttribute('Currency');
                                                                    $cancel[$count]['Currency'] = $ChargeCurrency;
                                                                    $ChargeAmount = $Charge->item(0)->getAttribute('Amount');
                                                                    $cancel[$count]['Amount'] = $ChargeAmount;
                                                                } else {
                                                                    $ChargeCurrency = "";
                                                                    $ChargeAmount = "";
                                                                }
                                                                $count ++;
                                                            }
                                                        }
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
        if ($Amount == "") {
            $pricesold_out = true;
        }
        $net = $Amount;
        if ($net > (float) $value["nettotal"]) {
            $oldtotal = $value['total'];
            $oldnettotal = $value['nettotal'];
            $value['nettotal'] = $net;
            if ($AbreuMarkup != 0) {
                $net = $net + (($net * $AbreuMarkup) / 100);
            }
            // Geo target markup
            if ($internalmarkup != 0) {
                $net = $net + (($net * $internalmarkup) / 100);
            }
            // Agent markup
            if ($agent_markup != 0) {
                $net = $net + (($net * $agent_markup) / 100);
            }
            // Fallback Markup
            if ($AbreuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                $net = $net + (($net * $HotelsMarkupFallback) / 100);
            }
            // Agent discount
            if ($agent_discount != 0) {
                $net = $net - (($net * $agent_discount) / 100);
            }
            if ($AbreuCurrency != "" and $currency != $AbreuCurrency and $currency != "") {
                $net = $CurrencyConverter->convert($net, $AbreuCurrency, $currency);
            }
            $value['total'] = $net;
            // error_log("\r\nNew Net Total: " . $oldnettotal . " -> " . $value['nettotal'] . "\r\n", 3, "/srv/www/htdocs/error_log");
            // error_log("\r\nNew Total: " . $oldtotal . " -> " . $value['total'] . "\r\n", 3, "/srv/www/htdocs/error_log");
            $sql = new Sql($db);
            $sql = "delete from dp_hotels_pricechange where session_id='" . $session_id . "' and quoteid='" . (string) $value['quoteid'] . "'";
            try {
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $results = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('dp_hotels_pricechange');
                $insert->values(array(
                    'datetime_created' => time(),
                    'session_id' => (string) $session_id,
                    'total' => (string) $value['total'],
                    'nettotal' => (string) $value['nettotal'],
                    'oldtotal' => (string) $oldtotal,
                    'oldnettotal' => (string) $oldnettotal,
                    'quoteid' => (string) $value['quoteid']
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $pricechanged = true;
            $warning = $translator->translate("Price has changed. Please review booking.");
        }
        //
        // Policies
        //
        if ($BookingCode == "") {
            $BookingCode = $bookingcode;
        }
        $item['code'] = $value['shid'];
        $item['name'] = $value['name'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['nett'];
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['nonrefundable'] = $nonrefundable;
        $item['recommended'] = $value['recommended'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        if ($nonrefundable == true) {
            $item['nonrefundable'] = true;
            $item['cancelpolicy'] = $translator->translate("This is a non refundable booking");
            $item['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
            $item['cancelpolicy_deadlinetimestamp'] = time();
        } else {
            if ($cancel[0]['TimeUnit'] == "Day") {
                $cDate = mktime(0, 0, 0, date("m", $fromAbreu), date("d", $fromAbreu) - $cancel[0]['Units'], date("Y", $fromAbreu));
                if ($cDate <= time()) {
                    $cDate = time();
                    $item['nonrefundable'] = true;
                }
                $item['cancelpolicy'] = $translator->translate("Charge") . " " . $cancel[0]['Amount'] . " " . $cancel[0]['Currency'] . " " . $translator->translate("if cancelled after") . " " . strftime("%a, %e %b %Y", $cDate);
                $item['cancelpolicy_details'] = $item['cancelpolicy'];
                $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $cDate);
                $item['cancelpolicy_deadlinetimestamp'] = $cDate;
            } else {
                $cancelation_details = "The Cancellation in " . $cancel[0]['Units'] . " " . $cancel[0]['TimeUnit'] . "s cost " . $cancel[0]['Amount'] . " " . $cancel[0]['Currency'];
                if ($count > 1) {
                    $cancelation_details = $cancelation_details . "<br/> If you Cancel in " . $cancel[0]['Units'] . " " . $cancel[0]['TimeUnit'] . "s cost " . $cancel[0]['Amount'] . " " . $cancel[0]['Currency'];
                }
                $item['cancelpolicy'] = $cancelation_details;
                $item['cancelpolicy_details'] = $cancelation_details;
                $item['cancelpolicy_deadline'] = $cancel[0]['Units'] . " " . $cancel[0]['TimeUnit'];
            }
        }
        
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mabreu where sid='" . $shid . "' and hid=" . $hid;
// error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
$row_hotel->buffer();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
// error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel->buffer();
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $row_country->buffer();
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
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
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
// error_log("\r\n" . print_r($responseContent, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
$hotel['checkin'] = $responseContent[$shid]['checkin'];
$hotel['fees'] = $responseContent[$shid]['fees'];
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown2;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['sales_taxes'] = $filter->filter($salestaxes);
$response['sales_taxesplain'] = number_format($salestaxes, 2, '.', '');
$response['taxes'] = $filter->filter($salestaxesfees);
$response['taxesplain'] = number_format($salestaxesfees, 2, '.', '');
$response['base_rate'] = $filter->filter($baserate);
$response['base_rateplain'] = number_format($baserate, 2, '.', '');
$response['occupancies'] = json_encode($occupancies);
$response['searchsettings'] = $searchsettings;
$response['ean'] = 1;
$response['eanbookhref'] = $href;
//
// Store Session
//
$sql = new Sql($db);
$sql = "delete from quote_session_hotel_multipolicies where session_id='" . $session_id . "' and sindex=$sindex";
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('quote_session_hotel_multipolicies');
$insert->values(array(
    'session_id' => $session_id,
    'sindex' => $sindex,
    'data' => base64_encode(serialize($response)),
    'searchsettings' => base64_encode(serialize($searchsettings))
), $insert::VALUES_MERGE);
try {
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['breakdown'] = $roombreakdown;
// error_log("\r\nAbreu Policies Multi - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>