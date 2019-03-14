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
//error_log("\r\n COMECOU DISNEY \r\n", 3, "/srv/www/htdocs/error_log");
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$disney = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id,city_xml58, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml58 = $row_settings["city_xml58"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml58 = "";
}
error_log("\r\n city_xml58 $city_xml58 \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='enablemajesticusadisney' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_disney = $affiliate_id;
} else {
    $affiliate_id_disney = 0;
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
    $sql = "select value from settings where name='majesticusaDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_disney";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}

$sql = "select value from settings where name='majesticusadisneyLoginEmail' and affiliate_id=$affiliate_id_disney";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusadisneyLoginEmail = $row_settings['value'];
}
//error_log("\r\n majesticusadisneyLoginEmail $majesticusadisneyLoginEmail \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='majesticusadisneyPassword' and affiliate_id=$affiliate_id_disney";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusadisneyPassword = base64_decode($row_settings['value']);
}
//error_log("\r\n majesticusadisneyPassword $majesticusadisneyPassword \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='majesticusadisneyMarkup' and affiliate_id=$affiliate_id_disney";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusadisneyMarkup = (double) $row_settings['value'];
} else {
    $majesticusadisneyMarkup = 0;
}
//error_log("\r\n majesticusadisneyMarkup $majesticusadisneyMarkup \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='majesticusadisneyServiceURL' and affiliate_id=$affiliate_id_disney";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusadisneyServiceURL = $row_settings['value'];
}
//error_log("\r\n majesticusaServiceURL $majesticusadisneyServiceURL \r\n", 3, "/srv/www/htdocs/error_log");

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
   <soap:Header/>
   <soap:Body>
      <tem:Search_Results_HotelDisney>
         <tem:SearchID>-1</tem:SearchID>
         <tem:CityID>729</tem:CityID>
         <tem:ZoneID>10077</tem:ZoneID>
         <tem:Text></tem:Text>
         <tem:SortBy>PRICELTOH</tem:SortBy>
         <tem:Arrival>' . strftime("%Y-%m-%d", $from) . '</tem:Arrival>
         <tem:Departure>' . strftime("%Y-%m-%d", $to) . '</tem:Departure>
         <tem:RoomQty>' . $rooms . '</tem:RoomQty>
         <tem:Rooms>
            <tem:clsRooms>
               <tem:Number>1</tem:Number>
               <tem:Adults>1</tem:Adults>
               <tem:Childs>
               </tem:Childs>
            </tem:clsRooms>
         </tem:Rooms>
         <tem:RoomType></tem:RoomType>
         <tem:RoomIntelligence>false</tem:RoomIntelligence>
         <tem:Filters>
         </tem:Filters>
         <tem:PageSize>30</tem:PageSize>
         <tem:PageIndex>1</tem:PageIndex>
         <tem:AgencyID>1663</tem:AgencyID>
         <tem:UserID>8731</tem:UserID>
         <!--Optional:-->
         <tem:DisneyCategory>
            <tem:string>VALUE</tem:string>
            <tem:string>MODERATE</tem:string>
            <tem:string>DELUXE</tem:string>
         </tem:DisneyCategory>
      </tem:Search_Results_HotelDisney>
   </soap:Body>
