<?php
error_log("\r\n CONVENCIONAL - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mconvencional where hid=" . $hid;
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
    $affiliate_id_convencional = 0;
    $sql = "select value from settings where name='convencionalLogin' and affiliate_id=$affiliate_id_convencional";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $convencionalLogin = $row_settings['value'];
    }
    $sql = "select value from settings where name='convencionalPassword' and affiliate_id=$affiliate_id_convencional";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $convencionalPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='convencionalMarkup' and affiliate_id=$affiliate_id_convencional";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $convencionalMarkup = (double) $row_settings['value'];
    } else {
        $convencionalMarkup = 0;
    }
    $sql = "select value from settings where name='convencionalName' and affiliate_id=$affiliate_id_convencional";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $convencionalName = $row_settings['value'];
    }
    $sql = "select value from settings where name='convencionalID' and affiliate_id=$affiliate_id_convencional";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $convencionalID = $row_settings['value'];
    }
    $sql = "select value from settings where name='convencionalServiceURL' and affiliate_id=$affiliate_id_convencional";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $convencionalServiceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='convencionalTimeout' and affiliate_id=$affiliate_id_convencional";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $convencionalTimeout = (int) $row_settings['value'];
    }
    
    $data = date("Y-m-d\TH:i:s.v");

    $raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xnet="http://xnetinfo.org/"><soap:Header/><soap:Body><xnet:getHotelAvail><xnet:aRequest EchoToken="' . sha1(mt_rand(1, 90000) . 'SALT') . '" TimeStamp="' . $data . '" Version="1.0"><xnet:POS><xnet:Source><xnet:RequestorID ID="' . $convencionalName . '" PartnerID="' . $convencionalID . '" Username="' . $convencionalLogin . '" Password="' . $convencionalPassword . '"/></xnet:Source> </xnet:POS><xnet:AvailRequest><xnet:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '" /><xnet:HotelSearchCriterion HotelCode="' . $hotellist . '"/></xnet:AvailRequest><xnet:RoomStayCandidates>';
    $raw .= '<xnet:RoomStayCandidate><xnet:Guest AgeType="ADT" Age="0" Count="' . $adults . '" />';
    for ($w = 0; $w < $children; $w ++) {
        $raw .= '<xnet:Guest AgeType="CHD" Age="' . $children_ages[$w] . '" Count="1" />';
    }
    $raw .= '</xnet:RoomStayCandidate>';
    $raw .= '</xnet:RoomStayCandidates></xnet:aRequest></xnet:getHotelAvail></soap:Body></soap:Envelope>';
    error_log("\r\n CONVENCIONAL RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    if ($convencionalTimeout == 0) {
        $convencionalTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: text/xml",
        "Content-type: text/xml;charset=\"utf-8\"",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $convencionalServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $convencionalTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $convencionalTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'convencional';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>