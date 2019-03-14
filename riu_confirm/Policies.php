<?php
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_travelplan where session_id='$session_id'";
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
$sql = "select value from settings where name='enabletravelplan' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $affiliate_id_travelplan = $affiliate_id;
} else {
    $affiliate_id_travelplan = 0;
}

$sql = "select value from settings where name='TravelPlanuser' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanuser = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanpassword' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='TravelPlanMarkup' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanMarkup = (double) $row_settings['value'];
} else {
    $TravelPlanMarkup = 0;
}
$sql = "select value from settings where name='TravelPlanserviceURL' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanserviceURL = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSystem' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSystem = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSalesChannel' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSalesChannel = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanlanguage' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanlanguage = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanConnectionString' and affiliate_id=$affiliate_id_travelplan". $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanConnectionString = $row_settings['value'];
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

$fromHotelbeds = DateTime::createFromFormat("d-m-Y", $from);
$toHotelbeds = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelbeds->diff($toHotelbeds);
$nights = $nights->format('%R%a');
$fromHotelbeds = $fromHotelbeds->getTimestamp();
$toHotelbeds = $toHotelbeds->getTimestamp();
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }

        $item = array();
        $total = $total + $value['total'];
        $item['name'] = $value['name'];
        $item['room'] = $value['room'];
        $item['RoomTypeCode'] = $value['RoomTypeCode'];
        $item['RoomDescription'] = $value['RoomDescription'];
        $item['NonRefundable'] = $value['NonRefundable'];
        $item['meal'] = $value['meal'];
        if ($tax > 0) {
            $tot = $value['total'] - floatval($tax);
            $item['subtotal'] = $filter->filter(floatval($tot));
            $item['tax'] = $filter->filter(floatval($tax));
        } else {
            $item['tax'] = "";
            $tot = $value['total'];
            $item['subtotal'] = $filter->filter(floatval($tot));
        }
        $item['total'] = $filter->filter($tot);
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        // Get Cancellation Policies
        // $client = new SoapClient($touricoholidayserviceurl, array(
        // 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        // "trace" => 1,
        // "exceptions" => 0,
        // 'soap_version' => SOAP_1_1
        // ));
        // $ns = 'http://schemas.tourico.com/webservices/authentication';
        // $dataAuth = array(
        // 'LoginName' => new SoapVar($touricoholidayslogin, XSD_STRING, null, null, null, $ns),
        // 'Password' => new SoapVar($touricoholidayspassword, XSD_STRING, null, null, null, $ns),
        // 'Culture' => new SoapVar($Culture, XSD_STRING, null, null, null, $ns),
        // 'Version' => new SoapVar($Version, XSD_STRING, null, null, null, $ns)
        // );
        // $hBody = new SoapVar($dataAuth, SOAP_ENC_OBJECT);
        // $header = new SoapHeader($ns, 'AuthenticationHeader', $hBody);
        // $client->__setSoapHeaders(array(
        // $header
        // ));
        // $hotelRoomTypeId_array[$k] = $selectedRows[$k]['QuoteId'];
        // $occupId_array[$k] = $selectedRows[$k]['RoomTypecode'];
        // $bbId_array[$k] = $selectedRows[$k]['bedTypeId'];
        // $params = array();
        // $params['nResId'] = "0";
        // $params['hotelId'] = $shid;
        // $params['hotelRoomTypeId'] = $value['hrtid'];
        // $params['productId'] = $value['pid'];
        // $params['dtCheckIn'] = strftime("%Y-%m-%d", $fromHotelbeds);
        // $params['dtCheckOut'] = strftime("%Y-%m-%d", $toHotelbeds);
        // $ns = "http://schemas.tourico.com/webservices/hotelv3";
        // try {
        // $client->__soapCall('GetCancellationPolicies', array(
        // $params
        // ), array(
        // 'uri' => '"http://tourico.com/webservices/hotelv3'
        // ));
        // } catch (Exception $e) {
        // try {
        // $db2 = new \Zend\Db\Adapter\Adapter($config);
        // $sql = new Sql($db2);
        // $insert = $sql->insert();
        // $insert->into('log_tourico');
        // $insert->values(array(
        // 'datetime_created' => time(),
        // 'filename' => 'Policies.php',
        // 'errorline' => 0,
        // 'errormessage' => "Exception",
        // 'sqlcontext' => $e->getMessage(),
        // 'errcontext' => ''
        // ), $insert::VALUES_MERGE);
        // $statement = $sql->prepareStatementForSqlObject($insert);
        // $results = $statement->execute();
        // $db2->getDriver()
        // ->getConnection()
        // ->disconnect();
        // } catch (Exception $e) {
        // $logger = new Logger();
        // $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        // $logger->addWriter($writer);
        // $logger->info($e->getMessage());
        // }
        // }
        // $xmlresult = $client->__getLastRequest();
        // $xmlresult = $client->__getLastRequest();
        // try {
        // $db2 = new \Zend\Db\Adapter\Adapter($config);
        // $sql = new Sql($db2);
        // $insert = $sql->insert();
        // $insert->into('log_tourico');
        // $insert->values(array(
        // 'datetime_created' => time(),
        // 'filename' => 'Policies.php',
        // 'errorline' => 0,
        // 'errormessage' => $xmlresult,
        // 'sqlcontext' => $xmlresult,
        // 'errcontext' => ''
        // ), $insert::VALUES_MERGE);
        // $statement = $sql->prepareStatementForSqlObject($insert);
        // $results = $statement->execute();
        // $db2->getDriver()
        // ->getConnection()
        // ->disconnect();
        // } catch (Exception $e) {
        // $logger = new Logger();
        // $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        // $logger->addWriter($writer);
        // $logger->info($e->getMessage());
        // }
        // $cancelation_string = "";
        // $inputDoc = new DOMDocument();
        // $inputDoc->loadXML($client->__getLastResponse());
        // $node = $inputDoc->getElementsByTagName("CancelPolicy");
        // if ($node->length > 0) {
        // $node = $inputDoc->getElementsByTagName("CancelPenalty");
        // if ($node->length > 0) {
        // for ($cCancelPenalty = 0; $cCancelPenalty < $node->length; $cCancelPenalty ++) {
        // $Deadline = $node->item($cCancelPenalty)->getElementsByTagName("Deadline");
        // if ($Deadline->length > 0) {
        // $OffsetUnitMultiplier = $Deadline->item(0)->getAttribute("OffsetUnitMultiplier");
        // $OffsetTimeUnit = $Deadline->item(0)->getAttribute("OffsetTimeUnit");
        // if ($OffsetUnitMultiplier > 24) {
        // if (strtolower($OffsetTimeUnit) == "hour") {
        // $OffsetTimeUnit = $translator->translate("days");
        // $OffsetUnitMultiplier = $OffsetUnitMultiplier / 24;
        // }
        // }
        // $OffsetDropTime = $Deadline->item(0)->getAttribute("OffsetDropTime");
        // }
        // $AmountPercent = $node->item($cCancelPenalty)->getElementsByTagName("AmountPercent");
        // if ($AmountPercent->length > 0) {
        // $NmbrOfNights = $AmountPercent->item(0)->getAttribute("NmbrOfNights");
        // $BasisType = $AmountPercent->item(0)->getAttribute("BasisType");
        // }
        // if ($cancelation_string != "") {
        // $cancelation_string = $cancelation_string . "<br/><br/>";
        // }
        // if ($OffsetDropTime == "BeforeArrival") {
        // $OffsetDropTime2 = $translator->translate("before checkin date");
        // } else {
        // $OffsetDropTime2 = $translator->translate("after booking is made");
        // }
        // if ($BasisType == "Nights") {
        // if ($NmbrOfNights == 1) {
        // $BasisType = $translator->translate("night");
        // } else {
        // $BasisType = $translator->translate("nights");
        // }
        // }
        // if ($OffsetDropTime == "BeforeArrival" and $OffsetUnitMultiplier == 0) {
        // $cancelation_string = $cancelation_string . $translator->translate("Charge") . " " . $NmbrOfNights . " " . $BasisType . " " . $translator->translate("if") . " " . $translator->translate("No show") . " " . $translator->translate(" - No show means that the guest did not cancel the reservation and did not stay at the hotel during the reserved period of time");
        // } else {
        // $reason = $translator->translate("if cancelled");
        // $cancelation_string = $cancelation_string . $translator->translate("Charge") . " " . $NmbrOfNights . " " . $BasisType . " " . $reason . " " . $OffsetUnitMultiplier . " " . strtolower($OffsetTimeUnit) . " " . $OffsetDropTime2;
        // }
        // }
        // }
        // }
        // $item['cancelpolicy'] = $cancelation_string;
        // EOF Policies
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $hotel = array();
    $sql = "select sid from xmlhotels_mtravelplan where sid=" . $shid . " and hid=" . $hid;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_hotel = $statement->execute();
    if (! $row_hotel->valid()) {
        $response['error'] = "Unable to handle request #5";
        return false;
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
?>