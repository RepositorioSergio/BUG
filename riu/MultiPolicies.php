<?php
use Zend\Http\Client;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$failed = false;
$valid = 0;
$hid = 0;
$newnet = 0;
$shid = 0;
$sindex = $index;
if ($details == "hoteldetails") {
    // Detail level
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_riu where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_riu where session_id='$session_id'";
}
$db = new \Zend\Db\Adapter\Adapter($config);
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $xmlrequest = $row_settings["xmlrequest"];
    $xmlresult = $row_settings["xmlresult"];
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $lang = $searchsettings['lang'];
    $currency = $searchsettings['currency'];
    $from = $searchsettings['from'];
    $to = $searchsettings['to'];
    $destination = $searchsettings['destination'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    $index = $searchsettings['index'];
    $ipaddress = $searchsettings['ipaddress'];
    $nationality = $searchsettings['nationality'];
    $residency = $searchsettings['residency'];
    $rooms = $searchsettings['rooms'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_riu = $affiliate_id;
} else {
    $affiliate_id_riu = 0;
}
$sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $riuServiceURL = $row['value'];
}
$sql = "select value from settings where name='riuCommission' and affiliate_id=$affiliate_id_riu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $riuCommission = $row['value'];
} else {
    $riuCommission = 0;
}
$sql = "select value from settings where name='riuMarkup' and affiliate_id=$affiliate_id_riu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuMarkup = (double) $row_settings['value'];
} else {
    $riuMarkup = 0;
}
$sql = "select value from settings where name='riuPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_riu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sourceMarket = $row_settings['value'];
}

