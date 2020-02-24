<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nGTA - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    if ($response != "") {
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $responseElement = $inputDoc->documentElement;
        $xpath = new DOMXPath($inputDoc);
        $errorsElements = $xpath->query('Errors', $responseElement);
        if ($errorsElements->length > 0) {
            // Process Errors
            // Log error
        }
        $errorsElements = $xpath->query('ResponseDetails/SearchHotelPricePaxResponse/Errors', $responseElement);
        if ($errorsElements->length > 0) {
            // Process Errors
            // Log error
        } else {
            // Process Response Data
            $searchHotelPriceReponseElements = "";
            $searchHotelPriceReponseElements = $xpath->query('ResponseDetails/SearchHotelPricePaxResponse', $responseElement);
            foreach ($searchHotelPriceReponseElements as $searchHotelPriceReponseElement) {
                $hotelElements = $xpath->query('HotelDetails/Hotel', $searchHotelPriceReponseElement);
                foreach ($hotelElements as $hotelElement) {
                    // Process each Hotel
                    $recommended = $hotelElement->getAttribute("Recommended");
                    $special = false;
                    $xExtraCount = 0;
                    $cityExtra = $xpath->query('City', $hotelElement);
                    $cityExtra = $cityExtra->item(0)->getAttributeNode('Code');
                    $cityExtra = $cityExtra->value;
                    $item = $xpath->query('Item', $hotelElement);
                    $shidTmp = $item->item(0)->getAttributeNode('Code');
                    $item = $item->item(0)->textContent;
                    $shid = $shidTmp->value;
                    
                    $hotelElementsExtra = $xpath->query('EssentialInformation/Information', $hotelElement);
                    $xEssentialInformation = 0;
                    foreach ($hotelElementsExtra as $hotelElementExtra) {
                        $EssentialInformation = "";
                        $TextAux = $xpath->query('Text', $hotelElementExtra);
                        $TextAux = $TextAux->item(0)->textContent;
                        if ($EssentialInformation != "") {
                            $EssentialInformation .= " ~ ";
                        }
                        $EssentialInformation .= $TextAux;
                        $xEssentialInformation = $xEssentialInformation + 1;
                    }
                    $xRoomIndex = 0;
                    $PaxRoomSearchResults = $xpath->query('PaxRoomSearchResults/PaxRoom', $hotelElement);
                    foreach ($PaxRoomSearchResults as $PaxRoomSearchResult) {
                        $RoomIndex = $PaxRoomSearchResults->item($xRoomIndex)->getAttribute('RoomIndex');
                        $xRoomIndex = $xRoomIndex + 1;
                        $hotelElementsExtra = $xpath->query('RoomCategories/RoomCategory', $PaxRoomSearchResult);
                        $baseCounterDetails = 0;
                        $hotelSelected = 0;
                        $xRoomCategoryId = 0;
                        foreach ($hotelElementsExtra as $hotelElementExtra) {
                            $EssentialInformation = "";
                            $hotelElementsExtraTmp = $xpath->query('EssentialInformation/Information', $hotelElementExtra);
                            foreach ($hotelElementsExtraTmp as $hotelElementExtraAux) {
                                $TextAux = $xpath->query('Text', $hotelElementExtraAux);
                                $TextAux = $TextAux->item(0)->textContent;
                                if ($EssentialInformation != "") {
                                    $EssentialInformation .= " ~ ";
                                }
                                $EssentialInformation .= $TextAux;
                                $xEssentialInformation = $xEssentialInformation + 1;
                            }
                            $pricebreakdown = array();
                            $pricebreakdownCount = 0;
                            $offerid = $hotelElementsExtra->item($xRoomCategoryId)->getAttributeNode('Id');
                            $offerid = $offerid->value;
                            if ($xExtraCount == 0) {
                                $itemPriceFrom = $xpath->query('ItemPrice', $hotelElementExtra);
                                $Currency = $itemPriceFrom->item(0)->getAttributeNode('Currency');
                                $base_currency = $Currency->value;
                                $CommissionPercentage = $itemPriceFrom->item(0)->getAttribute('CommissionPercentage');
                                $GrossWithoutDiscount = $itemPriceFrom->item(0)->getAttribute('GrossWithoutDiscount');
                                $IncludedOfferDiscount = $itemPriceFrom->item(0)->getAttribute('IncludedOfferDiscount');
                                if ($IncludedOfferDiscount != "") {
                                    if (is_numeric($IncludedOfferDiscount)) {
                                        if ($IncludedOfferDiscount > 0) {
                                            if ($gtamarkup > 0) {
                                                $IncludedOfferDiscount = $IncludedOfferDiscount + (($IncludedOfferDiscount * $gtamarkup) / 100);
                                            }
                                            if ($internalmarkup > 0) {
                                                $IncludedOfferDiscount = $IncludedOfferDiscount + (($IncludedOfferDiscount * $internalmarkup) / 100);
                                            }
                                            if ($agent_markup > 0) {
                                                $IncludedOfferDiscount = $IncludedOfferDiscount + (($IncludedOfferDiscount * $agent_markup) / 100);
                                            }
                                            // Fallback Markup
                                            if ($gtamarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                                $IncludedOfferDiscount = $IncludedOfferDiscount + (($IncludedOfferDiscount * $HotelsMarkupFallback) / 100);
                                            }
                                            if ($agent_discount > 0) {
                                                $IncludedOfferDiscount = $IncludedOfferDiscount - (($IncludedOfferDiscount * $agent_discount) / 100);
                                            }
                                            if ($scurrency != "" and $base_currency != $scurrency) {
                                                $IncludedOfferDiscount = $CurrencyConverter->convert($IncludedOfferDiscount, $base_currency, $scurrency);
                                            }
                                        }
                                    }
                                }
                                $confirmationFrom = $xpath->query('Confirmation', $hotelElementExtra);
                                $confirmationFrom = $confirmationFrom->item(0)->textContent;
                                // Markup
                                // if ($gtamarkup != "") {
                                // if (is_numeric($gtamarkup)) {
                                // if ($gtamarkup != 0) {
                                // if (is_numeric($itemPriceFrom)) {
                                // if ($itemPriceFrom != "") {
                                // $itemPriceFrom = $itemPriceFrom + (($itemPriceFrom * $gtamarkup) / 100);
                                // }
                                // }
                                // }
                                // }
                                // }
                                // Geo target markup
                                // if ($internalmarkup > 0) {
                                // if (is_numeric($itemPriceFrom)) {
                                // if ($itemPriceFrom != "") {
                                // $itemPriceFrom = $itemPriceFrom + (($itemPriceFrom * $internalmarkup) / 100);
                                // }
                                // }
                                // }
                                // Agent markup
                                // if ($agent_markup > 0) {
                                // if (is_numeric($itemPriceFrom)) {
                                // if ($itemPriceFrom != "") {
                                // $itemPriceFrom = $itemPriceFrom + (($itemPriceFrom * $agent_markup) / 100);
                                // }
                                // }
                                // }
                                // Agent discount
                                // if ($agent_discount > 0) {
                                // if (is_numeric($itemPriceFrom)) {
                                // if ($itemPriceFrom != "") {
                                // $itemPriceFrom = $itemPriceFrom - (($itemPriceFrom * $agent_discount) / 100);
                                // }
                                // }
                                // }
                                // if ($currency_table_exists == 1) {
                                // if ($CURRENCY != "") {
                                // $itemPriceFrom = $CurrencyConverter->convert($itemPriceFrom, $base_currency, $CURRENCY);
                                // $Currency = $CURRENCY;
                                // }
                                // }
                                $xExtraCount = 1;
                            }
                            // Specials
                            $offer = $xpath->query('Offer', $hotelElementExtra);
                            if ($offer->length > 0) {
                                $offercode = $offer->item(0)->getAttribute('Code');
                                $offer = $offer->item(0)->nodeValue;
                            } else {
                                $offercode = "";
                                $offer = "";
                            }
                            $itemPrice = $xpath->query('ItemPrice', $hotelElementExtra);
                            $Currency = $itemPrice->item(0)->getAttributeNode('Currency');
                            $base_currency = $Currency->value;
                            $Currency = $Currency->value;
                            $itemPrice = $itemPrice->item(0)->textContent;
                            $itemPriceFromNet = $itemPrice;
                            $confirmation = $xpath->query('Confirmation', $hotelElementExtra);
                            $confirmationCode = $confirmation->item(0)->getAttributeNode('Code');
                            $confirmationCode = $confirmationCode->value;
                            $confirmation = $confirmation->item(0)->textContent;
                            $roomdescription = $xpath->query('Description', $hotelElementExtra);
                            $roomdescription = $roomdescription->item(0)->textContent;
                            $SharingBedding = $xpath->query('SharingBedding', $hotelElementExtra);
                            if ($SharingBedding->length > 0) {
                                $SharingBedding = $SharingBedding->item(0)->textContent;
                            } else {
                                $SharingBedding = "";
                            }
                            if ($SharingBedding == "") {
                                $SharingBedding = 0;
                            } else {
                                if ($SharingBedding == "false") {
                                    $SharingBedding = 0;
                                } else {
                                    $SharingBedding = 1;
                                }
                            }
                            if ($SharingBedding == 1) {
                                $roomdescription = $roomdescription . " - " . $translator->translate("Child sharing existing bedding");
                            }
                            $meal = $xpath->query('Meals/Basis', $hotelElementExtra);
                            $mealcode = $meal->item(0)->getAttributeNode('Code');
                            $mealcode = $mealcode->value;
                            $meal = $meal->item(0)->textContent;
                            $cancellationpolicy = '';
                            $ChargeConditions = $xpath->query('ChargeConditions', $hotelElementExtra);
                            if ($ChargeConditions->length > 0) {
                                $ChargeCondition = $ChargeConditions->item(0)->getElementsByTagName("ChargeCondition");
                                for ($xChargeCondition = 0; $xChargeCondition < $ChargeCondition->length; $xChargeCondition ++) {
                                    if ($cancellationpolicy != "") {
                                        $cancellationpolicy .= "<br/><br/>";
                                    }
                                    $cancellationpolicy .= ucwords($ChargeCondition->item($xChargeCondition)->getAttribute('Type'));
                                    if ($ChargeCondition->item($xChargeCondition)->getAttribute('Allowable') == 'false') {
                                        $cancellationpolicy .= "<br/>" . $translator->translate("Not allowable") . "<br/><br/>";
                                    } else {
                                        $Condition = $ChargeCondition->item($xChargeCondition)->getElementsByTagName("Condition");
                                        for ($xCondition = 0; $xCondition < $Condition->length; $xCondition ++) {
                                            if ($Condition->item($xCondition)->getAttribute('Charge') == 'false') {
                                                $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $from))) . " " . $translator->translate("or earlier") . " " . $translator->translate("you will not be charged");
                                            } else {
                                                if ($Condition->item($xCondition)->getAttribute('FromDay') == 0) {
                                                    if ($Condition->item($xCondition)->getAttribute('ToDay') == "") {
                                                        if ($Condition->item($xCondition)->getAttribute('Allowable') == "false") {
                                                            $cancellationpolicy .= "<br/>" . $translator->translate("Not allowable");
                                                        } else {
                                                            $cancellationpolicy .= "<br/>" . $translator->translate("Effective today you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                        }
                                                    } else {
                                                        $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $from))) . " " . $translator->translate("onwards") . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                    }
                                                } else {
                                                    if ($Condition->item($xCondition)->getAttribute('ToDay') == $Condition->item($xCondition)->getAttribute('FromDay')) {
                                                        $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $from))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                    } else {
                                                        if ($Condition->item($xCondition)->getAttribute('Type') == "cancellation") {
                                                            $FromDay = $Condition->item($xCondition)->getAttribute('FromDay');
                                                            if ($FromDay == "") {
                                                                $FromDay = 0;
                                                            }
                                                            if (! is_numeric($FromDay)) {
                                                                $FromDay = 0;
                                                            }
                                                            $ToDay = $Condition->item($xCondition)->getAttribute('ToDay');
                                                            if ($ToDay == "") {
                                                                $ToDay = 0;
                                                            }
                                                            if (! is_numeric($ToDay)) {
                                                                $ToDay = 0;
                                                            }
                                                            if ($ToDay == 0) {
                                                                if ($cancellationpolicy != "") {
                                                                    $cancellationpolicy .= "<br/>";
                                                                }
                                                                $cancellationpolicy .= $translator->translate("From today to") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $FromDay, date("Y", $from))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                            } else {
                                                                $a = mktime(0, 0, 0, date("m", $from), date("d", $from) - $ToDay, date("Y", $from));
                                                                $b = mktime(0, 0, 0, date("m", $from), date("d", $from) - $FromDay, date("Y", $from));
                                                                if ($a > b) {
                                                                    $cancellationpolicy .= $FromDay . "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $FromDay, date("Y", $from))) . " " . $translator->translate("to") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $from))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                                } else {
                                                                    $cancellationpolicy .= $FromDay . "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $from))) . " " . $translator->translate("to") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $FromDay, date("Y", $from))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                                }
                                                            }
                                                        } else {
                                                            $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $from))) . " or earlier " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $HotelRoomPrices = $xpath->query('HotelRoomPrices', $hotelElementExtra);
                            if ($HotelRoomPrices->length > 0) {
                                $HotelRoom = $HotelRoomPrices->item(0)->getElementsByTagName("HotelRoom");
                                if ($HotelRoom->length > 0) {
                                    $PriceRanges = $HotelRoom->item(0)->getElementsByTagName("PriceRanges");
                                    if ($PriceRanges->length > 0) {
                                        $PriceRanges = $PriceRanges->item(0)->getElementsByTagName("PriceRange");
                                        for ($xPriceRanges = 0; $xPriceRanges < $PriceRanges->length; $xPriceRanges ++) {
                                            $DateRange = $PriceRanges->item($xPriceRanges)->getElementsByTagName("DateRange");
                                            if ($DateRange->length > 0) {
                                                $FromDate = $DateRange->item(0)->getElementsByTagName("FromDate");
                                                if ($FromDate->length > 0) {
                                                    $FromDate = $FromDate->item(0)->nodeValue;
                                                }
                                                $ToDate = $DateRange->item(0)->getElementsByTagName("ToDate");
                                                if ($ToDate->length > 0) {
                                                    $ToDate = $ToDate->item(0)->nodeValue;
                                                }
                                            }
                                            $Price = $PriceRanges->item($xPriceRanges)->getElementsByTagName("Price");
                                            if ($Price->length > 0) {
                                                $Gross = (float) $Price->item(0)->getAttribute("Gross");
                                                if ($gtamarkup > 0) {
                                                    $Gross = $Gross + (($Gross * $gtamarkup) / 100);
                                                }
                                                if ($internalmarkup > 0) {
                                                    $Gross = $Gross + (($Gross * $internalmarkup) / 100);
                                                }
                                                if ($agent_markup > 0) {
                                                    $Gross = $Gross + (($Gross * $agent_markup) / 100);
                                                }
                                                if ($agent_discount > 0) {
                                                    $Gross = $Gross - (($Gross * $agent_discount) / 100);
                                                }
                                                // Fallback Markup
                                                if ($gtamarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                                    $Gross = $Gross + (($Gross * $HotelsMarkupFallback) / 100);
                                                }
                                                if ($scurrency != "" and $base_currency != $scurrency) {
                                                    $Gross = $CurrencyConverter->convert($Gross, $base_currency, $scurrency);
                                                }
                                                $Nights = $Price->item(0)->getAttribute("Nights");
                                                $fMonth = substr($FromDate, 5, 2);
                                                $fDay = substr($FromDate, 8, 2);
                                                $fYear = substr($FromDate, 0, 4);
                                                $tMonth = substr($ToDate, 5, 2);
                                                $tDay = substr($ToDate, 8, 2);
                                                $tYear = substr($ToDate, 0, 4);
                                                $FromDate = mktime(0, 0, 0, $fMonth, $fDay, $fYear);
                                                // $ToDate = mktime(0, 0, 0, $tMonth, $tDay, $tYear);
                                                // $year_diff = date("Y", $ToDate) - date("Y", $FromDate);
                                                // if ($year_diff == "") {
                                                // $year_diff = 0;
                                                // }
                                                // if (! is_numeric($year_diff)) {
                                                // $year_diff = 0;
                                                // }
                                                // $numberofdaysAux = date('z', $ToDate) - date('z', $FromDate) + (((int) date('L', $FromDate)) ? ($year_diff * 366) : ($year_diff * 365));
                                                for ($rZZ = 0; $rZZ < $Nights; $rZZ ++) {
                                                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $FromDate), date("d", $FromDate) + $rZZ, date("y", $FromDate)));
                                                    $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($Gross);
                                                    $pricebreakdown[$pricebreakdownCount]['priceplain'] = $Gross;
                                                    $pricebreakdownCount = $pricebreakdownCount + 1;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (! is_numeric($itemPrice)) {
                                $itemPrice = 0;
                            }
                            $rooms[$baseCounterDetails]['hotelid'] = $shid;
                            $rooms[$baseCounterDetails]['shid'] = $shid;
                            if ($confirmationCode == "IM") {
                                $rooms[$baseCounterDetails]['status'] = 1;
                            } elseif ($confirmationCode == "OR") {
                                $roomdescription .= " - " . $translator->translate("On Request Rate");
                                $rooms[$baseCounterDetails]['status'] = 4;
                            }
                            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-11";
                            $rooms[$baseCounterDetails]['room'] = $roomdescription;
                            $rooms[$baseCounterDetails]['room_description'] = $roomdescription;
                            $rooms[$baseCounterDetails]['countrytag'] = $countrytag;
                            $rooms[$baseCounterDetails]['ccd'] = $city_xml11;
                            $rooms[$baseCounterDetails]['adults'] = $adults;
                            $rooms[$baseCounterDetails]['children'] = $children;
                            $rooms[$baseCounterDetails]['nettotal'] = (double) $itemPrice;
                            if ($gtamarkup != 0) {
                                $itemPrice = $itemPrice + (($itemPrice * $gtamarkup) / 100);
                            }
                            // Geo target markup
                            if ($internalmarkup != 0) {
                                $itemPrice = $itemPrice + (($itemPrice * $internalmarkup) / 100);
                            }
                            // Agent markup
                            if ($agent_markup != 0) {
                                $itemPrice = $itemPrice + (($itemPrice * $agent_markup) / 100);
                            }
                            // Fallback Markup
                            if ($gtamarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                $itemPrice = $itemPrice + (($itemPrice * $HotelsMarkupFallback) / 100);
                            }
                            // Agent discount
                            if ($agent_discount != 0) {
                                $itemPrice = $itemPrice - (($itemPrice * $agent_discount) / 100);
                            }
                            if ($scurrency != "" and $currency != $scurrency) {
                                $itemPrice = $CurrencyConverter->convert($itemPrice, $currency, $scurrency);
                            }
                            $rooms[$baseCounterDetails]['total'] = (double) $itemPrice;
                            $rooms[$baseCounterDetails]['totalplain'] = (double) $itemPrice;
                            if ($mealcode == 'N' or $mealcode == '') {
                                $rooms[$baseCounterDetails]['meal'] = utf8_encode(htmlentities($translator->translate("Room Only"), ENT_QUOTES));
                                $rooms[$baseCounterDetails]['mealcode'] = $mealcode;
                            } else {
                                try {
                                    $sql = "select mapped from board_mapping where description='" . addslashes($meal) . "'";
                                    $statement = $db->createStatement($sql);
                                    $statement->prepare();
                                    $row_board_mapping = $statement->execute();
                                    $row_board_mapping->buffer();
                                    if ($row_board_mapping->valid()) {
                                        $row_board_mapping = $row_board_mapping->current();
                                        $meal = $row_board_mapping["mapped"];
                                    }
                                } catch (\Exception $e) {
                                    $logger = new Logger();
                                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                    $logger->addWriter($writer);
                                    $logger->info($e->getMessage());
                                }
                                $rooms[$baseCounterDetails]['meal'] = $meal;
                                $rooms[$baseCounterDetails]['mealcode'] = $mealcode;
                            }
                            
                            $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                            $rooms[$baseCounterDetails]['scurrency'] = $base_currency;
                            //
                            // Special
                            //
                            if ($offercode != "") {
                                $rooms[$baseCounterDetails]['special'] = true;
                                $rooms[$baseCounterDetails]['specialdescription'] = $offercode;
                            } else {
                                $rooms[$baseCounterDetails]['special'] = false;
                                $rooms[$baseCounterDetails]['specialdescription'] = "";
                            }
                            $rooms[$baseCounterDetails]['cots'] = $numberofcots;
                            $rooms[$baseCounterDetails]['nrextrabeds'] = $nrextrabeds;
                            //
                            // Cancellation policies
                            //
                            if ($EssentialInformation != "") {
                                $cancellationpolicy .= "<br/>" . $EssentialInformation;
                            }
                            $rooms[$baseCounterDetails]['nonrefundable'] = false;
                            $rooms[$baseCounterDetails]['cancelpolicy'] = $cancellationpolicy;
                            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                            $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                            
                            $rooms[$baseCounterDetails]['currency'] = strtoupper($base_currency);
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
    $delete->from('quote_session_gta');
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
    $insert->into('quote_session_gta');
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
error_log("\r\nEOF - GTA\r\n", 3, "/srv/www/htdocs/error_log");
?>