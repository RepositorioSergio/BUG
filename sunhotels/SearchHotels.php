<?php
$scurrency = strtoupper($currency);
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
error_log("\r\n COMECOU SUNHOTELS TERCA \r\n", 3, "/srv/www/htdocs/error_log");
unset($tmp);
$sfilter = array();
$sunhotels = false;
$sql = "select city_xml50, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml50 = $row_settings["city_xml50"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml50 = 0;
}
if ($city_xml50 != "") {
    $city_xml50 = explode(":", $city_xml50);
    $x50_0 = $city_xml50[0];
    $x50_1 = $city_xml50[1];
    $x50_2 = $city_xml50[2];
}
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_sunhotels";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
if ((int) $residency > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $residency;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $residenceMarket = $row_settings["iso_code_2"];
    } else {
        $residenceMarket = "";
    }
} else {
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_sunhotels";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $residenceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='TravelPlanMarkup' and affiliate_id=$affiliate_id_sunhotels";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanMarkup = (double) $row_settings['value'];
} else {
    $TravelPlanMarkup = 0;
}
$sql = "select value from settings where name='TravelPlanserviceURL' and affiliate_id=$affiliate_id_sunhotels";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanserviceURL = $row_settings['value'];
}


$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');
$userName = "testagent";
$password = "785623";
$checkin = date('Y-m-d', $from);
$checkout = date('Y-m-d', $to);
$currency = "USD";
$language = "en";
$customercountry = "gb";
$destinationid = 695;
$numberadults = 0;
$numberchildrens = 0;
$numberinfants = 0;
$childrenages = "";
$tam = 0;
error_log("\r\n ANTES URL \r\n", 3, "/srv/www/htdocs/error_log");
$url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx/SearchV2?userName=$userName&password=$password&language=en&currencies=$currency&checkInDate=$checkin&checkOutDate=$checkout&numberOfRooms=$rooms&destination=&destinationID=$destinationid&hotelIDs=&resortIDs=&accommodationTypes=";
for ($r=0; $r < $rooms; $r++) { 
    $numberadults = $numberadults + $selectedAdults[$r];
    if (count($selectedChildren[$r]) > 0) {
        $numberchildrens = $numberchildrens + $selectedChildren[$r];
        for ($z=0; $z < $selectedChildren[$r]; $z++) { 
            if ($selectedChildrenAges[$r][$z] >= 2) {
                if ($tam >= 1) {
                    $childrenages = $childrenages . "," . $selectedChildrenAges[$r][$z];
                } else {
                    $childrenages = $childrenages . $selectedChildrenAges[$r][$z];
                }
            } else {
                $numberinfants = 1;
            }  
            $tam = $tam + 1; 
        }
    }
}
$url = $url . "&numberOfAdults=$numberadults&numberOfChildren=$numberchildrens&childrenAges=$childrenages&infant=0&sortBy=&sortOrder=&exactDestinationMatch=&blockSuperdeal=&showTransfer=&mealIds=&showCoordinates=&showReviews=&referencePointLatitude=&referencePointLongitude=&maxDistanceFromReferencePoint=&minStarRating=&maxStarRating=&featureIds=&minPrice=&maxPrice=&themeIds=&excludeSharedRooms=&excludeSharedFacilities=&prioritizedHotelIds=&totalRoomsInBatch=&paymentMethodId=&CustomerCountry=gb&B2C=";

