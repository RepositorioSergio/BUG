<?php
error_log("\r\n PALLADIUM - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mpalladium where hid=" . $hid;
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
    $affiliate_id_palladium = 0;
    $sql = "select value from settings where name='PalladiumHotelGroupusername' and affiliate_id=$affiliate_id_palladium";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $PalladiumHotelGroupusername = $row_settings['value'];
    }
    $sql = "select value from settings where name='PalladiumHotelGroupmarkup' and affiliate_id=$affiliate_id_palladium";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $PalladiumHotelGroupmarkup = (double) $row_settings['value'];
    } else {
        $PalladiumHotelGroupmarkup = 0;
    }
    $sql = "select value from settings where name='PalladiumHotelGroupTimeout' and affiliate_id=$affiliate_id_palladium";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $PalladiumHotelGroupTimeout = (int) $row_settings['value'];
    } else {
        $PalladiumHotelGroupTimeout = 0;
    }
    $sql = "select value from settings where name='PalladiumHotelGroupserviceurl' and affiliate_id=$affiliate_id_palladium";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $PalladiumHotelGroupserviceurl = $row_settings['value'];
    }

    $raw = '<?xml version="1.0" encoding="UTF-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:clo="http://www.cloudhospitality.com" xmlns:ns="http://www.opentravel.org/OTA/2003/05"><soap:Header/><soap:Body><clo:GetAvailability><ns:AvailabilityRequest PrimaryLangID="en"><ns:POS><ns:Source><ns:RequestorID ID="' . $PalladiumHotelGroupusername . '" Type="13"/><ns:BookingChannel Type="2"><ns:CompanyName Code=""/></ns:BookingChannel></ns:Source></ns:POS><ns:AvailRequestSegments><ns:AvailRequestSegment><ns:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '"/><ns:RatePlanCandidates><ns:RatePlanCandidate RatePlanCode=""></ns:RatePlanCandidate></ns:RatePlanCandidates><ns:RoomStayCandidates>';
    $rCount = 1;
    // RoomTypeCode="1"
    $raw .= '<ns:RoomStayCandidate RPH="' . $rCount . '" Quantity="1"><ns:GuestCounts><ns:GuestCount Count="' . $adults . '" AgeQualifyingCode="10" Age="30"/>';
    // Person code identifier 10=adult, 8=child, 7=baby
    for ($z = 0; $z < $children; $z ++) {
        if ($children_ages[$z] <= 2) {
            $raw .= '<ns:GuestCount Count="1" AgeQualifyingCode="7" Age="' . $children_ages[$z] . '"/>';
        } else {
            $raw .= '<ns:GuestCount Count="1" AgeQualifyingCode="8" Age="' . $children_ages[$z] . '"/>';
        }
    }
    $raw .= '</ns:GuestCounts></ns:RoomStayCandidate>';
    $rCount = $rCount + 1;
    $raw .= '</ns:RoomStayCandidates><ns:HotelSearchCriteria><ns:Criterion><ns:HotelRef HotelCode="' . $hotellist . '" AreaID="" HotelCityCode=""/></ns:Criterion></ns:HotelSearchCriteria></ns:AvailRequestSegment></ns:AvailRequestSegments></ns:AvailabilityRequest></clo:GetAvailability>
    </soapenv:Body>
    </soapenv:Envelope>';
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    if ($PalladiumHotelGroupTimeout == 0) {
        $PalladiumHotelGroupTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/xml",
        "Content-type: text/xml;charset=UTF-8",
        "Content-Encoding: UTF-8",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $PalladiumHotelGroupserviceurl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $PalladiumHotelGroupTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $PalladiumHotelGroupTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'palladium';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>