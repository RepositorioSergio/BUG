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
error_log("\r\n COMECA POLICIES \r\n", 3, "/srv/www/htdocs/error_log");
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_abreu where session_id='$session_id'";
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
$sql = "select value from settings where name='enableabreu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_abreu = $affiliate_id;
} else {
    $affiliate_id_abreu = 0;
}

$sql = "select value from settings where name='AbreuUsername' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuUsername = $row_settings['value'];
}
$sql = "select value from settings where name='Abreupassword' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreupassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='AbreuMarkup' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuMarkup = (double) $row_settings['value'];
} else {
    $AbreuMarkup = 0;
}
//URL
$sql = "select value from settings where name='AbreuHOTELAVAILABILITY' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuHOTELAVAILABILITY = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuContext' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuContext = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuCustomerID' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuCustomerID = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuOnRequest' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuOnRequest = $row_settings['value'];
}
$sql = "select value from settings where name='Abreub2cMarkup' and affiliate_id=$affiliate_id_abreu" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreub2cMarkup = $row_settings['value'];
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

$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['HotelId'];
            $scode = $value['shid'];
            $hotel_code = $value['shid'];
            $hotelName = $value['name'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }

        $roomid = $value['roomid'];
        $meal = $value['MealPlan'];

        $item = array();
        $cancelation_deadline = 0;
        $cancelation_details = "";
        error_log("\r\n ANTES CURL $meal \r\n", 3, "/srv/www/htdocs/error_log");
        $from = date("Y-m-d", strtotime($from));
        $to = date("Y-m-d", strtotime($to));

        $raw2 = '<?xml version="1.0" encoding="utf-8"?><soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/"><soap-env:Header><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><wsse:Username>' . $AbreuUsername . '</wsse:Username><wsse:Password>' . $Abreupassword . '</wsse:Password><Context>' . $AbreuContext . '</Context></wsse:Security></soap-env:Header><soap-env:Body><OTA_HotelAvailRQ xmlns=" http://parsec.es/hotelapi/OTA2014Compact" ><HotelSearch><Currency Code="USD" /><DateRange Start="' . $from . '" End="' . $to . '" /><GuestCountry Code="ES"/><RoomCandidates><RoomCandidate RPH="1" RoomTypeCode="' . $roomid . '"><Guests><Guest AgeCode="A" Count="2" /></Guests></RoomCandidate></RoomCandidates></HotelSearch></OTA_HotelAvailRQ></soap-env:Body></soap-env:Envelope>';

        error_log("\r\n Request: $raw2 \r\n", 3, "/srv/www/htdocs/error_log");

        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $AbreuHOTELAVAILABILITY);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/xml",
            "Content-type: text/xml",
            "Content-length: " . strlen($raw2)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        // if ($response === false) {
        // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
        // } else {
        // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
        // }
        error_log("\r\n RESPONSE ABREU: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        curl_close($ch);

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_abreu');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $$AbreuHOTELAVAILABILITY . $raw2,
                'sqlcontext' => $response2,
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

        $cancel = array();
        $count = 0;

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response2);
        $OTA_HotelAvailRS = $inputDoc->getElementsByTagName('OTA_HotelAvailRS');

        $Hotels = $OTA_HotelAvailRS->item(0)->getElementsByTagName('Hotels');
        if ($Hotels->length > 0) {
            $Hotel = $Hotels->item(0)->getElementsByTagName('Hotel');
            if ($Hotel->length > 0) {
                for ($i=0; $i < $Hotel->length; $i++) { 
                    $Info = $Hotel->item($i)->getElementsByTagName('Info');
                    if ($Info->length > 0) {
                        $Recommended = $Info->item(0)->getAttribute('Recommended');
                        $MasterCode = $Info->item(0)->getAttribute('MasterCode');
                        $Rating = $Info->item(0)->getAttribute('Rating');
                        $HotelCityCode = $Info->item(0)->getAttribute('HotelCityCode');
                        $HotelName = $Info->item(0)->getAttribute('HotelName');
                        $HotelCode = $Info->item(0)->getAttribute('HotelCode');
                    }

                    $BestPrice = $Hotel->item($i)->getElementsByTagName('BestPrice');
                    if ($BestPrice->length > 0) {
                        $Currency = $BestPrice->item(0)->getAttribute('Currency');
                        $Amount = $BestPrice->item(0)->getAttribute('Amount');
                    }

                    $Rooms = $Hotel->item($i)->getElementsByTagName('Rooms');
                    if ($Rooms->length > 0) {
                        $Room = $Rooms->item(0)->getElementsByTagName('Room');
                        if ($Room->length > 0) {
                            for ($j=0; $j < $Room->length; $j++) { 
                                $RPH = $Room->item($j)->getAttribute('RPH');
                                $Status = $Room->item($j)->getAttribute('Status');
                                $Best = $Room->item($j)->getAttribute('Best');

                                $RoomType = $Room->item($j)->getElementsByTagName('RoomType');
                                if ($RoomType->length > 0) {
                                    $Code = $RoomType->item(0)->getAttribute('Code');
                                    $Name = $RoomType->item(0)->getAttribute('Name');
                                    $Special = $RoomType->item(0)->getElementsByTagName('Special');
                                    if ($Special->length > 0) {
                                        $Special = $Special->item(0)->nodeValue;
                                    } else {
                                        $Special = "";
                                    }
                                }
                                
                                $RoomRates = $Room->item($j)->getElementsByTagName('RoomRates');
                                if ($RoomRates->length > 0) {
                                    $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
                                    if ($RoomRate->length > 0) {
                                        $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');
                                        $BookingCode = $RoomRate->item(0)->getAttribute('BookingCode');
                                        if ($Code == $roomid and $meal == $MealPlan) {
                                            $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                                            if ($Total->length > 0) {
                                                $Currency = $Total->item(0)->getAttribute('Currency');
                                                $Amount = $Total->item(0)->getAttribute('Amount');
                                                $MinPrice = $Total->item(0)->getAttribute('MinPrice');
                                                $Commission = $Total->item(0)->getAttribute('Commission');
                                            } else {
                                                $Currency = "";
                                                $Amount = 0;
                                                $MinPrice = 0;
                                                $Commission = 0;
                                            }
                                            $CancelPenalties = $RoomRate->item(0)->getElementsByTagName('CancelPenalties');
                                            if ($CancelPenalties->length > 0) {
                                                $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName('CancelPenalty');
                                                if ($CancelPenalty->length > 0) {
                                                    for ($x=0; $x < $CancelPenalty->length; $x++) { 
                                                        $Deadline = $CancelPenalty->item($x)->getElementsByTagName('Deadline');
                                                        if ($Deadline->length > 0) {
                                                            $Units = $Deadline->item(0)->getAttribute('Units');
                                                            $cancel[$count]['Units'] = $Units;
                                                            $TimeUnit = $Deadline->item(0)->getAttribute('TimeUnit');
                                                            $cancel[$count]['TimeUnit'] = $TimeUnit;
                                                        } else {
                                                            $Units = "";
                                                            $TimeUnit = "";
                                                        }
                                                        $Charge = $CancelPenalty->item($x)->getElementsByTagName('Charge');
                                                        if ($Charge->length > 0) {
                                                            $ChargeCurrency = $Charge->item(0)->getAttribute('Currency');
                                                            $cancel[$count]['Currency'] = $ChargeCurrency;
                                                            $ChargeAmount = $Charge->item(0)->getAttribute('Amount');
                                                            $cancel[$count]['Amount'] = $ChargeAmount;
                                                        } else {
                                                            $ChargeCurrency = "";
                                                            $ChargeAmount = "";
                                                        }
                                                        $count++;
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

        //
        // Policies
        //
        $item['code'] = $value['shid'];
        $item['name'] = $value['name'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['nett'];
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['RoomTypeCode'] = $value['RoomTypeCode'];
        $item['RoomType'] = $value['RoomType'];
        $item['RoomDescription'] = $value['RoomDescription'];
        $item['RateCode'] = $value['RateCode'];
        $item['boardtype'] = $value['boardtype'];
        $item['NonRefundable'] = $value['NonRefundable'];
        $item['Recommended'] = $value['Recommended'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);

        $cancelation_details = "The Cancellation in " . $cancel[0]['Units'] . " " . $cancel[0]['TimeUnit'] . "s cost " . $cancel[0]['Amount'] . " " . $cancel[0]['Currency'];
        if ($count > 1) {
            $cancelation_details = $cancelation_details . "<br/> If you Cancel in " . $cancel[0]['Units'] . " " . $cancel[0]['TimeUnit'] . "s cost " . $cancel[0]['Amount'] . " " . $cancel[0]['Currency'];
        }
        error_log("\r\n cancelation_details: $cancelation_details \r\n", 3, "/srv/www/htdocs/error_log");
        $item['cancelpolicy'] = $cancelation_details;  
        $item['cancelpolicy_deadline'] = $cancel[0]['Units'] . " " . $cancel[0]['TimeUnit'];
         /* $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
         * $item['cancelpolicy_details'] = $cancelation_details;
         */
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mabreu where sid='" . $shid . "' and hid=" . $hid;
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