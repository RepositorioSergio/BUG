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
error_log("\r\n COMECOU OTS \r\n", 3, "/srv/www/htdocs/error_log");
unset($tmp);
$sfilter = array();
$ots = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id,city_xml47, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml47 = $row_settings["city_xml47"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml47 = "";
}
$sql = "select value from settings where name='enableOTS' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_ots = $affiliate_id;
} else {
    $affiliate_id_ots = 0;
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
    $sql = "select value from settings where name='otsDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_ots";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='OTSID' and affiliate_id=$affiliate_id_ots";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $OTSID = $row_settings['value'];
}
$sql = "select value from settings where name='OTSPassword' and affiliate_id=$affiliate_id_ots";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $OTSPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='OTSMarkup' and affiliate_id=$affiliate_id_ots";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $OTSMarkup = (double) $row_settings['value'];
} else {
    $OTSMarkup = 0;
}
$sql = "select value from settings where name='OTSServiceURL' and affiliate_id=$affiliate_id_ots";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $OTSServiceURL = $row_settings['value'];
}
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$date = new Datetime();
$timestamp = $date->format('U');
$time = date('H:i:s', time());
$countryCode = 'US';
$Destination = 'New York';
$Region = 'New York City Area';
$count = 0;
$raw = '<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" AvailRatesOnly="true" Version="0.1">
<POS>
    <Source>
        <RequestorID Instance="MF001" ID_Context="AxisData" ID="' . $OTSID . '" Type="22"/>
    </Source>
    <Source>
        <RequestorID Type="88" ID="' . $OTSID . '" MessagePassword="' . $OTSPassword . '"/>
    </Source>
</POS>
<AvailRequestSegments>
    <AvailRequestSegment>
        <StayDateRange End="' . strftime("%Y-%m-%d", $to) . '" Start="' . strftime("%Y-%m-%d", $from) . '"/>
        <RoomStayCandidates>';
        for ($r=0; $r < $rooms; $r++) { 
            $raw = $raw . '<RoomStayCandidate Quantity="1" RPH="' . ($r+1) . '">
            <GuestCounts>
            <GuestCount Age="32" Count="' . $selectedAdults[$r] . '" AgeQualifyingCode="10"/>';
            if ($selectedChildren[$r] > 0) {
                for ($z=0; $z < $selectedChildren[$r]; $z++) { 
                    if ($selectedChildrenAges[$r][$z] > 1 ) {
                        $count = $count +1;
                        $raw = $raw . '<GuestCount Age="' . $selectedChildrenAges[$r][$z] . '" Count="' . $count . '" AgeQualifyingCode="8"/>';
                        $count = 0;
                    } else {
                        $count = $count +1;
                        $raw = $raw . '<GuestCount Age="' . $selectedChildrenAges[$r][$z] . '" Count="' . $count . '" AgeQualifyingCode="7"/>';
                        $count = 0;
                    }
                }
            }
            $raw = $raw . '</GuestCounts>
                    </RoomStayCandidate>';
        }
            
$raw = $raw . '</RoomStayCandidates>
        <HotelSearchCriteria>
            <Criterion>
                <RefPoint CodeContext="CountryCode">' . $countryCode . '</RefPoint>
                <RefPoint CodeContext="Destination">' . $Destination . '</RefPoint>
                <RefPoint CodeContext="Region">' . $Region . '</RefPoint>
            </Criterion>
        </HotelSearchCriteria>
    </AvailRequestSegment>
</AvailRequestSegments>
</OTA_HotelAvailRQ>';

error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");

