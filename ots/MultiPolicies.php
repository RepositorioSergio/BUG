<?php
error_log("\r\nMulti Policies OTS\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
use Zend\Http\Client;
use Zend\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$salestaxes = 0;
$salestaxesfees = 0;
$baserate = 0;
$affiliate_id_expedia = 0;
$occupancies = "";
$sindex = $index;
$db = new \Zend\Db\Adapter\Adapter($config);
if ($details == "hoteldetails") {
    // Detail level
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_ots where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_ots where session_id='$session_id'";
}
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $lang = $searchsettings['lang'];
    $currency = $searchsettings['currency'];
    $from = $searchsettings['from'];
    $to = $searchsettings['to'];
    $destination = $searchsettings['destination'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    $index = $searchsettings['index'];
    $ipaddress = $searchsettings['ipaddress'];
    $nationality = $searchsettings['nationality'];
    $residency = $searchsettings['residency'];
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
    if ($details == "hoteldetails") {
        $selectedAdults = array();
        $selectedAdults[$nroom] = $adt;
        // Children + Ages
        $selectedChildrenAges = array();
        $selectedChildren = array();
        $selectedChildren[$nroom] = $chd;
        if ($chd > 0) {
            $children_ages = explode(",", $children_ages);
            for ($w = 0; $w < count($children_ages); $w ++) {
                $selectedChildrenAges[$nroom][$w] = $children_ages[$w];
            }
        }
    }
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$affiliate_id = 0;
$branch_filter = '';
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
$sql = "select value from settings where name='OTSID' and affiliate_id=$affiliate_id_ots" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $OTSID = $row_settings['value'];
}
$sql = "select value from settings where name='OTSPassword' and affiliate_id=$affiliate_id_ots" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $OTSPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='OTSMarkup' and affiliate_id=$affiliate_id_ots" . $branch_filter;
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
$sql = "select value from settings where name='OTSServiceURL' and affiliate_id=$affiliate_id_ots" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $OTSServiceURL = $row_settings['value'];
}
$outputArray = array();
$arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
foreach ($arrIt as $sub) {
    $subArray = $arrIt->getSubIterator();
    if (isset($quoteid[$nroom])) {
        if (isset($subArray['quoteid'])) {
            if ($subArray['quoteid'] === $quoteid[$nroom]) {
                $outputArray[] = iterator_to_array($subArray);
                $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                    ->key();
            }
        }
    }
}
$breakdownTmp = array();
if (! is_array($outputArray)) {
    $response['error'] = "Unable to handle request #3";
    return false;
} else {
    array_push($breakdownTmp, $outputArray);
}
$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');
$c = $nroom;
$response = array();
$roombreakdown2 = array();
foreach ($breakdownTmp as $k => $v) {
    foreach ($v as $key => $value) {
        $shid = $value['shid'];
        $code = $value['hotelid'];
        $scode = $value['shid'];
        $HotelId = $value['hotelid'];
        $room_code = $value['roomid'];
        
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";

        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $item = array();
        $roomtypecode = $value['roomtypecode'];
        $meal = $value['mealinformation'];
        $count = 0;
        $count2 = 0;
        //
        // EOF Policies
        //
        // EOF Check prices & availability
        //

        $raw = '<OTA_HotelResRQ ResStatus="Quote" EchoToken="550e8efd-344e-4f13-9551-d517a9520bbd" Version="2009.1" xmlns="http://www.opentravel.org/OTA/2003/05">
        <POS>
        <Source>
            <RequestorID Type="88" ID="' . $OTSID . '" MessagePassword="' . $OTSPassword . '"/> 
        </Source>
        <Source>
            <RequestorID ID_Context="AxisData" Type="22" ID="' . $OTSID . '"/>
        </Source>
        </POS>
        <HotelReservations>
        <HotelReservation>
            <RoomStays>';
            for ($r=0; $r < count($selectedAdults); $r++) { 
                $raw = $raw . '<RoomStay RPH="' . ($r + 1) . '">
                <RoomTypes>
                    <RoomType RoomTypeCode="' . $roomtypecode . '" />
                </RoomTypes>
                <TimeSpan End="' . $to_date . '" Start="' . $from_date . '" />
                <BasicPropertyInfo HotelCode="' . $HotelId . '" />
                <ResGuestRPHs>';
                for ($y=0; $y < $selectedAdults[$r]; $y++) { 
                    $count = $count + 1;
                    $raw = $raw . '<ResGuestRPH RPH="' . $count . '" />';
                }

                if ($selectedChildren[$r] > 0) {
                    for ($z=0; $z < $selectedChildren[$r]; $z++) { 
                        $count = $count + 1;
                        $raw = $raw . '<ResGuestRPH RPH="' . $count . '" />';
                    }
                }
                $raw = $raw . '</ResGuestRPHs>
                    <ServiceRPHs>
                        <ServiceRPH RPH="1" />
                    </ServiceRPHs>
                </RoomStay>';
            }

            $raw = $raw . '</RoomStays>
            <Services>
                <Service ServiceInventoryCode="' . $meal . '" ServiceRPH="1" />
            </Services>
            <ResGuests>';
            for ($x=0; $x < $selectedAdults[$nroom]; $x++) { 
                $count2 = $count2 + 1;
                $raw = $raw . '<ResGuest AgeQualifyingCode="10" ResGuestRPH="' . $count2 . '">
                    <GuestCounts>
                    <GuestCount Age="30" />
                    </GuestCounts>
                </ResGuest>';
            }
            if ($chd > 0) {
                $children_ages = explode(",", $children_ages);
                for ($w = 0; $w < count($children_ages); $w ++) {
                    $selectedChildrenAges[$nroom][$w] = $children_ages[$w];
                    $count2 = $count2 + 1;
                    if ($selectedChildrenAges[$nroom][$w] > 1) {
                        $raw = $raw . '<ResGuest AgeQualifyingCode="8" ResGuestRPH="' . $count2 . '">
                            <GuestCounts>
                            <GuestCount Age="' . $selectedChildrenAges[$nroom][$w] . '" />
                            </GuestCounts>
                        </ResGuest>';
                    } else {
                        $raw = $raw . '<ResGuest AgeQualifyingCode="7" ResGuestRPH="' . $count2 . '">
                            <GuestCounts>
                            <GuestCount Age="' . $selectedChildrenAges[$nroom][$w] . '" />
                            </GuestCounts>
                        </ResGuest>';
                    }
                }
            }
        $raw = $raw . '</ResGuests>
        </HotelReservation>
        </HotelReservations>
        </OTA_HotelResRQ>';

        error_log("\r\n RAW - $raw \r\n", 3, "/srv/www/htdocs/error_log");

        $headers = array(
            "Accept: application/xml",
            "Content-type: application/x-www-form-urlencoded",
            "Content-Encoding: UTF-8",
            "Accept-Encoding: gzip,deflate",
            "Content-length: " . strlen($raw)
        );
        
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
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

        error_log("\r\n Response - $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_snaptravel');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $OTSServiceURL,
                'sqlcontext' => $response2,
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
        $inputDoc->loadXML($response2);
        $OTA_HotelResRS = $inputDoc->getElementsByTagName("OTA_HotelResRS");

        $HotelReservations = $OTA_HotelResRS->item(0)->getElementsByTagName("HotelReservations");
        if ($HotelReservations->length > 0) {
            $HotelReservation = $HotelReservations->item(0)->getElementsByTagName("HotelReservation");
            if ($HotelReservation->length > 0) {
                $RoomStays = $HotelReservation->item(0)->getElementsByTagName("RoomStays");
                if ($RoomStays->length > 0) {
                    $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
                    if ($RoomStay->length > 0) {
                        $IndexNumber = $RoomStay->item(0)->getAttribute("IndexNumber");
                        $RPH = $RoomStay->item(0)->getAttribute("RPH");
                        $Total = $RoomStay->item(0)->getElementsByTagName("Total");
                        if ($Total->length > 0) {
                        $AmountAfterTax = $Total->item(0)->getElementsByTagName("AmountAfterTax");
                        $CurrencyCode = $Total->item(0)->getElementsByTagName("CurrencyCode");
                        } else {
                        $AmountAfterTax = "";
                        $CurrencyCode = "";
                        }
                    }
                }
                //ResGlobalInfo
                $CancellationPoliciesArray = array();
                $count3 = 0;
                $ResGlobalInfo = $HotelReservation->item(0)->getElementsByTagName("ResGlobalInfo");
                if ($ResGlobalInfo->length > 0) {
                    $TotalRGI = $ResGlobalInfo->item(0)->getElementsByTagName("Total");
                    if ($TotalRGI->length > 0) {
                        $AmountAfterTaxRGI = $TotalRGI->item(0)->getElementsByTagName("AmountAfterTax");
                        $CurrencyCodeRGI = $TotalRGI->item(0)->getElementsByTagName("CurrencyCode");
                    } else {
                        $AmountAfterTaxRGI = "";
                        $CurrencyCodeRGI = "";
                    }
                    $CancelPenalties = $ResGlobalInfo->item(0)->getElementsByTagName("CancelPenalties");
                    if ($CancelPenalties->length > 0) {
                        $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                        if ($CancelPenalty->length > 0) {
                            for ($i=0; $i < $CancelPenalty->length; $i++) { 
                                $Item_RPH = $CancelPenalty->item($i)->getAttribute("Item_RPH");
                                //Deadline
                                $Deadline = $CancelPenalty->item($i)->getElementsByTagName("Deadline");
                                if ($Deadline->length > 0) {
                                    $OffsetDropTime = $Deadline->item(0)->getAttribute("OffsetDropTime");
                                    $OffsetTimeUnit = $Deadline->item(0)->getAttribute("OffsetTimeUnit");
                                    $OffsetUnitMultiplier = $Deadline->item(0)->getAttribute("OffsetUnitMultiplier");
                                }
                                //AmountPercent
                                $AmountPercent = $CancelPenalty->item($i)->getElementsByTagName("AmountPercent");
                                if ($AmountPercent->length > 0) {
                                    $Percent = $AmountPercent->item(0)->getAttribute("Percent");
                                    $NmbrOfNights = $AmountPercent->item(0)->getAttribute("NmbrOfNights");
                                }
                                $CancellationPoliciesArray[$count3]['Item_RPH'] = $Item_RPH;
                                $CancellationPoliciesArray[$count3]['OffsetDropTime'] = $OffsetDropTime;
                                $CancellationPoliciesArray[$count3]['OffsetTimeUnit'] = $OffsetTimeUnit;
                                $CancellationPoliciesArray[$count3]['OffsetUnitMultiplier'] = $OffsetUnitMultiplier;
                                $CancellationPoliciesArray[$count3]['Percent'] = $Percent;
                                $CancellationPoliciesArray[$count3]['NmbrOfNights'] = $NmbrOfNights;
                                $count3 = $count3 + 1;
                            }
                        }
                    }
                }
                //TPA_Extensions
                $TPA_Extensions = $HotelReservation->item(0)->getElementsByTagName("TPA_Extensions");
                if ($TPA_Extensions->length > 0) {
                    $BookingStatus = $TPA_Extensions->item(0)->getElementsByTagName("BookingStatus");
                    if ($BookingStatus->length > 0) {
                        $ReservationStatusType = $BookingStatus->item(0)->getAttribute("ReservationStatusType");
                    }
                }
            }
        }
      
        $tam = count($CancellationPoliciesArray); 
        for ($i=0; $i < $tam; $i++) { 
            $cancelpolicy = $cancelpolicy . 'If you cancel reservation ' . $CancellationPoliciesArray[$i]['OffsetUnitMultiplier'] . ' ' . $CancellationPoliciesArray[$i]['OffsetTimeUnit'] . 's ' . $CancellationPoliciesArray[$i]['OffsetDropTime'] . ' cost ' . $CancellationPoliciesArray[$i]['NmbrOfNights'] . ' nights ' . $CancellationPoliciesArray[$i]['Percent'] . '% . <br/>';
        }   
        $cancelpolicy_deadline = $CancellationPoliciesArray[($tam - 1)]['OffsetUnitMultiplier'] . ' ' . $CancellationPoliciesArray[($tam - 1)]['OffsetTimeUnit'] . 's';

        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $item['subtotal'] = $filter->filter(floatval($tot));
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        $item['cancelpolicy'] = $cancelpolicy;
        $item['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;
        $item['cancelpolicy_deadline'] = $cancelpolicy_deadline;
        
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mots where sid='" . $shid . "' and hid=" . $hid;
error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
$row_hotel->buffer();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel->buffer();
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $row_country->buffer();
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result instanceof ResultInterface && $result->isQueryResult()) {
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        foreach ($resultSet as $row) {
            $item = array();
            $item['url'] = "//world-wide-web-servers.com/static/hotels/" . $row->url;
            $item['description'] = $row->description;
            array_push($images, $item);
        }
    }
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
// error_log("\r\n" . print_r($responseContent, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
$hotel['checkin'] = $responseContent[$shid]['checkin'];
$hotel['fees'] = $responseContent[$shid]['fees'];
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown2;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['sales_taxes'] = $filter->filter($salestaxes);
$response['sales_taxesplain'] = number_format($salestaxes, 2, '.', '');
$response['taxes'] = $filter->filter($salestaxesfees);
$response['taxesplain'] = number_format($salestaxesfees, 2, '.', '');
$response['base_rate'] = $filter->filter($baserate);
$response['base_rateplain'] = number_format($baserate, 2, '.', '');
$response['occupancies'] = json_encode($occupancies);
$response['searchsettings'] = $searchsettings;
$response['ean'] = 1;
$response['eanbookhref'] = $href;
//
// Store Session
//
$sql = new Sql($db);
$sql = "delete from quote_session_hotel_multipolicies where session_id='" . $session_id . "' and sindex=$sindex";
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('quote_session_hotel_multipolicies');
$insert->values(array(
    'session_id' => $session_id,
    'sindex' => $sindex,
    'data' => base64_encode(serialize($response)),
    'searchsettings' => base64_encode(serialize($searchsettings))
), $insert::VALUES_MERGE);
try {
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['breakdown'] = $roombreakdown;
error_log("\r\nOTS Policies Multi - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>