<?php
error_log("\r\n INFINITAS - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_minfinitas where hid=" . $hid;
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
        $hotellist .= '' . $row->sid . '';
    }
}
//$hotellist = 'MELPABUC';
error_log("\r\n hotellist - $hotellist \r\n", 3, "/srv/www/htdocs/error_log");
if ($hotellist != "") {
    $affiliate_id_infinitas = 0;
    $sql = "select value from settings where name='infinitastarget' and affiliate_id=$affiliate_id_infinitas";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitastarget = (int) $row_settings["value"];
    } else {
        $infinitastarget = 0;
    }
    $sql = "select value from settings where name='infinitasID' and affiliate_id=$affiliate_id_infinitas";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasID = $row_settings['value'];
    }
    $sql = "select value from settings where name='infinitasPassword' and affiliate_id=$affiliate_id_infinitas";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='infinitasMarkup' and affiliate_id=$affiliate_id_infinitas";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasMarkup = (double) $row_settings['value'];
    } else {
        $infinitasMarkup = 0;
    }
    $sql = "select value from settings where name='infinitasServiceURL' and affiliate_id=$affiliate_id_infinitas";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasServiceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='infinitasPartnerID' and affiliate_id=$affiliate_id_infinitas";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasPartnerID = $row_settings['value'];
    }
    $sql = "select value from settings where name='infinitasTimeout' and affiliate_id=$affiliate_id_infinitas";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $infinitasTimeout = (int) $row_settings['value'];
    }

    $url = $infinitasServiceURL . 'hotelavail/list?partner_id=' . $infinitasPartnerID . '&user_name=' . $infinitasID . '&password=' . $infinitasPassword . '&target=' . $infinitastarget . '&version=1.6&start=' . strftime("%Y-%m-%d", $from) . '&end=' . strftime("%Y-%m-%d", $to) . '&hotel=' . $hotellist . '&rooms=';
    $url2 = "";
    $url2 = $url2 . '[';
    $url2 = $url2 . '{"Guests": [';
    $url2 = $url2 . '{"AgeQualifying":0,"Age":0,"Count":' . $adults . '}';
    if ($children > 0) {
        $url2 = $url2 . ',';
        for ($ch = 0; $ch < $children; $ch ++) {
            $url2 = $url2 . '{"AgeQualifying":1,"Age":' . $children_ages[$ch] . ',"Count":1},';
        }
    }
    $url2 = $url2 . ']},';
    $url2 = $url2 . ']';
    $url2 = urlencode($url2);
    $url = $url . '' . $url2;

    if ($infinitasTimeout == 0) {
        $infinitasTimeout = 120;
    }

    $ch = curl_init();
    /* curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Host: infinitash.redirectme.net",
        "Content-type: text/xml; charset=utf-8",
        "SOAPAction: http://www.hubinfo.org/2016/07/queryHotelAvail",
        "Content-length: " . strlen($raw)
    )); */
    //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, false);
    //curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $infinitasTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $infinitasTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'infinitastravel';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>