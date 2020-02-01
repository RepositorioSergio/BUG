<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nTotalStay - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $node = $inputDoc->getElementsByTagName("PropertyResult");
    for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
        $TotalProperties = $node->item($rAUX)->getElementsByTagName("TotalProperties");
        if ($TotalProperties->length > 0) {
            $TotalProperties = $TotalProperties->item(0)->nodeValue;
        } else {
            $TotalProperties = "";
        }
        $PropertyID = $node->item($rAUX)->getElementsByTagName('PropertyID');
        if ($PropertyID->length > 0) {
            $PropertyID = $PropertyID->item(0)->nodeValue;
        } else {
            $PropertyID = "";
        }
        $shid = $PropertyID;
        $PropertyReferenceID = $node->item($rAUX)->getElementsByTagName('PropertyReferenceID');
        if ($PropertyReferenceID->length > 0) {
            $PropertyReferenceID = $PropertyReferenceID->item(0)->nodeValue;
        } else {
            $PropertyReferenceID = "";
        }
        $PropertyName = $node->item($rAUX)->getElementsByTagName('PropertyName');
        if ($PropertyName->length > 0) {
            $PropertyName = $PropertyName->item(0)->nodeValue;
        } else {
            $PropertyName = "";
        }
        $Rating = $node->item($rAUX)->getElementsByTagName('Rating');
        if ($Rating->length > 0) {
            $Rating = $Rating->item(0)->nodeValue;
        } else {
            $Rating = "";
        }
        $OurRating = $node->item($rAUX)->getElementsByTagName('OurRating');
        if ($OurRating->length > 0) {
            $OurRating = $OurRating->item(0)->nodeValue;
        } else {
            $OurRating = "";
        }
        $Country = $node->item($rAUX)->getElementsByTagName('Country');
        if ($Country->length > 0) {
            $Country = $Country->item(0)->nodeValue;
        } else {
            $Country = "";
        }
        $Region = $node->item($rAUX)->getElementsByTagName('Region');
        if ($Region->length > 0) {
            $Region = $Region->item(0)->nodeValue;
        } else {
            $Region = "";
        }
        $Resort = $node->item($rAUX)->getElementsByTagName('Resort');
        if ($Resort->length > 0) {
            $Resort = $Resort->item(0)->nodeValue;
        } else {
            $Resort = "";
        }
        $SearchURL2 = $node->item($rAUX)->getElementsByTagName('SearchURL');
        if ($SearchURL2->length > 0) {
            $SearchURL2 = $SearchURL2->item(0)->nodeValue;
        } else {
            $SearchURL2 = "";
        }
        // ROOMS
        $RoomTypes = $node->item($rAUX)->getElementsByTagName('RoomTypes');
        if ($RoomTypes->length > 0) {
            $RoomType = $RoomTypes->item(0)->getElementsByTagName('RoomType');
            if ($RoomType->length > 0) {
                for ($Auxjj = 0; $Auxjj < $RoomType->length; $Auxjj ++) {
                    $Seq = $RoomType->item($Auxjj)->getElementsByTagName('Seq');
                    if ($Seq->length > 0) {
                        $Seq = $Seq->item(0)->nodeValue;
                        $PropertyRoomTypeID = $RoomType->item($Auxjj)->getElementsByTagName('PropertyRoomTypeID');
                        if ($PropertyRoomTypeID->length > 0) {
                            $PropertyRoomTypeID = $PropertyRoomTypeID->item(0)->nodeValue;
                        } else {
                            $PropertyRoomTypeID = "";
                        }
                        $BookingToken = $RoomType->item($Auxjj)->getElementsByTagName('BookingToken');
                        if ($BookingToken->length > 0) {
                            $BookingToken = $BookingToken->item(0)->nodeValue;
                        } else {
                            $BookingToken = "";
                        }
                        $MealBasisID = $RoomType->item($Auxjj)->getElementsByTagName('MealBasisID');
                        if ($MealBasisID->length > 0) {
                            $MealBasisID = $MealBasisID->item(0)->nodeValue;
                        } else {
                            $MealBasisID = "";
                        }
                        $RoomType2 = $RoomType->item($Auxjj)->getElementsByTagName('RoomType');
                        if ($RoomType2->length > 0) {
                            $RoomType2 = $RoomType2->item(0)->nodeValue;
                        } else {
                            $RoomType2 = "";
                        }
                        error_log("\r\nRoomType2 = $RoomType2\r\n", 3, "/srv/www/htdocs/error_log");
                        $RoomView = $RoomType->item($Auxjj)->getElementsByTagName('RoomView');
                        if ($RoomView->length > 0) {
                            $RoomView = $RoomView->item(0)->nodeValue;
                        } else {
                            $RoomView = "";
                        }
                        $MealBasis = $RoomType->item($Auxjj)->getElementsByTagName('MealBasis');
                        if ($MealBasis->length > 0) {
                            $MealBasis = $MealBasis->item(0)->nodeValue;
                        } else {
                            $MealBasis = "";
                        }
                        $SubTotal = $RoomType->item($Auxjj)->getElementsByTagName('SubTotal');
                        if ($SubTotal->length > 0) {
                            $SubTotal = $SubTotal->item(0)->nodeValue;
                        } else {
                            $SubTotal = "";
                        }
                        $Discount = $RoomType->item($Auxjj)->getElementsByTagName('Discount');
                        if ($Discount->length > 0) {
                            $Discount = $Discount->item(0)->nodeValue;
                        } else {
                            $Discount = "";
                        }
                        $OnRequest = $RoomType->item($Auxjj)->getElementsByTagName('OnRequest');
                        if ($OnRequest->length > 0) {
                            $OnRequest = $OnRequest->item(0)->nodeValue;
                        } else {
                            $OnRequest = "";
                        }
                        $Total = $RoomType->item($Auxjj)->getElementsByTagName('Total');
                        if ($Total->length > 0) {
                            $Total = $Total->item(0)->nodeValue;
                        } else {
                            $Total = "";
                        }
                        $Adults = $RoomType->item($Auxjj)->getElementsByTagName('Adults');
                        if ($Adults->length > 0) {
                            $Adults = $Adults->item(0)->nodeValue;
                        } else {
                            $Adults = "";
                        }
                        $Children = $RoomType->item($Auxjj)->getElementsByTagName('Children');
                        if ($Children->length > 0) {
                            $Children = $Children->item(0)->nodeValue;
                        } else {
                            $Children = "";
                        }
                        $Infants = $RoomType->item($Auxjj)->getElementsByTagName('Infants');
                        if ($Infants->length > 0) {
                            $Infants = $Infants->item(0)->nodeValue;
                        } else {
                            $Infants = "";
                        }
                        $Adjustments = $RoomType->item($Auxjj)->getElementsByTagName('Adjustments');
                        if ($Adjustments->length > 0) {
                            $Adjustments = $Adjustments->item(0)->nodeValue;
                        } else {
                            $Adjustments = "";
                        }
                        $Errata = $RoomType->item($Auxjj)->getElementsByTagName('Errata');
                        if ($Errata->length > 0) {
                            $Errata = $Errata->item(0)->nodeValue;
                        } else {
                            $Errata = "";
                        }
                        $OptionalSupplements = $RoomType->item($Auxjj)->getElementsByTagName('OptionalSupplements');
                        if ($OptionalSupplements->length > 0) {
                            $OptionalSupplements = $OptionalSupplements->item(0)->nodeValue;
                        } else {
                            $OptionalSupplements = "";
                        }
                        $rooms[$baseCounterDetails]['name'] = $PropertyName;
                        $rooms[$baseCounterDetails]['hotelid'] = $PropertyID;
                        $rooms[$baseCounterDetails]['shid'] = $totalstayshid;
                        $rooms[$baseCounterDetails]['status'] = 1;
                        $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-44";
                        $rooms[$baseCounterDetails]['room'] = $RoomType2;
                        $rooms[$baseCounterDetails]['roomtype'] = $RoomType2;
                        $rooms[$baseCounterDetails]['adults'] = $Adults;
                        $rooms[$baseCounterDetails]['children'] = $Children;
                        $rooms[$baseCounterDetails]['infants'] = $Infants;
                        $rooms[$baseCounterDetails]['nettotal'] = (double) $SubTotal;
                        if ($totalstayMarkup != 0) {
                            $Total = $Total + (($Total * $totalstayMarkup) / 100);
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
                        if ($totalstayMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                            $sql = "select mapped from board_mapping where description='" . addslashes($MealBasis) . "'";
                            $statement = $db->createStatement($sql);
                            $statement->prepare();
                            $row_board_mapping = $statement->execute();
                            $row_board_mapping->buffer();
                            if ($row_board_mapping->valid()) {
                                $row_board_mapping = $row_board_mapping->current();
                                $MealBasis = $row_board_mapping["mapped"];
                            }
                        } catch (\Exception $e) {
                            $logger = new Logger();
                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                            $logger->addWriter($writer);
                            $logger->info($e->getMessage());
                        }
                        $rooms[$baseCounterDetails]['meal'] = $translator->translate($MealBasis);
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
                        $rooms[$baseCounterDetails]['mealid'] = $MealBasisID;
                        $rooms[$baseCounterDetails]['PropertyRoomTypeID'] = $PropertyRoomTypeID;
                        $rooms[$baseCounterDetails]['BookingToken'] = $BookingToken;
                        $rooms[$baseCounterDetails]['currency'] = strtoupper($currency);
                        // TODO
                        $rooms[$baseCounterDetails]['cancelpolicy'] = "TODO";
                        $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                        $rooms[$baseCounterDetails]['cancelpolicy_details'] = "TODO";
                        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = "TODO";
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
    $delete->from('quote_session_totalstay');
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
    $insert->into('quote_session_totalstay');
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