<?php
$scurrency = strtoupper($currency);
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$palladium = false;
$sql = "select city_xml15 from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml15 = $row_settings["city_xml15"];
} else {
    $city_xml15 = "";
}
if ($city_xml15 != "") {
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
        $sql = "select value from settings where name='PalladiumHotelGroupDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_palladium";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='PalladiumHotelGroupusername' and affiliate_id=$affiliate_id_palladium";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $PalladiumHotelGroupusername = $row_settings['value'];
    }
    $sql = "select value from settings where name='PalladiumHotelGroupmarkup' and affiliate_id=$affiliate_id_palladium";
    $statement = $db->createStatement($sql);
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
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $PalladiumHotelGroupserviceurl = $row_settings['value'];
    }
    $dateStart = new DateTime(strftime("%Y-%m-%d", $from));
    $dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
    $noOfNights = $dateStart->diff($dateEnd)->format('%d');
    $date = new Datetime();
    $timestamp = $date->format('U');
    $raw = '<?xml version="1.0" encoding="UTF-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:clo="http://www.cloudhospitality.com" xmlns:ns="http://www.opentravel.org/OTA/2003/05"><soap:Header/><soap:Body><clo:GetAvailability><ns:AvailabilityRequest PrimaryLangID="?"><ns:POS><ns:Source><ns:RequestorID ID="' . $PalladiumHotelGroupusername . '" Type="13"/><ns:BookingChannel Type="2"><ns:CompanyName Code=""/></ns:BookingChannel></ns:Source></ns:POS><ns:AvailRequestSegments><ns:AvailRequestSegment><ns:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '"/><ns:RoomStayCandidates>';
    $rCount = 1;
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        // RoomTypeCode="1"
        $raw .= '<ns:RoomStayCandidate RPH="' . $rCount . '" Quantity="1"><ns:GuestCounts><ns:GuestCount Count="' . $selectedAdults[$r] . '" AgeQualifyingCode="10" Age="30"/>';
        // Person code identifier 10=adult, 8=child, 7=baby
        for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
            if ($selectedChildrenAges[$r][$z] <= 2) {
                $raw .= '<ns:GuestCount Count="1" AgeQualifyingCode="7" Age="' . $selectedChildrenAges[$r][$z] . '"/>';
            } else {
                $raw .= '<ns:GuestCount Count="1" AgeQualifyingCode="8" Age="' . $selectedChildrenAges[$r][$z] . '"/>';
            }
        }
        $raw .= '</ns:GuestCounts></ns:RoomStayCandidate>';
        $rCount = $rCount + 1;
    }
    $raw .= '</ns:RoomStayCandidates><ns:HotelSearchCriteria><ns:Criterion><ns:HotelRef HotelCode="" AreaID="" HotelCityCode="' . $city_xml15 . '"/></ns:Criterion></ns:HotelSearchCriteria></ns:AvailRequestSegment></ns:AvailRequestSegments></ns:AvailabilityRequest></clo:GetAvailability>
