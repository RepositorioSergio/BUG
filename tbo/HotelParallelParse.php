<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n TBO - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $HotelRoomAvailabilityResponse = $Body->item(0)->getElementsByTagName("HotelRoomAvailabilityResponse");

    $HotelRooms = $HotelRoomAvailabilityResponse->item(0)->getElementsByTagName("HotelRooms");
    if ($HotelRooms->length > 0) {
        $HotelRoom = $HotelRooms->item(0)->getElementsByTagName("HotelRoom");
        if ($HotelRoom->length > 0) {
            for ($i=0; $i < $HotelRoom->length; $i++) { 
                $RoomIndex = $HotelRoom->item($i)->getElementsByTagName("RoomIndex");
                if ($RoomIndex->length > 0) {
                    $RoomIndex = $RoomIndex->item(0)->nodeValue;
                } else {
                    $RoomIndex = "";
                }
                $RoomTypeName = $HotelRoom->item($i)->getElementsByTagName("RoomTypeName");
                if ($RoomTypeName->length > 0) {
                    $RoomTypeName = $RoomTypeName->item(0)->nodeValue;
                } else {
                    $RoomTypeName = "";
                }
                $Inclusion = $HotelRoom->item($i)->getElementsByTagName("Inclusion");
                if ($Inclusion->length > 0) {
                    $Inclusion = $Inclusion->item(0)->nodeValue;
                } else {
                    $Inclusion = "";
                }
                $RoomTypeCode = $HotelRoom->item($i)->getElementsByTagName("RoomTypeCode");
                if ($RoomTypeCode->length > 0) {
                    $RoomTypeCode = $RoomTypeCode->item(0)->nodeValue;
                } else {
                    $RoomTypeCode = "";
                }
                $RatePlanCode = $HotelRoom->item($i)->getElementsByTagName("RatePlanCode");
                if ($RatePlanCode->length > 0) {
                    $RatePlanCode = $RatePlanCode->item(0)->nodeValue;
                } else {
                    $RatePlanCode = "";
                }
                $RoomPromtion = $HotelRoom->item($i)->getElementsByTagName("RoomPromtion");
                if ($RoomPromtion->length > 0) {
                    $RoomPromtion = $RoomPromtion->item(0)->nodeValue;
                } else {
                    $RoomPromtion = "";
                }
                $Amenities = $HotelRoom->item($i)->getElementsByTagName("Amenities");
                if ($Amenities->length > 0) {
                    $Amenities = $Amenities->item(0)->nodeValue;
                } else {
                    $Amenities = "";
                }
                $MealType = $HotelRoom->item($i)->getElementsByTagName("MealType");
                if ($MealType->length > 0) {
                    $MealType = $MealType->item(0)->nodeValue;
                } else {
                    $MealType = "";
                }

                $RoomRate = $HotelRoom->item($i)->getElementsByTagName("RoomRate");
                if ($RoomRate->length > 0) {
                    $B2CRates = $RoomRate->item(0)->getAttribute("B2CRates");
                    $PrefCurrency = $RoomRate->item(0)->getAttribute("PrefCurrency");
                    $TotalFare = $RoomRate->item(0)->getAttribute("TotalFare");
                    $PrefPrice = $RoomRate->item(0)->getAttribute("PrefPrice");
                    $RoomTax = $RoomRate->item(0)->getAttribute("RoomTax");
                    $AgentMarkUp = $RoomRate->item(0)->getAttribute("AgentMarkUp");
                    $Currency = $RoomRate->item(0)->getAttribute("Currency");
                    $RoomFare = $RoomRate->item(0)->getAttribute("RoomFare");
                    $IsPackageRate = $RoomRate->item(0)->getAttribute("IsPackageRate");

                    $ExtraGuestCharges = $RoomRate->item(0)->getElementsByTagName("ExtraGuestCharges");
                    if ($ExtraGuestCharges->length > 0) {
                        $ExtraGuestCharges = $ExtraGuestCharges->item(0)->nodeValue;
                    } else {
                        $ExtraGuestCharges = "";
                    }
                    $ChildCharges = $RoomRate->item(0)->getElementsByTagName("ChildCharges");
                    if ($ChildCharges->length > 0) {
                        $ChildCharges = $ChildCharges->item(0)->nodeValue;
                    } else {
                        $ChildCharges = "";
                    }
                    $Discount = $RoomRate->item(0)->getElementsByTagName("Discount");
                    if ($Discount->length > 0) {
                        $Discount = $Discount->item(0)->nodeValue;
                    } else {
                        $Discount = "";
                    }
                    $OtherCharges = $RoomRate->item(0)->getElementsByTagName("OtherCharges");
                    if ($OtherCharges->length > 0) {
                        $OtherCharges = $OtherCharges->item(0)->nodeValue;
                    } else {
                        $OtherCharges = "";
                    }
                    $ServiceTax = $RoomRate->item(0)->getElementsByTagName("ServiceTax");
                    if ($ServiceTax->length > 0) {
                        $ServiceTax = $ServiceTax->item(0)->nodeValue;
                    } else {
                        $ServiceTax = "";
                    }

                    $DayRates = $RoomRate->item(0)->getElementsByTagName("DayRates");
                    if ($DayRates->length > 0) {
                        $DayRates2 = $DayRates->item(0)->getElementsByTagName("DayRates");
                        if ($DayRates2->length > 0) {
                            for ($j=0; $j < $DayRates2->length; $j++) { 
                                $BaseFare = $DayRates2->item($j)->getAttribute("BaseFare");
                                $Date = $DayRates2->item($j)->getAttribute("Date");
                            }
                        }
                    }
                } 

                $Supplements = $HotelRoom->item($i)->getElementsByTagName("Supplements");
                if ($Supplements->length > 0) {
                    $Supplement = $Supplements->item(0)->getElementsByTagName("Supplement");
                    if ($Supplement->length > 0) {
                        for ($k=0; $k < $Supplement->length; $k++) { 
                            $SuppID = $Supplement->item($k)->getAttribute("SuppID");
                            $SuppName = $Supplement->item($k)->getAttribute("SuppName");
                            $Type = $Supplement->item($k)->getAttribute("Type");
                            $SuppIsMandatory = $Supplement->item($k)->getAttribute("SuppIsMandatory");
                            $SuppChargeType = $Supplement->item($k)->getAttribute("SuppChargeType");
                            $Price = $Supplement->item($k)->getAttribute("Price");
                            $CurrencyCode = $Supplement->item($k)->getAttribute("CurrencyCode");
                        }
                    }
                }
                $CancelPolicies = $HotelRoom->item($i)->getElementsByTagName("CancelPolicies");
                if ($CancelPolicies->length > 0) {
                    $LastCancellationDeadline = $CancelPolicies->item(0)->getElementsByTagName("LastCancellationDeadline");
                    if ($LastCancellationDeadline->length > 0) {
                        $LastCancellationDeadline = $LastCancellationDeadline->item(0)->nodeValue;
                    } else {
                        $LastCancellationDeadline = "";
                    }
                    $DefaultPolicy = $CancelPolicies->item(0)->getElementsByTagName("DefaultPolicy");
                    if ($DefaultPolicy->length > 0) {
                        $DefaultPolicy = $DefaultPolicy->item(0)->nodeValue;
                    } else {
                        $DefaultPolicy = "";
                    }
                    
                    $CancelPolicy = $CancelPolicies->item(0)->getElementsByTagName("CancelPolicy");
                    if ($CancelPolicy->length > 0) {
                        $RoomTypeName = $CancelPolicy->item(0)->getAttribute("RoomTypeName");
                        $FromDate = $CancelPolicy->item(0)->getAttribute("FromDate");
                        $ToDate = $CancelPolicy->item(0)->getAttribute("ToDate");
                        $ChargeType = $CancelPolicy->item(0)->getAttribute("ChargeType");
                        $CancellationCharge = $CancelPolicy->item(0)->getAttribute("CancellationCharge");
                        $Currency = $CancelPolicy->item(0)->getAttribute("Currency");
                    }
                }
                $rooms[$baseCounterDetails]['name'] = $HotelName;
                $rooms[$baseCounterDetails]['hotelid'] = $HotelCode;
                $rooms[$baseCounterDetails]['roomid'] = $RoomIndex;
                $rooms[$baseCounterDetails]['code'] = $shid;
                $rooms[$baseCounterDetails]['scode'] = $shid;
                $rooms[$baseCounterDetails]['shid'] = $shid;
                $rooms[$baseCounterDetails]['status'] = 1;
                $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-61";
                $rooms[$baseCounterDetails]['room'] = $RoomTypeName;
                $rooms[$baseCounterDetails]['roomtype'] = $RoomTypeCode;
                $rooms[$baseCounterDetails]['room_description'] = $RoomTypeName;
                $rooms[$baseCounterDetails]['RatePlanCode'] = $RatePlanCode;
                $rooms[$baseCounterDetails]['SessionId'] = $SessionId;
                $rooms[$baseCounterDetails]['ResultIndex'] = $ResultIndex;
                $rooms[$baseCounterDetails]['adults'] = $adults;
                $rooms[$baseCounterDetails]['children'] = $children;
                $rooms[$baseCounterDetails]['nettotal'] = (double) $RoomFare;
                if ($tboMarkup != 0) {
                    $TotalFare = $TotalFare + (($TotalFare * $tboMarkup) / 100);
                }
                // Geo target markup
                if ($internalmarkup != 0) {
                    $TotalFare = $TotalFare + (($TotalFare * $internalmarkup) / 100);
                }
                // Agent markup
                if ($agent_markup != 0) {
                    $TotalFare = $TotalFare + (($TotalFare * $agent_markup) / 100);
                }
                // Fallback Markup
                if ($tboMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                    $TotalFare = $TotalFare + (($TotalFare * $HotelsMarkupFallback) / 100);
                }
                // Agent discount
                if ($agent_discount != 0) {
                    $TotalFare = $TotalFare - (($TotalFare * $agent_discount) / 100);
                }
                if ($scurrency != "" and $currency != $scurrency) {
                    $TotalFare = $CurrencyConverter->convert($TotalFare, $currency, $scurrency);
                }
                $rooms[$baseCounterDetails]['total'] = (double) $TotalFare;
                $rooms[$baseCounterDetails]['totalplain'] = (double) $TotalFare;
                try {
                    $sql = "select mapped from board_mapping where description='" . addslashes($MealType) . "'";
                    $statement = $db->createStatement($sql);
                    $statement->prepare();
                    $row_board_mapping = $statement->execute();
                    $row_board_mapping->buffer();
                    if ($row_board_mapping->valid()) {
                        $row_board_mapping = $row_board_mapping->current();
                        $MealType = $row_board_mapping["mapped"];
                    }
                } catch (\Exception $e) {
                    $logger = new Logger();
                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                }
                $rooms[$baseCounterDetails]['meal'] = $translator->translate($MealType);
                $pricebreakdown = array();
                $pricebreakdownCount = 0;
                $amount = $SellerNetPrice / $noOfNights;
                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                    $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                    $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                    $pricebreakdownCount = $pricebreakdownCount + 1;
                }
                $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                $rooms[$baseCounterDetails]['scurrency'] = $ClientCurrencyCode;
                //
                // Special
                //
                if ($RoomPromtion != "") {
                    $rooms[$baseCounterDetails]['special'] = true;
                    $rooms[$baseCounterDetails]['specialdescription'] = $RoomPromtion;
                } else {
                    $rooms[$baseCounterDetails]['special'] = false;
                    $rooms[$baseCounterDetails]['specialdescription'] = "";
                }
                $rooms[$baseCounterDetails]['DailyCostCancel'] = $DailyCostCancel;
                //
                // Cancellation policies
                //
                if ($FromDate == "") {
                    $rooms[$baseCounterDetails]['nonrefundable'] = true;
                    $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking");
                    $rooms[$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
                    $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                    $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
                } else {
                    $cancelation_details = "From Date " . $FromDate . " To Date " . $ToDate . " Charge " . $CancellationCharge . "% amount of the booking.";;
                    $rooms[$baseCounterDetails]['cancelpolicy'] = $cancelation_details;
                    $rooms[$baseCounterDetails]['cancelpolicy_details'] = $cancelation_details;
                    $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = date('d-m-Y', strtotime($LastCancellationDeadline));
                }
                $rooms[$baseCounterDetails]['currency'] = strtoupper($scurrency);
                $baseCounterDetails ++;
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
    $delete->from('quote_session_tbo');
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
    $insert->into('quote_session_tbo');
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