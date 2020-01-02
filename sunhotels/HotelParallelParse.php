<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nSunHotels - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\n Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $mealname = "";
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $searchresult = $inputDoc->getElementsByTagName("searchresult");
    $hotelInfo = $searchresult->item(0)->getElementsByTagName("hotels");
    $hotel = $hotelInfo->item(0)->getElementsByTagName("hotel");
    if ($hotel->length > 0) {
        $hotelid = $hotel->item(0)->getElementsByTagName("hotel.id");
        if ($hotelid->length > 0) {
            $hotelid = $hotelid->item(0)->nodeValue;
        } else {
            $hotelid = "";
        }
        $shid = $hotelid;
        $destination_id = $hotel->item(0)->getElementsByTagName("destination_id");
        if ($destination_id->length > 0) {
            $destination_id = $destination_id->item(0)->nodeValue;
        } else {
            $destination_id = "";
        }
        $resort_id = $hotel->item(0)->getElementsByTagName("resort_id");
        if ($resort_id->length > 0) {
            $resort_id = $resort_id->item(0)->nodeValue;
        } else {
            $resort_id = "";
        }
        $transfer = $hotel->item(0)->getElementsByTagName("transfer");
        if ($transfer->length > 0) {
            $transfer = $transfer->item(0)->nodeValue;
        } else {
            $transfer = "";
        }
        $notes = $hotel->item(0)->getElementsByTagName("notes");
        if ($notes->length > 0) {
            $notes = $notes->item(0)->nodeValue;
        } else {
            $notes = "";
        }
        $codes = $hotel->item(0)->getElementsByTagName("codes");
        if ($codes->length > 0) {
            $code = $codes->item(0)->getElementsByTagName("code");
            if ($code->length > 0) {
                $codetype = $code->item(0)->getAttribute("type");
                $codevalue = $code->item(0)->getAttribute("value");
            } else {
                $codetype = "";
                $codevalue = "";
            }
        }
        $distance = $hotel->item(0)->getElementsByTagName("distance");
        if ($distance->length > 0) {
            $distance = $distance->item(0)->nodeValue;
        } else {
            $distance = "";
        }
        
        $roomtypes = $hotel->item(0)->getElementsByTagName("roomtypes");
        if ($roomtypes->length > 0) {
            $roomtype = $roomtypes->item(0)->getElementsByTagName("roomtype");
            if ($roomtype->length > 0) {
                for ($j = 0; $j < $roomtype->length; $j ++) {
                    $roomtypeid = $roomtype->item($j)->getElementsByTagName("roomtype.ID");
                    if ($roomtypeid->length > 0) {
                        $roomtypeid = $roomtypeid->item(0)->nodeValue;
                    } else {
                        $roomtypeid = "";
                    }
                    
                    $roomsInfo = $roomtype->item($j)->getElementsByTagName("rooms");
                    if ($roomsInfo->length > 0) {
                        $room = $roomsInfo->item(0)->getElementsByTagName("room");
                        if ($room->length > 0) {
                            for ($jAux = 0; $jAux < $room->length; $jAux ++) {
                                $roomid = $room->item($jAux)->getElementsByTagName("id");
                                if ($roomid->length > 0) {
                                    $roomid = $roomid->item(0)->nodeValue;
                                } else {
                                    $roomid = "";
                                }
                                $beds = $room->item($jAux)->getElementsByTagName("beds");
                                if ($beds->length > 0) {
                                    $beds = $beds->item(0)->nodeValue;
                                } else {
                                    $beds = "";
                                }
                                $extrabeds = $room->item($jAux)->getElementsByTagName("extrabeds");
                                if ($extrabeds->length > 0) {
                                    $extrabeds = $extrabeds->item(0)->nodeValue;
                                } else {
                                    $extrabeds = "";
                                }
                                $notes = $room->item($jAux)->getElementsByTagName("notes");
                                if ($notes->length > 0) {
                                    $note = $notes->item(0)->getElementsByTagName("note");
                                    if ($note->length > 0) {
                                        $notestart_date = $note->item(0)->getAttribute("start_date");
                                        $noteend_date = $note->item(0)->getAttribute("end_date");
                                        $text = $note->item(0)->getElementsByTagName("text");
                                        if ($text->length > 0) {
                                            $text = $text->item(0)->nodeValue;
                                        } else {
                                            $text = "";
                                        }
                                    }
                                }
                                $isSuperDeal = $room->item($jAux)->getElementsByTagName("isSuperDeal");
                                if ($isSuperDeal->length > 0) {
                                    $isSuperDeal = $isSuperDeal->item(0)->nodeValue;
                                } else {
                                    $isSuperDeal = "";
                                }
                                $isBestBuy = $room->item($jAux)->getElementsByTagName("isBestBuy");
                                if ($isBestBuy->length > 0) {
                                    $isBestBuy = $isBestBuy->item(0)->nodeValue;
                                } else {
                                    $isBestBuy = "";
                                }
                                $cancellation_policies = $room->item($jAux)->getElementsByTagName("cancellation_policies");
                                if ($cancellation_policies->length > 0) {
                                    $cancellation_policy = $cancellation_policies->item(0)->getElementsByTagName("cancellation_policy");
                                    if ($cancellation_policy->length > 0) {
                                        $deadline = $cancellation_policy->item(0)->getElementsByTagName("deadline");
                                        if ($deadline->length > 0) {
                                            $deadline = $deadline->item(0)->nodeValue;
                                        } else {
                                            $deadline = "";
                                        }
                                        $percentage = $cancellation_policy->item(0)->getElementsByTagName("percentage");
                                        if ($percentage->length > 0) {
                                            $percentage = $percentage->item(0)->nodeValue;
                                        } else {
                                            $percentage = "";
                                        }
                                    }
                                }
                                $paymentMethods = $room->item($jAux)->getElementsByTagName("paymentMethods");
                                if ($paymentMethods->length > 0) {
                                    $paymentMethod = $paymentMethods->item(0)->getElementsByTagName("paymentMethod");
                                    if ($paymentMethod->length > 0) {
                                        $paymentMethodid = $paymentMethod->item(0)->getAttribute("id");
                                    }
                                }
                                
                                $meals = $room->item($jAux)->getElementsByTagName("meals");
                                if ($meals->length > 0) {
                                    $meal = $meals->item(0)->getElementsByTagName("meal");
                                    if ($meal->length > 0) {
                                        for ($jAux2 = 0; $jAux2 < $meal->length; $jAux2 ++) {
                                            $mealid = $meal->item($jAux2)->getElementsByTagName("id");
                                            if ($mealid->length > 0) {
                                                $mealid = $mealid->item(0)->nodeValue;
                                            } else {
                                                $mealid = "";
                                            }
                                            $labelId = $meal->item($jAux2)->getElementsByTagName("labelId");
                                            if ($labelId->length > 0) {
                                                $labelId = $labelId->item(0)->nodeValue;
                                            } else {
                                                $labelId = "";
                                            }
                                            $discount = $meal->item($jAux2)->getElementsByTagName("discount");
                                            if ($discount->length > 0) {
                                                $typeId = $discount->item(0)->getElementsByTagName("typeId");
                                                if ($typeId->length > 0) {
                                                    $typeId = $typeId->item(0)->nodeValue;
                                                } else {
                                                    $typeId = "";
                                                }
                                                $amounts = $discount->item(0)->getElementsByTagName("amounts");
                                                if ($amounts->length > 0) {
                                                    $amount = $amounts->item(0)->getElementsByTagName("amount");
                                                    if ($amount->length > 0) {
                                                        $amountcurrency = $amount->item(0)->getAttribute("currency");
                                                        $amountpaymentMethods = $amount->item(0)->getAttribute("paymentMethods");
                                                    }
                                                }
                                            }
                                            $price2 = 0;
                                            $prices = $meal->item($jAux2)->getElementsByTagName("prices");
                                            if ($prices->length > 0) {
                                                $price = $prices->item(0)->getElementsByTagName("price");
                                                if ($price->length > 0) {
                                                    $paymentMethods = $price->item(0)->getAttribute("paymentMethods");
                                                    $currency = $price->item(0)->getAttribute("currency");
                                                    $price2 = $price->item(0)->nodeValue;
                                                } else {
                                                    $paymentMethods = "";
                                                    $currency = "";
                                                    $price2 = 0;
                                                }
                                            }
                                            
                                            if ($mealid == 1) {
                                                $mealname = "No meals";
                                            } else if ($mealid == 3) {
                                                $mealname = "Breakfast";
                                            } else if ($mealid == 4) {
                                                $mealname = "Half board";
                                            } else if ($mealid == 5) {
                                                $mealname = "Full board";
                                            } else if ($mealid == 6) {
                                                $mealname = "All inclusive";
                                            }
                                            
                                            // $rooms[$baseCounterDetails]['name'] = $name;
                                            $rooms[$baseCounterDetails]['hotelid'] = $hotelid;
                                            $rooms[$baseCounterDetails]['roomid'] = $roomid;
                                            $rooms[$baseCounterDetails]['code'] = $shid;
                                            $rooms[$baseCounterDetails]['scode'] = $shid;
                                            $rooms[$baseCounterDetails]['shid'] = $shid;
                                            $rooms[$baseCounterDetails]['status'] = 1;
                                            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-7";
                                            $rooms[$baseCounterDetails]['room'] = $roomid;
                                            $rooms[$baseCounterDetails]['room_description'] = $roomid;
                                            $rooms[$baseCounterDetails]['rate_code'] = $ratetype;
                                            $rooms[$baseCounterDetails]['ratecategoryid'] = $ratecategoryid;
                                            $rooms[$baseCounterDetails]['ratePlanscode'] = $roomtypeid;
                                            $rooms[$baseCounterDetails]['adults'] = $adults;
                                            $rooms[$baseCounterDetails]['children'] = $children;
                                            $rooms[$baseCounterDetails]['childrenages'] = $childrenages;
                                            $rooms[$baseCounterDetails]['total'] = (double) $price2;
                                            $rooms[$baseCounterDetails]['totalplain'] = (double) $price2;
                                            $rooms[$baseCounterDetails]['nettotal'] = (double) $price2;
                                            $rooms[$baseCounterDetails]['mealid'] = $mealid;
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
                                            $rooms[$baseCounterDetails]['meal'] = $translator->translate($mealname);
                                            $pricebreakdown = array();
                                            $pricebreakdownCount = 0;
                                            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                                $amount = $price2 / $noOfNights;
                                                if ($roomerMarkup != 0) {
                                                    $amount = $amount + (($amount * $roomerMarkup) / 100);
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
                                                if ($roomerMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                                            
                                            $rooms[$baseCounterDetails]['special'] = false;
                                            $rooms[$baseCounterDetails]['specialdescription'] = "";
                                            
                                            if ($deadline != "") {
                                                $rooms[$baseCounterDetails]['cancelpolicy'] = $percentage . "%";
                                                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $deadline . " hours";
                                            } else {
                                                $checkin = date('Y-m-d', $from);
                                                $rooms[$baseCounterDetails]['cancelpolicy'] = $percentage . "%";
                                                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $checkin;
                                            }
                                            // $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $rooms[$baseCounterDetails]['cancelpolicy_deadline'];
                                            
                                            $rooms[$baseCounterDetails]['currency'] = strtoupper($currency);
                                            $baseCounterDetails ++;
                                            // $agoda = true;
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
    // Store Session
    error_log("\r\nStore Session - $hid - $session_id - $index\r\n", 3, "/srv/www/htdocs/error_log");
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_sunhotels');
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
    $insert->into('quote_session_sunhotels');
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
error_log("\r\nEOF SunHotels - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");

?>