<?php
error_log("\r\nMulti Policies Sun Hotels\r\n", 3, "/srv/www/htdocs/error_log");
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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_sunhotels where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_sunhotels where session_id='$session_id'";
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
$sql = "select value from settings where name='enableexpedia' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_sunhotels = $affiliate_id;
} else {
    $affiliate_id_sunhotels = 0;
}
$sql = "select value from settings where name='expediaRevisionVersion' and affiliate_id=$affiliate_id_sunhotels" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaRevisionVersion = $row_settings['value'];
} else {
    $expediaRevisionVersion = "2.3";
}
$sql = "select value from settings where name='expediaAPIKey' and affiliate_id=$affiliate_id_sunhotels" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaAPIKey = $row_settings['value'];
}
$sql = "select value from settings where name='expediaMarkup' and affiliate_id=$affiliate_id_sunhotels" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaMarkup = (double) $row_settings['value'];
} else {
    $expediaMarkup = 0;
}
$sql = "select value from settings where name='expediaSharedSecret' and affiliate_id=$affiliate_id_sunhotels" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaSharedSecret = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='expediaServiceURL' and affiliate_id=$affiliate_id_sunhotels" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaServiceURL = $row_settings['value'];
    $expediaServiceURL = rtrim($expediaServiceURL, "/");
}
$sql = "select value from settings where name='expediaPaymentTerms' and affiliate_id=$affiliate_id_sunhotels" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaPaymentTerms = $row_settings['value'];
}
$sql = "select value from settings where name='expediaRevisionVersion' and affiliate_id=$affiliate_id_sunhotels" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $expediaRevisionVersion = $row_settings['value'];
} else {
    $expediaRevisionVersion = "2.3";
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

        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $start = $value['start'];
        $end = $value['end'];
        $cnights = $value['nights'];
        $ccurrency = $value['currency'];
        if ($start != "" and $end != "") {
            if ($cnights != "") {
                if ($cnights == 1) {
                    $Description = $translator->translate("Cancel between") . " " . $start . " " . $translator->translate("and") . " " . $end . " " . $cnights . " " . $translator->translate("night charge");
                } else {
                    $Description = $translator->translate("Cancel between") . " " . $start . " " . $translator->translate("and") . " " . $end . " " . $cnights . " " . $translator->translate("nights charge");
                }
            }
            $cancelpolicy_deadline = strtotime($start);
            $cancelpolicy = $Description;
        }
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";
        
        $pricetotal = (int) $value['total'];
        $mealid = $value['mealid'];
        $childrenages = $value['childrenages'];
        $userName = "testagent";
        $password = "785623";
        $currency = "USD";
        $language = "en";
        $customercountry = "gb";
        $numberrooms = 1;
        
        $url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx?op=PreBookV2";
        
        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <PreBookV2 xmlns="http://xml.sunhotels.net/15/">
                <userName>' . $userName . '</userName>
                <password>' . $password . '</password>
                <currency>' . $currency . '</currency>
                <language>' . $language . '</language>
                <checkInDate>' . $from_date . '</checkInDate>
                <checkOutDate>' . $to_date . '</checkOutDate>
                <rooms>' . $numberrooms . '</rooms>
                <adults>' . $adt . '</adults>
                <children>' . $chd . '</children>
                <childrenAges>' . $childrenages . '</childrenAges>
                <infant>0</infant>
                <mealId>' . $mealid . '</mealId>
                <customerCountry>' . $customercountry . '</customerCountry>
                <b2c>0</b2c>
                <searchPrice>' . $pricetotal . '</searchPrice>
                <roomId>' . $room_code . '</roomId>
                <hotelId></hotelId>
                <roomtypeId></roomtypeId>
                <blockSuperDeal></blockSuperDeal>
                <showPriceBreakdown>true</showPriceBreakdown>
            </PreBookV2>
        </soap:Body>
        </soap:Envelope>';
        error_log("\r\n RAW $raw \r\n", 3, "/srv/www/htdocs/error_log");
        
        $headers = array(
            'Accept-Encoding: gzip,deflate',
            'Host: xml.sunhotels.net',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: http://xml.sunhotels.net/15/PreBookV2',
            'Content-Length: ' . strlen($raw)
        );
        
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        error_log("\r\n SunHotels Response: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_expedia');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $url,
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
        // error_log("\r\n" . print_r($response2, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        
        // $status = $response2['status'];
        // if ($status == "changed") {
        // // Price Changed
        // $pricechanged = true;
        // } elseif ($status == "sold_out") {
        // // Sold Out
        // $pricesold_out = true;
        // }
        $pricechanged = true;
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response2);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        
        $PreBookV2Response = $Body->item(0)->getElementsByTagName("PreBookV2Response");
        if ($PreBookV2Response->length > 0) {
            $preBookResult = $PreBookV2Response->item(0)->getElementsByTagName("preBookResult");
            if ($preBookResult->length > 0) {
                $PreBookCode = $preBookResult->item(0)->getElementsByTagName("PreBookCode");
                if ($PreBookCode->length > 0) {
                    $PreBookCode = $PreBookCode->item(0)->nodeValue;
                } else {
                    $PreBookCode = "";
                }
                error_log("\r\n PreBookCode: $PreBookCode \r\n", 3, "/srv/www/htdocs/error_log");
                $price2 = 0;
                $Price = $preBookResult->item(0)->getElementsByTagName("Price");
                if ($Price->length > 0) {
                    $currency = $Price->item(0)->getAttribute("currency");
                    $price2 = $Price->item(0)->nodeValue;
                } else {
                    $currency = "";
                    $price2 = "";
                }
                
                // Notes
                $Notes = $preBookResult->item(0)->getElementsByTagName("Notes");
                if ($Notes->length > 0) {
                    $Note = $Notes->item(0)->getElementsByTagName("Note");
                    if ($Note->length > 0) {
                        for ($i = 0; $i < $Note->length; $i ++) {
                            $end_date = $Note->item($i)->getAttribute("end_date");
                            $start_date = $Note->item($i)->getAttribute("start_date");
                            $text = $Note->item($i)->getElementsByTagName("text");
                            if ($text->length > 0) {
                                $text = $text->item(0)->nodeValue;
                            } else {
                                $text = "";
                            }
                        }
                    }
                }
                // PriceBreakdown
                $PriceBreakdown = $preBookResult->item(0)->getElementsByTagName("PriceBreakdown");
                if ($PriceBreakdown->length > 0) {
                    $currency = $PriceBreakdown->item(0)->getAttribute("currency");
                    $to = $PriceBreakdown->item(0)->getAttribute("to");
                    $from = $PriceBreakdown->item(0)->getAttribute("from");
                    $total2 = $PriceBreakdown->item(0)->getAttribute("total");
                    $guest = $PriceBreakdown->item(0)->getElementsByTagName("guest");
                    if ($guest->length > 0) {
                        for ($j = 0; $j < $guest->length; $j ++) {
                            $guesttotal = $guest->item($j)->getAttribute("total");
                            $price = $guest->item($j)->getElementsByTagName("price");
                            if ($price->length > 0) {
                                $value = $price->item(0)->getAttribute("value");
                                $type = $price->item(0)->getAttribute("type");
                                $breakdown = $price->item(0)->getAttribute("breakdown");
                            } else {
                                $value = "";
                                $type = "";
                                $breakdown = "";
                            }
                        }
                    }
                } else {
                    $currency = "";
                    $total2 = "";
                    $from = "";
                    $to = "";
                }
                // CancellationPolicies
                $CancellationPoliciesText = "";
                $CancellationPoliciesArray = array();
                $count = 0;
                $CancellationPolicies = $preBookResult->item(0)->getElementsByTagName("CancellationPolicies");
                if ($CancellationPolicies->length > 0) {
                    $CancellationPolicy = $CancellationPolicies->item(0)->getElementsByTagName("CancellationPolicy");
                    if ($CancellationPolicy->length > 0) {
                        for ($c=0; $c < $CancellationPolicy->length; $c++) { 
                            $deadline = $CancellationPolicy->item($c)->getElementsByTagName("deadline");
                            if ($deadline->length > 0) {
                                $deadline = $deadline->item(0)->nodeValue;
                            } else {
                                $deadline = "";
                            }
                            $percentage = $CancellationPolicy->item($c)->getElementsByTagName("percentage");
                            if ($percentage->length > 0) {
                                $percentage = $percentage->item(0)->nodeValue;
                            } else {
                                $percentage = "";
                            }
                            $text = $CancellationPolicy->item($c)->getElementsByTagName("text");
                            if ($text->length > 0) {
                                $text = $text->item(0)->nodeValue;
                            } else {
                                $text = "";
                            }
                            $CancellationPoliciesArray[$count]['deadline'] = $deadline;
                            $CancellationPoliciesArray[$count]['percentage'] = $percentage;
                            $CancellationPoliciesText = $CancellationPoliciesText . $text . "\n";
                        }
                    }
                }
            }
        }
        if ($pricechanged == true) {
            $oldtotal = $value['total'];
            $oldnettotal = $value['nettotal'];
            $value['nettotal'] = $price2;
            if ($expediaMarkup != 0) {
                $valueRInclusive = $valueRInclusive + (($valueRInclusive * $expediaMarkup) / 100);
            }
            // Geo target markup
            if ($internalmarkup != 0) {
                $valueRInclusive = $valueRInclusive + (($valueRInclusive * $internalmarkup) / 100);
            }
            // Agent markup
            if ($agent_markup != 0) {
                $valueRInclusive = $valueRInclusive + (($valueRInclusive * $agent_markup) / 100);
            }
            // Fallback Markup
            if ($expediaMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                $valueRInclusive = $valueRInclusive + (($valueRInclusive * $HotelsMarkupFallback) / 100);
            }
            // Agent discount
            if ($agent_discount != 0) {
                $valueRInclusive = $valueRInclusive - (($valueRInclusive * $agent_discount) / 100);
            }
            if ($scurrency != "" and $currency != $scurrency and $request_currency != "") {
                $valueRInclusive = $CurrencyConverter->convert($valueRInclusive, $currencyRInclusive, $scurrency);
            }
            $value['total'] = $price2;
            error_log("\r\nNew Net Total: " . $oldnettotal . " -> " . $value['nettotal'] . "\r\n", 3, "/srv/www/htdocs/error_log");
            error_log("\r\nNew Total: " . $oldtotal . " -> " . $value['total'] . "\r\n", 3, "/srv/www/htdocs/error_log");
            $sql = new Sql($db);
            $sql = "delete from dp_hotels_pricechange where session_id='" . $session_id . "' and quoteid='" . (string) $value['quoteid'] . "'";
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
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('dp_hotels_pricechange');
                $insert->values(array(
                    'datetime_created' => time(),
                    'session_id' => (string) $session_id,
                    'total' => (string) $value['total'],
                    'nettotal' => (string) $value['nettotal'],
                    'oldtotal' => (string) $oldtotal,
                    'oldnettotal' => (string) $oldnettotal,
                    'quoteid' => (string) $value['quoteid']
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
        }
        //
        // EOF Policies
        //
        // EOF Check prices & availability
        //
        // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        //
        $cancelpolicy = $CancellationPoliciesText;
        if ($CancellationPoliciesArray[0]['deadline'] == "") {
            $cancelpolicy_deadline = time();
        } else {
            $cancelpolicy_deadline = $CancellationPoliciesArray[0]['deadline'] . " hours";
        }
        error_log("\r\nA - Cancel Policy Deadline / Cancel Policy / Status = " . $cancelpolicy_deadline . " - " . $cancelpolicy . " - " . $status . " \r\n", 3, "/srv/www/htdocs/error_log");
        if ($cancelpolicy_deadline == 0 and $status == "matched") {
            $cancelpolicy_deadline = $value['cancelpolicy_deadlinetimestamp'];
        }
        if ($cancelpolicy == "" and $status == "matched") {
            $cancelpolicy = $value['cancelpolicy'];
        }
        error_log("\r\nB - Cancel Policy Deadline = " . $cancelpolicy_deadline . "\r\n", 3, "/srv/www/htdocs/error_log");
        error_log("\r\nCancel Policy = " . $cancelpolicy . "\r\n", 3, "/srv/www/htdocs/error_log");
        $total = $total + $price2;
        $tot = $price2;
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        $item['total'] = $price2;
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $item['subtotal'] = $filter->filter(floatval($tot));
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        $item['cancelpolicy'] = $cancelpolicy;
        $item['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;
        $item['cancelpolicy_deadline'] = $cancelpolicy_deadline;
        /*
         * $item['nonrefundable'] = $value['nonrefundable'];
         * if ($item['nonrefundable'] == true) {
         * $item['cancelpolicy_deadline'] = 0;
         * $item['cancelpolicy'] = $translator->translate("This booking is non-refundable and cannot be amended or modified. Failure to arrive at your hotel will be treated as a No-Show and no refund will be given.");
         * }
         */
        $item['cancelpolicy_details'] = $cancelpolicy;
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_msunhotels where sid='" . $shid . "' and hid=" . $hid;
error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n PASSOU POR 3  \r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n PASSOU POR 4  \r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\nSunHotels Policies Multi - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>