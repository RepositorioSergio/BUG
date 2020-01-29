<?php
error_log("\r\n TBO - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mtbo where hid=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $result = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $hotellist .= '' . $row->sid . '';
    }
}
if ($hotellist != "") {
    $affiliate_id_tbo = 0;
    $sql = "select value from settings where name='tboLoginEmail' and affiliate_id=$affiliate_id_tbo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $tboLoginEmail = $row_settings['value'];
    }
    $sql = "select value from settings where name='tboPassword' and affiliate_id=$affiliate_id_tbo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $tboPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='tboServiceURL' and affiliate_id=$affiliate_id_tbo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $tboServiceURL = $row['value'];
    }
    $sql = "select value from settings where name='tboMarkup' and affiliate_id=$affiliate_id_tbo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $tboMarkup = (double) $row_settings['value'];
    } else {
        $tboMarkup = 0;
    }
    $sql = "select value from settings where name='tboTimeout' and affiliate_id=$affiliate_id_tbo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $tboTimeout = (int) $row_settings['value'];
    }
    if ($tboTimeout == 0) {
        $tboTimeout = 120;
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
        $sql = "select value from settings where name='tboDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_tbo";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }

    $raw2 = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
    <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing" >
        <hot:Credentials UserName="' . $tboLoginEmail . '" Password="' . $tboPassword . '">
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
            <hot:NoOfRooms>1</hot:NoOfRooms>
            <hot:GuestNationality>' . $sourceMarket . '</hot:GuestNationality>
            <hot:IsRoomInfoRequired>true</hot:IsRoomInfoRequired>
            <hot:RoomGuests>';
            $raw2 = $raw2 . '<hot:RoomGuest AdultCount="' . $adults . '" ChildCount="' . $children . '">';
            if ($children > 0) {
                $raw2 = $raw2 . '<hot:ChildAge>';
                for ($y=0; $y < $children; $y++) { 
                    $raw2 = $raw2 . '<hot:int>' . $children_ages[$y] . '</hot:int>';
                }
                $raw2 = $raw2 . '</hot:ChildAge></hot:RoomGuest>';
            } else {
                $raw2 = $raw2 . '</hot:RoomGuest>';
            }
    $raw2 = $raw2 . '</hot:RoomGuests>
            <hot:ResultCount>0</hot:ResultCount>
            <hot:Filters>
                <hot:HotelCodeList>' . $hotellist . '</hot:HotelCodeList>
                <hot:StarRating>All</hot:StarRating>
                <hot:OrderBy>PriceAsc</hot:OrderBy>
            </hot:Filters>
            <hot:ResponseTime>' . $tboTimeout . '</hot:ResponseTime>
        </hot:HotelSearchRequest>
    </soap:Body>
    </soap:Envelope>';

    $headers = array(
        "Content-type: application/soap+xml; charset=utf-8",
        "Content-length: " . strlen($raw2)
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $tboServiceURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response2 = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);

    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response2);
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
                    $RoomInfo = $RoomDetails->item(0)->getElementsByTagName("Room");
                    if ($RoomInfo->length > 0) {
                        for ($r=0; $r < $RoomInfo->length; $r++) { 
                            $Inclusion = $RoomInfo->item($r)->getAttribute("Inclusion");
                            $RatePlanCode = $RoomInfo->item($r)->getAttribute("RatePlanCode");
                            $RoomType = $RoomInfo->item($r)->getAttribute("RoomType");
                        }
                    }
                }
            }
        }
    }


    $raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
        <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
            <hot:Credentials UserName="' . $tboLoginEmail . '" Password="' . $tboPassword . '">
            </hot:Credentials>
            <wsa:Action>http://TekTravel/HotelBookingApi/AvailableHotelRooms</wsa:Action>
            <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
        </soap:Header>
        <soap:Body>
            <hot:HotelRoomAvailabilityRequest>
                <hot:SessionId>' . $SessionId . '</hot:SessionId>
                <hot:ResultIndex>' . $ResultIndex . '</hot:ResultIndex>
                <hot:HotelCode>' . $HotelCode . '</hot:HotelCode>
                <hot:ResponseTime>' . $tboTimeout . '</hot:ResponseTime>
                <hot:IsCancellationPolicyRequired>true</hot:IsCancellationPolicyRequired>
            </hot:HotelRoomAvailabilityRequest>
        </soap:Body>
        </soap:Envelope>';

    error_log("\r\nRTS RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: application/soap+xml; charset=utf-8",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $tboServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $tboTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $tboTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'tbo';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>