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
$actiontravel = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
error_log("\r\n COMECOU ACTIONTRAVEL QUINTA FEIRA MANHA 12H00 \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select name, country_id, zone_id, city_xml35, code from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml35 = $row_settings["city_xml35"];
    $code = $row_settings["code"];
} else {
    $city_xml35 = "";
}
error_log("\r\n city_xml35  $city_xml35  \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='enableactiontravel' and affiliate_id=$affiliate_id";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_actiontravel = $affiliate_id;
} else {
    $affiliate_id_actiontravel = 0;
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
    $sql = "select value from settings where name='ActionTravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_actiontravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='ActionTravelLoginEmail' and affiliate_id=$affiliate_id_actiontravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $ActionTravelLoginEmail = $row_settings['value'];
}
error_log("\r\n ActionTravelLoginEmail  $ActionTravelLoginEmail  \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='ActionTravelPassword' and affiliate_id=$affiliate_id_actiontravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $ActionTravelPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='ActionTravelMarkup' and affiliate_id=$affiliate_id_actiontravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $ActionTravelMarkup = (double) $row_settings['value'];
} else {
    $ActionTravelMarkup = 0;
}
error_log("\r\n ActionTravelMarkup  $ActionTravelMarkup  \r\n", 3, "/srv/www/htdocs/error_log");
// URL
$sql = "select value from settings where name='ActionTravelServiceURL' and affiliate_id=$affiliate_id_actiontravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $ActionTravelServiceURL = $row_settings['value'];
}
error_log("\r\n ActionTravelServiceURL  $ActionTravelServiceURL  \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='ActionTravelb2cMarkup' and affiliate_id=$affiliate_id_actiontravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $ActionTravelb2cMarkup = $row_settings['value'];
}

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$date = new Datetime();
$timestamp = $date->format('U');

