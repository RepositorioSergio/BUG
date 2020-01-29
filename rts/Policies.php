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
error_log("\r\n COMECOU POLICIES \r\n", 3, "/srv/www/htdocs/error_log");
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_rts where session_id='$session_id'";
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
$sql = "select value from settings where name='enablerts' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_rts = $affiliate_id;
} else {
    $affiliate_id_rts = 0;
}
$sql = "select value from settings where name='rtsID' and affiliate_id=$affiliate_id_rts" . $branch_filter;
;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsID = $row_settings['value'];
}
$sql = "select value from settings where name='rtsPassword' and affiliate_id=$affiliate_id_rts" . $branch_filter;
;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='rtsSiteCode' and affiliate_id=$affiliate_id_rts" . $branch_filter;
;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsSiteCode = $row_settings['value'];
}
$sql = "select value from settings where name='rtsRequestType' and affiliate_id=$affiliate_id_rts" . $branch_filter;
;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsRequestType = $row_settings['value'];
}
$sql = "select value from settings where name='rtsServiceURL' and affiliate_id=$affiliate_id_rts" . $branch_filter;
;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsServiceURL = $row['value'];
}
error_log("\r\n rtsServiceURL  $rtsServiceURL  \r\n", 3, "/srv/www/htdocs/error_log");

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

$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');

