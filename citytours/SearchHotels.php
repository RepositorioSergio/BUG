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
//error_log("\r\n COMECOU CITYTOURS TARDE \r\n", 3, "/srv/www/htdocs/error_log");
unset($tmp);
$sfilter = array();
$citytours = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id,city_xml45, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml45 = $row_settings["city_xml45"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml45 = "";
}
$sql = "select value from settings where name='enablecitytours' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_citytours = $affiliate_id;
} else {
    $affiliate_id_citytours = 0;
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
    $sql = "select value from settings where name='citytoursDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_citytours";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='citytoursID' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursID = $row_settings['value'];
}
$sql = "select value from settings where name='citytoursPassword' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='citytoursMarkup' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursMarkup = (double) $row_settings['value'];
} else {
    $citytoursMarkup = 0;
}
$sql = "select value from settings where name='citytoursServiceURL' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursServiceURL = $row_settings['value'];
}
//error_log("\r\n citytoursServiceURL: $citytoursServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='citytoursAgencyCode' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursAgencyCode = $row_settings['value'];
}
$sql = "select value from settings where name='citytoursSystem' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursSystem = $row_settings['value'];
}
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');
$raw = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
  <HotelSearch xmlns="http://tempuri.org/">
  <OTA_HotelSearchAvailRQ xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" Version="0" xmlns="http://www.opentravel.org/OTA/2003/05">
  <POS>
    <Source>
      <RequestorID Type="TD" ID="TESTID" />
      <TPA_Extensions>
        <TPA_Extensions xmlns="">
          <Provider>
            <System>' . $citytoursSystem . '</System>
            <Userid>' . $citytoursID . '</Userid>
            <Password>' . $citytoursPassword . '</Password>
            <AgencyCode>' . $citytoursAgencyCode . '</AgencyCode>
          </Provider>
        </TPA_Extensions>
      </TPA_Extensions>
    </Source>
  </POS>
  <Criteria>
    <Criterion>
      <Address>
        <CityName>new york city</CityName>
        <StateProv>new york</StateProv>
        <CountryName>united states</CountryName>
      </Address>
    </Criterion>
  </Criteria>
  <AvailRequestSegments>
    <AvailRequestSegment>
      <StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '"/>
      <RoomStayCandidates>';
      for ($z=0; $z < count($selectedAdults); $z++) { 
        $raw = $raw . '<RoomStayCandidate Quantity="4">
        <GuestCounts>
          <GuestCount AgeQualifyingCode="10" Count="' . $selectedAdults[$z] . '"/>';
          if ($selectedChildren[$z] > 0) {
                $raw = $raw . '<GuestCount AgeQualifyingCode="8" Count="' . $selectedAdults[$z] . '"/>';
          }
        $raw = $raw . '</GuestCounts>
        </RoomStayCandidate>';
    }
$raw = $raw . '</RoomStayCandidates>
    </AvailRequestSegment>
  </AvailRequestSegments>
</OTA_HotelSearchAvailRQ>
</HotelSearch>
  </soap:Body>
</soap:Envelope>';

