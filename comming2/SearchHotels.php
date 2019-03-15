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
$coming2 = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id,city_xml45, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml45 = $row_settings["city_xml45"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml45 = "";
}
$sql = "select value from settings where name='enablecoming2' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_coming2 = $affiliate_id;
} else {
    $affiliate_id_coming2 = 0;
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
    $sql = "select value from settings where name='GetaroomDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_coming2";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='coming2login' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2login = $row_settings['value'];
}
$sql = "select value from settings where name='coming2password' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2password = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='coming2Markup' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2Markup = (double) $row_settings['value'];
} else {
    $coming2Markup = 0;
}
$sql = "select value from settings where name='coming2ServiceURL' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2ServiceURL = $row_settings['value'];
}
$sql = "select value from settings where name='coming2Company' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2Company = $row_settings['value'];
}
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$date = new Datetime();
$timestamp = $date->format('U');
$raw = '{
    "AvailabilityId": "",
    "Language": "ES",
    "CurrencyCode": "EUR",
    "Customer": "",
    "FromDate": "' . strftime("%Y-%m-%d", $from) . '",
    "ToDate": "' . strftime("%Y-%m-%d", $to) . '",
    "Areas": [ "PUJ" ],
    "Rooms": [
        {
            "RoomCandidateId": "1",
            "Paxes": [
                {
                    "PaxType": "Adult",
                    "Age": 30
                },
                {
                    "PaxType": "Adult",
                    "Age": 30
                }
            ]
        }
    ],
    "Skip": 0,
    "Limit": 50,
    "Filter": {
        "MinPrice": 0,
        "MaxPrice": 0,
        "PackageRates": "All",
        "ResidentRates": "Yes",
        "SeniorRates": "No",
        "NonRefundableRates": "All"
    },
    "OrderBy": {
        "Direction": "Ascending",
        "Field": "Price"
    }
}';

error_log("\r\n $raw  \r\n", 3, "/srv/www/htdocs/error_log");

if ($coming2ServiceURL != "" and $coming2login != "" and $coming2password != "") {
    $passuser = "$coming2login:$coming2password";
    $auth = base64_encode($passuser);
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $coming2ServiceURL . "/Hotel/Availability");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Basic " . $auth,
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_coming2');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $coming2ServiceURL . $raw,
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
    $response = json_decode($response, true);
    // Results
    $AvailabilityId = $response['AvailabilityId'];
    $Language = $response['Language'];
    $Market = $response['Market'];
    $Customer = $response['Customer'];
    $FromDate = $response['FromDate'];
    $ToDate = $response['ToDate'];
    $Hotels = $response['Hotels'];
    for ($j = 0; $j < count($Hotels); $j ++) {
        $Code = $Hotels[$j]['Code'];
        $shid = $Code;
        $sfilter[] = " sid='$Code' ";
        $Name = $Hotels[$j]['Name'];
        $CategoryCode = $Hotels[$j]['CategoryCode'];
        $CategoryName = $Hotels[$j]['CategoryName'];
        $Description = $Hotels[$j]['Description'];
        $Latitude = $Hotels[$j]['Latitude'];
        $Longitude = $Hotels[$j]['Longitude'];
        $Address = $Hotels[$j]['Address'];
        $MealPlans = $Hotels[$j]['MealPlans'];
        for ($jAux = 0; $jAux < count($MealPlans); $jAux ++) {
            $CodeMealPlans = $MealPlans[$jAux]['Code'];
            $Name = $MealPlans[$jAux]['Name'];
            $Options = $MealPlans[$jAux]['Options'];
            for ($jAux2 = 0; $jAux2 < count($Options); $jAux2 ++) {
                $RoomCandidateId = $Options[$jAux2]['RoomCandidateId'];
                $Status = $Options[$jAux2]['Status'];
                $Adults = $Options[$jAux2]['Adults'];
                $Childs = $Options[$jAux2]['Childs'];
                $Enfants = $Options[$jAux2]['Enfants'];
                
                $Rooms = $Options[$jAux2]['Rooms'];
                for ($jAux3 = 0; $jAux3 < count($Rooms); $jAux3 ++) {
                    $Id = $Rooms[$jAux3]['Id'];
                    $CodeRooms = $Rooms[$jAux3]['Code'];
                    $NameRooms = $Rooms[$jAux3]['Name'];
                    $RateCode = $Rooms[$jAux3]['RateCode'];
                    $RateName = $Rooms[$jAux3]['RateName'];
                    $NonRefundable = $Rooms[$jAux3]['NonRefundable'];
                    $Package = $Rooms[$jAux3]['Package'];
                    $Senior = $Rooms[$jAux3]['Senior'];
                    $Residents = $Rooms[$jAux3]['Residents'];
                    $Remarks = $Rooms[$jAux3]['Remarks'];
                    $Price = $Rooms[$jAux3]['Price'];
                    if (count($Price) > 0) {
                        $CurrencyCode = $Price['CurrencyCode'];
                        $Amount = $Price['Amount'];
                        $Commission = $Price['Commission'];
                        $Binding = $Price['Binding'];
                    } else {
                        $CurrencyCode = "";
                        $Amount = "";
                        $Commission = "";
                        $Binding = "";
                    }
                    
                    $CancelPenalties = $Rooms[$jAux3]['CancelPenalties'];
                    for ($iAux4 = 0; $iAux4 < count($CancelPenalties); $iAux4 ++) {
                        $HoursBefore = $CancelPenalties[$jAux4]['HoursBefore'];
                        $Description = $CancelPenalties[$jAux4]['Description'];
                        
                        $Penalty = $CancelPenalties[$jAux4]['Penalty'];
                        if (count($Penalty) > 0) {
                            $PenaltyType = $Penalty['PenaltyType'];
                            $CurrencyCode2 = $Penalty['CurrencyCode'];
                            $Value = $Penalty['Value'];
                            $IsNetPrice = $Penalty['IsNetPrice'];
                        } else {
                            $PenaltyType = "";
                            $CurrencyCode2 = "";
                            $Value = "";
                            $IsNetPrice = "";
                        }
                    }
                    for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                        if (is_array($tmp[$shid]['details'][$zRooms])) {
                            $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                        } else {
                            $baseCounterDetails = 0;
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $Code;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $NameRooms;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $Name;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-60";
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $Amount;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $Code;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $CurrencyCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_code'] = $CodeRooms;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $NameRooms;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $CodeRooms;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateCode'] = $RateCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateName'] = $RateName;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = $NonRefundable;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = $Amount;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($CodeMealPlans);
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $CurrencyCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                        $pricebreakdown = array();
                        $pricebreakdownCount = 0;
                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                            $amount = $Amount / $noOfNights;
                            if ($coming2Markup != 0) {
                                $amount = $amount + (($amount * $coming2Markup) / 100);
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
                            if ($coming2Markup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                            $pricebreakdownCount ++;
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                        /*
                         * $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $Description;
                         * $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicies'] = $Description;
                         */
                    }
                }
            }
        }
        $coming2 = true;
    }
}
if ($coming2 == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mcoming2 where " . $sfilter;
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
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 60;
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_coming2');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_coming2');
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
?>