</soapenv:Body>
</soapenv:Envelope>';
    if ($PalladiumHotelGroupserviceurl != "" and $PalladiumHotelGroupusername != "") {
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $PalladiumHotelGroupserviceurl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/xml",
            "Content-type: text/xml;charset=UTF-8",
            "Content-Encoding: UTF-8",
            "Content-length: " . strlen($raw)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        // if ($response === false) {
        // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
        // } else {
        // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
        // }
        // error_log("\r\n $PalladiumHotelGroupserviceurl \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\n $raw \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\n RESPONSE PAL6: $response \r\n", 3, "/srv/www/htdocs/error_log");
        curl_close($ch);
        // Descomentar para ver o resultado do provider
        // Nao esquecer de alterar o session id para testar por causa de cache
        // echo $response;
        // die();
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_palladium');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $PalladiumHotelGroupserviceurl . $raw,
                'sqlcontext' => $response,
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
        // echo $response;
        // die();
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $node = $inputDoc->getElementsByTagName('RoomStay');
        for ($c = 0; $c < $node->length; $c ++) {
            $RoomStayCandidateRPH = $node->item($c)->getAttribute("RoomStayCandidateRPH");
            $BasicPropertyInfo = $node->item($c)->getElementsByTagName("BasicPropertyInfo");
            if ($BasicPropertyInfo->length > 0) {
                $ChainName = $BasicPropertyInfo->item(0)->getAttribute("ChainName");
                $ChainCode = $BasicPropertyInfo->item(0)->getAttribute("ChainCode");
                $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
            } else {
                $ChainName = "";
                $ChainCode = "";
                $HotelCode = "";
            }
            $shid = $HotelCode;
            $sfilter[] = " sid='$HotelCode' ";
            $RoomTypesArray = array();
            $RoomTypesArrayCount = 0;
            $RoomTypes = $node->item($c)->getElementsByTagName("RoomTypes");
            $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
            for ($k = 0; $k < $RoomType->length; $k ++) {
                $RoomDescription = $RoomType->item($k)->getElementsByTagName("RoomDescription");
                $RoomDescriptionText = $RoomDescription->item(0)->getElementsByTagName("Text");
                $RoomTypeTmp = $RoomType->item($k)->getAttribute("RoomType");
                $RoomTypeCodeT = $RoomType->item($k)->getAttribute("RoomTypeCode");
                
                $RoomTypesArray[$RoomTypesArrayCount]['RoomType'] = $RoomTypeTmp;
                $RoomTypesArray[$RoomTypesArrayCount]['RoomTypeCode'] = $RoomTypeCodeT;
                $RoomTypesArray[$RoomTypesArrayCount]['RoomDescription'] = $RoomDescriptionText;
                $RoomTypesArrayCount ++;
            }
            
            // RATEPLANS
            // $RatePlans = $node->item($c)->getElementsByTagName("RatePlans");
            // $RatePlan = $RatePlans->item(0)->getElementsByTagName("RatePlan");
            // $Comission = $RatePlan->item(0)->getElementsByTagName("Comission");
            // $Percent = $Comission->item(0)->getAttribute("Percent");
            // error_log("\r\n Percent $Percent \r\n", 3, "/srv/www/htdocs/error_log");
            // $RatePlanCode = $RatePlan->item(0)->getAttribute("RatePlanCode");
            // error_log("\r\n RatePlanCode2 $RatePlanCode \r\n", 3, "/srv/www/htdocs/error_log");
            
            $RoomRates = $node->item($c)->getElementsByTagName("RoomRates");
            $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
            for ($k = 0; $k < $RoomRate->length; $k ++) {
                $RoomTypeCode = $RoomRate->item($k)->getAttribute("RoomTypeCode");
                $RatePlanCode = $RoomRate->item($k)->getAttribute("RatePlanCode");
                $AvailabilityStatus = $RoomRate->item($k)->getAttribute("AvailabilityStatus");
                $PromotionCode = $RoomRate->item($k)->getAttribute("PromotionCode");
                $RatePlanType = $RoomRate->item($k)->getAttribute("RatePlanType");
                $NumberOfUnits = $RoomRate->item($k)->getAttribute("NumberOfUnits");
                $Rates = $RoomRate->item($k)->getElementsByTagName("Rates");
                $Rate = $Rates->item(0)->getElementsByTagName("Rate");
                $Base = $Rate->item(0)->getElementsByTagName("Base");
                $CurrencyCode = $Base->item(0)->getAttribute("CurrencyCode");
                $AmountBeforeTax = $Base->item(0)->getAttribute("AmountBeforeTax");
                $AmountAfterTax = $Base->item(0)->getAttribute("AmountAfterTax");
                
                $CancelPolicies = $Rate->item(0)->getElementsByTagName("CancelPolicies");
                $CancelPenalty = $CancelPolicies->item(0)->getElementsByTagName("CancelPenalty");
                $NonRefundable = $CancelPenalty->item(0)->getElementsByTagName("NonRefundable");
                $Deadline = $CancelPenalty->item(0)->getElementsByTagName("Deadline");
                $AbsoluteDeadline = $Deadline->item(0)->getElementsByTagName("AbsoluteDeadline");
                $OffsetTimeUnit = $Deadline->item(0)->getElementsByTagName("OffsetTimeUnit");
                $OffsetUnitMultiplier = $Deadline->item(0)->getElementsByTagName("OffsetUnitMultiplier");
                
                $AmountPercent = $CancelPenalty->item(0)->getElementsByTagName("AmountPercent");
                $NmbrOfNights = $AmountPercent->item(0)->getElementsByTagName("NmbrOfNights");
                $PenaltyDescription = $CancelPenalty->item(0)->getElementsByTagName("PenaltyDescription");
                $PenaltyDescriptionText = $PenaltyDescription->item(0)->getElementsByTagName("Text");
                $PolicyCode = $CancelPenalty->item(0)->getAttribute("PolicyCode");
                
                $PaymentPolicies = $Rate->item(0)->getElementsByTagName("PaymentPolicies");
                $GuaranteePayment = $PaymentPolicies->item(0)->getElementsByTagName("GuaranteePayment");
                $DescriptionPP = $GuaranteePayment->item(0)->getElementsByTagName("Description");
                $DescriptionTextP = $DescriptionPP->item(0)->getElementsByTagName("Text");
                $PolicyCodePP = $DescriptionPP->item(0)->getAttribute("PolicyCode");
                
                $ExpireDate = $Rate->item(0)->getAttribute("ExpireDate");
                $EffectiveDate = $Rate->item(0)->getAttribute("EffectiveDate");
                
                $Total = $RoomRate->item($k)->getElementsByTagName("Total");
                if ($Total->length > 0) {
                    $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                    $AmountBeforeTax = $Total->item(0)->getAttribute("AmountBeforeTax");
                    $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                } else {
                    $AmountAfterTax = 0;
                    $AmountBeforeTax = 0;
                    $CurrencyCode = "";
                }
                
                if (is_array($RoomTypesArray)) {
                    $key2 = array_search($RoomTypeCodeT, array_column($RoomTypesArray, 'RoomTypeCode'));
                    $RoomType2 = $RoomTypesArray[$k]['RoomType'];
                } else {
                    $RoomType2 = $RoomTypeCode;
                }
                
                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                    // if ($selectedAdults[$zRooms] == $stdAdults) {
                    // Chidlren ??
                    // if ($selectedChildren[$zRooms] == $children) {
                    if (is_array($tmp[$shid])) {
                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                    } else {
                        $baseCounterDetails = 0;
                    }
                    
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $ChainName;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-15";
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomType2;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $RoomTypeCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomType'] = $RoomType2;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RatePlanCode'] = $RatePlanCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $RoomType2;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['supplierType'] = $ChainCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $AmountAfterTax;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $AmountBeforeTax;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['NonRefundable'] = $NonRefundable;
                    if ($PromotionCode != "") {
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $PromotionCode;
                    } else {
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                    }
                    
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($RatePlanCode);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $amount = $noOfNights * $AmountAfterTax;
                        if ($PalladiumHotelGroupmarkup != 0) {
                            $amount = $amount + (($amount * $PalladiumHotelGroupmarkup) / 100);
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
                        if ($PalladiumHotelGroupmarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                    
                    if ($AmountPercent != "") {
                        $cancellationPolicy = gettext('Charge') . ' ' . $AmountPercent . ' ';
                        if ($PenaltyDescriptionText) {
                            $cancellationPolicy = $cancellationPolicy . '(' . $PenaltyDescriptionText . ') ' . gettext("if cancelled after") . ' ' . $EffectiveDate;
                        } else {
                            $cancellationPolicy = $cancellationPolicy . gettext("if cancelled after") . ' ' . $EffectiveDate;
                        }
                    } else {
                        $cancellationPolicy = "";
                    }
                    
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $cancellationPolicy;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicies'] = $cancellationPolicy;
                }
            }
            $palladium = true;
        }
    }
    // error_log("\r\n palladium: $palladium \r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
    
    if ($palladium == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $sql = "select hid, sid from xmlhotels_mpalladium where " . $sfilter;
            // error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        if (is_array($sidfilter)) {
            $sidfilter = implode(',', $sidfilter);
            $query = 'call xmlhotels("' . $sidfilter . '")';
            $supplier = 15;
            try {
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_palladium');
                $delete->where(array(
                    'session_id' => $session_id
                ));
                $statement = $sql->prepareStatementForSqlObject($delete);
                $results = $statement->execute();
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_palladium');
                $insert->values(array(
                    'session_id' => $session_id,
                    'xmlrequest' => (string) $raw,
                    'xmlresult' => (string) $response,
                    'data' => base64_encode(serialize($hotels_array)),
                    'searchsettings' => base64_encode(serialize($requestdata))
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
            } catch (Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
        }
    }
}
?>