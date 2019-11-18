jjjhm<?php
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
error_log("\r\nSymRooms - Policies\r\n", 3, "/srv/www/htdocs/error_log");
$dbSymrooms = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_symrooms where session_id='$session_id'";
    $statement = $dbSymrooms->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (Exception $e) {
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
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$sql = "select value from settings where name='enableSymrooms' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $dbSymrooms->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_roomer = $affiliate_id;
} else {
    $affiliate_id_roomer = 0;
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

/*
 * $fromHotelsPRO = $fromHotelsPRO->getTimestamp();
 * $toHotelsPro = $toHotelsPro->getTimestamp();
 */
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
            $optionRefId = $value['optionRefId'];
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
        error_log("\r\nSymRooms Curl\r\n", 3, "/srv/www/htdocs/error_log");
        $url = "https://api.travelgatex.com/";
        $raw = '{"query":"{\n  hotelX {\n    search( criteria: {\n                checkIn: \"2019-12-23\",\n                checkOut: \"2019-12-24\",\n                hotels: [\"1\"],\n                occupancies: [ {paxes: [{age: 30}, {age: 30}]}]},\n                settings: {\n                      client: \"Demo_Client\",\n                      testMode: true,\n                      context: \"HOTELTEST\"}) {\n      options {\n        id\n        supplierCode\n        hotelCode\n        hotelName\n        boardCode\n    paymentType\n    status\n    rooms {\n    occupancyRefId\n     code\n   description\n    refundable\n    units\n    roomPrice {\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n    }\n    }\n    }\n  beds {\n    type\n    description\n    count\n    shared\n    }\n    ratePlans {\n    code\n    name\n    effectiveDate\n  expireDate\n   }\n    promotions {\n    code\n    name\n    effectiveDate\n    expireDate\n  }\n  }\n  supplements {\n   code\n    name\n    description\n    supplementType\n    chargeType\n    mandatory\n    durationType\n    quantity\n    unit\n    effectiveDate\n    expireDate\n    resort {\n    code\n    name\n    description\n    }\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n  }\n  }\n  }\n   surcharges {\n    chargeType\n    description\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n  }\n  }\n  }\n    rateRules \n    cancelPolicy {\n    refundable\n    cancelPenalties {\n    hoursBefore\n    penaltyType\n    currency\n   value\n  }\n  }\n      price {\n          net\n          currency\n        }\n      }\n      errors {\n        code\n        type\n        description\n      }\n      warnings {\n        code\n        type\n        description\n      }\n    }\n  }\n}"}';
        $headers = array(
            'Authorization: Apikey 64780338-49c8-4439-7c7d-d03c2033b145',
            'Accept-Encoding: gzip, deflate, br',
            'Content-Type: application/json',
            'Accept: application/json',
            'Connection: keep-alive',
            'DNT: 1',
            'Origin: https://api.travelgatex.com'
        );
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        $endTime = microtime();
        error_log("\r\nResponse: $response2\r\n", 3, "/srv/www/htdocs/error_log");
        $response2 = json_decode($response2, true);
        
        $raterule = "";
        $cancel = array();
        $count = 0;
        $count2 = 0;
        
        $data = $response2['data'];
        $hotelX = $data['hotelX'];
        $search = $hotelX['search'];
        
        // options
        $options = $search['options'];
        for ($i = 0; $i < count($options); $i ++) {
            $id = $options[$i]['id'];
            $supplierCode = $options[$i]['supplierCode'];
            $hotelCode = $options[$i]['hotelCode'];
            if ($id == $optionRefId and $hotelCode == $hotel_code) {
                $hotelName = $options[$i]['hotelCode'];
                $boardCode = $options[$i]['boardCode'];
                $paymentType = $options[$i]['paymentType'];
                $status = $options[$i]['status'];
                $token = $options[$i]['token'];
                
                // supplements
                $supplements = $options[$i]['supplements'];
                // surcharges
                $surcharges = $options[$i]['surcharges'];
                if (count($surcharges) > 0) {
                    for ($j = 0; $j < count($surcharges); $j ++) {
                        $chargeType = $surcharges[$j]['chargeType'];
                        $scdescription = $surcharges[$j]['description'];
                        $price = $roomPrice['price'];
                        $sccurrency = $price['currency'];
                        $scbinding = $price['binding'];
                        $scnet = $price['net'];
                        $scgross = $price['gross'];
                        $exchange = $price['exchange'];
                        $sccurrency = $exchange['currency'];
                        $scrate = $exchange['rate'];
                    }
                }
                // rateRules
                $rateRules = $options[$i]['rateRules'];
                if (count($rateRules) > 0) {
                    for ($j = 0; $j < count($rateRules); $j ++) {
                        $raterule = $rateRules[$j];
                    }
                }
                
                $price = $options[$i]['price'];
                $net = $price['net'];
                $currency = $price['currency'];
                
                // cancelPolicy
                $cancelPolicy = $options[$i]['cancelPolicy'];
                $CPrefundable = $cancelPolicy['refundable'];
                // cancelPenalties
                $cancelPenalties = $cancelPolicy['cancelPenalties'];
                $count2 = count($cancelPenalties);
                if (count($cancelPenalties) > 0) {
                    for ($c = 0; $c < count($cancelPenalties); $c ++) {
                        $cancel[$count]['hoursBefore'] = $cancelPenalties[$c]['hoursBefore'];
                        $cancel[$count]['penaltyType'] = $cancelPenalties[$c]['penaltyType'];
                        $cancel[$count]['currency'] = $cancelPenalties[$c]['currency'];
                        $cancel[$count]['value'] = $cancelPenalties[$c]['value'];
                        $count = $count + 1;
                    }
                }
                
                // rooms
                $rooms = $options[$i]['rooms'];
                for ($r = 0; $r < count($rooms); $r ++) {
                    $occupancyRefId = $rooms[$r]['occupancyRefId'];
                    $room_code = $rooms[$r]['code'];
                    $description = $rooms[$r]['description'];
                    $refundable = $rooms[$r]['refundable'];
                    $units = $rooms[$r]['units'];
                    
                    $promotions = $rooms[$r]['promotions'];
                    if (count($promotions) > 0) {
                        for ($l = 0; $l < count($promotions); $l ++) {
                            $promotionscode = $promotions[$l]['code'];
                            $promotionsname = $promotions[$l]['name'];
                            $promotionseffectiveDate = $promotions[$l]['effectiveDate'];
                            $promotionscodeexpireDate = $promotions[$l]['expireDate'];
                        }
                    }
                    
                    // roomPrice
                    $roomPrice = $rooms[$r]['roomPrice'];
                    $price = $roomPrice['price'];
                    $currency = $price['currency'];
                    $binding = $price['binding'];
                    $net = $price['net'];
                    $gross = $price['gross'];
                    $exchange = $price['exchange'];
                    $currency = $exchange['currency'];
                    $rate = $exchange['rate'];
                    
                    // beds
                    $beds = $rooms[$r]['beds'];
                    for ($k = 0; $k < count($beds); $k ++) {
                        $type = $beds[$k]['type'];
                        $descriptionbeds = $beds[$k]['description'];
                        $count = $beds[$k]['count'];
                        $shared = $beds[$k]['shared'];
                    }
                    
                    $ratePlans = $rooms[$r]['ratePlans'];
                    for ($y = 0; $y < count($ratePlans); $y ++) {
                        $ratePlanscode = $ratePlans[$y]['code'];
                        $name = $ratePlans[$y]['name'];
                        $effectiveDate = $ratePlans[$y]['effectiveDate'];
                        $expireDate = $ratePlans[$y]['expireDate'];
                    }
                }
            }
        }
        
        //
        // Policies
        //
        $item['code'] = $hotelCode;
        $item['name'] = $hotelName;
        $item['total'] = $gross;
        $item['nett'] = $net;
        $total = $total + $gross;
        $tot = $gross;
        error_log("\r\nTotal: $tot \r\n", 3, "/srv/www/htdocs/error_log");
        $item['room'] = $description;
        $item['RoomTypeCode'] = $room_code;
        $item['RoomType'] = $type;
        $item['RoomDescription'] = $description;
        $item['meal'] = $type;
        $item['total'] = $gross;
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        error_log("\r\nAvg: $avg \r\n", 3, "/srv/www/htdocs/error_log");
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        //
        // $newDate = date("d-m-Y", strtotime($upto_date));
        //
        if ($count2 > 0) {
            $cancelation_details = "The Cancellation " . $cancel[0]['penaltyType'] . " cost " . $cancel[0]['value'] . " " . $cancel[0]['currency'] . ".<br/>If Cancel " . $cancel[1]['hoursBefore'] . " hours before, The Cancellation " . $cancel[1]['penaltyType'] . " cost " . $cancel[1]['value'] . " " . $cancel[1]['currency'] . ".";
            $cancelation_deadline = $cancel[0]['hoursBefore'] . " hours before";
            error_log("\r\n ENTROU \r\n", 3, "/srv/www/htdocs/error_log");
            $item['cancelpolicy'] = $cancelation_details;
            $item['cancelpolicy_deadline'] = $cancelation_deadline;
        } else {
            $cancelation_details = "The Cancellation is no Refundable.";
            $cancelation_deadline = "0 hours before";
            error_log("\r\n ENTROU \r\n", 3, "/srv/www/htdocs/error_log");
            $item['cancelpolicy'] = $cancelation_details;
            $item['cancelpolicy_deadline'] = $cancelation_deadline;
        }
        
        /*
         * $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
         * $item['cancelpolicy_details'] = $cancelation_details;
         */
        
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_msymrooms where sid='" . $shid . "' and hid=" . $hid;
$statement = $dbSymrooms->createStatement($sql);
try {
    $statement->prepare();
} catch (Exception $e) {
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
$statement = $dbSymrooms->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (Exception $e) {
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
    $statement2 = $dbSymrooms->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (Exception $e) {
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
    $statement = $dbSymrooms->createStatement($sql);
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
$dbSymrooms->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\nEOF Policies SymRooms\r\n", 3, "/srv/www/htdocs/error_log");
?>