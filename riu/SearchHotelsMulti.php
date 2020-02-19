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
$db = new \Zend\Db\Adapter\Adapter($config);
unset($tmp);
$sfilter = array();
$riu = false;
$failed = false;
$hotellist = "";
$sql = "select sid from xmlhotels_mriu, xmlhotels where xmlhotels.city=" . $destination . " and xmlhotels.id=xmlhotels_mriu.hid";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$result2 = $statement2->execute();
$result2->buffer();
if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
    $resultSet2 = new ResultSet();
    $resultSet2->initialize($result2);
    foreach ($resultSet2 as $row2) {
        $hotellist .= '<ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">' . $row2['sid'] . '</ns2:int>';
    }
}
$sql = "select sid from xmlhotels_riu_mapping, xmlhotels where xmlhotels_riu_mapping.city=" . $destination . " and xmlhotels.id=xmlhotels_riu_mapping.hid";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$result2 = $statement2->execute();
$result2->buffer();
if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
    $resultSet2 = new ResultSet();
    $resultSet2->initialize($result2);
    foreach ($resultSet2 as $row2) {
        $hotellist .= '<ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">' . $row2['sid'] . '</ns2:int>';
    }
}
if ($hotellist != "") {
    if (file_exists("src/App/language/" . $lang . ".mo")) {
        $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
    }
    $affiliate_id = 0;
    $branch_filter = "";
    $sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $affiliate_id_riu = $affiliate_id;
    } else {
        $affiliate_id_riu = 0;
    }
    // if ((int) $nationality > 0) {
    // $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    // $statement2 = $db->createStatement($sql);
    // $statement2->prepare();
    // $row_settings = $statement2->execute();
    // $row_settings->buffer();
    // if ($row_settings->valid()) {
    // $row_settings = $row_settings->current();
    // $sourceMarket = $row_settings["iso_code_2"];
    // }
    // }
    // if ($sourceMarket == "") {
    $sql = "select value from settings where name='riuPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
    // }
    $sql = "select value from settings where name='riuMarkup' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuMarkup = (double) $row_settings['value'];
    } else {
        $riuMarkup = 0;
    }
    $sql = "select value from settings where name='riuCommission' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuCommission = (float) $row_settings['value'];
    } else {
        $riuCommission = 0;
    }
    $sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuLoginEmail = $row_settings['value'];
    }
    $sql = "select value from settings where name='riuTimeout' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuTimeout = (int) $row_settings['value'];
    } else {
        $riuTimeout = 0;
    }
    if ($riuTimeout == 0) {
        $riuTimeout = 120;
    }
    $sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $riuServiceURL = $row['value'];
    }
    $date = new Datetime();
    $timestamp = $date->format('U');
    $userpass = $riuLoginEmail . ':' . $riuPassword;
    $login = base64_encode($userpass);
    if ($failed == false) {
        if ($riuServiceURL != "" and $riuLoginEmail != "" and $riuPassword != "") {
            $nC = 0;
            $multiParallel = array();
            $multiParallelSession = array();
            $multiParallel = curl_multi_init();
            for ($r = 0; $r < $rooms; $r ++) {
                $raw2 = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><soap:Body><loginXML xmlns="http://services.enginexml.rumbonet.riu.com"><in0 xmlns="http://services.enginexml.rumbonet.riu.com"><acceso xmlns="http://dtos.common.rumbonet.riu.com">XML</acceso><codigoIdioma xmlns="http://dtos.common.rumbonet.riu.com">US</codigoIdioma><codigoPais xmlns="http://dtos.common.rumbonet.riu.com">E</codigoPais><ipCustomer xmlns="http://dtos.common.rumbonet.riu.com" xsi:nil="true" /><usuarioOpera xmlns="http://dtos.common.rumbonet.riu.com" xsi:nil="true" /><usuarioOperaId xmlns="http://dtos.common.rumbonet.riu.com">0</usuarioOperaId></in0></loginXML></soap:Body></soap:Envelope>';
                $startTime = microtime();
                $client1 = new Client();
                $client1->setOptions(array(
                    'timeout' => 100,
                    'sslverifypeer' => false,
                    'sslverifyhost' => false
                ));
                $client1->setHeaders(array(
                    "Content-type: text/xml;charset=\"utf-8\"",
                    "Accept: text/xml",
                    "Cache-Control: no-cache",
                    "Pragma: no-cache",
                    "Authorization: Basic " . $login,
                    "Content-length: " . strlen($raw2)
                ));
                $client1->setUri($riuServiceURL);
                $client1->setMethod('POST');
                $client1->setRawBody($raw2);
                $response2 = $client1->send();
                if ($response2->isSuccess()) {
                    $headers = $response2->getHeaders();
                    $response2 = $response2->getBody();
                } else {
                    $logger = new Logger();
                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                    $logger->addWriter($writer);
                    $logger->info($client1->getUri());
                    $logger->info($response2->getStatusCode() . " - " . $response2->getReasonPhrase());
                    $failed = true;
                }
                $endTime = microtime();
                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('log_riu');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'filename' => 'SearchHotels.php',
                        'errorline' => $this->microtime_diff($startTime, $endTime),
                        'errormessage' => $riuServiceURL . $raw2,
                        'sqlcontext' => base64_encode(serialize($headers->toArray())),
                        'errcontext' => (string) $userpass
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                } catch (\Exception $e) {
                    $logger = new Logger();
                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                    $logger->addWriter($writer);
                }
                
                $JSESSIONID = "";
                $x = $headers->toArray();
                $x = $x["Set-Cookie"];
                if (is_array($x)) {
                    for ($z = 0; $z < count($x); $z ++) {
                        $xTmp = explode(";", $x[$z]);
                        $xTmp = $xTmp[0];
                        $xTmp = explode("=", $xTmp);
                        // error_log("\r\n0=" . $xTmp[0] . "\r\n", 3, "/srv/www/htdocs/error_log");
                        // error_log("\r\n1=" . $xTmp[1] . "\r\n", 3, "/srv/www/htdocs/error_log");
                        if ($xTmp[0] == 'JSESSIONID') {
                            $JSESSIONID = $xTmp[1];
                            break;
                        }
                    }
                }
                if ($JSESSIONID == "") {
                    error_log("\r\nUnable to retreive JSESSIONID for RIU\r\n", 3, "/srv/www/htdocs/error_log");
                    error_log("\r\nHeaders:\r\n" . print_r($x, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
                } else {
                    $children = 0;
                    $infants = 0;
                    $adults = $selectedAdults[$r];
                    for ($z = 0; $z < (int) $selectedChildren[$r]; $z ++) {
                        if ($selectedChildrenAges[$r][$z] < 2) {
                            $infants ++;
                        } else {
                            $children ++;
                        }
                    }
                    $raw = '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Header/><soapenv:Body><ns6:HotelAvail xmlns:ns6="http://services.enginexml.rumbonet.riu.com"><ns6:in0><ns1:AdultsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . $adults . '</ns1:AdultsCount><ns1:ChildCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . $children . '</ns1:ChildCount><ns1:CountryCode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . $sourceMarket . '</ns1:CountryCode><HotelList xmlns="http://dtos.enginexml.rumbonet.riu.com"><HotelsList>' . $hotellist . '</HotelsList></HotelList><ns1:InfantsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . $infants . '</ns1:InfantsCount><ns1:Language xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">US</ns1:Language><ns1:MealPlan xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/><ns1:promocode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com"/><ns1:rateReference xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/><RoomList xmlns="http://dtos.enginexml.rumbonet.riu.com"><RoomConfig><RoomStayCandidate><AdultsCount>' . $selectedAdults[$r] . '</AdultsCount><ChildCount>' . $selectedChildren[$r] . '</ChildCount>';
                    if ($selectedChildren[$r] > 0) {
                        $raw .= '<Ages>';
                        for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                            $raw .= '<ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">' . $selectedChildrenAges[$r][$z] . '</ns2:int>';
                        }
                        $raw .= '</Ages>';
                    }
                    $raw .= '<InfantsCount>' . $infants . '</InfantsCount></RoomStayCandidate></RoomConfig></RoomList><ns1:RoomsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">1</ns1:RoomsCount><ns1:StayDateStart xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . strftime("%Y%m%d", $from) . '</ns1:StayDateStart><ns1:StayDateEnd xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . strftime("%Y%m%d", $to) . '</ns1:StayDateEnd></ns6:in0></ns6:HotelAvail></soapenv:Body></soapenv:Envelope>';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $riuServiceURL);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
                    curl_setopt($ch, CURLOPT_VERBOSE, false);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $riuTimeout);
                    curl_setopt($ch, CURLOPT_TIMEOUT, $riuTimeout);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "Content-length: " . strlen($raw),
                        "Cookie: JSESSIONID=" . $JSESSIONID
                    ));
                    curl_multi_add_handle($multiParallel, $ch);
                    $requestsParallel[$nC] = $r;
                    $channelsParallel[$nC] = $ch;
                    $multiParallelSession[$nC] = $JSESSIONID;
                    $nC ++;
                }
            }
            $active = null;
            do {
                $mrc = curl_multi_exec($multiParallel, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($multiParallel) == - 1) {
                    continue;
                }
                do {
                    $mrc = curl_multi_exec($multiParallel, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                if ($mrc != CURLM_OK) {
                    error_log("\r\nCurl Multi Exec Error:" . curl_multi_strerror($mrc) . "\r\n", 3, "/srv/www/htdocs/error_log");
                }
            }
            foreach ($channelsParallel as $zRooms => $channel) {
                $response = curl_multi_getcontent($channel);
                $raw = $requestsParallel[$zRooms];
                $JSESSIONID = $multiParallelSession[$zRooms];
                curl_multi_remove_handle($multiParallel, $channel);
                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('log_riu');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'filename' => 'SearchHotels.php',
                        'errorline' => $zRooms,
                        'errormessage' => $riuServiceURL . $raw,
                        'sqlcontext' => $response,
                        'errcontext' => (string) $JSESSIONID
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
                $Envelope = $inputDoc->getElementsByTagName("Envelope");
                $Body = $Envelope->item(0)->getElementsByTagName("Body");
                $HotelAvailResponse = $Body->item(0)->getElementsByTagName("HotelAvailResponse");
                $HotelAvailResponse2 = $HotelAvailResponse->item(0)->getElementsByTagName("HotelAvailResponse");
                $availabilityList = $HotelAvailResponse2->item(0)->getElementsByTagName("availabilityList");
                $node = $availabilityList->item(0)->getElementsByTagName("AvailabilityGroup");
                for ($i = 0; $i < $node->length; $i ++) {
                    $amount = $node->item($i)->getElementsByTagName("amount");
                    if ($amount->length > 0) {
                        $amount = $amount->item(0)->nodeValue;
                    } else {
                        $amount = "";
                    }
                    $amountWithoutOffer = $node->item($i)->getElementsByTagName("amountWithoutOffer");
                    if ($amountWithoutOffer->length > 0) {
                        $amountWithoutOffer = $amountWithoutOffer->item(0)->nodeValue;
                    } else {
                        $amountWithoutOffer = "";
                    }
                    $amountNetCommissionable = $amount;
                    if ($riuCommission > 0) {
                        $amount = $amount - (($amount * $riuCommission) / 100);
                        $amountWithoutOffer = $amountWithoutOffer - (($amountWithoutOffer * $riuCommission) / 100);
                    }
                    $amountNet = $amount;
                    $amountWithoutOfferNet = $amountWithoutOffer;
                    $currencyCode = $node->item($i)->getElementsByTagName("currencyCode");
                    if ($currencyCode->length > 0) {
                        $currencyCode = $currencyCode->item(0)->nodeValue;
                    } else {
                        $currencyCode = "";
                    }
                    if ($riuMarkup != 0) {
                        $amount = $amount + (($amount * $riuMarkup) / 100);
                        $amountWithoutOffer = $amountWithoutOffer + (($amountWithoutOffer * $riuMarkup) / 100);
                    }
                    // Geo target markup
                    if ($internalmarkup != 0) {
                        $amount = $amount + (($amount * $internalmarkup) / 100);
                        $amountWithoutOffer = $amountWithoutOffer + (($amountWithoutOffer * $internalmarkup) / 100);
                    }
                    // Agent markup
                    if ($agent_markup != 0) {
                        $amount = $amount + (($amount * $agent_markup) / 100);
                        $amountWithoutOffer = $amountWithoutOffer + (($amountWithoutOffer * $agent_markup) / 100);
                    }
                    // Fallback Markup
                    if ($riuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                        $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                        $amountWithoutOffer = $amountWithoutOffer + (($amountWithoutOffer * $HotelsMarkupFallback) / 100);
                    }
                    // Agent discount
                    if ($agent_discount != 0) {
                        $amount = $amount - (($amount * $agent_discount) / 100);
                        $amountWithoutOffer = $amountWithoutOffer - (($amountWithoutOffer * $agent_discount) / 100);
                    }
                    $currency = $currencyCode;
                    if ($scurrency != "" and $currency != $scurrency) {
                        $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                        $amountWithoutOffer = $CurrencyConverter->convert($amountWithoutOffer, $currency, $scurrency);
                    }
                    $hotelID = $node->item($i)->getElementsByTagName("hotelID");
                    if ($hotelID->length > 0) {
                        $hotelID = $hotelID->item(0)->nodeValue;
                    } else {
                        $hotelID = "";
                    }
                    $shid = $hotelID;
                    $sfilter[] = " sid='$hotelID' ";
                    $moroccoTaxes = $node->item($i)->getElementsByTagName("moroccoTaxes");
                    if ($moroccoTaxes->length > 0) {
                        $moroccoTaxes = $moroccoTaxes->item(0)->nodeValue;
                    } else {
                        $moroccoTaxes = "";
                    }
                    $quoteType = $node->item($i)->getElementsByTagName("quoteType");
                    if ($quoteType->length > 0) {
                        $quoteType = $quoteType->item(0)->nodeValue;
                    } else {
                        $quoteType = "";
                    }
                    $rateHotel = $node->item($i)->getElementsByTagName("rateHotel");
                    if ($rateHotel->length > 0) {
                        $rateHotel = $rateHotel->item(0)->nodeValue;
                    } else {
                        $rateHotel = "";
                    }
                    $taxIncluded = $node->item($i)->getElementsByTagName("taxIncluded");
                    if ($taxIncluded->length > 0) {
                        $taxIncluded = $taxIncluded->item(0)->nodeValue;
                    } else {
                        $taxIncluded = "";
                    }
                    $typePrice = $node->item($i)->getElementsByTagName("typePrice");
                    if ($typePrice->length > 0) {
                        $typePrice = $typePrice->item(0)->nodeValue;
                    } else {
                        $typePrice = "";
                    }
                    $uniqueID = $node->item($i)->getElementsByTagName("uniqueID");
                    if ($uniqueID->length > 0) {
                        $uniqueID = $uniqueID->item(0)->nodeValue;
                    } else {
                        $uniqueID = "";
                    }
                    $promocode = $node->item($i)->getElementsByTagName("promocode");
                    if ($promocode->length > 0) {
                        $promocode = $promocode->item(0)->nodeValue;
                    } else {
                        $promocode = "";
                    }
                    $rateReference = $node->item($i)->getElementsByTagName("rateReference");
                    if ($rateReference->length > 0) {
                        $rateReference = $rateReference->item(0)->nodeValue;
                    } else {
                        $rateReference = "";
                    }
                    // roomList
                    $roomList = $node->item($i)->getElementsByTagName("roomList");
                    if ($roomList->length > 0) {
                        $RoomStayGroup = $roomList->item(0)->getElementsByTagName("RoomStayGroup");
                        if ($RoomStayGroup->length > 0) {
                            $RSGamount = $RoomStayGroup->item(0)->getElementsByTagName("amount");
                            if ($RSGamount->length > 0) {
                                $RSGamount = $RSGamount->item(0)->nodeValue;
                            } else {
                                $RSGamount = "";
                            }
                            $mealPlan = $RoomStayGroup->item(0)->getElementsByTagName("mealPlan");
                            if ($mealPlan->length > 0) {
                                $mealPlan = $mealPlan->item(0)->nodeValue;
                            } else {
                                $mealPlan = "";
                            }
                            $roomTypeCode = $RoomStayGroup->item(0)->getElementsByTagName("roomTypeCode");
                            if ($roomTypeCode->length > 0) {
                                $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
                            } else {
                                $roomTypeCode = "";
                            }
                            $roomConfig = $RoomStayGroup->item(0)->getElementsByTagName("roomConfig");
                            if ($roomConfig->length > 0) {
                                $AdultsCount = $roomConfig->item(0)->getElementsByTagName("AdultsCount");
                                if ($AdultsCount->length > 0) {
                                    $AdultsCount = $AdultsCount->item(0)->nodeValue;
                                } else {
                                    $AdultsCount = "";
                                }
                                $Ages = $roomConfig->item(0)->getElementsByTagName("Ages");
                                if ($Ages->length > 0) {
                                    $Ages = $Ages->item(0)->nodeValue;
                                } else {
                                    $Ages = "";
                                }
                                $ChildCount = $roomConfig->item(0)->getElementsByTagName("ChildCount");
                                if ($ChildCount->length > 0) {
                                    $ChildCount = $ChildCount->item(0)->nodeValue;
                                } else {
                                    $ChildCount = "";
                                }
                                $InfantsCount = $roomConfig->item(0)->getElementsByTagName("InfantsCount");
                                if ($InfantsCount->length > 0) {
                                    $InfantsCount = $InfantsCount->item(0)->nodeValue;
                                } else {
                                    $InfantsCount = "";
                                }
                            }
                        }
                    }
                    $hab = array();
                    $translationTHabs = $node->item($i)->getElementsByTagName("translationTHabs");
                    if ($translationTHabs->length > 0) {
                        $listTHabs = $translationTHabs->item(0)->getElementsByTagName("listTHabs");
                        if ($listTHabs->length > 0) {
                            $THabsDto = $listTHabs->item(0)->getElementsByTagName("THabsDto");
                            if ($THabsDto->length > 0) {
                                $codTha = $THabsDto->item(0)->getElementsByTagName("codTha");
                                if ($codTha->length > 0) {
                                    $codTha = $codTha->item(0)->nodeValue;
                                } else {
                                    $codTha = "";
                                }
                                // $hotel = $THabsDto->item(0)->getElementsByTagName("hotel");
                                // if ($hotel->length > 0) {
                                // $hotel = $hotel->item(0)->nodeValue;
                                // } else {
                                // $hotel = "";
                                // }
                                $listTranslation = $THabsDto->item(0)->getElementsByTagName("listTranslation");
                                if ($listTranslation->length > 0) {
                                    $TranslationDto = $listTranslation->item(0)->getElementsByTagName("TranslationDto");
                                    if ($TranslationDto->length > 0) {
                                        for ($l = 0; $l < $TranslationDto->length; $l ++) {
                                            $description = $TranslationDto->item($l)->getElementsByTagName("description");
                                            if ($description->length > 0) {
                                                $description = $description->item(0)->nodeValue;
                                            } else {
                                                $description = "";
                                            }
                                            $language = $TranslationDto->item($l)->getElementsByTagName("language");
                                            if ($language->length > 0) {
                                                $language = $language->item(0)->nodeValue;
                                            } else {
                                                $language = "";
                                            }
                                            $hab[$codTha][$language] = $description;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (is_array($tmp[$shid])) {
                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                    } else {
                        $baseCounterDetails = 0;
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-57";
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['JSESSIONID'] = $JSESSIONID;
                    if ($hab[$roomTypeCode]['US'] != "") {
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $hab[$roomTypeCode]['US'];
                    } else {
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $roomTypeCode;
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomtypecode'] = $roomTypeCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ratehotel'] = $rateHotel;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quotetype'] = $quoteType;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $AdultsCount;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $ChildCount;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['infants'] = $InfantsCount;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $amount;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $amountNet;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotalcommissionable'] = $amountNetCommissionable;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ratereference'] = $rateReference;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['mealplan'] = $mealPlan;
                    try {
                        $sql = "select mapped from board_mapping where description='" . addslashes($mealPlan) . "'";
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        $row_board_mapping = $statement->execute();
                        $row_board_mapping->buffer();
                        if ($row_board_mapping->valid()) {
                            $row_board_mapping = $row_board_mapping->current();
                            $mealPlan = $row_board_mapping["mapped"];
                        }
                    } catch (\Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($mealPlan);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $amount = $amountWithoutOffer / $noOfNights;
                        $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                        $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                        $pricebreakdownCount = $pricebreakdownCount + 1;
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currencyCode;
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['promocode'] = $promocode;
                    // TODO - $amountWithoutOffer - > Mostrar com strike
                    if ($promocode != "") {
                        $tmp[$shid]['special'] = true;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $translator->translate($promocode);
                    } else {
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                    }
                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['sourcemarket'] = $sourceMarket;
                    $riu = true;
                }
            }
            if ($riu == true) {
                $sfilter = implode(' or ', $sfilter);
                try {
                    $sql = "select hid, sid from xmlhotels_mriu where " . $sfilter;
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
                    $supplier = 57;
                    try {
                        $sql = new Sql($db);
                        $delete = $sql->delete();
                        $delete->from('quote_session_riu');
                        $delete->where(array(
                            'session_id' => $session_id
                        ));
                        $statement = $sql->prepareStatementForSqlObject($delete);
                        $results = $statement->execute();
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('quote_session_riu');
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
}
?>