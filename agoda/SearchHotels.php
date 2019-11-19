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
$agoda = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
error_log("\r\n COMECOU AGODA \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select name, country_id, zone_id,city_xml19, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml19 = $row_settings["city_xml19"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml19 = "";
}
$city_xml19 = "HKG";

$affiliate_id = 0;
$sql = "select value from settings where name='enablerts' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_rts = $affiliate_id;
} else {
    $affiliate_id_rts = 0;
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
    $sql = "select value from settings where name='rtsDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='rtsID' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsID = $row_settings['value'];
}
$sql = "select value from settings where name='rtsPassword' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='rtsSiteCode' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsSiteCode = $row_settings['value'];
}
$sql = "select value from settings where name='rtsRequestType' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsRequestType = $row_settings['value'];
}
$sql = "select value from settings where name='rtsServiceURL' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsServiceURL = $row['value'];
}
$sql = "select value from settings where name='rtsMarkup' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsMarkup = (double) $row_settings['value'];
} else {
    $rtsMarkup = 0;
}

$dateStart = new DateTime(strftime("%d-%m-%Y", $from));
$dateEnd = new DateTime(strftime("%d-%m-%Y", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$url = "http://sandbox-affiliateapi.agoda.com/xmlpartner/xmlservice/search_lrv3";

// error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
if ($url != "") {

    $raw = '<?xml version="1.0" encoding="UTF-8"?>
<AvailabilityRequestV2 xmlns="http://xml.agoda.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" siteid="1831338" apikey="b57a754c-5e06-4cdd-ac0d-2ea58c48ef74">
   <Type>6</Type>
   <Id>12153,12157</Id>
   <CheckIn>2019-12-25</CheckIn>
   <CheckOut>2019-12-27</CheckOut>
   <Rooms>1</Rooms>
   <Adults>2</Adults>
   <Children>0</Children>
   <Language>en-us</Language>
   <Currency>USD</Currency>
   <UserCountry>US</UserCountry>
</AvailabilityRequestV2>';


    $headers = array(
        'Accept-Encoding' => 'gzip,deflate',
        'Content-Length' => strlen($raw),
        'Content-Type' => 'text/xml;charset=utf-8',
        'Authorization' => '1831338:b57a754c-5e06-4cdd-ac0d-2ea58c48ef74'
    ); 

    $startTime = microtime();
    /* $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING , "gzip");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch); */
    $client = new Client();
    $client->setOptions(array(
        'timeout' => 100,
        'sslverifypeer' => false,
        'sslverifyhost' => false
    ));
    $client->setHeaders(array(
        'Accept-Encoding' => 'gzip,deflate',
        'Content-Length' => strlen($raw),
        'Content-Type' => 'text/xml;charset=utf-8',
        'Authorization' => '1831338:b57a754c-5e06-4cdd-ac0d-2ea58c48ef74'
    ));
    $client->setUri($url);
    $client->setMethod('POST');
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
    $endTime = microtime();
    error_log("\r\n Response AGODA: $response \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_agoda');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $url,
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
    $AvailabilityLongResponseV2 = $inputDoc->getElementsByTagName("AvailabilityLongResponseV2");
    $searchid = $AvailabilityLongResponseV2->item(0)->getAttribute('searchid');

    $Hotels = $AvailabilityLongResponseV2->item(0)->getElementsByTagName('Hotels');
    if ($Hotels->length > 0) {
        $Hotel = $Hotels->item(0)->getElementsByTagName('Hotel');
        if ($Hotel->length > 0) {
            for ($i=0; $i < $Hotel->length; $i++) { 
                $Id = $Hotel->item($i)->getElementsByTagName('Id');
                if ($Id->length > 0) {
                    $Id = $Id->item(0)->nodeValue;
                } else {
                    $Id = "";
                }
                $shid = $Id;
                $sfilter[] = " sid='$Id' ";

                $CheapestRoom = $Hotel->item($i)->getElementsByTagName('CheapestRoom');
                if ($CheapestRoom->length > 0) {
                    $inclusive = $CheapestRoom->item(0)->getAttribute('inclusive');
                    $fees = $CheapestRoom->item(0)->getAttribute('fees');
                    $tax = $CheapestRoom->item(0)->getAttribute('tax');
                    $exclusive = $CheapestRoom->item(0)->getAttribute('exclusive');
                } else {
                    $inclusive = "";
                    $fees = "";
                    $tax = "";
                    $exclusive = "";
                }
                $PaxSettings = $Hotel->item($i)->getElementsByTagName('PaxSettings');
                if ($PaxSettings->length > 0) {
                    $childage = $PaxSettings->item(0)->getAttribute('childage');
                    $infantage = $PaxSettings->item(0)->getAttribute('infantage');
                    $submit = $PaxSettings->item(0)->getAttribute('submit');
                } else {
                    $childage = "";
                    $infantage = "";
                    $submit = "";
                }

                $Rooms = $Hotel->item($i)->getElementsByTagName('Rooms');
                if ($Rooms->length > 0) {
                    $Room = $Rooms->item(0)->getElementsByTagName('Room');
                    if ($Room->length > 0) {
                        for ($r=0; $r < $Room->length; $r++) { 
                            $Roomid = $Room->item($r)->getAttribute('id');
                            $Roomname = $Room->item($r)->getAttribute('name');
                            $promoeligible = $Room->item($r)->getAttribute('promoeligible');
                            $blockid = $Room->item($r)->getAttribute('blockid');
                            $ratecategoryid = $Room->item($r)->getAttribute('ratecategoryid');
                            $model = $Room->item($r)->getAttribute('model');
                            $currency = $Room->item($r)->getAttribute('currency');
                            $ratetype = $Room->item($r)->getAttribute('ratetype');
                            $rateplan = $Room->item($r)->getAttribute('rateplan');
                            $lineitemid = $Room->item($r)->getAttribute('lineitemid');
                            $promotionid = $Room->item($r)->getAttribute('promotionid');

                            $StandardTranslation = $Room->item($r)->getElementsByTagName('StandardTranslation');
                            if ($StandardTranslation->length > 0) {
                                $StandardTranslation = $StandardTranslation->item(0)->nodeValue;
                            } else {
                                $StandardTranslation = "";
                            }
                            $RemainingRooms = $Room->item($r)->getElementsByTagName('RemainingRooms');
                            if ($RemainingRooms->length > 0) {
                                $RemainingRooms = $RemainingRooms->item(0)->nodeValue;
                            } else {
                                $RemainingRooms = "";
                            }
                            //Benefits
                            $benefitArray = array();
                            $count3 = 0;
                            $Benefits = $Room->item($r)->getElementsByTagName('Benefits');
                            if ($Benefits->length > 0) {
                                $Benefit = $Benefits->item(0)->getElementsByTagName('Benefit');
                                if ($Benefit->length > 0) {
                                    for ($b=0; $b < $Benefit->length; $b++) { 
                                        $Benefitid = $Benefit->item($b)->getAttribute('id');
                                        $BenefitName = $Benefit->item($b)->getElementsByTagName('Name');
                                        if ($BenefitName->length > 0) {
                                            $BenefitName = $BenefitName->item(0)->nodeValue;
                                        } else {
                                            $BenefitName = "";
                                        }
                                        $BenefitTranslation = $Benefit->item($b)->getElementsByTagName('Translation');
                                        if ($BenefitTranslation->length > 0) {
                                            $BenefitTranslation = $BenefitTranslation->item(0)->nodeValue;
                                        } else {
                                            $BenefitTranslation = "";
                                        }
                                        $benefitArray[$count3]["id"] = $Benefitid;
                                        $benefitArray[$count3]["Name"] = $BenefitName;
                                        $benefitArray[$count3]["Translation"] = $BenefitTranslation;
                                        $count3 = $count3 + 1;
                                    }
                                }
                            }
                            //ParentRoom
                            $ParentRoom = $Room->item($r)->getElementsByTagName('ParentRoom');
                            if ($ParentRoom->length > 0) {
                                $ParentRoomid = $ParentRoom->item(0)->getAttribute('id');
                                $ParentRoomname = $ParentRoom->item(0)->getAttribute('name');
                                $ParentRoomtranslationname = $ParentRoom->item(0)->getAttribute('translationname');
                            } else {
                                $ParentRoomid = "";
                                $ParentRoomname = "";
                                $ParentRoomtranslationname = "";
                            }
                            //MaxRoomOccupancy
                            $MaxRoomOccupancy = $Room->item($r)->getElementsByTagName('MaxRoomOccupancy');
                            if ($MaxRoomOccupancy->length > 0) {
                                $extrabeds = $MaxRoomOccupancy->item(0)->getAttribute('extrabeds');
                                $normalbedding = $MaxRoomOccupancy->item(0)->getAttribute('normalbedding');
                            } else {
                                $extrabeds = "";
                                $normalbedding = "";
                            }
                            //RateInfo
                            $RateInfo = $Room->item($r)->getElementsByTagName('RateInfo');
                            if ($RateInfo->length > 0) {
                                $Included = $RateInfo->item(0)->getElementsByTagName('Included');
                                if ($Included->length > 0) {
                                    $Included = $Included->item(0)->nodeValue;
                                } else {
                                    $Included = "";
                                }
                                $Rate = $RateInfo->item(0)->getElementsByTagName('Rate');
                                if ($Rate->length > 0) {
                                    $Rateinclusive = $Rate->item(0)->getAttribute('inclusive');
                                    $Ratefees = $Rate->item(0)->getAttribute('fees');
                                    $Ratetax = $Rate->item(0)->getAttribute('tax');
                                    $Rateexclusive = $Rate->item(0)->getAttribute('exclusive');
                                } else {
                                    $Rateinclusive = "";
                                    $Ratefees = "";
                                    $Ratetax = "";
                                    $Rateexclusive = "";
                                }
                                $Promotion = $RateInfo->item(0)->getElementsByTagName('Promotion');
                                if ($Promotion->length > 0) {
                                    $text = $Promotion->item(0)->getAttribute('text');
                                    $savings = $Promotion->item(0)->getAttribute('savings');
                                } else {
                                    $text = "";
                                    $savings = "";
                                }
                                $TotalPaymentAmount = $RateInfo->item(0)->getElementsByTagName('TotalPaymentAmount');
                                if ($TotalPaymentAmount->length > 0) {
                                    $TotalPaymentAmountinclusive = $TotalPaymentAmount->item(0)->getAttribute('inclusive');
                                    $TotalPaymentAmountfees = $TotalPaymentAmount->item(0)->getAttribute('fees');
                                    $TotalPaymentAmounttax = $TotalPaymentAmount->item(0)->getAttribute('tax');
                                    $TotalPaymentAmountexclusive = $TotalPaymentAmount->item(0)->getAttribute('exclusive');
                                } else {
                                    $TotalPaymentAmountinclusive = "";
                                    $TotalPaymentAmountfees = "";
                                    $TotalPaymentAmounttax = "";
                                    $TotalPaymentAmountexclusive = "";
                                }
                            }

                            //Cancellation
                            $policy = "";
                            $policyTrans = "";
                            $policyParam = "";
                            $policyArray = array();
                            $count2 = 0;
                            $Cancellation = $Room->item($r)->getElementsByTagName('Cancellation');
                            if ($Cancellation->length > 0) {
                                $PolicyText = $Cancellation->item(0)->getElementsByTagName('PolicyText');
                                if ($PolicyText->length > 0) {
                                    $language = $PolicyText->item(0)->getAttribute('language');
                                    $policy = $PolicyText->item(0)->nodeValue;
                                } else {
                                    $policy = "";
                                }
                                $PolicyTranslated = $Cancellation->item(0)->getElementsByTagName('PolicyTranslated');
                                if ($PolicyTranslated->length > 0) {
                                    $language = $PolicyTranslated->item(0)->getAttribute('language');
                                    $policyTrans = $PolicyTranslated->item(0)->nodeValue;
                                } else {
                                    $policyTrans = "";
                                }
                                $PolicyParameters = $Cancellation->item(0)->getElementsByTagName('PolicyParameters');
                                if ($PolicyParameters->length > 0) {
                                    $PolicyParameter = $PolicyParameters->item(0)->getElementsByTagName('PolicyParameter');
                                    if ($PolicyParameter->length > 0) {
                                        for ($j=0; $j < $PolicyParameter->length; $j++) { 
                                            $PolicyParametercharge = $PolicyParameter->item($j)->getAttribute('charge');
                                            $PolicyParameterdays = $PolicyParameter->item($j)->getAttribute('days');
                                            $policyParam = $PolicyParameter->item($j)->nodeValue;
                                        }
                                    }
                                }
                                $PolicyDates = $Cancellation->item(0)->getElementsByTagName('PolicyDates');
                                if ($PolicyDates->length > 0) {
                                    $PolicyDate = $PolicyDates->item(0)->getElementsByTagName('PolicyDate');
                                    if ($PolicyDate->length > 0) {
                                        for ($p=0; $p < $PolicyDate->length; $p++) { 
                                            $before = $PolicyDate->item($p)->getAttribute('before');
                                            $after = $PolicyDate->item($p)->getAttribute('after');

                                            $policyArray[$count2]["before"] = $before;
                                            $policyArray[$count2]["after"] = $after;

                                            $RatePD = $PolicyDate->item($p)->getElementsByTagName('Rate');
                                            if ($RatePD->length > 0) {
                                                $RatePDinclusive = $RatePD->item(0)->getAttribute('inclusive');
                                                $RatePDfees = $RatePD->item(0)->getAttribute('fees');
                                                $RatePDtax = $RatePD->item(0)->getAttribute('tax');
                                                $RatePDexclusive = $RatePD->item(0)->getAttribute('exclusive');

                                                $policyArray[$count2]["inclusive"] = $RatePDinclusive;
                                                $policyArray[$count2]["fees"] = $RatePDfees;
                                                $policyArray[$count2]["tax"] = $RatePDtax;
                                                $policyArray[$count2]["exclusive"] = $RatePDexclusive;
                                                
                                            } else {
                                                $RatePDinclusive = "";
                                                $RatePDfees = "";
                                                $RatePDtax = "";
                                                $RatePDexclusive = "";
                                            }
                                            $count2 = $count2 + 1;
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
                                
                                //$tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $hotelName;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $Id;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $Roomid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $hotelCode;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $shid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['searchid'] = $searchid;
                                // cancellationType nao existe
                                // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $c_type;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-37";
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $Roomname;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $Roomname;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $Roomname;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rate_code'] = $ratetype;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ratetype'] = $ratetype;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ratecategoryid'] = $ratecategoryid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['blockid'] = $blockid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateIncluded'] = $Included;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ratePlanscode'] = $rateplan;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateExclusive'] = $rateExclusive;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateTax'] = $rateTax;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateFees'] = $rateFees;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateInclusive'] = $rateInclusive;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['promotionid'] = $promotionid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['lineitemid'] = $lineitemid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $TotalPaymentAmountinclusive;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $TotalPaymentAmountexclusive;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($benefitArray[0]["Name"]);
                                $pricebreakdown = array();
                                $pricebreakdownCount = 0;
                                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                    $amount = $TotalPaymentAmountinclusive / $noOfNights;
                                    if ($rtsMarkup != 0) {
                                        $amount = $amount + (($amount * $rtsMarkup) / 100);
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
                                    if ($rtsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";

                                
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $policy;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $policyArray[0]["before"];

                                $count = $count + 1;
                            }
                        $agoda = true;
                        }
                    }
                }
            }
        }
    }
}

//error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($agoda == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_magoda where " . $sfilter;
        //error_log("\r\n AGODA SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
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

    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 37;
        //error_log("\r\SYM QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_agoda');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_agoda');
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
?>