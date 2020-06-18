<?php
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
error_log("\r\n ITERPEC - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);
    $TimeSpan = $response['TimeSpan'];
    $Token = $response['Token'];
    $TotalTime = $response['TotalTime'];

    $Hotels = $response['Hotels'];
    for ($i=0; $i < count($Hotels); $i++) { 
        $HotelId = $Hotels[$i]['HotelId'];
        $shid = $HotelId;
        $sfilter[] = " sid='$HotelId' ";
        $Name = $Hotels[$i]['Name'];
        $Longitude = $Hotels[$i]['Longitude'];
        $Latitude = $Hotels[$i]['Latitude'];
        $Category = $Hotels[$i]['Category'];
        $Address = $Hotels[$i]['Address'];
        $CustomFields = $Hotels[$i]['CustomFields'];
        for ($iAux=0; $iAux < count($CustomFields); $iAux++) { 
            $CustomFieldsName = $CustomFields[$iAux]['Name'];
            $CustomFieldsValue = $CustomFields[$iAux]['Value'];
        }
        $Rooms = $Hotels[$i]['Rooms'];
        for ($j=0; $j < count($Rooms); $j++) { 
            $RoomId = $Rooms[$j]['Id'];
            $BoardDescription = $Rooms[$j]['BoardDescription'];
            $CustomFields = $Rooms[$j]['CustomFields'];
            $HasBreakfast = $Rooms[$j]['HasBreakfast'];
            $IsAvailable = $Rooms[$j]['IsAvailable'];
            $IsNonRefundable = $Rooms[$j]['IsNonRefundable'];
            $IsPrePayment = $Rooms[$j]['IsPrePayment'];
            $MediaRoomId = $Rooms[$j]['MediaRoomId'];
            $NumAdults = $Rooms[$j]['NumAdults'];
            $PayDirectToHotel = $Rooms[$j]['PayDirectToHotel'];
            $Quantity = $Rooms[$j]['Quantity'];
            $RoomDescription = $Rooms[$j]['RoomDescription'];
            $SellingPricePerRoom = $Rooms[$j]['SellingPricePerRoom'];
            $SellingPriceCurrency = $SellingPricePerRoom['Currency'];
            $SellingPriceValue = $SellingPricePerRoom['Value'];
            $TotalSellingPrice = $Rooms[$j]['TotalSellingPrice'];
            $Currency = $TotalSellingPrice['Currency'];
            $Value = $TotalSellingPrice['Value'];
            $ThumbUrl = $Rooms[$j]['ThumbUrl'];
            $CancellationPolicies = $Rooms[$j]['CancellationPolicies'];
            for ($k=0; $k < count($CancellationPolicies); $k++) { 
                $EndDate = $CancellationPolicies[$k]['EndDate'];
                $StartDate = $CancellationPolicies[$k]['StartDate'];
                $CancellationPoliciesValue = $CancellationPolicies[$k]['Value'];
                $ValueCurrency = $CancellationPoliciesValue['Currency'];
                $Value2 = $CancellationPoliciesValue['Value'];
            }
            $total = $Value;
            $nettotal = $Value;
            $rooms[$baseCounterDetails]['name'] = $Name;
            $rooms[$baseCounterDetails]['hotelid'] = $HotelId;
            $rooms[$baseCounterDetails]['roomid'] = $RoomId;
            $rooms[$baseCounterDetails]['shid'] = $HotelId;
            $rooms[$baseCounterDetails]['status'] = 1;
            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-48";
            $rooms[$baseCounterDetails]['room'] = $RoomDescription;
            $rooms[$baseCounterDetails]['roomtype'] = $RoomTypeCode;
            $rooms[$baseCounterDetails]['room_description'] = $RoomDescription;
            $rooms[$baseCounterDetails]['token'] = $Token;
            $rooms[$baseCounterDetails]['adults'] = $adults;
            $rooms[$baseCounterDetails]['children'] = $children;
            $rooms[$baseCounterDetails]['nettotal'] = (double) $nettotal;
            if ($rtsMarkup != 0) {
                $total = $total + (($total * $rtsMarkup) / 100);
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
            if ($rtsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
            $rooms[$baseCounterDetails]['scurrency'] = $Currency;
            //
            // Special
            //
            $rooms[$baseCounterDetails]['special'] = false;
            $rooms[$baseCounterDetails]['specialdescription'] = "";
            //
            // Cancellation policies
            //
            $from_date = date('Y-m-d',strtotime($StartDate));
            $to_date = date('Y-m-d',strtotime($EndDate));
            $cancelpolicy = "If you cancel booking " . $from_date . " To date " . $to_date . " cost " . $Value2. "" . $ValueCurrency;
            if ($IsNonRefundable !== "false") {
                $rooms[$baseCounterDetails]['nonrefundable'] = true;
                $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate($cancelpolicy);
                $rooms[$baseCounterDetails]['cancelpolicy_details'] = $translator->translate($cancelpolicy);
                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $to_date);
                $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $to_date;
            } else {
                $rooms[$baseCounterDetails]['nonrefundable'] = false;
                $rooms[$baseCounterDetails]['cancelpolicy'] = $cancelpolicy;
                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $to_date);
                $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $to_date;
            }
            $rooms[$baseCounterDetails]['currency'] = strtoupper($Currency);
            $baseCounterDetails ++;
        }
    }
    //
    // Store Session
    //
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_iterpec');
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
    $insert->into('quote_session_iterpec');
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
error_log("\r\n EOF - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
?>