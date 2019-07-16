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
error_log("\r\n COMECA POLICIES \r\n", 3, "/srv/www/htdocs/error_log");
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_expedia where session_id='$session_id'";
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
$branch_filter = '';
$sql = "select value from settings where name='enableexpedia' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_expedia= $affiliate_id;
} else {
    $affiliate_id_expedia = 0;
}

$sql = "select value from settings where name='expediaAPIKey' and affiliate_id=$affiliate_id_expedia" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaAPIKey = $row_settings['value'];
}
$sql = "select value from settings where name='expediaMarkup' and affiliate_id=$affiliate_id_expedia" . $branch_filter;
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
$sql = "select value from settings where name='expediaSharedSecret' and affiliate_id=$affiliate_id_expedia" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaSharedSecret = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='expediaServiceURL' and affiliate_id=$affiliate_id_expedia" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaServiceURL = $row_settings['value'];
}
error_log("\r\n expediaServiceURL  $expediaServiceURL \r\n", 3, "/srv/www/htdocs/error_log");

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
        error_log("\r\n ENTROU FOREACH \r\n", 3, "/srv/www/htdocs/error_log");
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['HotelId'];
            $scode = $value['shid'];
            $HotelId = $value['HotelId'];
            $href_price_check = $value['href_price_check'];
            $href_payment_options = $value['href_payment_options'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }

        $end = $value['end'];
        $start = $value['start'];
        $Description = "You can cancel from " . $start . " until " . $end;
        $cancelpolicy = $Description;
        $cancelpolicy_deadline = $end;
        

        $from_date = date('Y-m-d' , strtotime($from));
        $to_date = date('Y-m-d' , strtotime($to));


        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";

        $url = 'https://test.ean.com';

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

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $href_price_check);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json",
            "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
            "Accept-Encoding: gzip",
            "Authorization: " . $authorization,
            "Customer-Ip: " . $ipaddress
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

        error_log("\r\n RESPONSE  $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        
        try {
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db2);
            $insert = $sql->insert();
            $insert->into('log_expedia');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $expediaServiceURL,
                'sqlcontext' => $response2,
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
        $response2 = json_decode($response2, true);

        $night = array();
        $occupancie = array();

        $status = $response2['status'];
        $links = $response2['links'];
        $book = $links['book'];
        $method = $book['method'];
        $href = $book['href'];

        $occupancies = $response2['occupancies'];
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
            $marketing_fee = $totals['marketing_fee'];
            $billable_currency = $marketing_fee['billable_currency'];
            $valueBMFee = $billable_currency['value'];
            $currencyBMFee = $billable_currency['currency'];
            $request_currency = $marketing_fee['request_currency'];
            $valueRMFee = $request_currency['value'];
            $currencyRMFee = $request_currency['currency'];

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
        }

        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $url . $href_payment_options);
        curl_setopt($ch2, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch2, CURLOPT_HEADER, false);
        curl_setopt($ch2, CURLOPT_VERBOSE, true);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            "Accept: application/json",
            "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
            "Accept-Encoding: gzip",
            "Authorization: " . $authorization,
            "Customer-Ip: " . $ipaddress
        ));
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $response3 = curl_exec($ch2);
        $error2 = curl_error($ch2);
        $headers2 = curl_getinfo($ch2);
        curl_close($ch2);
        error_log("\r\n RESPONSE Payment  $response3 \r\n", 3, "/srv/www/htdocs/error_log");


        $ch3 = curl_init();
        curl_setopt($ch3, CURLOPT_URL, $expediaServiceURL . 'properties/24051641/deposit-policies?token=REhZAQsABAMGQggMV1pFAV1YVA5cZhBYEgNKH0ZcDEgWWAhUakkFF1xZajB1KHd3dSp7IUJHBgdUSxBdXAFpQVtDRVBTDkJURwtQSEdcDkYeQ1BTURYXW1cCb0JcXFtZCFUEUVYFBk4NBVxSGQxRBAFLVwMAVRQDC1pRDwBQAgJbDlMVQ1xHAWhMHxJRXFRWXwBMXRYeDlsCQBFTWgUaDwcTURAVQQ1ZUBNuFVFAR1sOXm4LBgtXVlJRUwYHVB5cVlJWGlAPWAtMVl8BA0tTDloHVQAOCVVUBAAWF1NYAEA6AVoFCwtVDV5PBlNHDEUGHkddF0RaWw1rD1FYUQIAWVZXBwFOCgUCVUsFXgVfSA1ZVwQVUgUFXQEDVlNRA1pRFglWDVNHUQVQXF1XGTBgFhNQR1I7CkNMD1kIBBdKBFhTF2dRDBVZQgxeCF1aFQkNDUcDDTpDU1BTBV5dRANVD11DAxZdXDwSXllWWwZUAwhPUVBIAFsyUlNDUXYDUEcBcQoASwYBA2kRAggOQFNCWB4GWhdeTRBAOQVWXVAJNmtAVAdDXF1ZUxIHUQFXDVpYBFcFVV0YU1gcCFcSQgdERlpTRWxGQV9TXg5ROl1QXAMJAgMDAkIGR0sXVVhWQVttYyAQXlYTWAZMXQoGa1taAFcIUAkMBx8GWwZQXVsTR1kLBgEKHQUKSQYPQCFcBFNYShRUDFAMVQlRA1gHDVA=');
        curl_setopt($ch3, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch3, CURLOPT_HEADER, false);
        curl_setopt($ch3, CURLOPT_VERBOSE, true);
        curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch3, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch3, CURLOPT_HTTPHEADER, array(
            "Accept: application/json",
            "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
            "Accept-Encoding: gzip",
            "Authorization: " . $authorization,
            "Customer-Ip: " . $ipaddress
        ));
        curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
        $response4 = curl_exec($ch3);
        $error3 = curl_error($ch3);
        $headers3 = curl_getinfo($ch3);
        curl_close($ch3);
        error_log("\r\n RESPONSE DEPOSIT  $response4 \r\n", 3, "/srv/www/htdocs/error_log");

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
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);


        $item['cancelpolicy'] = $cancelpolicy;
        $item['cancelpolicy_deadline'] = $cancelpolicy_deadline;
        //$item['cancelpolicy_deadlinetimestamp'] = $CancellationDeadline; 
        $item['cancelpolicy_details'] = $cancelpolicy;
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mexpedia where sid='" . $shid . "' and hid=" . $hid;
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