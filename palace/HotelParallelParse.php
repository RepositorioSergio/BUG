<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
// error_log("\r\nPalace - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    // error_log("\r\nPalace Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $roomInfo = $channelsParallelPalaceRoomDescription[$kchannel];
    $bed = $channelsParallelPalaceBedDescription[$kchannel];
    $roomtype = $channelsParallelPalaceRoomType[$kchannel];
    $bedtype = $channelsParallelPalaceBedType[$kchannel];
    $valid = true;
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    if ($Envelope->length > 0) {
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        if ($Body->length > 0) {
            $GetAvailabilityResponse = $Body->item(0)->getElementsByTagName("GetAvailabilityResponse");
            if ($GetAvailabilityResponse->length > 0) {
                $roomAvailabilityResponse = $GetAvailabilityResponse->item(0)->getElementsByTagName("roomAvailabilityResponse");
                if ($roomAvailabilityResponse->length > 0) {
                    $Hotel = $roomAvailabilityResponse->item(0)->getElementsByTagName('Hotel');
                    if ($Hotel->length > 0) {
                        $Hotel = $Hotel->item(0)->nodeValue;
                    } else {
                        $Hotel = "";
                    }
                    $shid = $Hotel;
                    // if ($agent_id == 701 and $Hotel == "BP") {
                    // error_log("\r\nParallel Return Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
                    // }
                    $TotalAmount = $roomAvailabilityResponse->item(0)->getElementsByTagName('TotalAmount');
                    if ($TotalAmount->length > 0) {
                        $TotalAmount = $TotalAmount->item(0)->nodeValue;
                    } else {
                        $TotalAmount = "";
                    }
                    $nettotal = $TotalAmount;
                    $Moneda = $roomAvailabilityResponse->item(0)->getElementsByTagName('Moneda');
                    if ($Moneda->length > 0) {
                        $Moneda = $Moneda->item(0)->nodeValue;
                    } else {
                        $Moneda = "";
                    }
                    $TipoCambio = $roomAvailabilityResponse->item(0)->getElementsByTagName('TipoCambio');
                    if ($TipoCambio->length > 0) {
                        $TipoCambio = $TipoCambio->item(0)->nodeValue;
                    } else {
                        $TipoCambio = "";
                    }
                    $Tarifa1raNoche = $roomAvailabilityResponse->item(0)->getElementsByTagName('Tarifa1raNoche');
                    if ($Tarifa1raNoche->length > 0) {
                        $Tarifa1raNoche = $Tarifa1raNoche->item(0)->nodeValue;
                    } else {
                        $Tarifa1raNoche = "";
                    }
                    $RateCode = $roomAvailabilityResponse->item(0)->getElementsByTagName('RateCode');
                    if ($RateCode->length > 0) {
                        $RateCode = $RateCode->item(0)->nodeValue;
                    } else {
                        $RateCode = "";
                    }
                    $DescripcionTarifa = $roomAvailabilityResponse->item(0)->getElementsByTagName('DescripcionTarifa');
                    if ($DescripcionTarifa->length > 0) {
                        $DescripcionTarifa = $DescripcionTarifa->item(0)->nodeValue;
                    } else {
                        $DescripcionTarifa = "";
                    }
                    $Data = $roomAvailabilityResponse->item(0)->getElementsByTagName('Data');
                    if ($Data->length > 0) {
                        $Availability = $Data->item(0)->getElementsByTagName('Availability');
                        if ($Availability->length > 0) {
                            $dayAvailable = $Availability->item(0)->getElementsByTagName('dayAvailable');
                            if ($dayAvailable->length > 0) {
                                for ($i = 0; $i < $dayAvailable->length; $i ++) {
                                    $Day = $dayAvailable->item($i)->getElementsByTagName('Day');
                                    if ($Day->length > 0) {
                                        $Day = $Day->item(0)->nodeValue;
                                    } else {
                                        $Day = "";
                                    }
                                    $Available = $dayAvailable->item($i)->getElementsByTagName('Available');
                                    if ($Available->length > 0) {
                                        $Available = $Available->item(0)->nodeValue;
                                    } else {
                                        $Available = "";
                                    }
                                    $Rate = $dayAvailable->item($i)->getElementsByTagName('Rate');
                                    if ($Rate->length > 0) {
                                        $Rate = $Rate->item(0)->nodeValue;
                                    } else {
                                        $Rate = "";
                                    }
                                    $RateCode = $dayAvailable->item($i)->getElementsByTagName('RateCode');
                                    if ($RateCode->length > 0) {
                                        $RateCode = $RateCode->item(0)->nodeValue;
                                    } else {
                                        $RateCode = "";
                                    }
                                    $RateCodeDescription = $dayAvailable->item($i)->getElementsByTagName('RateCodeDescription');
                                    if ($Day->length > 0) {
                                        $RateCodeDescription = $RateCodeDescription->item(0)->nodeValue;
                                    } else {
                                        $RateCodeDescription = "";
                                    }
                                    $Currency = $dayAvailable->item($i)->getElementsByTagName('Currency');
                                    if ($Currency->length > 0) {
                                        $Currency = $Currency->item(0)->nodeValue;
                                    } else {
                                        $Currency = "";
                                    }
                                }
                            }
                        } else {
                            $valid = false;
                        }
                    }
                    if ($bed != "") {
                        $roomInfo .= " - " . $bed;
                    }
                    if ($valid == true) {
                        $rooms[$baseCounterDetails]['hotelid'] = $Hotel;
                        // $rooms[$baseCounterDetails]['roomid'] = $IDRoomRate;
                        $rooms[$baseCounterDetails]['code'] = $shid;
                        $rooms[$baseCounterDetails]['scode'] = $shid;
                        $rooms[$baseCounterDetails]['shid'] = $shid;
                        $rooms[$baseCounterDetails]['status'] = 1;
                        $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-5";
                        $rooms[$baseCounterDetails]['room'] = $roomInfo;
                        $rooms[$baseCounterDetails]['roomtype'] = $roomtype;
                        $rooms[$baseCounterDetails]['bedtype'] = $bedtype;
                        $rooms[$baseCounterDetails]['nighttarif'] = $Tarifa1raNoche;
                        $rooms[$baseCounterDetails]['ratecode'] = $RateCode;
                        $rooms[$baseCounterDetails]['descripciontarifa'] = $DescripcionTarifa;
                        $rooms[$baseCounterDetails]['adults'] = $adults;
                        $rooms[$baseCounterDetails]['children'] = $children;
                        $rooms[$baseCounterDetails]['nettotal'] = (double) $nettotal;
                        if ($palaceresortsMarkup != 0) {
                            $TotalAmount = $TotalAmount + (($TotalAmount * $palaceresortsMarkup) / 100);
                        }
                        // Geo target markup
                        if ($internalmarkup != 0) {
                            $TotalAmount = $TotalAmount + (($TotalAmount * $internalmarkup) / 100);
                        }
                        // Agent markup
                        if ($agent_markup != 0) {
                            $TotalAmount = $TotalAmount + (($TotalAmount * $agent_markup) / 100);
                        }
                        // Fallback Markup
                        if ($palaceresortsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                            $TotalAmount = $TotalAmount + (($TotalAmount * $HotelsMarkupFallback) / 100);
                        }
                        // Agent discount
                        if ($agent_discount != 0) {
                            $TotalAmount = $TotalAmount - (($TotalAmount * $agent_discount) / 100);
                        }
                        if ($scurrency != "" and $currency != $scurrency) {
                            $TotalAmount = $CurrencyConverter->convert($TotalAmount, $currency, $scurrency);
                        }
                        $rooms[$baseCounterDetails]['total'] = (double) $TotalAmount;
                        $rooms[$baseCounterDetails]['totalplain'] = (double) $TotalAmount;
                        $mealplan = "All Inclusive";
                        try {
                            $sql = "select mapped from board_mapping where description='" . addslashes($mealplan) . "'";
                            $statement = $db->createStatement($sql);
                            $statement->prepare();
                            $row_board_mapping = $statement->execute();
                            $row_board_mapping->buffer();
                            if ($row_board_mapping->valid()) {
                                $row_board_mapping = $row_board_mapping->current();
                                $mealplan = $row_board_mapping["mapped"];
                            }
                        } catch (\Exception $e) {
                            $logger = new Logger();
                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                            $logger->addWriter($writer);
                            $logger->info($e->getMessage());
                        }
                        $rooms[$baseCounterDetails]['meal'] = $translator->translate($mealplan);
                        $pricebreakdown = array();
                        $pricebreakdownCount = 0;
                        $amount = $TotalAmount / $noOfNights;
                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                            $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                            $pricebreakdownCount = $pricebreakdownCount + 1;
                        }
                        $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                        $rooms[$baseCounterDetails]['scurrency'] = $Moneda;
                        //
                        // Special
                        //
                        $rooms[$baseCounterDetails]['special'] = false;
                        $rooms[$baseCounterDetails]['specialdescription'] = "";
                        //
                        // Cancellation policies
                        //
                        if (mktime(0, 0, 0, date("m"), date("d") + $palaceresortsCancellationPolicy, date("y")) >= $from) {
                            $rooms[$baseCounterDetails]['nonrefundable'] = true;
                            $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking");
                            $rooms[$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
                            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                            $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
                        } else {
                            $rooms[$baseCounterDetails]['nonrefundable'] = false;
                            $rooms[$baseCounterDetails]['cancelpolicy'] = $palaceresortsCancellationPolicy . " " . $translator->translate("day(s) prior to arrival") . " - " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $palaceresortsCancellationPolicy, date("y", $from)));
                            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = mktime(0, 0, 0, date("m", $from), date("d", $from) - $palaceresortsCancellationPolicy, date("y", $from));
                        }
                        $rooms[$baseCounterDetails]['currency'] = strtoupper($Moneda);
                        $baseCounterDetails ++;
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
    $delete->from('quote_session_palace');
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
    $insert->into('quote_session_palace');
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
// error_log("\r\nEOF - EOF Hotel Parallel Parse\r\n", 3, "/srv/www/htdocs/error_log");
?>