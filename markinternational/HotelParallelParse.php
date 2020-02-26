<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nMark International - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    $response = str_replace("&lt;", "<", $response);
    $response = str_replace("&gt;", ">", $response);
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    if ($inputDoc != NULL) {
        $node = $inputDoc->getElementsByTagName("string");
        if ($node->length > 0) {
            $RequestInfo = $node->item(0)->getElementsByTagName("VAXXML");
            if ($RequestInfo->length > 0) {
                $Header = $RequestInfo->item(0)->getElementsByTagName("Header");
                if ($Header->length > 0) {
                    $SessionId = $Header->item(0)->getAttribute("SessionId");
                }
                // REQSPONSE
                $Response = $RequestInfo->item(0)->getElementsByTagName("Response");
                if ($Response->length > 0) {
                    // TRAVAILAVAL
                    $PassengerTypeQuantityArray = array();
                    $TravelerAvail = $Response->item(0)->getElementsByTagName("TravelerAvail");
                    if ($TravelerAvail->length > 0) {
                        $PassengerTypeQuantity = $TravelerAvail->item(0)->getElementsByTagName("PassengerTypeQuantity");
                        if ($PassengerTypeQuantity->length > 0) {
                            for ($z = 0; $z < $PassengerTypeQuantity->length; $z ++) {
                                $Age = $PassengerTypeQuantity->item($z)->getAttribute("Age");
                                if ($Age != "") {
                                    $PassengerTypeQuantityArray[$Age] = $PassengerTypeQuantity->item($x)->nodeValue;
                                }
                                $Type = $PassengerTypeQuantity->item($z)->getAttribute("Type");
                                if ($Key != "") {
                                    $PassengerTypeQuantityArray[$Type] = $PassengerTypeQuantity->item($x)->nodeValue;
                                }
                                $Seq = $PassengerTypeQuantity->item($z)->getAttribute("Seq");
                                if ($Seq != "") {
                                    $PassengerTypeQuantityArray[$Seq] = $PassengerTypeQuantity->item($x)->nodeValue;
                                }
                            }
                        }
                    }
                    // TravelerAvailSets
                    $TravelerAvailSetArray = array();
                    $TravelerAvailSets = $Response->item(0)->getElementsByTagName("TravelerAvailSets");
                    if ($TravelerAvailSets->length > 0) {
                        $TravelerAvailSet = $TravelerAvailSets->item(0)->getElementsByTagName("TravelerAvailSet");
                        if ($TravelerAvailSet->length > 0) {
                            for ($z = 0; $z < $TravelerAvailSet->length; $z ++) {
                                $Seq = $TravelerAvailSet->item($z)->getAttribute("Seq");
                                if ($Seq != "") {
                                    $TravelerAvailSetArray[$Seq] = $TravelerAvailSet->item($x)->nodeValue;
                                }
                                $PassengerTypeQuantityRef = $TravelerAvailSet->item($z)->getElementsByTagName("PassengerTypeQuantityRef");
                                for ($y = 0; $y < $PassengerTypeQuantityRef->length; $y ++) {
                                    $Seq = $PassengerTypeQuantityRef->item($y)->getAttribute("Seq");
                                    if ($Seq != "") {
                                        $TravelerAvailSetArray[$Seq] = $PassengerTypeQuantityRef->item($y)->nodeValue;
                                    }
                                    $Lead = $PassengerTypeQuantityRef->item($y)->getAttribute("Lead");
                                    if ($Lead != "") {
                                        $TravelerAvailSetArray[$Lead] = $PassengerTypeQuantityRef->item($y)->nodeValue;
                                    }
                                }
                            }
                        }
                    }
                    //
                    // CityLookUp
                    //
                    $CityLookUp = $Response->item(0)->getElementsByTagName("CityLookUp");
                    $City = $CityLookUp->item(0)->getElementsByTagName("City");
                    $State = $City->item(0)->getAttribute("State");
                    $Name = $City->item(0)->getAttribute("Name");
                    $LocationCode = $City->item(0)->getAttribute("LocationCode");
                    // DESCRIPTIONS
                    $Descriptions = array();
                    $DescriptionsTag = $Response->item(0)->getElementsByTagName("Descriptions");
                    if ($DescriptionsTag->length > 0) {
                        if ($DescriptionsTag->length > 0) {
                            $Description = $DescriptionsTag->item(0)->getElementsByTagName("Description");
                            for ($x = 0; $x < $Description->length; $x ++) {
                                $Key = $Description->item($x)->getAttribute("Key");
                                if ($Key != "") {
                                    $Descriptions[$Key] = $Description->item($x)->nodeValue;
                                }
                                $text = $Description->item($x)->getAttribute("text");
                                if ($text != "") {
                                    $Descriptions[$text] = $Description->item($x)->nodeValue;
                                }
                            }
                        }
                    }
                    //
                    // Availability
                    //
                    $Availability = $Response->item(0)->getElementsByTagName("Availability");
                    if ($Availability->length > 0) {
                        $Results = $Availability->item(0)->getElementsByTagName("Results");
                        if ($Results->length > 0) {
                            $Seq = $Results->item(0)->getAttribute("Seq");
                            $Air = $Results->item(0)->getAttribute("Air");
                            $Hotel = $Results->item(0)->getAttribute("Hotel");
                            $Feature = $Results->item(0)->getAttribute("Feature");
                            $Car = $Results->item(0)->getAttribute("Car");
                            $LowDate = $Results->item(0)->getAttribute("LowDate");
                            $HighDate = $Results->item(0)->getAttribute("HighDate");
                            $HotelAvailRS = $Results->item(0)->getElementsByTagName("HotelAvailRS");
                            $Hotel = $HotelAvailRS->item(0)->getElementsByTagName("Hotel");
                            // if ($Hotel->length > 0) {
                            for ($x = 0; $x < $Hotel->length; $x ++) {
                                $HotelCode = $Hotel->item($x)->getAttribute("HotelCode");
                                $Name = $Hotel->item($x)->getAttribute("Name");
                                $shid = $HotelCode;
                                $FareType = $Hotel->item($x)->getAttribute("FareType");
                                $ItemId = $Hotel->item($x)->getAttribute("ItemId");
                                error_log("\r\n$HotelCode - $Name\r\n", 3, "/srv/www/htdocs/error_log");
                                $ChainCode = $Hotel->item($x)->getAttribute("ChainCode");
                                $Priority = $Hotel->item($x)->getAttribute("Priority");
                                $POIProximity = $Hotel->item($x)->getElementsByTagName("POIProximity");
                                if ($POIProximity->length > 0) {
                                    $Distance = $POIProximity->item(0)->getAttribute("Distance");
                                    $Direction = $POIProximity->item(0)->getAttribute("Direction");
                                    $Units = $POIProximity->item(0)->getAttribute("Units");
                                    $POICode = $POIProximity->item(0)->getAttribute("POICode");
                                    $PointOfInterest = $POIProximity->item(0)->getAttribute("PointOfInterest");
                                } else {
                                    $Distance = "";
                                    $Direction = "";
                                    $Units = "";
                                    $POICode = "";
                                    $PointOfInterest = "";
                                }
                                $Rating = $Hotel->item($x)->getElementsByTagName("Rating");
                                if ($Rating->length > 0) {
                                    $Preferred = $Rating->item(0)->getAttribute("Preferred");
                                    $Value = $Rating->item(0)->getAttribute("Value");
                                } else {
                                    $Preferred = "";
                                    $Value = "";
                                }
                                $Address = $Hotel->item($x)->getElementsByTagName("Address");
                                if ($Address->length > 0) {
                                    $AddressLine1 = $Address->item(0)->getAttribute("AddressLine1");
                                    $AddressLine2 = $Address->item(0)->getAttribute("AddressLine2");
                                    $City = $Address->item(0)->getAttribute("City");
                                    $State = $Address->item(0)->getAttribute("State");
                                    $PostalCode = $Address->item(0)->getAttribute("PostalCode");
                                    $CountryCode = $Address->item(0)->getAttribute("CountryCode");
                                    $Description = $Address->item(0)->getAttribute("Description");
                                } else {
                                    $AddressLine1 = "";
                                    $AddressLine2 = "";
                                    $City = "";
                                    $State = "";
                                    $PostalCode = "";
                                    $CountryCode = "";
                                    $Description = "";
                                }
                                $Telephone = $Hotel->item($x)->getElementsByTagName("Telephone");
                                if ($Telephone->length > 0) {
                                    $Type = $Telephone->item(0)->getAttribute("Type");
                                    $Telephone = $Telephone->item(0)->nodeValue;
                                } else {
                                    $Type = "";
                                    $Telephone = "";
                                }
                                
                                $HotelRatePlan = $Hotel->item($x)->getElementsByTagName("HotelRatePlan");
                                $baseCounterDetails = 0;
                                if ($HotelRatePlan->length > 0) {
                                    for ($xy = 0; $xy < $HotelRatePlan->length; $xy ++) {
                                        $SpecialName = "";
                                        $TravelerAvailSet = $HotelRatePlan->item($xy)->getAttribute("TravelerAvailSet");
                                        $RatePlanId = $HotelRatePlan->item($xy)->getAttribute("RatePlanId");
                                        $RoomCode = $HotelRatePlan->item($xy)->getAttribute("RoomCode");
                                        $PlanCode = $HotelRatePlan->item($xy)->getAttribute("PlanCode");
                                        $PlanDescription = $HotelRatePlan->item($xy)->getAttribute("PlanDescription");
                                        $RoomDescription = $HotelRatePlan->item($xy)->getAttribute("RoomDescription");
                                        $SalesDescriptionTmp = $HotelRatePlan->item($xy)->getElementsByTagName("SalesDescription");
                                        if ($SalesDescriptionTmp->length > 0) {
                                            for ($xY = 0; $xY < $SalesDescriptionTmp->length; $xY ++) {
                                                if ($SalesDescriptionTmp->item($xY)->nodeValue != "") {
                                                    if ($SpecialName != "") {
                                                        $SpecialName = $SpecialName . "<br/>";
                                                    }
                                                    $SpecialName = $SpecialName . $Descriptions[$SalesDescriptionTmp->item($xY)->nodeValue];
                                                }
                                            }
                                        }
                                        $PricingInfo = $HotelRatePlan->item($xy)->getElementsByTagName("PricingInfo");
                                        if ($PricingInfo->length > 0) {
                                            $Base = $PricingInfo->item(0)->getAttribute("Base");
                                            $Taxes = $PricingInfo->item(0)->getAttribute("Taxes");
                                            $Fees = $PricingInfo->item(0)->getAttribute("Fees");
                                            $Markups = $PricingInfo->item(0)->getAttribute("Markups");
                                            $Total = $PricingInfo->item(0)->getAttribute("Total");
                                            // error_log("\r\n Total $Total \r\n", 3, "/srv/www/htdocs/error_log");
                                            $Currency = $PricingInfo->item(0)->getAttribute("Currency");
                                            $Currency_Base = $Currency;
                                            $Price = $PricingInfo->item(0)->getElementsByTagName("Price");
                                            if ($Price->length > 0) {
                                                for ($pr = 0; $pr < $Price->length; $pr ++) {
                                                    
                                                    $Price_Type = $Price->item($pr)->getAttribute("Type");
                                                    $Price_LowAge = $Price->item($pr)->getAttribute("LowAge");
                                                    $Price_HighAge = $Price->item($pr)->getAttribute("HighAge");
                                                    $Price_Base = $Price->item($pr)->getAttribute("Base");
                                                    $Price_Taxes = $Price->item($pr)->getAttribute("Taxes");
                                                    $Price_Fees = $Price->item($pr)->getAttribute("Fees");
                                                    $Price_Markups = $Price->item($pr)->getAttribute("Markups");
                                                    $Price_Total = $Price->item($pr)->getAttribute("Total");
                                                    // error_log("\r\n Price_Total $Price_Total \r\n", 3, "/srv/www/htdocs/error_log");
                                                    $Price_QuantityMinimum = $Price->item($pr)->getAttribute("QuantityMinimum");
                                                    $Price_QuantityMaximum = $Price->item($pr)->getAttribute("QuantityMaximum");
                                                    
                                                    $rooms[$baseCounterDetails]['name'] = $Name;
                                                    $rooms[$baseCounterDetails]['hotelid'] = $HotelCode;
                                                    $rooms[$baseCounterDetails]['shid'] = $shid;
                                                    $rooms[$baseCounterDetails]['status'] = 1;
                                                    $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-17";
                                                    $rooms[$baseCounterDetails]['room'] = $RoomDescription;
                                                    $rooms[$baseCounterDetails]['roomtypecode'] = $RoomCode;
                                                    $rooms[$baseCounterDetails]['room_description'] = $RoomDescription;
                                                    $rooms[$baseCounterDetails]['RateCode'] = $RatePlanId;
                                                    $rooms[$baseCounterDetails]['city_xml17'] = $city_xml17;
                                                    $rooms[$baseCounterDetails]['adults'] = $adults;
                                                    $rooms[$baseCounterDetails]['children'] = $children;
                                                    $rooms[$baseCounterDetails]['nettotal'] = (double) $Base;
                                                    if ($MarkInternationalMarkup != 0) {
                                                        $Total = $Total + (($Total * $MarkInternationalMarkup) / 100);
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
                                                    if ($MarkInternationalMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                                                    try {
                                                        $sql = "select mapped from board_mapping where description='" . addslashes($RoomCode) . "'";
                                                        $statement = $db->createStatement($sql);
                                                        $statement->prepare();
                                                        $row_board_mapping = $statement->execute();
                                                        $row_board_mapping->buffer();
                                                        if ($row_board_mapping->valid()) {
                                                            $row_board_mapping = $row_board_mapping->current();
                                                            $RoomCode = $row_board_mapping["mapped"];
                                                        }
                                                    } catch (\Exception $e) {
                                                        $logger = new Logger();
                                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                                        $logger->addWriter($writer);
                                                        $logger->info($e->getMessage());
                                                    }
                                                    $rooms[$baseCounterDetails]['meal'] = $translator->translate($RoomCode);
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
                                                    $rooms[$baseCounterDetails]['scurrency'] = $currency;
                                                    //
                                                    // Special
                                                    //
                                                    $rooms[$baseCounterDetails]['special'] = false;
                                                    $rooms[$baseCounterDetails]['specialdescription'] = "";
                                                    //
                                                    // Cancellation policies
                                                    //
                                                    $rooms[$baseCounterDetails]['nonrefundable'] = false;
                                                    $rooms[$baseCounterDetails]['cancelpolicy'] = "";
                                                    $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                                                    $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                                                    
                                                    $rooms[$baseCounterDetails]['currency'] = strtoupper($currency);
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
    $delete->from('quote_session_markinternational');
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
    $insert->into('quote_session_markinternational');
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
error_log("\r\nMark International - Hotel Parallel Search - Parse - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>