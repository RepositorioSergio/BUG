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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_sunhotels where session_id='$session_id'";
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
error_log("\r\n COMECA ENABLE \r\n", 3, "/srv/www/htdocs/error_log");
$affiliate_id = 0;
$sql = "select value from settings where name='enableroomer' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_sunhotels = $affiliate_id;
} else {
    $affiliate_id_sunhotels = 0;
}

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


/*
 * $fromHotelsPRO = $fromHotelsPRO->getTimestamp();
 * $toHotelsPro = $toHotelsPro->getTimestamp();
 */
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

        $pricetotal = (int)$value['total'];
        $mealid = $value['mealid'];
        
        $url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx?op=PreBookV2";

        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <PreBookV2 xmlns="http://xml.sunhotels.net/15/">
            <userName>testagent</userName>
            <password>785623</password>
            <currency>USD</currency>
            <language>en</language>
            <checkInDate>2019-12-12</checkInDate>
            <checkOutDate>2019-12-14</checkOutDate>
            <rooms>1</rooms>
            <adults>2</adults>
            <children>0</children>
            <childrenAges></childrenAges>
            <infant>0</infant>
            <mealId>' . $mealid . '</mealId>
            <customerCountry>gb</customerCountry>
            <b2c>0</b2c>
            <searchPrice>' . $pricetotal . '</searchPrice>
            <roomId>' . $room_code . '</roomId>
            <hotelId></hotelId>
            <roomtypeId></roomtypeId>
            <blockSuperDeal></blockSuperDeal>
            <showPriceBreakdown>true</showPriceBreakdown>
            </PreBookV2>
        </soap:Body>
        </soap:Envelope>';

        $headers = array(
            'Accept-Encoding: gzip,deflate',
            'Host: xml.sunhotels.net',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: http://xml.sunhotels.net/15/PreBookV2',
            'Content-Length: ' . strlen($raw)
        ); 

        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING , "gzip,deflate");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        // Descomentar para ver o resultado do provider
        // Nao esquecer de alterar o session id para testar por causa de cache
        // echo $response;
        // die();
        $endTime = microtime();
        error_log("\r\n Response: $response2 \r\n", 3, "/srv/www/htdocs/error_log");

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response2);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");

        $PreBookV2Response = $Body->item(0)->getElementsByTagName("PreBookV2Response");
        if ($PreBookV2Response->length > 0) {
            $preBookResult = $PreBookV2Response->item(0)->getElementsByTagName("preBookResult");
            if ($preBookResult->length > 0) {
                $PreBookCode = $preBookResult->item(0)->getElementsByTagName("PreBookCode");
                if ($PreBookCode->length > 0) {
                    $PreBookCode = $PreBookCode->item(0)->nodeValue;
                } else {
                    $PreBookCode = "";
                }
                error_log("\r\n PreBookCode: $PreBookCode \r\n", 3, "/srv/www/htdocs/error_log");
                $price2 = 0;
                $Price = $preBookResult->item(0)->getElementsByTagName("Price");
                if ($Price->length > 0) {
                    $currency = $Price->item(0)->getAttribute("currency");
                    $price2 = $Price->item(0)->nodeValue;
                } else {
                    $currency = "";
                    $price2 = "";
                }
                //Notes
                $Notes = $preBookResult->item(0)->getElementsByTagName("Notes");
                if ($Notes->length > 0) {
                    $Note = $Notes->item(0)->getElementsByTagName("Note");
                    if ($Note->length > 0) {
                        for ($i=0; $i < $Note->length; $i++) { 
                            $end_date = $Note->item($i)->getAttribute("end_date");
                            $start_date = $Note->item($i)->getAttribute("start_date");
                            $text = $Note->item($i)->getElementsByTagName("text");
                            if ($text->length > 0) {
                                $text = $text->item(0)->nodeValue;
                            } else {
                                $text = "";
                            }
                        }
                    }
                }
                //PriceBreakdown
                $PriceBreakdown = $preBookResult->item(0)->getElementsByTagName("PriceBreakdown");
                if ($PriceBreakdown->length > 0) {
                    $currency = $PriceBreakdown->item(0)->getAttribute("currency");
                    $to = $PriceBreakdown->item(0)->getAttribute("to");
                    $from = $PriceBreakdown->item(0)->getAttribute("from");
                    $total = $PriceBreakdown->item(0)->getAttribute("total");
                    $guest = $PriceBreakdown->item(0)->getElementsByTagName("guest");
                    if ($guest->length > 0) {
                        for ($j=0; $j < $guest->length; $j++) { 
                            $guesttotal = $guest->item($j)->getAttribute("total");
                            $price = $guest->item($j)->getElementsByTagName("price");
                            if ($price->length > 0) {
                                $value = $price->item(0)->getAttribute("value");
                                $type = $price->item(0)->getAttribute("type");
                                $breakdown = $price->item(0)->getAttribute("breakdown");
                            } else {
                                $value = "";
                                $type = "";
                                $breakdown = "";
                            }
                            
                        }
                    }
                } else {
                    $currency = "";
                    $total = "";
                    $from = "";
                    $to = "";
                }
                //CancellationPolicies
                $CancellationPolicies = $preBookResult->item(0)->getElementsByTagName("CancellationPolicies");
                if ($CancellationPolicies->length > 0) {
                    $CancellationPolicy = $CancellationPolicies->item(0)->getElementsByTagName("CancellationPolicy");
                    if ($CancellationPolicy->length > 0) {
                        $deadline = $CancellationPolicy->item(0)->getElementsByTagName("deadline");
                        if ($deadline->length > 0) {
                            $deadline = $deadline->item(0)->nodeValue;
                        } else {
                            $deadline = "";
                        }
                        $percentage = $CancellationPolicy->item(0)->getElementsByTagName("percentage");
                        if ($percentage->length > 0) {
                            $percentage = $percentage->item(0)->nodeValue;
                        } else {
                            $percentage = "";
                        }
                        $text = $CancellationPolicy->item(0)->getElementsByTagName("text");
                        if ($text->length > 0) {
                            $text = $text->item(0)->nodeValue;
                        } else {
                            $text = "";
                        }
                    }
                }
            }
        }


        //
        // Policies
        //
        $item['code'] = $value['shid'];
        //$item['name'] = $value['name'];
        $item['total'] = $price2;
        $item['nett'] = $price2;
        $total = $total + $price2;
        $tot = $price2;
        $item['room'] = $value['room'];
        $item['RoomTypeCode'] = $value['RoomTypeCode'];
        $item['RoomType'] = $value['RoomTypeCode'];
        //$item['RoomDescription'] = $value['room_description'];
        $item['meal'] = $value['meal'];
        $item['total'] = $price2;
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        
        if ($deadline != "") {
            $cancelation_details = $text;
            $cancelation_deadline = $deadline . 'days';
           
            $item['cancelpolicy'] = $cancelation_details;
            $item['cancelpolicy_deadline'] = $cancelation_deadline;
        } else {
            $cancelation_details = $text;
            $cancelation_deadline = $from;
           
            $item['cancelpolicy'] = $cancelation_details;
            $item['cancelpolicy_deadline'] = $cancelation_deadline;
        }
         
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_msunhotels where sid='" . $shid . "' and hid=" . $hid;
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