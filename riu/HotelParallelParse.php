<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nRIU - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nRiu Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $HotelAvailResponse = $Body->item(0)->getElementsByTagName("HotelAvailResponse");
    $HotelAvailResponse2 = $HotelAvailResponse->item(0)->getElementsByTagName("HotelAvailResponse");
    $availabilityList = $HotelAvailResponse2->item(0)->getElementsByTagName("availabilityList");
    $node = $availabilityList->item(0)->getElementsByTagName("AvailabilityGroup");
    for ($i = 0; $i < $node->length; $i ++) {
        $amount = $node->item($i)->getElementsByTagName("amount");
        if ($amount->length > 0) {
            $amount = $amount->item(0)->nodeValue;
        } else {
            $amount = "";
        }
        $amountWithoutOffer = $node->item($i)->getElementsByTagName("amountWithoutOffer");
        if ($amountWithoutOffer->length > 0) {
            $amountWithoutOffer = $amountWithoutOffer->item(0)->nodeValue;
        } else {
            $amountWithoutOffer = "";
        }
        $amountNetCommissionable = $amount;
        if ($riuCommission > 0) {
            $amount = $amount - (($amount * $riuCommission) / 100);
            $amountWithoutOffer = $amountWithoutOffer - (($amountWithoutOffer * $riuCommission) / 100);
        }
        $amountNet = $amount;
        $amountWithoutOfferNet = $amountWithoutOffer;
        $currencyCode = $node->item($i)->getElementsByTagName("currencyCode");
        if ($currencyCode->length > 0) {
            $currencyCode = $currencyCode->item(0)->nodeValue;
        } else {
            $currencyCode = "";
        }
        if ($riuMarkup != 0) {
            $amount = $amount + (($amount * $riuMarkup) / 100);
            $amountWithoutOffer = $amountWithoutOffer + (($amountWithoutOffer * $riuMarkup) / 100);
        }
        // Geo target markup
        if ($internalmarkup != 0) {
            $amount = $amount + (($amount * $internalmarkup) / 100);
            $amountWithoutOffer = $amountWithoutOffer + (($amountWithoutOffer * $internalmarkup) / 100);
        }
        // Agent markup
        if ($agent_markup != 0) {
            $amount = $amount + (($amount * $agent_markup) / 100);
            $amountWithoutOffer = $amountWithoutOffer + (($amountWithoutOffer * $agent_markup) / 100);
        }
        // Fallback Markup
        if ($riuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
            $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
            $amountWithoutOffer = $amountWithoutOffer + (($amountWithoutOffer * $HotelsMarkupFallback) / 100);
        }
        // Agent discount
        if ($agent_discount != 0) {
            $amount = $amount - (($amount * $agent_discount) / 100);
            $amountWithoutOffer = $amountWithoutOffer - (($amountWithoutOffer * $agent_discount) / 100);
        }
        $currency = $currencyCode;
        if ($scurrency != "" and $currency != $scurrency) {
            $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
            $amountWithoutOffer = $CurrencyConverter->convert($amountWithoutOffer, $currency, $scurrency);
        }
        $hotelID = $node->item($i)->getElementsByTagName("hotelID");
        if ($hotelID->length > 0) {
            $hotelID = $hotelID->item(0)->nodeValue;
        } else {
            $hotelID = "";
        }
        $moroccoTaxes = $node->item($i)->getElementsByTagName("moroccoTaxes");
        if ($moroccoTaxes->length > 0) {
            $moroccoTaxes = $moroccoTaxes->item(0)->nodeValue;
        } else {
            $moroccoTaxes = "";
        }
        $quoteType = $node->item($i)->getElementsByTagName("quoteType");
        if ($quoteType->length > 0) {
            $quoteType = $quoteType->item(0)->nodeValue;
        } else {
            $quoteType = "";
        }
        $rateHotel = $node->item($i)->getElementsByTagName("rateHotel");
        if ($rateHotel->length > 0) {
            $rateHotel = $rateHotel->item(0)->nodeValue;
        } else {
            $rateHotel = "";
        }
        $taxIncluded = $node->item($i)->getElementsByTagName("taxIncluded");
        if ($taxIncluded->length > 0) {
            $taxIncluded = $taxIncluded->item(0)->nodeValue;
        } else {
            $taxIncluded = "";
        }
        $typePrice = $node->item($i)->getElementsByTagName("typePrice");
        if ($typePrice->length > 0) {
            $typePrice = $typePrice->item(0)->nodeValue;
        } else {
            $typePrice = "";
        }
        $uniqueID = $node->item($i)->getElementsByTagName("uniqueID");
        if ($uniqueID->length > 0) {
            $uniqueID = $uniqueID->item(0)->nodeValue;
        } else {
            $uniqueID = "";
        }
        $promocode = $node->item($i)->getElementsByTagName("promocode");
        if ($promocode->length > 0) {
            $promocode = $promocode->item(0)->nodeValue;
        } else {
            $promocode = "";
        }
        $rateReference = $node->item($i)->getElementsByTagName("rateReference");
        if ($rateReference->length > 0) {
            $rateReference = $rateReference->item(0)->nodeValue;
        } else {
            $rateReference = "";
        }
        // roomList
        $roomList = $node->item($i)->getElementsByTagName("roomList");
        if ($roomList->length > 0) {
            $RoomStayGroup = $roomList->item(0)->getElementsByTagName("RoomStayGroup");
            if ($RoomStayGroup->length > 0) {
                $RSGamount = $RoomStayGroup->item(0)->getElementsByTagName("amount");
                if ($RSGamount->length > 0) {
                    $RSGamount = $RSGamount->item(0)->nodeValue;
                } else {
                    $RSGamount = "";
                }
                $mealPlan = $RoomStayGroup->item(0)->getElementsByTagName("mealPlan");
                if ($mealPlan->length > 0) {
                    $mealPlan = $mealPlan->item(0)->nodeValue;
                } else {
                    $mealPlan = "";
                }
                $roomTypeCode = $RoomStayGroup->item(0)->getElementsByTagName("roomTypeCode");
                if ($roomTypeCode->length > 0) {
                    $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
                } else {
                    $roomTypeCode = "";
                }
                $roomConfig = $RoomStayGroup->item(0)->getElementsByTagName("roomConfig");
                if ($roomConfig->length > 0) {
                    $AdultsCount = $roomConfig->item(0)->getElementsByTagName("AdultsCount");
                    if ($AdultsCount->length > 0) {
                        $AdultsCount = $AdultsCount->item(0)->nodeValue;
                    } else {
                        $AdultsCount = "";
                    }
                    $Ages = $roomConfig->item(0)->getElementsByTagName("Ages");
                    if ($Ages->length > 0) {
                        $Ages = $Ages->item(0)->nodeValue;
                    } else {
                        $Ages = "";
                    }
                    $ChildCount = $roomConfig->item(0)->getElementsByTagName("ChildCount");
                    if ($ChildCount->length > 0) {
                        $ChildCount = $ChildCount->item(0)->nodeValue;
                    } else {
                        $ChildCount = "";
                    }
                    $InfantsCount = $roomConfig->item(0)->getElementsByTagName("InfantsCount");
                    if ($InfantsCount->length > 0) {
                        $InfantsCount = $InfantsCount->item(0)->nodeValue;
                    } else {
                        $InfantsCount = "";
                    }
                }
            }
        }
        $hab = array();
        $translationTHabs = $node->item($i)->getElementsByTagName("translationTHabs");
        if ($translationTHabs->length > 0) {
            $listTHabs = $translationTHabs->item(0)->getElementsByTagName("listTHabs");
            if ($listTHabs->length > 0) {
                $THabsDto = $listTHabs->item(0)->getElementsByTagName("THabsDto");
                if ($THabsDto->length > 0) {
                    $codTha = $THabsDto->item(0)->getElementsByTagName("codTha");
                    if ($codTha->length > 0) {
                        $codTha = $codTha->item(0)->nodeValue;
                    } else {
                        $codTha = "";
                    }
                    $listTranslation = $THabsDto->item(0)->getElementsByTagName("listTranslation");
                    if ($listTranslation->length > 0) {
                        $TranslationDto = $listTranslation->item(0)->getElementsByTagName("TranslationDto");
                        if ($TranslationDto->length > 0) {
                            for ($l = 0; $l < $TranslationDto->length; $l ++) {
                                $description = $TranslationDto->item($l)->getElementsByTagName("description");
                                if ($description->length > 0) {
                                    $description = $description->item(0)->nodeValue;
                                } else {
                                    $description = "";
                                }
                                $language = $TranslationDto->item($l)->getElementsByTagName("language");
                                if ($language->length > 0) {
                                    $language = $language->item(0)->nodeValue;
                                } else {
                                    $language = "";
                                }
                                $hab[$codTha][$language] = $description;
                            }
                        }
                    }
                }
            }
        }
        $rooms[$baseCounterDetails]['hotelid'] = $hotelID;
        $rooms[$baseCounterDetails]['code'] = $hotelID;
        $rooms[$baseCounterDetails]['scode'] = $hotelID;
        $rooms[$baseCounterDetails]['shid'] = $hotelID;
        $rooms[$baseCounterDetails]['status'] = 1;
        $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-57";
        $rooms[$baseCounterDetails]['JSESSIONID'] = $JSESSIONID;
        if ($hab[$roomTypeCode]['US'] != "") {
            $rooms[$baseCounterDetails]['room'] = $hab[$roomTypeCode]['US'];
        } else {
            $rooms[$baseCounterDetails]['room'] = $roomTypeCode;
        }
        $rooms[$baseCounterDetails]['roomtypecode'] = $roomTypeCode;
        $rooms[$baseCounterDetails]['ratehotel'] = $rateHotel;
        $rooms[$baseCounterDetails]['quotetype'] = $quoteType;
        $rooms[$baseCounterDetails]['ratereference'] = $rateReference;
        $rooms[$baseCounterDetails]['nettotalcommissionable'] = $amountNetCommissionable;
        $rooms[$baseCounterDetails]['mealplan'] = $mealPlan;
        $rooms[$baseCounterDetails]['adults'] = $AdultsCount;
        $rooms[$baseCounterDetails]['children'] = $ChildCount;
        $rooms[$baseCounterDetails]['infants'] = $InfantsCount;
        $rooms[$baseCounterDetails]['nettotal'] = (double) $amountNet;
        $rooms[$baseCounterDetails]['total'] = (double) $amount;
        $rooms[$baseCounterDetails]['totalplain'] = (double) $amount;
        try {
            $sql = "select mapped from board_mapping where description='" . addslashes($mealPlan) . "'";
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $row_board_mapping = $statement->execute();
            $row_board_mapping->buffer();
            if ($row_board_mapping->valid()) {
                $row_board_mapping = $row_board_mapping->current();
                $mealPlan = $row_board_mapping["mapped"];
            }
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        $rooms[$baseCounterDetails]['meal'] = $translator->translate($mealPlan);
        $pricebreakdown = array();
        $pricebreakdownCount = 0;
        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
            $amount = $amountWithoutOffer / $noOfNights;
            $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
            $pricebreakdownCount = $pricebreakdownCount + 1;
        }
        $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
        $rooms[$baseCounterDetails]['scurrency'] = $currencyCode;
        $rooms[$baseCounterDetails]['promocode'] = $promocode;
        //
        // Special
        //
        if ($promocode != "") {
            $rooms[$baseCounterDetails]['special'] = true;
            $rooms[$baseCounterDetails]['specialdescription'] = $promocode;
        } else {
            $rooms[$baseCounterDetails]['special'] = false;
            $rooms[$baseCounterDetails]['specialdescription'] = "";
        }
        //
        // Cancellation policies
        //
        $rooms[$baseCounterDetails]['cancelpolicy'] = "";
        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
        $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
        
        $rooms[$baseCounterDetails]['currency'] = strtoupper($currencyCode);
        $baseCounterDetails ++;
    }
    //
    // Store Session
    //
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_riu');
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
    $insert->into('quote_session_riu');
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