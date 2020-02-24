<?php
$scurrency = strtoupper($currency);
unset($tmp);
unset($array);
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
use Zend\Http\Client;
use Zend\Http\Request;
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
$sql = "select value from settings where name='gtalogin' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtalogin = $row_settings["value"];
}
$sql = "select value from settings where name='gtaemail' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtaemail = $row_settings["value"];
}
$sql = "select value from settings where name='gtapassword' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtapassword = base64_decode($row_settings["value"]);
}
$sql = "select value from settings where name='gtacurrency' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtacurrency = $row_settings["value"];
}
$sql = "select value from settings where name='gtaEnableTripadvisorRatings' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtaEnableTripadvisorRatings = $row_settings["value"];
}
$sql = "select value from settings where name='gtaTripAdvisorPartnerKey' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtaTripAdvisorPartnerKey = $row_settings["value"];
}
$sql = "select value from settings where name='gtaTimeout' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtaTimeout = (int) $row_settings["value"];
} else {
    $gtaTimeout = 0;
}
$sql = "select value from settings where name='gtatesting' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtatesting = $row_settings["value"];
}
$sql = "select value from settings where name='gtamarkup' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtamarkup = $row_settings["value"];
}
$sql = "select value from settings where name='gtaInventory' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtaInventory = $row_settings["value"];
}
$sql = "select value from settings where name='gtasubmissionurl' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtasubmissionurl = $row_settings["value"];
}
$sql = "select value from settings where name='gtalogin' and affiliate_id=$affiliate_id_gta";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtalogin = $row_settings["value"];
}
$sql = "select city_xml11 from cities where id=" . $destination;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml11 = $row_settings["city_xml11"];
} else {
    $city_xml11 = "";
}
if ($nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . $nationality;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $countrytag = ' Country="' . $row_settings["iso_code_2"] . '"';
    } else {
        $countrytag = "";
    }
} else {
    $countrytag = "";
}
//
// https://interface.demo.gta-travel.com/tcubeukapi/RequestListenerServlet
// https://www.travelcube.com/tcubeukapi/RequestListenerServlet
// https://rbs.gta-travel.com/rbsusapi/RequestListenerServlet
//
if ($gtatesting == 1) {
    $gtasubmissionurl = "https://interface.demo.gta-travel.com/rbsusapi/RequestListenerServlet";
}
$languageGTA = substr($language, 0, 2);
if ($languageGTA == "") {
    $languageGTA = "en";
}
if ($city_xml11 != "") {
    $numberofcots = "";
    $nrextrabeds = 0;
    $maxAdults = 0;
    $maxChildren = 0;
    for ($r = 0; $r < count($selectedAdults); $r ++) {
        $maxAdults = $maxAdults + $selectedAdults[$r];
        $maxChildren = $maxChildren + $selectedChildren[$r];
    }
    $aux = $maxChildren + $maxAdults;
    if ($aux <= 9) {
        $xmlrequest = '<?xml version="1.0" encoding="UTF-8" ?><Request><Source><RequestorID Client="' . $gtalogin . '" EMailAddress="' . $gtaemail . '" Password="' . $gtapassword . '"/>';
        if ($gtacurrency != "") {
            $Currency = $gtacurrency;
            $xmlrequest .= '<RequestorPreferences Language="' . $languageGTA . '"  Currency="' . $gtacurrency . '"' . $countrytag . '>';
        } else {
            $xmlrequest .= '<RequestorPreferences Language="' . $languageGTA . '"' . $countrytag . '>';
        }
        $xmlrequest .= '<RequestMode>SYNCHRONOUS</RequestMode></RequestorPreferences></Source><RequestDetails><SearchHotelPricePaxRequest><ItemDestination DestinationType="city" DestinationCode="' . $city_xml11 . '"/>';
        if ($gtaInventory == true) {
            $xmlrequest .= '<ImmediateConfirmationOnly />';
        }
        $xmlrequest .= '<PeriodOfStay><CheckInDate>' . date(strftime("%Y-%m-%d", $from)) . '</CheckInDate><Duration>' . $nights . '</Duration></PeriodOfStay>';
        //
        // Location Code
        // Optional. Used to return all items within the specified location
        // code.
        // LocationCode
        //
        // Facility Codes
        // Optional. A list of <FacilityCode> elements specifying requested
        // facilities at the hotel, e.g. swimming pool, tennis courts etc.
        // <FacilityCodes>
        // <FacilityCode>*SO</FacilityCode>
        // <FacilityCode>*TE</FacilityCode>
        // </FacilityCodes>
        //
        // FacilityCodes
        //
        // Item Name
        // Optional. If specified all items containing the given characters will
        // be
        // returned.
        // Note: Item names are not case sensitive.
        // ItemName
        //
        // Item Code
        // Optional. If specified the details for the single code will be
        // returned.
        // ItemCode
        // Add rooms
        $xmlrequest .= '<IncludeRecommended /><IncludePriceBreakdown /><IncludeChargeConditions />';
        $numberofcotsString = '';
        $xmlrequest .= '<PaxRooms>';
        for ($r = 0; $r < count($selectedAdults); $r ++) {
            $cots = 0;
            if ($selectedChildren[$r] > 0) {
                if ($selectedChildrenAges[$r][$z] < 2) {
                    $cots = $cots + 1;
                }
            }
            $xmlrequest .= '<PaxRoom Adults="' . $selectedAdults[$r] . '" Cots="' . $cots . '" RoomIndex="' . ($r + 1) . '">';
            if ($selectedChildren[$r] > 0) {
                $xmlrequest .= '<ChildAges>';
                for ($zK = 0; $zK < $selectedChildren[$r]; $zK ++) {
                    if ($selectedChildrenAges[$r][$zK] > 1) {
                        $xmlrequest .= '<Age>' . $selectedChildrenAges[$r][$zK] . '</Age>';
                    }
                }
                $xmlrequest .= '</ChildAges>';
            }
            $xmlrequest .= '</PaxRoom>';
        }
        $xmlrequest .= '</PaxRooms>';
        if ((int) $stars > 0) {
            $xmlrequest .= '<StarRatingRange><Min>' . (int) $stars . '</Min><Max>5</Max></StarRatingRange>';
        }
        $xmlrequest .= '</SearchHotelPricePaxRequest></RequestDetails></Request>';
        $startTime = microtime();
        $client = new Client();
        if ($gtaTimeout == 0) {
            $gtaTimeout = 120;
        }
        $client->setOptions(array(
            'timeout' => $gtaTimeout
        ));
        $client->setHeaders(array(
            "Accept-Encoding" => "gzip, deflate",
            "User-Agent" => "curl/7.37.0",
            "Content-Encoding" => "UTF-8",
            "Content-Type" => "text/xml; charset=UTF-8"
        ));
        $client->setUri($gtasubmissionurl);
        $client->setRawBody($xmlrequest);
        $client->setMethod('POST');
        $response = $client->send();
        $validSubmit = true;
        if ($response->isSuccess()) {
            $xmlresult = $response->getBody();
        } else {
            $validSubmit = false;
            error_log("\r\nGTA URL: $gtasubmissionurl\r\n", 3, "/srv/www/htdocs/error_log");
            error_log("\r\nGTA:" . $response->getStatusCode() . "\r\n", 3, "/srv/www/htdocs/error_log");
            error_log("\r\nGTA:" . $response->getReasonPhrase() . "\r\n", 3, "/srv/www/htdocs/error_log");
            error_log("\r\nGTA Request: $xmlrequest \r\n", 3, "/srv/www/htdocs/error_log");
        }
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_gta');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $xmlrequest,
                'sqlcontext' => $xmlresult,
                'errcontext' => ''
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            $validSubmit = false;
        }
        if ($validSubmit == true) {
            if ($xmlresult != "") {
                $inputDoc = new DOMDocument();
                $inputDoc->loadXML($xmlresult);
                $responseElement = $inputDoc->documentElement;
                $xpath = new DOMXPath($inputDoc);
                $errorsElements = $xpath->query('Errors', $responseElement);
                if ($errorsElements->length > 0) {
                    // Process Errors
                    // Log error
                }
                $errorsElements = $xpath->query('ResponseDetails/SearchHotelPricePaxResponse/Errors', $responseElement);
                if ($errorsElements->length > 0) {
                    // Process Errors
                    // Log error
                } else {
                    // Process Response Data
                    $searchHotelPriceReponseElements = "";
                    $searchHotelPriceReponseElements = $xpath->query('ResponseDetails/SearchHotelPricePaxResponse', $responseElement);
                    foreach ($searchHotelPriceReponseElements as $searchHotelPriceReponseElement) {
                        $hotelElements = $xpath->query('HotelDetails/Hotel', $searchHotelPriceReponseElement);
                        foreach ($hotelElements as $hotelElement) {
                            // Process each Hotel
                            $recommended = $hotelElement->getAttribute("Recommended");
                            $special = false;
                            $xExtraCount = 0;
                            $cityExtra = $xpath->query('City', $hotelElement);
                            $cityExtra = $cityExtra->item(0)->getAttributeNode('Code');
                            $cityExtra = $cityExtra->value;
                            $item = $xpath->query('Item', $hotelElement);
                            $shidTmp = $item->item(0)->getAttributeNode('Code');
                            $item = $item->item(0)->textContent;
                            $shid = $shidTmp->value;
                            $EssentialInformation = "";
                            $hotelElementsExtra = $xpath->query('EssentialInformation/Information', $hotelElement);
                            $xEssentialInformation = 0;
                            foreach ($hotelElementsExtra as $hotelElementExtra) {
                                $TextAux = $xpath->query('Text', $hotelElementExtra);
                                $TextAux = $TextAux->item(0)->textContent;
                                if ($EssentialInformation != "") {
                                    $EssentialInformation .= " ~ ";
                                }
                                $EssentialInformation .= $TextAux;
                                $xEssentialInformation = $xEssentialInformation + 1;
                            }
                            $xRoomIndex = 0;
                            $PaxRoomSearchResults = $xpath->query('PaxRoomSearchResults/PaxRoom', $hotelElement);
                            foreach ($PaxRoomSearchResults as $PaxRoomSearchResult) {
                                $RoomIndex = $PaxRoomSearchResults->item($xRoomIndex)->getAttribute('RoomIndex');
                                $xRoomIndex = $xRoomIndex + 1;
                                $hotelElementsExtra = $xpath->query('RoomCategories/RoomCategory', $PaxRoomSearchResult);
                                $baseCounterDetails = 0;
                                $hotelSelected = 0;
                                $xRoomCategoryId = 0;
                                foreach ($hotelElementsExtra as $hotelElementExtra) {
                                    $hotelElementsExtraTmp = $xpath->query('EssentialInformation/Information', $hotelElementExtra);
                                    foreach ($hotelElementsExtraTmp as $hotelElementExtraAux) {
                                        $TextAux = $xpath->query('Text', $hotelElementExtraAux);
                                        $TextAux = $TextAux->item(0)->textContent;
                                        if ($EssentialInformation != "") {
                                            $EssentialInformation .= " ~ ";
                                        }
                                        $EssentialInformation .= $TextAux;
                                        $xEssentialInformation = $xEssentialInformation + 1;
                                    }
                                    $pricebreakdown = array();
                                    $pricebreakdownCount = 0;
                                    $offerid = $hotelElementsExtra->item($xRoomCategoryId)->getAttributeNode('Id');
                                    $offerid = $offerid->value;
                                    if ($xExtraCount == 0) {
                                        $itemPriceFrom = $xpath->query('ItemPrice', $hotelElementExtra);
                                        $Currency = $itemPriceFrom->item(0)->getAttributeNode('Currency');
                                        $base_currency = $Currency->value;
                                        $CommissionPercentage = $itemPriceFrom->item(0)->getAttribute('CommissionPercentage');
                                        $GrossWithoutDiscount = $itemPriceFrom->item(0)->getAttribute('GrossWithoutDiscount');
                                        $IncludedOfferDiscount = $itemPriceFrom->item(0)->getAttribute('IncludedOfferDiscount');
                                        if ($IncludedOfferDiscount != "") {
                                            if (is_numeric($IncludedOfferDiscount)) {
                                                if ($IncludedOfferDiscount > 0) {
                                                    if ($gtamarkup > 0) {
                                                        $IncludedOfferDiscount = $IncludedOfferDiscount + (($IncludedOfferDiscount * $gtamarkup) / 100);
                                                    }
                                                    if ($internalmarkup > 0) {
                                                        $IncludedOfferDiscount = $IncludedOfferDiscount + (($IncludedOfferDiscount * $internalmarkup) / 100);
                                                    }
                                                    if ($agent_markup > 0) {
                                                        $IncludedOfferDiscount = $IncludedOfferDiscount + (($IncludedOfferDiscount * $agent_markup) / 100);
                                                    }
                                                    // Fallback Markup
                                                    if ($gtamarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                                        $IncludedOfferDiscount = $IncludedOfferDiscount + (($IncludedOfferDiscount * $HotelsMarkupFallback) / 100);
                                                    }
                                                    if ($agent_discount > 0) {
                                                        $IncludedOfferDiscount = $IncludedOfferDiscount - (($IncludedOfferDiscount * $agent_discount) / 100);
                                                    }
                                                    if ($scurrency != "" and $base_currency != $scurrency) {
                                                        $IncludedOfferDiscount = $CurrencyConverter->convert($IncludedOfferDiscount, $base_currency, $scurrency);
                                                    }
                                                }
                                            }
                                        }
                                        $confirmationFrom = $xpath->query('Confirmation', $hotelElementExtra);
                                        $confirmationFrom = $confirmationFrom->item(0)->textContent;
                                        // Markup
                                        // if ($gtamarkup != "") {
                                        // if (is_numeric($gtamarkup)) {
                                        // if ($gtamarkup != 0) {
                                        // if (is_numeric($itemPriceFrom)) {
                                        // if ($itemPriceFrom != "") {
                                        // $itemPriceFrom = $itemPriceFrom + (($itemPriceFrom * $gtamarkup) / 100);
                                        // }
                                        // }
                                        // }
                                        // }
                                        // }
                                        // Geo target markup
                                        // if ($internalmarkup > 0) {
                                        // if (is_numeric($itemPriceFrom)) {
                                        // if ($itemPriceFrom != "") {
                                        // $itemPriceFrom = $itemPriceFrom + (($itemPriceFrom * $internalmarkup) / 100);
                                        // }
                                        // }
                                        // }
                                        // Agent markup
                                        // if ($agent_markup > 0) {
                                        // if (is_numeric($itemPriceFrom)) {
                                        // if ($itemPriceFrom != "") {
                                        // $itemPriceFrom = $itemPriceFrom + (($itemPriceFrom * $agent_markup) / 100);
                                        // }
                                        // }
                                        // }
                                        // Agent discount
                                        // if ($agent_discount > 0) {
                                        // if (is_numeric($itemPriceFrom)) {
                                        // if ($itemPriceFrom != "") {
                                        // $itemPriceFrom = $itemPriceFrom - (($itemPriceFrom * $agent_discount) / 100);
                                        // }
                                        // }
                                        // }
                                        // if ($currency_table_exists == 1) {
                                        // if ($CURRENCY != "") {
                                        // $itemPriceFrom = $CurrencyConverter->convert($itemPriceFrom, $base_currency, $CURRENCY);
                                        // $Currency = $CURRENCY;
                                        // }
                                        // }
                                        $xExtraCount = 1;
                                    }
                                    // Specials
                                    $offer = $xpath->query('Offer', $hotelElementExtra);
                                    if ($offer->length > 0) {
                                        $offercode = $offer->item(0)->getAttribute('Code');
                                        $offer = $offer->item(0)->nodeValue;
                                    } else {
                                        $offercode = "";
                                        $offer = "";
                                    }
                                    $itemPrice = $xpath->query('ItemPrice', $hotelElementExtra);
                                    $Currency = $itemPrice->item(0)->getAttributeNode('Currency');
                                    $base_currency = $Currency->value;
                                    $Currency = $Currency->value;
                                    $itemPrice = $itemPrice->item(0)->textContent;
                                    $itemPriceFromNet = $itemPrice;
                                    $confirmation = $xpath->query('Confirmation', $hotelElementExtra);
                                    $confirmationCode = $confirmation->item(0)->getAttributeNode('Code');
                                    $confirmationCode = $confirmationCode->value;
                                    $confirmation = $confirmation->item(0)->textContent;
                                    $roomdescription = $xpath->query('Description', $hotelElementExtra);
                                    $roomdescription = $roomdescription->item(0)->textContent;
                                    $SharingBedding = $xpath->query('SharingBedding', $hotelElementExtra);
                                    if ($SharingBedding->length > 0) {
                                        $SharingBedding = $SharingBedding->item(0)->textContent;
                                    } else {
                                        $SharingBedding = "";
                                    }
                                    if ($SharingBedding == "") {
                                        $SharingBedding = 0;
                                    } else {
                                        if ($SharingBedding == "false") {
                                            $SharingBedding = 0;
                                        } else {
                                            $SharingBedding = 1;
                                        }
                                    }
                                    if ($SharingBedding == 1) {
                                        $roomdescription = $roomdescription . " - " . $translator->translate("Child sharing existing bedding");
                                    }
                                    $meal = $xpath->query('Meals/Basis', $hotelElementExtra);
                                    $mealcode = $meal->item(0)->getAttributeNode('Code');
                                    $mealcode = $mealcode->value;
                                    $meal = $meal->item(0)->textContent;
                                    $cancellationpolicy = '';
                                    $ChargeConditions = $xpath->query('ChargeConditions', $hotelElementExtra);
                                    if ($ChargeConditions->length > 0) {
                                        $ChargeCondition = $ChargeConditions->item(0)->getElementsByTagName("ChargeCondition");
                                        for ($xChargeCondition = 0; $xChargeCondition < $ChargeCondition->length; $xChargeCondition ++) {
                                            if ($cancellationpolicy != "") {
                                                $cancellationpolicy .= "<br/><br/>";
                                            }
                                            $cancellationpolicy .= ucwords($ChargeCondition->item($xChargeCondition)->getAttribute('Type'));
                                            if ($ChargeCondition->item($xChargeCondition)->getAttribute('Allowable') == 'false') {
                                                $cancellationpolicy .= "<br/>" . $translator->translate("Not allowable") . "<br/><br/>";
                                            } else {
                                                $Condition = $ChargeCondition->item($xChargeCondition)->getElementsByTagName("Condition");
                                                for ($xCondition = 0; $xCondition < $Condition->length; $xCondition ++) {
                                                    if ($Condition->item($xCondition)->getAttribute('Charge') == 'false') {
                                                        $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $from))) . " " . $translator->translate("or earlier") . " " . $translator->translate("you will not be charged");
                                                    } else {
                                                        if ($Condition->item($xCondition)->getAttribute('FromDay') == 0) {
                                                            if ($Condition->item($xCondition)->getAttribute('ToDay') == "") {
                                                                if ($Condition->item($xCondition)->getAttribute('Allowable') == "false") {
                                                                    $cancellationpolicy .= "<br/>" . $translator->translate("Not allowable");
                                                                } else {
                                                                    $cancellationpolicy .= "<br/>" . $translator->translate("Effective today you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                                }
                                                            } else {
                                                                $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $from))) . " " . $translator->translate("onwards") . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                            }
                                                        } else {
                                                            if ($Condition->item($xCondition)->getAttribute('ToDay') == $Condition->item($xCondition)->getAttribute('FromDay')) {
                                                                $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $from))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                            } else {
                                                                if ($Condition->item($xCondition)->getAttribute('Type') == "cancellation") {
                                                                    $FromDay = $Condition->item($xCondition)->getAttribute('FromDay');
                                                                    if ($FromDay == "") {
                                                                        $FromDay = 0;
                                                                    }
                                                                    if (! is_numeric($FromDay)) {
                                                                        $FromDay = 0;
                                                                    }
                                                                    $ToDay = $Condition->item($xCondition)->getAttribute('ToDay');
                                                                    if ($ToDay == "") {
                                                                        $ToDay = 0;
                                                                    }
                                                                    if (! is_numeric($ToDay)) {
                                                                        $ToDay = 0;
                                                                    }
                                                                    if ($ToDay == 0) {
                                                                        if ($cancellationpolicy != "") {
                                                                            $cancellationpolicy .= "<br/>";
                                                                        }
                                                                        $cancellationpolicy .= $translator->translate("From today to") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $FromDay, date("Y", $from))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                                    } else {
                                                                        $a = mktime(0, 0, 0, date("m", $from), date("d", $from) - $ToDay, date("Y", $from));
                                                                        $b = mktime(0, 0, 0, date("m", $from), date("d", $from) - $FromDay, date("Y", $from));
                                                                        if ($a > b) {
                                                                            $cancellationpolicy .= $FromDay . "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $FromDay, date("Y", $from))) . " " . $translator->translate("to") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $from))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                                        } else {
                                                                            $cancellationpolicy .= $FromDay . "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $from))) . " " . $translator->translate("to") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $FromDay, date("Y", $from))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                                        }
                                                                    }
                                                                } else {
                                                                    $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $from))) . " or earlier " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $HotelRoomPrices = $xpath->query('HotelRoomPrices', $hotelElementExtra);
                                    if ($HotelRoomPrices->length > 0) {
                                        $HotelRoom = $HotelRoomPrices->item(0)->getElementsByTagName("HotelRoom");
                                        if ($HotelRoom->length > 0) {
                                            $PriceRanges = $HotelRoom->item(0)->getElementsByTagName("PriceRanges");
                                            if ($PriceRanges->length > 0) {
                                                $PriceRanges = $PriceRanges->item(0)->getElementsByTagName("PriceRange");
                                                for ($xPriceRanges = 0; $xPriceRanges < $PriceRanges->length; $xPriceRanges ++) {
                                                    $DateRange = $PriceRanges->item($xPriceRanges)->getElementsByTagName("DateRange");
                                                    if ($DateRange->length > 0) {
                                                        $FromDate = $DateRange->item(0)->getElementsByTagName("FromDate");
                                                        if ($FromDate->length > 0) {
                                                            $FromDate = $FromDate->item(0)->nodeValue;
                                                        }
                                                        $ToDate = $DateRange->item(0)->getElementsByTagName("ToDate");
                                                        if ($ToDate->length > 0) {
                                                            $ToDate = $ToDate->item(0)->nodeValue;
                                                        }
                                                    }
                                                    $Price = $PriceRanges->item($xPriceRanges)->getElementsByTagName("Price");
                                                    if ($Price->length > 0) {
                                                        $Gross = (float) $Price->item(0)->getAttribute("Gross");
                                                        if ($gtamarkup > 0) {
                                                            $Gross = $Gross + (($Gross * $gtamarkup) / 100);
                                                        }
                                                        if ($internalmarkup > 0) {
                                                            $Gross = $Gross + (($Gross * $internalmarkup) / 100);
                                                        }
                                                        if ($agent_markup > 0) {
                                                            $Gross = $Gross + (($Gross * $agent_markup) / 100);
                                                        }
                                                        if ($agent_discount > 0) {
                                                            $Gross = $Gross - (($Gross * $agent_discount) / 100);
                                                        }
                                                        // Fallback Markup
                                                        if ($gtamarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                                            $Gross = $Gross + (($Gross * $HotelsMarkupFallback) / 100);
                                                        }
                                                        if ($scurrency != "" and $base_currency != $scurrency) {
                                                            $Gross = $CurrencyConverter->convert($Gross, $base_currency, $scurrency);
                                                        }
                                                        $Nights = $Price->item(0)->getAttribute("Nights");
                                                        $fMonth = substr($FromDate, 5, 2);
                                                        $fDay = substr($FromDate, 8, 2);
                                                        $fYear = substr($FromDate, 0, 4);
                                                        $tMonth = substr($ToDate, 5, 2);
                                                        $tDay = substr($ToDate, 8, 2);
                                                        $tYear = substr($ToDate, 0, 4);
                                                        $FromDate = mktime(0, 0, 0, $fMonth, $fDay, $fYear);
                                                        // $ToDate = mktime(0, 0, 0, $tMonth, $tDay, $tYear);
                                                        // $year_diff = date("Y", $ToDate) - date("Y", $FromDate);
                                                        // if ($year_diff == "") {
                                                        // $year_diff = 0;
                                                        // }
                                                        // if (! is_numeric($year_diff)) {
                                                        // $year_diff = 0;
                                                        // }
                                                        // $numberofdaysAux = date('z', $ToDate) - date('z', $FromDate) + (((int) date('L', $FromDate)) ? ($year_diff * 366) : ($year_diff * 365));
                                                        for ($rZZ = 0; $rZZ < $Nights; $rZZ ++) {
                                                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $FromDate), date("d", $FromDate) + $rZZ, date("y", $FromDate)));
                                                            $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($Gross);
                                                            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $Gross;
                                                            $pricebreakdownCount = $pricebreakdownCount + 1;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (! is_numeric($itemPrice)) {
                                        $itemPrice = 0;
                                    }
                                    // Markup
                                    if ($gtamarkup > 0) {
                                        $itemPrice = $itemPrice + (($itemPrice * $gtamarkup) / 100);
                                    }
                                    if ($internalmarkup > 0) {
                                        $itemPrice = $itemPrice + (($itemPrice * $internalmarkup) / 100);
                                    }
                                    if ($agent_markup > 0) {
                                        $itemPrice = $itemPrice + (($itemPrice * $agent_markup) / 100);
                                    }
                                    // Fallback Markup
                                    if ($gtamarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                        $itemPrice = $itemPrice + (($itemPrice * $HotelsMarkupFallback) / 100);
                                    }
                                    // Agent discount
                                    if ($agent_discount > 0) {
                                        $itemPrice = $itemPrice - (($itemPrice * $agent_discount) / 100);
                                    }
                                    if ($scurrency != "" and $base_currency != $scurrency) {
                                        $itemPrice = $CurrencyConverter->convert($itemPrice, $base_currency, $scurrency);
                                    }
                                    // Detailed Prices
                                    $zRooms = $RoomIndex - 1;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $itemPrice;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $base_currency;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-11";
                                    if ($confirmationCode == "IM") {
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                    } elseif ($confirmationCode == "OR") {
                                        $roomdescription .= " - " . $translator->translate("On Request Rate");
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 4;
                                    }
                                    $roomdescription = ucwords(strtolower($roomdescription));
                                    //
                                    // RTM Caching
                                    //
                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('rtm_caching');
                                        $insert->values(array(
                                            'shid' => $shid,
                                            'supplier' => 11,
                                            'room' => $roomdescription
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                    } catch (\Exception $e) {
                                        $logger = new Logger();
                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                        $logger->addWriter($writer);
                                        $logger->info($e->getMessage());
                                    }
                                    // EOF
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $roomdescription;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ccd'] = $city_xml11;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $itemPrice;
                                    if ($mealcode == 'N' or $mealcode == '') {
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['mealcode'] = $mealcode;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = utf8_encode(htmlentities($translator->translate("Room Only"), ENT_QUOTES));
                                    } else {
                                        try {
                                            $sql = "select mapped from board_mapping where description='" . addslashes($meal) . "'";
                                            $statement = $db->createStatement($sql);
                                            $statement->prepare();
                                            $row_board_mapping = $statement->execute();
                                            $row_board_mapping->buffer();
                                            if ($row_board_mapping->valid()) {
                                                $row_board_mapping = $row_board_mapping->current();
                                                $meal = $row_board_mapping["mapped"];
                                            }
                                        } catch (\Exception $e) {
                                            $logger = new Logger();
                                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                            $logger->addWriter($writer);
                                            $logger->info($e->getMessage());
                                        }
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['mealcode'] = $mealcode;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $meal;
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $maxAdults;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $maxChildren;
                                    if ($offercode != "") {
                                        $special = true;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $offer;
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['offerid'] = $offerid;
                                    if ((int) $IncludedOfferDiscount > 0) {
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['discount'] = $filter->filter($IncludedOfferDiscount);
                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['totalold'] = $filter->filter($IncludedOfferDiscount + $itemPrice);
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cots'] = $numberofcots;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nrextrabeds'] = $nrextrabeds;
                                    if ($EssentialInformation != "") {
                                        $cancellationpolicy .= "<br/>" . $EssentialInformation;
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['countrytag'] = $countrytag;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $cancellationpolicy;
                                    $baseCounterDetails ++;
                                    $xRoomCategoryId ++;
                                }
                            }
                            if ($recommended == true) {
                                $tmp[$shid]['recommended'] = 1;
                            }
                            if ($special == true) {
                                $tmp[$shid]['special'] = true;
                            } else {
                                $tmp[$shid]['special'] = false;
                            }
                            $sfilter[] = "(sid='$shid' and ccd='$cityExtra')";
                            $valid = 1;
                        }
                    }
                }
            }
        }
        if ($valid == 1) {
            $sfilter = implode(' or ', $sfilter);
            try {
                $sql = "select hid, sid from xmlhotels_mgta where " . $sfilter;
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $result = $statement->execute();
                $result->buffer();
                if ($result instanceof ResultInterface && $result->isQueryResult()) {
                    $resultSet = new ResultSet();
                    $resultSet->initialize($result);
                    foreach ($resultSet as $row) {
                        $sidfilter[] = $row->hid;
                        if (is_array($hotels_array[$row->hid])) {
                            // Append to original details
                            $tmph = $hotels_array[$row->hid]['details'];
                            $tmps = $tmp[$row->sid]['details'];
                            foreach ($tmph as $key => $value) {
                                $last = count($tmph[$key]);
                                foreach ($tmps[$key] as $keyd => $valued) {
                                    $tmph[$key][$last] = $valued;
                                    $last ++;
                                }
                            }
                            $hotels_array[$row->hid]['details'] = $tmph;
                        } else {
                            $hotels_array[$row->hid] = $tmp[$row->sid];
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
                $supplier = 11;
                $sidfilter = implode(',', $sidfilter);
                $query = 'call xmlhotels("' . $sidfilter . '")';
                // Store Session
                try {
                    $sql = new Sql($db);
                    $delete = $sql->delete();
                    $delete->from('quote_session_gta');
                    $delete->where(array(
                        'session_id' => $session_id
                    ));
                    $statement = $sql->prepareStatementForSqlObject($delete);
                    $results = $statement->execute();
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('quote_session_gta');
                    $insert->values(array(
                        'session_id' => $session_id,
                        'xmlrequest' => (string) $xmlrequest,
                        'xmlresult' => (string) $xmlresult,
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