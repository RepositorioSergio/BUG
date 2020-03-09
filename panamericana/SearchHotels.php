<?php
$scurrency = strtoupper($currency);
unset($tmp);
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
// error_log("\r\nSearch Panamericana\r\n", 3, "/srv/www/htdocs/error_log");
if (! function_exists('xml2array')) {

    function xml2array($contents, $get_attributes = 1)
    {
        if (! $contents)
            return array();
        // if (! function_exists('xml_parser_create')) {
        // return array();
        // }
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $contents, $xml_values);
        xml_parser_free($parser);
        if (! $xml_values)
            return;
        // Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();
        $current = &$xml_array;
        foreach ($xml_values as $data) {
            unset($attributes, $value);
            extract($data);
            $result = '';
            if ($get_attributes) {
                $result = array();
                if (isset($value))
                    $result["value"] = $value;
                if (isset($attributes)) {
                    foreach ($attributes as $attr => $val) {
                        if ($get_attributes == 1)
                            $result["attr"][$attr] = $val;
                    }
                }
            } elseif (isset($value)) {
                $result = $value;
            }
            if ($type == "open") {
                $parent[$level - 1] = &$current;
                if (! is_array($current) or (! in_array($tag, array_keys($current)))) {
                    $current[$tag] = $result;
                    $current = &$current[$tag];
                } else {
                    if (isset($current[$tag][0])) {
                        array_push($current[$tag], $result);
                    } else {
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        );
                    }
                    $last = count($current[$tag]) - 1;
                    $current = &$current[$tag][$last];
                }
            } elseif ($type == "complete") {
                if (! isset($current[$tag])) {
                    $current[$tag] = $result;
                } else {
                    if ((is_array($current[$tag]) and $get_attributes == 0) or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
                        array_push($current[$tag], $result);
                    } else {
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        );
                    }
                }
            } elseif ($type == 'close') {
                $current = &$parent[$level - 1];
            }
        }
        return ($xml_array);
    }
}
$sql = "select value from settings where name='panamericanaServiceURL' and affiliate_id=$affiliate_id_panamericana";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaServiceURL = $row_settings["value"];
}
$sql = "select value from settings where name='panamericanaID' and affiliate_id=$affiliate_id_panamericana";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaID = $row_settings["value"];
}
$sql = "select value from settings where name='panamericanaPassword' and affiliate_id=$affiliate_id_panamericana";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaPassword = base64_decode($row_settings["value"]);
}
$sql = "select value from settings where name='panamericanaMarkup' and affiliate_id=$affiliate_id_panamericana";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaMarkup = (int) $row_settings["value"];
}
$sql = "select value from settings where name='panamericanaCurrencyCode' and affiliate_id=$affiliate_id_panamericana";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaCurrencyCode = $row_settings["value"];
} else {
    $panamericanaCurrencyCode = "";
}
$sql = "select value from settings where name='panamericanaTimeout' and affiliate_id=$affiliate_id_panamericana";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaTimeout = (int) $row_settings["value"];
} else {
    $panamericanaTimeout = 0;
}
$sql = "select name, country_id, zone_id, city_xml69, code from cities where id=" . $destination;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $country_selected = (int) $row_settings["country_id"];
    $state_selected = (int) $row_settings["zone_id"];
    $geo_location_name = $row_settings["name"];
    $DestinationZone = (int) $row_settings["city_xml69"];
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
        $sql = "select value from settings where name='panamericanaNationalityIsoCode2' and affiliate_id=$affiliate_id_panamericana";
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
    $sql = "select value from settings where name='panamericanaNationalityIsoCode2' and affiliate_id=$affiliate_id_panamericana";
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
if ($DestinationZone != "") {
    $xmlrequest = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><OTA_HotelAvailService xmlns="http://www.opentravel.org/OTA/2003/05"><OTA_HotelAvailRQ PrimaryLangID="en"><POS><Source AgentDutyCode="' . $panamericanaID . '"><RequestorID Type="1" MessagePassword="' . $panamericanaPassword . '"/></Source></POS><AvailRequestSegments><AvailRequestSegment><StayDateRange End="' . strftime("%Y-%m-%d", $to) . '" Start="' . strftime("%Y-%m-%d", $from) . '" /><RoomStayCandidates>';
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        $xmlrequest .= '<RoomStayCandidate Quantity="1"><GuestCounts><GuestCount Count="' . $selectedAdults[$r] . '" />';
        if ($selectedChildren[$r] > 0) {
            for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                $xmlrequest .= '<GuestCount Age="' . $selectedChildrenAges[$r][$z] . '" Count="1" />';
            }
        }
        $xmlrequest .= '</GuestCounts></RoomStayCandidate>';
    }
    $xmlrequest .= '</RoomStayCandidates><HotelSearchCriteria><Criterion><HotelRef HotelCityCode="' . $DestinationZone . '" /><TPA_Extensions><ShowBasicInfo>0</ShowBasicInfo><ShowCatalogueData>0</ShowCatalogueData><ShowNettPrice>1</ShowNettPrice><ShowOnlyAvailable>1</ShowOnlyAvailable><ShowPromotions>1</ShowPromotions><ShowCancellationPolicy>0</ShowCancellationPolicy><ShowDailyAvailabilityBreakdown>0</ShowDailyAvailabilityBreakdown>';
    if ($panamericanaCurrencyCode != "") {
        $xmlrequest .= '<ForceCurrency>USD</ForceCurrency>';
    }
    if ($sourceMarket != "") {
        $xmlrequest .= '<PaxCountry>' . $sourceMarket . '</PaxCountry>';
    }
    $xmlrequest .= '</TPA_Extensions></Criterion></HotelSearchCriteria></AvailRequestSegment></AvailRequestSegments></OTA_HotelAvailRQ></OTA_HotelAvailService></soap:Body></soap:Envelope>';
    // <ShowCancellationPolicy>1</ShowCancellationPolicy>
    // <ShowDailyAvailabilityBreakdown>1</ShowDailyAvailabilityBreakdown>
    // <ShowOnlyBestPriceCombination>1</ShowOnlyBestPriceCombination>
    // error_log("\r\nSearch Panamericana - EOF - $xmlrequest\r\n", 3, "/srv/www/htdocs/error_log");
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $panamericanaServiceURL . "OTA_HotelAvail.asmx");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlrequest);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if ($panamericanaTimeout == 0) {
        $panamericanaTimeout = 120;
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $panamericanaTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $panamericanaTimeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/xml",
        "Content-type: text/xml",
        "Accept-Encoding: gzip, deflate",
        "Content-length: " . strlen($xmlrequest)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $xmlresult = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_panamericana');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $xmlrequest,
            'sqlcontext' => $xmlresult,
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
    if ($xmlresult != "") {
        // error_log("\r\n $xmlresult \r\n", 3, "/srv/www/htdocs/error_log");
        $parse = xml2array($xmlresult);
        $translator = new Translator();
        if (file_exists("src/App/language/" . $lang . ".mo")) {
            $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
        }
        $SequenceNmbr = $parse["soap:Envelope"]["soap:Body"]["OTA_HotelAvailServiceResponse"]["OTA_HotelAvailRS"]["attr"]["SequenceNmbr"];
        $parse = $parse["soap:Envelope"]["soap:Body"]["OTA_HotelAvailServiceResponse"]["OTA_HotelAvailRS"]["RoomStays"]["RoomStay"];
        foreach ($parse as $key => $val) {
            $baseCounterDetails = 0;
            $Code = $val["BasicPropertyInfo"]["attr"]["HotelCode"];
            if ($val["RoomRates"]["RoomRate"]["Total"]["attr"]["CurrencyCode"] != "") {
                $base_currency = $val["RoomRates"]["RoomRate"]["Total"]["attr"]["CurrencyCode"];
            }
            // Removed / Before $val["RoomRates"]["RoomRate"]
            // foreach ($val["RoomRates"]["RoomRate"] as $keyRoomRate => $valRoomRate) {
            foreach ($val["RoomRates"]["RoomRate"] as $keyRoomRate => $valRoomRate) {
                $NonRefundable = $valRoomRate["TPA_Extensions"]["NonRefundable"]["value"];
                $RatePlanCode = $valRoomRate["attr"]["RatePlanCode"];
                $AvailabilityStatus = $valRoomRate["attr"]["AvailabilityStatus"];
                $Board = $valRoomRate["attr"]["RatePlanCategory"];
                if ($valRoomRate["TPA_Extensions"]["RecommendedPrice"]["attr"]["CurrencyCode"] != "") {
                    $base_currency = $valRoomRate["TPA_Extensions"]["RecommendedPrice"]["attr"]["CurrencyCode"];
                }
                if (is_array($valRoomRate["Rates"])) {
                    foreach ($valRoomRate["Rates"] as $keyRates => $valRates) {
                        if (is_array($valRoomRate["Features"]["Feature"])) {
                            if ($valRoomRate["Features"]["Feature"][0]["attr"]["RoomViewCode"] == "PROMO" or $valRoomRate["Features"]["Feature"][1]["attr"]["RoomViewCode"] == "PROMO" or $valRoomRate["Features"]["Feature"][2]["attr"]["RoomViewCode"] == "PROMO" or $valRoomRate["Features"]["Feature"][3]["attr"]["RoomViewCode"] == "PROMO") {
                                $special = true;
                                if ($valRoomRate["Features"]["Feature"][0]["attr"]["RoomViewCode"] == "PROMO") {
                                    if ($valRoomRate["Features"]["Feature"][0]["Description"]["attr"]["Name"] == "VALUE") {
                                        $specialdescription = $valRoomRate["Features"]["Feature"][0]["Description"]["Text"]["value"];
                                    }
                                    if ($specialdescription == "") {
                                        if ($valRoomRate["Features"]["Feature"][0]["Description"][0]["attr"]["Name"] == "VALUE") {
                                            $specialdescription = $valRoomRate["Features"]["Feature"][0]["Description"][0]["Text"]["value"];
                                        }
                                        if ($specialdescription == "") {
                                            if ($valRoomRate["Features"]["Feature"][0]["Description"][1]["attr"]["Name"] == "DESC") {
                                                $specialdescription = $valRoomRate["Features"]["Feature"][0]["Description"][1]["Text"]["value"];
                                            }
                                        }
                                    }
                                    if ($specialdescription == "NON REFUNDABLE" or $specialdescription == "Non-refundable rate. No amendments permitted") {
                                        $specialdescription = "";
                                        $special = false;
                                    }
                                } elseif ($valRoomRate["Features"]["Feature"][1]["attr"]["RoomViewCode"] == "PROMO") {
                                    if ($valRoomRate["Features"]["Feature"][1]["Description"]["attr"]["Name"] == "VALUE") {
                                        $specialdescription = $valRoomRate["Features"]["Feature"][1]["Description"]["Text"]["value"];
                                    }
                                    if ($specialdescription == "") {
                                        if ($valRoomRate["Features"]["Feature"][1]["Description"][0]["attr"]["Name"] == "VALUE") {
                                            $specialdescription = $valRoomRate["Features"]["Feature"][1]["Description"][0]["Text"]["value"];
                                        }
                                        if ($specialdescription == "") {
                                            if ($valRoomRate["Features"]["Feature"][1]["Description"][1]["attr"]["Name"] == "DESC") {
                                                $specialdescription = $valRoomRate["Features"]["Feature"][1]["Description"][1]["Text"]["value"];
                                            }
                                        }
                                    }
                                    if ($specialdescription == "NON REFUNDABLE" or $specialdescription == "Non-refundable rate. No amendments permitted") {
                                        $specialdescription = "";
                                        $special = false;
                                    }
                                } elseif ($valRoomRate["Features"]["Feature"][2]["attr"]["RoomViewCode"] == "PROMO") {
                                    if ($valRoomRate["Features"]["Feature"][2]["Description"]["attr"]["Name"] == "VALUE") {
                                        $specialdescription = $valRoomRate["Features"]["Feature"][2]["Description"]["Text"]["value"];
                                    }
                                    if ($specialdescription == "") {
                                        if ($valRoomRate["Features"]["Feature"][2]["Description"][0]["attr"]["Name"] == "VALUE") {
                                            $specialdescription = $valRoomRate["Features"]["Feature"][2]["Description"][0]["Text"]["value"];
                                        }
                                        if ($specialdescription == "") {
                                            if ($valRoomRate["Features"]["Feature"][2]["Description"][1]["attr"]["Name"] == "DESC") {
                                                $specialdescription = $valRoomRate["Features"]["Feature"][2]["Description"][1]["Text"]["value"];
                                            }
                                        }
                                    }
                                    if ($specialdescription == "NON REFUNDABLE" or $specialdescription == "Non-refundable rate. No amendments permitted") {
                                        $specialdescription = "";
                                        $special = false;
                                    }
                                } elseif ($valRoomRate["Features"]["Feature"][3]["attr"]["RoomViewCode"] == "PROMO") {
                                    if ($valRoomRate["Features"]["Feature"][3]["Description"]["attr"]["Name"] == "VALUE") {
                                        $specialdescription = $valRoomRate["Features"]["Feature"][3]["Description"]["Text"]["value"];
                                    }
                                    if ($specialdescription == "") {
                                        if ($valRoomRate["Features"]["Feature"][3]["Description"][0]["attr"]["Name"] == "VALUE") {
                                            $specialdescription = $valRoomRate["Features"]["Feature"][3]["Description"][0]["Text"]["value"];
                                        }
                                        if ($specialdescription == "") {
                                            if ($valRoomRate["Features"]["Feature"][3]["Description"][1]["attr"]["Name"] == "DESC") {
                                                $specialdescription = $valRoomRate["Features"]["Feature"][3]["Description"][1]["Text"]["value"];
                                            }
                                        }
                                    }
                                    if ($specialdescription == "NON REFUNDABLE" or $specialdescription == "Non-refundable rate. No amendments permitted") {
                                        $specialdescription = "";
                                        $special = false;
                                    }
                                } else {
                                    $specialdescription = "";
                                    $special = false;
                                }
                            } else {
                                $specialdescription = "";
                                $special = false;
                            }
                        } else {
                            $specialdescription = "";
                            $special = false;
                        }
                        
                        if (! is_array($valRates[0])) {
                            $t1[0] = $valRates;
                            $valRates = $t1;
                        }
                        if (is_array($valRates)) {
                            foreach ($valRates as $keyRate => $valRate) {
                                $Name = $valRate["RateDescription"]["Text"]["value"];
                                if ($Name != "") {
                                    $Units = $valRate["attr"]["NumberOfUnits"];
                                    $RateSource = $valRate["attr"]["RateSource"];
                                    $Total = $valRate["Total"]["attr"]["AmountAfterTax"];
                                    if ($base_currency == "") {
                                        if ($valRate["Total"]["attr"]["CurrencyCode"] != "") {
                                            $base_currency = $valRate["Total"]["attr"]["CurrencyCode"];
                                        }
                                    }
                                    if ($base_currency == "" and $panamericanaCurrencyCode != "") {
                                        $base_currency = $panamericanaCurrencyCode;
                                    }
                                    $nettotal = $valRate["Total"]["attr"]["AmountAfterTax"];
                                    if (intval($Total) > 0) {
                                        $tRooms = $RateSource - 1;
                                        // Markup
                                        if ($panamericanaMarkup > 0) {
                                            $Total = $Total + (($Total * $panamericanaMarkup) / 100);
                                        }
                                        // Geo target markup
                                        if ($internalmarkup > 0) {
                                            $Total = $Total + (($Total * $internalmarkup) / 100);
                                        }
                                        // Agent markup
                                        if ($agent_markup > 0) {
                                            $Total = $Total + (($Total * $agent_markup) / 100);
                                        }
                                        // Fallback Markup
                                        if ($panamericanaMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                            $Total = $Total + (($Total * $HotelsMarkupFallback) / 100);
                                        }
                                        // Agent discount
                                        if ($agent_discount > 0) {
                                            $Total = $Total - (($Total * $agent_discount) / 100);
                                        }
                                        if ($base_currency != $scurrency) {
                                            $Total = $CurrencyConverter->convert($Total, $base_currency, $scurrency);
                                        }
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["total"] = (double) $Total;
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["nettotal"] = $nettotal;
                                        $Name = str_replace("&lt;p&gt;", "", $Name);
                                        $Name = str_replace("&amp;", " ", $Name);
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["room"] = substr($Name, 0, 150);
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["scurrency"] = $base_currency;
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["smbr"] = $SequenceNmbr;
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["rid"] = $RatePlanCode;
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["shid"] = $Code;
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["adults"] = $selectedAdults[$tRooms];
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["children"] = $selectedChildren[$tRooms];
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-69";
                                        if ($special == true) {
                                            $tmp[$Code]['special'] = true;
                                            $tmp[$Code]['details'][$tRooms][$baseCounterDetails]['special'] = true;
                                            $tmp[$Code]['details'][$tRooms][$baseCounterDetails]['specialdescription'] = $specialdescription;
                                        }
                                        if ($Board == '') {
                                            $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["meal"] = $translator->translate("Room Only");
                                        } else {
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
                                            $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["meal"] = $translator->translate($Board);
                                        }
                                        // $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["pricebreakdown"] = $pricebreakdown;
                                        $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["pricebreakdown"] = "";
                                        if ($AvailabilityStatus == "AvailableForSale") {
                                            $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["status"] = 1;
                                        } else {
                                            $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["status"] = 4;
                                        }
                                        if ($NonRefundable == 1) {
                                            $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["nonrefundable"] = true;
                                        } else {
                                            $tmp[$Code]["details"][$tRooms][$baseCounterDetails]["nonrefundable"] = false;
                                        }
                                        if ($rooms > 1) {
                                            $tmp[$Code]['details'][$tRooms][$baseCounterDetails]['nodedup'] = true;
                                        } else {
                                            $tmp[$Code]['details'][$tRooms][$baseCounterDetails]['nodedup'] = false;
                                        }
                                        // OK
                                        $baseCounterDetails ++;
                                        $sfilter[] = " sid='" . $Code . "'";
                                        $valid = 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        unset($parse);
    }
}
if ($valid == 1) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mpanamericana where " . $sfilter;
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $result = $statement->execute();
        $result->buffer();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new ResultSet();
            $resultSet->initialize($result);
            foreach ($resultSet as $row) {
                // $sidfilter[] = "id=" . $row->hid;
                $sidfilter[] = $row->hid;
                if (is_array($hotels_array[$row->hid])) {
                    // Append to original details
                    $tmph = $hotels_array[$row->hid]['details'];
                    $tmps = $tmp[$row->sid]['details'];
                    foreach ($tmph as $key => $value) {
                        $last = count($tmph[$key]);
                        foreach ($tmps[$key] as $keyd => $valued) {
                            $tmph[$key][$last] = $valued;
                            $last ++;
                        }
                    }
                    $hotels_array[$row->hid]['details'] = $tmph;
                } else {
                    $hotels_array[$row->hid] = $tmp[$row->sid];
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
        $supplier = 69;
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        // Store Session
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_panamericana');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_panamericana');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $xmlrequest,
                'xmlresult' => (string) $xmlresult,
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
// error_log("\r\nSearch Panamericana - EOF\r\n", 3, "/srv/www/htdocs/error_log");
// Reset data
$xmlrequest = null;
$xmlresult = null;
$sidfilter = null;
$parse = null;
?>