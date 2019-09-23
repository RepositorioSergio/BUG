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
$roomer = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
error_log("\r\n COMECOU ROOMER \r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n TODO - RTS - city_xml19 : $city_xml19 \r\n", 3, "/srv/www/htdocs/error_log");
$affiliate_id = 0;
$sql = "select value from settings where name='enablerts' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_rts = $affiliate_id;
} else {
    $affiliate_id_rts = 0;
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
    $sql = "select value from settings where name='rtsDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='rtsID' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsID = $row_settings['value'];
}
$sql = "select value from settings where name='rtsPassword' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='rtsSiteCode' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsSiteCode = $row_settings['value'];
}
$sql = "select value from settings where name='rtsRequestType' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsRequestType = $row_settings['value'];
}
$sql = "select value from settings where name='rtsServiceURL' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsServiceURL = $row['value'];
}
$sql = "select value from settings where name='rtsMarkup' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsMarkup = (double) $row_settings['value'];
} else {
    $rtsMarkup = 0;
}

$url = 'http://b2b-sandbox.roomerapi.com/api/search_by_hotels?check_in=2019-11-15&check_out=2019-11-17&hotel_list=25541,85441,1142&pos=es&adults=2&children=3&children_ages=2,4,7&number_of_results=30&platform=API';

// error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
if ($url != "") {
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
    $response = curl_exec($ch);
    curl_close($ch);
    // Descomentar para ver o resultado do provider
    // Nao esquecer de alterar o session id para testar por causa de cache
    // echo $response;
    // die();
    $endTime = microtime();
    error_log("\r\nResponse ROOMER: $response \r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_roomer');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $url,
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

    $age = "";
    $amenity = "";
    $success = $response['success'];
    $data = $response['data'];
    if (count($data) > 0) {
        $check_in = $data['check_in'];
        $check_out = $data['check_out'];
        $found_hotels_count = $data['found_hotels_count'];
        $currency = $data['currency'];
        $adults = $data['adults'];
        $children = $data['children'];
        $affiliate_id = $data['affiliate_id'];

        $children_ages = $data['children_ages'];
        if (count($children_ages) > 0) {
            for ($i=0; $i < count($children_ages); $i++) { 
                $age = $children_ages[$i];
            }
        }

        $hotel_list = $data['hotel_list'];
        if (count($hotel_list) > 0) {
            for ($j=0; $j < count($hotel_list); $j++) { 
                $id = $hotel_list[$j]['id'];
                $shid = $id;
                $sfilter[] = " sid='$id' ";
                $partner_hotel_id = $hotel_list[$j]['partner_hotel_id'];
                $name = $hotel_list[$j]['name'];
                $address = $hotel_list[$j]['address'];
                $city = $hotel_list[$j]['city'];
                $state = $hotel_list[$j]['state'];
                $country = $hotel_list[$j]['country'];
                $latitude = $hotel_list[$j]['latitude'];
                $longitude = $hotel_list[$j]['longitude'];
                $stars_rating = $hotel_list[$j]['stars_rating'];
                $zone_name = $hotel_list[$j]['zone_name'];
                $bed_choice_available = $hotel_list[$j]['bed_choice_available'];

                $amenities = $hotel_list[$j]['amenities'];
                $parking = $amenities['parking'];
                $wifi = $amenities['wifi'];


                $amenity_data = $hotel_list[$j]['amenity_data'];
                if (count($amenity_data) > 0) {
                    for ($jAux=0; $jAux < count($amenity_data); $jAux++) { 
                        $amenity = $amenity_data[$jAux];
                    }
                }

                $rooms = $hotel_list[$j]['rooms'];
                if (count($rooms) > 0) {
                    for ($jAux2=0; $jAux2 < count($rooms); $jAux2++) { 
                        $room_code = $rooms[$jAux2]['room_code'];
                        $description = $rooms[$jAux2]['description'];
                        $room_type_name = $rooms[$jAux2]['room_type_name'];
                        $breakfast = $rooms[$jAux2]['breakfast'];
                        $board_name = $rooms[$jAux2]['board_name'];
                        $rate_code = $rooms[$jAux2]['rate_code'];

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
                        $details = $details['details'];
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

                        for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                            if (is_array($tmp[$shid])) {
                                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                            } else {
                                $baseCounterDetails = 0;
                            }
                            
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $name;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $id;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $room_code;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $id;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $shid;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                            // cancellationType nao existe
                            // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $c_type;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-62";
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $room_type_name;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $room_type_name;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $room_type_name;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rate_code'] = $rate_code;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $pricebb;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $pricebb;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($BreakfastTypeName);
                            $pricebreakdown = array();
                            $pricebreakdownCount = 0;
                            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                $amount = $pricebb / $noOfNights;
                                if ($rtsMarkup != 0) {
                                    $amount = $amount + (($amount * $rtsMarkup) / 100);
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
                                if ($rtsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $ClientCurrencyCode;
                            
                            if ($PromotionName != "") {
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $PromotionName;
                            } else {
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                            }
                            
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['FareRateType'] = $FareRateType;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['DailyCostCancel'] = $DailyCostCancel;
                            /*
                             * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $CancelCost;
                             * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $DeadLineCancel;
                             */
                            $count = $count + 1;
                        }
                    }
                }
            }
            $roomer = true;
        }
    }
}
error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($roomer == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mroomer where " . $sfilter;
        error_log("\r\n ROOMER SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
    error_log("\r\n PASSA AQUI \r\n", 3, "/srv/www/htdocs/error_log");
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 62;
        error_log("\r\nROOMER QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_roomer');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_roomer');
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