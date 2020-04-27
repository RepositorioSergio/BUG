<?php
// error_log("\r\nRTS - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mamericantours where hid=" . $hid;
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
        $hotellist .= '<Criterion><HotelRef HotelCode="' . $row->sid . '"/></Criterion>';
    }
}
if ($hotellist != "") {
    $affiliate_id = 0;
    $sql = "select value from settings where name='enableati' and affiliate_id=$affiliate_id" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $affiliate_id_ati = $affiliate_id;
    } else {
        $affiliate_id_ati = 0;
    }
    if ((int) $nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $row_settings = $statement2->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings["iso_code_2"];
        } else {
            $sourceMarket = "";
        }
    } else {
        $sql = "select value from settings where name='atiDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_ati";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='atiUsername' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $atiUsername = $row_settings['value'];
    }
    $sql = "select value from settings where name='atiPassword' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $atiPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='atiaffiliates_id' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $atiaffiliates_id = $row_settings['value'];
    }
    $sql = "select value from settings where name='atib2cMarkup' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $atib2cMarkup = $row_settings['value'];
    }
    $sql = "select value from settings where name='atiServiceURL' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $atiServiceURL = $row['value'];
    }
    $sql = "select value from settings where name='atiMarkup' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $atiMarkup = (double) $row_settings['value'];
    } else {
        $atiMarkup = 0;
    }
    $sql = "select value from settings where name='atibranches_id' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $atibranches_id = $row_settings['value'];
    }
    $sql = "select value from settings where name='atiParallelSearch' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $atiParallelSearch = $row_settings['value'];
    }
    $sql = "select value from settings where name='atiSearchSortorder' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $atiSearchSortorder = (double) $row_settings['value'];
    } else {
        $atiSearchSortorder = 0;
    }
    $sql = "select value from settings where name='atiTimeout' and affiliate_id=$affiliate_id_ati";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $atiTimeout = (int) $row_settings['value'];
    }

    $duration = 'P0Y0M' . $noOfNights . 'D';
    $raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
    <soap:Header/>
    <soap:Body>
        <ns1:OTA_HotelAvailRQ Version="1.3">
            <POS>
                <Source ERSP_UserID="' . $atiUsername . '"/>
            </POS>
            <AvailRequestSegments>
                <AvailRequestSegment AvailReqType="AMENITIES">
                    <StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" Duration="' . $duration . '"></StayDateRange>
                    <RoomStayCandidates>';
                    for ($r=0; $r < 1; $r++) { 
                        $raw .= '<RoomStayCandidate>
                        <GuestCounts>
                            <GuestCount AgeQualifyingCode="10" Count="' . $adults . '"/>';
                        if ($children > 0) {
                            for ($z=0; $z < $children; $z++) { 
                                $raw .= '<GuestCount AgeQualifyingCode="08" Age="' . $children_ages[$r][$z] . '" Count="' . $children . '"/>';
                            }
                        }
                        $raw .= '</GuestCounts>
                        </RoomStayCandidate>';
                    }
            $raw .= '</RoomStayCandidates>
                    <HotelSearchCriteria>' . $hotellist . '
                    </HotelSearchCriteria>
                </AvailRequestSegment>
            </AvailRequestSegments>
        </ns1:OTA_HotelAvailRQ>
    </soap:Body>
    </soap:Envelope>';
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");

    $encode = $atiUsername . ":" . $atiPassword;
    $auth = base64_encode("$encode");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: text/xml;charset=UTF-8",
        "Accept-Encoding: gzip, deflate",
        "Authorization: Basic " . $auth,
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $atiServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $atiTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $atiTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'ati';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>