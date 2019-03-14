<?php
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
if (file_exists("src/App/language/" . $lang . ".mo")) {
    $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
}
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$db = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_majestic where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
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
    $adt = $searchsettings['adt'];
    $chd = $searchsettings['chd'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$sql = "select value from settings where name='enablemajesticusa' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_majestic = $affiliate_id;
} else {
    $affiliate_id_majestic = 0;
}

$sql = "select value from settings where name='majesticusaLoginEmail' and affiliate_id=$affiliate_id_majestic" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='majesticusaPassword' and affiliate_id=$affiliate_id_majestic" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='majesticusaMarkup' and affiliate_id=$affiliate_id_majestic" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaMarkup = (double) $row_settings['value'];
} else {
    $majesticusaMarkup = 0;
}
$sql = "select value from settings where name='majesticusaServiceURL' and affiliate_id=$affiliate_id_majestic" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaServiceURL = $row_settings['value'];
}
$breakdown = array();
for ($w = 0; $w < count($quoteid); $w ++) {
    $outputArray = array();
    $arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
    foreach ($arrIt as $sub) {
        $subArray = $arrIt->getSubIterator();
        if (isset($quoteid[$w])) {
            if (isset($subArray['quoteid'])) {
                if ($subArray['quoteid'] === $quoteid[$w]) {
                    $outputArray[] = iterator_to_array($subArray);
                    $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                        ->key();
                }
            }
        }
    }
    if (! is_array($outputArray)) {
        $response['error'] = "Unable to handle request #3";
        return false;
    } else {
        array_push($breakdown, $outputArray);
    }
}

