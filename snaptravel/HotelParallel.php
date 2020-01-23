<?php
error_log("\r\n Youtravel - Hotel Parallel Search SNAPTRAVEL\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_msnaptravel where hid=" . $hid;
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
        if ($hotellist == "") {
            $hotellist = $row->sid;
        } else {
            $hotellist .= "," . $row->sid;
        }
    }
}
$hotellist = [108540,112915,118583,118903,119566,122212];
if ($hotellist != "") {
    $affiliate_id_snaptravel = 0;
    $sql = "select value from settings where name='snaptravelbranches_id' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptravelbranches_id = $row_settings['value'];
    }
    $sql = "select value from settings where name='snaptravelRevisionVersion' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptravelRevisionVersion = $row_settings['value'];
    }
    $sql = "select value from settings where name='snaptraveldaleschannel' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptraveldaleschannel = $row_settings['value'];
    }
    $sql = "select value from settings where name='snaptravelsalesenvironment' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptravelsalesenvironment = $row_settings['value'];
    }
    $sql = "select value from settings where name='snaptravelSearchSortorder' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptravelSearchSortorder = $row_settings['value'];
    }
    $sql = "select value from settings where name='snaptravelSharedSecret' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptravelSharedSecret = $row_settings['value'];
    }
    $sql = "select value from settings where name='snaptravelServiceURL' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptravelServiceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='snaptravelTimeout' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptravelTimeout = $row_settings['value'];
    }
    $sql = "select value from settings where name='snaptravelAPIKey' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $snaptravelAPIKey = $row['value'];
    }
    $sql = "select value from settings where name='snaptravelMarkup' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptravelMarkup = (double) $row_settings['value'];
    } else {
        $snaptravelMarkup = 0;
    }
    $sql = "select value from settings where name='snaptravelb2cMarkup' and affiliate_id=$affiliate_id_snaptravel" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $snaptravelb2cMarkup = (double) $row_settings['value'];
    } else {
        $snaptravelb2cMarkup = 0;
    }
    $sfilter = array();
    $signature = hash("sha256", $hotelbedsuser . $hotelbedspassword . time());
    $endpoint = $hotelbedsserviceURL . "hotel-api/1.0/hotels";
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

    $local = 'en_US';
    $hotelList = "108540,112915,118583,118903,119566,122212";
    $currency = 'USD';

    $raw2 = '{
        "arrivalDate": "' . strftime("%m/%d/%Y", $from) . '",
        "departureDate": "' . strftime("%m/%d/%Y", $to) . '",
        "room1": "2",
        "hotelIdList": [' . $hotelList . '],
        "locale": "' . $local . '",
        "currencyCode": "' . $currency . '",
        "timeout": ' . $snaptravelTimeout . '
      }';
    
    $headers2 = array(
        "x-api-key: $snaptravelAPIKey",
        "Content-Type: application/json",
        "version: $snaptravelRevisionVersion",
        "Content-Length: " . strlen($raw2)
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $snaptravelServiceURL . 'b2b');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
    $response2 = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    
    error_log("\r\n Response: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
    
    $response2 = json_decode($response2, true);
    
    $HotelListResponse = $response2['HotelListResponse'];
    $customerSessionId = $HotelListResponse['customerSessionId'];
    
    $hotelId = 119566;

    $numbers = '';
    $raw = '{
        "hotelId": ' . $hotelId . ',
        "sessionId": "' . $customerSessionId . '",
        "arrivalDate": "' . strftime("%m/%d/%Y", $from) . '",
        "departureDate": "' . strftime("%m/%d/%Y", $to) . '",';
        /* for ($r=0; $r < count($adults); $r++) { 
            $numbers = $adults[$r];
            if (count($children[$r]) > 0) {
                for ($z=0; $z < $children[$r]; $z++) { 
                    $numbers = $numbers . ',' . $selectedChildrenAges[$r][$z];
                }
            }
            $raw = $raw . '"room' . ($r+1) .'": "' . $numbers . '",';
        } */

        $raw = $raw . '"room1": "2",
        "locale": "' . $local . '",
        "currencyCode": "' . $currency . '",
        "timeout": ' . $snaptravelTimeout . '
    }';
    error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");
    
    if ($hotelbedsTimeout == 0) {
        $hotelbedsTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "x-api-key: $snaptravelAPIKey",
        "Content-Type: application/json",
        "version: $snaptravelRevisionVersion",
        "Content-Length: " . strlen($raw)
    ));
    
    // curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $snaptravelServiceURL . 'avail');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_POST, true);
    //curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $hotelbedsTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $hotelbedsTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'snaptravel';
    $channelsParallel[$nC] = $ch;
    $nC ++;
}

?>