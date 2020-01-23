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
$snaptravel = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
error_log("\r\n COMECOU SNAPTRAVEL \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select name, country_id, zone_id,city_xml19, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml19 = $row_settings["city_xml19"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml19 = "";
}
$city_xml19 = "HKG";

$affiliate_id = 0;
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
    $sql = "select value from settings where name='snaptravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_snaptravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='snaptravelbranches_id' and affiliate_id=$affiliate_id_snaptravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelbranches_id = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelRevisionVersion' and affiliate_id=$affiliate_id_snaptravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelRevisionVersion = $row_settings['value'];
}
$sql = "select value from settings where name='snaptraveldaleschannel' and affiliate_id=$affiliate_id_snaptravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptraveldaleschannel = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelsalesenvironment' and affiliate_id=$affiliate_id_snaptravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelsalesenvironment = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelSearchSortorder' and affiliate_id=$affiliate_id_snaptravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelSearchSortorder = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelSharedSecret' and affiliate_id=$affiliate_id_snaptravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelSharedSecret = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelServiceURL' and affiliate_id=$affiliate_id_snaptravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelServiceURL = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelTimeout' and affiliate_id=$affiliate_id_snaptravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $snaptravelTimeout = $row_settings['value'];
}
$sql = "select value from settings where name='snaptravelAPIKey' and affiliate_id=$affiliate_id_snaptravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $snaptravelAPIKey = $row['value'];
}
$sql = "select value from settings where name='snaptravelMarkup' and affiliate_id=$affiliate_id_snaptravel";
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
$sql = "select value from settings where name='snaptravelb2cMarkup' and affiliate_id=$affiliate_id_snaptravel";
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

// error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
if ($snaptravelServiceURL != "") {
    $local = 'en_US';
    $hotelList = "108540,112915,118583,118903,119566,122212";
    $raw2 = '{
        "arrivalDate": "' . strftime("%m/%d/%Y", $from) . '",
        "departureDate": "' . strftime("%m/%d/%Y", $to) . '",';
    for ($r = 0; $r < $rooms; $r ++) {
        $numbers = $selectedAdults[$r];
        if (count($selectedChildren[$r]) > 0) {
            for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                $numbers = $numbers . ',' . $selectedChildrenAges[$r][$z];
            }
        }
        $raw2 = $raw2 . '"room' . ($r + 1) . '": "' . $numbers . '",';
    }
    $raw2 = $raw2 . '"hotelIdList": [' . $hotelList . '],
        "locale": "' . $local . '",
        "currencyCode": "' . strtoupper($currency) . '",
        "timeout": ' . $snaptravelTimeout . '
      }';
    error_log("\r\n RAW2: $raw2 \r\n", 3, "/srv/www/htdocs/error_log");
    $headers2 = array(
        "x-api-key: $snaptravelAPIKey",
        "Content-Type: application/json",
        "version: $snaptravelRevisionVersion",
        "Content-Length: " . strlen($raw2)
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $snaptravelServiceURL . 'b2b');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
    $response2 = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    
    error_log("\r\n Response: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
    
    $response2 = json_decode($response2, true);
    
    $HotelListResponse = $response2['HotelListResponse'];
    $customerSessionId = $HotelListResponse['customerSessionId'];
    
    $hotelId = 119566;
    $numbers = '';
    $raw = '{
        "hotelId": ' . $hotelId . ',
        "sessionId": "' . $customerSessionId . '",
        "arrivalDate": "' . strftime("%m/%d/%Y", $from) . '",
        "departureDate": "' . strftime("%m/%d/%Y", $to) . '",';
    for ($r = 0; $r < $rooms; $r ++) {
        $numbers = $selectedAdults[$r];
        if (count($selectedChildren[$r]) > 0) {
            for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                $numbers = $numbers . ',' . $selectedChildrenAges[$r][$z];
            }
        }
        $raw = $raw . '"room' . ($r + 1) . '": "' . $numbers . '",';
    }
    
    $raw = $raw . '"locale": "' . $local . '",
        "currencyCode": "' . strtoupper($currency) . '",
        "timeout": ' . $snaptravelTimeout . '
    }';
    error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");
    
    $headers = array(
        "x-api-key: $snaptravelAPIKey",
        "Content-Type: application/json",
        "version: $snaptravelRevisionVersion",
        "Content-Length: " . strlen($raw)
    );
    
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $snaptravelServiceURL . 'avail');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    
    // error_log("\r\n Response2: $response \r\n", 3, "/srv/www/htdocs/error_log");
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_snaptravel');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $snaptravelServiceURL,
            'sqlcontext' => $response,
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
    
    $response = json_decode($response, true);
    
    error_log("\r\n " . print_r($response, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
    
    $HotelRoomAvailabilityResponse = $response['HotelRoomAvailabilityResponse'];
    
    // error_log("\r\n SIZE = " . $HotelRoomAvailabilityResponse['@size'] . " \r\n", 3, "/srv/www/htdocs/error_log");
    
    if (count($HotelRoomAvailabilityResponse) > 0) {
        $hotelId = $HotelRoomAvailabilityResponse['hotelId'];
        $shid = $hotelId;
        $sfilter[] = " sid='$hotelId' ";
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
                            $currencyCode = $ChargeableRateInfo['currencyCode'];
                            $total = $ChargeableRateInfo['total'];
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
                
                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                    if (is_array($tmp[$shid])) {
                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                    } else {
                        $baseCounterDetails = 0;
                    }
                    
                    // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $name;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $hotelId;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $id;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $hotelId;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['sessionid'] = $customerSessionId;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-70";
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $description;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $description;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $roomTypeCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rate_code'] = $rateCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateKey'] = $rateKey;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $total;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($roomTypeDescription);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $amount = $total / $noOfNights;
                        if ($snaptravelMarkup != 0) {
                            $amount = $amount + (($amount * $snaptravelMarkup) / 100);
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
                        if ($snaptravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                    
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                    
                    /*
                     * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $CancelCost;
                     * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $DeadLineCancel;
                     */
                    $count = $count + 1;
                }
            }
        }
    }
    $snaptravel = true;
}

// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($snaptravel == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_msnaptravel where " . $sfilter;
        // error_log("\r\n SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 70;
        // error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_snaptravel');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_snaptravel');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $response,
                'data' => base64_encode(serialize($hotels_array)),
                'searchsettings' => base64_encode(serialize($requestdata))
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
}
?>