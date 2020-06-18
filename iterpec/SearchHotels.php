<?php
$scurrency = strtoupper($currency);
use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$iterpec = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
error_log("\r\n Start ITERPEC\r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select name, country_id, zone_id,city_xml19, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml19 = $row_settings["city_xml19"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml19 = "";
}
$affiliate_id = 0;
$sql = "select value from settings where name='enableiterpec' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_iterpec = $affiliate_id;
} else {
    $affiliate_id_iterpec = 0;
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
    $sql = "select value from settings where name='iterpecDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='iterpeclogin' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpeclogin = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecpassword' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='iterpecServiceURL' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $iterpecServiceURL = $row['value'];
}
$sql = "select value from settings where name='iterpecMarkup' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecMarkup = (double) $row_settings['value'];
} else {
    $iterpecMarkup = 0;
}
$sql = "select value from settings where name='iterpecaffiliates_id' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecaffiliates_id = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecb2cMarkup' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecb2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecbranches_id' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecbranches_id = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecParallelSearch' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecParallelSearch = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecSearchSortorder' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecSearchSortorder = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecTimeout' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecTimeout = (int)$row_settings['value'];
}


$raw = '{
    "Credential": {
      "Username": "' . $iterpeclogin . '",
      "Password": "' . $iterpecpassword . '"
    },
    "Criteria":{
      "DestinationId": 1003937,
      "NumNights": ' . $noOfNights . ',
      "ReturnHotelStaticData": true,
      "ReturnOnRequestRooms": true,
      "CheckinDate": "' . strftime("%Y-%m-%d", $from) . '",
      "MainPaxCountryCodeNationality": "' . $sourceMarket . '",
      "SearchRooms": [';
      $length = 0;
      for ($r=0; $r < count($selectedAdults); $r++) { 
          $raw .= '{
            "NumAdults": ' . $selectedAdults[$r] . ',';
            if ($selectedChildren[$r] > 0) {
                $raw .= '"ChildAges": [';
                $tam = $selectedChildren[$r];
                for ($z=0; $z < $selectedChildren[$r]; $z++) { 
                    if ($tam == ($selectedChildren[$r] - 1)) {
                        $raw .= $selectedChildrenAges[$r][$z];
                    } else {
                        $raw .= $selectedChildrenAges[$r][$z] . ',';
                    }
                    $tam = $tam + 1;
                }
                $raw .= ']'; 
            }
            $raw .= '"Quantity": ' . $rooms . ' }';
            if ($length < (count($selectedAdults) - 1)) {
                $raw .= ',';
            }
            $length = $length + 1;
      }
    $raw .= '],
      "Filters": {
         "MinPrice": 10.00,
         "MaxPrice": 1000.00,
         "CheapestRoomOnly": true
      }
    }
  }';
 error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
