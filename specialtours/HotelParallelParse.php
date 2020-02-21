<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nSpecialtours - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $node = $inputDoc->getElementsByTagName("Alloc");
    // Results
    for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
        $HoID = $node->item($rAUX)->getElementsByTagName("HoID");
        if ($HoID->length > 0) {
            $HoID = $HoID->item(0)->nodeValue;
        } else {
            $HoID = "";
        }
        $shid = $HoID;
        $RST = $node->item($rAUX)->getElementsByTagName("RST");
        if ($RST->length > 0) {
            $RST = $RST->item(0)->nodeValue;
        } else {
            $RST = "";
        }
        $RoomDescr = $node->item($rAUX)->getElementsByTagName("RoomDescr");
        if ($RoomDescr->length > 0) {
            $RoomDescr = $RoomDescr->item(0)->nodeValue;
        } else {
            $RoomDescr = "";
        }
        $BFK = $node->item($rAUX)->getElementsByTagName("BFK");
        if ($BFK->length > 0) {
            $BFK = $BFK->item(0)->nodeValue;
        } else {
            $BFK = "";
        }
        // $Pr = $valueAlloc['Pr'];
        // $Pr = $node->item($rAUX)->getElementsByTagName("Pr");
        // if ($Pr->length > 0) {
        // $Pr = $Pr->item(0)->nodeValue;
        // } else {
        // $Pr = "";
        // }
        $PrCur = $node->item($rAUX)->getElementsByTagName("PrCur");
        if ($PrCur->length > 0) {
            $PrCur = $PrCur->item(0)->nodeValue;
        } else {
            $PrCur = "";
        }
        $TotPricePUB = $node->item($rAUX)->getElementsByTagName("TotPricePUB");
        if ($TotPricePUB->length > 0) {
            $TotPricePUB = $TotPricePUB->item(0)->nodeValue;
        } else {
            $TotPricePUB = "";
        }
        $Avail = $node->item($rAUX)->getElementsByTagName("Avail");
        if ($Avail->length > 0) {
            $Avail = $Avail->item(0)->nodeValue;
        } else {
            $Avail = "";
        }
        $PrCD = $node->item($rAUX)->getElementsByTagName("PrCD");
        if ($PrCD->length > 0) {
            $PrCD = $PrCD->item(0)->nodeValue;
        } else {
            $PrCD = "";
        }
        $TotalEUR = $node->item($rAUX)->getElementsByTagName("TotalEUR");
        if ($TotalEUR->length > 0) {
            $TotalEUR = $TotalEUR->item(0)->nodeValue;
        } else {
            $TotalEUR = "";
        }
        $CLXPolicy = $node->item($rAUX)->getElementsByTagName("CLXPolicy");
        if ($CLXPolicy->length > 0) {
            $LastNoChargeCLX = $CLXPolicy->item(0)->getElementsByTagName("LastNoChargeCLX");
            if ($LastNoChargeCLX->length > 0) {
                $LastNoChargeCLX = $LastNoChargeCLX->item(0)->nodeValue;
            } else {
                $LastNoChargeCLX = "";
            }
            $NonShow = $CLXPolicy->item(0)->getElementsByTagName("NonShow ");
            if ($NonShow->length > 0) {
                $chargeN = $NonShow->item(0)->getAttribute("charge");
                $currencyN = $NonShow->item(0)->getAttribute("currency");
                $publicchargeN = $NonShow->item(0)->getAttribute("publiccharge");
            } else {
                $NonShow = "";
            }
            $CLXDetails = $CLXPolicy->item(0)->getElementsByTagName("CLXDetails ");
            if ($CLXDetails->length > 0) {
                $from = $CLXDetails->item(0)->getAttribute("from");
                $chargeCLX = $CLXDetails->item(0)->getAttribute("charge");
                $currencyCLX = $CLXDetails->item(0)->getAttribute("currency");
                $publicchargeCLX = $CLXDetails->item(0)->getAttribute("publiccharge");
            } else {
                $NonShow = "";
            }
        }
        $Remarks = $node->item($rAUX)->getElementsByTagName("Remarks");
        if ($Remarks->length > 0) {
            $Remarks = $Remarks->item(0)->nodeValue;
        } else {
            $Remarks = "";
        }
        $rooms[$baseCounterDetails]['hotelid'] = $HoID;
        $rooms[$baseCounterDetails]['code'] = $HoID;
        $rooms[$baseCounterDetails]['scode'] = $HoID;
        $rooms[$baseCounterDetails]['shid'] = $shid;
        $rooms[$baseCounterDetails]['status'] = 1;
        $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-33";
        $rooms[$baseCounterDetails]['room'] = $RST . " - " . $RoomDescr;
        $rooms[$baseCounterDetails]['room_description'] = $RoomDescr;
        $rooms[$baseCounterDetails]['adults'] = $adults;
        $rooms[$baseCounterDetails]['children'] = $children;
        $rooms[$baseCounterDetails]['nettotal'] = (double) $TotalEUR;
        if ($SpecialToursMarkup != 0) {
            $TotalEUR = $TotalEUR + (($TotalEUR * $SpecialToursMarkup) / 100);
        }
        // Geo target markup
        if ($internalmarkup != 0) {
            $TotalEUR = $TotalEUR + (($TotalEUR * $internalmarkup) / 100);
        }
        // Agent markup
        if ($agent_markup != 0) {
            $TotalEUR = $TotalEUR + (($TotalEUR * $agent_markup) / 100);
        }
        // Fallback Markup
        if ($SpecialToursMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
            $TotalEUR = $TotalEUR + (($TotalEUR * $HotelsMarkupFallback) / 100);
        }
        // Agent discount
        if ($agent_discount != 0) {
            $TotalEUR = $TotalEUR - (($TotalEUR * $agent_discount) / 100);
        }
        if ($scurrency != "" and $currency != $scurrency) {
            $TotalEUR = $CurrencyConverter->convert($TotalEUR, $currency, $scurrency);
        }
        $rooms[$baseCounterDetails]['total'] = (double) $TotalEUR;
        $rooms[$baseCounterDetails]['totalplain'] = (double) $TotalEUR;
        if ($BFK == "") {
            $BFK = "Room Only";
        }
        try {
            $sql = "select mapped from board_mapping where description='" . addslashes($BFK) . "'";
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $row_board_mapping = $statement->execute();
            $row_board_mapping->buffer();
            if ($row_board_mapping->valid()) {
                $row_board_mapping = $row_board_mapping->current();
                $BFK = $row_board_mapping["mapped"];
            }
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        $rooms[$baseCounterDetails]['meal'] = $translator->translate($BFK);
        $pricebreakdown = array();
        $pricebreakdownCount = 0;
        $amount = $TotalEUR / $noOfNights;
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
        /*
         * $procurar = "Non-Refundable";
         * if (strpos($PromotionName, $procurar) !== false) {
         * $rooms[$baseCounterDetails]['nonrefundable'] = true;
         * $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking");
         * $rooms[$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
         * $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
         * $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
         * } else {
         * $rooms[$baseCounterDetails]['cancelpolicy'] = "";
         * $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
         * $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
         * }
         */
        $rooms[$baseCounterDetails]['cancelpolicy'] = "";
        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;

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
    $delete->from('quote_session_specialtours');
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
    $insert->into('quote_session_specialtours');
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