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
$travco = false;
error_log("\r\n COMECOU TRAVCO TERCA FEIRA TARDE 14H30 \r\n", 3, "/srv/www/htdocs/error_log");
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id,city_xml23, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml23 = $row_settings["city_xml23"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml23 = "";
}
error_log("\r\n city_xml23 $city_xml23 \r\n", 3, "/srv/www/htdocs/error_log");

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
    $sql = "select value from settings where name='TravcoDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travco";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}

$sql = "select value from settings where name='TravcoAgentCode' and affiliate_id=$affiliate_id_travco";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravcoAgentCode = $row_settings['value'];
}
error_log("\r\n TravcoAgentCode $TravcoAgentCode \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='TravcoAgentPassword' and affiliate_id=$affiliate_id_travco";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravcoAgentPassword = base64_decode($row_settings['value']);
}

$sql = "select value from settings where name='TravcoMarkup' and affiliate_id=$affiliate_id_travco";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravcoMarkup = (double) $row_settings['value'];
} else {
    $TravcoMarkup = 0;
}
error_log("\r\n TravcoMarkup $TravcoMarkup \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='TravcoServiceURL' and affiliate_id=$affiliate_id_travco";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravcoServiceURL = $row_settings['value'];
}
error_log("\r\n TravcoServiceURL $TravcoServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='Travcob2cMarkup' and affiliate_id=$affiliate_id_travco";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Travcob2cMarkup = $row_settings['value'];
}

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');
error_log("\r\n ANTES IF\r\n", 3, "/srv/www/htdocs/error_log");
// $city_xml23 = "London";
if ($city_xml23 != "") {
    error_log("\r\n IF\r\n", 3, "/srv/www/htdocs/error_log");
    $single_rooms = 0;
    $double_rooms = 0;
    $triple_rooms = 0;
    $double_extra_beds = 0;
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        if ($selectedAdults[$r] >= 4) {
            // Travco does not support more than 3 rooms
            $breakSearch = 1;
        }
        switch ($selectedAdults[$r]) {
            case 1:
                $single_rooms = 1;
                break;
            case 2:
                $double_rooms = 1;
                if ($selectedChildren[$r] != 0) {
                    $double_extra_beds = 1;
                }
                break;
            case 3:
                $triple_rooms = 1;
                break;
                break;
            default:
                $double_rooms = 1;
                if ($selectedChildren[$r] != 0) {
                    $double_extra_beds = 1;
                }
                break;
        }
    }
    if ($breakSearch == 0) {
        $raw = 'XMLString=<?xml version="1.0" encoding="UTF-8"?><BOOKING type="HA" lang="en-GB"  returnURLNeed="no" returnURL="http://" AGENTCODE="' . $TravcoAgentCode . '" AGENTPASSWORD="' . $TravcoAgentPassword . '" AVAILABLE_HOTELS_ONLY="YES" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://xmlv5test.travco.co.uk/trlink/schema/HotelAvailabilityV6Snd.xsd"><DATA COUNTRY_CODE="' . $country_id . '" CITY_CODE="' . $city_xml23 . '"><ROOMS_DATA SINGLE_ROOMS="' . $single_rooms . '" DOUBLE_ROOMS="' . $double_rooms . '" TRIPLE_ROOMS="' . $triple_rooms . '" DOUBLE_EXTRA_BEDS="' . $double_extra_beds . '" /><DATE_DATA CHECK_IN_DATE="' . strftime("%d/%b/%Y", $from) . '" CHECK_OUT_DATE="' . strftime("%d/%b/%Y", $to) . '"/><OPTIONAL_DATA NeedReductionAmount="YES" NeedHotelMessages="YES" NeedFreeNightDetails="YES" SortingOrder="Low"/><ADDITIONAL_DATA PICTURE_NEED="NO" AMENITY_NEED="NO" HOTEL_ADDRESS_NEED="NO" TELEPHONE_NO_NEED="NO" FAX_NO_NEED="NO" EMAIL_NEED="NO" HotelDescription="NO" HotelCity="NO" HotelProperties="NO" HotelArrivalPointOther="NO" HotelArrivalPoint="NO" GeoCodes="NO" Location="NO" CityArea="NO" EnglishTextNeed="NO"/></DATA></BOOKING>';
        //error_log("\r\n Request: $raw  \r\n", 3, "/srv/www/htdocs/error_log");
        if ($TravcoServiceURL != "" and $TravcoAgentCode != "" and $TravcoAgentPassword != "") {
            $startTime = microtime();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $TravcoServiceURL);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept-Encoding: gzip',
                'Host:xmlv6.travco.co.uk'
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
            //error_log("\r\n END POINT: $TravcoServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
            //error_log("\r\n RESPONSE: $response \r\n", 3, "/srv/www/htdocs/error_log");
            curl_close($ch);

            $endTime = microtime();
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('log_travco');
                $insert->values(array(
                    'datetime_created' => time(),
                    'filename' => 'SearchHotels.php',
                    'errorline' => $this->microtime_diff($startTime, $endTime),
                    'errormessage' => $TravcoServiceURL . $raw,
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
             error_log("\r\n ANTES PARSE TERCA FEIRA \r\n", 3, "/srv/www/htdocs/error_log");
            // echo $response;
            // die();
            $inputDoc = new DOMDocument();
            $inputDoc->loadXML($response);
            if ($inputDoc != NULL) {
            $responseElement = $inputDoc->documentElement;
            $xpath = new DOMXPath($inputDoc);
            $errorsElements = $xpath->query('/RETURNDATA/MESSAGE', $responseElement);
            if ($errorsElements->length > 0) {
                $ErrorMsg = $errorsElements->item(0)->nodeValue;
                $pos = strpos($ErrorMsg, "Error");
                if ($pos === false) {

            $searchHotelPriceReponseElements = "";
            $searchHotelPriceReponseElements = $xpath->query('/RETURNDATA/DATA', $responseElement);
            foreach ($searchHotelPriceReponseElements as $searchHotelPriceReponseElement) {
                $COUNTRY_NAME = $searchHotelPriceReponseElement->getElementsByTagName("COUNTRY_NAME");
                $CITY_NAME = $searchHotelPriceReponseElement->getElementsByTagName("CITY_NAME");
                $NO_OF_CHILDREN = $searchHotelPriceReponseElement->getAttribute("NO_OF_CHILDREN");
                $NO_OF_ADULTS = $searchHotelPriceReponseElement->getAttribute("NO_OF_ADULTS");
                $COUNTRY_CODE = $searchHotelPriceReponseElement->getAttribute("COUNTRY_CODE");
                $CITY_CODE = $searchHotelPriceReponseElement->getAttribute("CITY_CODE");
                $CHECK_OUT_DATE = $searchHotelPriceReponseElement->getAttribute("CHECK_OUT_DATE");
                $CHECK_IN_DATE = $searchHotelPriceReponseElement->getAttribute("CHECK_IN_DATE");

                $node = $searchHotelPriceReponseElement->getElementsByTagName("HOTEL_DATA");
                for ($hd=0; $hd < $node->length; $hd++) { 
                    $HOTEL_NAME = $node->item($hd)->getElementsByTagName("HOTEL_NAME");
                    if ($HOTEL_NAME->length > 0) {
                        $HOTEL_NAME = $HOTEL_NAME->item(0)->nodeValue;
                    } else {
                        $HOTEL_NAME = "";
                    }
                    $HOTEL_STAR = $node->item($hd)->getElementsByTagName("HOTEL_STAR");
                    if ($HOTEL_STAR->length > 0) {
                        $HOTEL_STAR = $HOTEL_STAR->item(0)->nodeValue;
                    } else {
                        $HOTEL_STAR = "";
                    }
                    $CURRENCY_NAME = $node->item($hd)->getElementsByTagName("CURRENCY_NAME");
                    if ($CURRENCY_NAME->length > 0) {
                        $CURRENCY_NAME = $CURRENCY_NAME->item(0)->nodeValue;
                    } else {
                        $CURRENCY_NAME = "";
                    }
                    $STATUS = $node->item($hd)->getAttribute("STATUS");
                    $HOTEL_CODE = $node->item($hd)->getAttribute("HOTEL_CODE");
                    $shid = $HOTEL_CODE;
                    $sfilter[] = " sid='$HOTEL_CODE' ";
                    $CURRENCY_CODE = $node->item($hd)->getAttribute("CURRENCY_CODE");

                    $node2 = $node->item($hd)->getElementsByTagName("ROOM_DATA");
                    for ($i=0; $i < $node2->length; $i++) { 
                    $ROOM_NAME = $node2->item($i)->getElementsByTagName("ROOM_NAME");
                    if ($ROOM_NAME->length > 0) {
                        $ROOM_NAME = $ROOM_NAME->item(0)->nodeValue;
                    } else {
                        $ROOM_NAME = "";
                    }
                    $ADULT_PRICE_DETAILS = $node2->item($i)->getElementsByTagName("ADULT_PRICE_DETAILS");
                    if ($ADULT_PRICE_DETAILS->length > 0) {
                        $ADULT_PRICE_DETAILS = $ADULT_PRICE_DETAILS->item(0)->nodeValue;
                    } else {
                        $ADULT_PRICE_DETAILS = "";
                    }
                    $TOTAL_ADULT_PRICE = $node2->item($i)->getAttribute("TOTAL_ADULT_PRICE");
                    $ROOM_PAX = $node2->item($i)->getAttribute("ROOM_PAX");
                    $ROOM_CODE = $node2->item($i)->getAttribute("ROOM_CODE");
                    $PRICE_CODE = $node2->item($i)->getAttribute("PRICE_CODE");
                    $NO_OF_EXTRA_BEDS = $node2->item($i)->getAttribute("NO_OF_EXTRA_BEDS");
                    $EXTRA_BED_INDICATOR = $node2->item($i)->getAttribute("EXTRA_BED_INDICATOR");
                    $CHILD_PRICE = $node2->item($i)->getAttribute("CHILD_PRICE");
                    $CCHARGES_CODE = $node2->item($i)->getAttribute("CCHARGES_CODE");
                    $ADULT_PRICE = $node2->item($i)->getAttribute("ADULT_PRICE");

                    $ReducedPriceDetails = $node2->item($i)->getElementsByTagName("ReducedPriceDetails");
                    if ($ReducedPriceDetails->length > 0) {
                        $AdultReductionPercentage = $ReducedPriceDetails->item(0)->getAttribute("AdultReductionPercentage");
                        $AdultReductionAmount = $ReducedPriceDetails->item(0)->getAttribute("AdultReductionAmount");
                    } else {
                        $ReducedPriceDetails = "";
                    }
                    
                // error_log("\r\n INCLUDESDINNER $INCLUDESDINNER \r\n", 3, "/srv/www/htdocs/error_log");
                
                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                    if (is_array($tmp[$shid])) {
                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                    } else {
                        $baseCounterDetails = 0;
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HOTEL_NAME;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $HOTEL_CODE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-23";
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $ROOM_NAME;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $ROOM_CODE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $ROOM_NAME;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateCode'] = $CCHARGES_CODE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['OCCUPANCY'] = $CCHARGES_CODE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['CCHARGES_CODE'] = $CCHARGES_CODE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TOTAL_ADULT_PRICE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $TOTAL_ADULT_PRICE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ADULT_PRICE'] = $ADULT_PRICE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ADULT_PRICE_DETAILS'] = $ADULT_PRICE_DETAILS;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['CHILD_PRICE'] = $CHILD_PRICE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['maxpersons'] = $ROOM_PAX;
                    $RULE_TEXT = "";
                    if ($AdultReductionPercentage > 0) {
                        $RULE_TEXT = $AdultReductionPercentage . "% " . gettext("discount");
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $RULE_TEXT;
                    } else {
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                    }
                    
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($ROOM_CODE);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $amount = $noOfNights * $TOTAL_ADULT_PRICE;
                        if ($TravcoMarkup != 0) {
                            $amount = $amount + (($amount * $TravcoMarkup) / 100);
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
                        if ($TravcoMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $CURRENCY_CODE;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CURRENCY_CODE;
                }
            }
            }
            }
        }
    }
}
            $travco = true;
        }
    }
}
 //error_log("\r\n TMP2:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

 if ($travco == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mtravco where " . $sfilter;
        error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        error_log("\r\n PASSOU 1 $result2 \r\n", 3, "/srv/www/htdocs/error_log");
        $result2->buffer();
        
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            error_log("\r\n PASSOU 2 \r\n", 3, "/srv/www/htdocs/error_log");
            foreach ($resultSet2 as $row2) {
                // $sidfilter[] = "id=" . $row2->hid;
                $sidfilter[] = $row2->hid;
                error_log("\r\n PASSOU 3 \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 23;
        error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_travco');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_travco');
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