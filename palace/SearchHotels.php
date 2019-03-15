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
error_log("\r\n city_xml23 $city_xml23 \r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n palaceresortslogin $palaceresortslogin \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='palaceresortspassword' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortspassword = base64_decode($row_settings['value']);
}
error_log("\r\n palaceresortspassword $palaceresortspassword \r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n palaceresortswebMarkup $palaceresortsMarkup \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='palaceresortswebserviceurl' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortswebserviceurl = $row_settings['value'];
}
error_log("\r\n palaceresortswebserviceurl $palaceresortswebserviceurl \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='palaceresortsAgencyCode' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortsAgencyCode = $row_settings['value'];
}
error_log("\r\n palaceresortsAgencyCode $palaceresortsAgencyCode \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='palaceresortsSecurityCode' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortsSecurityCode = $row_settings['value'];
}
error_log("\r\n palaceresortsecuritycode $palaceresortsSecurityCode \r\n", 3, "/srv/www/htdocs/error_log");

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');
error_log("\r\n ANTES IF\r\n", 3, "/srv/www/htdocs/error_log");

//if ($city_xml23 != "") {
    //$url = "https://api.palaceresorts.com/EnterpriseServiceInterface/ServiceInterface.asmx?wsdl";
    $raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:ns="http://localhost/xmlschemas/enterpriseservice/16-07-2009/" xmlns:ava="http://localhost/pr_xmlschemas/hotel/01-03-2006/availabilityRequest.xsd" xmlns:ava1="http://localhost/pr_xmlschemas/hotel/01-03-2006/availability.xsd" xmlns:aut="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd">
    <soap:Body>
        <ns:GetAvailability_AllHotels>
            <ava:availabilityRequest>
                <ava:data>
                <ava1:arrival_date>2019-05-20T00:00:00</ava1:arrival_date>
                <ava1:departure_date>2019-05-22T00:00:00</ava1:departure_date>
                <ava1:adultos>2</ava1:adultos>
                <ava1:menores>0</ava1:menores>
                <ava1:baby>0</ava1:baby>
                <ava1:child>0</ava1:child>
                <ava1:kid>0</ava1:kid>
                <ava1:idioma>EN</idioma>
                <ava1:agency_cd>' . $palaceresortsAgencyCode . '</ava1:agency_cd>
                </ava:data>
                <ava:AuthInfo>
                <aut:Recnum>0</aut:Recnum>
                <aut:Ent_User>' . $palaceresortslogin . '</aut:Ent_User>
                <aut:Ent_Pass>' . $palaceresortspassword . '</aut:Ent_Pass>
                <aut:Ent_Term>' . $palaceresortsSecurityCode . '</aut:Ent_Term>
                </ava:AuthInfo>
            </ava:availabilityRequest>
        </ns:GetAvailability_AllHotels>
    </soap:Body>
    </soap:Envelope>';

    //error_log("\r\n Request: $raw  \r\n", 3, "/srv/www/htdocs/error_log");
    if ($palaceresortswebserviceurl != "" and $palaceresortslogin != "" and $palaceresortspassword) {

        $headers = array(
            "Content-type: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Host: api.palaceresorts.com",
            "SOAPAction: http://localhost/xmlschemas/enterpriseservice/16-07-2009/GetAvailability_AllHotels",
            "Content-length: " . strlen($raw)
        ); // SOAPAction: your op URL
        

        $startTime = microtime();
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
        if ($response === false) {
        error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
        error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
        }
        //error_log("\r\n END POINT: $palaceServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
        error_log("\r\n RESPONSE: $response \r\n", 3, "/srv/www/htdocs/error_log");
        curl_close($ch);
        $endTime = microtime();
        die();
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
        error_log("\r\n TMP2:" . print_r($response, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        error_log("\r\n ANTES PARSE \r\n", 3, "/srv/www/htdocs/error_log");

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        if ($inputDoc != NULL) {

            $shid = $HOTEL_CODE;
            $sfilter[] = " sid='$HOTEL_CODE' ";


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
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $CURRENCY_CODE;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CURRENCY_CODE;
            }
        }
        $palace = true;
    }
        
//}

 //error_log("\r\n TMP2:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

 if ($palace == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mpalace where " . $sfilter;
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