<?php
error_log("\r\n HOTELDO - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
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
error_log("\r\n HOTEL $hotellist \r\n", 3, "/srv/www/htdocs/error_log");
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
    /* $sql = "select value from settings where name='HotelDoTimeout' and affiliate_id=$affiliate_id_ots";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $HotelDoTimeout = (int) $row_settings['value'];
    } */
    $HotelDoTimeout = 0;

    $selectedAdults = array();
    $selectedAdults[$nroom] = $adults;
    $selectedChildren = array();
    $selectedChildren[$nroom] = $children;
    $selectedChildrenAges = array();

    $url = '/GetQuoteHotels?a=' . $HotelDouser . '&co=MX&c=pe&sd=' . strftime("%Y%m%d", $from) . '&ed=' . strftime("%Y%m%d", $to) . '&h=' . $hotellist . '&rt=&mp=';
    for ($r=0; $r < count($selectedAdults); $r++) { 
        $url = $url . '&r='. ($r + 1) .'&r' . ($r + 1) . 'a=' . $selectedAdults[$nroom] . '';
        if ($children > 0) {
            $children_ages = explode(",", $children_ages);
            for ($w = 0; $w < count($children_ages); $w ++) {
                $selectedChildrenAges[$nroom][$w] = $children_ages[$w];
                $url = $url . '&r' . ($r + 1) . 'k=' . $children . '&r' . ($r + 1) . 'k' . ($w + 1) . 'a=' . $selectedChildrenAges[$nroom][$w] . '';
            }
        }
    }
    $url .= '&d=&l=esp&hash=hs:true;hp:true';
    $url2 = $HotelDoserviceURL . $url;
    error_log("\r\n URL $url2 \r\n", 3, "/srv/www/htdocs/error_log");
    if ($HotelDoTimeout == 0) {
        $HotelDoTimeout = 120;
    }
    $ch = curl_init();
    //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $HotelDoserviceURL . $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, false);
    //curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $HotelDoTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $HotelDoTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'hoteldo';
    $channelsParallel[$nC] = $ch;
    //$channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>