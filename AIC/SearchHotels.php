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
unset($tmp);
$sfilter = array();
$bonotel = false;
$db2 = new \Zend\Db\Adapter\Adapter($config);
$sql = "select city_xml32, latitude, longitude from cities where id=" . $destination;
$statement2 = $db2->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml32 = $row_settings["city_xml32"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml32 = 0;
}
$db2->getDriver()
    ->getConnection()
    ->disconnect();
if ((int) $nationality > 0) {
    $db2 = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement2 = $db2->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
    $db2->getDriver()
        ->getConnection()
        ->disconnect();
} else {
    $db2 = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select value from settings where name='AICTravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_aic";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}
$sql = "select value from settings where name='AICTravellogin' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravellogin = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
// error_log("\r\n PASSOU LOGIN \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='AICTravelpassword' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravelpassword = base64_decode($row_settings['value']);
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
// error_log("\r\n PASSOU PASSWORD \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='AICTravelMarkup' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravelMarkup = (double) $row_settings['value'];
} else {
    $AICTravelMarkup = 0;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
// error_log("\r\n PASSOU MARKUP \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='AICTravelServiceURL' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravelServiceURL = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
// error_log("\r\n COMECOU2 AIC TRAVELURL \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='AICTravelCompany' and affiliate_id=$affiliate_id_aic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AICTravelCompany = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
// error_log("\r\n COMECOU2 AIC TRAVELCOMPANY \r\n", 3, "/srv/www/htdocs/error_log");
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
        $db2 = new \Zend\Db\Adapter\Adapter($config);
        $sql = new Sql($db2);
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
        $db2->getDriver()
            ->getConnection()
            ->disconnect();
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
                    $code = $hotel->item(0)->getAttribute('code');
                    $sfilter[] = " sid=$code ";
                    $name = $hotel->item(0)->getAttribute('name');
                    $stars = $hotel->item(0)->getAttribute('stars');
                    $location = $hotel->item(0)->getAttribute('location');
                    $agreement = $hotel->item($i)->getElementsByTagName('agreement');
                    if ($agreement->length > 0) {
                        $id = $agreement->item(0)->getAttribute('id');
                        $available = $agreement->item(0)->getAttribute('available');
                        $c_type = $agreement->item(0)->getAttribute('c_type');
                        $room_basis = $agreement->item(0)->getAttribute('room_basis');
                        $meal_basis = $agreement->item(0)->getAttribute('meal_basis');
                        $currency = $agreement->item(0)->getAttribute('currency');
                        $deadline = $agreement->item(0)->getAttribute('deadline');
                        $total = $agreement->item(0)->getAttribute('total');
                        $is_dynamic = $agreement->item(0)->getAttribute('is_dynamic');
                        $room_type = $agreement->item(0)->getAttribute('room_type');
                        $room = $agreement->item(0)->getElementsByTagName('room');
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
                                            $extrabedprice = "";
                                        }
                                    }
                                }
                            }
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
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['name'] = $name;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['shid'] = $code;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                    // cancellationType nao existe
                    // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $c_type;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-32";
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['room'] = $room_type;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['room_type'] = "zz";
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['adults'] = $occupancy;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['children'] = $occupancyChild;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['nett'] = $nettroom;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($room_basis);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    $Gross = $total / $numbernights;
                    for ($rZZ = 0; $rZZ < $numbernights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $amount = $numbernights * $nettroom;
                        if ($AICTravelMarkup != 0) {
                            $amount = $amount + (($amount * $AICTravelMarkup) / 100);
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
                        if ($AICTravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                    $tmp[$code]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
                    // policyDescription nao existe
                    // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $policyDescription;
                    $vhb = 1;
                    // }
                    // }
                }
            }
        }
    }
}
// Paulo
// Tudo para baixo esta bem - nao alterar
if ($vhb == 1) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $db2 = new \Zend\Db\Adapter\Adapter($config);
        $sql = "select hid, sid from xmlhotels_maic where " . $sfilter;
        // error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db2->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
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
        $db2->getDriver()
            ->getConnection()
            ->disconnect();
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
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db2);
            $delete = $sql->delete();
            $delete->from('quote_session_aic');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db2);
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
            $db2->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
?>