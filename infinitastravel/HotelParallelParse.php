<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n INFINITAS - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $HUB_HotelAvailRS = $inputDoc->getElementsByTagName("HUB_HotelAvailRS");
    $RoomStays = $HUB_HotelAvailRS->item(0)->getElementsByTagName("RoomStays");
    if ($RoomStays->length > 0) {
        $node = $RoomStays->item(0)->getElementsByTagName("RoomStay");
        for ($i = 0; $i < $node->length; $i ++) {
            $Hotel = $node->item($i)->getElementsByTagName("Hotel");
            if ($Hotel->length > 0) {
                $Code = $Hotel->item(0)->getAttribute("Code");
                $shid = $Code;
                $sfilter[] = " sid='$Code' ";
                $Name = $Hotel->item(0)->getAttribute("Name");
                $StarRating = $Hotel->item(0)->getAttribute("StarRating");
                $Description = $Hotel->item(0)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    $Description = $Description->item(0)->nodeValue;
                } else {
                    $Description = "";
                }
                // $MainPhoto = $Hotel->item(0)->getElementsByTagName("MainPhoto");
                // if ($MainPhoto->length > 0) {
                // $MainPhoto = $MainPhoto->item(0)->nodeValue;
                // } else {
                // $MainPhoto = "";
                // }
                // $Address = $Hotel->item(0)->getElementsByTagName("Address");
                // if ($Address->length > 0) {
                // $Latitude = $Address->item(0)->getAttribute("Latitude");
                // $Longitude = $Address->item(0)->getAttribute("Longitude");
                // $City = $Address->item(0)->getElementsByTagName("City");
                // if ($City->length > 0) {
                // $CityCode = $City->item(0)->getAttribute("Code");
                // $CityName = $City->item(0)->getAttribute("Name");
                // $CountryCode = $City->item(0)->getAttribute("CountryCode");
                // } else {
                // $Code = "";
                // $Name = "";
                // $CountryCode = "";
                // }
                // $Address2 = $Address->item(0)->getElementsByTagName("Address");
                // if ($Address2->length > 0) {
                // $Address2 = $Address2->item(0)->nodeValue;
                // } else {
                // $Address2 = "";
                // }
                // }
                // $PhoneNumbers = $Hotel->item(0)->getElementsByTagName("PhoneNumbers");
                // if ($PhoneNumbers->length > 0) {
                // $PhoneNumber = $PhoneNumbers->item(0)->getElementsByTagName("PhoneNumber");
                // if ($PhoneNumber->length > 0) {
                // $LineNumber = $PhoneNumber->item(0)->getAttribute("LineNumber");
                // $Prefix = $PhoneNumber->item(0)->getAttribute("Prefix");
                // $CountryAccessCode = $PhoneNumber->item(0)->getAttribute("CountryAccessCode");
                // $AreaCityCode = $PhoneNumber->item(0)->getAttribute("AreaCityCode");
                // } else {
                // $LineNumber = "";
                // $Prefix = "";
                // $CountryAccessCode = "";
                // $AreaCityCode = "";
                // }
                // }
                $RoomTypes = $node->item($i)->getElementsByTagName("RoomTypes");
                if ($RoomTypes->length > 0) {
                    $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        for ($j = 0; $j < $RoomType->length; $j ++) {
                            $RoomTypeCode = $RoomType->item($j)->getAttribute("Code");
                            $RoomTypeName = $RoomType->item($j)->getAttribute("Name");
                            $Rate = $RoomType->item($j)->getElementsByTagName("Rate");
                            if ($Rate->length > 0) {
                                $Comments = $Rate->item(0)->getElementsByTagName("Comments");
                                if ($Comments->length > 0) {
                                    $Comments = $Comments->item(0)->nodeValue;
                                } else {
                                    $Comments = "";
                                }
                                $IsImmediateConfirmation = $Rate->item(0)->getAttribute("IsImmediateConfirmation");
                                $HasAllIncluded = $Rate->item(0)->getAttribute("HasAllIncluded");
                                $HasBkftIncluded = $Rate->item(0)->getAttribute("HasBkftIncluded");
                                $HasFapIncluded = $Rate->item(0)->getAttribute("HasFapIncluded");
                                $HasMapIncluded = $Rate->item(0)->getAttribute("HasMapIncluded");
                                $CancelCost = $Rate->item(0)->getAttribute("CancelCost");
                                $DailyCostCancel = $Rate->item(0)->getAttribute("DailyCostCancel");
                                $DeadLineCancel = (int) $Rate->item(0)->getAttribute("DeadLineCancel");
                                $ChargingUnit = $Rate->item(0)->getAttribute("ChargingUnit");
                                $TotalValue = $Rate->item(0)->getAttribute("TotalValue");
                                $TotalValueNet = $TotalValue;
                                
                                $ID = $Rate->item(0)->getAttribute("ID");
                                $RoomsRateOccupants = $Rate->item(0)->getElementsByTagName("RoomsRateOccupants");
                                if ($RoomsRateOccupants->length > 0) {
                                    $RoomRateOccupants = $RoomsRateOccupants->item(0)->getElementsByTagName("RoomRateOccupants");
                                    for ($zRooms = 0; $zRooms < $RoomRateOccupants->length; $zRooms ++) {
                                        $TotalValueRO = $RoomRateOccupants->item($zRooms)->getAttribute("TotalValue");
                                        $TotalValueNetRO = $TotalValueRO;
                                        $OccupantsID = $RoomRateOccupants->item($zRooms)->getAttribute("OccupantsID");
                                        // $Guest = $RoomRateOccupants->item($zRooms)->getElementsByTagName("Guest");
                                        // if ($Guest->length > 0) {
                                        // $Count = $Guest->item(0)->getAttribute("Count");
                                        // $Age = $Guest->item(0)->getAttribute("Age");
                                        // $AgeQualifying = $Guest->item(0)->getAttribute("AgeQualifying");
                                        // } else {
                                        // $Count = "";
                                        // $Age = "";
                                        // $AgeQualifying = "";
                                        // }
                                        $AccommodationRate = $RoomRateOccupants->item($zRooms)->getElementsByTagName("AccommodationRate");
                                        if ($AccommodationRate->length > 0) {
                                            $Occupation = $AccommodationRate->item(0)->getAttribute("Occupation");
                                            $DailyRate = $AccommodationRate->item(0)->getElementsByTagName("DailyRate");
                                            if ($DailyRate->length > 0) {
                                                $DRTotalValue = $DailyRate->item(0)->getAttribute("TotalValue");
                                                $DailyValue = $DailyRate->item(0)->getAttribute("DailyValue");
                                                $End = $DailyRate->item(0)->getAttribute("End");
                                                $Start = $DailyRate->item(0)->getAttribute("Start");
                                            } else {
                                                $DRTotalValue = "";
                                                $DailyValue = "";
                                                $End = "";
                                                $Start = "";
                                            }
                                            $Guarantee = $AccommodationRate->item(0)->getElementsByTagName("Guarantee");
                                            if ($Guarantee->length > 0) {
                                                $Percentage = $Guarantee->item(0)->getAttribute("Percentage");
                                                $Deadline = $Guarantee->item(0)->getAttribute("Deadline");
                                                $Type = $Guarantee->item(0)->getElementsByTagName("Type");
                                                if ($Type->length > 0) {
                                                    $Type = $Type->item(0)->nodeValue;
                                                } else {
                                                    $Type = "";
                                                }
                                            } else {
                                                $Percentage = "";
                                                $Deadline = "";
                                                $Type = "";
                                            }
                                        } else {
                                            $Occupation = "";
                                        }

                                        $rooms[$baseCounterDetails]['name'] = $Name;
                                        $rooms[$baseCounterDetails]['hotelid'] = $Code;
                                        $rooms[$baseCounterDetails]['roomid'] = $RoomTypeCode;
                                        $rooms[$baseCounterDetails]['code'] = $shid;
                                        $rooms[$baseCounterDetails]['scode'] = $shid;
                                        $rooms[$baseCounterDetails]['shid'] = $shid;
                                        if ($IsImmediateConfirmation == "true") {
                                            $rooms[$baseCounterDetails]['status'] = 1;
                                        } else {
                                            $RoomTypeName .= " - " . $translator->translate("On Request Rate");
                                            $rooms[$baseCounterDetails]['status'] = 4;
                                        }
                                        
                                        $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-59";
                                        $rooms[$baseCounterDetails]['room'] = $RoomTypeName;
                                        $rooms[$baseCounterDetails]['roomtype'] = $RoomTypeCode;
                                        $rooms[$baseCounterDetails]['room_description'] = $RoomTypeName;
                                        $rooms[$baseCounterDetails]['adults'] = $adults;
                                        $rooms[$baseCounterDetails]['children'] = $children;
                                        $rooms[$baseCounterDetails]['nettotal'] = (double) $TotalValueNetRO;
                                        if ($infinitasMarkup != 0) {
                                            $TotalValueRO = $TotalValueRO + (($TotalValueRO * $infinitasMarkup) / 100);
                                        }
                                        // Geo target markup
                                        if ($internalmarkup != 0) {
                                            $TotalValueRO = $TotalValueRO + (($TotalValueRO * $internalmarkup) / 100);
                                        }
                                        // Agent markup
                                        if ($agent_markup != 0) {
                                            $TotalValueRO = $TotalValueRO + (($TotalValueRO * $agent_markup) / 100);
                                        }
                                        // Fallback Markup
                                        if ($infinitasMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                            $TotalValueRO = $TotalValueRO + (($TotalValueRO * $HotelsMarkupFallback) / 100);
                                        }
                                        // Agent discount
                                        if ($agent_discount != 0) {
                                            $TotalValueRO = $TotalValueRO - (($TotalValueRO * $agent_discount) / 100);
                                        }
                                        if ($scurrency != "" and $currency != $scurrency) {
                                            $TotalValueRO = $CurrencyConverter->convert($TotalValueRO, $currency, $scurrency);
                                        }
                                        $rooms[$baseCounterDetails]['total'] = (double) $TotalValueRO;
                                        $rooms[$baseCounterDetails]['totalplain'] = (double) $TotalValueRO;

                                        $BoardType = $translator->translate("Room Only");
                                        if ($HasBkftIncluded == "true") {
                                            $BoardType = $translator->translate("Bed & Breakfast");
                                        }
                                        if ($HasMapIncluded == "true") {
                                            $BoardType = $translator->translate("Half Board");
                                        }
                                        if ($HasFapIncluded == "true") {
                                            $BoardType = $translator->translate("Full Board");
                                        }
                                        if ($HasAllIncluded == "true") {
                                            $BoardType = $translator->translate("All Inclusive");
                                        }
                                        try {
                                            $sql = "select mapped from board_mapping where description='" . addslashes($BoardType) . "'";
                                            $statement = $db->createStatement($sql);
                                            $statement->prepare();
                                            $row_board_mapping = $statement->execute();
                                            $row_board_mapping->buffer();
                                            if ($row_board_mapping->valid()) {
                                                $row_board_mapping = $row_board_mapping->current();
                                                $BoardType = $row_board_mapping["mapped"];
                                            }
                                        } catch (\Exception $e) {
                                            $logger = new Logger();
                                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                            $logger->addWriter($writer);
                                            $logger->info($e->getMessage());
                                        }
                                        $rooms[$baseCounterDetails]['meal'] = $translator->translate($BoardType);
                                        $pricebreakdown = array();
                                        $pricebreakdownCount = 0;
                                        $amount = $TotalValueRO / $noOfNights;
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
                                        $CancelCost = $CurrencyConverter->convert($CancelCost, $currency, $scurrency);
                                        $ddate = mktime(0, 0, 0, date("m", $from), date("d", $from) - ($DeadLineCancel + 1), date("y", $from));
                                        if ($ddate < time()) {
                                            $rooms[$baseCounterDetails]['nonrefundable'] = true;
                                            $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking. You will be charged full stay of the booking.");
                                            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = time();
                                        } else {
                                            $rooms[$baseCounterDetails]['nonrefundable'] = false;
                                            $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("Charge") . " " . $scurrency . " " . $CancelCost . " " . $translator->translate("if cancelled on or after") . " " . strftime("%a, %d %B %Y", $ddate);
                                            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $ddate;
                                        }
                                        
                                        $rooms[$baseCounterDetails]['nodedup'] = false;

                                        $rooms[$baseCounterDetails]['currency'] = strtoupper($CurrencyCode);
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
    $delete->from('quote_session_infinitastravel');
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
    $insert->into('quote_session_infinitastravel');
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