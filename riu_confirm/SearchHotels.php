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
$travelplan = false;
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
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan";
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
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $residenceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='TravelPlanuser' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanuser = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanpassword' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='TravelPlanMarkup' and affiliate_id=$affiliate_id_travelplan";
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
$sql = "select value from settings where name='TravelPlanserviceURL' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanserviceURL = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSystem' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSystem = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSalesChannel' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSalesChannel = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanlanguage' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanlanguage = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanConnectionString' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanConnectionString = $row_settings['value'];
}

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');

$xml = '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://services.enginexml.rumbonet.riu.com" xmlns:dtos="http://dtos.enginexml.rumbonet.riu.com">    <soapenv:Header/>    <soapenv:Body>       <ser:RecoverRates>          <ser:in0>             <dtos:CountryCode>ES</dtos:CountryCode>             <dtos:hotelID>562</dtos:hotelID>             <dtos:Language>E</dtos:Language>             <dtos:stayDateEnd>20090601</dtos:stayDateEnd>             <dtos:stayDateStart>2009063</dtos:stayDateStart>          </ser:in0>       </ser:RecoverRates>    </soapenv:Body> </soapenv:Envelope';
if ($TravelPlanserviceURL != "" and $TravelPlanuser != "" and $TravelPlanpassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $TravelPlanserviceURL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept-Encoding: gzip, deflate",
        "Accept: application/xml",
        "Content-type: application/xml",
        "Content-length: " . strlen($xml)
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
    // error_log("\r\n RESPONSE2: $response \r\n", 3, "/srv/www/htdocs/error_log");
    curl_close($ch);
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_travelplan');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $TravelPlanserviceURL . $xml,
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
    $Envelope = $inputDoc->getElementsByTagName('Envelope');
    $Body = $Envelope->item(0)->getElementsByTagName('Body');
    $RecoverRatesResponse = $Body->item(0)->getElementsByTagName('RecoverRatesResponse');
    $RecoverRatesResponse = $RecoverRatesResponse->item(0)->getElementsByTagName('RecoverRatesResponse');
    $node = $RecoverRatesResponse->item(0)->getElementsByTagName('ratesList');
    for ($r = 0; $r < $node->length; $r++) {
        $adultsNumber = $node->item($r)->getElementsByTagName('adultsNumber');
        if ($adultsNumber->length > 0) {
            $adultsNumber = $adultsNumber->item(0)->nodeValue;
        } else {
            $adultsNumber = "";
        }
        $amounts = $node->item($r)->getElementsByTagName('amounts');
        if ($amounts->length > 0) {
            $PricesPeriods = $amounts->item(0)->getElementsByTagName('PricesPeriods');
            if ($PricesPeriods->length > 0) {
                $amount = $PricesPeriods->item(0)->getElementsByTagName('amount');
                if ($amount->length > 0) {
                    $amount = $amount->item(0)->nodeValue;
                } else {
                    $amount = "";
                }
                $stayDateEnd = $PricesPeriods->item(0)->getElementsByTagName('stayDateEnd');
                if ($stayDateEnd->length > 0) {
                    $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
                } else {
                    $stayDateEnd = "";
                }
                $stayDateStart = $PricesPeriods->item(0)->getElementsByTagName('stayDateStart');
                if ($stayDateStart->length > 0) {
                    $stayDateStart = $stayDateStart->item(0)->nodeValue;
                } else {
                    $stayDateStart = "";
                }
                $value = $PricesPeriods->item(0)->getElementsByTagName('value');
                if ($value->length > 0) {
                    $value = $value->item(0)->nodeValue;
                } else {
                    $value = "";
                }
            }
        }
        $childAgeEnd = $node->item($r)->getElementsByTagName('childAgeEnd');
        if ($childAgeEnd->length > 0) {
            $childAgeEnd = $childAgeEnd->item(0)->nodeValue;
        } else {
            $childAgeEnd = "";
        }
        $childAgeStart = $node->item($r)->getElementsByTagName('childAgeStart');
        if ($childAgeStart->length > 0) {
            $childAgeStart = $childAgeStart->item(0)->nodeValue;
        } else {
            $childAgeStart = "";
        }
        $childsNumber = $node->item($r)->getElementsByTagName('childsNumber');
        if ($childsNumber->length > 0) {
            $childsNumber = $childsNumber->item(0)->nodeValue;
        } else {
            $childsNumber = "";
        }
        $currencyCode = $node->item($r)->getElementsByTagName('currencyCode');
        if ($currencyCode->length > 0) {
            $currencyCode = $currencyCode->item(0)->nodeValue;
        } else {
            $currencyCode = "";
        }
        $guestTypeCode = $node->item($r)->getElementsByTagName('guestTypeCode');
        if ($guestTypeCode->length > 0) {
            $guestTypeCode = $guestTypeCode->item(0)->nodeValue;
        } else {
            $guestTypeCode = "";
        }
        $mealPlan = $node->item($r)->getElementsByTagName('mealPlan');
        if ($mealPlan->length > 0) {
            $mealPlan = $mealPlan->item(0)->nodeValue;
        } else {
            $mealPlan = "";
        }
        $shid = $hotelID;
        $sfilter[] = " sid='$shid' ";
        $minimunStays = $node->item($r)->getElementsByTagName('minimunStays');
        if ($minimunStays->length > 0) {
            $minimunStays = $minimunStays->item(0)->nodeValue;
        } else {
            $minimunStays = "";
        }
        $paxPosition = $node->item($r)->getElementsByTagName('paxPosition');
        if ($paxPosition->length > 0) {
            $paxPosition = $paxPosition->item(0)->nodeValue;
        } else {
            $paxPosition = "";
        }
        $quoteType = $node->item($r)->getElementsByTagName('quoteType');
        if ($quoteType->length > 0) {
            $quoteType = $quoteType->item(0)->nodeValue;
        } else {
            $quoteType = "";
        }
        $rateHotel = $node->item($r)->getElementsByTagName('rateHotel');
        if ($rateHotel->length > 0) {
            $rateHotel = $rateHotel->item(0)->nodeValue;
        } else {
            $rateHotel = "";
        }
        $rateType = $node->item($r)->getElementsByTagName('rateType');
        if ($rateType->length > 0) {
            $rateType = $rateType->item(0)->nodeValue;
        } else {
            $rateType = "";
        }

        $roomTypeCode = $node->item($r)->getElementsByTagName('roomTypeCode');
        if ($roomTypeCode->length > 0) {
            $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
        } else {
            $roomTypeCode = "";
        }

        $stayDateEnd = $node->item($r)->getElementsByTagName('stayDateEnd');
        if ($stayDateEnd->length > 0) {
            $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
        } else {
            $stayDateEnd = "";
        }
        $stayDateStart = $node->item($r)->getElementsByTagName('stayDateStart');
        if ($stayDateStart->length > 0) {
            $stayDateStart = $stayDateStart->item(0)->nodeValue;
        } else {
            $stayDateStart = "";
        }
        //release
        $release = $node->item($r)->getElementsByTagName('release');
        $PricesPeriods = $release->item(0)->getElementsByTagName('PricesPeriods');
        if ($PricesPeriods->length > 0) {
            for ($j=0; $j < $PricesPeriods->length; $j++) { 
                $amountPP = $PricesPeriods->item($j)->getElementsByTagName('amount');
                if ($amountPP->length > 0) {
                    $amountPP = $amountPP->item(0)->nodeValue;
                } else {
                    $amountPP = "";
                }
                $stayDateEndPP = $PricesPeriods->item($j)->getElementsByTagName('stayDateEnd');
                if ($stayDateEndPP->length > 0) {
                    $stayDateEndPP = $stayDateEndPP->item(0)->nodeValue;
                } else {
                    $stayDateEndPP = "";
                }
                $stayDateStartPP = $PricesPeriods->item($j)->getElementsByTagName('stayDateStart');
                if ($stayDateStartPP->length > 0) {
                    $stayDateStartPP = $stayDateStartPP->item(0)->nodeValue;
                } else {
                    $stayDateStartPP = "";
                }
                $valuePP = $PricesPeriods->item($j)->getElementsByTagName('value');
                if ($valuePP->length > 0) {
                    $valuePP = $valuePP->item(0)->nodeValue;
                } else {
                    $valuePP = "";
                }
            }
        }
        

        for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
            if (is_array($tmp[$shid])) {
                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
            } else {
                $baseCounterDetails = 0;
            }
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $hotelID;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $hotelID;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-50";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $AdultsCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $ChildCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['infants'] = $InfantsCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $amount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $amountWithoutOffer;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($mealPlan);
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                $amount = $noOfNights * $AmountAfterTax;
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
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currencyCode;
            if ($promocode != "") {
                $tmp[$shid]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $translator->translate($promocode);
            } else {
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
            }
            // policyDescription nao existe
            // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $policyDescription;
            $travelplan = true;
        }
    }
}
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($travelplan == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mglobalia where " . $sfilter;
        error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 50;
        error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_travelplan');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_travelplan');
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