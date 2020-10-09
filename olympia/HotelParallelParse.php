<?php
error_log("\r\nOlympia Europe - Hotel Parallel Parse\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
if ($response != "") {
    $from = date('Y-m-d', $from);
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $OTA_HotelAvailRS = $inputDoc->getElementsByTagName('OTA_HotelAvailRS');
    $Hotelsb = $OTA_HotelAvailRS->item(0)->getElementsByTagName('Hotels');
    if ($Hotelsb->length > 0) {
        $DateRange = $Hotelsb->item(0)->getElementsByTagName('DateRange');
        if ($DateRange->length > 0) {
            $Start = $DateRange->item(0)->getAttribute('Start');
            $End = $DateRange->item(0)->getAttribute('End');
        }
        $RoomCandidates = $Hotelsb->item(0)->getElementsByTagName('RoomCandidates');
        if ($RoomCandidates->length > 0) {
            $RoomCandidate = $RoomCandidates->item(0)->getElementsByTagName('RoomCandidate');
            if ($RoomCandidate->length > 0) {
                $RPH = $RoomCandidate->item(0)->getAttribute('RPH');
                $Guests = $RoomCandidate->item(0)->getElementsByTagName('Guests');
                if ($Guests->length > 0) {
                    $Guest = $Guests->item(0)->getElementsByTagName('Guest');
                    if ($Guest->length > 0) {
                        $AgeCode = $Guest->item(0)->getAttribute('AgeCode');
                        $Count = $Guest->item(0)->getAttribute('Count');
                    }
                }
            }
        }
        $Hotelb = $Hotelsb->item(0)->getElementsByTagName('Hotel');
        if ($Hotelb->length > 0) {
            for ($i = 0; $i < $Hotelb->length; $i ++) {
                $Info = $Hotelb->item($i)->getElementsByTagName('Info');
                if ($Info->length > 0) {
                    $HotelCode = $Info->item(0)->getAttribute('HotelCode');
                    $shid = $HotelCode;
                    $HotelName = $Info->item(0)->getAttribute('HotelName');
                    $HotelCityCode = $Info->item(0)->getAttribute('HotelCityCode');
                    $Rating = $Info->item(0)->getAttribute('Rating');
                    $MasterCode = $Info->item(0)->getAttribute('MasterCode');
                    $Recommended = $Info->item(0)->getAttribute('Recommended');
                    $HotelProvider = $Info->item(0)->getElementsByTagName('HotelProvider');
                    if ($HotelProvider->length > 0) {
                        $HotelProvider = $HotelProvider->item(0)->nodeValue;
                    } else {
                        $HotelProvider = "";
                    }
                    $HotelIdent = $Info->item(0)->getElementsByTagName('HotelIdent');
                    if ($HotelIdent->length > 0) {
                        $HotelIdent = $HotelIdent->item(0)->nodeValue;
                    } else {
                        $HotelIdent = "";
                    }
                }
                $BestPrice = $Hotelb->item($i)->getElementsByTagName('BestPrice');
                if ($BestPrice->length > 0) {
                    $Amount = $BestPrice->item(0)->getAttribute('Amount');
                    $Currency = $BestPrice->item(0)->getAttribute('Currency');
                }
                $Rooms = $Hotelb->item($i)->getElementsByTagName('Rooms');
                if ($Rooms->length > 0) {
                    $Room = $Rooms->item(0)->getElementsByTagName('Room');
                    if ($Room->length > 0) {
                        for ($iAux = 0; $iAux < $Room->length; $iAux ++) {
                            $RPH = $Room->item($iAux)->getAttribute('RPH');
                            $Best = $Room->item($iAux)->getAttribute('Best');
                            $Status = $Room->item($iAux)->getAttribute('Status');
                            $RoomType = $Room->item($iAux)->getElementsByTagName('RoomType');
                            if ($RoomType->length > 0) {
                                $RoomTypeCode = $RoomType->item(0)->getAttribute('Code');
                                $RoomTypeName = $RoomType->item(0)->getAttribute('Name');
                            }
                            $RoomRates = $Room->item($iAux)->getElementsByTagName('RoomRates');
                            if ($RoomRates->length > 0) {
                                $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
                                if ($RoomRate->length > 0) {
                                    $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');
                                    $BookingCode = $RoomRate->item(0)->getAttribute('BookingCode');
                                    $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                                    if ($Total->length > 0) {
                                        $Amount = $Total->item(0)->getAttribute('Amount');
                                        $Commission = $Total->item(0)->getAttribute('Commission');
                                        $Currency = $Total->item(0)->getAttribute('Currency');
                                    }
                                    $total = $Amount;
                                    $nettotal = $total;
                                    $CancelPenaltyArray = array();
                                    $count = 0;
                                    $CancelPenalties = $RoomRate->item(0)->getElementsByTagName('CancelPenalties');
                                    if ($CancelPenalties->length > 0) {
                                        $CancellationCostsToday = $CancelPenalties->item(0)->getAttribute('CancellationCostsToday');
                                        $NonRefundable = $CancelPenalties->item(0)->getAttribute('NonRefundable');
                                        $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName('CancelPenalty');
                                        if ($CancelPenalty->length > 0) {
                                            $cancelpolicy = "";
                                            for ($iAux2=0; $iAux2 < $CancelPenalty->length; $iAux2++) { 
                                                $Deadline = $CancelPenalty->item($iAux2)->getElementsByTagName('Deadline');
                                                if ($Deadline->length > 0) {
                                                    $TimeUnit = $Deadline->item(0)->getAttribute('TimeUnit');
                                                    $Units = $Deadline->item(0)->getAttribute('Units');
                                                }
                                                $Charge = $CancelPenalty->item($iAux2)->getElementsByTagName('Charge');
                                                if ($Charge->length > 0) {
                                                    $ChargeAmount = $Charge->item(0)->getAttribute('Amount');
                                                    $ChargeCurrency = $Charge->item(0)->getAttribute('Currency');
                                                }
                                                $CancelPenaltyArray[$count]['Units'] = $Units;
                                                $cancelpolicy .= $translator->translate("If you cancel booking ") . $Units . " " . $translator->translate($TimeUnit) . "(s) " . $translator->translate(" before checkin cost ") . $ChargeCurrency . $ChargeAmount . " .<br>";
                                                $count = $count + 1;
                                            }
                                        }
                                    }
                                }
                            }
                            
                            $rooms[$baseCounterDetails]['name'] = $HotelName;
                            $rooms[$baseCounterDetails]['hotelid'] = $HotelCode;
                            $rooms[$baseCounterDetails]['roomid'] = $RoomTypeCode;
                            $rooms[$baseCounterDetails]['shid'] = $shid;
                            $rooms[$baseCounterDetails]['status'] = 1;
                            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-75";
                            $rooms[$baseCounterDetails]['room'] = $RoomTypeName;
                            $rooms[$baseCounterDetails]['roomtype'] = $RoomTypeCode;
                            $rooms[$baseCounterDetails]['room_description'] = $RoomTypeName;
                            $rooms[$baseCounterDetails]['BookingCode'] = $BookingCode;
                            $rooms[$baseCounterDetails]['adults'] = $adults;
                            $rooms[$baseCounterDetails]['children'] = $children;
                            $rooms[$baseCounterDetails]['nettotal'] = (double) $nettotal;
                            if ($olympiaeuropeMarkup != 0) {
                                $total = $total + (($total * $olympiaeuropeMarkup) / 100);
                            }
                            // Geo target markup
                            if ($internalmarkup != 0) {
                                $total = $total + (($total * $internalmarkup) / 100);
                            }
                            // Agent markup
                            if ($agent_markup != 0) {
                                $total = $total + (($total * $agent_markup) / 100);
                            }
                            // Fallback Markup
                            if ($olympiaeuropeMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                $total = $total + (($total * $HotelsMarkupFallback) / 100);
                            }
                            // Agent discount
                            if ($agent_discount != 0) {
                                $total = $total - (($total * $agent_discount) / 100);
                            }
                            if ($scurrency != "" and $currency != $scurrency) {
                                $total = $CurrencyConverter->convert($total, $currency, $scurrency);
                            }
                            $rooms[$baseCounterDetails]['total'] = (double) $total;
                            $rooms[$baseCounterDetails]['totalplain'] = (double) $total;
                            try {
                                $sql = "select mapped from board_mapping where description='" . addslashes($MealPlan) . "'";
                                $statement = $db->createStatement($sql);
                                $statement->prepare();
                                $row_board_mapping = $statement->execute();
                                $row_board_mapping->buffer();
                                if ($row_board_mapping->valid()) {
                                    $row_board_mapping = $row_board_mapping->current();
                                    $MealPlan = $row_board_mapping["mapped"];
                                }
                            } catch (\Exception $e) {
                                $logger = new Logger();
                                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                $logger->addWriter($writer);
                                $logger->info($e->getMessage());
                            }
                            $rooms[$baseCounterDetails]['meal'] = $translator->translate($MealPlan);
                            $pricebreakdown = array();
                            $pricebreakdownCount = 0;
                            $amount = $total / $noOfNights;
                            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                                $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                $pricebreakdownCount = $pricebreakdownCount + 1;
                            }
                            $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                            $rooms[$baseCounterDetails]['scurrency'] = $Currency;
                            //
                            // Special
                            //
                            $rooms[$baseCounterDetails]['special'] = false;
                            $rooms[$baseCounterDetails]['specialdescription'] = "";
                            
                            //
                            // Cancellation policies
                            //
                            if ($NonRefundable == 0) {
                                $rooms[$baseCounterDetails]['nonrefundable'] = false;
                            } else {
                                $rooms[$baseCounterDetails]['nonrefundable'] = true;
                            }
                            $days = "- " . $CancelPenaltyArray[0]['Units'] . " days";
                            $date = strftime("%a, %e %b %Y", strtotime($from.$days));
                            $rooms[$baseCounterDetails]['cancelpolicy'] = $cancelpolicy;
                            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $date;
                            $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $date;
                            $rooms[$baseCounterDetails]['currency'] = strtoupper($ClientCurrencyCode);
                            $baseCounterDetails ++;
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
    $delete->from('quote_session_olympiaeurope');
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
    $insert->into('quote_session_olympiaeurope');
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