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
if (file_exists("src/App/language/" . $lang . ".mo")) {
    $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
}
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$bonotel = false;
$sql = "select city_xml32, latitude, longitude from cities where id=" . $destination;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml32 = $row_settings["city_xml32"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml32 = 0;
}
error_log("\r\n city_xml32: $city_xml32 \r\n", 3, "/srv/www/htdocs/error_log");
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='AICTravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_aic";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='AICTravellogin' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravellogin = $row_settings['value'];
}
$sql = "select value from settings where name='AICTravelpassword' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravelpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='AICTravelMarkup' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravelMarkup = (double) $row_settings['value'];
} else {
    $AICTravelMarkup = 0;
}
$sql = "select value from settings where name='AICTravelServiceURL' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravelServiceURL = $row_settings['value'];
}
$sql = "select value from settings where name='AICTravelCompany' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravelCompany = $row_settings['value'];
}
$date = new Datetime();
$timestamp = $date->format('U');
$city_xml32 = "rom";
$raw = '<?xml version="1.0" encoding="UTF-8"?>
<envelope>
    <header>
    	<actor>' . $AICTravelCompany . '</actor>
        <user>' . $AICTravellogin . '</user>
        <password>' . $AICTravelpassword . '</password>
        <version>1.6.1</version>
        <timestamp>' . $timestamp . '</timestamp>
    </header>
    <query type="availability" product="hotel">
    	<nationality>' . $sourceMarket . '</nationality>
        <checkin date="' . strftime("%Y-%m-%d", $from) . '"/>
        <checkout date="' . strftime("%Y-%m-%d", $to) . '"/>
        <city code="' . $city_xml32 . '"/>
        <details>
            <room type="dbl" required="1" />
        </details>
    </query>
