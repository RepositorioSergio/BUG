<?php
$scurrency = strtoupper($currency);
use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$ati = false;
error_log("\r\n Start ATI\r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select name, country_id, zone_id,city_xml19, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml19 = $row_settings["city_xml19"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml19 = "";
}
$affiliate_id = 0;
$sql = "select value from settings where name='enableati' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_ati = $affiliate_id;
} else {
    $affiliate_id_ati = 0;
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
    $sql = "select value from settings where name='atiDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='atiUsername' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $atiUsername = $row_settings['value'];
}
$sql = "select value from settings where name='atiPassword' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $atiPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='atiaffiliates_id' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $atiaffiliates_id = $row_settings['value'];
}
$sql = "select value from settings where name='atib2cMarkup' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $atib2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='atiServiceURL' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $atiServiceURL = $row['value'];
}
$sql = "select value from settings where name='atiMarkup' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $atiMarkup = (double) $row_settings['value'];
} else {
    $atiMarkup = 0;
}
$sql = "select value from settings where name='atibranches_id' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $atibranches_id = $row_settings['value'];
}
$sql = "select value from settings where name='atiParallelSearch' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $atiParallelSearch = $row_settings['value'];
}
$sql = "select value from settings where name='atiSearchSortorder' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $atiSearchSortorder = (double) $row_settings['value'];
} else {
    $atiSearchSortorder = 0;
}
$sql = "select value from settings where name='atiTimeout' and affiliate_id=$affiliate_id_ati";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $atiTimeout = (int) $row_settings['value'];
}
$duration = 'P0Y0M' . $noOfNights . 'D';
$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
<soap:Header/>
<soap:Body>
    <ns1:OTA_HotelAvailRQ Version="1.3">
        <POS>
            <Source ERSP_UserID="' . $atiUsername . '"/>
        </POS>
        <AvailRequestSegments>
            <AvailRequestSegment AvailReqType="AMENITIES">
                <StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" Duration="' . $duration . '"></StayDateRange>
                <RoomStayCandidates>';
                for ($r=0; $r < $rooms; $r++) { 
                    $raw .= '<RoomStayCandidate>
                    <GuestCounts>
                        <GuestCount AgeQualifyingCode="10" Count="' . $selectedAdults[$r] . '"/>';
                    if ($selectedChildren[$r] > 0) {
                        for ($z=0; $z < $selectedChildren[$r]; $z++) { 
                            $raw .= '<GuestCount AgeQualifyingCode="08" Age="' . $selectedChildrenAges[$r][$z] . '" Count="' . $selectedChildren[$r] . '"/>';
                        }
                    }
                    $raw .= '</GuestCounts>
                    </RoomStayCandidate>';
                }
        $raw .= '</RoomStayCandidates>
                <HotelSearchCriteria>
                    <Criterion>
                        <HotelRef HotelCityCode="10203"/>
                    </Criterion>
                </HotelSearchCriteria>
            </AvailRequestSegment>
        </AvailRequestSegments>
    </ns1:OTA_HotelAvailRQ>