/*
 * $fromHotelsPRO = $fromHotelsPRO->getTimestamp();
 * $toHotelsPro = $toHotelsPro->getTimestamp();
 */
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['hotelid'];
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
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        $CityCode = $value['CityCode'];
        $roomtypecode = $value['roomtypecode'];
        $ItemCode = $value['ItemCode'];
        $ItemNo = $value['ItemNo'];

        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rts="http://www.rts.co.kr/">
        <soapenv:Header>
            <rts:BaseInfo>
                <!--Optional:-->
                <rts:SiteCode>' . $rtsSiteCode . '</rts:SiteCode>
                <!--Optional:-->
                <rts:Password>' . $rtsPassword . '</rts:Password>
                <!--Optional:-->
                <rts:RequestType>NetPartner</rts:RequestType>
            </rts:BaseInfo>
        </soapenv:Header>
        <soapenv:Body>
            <rts:GetRemarkHotelInformationForCustomerCount>
                <rts:HotelSearchListNetGuestCount>
                    <!--Optional:-->
                    <rts:LanguageCode>AR</rts:LanguageCode>
                    <!--Optional:-->
                    <rts:TravelerNationality>AR</rts:TravelerNationality>
                    <!--Optional:-->
                    <rts:CityCode>' . $CityCode . '</rts:CityCode>
                    <!--Optional:-->
                    <rts:CheckInDate>' . $from_date . '</rts:CheckInDate>
                    <!--Optional:-->
                    <rts:CheckOutDate>' . $to_date . '</rts:CheckOutDate>
                    <!--Optional:-->
                    <rts:StarRating>0</rts:StarRating>
                    <!--Optional:-->
                    <rts:LocationCode></rts:LocationCode>
                    <!--Optional:-->
                    <rts:SupplierCompCode></rts:SupplierCompCode>
                    <rts:AvailableHotelOnly>true</rts:AvailableHotelOnly>
                    <rts:RecommendHotelOnly>false</rts:RecommendHotelOnly>
                    <!--Optional:-->
                    <rts:ClientCurrencyCode>USD</rts:ClientCurrencyCode>
                    <!--Optional:-->
                    <rts:ItemName></rts:ItemName>
                    <!--Optional:-->
                    <rts:SellerMarkup>*1</rts:SellerMarkup>
                    <rts:CompareYn>false</rts:CompareYn>
                    <!--Optional:-->
                    <rts:SortType></rts:SortType>
                    <!--Optional:-->
                    <rts:ItemCodeList>
                    <!--Zero or more repetitions:-->
                    <rts:ItemCodeInfo>
                        <!--Optional:-->
                        <rts:ItemCode>' . $ItemCode . '</rts:ItemCode>
                        <rts:ItemNo>' . $ItemNo . '</rts:ItemNo>
                    </rts:ItemCodeInfo>
                    </rts:ItemCodeList>
                    <!--Optional:-->
                    <rts:GuestList>
                    <!--Zero or more repetitions:-->
                    <rts:GuestsInfo>
                        <rts:AdultCount>' . $adt . '</rts:AdultCount>
                        <rts:ChildCount>' . $chd . '</rts:ChildCount>
                        <rts:RoomCount>1</rts:RoomCount>';
                        for ($z=0; $z < $chd; $z++) { 
                            $raw .= '<rts:ChildAge' . ($z+1) . '>' . $children_ages[$z] . '</rts:ChildAge' . ($z+1) . '>';
                        }
                $raw .= '</rts:GuestsInfo>			   
                    </rts:GuestList>
                </rts:HotelSearchListNetGuestCount>
                <!--Optional:-->
                <rts:RoomTypeCode>' . $roomtypecode . '</rts:RoomTypeCode>
            </rts:GetRemarkHotelInformationForCustomerCount>
        </soapenv:Body>
        </soapenv:Envelope>';
        error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");

        $headers = array(
            "Content-type: text/xml",
            "SOAPAction: http://www.rts.co.kr/GetRemarkHotelInformationForCustomerCount",
            "Content-length: " . strlen($raw)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $rtsServiceURL . 'WebServiceProjects/NetWebService/WsHotelProducts.asmx');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        curl_close($ch);

        $response2 = str_replace('&lt;', '<', $response2);
        $response2 = str_replace('&gt;', '>', $response2);
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_rts');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $rtsServiceURL,
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

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response2);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $GetRemarkHotelInformationForCustomerCountResponse = $Body->item(0)->getElementsByTagName("GetRemarkHotelInformationForCustomerCountResponse");
        // GetRemarkHotelInformationForCustomerCountResult
        $GetRemarkHotelInformationForCustomerCountResult = $GetRemarkHotelInformationForCustomerCountResponse->item(0)->getElementsByTagName("GetRemarkHotelInformationForCustomerCountResult");

        // RemarkHotelInformation
        $RemarkHotelInformation = $GetRemarkHotelInformationForCustomerCountResult->item(0)->getElementsByTagName("RemarkHotelInformation");

        $ItemCode = $RemarkHotelInformation->item(0)->getElementsByTagName("ItemCode");
        if ($ItemCode->length > 0) {
            $ItemCode = $ItemCode->item(0)->nodeValue;
        } else {
            $ItemCode = "";
        }
        $ItemNo = $RemarkHotelInformation->item(0)->getElementsByTagName("ItemNo");
        if ($ItemNo->length > 0) {
            $ItemNo = $ItemNo->item(0)->nodeValue;
        } else {
            $ItemNo = "";
        }
        $RoomTypeCode = $RemarkHotelInformation->item(0)->getElementsByTagName("RoomTypeCode");
        if ($RoomTypeCode->length > 0) {
            $RoomTypeCode = $RoomTypeCode->item(0)->nodeValue;
        } else {
            $RoomTypeCode = "";
        }

        $Remarks = $RemarkHotelInformation->item(0)->getElementsByTagName("Remarks");
        $Remark1 = $Remarks->item(0)->getElementsByTagName("Remark1");
        if ($Remark1->length > 0) {
            $Remark1 = $Remark1->item(0)->nodeValue;
        } else {
            $Remark1 = "";
        }
        $Remark2 = $Remarks->item(0)->getElementsByTagName("Remark2");
        if ($Remark2->length > 0) {
            $Remark2 = $Remark2->item(0)->nodeValue;
        } else {
            $Remark2 = "";
        }

        //CANCELDEADLINE
        $raw2 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rts="http://www.rts.co.kr/">
        <soapenv:Header>
        <rts:BaseInfo>
            <!--Optional:-->
            <rts:SiteCode>' . $rtsSiteCode . '</rts:SiteCode>
            <!--Optional:-->
            <rts:Password>' . $rtsPassword . '</rts:Password>
            <!--Optional:-->
            <rts:RequestType>NetPartner</rts:RequestType>
        </rts:BaseInfo>
        </soapenv:Header>
        <soapenv:Body>
        <rts:GetCancelDeadlineForCustomerCount>
            <rts:GetCancelDeadline>
                <!--Optional:-->
                <rts:ItemCode>' . $ItemCode . '</rts:ItemCode>
                <rts:ItemNo>' . $ItemNo . '</rts:ItemNo>
                <!--Optional:-->
                <rts:RoomTypeCode>' . $RoomTypeCode . '</rts:RoomTypeCode>
                <!--Optional:-->
                <rts:CheckInDate>' . $from_date . '</rts:CheckInDate>
                <!--Optional:-->
                <rts:CheckOutDate>' . $to_date . '</rts:CheckOutDate>
                <!--Optional:-->
                <rts:GuestList>
                    <!--Zero or more repetitions:-->
                    <rts:GuestsInfo>
                    <rts:AdultCount>' . $adt . '</rts:AdultCount>
                    <rts:ChildCount>' . $chd . '</rts:ChildCount>
                    <rts:RoomCount>1</rts:RoomCount>';
                    if ($chd > 0) {
                        for ($z=0; $z < $chd; $z++) { 
                            $raw2 .= '<rts:ChildAge' . ($z+1) . '>' . $children_ages[$z] . '</rts:ChildAge' . ($z+1) . '>';
                        }
                    } else {
                        $raw2 .= '<rts:ChildAge1>0</rts:ChildAge1>
                        <rts:ChildAge2>0</rts:ChildAge2>';
                    }
                    
                $raw2 .= '</rts:GuestsInfo>
                </rts:GuestList>
                <!--Optional:-->
                <rts:LanguageCode>AR</rts:LanguageCode>
                <!--Optional:-->
                <rts:TravelerNationality>AR</rts:TravelerNationality>
            </rts:GetCancelDeadline>
        </rts:GetCancelDeadlineForCustomerCount>
        </soapenv:Body>
        </soapenv:Envelope>';
        error_log("\r\n RAW2: $raw2 \r\n", 3, "/srv/www/htdocs/error_log");


        $headers2 = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "SOAPAction: http://www.rts.co.kr/GetCancelDeadlineForCustomerCount",
            "Content-length: " . strlen($raw2)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $rtsServiceURL . 'WebServiceProjects/NetWebService/WsHotelProducts.asmx');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
        $response3 = curl_exec($ch);
        curl_close($ch);

        $response3 = str_replace('&lt;', '<', $response3);
        $response3 = str_replace('&gt;', '>', $response3);
        error_log("\r\n Response: $response3 \r\n", 3, "/srv/www/htdocs/error_log");

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response3);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $GetCancelDeadlineForCustomerCountResponse = $Body->item(0)->getElementsByTagName("GetCancelDeadlineForCustomerCountResponse");
        if ($GetCancelDeadlineForCustomerCountResponse->length > 0) {
            // GetCancelDeadlineForCustomerCountResult
            $GetCancelDeadlineForCustomerCountResult = $GetCancelDeadlineForCustomerCountResponse->item(0)->getElementsByTagName("GetCancelDeadlineForCustomerCountResult");

            // GetCancelDeadlineResponse
            $GetCancelDeadlineResponse = $GetCancelDeadlineForCustomerCountResult->item(0)->getElementsByTagName("GetCancelDeadlineResponse");
            $GetCancelDeadlineResult = $GetCancelDeadlineResponse->item(0)->getElementsByTagName("GetCancelDeadlineResult");

            $CancelDeadlineDate = $GetCancelDeadlineResult->item(0)->getElementsByTagName("CancelDeadlineDate");
            if ($CancelDeadlineDate->length > 0) {
                $CancelDeadlineDate = $CancelDeadlineDate->item(0)->nodeValue;
            } else {
                $CancelDeadlineDate = "";
            }
            $TypeCode = $GetCancelDeadlineResult->item(0)->getElementsByTagName("TypeCode");
            if ($TypeCode->length > 0) {
                $TypeCode = $TypeCode->item(0)->nodeValue;
            } else {
                $TypeCode = "";
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
        
        $promotion = $value['specialdescription'];
        $procurar = "Non-Refundable";
        if (strpos($promotion, $procurar) !== false) {
            $item['nonrefundable'] = true;
            $item['cancelpolicy'] = $translator->translate("This is a non refundable booking");
            $item['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
            $item['cancelpolicy_deadlinetimestamp'] = time();
        }
        
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mrts where sid='" . $shid . "' and hid=" . $hid;
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