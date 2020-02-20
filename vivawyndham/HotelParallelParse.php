<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n VIVA - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
error_log("\r\nResponseANT - $response\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $node = $inputDoc->getElementsByTagName("RoomStay");
    for ($c = 0; $c < $node->length; $c ++) {
        $RoomStayCandidateRPH = $node->item($c)->getAttribute("RoomStayCandidateRPH");
        $BasicPropertyInfo = $node->item($c)->getElementsByTagName("BasicPropertyInfo");
        if ($BasicPropertyInfo->length > 0) {
            $shid = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
            // Comment because of performance
            $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
            // $HotelCategoryCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCategoryCode");
            // $HotelCategoryDescription = $BasicPropertyInfo->item(0)->getAttribute("HotelCategoryDescription");
            // $HotelCityCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCityCode");
            $ChainCode = $BasicPropertyInfo->item(0)->getAttribute("ChainCode");
        } else {
            $shid = "";
            $HotelName = "";
            // $HotelCategoryCode = "";
            // $HotelCategoryDescription = "";
            // $HotelCityCode = "";
            $ChainCode = "";
        }
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
    
    
            $rooms[$baseCounterDetails]['name'] = $HotelName;
            $rooms[$baseCounterDetails]['hotelid'] = $shid;
            $rooms[$baseCounterDetails]['roomid'] = $IDRoomRate;
            $rooms[$baseCounterDetails]['code'] = $shid;
            $rooms[$baseCounterDetails]['scode'] = $shid;
            $rooms[$baseCounterDetails]['shid'] = $shid;
            $rooms[$baseCounterDetails]['status'] = 1;
            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-67";
            if ($RoomRateDescription != "") {
                $rooms[$baseCounterDetails]['room'] = ucwords(strtolower($RoomType) . " - " . strtolower($RoomRateDescription));
            } else {
                $rooms[$baseCounterDetails]['room'] = ucwords(strtolower($RoomType));
            }
            $rooms[$baseCounterDetails]['roomtype'] = $RoomType;
            $rooms[$baseCounterDetails]['room_description'] = $RoomType;
            $rooms[$baseCounterDetails]['mealcode'] = $MealPlanCodes;
            $rooms[$baseCounterDetails]['rateplancode'] = $RatePlanCode;
            $rooms[$baseCounterDetails]['chaincode'] = $ChainCode;
            $rooms[$baseCounterDetails]['vendorcode'] = $VendorCode;
            $rooms[$baseCounterDetails]['nonrefundable'] = $nonRefundable;
            $rooms[$baseCounterDetails]['roomtypecode'] = $RoomTypeCode;
            $rooms[$baseCounterDetails]['promotioncode'] = $PromotionCode;
            $rooms[$baseCounterDetails]['rateplanid'] = $RatePlanID;
            $rooms[$baseCounterDetails]['adults'] = $adults;
            $rooms[$baseCounterDetails]['children'] = $children;
            $rooms[$baseCounterDetails]['nettotal'] = (double) $Total;
            if ($VivaWyndhamMarkup != 0) {
                $Total = $Total + (($Total * $VivaWyndhamMarkup) / 100);
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
            if ($VivaWyndhamMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                $Total = $Total + (($Total * $HotelsMarkupFallback) / 100);
            }
            // Agent discount
            if ($agent_discount != 0) {
                $Total = $Total - (($Total * $agent_discount) / 100);
            }
            if ($scurrency != "" and $currency != $scurrency) {
                $Total = $CurrencyConverter->convert($Total, $currency, $scurrency);
            }
            $rooms[$baseCounterDetails]['total'] = (double) $Total;
            $rooms[$baseCounterDetails]['totalplain'] = (double) $Total;
            try {
                $sql = "select mapped from board_mapping where description='" . addslashes($MealPlanDescription) . "'";
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_board_mapping = $statement->execute();
                $row_board_mapping->buffer();
                if ($row_board_mapping->valid()) {
                    $row_board_mapping = $row_board_mapping->current();
                    $MealPlanDescription = $row_board_mapping["mapped"];
                }
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $rooms[$baseCounterDetails]['meal'] = $translator->translate($MealPlanDescription);
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            $amount = $Total / $noOfNights;
            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                $pricebreakdownCount = $pricebreakdownCount + 1;
            }
            $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
            $rooms[$baseCounterDetails]['scurrency'] = $base_currency;
            //
            // Special
            //
            if ($PromotionName != "") {
                $rooms[$baseCounterDetails]['special'] = true;
                $rooms[$baseCounterDetails]['specialdescription'] = $PromotionName;
            } else {
                $rooms[$baseCounterDetails]['special'] = false;
                $rooms[$baseCounterDetails]['specialdescription'] = "";
            }
            //
            // Cancellation policies
            //
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
            $rooms[$baseCounterDetails]['cancelpolicy'] = $cancellationPolicy;
            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $cancelpolicy_deadline;

            $rooms[$baseCounterDetails]['currency'] = strtoupper($base_currency);
            $baseCounterDetails ++;
        }
    }
    //
    // Store Session
    //
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_vivawyndham');
    $delete->where(array(
        'session_id' => $session_id_tmp
    ));
    $statement = $sql->prepareStatementForSqlObject($delete);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('quote_session_vivawyndham');
    $insert->values(array(
        'session_id' => $session_id_tmp,
        'xmlrequest' => (string) $request,
        'xmlresult' => (string) $response,
        'data' => base64_encode(serialize($srooms)),
        'searchsettings' => base64_encode(serialize($requestdata))
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
}
?>