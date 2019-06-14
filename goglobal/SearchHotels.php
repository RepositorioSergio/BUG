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
error_log("\r\n COMECOU GOGLOBAL MANHA \r\n", 3, "/srv/www/htdocs/error_log");
$sfilter = array();
$goglobal = false;
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
$sql = "select value from settings where name='enablegoglobal' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_goglobal = $affiliate_id;
} else {
    $affiliate_id_goglobal = 0;
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
    $sql = "select value from settings where name='goglobalDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_goglobal";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='GoGlobalPassword' and affiliate_id=$affiliate_id_goglobal" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GoGlobalPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='GoGlobalMarkup' and affiliate_id=$affiliate_id_goglobal" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GoGlobalMarkup = (double) $row_settings['value'];
} else {
    $GoGlobalMarkup = 0;
}
$sql = "select value from settings where name='GoGlobalLoginEmail' and affiliate_id=$affiliate_id_goglobal" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GoGlobalLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='GoGlobalServiceURL' and affiliate_id=$affiliate_id_goglobal" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GoGlobalServiceURL = $row_settings['value'];
}
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$date = new Datetime();
$timestamp = $date->format('U');

$goglobalServiceURL = 'http://xml.qa.goglobal.travel/XMLWebService.asmx';
$agencyID = '1521636';
$gogloballogin = 'CLUB1XML';
$goglobalpassword = 'andrade1998';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:gog="http://www.goglobal.travel/">
<soapenv:Header/>
<soapenv:Body>
   <gog:MakeRequest>
      <gog:requestType>11</gog:requestType>
      <gog:xmlRequest><![CDATA[
        <Root>
            <Header>
                <Agency>' . $agencyID . '</Agency>
                <User>' . $GoGlobalLoginEmail . '</User>
                <Password>' . $GoGlobalPassword . '</Password>
                <Operation>HOTEL_SEARCH_REQUEST</Operation>
                <OperationType>Request</OperationType>
            </Header>
            <Main Version="2" ResponseFormat="JSON">
                <SortOrder>1</SortOrder>
                <FilterPriceMin>0</FilterPriceMin>
                <FilterPriceMax>10000</FilterPriceMax>
                <MaximumWaitTime>230</MaximumWaitTime>
                <MaxResponses>1000</MaxResponses>
                <FilterRoomBasises>
                    <FilterRoomBasis></FilterRoomBasis>
                </FilterRoomBasises>
                <HotelName></HotelName>
                <Apartments>false</Apartments>
                <CityCode>75</CityCode>
                <ArrivalDate>' . strftime("%Y-%m-%d", $from) . '</ArrivalDate>
                <Nights>' . $noOfNights . '</Nights>
                <Rooms>
                    <Room Adults="2" RoomCount="1"></Room>
                </Rooms>
            </Main>
        </Root>
      ]]></gog:xmlRequest>
   </gog:MakeRequest>
</soapenv:Body>
</soapenv:Envelope>';
//error_log("\r\n$raw\r\n", 3, "/srv/www/htdocs/error_log");

if ($GoGlobalServiceURL != "" and $GoGlobalLoginEmail != "" and $GoGlobalPassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GoGlobalServiceURL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: text/xml; charset=utf-8",
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
        $insert->into('log_goglobal');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $GoGlobalServiceURL . $raw,
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
    
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $MakeRequestResult = $inputDoc->getElementsByTagName("MakeRequestResult");
    if ($MakeRequestResult->length > 0) {
        $response = $MakeRequestResult->item(0)->nodeValue;
    } else {
        $response = "";
    }
    
    error_log("\r\n RESPONSE  $response\r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);
    $Hotels = $response['Hotels'];

    // Results
    for ($i = 0; $i < count($Hotels); $i ++) {
        $HotelName = $Hotels[$i]['HotelName'];
        $HotelCode = $Hotels[$i]['HotelCode'];
        $shid = $HotelCode;
        $sfilter[] = " sid='$HotelCode' ";
        $CountryId = $Hotels[$i]['CountryId'];
        $CityId = $Hotels[$i]['CityId'];
        $Location = $Hotels[$i]['Location'];
        $Thumbnail = $Hotels[$i]['Thumbnail'];
        $Longitude = $Hotels[$i]['Longitude'];
        $Latitude = $Hotels[$i]['Latitude'];
        //error_log("\r\n HotelCode $HotelCode  \r\n", 3, "/srv/www/htdocs/error_log");
        
        $Offers = $Hotels[$i]['Offers'];
        for ($j = 0; $j < count($Offers); $j ++) {
            $HotelSearchCode = $Offers[$j]['HotelSearchCode'];
            $CxlDeadLine = $Offers[$j]['CxlDeadLine'];
            $NonRef = $Offers[$j]['NonRef'];
            $RoomBasis = $Offers[$j]['RoomBasis'];
            $Availability = $Offers[$j]['Availability'];
            $TotalPrice = $Offers[$j]['TotalPrice'];
            $Currency = $Offers[$j]['Currency'];
            $Category = $Offers[$j]['Category'];
            $Remark = $Offers[$j]['Remark'];
            $Special = $Offers[$j]['Special'];
            $Preferred = $Offers[$j]['Preferred'];
            
            $room = '';
            $Rooms = $Offers[$j]['Rooms'];
            for ($k = 0; $k < count($Rooms); $k++) {
                $room = $Rooms[$k];
                
                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                    if (is_array($tmp[$shid]['details'][$zRooms])) {
                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                    } else {
                        $baseCounterDetails = 0;
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $HotelCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $HotelCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelSearchCode'] = $HotelSearchCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $room;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-4";
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $TotalPrice;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $HotelCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $Currency;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_code'] = $RoomBasis;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $room;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $Category;
                    /*
                        * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateCode'] = $RateCode;
                        * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateName'] = $RateName;
                        */
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = false;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = $NonRef;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $Currency;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = $TotalPrice;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($RoomBasis);
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $Currency;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $amount = $TotalPrice / $noOfNights;
                        if ($GoGlobalMarkup != 0) {
                            $amount = $amount + (($amount * $GoGlobalMarkup) / 100);
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
                        if ($GoGlobalMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $Currency;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                    /*
                        * $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $Description;
                        * $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicies'] = $Description;
                        */
                }
                
            }
        }
    }
    $goglobal = true;
}

//error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($goglobal == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mgoglobal where " . $sfilter;
        //error_log("\r\n SQL  $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 4;
        //error_log("\r\n QUERY  $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_goglobal');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_goglobal');
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