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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_jactravel where session_id='$session_id'";
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
$sql = "select value from settings where name='enablejactravel' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_jactravel = $affiliate_id;
} else {
    $affiliate_id_jactravel = 0;
}

error_log("\r\n PASSOU ENABLE \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='JacTravelClientName' and affiliate_id=$affiliate_id_jactravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelClientName = $row_settings['value'];
}

$sql = "select value from settings where name='JacTravelKey' and affiliate_id=$affiliate_id_jactravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelKey = $row_settings['value'];
}

$sql = "select value from settings where name='JacTravelMarkup' and affiliate_id=$affiliate_id_jactravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelMarkup = (double) $row_settings['value'];
} else {
    $JacTravelMarkup = 0;
}

$sql = "select value from settings where name='JacTravelSearchServiceURLServiceSearch' and affiliate_id=$affiliate_id_jactravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelSearchServiceURLServiceSearch = $row_settings['value'];
}

$sql = "select value from settings where name='JacTravelAvailability' and affiliate_id=$affiliate_id_jactravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelAvailability = $row_settings['value'];
}

$sql = "select value from settings where name='JacTravelcurrencycode' and affiliate_id=$affiliate_id_jactravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelcurrencycode = $row_settings['value'];
}
error_log("\r\n ENDPOINT $JacTravelSearchServiceURLServiceSearch \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='JacTravelSearchServiceURLAvailabilityAndPrices' and affiliate_id=$affiliate_id_jactravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelSearchServiceURLAvailabilityAndPrices = $row_settings['value'];
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
error_log("\r\n PASSOU BREAK \r\n", 3, "/srv/www/htdocs/error_log");
/*
 * $fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
 * $toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
 * $nights = $fromHotelsPRO->diff($toHotelsPro);
 * $nights = $nights->format('%a');
 */

error_log("\r\n PASSOU NIGHTS \r\n", 3, "/srv/www/htdocs/error_log");
$requestData = '<HOTEL_AVAILABILITY_AND_PRICE_SEARCH_CRITERIA><VERSION_HISTORY APPLICATION_NAME="TS  HOTELS API 1.0" XML_FILE_NAME="XMLFileName" LICENCE_KEY="' . $JacTravelKey . '" TS_API_VERSION="v309.0.0"><XML_VERSION_NO>3.0</XML_VERSION_NO></VERSION_HISTORY><SERVICE_ID AVAILABLE_ONLY="' . $JacTravelAvailability . '">' . $selectedRows[0]['SERVICE_ID'] . '</SERVICE_ID><CLIENT_NAME>' . $JacTravelClientName . '</CLIENT_NAME><BOOKING_START_DATE>' . strftime("%d %b %y", $from) . '</BOOKING_START_DATE><BOOKING_END_DATE>' . strftime("%d %b %y", mktime(0, 0, 0, date("m", $to), date("d", $to) - 1, date("Y", $to))) . '</BOOKING_END_DATE><ROOM_REPLY><ALL_ROOM/></ROOM_REPLY><ROOMS_REQUIRED>';
            for ($x = 0; $x < count($selectedAdults); $x ++) {
                $requestData .= '<ROOM><OCCUPANCY>' . $selectedRows[$x]['OCCUPANCY'] . '</OCCUPANCY><QUANTITY>1</QUANTITY>';
                if ($selectedChildren[$x] > 0) {
                    $requestData = $requestData . '<NO_OF_PASSENGERS>' . $selectedAdults[$x] . '</NO_OF_PASSENGERS><NO_OF_CHILDREN>' . $selectedChildren[$x] . '</NO_OF_CHILDREN>';
                    $agesarray = "";
                    for ($kA = 0; $kA < count($selectedChildrenAges[$x]); $kA ++) {
                        if ($agesarray == "") {
                            $agesarray = $selectedChildrenAges[$x][$kA];
                        } else {
                            $agesarray = $agesarray . "," . $selectedChildrenAges[$x][$kA];
                        }
                    }
                    $requestData = $requestData . '<AGES_OF_CHILDREN>' . $agesarray . '</AGES_OF_CHILDREN>';
                } else {
                    if ($selectedRows[$x]['OCCUPANCY'] == 7 or $selectedRows[$x]['OCCUPANCY'] == 8) {
                        $requestData = $requestData . '<NO_OF_PASSENGERS>' . $selectedAdults[$x] . '</NO_OF_PASSENGERS><NO_OF_CHILDREN>' . $selectedChildren[$x] . '</NO_OF_CHILDREN><AGES_OF_CHILDREN>0</AGES_OF_CHILDREN>';
                    }
                }
                $requestData .= '</ROOM>';
            }
            $requestData .= '</ROOMS_REQUIRED></HOTEL_AVAILABILITY_AND_PRICE_SEARCH_CRITERIA>';
error_log("\r\n PASSOU XML \r\n", 3, "/srv/www/htdocs/error_log");

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
        $cancelation_deadline = 0;
        $cancelation_details = "";
        error_log("\r\n ANTES CURL \r\n", 3, "/srv/www/htdocs/error_log");
        
        /*
         * $ch = curl_init();
         * curl_setopt($ch, CURLOPT_URL, $PalladiumHotelGroupserviceurl);
         * curl_setopt($ch, CURLOPT_HEADER, false);
         * curl_setopt($ch, CURLOPT_POST, true);
         * curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
         * curl_setopt($ch, CURLOPT_VERBOSE, 1);
         * curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
         * curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         * curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
         * curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         * "Accept: application/xml",
         * "Content-type: text/xml",
         * "Content-length: " . strlen($xml)
         * ));
         * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         * $response = curl_exec($ch);
         * $error = curl_error($ch);
         * $headers = curl_getinfo($ch);
         * curl_close($ch);
         * error_log("\r\n RESPONSE PAL6: $response \r\n", 3, "/srv/www/htdocs/error_log");
         */
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
        $item['RoomTypeCode'] = $value['RoomTypeCode'];
        $item['RoomDescription'] = $value['RoomDescription'];
        $item['RateCode'] = $value['RateCode'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        /*$item['cancelpolicy'] = str_replace("<p><br /></p>", "", $value['cancelpolicy']);
        
         * $item['cancelpolicy_deadline'] = strftime("%d-%m-%Y", $cancelation_deadline);
         * $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
         * $item['cancelpolicy_details'] = $cancelation_details;
         */
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mtotalstay where sid='" . $shid . "' and hid=" . $hid;
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