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
error_log("\r\n COMECOU ABREU \r\n", 3, "/srv/www/htdocs/error_log");
unset($tmp);
$sfilter = array();
$abreu = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
$sql = "select city_xml41 from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml41 = $row_settings["city_xml41"];
} else {
    $city_xml41 = "";
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
    $sql = "select value from settings where name='AbreuDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='AbreuUsername' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuUsername = $row_settings['value'];
}

$sql = "select value from settings where name='Abreupassword' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreupassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='AbreuMarkup' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuMarkup = (double) $row_settings['value'];
} else {
    $AbreuMarkup = 0;
}
// URL
$sql = "select value from settings where name='AbreuHOTELAVAILABILITY' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuHOTELAVAILABILITY = $row_settings['value'];
}

$sql = "select value from settings where name='AbreuContext' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuContext = $row_settings['value'];
}

$sql = "select value from settings where name='AbreuCustomerID' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuCustomerID = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuOnRequest' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuOnRequest = $row_settings['value'];
}

$sql = "select value from settings where name='Abreub2cMarkup' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreub2cMarkup = $row_settings['value'];
}

for ($rAux = 0; $rAux < $rooms; $rAux ++) {
    $auxArray[$rAux]['qty'] = 1;
    $auxArray[$rAux]['selectedAdults'] = $selectedAdults[$rAux];
    $auxArray[$rAux]['selectedChildren'] = $selectedChildren[$rAux];
}
// error_log("\r\n DEPOIS FOR " . count($auxArray) . " \r\n", 3, "/srv/www/htdocs/error_log");

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$date = new Datetime();
$timestamp = $date->format('U');
// error_log("\r\n ANTES RAW \r\n", 3, "/srv/www/htdocs/error_log");

