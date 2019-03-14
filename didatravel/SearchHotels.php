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
 error_log("\r\n COMECOU DIDATRAVEL QUINTA FEIRA MANHA AGORA 12H00 \r\n", 3, "/srv/www/htdocs/error_log");
unset($tmp);
$sfilter = array();
$didatravel = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
$sql = "select city_xml29 from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml29 = $row_settings["city_xml29"];
} else {
    $city_xml29 = "";
}
$sql = "select value from settings where name='enabledidatravel' and affiliate_id=$affiliate_id";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $affiliate_id_didatravel = $affiliate_id;
} else {
    $affiliate_id_didatravel = 0;
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
    $sql = "select value from settings where name='didatravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_didatravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}

$sql = "select value from settings where name='didatravelclientid' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravelclientid = $row_settings['value'];
}
// error_log("\r\n didatravelclientid $didatravelclientid \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='didatravellicensekey' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravellicensekey = $row_settings['value'];
}
// error_log("\r\n didatravellicensekey $didatravellicensekey \r\n", 3, "/srv/www/htdocs/error_log");

$sql = "select value from settings where name='didatravelMarkup' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravelMarkup = (double) $row_settings['value'];
} else {
    $didatravelMarkup = 0;
}
$sql = "select value from settings where name='didatravelserviceurl' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravelserviceurl = $row_settings['value'];
}
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$date = new Datetime();
$timestamp = $date->format('U');
$count = 0;
$city_xml29 = 178308;
if ($city_xml29 != "") {
    //
    // Utilizar em detalhes
    //
    // Procurar por Hotel Id - "HotelIDList": [5982, 11, 7017, 239133, 1672],
    //
    // City Search only allows LowestPriceOnly, otherwise set to false
    //
    // "Destination": { "CityCode": "' . $city_xml29 . '" },
    //

    $LowestPriceOnly = "false";
    $raw = '
    {
        "Header": { "ClientID": "' . $didatravelclientid . '", "LicenseKey": "' . $didatravellicensekey . '" },
        "HotelIDList": [5982, 11, 7017, 239133, 1672],
        "CheckOutDate": "' . strftime("%Y-%m-%d", $to) . '",
        "CheckInDate": "' . strftime("%Y-%m-%d", $from) . '",
        "IsRealTime": {
            "Value": false,
            "RoomCount": ' . $rooms . '
        }, 
        "LowestPriceOnly": ' . $LowestPriceOnly . ',
        "RealTimeOccupancy": {';
    for ($i = 0; $i < count($selectedAdults); $i ++) {
        $raw .= '"ChildCount": ' . $selectedChildren[$i] . ',
                        "AdultCount": ' . $selectedAdults[$i] . ',
                        ';
        if ($selectedChildren[$i] > 0) {
            $count = count($selectedChildren[$i]);
            $raw .= '"ChildAgeDetails": [';
            for ($j = 0; $j < $selectedChildren[$i]; $j ++) {
                $raw .= $selectedChildrenAges[$i][$j];
                if ($j < $count - 1) {
                    $raw .= ',';
                }
            }
            $raw .= ']';
        } else {
            $raw .= '"ChildAgeDetails": []';
        }
        if ($i < count($selectedAdults) - 1) {
            $raw .= ',';
        }
    }
    
    $raw .= '
        },
        "Nationality": "' . $sourceMarket . '",
        "Currency": "CNY"
      }
    ';
    
    if ($didatravelserviceurl != "" and $didatravelclientid != "" and $didatravellicensekey != "") {
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $didatravelserviceurl . "api/rate/pricesearch?\$format=json");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        // if ($response === false) {
        // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
        // } else {
        // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
        // }
        // error_log("\r\n $PalladiumHotelGroupserviceurl \r\n", 3, "/srv/www/htdocs/error_log");
        error_log("\r\n $raw \r\n", 3, "/srv/www/htdocs/error_log");
         //error_log("\r\n RESPONSE DIDA: $response \r\n", 3, "/srv/www/htdocs/error_log");
        curl_close($ch);
        // Descomentar para ver o resultado do provider
        // Nao esquecer de alterar o session id para testar por causa de cache
        // echo $response;
        // die();
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_didatravel');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $didatravelserviceurl . $raw,
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
        
        $array = json_decode($response, true);
        
        //error_log("\r\n RESPONSE DIDA: $response \r\n", 3, "/srv/www/htdocs/error_log");
        $Success = $array['Success'];
        $PriceDetails = $Success['PriceDetails'];
        $HotelList = $PriceDetails['HotelList'];
        foreach ($HotelList as $key => $valueHotelList) {
            $HotelID = $valueHotelList['HotelID'];
            $shid = $HotelID;
            $sfilter[] = " sid='$HotelID' ";
            // error_log("\r\n HotelID : $HotelID \r\n", 3, "/srv/www/htdocs/error_log");
            $Destination = $valueHotelList['Destination'];
            $CityCode = $Destination['CityCode'];
            $LowestPrice = $valueHotelList['LowestPrice'];
            $Value = $LowestPrice['Value'];
            $HotelName = $valueHotelList['HotelName'];
            
            $RatePlanList = $valueHotelList['RatePlanList'];
            foreach ($RatePlanList as $key => $valueRatePlanList) {
                $TotalPrice = $valueRatePlanList['TotalPrice'];
                 error_log("\r\n TotalPrice : $TotalPrice \r\n", 3, "/srv/www/htdocs/error_log");
                $RoomStatus = $valueRatePlanList['RoomStatus'];
                $BreakfastType = $valueRatePlanList['BreakfastType'];
                $BedType = $valueRatePlanList['BedType'];

                $sql = "select Name from didatravel_bedtypes where ID=$BedType";
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_settings = $statement->execute();
                $row_settings->buffer();
                if ($row_settings->valid()) {
                    $row_settings = $row_settings->current();
                    $BedTypeName = $row_settings['Name'];
                }
                error_log("\r\n BedTypeName : $BedTypeName \r\n", 3, "/srv/www/htdocs/error_log");

                $sql = "select Name from didatravel_breakfasts where ID=$BreakfastType";
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_settings = $statement->execute();
                $row_settings->buffer();
                if ($row_settings->valid()) {
                    $row_settings = $row_settings->current();
                    $BreakFastName = $row_settings['Name'];
                } 
                
                $PriceList = $valueRatePlanList['PriceList'];
                foreach ($PriceList as $key => $valuePriceList) {
                    $StayDate = $valuePriceList['StayDate'];
                    $Price = $valuePriceList['Price'];
                }
                
                $RatePlanCancellationPolicyList = $valueRatePlanList['RatePlanCancellationPolicyList'];
                foreach ($RatePlanCancellationPolicyList as $key => $valueRatePlanCancellationPolicyList) {
                    $Amount = $valueRatePlanCancellationPolicyList['Amount'];
                    $FromDate = $valueRatePlanCancellationPolicyList['FromDate'];
                }
                $StandardOccupancy = $valueRatePlanList['StandardOccupancy'];
                $InventoryCount = $valueRatePlanList['InventoryCount'];
                $MaxOccupancy = $valueRatePlanList['MaxOccupancy'];
                $Currency = $valueRatePlanList['Currency'];
                $RatePlanName = $valueRatePlanList['RatePlanName'];
                $RatePlanID = $valueRatePlanList['RatePlanID'];
                
                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                    if (is_array($tmp[$shid])) {
                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                    } else {
                        $baseCounterDetails = 0;
                    }


                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $HotelID;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-29";
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RatePlanName;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $RatePlanID;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomType'] = $BedType;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RatePlanID'] = $RatePlanID;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $RatePlanName;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TotalPrice;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $TotalPrice;
                    
                    /*
                     * if ($PromotionCode != "") {
                     * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                     * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $PromotionCode;
                     * } else {
                     */
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                    // }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($BreakFastName);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $amount = $TotalPrice / $noOfNights;
                        if ($didatravelMarkup != 0) {
                            $amount = $amount + (($amount * $didatravelMarkup) / 100);
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
                        if ($didatravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $Currency;
                    /*
                     * $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $Description;
                     * $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicies'] = $Description;
                     */
                }
            }
        }
        $didatravel = true;
    }
}
if ($didatravel == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mdidatravel where " . $sfilter;
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
        $supplier = 29;
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_didatravel');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_didatravel');
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