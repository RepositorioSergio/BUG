<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n Symrooms - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);
    
    $raterule = "";
    
    $data = $response['data'];
    $hotelX = $data['hotelX'];
    $search = $hotelX['search'];
    
    // options
    $options = $search['options'];
    for ($i = 0; $i < count($options); $i ++) {
        $id = $options[$i]['id'];
        $supplierCode = $options[$i]['supplierCode'];
        $hotelCode = $options[$i]['hotelCode'];
        //error_log("\r\n hotelCode - $hotelCode\r\n", 3, "/srv/www/htdocs/error_log");
        $shid = $hotelCode;
        $hotelName = $options[$i]['hotelCode'];
        $boardCode = $options[$i]['boardCode'];
        $paymentType = $options[$i]['paymentType'];
        $status = $options[$i]['status'];
        $token = $options[$i]['token'];
        
        // supplements
        $supplements = $options[$i]['supplements'];
        // surcharges
        $surcharges = $options[$i]['surcharges'];
        if (count($surcharges) > 0) {
            for ($j = 0; $j < count($surcharges); $j ++) {
                $chargeType = $surcharges[$j]['chargeType'];
                $scdescription = $surcharges[$j]['description'];
                $price = $roomPrice['price'];
                $sccurrency = $price['currency'];
                $scbinding = $price['binding'];
                $scnet = $price['net'];
                $scgross = $price['gross'];
                $exchange = $price['exchange'];
                $sccurrency = $exchange['currency'];
                $scrate = $exchange['rate'];
            }
        }
        // rateRules
        $rateRules = $options[$i]['rateRules'];
        if (count($rateRules) > 0) {
            for ($j = 0; $j < count($rateRules); $j ++) {
                $raterule = $rateRules[$j];
            }
        }
        
        $price = $options[$i]['price'];
        $net = $price['net'];
        $currency = $price['currency'];
        
        // cancelPolicy
        $cancelArray = array();
        $count2 = 0;
        $cancelPolicy = $options[$i]['cancelPolicy'];
        $CPrefundable = $cancelPolicy['refundable'];
        // cancelPenalties
        $cancelPenalties = $cancelPolicy['cancelPenalties'];
        if (count($cancelPenalties) > 0) {
            for ($c = 0; $c < count($cancelPenalties); $c++) {
                $cancelArray[$count2]['hoursBefore'] = $cancelPenalties[$c]['hoursBefore'];
                $cancelArray[$count2]['penaltyType'] = $cancelPenalties[$c]['penaltyType'];
                $cancelArray[$count2]['currency'] = $cancelPenalties[$c]['currency'];
                $cancelArray[$count2]['value'] = $cancelPenalties[$c]['value'];
                $count2 = $count2 + 1;
            }
        }
        
        // rooms
        $Rooms = $options[$i]['rooms'];
        for ($r = 0; $r < count($Rooms); $r ++) {
            $occupancyRefId = $Rooms[$r]['occupancyRefId'];
            $room_code = $Rooms[$r]['code'];
            $description = $Rooms[$r]['description'];
            $refundable = $Rooms[$r]['refundable'];
            $units = $Rooms[$r]['units'];
            
            $promotions = $Rooms[$r]['promotions'];
            if (count($promotions) > 0) {
                for ($l = 0; $l < count($promotions); $l ++) {
                    $promotionscode = $promotions[$l]['code'];
                    $promotionsname = $promotions[$l]['name'];
                    $promotionseffectiveDate = $promotions[$l]['effectiveDate'];
                    $promotionscodeexpireDate = $promotions[$l]['expireDate'];
                }
            }
            
            // roomPrice
            $roomPrice = $Rooms[$r]['roomPrice'];
            $price = $roomPrice['price'];
            $currency = $price['currency'];
            $binding = $price['binding'];
            $net = $price['net'];
            $gross = $price['gross'];
            $exchange = $price['exchange'];
            $currency = $exchange['currency'];
            $rate = $exchange['rate'];
            
            // beds
            $beds = $Rooms[$r]['beds'];
            for ($k = 0; $k < count($beds); $k ++) {
                $type = $beds[$k]['type'];
                $descriptionbeds = $beds[$k]['description'];
                $count = $beds[$k]['count'];
                $shared = $beds[$k]['shared'];
            }
            
            $ratePlans = $Rooms[$r]['ratePlans'];
            for ($y = 0; $y < count($ratePlans); $y ++) {
                $ratePlanscode = $ratePlans[$y]['code'];
                $name = $ratePlans[$y]['name'];
                $effectiveDate = $ratePlans[$y]['effectiveDate'];
                $expireDate = $ratePlans[$y]['expireDate'];
            }

            $rooms[$baseCounterDetails]['name'] = $hotelName;
            $rooms[$baseCounterDetails]['hotelid'] = $hotelCode;
            $rooms[$baseCounterDetails]['roomid'] = $room_code;
            $rooms[$baseCounterDetails]['code'] = $shid;
            $rooms[$baseCounterDetails]['scode'] = $shid;
            $rooms[$baseCounterDetails]['shid'] = $shid;
            $rooms[$baseCounterDetails]['status'] = 1;
            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-65";
            $rooms[$baseCounterDetails]['room'] = $description;
            $rooms[$baseCounterDetails]['room_description'] = $description;
            $rooms[$baseCounterDetails]['rate_code'] = $rate;
            $rooms[$baseCounterDetails]['room_type'] = $type;
            $rooms[$baseCounterDetails]['ratecategoryid'] = $ratecategoryid;
            $rooms[$baseCounterDetails]['ratePlanscode'] = $ratePlanscode;
            $rooms[$baseCounterDetails]['adults'] = $adults;
            $rooms[$baseCounterDetails]['children'] = $children;
            $rooms[$baseCounterDetails]['total'] = (double) $gross;
            $rooms[$baseCounterDetails]['totalplain'] = (double) $gross;
            $rooms[$baseCounterDetails]['nettotal'] = (double) $net;

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
            $rooms[$baseCounterDetails]['meal'] = $translator->translate($ratePlanscode);
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                $amount = $gross / $noOfNights;
                if ($hotelbedsMarkup != 0) {
                    $amount = $amount + (($amount * $hotelbedsMarkup) / 100);
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
                if ($hotelbedsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
            $rooms[$baseCounterDetails]['scurrency'] = $scurrency;
            if ($promotionsname != "") {
                $rooms[$baseCounterDetails]['special'] = true;
                $rooms[$baseCounterDetails]['specialdescription'] = $promotionsname;
            } else {
                $rooms[$baseCounterDetails]['special'] = false;
                $rooms[$baseCounterDetails]['specialdescription'] = "";
            }

            if ($CPrefundable == true) {
                $cancelation_details = "The Cancellation " . $cancelArray[0]['penaltyType'] . " in " . $cancelArray[0]['value'] . " " . $cancelArray[0]['currency'] . ".<br/>If Cancel " . $cancelArray[1]['hoursBefore'] . " hours before, The Cancellation " . $cancelArray[1]['penaltyType'] . " cost " . $cancelArray[1]['value'] . " " . $cancelArray[1]['currency'] . ".";
                $cancelation_deadline = $cancelArray[0]['hoursBefore'] . " hours";
                $rooms[$baseCounterDetails]['cancelpolicy'] = $cancelation_details;
                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $cancelation_deadline;
                $count2 = 0;
            } else {
                $cancelation_details = "The Cancellation is no Refundable.";
                $cancelation_deadline = "0 hours";
                $rooms[$baseCounterDetails]['cancelpolicy'] = $cancelation_details;
                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $cancelation_deadline;
            }
            $rooms[$baseCounterDetails]['currency'] = strtoupper($currency);
            $baseCounterDetails ++;
        //$agoda = true;
        }
    }
 
    // Store Session
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_symrooms');
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
    $insert->into('quote_session_symrooms');
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