<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n COMING - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    error_log("\r\n FROM - $from\r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);
    // Results
    $AvailabilityId = $response['AvailabilityId'];
    $Language = $response['Language'];
    $Market = $response['Market'];
    $Customer = $response['Customer'];
    $FromDate = $response['FromDate'];
    $ToDate = $response['ToDate'];
    $Hotels = $response['Hotels'];
    for ($j = 0; $j < count($Hotels); $j ++) {
        $Code = $Hotels[$j]['Code'];
        $shid = $Code;
        $Name = $Hotels[$j]['Name'];
        // $CategoryCode = $Hotels[$j]['CategoryCode'];
        // $CategoryName = $Hotels[$j]['CategoryName'];
        // $Description = $Hotels[$j]['Description'];
        // $Latitude = $Hotels[$j]['Latitude'];
        // $Longitude = $Hotels[$j]['Longitude'];
        // $Address = $Hotels[$j]['Address'];
        $MealPlans = $Hotels[$j]['MealPlans'];
        for ($jAux = 0; $jAux < count($MealPlans); $jAux ++) {
            $CodeMealPlans = $MealPlans[$jAux]['Code'];
            $Options = $MealPlans[$jAux]['Options'];
            for ($jAux2 = 0; $jAux2 < count($Options); $jAux2 ++) {
                $RoomCandidateId = $Options[$jAux2]['RoomCandidateId'];
                $Status = $Options[$jAux2]['Status'];
                $Adults = $Options[$jAux2]['Adults'];
                $Childs = $Options[$jAux2]['Childs'];
                $Enfants = $Options[$jAux2]['Enfants'];
                $Rooms = $Options[$jAux2]['Rooms'];
                for ($jAux3 = 0; $jAux3 < count($Rooms); $jAux3 ++) {
                    $Id = $Rooms[$jAux3]['Id'];
                    $CodeRooms = $Rooms[$jAux3]['Code'];
                    $NameRooms = $Rooms[$jAux3]['Name'];
                    $RateCode = $Rooms[$jAux3]['RateCode'];
                    $RateName = $Rooms[$jAux3]['RateName'];
                    $NonRefundable = $Rooms[$jAux3]['NonRefundable'];
                    $Package = $Rooms[$jAux3]['Package'];
                    $Senior = $Rooms[$jAux3]['Senior'];
                    $Residents = $Rooms[$jAux3]['Residents'];
                    $Remarks = $Rooms[$jAux3]['Remarks'];
                    $Price = $Rooms[$jAux3]['Price'];
                    if (count($Price) > 0) {
                        $CurrencyCode = $Price['CurrencyCode'];
                        $Amount = $Price['Amount'];
                        $Commission = $Price['Commission'];
                        $Binding = $Price['Binding'];
                    } else {
                        $CurrencyCode = "";
                        $Amount = "";
                        $Commission = "";
                        $Binding = "";
                    }
                    $currency = $CurrencyCode;
                    $CancelPolicy = "";
                    $cancelation_deadline = 0;
                    $CancelPenalties = $Rooms[$jAux3]['CancelPenalties'];
                    for ($iAux4 = 0; $iAux4 < count($CancelPenalties); $iAux4 ++) {
                        if ($iAux4 > 0) {
                            $CancelPolicy .= "<br/>";
                        }
                        $HoursBefore = $CancelPenalties[$iAux4]['HoursBefore'];
                        $Description = $CancelPenalties[$iAux4]['Description'];
                        $Penalty = $CancelPenalties[$iAux4]['Penalty'];
                        if (count($Penalty) > 0) {
                            $PenaltyType = $Penalty['PenaltyType'];
                            $CurrencyCode2 = $Penalty['CurrencyCode'];
                            $Value = $Penalty['Value'];
                            $IsNetPrice = $Penalty['IsNetPrice'];
                        } else {
                            $PenaltyType = "";
                            $CurrencyCode2 = "";
                            $Value = "";
                            $IsNetPrice = "";
                        }
                        $offset = ($HoursBefore + 24) / 24;
                        if ($cancelation_deadline == 0) {
                            $cancelation_deadline = mktime(0, 0, 0, date("m", $from), date("d", $from) + $offset, date("y", $from));
                        } else {
                            if ($cancelation_deadline > mktime(0, 0, 0, date("m", $from), date("d", $from) + $offset, date("y", $from))) {
                                $cancelation_deadline = mktime(0, 0, 0, date("m", $from), date("d", $from) + $offset, date("y", $from));
                            }
                        }
                        $CancelPolicy .= $translator->translate("Pay") . " " . $CurrencyCode2 . " " . $Value . " " . $translator->translate("if cancelled on or after") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $offset, date("y", $from)));
                    }
                    if ($Package == "true") {
                        $NameRooms .= " - " . $translator->translate("Package Rate");
                    }
                    if ($Senior == "true") {
                        $NameRooms .= " - " . $translator->translate("Senior Rate");
                    }
                    if ($Residents == "true") {
                        $NameRooms .= " - " . $translator->translate("Residents Rate");
                    }
                    $AmountNet = $Amount;

                    $rooms[$baseCounterDetails]['name'] = $Name;
                    $rooms[$baseCounterDetails]['hotelid'] = $Code;
                    $rooms[$baseCounterDetails]['roomid'] = $Id;
                    $rooms[$baseCounterDetails]['code'] = $Code;
                    $rooms[$baseCounterDetails]['scode'] = $shid;
                    $rooms[$baseCounterDetails]['shid'] = $shid;
                    $rooms[$baseCounterDetails]['status'] = 1;
                    $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-60";
                    $rooms[$baseCounterDetails]['room'] = $NameRooms;
                    $rooms[$baseCounterDetails]['room_code'] = $CodeRooms;
                    $rooms[$baseCounterDetails]['recommended'] = false;
                    $rooms[$baseCounterDetails]['adults'] = $adults;
                    $rooms[$baseCounterDetails]['children'] = $children;
                    $rooms[$baseCounterDetails]['nettotal'] = (double) $AmountNet;
                    if ($coming2Markup != 0) {
                        $Amount = $Amount + (($Amount * $coming2Markup) / 100);
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
                    if ($coming2Markup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                        $sql = "select mapped from board_mapping where description='" . addslashes($boardtype) . "'";
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        $row_board_mapping = $statement->execute();
                        $row_board_mapping->buffer();
                        if ($row_board_mapping->valid()) {
                            $row_board_mapping = $row_board_mapping->current();
                            $boardtype = $row_board_mapping["mapped"];
                        }
                    } catch (\Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $rooms[$baseCounterDetails]['meal'] = $translator->translate($boardtype);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    $amount = $Amount / $noOfNights;
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
                    if ($NonRefundable == "true") {
                        $rooms[$baseCounterDetails]['nonrefundable'] = true;
                        $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking.") . "<br/>" . $CancelPolicy;
                        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $cancelation_deadline;
                    } else {
                        $rooms[$baseCounterDetails]['nonrefundable'] = false;
                        $rooms[$baseCounterDetails]['cancelpolicy'] = $CancelPolicy;
                        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $cancelation_deadline;
                    }

                    /* if ($rooms > 1) {
                        $rooms[$baseCounterDetails]['nodedup'] = true;
                    } else {
                        $rooms[$baseCounterDetails]['nodedup'] = false;
                    } */
                    
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
    $delete->from('quote_session_coming2');
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
    $insert->into('quote_session_coming2');
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