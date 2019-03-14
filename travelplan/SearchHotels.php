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
$travelplan = false;
$sql = "select city_xml50, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml50 = $row_settings["city_xml50"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml50 = 0;
}
if ($city_xml50 != "") {
    $city_xml50 = explode(":", $city_xml50);
    $x50_0 = $city_xml50[0];
    $x50_1 = $city_xml50[1];
    $x50_2 = $city_xml50[2];
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
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
if ((int) $residency > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $residency;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $residenceMarket = $row_settings["iso_code_2"];
    } else {
        $residenceMarket = "";
    }
} else {
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $residenceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='TravelPlanuser' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanuser = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanpassword' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='TravelPlanMarkup' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanMarkup = (double) $row_settings['value'];
} else {
    $TravelPlanMarkup = 0;
}
$sql = "select value from settings where name='TravelPlanserviceURL' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanserviceURL = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSystem' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSystem = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSalesChannel' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSalesChannel = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanlanguage' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanlanguage = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanConnectionString' and affiliate_id=$affiliate_id_travelplan";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanConnectionString = $row_settings['value'];
}

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');
$xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns0="http://www.opentravel.org/OTA/2003/05"><soapenv:Header/><soapenv:Body><ns0:OTA_HotelAvailRQ Version="1"><ns0:AvailRequestSegments><ns0:AvailRequestSegment><ns0:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '"/>';
if ($residenceMarket != "" or $sourceMarket != "") {
    $xml = $xml . '<ns0:Profiles>';
    $RPH = 1;
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        for ($z = 0; $z < $selectedAdults[$r]; $z ++) {
            $xml = $xml . '<ns0:ProfileInfo><ns0:Profile RPH="' . $RPH . '"><ns0:Customer>';
            if ($residenceMarket != "") {
                $xml = $xml . '<ns0:Address><ns0:CountryName Code="' . $residenceMarket . '"/></ns0:Address>';
            }
            if ($sourceMarket != "") {
                $xml = $xml . '<ns0:CitizenCountryName Code="' . $sourceMarket . '"/>';
            }
            $xml = $xml . '</ns0:Customer></ns0:Profile></ns0:ProfileInfo>';
            $RPH = $RPH + 1;
        }
        if ($selectedChildren[$r] > 0) {
            for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                $xml = $xml . '<ns0:ProfileInfo><ns0:Profile RPH="' . $RPH . '"><ns0:Customer>';
                if ($residenceMarket != "") {
                    $xml = $xml . '<ns0:Address><ns0:CountryName Code="' . $residenceMarket . '"/></ns0:Address>';
                }
                if ($sourceMarket != "") {
                    $xml = $xml . '<ns0:CitizenCountryName Code="' . $sourceMarket . '"/>';
                }
                $xml = $xml . '</ns0:Customer></ns0:Profile></ns0:ProfileInfo>';
                $RPH = $RPH + 1;
            }
        }
    }
    $xml = $xml . '</ns0:Profiles>';
}
$xml = $xml . '<ns0:RoomStayCandidates>';
$RPH = 1;
for ($r = 0; $r < count($selectedAdults); $r ++) {
    $xml = $xml . '<ns0:RoomStayCandidate><ns0:GuestCounts>';
    for ($z = 0; $z < $selectedAdults[$r]; $z ++) {
        $xml = $xml . '<ns0:GuestCount Count="1" Age="30" ResGuestRPH="' . $RPH . '" />';
        $RPH = $RPH + 1;
    }
    if ($selectedChildren[$r] > 0) {
        for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
            $xml = $xml . '<ns0:GuestCount Count="1" Age="' . $selectedChildrenAges[$r][$z] . '" ResGuestRPH="' . $RPH . '" />';
            $RPH = $RPH + 1;
        }
    }
    $xml = $xml . '</ns0:GuestCounts></ns0:RoomStayCandidate>';
}
$xml = $xml . '</ns0:RoomStayCandidates><ns0:TPA_Extensions><ns0:Providers><ns0:Provider Provider="GSI"><ns0:Credentials><ns0:Credential CredentialCode="' . $TravelPlanuser . '" CredentialName="AccountCode"/><ns0:Credential CredentialCode="' . $TravelPlanpassword . '" CredentialName="Password"/><ns0:Credential CredentialCode="' . $TravelPlanSystem . '" CredentialName="System"/><ns0:Credential CredentialCode="' . $TravelPlanSalesChannel . '" CredentialName="SalesChannel"/><ns0:Credential CredentialCode="' . $TravelPlanlanguage . '" CredentialName="Language"/><ns0:Credential CredentialCode="' . $TravelPlanConnectionString . '" CredentialName="ConnectionString"/></ns0:Credentials><ns0:ProviderAreas><ns0:Area TypeCode="Country" AreaCode="' . $x50_0 . '"/><ns0:Area TypeCode="Province" AreaCode="' . $x50_1 . '"/>';
if ($x50_2 != "") {
    $xml = $xml . '<ns0:Area TypeCode="Town" AreaCode="' . $x50_2 . '"/>';
}
$xml = $xml . '</ns0:ProviderAreas></ns0:Provider></ns0:Providers><ns0:ProviderTokens><ns0:Token TokenName="ResponseMode" TokenCode="4"/></ns0:ProviderTokens></ns0:TPA_Extensions></ns0:AvailRequestSegment></ns0:AvailRequestSegments></ns0:OTA_HotelAvailRQ></soapenv:Body></soapenv:Envelope>';
if ($TravelPlanserviceURL != "" and $TravelPlanuser != "" and $TravelPlanpassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $TravelPlanserviceURL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept-Encoding: gzip, deflate",
        "Accept: application/xml",
        "Content-type: application/xml",
        "Content-length: " . strlen($xml)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // $response = curl_exec($ch);
    include "/srv/www/htdocs/ages.xml/src/App/Action/Travelplan/debug.php";
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    // if ($response === false) {
    // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    // } else {
    // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    // }
    // error_log("\r\n RESPONSE2: $response \r\n", 3, "/srv/www/htdocs/error_log");
    curl_close($ch);
    // Descomentar para ver o resultado do provider
    // Nao esquecer de alterar o session id para testar por causa de cache
    // echo $response;
    // die();
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_travelplan');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $TravelPlanserviceURL . $xml,
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
    // echo $response;
    // die();
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $hotelsElement = $inputDoc->getElementsByTagName('RoomStay');
    for ($r = 0; $r < $hotelsElement->length; $r ++) {
        $RoomTypes = $hotelsElement->item($r)->getElementsByTagName('RoomTypes');
        $RoomType = $RoomTypes->item(0)->getElementsByTagName('RoomType');
        $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
        $RoomDescription = $RoomType->item(0)->getElementsByTagName('RoomDescription');
        $Name = $RoomDescription->item(0)->getAttribute("Name");
        
        $RatePlans = $hotelsElement->item($r)->getElementsByTagName('RatePlans');
        $RatePlan = $RatePlans->item(0)->getElementsByTagName('RatePlan');
        $RatePlanCode = $RatePlan->item(0)->getAttribute("RatePlanCode");
        $RatePlanName = $RatePlan->item(0)->getAttribute("RatePlanName");
        $MealsIncluded = $RatePlan->item(0)->getElementsByTagName('MealsIncluded');
        $MealPlanCodes = $MealsIncluded->item(0)->getAttribute("MealPlanCodes");
        // ROOMRATES
        $RoomRates = $hotelsElement->item($r)->getElementsByTagName('RoomRates');
        $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
        $Rates = $RoomRate->item(0)->getElementsByTagName('Rates');
        $Rate = $Rates->item(0)->getElementsByTagName('Rate');
        $CancelPolicies = $Rate->item(0)->getElementsByTagName('CancelPolicies');
        $CancelPenalty = $CancelPolicies->item(0)->getElementsByTagName('CancelPenalty');
        $NonRefundable = $CancelPenalty->item(0)->getAttribute("NonRefundable");
        $Total = $Rate->item(0)->getElementsByTagName('Total');
        $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
        $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
        $TPA_Extensions = $Rate->item(0)->getElementsByTagName('TPA_Extensions');
        $ProviderTokens = $TPA_Extensions->item(0)->getElementsByTagName('ProviderTokens');
        $Token = $ProviderTokens->item(0)->getElementsByTagName('Token');
        $TokenName = $Token->item(0)->getAttribute("TokenName");
        $TokenCode = $Token->item(0)->getAttribute("TokenCode");
        $RoomToken = $TPA_Extensions->item(0)->getElementsByTagName('RoomToken');
        $Token = $RoomToken->item(0)->getAttribute("Token");
        
        $RoomRateDescription = $RoomRate->item(0)->getElementsByTagName('RoomRateDescription');
        for ($ii = 0; $ii < $RoomRateDescription->length; $ii ++) {
            $NameRRD .= $RoomRateDescription->item($ii)->getAttribute("Name") . "-";
        }
        $AdultCount = 0;
        $ChildCount = 0;
        $Ages = "";
        $AgesCount = 0;
        $GuestCounts = $RoomRate->item(0)->getElementsByTagName("GuestCounts");
        if ($GuestCounts->length > 0) {
            $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
            for ($zGuestCount = 0; $zGuestCount < $GuestCount->length; $zGuestCount ++) {
                $Age = $GuestCount->item($zGuestCount)->getAttribute("Age");
                $Count = $GuestCount->item($zGuestCount)->getAttribute("Count");
                if ($Age == 30) {
                    $AdultCount = $AdultCount + $Count;
                } else {
                    $ChildCount = $ChildCount + 1;
                    $Ages[$AgesCount] = $Age;
                    $AgesCount = $AgesCount + 1;
                }
            }
        } else {
            $ChildCount = 0;
            $AdultCount = 0;
        }
        
        $RoomTypeCode = $RoomRate->item(0)->getAttribute("RoomTypeCode");
        $RatePlanCode = $RoomRate->item(0)->getAttribute("RatePlanCode");
        $InvBlockCode = $RoomRate->item(0)->getAttribute("InvBlockCode");
        $AvailabilityStatus = $RoomRate->item(0)->getAttribute("AvailabilityStatus");
        
        // TIMESPAN
        $TimeSpan = $hotelsElement->item($r)->getElementsByTagName('TimeSpan');
        $Start = $TimeSpan->item(0)->getAttribute("Start");
        $End = $TimeSpan->item(0)->getAttribute("End");
        
        $BasicPropertyInfo = $hotelsElement->item($r)->getElementsByTagName('BasicPropertyInfo');
        $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
        $shid = $HotelCode;
        $sfilter[] = " sid='$shid' ";
        $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
        
        // $TPA_Extensions = $hotelsElement->item($r)->getElementsByTagName('TPA_Extensions');
        // $Address = $TPA_Extensions->item(0)->getElementsByTagName('Address');
        // if ($Address->length > 0) {
        // $Address = $Address->item(0)->nodeValue;
        // } else {
        // $Address = "";
        // }
        // $Attributes = $TPA_Extensions->item(0)->getElementsByTagName('Attributes');
        // if ($Attributes->length > 0) {
        // $Attributes = $Attributes->item(0)->nodeValue;
        // } else {
        // $Attributes = "";
        // }
        // $HotelInfo = $TPA_Extensions->item(0)->getElementsByTagName('HotelInfo');
        // $CategoryCode = $HotelInfo->item(0)->getElementsByTagName('CategoryCode');
        // $CodeCC = $CategoryCode->item(0)->getAttribute("Code");
        // error_log("\r\n CodeCC: $CodeCC \r\n", 3, "/srv/www/htdocs/error_log");
        // $CategoryUngroupedCode = $HotelInfo->item(0)->getElementsByTagName('CategoryUngroupedCode');
        // $CodeUC = $CategoryUngroupedCode->item(0)->getAttribute("Code");
        // $CategoryName = $HotelInfo->item(0)->getElementsByTagName('CategoryName');
        // $NameCN = $CategoryName->item(0)->getAttribute("Name");
        // $IdH = $HotelInfo->item(0)->getElementsByTagName('Id');
        // $IDHC= $IdH->item(0)->getAttribute("ID");
        // $NameH = $HotelInfo->item(0)->getElementsByTagName('Name');
        // $NameHN = $NameH->item(0)->getAttribute("Name");
        // $TypeH = $HotelInfo->item(0)->getElementsByTagName('Type');
        // $TypeHH = $TypeH->item(0)->getAttribute("Type");
        // error_log("\r\n TypeHH: $TypeHH \r\n", 3, "/srv/www/htdocs/error_log");
        // $ProviderTokens = $TPA_Extensions->item(0)->getElementsByTagName('ProviderTokens');
        // $ProviderID = $TPA_Extensions->item(0)->getElementsByTagName('ProviderID');
        // $Provider = $ProviderID->item(0)->getAttribute("Provider");
        // error_log("\r\n Provider: $Provider \r\n", 3, "/srv/www/htdocs/error_log");
        for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
            // if ($selectedAdults[$zRooms] == $stdAdults) {
            // Chidlren ??
            // if ($selectedChildren[$zRooms] == $children) {
            if (is_array($tmp[$shid])) {
                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
            } else {
                $baseCounterDetails = 0;
            }
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-50";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $Name;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $RoomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $Name;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $AdultCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $ChildCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $AmountAfterTax;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $AmountAfterTax;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['NonRefundable'] = $NonRefundable;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($MealPlanCodes);
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                $amount = $noOfNights * $AmountAfterTax;
                if ($TravelPlanMarkup != 0) {
                    $amount = $amount + (($amount * $TravelPlanMarkup) / 100);
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
                if ($TravelPlanMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
            // policyDescription nao existe
            // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $policyDescription;
            $travelplan = true;
        }
    }
}
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($travelplan == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mglobalia where " . $sfilter;
        error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 50;
        error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_travelplan');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_travelplan');
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