</envelope>';
if ($AICTravelServiceURL != "" and $AICTravellogin != "" and $AICTravelpassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $AICTravelServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml; charset=utf-8',
        'Content-Length: ' . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    // if ($response === false) {
    // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    // } else {
    // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    // }
    // error_log("\r\n PAULO \r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\n REQUEST: $raw \r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\n RESPONSE: $response \r\n", 3, "/srv/www/htdocs/error_log");
    curl_close($ch);
    // Descomentar para ver o resultado do provider
    // Nao esquecer de alterar o session id para testar por causa de cache
    // echo $response;
    // die();
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_aic');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $AICTravelServiceURL . $raw,
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
    // echo $response;
    // die();
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $node = $inputDoc->getElementsByTagName("response");
    for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
        $search = $node->item($rAUX)->getElementsByTagName("search");
        $numbersearch = $search->item(0)->getAttribute('number');
        $nights = $node->item($rAUX)->getElementsByTagName('nights');
        $numbernights = $nights->item(0)->getAttribute('number');
        $checkin = $node->item($rAUX)->getElementsByTagName('checkin');
        $datecheckin = $checkin->item(0)->getAttribute('date');
        $checkout = $node->item($rAUX)->getElementsByTagName('checkout');
        $datecheckout = $checkout->item(0)->getAttribute('date');
        if ($checkout->length > 0) {
            $checkout = $checkout->item(0)->nodeValue;
        } else {
            $checkout = "";
        }
        $hotelsVector = $node->item($rAUX)->getElementsByTagName('hotels');
        if ($hotelsVector->length > 0) {
            $hotel = $hotelsVector->item(0)->getElementsByTagName('hotel');
            if ($hotel->length > 0) {
                for ($i = 0; $i < $hotel->length; $i ++) {
                    $code = $hotel->item($i)->getAttribute('code');
                    $shid = $code;
                    $sfilter[] = " sid='$code' ";
                    $name = $hotel->item($i)->getAttribute('name');
                    $stars = $hotel->item($i)->getAttribute('stars');
                    $location = $hotel->item(0)->getAttribute('location');
                    $agreement = $hotel->item($i)->getElementsByTagName('agreement');
                    if ($agreement->length > 0) {
                        for ($iAux=0; $iAux < $agreement->length; $iAux++) { 
                            $remarkcode = "";
                            $id = $agreement->item($iAux)->getAttribute('id');
                            $available = $agreement->item($iAux)->getAttribute('available');
                            $c_type = $agreement->item($iAux)->getAttribute('c_type');
                            $room_basis = $agreement->item($iAux)->getAttribute('room_basis');
                            $meal_basis = $agreement->item($iAux)->getAttribute('meal_basis');
                            $currency = $agreement->item($iAux)->getAttribute('currency');
                            $deadline = $agreement->item($iAux)->getAttribute('deadline');
                            $total = $agreement->item($iAux)->getAttribute('total');
                            $is_dynamic = $agreement->item($iAux)->getAttribute('is_dynamic');
                            $room_type = $agreement->item($iAux)->getAttribute('room_type');

                            //policies
                            $policies = $agreement->item($iAux)->getElementsByTagName('policies');
                            if ($policies->length > 0) {
                                $policy = $policies->item(0)->getElementsByTagName('policy');
                                if ($policy->length > 0) {
                                    $percentage = $policy->item(0)->getAttribute('percentage');
                                    $from2 = $policy->item(0)->getAttribute('from');
                                }
                            }

                            //remarks
                            $remarks = $agreement->item($iAux)->getElementsByTagName('remarks');
                            if ($remarks->length > 0) {
                                $remark = $remarks->item(0)->getElementsByTagName('remark');
                                if ($remark->length > 0) {
                                    $remarkcode = $remark->item(0)->getAttribute('code');
                                    $remarktext = $remark->item(0)->getAttribute('text');
                                }
                            }

                            //deadline
                            $deadline2 = $agreement->item($iAux)->getElementsByTagName('deadline');
                            if ($deadline2->length > 0) {
                                $value = $deadline2->item(0)->getAttribute('value');
                                $date = $deadline2->item(0)->getAttribute('date');
                            }

                            $room = $agreement->item($iAux)->getElementsByTagName('room');
                            if ($room->length > 0) {
                                for ($Auxk = 0; $Auxk < $room->length; $Auxk ++) {
                                    $type = $room->item($Auxk)->getAttribute('type');
                                    $required = $room->item($Auxk)->getAttribute('required');
                                    $occupancy = $room->item($Auxk)->getAttribute('occupancy');
                                    $extrabed = $room->item($Auxk)->getAttribute('extrabed');
                                    $age = $room->item($Auxk)->getAttribute('age');
                                    $occupancyChild = $room->item($Auxk)->getAttribute('occupancyChild');
                                    $price = $room->item(0)->getElementsByTagName('price');
                                    if ($price->length > 0) {
                                        for ($Auxkk = 0; $Auxkk < $price->length; $Auxkk ++) {
                                            $fromprice = $price->item($Auxkk)->getAttribute('from');
                                            $toprice = $price->item($Auxkk)->getAttribute('to');
                                            $roomprice = $price->item(0)->getElementsByTagName('roomprice');
                                            $nettroom = $roomprice->item(0)->getAttribute('nett');
                                            $extrabedprice = $price->item(0)->getElementsByTagName('extrabedprice');
                                            if ($extrabedprice->length > 0) {
                                                $nettextrabed = $extrabedprice->item(0)->getAttribute('nett');
                                            } else {
                                                $nettextrabed = "";
                                            }
                                        }
                                    }
                                    for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                                        // if ($selectedAdults[$zRooms] == $stdAdults) {
                                        // Chidlren ??
                                        // if ($selectedChildren[$zRooms] == $children) {
                                        if (is_array($tmp[$code])) {
                                            $baseCounterDetails = count($tmp[$code]['details'][$zRooms]);
                                        } else {
                                            $baseCounterDetails = 0;
                                        }
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $name;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $code;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $code;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $id;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                        // cancellationType nao existe
                                        // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $c_type;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-32";
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $room_type;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = "zz";
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['availabilityid'] = $numbersearch;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $occupancy;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $occupancyChild;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $nettroom;
                                        if ($AICTravelMarkup != 0) {
                                            $total = $total + (($total * $AICTravelMarkup) / 100);
                                        }
                                        // Geo target markup
                                        if ($internalmarkup != 0) {
                                            $total = $total + (($total * $internalmarkup) / 100);
                                        }
                                        // Agent markup
                                        if ($agent_markup != 0) {
                                            $total = $total + (($total * $agent_markup) / 100);
                                        }
                                        // Fallback Markup
                                        if ($AICTravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                            $total = $total + (($total * $HotelsMarkupFallback) / 100);
                                        }
                                        // Agent discount
                                        if ($agent_discount != 0) {
                                            $total = $total - (($total * $agent_discount) / 100);
                                        }
                                        if ($scurrency != "" and $currency != $scurrency) {
                                            $total = $CurrencyConverter->convert($total, $currency, $scurrency);
                                        }
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                        try {
                                            $sql = "select mapped from board_mapping where description='" . addslashes($room_basis) . "'";
                                            $statement = $db->createStatement($sql);
                                            $statement->prepare();
                                            $row_board_mapping = $statement->execute();
                                            $row_board_mapping->buffer();
                                            if ($row_board_mapping->valid()) {
                                                $row_board_mapping = $row_board_mapping->current();
                                                $room_basis = $row_board_mapping["mapped"];
                                            }
                                        } catch (\Exception $e) {
                                            $logger = new Logger();
                                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                            $logger->addWriter($writer);
                                            $logger->info($e->getMessage());
                                        }
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($room_basis);
                                        $pricebreakdown = array();
                                        $pricebreakdownCount = 0;
                                        $amount = $total / $noOfNights;
                                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                            $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                                            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                            $pricebreakdownCount = $pricebreakdownCount + 1;
                                        }
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
                                        $after = date('Y-m-d', strtotime($from2));
                                        if ($remarkcode == "NONREFUNDABLE") {
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = true;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking");
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
                                        } else {
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy'] = "If you cancel booking after " . $after . " pay " . $percentage . "% of total.";
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadline'] = $deadline;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $deadline;
                                        }
                                        $vhb = 1;
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

if ($vhb == 1) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_maic where " . $sfilter;
        // error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $result = $statement->execute();
        $result->buffer();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new ResultSet();
            $resultSet->initialize($result);
            foreach ($resultSet as $row) {
                $sidfilter[] = $row->hid;
                if (is_array($hotels_array[$row->hid])) {
                    // Append to original details
                    $tmph = $hotels_array[$row->hid]['details'];
                    $tmps = $tmp[$row->sid]['details'];
                    foreach ($tmph as $key => $value) {
                        $last = count($tmph[$key]);
                        foreach ($tmps[$key] as $keyd => $valued) {
                            $tmph[$key][$last] = $valued;
                            $last ++;
                        }
                    }
                    $hotels_array[$row->hid]['details'] = $tmph;
                } else {
                    $hotels_array[$row->hid] = $tmp[$row->sid];
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
        $supplier = 32;
        // error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_aic');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_aic');
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
?>