<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n ACTIONTRAVEL - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
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
                    $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                    $shid = $HotelCode;
                    // $BestDeal = $node->item($c)->getAttribute("BestDeal");
                    $RoomRates = $node->item($c)->getElementsByTagName("RoomRates");
                    $RoomRate = $node->item($c)->getElementsByTagName("RoomRate");
                    for ($y = 0; $y < $RoomRate->length; $y ++) {
                        $NonRefundable = $RoomRate->item($y)->getElementsByTagName("NonRefundable");
                        if ($NonRefundable->length > 0) {
                            $NonRefundable = $NonRefundable->item(0)->nodeValue;
                        } else {
                            $NonRefundable = "";
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
                                        $Name = str_replace("&lt;p&gt;", "", $Name);
                                        $Name = str_replace("&amp;", " ", $Name);
    
                                        $rooms[$baseCounterDetails]['hotelid'] = $HotelCode;
                                        $rooms[$baseCounterDetails]['code'] = $HotelCode;
                                        $rooms[$baseCounterDetails]['scode'] = $shid;
                                        $rooms[$baseCounterDetails]['shid'] = $shid;
                                        $rooms[$baseCounterDetails]['status'] = 1;
                                        $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-35";
                                        $rooms[$baseCounterDetails]['room'] = substr($Name, 0, 150);
                                        $rooms[$baseCounterDetails]['smbr'] = $SequenceNmbr;
                                        $rooms[$baseCounterDetails]['rid'] = $RatePlanCode;
                                        $rooms[$baseCounterDetails]['adults'] = $adults;
                                        $rooms[$baseCounterDetails]['children'] = $children;
                                        $rooms[$baseCounterDetails]['nettotal'] = (double) $NetTotal;
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
                                        $rooms[$baseCounterDetails]['total'] = (double) $Total;
                                        $rooms[$baseCounterDetails]['totalplain'] = (double) $Total;
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
                                        $rooms[$baseCounterDetails]['meal'] = $translator->translate($Board);
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
                                        $rooms[$baseCounterDetails]['scurrency'] = $scurrency;
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
                                            $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking.") . "<br/>" . $cancellationPolicy;
                                            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                                        } else {
                                            $rooms[$baseCounterDetails]['nonrefundable'] = false;
                                            $rooms[$baseCounterDetails]['cancelpolicy'] = $cancellationPolicy;
                                            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                                        }
                                        $rooms[$baseCounterDetails]['currency'] = strtoupper($scurrency);
                                        $baseCounterDetails ++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    //
    // Store Session
    //
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_actiontravel');
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
    $insert->into('quote_session_actiontravel');
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