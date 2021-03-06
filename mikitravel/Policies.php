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
try {
    $db = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_mikitravel where session_id='$session_id'";
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
    $rooms = $searchsettings['rooms'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='enablemikitravel' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $affiliate_id_mikitravel = $affiliate_id;
} else {
    $affiliate_id_mikitravel = 0;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='mikitraveluserid' and affiliate_id=$affiliate_id_mikitravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mikitraveluserid = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='mikitravelpassword' and affiliate_id=$affiliate_id_mikitravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelsproPassword = base64_decode($row_settings['value']);
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='mikitravelserviceurl' and affiliate_id=$affiliate_id_mikitravel" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mikitravelserviceurl = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
    error_log("\r\n URL $mikitravelserviceurl \r\n", 3, "/srv/www/htdocs/error_log");
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
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='mikitravelMarkup' and affiliate_id=$affiliate_id_mikitravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mikitravelMarkup = (double) $row_settings['value'];
} else {
    $mikitravelMarkup = 0;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='mikitravelagentcode' and affiliate_id=$affiliate_id_mikitravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mikitravelagentcode = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
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
            $rateIdentifier = $value['rateIdentifier'];
            $scode = $value['scode'];
            $hotel_code = $value['shid'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";
        //
        // Policies
        //
        $item['code'] = $value['shid'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['nettotal'];
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
        /* $item['cancelpolicy'] = str_replace("<p><br /></p>", "", $value['cancelpolicy']);
        $item['cancelpolicy_deadline'] = strftime("%d-%m-%Y", $cancelation_deadline);
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        $item['cancelpolicy_details'] = $cancelation_details; */
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Zend\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mmikitravel where sid='" . $shid . "' and hid=" . $hid;
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