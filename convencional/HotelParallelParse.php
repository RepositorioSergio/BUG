<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n CONVENCIONAL - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $getHotelAvailResponse = $Body->item(0)->getElementsByTagName("getHotelAvailResponse");
    $getHotelAvailResult = $getHotelAvailResponse->item(0)->getElementsByTagName("getHotelAvailResult");
    $ID = $getHotelAvailResult->item(0)->getAttribute("ID");
    $RoomStays = $getHotelAvailResult->item(0)->getElementsByTagName("RoomStays");
    $node = $RoomStays->item(0)->getElementsByTagName("RoomStay");
    for ($i = 0; $i < $node->length; $i ++) {
        // Hotel
        $Hotel = $node->item($i)->getElementsByTagName("Hotel");
        if ($Hotel->length > 0) {
            $Code = $Hotel->item(0)->getAttribute("Code");
            $shid = $Code;
            $Name = $Hotel->item(0)->getAttribute("Name");
            // $StarRating = $Hotel->item(0)->getAttribute("StarRating");
            // $SubCategory = $Hotel->item(0)->getAttribute("SubCategory");
            // $Email = $Hotel->item(0)->getAttribute("Email");
            // $Url = $Hotel->item(0)->getAttribute("Url");
            // $UrlVirtualTour = $Hotel->item(0)->getAttribute("UrlVirtualTour");
            // $MinAccommodationRate = $Hotel->item(0)->getAttribute("MinAccommodationRate");
            // $MaxAccommodationRate = $Hotel->item(0)->getAttribute("MaxAccommodationRate");
            // $Description = $Hotel->item(0)->getElementsByTagName("Description");
            // if ($Description->length > 0) {
            // $Description = $Description->item(0)->nodeValue;
            // } else {
            // $Description = "";
            // }
            $Comments = $Hotel->item(0)->getElementsByTagName("Comments");
            if ($Comments->length > 0) {
                $Comments = $Comments->item(0)->nodeValue;
            } else {
                $Comments = "";
            }
            // $Address2 = "";
            // $Address = $Hotel->item(0)->getElementsByTagName("Address");
            // if ($Address->length > 0) {
            // $Latitude = $Address->item(0)->getAttribute("Latitude");
            // $Longitude = $Address->item(0)->getAttribute("Longitude");
            // $Address2 = $Address->item(0)->nodeValue;
            // $City = $Address->item(0)->getElementsByTagName("City");
            // if ($City->length > 0) {
            // $CityCode = $City->item(0)->getAttribute("Code");
            // $CityName = $City->item(0)->getAttribute("Name");
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
            // $MainPhoto = $Hotel->item(0)->getElementsByTagName("MainPhoto");
            // if ($MainPhoto->length > 0) {
            // $MainPhoto = $MainPhoto->item(0)->nodeValue;
            // } else {
            // $MainPhoto = "";
            // }
            // $MinAccommodationRateCurrency = $Hotel->item(0)->getElementsByTagName("MinAccommodationRateCurrency");
            // if ($MinAccommodationRateCurrency->length > 0) {
            // $MinAccCode = $MinAccommodationRateCurrency->item(0)->getAttribute("Code");
            // } else {
            // $MinAccCode = "";
            // }
            // $MaxAccommodationRateCurrency = $Hotel->item(0)->getElementsByTagName("MaxAccommodationRateCurrency");
            // if ($MaxAccommodationRateCurrency->length > 0) {
            // $MaxAccCode = $MaxAccommodationRateCurrency->item(0)->getAttribute("Code");
            // } else {
            // $MaxAccCode = "";
            // }
        }
        // RoomRates
        $RoomRates = $node->item($i)->getElementsByTagName("RoomRates");
        if ($RoomRates->length > 0) {
            $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
            if ($RoomRate->length > 0) {
                for ($k = 0; $k < $RoomRate->length; $k ++) {
                    $IDRoomRate = $RoomRate->item($k)->getAttribute("ID");
                    $FitGroup = $RoomRate->item($k)->getAttribute("FitGroup");
                    $HasAllIncluded = $RoomRate->item($k)->getAttribute("HasAllIncluded");
                    $HasBkftIncluded = $RoomRate->item($k)->getAttribute("HasBkftIncluded");
                    $HasFapIncluded = $RoomRate->item($k)->getAttribute("HasFapIncluded");
                    $HasMapIncluded = $RoomRate->item($k)->getAttribute("HasMapIncluded");
                    $CancelCost = $RoomRate->item($k)->getAttribute("CancelCost");
                    $DailyCostCancel = $RoomRate->item($k)->getAttribute("DailyCostCancel");
                    $DeadLineCancel = (int) $RoomRate->item($k)->getAttribute("DeadLineCancel");
                    $ChargingUnit = $RoomRate->item($k)->getAttribute("ChargingUnit");
                    $TotalValue = $RoomRate->item($k)->getAttribute("TotalValue");
                    $TotalValueNet = $TotalValue;
                    $Currency = $RoomRate->item($k)->getElementsByTagName("Currency");
                    if ($Currency->length > 0) {
                        $CurrencyCode = $Currency->item(0)->getAttribute("Code");
                    } else {
                        $CurrencyCode = "";
                    }
                    $currency = $CurrencyCode;
                    
                    $Market = $RoomRate->item($k)->getElementsByTagName("Market");
                    if ($Market->length > 0) {
                        $MarketCode = $Market->item(0)->getAttribute("Code");
                    } else {
                        $MarketCode = "";
                    }
                    $Comments = $RoomRate->item($k)->getElementsByTagName("Comments");
                    if ($Comments->length > 0) {
                        $Comments = $Comments->item(0)->nodeValue;
                    } else {
                        $Comments = "";
                    }
                    // RoomType
                    $RoomType = $RoomRate->item($k)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $RoomTypeCode = $RoomType->item(0)->getAttribute("Code");
                        $RoomTypeName = $RoomType->item(0)->getAttribute("Name");
                        $RoomsOccupants = $RoomType->item(0)->getElementsByTagName("RoomsOccupants");
                        $RoomOccupants = $RoomsOccupants->item(0)->getElementsByTagName("RoomOccupants");
                        $RoomRateOccupants = $RoomOccupants->item(0)->getElementsByTagName("RoomRateOccupants");
                        if ($RoomRateOccupants->length > 0) {
                            $OccupantsID = $RoomRateOccupants->item(0)->getAttribute("OccupantsID");
                            $IsImmediateConfirmation = $RoomRateOccupants->item(0)->getAttribute("IsImmediateConfirmation");
                            $TotalValueRate = $RoomRateOccupants->item(0)->getAttribute("TotalValue");
                            $Guest = $RoomRateOccupants->item(0)->getElementsByTagName("Guest");
                            if ($Guest->length > 0) {
                                $Count = $Guest->item(0)->getAttribute("Count");
                                $Age = $Guest->item(0)->getAttribute("Age");
                                $AgeType = $Guest->item(0)->getAttribute("AgeType");
                            } else {
                                $Count = "";
                                $Age = "";
                                $AgeType = "";
                            }
                            $AccommodationRate = $RoomRateOccupants->item(0)->getElementsByTagName("AccommodationRate");
                            if ($AccommodationRate->length > 0) {
                                $Occupation = $AccommodationRate->item(0)->getAttribute("Occupation");
                                $DailyRate = $AccommodationRate->item(0)->getElementsByTagName("DailyRate");
                                if ($DailyRate->length > 0) {
                                    for ($d = 0; $d < $DailyRate->length; $d ++) {
                                        $TotalValueAcc = $DailyRate->item($d)->getAttribute("TotalValue");
                                        $DailyValue = $DailyRate->item($d)->getAttribute("DailyValue");
                                        $End = $DailyRate->item($d)->getAttribute("End");
                                        $Start = $DailyRate->item($d)->getAttribute("Start");
                                    }
                                }
                                $Guarantee = $AccommodationRate->item(0)->getElementsByTagName("Guarantee");
                                if ($Guarantee->length > 0) {
                                    $Type = $Guarantee->item(0)->getAttribute("Type");
                                    $Percentage = $Guarantee->item(0)->getAttribute("Percentage");
                                    $Deadline = $Guarantee->item(0)->getAttribute("Deadline");
                                } else {
                                    $Type = "";
                                    $Percentage = "";
                                    $Deadline = "";
                                }
                            } else {
                                $Occupation = "";
                            }
                            $OptionalServices = $RoomRateOccupants->item(0)->getElementsByTagName("OptionalServices");
                            if ($OptionalServices->length > 0) {
                                $OptionalService = $OptionalServices->item(0)->getElementsByTagName("OptionalService");
                                if ($OptionalService->length > 0) {
                                    $OptionalServiceCode = $OptionalService->item(0)->getAttribute("Code");
                                    $OptionalServiceName = $OptionalService->item(0)->getAttribute("Name");
                                    $OptionalServiceOccupation = $OptionalService->item(0)->getAttribute("Occupation");
                                    $OptionalServiceRateID = $OptionalService->item(0)->getAttribute("RateID");
                                    $OptionalServiceValue = $OptionalService->item(0)->getAttribute("Value");
                                    $OptionalServiceChargeUnit = $OptionalService->item(0)->getAttribute("ChargeUnit");
                                    $OptionalServiceCategoryCode = $OptionalService->item(0)->getAttribute("CategoryCode");
                                    $DailyRate = $OptionalService->item(0)->getElementsByTagName("DailyRate");
                                    if ($DailyRate->length > 0) {
                                        for ($op = 0; $op < $DailyRate->length; $op ++) {
                                            $DailyRateTotalValue = $DailyRate->item($op)->getAttribute("TotalValue");
                                            $DailyRateDailyValue = $DailyRate->item($op)->getAttribute("DailyValue");
                                            $DailyRateEnd = $DailyRate->item($op)->getAttribute("End");
                                            $DailyRateStart = $DailyRate->item($op)->getAttribute("Start");
                                        }
                                    }
                                } else {
                                    $OptionalServiceCode = "";
                                    $OptionalServiceName = "";
                                    $OptionalServiceOccupation = "";
                                    $OptionalServiceRateID = "";
                                    $OptionalServiceValue = "";
                                    $OptionalServiceChargeUnit = "";
                                    $OptionalServiceCategoryCode = "";
                                }
                            }
                        } else {
                            $OccupantsID = "";
                            $IsImmediateConfirmation = "";
                            $TotalValueRate = "";
                        }
                    } else {
                        $RoomTypeCode = "";
                        $RoomTypeName = "";
                    }

                    $rooms[$baseCounterDetails]['name'] = $Name;
                    $rooms[$baseCounterDetails]['hotelid'] = $Code;
                    $rooms[$baseCounterDetails]['roomid'] = $IDRoomRate;
                    $rooms[$baseCounterDetails]['code'] = $Code;
                    $rooms[$baseCounterDetails]['scode'] = $Code;
                    $rooms[$baseCounterDetails]['shid'] = $shid;
                    $rooms[$baseCounterDetails]['status'] = 1;
                    $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-38";
                    $rooms[$baseCounterDetails]['room'] = ucwords(strtolower($RoomTypeName));
                    $rooms[$baseCounterDetails]['roomtype'] = $RoomTypeCode;
                    $rooms[$baseCounterDetails]['room_description'] = $Comments;
                    $rooms[$baseCounterDetails]['CityCode'] = $CityCode;
                    $rooms[$baseCounterDetails]['ItemCode'] = $ItemCode;
                    $rooms[$baseCounterDetails]['ItemNo'] = $ItemNo;
                    $rooms[$baseCounterDetails]['adults'] = $adults;
                    $rooms[$baseCounterDetails]['children'] = $children;
                    $rooms[$baseCounterDetails]['nettotal'] = (double) $TotalValueNet;
                    if ($convencionalMarkup != 0) {
                        $TotalValue = $TotalValue + (($TotalValue * $convencionalMarkup) / 100);
                    }
                    // Geo target markup
                    if ($internalmarkup != 0) {
                        $TotalValue = $TotalValue + (($TotalValue * $internalmarkup) / 100);
                    }
                    // Agent markup
                    if ($agent_markup != 0) {
                        $TotalValue = $TotalValue + (($TotalValue * $agent_markup) / 100);
                    }
                    // Fallback Markup
                    if ($convencionalMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                        $TotalValue = $TotalValue + (($TotalValue * $HotelsMarkupFallback) / 100);
                    }
                    // Agent discount
                    if ($agent_discount != 0) {
                        $TotalValue = $TotalValue - (($TotalValue * $agent_discount) / 100);
                    }
                    if ($scurrency != "" and $currency != $scurrency) {
                        $TotalValue = $CurrencyConverter->convert($TotalValue, $currency, $scurrency);
                    }
                    $rooms[$baseCounterDetails]['total'] = (double) $TotalValue;
                    $rooms[$baseCounterDetails]['totalplain'] = (double) $TotalValue;

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
                    $amount = $TotalValue / $noOfNights;
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

                    $rooms[$baseCounterDetails]['DailyRateStart'] = $DailyRateStart;
                    $rooms[$baseCounterDetails]['DailyCostCancel'] = $DailyCostCancel;
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
                    $rooms[$baseCounterDetails]['currency'] = strtoupper($currency);
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
    $delete->from('quote_session_convencional');
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
    $insert->into('quote_session_convencional');
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