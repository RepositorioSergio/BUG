<?php
error_log("\r\n ENJOYCUBA - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_menjoycuba where hid=" . $hid;
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
if ($hotellist != "") {
    $affiliate_id_enjoycuba = 0;
    $sql = "select value from settings where name='enjoycubaapikey' and affiliate_id=$affiliate_id_enjoycuba";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $enjoycubaapikey = $row_settings['value'];
    }
    $sql = "select value from settings where name='enjoycubaMarkup' and affiliate_id=$affiliate_id_enjoycuba";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $enjoycubaMarkup = (double) $row_settings['value'];
    } else {
        $enjoycubaMarkup = 0;
    }
    $sql = "select value from settings where name='enjoycubaTimeout' and affiliate_id=$affiliate_id_enjoycuba";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $enjoycubaTimeout = (int) $row_settings['value'];
    } else {
        $enjoycubaTimeout = 0;
    }
    $sql = "select value from settings where name='enjoycubawebserviceurl' and affiliate_id=$affiliate_id_enjoycuba";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $enjoycubawebserviceurl = $row_settings['value'];
    }

    $raw = 'filter={"lang": "2", "id_hotel": "' . $hotellist . '", "startDate": "' . strftime("%Y-%m-%d", $from) . '", "endDate": "' . strftime("%Y-%m-%d", $to) . '", "currency": "2", "combination": "' . $adults . ',' . $children . ',0", "limit": "9999", "offset": "0"}';
    
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    if ($enjoycubaTimeout == 0) {
        $enjoycubaTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-API-KEY: ' . $enjoycubaapikey
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $enjoycubawebserviceurl . 'filtersC');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $enjoycubaTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $enjoycubaTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'enjoycuba';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>