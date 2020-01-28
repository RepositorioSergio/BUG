<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n ABREU - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $OTA_HotelAvailRS = $inputDoc->getElementsByTagName('OTA_HotelAvailRS');

    $Hotels = $OTA_HotelAvailRS->item(0)->getElementsByTagName('Hotels');
    if ($Hotels->length > 0) {
        $Hotel = $Hotels->item(0)->getElementsByTagName('Hotel');
        if ($Hotel->length > 0) {
            for ($i=0; $i < $Hotel->length; $i++) { 
                $Info = $Hotel->item($i)->getElementsByTagName('Info');
                if ($Info->length > 0) {
                    $Recommended = $Info->item(0)->getAttribute('Recommended');
                    $MasterCode = $Info->item(0)->getAttribute('MasterCode');
                    $Rating = $Info->item(0)->getAttribute('Rating');
                    $HotelCityCode = $Info->item(0)->getAttribute('HotelCityCode');
                    $HotelName = $Info->item(0)->getAttribute('HotelName');
                    $HotelCode = $Info->item(0)->getAttribute('HotelCode');
                }

                $BestPrice = $Hotel->item($i)->getElementsByTagName('BestPrice');
                if ($BestPrice->length > 0) {
                    $Currency = $BestPrice->item(0)->getAttribute('Currency');
                    $Amount = $BestPrice->item(0)->getAttribute('Amount');
                }

                $Rooms = $Hotel->item($i)->getElementsByTagName('Rooms');
                if ($Rooms->length > 0) {
                    $Room = $Rooms->item(0)->getElementsByTagName('Room');
                    if ($Room->length > 0) {
                        for ($j=0; $j < $Room->length; $j++) { 
                            $RPH = $Room->item($j)->getAttribute('RPH');
                            $Status = $Room->item($j)->getAttribute('Status');
                            $Best = $Room->item($j)->getAttribute('Best');

                            $RoomType = $Room->item($j)->getElementsByTagName('RoomType');
                            if ($RoomType->length > 0) {
                                $Code = $RoomType->item(0)->getAttribute('Code');
                                $Name = $RoomType->item(0)->getAttribute('Name');
                                $Special = $RoomType->item(0)->getElementsByTagName('Special');
                                if ($Special->length > 0) {
                                    $Special = $Special->item(0)->nodeValue;
                                } else {
                                    $Special = "";
                                }
                            }

                            $RoomRates = $Room->item($j)->getElementsByTagName('RoomRates');
                            if ($RoomRates->length > 0) {
                                $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
                                if ($RoomRate->length > 0) {
                                    for ($i=0; $i < $RoomRate->length; $i++) { 
                                        $MealPlan = $RoomRate->item($i)->getAttribute('MealPlan');
                                        $BookingCode = $RoomRate->item($i)->getAttribute('BookingCode');
                                        $Total = $RoomRate->item($i)->getElementsByTagName('Total');
                                        if ($Total->length > 0) {
                                            $Currency = $Total->item(0)->getAttribute('Currency');
                                            $Amount = $Total->item(0)->getAttribute('Amount');
                                            $MinPrice = $Total->item(0)->getAttribute('MinPrice');
                                            $Commission = $Total->item(0)->getAttribute('Commission');
                                        } else {
                                            $Currency = "";
                                            $Amount = 0;
                                            $MinPrice = 0;
                                            $Commission = 0;
                                        }
                                    
                                        $rooms[$baseCounterDetails]['name'] = $Name;
                                        $rooms[$baseCounterDetails]['hotelid'] = $HotelCode;
                                        $rooms[$baseCounterDetails]['roomid'] = $Code;
                                        $rooms[$baseCounterDetails]['code'] = $HotelCode;
                                        $rooms[$baseCounterDetails]['scode'] = $HotelCode;
                                        $rooms[$baseCounterDetails]['shid'] = $HotelCode;
                                        $rooms[$baseCounterDetails]['status'] = 1;
                                        $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-41";
                                        $rooms[$baseCounterDetails]['room'] = $Name;
                                        $rooms[$baseCounterDetails]['roomtype'] = $Name;
                                        $rooms[$baseCounterDetails]['room_description'] = $Special;
                                        $rooms[$baseCounterDetails]['MealPlanCode'] = $MealPlan;
                                        $rooms[$baseCounterDetails]['bookingcode'] = $BookingCode;
                                        $rooms[$baseCounterDetails]['adults'] = $adults;
                                        $rooms[$baseCounterDetails]['children'] = $children;
                                        $rooms[$baseCounterDetails]['nettotal'] = (double) $Amount;
                                        if ($scurrency != "" and $AbreuCurrency != $scurrency and $AbreuCurrency != "") {
                                            $total = $CurrencyConverter->convert($total, $AbreuCurrency, $scurrency);
                                        }
                                        if ($AbreuMarkup != 0) {
                                            $Amount = $Amount + (($Amount * $AbreuMarkup) / 100);
                                        }
                                        // Geo target markup
                                        if ($internalmarkup != 0) {
                                            $Amount = $Amount + (($Amount * $internalmarkup) / 100);
                                        }
                                        // Agent markup
                                        if ($agent_markup != 0) {
                                            $Amount = $Amount + (($Amount * $agent_markup) / 100);
                                        }
                                        // Fallback Markup
                                        if ($AbreuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                            $Amount = $Amount + (($Amount * $HotelsMarkupFallback) / 100);
                                        }
                                        // Agent discount
                                        if ($agent_discount != 0) {
                                            $Amount = $Amount - (($Amount * $agent_discount) / 100);
                                        }
                                        if ($scurrency != "" and $currency != $scurrency) {
                                            $Amount = $CurrencyConverter->convert($Amount, $currency, $scurrency);
                                        }
                                        $rooms[$baseCounterDetails]['total'] = (double) $Amount;
                                        $rooms[$baseCounterDetails]['totalplain'] = (double) $Amount;
                                        try {
                                            $sql = "select mapped from board_mapping where description='" . addslashes($MealPlan) . "'";
                                            $statement = $db->createStatement($sql);
                                            $statement->prepare();
                                            $row_board_mapping = $statement->execute();
                                            $row_board_mapping->buffer();
                                            if ($row_board_mapping->valid()) {
                                                $row_board_mapping = $row_board_mapping->current();
                                                $Text = $row_board_mapping["mapped"];
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
                                        $amount = $Amount / $noOfNights;
                                        if ($AbreuMarkup != 0) {
                                            $amount = $amount + (($amount * $AbreuMarkup) / 100);
                                        }
                                        // Geo target markup
                                        if ($internalmarkup != 0) {
                                            $amount = $amount + (($amount * $internalmarkup) / 100);
                                        }
                                        // Agent markup
                                        if ($agent_markup != 0) {
                                            $amount = $amount + (($amount * $agent_markup) / 100);
                                        }
                                        // Fallback Markup
                                        if ($AbreuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                            $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                                        }
                                        // Agent discount
                                        if ($agent_discount != 0) {
                                            $amount = $amount - (($amount * $agent_discount) / 100);
                                        }
                                        if ($scurrency != "" and $currency != $scurrency) {
                                            $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                                        }
                                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                            $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                                            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                            $pricebreakdownCount = $pricebreakdownCount + 1;
                                        }
                                        $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                        $rooms[$baseCounterDetails]['scurrency'] = $CurrencyCode;
                                        $rooms[$baseCounterDetails]['sourceMarket'] = $sourceMarket;
                                        //
                                        // TODO - Specials
                                        //
                                        /* if ($PromotionName != "") {
                                            $rooms[$baseCounterDetails]['special'] = true;
                                            $rooms[$baseCounterDetails]['specialdescription'] = $PromotionName;
                                        } else { */
                                        $rooms[$baseCounterDetails]['special'] = false;
                                        $rooms[$baseCounterDetails]['specialdescription'] = "";
                                        //}
                                        //
                                        // TODO - Cancellation policies
                                        //
                                        $rooms[$baseCounterDetails]['cancelpolicy'] = "";

                                        $rooms[$baseCounterDetails]['currency'] = strtoupper($CurrencyCode);
                                        $baseCounterDetails ++;
                                        // $agoda = true;
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
    $delete->from('quote_session_abreu');
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
    $insert->into('quote_session_abreu');
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