<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n AIC - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\n Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $node = $inputDoc->getElementsByTagName("response");
    for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
        $search = $node->item($rAUX)->getElementsByTagName("search");
        $numbersearch = $search->item(0)->getAttribute('number');
        $nights = $node->item($rAUX)->getElementsByTagName('nights');
        $numbernights = $nights->item(0)->getAttribute('number');
        $checkin = $node->item($rAUX)->getElementsByTagName('checkin');
        $datecheckin = $checkin->item(0)->getAttribute('date');
        $checkout = $node->item($rAUX)->getElementsByTagName('checkout');
        $datecheckout = $checkout->item(0)->getAttribute('date');
        if ($checkout->length > 0) {
            $checkout = $checkout->item(0)->nodeValue;
        } else {
            $checkout = "";
        }
        $hotelsVector = $node->item($rAUX)->getElementsByTagName('hotels');
        if ($hotelsVector->length > 0) {
            $hotel = $hotelsVector->item(0)->getElementsByTagName('hotel');
            if ($hotel->length > 0) {
                for ($i = 0; $i < $hotel->length; $i ++) {
                    $code = $hotel->item($i)->getAttribute('code');
                    $shid = $code;
                    $name = $hotel->item($i)->getAttribute('name');
                    $stars = $hotel->item($i)->getAttribute('stars');
                    $location = $hotel->item($i)->getAttribute('location');
                    $agreement = $hotel->item($i)->getElementsByTagName('agreement');
                    if ($agreement->length > 0) {
                        for ($iAux=0; $iAux < $agreement->length; $iAux++) { 
                            $remarkcode = "";
                            $id = $agreement->item($iAux)->getAttribute('id');
                            $available = $agreement->item($iAux)->getAttribute('available');
                            $c_type = $agreement->item($iAux)->getAttribute('c_type');
                            $room_basis = $agreement->item($iAux)->getAttribute('room_basis');
                            $meal_basis = $agreement->item($iAux)->getAttribute('meal_basis');
                            $currency = $agreement->item($iAux)->getAttribute('currency');
                            $deadline = $agreement->item($iAux)->getAttribute('deadline');
                            $total = $agreement->item($iAux)->getAttribute('total');
                            $is_dynamic = $agreement->item($iAux)->getAttribute('is_dynamic');
                            $room_type = $agreement->item($iAux)->getAttribute('room_type');

                            //policies
                            $policies = $agreement->item($iAux)->getElementsByTagName('policies');
                            if ($policies->length > 0) {
                                $policy = $policies->item(0)->getElementsByTagName('policy');
                                if ($policy->length > 0) {
                                    $percentage = $policy->item(0)->getAttribute('percentage');
                                    $from2 = $policy->item(0)->getAttribute('from');
                                }
                            }

                            //remarks
                            $remarks = $agreement->item($iAux)->getElementsByTagName('remarks');
                            if ($remarks->length > 0) {
                                $remark = $remarks->item(0)->getElementsByTagName('remark');
                                if ($remark->length > 0) {
                                    $remarkcode = $remark->item(0)->getAttribute('code');
                                    $remarktext = $remark->item(0)->getAttribute('text');
                                }
                            }

                            //deadline
                            $deadline2 = $agreement->item($iAux)->getElementsByTagName('deadline');
                            if ($deadline2->length > 0) {
                                $value = $deadline2->item(0)->getAttribute('value');
                                $date = $deadline2->item(0)->getAttribute('date');
                            }

                            $room = $agreement->item($iAux)->getElementsByTagName('room');
                            if ($room->length > 0) {
                                for ($Auxk = 0; $Auxk < $room->length; $Auxk ++) {
                                    $type = $room->item($Auxk)->getAttribute('type');
                                    $required = $room->item($Auxk)->getAttribute('required');
                                    $occupancy = $room->item($Auxk)->getAttribute('occupancy');
                                    $extrabed = $room->item($Auxk)->getAttribute('extrabed');
                                    $age = $room->item($Auxk)->getAttribute('age');
                                    $occupancyChild = $room->item($Auxk)->getAttribute('occupancyChild');
                                    $price = $room->item(0)->getElementsByTagName('price');
                                    if ($price->length > 0) {
                                        for ($Auxkk = 0; $Auxkk < $price->length; $Auxkk ++) {
                                            $fromprice = $price->item($Auxkk)->getAttribute('from');
                                            $toprice = $price->item($Auxkk)->getAttribute('to');
                                            $roomprice = $price->item(0)->getElementsByTagName('roomprice');
                                            $nettroom = $roomprice->item(0)->getAttribute('nett');
                                            $extrabedprice = $price->item(0)->getElementsByTagName('extrabedprice');
                                            if ($extrabedprice->length > 0) {
                                                $nettextrabed = $extrabedprice->item(0)->getAttribute('nett');
                                            } else {
                                                $nettextrabed = "";
                                            }
                                        }
                                    }

                                                
                                    $rooms[$baseCounterDetails]['name'] = $name;
                                    $rooms[$baseCounterDetails]['hotelid'] = $code;
                                    $rooms[$baseCounterDetails]['roomid'] = $id;
                                    $rooms[$baseCounterDetails]['code'] = $shid;
                                    $rooms[$baseCounterDetails]['scode'] = $shid;
                                    $rooms[$baseCounterDetails]['shid'] = $shid;
                                    $rooms[$baseCounterDetails]['status'] = 1;
                                    $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-32";
                                    $rooms[$baseCounterDetails]['room'] = $room_type;
                                    $rooms[$baseCounterDetails]['room_type'] = "zz";
                                    $rooms[$baseCounterDetails]['availabilityid'] = $numbersearch;
                                    $rooms[$baseCounterDetails]['adults'] = $occupancy;
                                    $rooms[$baseCounterDetails]['children'] = $occupancyChild;
                                    $rooms[$baseCounterDetails]['nettotal'] = (double) $nettroom;
                                    if ($AICTravelMarkup != 0) {
                                        $total = $total + (($total * $AICTravelMarkup) / 100);
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
                                    if ($AICTravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                                        $sql = "select mapped from board_mapping where description='" . addslashes($room_basis) . "'";
                                        $statement = $db->createStatement($sql);
                                        $statement->prepare();
                                        $row_board_mapping = $statement->execute();
                                        $row_board_mapping->buffer();
                                        if ($row_board_mapping->valid()) {
                                            $row_board_mapping = $row_board_mapping->current();
                                            $room_basis = $row_board_mapping["mapped"];
                                        }
                                    } catch (\Exception $e) {
                                        $logger = new Logger();
                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                        $logger->addWriter($writer);
                                        $logger->info($e->getMessage());
                                    }
                                    $rooms[$baseCounterDetails]['meal'] = $translator->translate($room_basis);
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
                                    
                                    $rooms[$baseCounterDetails]['special'] = false;
                                    $rooms[$baseCounterDetails]['specialdescription'] = "";
                                    
                                    $after = date('Y-m-d', strtotime($from2));
                                    if ($remarkcode == "NONREFUNDABLE") {
                                        $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate($remarktext);
                                        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $deadline;
                                    } else {
                                        $rooms[$baseCounterDetails]['cancelpolicy'] = "If you cancel booking after " . $after . " pay " . $percentage . "% of total.";
                                        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $deadline;
                                    }
                                    // $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $rooms[$baseCounterDetails]['cancelpolicy_deadline'];
                                    
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
    // Store Session
    error_log("\r\nStore Session - $hid - $session_id - $index\r\n", 3, "/srv/www/htdocs/error_log");
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_aic');
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
    $insert->into('quote_session_aic');
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
error_log("\r\nEOF AIC - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");

?>