if ($iterpecServiceURL != "" and $iterpeclogin != "" and $iterpecpassword != "") {
    $headers = array(
        "Content-type: application/json",
        "Content-length: " . strlen($raw)
    );
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $iterpecServiceURL . 'ws/Rest/Hotel.svc/Search');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, $iterpecTimeout);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    error_log("\r\nResponse: $response \r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_iterpec');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $url . $raw,
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
    $TimeSpan = $response['TimeSpan'];
    $Token = $response['Token'];
    $TotalTime = $response['TotalTime'];

    $Hotels = $response['Hotels'];
    for ($i=0; $i < count($Hotels); $i++) { 
        $HotelId = $Hotels[$i]['HotelId'];
        $shid = $HotelId;
        $sfilter[] = " sid='$HotelId' ";
        $Name = $Hotels[$i]['Name'];
        $Longitude = $Hotels[$i]['Longitude'];
        $Latitude = $Hotels[$i]['Latitude'];
        $Category = $Hotels[$i]['Category'];
        $Address = $Hotels[$i]['Address'];
        $CustomFields = $Hotels[$i]['CustomFields'];
        for ($iAux=0; $iAux < count($CustomFields); $iAux++) { 
            $CustomFieldsName = $CustomFields[$iAux]['Name'];
            $CustomFieldsValue = $CustomFields[$iAux]['Value'];
        }
        $Rooms = $Hotels[$i]['Rooms'];
        for ($j=0; $j < count($Rooms); $j++) { 
            $RoomId = $Rooms[$j]['Id'];
            $BoardDescription = $Rooms[$j]['BoardDescription'];
            $CustomFields = $Rooms[$j]['CustomFields'];
            $HasBreakfast = $Rooms[$j]['HasBreakfast'];
            $IsAvailable = $Rooms[$j]['IsAvailable'];
            $IsNonRefundable = $Rooms[$j]['IsNonRefundable'];
            $IsPrePayment = $Rooms[$j]['IsPrePayment'];
            $MediaRoomId = $Rooms[$j]['MediaRoomId'];
            $NumAdults = $Rooms[$j]['NumAdults'];
            $PayDirectToHotel = $Rooms[$j]['PayDirectToHotel'];
            $Quantity = $Rooms[$j]['Quantity'];
            $RoomDescription = $Rooms[$j]['RoomDescription'];
            $SellingPricePerRoom = $Rooms[$j]['SellingPricePerRoom'];
            $SellingPriceCurrency = $SellingPricePerRoom['Currency'];
            $SellingPriceValue = $SellingPricePerRoom['Value'];
            $TotalSellingPrice = $Rooms[$j]['TotalSellingPrice'];
            $Currency = $TotalSellingPrice['Currency'];
            $Value = $TotalSellingPrice['Value'];
            $ThumbUrl = $Rooms[$j]['ThumbUrl'];
            $CancellationPolicies = $Rooms[$j]['CancellationPolicies'];
            for ($k=0; $k < count($CancellationPolicies); $k++) { 
                $EndDate = $CancellationPolicies[$k]['EndDate'];
                $StartDate = $CancellationPolicies[$k]['StartDate'];
                $CancellationPoliciesValue = $CancellationPolicies[$k]['Value'];
                $ValueCurrency = $CancellationPoliciesValue['Currency'];
                $Value2 = $CancellationPoliciesValue['Value'];
            }
            $total = $Value;
            $nettotal = $Value;
            $zRooms = 0;
            if (is_array($tmp[$shid])) {
                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
            } else {
                $baseCounterDetails = 0;
            }
            
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $Name;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $HotelId;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $RoomId;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-48";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomDescription;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $RoomDescription;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['token'] = $Token;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $nettotal;
            if ($rtsMarkup != 0) {
                $total = $total + (($total * $rtsMarkup) / 100);
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
            if ($rtsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
            try {
                $sql = "select mapped from board_mapping where description='" . addslashes($BreakfastTypeName) . "'";
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_board_mapping = $statement->execute();
                $row_board_mapping->buffer();
                if ($row_board_mapping->valid()) {
                    $row_board_mapping = $row_board_mapping->current();
                    $BreakfastTypeName = $row_board_mapping["mapped"];
                }
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($BreakfastTypeName);
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
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $Currency;
            //
            // Special
            //
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
            //
            // Cancellation Policies
            //
            $from_date = date('Y-m-d',strtotime($StartDate));
            $to_date = date('Y-m-d',strtotime($EndDate));
            $cancelpolicy = "If you cancel booking " . $from_date . " To date " . $to_date . " cost " . $Value2. "" . $ValueCurrency;
            if ($IsNonRefundable !== "false") {
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $translator->translate($cancelpolicy);
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_details'] = $translator->translate($cancelpolicy);
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $to_date);
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $to_date;
            } else {
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy'] = $cancelpolicy;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $to_date);;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $to_date;
            }
            $count = $count + 1;
        }
    }
    $iterpec = true;
}
            
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($iterpec == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_miterpec where " . $sfilter;
        error_log("\r\n SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        $result2->buffer();
        error_log("\r\n PASSOU 1 \r\n", 3, "/srv/www/htdocs/error_log");
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            error_log("\r\n PASSOU 4 \r\n", 3, "/srv/www/htdocs/error_log");
            foreach ($resultSet2 as $row2) {
                error_log("\r\n PASSOU 5 \r\n", 3, "/srv/www/htdocs/error_log");
                // $sidfilter[] = "id=" . $row2->hid;
                $sidfilter[] = $row2->hid;
                if (is_array($hotels_array[$row2->hid])) {
                    error_log("\r\n PASSOU 6 \r\n", 3, "/srv/www/htdocs/error_log");
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
        error_log("\r\n PASSOU 2 \r\n", 3, "/srv/www/htdocs/error_log");
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    error_log("\r\n PASSOU 3 \r\n", 3, "/srv/www/htdocs/error_log");
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 48;
        error_log("\r\n Query: $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_iterpec');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_iterpec');
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
error_log("\r\n End ITERPEC\r\n", 3, "/srv/www/htdocs/error_log");
?>