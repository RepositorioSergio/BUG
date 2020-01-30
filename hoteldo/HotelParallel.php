<?php
error_log("\r\nHotelDO - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$affiliate_id_hoteldo = 0;
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarketHotelDO = $row_settings["iso_code_2"];
    } else {
        $sourceMarketHotelDO = "";
    }
} else {
    $sql = "select value from settings where name='HotelDoDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_hoteldo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarketHotelDO = $row_settings['value'];
    }
}
$hotellist = "";
$sql = "select sid from xmlhotels_mhoteldo where hid=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $result = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        if ($hotellist == "") {
            $hotellist = $row->sid;
        } else {
            $hotellist .= 'h=' . $row->sid;
        }
    }
}
error_log("\r\nHotel List $hotellist\r\n", 3, "/srv/www/htdocs/error_log");
if ($hotellist != "") {
    $affiliate_id_hoteldo = 0;
    $sql = "select value from settings where name='HotelDouser' and affiliate_id=$affiliate_id_hoteldo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $HotelDouser = $row_settings['value'];
    }
    $sql = "select value from settings where name='HotelDoMarkup' and affiliate_id=$affiliate_id_hoteldo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $HotelDoMarkup = (double) $row_settings['value'];
    } else {
        $HotelDoMarkup = 0;
    }
    $sql = "select value from settings where name='HotelDoserviceURL ' and affiliate_id=$affiliate_id_hoteldo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $HotelDoserviceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='HotelDoTimeout' and affiliate_id=$affiliate_id_hoteldo";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $HotelDoTimeout = (int) $row_settings['value'];
    }
    if ($lang == "es") {
        $l = "esp";
    } elseif ($lang = "pt") {
        $l = "por";
    } else {
        $l = "ing";
    }
    $url = '/GetQuoteHotels?a=' . $HotelDouser . '&co=' . $sourceMarketHotelDO . '&c=pe&sd=' . strftime("%Y%m%d", $from) . '&ed=' . strftime("%Y%m%d", $to) . '&h=' . $hotellist . '&rt=&mp=&r=1&r1a=' . $adults . '&r1k=' . $children;
    if ($children > 0) {
        $children_ages = explode(",", $children_ages);
        for ($w = 0; $w < $children; $w ++) {
            $url .= '&r1k' . ($w + 1) . 'a=' . $children_ages[$w] . '';
        }
    }
    $url .= '&d=&l=' . $l . '&hash=hs:true;hp:true';
    error_log("\r\nHotel Do URL : " . $HotelDoserviceURL . $url . "\r\n", 3, "/srv/www/htdocs/error_log");
    if ($HotelDoTimeout == 0) {
        $HotelDoTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $HotelDoserviceURL . $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $HotelDoTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $HotelDoTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'hoteldo';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $HotelDoserviceURL . $url;
    $nC ++;
}
?>