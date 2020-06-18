<?php
error_log("\r\n ITERPEC - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_miterpec where hid=" . $hid;
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
    $affiliate_id_iterpec = 0;
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
        $sql = "select value from settings where name='iterpecDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_iterpec";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='iterpeclogin' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $iterpeclogin = $row_settings['value'];
    }
    $sql = "select value from settings where name='iterpecpassword' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $iterpecpassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='iterpecServiceURL' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $iterpecServiceURL = $row['value'];
    }
    $sql = "select value from settings where name='iterpecMarkup' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $iterpecMarkup = (double) $row_settings['value'];
    } else {
        $iterpecMarkup = 0;
    }
    $sql = "select value from settings where name='iterpecaffiliates_id' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $iterpecaffiliates_id = $row_settings['value'];
    }
    $sql = "select value from settings where name='iterpecb2cMarkup' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $iterpecb2cMarkup = $row_settings['value'];
    }
    $sql = "select value from settings where name='iterpecbranches_id' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $iterpecbranches_id = $row_settings['value'];
    }
    $sql = "select value from settings where name='iterpecParallelSearch' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $iterpecParallelSearch = $row_settings['value'];
    }
    $sql = "select value from settings where name='iterpecSearchSortorder' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $iterpecSearchSortorder = $row_settings['value'];
    }
    $sql = "select value from settings where name='iterpecTimeout' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $iterpecTimeout = (int)$row_settings['value'];
    }

    $num_rooms = 1;

    $raw = '{
        "Credential": {
        "Username": "' . $iterpeclogin . '",
        "Password": "' . $iterpecpassword . '"
        },
        "Criteria":{
        "DestinationId": 1003937,
        "NumNights": ' . $noOfNights . ',
        "ReturnHotelStaticData": true,
        "ReturnOnRequestRooms": true,
        "CheckinDate": "' . strftime("%Y-%m-%d", $from) . '",
        "MainPaxCountryCodeNationality": "' . $sourceMarket . '",
        "SearchRooms": [';
        $raw .= '{
            "NumAdults": ' . $adults . ',';
            if ($children > 0) {
                $raw .= '"ChildAges": [';
                $tam = $children;
                for ($z=0; $z < $children; $z++) { 
                    if ($tam == ($children - 1)) {
                        $raw .= $children_ages;
                    } else {
                        $raw .= $children_ages . ',';
                    }
                    $tam = $tam + 1;
                }
                $raw .= ']'; 
            }
            $raw .= '"Quantity": ' . $num_rooms . ' }';
        $raw .= '],
        "Filters": {
            "MinPrice": 10.00,
            "MaxPrice": 1000.00,
            "CheapestRoomOnly": true,
            "SelectedHotelsIds": [' . $hotellist . ']
        }
        }
    }';
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: application/json",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $iterpecServiceURL . 'ws/Rest/Hotel.svc/Search');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $iterpecTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $iterpecTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'iterpec';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>