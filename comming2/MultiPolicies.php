<?php
error_log("\r\nMulti Policies RTS\r\n", 3, "/srv/www/htdocs/error_log");
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
$salestaxes = 0;
$salestaxesfees = 0;
$baserate = 0;
$affiliate_id_expedia = 0;
$occupancies = "";
$sindex = $index;
$db = new \Zend\Db\Adapter\Adapter($config);
if ($details == "hoteldetails") {
    // Detail level
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_coming2 where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_coming2 where session_id='$session_id'";
}
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_settings->buffer();
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
$branch_filter = '';
$sql = "select value from settings where name='enablecoming2' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_coming2 = $affiliate_id;
} else {
    $affiliate_id_coming2 = 0;
}
$sql = "select value from settings where name='coming2login' and affiliate_id=$affiliate_id_coming2" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2login = $row_settings['value'];
}
$sql = "select value from settings where name='coming2password' and affiliate_id=$affiliate_id_coming2" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2password = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='coming2Markup' and affiliate_id=$affiliate_id_coming2" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2Markup = (double) $row_settings['value'];
} else {
    $coming2Markup = 0;
}
$sql = "select value from settings where name='coming2ServiceURL' and affiliate_id=$affiliate_id_coming2" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2ServiceURL = $row_settings['value'];
}
$sql = "select value from settings where name='coming2Company' and affiliate_id=$affiliate_id_coming2" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2Company = $row_settings['value'];
}

