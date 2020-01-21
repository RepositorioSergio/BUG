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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_roomer where session_id='$session_id'";
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
    error_log("\r\n nationality  $nationality \r\n", 3, "/srv/www/htdocs/error_log");
    $residency = $searchsettings['residency'];
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
error_log("\r\n COMECA ENABLE \r\n", 3, "/srv/www/htdocs/error_log");
$affiliate_id = 0;
$sql = "select value from settings where name='enableroomer' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_roomer = $affiliate_id;
} else {
    $affiliate_id_roomer = 0;
}
/* $sql = "select value from settings where name='rtsID' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsID = $row_settings['value'];
}
$sql = "select value from settings where name='rtsPassword' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='rtsSiteCode' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsSiteCode = $row_settings['value'];
}
$sql = "select value from settings where name='rtsRequestType' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsRequestType = $row_settings['value'];
}
$sql = "select value from settings where name='rtsServiceURL' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsServiceURL = $row['value'];
}
error_log("\r\n rtsServiceURL  $rtsServiceURL  \r\n", 3, "/srv/www/htdocs/error_log"); */

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
            $room_code = $value['room_code'];
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
        error_log("\r\n ANTES CURL \r\n", 3, "/srv/www/htdocs/error_log");
        
        $url = 'http://b2b-sandbox.roomerapi.com/api/hotel_availability?check_in=2019-11-15&check_out=2019-11-17&hotel_id=1142&adults=2&children=3&children_ages=2,4,7&platform=API&pos=us';

        $headers = array(
            'Authorization: Token token=bfff17d3d81077f15d75abfcf115ed73',
            'Partner: paulo@club1hotels.com',
            'API-Version: 2.0'
        );
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        curl_close($ch);
        // Descomentar para ver o resultado do provider
        // Nao esquecer de alterar o session id para testar por causa de cache
        // echo $response;
        // die();
        $endTime = microtime();
        error_log("\r\nResponse ROOMER: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        $endTime = microtime();

        $response2 = json_decode($response2, true);

        $important = "";
        $taxes = "";
        $parag = "";

        $rooms = $response2['rooms'];
        if (count($rooms) > 0) {
            for ($jAux2=0; $jAux2 < count($rooms); $jAux2++) {
                $rate_code = $rooms[$jAux2]['rate_code']; 
                $room_code = $rooms[$jAux2]['room_code'];
                $description = $rooms[$jAux2]['description'];
                $bed_type = $rooms[$jAux2]['bed_type'];
                $board_name = $rooms[$jAux2]['board_name'];
                $room_type_name = $rooms[$jAux2]['room_type_name'];
                $breakfast = $rooms[$jAux2]['breakfast'];
                $check_in_instruction = $rooms[$jAux2]['check_in_instruction'];
                $occupancy_limit = $rooms[$jAux2]['occupancy_limit'];
                $zone_name = $rooms[$jAux2]['zone_name'];

                $prices = $rooms[$jAux2]['prices'];
                $is_best_value = $prices['is_best_value'];
                $recommended_price = $prices['recommended_price'];
                $benchmark_price = $prices['benchmark_price'];

                $b2c_rate = $prices['b2c_rate'];
                $price = $b2c_rate['price'];
                $tax = $b2c_rate['tax'];

                $b2b_rate = $prices['b2b_rate'];
                $pricebb = $b2b_rate['price'];
                $taxbb = $b2b_rate['tax'];

                $mobile_rate = $prices['mobile_rate'];
                $pricemr = $mobile_rate['price'];
                $taxmr = $mobile_rate['tax'];

                $fenced_rate = $prices['fenced_rate'];
                $pricefr = $fenced_rate['price'];
                $taxfr = $fenced_rate['tax'];

                $cancellation_policy = $rooms[$jAux2]['cancellation_policy'];
                $type = $cancellation_policy['type'];
                $details = $cancellation_policy['details'];
                if (count($details) > 0) {
                    for ($j=0; $j < count($details); $j++) { 
                        $upto_date = $details[$j]['upto_date'];
                        $refund_amount = $details[$j]['refund_amount'];
                        $text = $details[$j]['text'];
                    }
                }

                $fees = $rooms[$jAux2]['fees'];
                if (count($fees) > 0) {
                    $at_property = $transactions['at_property'];
                    if (count($at_property) > 0) {
                        for ($k=0; $k < count($at_property); $k++) { 
                            $name = $at_property[$k]['name'];
                            $amount = $at_property[$k]['amount'];
                        }
                    }
                    $included = $transactions['included'];
                    if (count($included) > 0) {
                        for ($k=0; $k < count($included); $k++) { 
                            $name = $included[$k]['name'];
                            $amount = $included[$k]['amount'];
                        }
                    }
                }

                $bedding_data = $rooms[$jAux2]['bedding_data'];
                if (count($bedding_data) > 0) {
                    for ($i=0; $i < count($bedding_data); $i++) { 
                        $bed_count = $bedding_data[$i]['bed_count'];
                        $bed_type = $bedding_data[$i]['bed_type'];
                    }
                }

                $important_information = $rooms[$jAux2]['important_information'];
                if (count($important_information) > 0) {
                    for ($k=0; $k < count($important_information); $k++) { 
                        $important = $important_information[$k];
                    }
                }

                $taxes_and_fees = $rooms[$jAux2]['taxes_and_fees'];
                if (count($taxes_and_fees) > 0) {
                    for ($k=0; $k < count($taxes_and_fees); $k++) { 
                        $taxes = $taxes_and_fees[$k];
                    }
                }

                $policy_data = $rooms[$jAux2]['policy_data'];
                if (count($policy_data) > 0) {
                    for ($k=0; $k < count($policy_data); $k++) { 
                        $title = $policy_data[$k]['title'];
                        $paragraph_data = $policy_data[$k]['paragraph_data'];
                        if (count($paragraph_data) > 0) {
                            for ($kAux=0; $kAux < count($paragraph_data); $kAux++) { 
                                $parag = $paragraph_data[$kAux];
                            }
                        }
                    }
                }

                $allowed_cards_data = $rooms[$jAux2]['allowed_cards_data'];
                if (count($allowed_cards_data) > 0) {
                    for ($k=0; $k < count($allowed_cards_data); $k++) { 
                        $card_type = $allowed_cards_data[$k]['card_type'];
                        $name = $allowed_cards_data[$k]['name'];
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
        $item['nett'] = $value['nett'];
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
        
        if ($text != "") {
            $newDate = date("d-m-Y", strtotime($upto_date));
            $cancelation_details = $text;
            $cancelation_deadline = $newDate;
            error_log("\r\n ENTROU \r\n", 3, "/srv/www/htdocs/error_log");
            $item['cancelpolicy'] = $cancelation_details;
            $item['cancelpolicy_deadline'] = $cancelation_deadline;
            /* $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
            $item['cancelpolicy_details'] = $cancelation_details; */
        }
         
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mroomer where sid='" . $shid . "' and hid=" . $hid;
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