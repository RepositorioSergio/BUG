<?php
error_log("\r\nOlympia Europe - Search Hotels\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$olympiaeurope = false;
$sql = "select name, country_id, zone_id,city_xml75, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml75 = $row_settings["city_xml75"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml75 = "";
}
$affiliate_id = 0;
$sql = "select value from settings where name='enableolympiaeurope' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_olympia = $affiliate_id;
} else {
    $affiliate_id_olympia = 0;
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
    $sql = "select value from settings where name='olympiaeuropeDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='olympiaeuropelogin' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropelogin = $row_settings['value'];
}
$sql = "select value from settings where name='olympiaeuropepassword' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropepassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='olympiaeuropeContextDatabase' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropeContextDatabase = $row_settings['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelAvailRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropeOTAHotelAvailRQ = $row_settings['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTABookingListRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTABookingListRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelCancelRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelCancelRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelDescInfoRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelDescInfoRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelReadRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelReadRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelResRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelResRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeOTAHotelSearchRQ' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeOTAHotelSearchRQ = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeMarkup' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $olympiaeuropeMarkup = (double) $row_settings['value'];
} else {
    $olympiaeuropeMarkup = 0;
}
$sql = "select value from settings where name='olympiaeuropeb2cMarkup' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeb2cMarkup = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeaffiliates_id' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropebranches_id' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropebranches_id = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeParallelSearch' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeParallelSearch = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeSearchSortorder' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeCurrencyCode' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeCurrencyCode = $row['value'];
}
$sql = "select value from settings where name='olympiaeuropeTimeout' and affiliate_id=$affiliate_id_olympia";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $olympiaeuropeTimeout = (int) $row['value'];
}
$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/">
	<soap-env:Header>
		<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			<wsse:Username>' . $olympiaeuropelogin . '</wsse:Username>
			<wsse:Password>' . $olympiaeuropepassword . '</wsse:Password>
			<Context>' . $olympiaeuropeContextDatabase . '</Context>
		</wsse:Security>
	</soap-env:Header>
	<soap-env:Body>
		<OTA_HotelAvailRQ xmlns="http://parsec.es/hotelapi/OTA2014Compact" >
			<HotelSearch>
				<Currency Code="' . $olympiaeuropeCurrencyCode . '"/>
				<HotelLocation CityCode="' . $city_xml75 . '"/>
				<DateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '"/>
                <RoomCandidates>'; 
                for ($r=0; $r < count($selectedAdults); $r++) { 
                    $raw .= '<RoomCandidate RPH="' . ($r + 1) . '"><Guests>
                        <Guest AgeCode="A" Count="' . $selectedAdults[$r] . '" />';
                    if ($selectedChildren[$r] > 0) {
                        for ($z=0; $z < $selectedChildren[$r]; $z++) { 
                            $raw .= '<Guest AgeCode="C" Count="1" Age="' . $selectedChildrenAges[$r][$z] . '"';
                        }
                    }
                    $raw .= '</Guests></RoomCandidate>';
                }
	$raw .=	'</RoomCandidates>
			</HotelSearch>
		</OTA_HotelAvailRQ>
	</soap-env:Body>
