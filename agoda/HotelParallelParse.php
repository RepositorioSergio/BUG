<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nAgoda - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $AvailabilityLongResponseV2 = $inputDoc->getElementsByTagName("AvailabilityLongResponseV2");
    $searchid = $AvailabilityLongResponseV2->item(0)->getAttribute('searchid');

    $Hotels = $AvailabilityLongResponseV2->item(0)->getElementsByTagName('Hotels');
    if ($Hotels->length > 0) {
        $Hotel = $Hotels->item(0)->getElementsByTagName('Hotel');
        if ($Hotel->length > 0) {
            for ($i=0; $i < $Hotel->length; $i++) { 
                $Id = $Hotel->item($i)->getElementsByTagName('Id');
                if ($Id->length > 0) {
                    $Id = $Id->item(0)->nodeValue;
                } else {
                    $Id = "";
                }
                error_log("\r\n Id - $Id\r\n", 3, "/srv/www/htdocs/error_log");
                $shid = $Id;
                $sfilter[] = " sid='$Id' ";

                $CheapestRoom = $Hotel->item($i)->getElementsByTagName('CheapestRoom');
                if ($CheapestRoom->length > 0) {
                    $inclusive = $CheapestRoom->item(0)->getAttribute('inclusive');
                    $fees = $CheapestRoom->item(0)->getAttribute('fees');
                    $tax = $CheapestRoom->item(0)->getAttribute('tax');
                    $exclusive = $CheapestRoom->item(0)->getAttribute('exclusive');
                } else {
                    $inclusive = "";
                    $fees = "";
                    $tax = "";
                    $exclusive = "";
                }
                $PaxSettings = $Hotel->item($i)->getElementsByTagName('PaxSettings');
                if ($PaxSettings->length > 0) {
                    $childage = $PaxSettings->item(0)->getAttribute('childage');
                    $infantage = $PaxSettings->item(0)->getAttribute('infantage');
                    $submit = $PaxSettings->item(0)->getAttribute('submit');
                } else {
                    $childage = "";
                    $infantage = "";
                    $submit = "";
                }

                $Rooms = $Hotel->item($i)->getElementsByTagName('Rooms');
                if ($Rooms->length > 0) {
                    $Room = $Rooms->item(0)->getElementsByTagName('Room');
                    if ($Room->length > 0) {
                        for ($r=0; $r < $Room->length; $r++) { 
                            $Roomid = $Room->item($r)->getAttribute('id');
                            $Roomname = $Room->item($r)->getAttribute('name');
                            $promoeligible = $Room->item($r)->getAttribute('promoeligible');
                            $blockid = $Room->item($r)->getAttribute('blockid');
                            $ratecategoryid = $Room->item($r)->getAttribute('ratecategoryid');
                            $model = $Room->item($r)->getAttribute('model');
                            $currency = $Room->item($r)->getAttribute('currency');
                            $ratetype = $Room->item($r)->getAttribute('ratetype');
                            $rateplan = $Room->item($r)->getAttribute('rateplan');
                            $lineitemid = $Room->item($r)->getAttribute('lineitemid');
                            $promotionid = $Room->item($r)->getAttribute('promotionid');
                            error_log("\r\n Roomid - $Roomid\r\n", 3, "/srv/www/htdocs/error_log");

                            $StandardTranslation = $Room->item($r)->getElementsByTagName('StandardTranslation');
                            if ($StandardTranslation->length > 0) {
                                $StandardTranslation = $StandardTranslation->item(0)->nodeValue;
                            } else {
                                $StandardTranslation = "";
                            }
                            $RemainingRooms = $Room->item($r)->getElementsByTagName('RemainingRooms');
                            if ($RemainingRooms->length > 0) {
                                $RemainingRooms = $RemainingRooms->item(0)->nodeValue;
                            } else {
                                $RemainingRooms = "";
                            }
                            //Benefits
                            $benefitArray = array();
                            $count3 = 0;
                            $Benefits = $Room->item($r)->getElementsByTagName('Benefits');
                            if ($Benefits->length > 0) {
                                $Benefit = $Benefits->item(0)->getElementsByTagName('Benefit');
                                if ($Benefit->length > 0) {
                                    for ($b=0; $b < $Benefit->length; $b++) { 
                                        $Benefitid = $Benefit->item($b)->getAttribute('id');
                                        $BenefitName = $Benefit->item($b)->getElementsByTagName('Name');
                                        if ($BenefitName->length > 0) {
                                            $BenefitName = $BenefitName->item(0)->nodeValue;
                                        } else {
                                            $BenefitName = "";
                                        }
                                        $BenefitTranslation = $Benefit->item($b)->getElementsByTagName('Translation');
                                        if ($BenefitTranslation->length > 0) {
                                            $BenefitTranslation = $BenefitTranslation->item(0)->nodeValue;
                                        } else {
                                            $BenefitTranslation = "";
                                        }
                                        $benefitArray[$count3]["id"] = $Benefitid;
                                        $benefitArray[$count3]["Name"] = $BenefitName;
                                        $benefitArray[$count3]["Translation"] = $BenefitTranslation;
                                        $count3 = $count3 + 1;
                                    }
                                }
                            }
                            //ParentRoom
                            $ParentRoom = $Room->item($r)->getElementsByTagName('ParentRoom');
                            if ($ParentRoom->length > 0) {
                                $ParentRoomid = $ParentRoom->item(0)->getAttribute('id');
                                $ParentRoomname = $ParentRoom->item(0)->getAttribute('name');
                                $ParentRoomtranslationname = $ParentRoom->item(0)->getAttribute('translationname');
                            } else {
                                $ParentRoomid = "";
                                $ParentRoomname = "";
                                $ParentRoomtranslationname = "";
                            }
                            //MaxRoomOccupancy
                            $MaxRoomOccupancy = $Room->item($r)->getElementsByTagName('MaxRoomOccupancy');
                            if ($MaxRoomOccupancy->length > 0) {
                                $extrabeds = $MaxRoomOccupancy->item(0)->getAttribute('extrabeds');
                                $normalbedding = $MaxRoomOccupancy->item(0)->getAttribute('normalbedding');
                            } else {
                                $extrabeds = "";
                                $normalbedding = "";
                            }
                            //RateInfo
                            $RateInfo = $Room->item($r)->getElementsByTagName('RateInfo');
                            if ($RateInfo->length > 0) {
                                $Included = $RateInfo->item(0)->getElementsByTagName('Included');
                                if ($Included->length > 0) {
                                    $Included = $Included->item(0)->nodeValue;
                                } else {
                                    $Included = "";
                                }
                                error_log("\r\n Included - $Included \r\n", 3, "/srv/www/htdocs/error_log");
                                $Rate = $RateInfo->item(0)->getElementsByTagName('Rate');
                                if ($Rate->length > 0) {
                                    $Rateinclusive = $Rate->item(0)->getAttribute('inclusive');
                                    $Ratefees = $Rate->item(0)->getAttribute('fees');
                                    $Ratetax = $Rate->item(0)->getAttribute('tax');
                                    $Rateexclusive = $Rate->item(0)->getAttribute('exclusive');
                                } else {
                                    $Rateinclusive = "";
                                    $Ratefees = "";
                                    $Ratetax = "";
                                    $Rateexclusive = "";
                                }
                                $Promotion = $RateInfo->item(0)->getElementsByTagName('Promotion');
                                if ($Promotion->length > 0) {
                                    $text = $Promotion->item(0)->getAttribute('text');
                                    $savings = $Promotion->item(0)->getAttribute('savings');
                                } else {
                                    $text = "";
                                    $savings = "";
                                }
                                $TotalPaymentAmount = $RateInfo->item(0)->getElementsByTagName('TotalPaymentAmount');
                                if ($TotalPaymentAmount->length > 0) {
                                    $TotalPaymentAmountinclusive = $TotalPaymentAmount->item(0)->getAttribute('inclusive');
                                    $TotalPaymentAmountfees = $TotalPaymentAmount->item(0)->getAttribute('fees');
                                    $TotalPaymentAmounttax = $TotalPaymentAmount->item(0)->getAttribute('tax');
                                    $TotalPaymentAmountexclusive = $TotalPaymentAmount->item(0)->getAttribute('exclusive');
                                } else {
                                    $TotalPaymentAmountinclusive = "";
                                    $TotalPaymentAmountfees = "";
                                    $TotalPaymentAmounttax = "";
                                    $TotalPaymentAmountexclusive = "";
                                }
                            }

                            //Cancellation
                            $policy = "";
                            $policyTrans = "";
                            $policyParam = "";
                            $policyArray = array();
                            $count2 = 0;
                            $Cancellation = $Room->item($r)->getElementsByTagName('Cancellation');
                            if ($Cancellation->length > 0) {
                                $PolicyText = $Cancellation->item(0)->getElementsByTagName('PolicyText');
                                if ($PolicyText->length > 0) {
                                    $language = $PolicyText->item(0)->getAttribute('language');
                                    $policy = $PolicyText->item(0)->nodeValue;
                                } else {
                                    $policy = "";
                                }
                                $PolicyTranslated = $Cancellation->item(0)->getElementsByTagName('PolicyTranslated');
                                if ($PolicyTranslated->length > 0) {
                                    $language = $PolicyTranslated->item(0)->getAttribute('language');
                                    $policyTrans = $PolicyTranslated->item(0)->nodeValue;
                                } else {
                                    $policyTrans = "";
                                }
                                $PolicyParameters = $Cancellation->item(0)->getElementsByTagName('PolicyParameters');
                                if ($PolicyParameters->length > 0) {
                                    $PolicyParameter = $PolicyParameters->item(0)->getElementsByTagName('PolicyParameter');
                                    if ($PolicyParameter->length > 0) {
                                        for ($j=0; $j < $PolicyParameter->length; $j++) { 
                                            $PolicyParametercharge = $PolicyParameter->item($j)->getAttribute('charge');
                                            $PolicyParameterdays = $PolicyParameter->item($j)->getAttribute('days');
                                            $policyParam = $PolicyParameter->item($j)->nodeValue;
                                        }
                                    }
                                }
                                $PolicyDates = $Cancellation->item(0)->getElementsByTagName('PolicyDates');
                                if ($PolicyDates->length > 0) {
                                    $PolicyDate = $PolicyDates->item(0)->getElementsByTagName('PolicyDate');
                                    if ($PolicyDate->length > 0) {
                                        for ($p=0; $p < $PolicyDate->length; $p++) { 
                                            $before = $PolicyDate->item($p)->getAttribute('before');
                                            $after = $PolicyDate->item($p)->getAttribute('after');

                                            $policyArray[$count2]["before"] = $before;
                                            $policyArray[$count2]["after"] = $after;

                                            $RatePD = $PolicyDate->item($p)->getElementsByTagName('Rate');
                                            if ($RatePD->length > 0) {
                                                $RatePDinclusive = $RatePD->item(0)->getAttribute('inclusive');
                                                $RatePDfees = $RatePD->item(0)->getAttribute('fees');
                                                $RatePDtax = $RatePD->item(0)->getAttribute('tax');
                                                $RatePDexclusive = $RatePD->item(0)->getAttribute('exclusive');

                                                $policyArray[$count2]["inclusive"] = $RatePDinclusive;
                                                $policyArray[$count2]["fees"] = $RatePDfees;
                                                $policyArray[$count2]["tax"] = $RatePDtax;
                                                $policyArray[$count2]["exclusive"] = $RatePDexclusive;
                                                
                                            } else {
                                                $RatePDinclusive = "";
                                                $RatePDfees = "";
                                                $RatePDtax = "";
                                                $RatePDexclusive = "";
                                            }
                                            $count2 = $count2 + 1;
                                        }
                                    }
                                }
                            }


                            //$rooms[$baseCounterDetails]['name'] = $name;
                            $rooms[$baseCounterDetails]['hotelid'] = $Id;
                            $rooms[$baseCounterDetails]['roomid'] = $Roomid;
                            $rooms[$baseCounterDetails]['code'] = $shid;
                            $rooms[$baseCounterDetails]['scode'] = $shid;
                            $rooms[$baseCounterDetails]['shid'] = $shid;
                            $rooms[$baseCounterDetails]['status'] = 1;
                            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-37";
                            $rooms[$baseCounterDetails]['room'] = $Roomname;
                            $rooms[$baseCounterDetails]['room_description'] = $Roomname;
                            $rooms[$baseCounterDetails]['rate_code'] = $ratetype;
                            $rooms[$baseCounterDetails]['searchid'] = $searchid;
                            $rooms[$baseCounterDetails]['ratecategoryid'] = $ratecategoryid;
                            $rooms[$baseCounterDetails]['ratePlanscode'] = $rateplan;
                            $rooms[$baseCounterDetails]['rateIncluded'] = $Included;
                            $rooms[$baseCounterDetails]['rateExclusive'] = $rateExclusive;
                            $rooms[$baseCounterDetails]['rateTax'] = $rateTax;
                            $rooms[$baseCounterDetails]['rateFees'] = $rateFees;
                            $rooms[$baseCounterDetails]['rateInclusive'] = $rateInclusive;
                            $rooms[$baseCounterDetails]['promotionid'] = $promotionid;
                            $rooms[$baseCounterDetails]['lineitemid'] = $lineitemid;
                            $rooms[$baseCounterDetails]['blockid'] = $blockid;
                            $rooms[$baseCounterDetails]['adults'] = $adults;
                            $rooms[$baseCounterDetails]['children'] = $children;
                            $rooms[$baseCounterDetails]['total'] = (double) $TotalPaymentAmountinclusive;
                            $rooms[$baseCounterDetails]['totalplain'] = (double) $TotalPaymentAmountinclusive;
                            $rooms[$baseCounterDetails]['nettotal'] = (double) $TotalPaymentAmountexclusive;
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
                            $rooms[$baseCounterDetails]['meal'] = $translator->translate($benefitArray[0]["Name"]);
                            $pricebreakdown = array();
                            $pricebreakdownCount = 0;
                            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                $amount = $TotalPaymentAmountinclusive / $noOfNights;
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
                            if ($PromotionName != "") {
                                $rooms[$baseCounterDetails]['special'] = true;
                                $rooms[$baseCounterDetails]['specialdescription'] = $PromotionName;
                            } else {
                                $rooms[$baseCounterDetails]['special'] = false;
                                $rooms[$baseCounterDetails]['specialdescription'] = "";
                            }

                            $rooms[$baseCounterDetails]['cancelpolicy'] = $policy;
                            $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = $policyArray[0]["before"];
                            //$rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $rooms[$baseCounterDetails]['cancelpolicy_deadline'];

                            $rooms[$baseCounterDetails]['currency'] = strtoupper($currency);
                            $baseCounterDetails ++;
                        //$agoda = true;
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
        $delete->from('quote_session_agoda');
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
        $insert->into('quote_session_agoda');
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