$outputArray = array();
$arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
foreach ($arrIt as $sub) {
    $subArray = $arrIt->getSubIterator();
    if (isset($quoteid[$nroom])) {
        if (isset($subArray['quoteid'])) {
            if ($subArray['quoteid'] === $quoteid[$nroom]) {
                $outputArray[] = iterator_to_array($subArray);
                $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                    ->key();
            }
        }
    }
}
$breakdownTmp = array();
if (! is_array($outputArray)) {
    $response['error'] = "Unable to handle request #3";
    return false;
} else {
    array_push($breakdownTmp, $outputArray);
}
$fromRIU = DateTime::createFromFormat("d-m-Y", $from);
$toRIU = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromRIU->diff($toRIU);
$nights = $nights->format('%R%a');
$fromRIU = $fromRIU->getTimestamp();
$toRIU = $toRIU->getTimestamp();
$c = $nroom;
$response = array();
$roombreakdown2 = array();
foreach ($breakdownTmp as $k => $v) {
    foreach ($v as $key => $value) {
        $shid = $value['shid'];
        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";
        $newnet = $value['nettotal'];
        $JSESSIONID = $value['JSESSIONID'];
        $ratehotel = $value['ratehotel'];
        $quotetype = $value['quotetype'];
        $mealplan = $value['mealplan'];
        $promocode = $value['promocode'];
        $RoomTypeCode = $value['roomtypecode'];
        $adults = $value['adults'];
        $children = $value['children'];
        $infants = $value['infants'];
        $rooms = 1;
        $raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><soap:Body><HotelBookingRule xmlns="http://services.enginexml.rumbonet.riu.com"><in0 xmlns="http://services.enginexml.rumbonet.riu.com"><AdultsCount xmlns="http://dtos.enginexml.rumbonet.riu.com">' . $adults . '</AdultsCount><ChildCount xmlns="http://dtos.enginexml.rumbonet.riu.com">' . $children . '</ChildCount><CountryCode xmlns="http://dtos.enginexml.rumbonet.riu.com">' . $sourceMarket . '</CountryCode><HotelID xmlns="http://dtos.enginexml.rumbonet.riu.com">' . $shid . '</HotelID><HotelReservationCode xmlns="http://dtos.enginexml.rumbonet.riu.com" xsi:nil="true" /><HotelReservationID xmlns="http://dtos.enginexml.rumbonet.riu.com">0</HotelReservationID><InfantsCount xmlns="http://dtos.enginexml.rumbonet.riu.com">0</InfantsCount><Language xmlns="http://dtos.enginexml.rumbonet.riu.com">US</Language><MealPlan xmlns="http://dtos.enginexml.rumbonet.riu.com">' . $mealplan . '</MealPlan><promocode xmlns="http://dtos.enginexml.rumbonet.riu.com">' . $promocode . '</promocode><quoteType xmlns="http://dtos.enginexml.rumbonet.riu.com">' . $quotetype . '</quoteType><RateHotel xmlns="http://dtos.enginexml.rumbonet.riu.com">' . $ratehotel . '</RateHotel><rateReference xmlns="http://dtos.enginexml.rumbonet.riu.com" xsi:nil="true" /><RoomList xmlns="http://dtos.enginexml.rumbonet.riu.com"><RoomConfig><RoomStayCandidateRule><AdultsCount>' . $adults . '</AdultsCount>';
        if ($children > 0) {
            $raw .= '<Ages>';
            for ($z = 0; $z < $children; $z ++) {
                $raw .= '<ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">' . $children_ages[$z] . '</ns2:int>';
            }
            $raw .= '</Ages>';
        } else {
            $raw .= '<Ages xsi:nil="true" />';
        }
        $raw .= '<ChildCount>' . $children . '</ChildCount><InfantsCount>0</InfantsCount><RoomTypeCode>' . $RoomTypeCode . '</RoomTypeCode></RoomStayCandidateRule></RoomConfig></RoomList><RoomsCount xmlns="http://dtos.enginexml.rumbonet.riu.com">' . $rooms . '</RoomsCount><StayDateStart xmlns="http://dtos.enginexml.rumbonet.riu.com">' . strftime("%Y%m%d", $fromRIU) . '</StayDateStart><StayDateEnd xmlns="http://dtos.enginexml.rumbonet.riu.com">' . strftime("%Y%m%d", $toRIU) . '</StayDateEnd></in0></HotelBookingRule></soap:Body></soap:Envelope>';
        
        $client = new Client();
        $client->setOptions(array(
            'timeout' => 100,
            'sslverifypeer' => false,
            'sslverifyhost' => false
        ));
        $client->setHeaders(array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: " . strlen($raw)
        ));
        $client->setUri($riuServiceURL);
        $client->setMethod('POST');
        $client->setCookies(array(
            'JSESSIONID' => $JSESSIONID
        ));
        $client->setRawBody($raw);
        $response2 = $client->send();
        if ($response2->isSuccess()) {
            $response2 = $response2->getBody();
        } else {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($client->getUri());
            $logger->info($response2->getStatusCode() . " - " . $response2->getReasonPhrase());
            $failed = true;
        } 
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_riu');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $riuServiceURL . $raw,
                'sqlcontext' => $response2,
                'errcontext' => ''
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        $CPdays = "";
        $CPreleaseDays = "";
        $vector = array();
        if ($failed == false) {
            $inputDoc = new DOMDocument();
            $inputDoc->loadXML($response2);
            $HotelBookingRuleResponse = $inputDoc->getElementsByTagName("HotelBookingRuleResponse");
            if ($HotelBookingRuleResponse->length > 0) {
                $HotelBookingRuleResponse2 = $HotelBookingRuleResponse->item(0)->getElementsByTagName("HotelBookingRuleResponse");
                if ($HotelBookingRuleResponse2->length > 0) {
                    $bookingRule = $HotelBookingRuleResponse2->item(0)->getElementsByTagName("bookingRule");
                    if ($bookingRule->length > 0) {
                        $adultsCount = $bookingRule->item(0)->getElementsByTagName("adultsCount");
                        if ($adultsCount->length > 0) {
                            $adultsCount = $adultsCount->item(0)->nodeValue;
                        } else {
                            $adultsCount = "";
                        }
                        $bookingAmount = $bookingRule->item(0)->getElementsByTagName("bookingAmount");
                        if ($bookingAmount->length > 0) {
                            $bookingAmount = $bookingAmount->item(0)->nodeValue;
                        } else {
                            $bookingAmount = "";
                        }
                        $childCount = $bookingRule->item(0)->getElementsByTagName("childCount");
                        if ($childCount->length > 0) {
                            $childCount = $childCount->item(0)->nodeValue;
                        } else {
                            $childCount = "";
                        }
                        $currencyCode = $bookingRule->item(0)->getElementsByTagName("currencyCode");
                        if ($currencyCode->length > 0) {
                            $currencyCode = $currencyCode->item(0)->nodeValue;
                        } else {
                            $currencyCode = "";
                        }
                        $hotelID = $bookingRule->item(0)->getElementsByTagName("hotelID");
                        if ($hotelID->length > 0) {
                            $hotelID = $hotelID->item(0)->nodeValue;
                        } else {
                            $hotelID = "";
                        }
                        $impPromocode = $bookingRule->item(0)->getElementsByTagName("impPromocode");
                        if ($impPromocode->length > 0) {
                            $impPromocode = $impPromocode->item(0)->nodeValue;
                        } else {
                            $impPromocode = "";
                        }
                        $infantsCount = $bookingRule->item(0)->getElementsByTagName("infantsCount");
                        if ($infantsCount->length > 0) {
                            $infantsCount = $infantsCount->item(0)->nodeValue;
                        } else {
                            $infantsCount = "";
                        }
                        $mealPlan = $bookingRule->item(0)->getElementsByTagName("mealPlan");
                        if ($mealPlan->length > 0) {
                            $mealPlan = $mealPlan->item(0)->nodeValue;
                        } else {
                            $mealPlan = "";
                        }
                        $numDays = $bookingRule->item(0)->getElementsByTagName("numDays");
                        if ($numDays->length > 0) {
                            $numDays = $numDays->item(0)->nodeValue;
                        } else {
                            $numDays = "";
                        }
                        $promocode = $bookingRule->item(0)->getElementsByTagName("promocode");
                        if ($promocode->length > 0) {
                            $promocode = $promocode->item(0)->nodeValue;
                        } else {
                            $promocode = "";
                        }
                        $quoteType = $bookingRule->item(0)->getElementsByTagName("quoteType");
                        if ($quoteType->length > 0) {
                            $quoteType = $quoteType->item(0)->nodeValue;
                        } else {
                            $quoteType = "";
                        }
                        $rateHotel = $bookingRule->item(0)->getElementsByTagName("rateHotel");
                        if ($rateHotel->length > 0) {
                            $rateHotel = $rateHotel->item(0)->nodeValue;
                        } else {
                            $rateHotel = "";
                        }
                        $rateReference = $bookingRule->item(0)->getElementsByTagName("rateReference");
                        if ($rateReference->length > 0) {
                            $rateReference = $rateReference->item(0)->nodeValue;
                        } else {
                            $rateReference = "";
                        }
                        $rateType = $bookingRule->item(0)->getElementsByTagName("rateType");
                        if ($rateType->length > 0) {
                            $rateType = $rateType->item(0)->nodeValue;
                        } else {
                            $rateType = "";
                        }
                        $roomsCount = $bookingRule->item(0)->getElementsByTagName("roomsCount");
                        if ($roomsCount->length > 0) {
                            $roomsCount = $roomsCount->item(0)->nodeValue;
                        } else {
                            $roomsCount = "";
                        }
                        $stayDateEnd = $bookingRule->item(0)->getElementsByTagName("stayDateEnd");
                        if ($stayDateEnd->length > 0) {
                            $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
                        } else {
                            $stayDateEnd = "";
                        }
                        $stayDateStart = $bookingRule->item(0)->getElementsByTagName("stayDateStart");
                        if ($stayDateStart->length > 0) {
                            $stayDateStart = $stayDateStart->item(0)->nodeValue;
                        } else {
                            $stayDateStart = "";
                        }
                        $typePrice = $bookingRule->item(0)->getElementsByTagName("typePrice");
                        if ($typePrice->length > 0) {
                            $typePrice = $typePrice->item(0)->nodeValue;
                        } else {
                            $typePrice = "";
                        }
                        $bookingRulePenalties = $bookingRule->item(0)->getElementsByTagName("bookingRulePenalties");
                        if ($bookingRulePenalties->length > 0) {
                            $cancelPenalties = $bookingRulePenalties->item(0)->getElementsByTagName("cancelPenalties");
                            if ($cancelPenalties->length > 0) {
                                $CPamount = $cancelPenalties->item(0)->getElementsByTagName("amount");
                                if ($CPamount->length > 0) {
                                    $CPamount = $CPamount->item(0)->nodeValue;
                                } else {
                                    $CPamount = "";
                                }
                                $CPdays = $cancelPenalties->item(0)->getElementsByTagName("days");
                                if ($CPdays->length > 0) {
                                    $CPdays = $CPdays->item(0)->nodeValue;
                                } else {
                                    $CPdays = "";
                                }
                                $CPpercent = $cancelPenalties->item(0)->getElementsByTagName("percent");
                                if ($CPpercent->length > 0) {
                                    $CPpercent = $CPpercent->item(0)->nodeValue;
                                } else {
                                    $CPpercent = "";
                                }
                                $CPreleaseDays = $cancelPenalties->item(0)->getElementsByTagName("releaseDays");
                                if ($CPreleaseDays->length > 0) {
                                    $CPreleaseDays = $CPreleaseDays->item(0)->nodeValue;
                                } else {
                                    $CPreleaseDays = "";
                                }
                                $CPtotalAmount = $cancelPenalties->item(0)->getElementsByTagName("totalAmount");
                                if ($CPtotalAmount->length > 0) {
                                    $CPtotalAmount = $CPtotalAmount->item(0)->nodeValue;
                                } else {
                                    $CPtotalAmount = "";
                                }
                            }
                            // modificationPenalties
                            $modificationPenalties = $bookingRulePenalties->item(0)->getElementsByTagName("modificationPenalties");
                            if ($modificationPenalties->length > 0) {
                                $MPamount = $modificationPenalties->item(0)->getElementsByTagName("amount");
                                if ($MPamount->length > 0) {
                                    $MPamount = $MPamount->item(0)->nodeValue;
                                } else {
                                    $MPamount = "";
                                }
                                $MPdays = $modificationPenalties->item(0)->getElementsByTagName("days");
                                if ($MPdays->length > 0) {
                                    $MPdays = $MPdays->item(0)->nodeValue;
                                } else {
                                    $MPdays = "";
                                }
                                $MPpercent = $modificationPenalties->item(0)->getElementsByTagName("percent");
                                if ($MPpercent->length > 0) {
                                    $MPpercent = $MPpercent->item(0)->nodeValue;
                                } else {
                                    $MPpercent = "";
                                }
                                $MPreleaseDays = $modificationPenalties->item(0)->getElementsByTagName("releaseDays");
                                if ($MPreleaseDays->length > 0) {
                                    $MPreleaseDays = $MPreleaseDays->item(0)->nodeValue;
                                } else {
                                    $MPreleaseDays = "";
                                }
                                $MPtotalAmount = $modificationPenalties->item(0)->getElementsByTagName("totalAmount");
                                if ($MPtotalAmount->length > 0) {
                                    $MPtotalAmount = $MPtotalAmount->item(0)->nodeValue;
                                } else {
                                    $MPtotalAmount = "";
                                }
                            }
                            // noShowPenalties
                            $noShowPenalties = $bookingRulePenalties->item(0)->getElementsByTagName("noShowPenalties");
                            if ($noShowPenalties->length > 0) {
                                $SPamount = $noShowPenalties->item(0)->getElementsByTagName("amount");
                                if ($SPamount->length > 0) {
                                    $SPamount = $SPamount->item(0)->nodeValue;
                                } else {
                                    $SPamount = "";
                                }
                                $SPdays = $noShowPenalties->item(0)->getElementsByTagName("days");
                                if ($SPdays->length > 0) {
                                    $SPdays = $SPdays->item(0)->nodeValue;
                                } else {
                                    $SPdays = "";
                                }
                                $SPpercent = $noShowPenalties->item(0)->getElementsByTagName("percent");
                                if ($SPpercent->length > 0) {
                                    $SPpercent = $SPpercent->item(0)->nodeValue;
                                } else {
                                    $SPpercent = "";
                                }
                                $SPreleaseDays = $noShowPenalties->item(0)->getElementsByTagName("releaseDays");
                                if ($SPreleaseDays->length > 0) {
                                    $SPreleaseDays = $SPreleaseDays->item(0)->nodeValue;
                                } else {
                                    $SPreleaseDays = "";
                                }
                                $SPtotalAmount = $noShowPenalties->item(0)->getElementsByTagName("totalAmount");
                                if ($SPtotalAmount->length > 0) {
                                    $SPtotalAmount = $SPtotalAmount->item(0)->nodeValue;
                                } else {
                                    $SPtotalAmount = "";
                                }
                            }
                        }
                        $roomStayList = $bookingRule->item(0)->getElementsByTagName("roomStayList");
                        if ($roomStayList->length > 0) {
                            $RoomStay = $roomStayList->item(0)->getElementsByTagName("RoomStay");
                            if ($RoomStay->length > 0) {
                                $mealPlanSupplementPeriodsList = $RoomStay->item(0)->getElementsByTagName("mealPlanSupplementPeriodsList");
                                if ($mealPlanSupplementPeriodsList->length > 0) {
                                    $mealPlanSupplementPeriodsList = $mealPlanSupplementPeriodsList->item(0)->nodeValue;
                                } else {
                                    $mealPlanSupplementPeriodsList = "";
                                }
                                $pricesPeriodsList = $RoomStay->item(0)->getElementsByTagName("pricesPeriodsList");
                                if ($pricesPeriodsList->length > 0) {
                                    $pricesPeriodsList = $pricesPeriodsList->item(0)->nodeValue;
                                } else {
                                    $pricesPeriodsList = "";
                                }
                                $roomAmount = $RoomStay->item(0)->getElementsByTagName("roomAmount");
                                if ($roomAmount->length > 0) {
                                    $roomAmount = $roomAmount->item(0)->nodeValue;
                                } else {
                                    $roomAmount = "";
                                }
                                $roomNumber = $RoomStay->item(0)->getElementsByTagName("roomNumber");
                                if ($roomNumber->length > 0) {
                                    $roomNumber = $roomNumber->item(0)->nodeValue;
                                } else {
                                    $roomNumber = "";
                                }
                                $roomTypeCode = $RoomStay->item(0)->getElementsByTagName("roomTypeCode");
                                if ($roomTypeCode->length > 0) {
                                    $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
                                } else {
                                    $roomTypeCode = "";
                                }
                                $roomGuestsList = $RoomStay->item(0)->getElementsByTagName("roomGuestsList");
                                if ($roomGuestsList->length > 0) {
                                    $RoomGuests = $roomGuestsList->item(0)->getElementsByTagName("RoomGuests");
                                    if ($RoomGuests->length > 0) {
                                        for ($k = 0; $k < $RoomGuests->length; $k ++) {
                                            $age = $RoomGuests->item($k)->getElementsByTagName("age");
                                            if ($age->length > 0) {
                                                $age = $age->item(0)->nodeValue;
                                            } else {
                                                $age = "";
                                            }
                                            $guestNumber = $RoomGuests->item($k)->getElementsByTagName("guestNumber");
                                            if ($guestNumber->length > 0) {
                                                $guestNumber = $guestNumber->item(0)->nodeValue;
                                            } else {
                                                $guestNumber = "";
                                            }
                                            $typeGuestCode = $RoomGuests->item($k)->getElementsByTagName("typeGuestCode");
                                            if ($typeGuestCode->length > 0) {
                                                $typeGuestCode = $typeGuestCode->item(0)->nodeValue;
                                            } else {
                                                $typeGuestCode = "";
                                            }
                                            $pricePeriodsList = $RoomGuests->item($k)->getElementsByTagName("pricePeriodsList");
                                            if ($pricePeriodsList->length > 0) {
                                                $PricePeriods = $pricePeriodsList->item(0)->getElementsByTagName("PricePeriods");
                                                if ($PricePeriods->length > 0) {
                                                    $amount = $PricePeriods->item(0)->getElementsByTagName("amount");
                                                    if ($amount->length > 0) {
                                                        $amount = $amount->item(0)->nodeValue;
                                                    } else {
                                                        $amount = "";
                                                    }
                                                    $amountPerNight = $PricePeriods->item(0)->getElementsByTagName("amountPerNight");
                                                    if ($amountPerNight->length > 0) {
                                                        $amountPerNight = $amountPerNight->item(0)->nodeValue;
                                                    } else {
                                                        $amountPerNight = "";
                                                    }
                                                    $stayDateEnd = $PricePeriods->item(0)->getElementsByTagName("stayDateEnd");
                                                    if ($stayDateEnd->length > 0) {
                                                        $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
                                                    } else {
                                                        $stayDateEnd = "";
                                                    }
                                                    $stayDateStart = $PricePeriods->item(0)->getElementsByTagName("stayDateStart");
                                                    if ($stayDateStart->length > 0) {
                                                        $stayDateStart = $stayDateStart->item(0)->nodeValue;
                                                    } else {
                                                        $stayDateStart = "";
                                                    }
                                                    $valueTmp = $PricePeriods->item(0)->getElementsByTagName("value");
                                                    if ($valueTmp->length > 0) {
                                                        $valueTmp = $valueTmp->item(0)->nodeValue;
                                                    } else {
                                                        $valueTmp = "";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $amountsList = $bookingRule->item(0)->getElementsByTagName("amountsList");
                        if ($amountsList->length > 0) {
                            $Amounts = $amountsList->item(0)->getElementsByTagName("Amounts");
                            if ($Amounts->length > 0) {
                                for ($j = 0; $j < $Amounts->length; $j ++) {
                                    $concept = $Amounts->item($j)->getElementsByTagName("concept");
                                    if ($concept->length > 0) {
                                        $concept = $concept->item(0)->nodeValue;
                                    } else {
                                        $concept = "";
                                    }
                                    $netAmount = $Amounts->item($j)->getElementsByTagName("netAmount");
                                    if ($netAmount->length > 0) {
                                        $netAmount = $netAmount->item(0)->nodeValue;
                                    } else {
                                        $netAmount = "";
                                    }
                                    $quote = $Amounts->item($j)->getElementsByTagName("quote");
                                    if ($quote->length > 0) {
                                        $quote = $quote->item(0)->nodeValue;
                                    } else {
                                        $quote = "";
                                    }
                                    $taxesList = $Amounts->item($j)->getElementsByTagName("taxesList");
                                    if ($taxesList->length > 0) {
                                        $taxesList = $taxesList->item(0)->nodeValue;
                                    } else {
                                        $taxesList = "";
                                    }
                                    $totalAmount = $Amounts->item($j)->getElementsByTagName("totalAmount");
                                    if ($totalAmount->length > 0) {
                                        $totalAmount = $totalAmount->item(0)->nodeValue;
                                    } else {
                                        $totalAmount = "";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $pricebreakdown = array();
        $pricebreakdownCount = 0;
        for ($rZZ = 0; $rZZ < $nights; $rZZ ++) {
            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
            $amount = $bookingAmount / $nights;
            if ($riuMarkup != 0) {
                $amount = $amount + (($amount * $riuMarkup) / 100);
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
            if ($riuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
            }
            // Agent discount
            if ($agent_discount != 0) {
                $amount = $amount - (($amount * $agent_discount) / 100);
            }
            if ($scurrency != "" and $currency != $scurrency) {
                $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
            }
            $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
            $pricebreakdownCount = $pricebreakdownCount + 1;
        }
        // error_log("\r\nDays: $CPdays\r\n", 3, "/srv/www/htdocs/error_log");
        if ($CPreleaseDays == "") {
            $cancelation_deadline = time();
            $item['nonrefundable'] = true;
        } else {
            $cancelation_deadline = mktime(0, 0, 0, date("n", $fromRIU), date("j", $fromRIU) - (int) $CPreleaseDays, date("Y", $fromRIU));
            if (time() > $cancelation_deadline) {
                $cancelation_deadline = time();
                $item['nonrefundable'] = true;
            } else {
                $item['nonrefundable'] = false;
            }
            $cancelation_details = $translator->translate("Charge") . " " . number_format($CPtotalAmount, 2, '.', '') . " " . $translator->translate("if cancelled on or after") . " " . strftime("%a, %e %b %Y", $cancelation_deadline);
        }
        // error_log("\r\n$cancelation_details - " . strftime("%m-%d-%Y", $cancelation_deadline) . "\r\n", 3, "/srv/www/htdocs/error_log");
        $newnet = $bookingAmount;
        if ($riuCommission > 0) {
            $com = $bookingAmount - (($bookingAmount * $riuCommission) / 100);
            if (($bookingAmount - $com) > $value['total']) {
                $newnet = $bookingAmount;
                $total = $newnet;
            } else {
                $total = $value['total'];
            }
        } else {
            if ($bookingAmount > $value['total']) {
                $newnet = $bookingAmount;
                $total = $newnet;
            } else {
                $total = $value['total'];
            }
        }
        $tot = $total;
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        $item['total'] = $filter->filter($tot);
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $item['subtotal'] = $filter->filter(floatval($tot));
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        $item['cancelpolicy'] = $cancelation_details;
        $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $cancelation_deadline);
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        $item['cancelpolicy_details'] = $cancelation_details;
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mriu where sid='" . $shid . "' and hid=" . $hid;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
$row_hotel->buffer();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel->buffer();
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $row_country->buffer();
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result instanceof ResultInterface && $result->isQueryResult()) {
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        foreach ($resultSet as $row) {
            $item = array();
            $item['url'] = "//world-wide-web-servers.com/static/hotels/" . $row->url;
            $item['description'] = $row->description;
            array_push($images, $item);
        }
    }
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown2;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
$response['newnet'] = $newnet;
// Store Session
$sql = new Sql($db);
$sql = "delete from quote_session_hotel_multipolicies where session_id='" . $session_id . "' and sindex=$sindex";
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('quote_session_hotel_multipolicies');
$insert->values(array(
    'session_id' => $session_id,
    'sindex' => $sindex,
    'data' => base64_encode(serialize($response)),
    'searchsettings' => base64_encode(serialize($searchsettings))
), $insert::VALUES_MERGE);
try {
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['breakdown'] = $roombreakdown;
?>