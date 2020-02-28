<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n TRAVELPLAN - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $hotelsElement = $inputDoc->getElementsByTagName('RoomStay');
    for ($r = 0; $r < $hotelsElement->length; $r ++) {
        $RoomTypes = $hotelsElement->item($r)->getElementsByTagName('RoomTypes');
        $RoomType = $RoomTypes->item(0)->getElementsByTagName('RoomType');
        $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
        $RoomDescription = $RoomType->item(0)->getElementsByTagName('RoomDescription');
        if ($RoomDescription->length > 0) {
            $Name = $RoomDescription->item(0)->getAttribute("Name");
        } else {
            $Name = "";
        }
        $RatePlans = $hotelsElement->item($r)->getElementsByTagName('RatePlans');
        $RatePlan = $RatePlans->item(0)->getElementsByTagName('RatePlan');
        $RatePlanCode = $RatePlan->item(0)->getAttribute("RatePlanCode");
        $RatePlanName = $RatePlan->item(0)->getAttribute("RatePlanName");
        $MealsIncluded = $RatePlan->item(0)->getElementsByTagName('MealsIncluded');
        $MealPlanCodes = $MealsIncluded->item(0)->getAttribute("MealPlanCodes");
        $RoomRates = $hotelsElement->item($r)->getElementsByTagName('RoomRates');
        $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
        $Rates = $RoomRate->item(0)->getElementsByTagName('Rates');
        $Rate = $Rates->item(0)->getElementsByTagName('Rate');
        $CancelPolicies = $Rate->item(0)->getElementsByTagName('CancelPolicies');
        $CancelPenalty = $CancelPolicies->item(0)->getElementsByTagName('CancelPenalty');
        $NonRefundable = $CancelPenalty->item(0)->getAttribute("NonRefundable");
        $Total = $Rate->item(0)->getElementsByTagName('Total');
        if ($Total->length > 0) {
            $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
            $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
        } else {
            $AmountAfterTax = 0;
            $CurrencyCode = "";
        }
        $TPA_Extensions = $Rate->item(0)->getElementsByTagName('TPA_Extensions');
        $ProviderTokens = $TPA_Extensions->item(0)->getElementsByTagName('ProviderTokens');
        $Token = $ProviderTokens->item(0)->getElementsByTagName('Token');
        $TokenName = $Token->item(0)->getAttribute("TokenName");
        $TokenCode = $Token->item(0)->getAttribute("TokenCode");
        $RoomToken = $TPA_Extensions->item(0)->getElementsByTagName('RoomToken');
        $Token = $RoomToken->item(0)->getAttribute("Token");
        $RoomRateDescription = $RoomRate->item(0)->getElementsByTagName('RoomRateDescription');
        if ($RoomRateDescription->length > 0) {
            $RoomRateDescription = $RoomRateDescription->item(0)->getAttribute("Name");
        } else {
            $RoomRateDescription = "";
        }
        $AdultCount = 0;
        $ChildCount = 0;
        $Ages = "";
        $AgesCount = 0;
        $GuestCounts = $RoomRate->item(0)->getElementsByTagName("GuestCounts");
        if ($GuestCounts->length > 0) {
            $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
            for ($zGuestCount = 0; $zGuestCount < $GuestCount->length; $zGuestCount ++) {
                $Age = $GuestCount->item($zGuestCount)->getAttribute("Age");
                $Count = $GuestCount->item($zGuestCount)->getAttribute("Count");
                if ($Age == 30) {
                    $AdultCount = $AdultCount + $Count;
                } else {
                    $ChildCount = $ChildCount + 1;
                    $Ages[$AgesCount] = $Age;
                    $AgesCount = $AgesCount + 1;
                }
            }
        } else {
            $ChildCount = 0;
            $AdultCount = 0;
        }
        $RoomTypeCode = $RoomRate->item(0)->getAttribute("RoomTypeCode");
        $RatePlanCode = $RoomRate->item(0)->getAttribute("RatePlanCode");
        $InvBlockCode = $RoomRate->item(0)->getAttribute("InvBlockCode");
        $AvailabilityStatus = $RoomRate->item(0)->getAttribute("AvailabilityStatus");
        $TimeSpan = $hotelsElement->item($r)->getElementsByTagName('TimeSpan');
        $Start = $TimeSpan->item(0)->getAttribute("Start");
        $End = $TimeSpan->item(0)->getAttribute("End");
        $BasicPropertyInfo = $hotelsElement->item($r)->getElementsByTagName('BasicPropertyInfo');
        $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
        $shid = $HotelCode;
        $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
        // $TPA_Extensions = $hotelsElement->item($r)->getElementsByTagName('TPA_Extensions');
        // $Address = $TPA_Extensions->item(0)->getElementsByTagName('Address');
        // if ($Address->length > 0) {
        // $Address = $Address->item(0)->nodeValue;
        // } else {
        // $Address = "";
        // }
        // $Attributes = $TPA_Extensions->item(0)->getElementsByTagName('Attributes');
        // if ($Attributes->length > 0) {
        // $Attributes = $Attributes->item(0)->nodeValue;
        // } else {
        // $Attributes = "";
        // }
        // $HotelInfo = $TPA_Extensions->item(0)->getElementsByTagName('HotelInfo');
        // $CategoryCode = $HotelInfo->item(0)->getElementsByTagName('CategoryCode');
        // $CodeCC = $CategoryCode->item(0)->getAttribute("Code");
        // error_log("\r\n CodeCC: $CodeCC \r\n", 3, "/srv/www/htdocs/error_log");
        // $CategoryUngroupedCode = $HotelInfo->item(0)->getElementsByTagName('CategoryUngroupedCode');
        // $CodeUC = $CategoryUngroupedCode->item(0)->getAttribute("Code");
        // $CategoryName = $HotelInfo->item(0)->getElementsByTagName('CategoryName');
        // $NameCN = $CategoryName->item(0)->getAttribute("Name");
        // $IdH = $HotelInfo->item(0)->getElementsByTagName('Id');
        // $IDHC= $IdH->item(0)->getAttribute("ID");
        // $NameH = $HotelInfo->item(0)->getElementsByTagName('Name');
        // $NameHN = $NameH->item(0)->getAttribute("Name");
        // $TypeH = $HotelInfo->item(0)->getElementsByTagName('Type');
        // $TypeHH = $TypeH->item(0)->getAttribute("Type");
        // error_log("\r\n TypeHH: $TypeHH \r\n", 3, "/srv/www/htdocs/error_log");
        // $ProviderTokens = $TPA_Extensions->item(0)->getElementsByTagName('ProviderTokens');
        // $ProviderID = $TPA_Extensions->item(0)->getElementsByTagName('ProviderID');
        // $Provider = $ProviderID->item(0)->getAttribute("Provider");
        // error_log("\r\n Provider: $Provider \r\n", 3, "/srv/www/htdocs/error_log");
        $Total = $AmountAfterTax;
    
        $rooms[$baseCounterDetails]['name'] = $HotelName;
        $rooms[$baseCounterDetails]['hotelid'] = $HotelCode;
        $rooms[$baseCounterDetails]['shid'] = $shid;
        $rooms[$baseCounterDetails]['status'] = 1;
        $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-50";
        $rooms[$baseCounterDetails]['room'] = $Name . " - " . $RoomTypeCode;
        $rooms[$baseCounterDetails]['RoomTypeCode'] = $RoomTypeCode;
        $rooms[$baseCounterDetails]['RatePlanCode'] = $RatePlanCode;
        $rooms[$baseCounterDetails]['Token'] = $Token;
        $rooms[$baseCounterDetails]['x50_0'] = $x50_0;
        $rooms[$baseCounterDetails]['x50_1'] = $x50_1;
        $rooms[$baseCounterDetails]['x50_2'] = $x50_2;
        $rooms[$baseCounterDetails]['adults'] = $adults;
        $rooms[$baseCounterDetails]['children'] = $children;
        $rooms[$baseCounterDetails]['nettotal'] = (double) $AmountAfterTax;
        if ($TravelPlanMarkup != 0) {
            $Total = $Total + (($Total * $TravelPlanMarkup) / 100);
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
        if ($TravelPlanMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
            $sql = "select mapped from board_mapping where description='" . addslashes($MealPlanCodes) . "'";
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $row_board_mapping = $statement->execute();
            $row_board_mapping->buffer();
            if ($row_board_mapping->valid()) {
                $row_board_mapping = $row_board_mapping->current();
                $MealPlanCodes = $row_board_mapping["mapped"];
            }
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        $rooms[$baseCounterDetails]['meal'] = $translator->translate($MealPlanCodes);
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
        $rooms[$baseCounterDetails]['scurrency'] = $currency;
        //
        // Special
        //
        $rooms[$baseCounterDetails]['special'] = false;
        $rooms[$baseCounterDetails]['specialdescription'] = "";
        //
        // Cancellation policies
        //
        if ($NonRefundable == "true") {
            $rooms[$baseCounterDetails]['nonrefundable'] = true;
            $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking.");
            $rooms[$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking.");
            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
            $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
        } else {
            $rooms[$baseCounterDetails]['nonrefundable'] = false;
            $rooms[$baseCounterDetails]['cancelpolicy'] = "";
            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
            $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
        }
        $rooms[$baseCounterDetails]['currency'] = strtoupper($currency);
        $baseCounterDetails ++;
    }
    //
    // Store Session
    //
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_travelplan');
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
    $insert->into('quote_session_travelplan');
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