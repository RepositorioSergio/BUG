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
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_hotelspro where session_id='$session_id'";
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
    $xmlrequest = $row_settings["xmlrequest"];
    $xmlresult = $row_settings["xmlresult"];
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
    $rooms = $searchsettings['rooms'];
    $adt = $searchsettings['adt'];
    $chd = $searchsettings['chd'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='enablehotelspro' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $affiliate_id_hotelspro = $affiliate_id;
} else {
    $affiliate_id_hotelspro = 0;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='hotelsproUsername' and affiliate_id=$affiliate_id_hotelspro" . $branch_filter;
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
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='hotelsproPassword' and affiliate_id=$affiliate_id_hotelspro" . $branch_filter;
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
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='hotelsproEndPointURL' and affiliate_id=$affiliate_id_hotelspro" . $branch_filter;
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
$db = new \Zend\Db\Adapter\Adapter($config);
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
$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');
$fromHotelsPRO = $fromHotelsPRO->getTimestamp();
$toHotelsPro = $toHotelsPro->getTimestamp();
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['code'];
            $scode = $value['scode'];
            $hotel_code = $value['shid'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";
        //
        // Policies
        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $hotelsproEndPointURL . "provision/" . $code);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $code);
        curl_setopt($ch, CURLOPT_USERPWD, $hotelsproUsername . ":" . $hotelsproPassword);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        // error_log("\r\nResponse Policies: $result . \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\nCode: $code \r\n", 3, "/srv/www/htdocs/error_log");
        $result1 = json_decode($result, true);
        // error_log("\r\nVECTOR RESULT1 " . print_r($result1, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        // $result1 = $arrayResponse['results'];
        $vector = array();
        // error_log("\r\nVECTOR COMECA DE NOVO \r\n", 3, "/srv/www/htdocs/error_log");
        $vector['code'] = $result1['code'];
        $vector['destination_code'] = $result1['destination_code'];
        $vector['shid'] = $result1['hotel_code'];
        $vector['additional_info'] = $result1['additional_info'];
        $vector['checkin'] = $result1['checkin'];
        $vector['checkout'] = $result1['checkout'];
        $vector['price'] = $result1['price'];
        $vector['currency'] = $result1['currency'];
        $vector['pay_at_hotel'] = $result1['pay_at_hotel'];
        $vector['hotel_price'] = $result1['hotel_price'];
        $vector['hotel_currency'] = $result1['hotel_currency'];
        $vector['meal_type'] = $result1['meal_type'];
        $vector['nonrefundable'] = $result1['nonrefundable'];
        $vector['view'] = $result1['view'];
        $vector['rooms'] = $result1['rooms'];
        $rooms = $vector['rooms'];
        $vector['offer'] = $result1['offer'];
        $vector['policies'] = $result1['policies'];
        $policies = $vector['policies'];
        $vector['fees'] = $result1['fees'];
        $vector['supports_cancellation'] = $result1['supports_cancellation'];
        $vector['taxes'] = $result1['taxes'];
        $vector['minimum_selling_price'] = $result1['minimum_selling_price'];
        foreach ($rooms as $key => $valueRooms1) {
            $pax = $valueRooms1['pax'];
            $room_category = $valueRooms1['room_category'];
            $room_description = $valueRooms1['room_description'];
            $nightly_prices = $valueRooms1['nightly_prices'];
            $room_type = $valueRooms1['room_type'];
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < count($nightly_prices); $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) + $rZZ, date("y", $fromHotelsPRO)));
                $amount = $nightly_prices[strftime("%Y-%m-%d", mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) + $rZZ, date("y", $fromHotelsPRO)))];
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
            $novotmp['room'] = $room_category;
            $novotmp['shid'] = $shid;
            $novotmp['code'] = $shid;
            $novotmp['room_category'] = $room_category;
            $novotmp['room_description'] = $room_description;
            $novotmp['room_type'] = $room_type;
            $novotmp['recommended'] = false;
            $novotmp['scurrency'] = $currency;
            $novotmp['currency'] = $currency;
            $novotmp['pricebreakdown'] = $pricebreakdown;
        }
        $vector['rooms'] = $novotmp;
        // error_log("\r\nNOVO VECTOR: " . print_r($vector, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db2);
            $insert = $sql->insert();
            $insert->into('log_hotelspro');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $hotelsproEndPointURL . "provision/" . $code,
                'sqlcontext' => $result,
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
        //
        // EOF Policies
        // EOF Check prices & availability
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        // $item['adults'] = $selectedAdults[$c];
        // $item['children'] = $selectedChildren[$c];
        // $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        if ($vector['nonrefundable'] == true) {
            $cancelation_string = $translator->translate("This is a non refundable offer. You will be charged full amount of the booking.");
            $cancelation_deadline = time();
        } else {
            foreach ($vector['policies'] as $key => $val) {
                $days_remaining = $val['days_remaining'];
                $ratio = $val['ratio'];
                if ($cancelation_string != "") {
                    $cancelation_string .= "<br/>";
                }
                $cancelation_string .= $translator->translate("Charge") . " " . number_format($ratio * $value['total'], 2, '.', '') . " " . $translator->translate("if cancelled on and after") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) - $days_remaining, date("y", $fromHotelsPRO)));
                if ($cancelation_deadline == 0) {
                    $cancelation_deadline = mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) - $days_remaining, date("y", $fromHotelsPRO));
                } else {
                    if (mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) - $days_remaining, date("y", $fromHotelsPRO)) < $cancelation_deadline) {
                        $cancelation_deadline = mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) - $days_remaining, date("y", $fromHotelsPRO));
                    }
                }
            }
        }
        if ($vector['additional_info'] != "") {
            if ($cancelation_string == "") {
                $cancelation_string = $vector['additional_info'];
            } else {
                $cancelation_string .= "<br/><br/>" . $vector['additional_info'];
            }
        }
        $item['cancelpolicy'] = $cancelation_string;
        $item['cancelpolicy_deadline'] = strftime("%d-%m-%Y", $cancelation_deadline);
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        $item['cancelpolicy_details'] = $cancelation_details;
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mhotelspro where sid='" . $shid . "' and hid=" . $hid;
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