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
error_log("\r\n COMECOU TBO SEXTA \r\n", 3, "/srv/www/htdocs/error_log");
$sfilter = array();
$tbo = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
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

$user = 'wingstest';
$pass = 'Win@59491374';

$url = "https://api.tbotechnology.in/HotelAPI_V7/HotelService.svc";

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing" >
    <hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
    </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/HotelSearch</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelSearchRequest>
        <hot:CheckInDate>' . strftime("%Y-%m-%d", $from) . '</hot:CheckInDate>
        <hot:CheckOutDate>' . strftime("%Y-%m-%d", $to) . '</hot:CheckOutDate>
        <hot:CountryName>United Arab Emirates</hot:CountryName>
        <hot:CityName>Dubai</hot:CityName>
        <hot:CityId>115936</hot:CityId>
        <hot:IsNearBySearchAllowed>false</hot:IsNearBySearchAllowed>
        <hot:NoOfRooms>' . $rooms . '</hot:NoOfRooms>
        <hot:GuestNationality>AE</hot:GuestNationality>
        <hot:IsRoomInfoRequired>true</hot:IsRoomInfoRequired>
        <hot:RoomGuests>';
        for ($r = 0; $r < count($selectedAdults); $r ++) {
            $raw = $raw . '<hot:RoomGuest AdultCount="' . $selectedAdults[$r] . '" ChildCount="' . $selectedChildren[$r] . '">';
            if ($selectedChildren[$r] > 0) {
                $raw = $raw . '<hot:ChildAge>';
                for ($y=0; $y < $selectedChildren[$r]; $y++) { 
                    $raw = $raw . '<hot:int>' . $selectedChildrenAges[$r][$y] . '</hot:int>';
                }
                $raw = $raw . '</hot:ChildAge></hot:RoomGuest>';
            } else {
                $raw = $raw . '</hot:RoomGuest>';
            }
            
        }
$raw = $raw . '</hot:RoomGuests>
        <hot:ResultCount>0</hot:ResultCount>
        <hot:Filters>
            <hot:StarRating>All</hot:StarRating>
            <hot:OrderBy>PriceAsc</hot:OrderBy>
        </hot:Filters>
        <hot:GeoCodes>
            <hot:Latitude>25.26899</hot:Latitude>
            <hot:Longitude>55.37896</hot:Longitude>
            <hot:SearchRadius>10</hot:SearchRadius>
            <hot:CountryCode>AE</hot:CountryCode>
        </hot:GeoCodes>
        <hot:ResponseTime>10</hot:ResponseTime>
    </hot:HotelSearchRequest>
