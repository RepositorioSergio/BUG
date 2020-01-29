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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_rts where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_rts where session_id='$session_id'";
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
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsServiceURL = $row['value'];
}
$sql = "select value from settings where name='rtsLanguageCode' and affiliate_id=$affiliate_id_rts" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsLanguageCode = $row['value'];
}
if ($rtsLanguageCode == "") {
    $rtsLanguageCode = "AR";
}
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='rtsDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
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
        
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        $CityCode = $value['CityCode'];
        $roomtypecode = $value['roomtype'];
        $ItemCode = $value['ItemCode'];
        $ItemNo = $value['ItemNo'];
        
        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rts="http://www.rts.co.kr/"><soapenv:Header><rts:BaseInfo><rts:SiteCode>' . $rtsSiteCode . '</rts:SiteCode><rts:Password>' . $rtsPassword . '</rts:Password><rts:RequestType>NetPartner</rts:RequestType></rts:BaseInfo></soapenv:Header>
        <soapenv:Body>
            <rts:GetRemarkHotelInformationForCustomerCount>
                <rts:HotelSearchListNetGuestCount>
                    <rts:LanguageCode>' . $rtsLanguageCode . '</rts:LanguageCode>
                    <rts:TravelerNationality>' . $sourceMarket . '</rts:TravelerNationality>
                    <rts:CityCode>' . $CityCode . '</rts:CityCode>
                    <rts:CheckInDate>' . $from_date . '</rts:CheckInDate>
                    <rts:CheckOutDate>' . $to_date . '</rts:CheckOutDate>
                    <rts:StarRating>0</rts:StarRating>
                    <rts:LocationCode></rts:LocationCode>
                    <rts:SupplierCompCode></rts:SupplierCompCode>
                    <rts:AvailableHotelOnly>true</rts:AvailableHotelOnly>
                    <rts:RecommendHotelOnly>false</rts:RecommendHotelOnly>
                    <rts:ClientCurrencyCode>' . $currency . '</rts:ClientCurrencyCode>
                    <rts:ItemName></rts:ItemName>
                    <rts:SellerMarkup>*1</rts:SellerMarkup>
                    <rts:CompareYn>false</rts:CompareYn>
                    <rts:SortType></rts:SortType>
                    <rts:ItemCodeList>
                    <rts:ItemCodeInfo>
                        <rts:ItemCode>' . $ItemCode . '</rts:ItemCode>
                        <rts:ItemNo>' . $ItemNo . '</rts:ItemNo>
                    </rts:ItemCodeInfo>
                    </rts:ItemCodeList>
                    <rts:GuestList>
                    <rts:GuestsInfo>
                        <rts:AdultCount>' . $adt . '</rts:AdultCount>
                        <rts:ChildCount>' . $chd . '</rts:ChildCount>
                        <rts:RoomCount>1</rts:RoomCount>';
                        if ($chd > 0) {
                            for ($z = 0; $z < $chd; $z ++) {
                                $raw .= '<rts:ChildAge' . ($z + 1) . '>' . $children_ages[$z] . '</rts:ChildAge' . ($z + 1) . '>';
                            }
                        } else {
                            $raw .= '<rts:ChildAge1>0</rts:ChildAge1>
                                        <rts:ChildAge2>0</rts:ChildAge2>';
                        }
        $raw .= '</rts:GuestsInfo>			   
                    </rts:GuestList>
                </rts:HotelSearchListNetGuestCount>
                <rts:RoomTypeCode>' . $roomtypecode . '</rts:RoomTypeCode>
            </rts:GetRemarkHotelInformationForCustomerCount></soapenv:Body></soapenv:Envelope>';
        error_log("\r\nRTS Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
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
        error_log("\r\nRTS Response: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
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
        //
        // CANCELDEADLINE
        //
        $raw2 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rts="http://www.rts.co.kr/">
        <soapenv:Header>
        <rts:BaseInfo>
            <rts:SiteCode>' . $rtsSiteCode . '</rts:SiteCode>
            <rts:Password>' . $rtsPassword . '</rts:Password>
            <rts:RequestType>NetPartner</rts:RequestType>
        </rts:BaseInfo>
        </soapenv:Header>
        <soapenv:Body>
        <rts:GetCancelDeadlineForCustomerCount>
            <rts:GetCancelDeadline>
                <rts:ItemCode>' . $ItemCode . '</rts:ItemCode>
                <rts:ItemNo>' . $ItemNo . '</rts:ItemNo>
                <rts:RoomTypeCode>' . $RoomTypeCode . '</rts:RoomTypeCode>
                <rts:CheckInDate>' . $from_date . '</rts:CheckInDate>
                <rts:CheckOutDate>' . $to_date . '</rts:CheckOutDate>
                <rts:GuestList>
                    <rts:GuestsInfo>
                    <rts:AdultCount>' . $adt . '</rts:AdultCount>
                    <rts:ChildCount>' . $chd . '</rts:ChildCount>
                    <rts:RoomCount>1</rts:RoomCount>';
        if ($chd > 0) {
            for ($z = 0; $z < $chd; $z ++) {
                $raw2 .= '<rts:ChildAge' . ($z + 1) . '>' . $children_ages[$z] . '</rts:ChildAge' . ($z + 1) . '>';
            }
        } else {
            $raw2 .= '<rts:ChildAge1>0</rts:ChildAge1>
                        <rts:ChildAge2>0</rts:ChildAge2>';
        }
        
        $raw2 .= '</rts:GuestsInfo>
                </rts:GuestList>
                <rts:LanguageCode>' . $rtsLanguageCode . '</rts:LanguageCode>
                <rts:TravelerNationality>' . $sourceMarket . '</rts:TravelerNationality>
            </rts:GetCancelDeadline>
        </rts:GetCancelDeadlineForCustomerCount>
        </soapenv:Body>
        </soapenv:Envelope>';
       // error_log("\r\nRTS RAW Request (2): $raw2 \r\n", 3, "/srv/www/htdocs/error_log");
        
        $raw3 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rts="http://www.rts.co.kr/">
        <soapenv:Header>
           <rts:BaseInfo>
              <!--Optional:-->
              <rts:SiteCode>CTM1X-00</rts:SiteCode>
              <!--Optional:-->
              <rts:Password>test1234</rts:Password>
              <!--Optional:-->
              <rts:RequestType>NetPartner</rts:RequestType>
           </rts:BaseInfo>
        </soapenv:Header>
        <soapenv:Body>
           <rts:GetCancelDeadlineForCustomerCount>
              <rts:GetCancelDeadline>
                 <!--Optional:-->
                 <rts:ItemCode>SEL0001</rts:ItemCode>
                 <rts:ItemNo>30</rts:ItemNo>
                 <!--Optional:-->
                 <rts:RoomTypeCode>5f576ffe-a19b-4548-a43e-6c66f1ffbb87|01001|Superior Double|RB|ADDZRDQ|BED10#6|E|20200417|Superior Double|BED01*1^BED10*1|N|HBHGRGMPKGFHPZ|~American buffet breakfast at hotel</rts:RoomTypeCode>
                 <!--Optional:-->
                 <rts:CheckInDate>2020-06-18</rts:CheckInDate>
                 <!--Optional:-->
                 <rts:CheckOutDate>2020-06-22</rts:CheckOutDate>
                 <!--Optional:-->
                 <rts:GuestList>
                    <!--Zero or more repetitions:-->
                    <rts:GuestsInfo>
                       <rts:AdultCount>2</rts:AdultCount>
                       <rts:ChildCount>0</rts:ChildCount>
                       <rts:RoomCount>1</rts:RoomCount>
                       <rts:ChildAge1>0</rts:ChildAge1>
                       <rts:ChildAge2>0</rts:ChildAge2>
                    </rts:GuestsInfo>
                 </rts:GuestList>
                 <!--Optional:-->
                 <rts:LanguageCode>AR</rts:LanguageCode>
                 <!--Optional:-->
                 <rts:TravelerNationality>AR</rts:TravelerNationality>
              </rts:GetCancelDeadline>
           </rts:GetCancelDeadlineForCustomerCount>
        </soapenv:Body>
        </soapenv:Envelope>';
        error_log("\r\nRTS RAW Request (3): $raw3 \r\n", 3, "/srv/www/htdocs/error_log");
        $headers2 = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "SOAPAction: http://www.rts.co.kr/GetCancelDeadlineForCustomerCount",
            "Content-length: " . strlen($raw3)
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $rtsServiceURL . 'WebServiceProjects/NetWebService/WsBookings.asmx');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
        $response3 = curl_exec($ch);
        curl_close($ch);
        
        $response3 = str_replace('&lt;', '<', $response3);
        $response3 = str_replace('&gt;', '>', $response3);
        error_log("\r\n Response: $response3 \r\n", 3, "/srv/www/htdocs/error_log");
        
        $CancelDeadlineDate = "";
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response3);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $GetCancelDeadlineForCustomerCountResponse = $Body->item(0)->getElementsByTagName("GetCancelDeadlineForCustomerCountResponse");
        if ($GetCancelDeadlineForCustomerCountResponse->length > 0) {
            // GetCancelDeadlineForCustomerCountResult
            $GetCancelDeadlineForCustomerCountResult = $GetCancelDeadlineForCustomerCountResponse->item(0)->getElementsByTagName("GetCancelDeadlineForCustomerCountResult");
            if ($GetCancelDeadlineForCustomerCountResult->length > 0) {
                // GetCancelDeadlineResponse
                $GetCancelDeadlineResponse = $GetCancelDeadlineForCustomerCountResult->item(0)->getElementsByTagName("GetCancelDeadlineResponse");
                if ($GetCancelDeadlineResponse->length > 0) {
                    $GetCancelDeadlineResult = $GetCancelDeadlineResponse->item(0)->getElementsByTagName("GetCancelDeadlineResult");
                    if ($GetCancelDeadlineResult->length > 0) {
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
                        error_log("\r\n CancelDeadlineDate: $CancelDeadlineDate \r\n", 3, "/srv/www/htdocs/error_log");
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
        
        if ($CancelDeadlineDate == "") {
            $promotion = $value['specialdescription'];
            $procurar = "Non-Refundable";
            if (strpos($promotion, $procurar) !== false) {
                $item['nonrefundable'] = true;
                $item['cancelpolicy'] = $translator->translate("This is a non refundable booking");
                $item['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
                $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                $item['cancelpolicy_deadlinetimestamp'] = time();
            }
        } else {
            $cancelation_details = "If you cancel after " . $CancelDeadlineDate . " is penalized.";
            $item['cancelpolicy'] = $cancelation_details;
            $item['cancelpolicy_details'] = $cancelation_details;
            $item['cancelpolicy_deadline'] = $CancelDeadlineDate;
        }
        // $cancelation_deadline = $value['DailyRateStart'];
        // $cancelation_details = $value['cancelpolicy'];
        // $item['cancelpolicy'] = $cancelpolicy;
        // $item['cancelpolicy_deadline'] = strftime("%d-%m-%Y", $cancelation_deadline);
        // $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        // $item['cancelpolicy_details'] = $cancelation_details;
        
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mrts where sid='" . $shid . "' and hid=" . $hid;
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