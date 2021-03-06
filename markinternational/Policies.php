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
error_log("\r\nMark International - COMECOU POLICIES\r\n", 3, "/srv/www/htdocs/error_log");
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_markinternational where session_id='$session_id'";
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
    $residency = $searchsettings['residency'];
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$sql = "select value from settings where name='enableMarkInternational' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_markinternational = $affiliate_id;
} else {
    $affiliate_id_markinternational = 0;
}
$sql = "select value from settings where name='MarkInternationalLogin' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalLogin = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalPassword' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalPassword = base64_decode($row_settings['value']);
}

$sql = "select value from settings where name='MarkInternationalMarkup' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalMarkup = (double) $row_settings['value'];
} else {
    $MarkInternationalMarkup = 0;
}
$sql = "select value from settings where name='MarkInternationalURL' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalURL = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalVendor' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalVendor = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalAgencyNumber' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalAgencyNumber = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalWebServices' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalWebServices = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalDynamicPackaging' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalDynamicPackaging = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalb2cMarkup' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalb2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalContact' and affiliate_id=$affiliate_id_markinternational" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalContact = $row_settings['value'];
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

$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['HotelId'];
            $scode = $value['shid'];
            $hotel_code = $value['shid'];
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $city_xml17 = $value['city_xml17'];
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));

        $item = array();
        $cancelation_deadline = 0;
        $cancelation_details = "";

        $raw = 'requestXml=<VAXXML xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.triseptsolutions.com/CancelPoliciesByDate/Request/11.0">
                <Header Vendor="' . $MarkInternationalVendor . '" Site="VAXXML" AgencyNumber="' . $MarkInternationalAgencyNumber . '" Contact="' . $MarkInternationalContact . '" Login="' . $MarkInternationalLogin . '" Password="' . $MarkInternationalPassword . '" DynamicPackageId="' . $MarkInternationalDynamicPackaging . '" />
                <Request>
                <HotelCodes>
                    <Hotel Code="' . $hotel_code . '" />
                </HotelCodes>
                    <OriginDestinationInformation Type="Checkin" LocationCode="' . $city_xml17 . '" DateTime="' . $from_date . 'T' . strftime("%H:%M:%S") . '" />
                    <OriginDestinationInformation Type="Checkout" LocationCode="' . $city_xml17 . '" DateTime="' . $to_date . 'T' . strftime("%H:%M:%S") . '" />
                </Request>
                </VAXXML>';
        error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");
        $headers = array(
            "Accept: application/xml",
            "Content-type: application/x-www-form-urlencoded",
            "Content-Encoding: UTF-8",
            "Accept-Encoding: gzip,deflate",
            "Content-length: " . strlen($raw)
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $MarkInternationalURL . 'CancelPoliciesByDateRequest');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response3 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        
        $response3 = str_replace('&lt;', '<', $response3);
        $response3 = str_replace('&gt;', '>', $response3);
        error_log("\r\n Response: $response3 \r\n", 3, "/srv/www/htdocs/error_log");
        
        $Description2 = "";
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response3);
        $string = $inputDoc->getElementsByTagName("string");
        $VAXXML = $string->item(0)->getElementsByTagName("VAXXML");
        if ($VAXXML->length > 0) {
            $Hotels = $VAXXML->item(0)->getElementsByTagName("Hotels");
            if ($Hotels->length > 0) {
                $Hotel = $Hotels->item(0)->getElementsByTagName("Hotel");
                if ($Hotel->length > 0) {
                    $HotelCode = $Hotel->item(0)->getAttribute("Code");
                    $RollingCancelPolicy = $Hotel->item(0)->getElementsByTagName("RollingCancelPolicy");
                    if ($RollingCancelPolicy->length > 0) {
                        $CutOffUnit = $RollingCancelPolicy->item(0)->getAttribute("CutOffUnit");
                        $CutOff = $RollingCancelPolicy->item(0)->getAttribute("CutOff");
                        $CancellationDeadline = $RollingCancelPolicy->item(0)->getAttribute("CancellationDeadline");
                        $DescriptionKey = $RollingCancelPolicy->item(0)->getAttribute("DescriptionKey");
                        $DaysOfWeek = $RollingCancelPolicy->item(0)->getAttribute("DaysOfWeek");
                        $HighDate = $RollingCancelPolicy->item(0)->getAttribute("HighDate");
                        $LowDate = $RollingCancelPolicy->item(0)->getAttribute("LowDate");
                        $NumberOfNightsPenalty = $RollingCancelPolicy->item(0)->getElementsByTagName("NumberOfNightsPenalty");
                        if ($NumberOfNightsPenalty->length > 0) {
                            $NumberOfNights = $NumberOfNightsPenalty->item(0)->getAttribute("NumberOfNights");
                        } else {
                            $NumberOfNights = "";
                        }
                        
                    } else {
                        $CutOffUnit = "";
                        $CutOff = "";
                        $CancellationDeadline = "";
                        $DescriptionKey = "";
                        $DaysOfWeek = "";
                        $HighDate = "";
                        $LowDate = "";
                    }
                    
                }
            }
            $Descriptions = $VAXXML->item(0)->getElementsByTagName("Descriptions");
            if ($Descriptions->length > 0) {
                $Description = $Descriptions->item(0)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    $Key = $Description->item(0)->getAttribute("Key");
                    $Description2 = $Description->item(0)->nodeValue;
                } else {
                    $Description2 = "";
                }
            }
        }
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
        $item['RoomType'] = $value['RoomType'];
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
        $item['cancelpolicy'] = str_replace("<p><br /></p>", "", $value['cancelpolicy']);
        
        if ($Description2 != "") {
            $item['nonrefundable'] = true;
            $item['cancelpolicy'] = $translator->translate("This is a non refundable booking.") . "<br/>" . $Description2;
            $item['cancelpolicy_details'] = $translator->translate("This is a non refundable booking.") . "<br/>" . $Description2;
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
        } else {
            $cancelation_details = "If you cancel after " . $CancellationDeadline . " is penalized " . $NumberOfNights . " night(s).";
            $item['nonrefundable'] = false;
            $item['cancelpolicy'] = $cancelation_details;
            $item['cancelpolicy_details'] = $cancelation_details;
            $item['cancelpolicy_deadline'] = date('D, d M Y', strtotime($CancellationDeadline));
        }

        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mmarkinternational where sid='" . $shid . "' and hid=" . $hid;
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