if ($city_xml41 != "") {
    
    $raw = '<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA ../../../html/OTASchemas/OTA_HotelAvailRQ.xsd" EchoToken="echo" TimeStamp="2008-03-03T09:19:01Z" Target="Production" Version="1" SequenceNmbr="1" AvailRatesOnly="true" BestOnly="true" RateRangeOnly="false" ExactMatchOnly="true" LanguageId="EN"><POS><Source><UniqueId URL="" Type="ParsysOperatorCode" Id="' . $AbreuCustomerID . ':' . $AbreuUsername . ':' . $Abreupassword . '"/></Source></POS><BookingSegments><BookingSegment xmlns="http://www.opentravel.org/OTA" AvailReqType="Room"><HotelReference HotelCityCode="' . $city_xml41 . '"/><SearchCodes><CodeRef CodeContext="CategoryOperator" Code="="/><CodeRef CodeContext="CategoryCode" Code="0"/><CodeRef CodeContext="Name" Code=""/></SearchCodes><StayDateRange StartDate="' . date("Y-m-d", $from) . 'T12:00:00Z" EndDate="' . date("Y-m-d", $to) . 'T12:00:00Z"/><RoomStayCandidates>';
    for ($r = 0; $r < count($auxArray); $r ++) {
        //
        // RoomTypeCode
        //
        // 0: SINGLE ROOM
        // 1: DOUBLE ROOM
        // 2: TWIN BED ROOM
        // 3: TRIPLE ROOM
        // 4: 2 ADULTS, 1 CHILD ROOM
        // 5: QUADRUPLE ROOM
        // 6: 3 ADULT, 1 CHILD
        // 7: 2 ADULT, 2 CHILD
        //
        if ($auxArray[$r]['selectedAdults'] == 1) {
            if ($auxArray[$r]['selectedChildren'] == 0) {
                $RoomTypeCode = 0;
            } elseif ($auxArray[$r]['selectedChildren'] == 1) {
                $RoomTypeCode = 2;
            } elseif ($auxArray[$r]['selectedChildren'] == 2) {
                $RoomTypeCode = 3;
            } elseif ($auxArray[$r]['selectedChildren'] == 3) {
                $RoomTypeCode = 4;
            } elseif ($auxArray[$r]['selectedChildren'] >= 4) {
                $RoomTypeCode = 5;
            } else {
                $RoomTypeCode = 0;
            }
        } elseif ($auxArray[$r]['selectedAdults'] == 2) {
            if ($auxArray[$r]['selectedChildren'] == 0) {
                $RoomTypeCode = 1;
            } elseif ($auxArray[$r]['selectedChildren'] == 1) {
                $RoomTypeCode = 4;
            } elseif ($auxArray[$r]['selectedChildren'] == 2) {
                $RoomTypeCode = 7;
            } elseif ($auxArray[$r]['selectedChildren'] >= 3) {
                $RoomTypeCode = 5;
            } else {
                $RoomTypeCode = 1;
            }
        } elseif ($auxArray[$r]['selectedAdults'] == 3) {
            if ($auxArray[$r]['selectedChildren'] == 0) {
                $RoomTypeCode = 3;
            } elseif ($auxArray[$r]['selectedChildren'] == 1) {
                $RoomTypeCode = 6;
            } elseif ($auxArray[$r]['selectedChildren'] >= 2) {
                $RoomTypeCode = 5;
            } else {
                $RoomTypeCode = 3;
            }
        } elseif ($auxArray[$r]['selectedAdults'] == 4) {
            $RoomTypeCode = 5;
        } else {
            $RoomTypeCode = 2;
        }
        $raw .= '<RoomStayCandidate xmlns="http://www.opentravel.org/OTA" RoomType="" RoomTypeCode="' . $RoomTypeCode . '" Quantity="1">';
        $passengers = $passengers + $auxArray[$r]['selectedAdults'];
        $raw .= '<GuestCount xmlns="http://www.opentravel.org/OTA" AgeQualCode="Adult" Count="' . $auxArray[$r]['selectedAdults'] . '"/>';
        if ($auxArray[$r]['selectedChildren'] > 0) {
            $raw .= '<GuestCount xmlns="http://www.opentravel.org/OTA" AgeQualCode="Child" Count="' . $auxArray[$r]['selectedChildren'] . '"/>';
            $passengers = $passengers + $auxArray[$r]['selectedChildren'];
            for ($k = 0; $k < $auxArray[$r]['selectedChildren']; $k ++) {
                $counterAges = 1;
                foreach ($selectedChildrenAges[$k] as $keyAux => $valueAux) {
                    $raw .= '<GuestAge Age="' . $valueAux . '" ind="' . $counterAges . '"/>';
                }
            }
        }
        $raw .= '</RoomStayCandidate>';
    }
    $raw .= '</RoomStayCandidates><TPA_Extensions><UniqueId URL="" Type="Reservation" Id=""/><OnRequest Id="' . $AbreuOnRequest . '"/></TPA_Extensions></BookingSegment></BookingSegments></OTA_HotelAvailRQ>';
    
    $raw2 = '<?xml version="1.0" encoding="utf-8"?><soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/"><soap-env:Header><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><wsse:Username>' . $AbreuUsername . '</wsse:Username><wsse:Password>' . $Abreupassword . '</wsse:Password><Context>' . $AbreuContext . '</Context></wsse:Security></soap-env:Header><soap-env:Body><OTA_HotelAvailRQ xmlns=" http://parsec.es/hotelapi/OTA2014Compact" ><HotelSearch><Currency Code="USD" /><HotelLocation CityCode="' . $city_xml41 . '" /><DateRange Start="' . date("Y-m-d", $from) . '" End="' . date("Y-m-d", $to) . '" /><GuestCountry Code="ES"/><RoomCandidates><RoomCandidate RPH="1"><Guests><Guest AgeCode="A" Count="2" /></Guests></RoomCandidate></RoomCandidates></HotelSearch></OTA_HotelAvailRQ></soap-env:Body></soap-env:Envelope>';

    error_log("\r\n Request: $raw2 \r\n", 3, "/srv/www/htdocs/error_log");
    if ($AbreuHOTELAVAILABILITY != "" and $AbreuUsername != "" and $Abreupassword != "") {
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $AbreuHOTELAVAILABILITY);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/xml",
            "Content-type: text/xml",
            "Content-length: " . strlen($raw2)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        // if ($response === false) {
        // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
        // } else {
        // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
        // }
        // error_log("\r\n $PalladiumHotelGroupserviceurl \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\n $raw \r\n", 3, "/srv/www/htdocs/error_log");
        //error_log("\r\n RESPONSE ABREU: $response \r\n", 3, "/srv/www/htdocs/error_log");
        curl_close($ch);
        // Descomentar para ver o resultado do provider
        // Nao esquecer de alterar o session id para testar por causa de cache
        // echo $response;
        // die();
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_abreu');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $AbreuHOTELAVAILABILITY . $raw,
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
        
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $OTA_HotelAvailRS = $inputDoc->getElementsByTagName('OTA_HotelAvailRS');

        $Hotels = $OTA_HotelAvailRS->item(0)->getElementsByTagName('Hotels');
        if ($Hotels->length > 0) {
            $Hotel = $Hotels->item(0)->getElementsByTagName('Hotel');
            if ($Hotel->length > 0) {
                for ($i=0; $i < $Hotel->length; $i++) { 
                    $Info = $Hotel->item($i)->getElementsByTagName('Info');
                    if ($Info->length > 0) {
                        $Recommended = $Info->item(0)->getAttribute('Recommended');
                        $MasterCode = $Info->item(0)->getAttribute('MasterCode');
                        $Rating = $Info->item(0)->getAttribute('Rating');
                        $HotelCityCode = $Info->item(0)->getAttribute('HotelCityCode');
                        $HotelName = $Info->item(0)->getAttribute('HotelName');
                        $HotelCode = $Info->item(0)->getAttribute('HotelCode');
                        $shid = $HotelCode;
                        $sfilter[] = " sid='$HotelCode' ";
                    }

                    $BestPrice = $Hotel->item($i)->getElementsByTagName('BestPrice');
                    if ($BestPrice->length > 0) {
                        $Currency = $BestPrice->item(0)->getAttribute('Currency');
                        $Amount = $BestPrice->item(0)->getAttribute('Amount');
                    }

                    $Rooms = $Hotel->item($i)->getElementsByTagName('Rooms');
                    if ($Rooms->length > 0) {
                        $Room = $Rooms->item(0)->getElementsByTagName('Room');
                        if ($Room->length > 0) {
                            for ($j=0; $j < $Room->length; $j++) { 
                                $RPH = $Room->item($j)->getAttribute('RPH');
                                $Status = $Room->item($j)->getAttribute('Status');
                                $Best = $Room->item($j)->getAttribute('Best');

                                $RoomType = $Room->item($j)->getElementsByTagName('RoomType');
                                if ($RoomType->length > 0) {
                                    $Code = $RoomType->item(0)->getAttribute('Code');
                                    $Name = $RoomType->item(0)->getAttribute('Name');
                                    $Special = $RoomType->item(0)->getElementsByTagName('Special');
                                    if ($Special->length > 0) {
                                        $Special = $Special->item(0)->nodeValue;
                                    } else {
                                        $Special = "";
                                    }
                                }

                                $RoomRates = $Room->item($j)->getElementsByTagName('RoomRates');
                                if ($RoomRates->length > 0) {
                                    $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
                                    if ($RoomRate->length > 0) {
                                        $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');
                                        $BookingCode = $RoomRate->item(0)->getAttribute('BookingCode');
                                        $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                                        if ($Total->length > 0) {
                                            $Currency = $Total->item(0)->getAttribute('Currency');
                                            $Amount = $Total->item(0)->getAttribute('Amount');
                                            $MinPrice = $Total->item(0)->getAttribute('MinPrice');
                                            $Commission = $Total->item(0)->getAttribute('Commission');
                                        } else {
                                            $Currency = "";
                                            $Amount = 0;
                                            $MinPrice = 0;
                                            $Commission = 0;
                                        }
                                        
                                    }
                                }

        
            
                                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                                    if (is_array($tmp[$shid])) {
                                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                    } else {
                                        $baseCounterDetails = 0;
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $HotelCode;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-41";
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['BookingCode'] = $BookingCode;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $Name;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $Code;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $Name;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomType'] = $Name;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RatePlanCode'] = $RateParsys;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $Special;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $Amount;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $Amount;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['MealPlan'] = $MealPlan;
                                    //$tmp[$shid]['details'][$zRooms][$baseCounterDetails]['NonRefundable'] = $NonRefundable;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['Recommended'] = $Recommended;
                                   // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['boardtype'] = $MealPlanDescription;
                                    
                                    /*
                                    * if ($PromotionCode != "") {
                                    * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                                    * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $PromotionCode;
                                    * } else {
                                    */
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                    // }
                                    
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($MealPlan);
                                    $pricebreakdown = array();
                                    $pricebreakdownCount = 0;
                                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                        $amount = $Amount / $noOfNights;
                                        if ($AbreuMarkup != 0) {
                                            $amount = $amount + (($amount * $AbreuMarkup) / 100);
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
                                        if ($AbreuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $Description;
                                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicies'] = $Description;
                                }
                            }
                        }
                    }
                }
            }
        }
        $abreu = true;
    }
}
// error_log("\r\n palladium: $palladium \r\n", 3, "/srv/www/htdocs/error_log");
//error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($abreu == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mabreu where " . $sfilter;
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
        $supplier = 41;
        //error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_abreu');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_abreu');
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
// }
?>