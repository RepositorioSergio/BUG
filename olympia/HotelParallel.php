<?php
error_log("\r\nOlympia Europe - Hotel Parallel\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_molympiaeurope where hid=" . $hid;
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
        if ($hotellist != "") {
            $hotellist .= ',' . $row->sid . '';
        } else {
            $hotellist .= '' . $row->sid . '';
        }
    }
}
if ($hotellist != "") {
    $affiliate_id_olympia = 0;
    $sql = "select value from settings where name='olympiaeuropelogin' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $olympiaeuropelogin = $row_settings['value'];
    }
    $sql = "select value from settings where name='olympiaeuropepassword' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $olympiaeuropepassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='olympiaeuropeContextDatabase' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $olympiaeuropeContextDatabase = $row_settings['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeOTAHotelAvailRQ' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $olympiaeuropeOTAHotelAvailRQ = $row_settings['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeOTABookingListRQ' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeOTABookingListRQ = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeOTAHotelCancelRQ' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeOTAHotelCancelRQ = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeOTAHotelDescInfoRQ' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeOTAHotelDescInfoRQ = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeOTAHotelReadRQ' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeOTAHotelReadRQ = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeOTAHotelResRQ' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeOTAHotelResRQ = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeOTAHotelSearchRQ' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeOTAHotelSearchRQ = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeMarkup' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $olympiaeuropeMarkup = (double) $row_settings['value'];
    } else {
        $olympiaeuropeMarkup = 0;
    }
    $sql = "select value from settings where name='olympiaeuropeb2cMarkup' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeb2cMarkup = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeaffiliates_id' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeaffiliates_id = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropebranches_id' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropebranches_id = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeParallelSearch' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeParallelSearch = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeSearchSortorder' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeSearchSortorder = $row['value'];
    }
    $sql = "select value from settings where name='olympiaeuropeTimeout' and affiliate_id=$affiliate_id_olympia";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $olympiaeuropeTimeout = (int) $row['value'];
    }
    $raw = '<?xml version="1.0" encoding="utf-8"?>
    <soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/">
        <soap-env:Header>
            <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <wsse:Username>' . $olympiaeuropelogin . '</wsse:Username>
                <wsse:Password>' . $olympiaeuropepassword . '</wsse:Password>
                <Context>' . $olympiaeuropeContextDatabase . '</Context>
            </wsse:Security>
        </soap-env:Header>
        <soap-env:Body>
            <OTA_HotelAvailRQ xmlns="http://parsec.es/hotelapi/OTA2014Compact" >
                <HotelSearch>
                    <Currency Code="EUR"/>
                    <HotelRef HotelCode="' . $hotellist . '"/>
                    <DateRange Start="'  . strftime("%Y-%m-%d", $from) .  '" End="'  . strftime("%Y-%m-%d", $to) .  '"/>
                    <RoomCandidates>  
                        <RoomCandidate RPH="1">
                            <Guests>
                                <Guest AgeCode="A" Count="' . $adults . '" />';
                if ($children > 0) {
                    for ($z=0; $z < $children; $z++) { 
                        $raw .= '<Guest AgeCode="C" Count="1" Age="' . $children_ages[$z] . '"';
                    }
                }
        $raw .= '</Guests>
                        </RoomCandidate>
                    </RoomCandidates>
                </HotelSearch>
            </OTA_HotelAvailRQ>
        </soap-env:Body>
    </soap-env:Envelope>';
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml; charset=utf-8',
        'Accept: application/xml',
        'Content-Length: ' . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $olympiaeuropeOTAHotelAvailRQ);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $olympiaeuropeTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $olympiaeuropeTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'olympiaeurope';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>