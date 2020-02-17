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
$palace = false;
// error_log("\r\nStart Palace\r\n", 3, "/srv/www/htdocs/error_log");
$hotelcodes = array();
$totalHotelsPalace = 0;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select hotelcode from palace_destinations where city_id=" . $destination;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        array_push($hotelcodes, $row['hotelcode']);
        $totalHotelsPalace ++;
    }
}
if ($totalHotelsPalace > 0) {
    // error_log("\r\nHotels:\r\n" . print_r($hotelcodes, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
    $sql = "select value from settings where name='enablepalaceresorts' and affiliate_id=$affiliate_id" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $affiliate_id_palace = $affiliate_id;
    } else {
        $affiliate_id_palace = 0;
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
        $sql = "select value from settings where name='palaceresortsDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_palace";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='palaceresortslogin' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortslogin = $row_settings['value'];
    }
    $sql = "select value from settings where name='palaceresortsCancellationPolicy' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortsCancellationPolicy = (int) $row_settings['value'];
    } else {
        $palaceresortsCancellationPolicy = 15;
    }
    $sql = "select value from settings where name='palaceresortspassword' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortspassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='palaceresortsMarkup' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortsMarkup = (double) $row_settings['value'];
    } else {
        $palaceresortsMarkup = 0;
    }
    $sql = "select value from settings where name='palaceresortswebserviceurl' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortswebserviceurl = $row_settings['value'];
    }
    $sql = "select value from settings where name='palaceresortsAgencyCode' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortsAgencyCode = $row_settings['value'];
    }
    $sql = "select value from settings where name='palaceresortsSecurityCode' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortsSecurityCode = $row_settings['value'];
    }
    if ($palaceresortswebserviceurl != "" and $palaceresortslogin != "" and $palaceresortspassword) {
        $arrival_date = strftime("%Y-%m-%d", $from);
        $departure_date = strftime("%Y-%m-%d", $to);
        $nCPalace = 0;
        $channelsParallelPalace = array();
        $channelsParallelPalaceRoom = array();
        $channelsParallelPalaceRoomDescription = array();
        $channelsParallelPalaceBedDescription = array();
        $channelsParallelPalaceRoomType = array();
        $channelsParallelPalaceBedType = array();
        $multiParallelPalace = curl_multi_init();
        for ($wHotels = 0; $wHotels < $totalHotelsPalace; $wHotels ++) {
            // error_log("\r\nHotel:" . $hotelcodes[$wHotels] . "\r\n", 3, "/srv/www/htdocs/error_log");
            $sql = "select description, roomtype, bed from palace_roomtypes where hotelcode='" . $hotelcodes[$wHotels] . "'";
            // error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $result = $statement->execute();
            $result->buffer();
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet = new ResultSet();
                $resultSet->initialize($result);
                foreach ($resultSet as $row) {
                    $sql = "select maxpax, allowchildren, maxchild, maxadults from palace_rooms where hotelcode='" . $hotelcodes[$wHotels] . "' and roomtype='" . $row['roomtype'] . "'";
                    // error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
                    $statement2 = $db->createStatement($sql);
                    $statement2->prepare();
                    $row_palace_rooms = $statement2->execute();
                    $row_palace_rooms->buffer();
                    $valid = true;
                    if ($row_palace_rooms->valid()) {
                        $row_palace_rooms = $row_palace_rooms->current();
                        $max_pax = $row_palace_rooms['maxpax'];
                        $max_adults = $row_palace_rooms['maxadults'];
                        $max_child = $row_palace_rooms['maxchild'];
                        $allowchildren = $row_palace_rooms['allowchildren'];
                    } else {
                        $valid = false;
                    }
                    for ($r = 0; $r < $rooms; $r ++) {
                        if (($selectedAdults[$r] + $selectedChildren[$r]) > $max_pax) {
                            $valid = false;
                        }
                        if ($selectedAdults[$r] > $max_adults) {
                            $valid = false;
                        }
                        if ($selectedChildren[$r] > $max_child) {
                            $valid = false;
                        }
                        if ($selectedChildren[$r] > 0) {
                            if ($allowchildren == "NO") {
                                $valid = false;
                            }
                        }
                        if ($valid == true) {
                            $raw = '<?xml version="1.0" encoding="UTF-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://localhost/pr_xmlschemas/hotel/01-03-2006/availability.xsd" xmlns:ns2="http://localhost/pr_xmlschemas/hotel/01-03-2006/availabilityRequest.xsd" xmlns:ns3="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd" xmlns:ns4="http://localhost/xmlschemas/enterpriseservice/16-07-2009/"><SOAP-ENV:Body><ns4:GetAvailability><ns2:availabilityRequest><ns2:data><ns1:hotel>' . $hotelcodes[$wHotels] . '</ns1:hotel><ns1:room_type>' . $row['roomtype'] . '</ns1:room_type><ns1:bed_type>' . $row['bed'] . '</ns1:bed_type><ns1:arrival_date>' . $arrival_date . '</ns1:arrival_date><ns1:departure_date>' . $departure_date . '</ns1:departure_date>
          <ns1:adultos>' . $selectedAdults[$r] . '</ns1:adultos>
          <ns1:menores>' . $selectedChildren[$r] . '</ns1:menores>
          <ns1:baby>0</ns1:baby>
          <ns1:child>0</ns1:child>
          <ns1:kid>0</ns1:kid><ns1:rate_plan></ns1:rate_plan><ns1:group_code></ns1:group_code><ns1:promotion_code></ns1:promotion_code><ns1:idioma></ns1:idioma><ns1:agency_cd>' . $palaceresortsAgencyCode . '</ns1:agency_cd></ns2:data><ns2:Tag></ns2:Tag><ns2:AuthInfo><ns3:Recnum>0</ns3:Recnum><ns3:Ent_User>' . $palaceresortslogin . '</ns3:Ent_User><ns3:Ent_Pass>' . $palaceresortspassword . '</ns3:Ent_Pass><ns3:Ent_Term>' . $palaceresortsSecurityCode . '</ns3:Ent_Term></ns2:AuthInfo></ns2:availabilityRequest></ns4:GetAvailability></SOAP-ENV:Body></SOAP-ENV:Envelope>';
                            // if ($agent_id == 701 and $hotelcodes[$wHotels] == "BP") {
                            // error_log("\r\nParallel Palace Request: $raw\r\n", 3, "/srv/www/htdocs/error_log");
                            // }
                            $headers = array(
                                "Content-type: text/xml",
                                "Cache-Control: no-cache",
                                "Pragma: no-cache",
                                "Host: api.palaceresorts.com",
                                "Content-length: " . strlen($raw)
                            );
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
                            curl_setopt($ch, CURLOPT_URL, $palaceresortswebserviceurl);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
                            curl_setopt($ch, CURLOPT_VERBOSE, false);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_multi_add_handle($multiParallelPalace, $ch);
                            $channelsParallelPalace[$nCPalace] = $ch;
                            $channelsParallelPalaceRoom[$nCPalace] = $r;
                            $channelsParallelPalaceRoomType[$nCPalace] = $row['roomtype'];
                            $channelsParallelPalaceBedType[$nCPalace] = $row['bed'];
                            $channelsParallelPalaceRoomDescription[$nCPalace] = $row['description'];
                            if ($row['bed'] == 'K') {
                                $channelsParallelPalaceBedDescription[$nCPalace] = $translator->translate("1 King Bed");
                            } elseif ($row['bed'] == 'D') {
                                $channelsParallelPalaceBedDescription[$nCPalace] = $translator->translate("2 Double Beds");
                            } else {
                                $channelsParallelPalaceBedDescription[$nCPalace] = "";
                            }
                            $nCPalace ++;
                        }
                    }
                }
            }
        }
        $activePalace = null;
        do {
            $mrc = curl_multi_exec($multiParallelPalace, $activePalace);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        while ($activePalace && $mrc == CURLM_OK) {
            if (curl_multi_select($multiParallelPalace) == - 1) {
                continue;
            }
            do {
                $mrc = curl_multi_exec($multiParallelPalace, $activePalace);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            if ($mrc != CURLM_OK) {
                error_log("\r\nCurl Multi Exec Error:" . curl_multi_strerror($mrc) . "\r\n", 3, "/srv/www/htdocs/error_log");
            }
        }
        foreach ($channelsParallelPalace as $kchannel => $channel) {
            $response = curl_multi_getcontent($channel);
            $room = $channelsParallelPalaceRoomDescription[$kchannel];
            $bed = $channelsParallelPalaceBedDescription[$kchannel];
            $roomtype = $channelsParallelPalaceRoomType[$kchannel];
            $bedtype = $channelsParallelPalaceBedType[$kchannel];
            $zRooms = $channelsParallelPalaceRoom[$kchannel];
            curl_multi_remove_handle($multiParallelPalace, $channel);
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('log_palace');
                $insert->values(array(
                    'datetime_created' => time(),
                    'filename' => 'SearchHotels.php',
                    'errorline' => $zRooms,
                    'errormessage' => $raw,
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
            if ($response != "") {
                $inputDoc = new DOMDocument();
                $inputDoc->loadXML($response);
                $Envelope = $inputDoc->getElementsByTagName("Envelope");
                if ($Envelope->length > 0) {
                    $Body = $Envelope->item(0)->getElementsByTagName("Body");
                    if ($Body->length > 0) {
                        $GetAvailabilityResponse = $Body->item(0)->getElementsByTagName("GetAvailabilityResponse");
                        if ($GetAvailabilityResponse->length > 0) {
                            $roomAvailabilityResponse = $GetAvailabilityResponse->item(0)->getElementsByTagName("roomAvailabilityResponse");
                            if ($roomAvailabilityResponse->length > 0) {
                                $Hotel = $roomAvailabilityResponse->item(0)->getElementsByTagName('Hotel');
                                if ($Hotel->length > 0) {
                                    $Hotel = $Hotel->item(0)->nodeValue;
                                } else {
                                    $Hotel = "";
                                }
                                $shid = $Hotel;
                                $sfilter[] = " sid='$Hotel' ";
                                // if ($agent_id == 701 and $Hotel == "BP") {
                                // error_log("\r\nParallel Return Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
                                // }
                                $TotalAmount = $roomAvailabilityResponse->item(0)->getElementsByTagName('TotalAmount');
                                if ($TotalAmount->length > 0) {
                                    $TotalAmount = $TotalAmount->item(0)->nodeValue;
                                } else {
                                    $TotalAmount = "";
                                }
                                $nettotal = $TotalAmount;
                                $Moneda = $roomAvailabilityResponse->item(0)->getElementsByTagName('Moneda');
                                if ($Moneda->length > 0) {
                                    $Moneda = $Moneda->item(0)->nodeValue;
                                } else {
                                    $Moneda = "";
                                }
                                $TipoCambio = $roomAvailabilityResponse->item(0)->getElementsByTagName('TipoCambio');
                                if ($TipoCambio->length > 0) {
                                    $TipoCambio = $TipoCambio->item(0)->nodeValue;
                                } else {
                                    $TipoCambio = "";
                                }
                                $Tarifa1raNoche = $roomAvailabilityResponse->item(0)->getElementsByTagName('Tarifa1raNoche');
                                if ($Tarifa1raNoche->length > 0) {
                                    $Tarifa1raNoche = $Tarifa1raNoche->item(0)->nodeValue;
                                } else {
                                    $Tarifa1raNoche = "";
                                }
                                $RateCode = $roomAvailabilityResponse->item(0)->getElementsByTagName('RateCode');
                                if ($RateCode->length > 0) {
                                    $RateCode = $RateCode->item(0)->nodeValue;
                                } else {
                                    $RateCode = "";
                                }
                                $DescripcionTarifa = $roomAvailabilityResponse->item(0)->getElementsByTagName('DescripcionTarifa');
                                if ($DescripcionTarifa->length > 0) {
                                    $DescripcionTarifa = $DescripcionTarifa->item(0)->nodeValue;
                                } else {
                                    $DescripcionTarifa = "";
                                }
                                $Data = $roomAvailabilityResponse->item(0)->getElementsByTagName('Data');
                                if ($Data->length > 0) {
                                    $Availability = $Data->item(0)->getElementsByTagName('Availability');
                                    if ($Availability->length > 0) {
                                        $dayAvailable = $Availability->item(0)->getElementsByTagName('dayAvailable');
                                        if ($dayAvailable->length > 0) {
                                            for ($i = 0; $i < $dayAvailable->length; $i ++) {
                                                $Day = $dayAvailable->item($i)->getElementsByTagName('Day');
                                                if ($Day->length > 0) {
                                                    $Day = $Day->item(0)->nodeValue;
                                                } else {
                                                    $Day = "";
                                                }
                                                $Available = $dayAvailable->item($i)->getElementsByTagName('Available');
                                                if ($Available->length > 0) {
                                                    $Available = $Available->item(0)->nodeValue;
                                                } else {
                                                    $Available = "";
                                                }
                                                $Rate = $dayAvailable->item($i)->getElementsByTagName('Rate');
                                                if ($Rate->length > 0) {
                                                    $Rate = $Rate->item(0)->nodeValue;
                                                } else {
                                                    $Rate = "";
                                                }
                                                $RateCode = $dayAvailable->item($i)->getElementsByTagName('RateCode');
                                                if ($RateCode->length > 0) {
                                                    $RateCode = $RateCode->item(0)->nodeValue;
                                                } else {
                                                    $RateCode = "";
                                                }
                                                $RateCodeDescription = $dayAvailable->item($i)->getElementsByTagName('RateCodeDescription');
                                                if ($Day->length > 0) {
                                                    $RateCodeDescription = $RateCodeDescription->item(0)->nodeValue;
                                                } else {
                                                    $RateCodeDescription = "";
                                                }
                                                $Currency = $dayAvailable->item($i)->getElementsByTagName('Currency');
                                                if ($Currency->length > 0) {
                                                    $Currency = $Currency->item(0)->nodeValue;
                                                } else {
                                                    $Currency = "";
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($bed != "") {
                                    $room .= " - " . $bed;
                                }
                                if ($palaceresortsMarkup != 0) {
                                    $TotalAmount = $TotalAmount + (($TotalAmount * $palaceresortsMarkup) / 100);
                                }
                                // Geo target markup
                                if ($internalmarkup != 0) {
                                    $TotalAmount = $TotalAmount + (($TotalAmount * $internalmarkup) / 100);
                                }
                                // Agent markup
                                if ($agent_markup != 0) {
                                    $TotalAmount = $TotalAmount + (($TotalAmount * $agent_markup) / 100);
                                }
                                // Fallback Markup
                                if ($palaceresortsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                    $TotalAmount = $TotalAmount + (($TotalAmount * $HotelsMarkupFallback) / 100);
                                }
                                // Agent discount
                                if ($agent_discount != 0) {
                                    $TotalAmount = $TotalAmount - (($TotalAmount * $agent_discount) / 100);
                                }
                                if ($Currency != "") {
                                    if ($scurrency != "" and $Currency != $scurrency) {
                                        $TotalAmount = $CurrencyConverter->convert($TotalAmount, $Currency, $scurrency);
                                    }
                                }
                                if (is_array($tmp[$shid])) {
                                    $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                } else {
                                    $baseCounterDetails = 0;
                                }
                                if (mktime(0, 0, 0, date("m"), date("d") + $palaceresortsCancellationPolicy, date("y")) >= $from) {
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = true;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = time();
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $translator->translate("Non Refundable");
                                } else {
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = mktime(0, 0, 0, date("m", $from), date("d", $from) - $palaceresortsCancellationPolicy, date("y", $from));
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $palaceresortsCancellationPolicy . " " . $translator->translate("day(s) prior to arrival") . " - " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $palaceresortsCancellationPolicy, date("y", $from)));
                                }
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-5";
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $room;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TotalAmount;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $nettotal;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate("All Inclusive");
                                //
                                // Hotel Specific
                                //
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['bedtype'] = $bedtype;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomtype'] = $roomtype;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nighttarif'] = $Tarifa1raNoche;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ratecode'] = $RateCode;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['descripciontarifa'] = $DescripcionTarifa;
                                //
                                // EOF
                                //
                                $pricebreakdown = array();
                                $pricebreakdownCount = 0;
                                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                    $amount = $TotalAmount / $noOfNights;
                                    $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                                    $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                    $pricebreakdownCount = $pricebreakdownCount + 1;
                                }
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $Moneda;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $Moneda;
                                // }
                                // Sort Prices
                                // for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                                usort($tmp[$shid]['details'][$zRooms], function ($a, $b) {
                                    return ($a["total"] < $b["total"]) ? - 1 : 1;
                                });
                                // }
                                $palace = true;
                            }
                        }
                    }
                }
            }
        }
        if ($palace == true) {
            $sfilter = implode(' or ', $sfilter);
            try {
                $sql = "select hid, sid from xmlhotels_mpalace where " . $sfilter;
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
                $supplier = 5;
                try {
                    $sql = new Sql($db);
                    $delete = $sql->delete();
                    $delete->from('quote_session_palace');
                    $delete->where(array(
                        'session_id' => $session_id
                    ));
                    $statement = $sql->prepareStatementForSqlObject($delete);
                    $results = $statement->execute();
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('quote_session_palace');
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
}
?>