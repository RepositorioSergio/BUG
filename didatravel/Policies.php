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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_didatravel where session_id='$session_id'";
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
$sql = "select value from settings where name='enabledidatravel' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $affiliate_id_didatravel = $affiliate_id;
} else {
    $affiliate_id_didatravel = 0;
}

$sql = "select value from settings where name='didatravelclientid' and affiliate_id=$affiliate_id_didatravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravelclientid = $row_settings['value'];
}
error_log("\r\n didatravelclientid $didatravelclientid \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='didatravellicensekey' and affiliate_id=$affiliate_id_didatravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravellicensekey = $row_settings['value'];
}
error_log("\r\n didatravellicensekey $didatravellicensekey \r\n", 3, "/srv/www/htdocs/error_log");

 $sql = "select value from settings where name='didatravelMarkup' and affiliate_id=$affiliate_id_didatravel" . $branch_filter;
 $statement = $db->createStatement($sql);
  $statement->prepare();
  $row_settings = $statement->execute();
  $row_settings->buffer();
  if ($row_settings->valid()) {
  $row_settings = $row_settings->current();
  $didatravelMarkup = (double) $row_settings['value'];
 } else {
  $didatravelMarkup = 0;
 }
 
// URL
$sql = "select value from settings where name='didatravelserviceurl' and affiliate_id=$affiliate_id_didatravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravelserviceurl = $row_settings['value'];
}
error_log("\r\n didatravelserviceurl $didatravelserviceurl \r\n", 3, "/srv/www/htdocs/error_log");

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



error_log("\r\n PASSOU XML $nights \r\n", 3, "/srv/www/htdocs/error_log");