error_log("\r\n URL $url \r\n", 3, "/srv/www/htdocs/error_log");
if ($url != "") {
    
    $headers = array(
        'Accept-Encoding: gzip,deflate',
        'Host: xml.sunhotels.net',
        'Content-Length: 0'
    );
    $startTime = microtime();
    $ch = curl_init();
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    // curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    // if ($response === false) {
    // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    // } else {
    // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    // }
    // error_log("\r\n RESPONSE: $response \r\n", 3, "/srv/www/htdocs/error_log");
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_sunhotels');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $url,
            'sqlcontext' => $response,
            'errcontext' => ''
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }

    $mealname = "";

    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $searchresult = $inputDoc->getElementsByTagName("searchresult");
    $hotelInfo = $searchresult->item(0)->getElementsByTagName("hotels");
    $hotel = $hotelInfo->item(0)->getElementsByTagName("hotel");
    if ($hotel->length > 0) {
        for ($i=0; $i < $hotel->length; $i++) { 
            $hotelid = $hotel->item($i)->getElementsByTagName("hotel.id");
            if ($hotelid->length > 0) {
                $hotelid = $hotelid->item(0)->nodeValue;
            } else {
                $hotelid = "";
            }
            $shid = $hotelid;
            $sfilter[] = " sid='$hotelid' ";
            $destination_id = $hotel->item($i)->getElementsByTagName("destination_id");
            if ($destination_id->length > 0) {
                $destination_id = $destination_id->item(0)->nodeValue;
            } else {
                $destination_id = "";
            }
            $resort_id = $hotel->item($i)->getElementsByTagName("resort_id");
            if ($resort_id->length > 0) {
                $resort_id = $resort_id->item(0)->nodeValue;
            } else {
                $resort_id = "";
            }
            $transfer = $hotel->item($i)->getElementsByTagName("transfer");
            if ($transfer->length > 0) {
                $transfer = $transfer->item(0)->nodeValue;
            } else {
                $transfer = "";
            }
            $notes = $hotel->item($i)->getElementsByTagName("notes");
            if ($notes->length > 0) {
                $notes = $notes->item(0)->nodeValue;
            } else {
                $notes = "";
            }
            $codes = $hotel->item($i)->getElementsByTagName("codes");
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
            $distance = $hotel->item($i)->getElementsByTagName("distance");
            if ($distance->length > 0) {
                $distance = $distance->item(0)->nodeValue;
            } else {
                $distance = "";
            }

            $roomtypes = $hotel->item($i)->getElementsByTagName("roomtypes");
            if ($roomtypes->length > 0) {
                $roomtype = $roomtypes->item(0)->getElementsByTagName("roomtype");
                if ($roomtype->length > 0) {
                    for ($j=0; $j < $roomtype->length; $j++) { 
                        $roomtypeid = $roomtype->item($j)->getElementsByTagName("roomtype.ID");
                        if ($roomtypeid->length > 0) {
                            $roomtypeid = $roomtypeid->item(0)->nodeValue;
                        } else {
                            $roomtypeid = "";
                        }

                        $rooms = $roomtype->item($j)->getElementsByTagName("rooms");
                        if ($rooms->length > 0) {
                            $room = $rooms->item(0)->getElementsByTagName("room");
                            if ($room->length > 0) {
                                for ($jAux=0; $jAux < $room->length; $jAux++) { 
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
                                            for ($jAux2=0; $jAux2 < $meal->length; $jAux2++) { 
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
                                                
                                            
            
                                                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                                                    // if ($selectedAdults[$zRooms] == $stdAdults) {
                                                    // Chidlren ??
                                                    // if ($selectedChildren[$zRooms] == $children) {
                                                    if (is_array($tmp[$shid])) {
                                                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                                    } else {
                                                        $baseCounterDetails = 0;
                                                    }
                                                    // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $hotelid;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-7";
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $roomid;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $roomid;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $roomtypeid;
                                                    // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $Name;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['childrenages'] = $childrenages;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['infants'] = $numberinfants;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $price2;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $price2;
                                                    // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['NonRefundable'] = $NonRefundable;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['mealid'] = $mealid;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($mealname);
                                                    $pricebreakdown = array();
                                                    $pricebreakdownCount = 0;
                                                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                                        $amount = $price2 / $noOfNights;
                                                        if ($TravelPlanMarkup != 0) {
                                                            $amount = $amount + (($amount * $TravelPlanMarkup) / 100);
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
                                                        if ($TravelPlanMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $percentage;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $deadline;
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
        }
        $sunhotels = true;
    }
}

// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($sunhotels == true) {
    $sfilter = implode(' or ', $sfilter);
    if ($sfilter != "") {
        try {
            $sql = "select hid, sid from xmlhotels_msunhotels where " . $sfilter;
            //error_log("\r\n SQL $sql \r\n", 3, "/srv/www/htdocs/error_log");
            $statement2 = $db->createStatement($sql);
            $statement2->prepare();
            $result2 = $statement2->execute();
            $result2->buffer();
            if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
                $resultSet2 = new ResultSet();
                $resultSet2->initialize($result2);
                foreach ($resultSet2 as $row2) {
                    // $sidfilter[] = "id=" . $row2->hid;
                    $sidfilter[] = $row2->hid;
                    if (is_array($hotels_array[$row2->hid])) {
                        // Append to original details
                        $tmph = $hotels_array[$row2->hid]['details'];
                        $tmps = $tmp[$row2->sid]['details'];
                        foreach ($tmph as $key => $value) {
                            $last = count($tmph[$key]);
                            foreach ($tmps[$key] as $keyd => $valued) {
                                $tmph[$key][$last] = $valued;
                                $last ++;
                            }
                        }
                        $hotels_array[$row2->hid]['details'] = $tmph;
                    } else {
                        $hotels_array[$row2->hid] = $tmp[$row2->sid];
                    }
                }
            }
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        if (is_array($sidfilter)) {
            $sidfilter = implode(',', $sidfilter);
            $query = 'call xmlhotels("' . $sidfilter . '")';
            $supplier = 7;
            //error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
            try {
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_sunhotels');
                $delete->where(array(
                    'session_id' => $session_id
                ));
                $statement = $sql->prepareStatementForSqlObject($delete);
                $results = $statement->execute();
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_sunhotels');
                $insert->values(array(
                    'session_id' => $session_id,
                    'xmlrequest' => (string) $raw,
                    'xmlresult' => (string) $response,
                    'data' => base64_encode(serialize($hotels_array)),
                    'searchsettings' => base64_encode(serialize($requestdata))
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
            } catch (Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
        }
    }
}
?>