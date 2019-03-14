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
$jactravel = false;
//error_log("\r\n COMECOU JAC SEGUNDA TARDE \r\n", 3, "/srv/www/htdocs/error_log");
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id,city_xml22, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml22 = $row_settings["city_xml22"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml22 = 0;
}

$sql = "select value from settings where name='enablejacktravel' and affiliate_id=$affiliate_id";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $affiliate_id_jactravel = $affiliate_id;
} else {
    $affiliate_id_jactravel = 0;
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
    $sql = "select value from settings where name='JacTravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_jactravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}

$sql = "select value from settings where name='JacTravelClientName' and affiliate_id=$affiliate_id_jactravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelClientName = $row_settings['value'];
}

$sql = "select value from settings where name='JacTravelKey' and affiliate_id=$affiliate_id_jactravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelKey = $row_settings['value'];
}

$sql = "select value from settings where name='JacTravelMarkup' and affiliate_id=$affiliate_id_jactravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelMarkup = (double) $row_settings['value'];
} else {
    $JacTravelMarkup = 0;
}

$sql = "select value from settings where name='JacTravelSearchServiceURLServiceSearch' and affiliate_id=$affiliate_id_jactravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelSearchServiceURLServiceSearch = $row_settings['value'];
}

$sql = "select value from settings where name='JacTravelAvailability' and affiliate_id=$affiliate_id_jactravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelAvailability = $row_settings['value'];
}

$sql = "select value from settings where name='JacTravelcurrencycode' and affiliate_id=$affiliate_id_jactravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $JacTravelcurrencycode = $row_settings['value'];
}

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');

