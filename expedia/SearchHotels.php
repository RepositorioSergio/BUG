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
//error_log("\r\n COMECOU EXPEDIA \r\n", 3, "/srv/www/htdocs/error_log");
$sfilter = array();
$expedia = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id,city_xml45, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml45 = $row_settings["city_xml45"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml45 = "";
}
$sql = "select value from settings where name='enableexpedia' and affiliate_id=$affiliate_id";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_expedia= $affiliate_id;
} else {
    $affiliate_id_expedia = 0;
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
    $sql = "select value from settings where name='expediaDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_expedia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='expediaAPIKey' and affiliate_id=$affiliate_id_expedia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaAPIKey = $row_settings['value'];
}
//error_log("\r\n expediaAPIKey  $expediaAPIKey \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='expediaMarkup' and affiliate_id=$affiliate_id_expedia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaMarkup = (double) $row_settings['value'];
} else {
    $expediaMarkup = 0;
}
$sql = "select value from settings where name='expediaSharedSecret' and affiliate_id=$affiliate_id_expedia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaSharedSecret = base64_decode($row_settings['value']);
}
//error_log("\r\n expediaSharedSecret  $expediaSharedSecret \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='expediaServiceURL' and affiliate_id=$affiliate_id_expedia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaServiceURL = $row_settings['value'];
}
//error_log("\r\n expediaServiceURL  $expediaServiceURL \r\n", 3, "/srv/www/htdocs/error_log");

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$date = new Datetime();
$timestamp = $date->format('U');

$timestamp2 = time();
$authorization = 'EAN APIKey=' . $expediaAPIKey . ',Signature=' . hash("sha512", $expediaAPIKey . $expediaSharedSecret . $timestamp2) . ',timestamp=' . time();

