<?php
error_log("\r\nMulti Policies TRAVCO \r\n", 3, "/srv/www/htdocs/error_log");
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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_travco where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_travco where session_id='$session_id'";
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
    if ($details == "hoteldetails") {
        $selectedAdults = array();
        $selectedAdults[$nroom] = $adt;
        // Children + Ages
        $selectedChildrenAges = array();
        $selectedChildren = array();
        $selectedChildren[$nroom] = $chd;
        if ($chd > 0) {
            $children_ages = explode(",", $children_ages);
            for ($w = 0; $w < count($children_ages); $w ++) {
                $selectedChildrenAges[$nroom][$w] = $children_ages[$w];
            }
        }
    }
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$affiliate_id = 0;
$branch_filter = '';
$sql = "select value from settings where name='enabletravco' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_travco = $affiliate_id;
} else {
    $affiliate_id_travco = 0;
}
$sql = "select value from settings where name='TravcoAgentCode' and affiliate_id=$affiliate_id_travco" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravcoAgentCode = $row_settings['value'];
}
$sql = "select value from settings where name='TravcoAgentPassword' and affiliate_id=$affiliate_id_travco" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravcoAgentPassword = base64_decode($row_settings['value']);
}

$sql = "select value from settings where name='TravcoMarkup' and affiliate_id=$affiliate_id_travco" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravcoMarkup = (double) $row_settings['value'];
} else {
    $TravcoMarkup = 0;
}
$sql = "select value from settings where name='TravcoServiceURL' and affiliate_id=$affiliate_id_travco" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravcoServiceURL = $row_settings['value'];
}
$sql = "select value from settings where name='TravcoTimeout' and affiliate_id=$affiliate_id_travco" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravcoTimeout = (int) $row_settings['value'];
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
        $code = $value['hotelid'];
        $scode = $value['shid'];
        $HotelId = $value['hotelid'];
        $room_code = $value['roomid'];
        
        $from_date = date('d/M/Y', strtotime($from));
        $to_date = date('d/M/Y', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        $ccharges_code = $value['CCHARGES_CODE'];
        $ccharges_overlap_code = "";
        
        $xml = 'XMLString=<?xml version=\'1.0\' encoding=\'UTF-8\'?><BOOKING type=\'HCAD\' lang=\'en-GB\' returnURLNeed=\'no\' returnURL=\'http://\' AGENTCODE=\'' . $TravcoAgentCode . '\' AGENTPASSWORD=\'' . $TravcoAgentPassword . '\' xmlns:xsi=\'http://www.w3.org/2001/XMLSchema-instance\' xsi:noNamespaceSchemaLocation=\'http://xmlv5test.travco.co.uk/trlink/schema/HotelCancellationDetailsV6Snd.xsd\'><HEADER><INTERNAL_CODE1>DWEBCN</INTERNAL_CODE1><INTERNAL_CODE2>ENQCANX</INTERNAL_CODE2><INTERNAL_CODE3>VB</INTERNAL_CODE3></HEADER><DATA HOTEL_CODE=\'' . $shid . '\' CHECK_IN_DATE=\'' . $from_date . '\'	DURATION=\'' . $nights . '\' CCHARGES_CODE=\'' . $ccharges_code . '\' CCHARGES_OVERLAP_CODE=\'' . $ccharges_overlap_code . '\' /></BOOKING>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $TravcoServiceURL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept-Encoding: gzip',
            'Host:xmlv6.travco.co.uk',
            "Content-length: " . strlen($xml)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultxml = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

        error_log("\r\n Response: $resultxml \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_travco');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $TravcoServiceURL,
                'sqlcontext' => $resultxml,
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

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($resultxml);
        if ($inputDoc != NULL) {
            $responseElement = $inputDoc->documentElement;
            $xpath = new DOMXPath($inputDoc);
            $cancellationDetailsReponseElements = "";
            $cancellationDetailsReponseElements = $xpath->query('/RETURNDATA/FULL_CANCELLATION_DETAILS', $responseElement);
            foreach ($cancellationDetailsReponseElements as $cancellationDetailsReponseElement) {
                $HOTEL_CODE = $cancellationDetailsReponseElement->getAttribute("HOTEL_CODE");
                $DURATION = $cancellationDetailsReponseElement->getAttribute("DURATION");
                $CHECK_IN_DATE = $cancellationDetailsReponseElement->getAttribute("CHECK_IN_DATE");
                $CCHARGES_CODE = $cancellationDetailsReponseElement->getAttribute("CCHARGES_CODE");
                $CCHARGES_OVERLAP_CODE = $cancellationDetailsReponseElement->getAttribute("CCHARGES_OVERLAP_CODE");
                $DETAILS = $cancellationDetailsReponseElement->getElementsByTagName("DETAILS");
                if ($DETAILS->length > 0) {
                    $TIME_AFTER = $DETAILS->item(0)->getAttribute("TIME_AFTER");
                    $NO_OF_DAYS_BEFORE_ARRIVAL = $DETAILS->item(0)->getAttribute("NO_OF_DAYS_BEFORE_ARRIVAL");
                    $EFFECTIVE_FROM = $DETAILS->item(0)->getAttribute("EFFECTIVE_FROM");
                    $FULL_CANCELLATION_POLICY = $DETAILS->item(0)->getElementsByTagName("FULL_CANCELLATION_POLICY");
                    if ($FULL_CANCELLATION_POLICY->length > 0) {
                        $FULL_CANCELLATION_POLICY = $FULL_CANCELLATION_POLICY->item(0)->nodeValue;
                    } else {
                        $FULL_CANCELLATION_POLICY = "";
                    }
                    $MESSAGE = $DETAILS->item(0)->getElementsByTagName("MESSAGE");
                    if ($MESSAGE->length > 0) {
                        $TIME_BEFORE = $MESSAGE->item(0)->getAttribute("TIME_BEFORE");
                        $LAST_DATE_BY_WHICH_TO_CANCEL = $MESSAGE->item(0)->getAttribute("LAST_DATE_BY_WHICH_TO_CANCEL");
                    }
                }
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
        
        $cancelation_details = $FULL_CANCELLATION_POLICY;
        $item['cancelpolicy'] = $cancelation_details;
        $item['cancelpolicy_details'] = $cancelation_details;
        $item['cancelpolicy_deadline'] = $LAST_DATE_BY_WHICH_TO_CANCEL;
        
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mtravco where sid='" . $shid . "' and hid=" . $hid;
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
error_log("\r\nRTS Policies Multi - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>