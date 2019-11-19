<?php
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
use Zend\Http\Client;
use Zend\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$total = 0;
error_log("\r\n COMECOU POLICIES \r\n", 3, "/srv/www/htdocs/error_log");
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_youtravel where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $lang = $searchsettings['lang'];
    $currency = $searchsettings['currency'];
    $from = $searchsettings['from'];
    $to = $searchsettings['to'];
    $destination = $searchsettings['destination'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    $index = $searchsettings['index'];
    $ipaddress = $searchsettings['ipaddress'];
    $nationality = $searchsettings['nationality'];
    error_log("\r\n nationality  $nationality \r\n", 3, "/srv/www/htdocs/error_log");
    $residency = $searchsettings['residency'];
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}

$affiliate_id = 0;
$sql = "select value from settings where name='enableroomer' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_roomer = $affiliate_id;
} else {
    $affiliate_id_roomer = 0;
}
/* $sql = "select value from settings where name='rtsID' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsID = $row_settings['value'];
}
$sql = "select value from settings where name='rtsPassword' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='rtsSiteCode' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsSiteCode = $row_settings['value'];
}
$sql = "select value from settings where name='rtsRequestType' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsRequestType = $row_settings['value'];
}
$sql = "select value from settings where name='rtsServiceURL' and affiliate_id=$affiliate_id_rts" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsServiceURL = $row['value'];
}
error_log("\r\n rtsServiceURL  $rtsServiceURL  \r\n", 3, "/srv/www/htdocs/error_log"); */

$breakdown = array();
for ($w = 0; $w < count($quoteid); $w ++) {
    $outputArray = array();
    $arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
    foreach ($arrIt as $sub) {
        $subArray = $arrIt->getSubIterator();
        if (isset($quoteid[$w])) {
            if (isset($subArray['quoteid'])) {
                if ($subArray['quoteid'] === $quoteid[$w]) {
                    $outputArray[] = iterator_to_array($subArray);
                    $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                        ->key();
                }
            }
        }
    }
    if (! is_array($outputArray)) {
        $response['error'] = "Unable to handle request #3";
        return false;
    } else {
        array_push($breakdown, $outputArray);
    }
}


$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');


$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['hotelid'];
            $scode = $value['shid'];
            $hotel_code = $value['shid'];
            $room_code = $value['roomid'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $item = array();
        $cancelation_deadline = 0;
        $cancelation_details = "";

        $url = 'http://testxml.youtravel.com/webservicestest/index.asp?Dstn=FAO&LangID=EN&Username=xmltestme&Password=testme&Nights=2&Checkin_Date=10/12/2019&Rooms=1&ADLTS_1=2&BT=1&YTS=1&CanxPol=1';

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
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        $endTime = microtime();
        error_log("\r\n Response2 YOUTRAVEL: $response2 \r\n", 3, "/srv/www/htdocs/error_log");

        $roomNumber = "";
        $number = 0;

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response2);
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
                    if ($ID == $hotel_code) {
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
                                        if ($RoomId == $room_code) {
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
                                            $CanxPolicy = $Room->item($j)->getElementsByTagName("CanxPolicy");
                                            if ($CanxPolicy->length > 0) {
                                                $token = $CanxPolicy->item(0)->getAttribute("token");
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
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $url2 = 'http://xml.youtravel.com/webservices/get_canx_policy.asp?token=' . $token;

        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING , "gzip");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response3 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        $endTime = microtime();
        error_log("\r\n Response YOUTRAVEL: $response3 \r\n", 3, "/srv/www/htdocs/error_log");


        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response3);
        $HtSearchRq = $inputDoc->getElementsByTagName("HtSearchRq");

        $Room = $HtSearchRq->item(0)->getElementsByTagName("Room");
        if ($Room->length > 0) {
            $Room = $Room->item(0)->nodeValue;
        } else {
            $Room = "";
        }

        $Policies = $HtSearchRq->item(0)->getElementsByTagName("Policies");
        if ($Policies->length > 0) {
            $Policy = $Policies->item(0)->getElementsByTagName("Policy");
            if ($Policy->length > 0) {
                for ($i=0; $i < $Policy->length; $i++) { 
                    $FromDate = $Policy->item($i)->getElementsByTagName("FromDate");
                    if ($FromDate->length > 0) {
                        $FromDate = $FromDate->item(0)->nodeValue;
                    } else {
                        $FromDate = "";
                    }
                    $Fees = $Policy->item($i)->getElementsByTagName("Fees");
                    if ($Fees->length > 0) {
                        $Fees = $Fees->item(0)->nodeValue;
                    } else {
                        $Fees = "";
                    }
                    $Currency = $Policy->item($i)->getElementsByTagName("Currency");
                    if ($Currency->length > 0) {
                        $Currency = $Currency->item(0)->nodeValue;
                    } else {
                        $Currency = "";
                    }
                }
            }
        }

        //
        // Policies
        //
        $item['code'] = $value['shid'];
        //$item['name'] = $hotelName;
        $item['total'] = $value['total'];
        $item['nett'] = $value['nettotal'];
        $total = $total + $value['total'];
        $tot = $value['total'];
        error_log("\r\n TOTAL $tot \r\n", 3, "/srv/www/htdocs/error_log");
        $item['room'] = $value['room'];
        $item['RoomType'] = $value['room_type'];
        $item['RoomDescription'] = $value['room_description'];
        $item['meal'] = $Board;
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        error_log("\r\n AVG  $avg \r\n", 3, "/srv/www/htdocs/error_log");
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        

        //$newDate = date("d-m-Y", strtotime($upto_date));

        $cancelation_details = "If you cancel after " . $FromDate . " cost " . $Fees . " " . $Currency . ".";
        $cancelation_deadline = $FromDate;
        error_log("\r\n ENTROU \r\n", 3, "/srv/www/htdocs/error_log");
        $item['cancelpolicy'] = $cancelation_details;
        $item['cancelpolicy_deadline'] = $cancelation_deadline;
        
        /* $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        $item['cancelpolicy_details'] = $cancelation_details; */
         
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_myoutravel where sid='" . $shid . "' and hid=" . $hid;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $db2 = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db2->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $db2->getDriver()
        ->getConnection()
        ->disconnect();
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$images = array();
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    if ($result instanceof ResultInterface && $result->isQueryResult()) {
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        foreach ($resultSet as $row) {
            $item = array();
            $item['url'] = "//world-wide-web-servers.com/static/hotels/" . $row->url;
            $item['description'] = $row->description;
            array_push($images, $item);
        }
    }
    $db->getDriver()
        ->getConnection()
        ->disconnect();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
$response['code'] = $vector['code'];
?>