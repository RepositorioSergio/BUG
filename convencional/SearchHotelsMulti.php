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
$convencional = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
$sql = "select name, Code, country_id, zone_id,city_xml38, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml38 = $row_settings["city_xml38"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml38 = "";
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
    $sql = "select value from settings where name='convencionalDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_convencional";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$affiliate_id = 0;
$sql = "select value from settings where name='enableconvencional' and affiliate_id=$affiliate_id";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_convencional = $affiliate_id;
} else {
    $affiliate_id_convencional = 0;
}
$sql = "select value from settings where name='convencionalLogin' and affiliate_id=$affiliate_id_convencional";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $convencionalLogin = $row_settings['value'];
}
$sql = "select value from settings where name='convencionalPassword' and affiliate_id=$affiliate_id_convencional";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $convencionalPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='convencionalMarkup' and affiliate_id=$affiliate_id_convencional";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $convencionalMarkup = (double) $row_settings['value'];
} else {
    $convencionalMarkup = 0;
}
$sql = "select value from settings where name='convencionalTimeout' and affiliate_id=$affiliate_id_convencional";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $convencionalTimeout = (int) $row_settings['value'];
} else {
    $convencionalTimeout = 0;
}
if ($convencionalTimeout == 0) {
    $convencionalTimeout = 120;
}
// URL
$sql = "select value from settings where name='convencionalName' and affiliate_id=$affiliate_id_convencional";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $convencionalName = $row_settings['value'];
}
$sql = "select value from settings where name='convencionalID' and affiliate_id=$affiliate_id_convencional";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $convencionalID = $row_settings['value'];
}
$sql = "select value from settings where name='convencionalServiceURL' and affiliate_id=$affiliate_id_convencional";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $convencionalServiceURL = $row_settings['value'];
}
$date = new Datetime();
$timestamp = $date->format('U');
$data = date("Y-m-d\TH:i:s.v");
if ($city_xml38 != "") {
    if ($convencionalServiceURL != "" and $convencionalLogin != "" and $convencionalPassword != "") {
        $nC = 0;
        $multiParallel = array();
        $multiParallel = curl_multi_init();
        for ($r = 0; $r < $rooms; $r ++) {
            $raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xnet="http://xnetinfo.org/"><soap:Header/><soap:Body><xnet:getHotelAvail><xnet:aRequest EchoToken="' . sha1(mt_rand(1, 90000) . 'SALT') . '" TimeStamp="' . $data . '" Version="1.0"><xnet:POS><xnet:Source><xnet:RequestorID ID="' . $convencionalName . '" PartnerID="' . $convencionalID . '" Username="' . $convencionalLogin . '" Password="' . $convencionalPassword . '"/></xnet:Source> </xnet:POS><xnet:AvailRequest><xnet:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '" /><xnet:HotelSearchCriterion HotelCityCode="' . $city_xml38 . '"/></xnet:AvailRequest><xnet:RoomStayCandidates><xnet:RoomStayCandidate><xnet:Guest AgeType="ADT" Age="0" Count="' . $selectedAdults[$r] . '" />';
            for ($w = 0; $w < $selectedChildren[$r]; $w ++) {
                $raw .= '<xnet:Guest AgeType="CHD" Age="' . $selectedChildrenAges[$r][$w] . '" Count="1" />';
            }
            $raw .= '</xnet:RoomStayCandidate></xnet:RoomStayCandidates></xnet:aRequest></xnet:getHotelAvail></soap:Body></soap:Envelope>';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $convencionalServiceURL);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $convencionalTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $convencionalTimeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Accept: text/xml",
                "Content-type: text/xml;charset=\"utf-8\"",
                "Content-length: " . strlen($raw)
            ));
            curl_multi_add_handle($multiParallel, $ch);
            $requestsParallel[$nC] = $r;
            $channelsParallel[$nC] = $ch;
            $nC ++;
        }
        $active = null;
        do {
            $mrc = curl_multi_exec($multiParallel, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multiParallel) == - 1) {
                continue;
            }
            do {
                $mrc = curl_multi_exec($multiParallel, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            if ($mrc != CURLM_OK) {
                error_log("\r\nCurl Multi Exec Error:" . curl_multi_strerror($mrc) . "\r\n", 3, "/srv/www/htdocs/error_log");
            }
        }
        foreach ($channelsParallel as $zRooms => $channel) {
            $response = curl_multi_getcontent($channel);
            $raw = $requestsParallel[$zRooms];
            curl_multi_remove_handle($multiParallel, $channel);
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('log_convencional');
                $insert->values(array(
                    'datetime_created' => time(),
                    'filename' => 'SearchHotels.php',
                    'errorline' => $zRooms,
                    'errormessage' => $convencionalServiceURL . $raw,
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
            $getHotelAvailResponse = $Body->item(0)->getElementsByTagName("getHotelAvailResponse");
            $getHotelAvailResult = $getHotelAvailResponse->item(0)->getElementsByTagName("getHotelAvailResult");
            $ID = $getHotelAvailResult->item(0)->getAttribute("ID");
            $RoomStays = $getHotelAvailResult->item(0)->getElementsByTagName("RoomStays");
            $node = $RoomStays->item(0)->getElementsByTagName("RoomStay");
            for ($i = 0; $i < $node->length; $i ++) {
                // Hotel
                $Hotel = $node->item($i)->getElementsByTagName("Hotel");
                if ($Hotel->length > 0) {
                    $Code = $Hotel->item(0)->getAttribute("Code");
                    $shid = $Code;
                    $sfilter[] = " sid='$Code' ";
                    // $Name = $Hotel->item(0)->getAttribute("Name");
                    // $StarRating = $Hotel->item(0)->getAttribute("StarRating");
                    // $SubCategory = $Hotel->item(0)->getAttribute("SubCategory");
                    // $Email = $Hotel->item(0)->getAttribute("Email");
                    // $Url = $Hotel->item(0)->getAttribute("Url");
                    // $UrlVirtualTour = $Hotel->item(0)->getAttribute("UrlVirtualTour");
                    // $MinAccommodationRate = $Hotel->item(0)->getAttribute("MinAccommodationRate");
                    // $MaxAccommodationRate = $Hotel->item(0)->getAttribute("MaxAccommodationRate");
                    // $Description = $Hotel->item(0)->getElementsByTagName("Description");
                    // if ($Description->length > 0) {
                    // $Description = $Description->item(0)->nodeValue;
                    // } else {
                    // $Description = "";
                    // }
                    $Comments = $Hotel->item(0)->getElementsByTagName("Comments");
                    if ($Comments->length > 0) {
                        $Comments = $Comments->item(0)->nodeValue;
                    } else {
                        $Comments = "";
                    }
                    // $Address2 = "";
                    // $Address = $Hotel->item(0)->getElementsByTagName("Address");
                    // if ($Address->length > 0) {
                    // $Latitude = $Address->item(0)->getAttribute("Latitude");
                    // $Longitude = $Address->item(0)->getAttribute("Longitude");
                    // $Address2 = $Address->item(0)->nodeValue;
                    // $City = $Address->item(0)->getElementsByTagName("City");
                    // if ($City->length > 0) {
                    // $CityCode = $City->item(0)->getAttribute("Code");
                    // $CityName = $City->item(0)->getAttribute("Name");
                    // }
                    // }
                    // $PhoneNumbers = $Hotel->item(0)->getElementsByTagName("PhoneNumbers");
                    // if ($PhoneNumbers->length > 0) {
                    // $PhoneNumber = $PhoneNumbers->item(0)->getElementsByTagName("PhoneNumber");
                    // if ($PhoneNumber->length > 0) {
                    // $LineNumber = $PhoneNumber->item(0)->getAttribute("LineNumber");
                    // $Prefix = $PhoneNumber->item(0)->getAttribute("Prefix");
                    // $CountryAccessCode = $PhoneNumber->item(0)->getAttribute("CountryAccessCode");
                    // $AreaCityCode = $PhoneNumber->item(0)->getAttribute("AreaCityCode");
                    // } else {
                    // $LineNumber = "";
                    // $Prefix = "";
                    // $CountryAccessCode = "";
                    // $AreaCityCode = "";
                    // }
                    // }
                    // $MainPhoto = $Hotel->item(0)->getElementsByTagName("MainPhoto");
                    // if ($MainPhoto->length > 0) {
                    // $MainPhoto = $MainPhoto->item(0)->nodeValue;
                    // } else {
                    // $MainPhoto = "";
                    // }
                    // $MinAccommodationRateCurrency = $Hotel->item(0)->getElementsByTagName("MinAccommodationRateCurrency");
                    // if ($MinAccommodationRateCurrency->length > 0) {
                    // $MinAccCode = $MinAccommodationRateCurrency->item(0)->getAttribute("Code");
                    // } else {
                    // $MinAccCode = "";
                    // }
                    // $MaxAccommodationRateCurrency = $Hotel->item(0)->getElementsByTagName("MaxAccommodationRateCurrency");
                    // if ($MaxAccommodationRateCurrency->length > 0) {
                    // $MaxAccCode = $MaxAccommodationRateCurrency->item(0)->getAttribute("Code");
                    // } else {
                    // $MaxAccCode = "";
                    // }
                }
                // RoomRates
                $RoomRates = $node->item($i)->getElementsByTagName("RoomRates");
                if ($RoomRates->length > 0) {
                    $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                    if ($RoomRate->length > 0) {
                        for ($k = 0; $k < $RoomRate->length; $k ++) {
                            $IDRoomRate = $RoomRate->item($k)->getAttribute("ID");
                            $FitGroup = $RoomRate->item($k)->getAttribute("FitGroup");
                            $HasAllIncluded = $RoomRate->item($k)->getAttribute("HasAllIncluded");
                            $HasBkftIncluded = $RoomRate->item($k)->getAttribute("HasBkftIncluded");
                            $HasFapIncluded = $RoomRate->item($k)->getAttribute("HasFapIncluded");
                            $HasMapIncluded = $RoomRate->item($k)->getAttribute("HasMapIncluded");
                            $CancelCost = $RoomRate->item($k)->getAttribute("CancelCost");
                            $DailyCostCancel = $RoomRate->item($k)->getAttribute("DailyCostCancel");
                            $DeadLineCancel = (int) $RoomRate->item($k)->getAttribute("DeadLineCancel");
                            $ChargingUnit = $RoomRate->item($k)->getAttribute("ChargingUnit");
                            $TotalValue = $RoomRate->item($k)->getAttribute("TotalValue");
                            $TotalValueNet = $TotalValue;
                            $Currency = $RoomRate->item($k)->getElementsByTagName("Currency");
                            if ($Currency->length > 0) {
                                $CurrencyCode = $Currency->item(0)->getAttribute("Code");
                            } else {
                                $CurrencyCode = "";
                            }
                            $currency = $CurrencyCode;
                            if ($convencionalMarkup != 0) {
                                $TotalValue = $TotalValue + (($TotalValue * $convencionalMarkup) / 100);
                            }
                            // Geo target markup
                            if ($internalmarkup != 0) {
                                $TotalValue = $TotalValue + (($TotalValue * $internalmarkup) / 100);
                            }
                            // Agent markup
                            if ($agent_markup != 0) {
                                $TotalValue = $TotalValue + (($TotalValue * $agent_markup) / 100);
                            }
                            // Fallback Markup
                            if ($convencionalMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                $TotalValue = $TotalValue + (($TotalValue * $HotelsMarkupFallback) / 100);
                            }
                            // Agent discount
                            if ($agent_discount != 0) {
                                $TotalValue = $TotalValue - (($TotalValue * $agent_discount) / 100);
                            }
                            if ($scurrency != "" and $currency != $scurrency) {
                                $TotalValue = $CurrencyConverter->convert($TotalValue, $currency, $scurrency);
                            }
                            $Market = $RoomRate->item($k)->getElementsByTagName("Market");
                            if ($Market->length > 0) {
                                $MarketCode = $Market->item(0)->getAttribute("Code");
                            } else {
                                $MarketCode = "";
                            }
                            $Comments = $RoomRate->item($k)->getElementsByTagName("Comments");
                            if ($Comments->length > 0) {
                                $Comments = $Comments->item(0)->nodeValue;
                            } else {
                                $Comments = "";
                            }
                            // RoomType
                            $RoomType = $RoomRate->item($k)->getElementsByTagName("RoomType");
                            if ($RoomType->length > 0) {
                                $RoomTypeCode = $RoomType->item(0)->getAttribute("Code");
                                $RoomTypeName = $RoomType->item(0)->getAttribute("Name");
                                $RoomsOccupants = $RoomType->item(0)->getElementsByTagName("RoomsOccupants");
                                $RoomOccupants = $RoomsOccupants->item(0)->getElementsByTagName("RoomOccupants");
                                $RoomRateOccupants = $RoomOccupants->item(0)->getElementsByTagName("RoomRateOccupants");
                                if ($RoomRateOccupants->length > 0) {
                                    $OccupantsID = $RoomRateOccupants->item(0)->getAttribute("OccupantsID");
                                    $IsImmediateConfirmation = $RoomRateOccupants->item(0)->getAttribute("IsImmediateConfirmation");
                                    $TotalValueRate = $RoomRateOccupants->item(0)->getAttribute("TotalValue");
                                    $Guest = $RoomRateOccupants->item(0)->getElementsByTagName("Guest");
                                    if ($Guest->length > 0) {
                                        $Count = $Guest->item(0)->getAttribute("Count");
                                        $Age = $Guest->item(0)->getAttribute("Age");
                                        $AgeType = $Guest->item(0)->getAttribute("AgeType");
                                    } else {
                                        $Count = "";
                                        $Age = "";
                                        $AgeType = "";
                                    }
                                    $AccommodationRate = $RoomRateOccupants->item(0)->getElementsByTagName("AccommodationRate");
                                    if ($AccommodationRate->length > 0) {
                                        $Occupation = $AccommodationRate->item(0)->getAttribute("Occupation");
                                        $DailyRate = $AccommodationRate->item(0)->getElementsByTagName("DailyRate");
                                        if ($DailyRate->length > 0) {
                                            for ($d = 0; $d < $DailyRate->length; $d ++) {
                                                $TotalValueAcc = $DailyRate->item($d)->getAttribute("TotalValue");
                                                $DailyValue = $DailyRate->item($d)->getAttribute("DailyValue");
                                                $End = $DailyRate->item($d)->getAttribute("End");
                                                $Start = $DailyRate->item($d)->getAttribute("Start");
                                            }
                                        }
                                        $Guarantee = $AccommodationRate->item(0)->getElementsByTagName("Guarantee");
                                        if ($Guarantee->length > 0) {
                                            $Type = $Guarantee->item(0)->getAttribute("Type");
                                            $Percentage = $Guarantee->item(0)->getAttribute("Percentage");
                                            $Deadline = $Guarantee->item(0)->getAttribute("Deadline");
                                        } else {
                                            $Type = "";
                                            $Percentage = "";
                                            $Deadline = "";
                                        }
                                    } else {
                                        $Occupation = "";
                                    }
                                    $OptionalServices = $RoomRateOccupants->item(0)->getElementsByTagName("OptionalServices");
                                    if ($OptionalServices->length > 0) {
                                        $OptionalService = $OptionalServices->item(0)->getElementsByTagName("OptionalService");
                                        if ($OptionalService->length > 0) {
                                            $OptionalServiceCode = $OptionalService->item(0)->getAttribute("Code");
                                            $OptionalServiceName = $OptionalService->item(0)->getAttribute("Name");
                                            $OptionalServiceOccupation = $OptionalService->item(0)->getAttribute("Occupation");
                                            $OptionalServiceRateID = $OptionalService->item(0)->getAttribute("RateID");
                                            $OptionalServiceValue = $OptionalService->item(0)->getAttribute("Value");
                                            $OptionalServiceChargeUnit = $OptionalService->item(0)->getAttribute("ChargeUnit");
                                            $OptionalServiceCategoryCode = $OptionalService->item(0)->getAttribute("CategoryCode");
                                            $DailyRate = $OptionalService->item(0)->getElementsByTagName("DailyRate");
                                            if ($DailyRate->length > 0) {
                                                for ($op = 0; $op < $DailyRate->length; $op ++) {
                                                    $DailyRateTotalValue = $DailyRate->item($op)->getAttribute("TotalValue");
                                                    $DailyRateDailyValue = $DailyRate->item($op)->getAttribute("DailyValue");
                                                    $DailyRateEnd = $DailyRate->item($op)->getAttribute("End");
                                                    $DailyRateStart = $DailyRate->item($op)->getAttribute("Start");
                                                }
                                            }
                                        } else {
                                            $OptionalServiceCode = "";
                                            $OptionalServiceName = "";
                                            $OptionalServiceOccupation = "";
                                            $OptionalServiceRateID = "";
                                            $OptionalServiceValue = "";
                                            $OptionalServiceChargeUnit = "";
                                            $OptionalServiceCategoryCode = "";
                                        }
                                    }
                                } else {
                                    $OccupantsID = "";
                                    $IsImmediateConfirmation = "";
                                    $TotalValueRate = "";
                                }
                            } else {
                                $RoomTypeCode = "";
                                $RoomTypeName = "";
                            }
                            if (is_array($tmp[$shid])) {
                                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                            } else {
                                $baseCounterDetails = 0;
                            }
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomrateid'] = $IDRoomRate;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $RoomTypeCode;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-38";
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = ucwords(strtolower($RoomTypeName));
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $Comments;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $RoomTypeCode;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TotalValue;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $TotalValueNet;
                            $BoardType = $translator->translate("Room Only");
                            if ($HasBkftIncluded == "true") {
                                $BoardType = $translator->translate("Bed & Breakfast");
                            }
                            if ($HasMapIncluded == "true") {
                                $BoardType = $translator->translate("Half Board");
                            }
                            if ($HasFapIncluded == "true") {
                                $BoardType = $translator->translate("Full Board");
                            }
                            if ($HasAllIncluded == "true") {
                                $BoardType = $translator->translate("All Inclusive");
                            }
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($BoardType);
                            $pricebreakdown = array();
                            $pricebreakdownCount = 0;
                            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                $amount = $TotalValue / $noOfNights;
                                $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                                $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                $pricebreakdownCount = $pricebreakdownCount + 1;
                            }
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['DailyRateStart'] = $DailyRateStart;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['DailyCostCancel'] = $DailyCostCancel;
                            $CancelCost = $CurrencyConverter->convert($CancelCost, $currency, $scurrency);
                            $ddate = mktime(0, 0, 0, date("m", $from), date("d", $from) - ($DeadLineCancel + 1), date("y", $from));
                            if ($ddate < time()) {
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = time();
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = true;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking. You will be charged full stay of the booking.");
                            } else {
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $ddate;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $translator->translate("Charge") . " " . $scurrency . " " . $CancelCost . " " . $translator->translate("if cancelled on or after") . " " . strftime("%a, %d %B %Y", $ddate);
                                ;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                            }
                            
                            $count = $count + 1;
                        }
                    }
                }
            }
            $convencional = true;
        }
    }
    if ($convencional == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $sql = "select hid, sid from xmlhotels_mconvencional where " . $sfilter;
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
            $query = 'call xmlhotels("' . $sidfilter . '")';
            $supplier = 38;
            try {
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_convencional');
                $delete->where(array(
                    'session_id' => $session_id
                ));
                $statement = $sql->prepareStatementForSqlObject($delete);
                $results = $statement->execute();
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_convencional');
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