<?php
error_log("\r\n IBERO - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$hotellist = "";
$sql = "select sid from xmlhotels_miberostar where hid=" . $hid;
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
    $affiliate_id_iberostar = 0;
    $sql = "select value from settings where name='IberstarHotelsusername' and affiliate_id=$affiliate_id_iberostar";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $IberstarHotelsusername = $row_settings['value'];
    }
    $sql = "select value from settings where name='IberstarHotelsmarkup' and affiliate_id=$affiliate_id_iberostar";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $IberstarHotelsmarkup = (double) $row_settings['value'];
    } else {
        $IberstarHotelsmarkup = 0;
    }
    $sql = "select value from settings where name='IberstarHotelsTimeout' and affiliate_id=$affiliate_id_iberostar";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $IberstarHotelsTimeout = (int) $row_settings['value'];
    } else {
        $IberstarHotelsTimeout = 0;
    }
    if ($IberstarHotelsTimeout == 0) {
        $IberstarHotelsTimeout = 120;
    }
    $sql = "select value from settings where name='IberstarHotelsserviceurl' and affiliate_id=$affiliate_id_iberostar";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $IberstarHotelsserviceurl = $row_settings['value'];
    }
    
    $raw = '<?xml version="1.0" encoding="UTF-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:clo="http://www.cloudhospitality.com" xmlns:ns="http://www.opentravel.org/OTA/2003/05"><soap:Header/><soap:Body><clo:GetAvailability><ns:AvailabilityRequest PrimaryLangID="en"><ns:POS><ns:Source><ns:RequestorID ID="' . $IberstarHotelsusername . '" Type="13"/><ns:BookingChannel Type="2"><ns:CompanyName Code=""/></ns:BookingChannel></ns:Source></ns:POS><ns:AvailRequestSegments><ns:AvailRequestSegment><ns:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '"/><ns:RatePlanCandidates><ns:RatePlanCandidate RatePlanCode=""></ns:RatePlanCandidate></ns:RatePlanCandidates><ns:RoomStayCandidates>';
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
    $raw .= '</ns:GuestCounts></ns:RoomStayCandidate></ns:RoomStayCandidates><ns:HotelSearchCriteria><ns:Criterion><ns:HotelRef HotelCode="' . $hotellist . '" AreaID="" HotelCityCode=""/></ns:Criterion></ns:HotelSearchCriteria></ns:AvailRequestSegment></ns:AvailRequestSegments></ns:AvailabilityRequest></clo:GetAvailability></soap:Body></soapenv:Envelope>';  
    error_log("\r\nRTS RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/xml",
            "Content-type: text/xml;charset=UTF-8",
            "Content-Encoding: UTF-8",
            "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $IberstarHotelsserviceurl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $IberstarHotelsTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $IberstarHotelsTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'iberostar';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>