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
if (file_exists("src/App/language/" . $lang . ".mo")) {
    $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
}
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$enjoycuba = false;
$sql = "select city_xml03 from cities where id=" . $destination;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml03 = (int) $row_settings["city_xml03"];
} else {
    $city_xml03 = 0;
}
// error_log("\r\nEnjoycuba - City XML03 : $city_xml03\r\n", 3, "/srv/www/htdocs/error_log");
if ($city_xml03 > 0) {
    $raw = 'filter={"lang": "2", "startDate": "' . strftime("%Y-%m-%d", $from) . '", "endDate": "' . strftime("%Y-%m-%d", $to) . '", "destination": "' . $city_xml03 . '", "currency": "2", "limit": "9999", "offset": "0"}';
    $sql = "select value from settings where name='enjoycubaapikey' and affiliate_id=$affiliate_id_enjoycuba";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $enjoycubaapikey = $row_settings['value'];
    }
    $sql = "select value from settings where name='enjoycubaMarkup' and affiliate_id=$affiliate_id_enjoycuba";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $enjoycubaMarkup = (double) $row_settings['value'];
    } else {
        $enjoycubaMarkup = 0;
    }
    $sql = "select value from settings where name='enjoycubaTimeout' and affiliate_id=$affiliate_id_enjoycuba";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $enjoycubaTimeout = (int) $row_settings['value'];
    } else {
        $enjoycubaTimeout = 0;
    }
    $sql = "select value from settings where name='enjoycubawebserviceurl' and affiliate_id=$affiliate_id_enjoycuba";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $enjoycubawebserviceurl = $row_settings['value'];
    }
    if ($enjoycubaapikey != "" and $enjoycubawebserviceurl != "") {
        // error_log("\r\nEnjoycuba: $raw\r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\nEnjoycuba: " . $enjoycubawebserviceurl . "filtersC\r\n", 3, "/srv/www/htdocs/error_log");
        if ($enjoycubaTimeout == 0) {
            $enjoycubaTimeout = 120;
        }
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $enjoycubawebserviceurl . "filtersC");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_TIMEOUT, $enjoycubaTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-API-KEY: ' . $enjoycubaapikey
        ));
        $response = curl_exec($ch);
        error_log("\r\nEnjoycuba: $response\r\n", 3, "/srv/www/htdocs/error_log");
        curl_close($ch);
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_enjoycuba');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $raw,
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
        $array = json_decode($response, true);
        foreach ($array as $key => $value) {
            $shid = $value['id_hotel'];
            $roomCombinations = $value['rooms_prices']['roomCombinations'];
            $cancellation_policy = "";
            $room_currency = $value['rooms_prices']['currency'];
            foreach ($roomCombinations as $keyRoom => $valueRoom) {
                $mealPlans = $valueRoom['roomType']['mealPlans'];
                foreach ($mealPlans as $keyRoomMealPlan => $valueRoomMealPlan) {
                    $priceCombinations = $valueRoomMealPlan['priceCombinations'];
                    foreach ($priceCombinations as $keyRoomMealPlanpriceCombinations => $valueRoomMealPlanpriceCombinations) {
                        $nettotal = $valueRoomMealPlanpriceCombinations['price'];
                        $total = $nettotal;
                        // Markup
                        if ($hotelbedsMarkup != 0) {
                            $total = $total + (($total * $hotelbedsMarkup) / 100);
                        }
                        // Geo target markup
                        if ($internalmarkup != 0) {
                            $total = $total + (($total * $internalmarkup) / 100);
                        }
                        // Agent markup
                        if ($agent_markup != 0) {
                            $total = $total + (($total * $agent_markup) / 100);
                        }
                        // Fallback Markup
                        if ($hotelbedsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                            $total = $total + (($total * $HotelsMarkupFallback) / 100);
                        }
                        // Agent discount
                        if ($agent_discount != 0) {
                            $total = $total - (($total * $agent_discount) / 100);
                        }
                        if ($scurrency != "" and $currency != $scurrency) {
                            $total = $CurrencyConverter->convert($total, $currency, $scurrency);
                        }
                        $room = ucwords(strtolower($valueRoom['roomType']['roomtypename']), "|( /-,");
                        $pk_roomtype = $valueRoom['roomType']['pk_roomtype'];
                        $pk_combination = $valueRoomMealPlanpriceCombinations['pk_combination'];
                        $pk_combination = explode(",", $pk_combination);
                        $adt = $pk_combination[0];
                        $chd = $pk_combination[1] + $pk_combination[2];
                        $room .= " - " . $valueRoomMealPlanpriceCombinations['description'];
                        $pricebreakdown = array();
                        $pricebreakdownCount = 0;
                        $Gross = $total / $nights;
                        for ($rZZ = 0; $rZZ < $nights; $rZZ ++) {
                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                            $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($Gross);
                            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $Gross;
                            $pricebreakdownCount = $pricebreakdownCount + 1;
                        }
                        $t = $valueRoomMealPlan["mealplanname"];
                        try {
                            $sql = "select mapped from board_mapping where description='" . addslashes($t) . "'";
                            $statement = $db->createStatement($sql);
                            $statement->prepare();
                            $row_board_mapping = $statement->execute();
                            $row_board_mapping->buffer();
                            if ($row_board_mapping->valid()) {
                                $row_board_mapping = $row_board_mapping->current();
                                $t = $row_board_mapping["mapped"];
                            }
                        } catch (\Exception $e) {
                            $logger = new Logger();
                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                            $logger->addWriter($writer);
                            $logger->info($e->getMessage());
                        }
                        for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                            if (($selectedAdults[$zRooms] <= $adt and $selectedChildren[$zRooms] <= $chd) or ($selectedAdults[$zRooms] <= $adt and (($selectedAdults[$zRooms] + $selectedChildren[$zRooms]) <= ($adt + $chd)))) {
                                if (is_array($tmp[$shid]['details'][$zRooms])) {
                                    $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                } else {
                                    $baseCounterDetails = 0;
                                }
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $room;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = false;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $cancellation_policy;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $nettotal;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $room_currency;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currency;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-3";
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['boardid'] = $valueRoomMealPlan['pk_mealplan'];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pk_roomtype'] = $pk_roomtype;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pk_combination'] = $pk_combination;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($t);
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                            }
                        }
                    }
                }
                if (is_array($tmp[$shid]['details'])) {
                    ksort($tmp[$shid]['details']);
                }
            }
            $sfilter[] = " sid=$shid ";
            $enjoycuba = true;
        }
        if ($enjoycuba == true) {
            $sfilter = implode(' or ', $sfilter);
            try {
                $sql = "select hid, sid from xmlhotels_menjoycuba where " . $sfilter;
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $result = $statement->execute();
                $result->buffer();
                if ($result instanceof ResultInterface && $result->isQueryResult()) {
                    $resultSet = new ResultSet();
                    $resultSet->initialize($result);
                    foreach ($resultSet as $row) {
                        $sidfilter[] = $row->hid;
                        if (is_array($hotels_array[$row->hid])) {
                            // Append to original details
                            $tmph = $hotels_array[$row->hid]['details'];
                            $tmps = $tmp[$row->sid]['details'];
                            foreach ($tmph as $key => $value) {
                                $last = count($tmph[$key]);
                                foreach ($tmps[$key] as $keyd => $valued) {
                                    $tmph[$key][$last] = $valued;
                                    $last ++;
                                }
                            }
                            $hotels_array[$row->hid]['details'] = $tmph;
                        } else {
                            $hotels_array[$row->hid] = $tmp[$row->sid];
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
                $supplier = 3;
                // Store Session
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_enjoycuba');
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
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_enjoycuba');
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
            }
        }
    }
}
?>