if ($citytoursServiceURL != "" and $citytoursID != "" and $citytoursPassword != "") {

    $headers = array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "SOAPAction: http://tempuri.org/HotelSearch",
        "Content-length: ".strlen($raw),
    );

    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $citytoursServiceURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw); // the SOAP request
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_citytours');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $citytoursServiceURL . $raw,
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
    //error_log("\r\n response: $response \r\n", 3, "/srv/www/htdocs/error_log");

    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $HotelSearchResponse = $Body->item(0)->getElementsByTagName("HotelSearchResponse");
    $OTA_HotelSearchAvailRS = $HotelSearchResponse->item(0)->getElementsByTagName("OTA_HotelSearchAvailRS");
    $AvailResponseSegment = $OTA_HotelSearchAvailRS->item(0)->getElementsByTagName("AvailResponseSegment");
    $BrokerCode = $AvailResponseSegment->item(0)->getAttribute("BrokerCode");
    //error_log("\r\n BrokerCode: $BrokerCode \r\n", 3, "/srv/www/htdocs/error_log");
    $HotelBlocks = $AvailResponseSegment->item(0)->getElementsByTagName("HotelBlocks");
    $node = $HotelBlocks->item(0)->getElementsByTagName("HotelBlock");
    for ($i = 0; $i < $node->length; $i++) {
        $BasicPropertyInfo = $node->item($i)->getElementsByTagName("BasicPropertyInfo");
        if ($BasicPropertyInfo->length > 0) {
            $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
            $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
            $HotelID = $BasicPropertyInfo->item(0)->getAttribute("HotelID");
            //error_log("\r\n HotelID: $HotelID \r\n", 3, "/srv/www/htdocs/error_log");
            $shid = $HotelID;
            $sfilter[] = " sid='$HotelID' ";
            $Address = $BasicPropertyInfo->item(0)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
                if ($AddressLine->length > 0) {
                    $AddressLine = $AddressLine->item(0)->nodeValue;
                } else {
                    $AddressLine = "";
                }
                $CityName = $Address->item(0)->getElementsByTagName("CityName");
                if ($CityName->length > 0) {
                    $CityName = $CityName->item(0)->nodeValue;
                } else {
                    $CityName = "";
                }
                //error_log("\r\n HotelID: $HotelID \r\n", 3, "/srv/www/htdocs/error_log");
                $PostalCode = $Address->item(0)->getElementsByTagName("PostalCode");
                if ($PostalCode->length > 0) {
                    $PostalCode = $PostalCode->item(0)->nodeValue;
                } else {
                    $PostalCode = "";
                }
                $StateProv = $Address->item(0)->getElementsByTagName("StateProv");
                if ($StateProv->length > 0) {
                    $StateProv = $StateProv->item(0)->nodeValue;
                } else {
                    $StateProv = "";
                }
                $CountryName = $Address->item(0)->getElementsByTagName("CountryName");
                if ($CountryName->length > 0) {
                    $Code = $CountryName->item(0)->getAttribute("Code");
                    $CountryName = $CountryName->item(0)->nodeValue;
                } else {
                    $CountryName = "";
                }
                //error_log("\r\n CountryName: $CountryName \r\n", 3, "/srv/www/htdocs/error_log");
            } else {
                $CityName = "";
            }

        }
        //RoomStays
        $RoomStays = $node->item($i)->getElementsByTagName("RoomStays");
        if ($RoomStays->length > 0) {
            $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
            if ($RoomStay->length > 0) {
                for ($iAux=0; $iAux < $RoomStay->length; $iAux++) { 
                    $Configuration = $RoomStay->item($iAux)->getAttribute("Configuration");
                    $AvailableIndicator = $RoomStay->item($iAux)->getAttribute("AvailableIndicator");
                    $AvailabilityID = $RoomStay->item($iAux)->getAttribute("AvailabilityID");
                    //error_log("\r\n AvailabilityID: $AvailabilityID \r\n", 3, "/srv/www/htdocs/error_log");
                    //RoomTypes
                    $RoomTypes = $RoomStay->item($iAux)->getElementsByTagName("RoomTypes");
                    $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $RoomID = $RoomType->item(0)->getAttribute("RoomID");
                        //error_log("\r\n RoomID: $RoomID \r\n", 3, "/srv/www/htdocs/error_log");
                        $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
                        $RoomType2 = $RoomType->item(0)->getAttribute("RoomType");
                        $NumberOfUnits = $RoomType->item(0)->getAttribute("NumberOfUnits");
                        $RoomDescription = $RoomType->item(0)->getElementsByTagName("RoomDescription");
                        if ($RoomDescription->length > 0) {
                            $RoomDescription = $RoomDescription->item(0)->nodeValue;
                        }else {
                            $RoomDescription = "";
                        }
                        //error_log("\r\n RoomDescription: $RoomDescription \r\n", 3, "/srv/www/htdocs/error_log");
                    }

                    //RatePlans
                    $RatePlans = $RoomStay->item($iAux)->getElementsByTagName("RatePlans");
                    $RatePlan = $RatePlans->item(0)->getElementsByTagName("RatePlan");
                    if ($RatePlan->length > 0) {
                        $ExistsMeals = $RatePlan->item(0)->getAttribute("ExistsMeals");
                        $ContractID = $RatePlan->item(0)->getAttribute("ContractID");
                        $RatePlanID = $RatePlan->item(0)->getAttribute("RatePlanID");
                        $DailyPrice = $RatePlan->item(0)->getAttribute("DailyPrice");
                        $Date = $RatePlan->item(0)->getAttribute("Date");
                        $RateResortFee = $RatePlan->item(0)->getAttribute("RateResortFee");
                        //error_log("\r\n RateResortFee: $RateResortFee \r\n", 3, "/srv/www/htdocs/error_log");
                    }

                    //RoomRates
                    $RoomRates = $RoomStay->item($iAux)->getElementsByTagName("RoomRates");
                    $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                    if ($RoomRate->length > 0) {
                        $NumberOfUnitsRoomRate = $RoomRate->item(0)->getAttribute("NumberOfUnits");
                        $RatePlanCode = $RoomRate->item(0)->getAttribute("RatePlanCode");
                        $BookingCode = $RoomRate->item(0)->getAttribute("BookingCode");
                        //error_log("\r\n BookingCode: $BookingCode \r\n", 3, "/srv/www/htdocs/error_log");
                        $Rates = $RoomRate->item(0)->getElementsByTagName("Rates");
                        $Rate = $Rates->item(0)->getElementsByTagName("Rate");
                        if ($Rate->length > 0) {
                            $ExpireDate = $Rate->item(0)->getAttribute("ExpireDate");
                            $EffectiveDate = $Rate->item(0)->getAttribute("EffectiveDate");

                            $Total = $Rate->item(0)->getElementsByTagName("Total");
                            if ($Total->length > 0) {
                                $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                                $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                                $AmountBeforeTax = $Total->item(0)->getAttribute("AmountBeforeTax");
                            } else {
                                $ExpireDate = "";
                                $ExpireDate = "";
                                $ExpireDate = "";
                            }
                            //error_log("\r\n AmountAfterTax: $AmountAfterTax \r\n", 3, "/srv/www/htdocs/error_log");

                        } else {
                            $ExpireDate = "";
                            $EffectiveDate = "";
                        }

                    }else {
                        $NumberOfUnits = "";
                        $RatePlanID = "";
                        $RatePlanID = "";
                    }

                    //TimeSpan
                    $TimeSpan = $RoomStay->item($iAux)->getElementsByTagName("TimeSpan");
                    if ($TimeSpan->length > 0) {
                        $IgnorarCutOff = $TimeSpan->item(0)->getAttribute("IgnorarCutOff");
                        $End = $TimeSpan->item(0)->getAttribute("End");
                        $Start = $TimeSpan->item(0)->getAttribute("Start");
                        //error_log("\r\n Start: $Start \r\n", 3, "/srv/www/htdocs/error_log");
                    } else {
                        $NumberOfUnits = "";
                        $RatePlanID = "";
                        $RatePlanID = "";
                    }
                    
                    //CancelPenalties
                    $CancelPenalties = $RoomStay->item($iAux)->getElementsByTagName("CancelPenalties");
                    $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                    if ($CancelPenalty->length > 0) {
                        $NonRefundable = $CancelPenalty->item(0)->getAttribute("NonRefundable");
                        $Deadline = $CancelPenalty->item(0)->getElementsByTagName("Deadline");
                        if ($Deadline->length > 0) {
                            $OffsetTimeUnit = $Deadline->item(0)->getAttribute("OffsetTimeUnit");
                            $AbsoluteDeadline = $Deadline->item(0)->getAttribute("AbsoluteDeadline");
                        } else {
                            $OffsetTimeUnit = "";
                            $AbsoluteDeadline = "";
                        }
                        $AmountPercent = $CancelPenalty->item(0)->getElementsByTagName("AmountPercent");
                        if ($AmountPercent->length > 0) {
                            $NmbrOfNights = $AmountPercent->item(0)->getAttribute("NmbrOfNights");
                        } else {
                            $NmbrOfNights = "";
                        }
                        $PenaltyDescription = $CancelPenalty->item(0)->getElementsByTagName("PenaltyDescription");
                        if ($PenaltyDescription->length > 0) {
                            $PenaltyDescription = $PenaltyDescription->item(0)->nodeValue;
                        } else {
                            $PenaltyDescription = "";
                        }
                    } else {
                        $NonRefundable = "";
                    }
                    //error_log("\r\n PenaltyDescription: $PenaltyDescription \r\n", 3, "/srv/www/htdocs/error_log");
                    //Messages
                    $Message2 = "";
                    $Messages = $RoomStay->item($iAux)->getElementsByTagName("Messages");
                    if ($Messages->length > 0) {
                        $Message = $Messages->item(0)->getElementsByTagName("Message");
                        if ($Message->length > 0) {
                            for ($iAux2=0; $iAux2 < $Message->length; $iAux2++) { 
                                $TypeMessage = $Message->item($iAux2)->getAttribute("TypeMessage");
                                $Message2 = $Message->item($iAux2)->nodeValue;
                            }
                        } else {
                            $Message2 = "";
                        }
                    }
                     
                    //error_log("\r\n ANTES ROOM \r\n", 3, "/srv/www/htdocs/error_log");
                    for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                        if (is_array($tmp[$shid]['details'][$zRooms])) {
                            $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                        } else {
                            $baseCounterDetails = 0;
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $HotelID;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $HotelCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomType2;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-45";
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $AmountBeforeTax;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['price'] = $AmountAfterTax;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $CurrencyCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $RoomDescription;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomID'] = $RoomID;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomType'] = $RoomType2;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $RoomTypeCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = $NonRefundable;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = $AmountAfterTax;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($RoomType2);
                        // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['boardtype'] = $meal_type;
                        // $t = $meal_type;
                        $pricebreakdown = array();
                        $pricebreakdownCount = 0;
                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                            $amount = $AmountAfterTax / $noOfNights;
                            if ($citytoursMarkup != 0) {
                                $amount = $amount + (($amount * $citytoursMarkup) / 100);
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
                            if ($citytoursMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                        
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $CurrencyCode;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $AbsoluteDeadline . " " . $OffsetTimeUnit;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $PenaltyDescription;
                    }
                }   
            }
            $citytours = true;
        }
    }
    //error_log("\r\n" . print_r($tmp, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
    if ($citytours == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $sql = "select hid, sid from xmlhotels_mcitytours where " . $sfilter;
            $statement2 = $db->createStatement($sql);
            $statement2->prepare();
            $result2 = $statement2->execute();
            $result2->buffer();
            //error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
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
            $supplier = 45;
            // Store Session
            $sql = new Sql($db);
            //error_log("\r\n$query\r\n", 3, "/srv/www/htdocs/error_log");
            $delete = $sql->delete();
            $delete->from('quote_session_citytours');
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
            $insert->into('quote_session_citytours');
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
?>