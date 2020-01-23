<?php
error_log("\r\nMulti Policies SNAPTRAVEL  \r\n", 3, "/srv/www/htdocs/error_log");
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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_snaptravel where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_snaptravel where session_id='$session_id'";
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
$sql = "select value from settings where name='enablesnaptravel' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_snaptravel = $affiliate_id;
} else {
    $affiliate_id_snaptravel = 0;
}
$sql = "select value from settings where name='snaptravelbranches_id' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelbranches_id = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelRevisionVersion' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelRevisionVersion = $row_settings['value'];
}
$sql = "select value from settings where name='snaptraveldaleschannel' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptraveldaleschannel = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelsalesenvironment' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelsalesenvironment = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelSearchSortorder' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelSearchSortorder = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelSharedSecret' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelSharedSecret = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelServiceURL' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelServiceURL = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelTimeout' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelTimeout = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelAPIKey' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $snaptravelAPIKey = $row['value'];
}
$sql = "select value from settings where name='snaptravelMarkup' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelMarkup = (double) $row_settings['value'];
} else {
    $snaptravelMarkup = 0;
}
$sql = "select value from settings where name='snaptravelb2cMarkup' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelb2cMarkup = (double) $row_settings['value'];
} else {
    $snaptravelb2cMarkup = 0;
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

        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $start = $value['start'];
        $end = $value['end'];
        $cnights = $value['nights'];
        $ccurrency = $value['currency'];
        if ($start != "" and $end != "") {
            if ($cnights != "") {
                if ($cnights == 1) {
                    $Description = $translator->translate("Cancel between") . " " . $start . " " . $translator->translate("and") . " " . $end . " " . $cnights . " " . $translator->translate("night charge");
                } else {
                    $Description = $translator->translate("Cancel between") . " " . $start . " " . $translator->translate("and") . " " . $end . " " . $cnights . " " . $translator->translate("nights charge");
                }
            }
            $cancelpolicy_deadline = strtotime($start);
            $cancelpolicy = $Description;
        }
        $from_date = date('m/d/Y', strtotime($from));
        $to_date = date('m/d/Y', strtotime($to));
        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";
        
        $sessionid =  $value['sessionid'];
        $rateKey =  $value['rateKey'];
        $pricetotal =  $value['total'];
        $mealid = $value['mealid'];
        $childrenages = $value['childrenages'];
        $numberrooms = 1;
        $local = 'en_US';
        $numbers = '';

        $raw = '{
            "hotelId": ' . $HotelId . ',
            "sessionId": "' . $sessionid . '",
            "arrivalDate": "' . $from_date . '",
            "departureDate": "' . $to_date . '",';
            for ($r = 0; $r < $rooms; $r ++) {
                $numbers = $selectedAdults[$r];
                if (count($selectedChildren[$r]) > 0) {
                    for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                        $numbers = $numbers . ',' . $selectedChildrenAges[$r][$z];
                    }
                }
                $raw = $raw . '"room' . ($r + 1) . '": "' . $numbers . '",';
            }
        $raw = $raw . '"rateKey": "' . $rateKey . '",
            "locale": "' . $local . '",
            "currencyCode": "' . strtoupper($currency) . '",
            "timeout": ' . $snaptravelTimeout . '
        }';
        error_log("\r\n RAW $raw \r\n", 3, "/srv/www/htdocs/error_log");
        
        $headers = array(
            "x-api-key: $snaptravelAPIKey",
            "Content-Type: application/json",
            "version: $snaptravelRevisionVersion",
            "Content-Length: " . strlen($raw)
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $snaptravelServiceURL . 'avail');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        error_log("\r\n Response: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_snaptravel');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $snaptravelServiceURL,
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
        // error_log("\r\n" . print_r($response2, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        
        // $status = $response2['status'];
        // if ($status == "changed") {
        // // Price Changed
        // $pricechanged = true;
        // } elseif ($status == "sold_out") {
        // // Sold Out
        // $pricesold_out = true;
        // }
        $response2 = json_decode($response2, true);
        $pricechanged = true;
        $HotelRoomAvailabilityResponse = $response2['HotelRoomAvailabilityResponse'];
        if (count($HotelRoomAvailabilityResponse) > 0) {
            $hotelId = $HotelRoomAvailabilityResponse['hotelId'];
            $size = $HotelRoomAvailabilityResponse['@size'];
            $customerSessionId = $HotelRoomAvailabilityResponse['customerSessionId'];
            $HotelRoomResponse = $HotelRoomAvailabilityResponse['HotelRoomResponse'];
            if (count($HotelRoomResponse) > 0) {
                for ($j = 0; $j < count($HotelRoomResponse); $j ++) {
                    $RoomImages = $HotelRoomResponse[$j]['RoomImages'];
                    $ValueAdds = $HotelRoomResponse[$j]['ValueAdds'];
                    $rateCode = $HotelRoomResponse[$j]['rateCode'];
                    $rateDescription = $HotelRoomResponse['rateDescription'];
                    $rateOccupancyPerRoom = $HotelRoomResponse[$j]['rateOccupancyPerRoom'];
                    $roomTypeCode = $HotelRoomResponse[$j]['roomTypeCode'];
                    $roomTypeDescription = $HotelRoomResponse[$j]['roomTypeDescription'];
                    // BedTypes
                    $BedTypes = $HotelRoomResponse[$j]['BedTypes'];
                    if (count($BedTypes) > 0) {
                        $size2 = $BedTypes['@size'];
                        $BedType = $BedTypes['BedType'];
                        if (count($BedType) > 0) {
                            for ($i = 0; $i < count($BedType); $i ++) {
                                $id = $BedType[$i]['@id'];
                                $description = $BedType[$i]['description'];
                            }
                        }
                    }
                    // RateInfos
                    $RateInfos = $HotelRoomResponse[$j]['RateInfos'];
                    if (count($RateInfos) > 0) {
                        $RateInfo = $RateInfos['RateInfo'];
                        if (count($RateInfo) > 0) {
                            $nonRefundable = $RateInfo['nonRefundable'];
                            $ChargeableRateInfo = $RateInfo['ChargeableRateInfo'];
                            if (count($ChargeableRateInfo) > 0) {
                                $currencyCode2 = $ChargeableRateInfo['@currencyCode'];
                                $totalC2 = $ChargeableRateInfo['@total'];
                                $currencyCode = $ChargeableRateInfo['currencyCode'];
                                $totalC = $ChargeableRateInfo['total'];
                            }
                            $RoomGroup = $RateInfo['RoomGroup'];
                            if (count($RoomGroup) > 0) {
                                $Room = $RoomGroup['Room'];
                                if (count($Room) > 0) {
                                    $rateKey = $Room['rateKey'];
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($pricechanged == true) {
            $oldtotal = $value['total'];
            $oldnettotal = $value['nettotal'];
            $value['nettotal'] = $totalC;
            if ($snaptravelMarkup != 0) {
                $valueRInclusive = $valueRInclusive + (($valueRInclusive * $snaptravelMarkup) / 100);
            }
            // Geo target markup
            if ($internalmarkup != 0) {
                $valueRInclusive = $valueRInclusive + (($valueRInclusive * $internalmarkup) / 100);
            }
            // Agent markup
            if ($agent_markup != 0) {
                $valueRInclusive = $valueRInclusive + (($valueRInclusive * $agent_markup) / 100);
            }
            // Fallback Markup
            if ($snaptravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                $valueRInclusive = $valueRInclusive + (($valueRInclusive * $HotelsMarkupFallback) / 100);
            }
            // Agent discount
            if ($agent_discount != 0) {
                $valueRInclusive = $valueRInclusive - (($valueRInclusive * $agent_discount) / 100);
            }
            if ($scurrency != "" and $currency != $scurrency and $request_currency != "") {
                $valueRInclusive = $CurrencyConverter->convert($valueRInclusive, $currencyRInclusive, $scurrency);
            }
            $value['total'] = $totalC;
            error_log("\r\n Total: $totalC \r\n", 3, "/srv/www/htdocs/error_log");
            error_log("\r\nNew Net Total: " . $oldnettotal . " -> " . $value['nettotal'] . "\r\n", 3, "/srv/www/htdocs/error_log");
            error_log("\r\nNew Total: " . $oldtotal . " -> " . $value['total'] . "\r\n", 3, "/srv/www/htdocs/error_log");
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
        }
        //
        // EOF Policies
        //
        // EOF Check prices & availability
        //
        // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        //
        // $cancelpolicy = $CancellationPoliciesText;
        // if ($CancellationPoliciesArray[0]['deadline'] == "") {
        //     $cancelpolicy_deadline = time();
        // } else {
        //     $cancelpolicy_deadline = $CancellationPoliciesArray[0]['deadline'] . " hours";
        // }
        // error_log("\r\nA - Cancel Policy Deadline / Cancel Policy / Status = " . $cancelpolicy_deadline . " - " . $cancelpolicy . " - " . $status . " \r\n", 3, "/srv/www/htdocs/error_log");
        // if ($cancelpolicy_deadline == 0 and $status == "matched") {
        //     $cancelpolicy_deadline = $value['cancelpolicy_deadlinetimestamp'];
        // }
        // if ($cancelpolicy == "" and $status == "matched") {
        //     $cancelpolicy = $value['cancelpolicy'];
        // }
        // error_log("\r\nB - Cancel Policy Deadline = " . $cancelpolicy_deadline . "\r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\nCancel Policy = " . $cancelpolicy . "\r\n", 3, "/srv/www/htdocs/error_log");
        $total = $total + $totalC;
        $tot = $totalC;
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        $item['total'] = $totalC;
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $item['subtotal'] = $filter->filter(floatval($tot));
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        //$item['cancelpolicy'] = $cancelpolicy;
        //$item['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;
        //$item['cancelpolicy_deadline'] = $cancelpolicy_deadline;
        
        $item['nonrefundable'] = $value['nonrefundable'];
        if ($item['nonrefundable'] == true) {
            $item['cancelpolicy_deadline'] = 0;
            $item['cancelpolicy'] = $translator->translate("This booking is non-refundable and cannot be amended or modified. Failure to arrive at your hotel will be treated as a No-Show and no refund will be given.");
        }
        $item['cancelpolicy_details'] = $cancelpolicy;
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_msnaptravel where sid='" . $shid . "' and hid=" . $hid;
error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n PASSOU POR 3  \r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n PASSOU POR 4  \r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\nSunHotels Policies Multi - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>