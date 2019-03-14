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
$markinternational = false;
$sql = "select name, country_id, zone_id,city_xml17, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml17 = $row_settings["city_xml17"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml17 = "";
}
$passengers = 0;
// VAX XML allows only 8 passengers to be requested per AvailabilityRequest. This can be divided between as many as 4 rooms. Currently I see you allowing the user to send us AvailabilityRequest for more than 8 total passengers.
for ($r = 0; $r < count($selectedAdults); $r ++) {
    $passengers = $passengers + $selectedAdults[$r];
    $passengers = $passengers + $selectedChildren[$r];
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
    $sql = "select value from settings where name='MarkInternationalDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}

$sql = "select value from settings where name='MarkInternationalLogin' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalLogin = $row_settings['value'];
}
// error_log("\r\n MarkInternationalLogin $MarkInternationalLogin \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='MarkInternationalPassword' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalPassword = base64_decode($row_settings['value']);
}

$sql = "select value from settings where name='MarkInternationalMarkup' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalMarkup = (double) $row_settings['value'];
} else {
    $MarkInternationalMarkup = 0;
}
// error_log("\r\n MarkInternationalMarkup $MarkInternationalMarkup \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='MarkInternationalURL' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalURL = $row_settings['value'];
}
// error_log("\r\n MarkInternationalURL $MarkInternationalURL \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='MarkInternationalVendor' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalVendor = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalAgencyNumber' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalAgencyNumber = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalWebServices' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalWebServices = $row_settings['value'];
}
// error_log("\r\n MarkInternationalWebServices $MarkInternationalWebServices \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='MarkInternationalDynamicPackaging' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalDynamicPackaging = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalb2cMarkup' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalb2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='MarkInternationalContact' and affiliate_id=$affiliate_id_markinternational";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $MarkInternationalContact = $row_settings['value'];
}

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');
if ($city_xml17 != "") {
    $raw = 'requestXml=<VAXXML xmlns="http://www.triseptsolutions.com/Availability/Request/10.0"><Header AgencyNumber="' . $MarkInternationalAgencyNumber . '" Contact="' . $MarkInternationalContact . '" Login="' . $MarkInternationalLogin . '" Password="' . $MarkInternationalWebServices . '" Vendor="' . $MarkInternationalVendor . '" DynamicPackageId="' . $MarkInternationalDynamicPackaging . '" Culture="en-us"  SessionId="" ShowCart="Y" /><Request Type="New" Seq="1" AbsoluteDestinationCode="' . $city_xml17 . '" AbsoluteOriginCode="' . $city_xml17 . '"><TravelerAvail>';
    $Seq = 1;
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        for ($z = 0; $z < $selectedAdults[$r]; $z ++) {
            $raw = $raw . '<PassengerTypeQuantity Seq="' . $Seq . '" Type="ADT" Age="40" />';
            $Seq = $Seq + 1;
        }
        for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
            $raw = $raw . '<PassengerTypeQuantity Seq="' . $Seq . '" Type="CHD" Age="' . $selectedChildrenAges[$r][$z] . '" />';
            $Seq = $Seq + 1;
        }
    }
    $raw = $raw . '</TravelerAvail>';
    // Hotel Availability Request
    $Seq = 1;
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        $raw = $raw . '<HotelAvailRQ Start="1" Length="999" SortType="Price"><TravelerAvailSet>';
        for ($z = 0; $z < $selectedAdults[$r]; $z ++) {
            $raw = $raw . '<PassengerSeq Seq="' . $Seq . '"/>';
            $Seq = $Seq + 1;
        }
        for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
            $raw = $raw . '<PassengerSeq Seq="' . $Seq . '"/>';
            $Seq = $Seq + 1;
        }
        $raw = $raw . '</TravelerAvailSet><OriginDestinationInformation Type="Checkin" LocationCode="' . $city_xml17 . '" DateTime="' . strftime("%Y-%m-%d", $from) . 'T' . strftime("%H:%M:%S") . '"/><OriginDestinationInformation Type="Checkout" LocationCode="' . $city_xml17 . '" DateTime="' . strftime("%Y-%m-%d", $to) . 'T' . strftime("%H:%M:%S") . '"/></HotelAvailRQ>';
    }
    $raw = $raw . '</Request></VAXXML>';
    // error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
    if ($MarkInternationalURL != "" and $MarkInternationalLogin != "" and $MarkInternationalPassword != "") {
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $MarkInternationalURL . 'AvailabilityRequest');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded"
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $response = curl_exec($ch);
        include "/srv/www/htdocs/ages.xml/src/App/Action/MarkInternational/debug.php";
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        // if ($response === false) {
        // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
        // } else {
        // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
        // }
        // error_log("\r\n END POINT: $TravcoServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\n RESPONSE: $response \r\n", 3, "/srv/www/htdocs/error_log");
        curl_close($ch);
        
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_markinternational');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $MarkInternationalURL . $raw,
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
        // error_log("\r\n ANTES PARSE \r\n", 3, "/srv/www/htdocs/error_log");
        // echo $response;
        // die();
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        if ($inputDoc != NULL) {
            $node = $inputDoc->getElementsByTagName("string");
            if ($node->length > 0) {
                $RequestInfo = $node->item(0)->getElementsByTagName("VAXXML");
                if ($RequestInfo->length > 0) {
                    $Header = $RequestInfo->item(0)->getElementsByTagName("Header");
                    if ($Header->length > 0) {
                        $SessionId = $Header->item(0)->getAttribute("SessionId");
                        // error_log("\r\n SessionId $SessionId \r\n", 3, "/srv/www/htdocs/error_log");
                    }
                    
                    // REQSPONSE
                    $Response = $RequestInfo->item(0)->getElementsByTagName("Response");
                    if ($Response->length > 0) {
                        
                        // TRAVAILAVAL
                        $PassengerTypeQuantityArray = array();
                        $TravelerAvail = $Response->item(0)->getElementsByTagName("TravelerAvail");
                        if ($TravelerAvail->length > 0) {
                            $PassengerTypeQuantity = $TravelerAvail->item(0)->getElementsByTagName("PassengerTypeQuantity");
                            if ($PassengerTypeQuantity->length > 0) {
                                for ($z = 0; $z < $PassengerTypeQuantity->length; $z ++) {
                                    $Age = $PassengerTypeQuantity->item($z)->getAttribute("Age");
                                    if ($Age != "") {
                                        $PassengerTypeQuantityArray[$Age] = $PassengerTypeQuantity->item($x)->nodeValue;
                                    }
                                    $Type = $PassengerTypeQuantity->item($z)->getAttribute("Type");
                                    if ($Key != "") {
                                        $PassengerTypeQuantityArray[$Type] = $PassengerTypeQuantity->item($x)->nodeValue;
                                    }
                                    $Seq = $PassengerTypeQuantity->item($z)->getAttribute("Seq");
                                    if ($Seq != "") {
                                        $PassengerTypeQuantityArray[$Seq] = $PassengerTypeQuantity->item($x)->nodeValue;
                                    }
                                }
                            }
                        }
                        
                        // TravelerAvailSets
                        $TravelerAvailSetArray = array();
                        $TravelerAvailSets = $Response->item(0)->getElementsByTagName("TravelerAvailSets");
                        if ($TravelerAvailSets->length > 0) {
                            $TravelerAvailSet = $TravelerAvailSets->item(0)->getElementsByTagName("TravelerAvailSet");
                            if ($TravelerAvailSet->length > 0) {
                                for ($z = 0; $z < $TravelerAvailSet->length; $z ++) {
                                    $Seq = $TravelerAvailSet->item($z)->getAttribute("Seq");
                                    if ($Seq != "") {
                                        $TravelerAvailSetArray[$Seq] = $TravelerAvailSet->item($x)->nodeValue;
                                    }
                                    $PassengerTypeQuantityRef = $TravelerAvailSet->item($z)->getElementsByTagName("PassengerTypeQuantityRef");
                                    for ($y = 0; $y < $PassengerTypeQuantityRef->length; $y ++) {
                                        $Seq = $PassengerTypeQuantityRef->item($y)->getAttribute("Seq");
                                        if ($Seq != "") {
                                            $TravelerAvailSetArray[$Seq] = $PassengerTypeQuantityRef->item($y)->nodeValue;
                                        }
                                        $Lead = $PassengerTypeQuantityRef->item($y)->getAttribute("Lead");
                                        if ($Lead != "") {
                                            $TravelerAvailSetArray[$Lead] = $PassengerTypeQuantityRef->item($y)->nodeValue;
                                        }
                                    }
                                }
                            }
                        }
                        
                        // CityLookUp
                        $CityLookUp = $Response->item(0)->getElementsByTagName("CityLookUp");
                        $City = $CityLookUp->item(0)->getElementsByTagName("City");
                        $State = $City->item(0)->getAttribute("State");
                        $Name = $City->item(0)->getAttribute("Name");
                        $LocationCode = $City->item(0)->getAttribute("LocationCode");
                        // error_log("\r\n LocationCode $LocationCode \r\n", 3, "/srv/www/htdocs/error_log");
                        // DESCRIPTIONS
                        $Descriptions = array();
                        $DescriptionsTag = $Response->item(0)->getElementsByTagName("Descriptions");
                        if ($DescriptionsTag->length > 0) {
                            if ($DescriptionsTag->length > 0) {
                                $Description = $DescriptionsTag->item(0)->getElementsByTagName("Description");
                                for ($x = 0; $x < $Description->length; $x ++) {
                                    $Key = $Description->item($x)->getAttribute("Key");
                                    if ($Key != "") {
                                        $Descriptions[$Key] = $Description->item($x)->nodeValue;
                                    }
                                    $text = $Description->item($x)->getAttribute("text");
                                    if ($text != "") {
                                        $Descriptions[$text] = $Description->item($x)->nodeValue;
                                    }
                                }
                            }
                        }
                        
                        // Availability
                        $Availability = $Response->item(0)->getElementsByTagName("Availability");
                        if ($Availability->length > 0) {
                            $Results = $Availability->item(0)->getElementsByTagName("Results");
                            if ($Results->length > 0) {
                                $Seq = $Results->item(0)->getAttribute("Seq");
                                $Air = $Results->item(0)->getAttribute("Air");
                                $Hotel = $Results->item(0)->getAttribute("Hotel");
                                $Feature = $Results->item(0)->getAttribute("Feature");
                                $Car = $Results->item(0)->getAttribute("Car");
                                $LowDate = $Results->item(0)->getAttribute("LowDate");
                                $HighDate = $Results->item(0)->getAttribute("HighDate");
                                // error_log("\r\n HighDate $HighDate \r\n", 3, "/srv/www/htdocs/error_log");
                                $HotelAvailRS = $Results->item(0)->getElementsByTagName("HotelAvailRS");
                                $Hotel = $HotelAvailRS->item(0)->getElementsByTagName("Hotel");
                                // if ($Hotel->length > 0) {
                                for ($x = 0; $x < $Hotel->length; $x ++) {
                                    $HotelCode = $Hotel->item($x)->getAttribute("HotelCode");
                                    $shid = $HotelCode;
                                    $sfilter[] = " sid='$HotelCode' ";
                                    // error_log("\r\n HotelCode $HotelCode \r\n", 3, "/srv/www/htdocs/error_log");
                                    $FareType = $Hotel->item($x)->getAttribute("FareType");
                                    $ItemId = $Hotel->item($x)->getAttribute("ItemId");
                                    $Name = $Hotel->item($x)->getAttribute("Name");
                                    $ChainCode = $Hotel->item($x)->getAttribute("ChainCode");
                                    $Priority = $Hotel->item($x)->getAttribute("Priority");
                                    $POIProximity = $Hotel->item($x)->getElementsByTagName("POIProximity");
                                    if ($POIProximity->length > 0) {
                                        $Distance = $POIProximity->item(0)->getAttribute("Distance");
                                        $Direction = $POIProximity->item(0)->getAttribute("Direction");
                                        $Units = $POIProximity->item(0)->getAttribute("Units");
                                        $POICode = $POIProximity->item(0)->getAttribute("POICode");
                                        $PointOfInterest = $POIProximity->item(0)->getAttribute("PointOfInterest");
                                    } else {
                                        $Distance = "";
                                        $Direction = "";
                                        $Units = "";
                                        $POICode = "";
                                        $PointOfInterest = "";
                                    }
                                    $Rating = $Hotel->item($x)->getElementsByTagName("Rating");
                                    if ($Rating->length > 0) {
                                        $Preferred = $Rating->item(0)->getAttribute("Preferred");
                                        $Value = $Rating->item(0)->getAttribute("Value");
                                    } else {
                                        $Preferred = "";
                                        $Value = "";
                                    }
                                    $Address = $Hotel->item($x)->getElementsByTagName("Address");
                                    if ($Address->length > 0) {
                                        $AddressLine1 = $Address->item(0)->getAttribute("AddressLine1");
                                        $AddressLine2 = $Address->item(0)->getAttribute("AddressLine2");
                                        $City = $Address->item(0)->getAttribute("City");
                                        $State = $Address->item(0)->getAttribute("State");
                                        $PostalCode = $Address->item(0)->getAttribute("PostalCode");
                                        $CountryCode = $Address->item(0)->getAttribute("CountryCode");
                                        $Description = $Address->item(0)->getAttribute("Description");
                                    } else {
                                        $AddressLine1 = "";
                                        $AddressLine2 = "";
                                        $City = "";
                                        $State = "";
                                        $PostalCode = "";
                                        $CountryCode = "";
                                        $Description = "";
                                    }
                                    $Telephone = $Hotel->item($x)->getElementsByTagName("Telephone");
                                    if ($Telephone->length > 0) {
                                        $Type = $Telephone->item(0)->getAttribute("Type");
                                        $Telephone = $Telephone->item(0)->nodeValue;
                                    } else {
                                        $Type = "";
                                        $Telephone = "";
                                    }
                                    
                                    $HotelRatePlan = $Hotel->item($x)->getElementsByTagName("HotelRatePlan");
                                    $baseCounterDetails = 0;
                                    if ($HotelRatePlan->length > 0) {
                                        for ($xy = 0; $xy < $HotelRatePlan->length; $xy ++) {
                                            $TravelerAvailSet = $HotelRatePlan->item($xy)->getAttribute("TravelerAvailSet");
                                            $RatePlanId = $HotelRatePlan->item($xy)->getAttribute("RatePlanId");
                                            $RoomCode = $HotelRatePlan->item($xy)->getAttribute("RoomCode");
                                            $PlanCode = $HotelRatePlan->item($xy)->getAttribute("PlanCode");
                                            $PlanDescription = $HotelRatePlan->item($xy)->getAttribute("PlanDescription");
                                            $RoomDescription = $HotelRatePlan->item($xy)->getAttribute("RoomDescription");
                                            $SalesDescriptionTmp = $HotelRatePlan->item($xy)->getElementsByTagName("SalesDescription");
                                            if ($SalesDescriptionTmp->length > 0) {
                                                for ($xY = 0; $xY < $SalesDescriptionTmp->length; $xY ++) {
                                                    if ($SalesDescriptionTmp->item($xY)->nodeValue != "") {
                                                        if ($SpecialName != "") {
                                                            $SpecialName = $SpecialName . "<br/>";
                                                        }
                                                        $SpecialName = $SpecialName . $Descriptions[$SalesDescriptionTmp->item($xY)->nodeValue];
                                                    }
                                                }
                                            }
                                            $PricingInfo = $HotelRatePlan->item($xy)->getElementsByTagName("PricingInfo");
                                            if ($PricingInfo->length > 0) {
                                                $Base = $PricingInfo->item(0)->getAttribute("Base");
                                                $Taxes = $PricingInfo->item(0)->getAttribute("Taxes");
                                                $Fees = $PricingInfo->item(0)->getAttribute("Fees");
                                                $Markups = $PricingInfo->item(0)->getAttribute("Markups");
                                                $Total = $PricingInfo->item(0)->getAttribute("Total");
                                                // error_log("\r\n Total $Total \r\n", 3, "/srv/www/htdocs/error_log");
                                                $Currency = $PricingInfo->item(0)->getAttribute("Currency");
                                                $Currency_Base = $Currency;
                                                $Price = $PricingInfo->item(0)->getElementsByTagName("Price");
                                                if ($Price->length > 0) {
                                                    for ($pr = 0; $pr < $Price->length; $pr ++) {
                                                        
                                                        $Price_Type = $Price->item($pr)->getAttribute("Type");
                                                        $Price_LowAge = $Price->item($pr)->getAttribute("LowAge");
                                                        $Price_HighAge = $Price->item($pr)->getAttribute("HighAge");
                                                        $Price_Base = $Price->item($pr)->getAttribute("Base");
                                                        $Price_Taxes = $Price->item($pr)->getAttribute("Taxes");
                                                        $Price_Fees = $Price->item($pr)->getAttribute("Fees");
                                                        $Price_Markups = $Price->item($pr)->getAttribute("Markups");
                                                        $Price_Total = $Price->item($pr)->getAttribute("Total");
                                                        // error_log("\r\n Price_Total $Price_Total \r\n", 3, "/srv/www/htdocs/error_log");
                                                        $Price_QuantityMinimum = $Price->item($pr)->getAttribute("QuantityMinimum");
                                                        $Price_QuantityMaximum = $Price->item($pr)->getAttribute("QuantityMaximum");
                                                        
                                                        // error_log("\r\n INCLUDESDINNER $INCLUDESDINNER \r\n", 3, "/srv/www/htdocs/error_log");
                                                        
                                                        for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                                                            if (is_array($tmp[$shid])) {
                                                                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                                            } else {
                                                                $baseCounterDetails = 0;
                                                            }
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $Name;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $HotelCode;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-17";
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomDescription;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $RoomCode;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $RoomDescription;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateCode'] = $RatePlanId;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['OCCUPANCY'] = $SessionId;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $Total;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $Base;
                                                            
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($RoomCode);
                                                            $pricebreakdown = array();
                                                            $pricebreakdownCount = 0;
                                                            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                                                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                                                $amount = $Total / $noOfNights;
                                                                if ($MarkInternationalMarkup != 0) {
                                                                    $amount = $amount + (($amount * $MarkInternationalMarkup) / 100);
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
                                                                if ($MarkInternationalMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currency;
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $markinternational = true;
    }
}

// error_log("\r\n TMP2:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($markinternational == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mmarkinternational where " . $sfilter;
        // error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 17;
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_markinternational');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_markinternational');
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