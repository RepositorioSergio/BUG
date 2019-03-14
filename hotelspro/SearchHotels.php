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
$hotelspro = false;
$db2 = new \Zend\Db\Adapter\Adapter($config);
$sql = "select city_xml18, latitude, longitude from cities where id=" . $destination;
$statement2 = $db2->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml18 = $row_settings["city_xml18"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml18 = 0;
}
$db2->getDriver()
    ->getConnection()
    ->disconnect();
if ((int) $nationality > 0) {
    $db2 = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement2 = $db2->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
    $db2->getDriver()
        ->getConnection()
        ->disconnect();
} else {
    $db2 = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select value from settings where name='hotelsproDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_hotelspro";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}
$pax = "";
for ($i = 0; $i < count($selectedAdults); $i ++) {
    $pax .= '&pax=' . $selectedAdults[$i];
    for ($z = 0; $z < $selectedChildren[$i]; $z ++) {
        $pax .= ',' . $selectedChildrenAges[$i][$z];
    }
}
// echo $pax;
// die();
// $city_xml18 = "";
if ($city_xml18 != "") {
    $raw = 'search/?currency=' . $scurrency . '&client_nationality=' . $sourceMarket . $pax . '&checkin=' . strftime("%Y-%m-%d", $from) . '&checkout=' . strftime("%Y-%m-%d", $to) . '&destination_code=' . $city_xml18;
} else {
    $raw = 'search/?' . $pax . '&checkout=' . strftime("%Y-%m-%d", $to) . '&checkin=' . strftime("%Y-%m-%d", $from) . '&lat=' . $latitude . '&lon=' . $longitude . '&radius=1000&client_nationality=' . $sourceMarket . '&currency=' . $scurrency;
}
// echo $raw;
// die();
$sql = "select value from settings where name='hotelsproUsername' and affiliate_id=$affiliate_id_hotelspro";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelsproUsername = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='hotelsproPassword' and affiliate_id=$affiliate_id_hotelspro";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelsproPassword = base64_decode($row_settings['value']);
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='hotelsproMarkup' and affiliate_id=$affiliate_id_hotelspro";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelsproMarkup = (double) $row_settings['value'];
} else {
    $hotelsproMarkup = 0;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='hotelsproEndPointURL' and affiliate_id=$affiliate_id_hotelspro";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelsproEndPointURL = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
if ($hotelsproEndPointURL != "" and $hotelsproUsername != "" and $hotelsproPassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $hotelsproEndPointURL . $raw);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_USERPWD, $hotelsproUsername . ":" . $hotelsproPassword);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    // Descomentar para ver o resultado do provider
    // Nao esquecer de alterar o session id para testar por causa de cache
    // echo $response;
    // die();
    $endTime = microtime();
    try {
        $db2 = new \Zend\Db\Adapter\Adapter($config);
        $sql = new Sql($db2);
        $insert = $sql->insert();
        $insert->into('log_hotelspro');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $hotelsproEndPointURL . $raw,
            'sqlcontext' => $response,
            'errcontext' => ''
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db2->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    // echo $response;
    // die();
    $array = json_decode($response, true);
    // Descomentar para ver o vector
    // Nao esquecer de alterar o session id para testar por causa de cache
    // Echo para ver o array completro
    // echo "<xmp>";
    // var_dump($array);
    // echo "</xmp>";
    // die();
    $count = $array['count'];
    $code = $array['code'];
    $next_page_code = $array['next_page_code'];
    $max_wait = $array['max_wait'];
    $response_time = $array['response_time'];
    $result = $array['results'];
    // Results
    foreach ($result as $key => $valueResult) {
        $shid = $valueResult['hotel_code'];
        $checkout = $valueResult['checkout'];
        $checkin = $valueResult['checkin'];
        $destination_code = $valueResult['destination_code'];
        $products = $valueResult['products'];
        // Products
        foreach ($products as $key => $valueProducts) {
            $codep = $valueProducts['code'];
            $offer = $valueProducts['offer'];
            $pay_at_hotel = $valueProducts['pay_at_hotel'];
            $price = $valueProducts['price'];
            $netprice = $price;
            $currency = $valueProducts['currency'];
            $rooms = $valueProducts['rooms'];
            $nonrefundable = $valueProducts['nonrefundable'];
            $supports_cancellation = $valueProducts['supports_cancellation'];
            $hotel_price = $valueProducts['hotel_price'];
            $meal_type = $valueProducts['meal_type'];
            $policies = $valueProducts['policies'];
            // $valueProducts['minimum_selling_price']
            // $valueProducts['view']
            // echo "<xmp>";
            // var_dump($valueProducts);
            // echo "</xmp>";
            // Rooms
            foreach ($rooms as $key => $valueRooms) {
                $pax = $valueRooms['pax'];
                $adults = $pax['adult_quantity'];
                $children = count($pax['children_ages']);
                $room_category = $valueRooms['room_category'];
                $room_description = $valueRooms['room_description'];
                $nightly_prices = $valueRooms['nightly_prices'];
                $room_type = $valueRooms['room_type'];
                $pricebreakdown = array();
                $pricebreakdownCount = 0;
                // echo "<xmp>";
                // var_dump($valueRooms);
                // echo "</xmp>";
                // for ($rZZ = 0; $rZZ < $nights; $rZZ ++) {
                // Markup
                if ($hotelsproMarkup != 0) {
                    $price = $price + (($price * $hotelsproMarkup) / 100);
                }
                // Geo target markup
                if ($internalmarkup != 0) {
                    $price = $price + (($price * $internalmarkup) / 100);
                }
                // Agent markup
                if ($agent_markup != 0) {
                    $price = $price + (($price * $agent_markup) / 100);
                }
                // Fallback Markup
                if ($hotelsproMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                    $price = $price + (($price * $HotelsMarkupFallback) / 100);
                }
                // Agent discount
                if ($agent_discount != 0) {
                    $price = $price - (($price * $agent_discount) / 100);
                }
                if ($scurrency != "" and $currency != $scurrency) {
                    $price = $CurrencyConverter->convert($price, $currency, $scurrency);
                }
                for ($rZZ = 0; $rZZ < count($nightly_prices); $rZZ ++) {
                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                    $amount = $nightly_prices[strftime("%Y-%m-%d", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)))];
                    if ($hotelsproMarkup != 0) {
                        $amount = $amount + (($amount * $hotelsproMarkup) / 100);
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
                    if ($hotelsproMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                    if (($selectedAdults[$zRooms] <= $adults and $selectedChildren[$zRooms] <= $children) or ($selectedAdults[$zRooms] <= $adults and (($selectedAdults[$zRooms] + $selectedChildren[$zRooms]) <= ($adults + $children)))) {
                        if (is_array($tmp[$shid]['details'][$zRooms])) {
                            $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                        } else {
                            $baseCounterDetails = 0;
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $code;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $room_category . " - " . $room_description;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-18";
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $netprice;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $codep;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['price'] = $price;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currency;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_category'] = $room_category;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $room_description;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $room_type;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = $nonrefundable;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = $price;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['boardtype'] = $meal_type;
                        $t = $meal_type;
                        try {
                            $db2 = new \Zend\Db\Adapter\Adapter($config);
                            $sql = "select mapped from board_mapping where description='" . addslashes($t) . "'";
                            $statement2 = $db2->createStatement($sql);
                            $statement2->prepare();
                            $row_board_mapping = $statement2->execute();
                            if ($row_board_mapping->valid()) {
                                $row_board_mapping = $row_board_mapping->current();
                                $t = $row_board_mapping["mapped"];
                            }
                            $db2->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (Exception $e) {
                            $logger = new Logger();
                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                            $logger->addWriter($writer);
                            $logger->info($e->getMessage());
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($t);
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currency;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                        if ($offer == true) {
                            $tmp[$shid]['special'] = true;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $translator->translate("Special Offer");
                        } else {
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                        }
                    }
                }
            }
            ksort($tmp[$shid]['details']);
        }
        $sfilter[] = " sid='$shid' ";
        $hotelspro = true;
    }
    // echo "<xmp>";
    // var_dump($tmp);
    // echo "</xmp>";
    // echo "<xmp>";
    // var_dump($sfilter);
    // echo "</xmp>";
    if ($hotelspro == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = "select hid, sid from xmlhotels_mhotelspro where " . $sfilter;
            $statement2 = $db2->createStatement($sql);
            $statement2->prepare();
            $result2 = $statement2->execute();
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
            $db2->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        if (is_array($sidfilter)) {
            $sidfilter = implode(',', $sidfilter);
            $query = 'call xmlhotels("' . $sidfilter . '")';
            $supplier = 18;
            // Store Session
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db2);
            $delete = $sql->delete();
            $delete->from('quote_session_hotelspro');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            try {
                $results = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $sql = new Sql($db2);
            $insert = $sql->insert();
            $insert->into('quote_session_hotelspro');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $response,
                'data' => base64_encode(serialize($hotels_array)),
                'searchsettings' => base64_encode(serialize($requestdata))
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            try {
                $results = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $db2->getDriver()
                ->getConnection()
                ->disconnect();
        }
    }
}
?>