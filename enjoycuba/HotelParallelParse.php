<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n ENJOYCUBA - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $array = json_decode($response, true);
    foreach ($array as $key => $value) {
        $shid = $value['id_hotel'];
        $roomCombinations = $value['rooms_prices']['roomCombinations'];
        $cancellation_policy = $value['cancellation_policy'];
        $room_currency = $value['rooms_prices']['currency'];
        foreach ($roomCombinations as $keyRoom => $valueRoom) {
            $mealPlans = $valueRoom['roomType']['mealPlans'];
            foreach ($mealPlans as $keyRoomMealPlan => $valueRoomMealPlan) {
                $priceCombinations = $valueRoomMealPlan['priceCombinations'];
                foreach ($priceCombinations as $keyRoomMealPlanpriceCombinations => $valueRoomMealPlanpriceCombinations) {
                    $nettotal = $valueRoomMealPlanpriceCombinations['price'];
                    $total = $nettotal;

                    $room = ucwords(strtolower($valueRoom['roomType']['roomtypename']), "|( /-,");
                    $pk_roomtype = $valueRoom['roomType']['pk_roomtype'];
                    $pk_combination = $valueRoomMealPlanpriceCombinations['pk_combination'];
                    $pk_combination = explode(",", $pk_combination);
                    $adt = $pk_combination[0];
                    $chd = $pk_combination[1] + $pk_combination[2];
                    $room .= " - " . $valueRoomMealPlanpriceCombinations['description'];

                    $rooms[$baseCounterDetails]['hotelid'] = $shid;
                    $rooms[$baseCounterDetails]['code'] = $shid;
                    $rooms[$baseCounterDetails]['scode'] = $shid;
                    $rooms[$baseCounterDetails]['shid'] = $shid;
                    $rooms[$baseCounterDetails]['status'] = 1;
                    $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-3";
                    $rooms[$baseCounterDetails]['room'] = $room;
                    $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                    $rooms[$baseCounterDetails]['boardid'] = $valueRoomMealPlan['pk_mealplan'];
                    $rooms[$baseCounterDetails]['pk_roomtype'] = $pk_roomtype;
                    $rooms[$baseCounterDetails]['pk_combination'] = $pk_combination;
                    $rooms[$baseCounterDetails]['adults'] = $adults;
                    $rooms[$baseCounterDetails]['children'] = $children;
                    $rooms[$baseCounterDetails]['nettotal'] = (double) $nettotal;
                    if ($enjoycubaMarkup != 0) {
                        $total = $total + (($total * $enjoycubaMarkup) / 100);
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
                    if ($enjoycubaMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                    $t = $valueRoomMealPlan["mealplanname"];
                    try {
                        $sql = "select mapped from board_mapping where description='" . addslashes($t) . "'";
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        $row_board_mapping = $statement->execute();
                        $row_board_mapping->buffer();
                        if ($row_board_mapping->valid()) {
                            $row_board_mapping = $row_board_mapping->current();
                            $t = $row_board_mapping["mapped"];
                        }
                    } catch (\Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $rooms[$baseCounterDetails]['meal'] = $translator->translate($t);
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
                    $rooms[$baseCounterDetails]['cancellation_policy'] = $cancellation_policy;
                    $rooms[$baseCounterDetails]['cancelpolicy'] = "";
                    $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                    $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;

                    $rooms[$baseCounterDetails]['currency'] = strtoupper($room_currency);
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
    $delete->from('quote_session_enjoycuba');
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
    $insert->into('quote_session_enjoycuba');
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