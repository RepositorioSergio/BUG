<?php
error_log("\r\nRakuten Policies\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\I18n\Translator\Translator;
use Laminas\Http\Client;
use Laminas\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$total = 0;
$db = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_rakuten where session_id='$session_id'";
$statement = $db->createStatement($sql);
$statement->prepare();
try {
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
    $sql = "select value from settings where name='rakutenDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
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
$fromRakuten = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromRakuten->diff($toHotelsPro);
$nights = $nights->format('%a');
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $hotel_code = $value['shid'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        $check_in_date = $value['check_in_date'];
        $check_out_date = $value['check_out_date'];
        $room_count = $value['room_count'];
        $currency = $value['currency'];
        $locale = $value['locale'];
        $source_market = $value['source_market'];
        $adults = $value['adults'];
        $children = $value['childrenb'];
        $room_type = $value['room_type'];

        $room_code = $value['room_code'];
        $rate_plan_code = $value['rate_plan_code'];
        $description = $value['description'];
        $food = $value['food'];
        $non_refundable = $value['non_refundable'];
        $queen = $value['queen'];
        $supplier_description = $value['supplier_description'];
        $booking_key = $value['booking_key'];
        $room_rate = $value['room_rate'];
        $room_rate_currency = $value['room_rate_currency'];
        $client_commission = $value['client_commission'];
        $client_commission_currency = $value['client_commission_currency'];
        $chargeable_rate = $value['chargeable_rate'];
        $chargeable_rate_currency = $value['chargeable_rate_currency'];
        $rate_type = $value['rate_type'];
        $room_view = $value['room_view'];
        
        if ($non_refundable == true) {
            $non_refundable = "true";
        } else {
            $non_refundable = "false";
        }

        $ages = "";
        $childrenb = "";
        if ($children == "") {
            $childrenb = "null";
        } else {
            for ($i=0; $i < count($children); $i++) { 
                if ($ages != "") {
                    $ages .= ',' . $children[$i];
                } else {
                    $ages .= $children[$i];
                }             
            }
            $childrenb = '[' . $ages . ']';
        }
        
        $raw = '{
            "search": {
                "hotel_id": "' . $shid . '",
                "check_in_date": "' . $check_in_date . '",
                "check_out_date": "' . $check_out_date . '",
                "room_count": ' . $room_count . ',
                "adult_count": ' . $adults . ',
                "currency": "' . $currency . '",
                "locale": "' . $locale . '",
                "source_market": "' . $source_market . '",
                "children": ' . $childrenb . '
            },
            "package": {
                "hotel_id": "' . $shid . '",
                "room_details": {
                "room_code": "' . $room_code . '",
                "rate_plan_code": "' . $rate_plan_code . '",
                "description": "' . $description . '",
                "food": ' . $food . ',
                "non_refundable": ' . $non_refundable . ',
                "room_type": "' . $room_type . '",
                "room_view": "' . $room_view . '",
                "beds": {
                    "queen": ' . $queen . '
                },
                "supplier_description": "' . $supplier_description . '"
            },
            "booking_key": "' . $booking_key . '",
            "room_rate": ' . $room_rate . ',
            "room_rate_currency": "' . $room_rate_currency . '",
            "client_commission": ' . $client_commission . ',
            "client_commission_currency": "' . $client_commission_currency . '",
            "chargeable_rate": ' . $chargeable_rate . ',
            "chargeable_rate_currency": "' . $chargeable_rate_currency . '",
            "rate_type": "' . $rate_type . '"
            }
           }';
        $client = new Client();
        $client->setOptions(array(
            'timeout' => 100,
            'sslverifypeer' => false,
            'sslverifyhost' => false
        ));
        $client->setHeaders(array(
            'accept-encoding' => 'gzip',
            'Content-Type' => 'application/json',
            'x-api-key' => '' . $rakutenAPIKey
        ));
        $client->setUri($rakutenServiceURL . 'booking_policy');
        $client->setMethod('POST');
        $client->setRawBody($raw);
        $response3 = $client->send();
        if ($response3->isSuccess()) {
            $response3 = $response3->getBody();
        } else {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($client->getUri());
            $logger->info($response3->getStatusCode() . " - " . $response3->getReasonPhrase());
            echo $return;
            echo $response3->getStatusCode() . " - " . $response3->getReasonPhrase();
            echo $return;
            die();
        }
        // error_log("\r\n Response3: $response3 \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_rakuten');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $rakutenServiceURL,
                'sqlcontext' => $response3,
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
        $response3 = json_decode($response3, true);
        $event_id = $response3['event_id'];
        $booking_policy_id = $response3['booking_policy_id'];
        // cancelation policy
        $cancellation_policy = $response3['cancellation_policy'];
        $remarks = $cancellation_policy['remarks'];
        $cancellation_policies = $cancellation_policy['cancellation_policies'];
        if (count($cancellation_policies) > 0) {
            for ($i = 0; $i < count($cancellation_policies); $i ++) {
                $penalty_percentage = $cancellation_policies[$i]['penalty_percentage'];
                $date_from = $cancellation_policies[$i]['date_from'];
                $date_to = $cancellation_policies[$i]['date_to'];
            }
        }
        // package
        $package = $response3['package'];
        $hotel_id = $package['hotel_id'];
        $booking_key = $package['booking_key'];
        $room_rate = $package['room_rate'];
        $room_rate_currency = $package['room_rate_currency'];
        $client_commission = $package['client_commission'];
        $client_commission_currency = $package['client_commission_currency'];
        $chargeable_rate = $package['chargeable_rate'];
        $chargeable_rate_currency = $package['chargeable_rate_currency'];
        $rate_type = $package['rate_type'];
        $room_details = $package['room_details'];
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
        //
        // GetBookingPolicy
        //
        $client = new Client();
        $client->setOptions(array(
            'timeout' => 100,
            'sslverifypeer' => false,
            'sslverifyhost' => false
        ));
        $client->setHeaders(array(
            'accept-encoding' => 'gzip',
            'Content-Type' => 'application/json',
            'x-api-key' => '' . $rakutenAPIKey
        ));
        $client->setUri($rakutenServiceURL . 'booking_policy/' . $booking_policy_id);
        $client->setMethod('GET');
        $response2 = $client->send();
        if ($response2->isSuccess()) {
            $response2 = $response2->getBody();
        } else {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($client->getUri());
            $logger->info($response2->getStatusCode() . " - " . $response2->getReasonPhrase());
            echo $return;
            echo $response2->getStatusCode() . " - " . $response2->getReasonPhrase();
            echo $return;
            die();
        }
        $response2 = json_decode($response2, true);
        $session_idHandler = $response2['session_id'];
        $event_id = $response2['event_id'];
        $booking_policy_id = $response2['booking_policy_id'];
        // cancelation policy
        $cancellation_policy = $response2['cancellation_policy'];
        $remarks = $cancellation_policy['remarks'];
        $cancellation_policies = $cancellation_policy['cancellation_policies'];
        if (count($cancellation_policies) > 0) {
            for ($i=0; $i < count($cancellation_policies); $i++) { 
                $penalty_percentage = $cancellation_policies[$i]['penalty_percentage'];
                $date_from = $cancellation_policies[$i]['date_from'];
                $date_to = $cancellation_policies[$i]['date_to'];
            }
        }
        // package
        $package = $response2['package'];
        $hotel_id = $package['hotel_id'];
        $booking_key = $package['booking_key'];
        $room_rate = $package['room_rate'];
        $room_rate_currency = $package['room_rate_currency'];
        $client_commission = $package['client_commission'];
        $client_commission_currency = $package['client_commission_currency'];
        $chargeable_rate = $package['chargeable_rate'];
        $chargeable_rate_currency = $package['chargeable_rate_currency'];
        $rate_type = $package['rate_type'];
        $room_details = $package['room_details'];
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
        // request
        $request = $response2['request'];
        $search = $request['search'];
        $check_in_date = $search['check_in_date'];
        $check_out_date = $search['check_out_date'];
        $source_market = $search['source_market'];
        $room_count = $search['room_count'];
        $adult_count = $search['adult_count'];
        $currency = $search['currency'];
        $locale = $search['locale'];
        $children = $search['children'];
        $package = $request['package'];
        $hotel_id = $package['hotel_id'];
        $booking_key = $package['booking_key'];
        $room_rate = $package['room_rate'];
        $room_rate_currency = $package['room_rate_currency'];
        $client_commission = $package['client_commission'];
        $client_commission_currency = $package['client_commission_currency'];
        $chargeable_rate = $package['chargeable_rate'];
        $chargeable_rate_currency = $package['chargeable_rate_currency'];
        $rate_type = $package['rate_type'];
        $room_details = $package['room_details'];
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
        
        //
        // Policies
        //
        $item['code'] = $value['shid'];
        $item['name'] = $value['name'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['nettotal'];
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
        
        if ($non_refundable !== false) {
            $item['nonrefundable'] = true;
            $item['cancelpolicy'] = $translator->translate("This is a non refundable booking.");
            $item['cancelpolicy_details'] = $translator->translate("This is a non refundable booking.");
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
            $item['cancelpolicy_deadlinetimestamp'] = time();
        } else {
            $cancelpolicy = $translator->translate("If you cancel this booking Date From ") . strftime("%a, %e %b %Y", strtotime($date_from)) . $translator->translate(" Date To ") . strftime("%a, %e %b %Y", strtotime($date_to)) . $translator->translate(" cost ") . $penalty_percentage . $translator->translate("% of total.");
            $cancelpolicy .= $translator->translate($remarks) . ".";
            $item['nonrefundable'] = false;
            $item['cancelpolicy'] = $cancelpolicy;
            $item['cancelpolicy_details'] = $cancelpolicy;
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", strtotime($date_to));
            $item['cancelpolicy_deadlinetimestamp'] = strtotime($date_to);
        }
        
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Laminas\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mzumata where sid='" . $shid . "' and hid=" . $hid;
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
$db = new \Laminas\Db\Adapter\Adapter($config);
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
    $db2 = new \Laminas\Db\Adapter\Adapter($config);
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
    $db = new \Laminas\Db\Adapter\Adapter($config);
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