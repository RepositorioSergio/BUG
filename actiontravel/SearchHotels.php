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
if (file_exists("src/App/language/" . $lang . ".mo")) {
    $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
}
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$actiontravel = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
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
        $sql = "select value from settings where name='ActionTravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_actiontravel";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        } else {
            $sourceMarket = "";
        }
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
    } else {
        $sourceMarket = "";
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
$sql = "select value from settings where name='ActionTravelb2cMarkup' and affiliate_id=$affiliate_id_actiontravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $ActionTravelb2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='ActionTravelTimeout' and affiliate_id=$affiliate_id_actiontravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $ActionTravelTimeout = (int) $row_settings['value'];
} else {
    $ActionTravelTimeout = 0;
}
$date = new Datetime();
$timestamp = $date->format('U');
if ($city_xml35 != "") {
    $raw = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><OTA_HotelAvailService xmlns="http://www.opentravel.org/OTA/2003/05"><OTA_HotelAvailRQ PrimaryLangID="en"><POS><Source AgentDutyCode="' . $ActionTravelLoginEmail . '"><RequestorID Type="1" MessagePassword="' . $ActionTravelPassword . '"/></Source></POS><AvailRequestSegments><AvailRequestSegment><StayDateRange End="' . strftime("%Y-%m-%d", $to) . '" Start="' . strftime("%Y-%m-%d", $from) . '" /><RoomStayCandidates>';
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        $raw .= '<RoomStayCandidate Quantity="1"><GuestCounts><GuestCount Count="' . $selectedAdults[$r] . '" />';
        if ($selectedChildren[$r] > 0) {
            for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                $raw .= '<GuestCount Age="' . $selectedChildrenAges[$r][$z] . '" Count="1" />';
            }
        }
        $raw .= '</GuestCounts></RoomStayCandidate>';
    }
    $raw .= '</RoomStayCandidates><HotelSearchCriteria><Criterion><HotelRef HotelCityCode="' . $city_xml35 . '"/><TPA_Extensions>';
    if ($sourceMarket != "") {
        $raw .= '<PaxCountry>' . $sourceMarket . '</PaxCountry>';
    }
    $raw .= '<ShowBasicInfo>0</ShowBasicInfo><ShowCatalogueData>0</ShowCatalogueData><ShowNettPrice>1</ShowNettPrice><ShowOnlyAvailable>1</ShowOnlyAvailable><ShowPromotions>1</ShowPromotions><ShowCancellationPolicy>1</ShowCancellationPolicy><ShowDailyAvailabilityBreakdown>1</ShowDailyAvailabilityBreakdown></TPA_Extensions></Criterion></HotelSearchCriteria></AvailRequestSegment></AvailRequestSegments></OTA_HotelAvailRQ></OTA_HotelAvailService></soap:Body></soap:Envelope>';
    if ($ActionTravelServiceURL != "" and $ActionTravelLoginEmail != "" and $ActionTravelPassword != "") {
        if ($ActionTravelTimeout == 0) {
            $ActionTravelTimeout = 120;
        }
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ActionTravelServiceURL . "OTA_HotelAvail.asmx");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $ActionTravelTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $ActionTravelTimeout);
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
        curl_close($ch);
        if ($response != "") {
            $response = gzdecode($response);
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
            } catch (\Exception $e) {
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
            if ($OTA_HotelAvailServiceResponse->length > 0) {
                $OTA_HotelAvailRS = $OTA_HotelAvailServiceResponse->item(0)->getElementsByTagName('OTA_HotelAvailRS');
                if ($OTA_HotelAvailRS->length > 0) {
                    $SequenceNmbr = $OTA_HotelAvailRS->item(0)->getAttribute("SequenceNmbr");
                    $IntCode = $OTA_HotelAvailRS->item(0)->getAttribute("IntCode");
                } else {
                    $SequenceNmbr = "";
                    $IntCode = "";
                }
                $RoomStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName('RoomStays');
                if ($RoomStays->length > 0) {
                    $node = $RoomStays->item(0)->getElementsByTagName('RoomStay');
                    for ($c = 0; $c < $node->length; $c ++) {
                        $BasicPropertyInfo = $node->item($c)->getElementsByTagName("BasicPropertyInfo");
                        if ($BasicPropertyInfo->length > 0) {
                            $baseCounterDetails = 0;
                            $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                            $shid = $HotelCode;
                            $sfilter[] = " sid='$HotelCode' ";
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
                                                $NetTotal = $Total;
                                                if ($ActionTravelMarkup != 0) {
                                                    $Total = $Total + (($Total * $ActionTravelMarkup) / 100);
                                                }
                                                // Geo target markup
                                                if ($internalmarkup != 0) {
                                                    $Total = $Total + (($Total * $internalmarkup) / 100);
                                                }
                                                // Agent markup
                                                if ($agent_markup != 0) {
                                                    $Total = $Total + (($Total * $agent_markup) / 100);
                                                }
                                                // Fallback Markup
                                                if ($ActionTravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                                    $Total = $Total + (($Total * $HotelsMarkupFallback) / 100);
                                                }
                                                // Agent discount
                                                if ($agent_discount != 0) {
                                                    $Total = $Total - (($Total * $agent_discount) / 100);
                                                }
                                                if ($scurrency != "" and $currency != $scurrency) {
                                                    $Total = $CurrencyConverter->convert($Total, $currency, $scurrency);
                                                }
                                                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                                                    if (is_array($tmp[$shid])) {
                                                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                                    } else {
                                                        $baseCounterDetails = 0;
                                                    }
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-35";
                                                    $Name = str_replace("&lt;p&gt;", "", $Name);
                                                    $Name = str_replace("&amp;", " ", $Name);
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = substr($Name, 0, 150);
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $Total;
                                                    $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["smbr"] = $SequenceNmbr;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rid'] = $RatePlanCode;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $NetTotal;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                                    if ($Board == "") {
                                                        $Board = "Room Only";
                                                    }
                                                    try {
                                                        $sql = "select mapped from board_mapping where description='" . addslashes($Board) . "'";
                                                        $statement = $db->createStatement($sql);
                                                        $statement->prepare();
                                                        $row_board_mapping = $statement->execute();
                                                        $row_board_mapping->buffer();
                                                        if ($row_board_mapping->valid()) {
                                                            $row_board_mapping = $row_board_mapping->current();
                                                            $Board = $row_board_mapping["mapped"];
                                                        }
                                                    } catch (\Exception $e) {
                                                        $logger = new Logger();
                                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                                        $logger->addWriter($writer);
                                                        $logger->info($e->getMessage());
                                                    }
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($Board);
                                                    $pricebreakdown = array();
                                                    $pricebreakdownCount = 0;
                                                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                                        $amount = $Total / $noOfNights;
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
        }
    }
}
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
        $supplier = 35;
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
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
?>