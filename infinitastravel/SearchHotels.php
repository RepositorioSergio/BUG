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
$infinitas = false;
$sql = "select name, country_id, zone_id,city_xml59, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml59 = $row_settings["city_xml59"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml59 = "";
}
if ($city_xml59 != "") {
    $sql = "select value from settings where name='enableinfinitas' and affiliate_id=$affiliate_id" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $affiliate_id_getaroom = $affiliate_id;
    } else {
        $affiliate_id_getaroom = 0;
    }
    $sql = "select value from settings where name='infinitastarget' and affiliate_id=$affiliate_id" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitastarget = (int) $row_settings["value"];
    } else {
        $infinitastarget = 0;
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
        $sql = "select value from settings where name='infinitasDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_getaroom";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='infinitasID' and affiliate_id=$affiliate_id_getaroom";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasID = $row_settings['value'];
    }
    $sql = "select value from settings where name='infinitasPassword' and affiliate_id=$affiliate_id_getaroom";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='infinitasMarkup' and affiliate_id=$affiliate_id_getaroom";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasMarkup = (double) $row_settings['value'];
    } else {
        $infinitasMarkup = 0;
    }
    $sql = "select value from settings where name='infinitasServiceURL' and affiliate_id=$affiliate_id_getaroom";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasServiceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='infinitasPartnerID' and affiliate_id=$affiliate_id_getaroom";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasPartnerID = $row_settings['value'];
    }
    $date = new Datetime();
    $timestamp = $date->format('U');
    $url = $infinitasServiceURL . 'hotelavail/list?partner_id=' . $infinitasPartnerID . '&user_name=' . $infinitasID . '&password=' . $infinitasPassword . '&target=' . $infinitastarget . '&version=1.6&start=' . strftime("%Y-%m-%d", $from) . '&end=' . strftime("%Y-%m-%d", $to) . '&city=' . $city_xml59 . '&rooms=';
    $url2 = "";
    $url2 = $url2 . '[';
    for ($z = 0; $z < $rooms; $z ++) {
        $url2 = $url2 . '{"Guests": [';
        $url2 = $url2 . '{"AgeQualifying":0,"Age":0,"Count":' . $selectedAdults[$z] . '}';
        if ($selectedChildren[$z] > 0) {
            $url2 = $url2 . ',';
            for ($ch = 0; $ch < $selectedChildren[$z]; $ch ++) {
                $url2 = $url2 . '{"AgeQualifying":1,"Age":' . $selectedChildrenAges[$z][$ch] . ',"Count":1},';
            }
        }
        $url2 = $url2 . ']},';
    }
    $url2 = $url2 . ']';
    $url2 = urlencode($url2);
    $url = $url . '' . $url2;
    if ($infinitasServiceURL != "" and $infinitasID != "" and $infinitasPassword != "") {
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_infinitastravel');
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
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $HUB_HotelAvailRS = $inputDoc->getElementsByTagName("HUB_HotelAvailRS");
        $RoomStays = $HUB_HotelAvailRS->item(0)->getElementsByTagName("RoomStays");
        if ($RoomStays->length > 0) {
            $node = $RoomStays->item(0)->getElementsByTagName("RoomStay");
            for ($i = 0; $i < $node->length; $i ++) {
                $Hotel = $node->item($i)->getElementsByTagName("Hotel");
                if ($Hotel->length > 0) {
                    $Code = $Hotel->item(0)->getAttribute("Code");
                    $shid = $Code;
                    $sfilter[] = " sid='$Code' ";
                    // $Name = $Hotel->item(0)->getAttribute("Name");
                    // $StarRating = $Hotel->item(0)->getAttribute("StarRating");
                    // $Description = $Hotel->item(0)->getElementsByTagName("Description");
                    // if ($Description->length > 0) {
                    // $Description = $Description->item(0)->nodeValue;
                    // } else {
                    // $Description = "";
                    // }
                    // $MainPhoto = $Hotel->item(0)->getElementsByTagName("MainPhoto");
                    // if ($MainPhoto->length > 0) {
                    // $MainPhoto = $MainPhoto->item(0)->nodeValue;
                    // } else {
                    // $MainPhoto = "";
                    // }
                    // $Address = $Hotel->item(0)->getElementsByTagName("Address");
                    // if ($Address->length > 0) {
                    // $Latitude = $Address->item(0)->getAttribute("Latitude");
                    // $Longitude = $Address->item(0)->getAttribute("Longitude");
                    // $City = $Address->item(0)->getElementsByTagName("City");
                    // if ($City->length > 0) {
                    // $CityCode = $City->item(0)->getAttribute("Code");
                    // $CityName = $City->item(0)->getAttribute("Name");
                    // $CountryCode = $City->item(0)->getAttribute("CountryCode");
                    // } else {
                    // $Code = "";
                    // $Name = "";
                    // $CountryCode = "";
                    // }
                    // $Address2 = $Address->item(0)->getElementsByTagName("Address");
                    // if ($Address2->length > 0) {
                    // $Address2 = $Address2->item(0)->nodeValue;
                    // } else {
                    // $Address2 = "";
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
                    $RoomTypes = $node->item($i)->getElementsByTagName("RoomTypes");
                    if ($RoomTypes->length > 0) {
                        $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                        if ($RoomType->length > 0) {
                            for ($j = 0; $j < $RoomType->length; $j ++) {
                                $RoomTypeCode = $RoomType->item($j)->getAttribute("Code");
                                $RoomTypeName = $RoomType->item($j)->getAttribute("Name");
                                $Rate = $RoomType->item($j)->getElementsByTagName("Rate");
                                if ($Rate->length > 0) {
                                    $Comments = $Rate->item(0)->getElementsByTagName("Comments");
                                    if ($Comments->length > 0) {
                                        $Comments = $Comments->item(0)->nodeValue;
                                    } else {
                                        $Comments = "";
                                    }
                                    $IsImmediateConfirmation = $Rate->item(0)->getAttribute("IsImmediateConfirmation");
                                    $HasAllIncluded = $Rate->item(0)->getAttribute("HasAllIncluded");
                                    $HasBkftIncluded = $Rate->item(0)->getAttribute("HasBkftIncluded");
                                    $HasFapIncluded = $Rate->item(0)->getAttribute("HasFapIncluded");
                                    $HasMapIncluded = $Rate->item(0)->getAttribute("HasMapIncluded");
                                    $CancelCost = $Rate->item(0)->getAttribute("CancelCost");
                                    $DailyCostCancel = $Rate->item(0)->getAttribute("DailyCostCancel");
                                    $DeadLineCancel = (int) $Rate->item(0)->getAttribute("DeadLineCancel");
                                    $ChargingUnit = $Rate->item(0)->getAttribute("ChargingUnit");
                                    $TotalValue = $Rate->item(0)->getAttribute("TotalValue");
                                    $TotalValueNet = $TotalValue;
                                    if ($infinitasMarkup != 0) {
                                        $TotalValue = $TotalValue + (($TotalValue * $infinitasMarkup) / 100);
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
                                    if ($infinitasMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                        $TotalValue = $TotalValue + (($TotalValue * $HotelsMarkupFallback) / 100);
                                    }
                                    // Agent discount
                                    if ($agent_discount != 0) {
                                        $TotalValue = $TotalValue - (($TotalValue * $agent_discount) / 100);
                                    }
                                    $Currency = $Rate->item(0)->getElementsByTagName("Currency");
                                    if ($Currency->length > 0) {
                                        $CurrencyCode = $Currency->item(0)->getAttribute("Code");
                                    } else {
                                        $CurrencyCode = "";
                                    }
                                    $currency = $CurrencyCode;
                                    if ($scurrency != "" and $currency != $scurrency) {
                                        $TotalValue = $CurrencyConverter->convert($TotalValue, $currency, $scurrency);
                                    }
                                    $ID = $Rate->item(0)->getAttribute("ID");
                                    $RoomsRateOccupants = $Rate->item(0)->getElementsByTagName("RoomsRateOccupants");
                                    if ($RoomsRateOccupants->length > 0) {
                                        $RoomRateOccupants = $RoomsRateOccupants->item(0)->getElementsByTagName("RoomRateOccupants");
                                        for ($zRooms = 0; $zRooms < $RoomRateOccupants->length; $zRooms ++) {
                                            $TotalValueRO = $RoomRateOccupants->item($zRooms)->getAttribute("TotalValue");
                                            $TotalValueNetRO = $TotalValueRO;
                                            if ($infinitasMarkup != 0) {
                                                $TotalValueRO = $TotalValueRO + (($TotalValueRO * $infinitasMarkup) / 100);
                                            }
                                            // Geo target markup
                                            if ($internalmarkup != 0) {
                                                $TotalValueRO = $TotalValueRO + (($TotalValueRO * $internalmarkup) / 100);
                                            }
                                            // Agent markup
                                            if ($agent_markup != 0) {
                                                $TotalValueRO = $TotalValueRO + (($TotalValueRO * $agent_markup) / 100);
                                            }
                                            // Fallback Markup
                                            if ($infinitasMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                                $TotalValueRO = $TotalValueRO + (($TotalValueRO * $HotelsMarkupFallback) / 100);
                                            }
                                            // Agent discount
                                            if ($agent_discount != 0) {
                                                $TotalValueRO = $TotalValueRO - (($TotalValueRO * $agent_discount) / 100);
                                            }
                                            if ($scurrency != "" and $currency != $scurrency) {
                                                $TotalValueRO = $CurrencyConverter->convert($TotalValueRO, $currency, $scurrency);
                                            }
                                            $OccupantsID = $RoomRateOccupants->item($zRooms)->getAttribute("OccupantsID");
                                            // $Guest = $RoomRateOccupants->item($zRooms)->getElementsByTagName("Guest");
                                            // if ($Guest->length > 0) {
                                            // $Count = $Guest->item(0)->getAttribute("Count");
                                            // $Age = $Guest->item(0)->getAttribute("Age");
                                            // $AgeQualifying = $Guest->item(0)->getAttribute("AgeQualifying");
                                            // } else {
                                            // $Count = "";
                                            // $Age = "";
                                            // $AgeQualifying = "";
                                            // }
                                            $AccommodationRate = $RoomRateOccupants->item($zRooms)->getElementsByTagName("AccommodationRate");
                                            if ($AccommodationRate->length > 0) {
                                                $Occupation = $AccommodationRate->item(0)->getAttribute("Occupation");
                                                $DailyRate = $AccommodationRate->item(0)->getElementsByTagName("DailyRate");
                                                if ($DailyRate->length > 0) {
                                                    $DRTotalValue = $DailyRate->item(0)->getAttribute("TotalValue");
                                                    $DailyValue = $DailyRate->item(0)->getAttribute("DailyValue");
                                                    $End = $DailyRate->item(0)->getAttribute("End");
                                                    $Start = $DailyRate->item(0)->getAttribute("Start");
                                                } else {
                                                    $DRTotalValue = "";
                                                    $DailyValue = "";
                                                    $End = "";
                                                    $Start = "";
                                                }
                                                $Guarantee = $AccommodationRate->item(0)->getElementsByTagName("Guarantee");
                                                if ($Guarantee->length > 0) {
                                                    $Percentage = $Guarantee->item(0)->getAttribute("Percentage");
                                                    $Deadline = $Guarantee->item(0)->getAttribute("Deadline");
                                                    $Type = $Guarantee->item(0)->getElementsByTagName("Type");
                                                    if ($Type->length > 0) {
                                                        $Type = $Type->item(0)->nodeValue;
                                                    } else {
                                                        $Type = "";
                                                    }
                                                } else {
                                                    $Percentage = "";
                                                    $Deadline = "";
                                                    $Type = "";
                                                }
                                            } else {
                                                $Occupation = "";
                                            }
                                            if (is_array($tmp[$shid])) {
                                                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                            } else {
                                                $baseCounterDetails = 0;
                                            }
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateid'] = $ID;
                                            // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomcode'] = $RoomTypeCode;
                                            if ($IsImmediateConfirmation == "true") {
                                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                            } else {
                                                $RoomTypeName .= " - " . $translator->translate("On Request Rate");
                                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 4;
                                            }
                                            // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $Type;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-59";
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomTypeName;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$OccupantsID];
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$OccupantsID];
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TotalValueRO;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $TotalValueNetRO;
                                            // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TotalValue;
                                            // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $TotalValueNet;
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
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $BoardType;
                                            $pricebreakdown = array();
                                            $pricebreakdownCount = 0;
                                            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                                // $amount = $TotalValue / $noOfNights;
                                                $amount = $TotalValueRO / $noOfNights;
                                                $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                                                $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                                $pricebreakdownCount = $pricebreakdownCount + 1;
                                            }
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
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
                                            if ($rooms > 1) {
                                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nodedup'] = true;
                                            } else {
                                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nodedup'] = false;
                                            }
                                            $baseCounterDetails ++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $infinitas = true;
        }
        if ($infinitas == true) {
            $sfilter = implode(' or ', $sfilter);
            try {
                $sql = "select hid, sid from xmlhotels_minfinitas where " . $sfilter;
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
                $supplier = 59;
                // Store Session
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_infinitastravel');
                $delete->where(array(
                    'session_id' => $session_id
                ));
                $statement = $sql->prepareStatementForSqlObject($delete);
                try {
                    $results = $statement->execute();
                } catch (\Exception $e) {
                    $logger = new Logger();
                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                }
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_infinitastravel');
                $insert->values(array(
                    'session_id' => $session_id,
                    'xmlrequest' => (string) $raw,
                    'xmlresult' => (string) $response,
                    'data' => base64_encode(serialize($hotels_array)),
                    'searchsettings' => base64_encode(serialize($requestdata))
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                try {
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
}
?>