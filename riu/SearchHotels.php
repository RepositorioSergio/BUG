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
//error_log("\r\n COMECOU RIU \r\n", 3, "/srv/www/htdocs/error_log");
$riu = false;
$sql = "select city_xml57, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml57 = $row_settings["city_xml57"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml57 = 0;
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
    $sql = "select value from settings where name='riuPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
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
$sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuLoginEmail = $row_settings['value'];
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
//error_log("\r\n riuServiceURL: $riuServiceURL \r\n", 3, "/srv/www/htdocs/error_log");

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');

$raw2 = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<soap:Body>
    <loginXML xmlns="http://services.enginexml.rumbonet.riu.com">
        <in0 xmlns="http://services.enginexml.rumbonet.riu.com">
            <acceso xmlns="http://dtos.common.rumbonet.riu.com">XML</acceso>
            <codigoIdioma xmlns="http://dtos.common.rumbonet.riu.com">US</codigoIdioma>
            <codigoPais xmlns="http://dtos.common.rumbonet.riu.com">E</codigoPais>
            <ipCustomer xmlns="http://dtos.common.rumbonet.riu.com" xsi:nil="true" />
            <usuarioOpera xmlns="http://dtos.common.rumbonet.riu.com" xsi:nil="true" />
            <usuarioOperaId xmlns="http://dtos.common.rumbonet.riu.com">0</usuarioOperaId>
        </in0>
    </loginXML>
</soap:Body>
</soap:Envelope>';

$userpass = $riuLoginEmail . ':' . $riuPassword;
$login = base64_encode($userpass);

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
    "Content-length: ".strlen($raw2)
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
    echo $return;
    echo $response2->getStatusCode() . " - " . $response2->getReasonPhrase();
    echo $return;
    die();
}
echo "<br/>RESPONSE";
$x = $headers->toArray();
$x = $x["Set-Cookie"][0];
$x = explode(";",$x);
$x = $x[0];
$x = explode("=",$x);
$JSESSIONID = $x[1];


$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Header/>
    <soapenv:Body>
        <ns6:HotelAvail xmlns:ns6="http://services.enginexml.rumbonet.riu.com">
            <ns6:in0>
                <ns1:AdultsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . count($selectedAdults) . '</ns1:AdultsCount>
                <ns1:ChildCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . count($selectedChildren) . '</ns1:ChildCount>
                <ns1:CountryCode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">PE</ns1:CountryCode>
                <HotelList xmlns="http://dtos.enginexml.rumbonet.riu.com">
                    <HotelsList>
                        <ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">216</ns2:int>
                        <ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">4401</ns2:int>
                    </HotelsList>
                </HotelList>
                <ns1:InfantsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">0</ns1:InfantsCount>
                <ns1:Language xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">E</ns1:Language>
                <ns1:MealPlan xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/>
                <ns1:promocode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com"/>
                <ns1:rateReference xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/>
                <RoomList xmlns="http://dtos.enginexml.rumbonet.riu.com">
                    <RoomConfig>';

                for ($r=0; $r < $rooms; $r++) { 
                    $raw = $raw . '<RoomStayCandidate><AdultsCount>' . $selectedAdults[$r] . '</AdultsCount>';
                    if ($selectedChildren[$r] > 0) {
                        $raw = $raw . '<ChildCount>' . $selectedChildren[$r] . '</ChildCount>
                        <InfantsCount>0</InfantsCount>';
                    }else {
                        $raw = $raw . '<ChildCount>' . $selectedChildren[$r] . '</ChildCount>
                        <InfantsCount>0</InfantsCount>';
                    }
                    $raw = $raw . '</RoomStayCandidate>';
                } 

            $raw = $raw . '</RoomConfig>
            </RoomList>
                <ns1:RoomsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . $rooms . '</ns1:RoomsCount>
                <ns1:StayDateEnd xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . strftime("%Y%m%d", $to) . '</ns1:StayDateEnd>
                <ns1:StayDateStart xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . strftime("%Y%m%d", $from) . '</ns1:StayDateStart>
            </ns6:in0>
        </ns6:HotelAvail>
    </soapenv:Body>
</soapenv:Envelope>';
//error_log("\r\n raw: $raw \r\n", 3, "/srv/www/htdocs/error_log");

if ($riuServiceURL != "" and $riuLoginEmail != "" and $riuPassword != "") {
    $startTime = microtime();
    $client = new Client();
    $client->setOptions(array(
        'timeout' => 100,
        'sslverifypeer' => false,
        'sslverifyhost' => false
    ));
    $client->setHeaders(array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "Content-length: ".strlen($raw)
    ));


    $client->setUri($riuServiceURL);
    $client->setMethod('POST');
    $client->setCookies(array(
        'JSESSIONID' => $JSESSIONID
    ));
    $client->setRawBody($raw);
    $response = $client->send();
    if ($response->isSuccess()) {
    $response = $response->getBody();
    } else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
    }
    // if ($response === false) {
    // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    // } else {
    // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    // }
    //error_log("\r\n RESPONSE2: $response \r\n", 3, "/srv/www/htdocs/error_log");
    // Descomentar para ver o resultado do provider
    // Nao esquecer de alterar o session id para testar por causa de cache
    // echo $response;
    // die();
    $endTime = microtime();
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_riu');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $riuServiceURL . $raw,
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
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $HotelAvailResponse = $Body->item(0)->getElementsByTagName("HotelAvailResponse");
    $HotelAvailResponse2 = $HotelAvailResponse->item(0)->getElementsByTagName("HotelAvailResponse");
    $availabilityList = $HotelAvailResponse2->item(0)->getElementsByTagName("availabilityList");
    $node = $availabilityList->item(0)->getElementsByTagName("AvailabilityGroup");
    for ($i=0; $i < $node->length; $i++) {       
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
        $currencyCode = $node->item($i)->getElementsByTagName("currencyCode");
        if ($currencyCode->length > 0) {
            $currencyCode = $currencyCode->item(0)->nodeValue;
        } else {
            $currencyCode = "";
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
        
        //roomList
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
                    $hotel = $THabsDto->item(0)->getElementsByTagName("hotel");
                    if ($hotel->length > 0) {
                        $hotel = $hotel->item(0)->nodeValue;
                    } else {
                        $hotel = "";
                    }

                    $listTranslation = $THabsDto->item(0)->getElementsByTagName("listTranslation");
                    if ($listTranslation->length > 0) {
                        $TranslationDto = $listTranslation->item(0)->getElementsByTagName("TranslationDto");
                        if ($TranslationDto->length > 0) {
                            for ($l=0; $l < $TranslationDto->length; $l++) { 
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
                            }
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
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $hotelID;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $hotelID;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-57";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['JSESSIONID'] = $JSESSIONID;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateHotel'] = $rateHotel;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteType'] = $quoteType;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $AdultsCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $ChildCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['infants'] = $InfantsCount;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $amountWithoutOffer;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $amountWithoutOffer;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($mealPlan);
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                $amount = $amountWithoutOffer / $noOfNights;
                if ($riuMarkup != 0) {
                    $amount = $amount + (($amount * $riuMarkup) / 100);
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
                if ($riuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currencyCode;
            if ($promocode != "") {
                $tmp[$shid]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $translator->translate($promocode);
            } else {
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
            }
            // policyDescription nao existe
            // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $policyDescription;
            $riu = true;
        }
    }
}
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($riu == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mriu where " . $sfilter;
        //error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        $supplier = 57;
        //error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
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
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
?>