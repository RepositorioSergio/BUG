<?php
error_log("\r\n VIVA - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
$query = "";
$scurrency = strtoupper($currency);
unset($tmp);
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
use Zend\Http\Client;
use Zend\Http\Request;
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
$VendorCode = "";
$hotellist = "";
$sql = "select sid from xmlhotels_mvivawyndham where hid=" . $hid;
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
    $affiliate_id_vivawyndham = 0;
    $branch_filter = "";
    $sql = "select value from settings where name='VivaWyndhamServiceURL' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamServiceURL = $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamUsername' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamUsername = $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhampassword' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhampassword = base64_decode($row_settings["value"]);
    }
    $sql = "select value from settings where name='VivaWyndhamMainVendorCode' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamMainVendorCode = $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamVendorCode1' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamVendorCode1 = $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamVendorCode2' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamVendorCode2 = $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamVendorCode3' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamVendorCode3 = $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamVendorCodeMarketCountry1' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamVendorCodeMarketCountry1 = $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamVendorCodeMarketCountry2' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamVendorCodeMarketCountry2 = $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamVendorCodeMarketCountry3' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamVendorCodeMarketCountry3 = $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamMarkup' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamMarkup = (int) $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamMarkup' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamMarkup = (int) $row_settings["value"];
    }
    $sql = "select value from settings where name='VivaWyndhamTimeout' and affiliate_id=$affiliate_id_vivawyndham" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $VivaWyndhamTimeout = (int) $row_settings["value"];
    }
    if ($VivaWyndhamTimeout == 0) {
        $VivaWyndhamTimeout = 120;
    }
    if ($VendorCode == "") {
        if ($VivaWyndhamVendorCodeMarketCountry1 != "") {
            if ($VivaWyndhamVendorCodeMarketCountry1 != "") {
                $xTmp = explode(";", $VivaWyndhamVendorCodeMarketCountry1);
                for ($xTmpCount = 0; $xTmpCount < count($xTmp); $xTmpCount ++) {
                    if ($xTmp[$xTmpCount] == $nationality) {
                        $VendorCode = $VivaWyndhamVendorCode1;
                        break;
                    }
                }
            }
        }
    }
    if ($VendorCode == "") {
        if ($VivaWyndhamVendorCodeMarketCountry2 != "") {
            if ($VivaWyndhamVendorCodeMarketCountry2 != "") {
                $xTmp = explode(";", $VivaWyndhamVendorCodeMarketCountry2);
                for ($xTmpCount = 0; $xTmpCount < count($xTmp); $xTmpCount ++) {
                    if ($xTmp[$xTmpCount] == $nationality) {
                        $VendorCode = $VivaWyndhamVendorCode2;
                        break;
                    }
                }
            }
        }
    }
    if ($VendorCode == "") {
        if ($VivaWyndhamVendorCodeMarketCountry3 != "") {
            if ($VivaWyndhamVendorCodeMarketCountry3 != "") {
                $xTmp = explode(";", $VivaWyndhamVendorCodeMarketCountry3);
                for ($xTmpCount = 0; $xTmpCount < count($xTmp); $xTmpCount ++) {
                    if ($xTmp[$xTmpCount] == $nationality) {
                        $VendorCode = $VivaWyndhamVendorCode3;
                        break;
                    }
                }
            }
        }
    }
    if ($VendorCode == "") {
        $VendorCode = $VivaWyndhamMainVendorCode;
    }

    $translator = new Translator();
    if (file_exists("src/App/language/" . $lang . ".mo")) {
        $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
    }
    
    $raw = '<?xml version="1.0" encoding="utf-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><SOAP-ENV:Body><m:OTA_HotelAvailRQ xmlns:m="http://www.opentravel.org/OTA/2003/05" Version="1" PrimaryLangID="en" AvailRatesOnly="true"><m:POS><m:Source><m:RequestorID MessagePassword="' . $VivaWyndhampassword . '" ID="' . $VivaWyndhamUsername . '" /></m:Source></m:POS><m:AvailRequestSegments><m:AvailRequestSegment><m:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '" /><m:RoomStayCandidates><m:RoomStayCandidate RPH="1" Quantity="1"><m:GuestCounts><m:GuestCount Count="' . $adults . '" AgeQualifyingCode="10" Age="30"/>';
    for ($z = 0; $z < $children; $z ++) {
        if ($children_ages[$z] <= 2) {
            $raw .= '<m:GuestCount Count="1" AgeQualifyingCode="7" Age="' . $children_ages[$z] . '"/>';
        } else {
            $raw .= '<m:GuestCount Count="1" AgeQualifyingCode="8" Age="' . $children_ages[$z] . '"/>';
        }
    }
    $raw .= '</m:GuestCounts></m:RoomStayCandidate></m:RoomStayCandidates><m:HotelSearchCriteria><m:Criterion><m:HotelRef HotelCode="' . $hotellist . '" HotelCityCode="" ChainCode="" VendorCode="' . $VendorCode . '" /></m:Criterion></m:HotelSearchCriteria></m:AvailRequestSegment></m:AvailRequestSegments></m:OTA_HotelAvailRQ></SOAP-ENV:Body></SOAP-ENV:Envelope>';
    
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept-Encoding: gzip, deflate",
        "User-Agent: curl/7.37.0",
        "Content-Type: text/xml",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $VivaWyndhamServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $VivaWyndhamTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $VivaWyndhamTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'vivawyndham';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>