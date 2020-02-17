<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n PALLADIUM - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
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
            $cancelpolicy_deadline = 0;
            $CancelPolicyArray = array();
            $CancelPolicyCount = 0;
            $CancelPolicies = $RoomRate->item($k)->getElementsByTagName("CancelPenalty");
            for ($kA = 0; $kA < $CancelPolicies->length; $kA ++) {
                $CNonRefundable = $CancelPolicies->item($kA)->getElementsByTagName("NonRefundable");
                if ($CNonRefundable->length > 0) {
                    $CNonRefundable = $CNonRefundable->item(0)->nodeValue;
                } else {
                    $CNonRefundable = "";
                }
                $AbsoluteDeadline = $CancelPolicies->item($kA)->getElementsByTagName("AbsoluteDeadline");
                if ($AbsoluteDeadline->length > 0) {
                    $AbsoluteDeadline = $AbsoluteDeadline->item(0)->nodeValue;
                } else {
                    $AbsoluteDeadline = 0;
                }
                $OffsetTimeUnit = $CancelPolicies->item($kA)->getElementsByTagName("OffsetTimeUnit");
                if ($OffsetTimeUnit->length > 0) {
                    $OffsetTimeUnit = $OffsetTimeUnit->item(0)->nodeValue;
                } else {
                    $OffsetTimeUnit = 0;
                }
                $OffsetUnitMultiplier = $CancelPolicies->item($kA)->getElementsByTagName("OffsetUnitMultiplier");
                if ($OffsetUnitMultiplier->length > 0) {
                    $OffsetUnitMultiplier = $OffsetUnitMultiplier->item(0)->nodeValue;
                } else {
                    $OffsetUnitMultiplier = 0;
                }
                $NmbrOfNights = $CancelPolicies->item($kA)->getElementsByTagName("NmbrOfNights");
                if ($NmbrOfNights->length > 0) {
                    $NmbrOfNights = $NmbrOfNights->item(0)->nodeValue;
                } else {
                    $NmbrOfNights = 0;
                }
                $PenaltyDescription = $CancelPolicies->item($kA)->getElementsByTagName("PenaltyDescription");
                if ($PenaltyDescription->length > 0) {
                    $PenaltyDescription = $PenaltyDescription->item(0)->getElementsByTagName("Text");
                    if ($PenaltyDescription->length > 0) {
                        $PenaltyDescription = $PenaltyDescription->item(0)->nodeValue;
                    } else {
                        $PenaltyDescription = "";
                    }
                } else {
                    $PenaltyDescription = "";
                }
                $CancelPolicyArray[$CancelPolicyCount]['NonRefundable'] = $CNonRefundable;
                $CancelPolicyArray[$CancelPolicyCount]['AbsoluteDeadline'] = $AbsoluteDeadline;
                $CancelPolicyArray[$CancelPolicyCount]['OffsetTimeUnit'] = $OffsetTimeUnit;
                $CancelPolicyArray[$CancelPolicyCount]['OffsetUnitMultiplier'] = $OffsetUnitMultiplier;
                $CancelPolicyArray[$CancelPolicyCount]['NmbrOfNights'] = $NmbrOfNights;
                $CancelPolicyArray[$CancelPolicyCount]['PenaltyDescription'] = $PenaltyDescription;
                $CancelPolicyCount ++;
                $tdeadline = substr($AbsoluteDeadline, 0, 10);
                $tdeadline = mktime(0, 0, 0, substr($tdeadline, 5, 2), substr($tdeadline, 8, 2), substr($tdeadline, 0, 4));
                if ($cancelpolicy_deadline == 0) {
                    $cancelpolicy_deadline = $tdeadline;
                } else {
                    if ($cancelpolicy_deadline > $tdeadline) {
                        $cancelpolicy_deadline = $tdeadline;
                    }
                }
            }
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
    
            $rooms[$baseCounterDetails]['name'] = $ChainName;
            $rooms[$baseCounterDetails]['hotelid'] = $HotelCode;
            $rooms[$baseCounterDetails]['code'] = $shid;
            $rooms[$baseCounterDetails]['scode'] = $shid;
            $rooms[$baseCounterDetails]['shid'] = $shid;
            $rooms[$baseCounterDetails]['status'] = 1;
            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-15";
            $rooms[$baseCounterDetails]['room'] = $RoomType2;
            $rooms[$baseCounterDetails]['RoomType'] = $RoomType2;
            $rooms[$baseCounterDetails]['RoomTypeCode'] = $RoomTypeCode;
            $rooms[$baseCounterDetails]['RatePlanCode'] = $RatePlanCode;
            $rooms[$baseCounterDetails]['adults'] = $adults;
            $rooms[$baseCounterDetails]['children'] = $children;
            $rooms[$baseCounterDetails]['nettotal'] = (double) $AmountBeforeTax;
            if ($PalladiumHotelGroupmarkup != 0) {
                $AmountAfterTax = $AmountAfterTax + (($AmountAfterTax * $PalladiumHotelGroupmarkup) / 100);
            }
            // Geo target markup
            if ($internalmarkup != 0) {
                $AmountAfterTax = $AmountAfterTax + (($AmountAfterTax * $internalmarkup) / 100);
            }
            // Agent markup
            if ($agent_markup != 0) {
                $AmountAfterTax = $AmountAfterTax + (($AmountAfterTax * $agent_markup) / 100);
            }
            // Fallback Markup
            if ($PalladiumHotelGroupmarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                $AmountAfterTax = $AmountAfterTax + (($AmountAfterTax * $HotelsMarkupFallback) / 100);
            }
            // Agent discount
            if ($agent_discount != 0) {
                $AmountAfterTax = $AmountAfterTax - (($AmountAfterTax * $agent_discount) / 100);
            }
            if ($scurrency != "" and $currency != $scurrency) {
                $AmountAfterTax = $CurrencyConverter->convert($AmountAfterTax, $currency, $scurrency);
            }
            $rooms[$baseCounterDetails]['total'] = (double) $AmountAfterTax;
            $rooms[$baseCounterDetails]['totalplain'] = (double) $AmountAfterTax;
            try {
                $sql = "select mapped from board_mapping where description='" . addslashes($RatePlanType) . "'";
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_board_mapping = $statement->execute();
                $row_board_mapping->buffer();
                if ($row_board_mapping->valid()) {
                    $row_board_mapping = $row_board_mapping->current();
                    $RatePlanType = $row_board_mapping["mapped"];
                }
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $rooms[$baseCounterDetails]['meal'] = $translator->translate($RatePlanType);
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            $amount = $AmountAfterTax / $noOfNights;
            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                $pricebreakdownCount = $pricebreakdownCount + 1;
            }
            $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
            $rooms[$baseCounterDetails]['scurrency'] = $currency;
            //
            // Special
            //
            if ($PromotionCode != "") {
                $rooms[$baseCounterDetails]['special'] = true;
                $rooms[$baseCounterDetails]['specialdescription'] = $PromotionCode;
            } else {
                $rooms[$baseCounterDetails]['special'] = false;
                $rooms[$baseCounterDetails]['specialdescription'] = "";
            }
            //
            // Cancellation policies
            //
            if ($CNonRefundable == "true") {
                $rooms[$baseCounterDetails]['nonrefundable'] = true;
                $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking.");
                $rooms[$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking.");
                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();        
            } else {
                $rooms[$baseCounterDetails]['nonrefundable'] = false;
                $rooms[$baseCounterDetails]['cancelpolicy'] = $PenaltyDescription;
                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $cancelpolicy_deadline;
                $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;
            }
            $rooms[$baseCounterDetails]['currency'] = strtoupper($CurrencyCode);
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
    $delete->from('quote_session_palladium');
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
    $insert->into('quote_session_palladium');
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