/*
 * $fromHotelsPRO = $fromHotelsPRO->getTimestamp();
 * $toHotelsPro = $toHotelsPro->getTimestamp();
 */
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
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }

        $from_date = date('Y-m-d' , strtotime($from));
        $to_date = date('Y-m-d' , strtotime($to));

        $raw = '
        {
            "PreBook": true,
            "CheckOutDate": "' . $to_date . '",
            "CheckInDate": "' . $from_date . '",
            "NumOfRooms": ' . $rooms . ',
            "HotelID": ' . $HotelId . ',
            "Header": {
                "LicenseKey": "' . $didatravellicensekey . '",
                "ClientID": "' . $didatravelclientid . '"
            },
            "OccupancyDetails": [';
            for ($i=0; $i < count($selectedAdults); $i++) { 
                $raw .= '{';
                $raw .= '"ChildCount": ' . $selectedChildren[$i] . ',
                        "AdultCount": ' . $selectedAdults[$i] . ',
                        "RoomNum": ' . ($i + 1) . ',
                        ';
                if ($selectedChildren[$i] > 0) {
                    $count = count($selectedChildren[$i]);
                    $raw .= '"ChildAgeDetails": ['; 
                    for ($j=0; $j < $selectedChildren[$i]; $j++) { 
                        $raw .= $selectedChildrenAges[$i][$j];
                        if ($j < $count - 1) {
                            $raw .= ',';
                        }
                    }
                    $raw .= ']';
                }else {
                    $raw .= '"ChildAgeDetails": []';
                }
                if ($i < count($selectedAdults) - 1) {
                    $raw .= '},';
                }else {
                    $raw .= '}';
                }
            }

        $raw .= '
            ],
            "Currency": "CNY",
            "Nationality": "CN",
            "RatePlanID": "' . $value['RatePlanID'] . '"
        }
        ';
        error_log("\r\n RAW $raw \r\n", 3, "/srv/www/htdocs/error_log");

        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";
        //
        // Policies
        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $didatravelserviceurl . "api/rate/PriceConfirm?\$format=json");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        // error_log("\r\nResponse Policies: $result . \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\nCode: $code \r\n", 3, "/srv/www/htdocs/error_log");
        $response = json_decode($response, true);
         error_log("\r\nVECTOR RESULT1 " . print_r($response, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        // $result1 = $arrayResponse['results'];
        $vector = array();
        // error_log("\r\nVECTOR COMECA DE NOVO \r\n", 3, "/srv/www/htdocs/error_log");
        
        $vector['Success'] = $response['Success'];
        $Success = $response['Success'];
        $PriceDetails = $Success['PriceDetails'];
        $ReferenceNo = $PriceDetails['ReferenceNo'];
        $vector['ReferenceNo'] = $PriceDetails['ReferenceNo'];
        $HotelList = $PriceDetails['HotelList'];
        foreach ($HotelList as $key => $valueHotelList) {
            $HotelID = $valueHotelList['HotelID'];
            $Destination = $valueHotelList['Destination'];
            $CityCode = $Destination['CityCode'];
            $HotelName = $valueHotelList['HotelName'];
            $TotalPriceWithoutSupplement = $valueHotelList['TotalPriceWithoutSupplement'];
            $TotalSupplement = $valueHotelList['TotalSupplement'];
            $TotalPrice = $valueHotelList['TotalPrice'];

            $countPolicy = 0;
            $CancellationPolicyArray = array();
            $CancellationPolicyList = $valueHotelList['CancellationPolicyList'];
            foreach ($CancellationPolicyList as $key => $valueCancellationPolicyList) {
                $Amount = $valueCancellationPolicyList['Amount'];
                $FromDate = $valueCancellationPolicyList['FromDate'];

                $CancellationPolicyArray[$countPolicy]['Amount'] = $Amount;
                $CancellationPolicyArray[$countPolicy]['FromDate'] = $FromDate;
                $countPolicy = $countPolicy + 1;
            }

            $RatePlanList = $valueHotelList['RatePlanList'];
            foreach ($RatePlanList as $key => $valueRatePlanList) {
                $TotalPrice = $valueRatePlanList['TotalPrice'];
                $RoomStatus = $valueRatePlanList['RoomStatus'];
                $BreakfastType = $valueRatePlanList['BreakfastType'];
                $BedType = $valueRatePlanList['BedType'];
                $RoomOccupancy = $valueRatePlanList['RoomOccupancy'];
                foreach ($RoomOccupancy as $key => $valueRoomOccupancy) {
                    $ChildCount = $valueRoomOccupancy['ChildCount'];
                    $AdultCount = $valueRoomOccupancy['AdultCount'];
                    $AdultCount = $valueRoomOccupancy['AdultCount'];
                }

                $StandardOccupancy = $valueRatePlanList['StandardOccupancy'];
                $PriceWithoutSupplement = $valueRatePlanList['PriceWithoutSupplement'];
                $Supplement = $valueRatePlanList['Supplement'];
                $InventoryCount = $valueRatePlanList['InventoryCount'];
                $MaxOccupancy = $valueRatePlanList['MaxOccupancy'];
                $Currency = $valueRatePlanList['Currency'];
                $RatePlanName = $valueRatePlanList['RatePlanName'];
                $RatePlanID = $valueRatePlanList['RatePlanID'];

                $PriceList = $valueRatePlanList['PriceList'];
                foreach ($PriceList as $key => $valuePriceList) {
                     $StayDate = $valuePriceList['StayDate'];
                     $Price = $valuePriceList['Price'];
                }


            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < $nights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) + $rZZ, date("y", $fromHotelsPRO)));
                $amount = $TotalPrice / $nights;
                if ($didatravelMarkup != 0) {
                    $amount = $amount + (($amount * $didatravelMarkup) / 100);
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
                if ($didatravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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

            $novotmp['room'] = $RatePlanName;
            $novotmp['shid'] = $shid;
            $novotmp['code'] = $shid;
            $novotmp['RoomStatus'] = $RoomStatus;
            $novotmp['room_description'] = $RatePlanName;
            $novotmp['RatePlanID'] = $RatePlanID;
            $novotmp['room_type'] = $RoomStatus;
            $novotmp['BreakfastType'] = $BreakfastType;
            $novotmp['BedType'] = $BedType;
            $novotmp['StandardOccupancy'] = $StandardOccupancy;
            $novotmp['PriceWithoutSupplement'] = $PriceWithoutSupplement;
            $novotmp['InventoryCount'] = $InventoryCount;
            $novotmp['MaxOccupancy'] = $MaxOccupancy;
            $novotmp['recommended'] = false;
            $novotmp['scurrency'] = $currency;
            $novotmp['currency'] = $currency;
            $novotmp['CancellationPolicyList'] = $CancellationPolicyArray;
            $novotmp['pricebreakdown'] = $pricebreakdown;
        }
        }
        $vector['HotelList'] = $novotmp;
        error_log("\r\nNOVO VECTOR: " . print_r($vector, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db2);
            $insert = $sql->insert();
            $insert->into('log_didatravel');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $didatravelserviceurl . $raw,
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

        $$cancelation_string  = "";
        $novovector = $vector['HotelList'];
        $room = $novovector['room'];
        error_log("\r\n ENTROU NOVO VECTOR $room \r\n", 3, "/srv/www/htdocs/error_log");
        $cancelPolicy = $novovector['CancellationPolicyList'];
        foreach ($cancelPolicy as $key => $valuecancelPolicy) {
            $cancelation_string .= "Charge " . $valuecancelPolicy['Amount'] . " if cancelled on and after ";
            error_log("\r\n cancelation_string2  $cancelation_string \r\n", 3, "/srv/www/htdocs/error_log");
            $cancelation_string .= $valuecancelPolicy['FromDate'] . "<br/><br/>";
            $cancelation_deadline = $valuecancelPolicy['FromDate'];
        }

        error_log("\r\n cancelation_string  $cancelation_string \r\n", 3, "/srv/www/htdocs/error_log");

        $item['cancelpolicy'] = $cancelation_string;
        $item['cancelpolicy_deadline'] = date('Y-m-d' , strtotime($cancelation_deadline));
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        /*$item['cancelpolicy_details'] = $cancelation_details; */
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mdidatravel where sid='" . $shid . "' and hid=" . $hid;
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