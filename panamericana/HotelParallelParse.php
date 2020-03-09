<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\n PANAMERICANA - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $parse = xml2array($response);
    $SequenceNmbr = $parse["soap:Envelope"]["soap:Body"]["OTA_HotelAvailServiceResponse"]["OTA_HotelAvailRS"]["attr"]["SequenceNmbr"];
    $parse = $parse["soap:Envelope"]["soap:Body"]["OTA_HotelAvailServiceResponse"]["OTA_HotelAvailRS"]["RoomStays"]["RoomStay"];
    $Code = $parse["BasicPropertyInfo"]["attr"]["HotelCode"];
    if ($parse["RoomRates"]["RoomRate"]["Total"]["attr"]["CurrencyCode"] != "") {
        $base_currency = $parse["RoomRates"]["RoomRate"]["Total"]["attr"]["CurrencyCode"];
    }
    // Removed / Before $val["RoomRates"]["RoomRate"]
    // foreach ($val["RoomRates"]["RoomRate"] as $keyRoomRate => $valRoomRate) {
    foreach ($parse["RoomRates"]["RoomRate"] as $keyRoomRate => $valRoomRate) {
        $NonRefundable = $valRoomRate["TPA_Extensions"]["NonRefundable"]["value"];
        $RatePlanCode = $valRoomRate["attr"]["RatePlanCode"];
        $AvailabilityStatus = $valRoomRate["attr"]["AvailabilityStatus"];
        $Board = $valRoomRate["attr"]["RatePlanCategory"];
        if ($valRoomRate["TPA_Extensions"]["RecommendedPrice"]["attr"]["CurrencyCode"] != "") {
            $base_currency = $valRoomRate["TPA_Extensions"]["RecommendedPrice"]["attr"]["CurrencyCode"];
        }
        if (is_array($valRoomRate["Rates"])) {
            foreach ($valRoomRate["Rates"] as $keyRates => $valRates) {
                if (is_array($valRoomRate["Features"]["Feature"])) {
                    if ($valRoomRate["Features"]["Feature"][0]["attr"]["RoomViewCode"] == "PROMO" or $valRoomRate["Features"]["Feature"][1]["attr"]["RoomViewCode"] == "PROMO" or $valRoomRate["Features"]["Feature"][2]["attr"]["RoomViewCode"] == "PROMO" or $valRoomRate["Features"]["Feature"][3]["attr"]["RoomViewCode"] == "PROMO") {
                        $special = true;
                        if ($valRoomRate["Features"]["Feature"][0]["attr"]["RoomViewCode"] == "PROMO") {
                            if ($valRoomRate["Features"]["Feature"][0]["Description"]["attr"]["Name"] == "VALUE") {
                                $specialdescription = $valRoomRate["Features"]["Feature"][0]["Description"]["Text"]["value"];
                            }
                            if ($specialdescription == "") {
                                if ($valRoomRate["Features"]["Feature"][0]["Description"][0]["attr"]["Name"] == "VALUE") {
                                    $specialdescription = $valRoomRate["Features"]["Feature"][0]["Description"][0]["Text"]["value"];
                                }
                                if ($specialdescription == "") {
                                    if ($valRoomRate["Features"]["Feature"][0]["Description"][1]["attr"]["Name"] == "DESC") {
                                        $specialdescription = $valRoomRate["Features"]["Feature"][0]["Description"][1]["Text"]["value"];
                                    }
                                }
                            }
                            if ($specialdescription == "NON REFUNDABLE" or $specialdescription == "Non-refundable rate. No amendments permitted") {
                                $specialdescription = "";
                                $special = false;
                            }
                        } elseif ($valRoomRate["Features"]["Feature"][1]["attr"]["RoomViewCode"] == "PROMO") {
                            if ($valRoomRate["Features"]["Feature"][1]["Description"]["attr"]["Name"] == "VALUE") {
                                $specialdescription = $valRoomRate["Features"]["Feature"][1]["Description"]["Text"]["value"];
                            }
                            if ($specialdescription == "") {
                                if ($valRoomRate["Features"]["Feature"][1]["Description"][0]["attr"]["Name"] == "VALUE") {
                                    $specialdescription = $valRoomRate["Features"]["Feature"][1]["Description"][0]["Text"]["value"];
                                }
                                if ($specialdescription == "") {
                                    if ($valRoomRate["Features"]["Feature"][1]["Description"][1]["attr"]["Name"] == "DESC") {
                                        $specialdescription = $valRoomRate["Features"]["Feature"][1]["Description"][1]["Text"]["value"];
                                    }
                                }
                            }
                            if ($specialdescription == "NON REFUNDABLE" or $specialdescription == "Non-refundable rate. No amendments permitted") {
                                $specialdescription = "";
                                $special = false;
                            }
                        } elseif ($valRoomRate["Features"]["Feature"][2]["attr"]["RoomViewCode"] == "PROMO") {
                            if ($valRoomRate["Features"]["Feature"][2]["Description"]["attr"]["Name"] == "VALUE") {
                                $specialdescription = $valRoomRate["Features"]["Feature"][2]["Description"]["Text"]["value"];
                            }
                            if ($specialdescription == "") {
                                if ($valRoomRate["Features"]["Feature"][2]["Description"][0]["attr"]["Name"] == "VALUE") {
                                    $specialdescription = $valRoomRate["Features"]["Feature"][2]["Description"][0]["Text"]["value"];
                                }
                                if ($specialdescription == "") {
                                    if ($valRoomRate["Features"]["Feature"][2]["Description"][1]["attr"]["Name"] == "DESC") {
                                        $specialdescription = $valRoomRate["Features"]["Feature"][2]["Description"][1]["Text"]["value"];
                                    }
                                }
                            }
                            if ($specialdescription == "NON REFUNDABLE" or $specialdescription == "Non-refundable rate. No amendments permitted") {
                                $specialdescription = "";
                                $special = false;
                            }
                        } elseif ($valRoomRate["Features"]["Feature"][3]["attr"]["RoomViewCode"] == "PROMO") {
                            if ($valRoomRate["Features"]["Feature"][3]["Description"]["attr"]["Name"] == "VALUE") {
                                $specialdescription = $valRoomRate["Features"]["Feature"][3]["Description"]["Text"]["value"];
                            }
                            if ($specialdescription == "") {
                                if ($valRoomRate["Features"]["Feature"][3]["Description"][0]["attr"]["Name"] == "VALUE") {
                                    $specialdescription = $valRoomRate["Features"]["Feature"][3]["Description"][0]["Text"]["value"];
                                }
                                if ($specialdescription == "") {
                                    if ($valRoomRate["Features"]["Feature"][3]["Description"][1]["attr"]["Name"] == "DESC") {
                                        $specialdescription = $valRoomRate["Features"]["Feature"][3]["Description"][1]["Text"]["value"];
                                    }
                                }
                            }
                            if ($specialdescription == "NON REFUNDABLE" or $specialdescription == "Non-refundable rate. No amendments permitted") {
                                $specialdescription = "";
                                $special = false;
                            }
                        } else {
                            $specialdescription = "";
                            $special = false;
                        }
                    } else {
                        $specialdescription = "";
                        $special = false;
                    }
                } else {
                    $specialdescription = "";
                    $special = false;
                }
                
                if (! is_array($valRates[0])) {
                    $t1[0] = $valRates;
                    $valRates = $t1;
                }
                if (is_array($valRates)) {
                    foreach ($valRates as $keyRate => $valRate) {
                        $Name = $valRate["RateDescription"]["Text"]["value"];
                        if ($Name != "") {
                            $Units = $valRate["attr"]["NumberOfUnits"];
                            $RateSource = $valRate["attr"]["RateSource"];
                            $Total = $valRate["Total"]["attr"]["AmountAfterTax"];
                            if ($base_currency == "") {
                                if ($valRate["Total"]["attr"]["CurrencyCode"] != "") {
                                    $base_currency = $valRate["Total"]["attr"]["CurrencyCode"];
                                }
                            }
                            if ($base_currency == "" and $panamericanaCurrencyCode != "") {
                                $base_currency = $panamericanaCurrencyCode;
                            }
                            $nettotal = $valRate["Total"]["attr"]["AmountAfterTax"];
                            $Name = str_replace("&lt;p&gt;", "", $Name);
                            $Name = str_replace("&amp;", " ", $Name);

                            $rooms[$baseCounterDetails]['hotelid'] = $Code;
                            $rooms[$baseCounterDetails]['shid'] = $Code;
                            if ($AvailabilityStatus == "AvailableForSale") {
                                $rooms[$baseCounterDetails]['status'] = 1;
                            } else {
                                $rooms[$baseCounterDetails]['status'] = 4;
                            }
                            $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-69";
                            $rooms[$baseCounterDetails]['room'] = substr($Name, 0, 150);
                            $rooms[$baseCounterDetails]['smbr'] = $SequenceNmbr;
                            $rooms[$baseCounterDetails]['rid'] = $RatePlanCode;
                            $rooms[$baseCounterDetails]['adults'] = $adults;
                            $rooms[$baseCounterDetails]['children'] = $children;
                            $rooms[$baseCounterDetails]['nettotal'] = (double) $nettotal;
                            if ($panamericanaMarkup != 0) {
                                $Total = $Total + (($Total * $panamericanaMarkup) / 100);
                            }
                            // Geo target markup
                            if ($internalmarkup != 0) {
                                $Total = $Total + (($Total * $internalmarkup) / 100);
                            }
                            // Agent markup
                            if ($agent_markup != 0) {
                                $Total = $Total + (($Total * $agent_markup) / 100);
                            }
                            // Fallback Markup
                            if ($panamericanaMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                $Total = $Total + (($Total * $HotelsMarkupFallback) / 100);
                            }
                            // Agent discount
                            if ($agent_discount != 0) {
                                $Total = $Total - (($Total * $agent_discount) / 100);
                            }
                            if ($scurrency != "" and $currency != $scurrency) {
                                $Total = $CurrencyConverter->convert($Total, $currency, $scurrency);
                            }
                            $rooms[$baseCounterDetails]['total'] = (double) $Total;
                            $rooms[$baseCounterDetails]['totalplain'] = (double) $Total;
                            if ($Board == '') {
                                $rooms[$baseCounterDetails]['meal'] = $translator->translate("Room Only");
                            } else {
                                try {
                                    $sql = "select mapped from board_mapping where description='" . addslashes($Board) . "'";
                                    $statement = $db->createStatement($sql);
                                    $statement->prepare();
                                    $row_board_mapping = $statement->execute();
                                    $row_board_mapping->buffer();
                                    if ($row_board_mapping->valid()) {
                                        $row_board_mapping = $row_board_mapping->current();
                                        $Board = $row_board_mapping["mapped"];
                                    }
                                } catch (\Exception $e) {
                                    $logger = new Logger();
                                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                    $logger->addWriter($writer);
                                    $logger->info($e->getMessage());
                                }
                                $rooms[$baseCounterDetails]['meal'] = $translator->translate($Board);
                            }
                            $pricebreakdown = array();
                            $pricebreakdownCount = 0;
                            $amount = $Total / $noOfNights;
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
                            if ($special == true) {
                                $rooms[$baseCounterDetails]['special'] = true;
                                $rooms[$baseCounterDetails]['specialdescription'] = $specialdescription;
                            } else {
                                $rooms[$baseCounterDetails]['special'] = false;
                                $rooms[$baseCounterDetails]['specialdescription'] = "";
                            }
                            //
                            //Nodeup
                            //
                            if ($rooms > 1) {
                                $rooms[$baseCounterDetails]['nodedup'] = true;
                            } else {
                                $rooms[$baseCounterDetails]['nodedup'] = false;
                            }                              
                            //
                            // Cancellation policies
                            //
                            if ($NonRefundable == 1) {
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
        }
    }

    //
    // Store Session
    //
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_panamericana');
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
    $insert->into('quote_session_panamericana');
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