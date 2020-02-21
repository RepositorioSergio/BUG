<?php
error_log("\r\nSpecialtours STB - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
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
if (file_exists("src/App/language/" . $lang . ".mo")) {
    $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
}
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$hotellist = "";
$sql = "select sid from xmlhotels_mspecialtours where hid=" . $hid;
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
    $affiliate_id_specialtours = 0;
    $sql = "select city_xml33, latitude, longitude from cities where id=" . $destination;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $city_xml33 = $row_settings["city_xml33"];
        $latitude = $row_settings["latitude"];
        $longitude = $row_settings["longitude"];
    } else {
        $city_xml33 = 0;
    }
    $sql = "select value from settings where name='SpecialTourslogin' and affiliate_id=$affiliate_id_specialtours";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $SpecialTourslogin = $row_settings['value'];
    }
    $sql = "select value from settings where name='SpecialTourspassword' and affiliate_id=$affiliate_id_specialtours";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $SpecialTourspassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='SpecialToursMarkup' and affiliate_id=$affiliate_id_specialtours";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $SpecialToursMarkup = (double) $row_settings['value'];
    } else {
        $SpecialToursMarkup = 0;
    }
    $sql = "select value from settings where name='SpecialToursServiceURL' and affiliate_id=$affiliate_id_specialtours";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $SpecialToursServiceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='SpecialToursTimeout' and affiliate_id=$affiliate_id_specialtours";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $SpecialToursTimeout = (int) $row_settings['value'];
    }
    if ($SpecialToursTimeout == 0) {
        $SpecialToursTimeout = 120;
    }
    $dateCheckin = new DateTime(strftime("%d-%m-%Y", $from));
    $dayCheckin = $dateCheckin->format('d');
    $monthCheckin = $dateCheckin->format('m');
    $yearCheckin = $dateCheckin->format('Y');
    $dateCheckout = new DateTime(strftime("%d-%m-%Y", $to));
    $dayCheckout = $dateCheckout->format('d');
    $monthCheckout = $dateCheckout->format('m');
    $yearCheckout = $dateCheckout->format('Y');
    $raw = 'pXML=<?xml version="1.0" encoding="utf-8" ?> <HtlAllocation><Version>1.41</Version><Agent><UName>' . $SpecialTourslogin . '</UName> <UPsw>' . $SpecialTourspassword . '</UPsw></Agent> 
        <ClientCountryID>68</ClientCountryID>
        <CoID>68</CoID>
        <CiID>11934</CiID>
        <HoID>' . $hotellist . '</HoID>
        <RT>DBL</RT>
        <Rooms>1</Rooms>
        <inDate><Day>' . $dayCheckin . '</Day><Month>' . $monthCheckin . '</Month><Year>' . $yearCheckin . '</Year></inDate>
        <outDate><Day>' . $dayCheckout . '</Day><Month>' . $monthCheckout . '</Month><Year>' . $yearCheckout . '</Year></outDate>
        <PriceCurrency>' . $scurrency . '</PriceCurrency>
        <OnlyAvailable>Y</OnlyAvailable><IncludeCLXPolicy>Y</IncludeCLXPolicy></HtlAllocation>';
    error_log("\r\nSpecial Tours RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        'Accept-Encoding: gzip',
        'Content-Length: ' . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $SpecialToursServiceURL . "STOLXMLAllocation.asmx/AllocationSearch");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $SpecialToursTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $SpecialToursTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'specialtours';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>