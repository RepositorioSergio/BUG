<?php
error_log("\r\nRATUKEN - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mzumata where hid=" . $hid;
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
error_log("\r\nRakuten hotellist - $hotellist \r\n", 3, "/srv/www/htdocs/error_log");
if ($hotellist != "") {
    $affiliate_id_rakuten = 0;
    $sql = "select value from settings where name='rakutenAPIKey' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rakutenAPIKey = $row_settings['value'];
    }
    $sql = "select value from settings where name='rakutenaffiliates_id' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rakutenaffiliates_id = $row_settings['value'];
    }
    $sql = "select value from settings where name='rakutenbranches_id' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rakutenbranches_id = $row_settings['value'];
    }
    $sql = "select value from settings where name='rakutenServiceURL' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenServiceURL = $row['value'];
    }
    $sql = "select value from settings where name='rakutenMarkup' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rakutenMarkup = (double) $row_settings['value'];
    } else {
        $rakutenMarkup = 0;
    }
    $sql = "select value from settings where name='rakutenServiceURL' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenServiceURL = $row['value'];
    }
    $sql = "select value from settings where name='rakutenb2cMarkup' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenb2cMarkup = $row['value'];
    }
    $sql = "select value from settings where name='rakutenParallelSearch' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenParallelSearch = $row['value'];
    }
    $sql = "select value from settings where name='rakutenSearchSortorder' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenSearchSortorder = $row['value'];
    }
    $sql = "select value from settings where name='rakutenTimeout' and affiliate_id=$affiliate_id_rakuten";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rakutenTimeout = (int) $row['value'];
    }
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
        $sql = "select value from settings where name='ratukenDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_rakuten";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $hotellist .= ",PLPo,7uLH,dnhy,9CPH,fst1,fst2,fst4,TJRf,KQQR,SvBX,WijN wijn,reFn,usg1,usj1"; // Test hotels
    $num_rooms = 1;
    $url = 'hotel_list?check_in_date=' . strftime("%Y-%m-%d", $from) . '&check_out_date=' . strftime("%Y-%m-%d", $to) . '&adult_count=' . $adults;
    $ages = "";
    if ($children > 0) {
        for ($i = 0; $i < $children; $i ++) {
            if ($ages != "") {
                $ages .= ',' . $children_ages[$i];
            } else {
                $ages = $children_ages[$i];
            }
        }
        $url .= '&children=' . $ages . '&room_count=' . $num_rooms . '&currency=' . strtoupper($currency) . '&source_market=' . $sourceMarket . '&hotel_id_list=' . urlencode($hotellist);
    } else {
        $url .= '&room_count=' . $num_rooms . '&currency=' . strtoupper($currency) . '&source_market=' . $sourceMarket . '&hotel_id_list=' . urlencode($hotellist);
    }
    error_log("\r\nRakuten url - $url \r\n", 3, "/srv/www/htdocs/error_log");
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'accept-encoding: gzip',
        'Content-Type: application/json',
        'x-api-key: ' . $rakutenAPIKey
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $rakutenServiceURL . $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $rakutenTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $rakutenTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'rakuten';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>