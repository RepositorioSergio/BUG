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
$db = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_totalstay where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $xmlrequest = $row_settings["xmlrequest"];
    $xmlresult = $row_settings["xmlresult"];
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
    $residency = $searchsettings['residency'];
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$sql = "select value from settings where name='enabletotalstay' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_totalstay = $affiliate_id;
} else {
    $affiliate_id_totalstay = 0;
}
$sql = "select value from settings where name='totalstayuser' and affiliate_id=$affiliate_id_totalstay" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $totalstayuser = $row_settings['value'];
}
$sql = "select value from settings where name='totalstaypassword' and affiliate_id=$affiliate_id_totalstay" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $totalstaypassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='totalstayMarkup' and affiliate_id=$affiliate_id_totalstay" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $totalstayMarkup = (double) $row_settings['value'];
} else {
    $totalstayMarkup = 0;
}
$sql = "select value from settings where name='totalstayserviceURL' and affiliate_id=$affiliate_id_totalstay" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $totalstayserviceURL = $row_settings['value'];
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
$fromHotelsPRO = $fromHotelsPRO->getTimestamp();
$toHotelsPro = $toHotelsPro->getTimestamp();
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['code'];
            $scode = $value['scode'];
            $hotel_code = $value['shid'];
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $mealid = $value['mealid'];
        $PropertyRoomTypeID = $value['PropertyRoomTypeID'];
        $BookingToken = $value['BookingToken'];
        $adults = $value['adults'];
        $children = $value['children'];
        $infants = $value['infants'];
        error_log("\r\n BookingToken - $BookingToken\r\n", 3, "/srv/www/htdocs/error_log");
        
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        
        if ($BookingToken == "") {
            $raw = 'Data=<PreBookRequest>
            <LoginDetails>
                <Login>' . $totalstayuser . '</Login>
                <Password>' . $totalstaypassword . '</Password>
                <CurrencyID>2</CurrencyID>
            </LoginDetails>
            <BookingDetails>
                <PropertyID>' . $shid . '</PropertyID>
                <ArrivalDate>' . $from_date . '</ArrivalDate>
                <Duration>' . $nights . '</Duration>
                <RoomBookings>
                    <RoomBooking>
                        <PropertyRoomTypeID>' . $PropertyRoomTypeID . '</PropertyRoomTypeID>
                        <MealBasisID>' . $mealid . '</MealBasisID>
                        <Adults>' . $adults . '</Adults>
                        <Children>' . $children . '</Children>
                        <Infants>' . $infants . '</Infants>';
                        if ($children > 0) {
                            $raw .= '<ChildAges>';
                            for ($z = 0; $z < $children; $z ++) {
                                $raw .= '<ChildAge><Age>' . $children_ages[$z] . '</Age></ChildAge>';
                            }
                            $raw .= '</ChildAges>';
                        }
            $raw .= '</RoomBooking>
                </RoomBookings>
            </BookingDetails>
            </PreBookRequest>';
        } else {
            $raw = 'Data=<PreBookRequest>
            <LoginDetails>
                <Login>' . $totalstayuser . '</Login>
                <Password>' . $totalstaypassword . '</Password>
                <CurrencyID>2</CurrencyID>
            </LoginDetails>
            <BookingDetails>
                <PropertyID>' . $hotel_code . '</PropertyID>
                <ArrivalDate>' . $from_date . '</ArrivalDate>
                <Duration>' . $nights . '</Duration>
                <RoomBookings>
                    <RoomBooking>
                        <BookingToken>' . $BookingToken . '</BookingToken>
                        <MealBasisID>' . $mealid . '</MealBasisID>
                        <Adults>' . $adults . '</Adults>
                        <Children>' . $children . '</Children>
                        <Infants>' . $infants . '</Infants>';
                        if ($children > 0) {
                            $raw .= '<ChildAges>';
                            for ($z = 0; $z < $children; $z ++) {
                                $raw .= '<ChildAge><Age>' . $children_ages[$z] . '</Age></ChildAge>';
                            }
                            $raw .= '</ChildAges>';
                        }
             $raw .= '</RoomBooking>
                </RoomBookings>
            </BookingDetails>
            </PreBookRequest>';
        }
        error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $totalstayserviceURL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response2 = curl_exec($ch);
        curl_close($ch);
        error_log("\r\nResponse - $response2\r\n", 3, "/srv/www/htdocs/error_log");
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response2);
        $PreBookResponse = $inputDoc->getElementsByTagName("PreBookResponse");
        if ($PreBookResponse->length > 0) {
            $CurrencyID = $PreBookResponse->item(0)->getElementsByTagName("CurrencyID");
            if ($CurrencyID->length > 0) {
                $CurrencyID = $CurrencyID->item(0)->nodeValue;
            } else {
                $CurrencyID = "";
            }
            $PreBookingToken = $PreBookResponse->item(0)->getElementsByTagName("PreBookingToken");
            if ($PreBookingToken->length > 0) {
                $PreBookingToken = $PreBookingToken->item(0)->nodeValue;
            } else {
                $PreBookingToken = "";
            }
            $TotalPrice = $PreBookResponse->item(0)->getElementsByTagName("TotalPrice");
            if ($TotalPrice->length > 0) {
                $TotalPrice = $TotalPrice->item(0)->nodeValue;
            } else {
                $TotalPrice = "";
            }
            $TotalCommission = $PreBookResponse->item(0)->getElementsByTagName("TotalCommission");
            if ($TotalCommission->length > 0) {
                $TotalCommission = $TotalCommission->item(0)->nodeValue;
            } else {
                $TotalCommission = "";
            }
            $VATOnCommission = $PreBookResponse->item(0)->getElementsByTagName("VATOnCommission");
            if ($VATOnCommission->length > 0) {
                $VATOnCommission = $VATOnCommission->item(0)->nodeValue;
            } else {
                $VATOnCommission = "";
            }
            $ReturnStatus = $PreBookResponse->item(0)->getElementsByTagName("ReturnStatus");
            if ($ReturnStatus->length > 0) {
                $Success = $ReturnStatus->item(0)->getElementsByTagName("Success");
                if ($Success->length > 0) {
                    $Success = $Success->item(0)->nodeValue;
                } else {
                    $Success = "";
                }
                $Exception = $ReturnStatus->item(0)->getElementsByTagName("Exception");
                if ($Exception->length > 0) {
                    $Exception = $Exception->item(0)->nodeValue;
                } else {
                    $Exception = "";
                }
            }
            $CancellationPoliciesArray = array();
            $count = 0;
            $Cancellations = $PreBookResponse->item(0)->getElementsByTagName("Cancellations");
            if ($Cancellations->length > 0) {
                $Cancellation = $Cancellations->item(0)->getElementsByTagName("Cancellation");
                if ($Cancellation->length > 0) {
                    for ($i=0; $i < $Cancellation->length; $i++) { 
                        $StartDate = $Cancellation->item($i)->getElementsByTagName("StartDate");
                        if ($StartDate->length > 0) {
                            $StartDate = $StartDate->item(0)->nodeValue;
                        } else {
                            $StartDate = "";
                        }
                        $EndDate = $Cancellation->item($i)->getElementsByTagName("EndDate");
                        if ($EndDate->length > 0) {
                            $EndDate = $EndDate->item(0)->nodeValue;
                        } else {
                            $EndDate = "";
                        }
                        $Penalty = $Cancellation->item($i)->getElementsByTagName("Penalty");
                        if ($Penalty->length > 0) {
                            $Penalty = $Penalty->item(0)->nodeValue;
                        } else {
                            $Penalty = "";
                        }
                        $CancellationPoliciesArray[$count]['StartDate'] = $StartDate;
                        $CancellationPoliciesArray[$count]['EndDate'] = $EndDate;
                        $CancellationPoliciesArray[$count]['Penalty'] = $Penalty;
                        $count = $count + 1;
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
        $item['room_type'] = $value['room_type'];
        $item['mealid'] = $value['mealid'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        
        $cancelation_deadline = date('Y-m-d', strtotime($CancellationPoliciesArray[0]['EndDate']));
        for ($j = 0; $j < count($CancellationPoliciesArray); $j++) {
            $cancelpolicy .= 'If you cancel booking From Date ' . date('Y-m-d', strtotime($CancellationPoliciesArray[$j]['StartDate'])) . ' To Date ' . date('Y-m-d', strtotime($CancellationPoliciesArray[0]['EndDate'])) . ' cost ' . $CancellationPoliciesArray[$j]['Penalty'] . '. <br/>';
        }

        $item['cancelpolicy'] = $cancelpolicy;
        $item['cancelpolicy_deadline'] = $cancelation_deadline;
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        $item['cancelpolicy_details'] = $cancelpolicy;
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mexclusivelyhotels where sid='" . $shid . "' and hid=" . $hid;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
$row_hotel->buffer();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel->buffer();
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $row_country->buffer();
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
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
} catch (\Exception $e) {
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>