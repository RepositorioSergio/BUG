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
$sindex = $index;
$shid = 0;
$dbPalladium = new \Zend\Db\Adapter\Adapter($config);
if ($details == "hoteldetails") {
    // Detail level
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_palladium where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_palladium where session_id='$session_id'";
}
try {
    $statement = $dbPalladium->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (\Exception $e) {
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
$sql = "select value from settings where name='enablepalladium' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $dbPalladium->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_palladium = $affiliate_id;
} else {
    $affiliate_id_palladium = 0;
}
$sql = "select value from settings where name='PalladiumHotelGroupusername' and affiliate_id=$affiliate_id_palladium";
$statement = $dbPalladium->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PalladiumHotelGroupusername = $row_settings['value'];
}
$sql = "select value from settings where name='PalladiumHotelGroupmarkup' and affiliate_id=$affiliate_id_palladium";
$statement = $dbPalladium->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PalladiumHotelGroupmarkup = (double) $row_settings['value'];
} else {
    $PalladiumHotelGroupmarkup = 0;
}
$sql = "select value from settings where name='PalladiumHotelGroupserviceurl' and affiliate_id=$affiliate_id_palladium";
$statement = $dbPalladium->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PalladiumHotelGroupserviceurl = $row_settings['value'];
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
$fromPalladium = DateTime::createFromFormat("d-m-Y", $from);
$toPalladium = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromPalladium->diff($toPalladium);
$nights = $nights->format('%a');
$fromPalladium = $fromPalladium->getTimestamp();
$toPalladium = $toPalladium->getTimestamp();
$c = $nroom;
$response = array();
$roombreakdown2 = array();
foreach ($breakdownTmp as $k => $v) {
    foreach ($v as $key => $value) {
        $shid = $value['shid'];
        $scode = $value['shid'];
        $RoomTypeCode = $value['RoomTypeCode'];
        $RatePlanCode = $value['RatePlanCode'];
        $RoomType = $value['RoomType'];
        $uniqid = md5(uniqid(rand(), true));
        $xml = '<?xml version="1.0" encoding="UTF-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:clo="http://www.cloudhospitality.com" xmlns:ns="http://www.opentravel.org/OTA/2003/05"><soap:Header/><soap:Body><clo:CreateReservation><ns:CreateReservationRequest PrimaryLangID="?" ResStatus="quote"><ns:POS><ns:Source><ns:RequestorID ID="' . $PalladiumHotelGroupusername . '" Type="13"/><ns:BookingChannel Type="2"/></ns:Source></ns:POS><ns:HotelReservations><ns:HotelReservation><ns:UniqueID ID="' . $uniqid . '" Type=""/><ns:RoomStays>';
        $rCount = 1;
        for ($r = 0; $r < count($selectedAdults); $r ++) {
            $xml .= '<ns:RoomStay RoomStayCandidateRPH="' . $rCount . '"><ns:RoomTypes><ns:RoomType RoomTypeCode="' . $RoomTypeCode . '" RoomType="' . $RoomType . '" NumberOfUnits="1"></ns:RoomType></ns:RoomTypes><ns:RatePlans><ns:RatePlan RatePlanCode="' . $RatePlanCode . '"></ns:RatePlan></ns:RatePlans><ns:GuestCounts>';
            $xml .= '<ns:GuestCount AgeQualifyingCode="10" Age="30" Count="' . $selectedAdults[$r] . '"/>';
            for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                if ($selectedChildrenAges[$r][$z] <= 2) {
                    $xml .= '<ns:GuestCount Count="1" AgeQualifyingCode="7" Age="' . $selectedChildrenAges[$r][$z] . '"/>';
                } else {
                    $xml .= '<ns:GuestCount Count="1" AgeQualifyingCode="8" Age="' . $selectedChildrenAges[$r][$z] . '"/>';
                }
            }
            $xml .= '</ns:GuestCounts><ns:TimeSpan Start="' . strftime("%Y-%m-%d", $fromPalladium) . '" End="' . strftime("%Y-%m-%d", $toPalladium) . '"/><ns:BasicPropertyInfo HotelCode="' . $shid . '" AreaID="" /></ns:RoomStay>';
            $rCount ++;
        }
        $xml .= '</ns:RoomStays><ns:ResGuests><ns:ResGuest><ns:Profiles><ns:ProfileInfo><ns:Profile><ns:Customer><ns:PersonName><ns:GivenName>?</ns:GivenName><ns:Surname>?</ns:Surname></ns:PersonName></ns:Customer></ns:Profile></ns:ProfileInfo></ns:Profiles></ns:ResGuest></ns:ResGuests><ns:ResGlobalInfo><ns:HotelReservationIDs><ns:HotelReservationID ResID_Type="8" ResID_Value="0"/></ns:HotelReservationIDs></ns:ResGlobalInfo></ns:HotelReservation></ns:HotelReservations></ns:CreateReservationRequest></clo:CreateReservation></soap:Body></soap:Envelope>';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $PalladiumHotelGroupserviceurl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/xml",
            "Content-type: text/xml",
            "Content-length: " . strlen($xml)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xmlresult = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        try {
            $sql = new Sql($dbPalladium);
            $insert = $sql->insert();
            $insert->into('log_palladium');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => 0,
                'errormessage' => $xml,
                'sqlcontext' => $xmlresult,
                'errcontext' => ''
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        //
        // Policies
        //
        $item = array();
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        $item['tax'] = "";
        $item['taxplain'] = "";
        $item['subtotal'] = $filter->filter(floatval($tot));
        $item['total'] = $filter->filter($tot);
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        $item['cancelpolicy'] = $value['cancelpolicy'];
        $item['cancelpolicy_deadlinetimestamp'] = $value['cancelpolicy_deadline'];
        $item['cancelpolicy_deadline'] = strftime("%a, %d %B %Y", $value['cancelpolicy_deadline']);
        array_push($roombreakdown2, $item);
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mpalladium where sid='" . $shid . "' and hid=" . $hid;
$statement = $dbPalladium->createStatement($sql);
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
$statement = $dbPalladium->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
    $row_hotel->buffer();
} catch (\Exception $e) {
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
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $dbPalladium->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
        $row_country->buffer();
    } catch (\Exception $e) {
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
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $dbPalladium->createStatement($sql);
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
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown2;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
// Store Session
$sql = new Sql($dbPalladium);
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
$sql = new Sql($dbPalladium);
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
$dbPalladium->getDriver()
    ->getConnection()
    ->disconnect();
?>