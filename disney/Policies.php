<?php
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
if (file_exists("src/App/language/" . $lang . ".mo")) {
    $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
}
error_log("\r\n COMECOU POLICIES DISNEY \r\n", 3, "/srv/www/htdocs/error_log");
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$db = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_disney where session_id='$session_id'";
    $statement = $db->createStatement($sql);
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
$sql = "select value from settings where name='enablemajesticusadisney' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_disney = $affiliate_id;
} else {
    $affiliate_id_disney = 0;
}
$sql = "select value from settings where name='majesticusadisneyLoginEmail' and affiliate_id=$affiliate_id_disney" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusadisneyLoginEmail = $row_settings['value'];
}
error_log("\r\n majesticusadisneyLoginEmail $majesticusadisneyLoginEmail \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='majesticusadisneyPassword' and affiliate_id=$affiliate_id_disney" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusadisneyPassword = base64_decode($row_settings['value']);
}
error_log("\r\n majesticusadisneyPassword $majesticusadisneyPassword \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='majesticusadisneyMarkup' and affiliate_id=$affiliate_id_disney" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusadisneyMarkup = (double) $row_settings['value'];
} else {
    $majesticusadisneyMarkup = 0;
}
error_log("\r\n majesticusadisneyMarkup $majesticusadisneyMarkup \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='majesticusadisneyServiceURL' and affiliate_id=$affiliate_id_disney" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusadisneyServiceURL = $row_settings['value'];
}
error_log("\r\n majesticusaServiceURL $majesticusadisneyServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
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
            $HotelId = $value['HotelId'];
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        
        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";


        $roomID = $value['roomid'];
        $maxpersons = $value['maxpersons'];
        $searchid = $value['searchid'];
        $montante = $value['total'];
        $date_arrival = date('Y-m-d', strtotime($from));
        $date_departure = date('Y-m-d', strtotime($to));

        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
        <soapenv:Header/>
        <soapenv:Body>
            <tem:SearchHotelDisney_GetTickets>
                <tem:HotelID>' . $HotelId . '</tem:HotelID>
                <tem:RoomID>' . $roomID . '</tem:RoomID>
                <tem:Arrival>' . $date_arrival . '</tem:Arrival>
                <tem:Departure>' . $date_departure . '</tem:Departure>
                <!--Optional:-->
                <tem:SearchID>' . $searchid . '</tem:SearchID>
                <!--Optional:-->
         <tem:SelectedRatePlanRoomIDs>
            <!--Zero or more repetitions:-->
            <tem:int>225</tem:int>
         </tem:SelectedRatePlanRoomIDs>
            </tem:SearchHotelDisney_GetTickets>
        </soapenv:Body>
        </soapenv:Envelope>';
        //error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");

        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $majesticusadisneyServiceURL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept-Encoding: gzip, deflate",
            "Accept: application/xml",
            "Content-type: text/xml",
            "Content-length: " . strlen($raw)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseCurl = curl_exec($ch);
        //error_log("\r\n RESPONSE: $responseCurl \r\n", 3, "/srv/www/htdocs/error_log");
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        $endTime = microtime();


        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_disney');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $$majesticusadisneyServiceURL . $raw,
                'sqlcontext' => $responseCurl,
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
        $vector = array();
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($responseCurl);
        $Envelope = $inputDoc->getElementsByTagName('Envelope');
        $Body = $Envelope->item(0)->getElementsByTagName('Body');
        $SearchHotelDisney_GetTicketsResponse = $Body->item(0)->getElementsByTagName('SearchHotelDisney_GetTicketsResponse');
        $SearchHotelDisney_GetTicketsResult = $SearchHotelDisney_GetTicketsResponse->item(0)->getElementsByTagName('SearchHotelDisney_GetTicketsResult');
        $node = $SearchHotelDisney_GetTicketsResult->item(0)->getElementsByTagName('clsDisneyTickets');
        if ($node->length > 0) {
            //TicketType
            $TicketType = $node->item(0)->getElementsByTagName('TicketType');
            if ($TicketType->length > 0) {
                $clsDisneyTicketType = $TicketType->item(0)->getElementsByTagName('clsDisneyTicketType');
                if ($clsDisneyTicketType->length > 0) {
                    $TicketID = $clsDisneyTicketType->item(0)->getElementsByTagName('TicketID');
                    if ($TicketID->length > 0) {
                        $TicketID = $TicketID->item(0)->nodeValue;
                    } else {
                        $TicketID = "";
                    }
                    $TicketTypeTxt = $clsDisneyTicketType->item(0)->getElementsByTagName('TicketTypeTxt');
                    if ($TicketTypeTxt->length > 0) {
                        $TicketTypeTxt = $TicketTypeTxt->item(0)->nodeValue;
                    } else {
                        $TicketTypeTxt = "";
                    }
                }
            }
            // Days
            $Days = $node->item(0)->getElementsByTagName('Days');
            if ($Days->length > 0) {
                $day = "";
                $string = $Days->item(0)->getElementsByTagName('string');
                for ($k=0; $k < $string->length; $k++) { 
                    $day = $string->item($k)->nodeValue;
                }
            }
        }


        $raw2 = '<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
        <soapenv:Header/>
        <soapenv:Body>
            <tem:SearchHotelDisney_GetMealPlan>
                <tem:HotelID>' . $HotelId . '</tem:HotelID>
                <tem:RoomID>' . $roomID . '</tem:RoomID>
                <tem:Arrival>' . $date_arrival . '</tem:Arrival>
                <tem:Departure>' . $date_departure . '</tem:Departure>
                <!--Optional:-->
                <tem:SearchID>' . $searchid . '</tem:SearchID>
                <!--Optional:-->
                <tem:SelectedRatePlanRoomIDs>
                    <!--Zero or more repetitions:-->
                    <tem:int>225</tem:int>
                </tem:SelectedRatePlanRoomIDs>
            </tem:SearchHotelDisney_GetMealPlan>
        </soapenv:Body>
        </soapenv:Envelope>';
        error_log("\r\n RAW: $raw2 \r\n", 3, "/srv/www/htdocs/error_log");

        $startTime = microtime();
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $majesticusadisneyServiceURL);
        curl_setopt($ch2, CURLOPT_HEADER, false);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $raw2);
        curl_setopt($ch2, CURLOPT_VERBOSE, 0);
        curl_setopt($ch2, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            "Accept-Encoding: gzip, deflate",
            "Accept: application/xml",
            "Content-type: text/xml",
            "Content-length: " . strlen($raw2)
        ));
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $responseCurl2 = curl_exec($ch2);
        error_log("\r\n RESPONSE: $responseCurl2 \r\n", 3, "/srv/www/htdocs/error_log");
        $error = curl_error($ch2);
        $headers = curl_getinfo($ch2);
        curl_close($ch2);
        $endTime = microtime();


        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_disney');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $$majesticusadisneyServiceURL . $raw2,
                'sqlcontext' => $responseCurl2,
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

        $inputDoc2 = new DOMDocument();
        $inputDoc2->loadXML($responseCurl2);
        $Envelope = $inputDoc2->getElementsByTagName('Envelope');
        $Body = $Envelope->item(0)->getElementsByTagName('Body');
        $SearchHotelDisney_GetMealPlanResponse = $Body->item(0)->getElementsByTagName('SearchHotelDisney_GetMealPlanResponse');
        $SearchHotelDisney_GetMealPlanResult = $SearchHotelDisney_GetMealPlanResponse->item(0)->getElementsByTagName('SearchHotelDisney_GetMealPlanResult');
        $node2 = $SearchHotelDisney_GetMealPlanResult->item(0)->getElementsByTagName('clsDisneyMealPlan');
        if ($node2->length > 0) {
            $NetPrice = $node2->item(0)->getElementsByTagName('NetPrice');
            if ($NetPrice->length > 0) {
                $NetPrice = $NetPrice->item(0)->nodeValue;
            } else {
                $NetPrice = "";
            }
            $TotalPrice = $node2->item(0)->getElementsByTagName('TotalPrice');
            if ($TotalPrice->length > 0) {
                $TotalPrice = $TotalPrice->item(0)->nodeValue;
            } else {
                $TotalPrice = "";
            }
            $MealPlanID = $node2->item(0)->getElementsByTagName('MealPlanID');
            if ($MealPlanID->length > 0) {
                $MealPlanID = $MealPlanID->item(0)->nodeValue;
            } else {
                $MealPlanID = "";
            }
            $MealPlanType = $node2->item(0)->getElementsByTagName('MealPlanType');
            if ($MealPlanType->length > 0) {
                $MealPlanType = $MealPlanType->item(0)->nodeValue;
            } else {
                $MealPlanType = "";
            }
            $IsFree = $node2->item(0)->getElementsByTagName('IsFree');
            if ($IsFree->length > 0) {
                $NetPrice = $IsFree->item(0)->nodeValue;
            } else {
                $IsFree = "";
            }
        }


        $vector['code'] = $HotelId;
        $pricebreakdown = array();
        $pricebreakdownCount = 0;
        for ($rZZ = 0; $rZZ < $nights; $rZZ ++) {
            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
            $amount = $montante / $nights;
            if ($majesticusadisneyMarkup != 0) {
                $amount = $amount + (($amount * $majesticusadisneyMarkup) / 100);
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
            if ($majesticusadisneyMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
            $pricebreakdownCount = $pricebreakdownCount + 1;
        }

        $total = $total + $montante;
        $item['name'] = $value['name'];
        $item['room'] = $room;
        $item['roomid'] = $value['room'];
        $item['roomtype'] = $value['roomtype'];
        $item['meal'] = $value['meal'];
        $item['status'] = $value['status'];
        $item['ticketid'] = $TicketID;
        $item['tickettxt'] = $TicketTypeTxt;
        $total = $total + $montante;
        $tot = $montante;
        $item['total'] = $filter->filter($tot);
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        /* $item['cancelpolicy'] = $cancelation_string;
        $item['cancelpolicy_deadline'] = date('Y-m-d', strtotime($cancelation_deadline));
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline; */
        
        array_push($roombreakdown, $item);
    }
    $c ++;
}
error_log("\r\n PASSA AQUI 4 \r\n", 3, "/srv/www/htdocs/error_log");
$hotel = array();
$sql = "select sid from xmlhotels_mdisney where sid='" . $shid . "' and hid=" . $hid;
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
$response['code'] = $vector['code'];
?>