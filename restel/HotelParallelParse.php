<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nRESTEL - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $error = $inputDoc->getElementsByTagName("error");
    if ($error->length > 0) {
        error_log("\r\n" . trim($error->item(0)->nodeValue) . "\r\n", 3, "/srv/www/htdocs/error_log");
    }
    $node = $inputDoc->getElementsByTagName("hot");
    $nodeLength = $node->length;
    for ($xHotel = 0; $xHotel < $nodeLength; $xHotel ++) {
        $shid = $node->item($xHotel)->getElementsByTagName("cod");
        if ($shid->length > 0) {
            $shid = $shid->item(0)->nodeValue;
            $city_tax = $node->item($xHotel)->getElementsByTagName("city_tax");
            if ($city_tax->length > 0) {
                $city_tax = $city_tax->item(0)->nodeValue;
            } else {
                $city_tax = "";
            }
            $res = $node->item($xHotel)->getElementsByTagName("res");
            if ($res->length > 0) {
                $pax = $res->item(0)->getElementsByTagName("pax");
                $length = $pax->length;
                for ($zRooms = 0; $zRooms < $length; $zRooms ++) {
                    $code = $pax->item(0)->getAttribute("cod");
                    $hab = $pax->item($zRooms)->getElementsByTagName("hab");
                    $hablength = $hab->length;
                    for ($baseCounterDetails = 0; $baseCounterDetails < $hablength; $baseCounterDetails ++) {
                        $cod = $hab->item($baseCounterDetails)->getAttribute("cod");
                        $desc = ucwords(strtolower($hab->item($baseCounterDetails)->getAttribute("desc")), "/+ ");
                        $reg = $hab->item($baseCounterDetails)->getElementsByTagName("reg");
                        $reglength = $reg->length;
                        for ($xReg = 0; $xReg < $reglength; $xReg ++) {
                            $regcod = $reg->item($xReg)->getAttribute("cod");
                            $regprr = $reg->item($xReg)->getAttribute("prr");
                            $regdiv = $reg->item($xReg)->getAttribute("div");
                            $regesr = $reg->item($xReg)->getAttribute("esr");
                            $regnr = $reg->item($xReg)->getAttribute("nr");
                            $slin = array();
                            $lin = $reg->item($xReg)->getElementsByTagName("lin");
                            $linlength = $lin->length;
                            for ($xLin = 0; $xLin < $linlength; $xLin ++) {
                                array_push($slin, $lin->item($xLin)->nodeValue);
                            }
                            $total = $regprr;
                            
                            $rooms[$baseCounterDetails]['hotelid'] = $shid;
                            $rooms[$baseCounterDetails]['code'] = $shid;
                            $rooms[$baseCounterDetails]['scode'] = $shid;
                            $rooms[$baseCounterDetails]['shid'] = $shid;
                            if ($regesr == "OK") {
                                $rooms[$baseCounterDetails]['status'] = 1;
                            } else {
                                $rooms[$baseCounterDetails]['status'] = 0;
                            }
                            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-39";
                            $rooms[$baseCounterDetails]['room'] = $desc;
                            $rooms[$baseCounterDetails]['city_tax'] = $city_tax;
                            $rooms[$baseCounterDetails]['paxhab'] = $code;
                            $rooms[$baseCounterDetails]['hab'] = $cod;
                            $rooms[$baseCounterDetails]['regcod'] = $regcod;
                            $rooms[$baseCounterDetails]['lin'] = serialize($slin);
                            $rooms[$baseCounterDetails]['adults'] = $adults;
                            $rooms[$baseCounterDetails]['children'] = $children;
                            $rooms[$baseCounterDetails]['nettotal'] = (double) $regprr;
                            if ($RestelHotUSAMarkup != 0) {
                                $total = $total + (($total * $RestelHotUSAMarkup) / 100);
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
                            if ($RestelHotUSAMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                                $sql = "select mapped from board_mapping where description='" . $regcod . "'";
                                $statement = $db->createStatement($sql);
                                $statement->prepare();
                                $row_board_mapping = $statement->execute();
                                $row_board_mapping->buffer();
                                if ($row_board_mapping->valid()) {
                                    $row_board_mapping = $row_board_mapping->current();
                                    $regcod = $row_board_mapping["mapped"];
                                }
                            } catch (\Exception $e) {
                                $logger = new Logger();
                                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                $logger->addWriter($writer);
                                $logger->info($e->getMessage());
                            }
                            $rooms[$baseCounterDetails]['meal'] = $translator->translate($regcod);
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
                            if ($regnr == 1) {
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
                            $rooms[$baseCounterDetails]['currency'] = strtoupper($currency);
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
    $delete->from('quote_session_restel');
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
    $insert->into('quote_session_restel');
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