error_log("\r\n ANTES RAW $city_xml35 \r\n", 3, "/srv/www/htdocs/error_log");
$city_xml35 = 1224;
if ($city_xml35 != "") {
    error_log("\r\n ENTROU IF \r\n", 3, "/srv/www/htdocs/error_log");
    $raw = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><OTA_HotelAvailService xmlns="http://www.opentravel.org/OTA/2003/05"><OTA_HotelAvailRQ PrimaryLangID="en"><POS><Source AgentDutyCode="' . $ActionTravelLoginEmail . '"><RequestorID Type="1" MessagePassword="' . $ActionTravelPassword . '"/></Source></POS><AvailRequestSegments><AvailRequestSegment><StayDateRange End="' . strftime("%Y-%m-%d", $to) . '" Start="' . strftime("%Y-%m-%d", $from) . '" /><RoomStayCandidates>';
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        $raw = $raw . '<RoomStayCandidate Quantity="1"><GuestCounts><GuestCount Count="' . $selectedAdults[$r] . '" />';
        if ($selectedChildren[$r] > 0) {
            for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                $raw = $raw . '<GuestCount Age="' . $selectedChildrenAges[$r][$z] . '" Count="1" />';
            }
        }
        $raw = $raw . '</GuestCounts></RoomStayCandidate>';
    }
    $raw = $raw . '</RoomStayCandidates><HotelSearchCriteria><Criterion><HotelRef HotelCityCode="' . $city_xml35 . '"/><TPA_Extensions><ShowBasicInfo>0</ShowBasicInfo><ShowCatalogueData>0</ShowCatalogueData><ShowNettPrice>1</ShowNettPrice><ShowOnlyAvailable>1</ShowOnlyAvailable><ShowPromotions>1</ShowPromotions><ShowCancellationPolicy>1</ShowCancellationPolicy><ShowDailyAvailabilityBreakdown>1</ShowDailyAvailabilityBreakdown></TPA_Extensions></Criterion></HotelSearchCriteria></AvailRequestSegment></AvailRequestSegments></OTA_HotelAvailRQ></OTA_HotelAvailService></soap:Body></soap:Envelope>';
     error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
    if ($ActionTravelServiceURL != "" and $ActionTravelLoginEmail != "" and $ActionTravelPassword != "") {
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ActionTravelServiceURL . "OTA_HotelAvail.asmx");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/xml",
            "Content-type: text/xml",
            "Accept-Encoding: gzip, deflate",
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
        curl_close($ch);
        $response = gzdecode($response);
        error_log("\r\n RESPONSE ACTIONTRAVEL: $response \r\n", 3, "/srv/www/htdocs/error_log");

        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_actiontravel');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $ActionTravelServiceURL . $raw,
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
        
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $Envelope = $inputDoc->getElementsByTagName('Envelope');
        $Body = $Envelope->item(0)->getElementsByTagName('Body');
        $OTA_HotelAvailServiceResponse = $Body->item(0)->getElementsByTagName('OTA_HotelAvailServiceResponse');
        $OTA_HotelAvailRS = $OTA_HotelAvailServiceResponse->item(0)->getElementsByTagName('OTA_HotelAvailRS');
        if ($OTA_HotelAvailRS->length > 0) {
            $SequenceNmbr = $OTA_HotelAvailRS->item(0)->getAttribute("SequenceNmbr");
            $IntCode = $OTA_HotelAvailRS->item(0)->getAttribute("IntCode");
        } else {
            $SequenceNmbr = "";
            $IntCode = "";
        }
        $RoomStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName('RoomStays');
        $node = $RoomStays->item(0)->getElementsByTagName('RoomStay');
        for ($c = 0; $c < $node->length; $c++) {
            $BasicPropertyInfo = $node->item($c)->getElementsByTagName("BasicPropertyInfo");
            if ($BasicPropertyInfo->length > 0) {
                $baseCounterDetails = 0;
                $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                $shid = $HotelCode;
                $sfilter[] = " sid='$HotelCode' ";
                error_log("\r\n HotelCode: $HotelCode \r\n", 3, "/srv/www/htdocs/error_log");
                // $BestDeal = $node->item($c)->getAttribute("BestDeal");
                $RoomRates = $node->item($c)->getElementsByTagName("RoomRates");
                $RoomRate = $node->item($c)->getElementsByTagName("RoomRate");
                for ($y = 0; $y < $RoomRate->length; $y ++) {
                    $NonRefundable = $RoomRate->item($y)->getElementsByTagName("NonRefundable");
                    if ($NonRefundable->length > 0) {
                        $NonRefundable = $NonRefundable->item(0)->nodeValue;
                    } else {
                        $NonRefundable = 0;
                    }
                    $PenaltyDescription = $RoomRate->item($y)->getElementsByTagName("CancellationPolicyRules");
                    if ($PenaltyDescription->length > 0) {
                        $cancellationPolicy = '';
                        $xCurrencyCode = $PenaltyDescription->item(0)->getAttribute("CurrencyCode");
                        $Rule = $PenaltyDescription->item(0)->getElementsByTagName("Rule");
                        for ($yRule = 0; $yRule < $Rule->length; $yRule ++) {
                            $rFrom = $Rule->item($yRule)->getAttribute("From");
                            $rTo = $Rule->item($yRule)->getAttribute("To");
                            $rDateFrom = $Rule->item($yRule)->getAttribute("DateFrom");
                            $rDateTo = $Rule->item($yRule)->getAttribute("DateTo");
                            $rType = $Rule->item($yRule)->getAttribute("Type");
                            $rFixedPrice = $Rule->item($yRule)->getAttribute("FixedPrice");
                            $rPercentPrice = $Rule->item($yRule)->getAttribute("PercentPrice");
                            $rNights = $Rule->item($yRule)->getAttribute("Nights");
                            $rFirstNightPrice = $Rule->item($yRule)->getAttribute("FirstNightPrice");
                            $rApplicationTypeNights = $Rule->item($yRule)->getAttribute("ApplicationTypeNights");
                            if ($cancellationPolicy != "") {
                                $cancellationPolicy .= "<br/>";
                            }
                            if ($rFixedPrice == "0.00" and $rNights == "0.00" and $rPercentPrice == "0.00" and $rApplicationTypeNights == "") {
                                $t = gettext("No penalty");
                            } elseif ($rFixedPrice != "0.00") {
                                $t = $xCurrencyCode . $rFixedPrice . " " . gettext("penalty");
                            } elseif ($rPercentPrice != "0.00") {
                                $t = $rPercentPrice . "% " . gettext("penalty");
                            } elseif ($rApplicationTypeNights != "") {
                                if ($rApplicationTypeNights == "FirstNight") {
                                    $t = gettext("Pay First Night");
                                    if ($rFirstNightPrice != "") {
                                        $t = $t . " " . $rFirstNightPrice . " " . $xCurrencyCode;
                                    }
                                } elseif ($rApplicationTypeNights == "Average") {
                                    $t = $rNights . " " . gettext("Average price of all nights");
                                }
                            }
                            if ($rType == "S") {
                                $cancellationPolicy .= gettext("No Show:") . " " . $t;
                            } elseif ($rType == "V") {
                                if ($rFrom != "") {
                                    $cancellationPolicy .= gettext("Cancelling") . " " . $rFrom . " ";
                                    if ($rTo != "") {
                                        $cancellationPolicy .= gettext("to") . " " . $rTo . " ";
                                    }
                                    $cancellationPolicy .= gettext("day(s) before check-in date you have") . " " . strtolower($t);
                                }
                            } elseif ($rType == "R") {
                                if ($rFrom != "") {
                                    $cancellationPolicy .= gettext("Cancelling") . " " . $rFrom . " ";
                                    if ($rTo != "") {
                                        $cancellationPolicy .= gettext("to") . " " . $rTo . " ";
                                    }
                                    $cancellationPolicy .= gettext("day(s) after confirmation would incur") . " " . strtolower($t);
                                }
                            }
                            // $rDateFrom / $rDateTo
                        }
                    } else {
                        $cancellationPolicy = "";
                    }
                    $RatePlanCode = $RoomRate->item($y)->getAttribute("RatePlanCode");
                    $AvailabilityStatus = $RoomRate->item($y)->getAttribute("AvailabilityStatus");
                    $Board = $RoomRate->item($y)->getAttribute("RatePlanCategory");
                    $BoardType = "";
                    $tRooms = 0;

                    $Features = $RoomRate->item($y)->getElementsByTagName("Features");
                    $Feature = $Features->item(0)->getElementsByTagName("Feature");
                    $RoomViewCode = $Feature->item(0)->getAttribute("RoomViewCode");

                    $Rates = $RoomRate->item($y)->getElementsByTagName("Rates");
                    if ($Rates->length > 0) {
                        $Rates = $Rates->item(0)->getElementsByTagName("Rate");
                        if ($Rates->length > 0) {
                            for ($zRooms = 0; $zRooms < $Rates->length; $zRooms ++) {
                                $Units = $Rates->item($zRooms)->getAttribute("NumberOfUnits");
                                for ($x = 0; $x < $Units; $x ++) {
                                    $Source = $Rates->item($zRooms)->getAttribute("RateSource");
                                    $RateMode = $Rates->item($zRooms)->getAttribute("RateMode");
                                    $Name = $Rates->item($zRooms)->getElementsByTagName("RateDescription");
                                    if ($Name->length > 0) {
                                        $Name = $Name->item(0)->getElementsByTagName("Text");
                                        if ($Name->length > 0) {
                                            $Name = $Name->item(0)->nodeValue;
                                        } else {
                                            $Name = "";
                                        }
                                    } else {
                                        $Name = "";
                                    }
                                    $Total = $Rates->item($zRooms)->getElementsByTagName("Total");
                                    if ($Total->length > 0) {
                                        $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                                        $Total = $Total->item(0)->getAttribute("AmountAfterTax");
                                    } else {
                                        $Total = 0;
                                        $CurrencyCode = "";
                                    }
            
            
            for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                if (is_array($tmp[$shid])) {
                    $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                } else {
                    $baseCounterDetails = 0;
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $HotelCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-35";
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $Name;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $RoomCategory;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomType'] = $RoomCategory;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateCode'] = $SELL_PRICE_ID;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $RoomViewCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['OCCUPANCY'] = $OCCUPANCY;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $Total;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $Total;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['NonRefundable'] = $nonRefundable;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['Recommended'] = $ISRECOMMENDEDPRODUCT;
                
                /*
                 * if ($PromotionCode != "") {
                 * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                 * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $PromotionCode;
                 * } else {
                 */
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                // }
                if ($Board == "") {
                    $Board = "Room Only";
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($Board);
                } else {
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($Board);
                }
                
                $pricebreakdown = array();
                $pricebreakdownCount = 0;
                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                    $amount = $Total / $noOfNights;
                    if ($ActionTravelMarkup != 0) {
                        $amount = $amount + (($amount * $ActionTravelMarkup) / 100);
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
                    if ($ActionTravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $scurrency;
                $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $cancellationPolicy;
                $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicies'] = $cancellationPolicy;
            }
        }
    }
}
                    }
                }
            }
        }
        $actiontravel = true;
    }
}
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($actiontravel == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mactiontravel where " . $sfilter;
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
        $supplier = 35;
        error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_actiontravel');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_actiontravel');
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
// }
?>