<?php
error_log("\r\nMark - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mmarkinternational where hid=" . $hid;
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
    $affiliate_id_markinternational = 0;
    $sql = "select value from settings where name='MarkInternationalLogin' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalLogin = $row_settings['value'];
    }
    $sql = "select value from settings where name='MarkInternationalPassword' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalPassword = base64_decode($row_settings['value']);
    }
    
    $sql = "select value from settings where name='MarkInternationalMarkup' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalMarkup = (double) $row_settings['value'];
    } else {
        $MarkInternationalMarkup = 0;
    }
    $sql = "select value from settings where name='MarkInternationalURL' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='MarkInternationalVendor' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalVendor = $row_settings['value'];
    }
    $sql = "select value from settings where name='MarkInternationalAgencyNumber' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalAgencyNumber = $row_settings['value'];
    }
    $sql = "select value from settings where name='MarkInternationalTimeout' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalTimeout = (int) $row_settings['value'];
    } else {
        $MarkInternationalTimeout = 0;
    }
    $sql = "select value from settings where name='MarkInternationalWebServices' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalWebServices = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='MarkInternationalDynamicPackaging' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalDynamicPackaging = $row_settings['value'];
    }
    $sql = "select value from settings where name='MarkInternationalb2cMarkup' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalb2cMarkup = $row_settings['value'];
    }
    $sql = "select value from settings where name='MarkInternationalContact' and affiliate_id=$affiliate_id_markinternational";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $MarkInternationalContact = $row_settings['value'];
    }
    $sql = "select city_xml17 from cities where id=" . $destination;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $city_xml17 = $row_settings["city_xml17"];
    } else {
        $city_xml17 = "";
    }
    $raw = 'requestXml=<VAXXML xmlns="http://www.triseptsolutions.com/Availability/Request/11.0"><Header AgencyNumber="' . $MarkInternationalAgencyNumber . '" Contact="' . $MarkInternationalContact . '" Login="' . $MarkInternationalLogin . '" Password="' . $MarkInternationalWebServices . '" Vendor="' . $MarkInternationalVendor . '" DynamicPackageId="' . $MarkInternationalDynamicPackaging . '" Culture="en-us"  SessionId="" ShowCart="Y" /><Request Type="New" Seq="1" AbsoluteDestinationCode="' . $city_xml17 . '" AbsoluteOriginCode="' . $city_xml17 . '"><TravelerAvail>';
    $Seq = 1;
    for ($z = 0; $z < $adults; $z ++) {
        $raw .= '<PassengerTypeQuantity Seq="' . $Seq . '" Type="ADT" Age="40" />';
        $Seq = $Seq + 1;
    }
    for ($z = 0; $z < $children; $z ++) {
        $raw .= '<PassengerTypeQuantity Seq="' . $Seq . '" Type="CHD" Age="' . $children_ages[$z] . '" />';
        $Seq = $Seq + 1;
    }
    $raw .= '</TravelerAvail>';
    $Seq = 1;
    $raw .= '<HotelAvailRQ Start="1" Length="999" SortType="Price"><TravelerAvailSet>';
    for ($z = 0; $z < $adults; $z ++) {
        $raw .= '<PassengerSeq Seq="' . $Seq . '"/>';
        $Seq = $Seq + 1;
    }
    for ($z = 0; $z < $children; $z ++) {
        $raw .= '<PassengerSeq Seq="' . $Seq . '"/>';
        $Seq = $Seq + 1;
    }
    $raw .= '</TravelerAvailSet><OriginDestinationInformation Type="Checkin" LocationCode="' . $city_xml17 . '" DateTime="' . strftime("%Y-%m-%d", $from) . 'T' . strftime("%H:%M:%S") . '"/><OriginDestinationInformation Type="Checkout" LocationCode="' . $city_xml17 . '" DateTime="' . strftime("%Y-%m-%d", $to) . 'T' . strftime("%H:%M:%S") . '"/><Criterion Type="Hotel_id" Value="' . $hotellist . '" /></HotelAvailRQ>';
    $raw .= '</Request></VAXXML>';
    error_log("\r\nMark RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    if ($MarkInternationalTimeout == 0) {
        $V = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/xml",
        "Content-type: application/x-www-form-urlencoded",
        "Content-Encoding: UTF-8",
        "Accept-Encoding: gzip,deflate",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $MarkInternationalURL . 'AvailabilityRequest');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $MarkInternationalTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $MarkInternationalTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'markinternational';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>