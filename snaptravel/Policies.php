<?php
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
$total = 0;
error_log("\r\n COMECOU POLICIES \r\n", 3, "/srv/www/htdocs/error_log");
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_snaptravel where session_id='$session_id'";
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
            $room_code = $value['roomid'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $item = array();
        $cancelation_deadline = 0;
        $cancelation_details = "";
        $text = "";
        $local = 'en_US';
        $sessionid = $value['sessionid'];
        $rateKey = $value['rateKey'];
        
        $from_date = date("m/d/Y", strtotime($from));
        $to_date = date("m/d/Y", strtotime($to));
        
        $numbers = '';
        $raw = '{
            "hotelId": ' . $hotel_code . ',
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
        // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
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
        
        $response2 = json_decode($response2, true);
        
        // error_log("\r\n " . print_r($response2, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        
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
        
        //
        // Policies
        //
        $item['code'] = $value['shid'];
        $item['total'] = $totalC;
        $item['nett'] = $value['nett'];
        $total = $total + $totalC;
        $tot = $totalC;
        $item['room'] = $value['room'];
        $item['RoomTypeCode'] = $value['room_type'];
        $item['RoomType'] = $value['room_type'];
        $item['RoomDescription'] = $value['room_description'];
        $item['meal'] = $roomTypeDescription;
        $item['total'] = $totalC;
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        
        if ($text != "") {
            $newDate = date("d-m-Y", strtotime($upto_date));
            $cancelation_details = $text;
            $cancelation_deadline = $newDate;
            error_log("\r\n ENTROU \r\n", 3, "/srv/www/htdocs/error_log");
            $item['cancelpolicy'] = $cancelation_details;
            $item['cancelpolicy_deadline'] = $cancelation_deadline;
            /*
             * $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
             * $item['cancelpolicy_details'] = $cancelation_details;
             */
        }
        
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_msnaptravel where sid='" . $shid . "' and hid=" . $hid;
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
$db = new \Zend\Db\Adapter\Adapter($config);
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
    $db2 = new \Zend\Db\Adapter\Adapter($config);
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
    $db = new \Zend\Db\Adapter\Adapter($config);
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