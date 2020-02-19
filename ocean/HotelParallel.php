<?php
error_log("\r\n OCEAN - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
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
$sql = "select sid from xmlhotels_mocean where hid=" . $hid;
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
    $affiliate_id_ocean = 0;
    $branch_filter = "";
    $sql = "select value from settings where name='OceanbyH10HotelsServiceURL' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsServiceURL = $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10HotelsUsername' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsUsername = $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10Hotelspassword' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10Hotelspassword = base64_decode($row_settings["value"]);
    }
    $sql = "select value from settings where name='OceanbyH10HotelsMainVendorCode' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsMainVendorCode = $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10HotelsVendorCode1' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsVendorCode1 = $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10HotelsVendorCode2' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsVendorCode2 = $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10HotelsVendorCode3' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsVendorCode3 = $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10HotelsVendorCodeMarketCountry1' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsVendorCodeMarketCountry1 = $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10HotelsVendorCodeMarketCountry2' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsVendorCodeMarketCountry2 = $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10HotelsVendorCodeMarketCountry3' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsVendorCodeMarketCountry3 = $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10HotelsMarkup' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsMarkup = (int) $row_settings["value"];
    }
    $sql = "select value from settings where name='OceanbyH10HotelsTimeout' and affiliate_id=$affiliate_id_ocean" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $OceanbyH10HotelsTimeout = (int) $row_settings["value"];
    } else {
        $OceanbyH10HotelsTimeout = 0;
    }
    if ($OceanbyH10HotelsTimeout == 0) {
        $OceanbyH10HotelsTimeout = 120;
    }
    $sql = "select city_xml24 from cities where id=" . $destination;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $HotelCityCode = $row_settings["city_xml24"];
    }
    if ($nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . $nationality;
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings["iso_code_2"];
        } else {
            $sourceMarket = "";
        }
    } else {
        $sourceMarket = "";
    }
    if ($VendorCode == "") {
        if ($OceanbyH10HotelsVendorCodeMarketCountry1 != "") {
            if ($OceanbyH10HotelsVendorCodeMarketCountry1 != "") {
                $xTmp = explode(";", $OceanbyH10HotelsVendorCodeMarketCountry1);
                for ($xTmpCount = 0; $xTmpCount < count($xTmp); $xTmpCount ++) {
                    if ($xTmp[$xTmpCount] == $nationality) {
                        $VendorCode = $OceanbyH10HotelsVendorCode1;
                        break;
                    }
                }
            }
        }
    }
    if ($VendorCode == "") {
        if ($OceanbyH10HotelsVendorCodeMarketCountry2 != "") {
            if ($OceanbyH10HotelsVendorCodeMarketCountry2 != "") {
                $xTmp = explode(";", $OceanbyH10HotelsVendorCodeMarketCountry2);
                for ($xTmpCount = 0; $xTmpCount < count($xTmp); $xTmpCount ++) {
                    if ($xTmp[$xTmpCount] == $nationality) {
                        $VendorCode = $OceanbyH10HotelsVendorCode2;
                        break;
                    }
                }
            }
        }
    }
    if ($VendorCode == "") {
        if ($OceanbyH10HotelsVendorCodeMarketCountry3 != "") {
            if ($OceanbyH10HotelsVendorCodeMarketCountry3 != "") {
                $xTmp = explode(";", $OceanbyH10HotelsVendorCodeMarketCountry3);
                for ($xTmpCount = 0; $xTmpCount < count($xTmp); $xTmpCount ++) {
                    if ($xTmp[$xTmpCount] == $nationality) {
                        $VendorCode = $OceanbyH10HotelsVendorCode3;
                        break;
                    }
                }
            }
        }
    }
    if ($VendorCode == "") {
        $VendorCode = $OceanbyH10HotelsMainVendorCode;
    }

    $translator = new Translator();
    if (file_exists("src/App/language/" . $lang . ".mo")) {
        $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
    }
    $nC = 0;
    $multiParallel = array();
    $multiParallel = curl_multi_init();
    
    $raw = '<?xml version="1.0" encoding="utf-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><SOAP-ENV:Body><m:OTA_HotelAvailRQ xmlns:m="http://www.opentravel.org/OTA/2003/05" Version="1" PrimaryLangID="en" AvailRatesOnly="true"><m:POS><m:Source><m:RequestorID MessagePassword="' . $OceanbyH10Hotelspassword . '" ID="' . $OceanbyH10HotelsUsername . '" /></m:Source></m:POS><m:AvailRequestSegments><m:AvailRequestSegment><m:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '" /><m:RoomStayCandidates><m:RoomStayCandidate RPH="1" Quantity="1"><m:GuestCounts><m:GuestCount Count="' . $adults . '" AgeQualifyingCode="10" Age="30"/>';
    for ($z = 0; $z < $children; $z ++) {
        if ($children_ages[$z] <= 2) {
            $raw .= '<m:GuestCount Count="1" AgeQualifyingCode="7" Age="' . $children_ages[$z] . '"/>';
        } else {
            $raw .= '<m:GuestCount Count="1" AgeQualifyingCode="8" Age="' . $children_ages[$z] . '"/>';
        }
    }
    $raw .= '</m:GuestCounts></m:RoomStayCandidate></m:RoomStayCandidates><m:HotelSearchCriteria><m:Criterion><m:HotelRef HotelCode="' . $hotellist . '" HotelCityCode="" ChainCode="" VendorCode="' . $VendorCode . '" /></m:Criterion></m:HotelSearchCriteria></m:AvailRequestSegment></m:AvailRequestSegments></m:OTA_HotelAvailRQ></SOAP-ENV:Body></SOAP-ENV:Envelope>';
    error_log("\r\nRTS RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept-Encoding: gzip, deflate",
        "User-Agent: curl/7.37.0",
        "Content-Type: text/xml",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $OceanbyH10HotelsServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $OceanbyH10HotelsTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $OceanbyH10HotelsTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'ocean';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>