</soap-env:Envelope>';
// error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
if ($olympiaeuropeOTAHotelAvailRQ != "" and $olympiaeuropelogin != "" and $olympiaeuropepassword != "") {
    $headers = array(
        'Content-Type: text/xml; charset=utf-8',
        'Accept: application/xml',
        'Content-Length: ' . strlen($raw)
    );
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $olympiaeuropeOTAHotelAvailRQ);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, $olympiaeuropeTimeout);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    error_log("\r\nResponse: $response \r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_olympiaeurope');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $olympiaeuropeOTAHotelAvailRQ . $raw,
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
    $OTA_HotelAvailRS = $inputDoc->getElementsByTagName('OTA_HotelAvailRS');
    $Hotelsb = $OTA_HotelAvailRS->item(0)->getElementsByTagName('Hotels');
    if ($Hotelsb->length > 0) {
        $DateRange = $Hotelsb->item(0)->getElementsByTagName('DateRange');
        if ($DateRange->length > 0) {
            $Start = $DateRange->item(0)->getAttribute('Start');
            $End = $DateRange->item(0)->getAttribute('End');
        }
        $RoomCandidates = $Hotelsb->item(0)->getElementsByTagName('RoomCandidates');
        if ($RoomCandidates->length > 0) {
            $RoomCandidate = $RoomCandidates->item(0)->getElementsByTagName('RoomCandidate');
            if ($RoomCandidate->length > 0) {
                $RPH = $RoomCandidate->item(0)->getAttribute('RPH');
                $Guests = $RoomCandidate->item(0)->getElementsByTagName('Guests');
                if ($Guests->length > 0) {
                    $Guest = $Guests->item(0)->getElementsByTagName('Guest');
                    if ($Guest->length > 0) {
                        $AgeCode = $Guest->item(0)->getAttribute('AgeCode');
                        $Count = $Guest->item(0)->getAttribute('Count');
                    }
                }
            }
        }
        $Hotelb = $Hotelsb->item(0)->getElementsByTagName('Hotel');
        if ($Hotelb->length > 0) {
            for ($i=0; $i < $Hotelb->length; $i++) { 
                $Info = $Hotelb->item($i)->getElementsByTagName('Info');
                if ($Info->length > 0) {
                    $HotelCode = $Info->item(0)->getAttribute('HotelCode');
                    $shid = $HotelCode;
                    $sfilter[] = " sid='$HotelCode' ";
                    $HotelName = $Info->item(0)->getAttribute('HotelName');
                    $HotelCityCode = $Info->item(0)->getAttribute('HotelCityCode');
                    $Rating = $Info->item(0)->getAttribute('Rating');
                    $MasterCode = $Info->item(0)->getAttribute('MasterCode');
                    $Recommended = $Info->item(0)->getAttribute('Recommended');
                    $HotelProvider = $Info->item(0)->getElementsByTagName('HotelProvider');
                    if ($HotelProvider->length > 0) {
                        $HotelProvider = $HotelProvider->item(0)->nodeValue;
                    } else {
                        $HotelProvider = "";
                    }
                    $HotelIdent = $Info->item(0)->getElementsByTagName('HotelIdent');
                    if ($HotelIdent->length > 0) {
                        $HotelIdent = $HotelIdent->item(0)->nodeValue;
                    } else {
                        $HotelIdent = "";
                    }
                }
                $BestPrice = $Hotelb->item($i)->getElementsByTagName('BestPrice');
                if ($BestPrice->length > 0) {
                    $Amount = $BestPrice->item(0)->getAttribute('Amount');
                    $Currency = $BestPrice->item(0)->getAttribute('Currency');
                }
                $Rooms = $Hotelb->item($i)->getElementsByTagName('Rooms');
                if ($Rooms->length > 0) {
                    $Room = $Rooms->item(0)->getElementsByTagName('Room');
                    if ($Room->length > 0) {
                        for ($iAux=0; $iAux < $Room->length; $iAux++) { 
                            $RPH = $Room->item($iAux)->getAttribute('RPH');
                            $Best = $Room->item($iAux)->getAttribute('Best');
                            $Status = $Room->item($iAux)->getAttribute('Status');
                            $RoomType = $Room->item($iAux)->getElementsByTagName('RoomType');
                            if ($RoomType->length > 0) {
                                $RoomTypeCode = $RoomType->item(0)->getAttribute('Code');
                                $RoomTypeName = $RoomType->item(0)->getAttribute('Name');
                            }
                            $RoomRates = $Room->item($iAux)->getElementsByTagName('RoomRates');
                            if ($RoomRates->length > 0) {
                                $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
                                if ($RoomRate->length > 0) {
                                    $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');
                                    $BookingCode = $RoomRate->item(0)->getAttribute('BookingCode');
                                    $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                                    if ($Total->length > 0) {
                                        $Amount = $Total->item(0)->getAttribute('Amount');
                                        $Commission = $Total->item(0)->getAttribute('Commission');
                                        $Currency = $Total->item(0)->getAttribute('Currency');
                                    }
                                }
                            }
                            $total = $Amount;
                            $nettotal = $total;
                            $zRooms = 0;
                            if (is_array($tmp[$shid])) {
                                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                            } else {
                                $baseCounterDetails = 0;
                            }
                            
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $HotelCode;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $RoomTypeCode;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-75";
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomTypeName;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $RoomTypeName;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $RoomTypeCode;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['BookingCode'] = $BookingCode;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $nettotal;
                            if ($olympiaeuropeMarkup != 0) {
                                $total = $total + (($total * $olympiaeuropeMarkup) / 100);
                            }
                            // Geo target markup
                            if ($internalmarkup != 0) {
                                $total = $total + (($total * $internalmarkup) / 100);
                            }
                            // Agent markup
                            if ($agent_markup != 0) {
                                $total = $total + (($total * $agent_markup) / 100);
                            }
                            // Fallback Markup
                            if ($olympiaeuropeMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                $total = $total + (($total * $HotelsMarkupFallback) / 100);
                            }
                            // Agent discount
                            if ($agent_discount != 0) {
                                $total = $total - (($total * $agent_discount) / 100);
                            }
                            if ($scurrency != "" and $currency != $scurrency) {
                                $total = $CurrencyConverter->convert($total, $currency, $scurrency);
                            }
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                            try {
                                $sql = "select mapped from board_mapping where description='" . addslashes($MealPlan) . "'";
                                $statement = $db->createStatement($sql);
                                $statement->prepare();
                                $row_board_mapping = $statement->execute();
                                $row_board_mapping->buffer();
                                if ($row_board_mapping->valid()) {
                                    $row_board_mapping = $row_board_mapping->current();
                                    $MealPlan = $row_board_mapping["mapped"];
                                }
                            } catch (\Exception $e) {
                                $logger = new Logger();
                                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                $logger->addWriter($writer);
                                $logger->info($e->getMessage());
                            }
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($MealPlan);
                            $pricebreakdown = array();
                            $pricebreakdownCount = 0;
                            $amount = $total / $noOfNights;
                            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                                $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                $pricebreakdownCount = $pricebreakdownCount + 1;
                            }
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $Currency;
                            //
                            // Special
                            //
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";

                            /* $procurar = "Non-Refundable";
                            if (strpos($PromotionName, $procurar) !== false) {
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = true;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking");
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
                            } else {
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy'] = "";
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                            } */
                            $count = $count + 1;
                        }
                    }
                }
                $olympiaeurope = true;
            }
        }
    }
}
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($olympiaeurope == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_molympiaeurope where " . $sfilter;
        error_log("\r\n SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
    error_log("\r\n PASSOU 1 \r\n", 3, "/srv/www/htdocs/error_log");
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 75;
        error_log("\r\n Query: $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_olympiaeurope');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_olympiaeurope');
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
error_log("\r\n End OLYMPIA\r\n", 3, "/srv/www/htdocs/error_log");
?>