if ($OTSServiceURL != "") {
    $headers = array(
        "Accept: application/xml",
        "Content-type: application/x-www-form-urlencoded",
        "Content-Encoding: UTF-8",
        "Accept-Encoding: gzip,deflate",
        "Content-length: " . strlen($raw)
    );
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $OTSServiceURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();

    error_log("\r\n RESPONSE: $response \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_ots');
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

    
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $OTA_HotelAvailRS = $inputDoc->getElementsByTagName("OTA_HotelAvailRS");
    //HotelStays
    $HotelStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName("HotelStays");
    if ($HotelStays->length > 0) {
        $HotelStay = $HotelStays->item(0)->getElementsByTagName("HotelStay");
        if ($HotelStay->length > 0) {
            for ($i=0; $i < $HotelStay->length; $i++) { 
                $BasicPropertyInfo = $HotelStay->item($i)->getElementsByTagName("BasicPropertyInfo");
                if ($BasicPropertyInfo->length > 0) {
                    $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                    $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
                    $AreaID = $BasicPropertyInfo->item(0)->getAttribute("AreaID");
                    $HotelCodeContext = $BasicPropertyInfo->item(0)->getAttribute("HotelCodeContext");

                    $Award = $BasicPropertyInfo->item(0)->getElementsByTagName("Award");
                    if ($Award->length > 0) {
                        $Rating = $Award->item(0)->getAttribute("Rating");
                    } else {
                        $Rating = "";
                    }

                    $img = "";
                    $VendorMessages = $BasicPropertyInfo->item(0)->getElementsByTagName("VendorMessages");
                    if ($VendorMessages->length > 0) {
                        $VendorMessage = $VendorMessages->item(0)->getElementsByTagName("VendorMessage");
                        if ($VendorMessage->length > 0) {
                            $Title = $VendorMessage->item(0)->getAttribute("Title");
                            $SubSection = $VendorMessage->item(0)->getElementsByTagName("SubSection");
                            if ($SubSection->length > 0) {
                                $Paragraph = $SubSection->item(0)->getElementsByTagName("Paragraph");
                                if ($Paragraph->length > 0) {
                                    $Image = $Paragraph->item(0)->getElementsByTagName("Image");
                                    if ($Image->length > 0) {
                                        for ($iAux=0; $iAux < $Image->length; $iAux++) { 
                                            $img = $Image->item($iAux)->nodeValue;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                } else {
                    $HotelCode = "";
                    $HotelName = "";
                    $AreaID = "";
                    $HotelCodeContext = "";
                }  
            }
        }
    }

    //Areas
    $txt = "";
    $Areas = $OTA_HotelAvailRS->item(0)->getElementsByTagName("Areas");
    if ($Areas->length > 0) {
        $Area = $Areas->item(0)->getElementsByTagName("Area");
        if ($Area->length > 0) {
            for ($j=0; $j < $Area->length; $j++) { 
                $AreaID = $Area->item($j)->getAttribute("AreaID");

                $AreaDescription = $Area->item($j)->getElementsByTagName("AreaDescription");
                if ($AreaDescription->length > 0) {
                    $Name = $AreaDescription->item($jAux)->getAttribute("Name");

                    $Text = $AreaDescription->item($jAux)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($jAux2=0; $jAux2 < $Text->length; $jAux2++) { 
                            $txt = $Text->item($jAux2)->nodeValue;
                        }
                    }
                }
            }
        }
    }

    //RoomStays
    $RoomStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName("RoomStays");
    if ($RoomStays->length > 0) {
        $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
        if ($RoomStay->length > 0) {
            for ($k=0; $k < $RoomStay->length; $k++) { 
                $ResponseType = $RoomStay->item($k)->getAttribute("ResponseType");
                $RPH = $RoomStay->item($k)->getAttribute("RPH");
                $RoomStayCandidateRPH = $RoomStay->item($k)->getAttribute("RoomStayCandidateRPH");

                $RoomTypes = $RoomStay->item($k)->getElementsByTagName("RoomTypes");
                if ($RoomTypes->length > 0) {
                    $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
                    }
                }

                $RoomRates = $RoomStay->item($k)->getElementsByTagName("RoomRates");
                if ($RoomRates->length > 0) {
                    $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                    if ($RoomRate->length > 0) {
                        $RoomTypeCode2 = $RoomRate->item(0)->getAttribute("RoomTypeCode");
                        $NumberOfUnits = $RoomRate->item(0)->getAttribute("NumberOfUnits");
                        //Rates
                        $Rates = $RoomRate->item(0)->getElementsByTagName("Rates");
                        if ($Rates->length > 0) {
                            $Rate = $Rates->item(0)->getElementsByTagName("Rate");
                            if ($Rate->length > 0) {
                                $Base = $Rate->item(0)->getElementsByTagName("Base");
                                if ($Base->length > 0) {
                                    $BaseCurrencyCode = $Base->item(0)->getAttribute("CurrencyCode");
                                    $BaseAmountAfterTax = $Base->item(0)->getAttribute("AmountAfterTax");
                                }
                                $Total = $Rate->item(0)->getElementsByTagName("Total");
                                if ($Total->length > 0) {
                                    $TotalCurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                                    $TotalAmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                                }
                            }
                        }
                        //Features
                        $Features = $RoomRate->item(0)->getElementsByTagName("Features");
                        if ($Features->length > 0) {
                            $Feature = $Features->item(0)->getElementsByTagName("Feature");
                            if ($Feature->length > 0) {
                                $Description = $Feature->item(0)->getElementsByTagName("Description");
                                if ($Description->length > 0) {
                                    $Text = $Description->item(0)->getElementsByTagName('Text');
                                    if ($Text->length > 0) {
                                        $Text = $Text->item(0)->nodeValue;
                                    } else {
                                        $Text = "";
                                    }
                                }
                            }
                        }
                    }
                }
                //GuestCounts
                $GuestCounts = $RoomStay->item($k)->getElementsByTagName("GuestCounts");
                if ($GuestCounts->length > 0) {
                    $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
                    if ($GuestCount->length > 0) {
                        $Count = $GuestCount->item(0)->getAttribute("Count");
                        $AgeQualifyingCode = $GuestCount->item(0)->getAttribute("AgeQualifyingCode");
                    }
                }
                //TimeSpan
                $TimeSpan = $RoomStay->item($k)->getElementsByTagName("TimeSpan");
                if ($TimeSpan->length > 0) {
                    $End = $TimeSpan->item(0)->getAttribute("End");
                    $Start = $TimeSpan->item(0)->getAttribute("Start");
                }
                //Reference
                $Reference = $RoomStay->item($k)->getElementsByTagName("Reference");
                if ($Reference->length > 0) {
                    $ID_Context = $Reference->item(0)->getAttribute("ID_Context");
                    $ReferenceID = $Reference->item(0)->getAttribute("ID");
                    $Type = $Reference->item(0)->getAttribute("Type");
                }
                //BasicPropertyInfo
                $BasicPropertyInfo = $RoomStay->item($k)->getElementsByTagName("BasicPropertyInfo");
                if ($BasicPropertyInfo->length > 0) {
                    $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                    $shid = $HotelCode;
                    $sfilter[] = " sid='$HotelCode' ";
                    $VendorMessages = $BasicPropertyInfo->item(0)->getElementsByTagName('VendorMessages');
                    if ($VendorMessages->length > 0) {
                        $VendorMessage = $VendorMessages->item(0)->getElementsByTagName('VendorMessage');
                        if ($VendorMessage->length > 0) {
                            $VendorMessageTitle = $VendorMessage->item(0)->getAttribute("Title");
                            $VendorMessageInfoType = $VendorMessage->item(0)->getAttribute("InfoType");

                            $SubSection = $VendorMessage->item(0)->getElementsByTagName('SubSection');
                            if ($SubSection->length > 0) {
                                for ($x=0; $x < $SubSection->length; $x++) { 
                                    $SubCode = $SubSection->item($x)->getAttribute("SubCode");
                                    $SubTitle = $SubSection->item($x)->getAttribute("SubTitle");

                                    $Paragraph = $SubSection->item($x)->getElementsByTagName('Paragraph');
                                    if ($Paragraph->length > 0) {
                                        $ParagraphText = $Paragraph->item(0)->getElementsByTagName('Text');
                                        if ($ParagraphText->length > 0) {
                                            $ParagraphText = $ParagraphText->item(0)->nodeValue;
                                        } else {
                                            $ParagraphText = "";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                    if (is_array($tmp[$shid])) {
                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                    } else {
                        $baseCounterDetails = 0;
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $HotelCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-1";
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomTypeCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $RoomTypeCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomtype'] = $RoomTypeCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TotalAmountAfterTax;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $BaseAmountAfterTax;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RPH'] = $RPH;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomStayCandidateRPH'] = $RPRoomStayCandidateRPHH;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                    try {
                        $sql = "select mapped from board_mapping where description='" . addslashes($Text) . "'";
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        $row_board_mapping = $statement->execute();
                        $row_board_mapping->buffer();
                        if ($row_board_mapping->valid()) {
                            $row_board_mapping = $row_board_mapping->current();
                            $NameM = $row_board_mapping["mapped"];
                        }
                    } catch (\Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($Text);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $amount = $TotalAmountAfterTax / $noOfNights;
                        if ($OTSMarkup != 0) {
                            $amount = $amount + (($amount * $OTSMarkup) / 100);
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
                        if ($OTSMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
    }
    $ots = true;
}

if ($ots == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mots where " . $sfilter;
        error_log("\r\n SQL $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 1;
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_ots');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_ots');
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