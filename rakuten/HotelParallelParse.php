<?php
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
error_log("\r\nRATUKEN - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);
    // $session_id = $response['session_id'];
    $event_id = $response['event_id'];
    $status = $response['status'];
    $search = $response['search'];
    $check_in_date = $search['check_in_date'];
    $check_out_date = $search['check_out_date'];
    $source_market = $search['source_market'];
    $room_count = $search['room_count'];
    $adult_count = $search['adult_count'];
    $currency = $search['currency'];
    $locale = $search['locale'];
    $children_array = $search['children'];
    $hotelsb = $response['hotels'];
    if (count($hotelsb) > 0) {
        for ($j = 0; $j < count($hotelsb); $j ++) {
            $id = $hotelsb[$j]['id'];
            $rates = $hotelsb[$j]['rates'];
            $packages = $rates['packages'];
            if (count($packages) > 0) {
                for ($jAux = 0; $jAux < count($packages); $jAux ++) {
                    $shid = $packages[$jAux]['hotel_id'];
                    // error_log("\r\nshid: $shid \r\n", 3, "/srv/www/htdocs/error_log");
                    $sfilter[] = " sid='$shid' ";
                    $booking_key = $packages[$jAux]['booking_key'];
                    $room_rate = $packages[$jAux]['room_rate'];
                    $room_rate_currency = $packages[$jAux]['room_rate_currency'];
                    $client_commission = $packages[$j]['client_commission'];
                    $client_commission_currency = $packages[$jAux]['client_commission_currency'];
                    $chargeable_rate = $packages[$jAux]['chargeable_rate'];
                    $chargeable_rate_currency = $packages[$jAux]['chargeable_rate_currency'];
                    $rate_type = $packages[$jAux]['rate_type'];
                    $room_details = $packages[$jAux]['room_details'];
                    $room_code = $room_details['room_code'];
                    $rate_plan_code = $room_details['rate_plan_code'];
                    $rate_plan_description = $room_details['rate_plan_description'];
                    $description = $room_details['description'];
                    $food = $room_details['food'];
                    $non_refundable = $room_details['non_refundable'];
                    $room_type = $room_details['room_type'];
                    $room_view = $room_details['room_view'];
                    $supplier_description = $room_details['supplier_description'];
                    $non_smoking = $room_details['non_smoking'];
                    $room_gender = $room_details['room_gender'];
                    $benefits = $room_details['benefits'];
                    $floor = $room_details['floor'];
                    $amenitites = $room_details['amenitites'];
                    $beds = $room_details['beds'];
                    $queen = $beds['queen'];
                    $total = $chargeable_rate;

                    // $rooms[$baseCounterDetails]['name'] = $Name;
                    $rooms[$baseCounterDetails]['hotelid'] = $shid;
                    $rooms[$baseCounterDetails]['roomid'] = $booking_key;
                    $rooms[$baseCounterDetails]['shid'] = $shid;
                    $rooms[$baseCounterDetails]['status'] = 1;
                    $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-74";
                    $rooms[$baseCounterDetails]['room'] = $description;
                    $rooms[$baseCounterDetails]['roomtype'] = $room_type;
                    $rooms[$baseCounterDetails]['room_description'] = $description;
                    $rooms[$baseCounterDetails]['check_in_date'] = $check_in_date;
                    $rooms[$baseCounterDetails]['check_out_date'] = $check_out_date;
                    $rooms[$baseCounterDetails]['room_count'] = $room_count;
                    $rooms[$baseCounterDetails]['currency'] = $currency;
                    $rooms[$baseCounterDetails]['locale'] = $locale;
                    $rooms[$baseCounterDetails]['childrenb'] = $children_array;
                    $rooms[$baseCounterDetails]['rate_type'] = $rate_type;
                    $rooms[$baseCounterDetails]['source_market'] = $source_market;
                    $rooms[$baseCounterDetails]['adults'] = $adults;
                    $rooms[$baseCounterDetails]['children'] = $children;
                    $rooms[$baseCounterDetails]['nettotal'] = (double) $room_rate;
                    if ($rakutenMarkup != 0) {
                        $total = $total + (($total * $rakutenMarkup) / 100);
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
                    if ($rakutenMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                        $sql = "select mapped from board_mapping where description='" . addslashes($room_type) . "'";
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        $row_board_mapping = $statement->execute();
                        $row_board_mapping->buffer();
                        if ($row_board_mapping->valid()) {
                            $row_board_mapping = $row_board_mapping->current();
                            $room_type = $row_board_mapping["mapped"];
                        }
                    } catch (\Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $rooms[$baseCounterDetails]['meal'] = $translator->translate($room_type);
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
                    $rooms[$baseCounterDetails]['scurrency'] = $scurrency;
                    //
                    // Special
                    //
                    $rooms[$baseCounterDetails]['special'] = false;
                    $rooms[$baseCounterDetails]['specialdescription'] = "";
                    //
                    // Cancellation policies
                    //
                    $procurar = "Non-Refundable";
                    if (strpos($PromotionName, $procurar) !== false) {
                        $rooms[$baseCounterDetails]['nonrefundable'] = true;
                        $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking.");
                        $rooms[$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking.");
                        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                        $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
                    } else {
                        $rooms[$baseCounterDetails]['nonrefundable'] = false;
                        $rooms[$baseCounterDetails]['cancelpolicy'] = "";
                        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                        $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                    }
                    $rooms[$baseCounterDetails]['currency'] = strtoupper($scurrency);
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
    $delete->from('quote_session_rakuten');
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
    $insert->into('quote_session_rakuten');
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