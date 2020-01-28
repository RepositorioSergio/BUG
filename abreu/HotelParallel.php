<?php
// error_log("\r\nAbreu - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mabreu where hid=" . $hid;
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
    $affiliate_id_abreu = 0;
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
        $sql = "select value from settings where name='AbreuDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_abreu";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='AbreuUsername' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AbreuUsername = $row_settings['value'];
    }
    
    $sql = "select value from settings where name='Abreupassword' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $Abreupassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='AbreuMarkup' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AbreuMarkup = (double) $row_settings['value'];
    } else {
        $AbreuMarkup = 0;
    }
    // URL
    $sql = "select value from settings where name='AbreuHOTELAVAILABILITY' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AbreuHOTELAVAILABILITY = $row_settings['value'];
    }
    $sql = "select value from settings where name='AbreuContext' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AbreuContext = $row_settings['value'];
    }
    $sql = "select value from settings where name='AbreuCustomerID' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AbreuCustomerID = $row_settings['value'];
    }
    $sql = "select value from settings where name='AbreuOnRequest' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AbreuOnRequest = $row_settings['value'];
    }
    $sql = "select value from settings where name='Abreub2cMarkup' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $Abreub2cMarkup = $row_settings['value'];
    }
    $sql = "select value from settings where name='abreuTimeout' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $abreuTimeout = (int) $row_settings['value'];
    }
    $sql = "select value from settings where name='AbreuCurrency' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AbreuCurrency = $row_settings['value'];
    }
    if ($AbreuCurrency == "") {
        $AbreuCurrency = "USD";
    }
    $raw = '<?xml version="1.0" encoding="utf-8"?><soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/"><soap-env:Header><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><wsse:Username>' . $AbreuUsername . '</wsse:Username><wsse:Password>' . $Abreupassword . '</wsse:Password><Context>' . $AbreuContext . '</Context></wsse:Security></soap-env:Header><soap-env:Body><OTA_HotelAvailRQ xmlns=" http://parsec.es/hotelapi/OTA2014Compact" ><HotelSearch><Currency Code="' . $AbreuCurrency . '" /><HotelRef HotelCode="' . $hotellist . '" /><DateRange Start="' . date("Y-m-d", $from) . '" End="' . date("Y-m-d", $to) . '" /><GuestCountry Code="' . $sourceMarket . '" /><RoomCandidates><RoomCandidate RPH="1"><Guests><Guest AgeCode="A" Count="' . $adults . '" />';
    for ($z = 0; $z < $children; $z ++) {
        $raw .= '<Guest AgeCode="C" Count="1" Age="' . $children_ages[$z] . '" />';
    }
    $raw .= '</Guests></RoomCandidate></RoomCandidates></HotelSearch></OTA_HotelAvailRQ></soap-env:Body></soap-env:Envelope>';
    // error_log("\r\nAbreu Request: $raw\r\n", 3, "/srv/www/htdocs/error_log");
    if ($abreuTimeout == 0) {
        $abreuTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/xml",
        "Content-type: text/xml",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $AbreuHOTELAVAILABILITY);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $abreuTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $abreuTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'abreu';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>