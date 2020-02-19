<?php
$query = "";
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
use Zend\Http\Client;
use Zend\Http\Request;
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
$VendorCode = "";
// Sort room details
if (! function_exists('details_sort_minrate_asc')) {

    function details_sort_minrate_asc($a, $b)
    {
        if (defined('SORTORDER_SEARCHPROPERTIES_MIXED')) {
            if (SORTORDER_SEARCHPROPERTIES_MIXED == 1) {
                $SORTORDER_SEARCHPROPERTIES_MIXED = 1;
            } else {
                $SORTORDER_SEARCHPROPERTIES_MIXED = 0;
            }
        } else {
            $SORTORDER_SEARCHPROPERTIES_MIXED = 0;
        }
        if ($SORTORDER_SEARCHPROPERTIES_MIXED == 1) {
            return ($a["Amount_plain"] < $b["Amount_plain"]) ? - 1 : 1;
        } else {
            if ($a["type"] == 0) {
                if ($a["type"] == $b["type"]) {
                    if ($a["type"] == 0) {
                        return ($a["Amount_plain"] < $b["Amount_plain"]) ? - 1 : 1;
                    } else {
                        return ($a["Amount_plain"] < $b["Amount_plain"]) ? - 1 : 1;
                    }
                } else {
                    return ($a["type"] < $b["type"]) ? - 1 : 1;
                }
            } else {
                if ($a["type"] != 0 and $b["type"] != 0) {
                    return ($a["Amount_plain"] < $b["Amount_plain"]) ? - 1 : 1;
                } else {
                    return ($a["type"] < $b["type"]) ? - 1 : 1;
                }
            }
        }
    }
}
$sql = "select value from settings where name='SandosServiceURL' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosServiceURL = $row_settings["value"];
}
$sql = "select value from settings where name='SandosUsername' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosUsername = $row_settings["value"];
}
$sql = "select value from settings where name='Sandospassword' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Sandospassword = base64_decode($row_settings["value"]);
}
$sql = "select value from settings where name='SandosMainVendorCode' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosMainVendorCode = $row_settings["value"];
}
$sql = "select value from settings where name='SandosVendorCode1' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosVendorCode1 = $row_settings["value"];
}
$sql = "select value from settings where name='SandosVendorCode2' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosVendorCode2 = $row_settings["value"];
}
$sql = "select value from settings where name='SandosVendorCode3' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosVendorCode3 = $row_settings["value"];
}
$sql = "select value from settings where name='SandosVendorCodeMarketCountry1' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosVendorCodeMarketCountry1 = $row_settings["value"];
}
$sql = "select value from settings where name='SandosVendorCodeMarketCountry2' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosVendorCodeMarketCountry2 = $row_settings["value"];
}
$sql = "select value from settings where name='SandosVendorCodeMarketCountry3' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosVendorCodeMarketCountry3 = $row_settings["value"];
}
$sql = "select value from settings where name='SandosMarkup' and affiliate_id=$affiliate_id_sandos" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SandosMarkup = (int) $row_settings["value"];
}
$sql = "select city_xml13 from cities where id=" . $destination;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelCityCode = $row_settings["city_xml13"];
}
if ($nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . $nationality;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sourceMarket = "";
}
if ($VendorCode == "") {
    if ($SandosVendorCodeMarketCountry1 != "") {
        if ($SandosVendorCodeMarketCountry1 != "") {
            $xTmp = explode(";", $SandosVendorCodeMarketCountry1);
            for ($xTmpCount = 0; $xTmpCount < count($xTmp); $xTmpCount ++) {
                if ($xTmp[$xTmpCount] == $nationality) {
                    $VendorCode = $SandosVendorCode1;
                    break;
                }
            }
        }
    }
}
if ($VendorCode == "") {
    if ($SandosVendorCodeMarketCountry2 != "") {
        if ($SandosVendorCodeMarketCountry2 != "") {
            $xTmp = explode(";", $SandosVendorCodeMarketCountry2);
            for ($xTmpCount = 0; $xTmpCount < count($xTmp); $xTmpCount ++) {
                if ($xTmp[$xTmpCount] == $nationality) {
                    $VendorCode = $SandosVendorCode2;
                    break;
                }
            }
        }
    }
}
if ($VendorCode == "") {
    if ($SandosVendorCodeMarketCountry3 != "") {
        if ($SandosVendorCodeMarketCountry3 != "") {
            $xTmp = explode(";", $SandosVendorCodeMarketCountry3);
            for ($xTmpCount = 0; $xTmpCount < count($xTmp); $xTmpCount ++) {
                if ($xTmp[$xTmpCount] == $nationality) {
                    $VendorCode = $SandosVendorCode3;
                    break;
                }
            }
        }
    }
}
if ($VendorCode == "") {
    $VendorCode = $SandosMainVendorCode;
}
if ($HotelCityCode != "" and $SandosServiceURL != "") {
    $translator = new Translator();
    if (file_exists("src/App/language/" . $lang . ".mo")) {
        $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
    }
    $validSearch = true;
    $startTime = microtime();
    $xmlrequest = '<?xml version="1.0" encoding="utf-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><SOAP-ENV:Body><m:OTA_HotelAvailRQ xmlns:m="http://www.opentravel.org/OTA/2003/05" Version="1" PrimaryLangID="en" AvailRatesOnly="true"><m:POS><m:Source><m:RequestorID MessagePassword="' . $Sandospassword . '" ID="' . $SandosUsername . '" /></m:Source></m:POS><m:AvailRequestSegments><m:AvailRequestSegment><m:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '" /><m:RoomStayCandidates>';
    $rCount = 1;
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        $xmlrequest .= '<m:RoomStayCandidate RPH="' . $rCount . '" Quantity="1"><m:GuestCounts><m:GuestCount Count="' . $selectedAdults[$r] . '" AgeQualifyingCode="10" Age="30"/>';
        // Person code identifier 10=adult, 8=child, 7=baby
        for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
            if ($selectedChildrenAges[$r][$z] <= 2) {
                $xmlrequest .= '<m:GuestCount Count="1" AgeQualifyingCode="7" Age="' . $selectedChildrenAges[$r][$z] . '"/>';
            } else {
                $xmlrequest .= '<m:GuestCount Count="1" AgeQualifyingCode="8" Age="' . $selectedChildrenAges[$r][$z] . '"/>';
            }
        }
        $xmlrequest .= '</m:GuestCounts></m:RoomStayCandidate>';
        $rCount = $rCount + 1;
    }
    $xmlrequest .= '</m:RoomStayCandidates><m:HotelSearchCriteria><m:Criterion><m:HotelRef HotelCode="" HotelCityCode="' . $HotelCityCode . '" ChainCode="" VendorCode="' . $VendorCode . '" /></m:Criterion></m:HotelSearchCriteria></m:AvailRequestSegment></m:AvailRequestSegments></m:OTA_HotelAvailRQ></SOAP-ENV:Body></SOAP-ENV:Envelope>';
    $client = new Client();
    $client->setOptions(array(
        'timeout' => 100
    ));
    $client->setHeaders(array(
        "Accept-Encoding" => "gzip, deflate",
        "User-Agent" => "curl/7.37.0",
        "Content-Type" => "text/xml"
    ));
    $client->setUri($SandosServiceURL);
    $client->setRawBody($xmlrequest);
    $client->setMethod('POST');
    $response = $client->send();
    if ($response->isSuccess()) {
        $xmlresult = $response->getBody();
    } else {
        error_log("\r\nSandos Search Error\r\n", 3, "/srv/www/htdocs/error_log");
        error_log("\r\n" . $response->getStatusCode() . "\r\n", 3, "/srv/www/htdocs/error_log");
        error_log("\r\n" . $response->getReasonPhrase() . "\r\n", 3, "/srv/www/htdocs/error_log");
        $validSearch = false;
    }
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_sandos');
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
        $validSearch = false;
    }
    if ($validSearch == true) {
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($xmlresult);
        $node = $inputDoc->getElementsByTagName("RoomStay");
        for ($c = 0; $c < $node->length; $c ++) {
            $RoomStayCandidateRPH = $node->item($c)->getAttribute("RoomStayCandidateRPH");
            $BasicPropertyInfo = $node->item($c)->getElementsByTagName("BasicPropertyInfo");
            if ($BasicPropertyInfo->length > 0) {
                $shid = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                // Comment because of performance
                // $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
                // $HotelCategoryCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCategoryCode");
                // $HotelCategoryDescription = $BasicPropertyInfo->item(0)->getAttribute("HotelCategoryDescription");
                // $HotelCityCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCityCode");
                $ChainCode = $BasicPropertyInfo->item(0)->getAttribute("ChainCode");
            } else {
                $shid = "";
                // $HotelName = "";
                // $HotelCategoryCode = "";
                // $HotelCategoryDescription = "";
                // $HotelCityCode = "";
                $ChainCode = "";
            }
            $zRooms = $RoomStayCandidateRPH - 1;
            if (is_array($tmp[$shid])) {
                if (is_array($tmp[$shid]['details'][$zRooms])) {
                    $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                } else {
                    $baseCounterDetails = 0;
                }
            } else {
                $baseCounterDetails = 0;
            }
            $RoomTypesArray = array();
            $RoomTypesArrayCount = 0;
            $RoomTypes = $node->item($c)->getElementsByTagName("RoomType");
            for ($k = 0; $k < $RoomTypes->length; $k ++) {
                $RoomTypeCode = $RoomTypes->item($k)->getAttribute("RoomTypeCode");
                $RoomDescription = $RoomTypes->item($k)->getElementsByTagName("RoomDescription");
                if ($RoomDescription->length > 0) {
                    // For future use
                    $RoomImage = $RoomDescription->item(0)->getElementsByTagName("Image");
                    if ($RoomImage->length > 0) {
                        $RoomImage = $RoomImage->item(0)->nodeValue;
                    } else {
                        $RoomImage = "";
                    }
                    $RoomDescription = $RoomDescription->item(0)->getAttribute("Name");
                } else {
                    $RoomDescription = $RoomTypeCode;
                    $RoomImage = "";
                }
                $RoomTypesArray[$RoomTypesArrayCount]['RoomTypeCode'] = $RoomTypeCode;
                $RoomTypesArray[$RoomTypesArrayCount]['RoomDescription'] = $RoomDescription;
                $RoomTypesArray[$RoomTypesArrayCount]['RoomImage'] = $RoomImage;
                $RoomTypesArrayCount ++;
            }
            $CancelPolicyArray = array();
            $CancelPolicyCount = 0;
            $CancelPolicies = $node->item($c)->getElementsByTagName("CancelPenalty");
            for ($k = 0; $k < $CancelPolicies->length; $k ++) {
                $CRatePlanID = $CancelPolicies->item($k)->getAttribute("RatePlanID");
                $CNonRefundable = $CancelPolicies->item($k)->getAttribute("NonRefundable");
                $CStart = $CancelPolicies->item($k)->getAttribute("Start");
                $CAmountPercent = $CancelPolicies->item($k)->getElementsByTagName("AmountPercent");
                if ($CAmountPercent->length > 0) {
                    $CAmountPercent = $CAmountPercent->item(0)->nodeValue;
                } else {
                    $CAmountPercent = 0;
                }
                $CPenaltyDescription = $CancelPolicies->item($k)->getElementsByTagName("PenaltyDescription");
                if ($CPenaltyDescription->length > 0) {
                    $CPenaltyDescription = $CPenaltyDescription->item(0)->nodeValue;
                } else {
                    $CPenaltyDescription = "";
                }
                $CancelPolicyArray[$CancelPolicyCount]['RatePlanID'] = $CRatePlanID;
                $CancelPolicyArray[$CancelPolicyCount]['NonRefundable'] = $CNonRefundable;
                $CancelPolicyArray[$CancelPolicyCount]['Start'] = $CStart;
                $CancelPolicyArray[$CancelPolicyCount]['AmountPercent'] = $CAmountPercent;
                $CancelPolicyArray[$CancelPolicyCount]['PenaltyDescription'] = $CPenaltyDescription;
                $CancelPolicyCount ++;
            }
            $RoomRates = $node->item($c)->getElementsByTagName("RoomRate");
            for ($k = 0; $k < $RoomRates->length; $k ++) {
                $MinimumMarkup = $RoomRates->item($k)->getAttribute("MinimumMarkup");
                $IsPriceNeto = $RoomRates->item($k)->getAttribute("IsPriceNeto");
                $RoomTypeCode = $RoomRates->item($k)->getAttribute("RoomTypeCode");
                $MealPlanCodes = $RoomRates->item($k)->getAttribute("MealPlanCodes");
                $MealPlanDescription = $RoomRates->item($k)->getAttribute("MealPlanDescription");
                $RatePlanID = $RoomRates->item($k)->getAttribute("RatePlanID");
                $RatePlanCode = $RoomRates->item($k)->getAttribute("RatePlanCode");
                $PromotionCode = $RoomRates->item($k)->getAttribute("PromotionCode");
                $NumberOfUnits = $RoomRates->item($k)->getAttribute("NumberOfUnits");
                $Commission = $RoomRates->item($k)->getAttribute("Commission");
                $AvailabilityStatus = $RoomRates->item($k)->getAttribute("AvailabilityStatus");
                $ReasonNotAvailable = $RoomRates->item($k)->getAttribute("ReasonNotAvailable");
                $PromotionName = $RoomRates->item($k)->getAttribute("PromotionName");
                $RoomRateDescription = $RoomRates->item($k)->getElementsByTagName("RoomRateDescription");
                if ($RoomRateDescription->length > 0) {
                    $RoomRateDescription = $RoomRateDescription->item(0)->nodeValue;
                } else {
                    $RoomRateDescription = "";
                }
                $Total = $RoomRates->item($k)->getElementsByTagName("Total");
                if ($Total->length > 0) {
                    $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                    $AmountBeforeTax = $Total->item(0)->getAttribute("AmountBeforeTax");
                    $base_currency = $Total->item(0)->getAttribute("CurrencyCode");
                } else {
                    $AmountAfterTax = 0;
                    $AmountBeforeTax = 0;
                    $base_currency = "";
                }
                $Total = $AmountAfterTax;
                if (is_array($RoomTypesArray)) {
                    $key = array_search($RoomTypeCode, array_column($RoomTypesArray, 'RoomTypeCode'));
                    $RoomType = $RoomTypesArray[$key]['RoomDescription'];
                } else {
                    $RoomType = $RoomTypeCode;
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-13";
                if ($RoomRateDescription != "") {
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = ucwords(strtolower($RoomType) . " - " . strtolower($RoomRateDescription));
                } else {
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = ucwords(strtolower($RoomType));
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $base_currency;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['mealcode'] = $MealPlanCodes;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateplancode'] = $RatePlanCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['chaincode'] = $ChainCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['vendorcode'] = $VendorCode;
                if ($scurrency != "" and $base_currency != $scurrency) {
                    $Total = $CurrencyConverter->convert($Total, $base_currency, $scurrency);
                }
                if ($PromotionName != "") {
                    $tmp[$shid]['special'] = true;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $PromotionName;
                }
                $cancelpolicy_deadline = 0;
                $key = array_search($RatePlanID, array_column($CancelPolicyArray, 'RatePlanID'));
                $nonRefundable = $CancelPolicyArray[$key]['NonRefundable'];
                if ($CancelPolicyArray[$key]['AmountPercent'] != "") {
                    $tdeadline = substr($CancelPolicyArray[$key]['Start'], 0, 10);
                    $tdeadline = mktime(0, 0, 0, substr($tdeadline, 5, 2), substr($tdeadline, 8, 2), substr($tdeadline, 0, 4));
                    if ($cancelpolicy_deadline == 0) {
                        $cancelpolicy_deadline = $tdeadline;
                    } else {
                        if ($cancelpolicy_deadline > $tdeadline) {
                            $cancelpolicy_deadline = $tdeadline;
                        }
                    }
                    $cancellationPolicy = gettext('Charge') . ' ' . $CancelPolicyArray[$key]['AmountPercent'] . ' ';
                    if ($CancelPolicyArray[$key]['PenaltyDescription']) {
                        $cancellationPolicy = $cancellationPolicy . '(' . $CancelPolicyArray[$key]['PenaltyDescription'] . ') ' . gettext("if cancelled after") . ' ' . $CancelPolicyArray[$key]['Start'];
                    } else {
                        $cancellationPolicy = $cancellationPolicy . gettext("if cancelled after") . ' ' . $CancelPolicyArray[$key]['Start'];
                    }
                } else {
                    $cancelpolicy_deadline = 0;
                    $cancellationPolicy = "";
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = $nonRefundable;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $cancellationPolicy;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $cancelpolicy_deadline;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $Total;
                // Markup
                if ($SandosMarkup != 0) {
                    $Total = $Total + (($Total * $SandosMarkup) / 100);
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
                if ($SandosMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                    $Total = $Total + (($Total * $HotelsMarkupFallback) / 100);
                }
                // Agent discount
                if ($agent_discount != 0) {
                    $Total = $Total - (($Total * $agent_discount) / 100);
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomtypecode'] = $RoomTypeCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['promotioncode'] = $PromotionCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateplanid'] = $RatePlanID;
                // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['allotment'] = $NumberOfUnits;
                $t = $MealPlanDescription;
                try {
                    $sql = "select mapped from board_mapping where description='" . addslashes($t) . "'";
                    $statement = $db->createStatement($sql);
                    $statement->prepare();
                    $row_board_mapping = $statement->execute();
                    $row_board_mapping->buffer();
                    if ($row_board_mapping->valid()) {
                        $row_board_mapping = $row_board_mapping->current();
                        $t = $row_board_mapping["mapped"];
                    }
                } catch (\Exception $e) {
                    $logger = new Logger();
                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($t);
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $Total;
                $priceBreakdown = array();
                $priceBreakdownCount = 0;
                for ($rDays = 0; $rDays < $nights; $rDays ++) {
                    $rDayAux = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rDays, date("Y", $from)));
                    $priceBreakdown[$priceBreakdownCount]['price'] = $filter->filter($Total / $nights);
                    $priceBreakdown[$priceBreakdownCount]['priceplain'] = (double) ($Total / $nights);
                    $priceBreakdown[$priceBreakdownCount]['date'] = $rDayAux;
                    $priceBreakdownCount ++;
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $priceBreakdown;
                $baseCounterDetails ++;
            }
            $sfilter[] = " sid='$shid' ";
            $valid = 1;
        }
    }
    if ($valid == 1) {
        $sfilter = implode(' or ', $sfilter);
        if ($sfilter != "") {
            try {
                $sql = "select hid, sid from xmlhotels_msandos where " . $sfilter;
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $result = $statement->execute();
                $result->buffer();
                if ($result instanceof ResultInterface && $result->isQueryResult()) {
                    $resultSet2 = new ResultSet();
                    $resultSet2->initialize($result);
                    foreach ($resultSet2 as $row) {
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
                $supplier = 13;
                $sidfilter = implode(',', $sidfilter);
                $query = 'call xmlhotels("' . $sidfilter . '")';
                // Store Session
                try {
                    $sql = new Sql($db);
                    $delete = $sql->delete();
                    $delete->from('quote_session_sandos');
                    $delete->where(array(
                        'session_id' => $session_id
                    ));
                    $statement = $sql->prepareStatementForSqlObject($delete);
                    $results = $statement->execute();
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('quote_session_sandos');
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
    }
}
?>