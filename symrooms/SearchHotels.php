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
$symrooms = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
error_log("\r\nSearch Hotels Symrooms\r\n", 3, "/srv/www/htdocs/error_log");
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
$city_xml19 = "HKG";
error_log("\r\n TODO - RTS - city_xml19 : $city_xml19 \r\n", 3, "/srv/www/htdocs/error_log");
$affiliate_id = 0;
$sql = "select value from settings where name='enablerts' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_rts = $affiliate_id;
} else {
    $affiliate_id_rts = 0;
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
    $sql = "select value from settings where name='rtsDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='rtsID' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsID = $row_settings['value'];
}
$sql = "select value from settings where name='rtsPassword' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='rtsSiteCode' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsSiteCode = $row_settings['value'];
}
$sql = "select value from settings where name='rtsRequestType' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsRequestType = $row_settings['value'];
}
$sql = "select value from settings where name='rtsServiceURL' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsServiceURL = $row['value'];
}
$sql = "select value from settings where name='rtsMarkup' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsMarkup = (double) $row_settings['value'];
} else {
    $rtsMarkup = 0;
}

$pesquisa = "hotel_id";

$url = "https://api.travelgatex.com/";
if ($url != "") {
    if ($pesquisa == "hotel_id") {
        $raw = '{"query":"{\n  hotelX {\n    search( criteria: {\n                checkIn: \"2019-12-23\",\n                checkOut: \"2019-12-24\",\n                hotels: [\"1\"],\n                occupancies: [ {paxes: [{age: 30}, {age: 30}]}]},\n                settings: {\n                      client: \"Demo_Client\",\n                      testMode: true,\n                      context: \"HOTELTEST\"}) {\n      options {\n        id\n        supplierCode\n        hotelCode\n        hotelName\n        boardCode\n    paymentType\n    status\n    rooms {\n    occupancyRefId\n     code\n   description\n    refundable\n    units\n    roomPrice {\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n    }\n    }\n    }\n  beds {\n    type\n    description\n    count\n    shared\n    }\n    ratePlans {\n    code\n    name\n    effectiveDate\n  expireDate\n   }\n    promotions {\n    code\n    name\n    effectiveDate\n    expireDate\n  }\n  }\n  supplements {\n   code\n    name\n    description\n    supplementType\n    chargeType\n    mandatory\n    durationType\n    quantity\n    unit\n    effectiveDate\n    expireDate\n    resort {\n    code\n    name\n    description\n    }\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n  }\n  }\n  }\n   surcharges {\n    chargeType\n    description\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n  }\n  }\n  }\n    rateRules \n    cancelPolicy {\n    refundable\n    cancelPenalties {\n    hoursBefore\n    penaltyType\n    currency\n   value\n  }\n  }\n      price {\n          net\n          currency\n        }\n    remarks\n    token\n    id\n      }\n      errors {\n        code\n        type\n        description\n      }\n      warnings {\n        code\n        type\n        description\n      }\n    }\n  }\n}"}';
    } else {
        $raw = '{"query":"{\n  hotelX {\n  search(\n  criteria: {\n  checkIn: \"2019-12-23\",\n  checkOut: \"2019-12-24\",\n  occupancies: [{\n paxes: [\n  {age: 30}, {age: 30}]}],\n  language: \"es\",\n  nationality: \"ES\",\n  currency: \"EUR\",\n  market: \"ES\",\n  destinations: [\"1149\", \"AD\", \"31256\"]\n  },\n  settings: {\n  suppliers: {\n code: \"HOTELTEST\"},\n  plugins: {\n  step: REQUEST,\n  pluginsType: [{\n  type: POST_STEP,\n  name: \"search_by_destination\",\n  parameters: [{\n  key: \"accessID\",\n  value: \"422\"}]\n }]\n  },\n  businessRules: null,\n  timeout: 24700,\n  context: \"HOTELTEST\",\n  client: \"Demo_Client\",\n  testMode: true},\n  filter: {\n  access: {\n  includes: []\n  }\n  }) {\n  options {\n  surcharges {\n  chargeType\n  mandatory\n  description\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  accessCode\n  supplierCode\n  market\n  hotelCode\n  hotelName\n  boardCode\n  paymentType\n  status\n  occupancies {\n  id\n  paxes {\n  age\n  }\n  }\n  rooms {\n occupancyRefId\n  code\n  description\n  refundable\n  units\n  roomPrice {\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  breakdown {\n  effectiveDate\n  expireDate\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  beds {\n  type\n  description\n  count\n  shared\n  }\n  ratePlans {\n  code\n  name\n  effectiveDate\n  expireDate\n  }\n  promotions {\n  code\n  name\n  effectiveDate\n  expireDate\n  }\n  }\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  addOns {\n  distribute\n  }\n  supplements {\n  code\n  name\n  description\n  supplementType\n  chargeType\n  mandatory\n  durationType\n  quantity\n  unit\n  effectiveDate\n  expireDate\n  resort {\n  code\n  name\n  description\n  }\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  surcharges {\n  chargeType\n  description\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  rateRules\n cancelPolicy {\n  refundable\n  cancelPenalties {\n  hoursBefore\n  penaltyType\n  currency\n  value\n  }\n  }\n  remarks\n  token\n  id\n  }\n  }\n  }\n  }"}';
    }
    
    error_log("\r\nSymRooms Request: $raw\r\n", 3, "/srv/www/htdocs/error_log");
    $headers = array(
        'Authorization: Apikey 64780338-49c8-4439-7c7d-d03c2033b145',
        'Accept-Encoding: gzip, deflate, br',
        'Content-Type: application/json',
        'Accept: application/json',
        'Connection: keep-alive',
        'DNT: 1',
        'Origin: https://api.travelgatex.com'
    );
    
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    error_log("\r\nResponse SYM: $response \r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_symrooms');
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
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    
    $response = json_decode($response, true);
    
    $raterule = "";
    
    $data = $response['data'];
    $hotelX = $data['hotelX'];
    $search = $hotelX['search'];
    
    // options
    $options = $search['options'];
    for ($i = 0; $i < count($options); $i ++) {
        $id = $options[$i]['id'];
        $supplierCode = $options[$i]['supplierCode'];
        $hotelCode = $options[$i]['hotelCode'];
        $shid = $hotelCode;
        $sfilter[] = " sid='$hotelCode' ";
        $hotelName = $options[$i]['hotelCode'];
        $boardCode = $options[$i]['boardCode'];
        $paymentType = $options[$i]['paymentType'];
        $status = $options[$i]['status'];
        $token = $options[$i]['token'];
        
        // supplements
        $supplements = $options[$i]['supplements'];
        // surcharges
        $surcharges = $options[$i]['surcharges'];
        if (count($surcharges) > 0) {
            for ($j = 0; $j < count($surcharges); $j ++) {
                $chargeType = $surcharges[$j]['chargeType'];
                $scdescription = $surcharges[$j]['description'];
                $price = $roomPrice['price'];
                $sccurrency = $price['currency'];
                $scbinding = $price['binding'];
                $scnet = $price['net'];
                $scgross = $price['gross'];
                $exchange = $price['exchange'];
                $sccurrency = $exchange['currency'];
                $scrate = $exchange['rate'];
            }
        }
        // rateRules
        $rateRules = $options[$i]['rateRules'];
        if (count($rateRules) > 0) {
            for ($j = 0; $j < count($rateRules); $j ++) {
                $raterule = $rateRules[$j];
            }
        }
        
        $price = $options[$i]['price'];
        $net = $price['net'];
        $currency = $price['currency'];
        
        // cancelPolicy
        $cancelPolicy = $options[$i]['cancelPolicy'];
        $CPrefundable = $cancelPolicy['refundable'];
        // cancelPenalties
        $cancelPenalties = $cancelPolicy['cancelPenalties'];
        for ($c = 0; $c < count($cancelPenalties); $c ++) {
            $hoursBefore = $cancelPenalties[$c]['hoursBefore'];
            $penaltyType = $cancelPenalties[$c]['penaltyType'];
            $currency = $cancelPenalties[$c]['currency'];
            $value = $cancelPenalties[$c]['value'];
        }
        
        // rooms
        $rooms = $options[$i]['rooms'];
        for ($r = 0; $r < count($rooms); $r ++) {
            $occupancyRefId = $rooms[$r]['occupancyRefId'];
            $room_code = $rooms[$r]['code'];
            $description = $rooms[$r]['description'];
            $refundable = $rooms[$r]['refundable'];
            $units = $rooms[$r]['units'];
            
            $promotions = $rooms[$r]['promotions'];
            if (count($promotions) > 0) {
                for ($l = 0; $l < count($promotions); $l ++) {
                    $promotionscode = $promotions[$l]['code'];
                    $promotionsname = $promotions[$l]['name'];
                    $promotionseffectiveDate = $promotions[$l]['effectiveDate'];
                    $promotionscodeexpireDate = $promotions[$l]['expireDate'];
                }
            }
            
            // roomPrice
            $roomPrice = $rooms[$r]['roomPrice'];
            $price = $roomPrice['price'];
            $currency = $price['currency'];
            $binding = $price['binding'];
            $net = $price['net'];
            $gross = $price['gross'];
            $exchange = $price['exchange'];
            $currency = $exchange['currency'];
            $rate = $exchange['rate'];
            
            // beds
            $beds = $rooms[$r]['beds'];
            for ($k = 0; $k < count($beds); $k ++) {
                $type = $beds[$k]['type'];
                $descriptionbeds = $beds[$k]['description'];
                $count = $beds[$k]['count'];
                $shared = $beds[$k]['shared'];
            }
            
            $ratePlans = $rooms[$r]['ratePlans'];
            for ($y = 0; $y < count($ratePlans); $y ++) {
                $ratePlanscode = $ratePlans[$y]['code'];
                $name = $ratePlans[$y]['name'];
                $effectiveDate = $ratePlans[$y]['effectiveDate'];
                $expireDate = $ratePlans[$y]['expireDate'];
            }
            
            for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                if (is_array($tmp[$shid])) {
                    $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                } else {
                    $baseCounterDetails = 0;
                }
                
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $hotelName;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $hotelCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $room_code;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $hotelCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $shid;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['optionRefId'] = $id;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                // cancellationType nao existe
                // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $c_type;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-65";
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $description;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $description;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $type;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rate_code'] = $rate;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ratePlanscode'] = $ratePlanscode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $gross;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $net;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($ratePlanscode);
                $pricebreakdown = array();
                $pricebreakdownCount = 0;
                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                    $amount = $gross / $noOfNights;
                    if ($rtsMarkup != 0) {
                        $amount = $amount + (($amount * $rtsMarkup) / 100);
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
                    if ($rtsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                
                /*
                 * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $CancelCost;
                 * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $DeadLineCancel;
                 */
                $count = $count + 1;
            }
        }
        $symrooms = true;
    }
}
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($symrooms == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_msymrooms where " . $sfilter;
        // error_log("\r\n SYM SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 65;
        // error_log("\r\SYM QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_symrooms');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_symrooms');
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
error_log("\r\nEOF Hotels Symrooms\r\n", 3, "/srv/www/htdocs/error_log");
?>