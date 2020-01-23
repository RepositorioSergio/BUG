<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n SNAPTRAVEL - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    $response = json_decode($response, true);
    error_log("\r\n " . print_r($response, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
    $HotelRoomAvailabilityResponse = $response['HotelRoomAvailabilityResponse'];
    
    // error_log("\r\n SIZE = " . $HotelRoomAvailabilityResponse['@size'] . " \r\n", 3, "/srv/www/htdocs/error_log");
    
    if (count($HotelRoomAvailabilityResponse) > 0) {
        $hotelId = $HotelRoomAvailabilityResponse['hotelId'];
        $size = $HotelRoomAvailabilityResponse['@size'];
        $customerSessionId = $HotelRoomAvailabilityResponse['customerSessionId'];
        $HotelRoomResponse = $HotelRoomAvailabilityResponse['HotelRoomResponse'];
        if (count($HotelRoomResponse) > 0) {
            for ($j = 0; $j < count($HotelRoomResponse); $j ++) {
                $RoomImages = $HotelRoomResponse[$j]['RoomImages'];
                $ValueAdds = $HotelRoomResponse[$j]['ValueAdds'];
                $rateCode = $HotelRoomResponse[$j]['rateCode'];
                $rateDescription = $HotelRoomResponse['rateDescription'];
                $rateOccupancyPerRoom = $HotelRoomResponse[$j]['rateOccupancyPerRoom'];
                $roomTypeCode = $HotelRoomResponse[$j]['roomTypeCode'];
                $roomTypeDescription = $HotelRoomResponse[$j]['roomTypeDescription'];
                // BedTypes
                $BedTypes = $HotelRoomResponse[$j]['BedTypes'];
                if (count($BedTypes) > 0) {
                    $size2 = $BedTypes['@size'];
                    $BedType = $BedTypes['BedType'];
                    if (count($BedType) > 0) {
                        for ($i = 0; $i < count($BedType); $i ++) {
                            $id = $BedType[$i]['@id'];
                            $description = $BedType[$i]['description'];
                        }
                    }
                }
                // RateInfos
                $RateInfos = $HotelRoomResponse[$j]['RateInfos'];
                if (count($RateInfos) > 0) {
                    $RateInfo = $RateInfos['RateInfo'];
                    if (count($RateInfo) > 0) {
                        $nonRefundable = $RateInfo['nonRefundable'];
                        $ChargeableRateInfo = $RateInfo['ChargeableRateInfo'];
                        if (count($ChargeableRateInfo) > 0) {
                            $currencyCode2 = $ChargeableRateInfo['@currencyCode'];
                            $total2 = $ChargeableRateInfo['@total'];
                            $currencyCode = $ChargeableRateInfo['currencyCode'];
                            $total = $ChargeableRateInfo['total'];
                        }
                        $RoomGroup = $RateInfo['RoomGroup'];
                        if (count($RoomGroup) > 0) {
                            $Room = $RoomGroup['Room'];
                            if (count($Room) > 0) {
                                $rateKey = $Room['rateKey'];
                            }
                        }
                    }
                }
                
                // $rooms[$baseCounterDetails]['name'] = $Hotel_Name;
                $rooms[$baseCounterDetails]['hotelid'] = $hotelId;
                $rooms[$baseCounterDetails]['roomid'] = $id;
                $rooms[$baseCounterDetails]['code'] = $hotelId;
                $rooms[$baseCounterDetails]['scode'] = $hotelId;
                $rooms[$baseCounterDetails]['shid'] = $hotelId;
                $rooms[$baseCounterDetails]['sessionid'] = $customerSessionId;
                $rooms[$baseCounterDetails]['status'] = 1;
                $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-70";
                $rooms[$baseCounterDetails]['room'] = $description;
                $rooms[$baseCounterDetails]['room_description'] = $description;
                $rooms[$baseCounterDetails]['rate_code'] = $rateCode;
                $rooms[$baseCounterDetails]['rateKey'] = $rateKey;
                $rooms[$baseCounterDetails]['nonrefundable'] = $nonRefundable;
                $rooms[$baseCounterDetails]['adults'] = $adults;
                $rooms[$baseCounterDetails]['children'] = $children;
                $rooms[$baseCounterDetails]['total'] = (double) $total;
                $rooms[$baseCounterDetails]['totalplain'] = (double) $total;
                $rooms[$baseCounterDetails]['nettotal'] = (double) $total;
                try {
                    $sql = "select mapped from board_mapping where description='" . addslashes($board_name) . "'";
                    $statement = $db->createStatement($sql);
                    $statement->prepare();
                    $row_board_mapping = $statement->execute();
                    $row_board_mapping->buffer();
                    if ($row_board_mapping->valid()) {
                        $row_board_mapping = $row_board_mapping->current();
                        $board_name = $row_board_mapping["mapped"];
                    }
                } catch (\Exception $e) {
                    $logger = new Logger();
                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                }
                $rooms[$baseCounterDetails]['meal'] = $translator->translate($roomTypeDescription);
                $pricebreakdown = array();
                $pricebreakdownCount = 0;
                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                    $amount = $total / $noOfNights;
                    if ($snaptravelMarkup != 0) {
                        $amount = $amount + (($amount * $snaptravelMarkup) / 100);
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
                    if ($snaptravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                        $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                    }
                    // Agent discount
                    if ($agent_discount != 0) {
                        $amount = $amount - (($amount * $agent_discount) / 100);
                    }
                    if ($scurrency != "" and $currency != $scurrency) {
                        $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                    }
                    $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                    $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                    $pricebreakdownCount = $pricebreakdownCount + 1;
                }
                $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                $rooms[$baseCounterDetails]['scurrency'] = $currencyCode;
                /*
                 * if ($PromotionName != "") {
                 * $rooms[$baseCounterDetails]['special'] = true;
                 * $rooms[$baseCounterDetails]['specialdescription'] = $PromotionName;
                 * } else {
                 */
                $rooms[$baseCounterDetails]['special'] = false;
                $rooms[$baseCounterDetails]['specialdescription'] = "";
                // }
                
                // $rooms[$baseCounterDetails]['cancelpolicy'] = $policy;
                // $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $policyArray[0]["before"];
                // $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $rooms[$baseCounterDetails]['cancelpolicy_deadline'];
                
                $rooms[$baseCounterDetails]['currency'] = strtoupper($currencyCode);
                $baseCounterDetails ++;
            }
        }
    }
    // Store Session
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_snaptravel');
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
    $insert->into('quote_session_snaptravel');
    $insert->values(array(
        'session_id' => $session_id_tmp,
        'xmlrequest' => (string) $raw,
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
