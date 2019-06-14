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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_goglobal where session_id='$session_id'";
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
$branch_filter = '';
$sql = "select value from settings where name='enablegoglobal' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_goglobal = $affiliate_id;
} else {
    $affiliate_id_goglobal = 0;
}

$sql = "select value from settings where name='GoGlobalPassword' and affiliate_id=$affiliate_id_goglobal" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GoGlobalPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='GoGlobalMarkup' and affiliate_id=$affiliate_id_goglobal" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GoGlobalMarkup = (double) $row_settings['value'];
} else {
    $GoGlobalMarkup = 0;
}
$sql = "select value from settings where name='GoGlobalLoginEmail' and affiliate_id=$affiliate_id_goglobal" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GoGlobalLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='GoGlobalServiceURL' and affiliate_id=$affiliate_id_goglobal" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GoGlobalServiceURL = $row_settings['value'];
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



error_log("\r\n PASSOU XML $nights \r\n", 3, "/srv/www/htdocs/error_log");

/*
 * $fromHotelsPRO = $fromHotelsPRO->getTimestamp();
 * $toHotelsPro = $toHotelsPro->getTimestamp();
 */
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        error_log("\r\n ENTROU FOREACH \r\n", 3, "/srv/www/htdocs/error_log");
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['HotelId'];
            $scode = $value['shid'];
            $HotelId = $value['HotelId'];
            $HotelSearchCode = $value['HotelSearchCode'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }

        $from_date = date('Y-m-d' , strtotime($from));
        $to_date = date('Y-m-d' , strtotime($to));

        $agencyID = '1521636';

        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:gog="http://www.goglobal.travel/">
        <soapenv:Header/>
        <soapenv:Body>
        <gog:MakeRequest>
            <gog:requestType>9</gog:requestType>
            <gog:xmlRequest><![CDATA[
                <Root>
                    <Header>
                        <Agency>' . $agencyID . '</Agency>
                        <User>' . $GoGlobalLoginEmail . '</User>
                        <Password>' . $GoGlobalPassword . '</Password>
                        <Operation>BOOKING_VALUATION_REQUEST</Operation>
                        <OperationType>Request</OperationType>
                    </Header>
                    <Main>
                        <HotelSearchCode>' . $HotelSearchCode . '</HotelSearchCode>
                        <ArrivalDate>' . $from_date . '</ArrivalDate>
                    </Main>
                </Root>
            ]]></gog:xmlRequest>
        </gog:MakeRequest>
        </soapenv:Body>
        </soapenv:Envelope>';
        error_log("\r\n RAW $raw \r\n", 3, "/srv/www/htdocs/error_log");

        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";
        //
        // Policies
        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $GoGlobalServiceURL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: text/xml; charset=utf-8",
            "Content-length: " . strlen($raw)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        // error_log("\r\nResponse Policies: $result . \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\nCode: $code \r\n", 3, "/srv/www/htdocs/error_log");
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response2);
        $MakeRequestResult = $inputDoc->getElementsByTagName("MakeRequestResult");
        if ($MakeRequestResult->length > 0) {
            $response2 = $MakeRequestResult->item(0)->nodeValue;
        } else {
            $response2 = "";
        }

        error_log("\r\n RESPONSE: $response2 \r\n", 3, "/srv/www/htdocs/error_log");

        $inputDoc2 = new DOMDocument();
        $inputDoc2->loadXML($response2);
        $Root = $inputDoc2->getElementsByTagName("Root");
        if ($Root->length > 0) {
            error_log("\r\n ENTROU 1 \r\n", 3, "/srv/www/htdocs/error_log");
            $Main = $Root->item(0)->getElementsByTagName("Main");
            if ($Main->length > 0) {
                error_log("\r\n ENTROU 2 \r\n", 3, "/srv/www/htdocs/error_log");
                $HotelSearchCode = $Main->item(0)->getElementsByTagName("HotelSearchCode");
                if ($HotelSearchCode->length > 0) {
                    $HotelSearchCode = $HotelSearchCode->item(0)->nodeValue;
                } else {
                    $HotelSearchCode = "";
                }
                $ArrivalDate = $Main->item(0)->getElementsByTagName("ArrivalDate");
                if ($ArrivalDate->length > 0) {
                    $ArrivalDate = $ArrivalDate->item(0)->nodeValue;
                } else {
                    $ArrivalDate = "";
                }
                $CancellationDeadline = $Main->item(0)->getElementsByTagName("CancellationDeadline");
                if ($CancellationDeadline->length > 0) {
                    $CancellationDeadline = $CancellationDeadline->item(0)->nodeValue;
                } else {
                    $CancellationDeadline = "";
                }
                error_log("\r\n CancellationDeadline: $CancellationDeadline \r\n", 3, "/srv/www/htdocs/error_log");
                $Remarks = $Main->item(0)->getElementsByTagName("Remarks");
                if ($Remarks->length > 0) {
                    $Remarks = $Remarks->item(0)->nodeValue;
                } else {
                    $Remarks = "";
                }
                $Rates = $Main->item(0)->getElementsByTagName("Rates");
                if ($Rates->length > 0) {
                    $Rates = $Rates->item(0)->nodeValue;
                } else {
                    $Rates = "";
                }
            }
        }
         
        
        try {
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db2);
            $insert = $sql->insert();
            $insert->into('log_goglobal');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $GoGlobalServiceURL . $raw,
                'sqlcontext' => $response,
                'errcontext' => ''
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db2->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        //
        // EOF Policies
        // EOF Check prices & availability
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);


        $item['cancelpolicy'] = $Remarks;
        $item['cancelpolicy_deadline'] = date('Y-m-d' , strtotime($CancellationDeadline));
        $item['cancelpolicy_deadlinetimestamp'] = $CancellationDeadline;
        /*$item['cancelpolicy_details'] = $cancelation_details; */
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mgoglobal where sid='" . $shid . "' and hid=" . $hid;
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