$city_xml22 = "London";
if ($city_xml22 != "") {
    $raw = '<SERVICE_SEARCH_REQUEST><VERSION_HISTORY APPLICATION_NAME="AppName" XML_FILE_NAME="XMLFileName" LICENCE_KEY="' . $JacTravelKey . '" TS_API_VERSION="TSAPIVersion"><XML_VERSION_NO>3.0</XML_VERSION_NO></VERSION_HISTORY><GEO_LOCATION_NAME>' . $city_xml22 . '</GEO_LOCATION_NAME><START_DATE>' . strftime("%d %b %y", $from) . '</START_DATE><NUMBER_OF_NIGHTS>' . $noOfNights . '</NUMBER_OF_NIGHTS><AVAILABLE_ONLY>' . $JacTravelAvailability . '</AVAILABLE_ONLY><GET_START_PRICE>true</GET_START_PRICE>';
    if ($JacTravelcurrencycode != "") {
        $raw .= '<CUR>' . $JacTravelcurrencycode . '</CUR>';
    }
    $raw .= '<ROOM_REPLY><ALL_ROOM/></ROOM_REPLY><ROOMS_REQUIRED>';
    // Room Type | Occupancy ID | Max/ Min Adult | Max/Min Child
    // Single | 1 | 1 / 1 | 0 / 0
    // Twin | 2 | 2 / 2 | 0 / 0
    // Double | 3 | 2 / 2 | 0 / 0
    // Triple | 4 | 3 / 3 | 0 / 0
    // Quad | 5 | 4 / 4 | 0 / 0
    // Family Room 1 | 7 | 2 / 2 | 1 / 1
    // Family Room 2 | 8 | 2 / 2 | 2 / 2
    $OCCUPANCYHandler = 0;
    for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
        $zAdults = $selectedAdults[$zRooms];
        if ($zAdults == "") {
            $zAdults = 2;
        }
        if (! is_numeric($zAdults)) {
            $zAdults = 2;
        }
        $zChildren = $selectedChildren[$zRooms];
        if ($zChildren == "") {
            $zChildren = 0;
        }
        if (! is_numeric($zChildren)) {
            $zChildren = 0;
        }
        $raw = $raw . '<ROOM>';
        switch ($zAdults) {
            case 1:
                if ($zChildren == 0) {
                    // Single
                    $raw = $raw . '<OCCUPANCY>1</OCCUPANCY>';
                    $selectedOCCUPANCY[1] = $zRooms;
                } elseif ($zChildren == 1) {
                    // Familty Room 1
                    $raw = $raw . '<OCCUPANCY>7</OCCUPANCY>';
                    $selectedOCCUPANCY[7] = $zRooms;
                } else {
                    // Familty Room 2
                    $raw = $raw . '<OCCUPANCY>8</OCCUPANCY>';
                    $selectedOCCUPANCY[8] = $zRooms;
                }
                break;
            case 2:
                if ($zChildren == 0) {
                    // Twin
                    $raw = $raw . '<OCCUPANCY>2</OCCUPANCY>';
                    $OCCUPANCYHandler = 2;
                    $selectedOCCUPANCY[2] = $zRooms;
                    $selectedOCCUPANCY[3] = $zRooms;
                } elseif ($zChildren == 1) {
                    // Familty Room 1
                    $raw = $raw . '<OCCUPANCY>7</OCCUPANCY>';
                    $selectedOCCUPANCY[7] = $zRooms;
                } else {
                    // Familty Room 2
                    $raw = $raw . '<OCCUPANCY>8</OCCUPANCY>';
                    $selectedOCCUPANCY[8] = $zRooms;
                }
                break;
            case 3:
                // Tripple
                $raw = $raw . '<OCCUPANCY>4</OCCUPANCY>';
                $selectedOCCUPANCY[4] = $zRooms;
                break;
            case 4:
                // Quad
                $raw = $raw . '<OCCUPANCY>5</OCCUPANCY>';
                $selectedOCCUPANCY[5] = $zRooms;
                break;
            default:
                // Twin
                $raw = $raw . '<OCCUPANCY>2</OCCUPANCY>';
                $OCCUPANCYHandler = 2;
                $selectedOCCUPANCY[2] = $zRooms;
                $selectedOCCUPANCY[3] = $zRooms;
                break;
        }
        $raw = $raw . '<QUANTITY>1</QUANTITY>';
        if ($zChildren > 0) {
            $raw = $raw . '<CHILDREN>';
            for ($z = 0; $z < $zChildren; $z ++) {
                $raw = $raw . '  <CHILD_RATE CHILD_QUANTITY="1" CHILD_AGE="' . $selectedChildrenAges[$zRooms][$z] . '"/>';
            }
            $raw = $raw . '</CHILDREN>';
        }
        $raw = $raw . '</ROOM>';
    }
    if ($OCCUPANCYHandler == 2) {
        // Add Double
        $raw = $raw . '<ROOM>';
        $raw = $raw . '<OCCUPANCY>3</OCCUPANCY>';
        $raw = $raw . '<QUANTITY>1</QUANTITY>';
        $raw = $raw . '</ROOM>';
    }
    $raw = $raw . '</ROOMS_REQUIRED>';
    $raw = $raw . '</SERVICE_SEARCH_REQUEST>';
    
    if ($JacTravelSearchServiceURLServiceSearch != "" and $JacTravelClientName != "" and $JacTravelKey != "") {
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $JacTravelSearchServiceURLServiceSearch);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-type: text/xml;charset=UTF-8",
            "Content-Encoding: UTF-8",
            "Content-length: " . strlen($raw)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        // if ($response === false) {
        // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
        // } else {
        // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
        // }
        $response = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $response);
        //error_log("\r\n RESPONSE3: $xml_string \r\n", 3, "/srv/www/htdocs/error_log");
        curl_close($ch);
        // Descomentar para ver o resultado do provider
        // Nao esquecer de alterar o session id para testar por causa de cache
        // echo $response;
        // die();
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_jactravel');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $JacTravelSearchServiceURLServiceSearch . $raw,
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
        //error_log("\r\n ANTES PARSE SEGUNDA TARDE \r\n", 3, "/srv/www/htdocs/error_log");
        // echo $response;
        // die();
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $responseElement = $inputDoc->documentElement;
        $xpath = new DOMXPath($inputDoc);
        $searchHotelPriceReponseElements = "";
        $searchHotelPriceReponseElements = $xpath->query('/SERVICE_SEARCH_RESPONSE/SERVICES/SERVICE', $responseElement);
        foreach ($searchHotelPriceReponseElements as $searchHotelPriceReponseElement) {
            $counter_details = 0;
            $OPTION_NAME = "";
            $CURRENCY_CODE = $searchHotelPriceReponseElement->getAttribute("CURRENCY");
            $STARTING_PRICE = $searchHotelPriceReponseElement->getAttribute("STARTING_PRICE");
            $ISRECOMMENDEDPRODUCT = $searchHotelPriceReponseElement->getAttribute("ISRECOMMENDEDPRODUCT");
            $AVAILABLE = $searchHotelPriceReponseElement->getAttribute("AVAILABLE");
            $RATING = $searchHotelPriceReponseElement->getAttribute("RATING");
            $LOCATION = $searchHotelPriceReponseElement->getAttribute("LOCATION");
            $SERVICE_NAME = $searchHotelPriceReponseElement->getAttribute("SERVICE_NAME");
            $SERVICE_ID = $searchHotelPriceReponseElement->getAttribute("SERVICE_ID");
            $shid = $SERVICE_ID;
            $sfilter[] = " sid='$SERVICE_ID' ";
            //error_log("\r\n SERVICE_ID $SERVICE_ID \r\n", 3, "/srv/www/htdocs/error_log");
            $OPTIONS = $searchHotelPriceReponseElement->getElementsByTagName("OPTIONS");
            $node = $OPTIONS->item(0)->getElementsByTagName("OPTION");
            //error_log("\r\n node $node->length \r\n", 3, "/srv/www/htdocs/error_log");
            for ($i=0; $i < $node->length; $i++) { 
                $OPTIONID = $node->item($i)->getElementsByTagName("OPTIONID");
                if ($OPTIONID->length > 0) {
                    $OPTIONID = $OPTIONID->item(0)->nodeValue;
                } else {
                    $OPTIONID = 0;
                }
                //error_log("\r\n OPTIONID3 $OPTIONID \r\n", 3, "/srv/www/htdocs/error_log");
                $OPTION_NAME = $node->item($i)->getElementsByTagName("OPTION_NAME");
                if ($OPTION_NAME->length > 0) {
                    $OPTION_NAME = $OPTION_NAME->item(0)->nodeValue;
                } else {
                    $OPTION_NAME = "";
                }
                //error_log("\r\n OPTION_NAME $OPTION_NAME \r\n", 3, "/srv/www/htdocs/error_log");
                $MinAdult = $node->item($i)->getElementsByTagName("MinAdult");
                if ($MinAdult->length > 0) {
                    $MinAdult = $MinAdult->item(0)->nodeValue;
                } else {
                    $MinAdult = 0;
                }
                //error_log("\r\n MinAdult $MinAdult \r\n", 3, "/srv/www/htdocs/error_log");
                $MaxAdult = $node->item($i)->getElementsByTagName("MaxAdult");
                if ($MaxAdult->length > 0) {
                    $MaxAdult = $MaxAdult->item(0)->nodeValue;
                } else {
                    $MaxAdult = 0;
                }
                $MinChild = $node->item($i)->getElementsByTagName("MinChild");
                if ($MinChild->length > 0) {
                    $MinChild = $MinChild->item(0)->nodeValue;
                } else {
                    $MinChild = 0;
                }
                $MaxChild = $node->item($i)->getElementsByTagName("MaxChild");
                if ($MaxChild->length > 0) {
                    $MaxChild = $MaxChild->item(0)->nodeValue;
                } else {
                    $MaxChild = 0;
                }
                $ChildMaxAge = $node->item($i)->getElementsByTagName("ChildMaxAge");
                if ($ChildMaxAge->length > 0) {
                    $ChildMaxAge = $ChildMaxAge->item(0)->nodeValue;
                } else {
                    $ChildMaxAge = 0;
                }
                //error_log("\r\n ChildMaxAge $ChildMaxAge \r\n", 3, "/srv/www/htdocs/error_log");
                $OCCUPANCY = $node->item($i)->getElementsByTagName("OCCUPANCY");
                if ($OCCUPANCY->length > 0) {
                    $OCCUPANCY = $OCCUPANCY->item(0)->nodeValue;
                } else {
                    $OCCUPANCY = 0;
                }
                //error_log("\r\n OCCUPANCY $OCCUPANCY \r\n", 3, "/srv/www/htdocs/error_log");
                $OPTION_STATUS = $node->item($i)->getElementsByTagName("OPTION_STATUS");
                if ($OPTION_STATUS->length > 0) {
                    $OPTION_STATUS = $OPTION_STATUS->item(0)->nodeValue;
                } else {
                    $OPTION_STATUS = "";
                }
                //error_log("\r\n OPTION_STATUS $OPTION_STATUS \r\n", 3, "/srv/www/htdocs/error_log");
                $RULE_TEXT = $node->item($i)->getElementsByTagName("RULE_TEXT");
                if ($RULE_TEXT->length > 0) {
                    $RULE_TEXT = $RULE_TEXT->item(0)->nodeValue;
                } else {
                    $RULE_TEXT = "";
                }

                //PRICES
                $PRICES = $node->item($i)->getElementsByTagName("PRICES");
                $node2 = $PRICES->item(0)->getElementsByTagName("PRICE");
                for ($j=0; $j < $node2->length; $j++) { 
                    $PRICE_DATE = $node2->item($j)->getElementsByTagName("PRICE_DATE");
                    if ($PRICE_DATE->length > 0) {
                        $PRICE_DATE = $PRICE_DATE->item(0)->nodeValue;
                    } else {
                        $PRICE_DATE = "";
                    }
                    $SELL_PRICE_ID = $node2->item($j)->getElementsByTagName("SELL_PRICE_ID");
                    if ($SELL_PRICE_ID->length > 0) {
                        $SELL_PRICE_ID = $SELL_PRICE_ID->item(0)->nodeValue;
                    } else {
                        $SELL_PRICE_ID = 0;
                    }
                    $SELL_PRICE_AMOUNT = $node2->item($j)->getElementsByTagName("SELL_PRICE_AMOUNT");
                    if ($SELL_PRICE_AMOUNT->length > 0) {
                        $SELL_PRICE_AMOUNT = $SELL_PRICE_AMOUNT->item(0)->nodeValue;
                    } else {
                        $SELL_PRICE_AMOUNT = 0;
                    }
                    $SELL_CURRENCY_CODE = $node2->item($j)->getElementsByTagName("SELL_CURRENCY_CODE");
                    if ($SELL_CURRENCY_CODE->length > 0) {
                        $SELL_CURRENCY_CODE = $SELL_CURRENCY_CODE->item(0)->nodeValue;
                    } else {
                        $SELL_CURRENCY_CODE = "";
                    }
                    $ORIGNAL_SELL_PRICE_AMOUNT = $node2->item($j)->getElementsByTagName("ORIGNAL_SELL_PRICE_AMOUNT");
                    if ($ORIGNAL_SELL_PRICE_AMOUNT->length > 0) {
                        $ORIGNAL_SELL_PRICE_AMOUNT = $ORIGNAL_SELL_PRICE_AMOUNT->item(0)->nodeValue;
                    } else {
                        $ORIGNAL_SELL_PRICE_AMOUNT = 0;
                    }
                    //error_log("\r\n ORIGNAL_SELL_PRICE_AMOUNT $ORIGNAL_SELL_PRICE_AMOUNT \r\n", 3, "/srv/www/htdocs/error_log");
                    $MEAL_PLAN = $node2->item($j)->getElementsByTagName("MEAL_PLAN");
                    $MEAL_PLAN_TEXT = $MEAL_PLAN->item(0)->getElementsByTagName("MEAL_PLAN_TEXT");
                    if ($MEAL_PLAN_TEXT->length > 0) {
                        $MEAL_PLAN_TEXT = $MEAL_PLAN_TEXT->item(0)->nodeValue;
                    } else {
                        $MEAL_PLAN_TEXT = 0;
                    }
                    $MEAL_PLAN_TYPE = $MEAL_PLAN->item(0)->getElementsByTagName("MEAL_PLAN_TYPE");
                    $INCLUDESBREAKFAST = $MEAL_PLAN_TYPE->item(0)->getElementsByTagName("INCLUDESBREAKFAST");
                    if ($INCLUDESBREAKFAST->length > 0) {
                        $INCLUDESBREAKFAST = $INCLUDESBREAKFAST->item(0)->nodeValue;
                    } else {
                        $INCLUDESBREAKFAST = 0;
                    }
                    $INCLUDESLUNCH = $MEAL_PLAN_TYPE->item(0)->getElementsByTagName("INCLUDESLUNCH");
                    if ($INCLUDESLUNCH->length > 0) {
                        $INCLUDESLUNCH = $INCLUDESLUNCH->item(0)->nodeValue;
                    } else {
                        $INCLUDESLUNCH = 0;
                    }
                    $INCLUDESDINNER = $MEAL_PLAN_TYPE->item(0)->getElementsByTagName("INCLUDESDINNER");
                    if ($INCLUDESDINNER->length > 0) {
                        $INCLUDESDINNER = $INCLUDESDINNER->item(0)->nodeValue;
                    } else {
                        $INCLUDESDINNER = 0;
                    }
                    //error_log("\r\n INCLUDESDINNER $INCLUDESDINNER \r\n", 3, "/srv/www/htdocs/error_log");

                    for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                        if (is_array($tmp[$shid])) {
                            $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                        } else {
                            $baseCounterDetails = 0;
                        }
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $SERVICE_NAME;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $SERVICE_ID;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-22";
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $OPTION_NAME;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $OPTIONID;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $OPTION_NAME;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RateCode'] = $SELL_PRICE_ID;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $SELL_PRICE_AMOUNT;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $SELL_PRICE_AMOUNT;
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = $ISRECOMMENDEDPRODUCT;
                        if ($RULE_TEXT != "") {
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $RULE_TEXT;
                        } else {
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                        }
                                
                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($MEAL_PLAN_TEXT);
                        $pricebreakdown = array();
                        $pricebreakdownCount = 0;
                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                            $amount = $noOfNights * $SELL_PRICE_AMOUNT;
                            if ($JacTravelMarkup != 0) {
                                $amount = $amount + (($amount * $JacTravelMarkup) / 100);
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
                            if ($JacTravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                    }
                }
            }
        }
        $jactravel = true;
    }
}
 //error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($jactravel == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mtotalstay where " . $sfilter;
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
        $supplier = 22;
        error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_jactravel');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_jactravel');
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