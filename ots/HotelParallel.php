<?php
error_log("\r\nOTS - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mots where hid=" . $hid;
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
        $hotellist .= '<HotelRef HotelCode="' . $row->sid . '"/>';
    }
}
if ($hotellist != "") {
    $affiliate_id_ots = 0;
    $sql = "select value from settings where name='OTSID' and affiliate_id=$affiliate_id_ots";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OTSID = $row_settings['value'];
    }
    $sql = "select value from settings where name='OTSTimeout' and affiliate_id=$affiliate_id_ots";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OTSTimeout = (int) $row_settings['value'];
    }
    $sql = "select value from settings where name='OTSPassword' and affiliate_id=$affiliate_id_ots";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OTSPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='OTSMarkup' and affiliate_id=$affiliate_id_ots";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OTSMarkup = (double) $row_settings['value'];
    } else {
        $OTSMarkup = 0;
    }
    $sql = "select value from settings where name='OTSServiceURL' and affiliate_id=$affiliate_id_ots";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OTSServiceURL = $row_settings['value'];
    }
    $count = 0;
    $raw = '<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="OTA_HotelAvailRQ.xsd" Version="0.1" EchoToken="123322344"><POS>
        <Source>
            <RequestorID Instance="MF001" ID_Context="AxisData" ID="' . $OTSID . '" Type="22"/>
        </Source>
        <Source>
            <RequestorID Type="88" ID="' . $OTSID . '" MessagePassword="' . $OTSPassword . '"/>
        </Source>
    </POS>
    <AvailRequestSegments><AvailRequestSegment><StayDateRange End="' . strftime("%Y-%m-%d", $to) . '" Start="' . strftime("%Y-%m-%d", $from) . '"/><RoomStayCandidates><RoomStayCandidate Quantity="1" RPH="1"><GuestCounts><GuestCount Age="32" Count="' . $adults . '" AgeQualifyingCode="10"/>';
    if ($children > 0) {
        for ($z = 0; $z < $children; $z ++) {
            if ($children_ages[$z] > 1) {
                $raw = $raw . '<GuestCount Age="' . $$children_ages[$z] . '" Count="1" AgeQualifyingCode="8"/>';
            } else {
                $raw = $raw . '<GuestCount Age="' . $children_ages[$z] . '" Count="1" AgeQualifyingCode="7"/>';
            }
        }
    }
    $raw = $raw . '</GuestCounts></RoomStayCandidate></RoomStayCandidates><HotelSearchCriteria><Criterion ExactMatch="true">' . $hotellist . '</Criterion></HotelSearchCriteria></AvailRequestSegment></AvailRequestSegments></OTA_HotelAvailRQ>';
    if ($OTSTimeout == 0) {
        $OTSTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept-Encoding: gzip,deflate',
        'Content-Length:' . strlen($raw),
        'Content-Type: text/xml;charset=utf-8'
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $OTSServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $OTSTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $OTSTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'ots';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>