</soap:Body>
</soap:Envelope>';
// error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");
if ($user != "" and $pass != "") {
    $headers = array(
        "Content-type: application/soap+xml; charset=utf-8",
        "Content-length: " . strlen($raw)
    );
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    // if ($response === false) {
    // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    // } else {
    // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    // }
    curl_close($ch);
    //error_log("\r\nResponse TBO: $response \r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_tbo');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $rtsServiceURL . $raw,
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
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $HotelSearchResponse = $Body->item(0)->getElementsByTagName("HotelSearchResponse");
    
    $SessionId = $HotelSearchResponse->item(0)->getElementsByTagName("SessionId");
    if ($SessionId->length > 0) {
        $SessionId = $SessionId->item(0)->nodeValue;
    } else {
        $SessionId = "";
    }
    $CityId = $HotelSearchResponse->item(0)->getElementsByTagName("CityId");
    if ($CityId->length > 0) {
        $CityId = $CityId->item(0)->nodeValue;
    } else {
        $CityId = "";
    }
    $CheckInDate = $HotelSearchResponse->item(0)->getElementsByTagName("CheckInDate");
    if ($CheckInDate->length > 0) {
        $CheckInDate = $CheckInDate->item(0)->nodeValue;
    } else {
        $CheckInDate = "";
    }
    $CheckOutDate = $HotelSearchResponse->item(0)->getElementsByTagName("CheckOutDate");
    if ($CheckOutDate->length > 0) {
        $CheckOutDate = $CheckOutDate->item(0)->nodeValue;
    } else {
        $CheckOutDate = "";
    }
    
    // RoomGuests
    $RoomGuests = $HotelSearchResponse->item(0)->getElementsByTagName("RoomGuests");
    if ($RoomGuests->length > 0) {
        $node = $RoomGuests->item(0)->getElementsByTagName("RoomGuest");
        for ($i = 0; $i < $node->length; $i ++) {
            $ChildCount = $node->item($i)->getAttribute("ChildCount");
            $AdultCount = $node->item($i)->getAttribute("AdultCount");
            $ChildAge = $node->item($i)->getElementsByTagName("ChildAge");
            if ($ChildAge->length > 0) {
                $int = $ChildAge->item(0)->getElementsByTagName("int");
                if ($int->length > 0) {
                    $int = $int->item(0)->nodeValue;
                } else {
                    $int = "";
                }
            }
        }
    }
    
    // HotelResultList
    $HotelResultList = $HotelSearchResponse->item(0)->getElementsByTagName("HotelResultList");
    if ($HotelResultList->length > 0) {
        $HotelResult = $HotelResultList->item(0)->getElementsByTagName("HotelResult");
        if ($HotelResult->length > 0) {
            for ($z = 0; $z < $HotelResult->length; $z ++) {
                $ResultIndex = $HotelResult->item($z)->getElementsByTagName("ResultIndex");
                if ($ResultIndex->length > 0) {
                    $ResultIndex = $ResultIndex->item(0)->nodeValue;
                } else {
                    $ResultIndex = "";
                }
                $IsPkgProperty = $HotelResult->item($z)->getElementsByTagName("IsPkgProperty");
                if ($IsPkgProperty->length > 0) {
                    $IsPkgProperty = $IsPkgProperty->item(0)->nodeValue;
                } else {
                    $IsPkgProperty = "";
                }
                $IsPackageRate = $HotelResult->item($z)->getElementsByTagName("IsPackageRate");
                if ($IsPackageRate->length > 0) {
                    $IsPackageRate = $IsPackageRate->item(0)->nodeValue;
                } else {
                    $IsPackageRate = "";
                }
                $MappedHotel = $HotelResult->item($z)->getElementsByTagName("MappedHotel");
                if ($MappedHotel->length > 0) {
                    $MappedHotel = $MappedHotel->item(0)->nodeValue;
                } else {
                    $MappedHotel = "";
                }

                $HotelInfo = $HotelResult->item($z)->getElementsByTagName("HotelInfo");
                if ($HotelInfo->length > 0) {
                    $HotelCode = $HotelInfo->item(0)->getElementsByTagName("HotelCode");
                    if ($HotelCode->length > 0) {
                        $HotelCode = $HotelCode->item(0)->nodeValue;
                    } else {
                        $HotelCode = "";
                    }
                    $shid = $HotelCode;
                    $sfilter[] = " sid='$HotelCode' ";
                    $HotelName = $HotelInfo->item(0)->getElementsByTagName("HotelName");
                    if ($HotelName->length > 0) {
                        $HotelName = $HotelName->item(0)->nodeValue;
                    } else {
                        $HotelName = "";
                    }
                    $HotelPicture = $HotelInfo->item(0)->getElementsByTagName("HotelPicture");
                    if ($HotelPicture->length > 0) {
                        $HotelPicture = $HotelPicture->item(0)->nodeValue;
                    } else {
                        $HotelPicture = "";
                    }
                    $HotelDescription = $HotelInfo->item(0)->getElementsByTagName("HotelDescription");
                    if ($HotelDescription->length > 0) {
                        $HotelDescription = $HotelDescription->item(0)->nodeValue;
                    } else {
                        $HotelDescription = "";
                    }
                    $Latitude = $HotelInfo->item(0)->getElementsByTagName("Latitude");
                    if ($Latitude->length > 0) {
                        $Latitude = $Latitude->item(0)->nodeValue;
                    } else {
                        $Latitude = "";
                    }
                    $Longitude = $HotelInfo->item(0)->getElementsByTagName("Longitude");
                    if ($Longitude->length > 0) {
                        $Longitude = $Longitude->item(0)->nodeValue;
                    } else {
                        $Longitude = "";
                    }
                    $HotelAddress = $HotelInfo->item(0)->getElementsByTagName("HotelAddress");
                    if ($HotelAddress->length > 0) {
                        $HotelAddress = $HotelAddress->item(0)->nodeValue;
                    } else {
                        $HotelAddress = "";
                    }
                    $Rating = $HotelInfo->item(0)->getElementsByTagName("Rating");
                    if ($Rating->length > 0) {
                        $Rating = $Rating->item(0)->nodeValue;
                    } else {
                        $Rating = "";
                    }
                    $HotelPromotion = $HotelInfo->item(0)->getElementsByTagName("HotelPromotion");
                    if ($HotelPromotion->length > 0) {
                        $HotelPromotion = $HotelPromotion->item(0)->nodeValue;
                    } else {
                        $HotelPromotion = "";
                    }
                    $TripAdvisorRating = $HotelInfo->item(0)->getElementsByTagName("TripAdvisorRating");
                    if ($TripAdvisorRating->length > 0) {
                        $TripAdvisorRating = $TripAdvisorRating->item(0)->nodeValue;
                    } else {
                        $TripAdvisorRating = "";
                    }
                    $TripAdvisorReviewURL = $HotelInfo->item(0)->getElementsByTagName("TripAdvisorReviewURL");
                    if ($TripAdvisorReviewURL->length > 0) {
                        $TripAdvisorReviewURL = $TripAdvisorReviewURL->item(0)->nodeValue;
                    } else {
                        $TripAdvisorReviewURL = "";
                    }
                }
                
                // MinHotelPrice
                $MinHotelPrice = $HotelResult->item($z)->getElementsByTagName("MinHotelPrice");
                if ($MinHotelPrice->length > 0) {
                    $OriginalPrice = $MinHotelPrice->item(0)->getAttribute("OriginalPrice");
                    $B2CRa = $MinHotelPrice->item(0)->getAttribute("B2CRa");
                    $Currency = $MinHotelPrice->item(0)->getAttribute("Currency");
                    $TotalPrice = $MinHotelPrice->item(0)->getAttribute("TotalPrice");
                    $PrefCurrency = $MinHotelPrice->item(0)->getAttribute("PrefCurrency");
                    $PrefPrice = $MinHotelPrice->item(0)->getAttribute("PrefPrice");
                }

                // RoomDetails
                $RoomDetails = $HotelResult->item($z)->getElementsByTagName("RoomDetails");
                if ($RoomDetails->length > 0) {
                    $Room = $RoomDetails->item(0)->getElementsByTagName("Room");
                    if ($Room->length > 0) {
                        for ($r=0; $r < $Room->length; $r++) { 
                            $Inclusion = $Room->item($r)->getAttribute("Inclusion");
                            $RatePlanCode = $Room->item($r)->getAttribute("RatePlanCode");
                            $RoomType = $Room->item($r)->getAttribute("RoomType");
                        
                
                            for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                                if (is_array($tmp[$shid])) {
                                    $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                } else {
                                    $baseCounterDetails = 0;
                                }
                                
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $HotelCode;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelPicture'] = $HotelPicture;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $RatePlanCode;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $RoomType;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $shid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['SessionId'] = $SessionId;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ResultIndex'] = $ResultIndex;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                // cancellationType nao existe
                                // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $c_type;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-61";
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomType;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $RoomType;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $RoomType;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TotalPrice;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $OriginalPrice;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($RatePlanCode);
                                $pricebreakdown = array();
                                $pricebreakdownCount = 0;
                                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                    $amount = $TotalPrice / $noOfNights;
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
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $Currency;
                                
                                if ($HotelPromotion != "") {
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $HotelPromotion;
                                } else {
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                }
                                
                                // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['FareRateType'] = $FareRateType;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['DailyCostCancel'] = $DailyCostCancel;
                                /*
                                * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $CancelCost;
                                * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $DeadLineCancel;
                                */
                                $count = $count + 1;
                            }
                        }
                    }
                }
            }
        }
        $tbo = true;
    }
}

// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($tbo == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mtbo where " . $sfilter;
        //error_log("\r\n TBO SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 61;
        //error_log("\r\n TBO QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_tbo');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_tbo');
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