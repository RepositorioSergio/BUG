<?php
error_log("\r\n AIC - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$hotellist = "";
$sql = "select sid from xmlhotels_maic where hid=" . $hid;
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
            $hotellist .= '<hotel id="' . $row->sid . '"/>';
    }
}
if ($hotellist != "") {
    $affiliate_id_aic = 0;
    if ((int) $nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
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
        $sql = "select value from settings where name='AICTravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_aic";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='AICTravellogin' and affiliate_id=$affiliate_id_aic";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AICTravellogin = $row_settings['value'];
    }
    $sql = "select value from settings where name='AICTravelpassword' and affiliate_id=$affiliate_id_aic";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AICTravelpassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='AICTravelMarkup' and affiliate_id=$affiliate_id_aic";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AICTravelMarkup = (double) $row_settings['value'];
    } else {
        $AICTravelMarkup = 0;
    }
    $sql = "select value from settings where name='AICTravelServiceURL' and affiliate_id=$affiliate_id_aic";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AICTravelServiceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='AICTravelCompany' and affiliate_id=$affiliate_id_aic";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AICTravelCompany = $row_settings['value'];
    }
    $sql = "select value from settings where name='AICTimeout' and affiliate_id=$affiliate_id_aic";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $AICTimeout = (int) $row_settings["value"];
    } else {
        $AICTimeout = 120;
    }
   
    $dateStart = new DateTime(strftime("%Y-%m-%d", $from));
    $dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
    $noOfNights = $dateStart->diff($dateEnd)->format('%d');
    
    $checkin = date('Y-m-d', $from);
    $checkout = date('Y-m-d', $to);
    $date = new Datetime();
    $timestamp = $date->format('U');
    $city_xml32 = 'rom';
    $raw = '<?xml version="1.0" encoding="UTF-8"?>
    <envelope>
        <header>
            <actor>' . $AICTravelCompany . '</actor>
            <user>' . $AICTravellogin . '</user>
            <password>' . $AICTravelpassword . '</password>
            <version>1.6.1</version>
            <timestamp>' . $timestamp . '</timestamp>
        </header>
        <query type="availability" product="hotel">
            <nationality>' . $sourceMarket . '</nationality>
            <checkin date="' . $checkin . '"/>
            <checkout date="' . $checkout . '"/>';
    $raw .= $hotellist;
    $raw .= '<details>
                <room type="dbl" required="1" />
            </details>
        </query>
    </envelope>';

    if ($AICTimeout == 0) {
        $AICTimeout = 120;
    }
    $ch = curl_init();
    $headers = array(
        'Content-Type: text/xml; charset=utf-8',
        'Content-Length: ' . strlen($raw)
    );
    // curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $AICTravelServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    //curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $AICTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $AICTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'aic';
    $channelsParallel[$nC] = $ch;
    $nC ++;
}
?>