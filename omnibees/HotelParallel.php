<?php
// error_log("\r\nRTS - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_momnibees where hid=" . $hid;
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
    $affiliate_id_omnibees = 0;
    $sql = "select value from settings where name='omnibeesMarkup' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesMarkup = (double) $row_settings['value'];
    } else {
        $omnibeesMarkup = 0;
    }
    $sql = "select value from settings where name='omnibeesLoginEmail' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesLoginEmail = $row_settings['value'];
    }
    $sql = "select value from settings where name='omnibeesPassword' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='omnibeesTimeout' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesTimeout = (int) $row_settings['value'];
    } else {
        $omnibeesTimeout = 0;
    }
    if ($omnibeesTimeout == "") {
        $omnibeesTimeout = 120;
    }
    $sql = "select value from settings where name='omnibeesServiceURL' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesServiceURL = $row_settings['value'];
    }

    $raw = '<?xml version="1.0" encoding="UTF-8"?>
    <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://schemas.datacontract.org/2004/07/Pull.BLL.Models" xmlns:ns2="http://tempuri.org/" xmlns:ns3="http://schemas.datacontract.org/2004/07/Pull.BLL.Models.OTA"><SOAP-ENV:Body><ns2:GetHotelAvail><ns2:login><ns1:Password>' . $omnibeesPassword . '</ns1:Password><ns1:UserName>' . $omnibeesLoginEmail . '</ns1:UserName></ns2:login><ns2:ota_HotelAvailRQ><ns3:BestOnly>false</ns3:BestOnly><ns3:EchoToken>' . sha1(mt_rand(1, 90000) . 'SALT') . '</ns3:EchoToken><ns3:HotelSearchCriteria><ns3:Criterion><ns3:HotelRefs><ns3:HotelRef><ns3:HotelCode>' . $hotellist . '</ns3:HotelCode></ns3:HotelRef></ns3:HotelRefs><ns3:RoomStayCandidatesType><ns3:RoomStayCandidates><ns3:RoomStayCandidate><ns3:GuestCountsType><ns3:GuestCounts><ns3:GuestCount><ns3:AgeQualifyCode>Adult</ns3:AgeQualifyCode><ns3:Count>' . $adults . '</ns3:Count></ns3:GuestCount>';
    if ($children > 0) {
        for ($z = 0; $z < $children; $z ++) {
            $raw .= '<ns3:GuestCount><ns3:Age>' . $children_ages[$z] . '</ns3:Age><ns3:AgeQualifyCode>Child</ns3:AgeQualifyCode><ns3:Count>1</ns3:Count></ns3:GuestCount>';
        }
    }
    $raw .= '</ns3:GuestCounts></ns3:GuestCountsType><ns3:Quantity>1</ns3:Quantity><ns3:RPH>0</ns3:RPH></ns3:RoomStayCandidate></ns3:RoomStayCandidates></ns3:RoomStayCandidatesType><ns3:StayDateRange><ns3:End>' . strftime("%Y-%m-%dT00:00:00", $to) . '</ns3:End><ns3:Start>' . strftime("%Y-%m-%dT00:00:00", $from) . '</ns3:Start></ns3:StayDateRange></ns3:Criterion></ns3:HotelSearchCriteria><ns3:PrimaryLangID>en</ns3:PrimaryLangID><ns3:Target>Test</ns3:Target><ns3:TimeStamp>' . strftime("%Y-%m-%dT%H:%m:%S", time()) . '</ns3:TimeStamp><ns3:Version>2.6</ns3:Version></ns2:ota_HotelAvailRQ></ns2:GetHotelAvail></SOAP-ENV:Body></SOAP-ENV:Envelope>';
    error_log("\r\nRTS RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");

    $action = "http://tempuri.org/IPull/GetHotelAvail";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "SOAPAction: " . $action,
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $omnibeesServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $omnibeesTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $omnibeesTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'omnibees';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>