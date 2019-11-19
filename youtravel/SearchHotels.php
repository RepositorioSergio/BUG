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
$youtravel = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
error_log("\r\n COMECOU YOUTRAVEL \r\n", 3, "/srv/www/htdocs/error_log");
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

$fromdate = date('d/m/Y', $from );
$countrooms = 1;

$url = 'http://testxml.youtravel.com/webservicestest/index.asp?Dstn=FAO&LangID=EN&Username=xmltestme&Password=testme&Nights=2&Checkin_Date=10/12/2019&Rooms=2&ADLTS_1=1&ADLTS_2=2&BT=1&SBT=1';
/* if ($rooms > 1) {
    for ($r=0; $r < count($selectedAdults); $r++) { 
        $url = $url . '&ADLTS_' . $countrooms . '=' . $selectedAdults[$r];
        $countrooms = $countrooms + 1;
    }
} else if ($rooms == 1 and $rooms > 0) {
    for ($r=0; $r < count($selectedAdults); $r++) { 
        $url = $url . '&ADLTS_1=' . $selectedAdults[$r];
    }
} */

//$url = $url . '&ADLTS_1=2&ADLTS_2=2&BT=1&SBT=1';
error_log("\r\n url YOUTRAVEL: $url \r\n", 3, "/srv/www/htdocs/error_log");
if ($url != "") {

    $headers = array(
        'Content-Type: text/xml;charset=ISO-8859-1',
        'Content-Length: 0'
    ); 

    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING , "gzip");
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    error_log("\r\n Response YOUTRAVEL: $response \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_youtravel');
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

    $roomNumber = "";
    $number = 0;

    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $HtSearchRq = $inputDoc->getElementsByTagName("HtSearchRq");
    $session = $HtSearchRq->item(0)->getElementsByTagName("session");
    if ($session->length > 0) {
        $id = $session->item(0)->getAttribute("id");
        $Currency = $session->item(0)->getElementsByTagName("Currency");
        if ($Currency->length > 0) {
            $Currency = $Currency->item(0)->nodeValue;
        } else {
            $Currency = "";
        }

        $Hotel = $session->item(0)->getElementsByTagName("Hotel");
        if ($Hotel->length > 0) {
            for ($i=0; $i < $Hotel->length; $i++) { 
                $ID = $Hotel->item($i)->getAttribute("ID");
                $shid = $ID;
                $sfilter[] = " sid='$ID' ";
                $Hotel_Name = $Hotel->item($i)->getElementsByTagName("Hotel_Name");
                if ($Hotel_Name->length > 0) {
                    $Hotel_Name = $Hotel_Name->item(0)->nodeValue;
                } else {
                    $Hotel_Name = "";
                }
                $Youtravel_Rating = $Hotel->item($i)->getElementsByTagName("Youtravel_Rating");
                if ($Youtravel_Rating->length > 0) {
                    $Youtravel_Rating = $Youtravel_Rating->item(0)->nodeValue;
                } else {
                    $Youtravel_Rating = "";
                }
                $Official_Rating = $Hotel->item($i)->getElementsByTagName("Official_Rating");
                if ($Official_Rating->length > 0) {
                    $Official_Rating = $Official_Rating->item(0)->nodeValue;
                } else {
                    $Official_Rating = "";
                }
                $Board_Type = $Hotel->item($i)->getElementsByTagName("Board_Type");
                if ($Board_Type->length > 0) {
                    $Board_Type = $Board_Type->item(0)->nodeValue;
                } else {
                    $Board_Type = "";
                }
                $Child_Age = $Hotel->item($i)->getElementsByTagName("Child_Age");
                if ($Child_Age->length > 0) {
                    $Child_Age = $Child_Age->item(0)->nodeValue;
                } else {
                    $Child_Age = "";
                }
                $Country = $Hotel->item($i)->getElementsByTagName("Country");
                if ($Country->length > 0) {
                    $Country = $Country->item(0)->nodeValue;
                } else {
                    $Country = "";
                }
                $Destination = $Hotel->item($i)->getElementsByTagName("Destination");
                if ($Hotel_Name->length > 0) {
                    $Destination = $Destination->item(0)->nodeValue;
                } else {
                    $Destination = "";
                }
                $Resort = $Hotel->item($i)->getElementsByTagName("Resort");
                if ($Resort->length > 0) {
                    $Resort = $Resort->item(0)->nodeValue;
                } else {
                    $Resort = "";
                }
                $Image = $Hotel->item($i)->getElementsByTagName("Image");
                if ($Image->length > 0) {
                    $Image = $Image->item(0)->nodeValue;
                } else {
                    $Image = "";
                }
                $Hotel_Desc = $Hotel->item($i)->getElementsByTagName("Hotel_Desc");
                if ($Hotel_Desc->length > 0) {
                    $Hotel_Desc = $Hotel_Desc->item(0)->nodeValue;
                } else {
                    $Hotel_Desc = "";
                }

                for ($x=0; $x < $rooms; $x++) { 
                    $number = $x + 1;
                    $roomNumber = $Hotel->item($x)->getElementsByTagName("Room_" . $number);
                    if ($roomNumber->length > 0) {
                        $Passengers = $roomNumber->item(0)->getElementsByTagName("Passengers");
                        if ($Passengers->length > 0) {
                            $Adults = $Passengers->item(0)->getAttribute("Adults");
                            $Children = $Passengers->item(0)->getAttribute("Children");
                            $Infants = $Passengers->item(0)->getAttribute("Infants");
                        }

                        $Room = $roomNumber->item(0)->getElementsByTagName("Room");
                        if ($Room->length > 0) {
                            for ($j=0; $j < $Room->length; $j++) { 
                                $RoomId = $Room->item($j)->getAttribute("Id");
                                $ADV = $Room->item($j)->getAttribute("ADV");
                                $Refundable = $Room->item($j)->getAttribute("Refundable");
                                $Type = $Room->item($j)->getElementsByTagName("Type");
                                if ($Type->length > 0) {
                                    $Type = $Type->item(0)->nodeValue;
                                } else {
                                    $Type = "";
                                }
                                $Board = $Room->item($j)->getElementsByTagName("Board");
                                if ($Board->length > 0) {
                                    $Board = $Board->item(0)->nodeValue;
                                } else {
                                    $Board = "";
                                }
                                $Rates = $Room->item($j)->getElementsByTagName("Rates");
                                if ($Rates->length > 0) {
                                    $Final_Rate = $Rates->item(0)->getAttribute("Final_Rate");
                                    $Original_Rate = $Rates->item(0)->getAttribute("Original_Rate");
                                } else {
                                    $Final_Rate = "";
                                    $Original_Rate = "";
                                }
                                $Offers = $Room->item($j)->getElementsByTagName("Offers");
                                if ($Offers->length > 0) {
                                    $Gala_Meals = $Offers->item(0)->getAttribute("Gala_Meals");
                                    $Free_Transfer = $Offers->item(0)->getAttribute("Free_Transfer");
                                    $Free_Stay = $Offers->item(0)->getAttribute("Free_Stay");
                                    $Early_Booking_Discount = $Offers->item(0)->getAttribute("Early_Booking_Discount");
                                    $Lastminute_Offer = $Offers->item(0)->getAttribute("Lastminute_Offer");
                                } else {
                                    $Gala_Meals = "";
                                    $Free_Transfer = "";
                                    $Free_Stay = "";
                                    $Early_Booking_Discount = "";
                                    $Lastminute_Offer = "";
                                }



                                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                                    if (is_array($tmp[$shid])) {
                                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                    } else {
                                        $baseCounterDetails = 0;
                                    }
                                    
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $Hotel_Name;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $ID;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $RoomId;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $ID;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $shid;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                    // cancellationType nao existe
                                    // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $c_type;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-34";
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $Type;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $Type;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $Type;
                                    //$tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rate_code'] = $rate;
                                    //$tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ratePlanscode'] = $ratePlanscode;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['Refundable'] = $Refundable;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $Original_Rate;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $Final_Rate;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($Board);
                                    $pricebreakdown = array();
                                    $pricebreakdownCount = 0;
                                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                        $amount = $Original_Rate / $noOfNights;
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
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $Currency;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";

                                    /*
                                        * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $CancelCost;
                                        * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $DeadLineCancel;
                                        */
                                    $count = $count + 1;
                                }
                            }
                        }
                    }
                }
            }
            $youtravel = true;
        }
    }
}

if ($youtravel == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_myoutravel where " . $sfilter;
       
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
        $supplier = 34;
        
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_youtravel');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_youtravel');
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