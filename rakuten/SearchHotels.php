<?php
$scurrency = strtoupper($currency);
use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$rakuten = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
error_log("\r\nStart Rakuten\r\n", 3, "/srv/www/htdocs/error_log");
$hcount = 0;
$hotellist = "";
$sql = "select sid from xmlhotels_mzumata, xmlhotels where xmlhotels.city=" . $destination . " and xmlhotels.id=xmlhotels_mzumata.hid";
// error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $result = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    error_log("\r\nRakuten - TODO - Change from 100 to 200 and remove fake test hotels\r\n", 3, "/srv/www/htdocs/error_log");
    foreach ($resultSet as $row) {
        // Patch - TODO -> More hotels
        if ($hcount < 150) {
            if ($row['sid'] != "") {
                if ($hotellist != "") {
                    $hotellist .= ",";
                }
                $hotellist .= $row['sid'];
                $hcount ++;
            }
        }
    }
}
error_log("\r\nRakuten - TODO - More hotels (200)\r\n", 3, "/srv/www/htdocs/error_log");
if ($hcount > 0) {
    $sql = "select name, country_id, zone_id latitude, longitude from cities where id=" . $destination;
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $name = $row_settings["name"];
        $country_id = $row_settings["country_id"];
        $zone_id = $row_settings["zone_id"];
        $latitude = $row_settings["latitude"];
        $longitude = $row_settings["longitude"];
    }
    $affiliate_id = 0;
    $sql = "select value from settings where name='enablerakuten' and affiliate_id=$affiliate_id" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $affiliate_id_rakuten = $affiliate_id;
    } else {
        $affiliate_id_rakuten = 0;
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
        $sql = "select value from settings where name='ratukenDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_rakuten";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='rakutenAPIKey' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rakutenAPIKey = $row_settings['value'];
    }
    $sql = "select value from settings where name='rakutenaffiliates_id' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rakutenaffiliates_id = $row_settings['value'];
    }
    $sql = "select value from settings where name='rakutenbranches_id' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rakutenbranches_id = $row_settings['value'];
    }
    $sql = "select value from settings where name='rakutenServiceURL' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenServiceURL = $row['value'];
    }
    $sql = "select value from settings where name='rakutenMarkup' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rakutenMarkup = (double) $row_settings['value'];
    } else {
        $rakutenMarkup = 0;
    }
    $sql = "select value from settings where name='rakutenServiceURL' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenServiceURL = $row['value'];
    }
    $sql = "select value from settings where name='rakutenb2cMarkup' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenb2cMarkup = $row['value'];
    }
    $sql = "select value from settings where name='rakutenParallelSearch' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenParallelSearch = $row['value'];
    }
    $sql = "select value from settings where name='rakutenSearchSortorder' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenSearchSortorder = $row['value'];
    }
    $sql = "select value from settings where name='rakutenTimeout' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenTimeout = (int) $row['value'];
    }
    if ($rakutenServiceURL != "" and $rakutenAPIKey != "") {
        $adults = 0;
        for ($r = 0; $r < count($selectedAdults); $r ++) {
            $adults = $adults + $selectedAdults[$r];
        }
        $url = 'hotel_list?check_in_date=' . strftime("%Y-%m-%d", $from) . '&check_out_date=' . strftime("%Y-%m-%d", $to) . '&adult_count=' . $adults;
        $children = 0;
        $ages = "";
        for ($r = 0; $r < count($selectedChildren); $r ++) {
            if ($selectedChildren[$r] > 0) {
                for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                    if ($ages != "") {
                        $ages .= ',' . $selectedChildrenAges[$r][$z];
                    } else {
                        $ages .= $selectedChildrenAges[$r][$z];
                    }
                    $children ++;
                }
            }
        }
        if ($children > 0) {
            $url .= '&children=' . $ages;
        }
        $hotellist .= ",PLPo,7uLH,dnhy,9CPH,fst1,fst2,fst4,TJRf,KQQR,SvBX,WijN wijn,reFn,usg1,usj1"; // Test hotels
        $url .= '&room_count=' . $rooms . '&currency=' . strtoupper($currency) . '&source_market=' . $sourceMarket . '&hotel_id_list=' . urlencode($hotellist);
        // error_log("\r\nrakutenAPIKey: $rakutenAPIKey\r\n", 3, "/srv/www/htdocs/error_log");
        $startTime = microtime();
        $session_idHandler = "";
        error_log("\r\nRakuten Request: $url\r\n", 3, "/srv/www/htdocs/error_log");
        for ($rPool = 0; $rPool <= 5; $rPool ++) {
            error_log("\r\nPool: $rPool\r\n", 3, "/srv/www/htdocs/error_log");
            $client = new Client();
            $client->setOptions(array(
                'timeout' => 100,
                'sslverifypeer' => false,
                'sslverifyhost' => false
            ));
            if ($session_idHandler != "") {
                $client->setHeaders(array(
                    'accept-encoding' => 'gzip',
                    'Content-Type' => 'application/json',
                    'x-api-key' => $rakutenAPIKey,
                    'session_id' => $session_idHandler
                ));
            } else {
                $client->setHeaders(array(
                    'accept-encoding' => 'gzip',
                    'Content-Type' => 'application/json',
                    'x-api-key' => $rakutenAPIKey
                ));
            }
            $client->setUri($rakutenServiceURL . $url);
            $client->setMethod('GET');
            $response = $client->send();
            if ($response->isSuccess()) {
                $response = $response->getBody();
            } else {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($client->getUri());
                $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
                error_log("\r\nRakuten Error - " . $response->getStatusCode() . " - " . $response->getReasonPhrase() . "\r\n", 3, "/srv/www/htdocs/error_log");
            }
            error_log("\r\nResponse: $response\r\n", 3, "/srv/www/htdocs/error_log");
            $endTime = microtime();
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('log_rakuten');
                $insert->values(array(
                    'datetime_created' => time(),
                    'filename' => 'SearchHotels.php',
                    'errorline' => $this->microtime_diff($startTime, $endTime),
                    'errormessage' => $rakutenServiceURL,
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
            $session_idHandler = $response['session_id'];
            $event_id = $response['event_id'];
            $status = $response['status'];
            if ($status == "complete") {
                error_log("\r\nStatus Complete\r\n", 3, "/srv/www/htdocs/error_log");
                break;
            }
        }
        $search = $response['search'];
        $check_in_date = $search['check_in_date'];
        $check_out_date = $search['check_out_date'];
        $source_market = $search['source_market'];
        $room_count = $search['room_count'];
        $adult_count = $search['adult_count'];
        $currency = $search['currency'];
        $locale = $search['locale'];
        $children = $search['children'];
        $hotelsb = $response['hotels'];
        if (count($hotelsb) > 0) {
            for ($j = 0; $j < count($hotelsb); $j ++) {
                $id = $hotelsb[$j]['id'];
                $rates = $hotelsb[$j]['rates'];
                $packages = $rates['packages'];
                if (count($packages) > 0) {
                    for ($jAux = 0; $jAux < count($packages); $jAux ++) {
                        $shid = $packages[$jAux]['hotel_id'];
                        // error_log("\r\nshid: $shid \r\n", 3, "/srv/www/htdocs/error_log");
                        $sfilter[] = " sid='$shid' ";
                        $booking_key = $packages[$jAux]['booking_key'];
                        $room_rate = $packages[$jAux]['room_rate'];
                        $room_rate_currency = $packages[$jAux]['room_rate_currency'];
                        $client_commission = $packages[$j]['client_commission'];
                        $client_commission_currency = $packages[$jAux]['client_commission_currency'];
                        $chargeable_rate = $packages[$jAux]['chargeable_rate'];
                        $chargeable_rate_currency = $packages[$jAux]['chargeable_rate_currency'];
                        $rate_type = $packages[$jAux]['rate_type'];
                        $room_details = $packages[$jAux]['room_details'];
                        $room_code = $room_details['room_code'];
                        $rate_plan_code = $room_details['rate_plan_code'];
                        $rate_plan_description = $room_details['rate_plan_description'];
                        $description = $room_details['description'];
                        $food = $room_details['food'];
                        $non_refundable = $room_details['non_refundable'];
                        $room_type = $room_details['room_type'];
                        $room_view = $room_details['room_view'];
                        $supplier_description = $room_details['supplier_description'];
                        $non_smoking = $room_details['non_smoking'];
                        $room_gender = $room_details['room_gender'];
                        $benefits = $room_details['benefits'];
                        $floor = $room_details['floor'];
                        $amenitites = $room_details['amenitites'];
                        $beds = $room_details['beds'];
                        $queen = $beds['queen'];
                        $total = $chargeable_rate;
                        $zRooms = 0;
                        if (is_array($tmp[$shid])) {
                            $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                        } else {
                            $baseCounterDetails = 0;
                        }
                        // error_log("\r\n$shid - $zRooms - $baseCounterDetails - $description - $room_type - $total\r\n", 3, "/srv/www/htdocs/error_log");
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $room_type;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $shid;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $booking_key;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-74";
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $description;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $description;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $room_type;
                        // Search
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['check_in_date'] = $check_in_date;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['check_out_date'] = $check_out_date;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_count'] = $room_count;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currency;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['locale'] = $locale;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['childrenb'] = $children;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rate_type'] = $rate_type;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['source_market'] = $source_market;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $room_rate;
                        if ($rakutenMarkup != 0) {
                            $total = $total + (($total * $rakutenMarkup) / 100);
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
                        if ($rakutenMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                            $total = $total + (($total * $HotelsMarkupFallback) / 100);
                        }
                        // Agent discount
                        if ($agent_discount != 0) {
                            $total = $total - (($total * $agent_discount) / 100);
                        }
                        if ($scurrency != "" and $currency != $scurrency) {
                            $total = $CurrencyConverter->convert($total, $currency, $scurrency);
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                        try {
                            $sql = "select mapped from board_mapping where description='" . addslashes($room_type) . "'";
                            $statement = $db->createStatement($sql);
                            $statement->prepare();
                            $row_board_mapping = $statement->execute();
                            $row_board_mapping->buffer();
                            if ($row_board_mapping->valid()) {
                                $row_board_mapping = $row_board_mapping->current();
                                $room_type = $row_board_mapping["mapped"];
                            }
                        } catch (\Exception $e) {
                            $logger = new Logger();
                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                            $logger->addWriter($writer);
                            $logger->info($e->getMessage());
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($room_type);
                        $pricebreakdown = array();
                        $pricebreakdownCount = 0;
                        $amount = $total / $noOfNights;
                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                            $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                            $pricebreakdownCount = $pricebreakdownCount + 1;
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $chargeable_rate_currency;
                        //
                        // Special
                        //
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                        //
                        // Cancelation Policies
                        //
                        if ($non_refundable !== false) {
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = true;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking");
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
                        } else {
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = "";
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                        }
                    }
                }
            }
            $rakuten = true;
        }
    }
    // error_log("\r\nRakuten TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
    if ($rakuten == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $sql = "select hid, sid from xmlhotels_mzumata where " . $sfilter;
            // error_log("\r\nRakuten SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        // error_log("\r\nRakuten TMP 2:" . print_r($hotels_array, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        if (is_array($sidfilter)) {
            $sidfilter = implode(',', $sidfilter);
            $query = 'call xmlhotels("' . $sidfilter . '")';
            $supplier = 74;
            // error_log("\r\nRakuten Query: $query \r\n", 3, "/srv/www/htdocs/error_log");
            try {
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_rakuten');
                $delete->where(array(
                    'session_id' => $session_id
                ));
                $statement = $sql->prepareStatementForSqlObject($delete);
                $results = $statement->execute();
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_rakuten');
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
}
error_log("\r\nEnd Rakuten\r\n", 3, "/srv/www/htdocs/error_log");
?>