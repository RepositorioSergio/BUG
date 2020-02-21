<?php
error_log("\r\n COMING - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mcoming2 where hid=" . $hid;
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
    $affiliate_id_coming2 = 0;
    $sql = "select value from settings where name='coming2login' and affiliate_id=$affiliate_id_coming2";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $coming2login = $row_settings['value'];
    }
    $sql = "select value from settings where name='coming2CurrencyCode' and affiliate_id=$affiliate_id_coming2";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $coming2CurrencyCode = strtoupper($row_settings['value']);
    } else {
        $coming2CurrencyCode = "USD";
    }
    $sql = "select value from settings where name='coming2Language' and affiliate_id=$affiliate_id_coming2";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $coming2Language = strtoupper($row_settings['value']);
    } else {
        $coming2Language = "EN";
    }
    $sql = "select value from settings where name='coming2password' and affiliate_id=$affiliate_id_coming2";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $coming2password = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='coming2Markup' and affiliate_id=$affiliate_id_coming2";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $coming2Markup = (double) $row_settings['value'];
    } else {
        $coming2Markup = 0;
    }
    $sql = "select value from settings where name='coming2ServiceURL' and affiliate_id=$affiliate_id_coming2";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $coming2ServiceURL = $row_settings['value'];
    }
    error_log("\r\n coming2ServiceURL - $coming2ServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
    $sql = "select value from settings where name='coming2Company' and affiliate_id=$affiliate_id_coming2";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $coming2Company = $row_settings['value'];
    }
    $sql = "select value from settings where name='coming2Timeout' and affiliate_id=$affiliate_id_coming2";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $coming2Timeout = (int) $row_settings['value'];
    }
    if ($coming2Timeout == 0) {
        $coming2Timeout = 120;
    }
    $raw = '{ "AvailabilityId": "", "Language": "' . $coming2Language . '", "CurrencyCode": "' . $coming2CurrencyCode . '", "Customer": "", "FromDate": "' . strftime("%Y-%m-%d", $from) . '", "ToDate": "' . strftime("%Y-%m-%d", $to) . '", "Hotels": [ "' . $hotellist . '" ], "Rooms": [';
    $z = 1;
    $raw .= '{ "RoomCandidateId": "' . $z . '", "Paxes": [';
    for ($w = 0; $w < $adults; $w ++) {
        if ($w > 0) {
            $raw .= ',';
        }
        $raw .= '{ "PaxType": "Adult", "Age": 30 }';
    }
    for ($w = 0; $w < $children; $w ++) {
        $raw .= ',{ "PaxType": "Child", "Age": ' . $children_ages[$w] . ' }';
    }
    $raw .= '] }], "Skip": 0, "Limit": 250, "Filter": { "MinPrice": 0, "MaxPrice": 0, "PackageRates": "All", "ResidentRates": "Yes", "SeniorRates": "Yes", "NonRefundableRates": "All" }, "OrderBy": { "Direction": "Ascending", "Field": "Price"} }';  
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    
    $passuser = "$coming2login:$coming2password";
    $auth = base64_encode($passuser);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Basic " . $auth,
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $coming2ServiceURL . 'Hotel/Availability');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $coming2Timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $coming2Timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'coming2';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>