</soap:Body>
</soap:Envelope>';
// error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
if ($atiServiceURL != "" and $atiUsername != "" and $atiPassword != "") {
    $encode = $atiUsername . ":" . $atiPassword;
    $auth = base64_encode("$encode");
    $headers = array(
        "Content-type: text/xml;charset=UTF-8",
        "Accept-Encoding: gzip, deflate",
        "Authorization: Basic " . $auth,
        "Content-length: " . strlen($raw)
    );
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $atiServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, $atiTimeout);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    // if ($response === false) {
    // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    // } else {
    // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    // }
    curl_close($ch);
    // error_log("\r\nResponse: $response \r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_ati');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $atiServiceURL . $raw,
            'sqlcontext' => $response,
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
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $OTA_HotelAvailRS = $Body->item(0)->getElementsByTagName("OTA_HotelAvailRS");
    if ($OTA_HotelAvailRS->length > 0) {
        $RoomStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName("RoomStays");
        if ($RoomStays->length > 0) {
            $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
            if ($RoomStay->length > 0) {
                for ($i=0; $i < $RoomStay->length; $i++) { 
                    $RoomTypes = $RoomStay->item($i)->getElementsByTagName("RoomTypes");
                    if ($RoomTypes->length > 0) {
                        $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                        if ($RoomType->length > 0) {
                            $IsRoom = $RoomType->item(0)->getAttribute("IsRoom");
                            $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
                            $Amenities = $RoomType->item(0)->getElementsByTagName("Amenities");
                            if ($Amenities->length > 0) {
                                $Amenity = $Amenities->item(0)->getElementsByTagName("Amenity");
                                if ($Amenity->length > 0) {
                                    $CodeDetail = $Amenity->item(0)->getAttribute("CodeDetail");
                                    $RoomAmenityCode = $Amenity->item(0)->getAttribute("RoomAmenityCode");
                                }
                            }
                            $RoomDescription = $RoomType->item(0)->getElementsByTagName("RoomDescription");
                            if ($RoomDescription->length > 0) {
                                $Text = $RoomDescription->item(0)->getElementsByTagName("Text");
                                if ($Text->length > 0) {
                                    $Text = $Text->item(0)->nodeValue;
                                } else {
                                    $Text = "";
                                }
                            }
                        }
                    }
                    $RoomRates = $RoomStay->item($i)->getElementsByTagName("RoomRates");
                    if ($RoomRates->length > 0) {
                        $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                        if ($RoomRate->length > 0) {
                            $RatePlanCode = $RoomRate->item(0)->getAttribute("RatePlanCode");
                            $Rates = $RoomRate->item(0)->getElementsByTagName("Rates");
                            if ($Rates->length > 0) {
                                $Rate = $Rates->item(0)->getElementsByTagName("Rate");
                                if ($Rate->length > 0) {
                                    for ($iAux=0; $iAux < $Rate->length; $iAux++) { 
                                        $EffectiveDate = $Rate->item($iAux)->getAttribute("EffectiveDate");
                                        $Base = $Rate->item($iAux)->getElementsByTagName("Base");
                                        if ($Base->length > 0) {
                                            $AmountAfterTax = $Base->item(0)->getAttribute("AmountAfterTax");
                                            $CurrencyCode = $Base->item(0)->getAttribute("CurrencyCode");
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $GuestCounts = $RoomStay->item($i)->getElementsByTagName("GuestCounts");
                    if ($GuestCounts->length > 0) {
                        $IsPerRoom = $GuestCounts->item(0)->getAttribute("IsPerRoom");
                        $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
                        if ($GuestCount->length > 0) {
                            for ($iAux2=0; $iAux2 < $GuestCount->length; $iAux2++) { 
                                $AgeQualifyingCode = $GuestCount->item($iAux2)->getAttribute("AgeQualifyingCode");
                                $Count = $GuestCount->item($iAux2)->getAttribute("Count");
                                $ResGuestRPH = $GuestCount->item($iAux2)->getAttribute("ResGuestRPH");
                            }
                        }
                    }
                    $BasicPropertyInfo = $RoomStay->item($i)->getElementsByTagName("BasicPropertyInfo");
                    if ($BasicPropertyInfo->length > 0) {
                        $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                        $shid = $HotelCode;
                        $sfilter[] = " sid='$HotelCode' ";
                        $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
                    }
                    $CancelPenalties = $RoomStay->item($i)->getElementsByTagName("CancelPenalties");
                    if ($CancelPenalties->length > 0) {
                        $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                        if ($CancelPenalty->length > 0) {
                            $Deadline = $CancelPenalty->item(0)->getElementsByTagName("Deadline");
                            if ($Deadline->length > 0) {
                                $AbsoluteDeadline = $Deadline->item(0)->getAttribute("AbsoluteDeadline");
                            }
                        }
                    }
                    $Total = $RoomStay->item($i)->getElementsByTagName("Total");
                    if ($Total->length > 0) {
                        $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                        $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                    }
                    $cent = substr($AmountAfterTax, -2);
                    $amount = substr($AmountAfterTax, 0, (strlen($AmountAfterTax) - 2));
                    $total2 = $amount . '.' . $cent;
                    $total = (double)$total2;
                    $nettotal = $total;

                    $zRooms = 0;
                    if (is_array($tmp[$shid])) {
                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                    } else {
                        $baseCounterDetails = 0;
                    }
                    
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $HotelCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $RoomTypeCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-40";
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $Text;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $Text;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $RoomTypeCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RatePlanCode'] = $RatePlanCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $nettotal;
                    if ($atiMarkup != 0) {
                        $total = $total + (($total * $atiMarkup) / 100);
                    }
                    // Geo target markup
                    if ($internalmarkup != 0) {
                        $total = $total + (($total * $internalmarkup) / 100);
                    }
                    // Agent markup
                    if ($agent_markup != 0) {
                        $total = $total + (($total * $agent_markup) / 100);
                    }
                    // Fallback Markup
                    if ($atiMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                        $total = $total + (($total * $HotelsMarkupFallback) / 100);
                    }
                    // Agent discount
                    if ($agent_discount != 0) {
                        $total = $total - (($total * $agent_discount) / 100);
                    }
                    if ($scurrency != "" and $currency != $scurrency) {
                        $total = $CurrencyConverter->convert($total, $currency, $scurrency);
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                    try {
                        $sql = "select mapped from board_mapping where description='" . addslashes($BreakfastTypeName) . "'";
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        $row_board_mapping = $statement->execute();
                        $row_board_mapping->buffer();
                        if ($row_board_mapping->valid()) {
                            $row_board_mapping = $row_board_mapping->current();
                            $BreakfastTypeName = $row_board_mapping["mapped"];
                        }
                    } catch (\Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($BreakfastTypeName);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    $amount = $total / $noOfNights;
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                        $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                        $pricebreakdownCount = $pricebreakdownCount + 1;
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                    //
                    // Special
                    //
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                    //
                    // Cancel Policies
                    //
                    $date = date('Y-m-d', strtotime($AbsoluteDeadline));
                    $deadline = strtotime($date);
                    $cancelpolicy_deadline = strftime("%a, %e %b %Y", $deadline);
                    $cancelpolicy = 'You must cancel a booking before ' . $cancelpolicy_deadline;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $translator->translate($cancelpolicy);
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $cancelpolicy_deadline;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy'] = $translator->translate($cancelpolicy);
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadline'] = $cancelpolicy_deadline;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;
                    
                    $count = $count + 1;
                }
            }
            $ati = true;
        }
    }
}
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($ati == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mamericantours where " . $sfilter;
        // error_log("\r\n SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        $result2->buffer();
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            foreach ($resultSet2 as $row2) {
                // $sidfilter[] = "id=" . $row2->hid;
                $sidfilter[] = $row2->hid;
                if (is_array($hotels_array[$row2->hid])) {
                    // Append to original details
                    $tmph = $hotels_array[$row2->hid]['details'];
                    $tmps = $tmp[$row2->sid]['details'];
                    foreach ($tmph as $key => $value) {
                        $last = count($tmph[$key]);
                        foreach ($tmps[$key] as $keyd => $valued) {
                            $tmph[$key][$last] = $valued;
                            $last ++;
                        }
                    }
                    $hotels_array[$row2->hid]['details'] = $tmph;
                } else {
                    $hotels_array[$row2->hid] = $tmp[$row2->sid];
                }
            }
        }
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 40;
        // error_log("\r\n Query: $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_ati');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_ati');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $response,
                'data' => base64_encode(serialize($hotels_array)),
                'searchsettings' => base64_encode(serialize($requestdata))
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
}
// error_log("\r\End ATI\r\n", 3, "/srv/www/htdocs/error_log");
?>