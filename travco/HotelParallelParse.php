<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n TRAVCO - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    if ($inputDoc != NULL) {
        $responseElement = $inputDoc->documentElement;
        $xpath = new DOMXPath($inputDoc);
        $errorsElements = $xpath->query('/RETURNDATA/MESSAGE', $responseElement);
        if ($errorsElements->length > 0) {
            $ErrorMsg = $errorsElements->item(0)->nodeValue;
            $pos = strpos($ErrorMsg, "Error");
            if ($pos === false) {
                $searchHotelPriceReponseElements = "";
                $searchHotelPriceReponseElements = $xpath->query('/RETURNDATA/DATA', $responseElement);
                foreach ($searchHotelPriceReponseElements as $searchHotelPriceReponseElement) {
                    $COUNTRY_NAME = $searchHotelPriceReponseElement->getElementsByTagName("COUNTRY_NAME");
                    $CITY_NAME = $searchHotelPriceReponseElement->getElementsByTagName("CITY_NAME");
                    $NO_OF_CHILDREN = $searchHotelPriceReponseElement->getAttribute("NO_OF_CHILDREN");
                    $NO_OF_ADULTS = $searchHotelPriceReponseElement->getAttribute("NO_OF_ADULTS");
                    $COUNTRY_CODE = $searchHotelPriceReponseElement->getAttribute("COUNTRY_CODE");
                    $CITY_CODE = $searchHotelPriceReponseElement->getAttribute("CITY_CODE");
                    $CHECK_OUT_DATE = $searchHotelPriceReponseElement->getAttribute("CHECK_OUT_DATE");
                    $CHECK_IN_DATE = $searchHotelPriceReponseElement->getAttribute("CHECK_IN_DATE");

                    $node = $searchHotelPriceReponseElement->getElementsByTagName("HOTEL_DATA");
                    for ($hd=0; $hd < $node->length; $hd++) { 
                        $HOTEL_NAME = $node->item($hd)->getElementsByTagName("HOTEL_NAME");
                        if ($HOTEL_NAME->length > 0) {
                            $HOTEL_NAME = $HOTEL_NAME->item(0)->nodeValue;
                        } else {
                            $HOTEL_NAME = "";
                        }
                        $HOTEL_STAR = $node->item($hd)->getElementsByTagName("HOTEL_STAR");
                        if ($HOTEL_STAR->length > 0) {
                            $HOTEL_STAR = $HOTEL_STAR->item(0)->nodeValue;
                        } else {
                            $HOTEL_STAR = "";
                        }
                        $CURRENCY_NAME = $node->item($hd)->getElementsByTagName("CURRENCY_NAME");
                        if ($CURRENCY_NAME->length > 0) {
                            $CURRENCY_NAME = $CURRENCY_NAME->item(0)->nodeValue;
                        } else {
                            $CURRENCY_NAME = "";
                        }
                        $STATUS = $node->item($hd)->getAttribute("STATUS");
                        $HOTEL_CODE = $node->item($hd)->getAttribute("HOTEL_CODE");
                        $shid = $HOTEL_CODE;
                        $CURRENCY_CODE = $node->item($hd)->getAttribute("CURRENCY_CODE");

                        $node2 = $node->item($hd)->getElementsByTagName("ROOM_DATA");
                        for ($i=0; $i < $node2->length; $i++) { 
                            $ROOM_NAME = $node2->item($i)->getElementsByTagName("ROOM_NAME");
                            if ($ROOM_NAME->length > 0) {
                                $ROOM_NAME = $ROOM_NAME->item(0)->nodeValue;
                            } else {
                                $ROOM_NAME = "";
                            }
                            $ADULT_PRICE_DETAILS = $node2->item($i)->getElementsByTagName("ADULT_PRICE_DETAILS");
                            if ($ADULT_PRICE_DETAILS->length > 0) {
                                $ADULT_PRICE_DETAILS = $ADULT_PRICE_DETAILS->item(0)->nodeValue;
                            } else {
                                $ADULT_PRICE_DETAILS = "";
                            }
                            $TOTAL_ADULT_PRICE = $node2->item($i)->getAttribute("TOTAL_ADULT_PRICE");
                            $ROOM_PAX = $node2->item($i)->getAttribute("ROOM_PAX");
                            $ROOM_CODE = $node2->item($i)->getAttribute("ROOM_CODE");
                            $PRICE_CODE = $node2->item($i)->getAttribute("PRICE_CODE");
                            $NO_OF_EXTRA_BEDS = $node2->item($i)->getAttribute("NO_OF_EXTRA_BEDS");
                            $EXTRA_BED_INDICATOR = $node2->item($i)->getAttribute("EXTRA_BED_INDICATOR");
                            $CHILD_PRICE = $node2->item($i)->getAttribute("CHILD_PRICE");
                            $CCHARGES_CODE = $node2->item($i)->getAttribute("CCHARGES_CODE");
                            $ADULT_PRICE = $node2->item($i)->getAttribute("ADULT_PRICE");

                            $ReducedPriceDetails = $node2->item($i)->getElementsByTagName("ReducedPriceDetails");
                            if ($ReducedPriceDetails->length > 0) {
                                $AdultReductionPercentage = $ReducedPriceDetails->item(0)->getAttribute("AdultReductionPercentage");
                                $AdultReductionAmount = $ReducedPriceDetails->item(0)->getAttribute("AdultReductionAmount");
                            } else {
                                $ReducedPriceDetails = "";
                            }

                            $rooms[$baseCounterDetails]['name'] = $HOTEL_NAME;
                            $rooms[$baseCounterDetails]['hotelid'] = $HOTEL_CODE;
                            $rooms[$baseCounterDetails]['roomid'] = $ROOM_CODE;
                            $rooms[$baseCounterDetails]['code'] = $HOTEL_CODE;
                            $rooms[$baseCounterDetails]['scode'] = $HOTEL_CODE;
                            $rooms[$baseCounterDetails]['shid'] = $shid;
                            $rooms[$baseCounterDetails]['status'] = 1;
                            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-23";
                            $rooms[$baseCounterDetails]['room'] = $ROOM_NAME;
                            $rooms[$baseCounterDetails]['roomtype'] = $ROOM_CODE;
                            $rooms[$baseCounterDetails]['room_description'] = $ROOM_NAME;
                            $rooms[$baseCounterDetails]['RateCode'] = $CCHARGES_CODE;
                            $rooms[$baseCounterDetails]['OCCUPANCY'] = $CCHARGES_CODE;
                            $rooms[$baseCounterDetails]['CCHARGES_CODE'] = $CCHARGES_CODE;
                            $rooms[$baseCounterDetails]['adults'] = $adults;
                            $rooms[$baseCounterDetails]['children'] = $children;
                            $rooms[$baseCounterDetails]['ADULT_PRICE'] = $ADULT_PRICE;
                            $rooms[$baseCounterDetails]['ADULT_PRICE_DETAILS'] = $ADULT_PRICE_DETAILS;
                            $rooms[$baseCounterDetails]['CHILD_PRICE'] = $CHILD_PRICE;
                            $rooms[$baseCounterDetails]['nettotal'] = (double) $TOTAL_ADULT_PRICE;
                            if ($TravcoMarkup != 0) {
                                $TOTAL_ADULT_PRICE = $TOTAL_ADULT_PRICE + (($TOTAL_ADULT_PRICE * $TravcoMarkup) / 100);
                            }
                            // Geo target markup
                            if ($internalmarkup != 0) {
                                $TOTAL_ADULT_PRICE = $TOTAL_ADULT_PRICE + (($TOTAL_ADULT_PRICE * $internalmarkup) / 100);
                            }
                            // Agent markup
                            if ($agent_markup != 0) {
                                $TOTAL_ADULT_PRICE = $TOTAL_ADULT_PRICE + (($TOTAL_ADULT_PRICE * $agent_markup) / 100);
                            }
                            // Fallback Markup
                            if ($TravcoMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                $TOTAL_ADULT_PRICE = $TOTAL_ADULT_PRICE + (($TOTAL_ADULT_PRICE * $HotelsMarkupFallback) / 100);
                            }
                            // Agent discount
                            if ($agent_discount != 0) {
                                $TOTAL_ADULT_PRICE = $TOTAL_ADULT_PRICE - (($TOTAL_ADULT_PRICE * $agent_discount) / 100);
                            }
                            if ($scurrency != "" and $currency != $scurrency) {
                                $TOTAL_ADULT_PRICE = $CurrencyConverter->convert($TOTAL_ADULT_PRICE, $currency, $scurrency);
                            }
                            $rooms[$baseCounterDetails]['total'] = (double) $TOTAL_ADULT_PRICE;
                            $rooms[$baseCounterDetails]['totalplain'] = (double) $TOTAL_ADULT_PRICE;
                            try {
                                $sql = "select mapped from board_mapping where description='" . addslashes($ROOM_CODE) . "'";
                                $statement = $db->createStatement($sql);
                                $statement->prepare();
                                $row_board_mapping = $statement->execute();
                                $row_board_mapping->buffer();
                                if ($row_board_mapping->valid()) {
                                    $row_board_mapping = $row_board_mapping->current();
                                    $ROOM_CODE = $row_board_mapping["mapped"];
                                }
                            } catch (\Exception $e) {
                                $logger = new Logger();
                                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                $logger->addWriter($writer);
                                $logger->info($e->getMessage());
                            }
                            $rooms[$baseCounterDetails]['meal'] = $translator->translate($ROOM_CODE);
                            $pricebreakdown = array();
                            $pricebreakdownCount = 0;
                            $amount = $TOTAL_ADULT_PRICE / $noOfNights;
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
                            if ($AdultReductionPercentage > 0) {
                                $RULE_TEXT = $AdultReductionPercentage . "% " . gettext("discount");
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $RULE_TEXT;
                            } else {
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                            }
                            $rooms[$baseCounterDetails]['FareRateType'] = $FareRateType;
                            $rooms[$baseCounterDetails]['DailyCostCancel'] = $DailyCostCancel;
                            //
                            // Cancellation policies
                            //
                            $procurar = "Non-Refundable";
                            if (strpos($PromotionName, $procurar) !== false) {
                                $rooms[$baseCounterDetails]['nonrefundable'] = true;
                                $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking");
                                $rooms[$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
                                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                                $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
                            } else {
                                $rooms[$baseCounterDetails]['cancelpolicy'] = "";
                                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                                $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                            }
                            $rooms[$baseCounterDetails]['currency'] = strtoupper($CURRENCY_CODE);
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
    $delete->from('quote_session_travco');
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
    $insert->into('quote_session_travco');
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