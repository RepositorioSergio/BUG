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
error_log("\r\n COMECOU PALACE \r\n", 3, "/srv/www/htdocs/error_log");
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

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');

// if ($city_xml23 != "") {
// $url = "https://api.palaceresorts.com/EnterpriseServiceInterface/ServiceInterface.asmx?wsdl";
$raw = '<?xml version="1.0" encoding="UTF-8"?>
    <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://localhost/pr_xmlschemas/hotel/01-03-2006/availability.xsd" xmlns:ns2="http://localhost/pr_xmlschemas/hotel/01-03-2006/availabilityRequest.xsd" xmlns:ns3="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd" xmlns:ns4="http://localhost/xmlschemas/enterpriseservice/16-07-2009/">
      <SOAP-ENV:Body>
        <ns4:GetAvailability>
          <ns2:availabilityRequest>
            <ns2:data>
              <ns1:hotel>MPG</ns1:hotel>
              <ns1:room_type>RV</ns1:room_type>
              <ns1:bed_type></ns1:bed_type>
              <ns1:arrival_date>2019-09-23</ns1:arrival_date>
              <ns1:departure_date>2019-09-28</ns1:departure_date>
              <ns1:adultos>2</ns1:adultos>
              <ns1:menores>0</ns1:menores>
              <ns1:baby>0</ns1:baby>
              <ns1:child>0</ns1:child>
              <ns1:kid>0</ns1:kid>
              <ns1:rate_plan></ns1:rate_plan>
              <ns1:group_code></ns1:group_code>
              <ns1:promotion_code></ns1:promotion_code>
              <ns1:idioma></ns1:idioma>
              <ns1:agency_cd>CTM-PERU</ns1:agency_cd>
            </ns2:data>
            <ns2:Tag></ns2:Tag>
            <ns2:AuthInfo>
              <ns3:Recnum>0</ns3:Recnum>
              <ns3:Ent_User>CTM-PERU</ns3:Ent_User>
              <ns3:Ent_Pass>x4Mg82k9WS</ns3:Ent_Pass>
              <ns3:Ent_Term>CTM-PERU</ns3:Ent_Term>
            </ns2:AuthInfo>
      </ns2:availabilityRequest>
    </ns4:GetAvailability>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

// error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
if ($palaceresortswebserviceurl != "" and $palaceresortslogin != "" and $palaceresortspassword) {
    
    $headers = array(
        "Content-type: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "Host: api.palaceresorts.com",
        "Content-length: " . strlen($raw)
    ); // SOAPAction: your op URL
    
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $palaceresortswebserviceurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_USERPWD, $soapUser . ":" . $soapPassword); // username and password - declared at the top of the doc
    // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw); // the SOAP request
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    // if ($response === false) {
    //     error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    // } else {
    //     error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    // }
    // error_log("\r\n END POINT: $palaceServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
    //error_log("\r\n RESPONSE: $response \r\n", 3, "/srv/www/htdocs/error_log");
    curl_close($ch);
    $endTime = microtime();
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_palace');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $palaceresortswebserviceurl . $raw,
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
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    
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
            $TotalAmount = $roomAvailabilityResponse->item(0)->getElementsByTagName('TotalAmount');
            if ($TotalAmount->length > 0) {
                $TotalAmount = $TotalAmount->item(0)->nodeValue;
            } else {
                $TotalAmount = "";
            }
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
            
            
            for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                if (is_array($tmp[$shid])) {
                    $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                } else {
                    $baseCounterDetails = 0;
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $Hotel;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $Hotel;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-5";
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RateCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $RateCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $ROOM_NAME;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateCode'] = $RateCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TotalAmount;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $Tarifa1raNoche;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($DescripcionTarifa);
                $pricebreakdown = array();
                $pricebreakdownCount = 0;
                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                    $amount = $TotalAmount / $noOfNights;
                    if ($palaceMarkup != 0) {
                        $amount = $amount + (($amount * $palaceMarkup) / 100);
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
                    if ($palaceMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $Moneda;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $Moneda;
            }
            $palace = true;
        }
    }
}

// error_log("\r\n TMP2:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($palace == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mpalace where " . $sfilter;
        //error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        //error_log("\r\n PASSOU 1 $result2 \r\n", 3, "/srv/www/htdocs/error_log");
        $result2->buffer();
        
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            //error_log("\r\n PASSOU 2 \r\n", 3, "/srv/www/htdocs/error_log");
            foreach ($resultSet2 as $row2) {
                // $sidfilter[] = "id=" . $row2->hid;
                $sidfilter[] = $row2->hid;
                //error_log("\r\n PASSOU 3 \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 5;
        //error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
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
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
?>