$fromHotelbeds = DateTime::createFromFormat("d-m-Y", $from);
$toHotelbeds = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelbeds->diff($toHotelbeds);
$nights = $nights->format('%R%a');
$fromHotelbeds = $fromHotelbeds->getTimestamp();
$toHotelbeds = $toHotelbeds->getTimestamp();
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $HotelId = $value['HotelId'];
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        
        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";
        $BookParam = $value['BookParam'];
        $OptionID = $value['optionid'];
        $roomID = $value['roomid'];
        $maxpersons = $value['maxpersons'];
        $roomQty = $value['roomQty'];
        $accid = $value['accid'];
        $date_arrival = date('Y-m-d', strtotime($from));
        $date_departure = date('Y-m-d', strtotime($to));
        $raw = '<?xml version="1.0" encoding="utf-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><SOAP-ENV:Header><m:AuthHeader xmlns:m="http://www.majesticusa.com/majesticweb_xml/"><m:Username>' . $majesticusaLoginEmail . '</m:Username><m:Password>' . $majesticusaPassword . '</m:Password></m:AuthHeader></SOAP-ENV:Header><SOAP-ENV:Body>
        <m:BookHotelPreviewV1 xmlns:m="http://www.majesticusa.com/majesticweb_xml/">
            <m:BookParam>' . $BookParam . '</m:BookParam>
            <m:arrival>' . $date_arrival . 'T11:00:00.0Z</m:arrival>
            <m:departure>' . $date_departure . 'T11:00:00.0Z</m:departure>
            <m:hotelid>' . $HotelId . '</m:hotelid>
            <m:roomid>' . $roomID . '</m:roomid>
            <m:optionid>' . $OptionID . '</m:optionid>
            <m:accomodationid>' . $accid . '</m:accomodationid>
            <m:maxpax>' . $maxpersons . '</m:maxpax>
            <m:roomqty>' . $roomQty . '</m:roomqty>
            <m:paxlist>
                <m:adults>' . $selectedAdults[$c] . '</m:adults>
                <m:child>' . $selectedChildren[$c] . '</m:child>
            </m:paxlist>
        </m:BookHotelPreviewV1></SOAP-ENV:Body></SOAP-ENV:Envelope>';
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $majesticusaServiceURL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept-Encoding: gzip, deflate",
            "Accept: application/xml",
            "Content-type: text/xml",
            "Content-length: " . strlen($raw),
            "SOAPAction: http://www.majesticusa.com/majesticweb_xml/BookHotelPreviewV1"
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseCurl = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_majestic');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $$majesticusaServiceURL . $raw,
                'sqlcontext' => $responseCurl,
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
        $vector = array();
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($responseCurl);
        $Envelope = $inputDoc->getElementsByTagName('Envelope');
        $Body = $Envelope->item(0)->getElementsByTagName('Body');
        $BookHotelPreviewV1Response = $Body->item(0)->getElementsByTagName('BookHotelPreviewV1Response');
        $BookHotelPreviewV1Result = $BookHotelPreviewV1Response->item(0)->getElementsByTagName('BookHotelPreviewV1Result');
        $Root = $BookHotelPreviewV1Result->item(0)->getElementsByTagName('Root');
        $node = $Root->item(0)->getElementsByTagName('BookingPreview');
        if ($node->length > 0) {
            $Status = $node->item(0)->getElementsByTagName('Status');
            if ($Status->length > 0) {
                $Status = $Status->item(0)->nodeValue;
            } else {
                $Status = "";
            }
            // HotelData
            $HotelData = $node->item(0)->getElementsByTagName('HotelData');
            if ($HotelData->length > 0) {
                $Hotelid = $HotelData->item(0)->getElementsByTagName('Hotelid');
                if ($Hotelid->length > 0) {
                    $Hotelid = $Hotelid->item(0)->nodeValue;
                } else {
                    $Hotelid = "";
                }
                $Room = $HotelData->item(0)->getElementsByTagName('Room');
                if ($Room->length > 0) {
                    $Room = $Room->item(0)->nodeValue;
                } else {
                    $Room = "";
                }
                $Accomodation = $HotelData->item(0)->getElementsByTagName('Accomodation');
                if ($Accomodation->length > 0) {
                    $Accomodation = $Accomodation->item(0)->nodeValue;
                } else {
                    $Accomodation = "";
                }
                $SpecialNotes = $HotelData->item(0)->getElementsByTagName('SpecialNotes');
                if ($SpecialNotes->length > 0) {
                    $SpecialNotes = $SpecialNotes->item(0)->nodeValue;
                } else {
                    $SpecialNotes = "";
                }
            }
            // Dates
            $Dates = $node->item(0)->getElementsByTagName('Dates');
            if ($Dates->length > 0) {
                $Arrival = $Dates->item(0)->getElementsByTagName('Arrival');
                if ($Arrival->length > 0) {
                    $Arrival = $Arrival->item(0)->nodeValue;
                } else {
                    $Arrival = "";
                }
                $Departure = $Dates->item(0)->getElementsByTagName('Departure');
                if ($Departure->length > 0) {
                    $Departure = $Departure->item(0)->nodeValue;
                } else {
                    $Departure = "";
                }
                $Nights = $Dates->item(0)->getElementsByTagName('Nights');
                if ($Nights->length > 0) {
                    $Nights = $Nights->item(0)->nodeValue;
                } else {
                    $Nights = "";
                }
            }
            // Price
            $Price = $node->item(0)->getElementsByTagName('Price');
            if ($Price->length > 0) {
                $Reference = $Price->item(0)->getElementsByTagName('Reference');
                if ($Reference->length > 0) {
                    $MealPlan = $Reference->item(0)->getAttribute('MealPlan');
                    $Accommodation = $Reference->item(0)->getAttribute('Accommodation');
                    $Reference = $Reference->item(0)->nodeValue;
                } else {
                    $Reference = "";
                }
                $Agency = $Price->item(0)->getElementsByTagName('Agency');
                if ($Agency->length > 0) {
                    $Commission = $Agency->item(0)->getElementsByTagName('Commission');
                    if ($Commission->length > 0) {
                        $Commission = $Commission->item(0)->nodeValue;
                    } else {
                        $Commission = "";
                    }
                    $Net = $Agency->item(0)->getElementsByTagName('Net');
                    if ($Net->length > 0) {
                        $Net = $Net->item(0)->nodeValue;
                    } else {
                        $Net = "";
                    }
                }
            }
            // ResortFee
            $ResortFee = $node->item(0)->getElementsByTagName('ResortFee');
            if ($ResortFee->length > 0) {
                $FeeTotal = $ResortFee->item(0)->getElementsByTagName('FeeTotal');
                if ($FeeTotal->length > 0) {
                    $FeeTotal = $FeeTotal->item(0)->nodeValue;
                } else {
                    $FeeTotal = "";
                }
                $Note = $ResortFee->item(0)->getElementsByTagName('Note');
                if ($Note->length > 0) {
                    $Note = $Note->item(0)->nodeValue;
                } else {
                    $Note = "";
                }
            }
            // CxlPolicy
            $CxlPolicy = $node->item(0)->getElementsByTagName('CxlPolicy');
            if ($CxlPolicy->length > 0) {
                $CxlDays = $CxlPolicy->item(0)->getElementsByTagName('CxlDays');
                if ($CxlDays->length > 0) {
                    $CxlDays = $CxlDays->item(0)->nodeValue;
                } else {
                    $CxlDays = "";
                }
                $NtsPenalty = $CxlPolicy->item(0)->getElementsByTagName('NtsPenalty');
                if ($NtsPenalty->length > 0) {
                    $NtsPenalty = $NtsPenalty->item(0)->nodeValue;
                } else {
                    $NtsPenalty = "";
                }
                $Term1 = $CxlPolicy->item(0)->getElementsByTagName('Term1');
                if ($Term1->length > 0) {
                    $FromT1 = $Term1->item(0)->getElementsByTagName('From');
                    if ($FromT1->length > 0) {
                        $FromT1 = $FromT1->item(0)->nodeValue;
                    } else {
                        $FromT1 = "";
                    }
                    $ToT1 = $Term1->item(0)->getElementsByTagName('To');
                    if ($ToT1->length > 0) {
                        $ToT1 = $ToT1->item(0)->nodeValue;
                    } else {
                        $ToT1 = "";
                    }
                    $CxlChargeT1 = $Term1->item(0)->getElementsByTagName('CxlCharge');
                    if ($CxlChargeT1->length > 0) {
                        $CxlChargeT1 = $CxlChargeT1->item(0)->nodeValue;
                    } else {
                        $CxlChargeT1 = "";
                    }
                }
                $Term2 = $CxlPolicy->item(0)->getElementsByTagName('Term2');
                if ($Term2->length > 0) {
                    $FromT2 = $Term2->item(0)->getElementsByTagName('From');
                    if ($FromT2->length > 0) {
                        $FromT2 = $FromT2->item(0)->nodeValue;
                    } else {
                        $FromT2 = "";
                    }
                    $ToT2 = $Term2->item(0)->getElementsByTagName('To');
                    if ($ToT2->length > 0) {
                        $ToT2 = $ToT2->item(0)->nodeValue;
                    } else {
                        $ToT2 = "";
                    }
                    $CxlChargeT2 = $Term2->item(0)->getElementsByTagName('CxlCharge');
                    if ($CxlChargeT2->length > 0) {
                        $CxlChargeT2 = $CxlChargeT2->item(0)->nodeValue;
                    } else {
                        $CxlChargeT2 = "";
                    }
                }
            }
            // Mealplan
            $Mealplan = $node->item(0)->getElementsByTagName('Mealplan');
            if ($Mealplan->length > 0) {
                $Room = $Mealplan->item(0)->getElementsByTagName('Room');
                if ($Room->length > 0) {
                    $room = $Room->item(0)->getElementsByTagName('room');
                    if ($room->length > 0) {
                        $room = $room->item(0)->nodeValue;
                    } else {
                        $room = "";
                    }
                    $Price = $Room->item(0)->getElementsByTagName('Price');
                    if ($Price->length > 0) {
                        $PriceId = $Price->item(0)->getElementsByTagName('PriceId');
                        if ($PriceId->length > 0) {
                            $PriceId = $PriceId->item(0)->nodeValue;
                        } else {
                            $PriceId = "";
                        }
                        $From = $Price->item(0)->getElementsByTagName('From');
                        if ($From->length > 0) {
                            $From = $From->item(0)->nodeValue;
                        } else {
                            $From = "";
                        }
                        $To = $Price->item(0)->getElementsByTagName('To');
                        if ($To->length > 0) {
                            $To = $To->item(0)->nodeValue;
                        } else {
                            $To = "";
                        }
                        $Meal = $Price->item(0)->getElementsByTagName('Meal');
                        if ($Meal->length > 0) {
                            $MealId = $Meal->item(0)->getElementsByTagName('MealId');
                            if ($MealId->length > 0) {
                                $MealId = $MealId->item(0)->nodeValue;
                            } else {
                                $MealId = "";
                            }
                            $Code = $Meal->item(0)->getElementsByTagName('Code');
                            if ($Code->length > 0) {
                                $Code = $Code->item(0)->nodeValue;
                            } else {
                                $Code = "";
                            }
                            $Name = $Meal->item(0)->getElementsByTagName('Name');
                            if ($Name->length > 0) {
                                $Name = $Name->item(0)->nodeValue;
                            } else {
                                $Name = "";
                            }
                            $AgeGroup = $Meal->item(0)->getElementsByTagName('AgeGroup');
                            if ($AgeGroup->length > 0) {
                                $AgeType = $AgeGroup->item(0)->getElementsByTagName('AgeType');
                                if ($AgeType->length > 0) {
                                    $AgeType = $AgeType->item(0)->nodeValue;
                                } else {
                                    $AgeType = "";
                                }
                                $GroupName = $AgeGroup->item(0)->getElementsByTagName('GroupName');
                                if ($GroupName->length > 0) {
                                    $GroupName = $GroupName->item(0)->nodeValue;
                                } else {
                                    $GroupName = "";
                                }
                                $IsIncluded = $AgeGroup->item(0)->getElementsByTagName('IsIncluded');
                                if ($IsIncluded->length > 0) {
                                    $IsIncluded = $IsIncluded->item(0)->nodeValue;
                                } else {
                                    $IsIncluded = "";
                                }
                            }
                        }
                    }
                }
            }
        }
        $vector['code'] = $Hotelid;
        $pricebreakdown = array();
        $pricebreakdownCount = 0;
        for ($rZZ = 0; $rZZ < $Nights; $rZZ ++) {
            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
            $amount = $Reference / $Nights;
            if ($majesticusaMarkup != 0) {
                $amount = $amount + (($amount * $majesticusaMarkup) / 100);
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
            if ($majesticusaMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
        if ($cancelation_string != "") {
            $cancelation_string .= "<br/><br/>";
        }
        $cancelation_string .= $translator->translate("Charge") . " " . number_format($CxlChargeT1, 2, '.', '') . " " . $translator->translate("if cancelled on or after") . " ";
        $cancelation_string .= $FromT1;
        $cancelation_deadline = $FromT1;
        $total = $total + $Reference;
        $item['name'] = $value['name'];
        $item['room'] = $room;
        $item['roomid'] = $roomID;
        $item['RoomTypeCode'] = $value['RoomTypeCode'];
        $item['RoomDescription'] = $value['RoomDescription'];
        $item['meal'] = $Code;
        $item['Status'] = $Status;
        $total = $total + $Reference;
        $tot = $Reference;
        $item['total'] = $filter->filter($tot);
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        $item['cancelpolicy'] = $cancelation_string;
        $item['cancelpolicy_deadline'] = date('Y-m-d', strtotime($cancelation_deadline));
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mmajestic where sid='" . $shid . "' and hid=" . $hid;
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
$response['breakdown'] = $roombreakdown;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
$response['code'] = $vector['code'];
?>