$ipaddress = '';
if ($_SERVER['HTTP_CLIENT_IP']) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
} else if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_X_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
} else if ($_SERVER['HTTP_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
} else if ($_SERVER['REMOTE_ADDR']) {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
} else {
    $ipaddress = 'UNKNOWN';
    $ipaddress = "142.44.216.144";
}


if ($expediaServiceURL != "" and $expediaSharedSecret != "" and $expediaAPIKey != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $expediaServiceURL . 'properties/availability?checkin=2019-09-15&checkout=2019-09-17&currency=USD&language=en-US&country_code=US&occupancy=2&sales_channel=website&sales_environment=hotel_package&sort_type=preferred&property_id=24051641');
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/json",
    "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
    "Authorization: " . $authorization,
    "Content-Type: application/json",
    "Accept-Encoding: gzip",
    "Customer-Ip: " . $ipaddress
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_expedia');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $expediaServiceURL,
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

    error_log("\r\n RESPONSE  $response\r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);

    $night = array();
    $occupancie = array();
    // Results
    for ($i = 0; $i < count($response); $i ++) {
        $property_id = $response[$i]['property_id'];
        $score = $response[$i]['score'];
        $shid = $property_id;
        $sfilter[] = " sid='$property_id' ";
        // links
        $links = $response[$i]['links'];
        $additional_rates = $links['additional_rates'];
        $method = $additional_rates['method'];
        $href = $additional_rates['href'];
    
        // rooms
        $rooms = $response[$i]['rooms'];
        for ($j=0; $j < count($rooms); $j++) { 
            $id = $rooms[$j]['id'];
            $room_name = $rooms[$j]['room_name'];
    
            $rates = $rooms[$j]['rates'];
            for ($k=0; $k < count($rates); $k++) { 
                $id_rates = $rates[$k]['id'];
                $available_rooms = $rates[$k]['available_rooms'];
                $refundable = $rates[$k]['refundable'];
                $fenced_deal = $rates[$k]['fenced_deal'];
                $fenced_deal_available = $rates[$k]['fenced_deal_available'];
                $deposit_required = $rates[$k]['deposit_required'];
                $merchant_of_record = $rates[$k]['merchant_of_record'];
                $promo_id = $rates[$k]['promo_id'];
                //links
                $links = $rates[$k]['links'];
                $payment_options = $links['payment_options'];
                $method = $payment_options['method'];
                $href_payment_options = $payment_options['href'];
    
                //amenities
                $amenities = $rates[$k]['amenities'];
                for ($kAux=0; $kAux < count($amenities); $kAux++) { 
                    $id_amenities = $amenities[$kAux]['id'];
                    $name = $amenities[$kAux]['name'];
                }
    
                //bed_groups
                $bed_groups = $rates[$k]['bed_groups'];
                for ($kAux2=0; $kAux2 < count($bed_groups); $kAux2++) { 
                    $links = $bed_groups[$kAux2]['links'];
                    $price_check = $links['price_check'];
                    $method = $price_check['method'];
                    $href_price_check = $price_check['href'];
    
                    $configuration = $bed_groups[$kAux2]['configuration'];
                    for ($kAux3=0; $kAux3 < count($configuration); $kAux3++) { 
                        $type = $configuration[$kAux3]['type'];
                        $size = $configuration[$kAux3]['size'];
                        $quantity = $configuration[$kAux3]['quantity'];
                    }
                }
    
                //cancel_penalties
                $cancel_penalties = $rates[$k]['cancel_penalties'];
                for ($z=0; $z < count($cancel_penalties); $z++) { 
                    $start = $cancel_penalties[$z]['start'];
                    $end = $cancel_penalties[$z]['end'];
                    $nights = $cancel_penalties[$z]['nights'];
                    $currencyCP = $cancel_penalties[$z]['currency'];
                }
                
                $occupancies = $rates[$k]['occupancies'];
                foreach ($occupancies as $key => $value) {
                    $occupancie = $occupancies[$key];
                    $nightly = $occupancie['nightly'];
                    for ($kA=0; $kA < count($nightly); $kA++) { 
                        $night = $nightly[$kA];
                        for ($kB=0; $kB < count($night); $kB++) { 
                            $type = $night[$kB]['type'];
                            $value = $night[$kB]['value'];
                            $currency = $night[$kB]['currency'];
                        }
                    }
    
                    $stay = $occupancie['stay'];
                    for ($x=0; $x < count($stay); $x++) { 
                        $type = $stay[$x]['type'];
                        $value = $stay[$x]['value'];
                        $currency = $stay[$x]['currency'];
                    }
    
                    $fees = $occupancie['fees'];
                    $mandatory_fee = $fees['mandatory_fee'];
                    $billable_currency = $mandatory_fee['billable_currency'];
                    $value = $billable_currency['value'];
                    $currency = $billable_currency['currency'];
                    $request_currency = $mandatory_fee['request_currency'];
                    $valueRC = $request_currency['value'];
                    $currencyRC = $request_currency['currency'];
    
                    $totals = $occupancie['totals'];
                    $inclusive = $totals['inclusive'];
                    $billable_currency = $inclusive['billable_currency'];
                    $valueBInclusive = $billable_currency['value'];
                    $currencyBInclusive = $billable_currency['currency'];
                    $request_currency = $inclusive['request_currency'];
                    $valueRInclusive = $request_currency['value'];
                    $currencyRInclusive = $request_currency['currency'];
    
                    $exclusive = $totals['exclusive'];
                    $billable_currency = $exclusive['billable_currency'];
                    $valueBExclusive = $billable_currency['value'];
                    $currencyBExclusive  = $billable_currency['currency'];
                    $request_currency = $exclusive['request_currency'];
                    $valueRExclusive = $request_currency['value'];
                    $currencyRExclusive = $request_currency['currency'];

                    for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                        if (is_array($tmp[$shid]['details'][$zRooms])) {
                            $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                        } else {
                            $baseCounterDetails = 0;
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $property_id;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $property_id;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $room_name;
                        //$tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-2";
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $valueRExclusive;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $property_id;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currencyBExclusive;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_code'] = $id;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $room_name;
                        //$tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $id;
                        /*
                            * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateCode'] = $RateCode;
                            * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateName'] = $RateName;
                            */
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = $refundable;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currencyRExclusive;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = $valueRExclusive;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($size);
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currencyRExclusive;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['href_price_check'] = $href_price_check;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['href_payment_options'] = $href_payment_options;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['start'] = $start;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['end'] = $end;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                        $pricebreakdown = array();
                        $pricebreakdownCount = 0;
                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                            $amount = $valueRExclusive / $noOfNights;
                            if ($expediaMarkup != 0) {
                                $amount = $amount + (($amount * $expediaMarkup) / 100);
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
                            if ($expediaMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                            $pricebreakdownCount ++;
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currencyRExclusive;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;

                        /* $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $Description;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicies'] = $Description;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $nights . ' night'; */
                    }
                }
            }
        }
    }
    $expedia = true;
}

//error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($expedia == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mexpedia where " . $sfilter;
        //error_log("\r\n SQL  $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 2;
        //error_log("\r\n QUERY  $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_expedia');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_expedia');
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