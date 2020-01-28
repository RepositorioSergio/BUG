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
$abreu = false;
$query = "";
$auxArray = array();
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
$sql = "select value from settings where name='AbreuCurrency' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuCurrency = $row_settings['value'];
}
if ($AbreuCurrency == "") {
    $AbreuCurrency = "USD";
}
$date = new Datetime();
$timestamp = $date->format('U');
if ($city_xml41 != "") {
    $raw = '<?xml version="1.0" encoding="utf-8"?><soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/"><soap-env:Header><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><wsse:Username>' . $AbreuUsername . '</wsse:Username><wsse:Password>' . $Abreupassword . '</wsse:Password><Context>' . $AbreuContext . '</Context></wsse:Security></soap-env:Header><soap-env:Body><OTA_HotelAvailRQ xmlns=" http://parsec.es/hotelapi/OTA2014Compact" ><HotelSearch><Currency Code="' . $AbreuCurrency . '" /><HotelLocation CityCode="' . $city_xml41 . '" /><DateRange Start="' . date("Y-m-d", $from) . '" End="' . date("Y-m-d", $to) . '" /><GuestCountry Code="' . $sourceMarket . '" /><RoomCandidates><RoomCandidate RPH="1"><Guests><Guest AgeCode="A" Count="' . $selectedAdults[0] . '" />';
    for ($z = 0; $z < $selectedChildren[0]; $z ++) {
        $raw .= '<Guest AgeCode="C" Count="1" Age="' . $selectedChildrenAges[0][$z] . '" />';
    }
    $raw .= '</Guests></RoomCandidate></RoomCandidates></HotelSearch></OTA_HotelAvailRQ></soap-env:Body></soap-env:Envelope>';
    // error_log("\r\nAbreu Request: $raw\r\n", 3, "/srv/www/htdocs/error_log");
    if ($AbreuHOTELAVAILABILITY != "" and $AbreuUsername != "" and $Abreupassword != "") {
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $AbreuHOTELAVAILABILITY);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/xml",
            "Content-type: text/xml",
            "Content-length: " . strlen($raw)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        // error_log("\r\nResponse Abreu: $response \r\n", 3, "/srv/www/htdocs/error_log");
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
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        if ($response != "") {
            $inputDoc = new DOMDocument();
            $inputDoc->loadXML($response);
            $OTA_HotelAvailRS = $inputDoc->getElementsByTagName('OTA_HotelAvailRS');
            if ($OTA_HotelAvailRS->length > 0) {
                $Hotels = $OTA_HotelAvailRS->item(0)->getElementsByTagName('Hotels');
                if ($Hotels->length > 0) {
                    $Hotel = $Hotels->item(0)->getElementsByTagName('Hotel');
                    if ($Hotel->length > 0) {
                        for ($i = 0; $i < $Hotel->length; $i ++) {
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
                                    for ($j = 0; $j < $Room->length; $j ++) {
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
                                        $MealPlanCode = "";
                                        $RoomRates = $Room->item($j)->getElementsByTagName('RoomRates');
                                        if ($RoomRates->length > 0) {
                                            $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
                                            if ($RoomRate->length > 0) {
                                                $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');
                                                $MealPlanCode = $MealPlan;
                                                $BookingCode = $RoomRate->item(0)->getAttribute('BookingCode');
                                                $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                                                if ($Total->length > 0) {
                                                    $Currency = $Total->item(0)->getAttribute('Currency');
                                                    $Amount = $Total->item(0)->getAttribute('Amount');
                                                    $MinPrice = $Total->item(0)->getAttribute('MinPrice');
                                                    $Commission = $Total->item(0)->getAttribute('Commission');
                                                } else {
                                                    $Amount = 0;
                                                    $MinPrice = 0;
                                                    $Commission = 0;
                                                }
                                            }
                                        }
                                        if (is_array($tmp[$shid])) {
                                            $baseCounterDetails = count($tmp[$shid]['details'][0]);
                                        } else {
                                            $baseCounterDetails = 0;
                                        }
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['name'] = $HotelName;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['shid'] = $shid;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['status'] = 1;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-41";
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['bookingcode'] = $BookingCode;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['room'] = $Name;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['roomid'] = $Code;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['mealplancode'] = $MealPlan;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['RoomDescription'] = $Special;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['adults'] = $selectedAdults[0];
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['children'] = $selectedChildren[0];
                                        if ($scurrency != "" and $AbreuCurrency != $scurrency and $AbreuCurrency != "") {
                                            $total = $CurrencyConverter->convert($total, $AbreuCurrency, $scurrency);
                                        }
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['nettotal'] = $Amount;
                                        // Markup
                                        if ($AbreuMarkup != 0) {
                                            $Amount = $Amount + (($Amount * $AbreuMarkup) / 100);
                                        }
                                        // Geo target markup
                                        if ($internalmarkup != 0) {
                                            $Amount = $Amount + (($Amount * $internalmarkup) / 100);
                                        }
                                        // Agent markup
                                        if ($agent_markup != 0) {
                                            $Amount = $Amount + (($Amount * $agent_markup) / 100);
                                        }
                                        // Fallback Markup
                                        if ($AbreuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                            $Amount = $Amount + (($Amount * $HotelsMarkupFallback) / 100);
                                        }
                                        // Agent discount
                                        if ($agent_discount != 0) {
                                            $Amount = $Amount - (($Amount * $agent_discount) / 100);
                                        }
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['total'] = (double) $Amount;
                                        // $tmp[$shid]['details'][0][$baseCounterDetails]['NonRefundable'] = $NonRefundable;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['recommended'] = $Recommended;
                                        /*
                                         * if ($PromotionCode != "") {
                                         * $tmp[$shid]['details'][0][$baseCounterDetails]['special'] = true;
                                         * $tmp[$shid]['details'][0][$baseCounterDetails]['specialdescription'] = $PromotionCode;
                                         * } else {
                                         */
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['special'] = false;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['specialdescription'] = "";
                                        // }
                                        try {
                                            $sql = "select mapped from board_mapping where description='" . addslashes($MealPlan) . "'";
                                            $statement = $db->createStatement($sql);
                                            $statement->prepare();
                                            $row_board_mapping = $statement->execute();
                                            $row_board_mapping->buffer();
                                            if ($row_board_mapping->valid()) {
                                                $row_board_mapping = $row_board_mapping->current();
                                                $MealPlan = $row_board_mapping["mapped"];
                                            }
                                        } catch (\Exception $e) {
                                            $logger = new Logger();
                                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                            $logger->addWriter($writer);
                                            $logger->info($e->getMessage());
                                        }
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['meal'] = $translator->translate($MealPlan);
                                        $pricebreakdown = array();
                                        $pricebreakdownCount = 0;
                                        $amountBreakdown = $Amount / $noOfNights;
                                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                            $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amountBreakdown);
                                            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amountBreakdown;
                                            $pricebreakdownCount = $pricebreakdownCount + 1;
                                        }
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['cancelpolicy'] = "";
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['cancelpolicies'] = "";
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['sourceMarket'] = $sourceMarket;
                                        $tmp[$shid]['details'][0][$baseCounterDetails]['MealPlanCode'] = $MealPlanCode;
                                    }
                                }
                            }
                        }
                        $abreu = true;
                    }
                }
            }
        }
    }
}
// error_log("\r\nAbreu:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($abreu == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mabreu where " . $sfilter;
        // error_log("\r\nAbreu SQL $sql\r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        $result2->buffer();
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            foreach ($resultSet2 as $row2) {
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
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        if ($sidfilter != "") {
            $query = 'call xmlhotels("' . $sidfilter . '")';
            // error_log("\r\nAbreu SQL $query\r\n", 3, "/srv/www/htdocs/error_log");
            $supplier = 41;
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
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
        }
    }
}
?>