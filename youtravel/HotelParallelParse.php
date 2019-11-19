<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n Youtravel - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\n Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $roomNumber = "";
    $number = 0;
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $HtSearchRq = $inputDoc->getElementsByTagName("HtSearchRq");

    $Search_Criteria = $HtSearchRq->item(0)->getElementsByTagName("Search_Criteria");
    if ($Search_Criteria->length > 0) {
        $Rooms = $Search_Criteria->item(0)->getElementsByTagName("Rooms");
        if ($Rooms->length > 0) {
            $Rooms = $Rooms->item(0)->nodeValue;
        } else {
            $Rooms = "";
        }
    }

    $session = $HtSearchRq->item(0)->getElementsByTagName("session");
    if ($session->length > 0) {
        $sessionid = $session->item(0)->getAttribute("id");
        $Currency = $session->item(0)->getElementsByTagName("Currency");
        if ($Currency->length > 0) {
            $Currency = $Currency->item(0)->nodeValue;
        } else {
            $Currency = "";
        }
        
        $Hotel = $session->item(0)->getElementsByTagName("Hotel");
        if ($Hotel->length > 0) {
            for ($i = 0; $i < $Hotel->length; $i ++) {
                $ID = $Hotel->item($i)->getAttribute("ID");
                $shid = $ID;
                error_log("\r\n ID - $ID\r\n", 3, "/srv/www/htdocs/error_log");
                $Hotel_Name = $Hotel->item($i)->getElementsByTagName("Hotel_Name");
                if ($Hotel_Name->length > 0) {
                    $Hotel_Name = $Hotel_Name->item(0)->nodeValue;
                } else {
                    $Hotel_Name = "";
                }
                $Youtravel_Rating = $Hotel->item($i)->getElementsByTagName("Youtravel_Rating");
                if ($Youtravel_Rating->length > 0) {
                    $Youtravel_Rating = $Youtravel_Rating->item(0)->nodeValue;
                } else {
                    $Youtravel_Rating = "";
                }
                $Official_Rating = $Hotel->item($i)->getElementsByTagName("Official_Rating");
                if ($Official_Rating->length > 0) {
                    $Official_Rating = $Official_Rating->item(0)->nodeValue;
                } else {
                    $Official_Rating = "";
                }
                $Board_Type = $Hotel->item($i)->getElementsByTagName("Board_Type");
                if ($Board_Type->length > 0) {
                    $Board_Type = $Board_Type->item(0)->nodeValue;
                } else {
                    $Board_Type = "";
                }
                $Child_Age = $Hotel->item($i)->getElementsByTagName("Child_Age");
                if ($Child_Age->length > 0) {
                    $Child_Age = $Child_Age->item(0)->nodeValue;
                } else {
                    $Child_Age = "";
                }
                $Country = $Hotel->item($i)->getElementsByTagName("Country");
                if ($Country->length > 0) {
                    $Country = $Country->item(0)->nodeValue;
                } else {
                    $Country = "";
                }
                $Destination = $Hotel->item($i)->getElementsByTagName("Destination");
                if ($Hotel_Name->length > 0) {
                    $Destination = $Destination->item(0)->nodeValue;
                } else {
                    $Destination = "";
                }
                $Resort = $Hotel->item($i)->getElementsByTagName("Resort");
                if ($Resort->length > 0) {
                    $Resort = $Resort->item(0)->nodeValue;
                } else {
                    $Resort = "";
                }
                $Image = $Hotel->item($i)->getElementsByTagName("Image");
                if ($Image->length > 0) {
                    $Image = $Image->item(0)->nodeValue;
                } else {
                    $Image = "";
                }
                $Hotel_Desc = $Hotel->item($i)->getElementsByTagName("Hotel_Desc");
                if ($Hotel_Desc->length > 0) {
                    $Hotel_Desc = $Hotel_Desc->item(0)->nodeValue;
                } else {
                    $Hotel_Desc = "";
                }
                
                for ($x = 0; $x < $Rooms; $x ++) {
                    $number = $x + 1;
                    $roomNumber = $Hotel->item($x)->getElementsByTagName("Room_" . $number);
                    if ($roomNumber->length > 0) {
                        $Passengers = $roomNumber->item(0)->getElementsByTagName("Passengers");
                        if ($Passengers->length > 0) {
                            $Adults = $Passengers->item(0)->getAttribute("Adults");
                            $Children = $Passengers->item(0)->getAttribute("Children");
                            $Infants = $Passengers->item(0)->getAttribute("Infants");
                        }
                        
                        $Room = $roomNumber->item(0)->getElementsByTagName("Room");
                        if ($Room->length > 0) {
                            for ($j = 0; $j < $Room->length; $j ++) {
                                $RoomId = $Room->item($j)->getAttribute("Id");
                                error_log("\r\n RoomId - $RoomId\r\n", 3, "/srv/www/htdocs/error_log");
                                $ADV = $Room->item($j)->getAttribute("ADV");
                                $Refundable = $Room->item($j)->getAttribute("Refundable");
                                $Type = $Room->item($j)->getElementsByTagName("Type");
                                if ($Type->length > 0) {
                                    $Type = $Type->item(0)->nodeValue;
                                } else {
                                    $Type = "";
                                }
                                $Board = $Room->item($j)->getElementsByTagName("Board");
                                if ($Board->length > 0) {
                                    $Board = $Board->item(0)->nodeValue;
                                } else {
                                    $Board = "";
                                }
                                $Rates = $Room->item($j)->getElementsByTagName("Rates");
                                if ($Rates->length > 0) {
                                    $Final_Rate = $Rates->item(0)->getAttribute("Final_Rate");
                                    $Original_Rate = $Rates->item(0)->getAttribute("Original_Rate");
                                } else {
                                    $Final_Rate = "";
                                    $Original_Rate = "";
                                }
                                $Offers = $Room->item($j)->getElementsByTagName("Offers");
                                if ($Offers->length > 0) {
                                    $Gala_Meals = $Offers->item(0)->getAttribute("Gala_Meals");
                                    $Free_Transfer = $Offers->item(0)->getAttribute("Free_Transfer");
                                    $Free_Stay = $Offers->item(0)->getAttribute("Free_Stay");
                                    $Early_Booking_Discount = $Offers->item(0)->getAttribute("Early_Booking_Discount");
                                    $Lastminute_Offer = $Offers->item(0)->getAttribute("Lastminute_Offer");
                                } else {
                                    $Gala_Meals = "";
                                    $Free_Transfer = "";
                                    $Free_Stay = "";
                                    $Early_Booking_Discount = "";
                                    $Lastminute_Offer = "";
                                }
                                
                                $rooms[$baseCounterDetails]['name'] = $Hotel_Name;
                                $rooms[$baseCounterDetails]['hotelid'] = $ID;
                                $rooms[$baseCounterDetails]['roomid'] = $RoomId;
                                $rooms[$baseCounterDetails]['code'] = $shid;
                                $rooms[$baseCounterDetails]['scode'] = $shid;
                                $rooms[$baseCounterDetails]['shid'] = $shid;
                                $rooms[$baseCounterDetails]['status'] = 1;
                                $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-34";
                                $rooms[$baseCounterDetails]['room'] = $Type;
                                $rooms[$baseCounterDetails]['room_description'] = $Type;
                                $rooms[$baseCounterDetails]['rate_code'] = $Type;
                                $rooms[$baseCounterDetails]['Refundable'] = $Refundable;
                                $rooms[$baseCounterDetails]['adults'] = $adults;
                                $rooms[$baseCounterDetails]['children'] = $children;
                                $rooms[$baseCounterDetails]['total'] = (double) $TotalPaymentAmountinclusive;
                                $rooms[$baseCounterDetails]['totalplain'] = (double) $Original_Rate;
                                $rooms[$baseCounterDetails]['nettotal'] = (double) $Final_Rate;
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
                                $rooms[$baseCounterDetails]['meal'] = $translator->translate($Board);
                                $pricebreakdown = array();
                                $pricebreakdownCount = 0;
                                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                    $amount = $Original_Rate / $noOfNights;
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
                                
                                $rooms[$baseCounterDetails]['currency'] = strtoupper($currency);
                                $baseCounterDetails ++;
                            }
                        }
                    }
                }
            }
        }
    }
    // Store Session
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_youtravel');
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
    $insert->into('quote_session_youtravel');
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