</soap:Envelope>';
//error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");
if ($majesticusadisneyServiceURL != "" and $majesticusadisneyLoginEmail != "" and $majesticusadisneyPassword) {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $majesticusadisneyServiceURL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept-Encoding: gzip, deflate",
        "Accept: application/xml",
        "Content-type: text/xml",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    
    //error_log("\r\n RESPONSE: $response \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_disney');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $majesticusadisneyServiceURL . $raw,
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
    
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName('Envelope');
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $Search_Results_HotelDisneyResponse = $Body->item(0)->getElementsByTagName("Search_Results_HotelDisneyResponse");
    $Search_Results_HotelDisneyResult = $Search_Results_HotelDisneyResponse->item(0)->getElementsByTagName("Search_Results_HotelDisneyResult");
    $node = $Search_Results_HotelDisneyResult->item(0)->getElementsByTagName("clsHotelDisney");
    if ($node->length > 0) {
        for ($i = 0; $i < $node->length; $i++) {
            $IsFavorite = $node->item($i)->getElementsByTagName("IsFavorite");
            if ($IsFavorite->length > 0) {
                $IsFavorite = $IsFavorite->item(0)->nodeValue;
            } else {
                $IsFavorite = "";
            }
            $HasTags = $node->item($i)->getElementsByTagName("HasTags");
            if ($HasTags->length > 0) {
                $HasTags = $HasTags->item(0)->nodeValue;
            } else {
                $HasTags = "";
            }
            $HasNotes = $node->item($i)->getElementsByTagName("HasNotes");
            if ($HasNotes->length > 0) {
                $HasNotes = $HasNotes->item(0)->nodeValue;
            } else {
                $HasNotes = "";
            }
            $SearchID = $node->item($i)->getElementsByTagName("SearchID");
            if ($SearchID->length > 0) {
                $SearchID = $SearchID->item(0)->nodeValue;
            } else {
                $SearchID = "";
            }
            $HotelName = $node->item($i)->getElementsByTagName("HotelName");
            if ($HotelName->length > 0) {
                $HotelName = $HotelName->item(0)->nodeValue;
            } else {
                $HotelName = "";
            }
            $HotelID = $node->item($i)->getElementsByTagName("HotelID");
            if ($HotelID->length > 0) {
                $HotelID = $HotelID->item(0)->nodeValue;
            } else {
                $HotelID = "";
            }
            $shid = $HotelID;
            $sfilter[] = " sid='$HotelID' ";
            $IsPromoAvailable = $node->item($i)->getElementsByTagName("IsPromoAvailable");
            if ($IsPromoAvailable->length > 0) {
                $IsPromoAvailable = $IsPromoAvailable->item(0)->nodeValue;
            } else {
                $IsPromoAvailable = "";
            }
            $Stars = $node->item($i)->getElementsByTagName("Stars");
            if ($Stars->length > 0) {
                $Stars = $Stars->item(0)->nodeValue;
            } else {
                $Stars = "";
            }
            $State = $node->item($i)->getElementsByTagName("State");
            if ($State->length > 0) {
                $State = $State->item(0)->nodeValue;
            } else {
                $State = "";
            }
            $Country = $node->item($i)->getElementsByTagName("Country");
            if ($Country->length > 0) {
                $Country = $Country->item(0)->nodeValue;
            } else {
                $Country = "";
            }
            $Address = $node->item($i)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $Address = $Address->item(0)->nodeValue;
            } else {
                $Address = "";
            }
            $Zip = $node->item($i)->getElementsByTagName("Zip");
            if ($Zip->length > 0) {
                $Zip = $Zip->item(0)->nodeValue;
            } else {
                $Zip = "";
            }
            $Phone = $node->item($i)->getElementsByTagName("Phone");
            if ($Phone->length > 0) {
                $Phone = $Phone->item(0)->nodeValue;
            } else {
                $Phone = "";
            }
            $Checkin = $node->item($i)->getElementsByTagName("Checkin");
            if ($Checkin->length > 0) {
                $Checkin = $Checkin->item(0)->nodeValue;
            } else {
                $Checkin = "";
            }
            $Checkout = $node->item($i)->getElementsByTagName("Checkout");
            if ($Checkout->length > 0) {
                $Checkout = $Checkout->item(0)->nodeValue;
            } else {
                $Checkout = "";
            }
            $ChildAge = $node->item($i)->getElementsByTagName("ChildAge");
            if ($ChildAge->length > 0) {
                $ChildAge = $ChildAge->item(0)->nodeValue;
            } else {
                $ChildAge = "";
            }
            $HotelNotes = $node->item($i)->getElementsByTagName("HotelNotes");
            if ($HotelNotes->length > 0) {
                $HotelNotes = $HotelNotes->item(0)->nodeValue;
            } else {
                $HotelNotes = "";
            }
            $ImageUrl = $node->item($i)->getElementsByTagName("ImageUrl");
            if ($ImageUrl->length > 0) {
                $ImageUrl = $ImageUrl->item(0)->nodeValue;
            } else {
                $ImageUrl = "";
            }
            $Lat = $node->item($i)->getElementsByTagName("Lat");
            if ($Lat->length > 0) {
                $Lat = $Lat->item(0)->nodeValue;
            } else {
                $Lat = "";
            }
            $Lon = $node->item($i)->getElementsByTagName("Lon");
            if ($Lon->length > 0) {
                $Lon = $Lon->item(0)->nodeValue;
            } else {
                $Lon = "";
            }
            //RoomTypes
            $RoomTypes = $node->item($i)->getElementsByTagName("RoomTypes");
            if ($RoomTypes->length > 0) {
                $clsRoomTypesDisney = $RoomTypes->item(0)->getElementsByTagName("clsRoomTypesDisney");
                for ($j=0; $j < $clsRoomTypesDisney->length; $j++) { 
                    $Multiplier = $clsRoomTypesDisney->item($j)->getElementsByTagName("Multiplier");
                    if ($Multiplier->length > 0) {
                        $Multiplier = $Multiplier->item(0)->nodeValue;
                    } else {
                        $Multiplier = "";
                    }
                    $RoomID = $clsRoomTypesDisney->item($j)->getElementsByTagName("RoomID");
                    if ($RoomID->length > 0) {
                        $RoomID = $RoomID->item(0)->nodeValue;
                    } else {
                        $RoomID = "";
                    }
                    $RoomType = $clsRoomTypesDisney->item($j)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $RoomType = $RoomType->item(0)->nodeValue;
                    } else {
                        $RoomType = "";
                    }
                    $AccID = $clsRoomTypesDisney->item($j)->getElementsByTagName("AccID");
                    if ($AccID->length > 0) {
                        $AccID = $AccID->item(0)->nodeValue;
                    } else {
                        $AccID = "";
                    }
                    $RateType = $clsRoomTypesDisney->item($j)->getElementsByTagName("RateType");
                    if ($RateType->length > 0) {
                        $RateType = $RateType->item(0)->nodeValue;
                    } else {
                        $RateType = "";
                    }
                    $RateTypeColor = $clsRoomTypesDisney->item($j)->getElementsByTagName("RateTypeColor");
                    if ($RateTypeColor->length > 0) {
                        $RateTypeColor = $RateTypeColor->item(0)->nodeValue;
                    } else {
                        $RateTypeColor = "";
                    }
                    $Price = $clsRoomTypesDisney->item($j)->getElementsByTagName("Price");
                    if ($Price->length > 0) {
                        $Price = $Price->item(0)->nodeValue;
                    } else {
                        $Price = "";
                    }
                    $Status = $clsRoomTypesDisney->item($j)->getElementsByTagName("Status");
                    if ($Status->length > 0) {
                        $Status = $Status->item(0)->nodeValue;
                    } else {
                        $Status = "";
                    }
                    $Image = $clsRoomTypesDisney->item($j)->getElementsByTagName("Image");
                    if ($Image->length > 0) {
                        $Image = $Image->item(0)->nodeValue;
                    } else {
                        $Image = "";
                    }
                    $MaxOccup = $clsRoomTypesDisney->item($j)->getElementsByTagName("MaxOccup");
                    if ($MaxOccup->length > 0) {
                        $MaxOccup = $MaxOccup->item(0)->nodeValue;
                    } else {
                        $MaxOccup = "";
                    }
                    $RoomDescription = $clsRoomTypesDisney->item($j)->getElementsByTagName("RoomDescription");
                    if ($RoomDescription->length > 0) {
                        $RoomDescription = $RoomDescription->item(0)->nodeValue;
                    } else {
                        $RoomDescription = "";
                    }
                    $StatusDisney = $clsRoomTypesDisney->item($j)->getElementsByTagName("StatusDisney");
                    if ($StatusDisney->length > 0) {
                        $clsStatusRoomDisney = $StatusDisney->item(0)->getElementsByTagName("clsStatusRoomDisney");
                        for ($k=0; $k < $clsStatusRoomDisney->length; $k++) { 
                            $Status2 = $clsStatusRoomDisney->item($k)->getElementsByTagName("Status");
                            if ($Status2->length > 0) {
                                $Status2 = $Status2->item(0)->nodeValue;
                            } else {
                                $Status2 = "";
                            }
                            $DisneyRatePlan = $clsStatusRoomDisney->item($k)->getElementsByTagName("DisneyRatePlan");
                            if ($DisneyRatePlan->length > 0) {
                                $DisneyRatePlan = $DisneyRatePlan->item(0)->nodeValue;
                            } else {
                                $DisneyRatePlan = "";
                            }
                        }
                    }

                    // error_log("\r\n INCLUDESDINNER $INCLUDESDINNER \r\n", 3, "/srv/www/htdocs/error_log");
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
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-58";
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomType;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $RoomID;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomtype'] = $RoomType;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['accid'] = $AccId;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['searchid'] = $SearchID;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $Price;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $Price;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = $Status;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['maxpersons'] = $MaxOccup;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($RateType);
                        $pricebreakdown = array();
                        $pricebreakdownCount = 0;
                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                            $amount = $Price / $noOfNights;
                            if ($majesticusaMarkup != 0) {
                                $amount = $amount + (($amount * $majesticusaMarkup) / 100);
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
                            if ($majesticusaMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $scurrency;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $scurrency;
                    }

                }
            }
            $disney = true;
        }
    }
}

// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($disney == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mdisney where " . $sfilter;
        // error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        // error_log("\r\n PASSOU 1 $result2 \r\n", 3, "/srv/www/htdocs/error_log");
        $result2->buffer();
        
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            // error_log("\r\n PASSOU 2 \r\n", 3, "/srv/www/htdocs/error_log");
            foreach ($resultSet2 as $row2) {
                // $sidfilter[] = "id=" . $row2->hid;
                $sidfilter[] = $row2->hid;
                // error_log("\r\n PASSOU 3 \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 58;
        // error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_disney');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_disney');
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