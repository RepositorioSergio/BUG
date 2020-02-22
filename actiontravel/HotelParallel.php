<?php
error_log("\r\n ACTIONTRAVEL - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mactiontravel where hid=" . $hid;
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
    $affiliate_id_actiontravel = 0;
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
            $sql = "select value from settings where name='ActionTravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_actiontravel";
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $row_settings = $statement->execute();
            $row_settings->buffer();
            if ($row_settings->valid()) {
                $row_settings = $row_settings->current();
                $sourceMarket = $row_settings['value'];
            } else {
                $sourceMarket = "";
            }
        }
    } else {
        $sql = "select value from settings where name='ActionTravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_actiontravel";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        } else {
            $sourceMarket = "";
        }
    }
    $sql = "select value from settings where name='ActionTravelLoginEmail' and affiliate_id=$affiliate_id_actiontravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $ActionTravelLoginEmail = $row_settings['value'];
    }
    $sql = "select value from settings where name='ActionTravelPassword' and affiliate_id=$affiliate_id_actiontravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $ActionTravelPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='ActionTravelMarkup' and affiliate_id=$affiliate_id_actiontravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $ActionTravelMarkup = (double) $row_settings['value'];
    } else {
        $ActionTravelMarkup = 0;
    }
    $sql = "select value from settings where name='ActionTravelServiceURL' and affiliate_id=$affiliate_id_actiontravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $ActionTravelServiceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='ActionTravelb2cMarkup' and affiliate_id=$affiliate_id_actiontravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $ActionTravelb2cMarkup = $row_settings['value'];
    }
    $sql = "select value from settings where name='ActionTravelTimeout' and affiliate_id=$affiliate_id_actiontravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $ActionTravelTimeout = (int) $row_settings['value'];
    } else {
        $ActionTravelTimeout = 0;
    } 
    if ($ActionTravelTimeout == 0) {
        $ActionTravelTimeout = 120;
    }   

    $raw = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><OTA_HotelAvailService xmlns="http://www.opentravel.org/OTA/2003/05"><OTA_HotelAvailRQ PrimaryLangID="en"><POS><Source AgentDutyCode="' . $ActionTravelLoginEmail . '"><RequestorID Type="1" MessagePassword="' . $ActionTravelPassword . '"/></Source></POS><AvailRequestSegments><AvailRequestSegment><StayDateRange End="' . strftime("%Y-%m-%d", $to) . '" Start="' . strftime("%Y-%m-%d", $from) . '" /><RoomStayCandidates>';
    $raw .= '<RoomStayCandidate Quantity="1"><GuestCounts><GuestCount Count="' . $adults . '" />';
    if ($children > 0) {
        for ($z = 0; $z < $children; $z ++) {
            $raw .= '<GuestCount Age="' . $children_ages[$z] . '" Count="1" />';
        }
    }
    $raw .= '</GuestCounts></RoomStayCandidate>';
    $raw .= '</RoomStayCandidates><HotelSearchCriteria><Criterion><HotelRef HotelCode="' . $hotellist . '" HotelCityCode=""/><TPA_Extensions>';
    if ($sourceMarket != "") {
        $raw .= '<PaxCountry>' . $sourceMarket . '</PaxCountry>';
    }
    $raw .= '<ShowBasicInfo>0</ShowBasicInfo><ShowCatalogueData>0</ShowCatalogueData><ShowNettPrice>1</ShowNettPrice><ShowOnlyAvailable>1</ShowOnlyAvailable><ShowPromotions>1</ShowPromotions><ShowCancellationPolicy>1</ShowCancellationPolicy><ShowDailyAvailabilityBreakdown>1</ShowDailyAvailabilityBreakdown></TPA_Extensions></Criterion></HotelSearchCriteria></AvailRequestSegment></AvailRequestSegments></OTA_HotelAvailRQ></OTA_HotelAvailService></soap:Body></soap:Envelope>';
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/xml",
        "Content-type: text/xml",
        "Accept-Encoding: gzip, deflate",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $ActionTravelServiceURL . 'OTA_HotelAvail.asmx');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $ActionTravelTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $ActionTravelTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'actiontravel';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>