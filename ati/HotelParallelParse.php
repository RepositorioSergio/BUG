<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n ATI - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $OTA_HotelAvailRS = $Body->item(0)->getElementsByTagName("OTA_HotelAvailRS");
    if ($OTA_HotelAvailRS->length > 0) {
        $RoomStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName("RoomStays");
        if ($RoomStays->length > 0) {
            $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
            if ($RoomStay->length > 0) {
                for ($i=0; $i < $RoomStay->length; $i++) { 
                    $RoomTypes = $RoomStay->item($i)->getElementsByTagName("RoomTypes");
                    if ($RoomTypes->length > 0) {
                        $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                        if ($RoomType->length > 0) {
                            $IsRoom = $RoomType->item(0)->getAttribute("IsRoom");
                            $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
                            $Amenities = $RoomType->item(0)->getElementsByTagName("Amenities");
                            if ($Amenities->length > 0) {
                                $Amenity = $Amenities->item(0)->getElementsByTagName("Amenity");
                                if ($Amenity->length > 0) {
                                    $CodeDetail = $Amenity->item(0)->getAttribute("CodeDetail");
                                    $RoomAmenityCode = $Amenity->item(0)->getAttribute("RoomAmenityCode");
                                }
                            }
                            $RoomDescription = $RoomType->item(0)->getElementsByTagName("RoomDescription");
                            if ($RoomDescription->length > 0) {
                                $Text = $RoomDescription->item(0)->getElementsByTagName("Text");
                                if ($Text->length > 0) {
                                    $Text = $Text->item(0)->nodeValue;
                                } else {
                                    $Text = "";
                                }
                            }
                        }
                    }
                    $RoomRates = $RoomStay->item($i)->getElementsByTagName("RoomRates");
                    if ($RoomRates->length > 0) {
                        $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                        if ($RoomRate->length > 0) {
                            $RatePlanCode = $RoomRate->item(0)->getAttribute("RatePlanCode");
                            $Rates = $RoomRate->item(0)->getElementsByTagName("Rates");
                            if ($Rates->length > 0) {
                                $Rate = $Rates->item(0)->getElementsByTagName("Rate");
                                if ($Rate->length > 0) {
                                    for ($iAux=0; $iAux < $Rate->length; $iAux++) { 
                                        $EffectiveDate = $Rate->item($iAux)->getAttribute("EffectiveDate");
                                        $Base = $Rate->item($iAux)->getElementsByTagName("Base");
                                        if ($Base->length > 0) {
                                            $AmountAfterTax = $Base->item(0)->getAttribute("AmountAfterTax");
                                            $CurrencyCode = $Base->item(0)->getAttribute("CurrencyCode");
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $GuestCounts = $RoomStay->item($i)->getElementsByTagName("GuestCounts");
                    if ($GuestCounts->length > 0) {
                        $IsPerRoom = $GuestCounts->item(0)->getAttribute("IsPerRoom");
                        $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
                        if ($GuestCount->length > 0) {
                            for ($iAux2=0; $iAux2 < $GuestCount->length; $iAux2++) { 
                                $AgeQualifyingCode = $GuestCount->item($iAux2)->getAttribute("AgeQualifyingCode");
                                $Count = $GuestCount->item($iAux2)->getAttribute("Count");
                                $ResGuestRPH = $GuestCount->item($iAux2)->getAttribute("ResGuestRPH");
                            }
                        }
                    }
                    $BasicPropertyInfo = $RoomStay->item($i)->getElementsByTagName("BasicPropertyInfo");
                    if ($BasicPropertyInfo->length > 0) {
                        $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                        $shid = $HotelCode;
                        $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
                    }
                    $CancelPenalties = $RoomStay->item($i)->getElementsByTagName("CancelPenalties");
                    if ($CancelPenalties->length > 0) {
                        $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                        if ($CancelPenalty->length > 0) {
                            $Deadline = $CancelPenalty->item(0)->getElementsByTagName("Deadline");
                            if ($Deadline->length > 0) {
                                $AbsoluteDeadline = $Deadline->item(0)->getAttribute("AbsoluteDeadline");
                            }
                        }
                    }
                    $Total = $RoomStay->item($i)->getElementsByTagName("Total");
                    if ($Total->length > 0) {
                        $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                        $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                    }
                    $cent = substr($AmountAfterTax, -2);
                    $amount = substr($AmountAfterTax, 0, (strlen($AmountAfterTax) - 2));
                    $total2 = $amount . '.' . $cent;
                    $total = (double)$total2;
                    $nettotal = $total;
    
                    $rooms[$baseCounterDetails]['name'] = $HotelName;
                    $rooms[$baseCounterDetails]['hotelid'] = $HotelCode;
                    $rooms[$baseCounterDetails]['roomid'] = $RoomTypeCode;
                    $rooms[$baseCounterDetails]['shid'] = $shid;
                    $rooms[$baseCounterDetails]['status'] = 1;
                    $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-40";
                    $rooms[$baseCounterDetails]['room'] = $Text;
                    $rooms[$baseCounterDetails]['roomtype'] = $RoomTypeCode;
                    $rooms[$baseCounterDetails]['room_description'] = $Text;
                    $rooms[$baseCounterDetails]['RatePlanCode'] = $RatePlanCode;
                    $rooms[$baseCounterDetails]['adults'] = $adults;
                    $rooms[$baseCounterDetails]['children'] = $children;
                    $rooms[$baseCounterDetails]['nettotal'] = (double) $nettotal;
                    if ($atiMarkup != 0) {
                        $total = $total + (($total * $atiMarkup) / 100);
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
                    if ($atiMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                        $sql = "select mapped from board_mapping where description='" . addslashes($BreakfastTypeName) . "'";
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        $row_board_mapping = $statement->execute();
                        $row_board_mapping->buffer();
                        if ($row_board_mapping->valid()) {
                            $row_board_mapping = $row_board_mapping->current();
                            $BreakfastTypeName = $row_board_mapping["mapped"];
                        }
                    } catch (\Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $rooms[$baseCounterDetails]['meal'] = $translator->translate($BreakfastTypeName);
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
                    $rooms[$baseCounterDetails]['scurrency'] = $CurrencyCode;
                    //
                    // Special
                    //
                    $rooms[$baseCounterDetails]['special'] = false;
                    $rooms[$baseCounterDetails]['specialdescription'] = "";
                    //
                    // Cancellation policies
                    //
                    $date = date('Y-m-d', strtotime($AbsoluteDeadline));
                    $deadline = strtotime($date);
                    $cancelpolicy_deadline = strftime("%a, %e %b %Y", $deadline);
                    $cancelpolicy = 'You must cancel a booking before ' . $cancelpolicy_deadline;
                    $rooms[$baseCounterDetails]['nonrefundable'] = false;
                    $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate($cancelpolicy);
                    $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $cancelpolicy_deadline;
                    $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;

                    $rooms[$baseCounterDetails]['currency'] = strtoupper($CurrencyCode);
                    $baseCounterDetails ++;
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
    $delete->from('quote_session_ati');
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
    $insert->into('quote_session_ati');
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