$outputArray = array();
$arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
foreach ($arrIt as $sub) {
    $subArray = $arrIt->getSubIterator();
    if (isset($quoteid[$nroom])) {
        if (isset($subArray['quoteid'])) {
            if ($subArray['quoteid'] === $quoteid[$nroom]) {
                $outputArray[] = iterator_to_array($subArray);
                $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                    ->key();
            }
        }
    }
}
$breakdownTmp = array();
if (! is_array($outputArray)) {
    $response['error'] = "Unable to handle request #3";
    return false;
} else {
    array_push($breakdownTmp, $outputArray);
}
$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');
$c = $nroom;
$response = array();
$roombreakdown2 = array();
foreach ($breakdownTmp as $k => $v) {
    foreach ($v as $key => $value) {
        $shid = $value['shid'];
        $roomid = $value['roomid'];
        $adults = $value['adults'];
        $children = $value['children'];

        $from2 = strtotime($from);
        $item = array();
        $cancelation_deadline = 0;
        $cancelation_details = "";

        $raw = '{
            "Rooms": [
                {
                    "Id": ' . $roomid . ',
                    "Paxes": [';
                    for ($w = 0; $w < $adults; $w ++) {
                        if ($w > 0) {
                            $raw .= ',';
                        }
                        $raw .= '{ "PaxType": "Adult", "Age": 30, "GivenName": "", "SurName": "" }';
                    }
                    for ($w = 0; $w < $children; $w ++) {
                        $raw .= ',{ "PaxType": "Child", "Age": ' . $children_ages[$w] . ', "GivenName": "", "SurName": "" }';
                    }
        $raw .= ']
                }
            ]
        }';

        $passuser = "$coming2login:$coming2password";
        $auth = base64_encode($passuser);
        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Basic " . $auth,
            "Content-length: " . strlen($raw)
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $coming2ServiceURL . 'Booking/Check');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        curl_close($ch);
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_coming2');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $coming2ServiceURL,
                'sqlcontext' => $response2,
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
        $response2 = json_decode($response2, true);
        // Results
        $HotelCode = $response2['HotelCode'];
        $HotelName = $response2['HotelName'];
        $Language = $response2['Language'];
        $Market = $response2['Market'];
        $Customer = $response2['Customer'];
        $FromDate = $response2['FromDate'];
        $ToDate = $response2['ToDate'];
        $ConfirmationId = $response2['ConfirmationId'];
        $BookingReference = $response2['BookingReference'];
        $Rooms = $response2['Rooms'];
        for ($jAux3 = 0; $jAux3 < count($Rooms); $jAux3 ++) {
            $Id = $Rooms[$jAux3]['Id'];
            $CodeRooms = $Rooms[$jAux3]['Code'];
            $NameRooms = $Rooms[$jAux3]['Name'];
            $MealPlanCode = $Rooms[$jAux3]['MealPlanCode'];
            $MealPlanName = $Rooms[$jAux3]['MealPlanName'];
            $Status = $Rooms[$jAux3]['Status'];
            $Adults = $Rooms[$jAux3]['Adults'];
            $Childs = $Rooms[$jAux3]['Childs'];
            $Enfants = $Rooms[$jAux3]['Enfants'];
            $RateCode = $Rooms[$jAux3]['RateCode'];
            $RateName = $Rooms[$jAux3]['RateName'];
            $NonRefundable = $Rooms[$jAux3]['NonRefundable'];
            $Package = $Rooms[$jAux3]['Package'];
            $Senior = $Rooms[$jAux3]['Senior'];
            $Residents = $Rooms[$jAux3]['Residents'];
            $Remarks = $Rooms[$jAux3]['Remarks'];
            $Price = $Rooms[$jAux3]['Price'];
            if (count($Price) > 0) {
                $CurrencyCode = $Price['CurrencyCode'];
                $Amount = $Price['Amount'];
                $Commission = $Price['Commission'];
                $Binding = $Price['Binding'];
            } else {
                $CurrencyCode = "";
                $Amount = "";
                $Commission = "";
                $Binding = "";
            }
            $Paxes = $Rooms[$jAux3]['Paxes'];
            for ($j=0; $j < count($Paxes); $j++) { 
                $PaxType = $Paxes[$j]['PaxType'];
                $Age = $Paxes[$j]['Age'];
                $GivenName = $Paxes[$j]['GivenName'];
                $SurName = $Paxes[$j]['SurName'];
            }
            $currency = $CurrencyCode;
            $CancelPolicy = "";
            $cancelation_deadline = 0;
            $CancelPenalties = $Rooms[$jAux3]['CancelPenalties'];
            for ($iAux4 = 0; $iAux4 < count($CancelPenalties); $iAux4 ++) {
                if ($iAux4 > 0) {
                    $CancelPolicy .= "<br/>";
                }
                $HoursBefore = $CancelPenalties[$iAux4]['HoursBefore'];
                $Description = $CancelPenalties[$iAux4]['Description'];
                $Penalty = $CancelPenalties[$iAux4]['Penalty'];
                if (count($Penalty) > 0) {
                    $PenaltyType = $Penalty['PenaltyType'];
                    $CurrencyCode2 = $Penalty['CurrencyCode'];
                    $Value = $Penalty['Value'];
                    $IsNetPrice = $Penalty['IsNetPrice'];
                } else {
                    $PenaltyType = "";
                    $CurrencyCode2 = "";
                    $Value = "";
                    $IsNetPrice = "";
                }
                $offset = ($HoursBefore + 24) / 24;
                if ($cancelation_deadline == 0) {
                    $cancelation_deadline = mktime(0, 0, 0, date("m", $from2), date("d", $from2) + $offset, date("y", $from2));
                } else {
                    if ($cancelation_deadline > mktime(0, 0, 0, date("m", $from2), date("d", $from2) + $offset, date("y", $from2))) {
                        $cancelation_deadline = mktime(0, 0, 0, date("m", $from2), date("d", $from2) + $offset, date("y", $from2));
                    }
                }
                $CancelPolicy .= $translator->translate("Pay") . " " . $CurrencyCode2 . " " . $Value . " " . $translator->translate("if cancelled on or after") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from2), date("d", $from2) + $offset, date("y", $from2)));
            }
        }
        //
        // EOF Policies
        //
        // EOF Check prices & availability
        //
        $item['code'] = $value['shid'];
        $item['name'] = $value['name'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['nett'];
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['room_code'] = $value['room_code'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);

        if ($NonRefundable == "true") {
            $item['nonrefundable'] = true;
            $item['cancelpolicy'] = $translator->translate("This is a non refundable booking.") . "<br/>" . $CancelPolicy;
            $item['cancelpolicy_deadline'] = strftime("%a, %d %B %Y", $cancelation_deadline);
        } else {
            $item['nonrefundable'] = false;
            $item['cancelpolicy'] = $CancelPolicy;
            $item['cancelpolicy_deadline'] = strftime("%a, %d %B %Y", $cancelation_deadline);
        }
        
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mcoming2 where sid='" . $shid . "' and hid=" . $hid;
// error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
$row_hotel->buffer();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
// error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel->buffer();
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $row_country->buffer();
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
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
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
// error_log("\r\n" . print_r($responseContent, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
$hotel['checkin'] = $responseContent[$shid]['checkin'];
$hotel['fees'] = $responseContent[$shid]['fees'];
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown2;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['sales_taxes'] = $filter->filter($salestaxes);
$response['sales_taxesplain'] = number_format($salestaxes, 2, '.', '');
$response['taxes'] = $filter->filter($salestaxesfees);
$response['taxesplain'] = number_format($salestaxesfees, 2, '.', '');
$response['base_rate'] = $filter->filter($baserate);
$response['base_rateplain'] = number_format($baserate, 2, '.', '');
$response['occupancies'] = json_encode($occupancies);
$response['searchsettings'] = $searchsettings;
$response['ean'] = 1;
$response['eanbookhref'] = $href;
//
// Store Session
//
$sql = new Sql($db);
$sql = "delete from quote_session_hotel_multipolicies where session_id='" . $session_id . "' and sindex=$sindex";
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('quote_session_hotel_multipolicies');
$insert->values(array(
    'session_id' => $session_id,
    'sindex' => $sindex,
    'data' => base64_encode(serialize($response)),
    'searchsettings' => base64_encode(serialize($searchsettings))
), $insert::VALUES_MERGE);
try {
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['breakdown'] = $roombreakdown;
?>