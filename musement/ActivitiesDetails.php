<?php
// TODO
error_log("\r\nActivities Details - Musement\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Http\Client;
use Laminas\Filter\AbstractFilter;
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
$db = new \Laminas\Db\Adapter\Adapter($config);
$affiliate_id_musement = 0;
$sql = "select value from settings where name='tuimusementclientid' and affiliate_id=$affiliate_id_musement";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tuimusementclientid = $row_settings['value'];
}
$sql = "select value from settings where name='tuimusementSecretKey' and affiliate_id=$affiliate_id_musement";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tuimusementSecretKey = $row_settings['value'];
}
$sql = "select value from settings where name='tuimusementendpoint' and affiliate_id=$affiliate_id_musement";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tuimusementendpoint = $row_settings['value'];
}
error_log("\r\n tuimusementendpoint: $tuimusementendpoint\r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='tuimusementTimeout' and affiliate_id=$affiliate_id_musement";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tuimusementTimeout = $row_settings['value'];
}
$sql = "select value from settings where name='tuimusementSearchSortorder' and affiliate_id=$affiliate_id_musement";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tuimusementSearchSortorder = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='tuimusementaffiliates_id' and affiliate_id=$affiliate_id_musement";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tuimusementaffiliates_id = $row_settings['value'];
}
$sql = "select value from settings where name='tuimusementb2cMarkup' and affiliate_id=$affiliate_id_musement";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tuimusementb2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='tuimusementMarkup' and affiliate_id=$affiliate_id_musement";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tuimusementMarkup = $row_settings['value'];
}
if ($tuimusementMarkup == "") {
    $tuimusementMarkup = 0;
}
if (! is_numeric($tuimusementMarkup)) {
    $tuimusementMarkup = 0;
}

$afrom = strftime("%Y-%m-%d", $from);
$ato = strftime("%Y-%m-%d", $to);
$tmp = 0;

$startTime = microtime();
$client = new Client();
if ($tuimusementTimeout == 0) {
    $tuimusementTimeout = 120;
}
$client->setOptions(array(
    'timeout' => $tuimusementTimeout,
    'sslverifypeer' => false
));
//$client->setRawBody($raw);
$client->setHeaders(array(
    "Content-Type" => "application/json"
));
$client->setUri($tuimusementendpoint . "activities/0002a94e-0a11-4bb1-bec2-5d2a40ae4da2/dates?date_from=2020-11-29&date_to=2020-11-30");
$client->setMethod('GET');
$response = $client->send();
if ($response->isSuccess()) {
    $response = $response->getBody();
} else {
    $failed = true;
}
$endTime = microtime();
error_log("\r\nResponse $response\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    $response = json_decode($response,true);
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('log_musementactivitiesdetails');
    $insert->values(array(
        'datetime_created' => time(),
        'filename' => 'ActivitiesActivities.php',
        'errorline' => $this->microtime_diff($startTime, $endTime),
        'errormessage' => $raw,
        'sqlcontext' => $response,
        'errcontext' => ''
    ), $insert::VALUES_MERGE);
    try {
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (count($response) > 0) {
        for ($i=0; $i < count($response); $i++) { 
            $day = $response[$i]['day'];
            $sold_out = $response[$i]['sold_out'];

            $retail_price = $response[$i]['retail_price'];
            $currency = $retail_price['currency'];
            $value = $retail_price['value'];
            $totalamount_total = $value;
            $totalamount_total_net = $value;
            $formatted_value = $retail_price['formatted_value'];
            $formatted_iso_value = $retail_price['formatted_iso_value'];

            $retail_price_without_service_fee = $response[$i]['retail_price_without_service_fee'];
            $currency = $retail_price_without_service_fee['currency'];
            $value = $retail_price_without_service_fee['value'];
            $formatted_value = $retail_price_without_service_fee['formatted_value'];
            $formatted_iso_value = $retail_price_without_service_fee['formatted_iso_value'];

            $service_fee = $response[$i]['service_fee'];
            $currency = $service_fee['currency'];
            $value = $service_fee['value'];
            $formatted_value = $service_fee['formatted_value'];
            $formatted_iso_value = $service_fee['formatted_iso_value'];

            $original_retail_price = $response[$i]['original_retail_price'];
            $currency = $original_retail_price['currency'];
            $value = $original_retail_price['value'];
            $formatted_value = $original_retail_price['formatted_value'];
            $formatted_iso_value = $original_retail_price['formatted_iso_value'];

            $original_retail_price_without_service_fee = $response[$i]['original_retail_price_without_service_fee'];
            $currency = $original_retail_price_without_service_fee['currency'];
            $value = $original_retail_price_without_service_fee['value'];
            $formatted_value = $original_retail_price_without_service_fee['formatted_value'];
            $formatted_iso_value = $original_retail_price_without_service_fee['formatted_iso_value'];

            $discount_amount = $response[$i]['discount_amount'];
            $currency = $discount_amount['currency'];
            $value = $discount_amount['value'];
            $formatted_value = $discount_amount['formatted_value'];
            $formatted_iso_value = $discount_amount['formatted_iso_value'];
    
            $rate['nettotal'] = $totalamount_total_net;
            // error_log("\r\nAgent Markup: $agent_markup\n", 3, "/srv/www/htdocs/error_log");
            // error_log("\r\nInternal Markup: $internalmarkup\n", 3, "/srv/www/htdocs/error_log");
            // error_log("\r\nActivities Fall Back Markup: $ActivitiesMarkupFallback\n", 3, "/srv/www/htdocs/error_log");
            if ($scurrency != "" and $currency != $scurrency) {
                $totalamount_total = $CurrencyConverter->convert($totalamount_total, $currency, $scurrency);
                $totalamount_total_net = $CurrencyConverter->convert($totalamount_total_net, $currency, $scurrency);
            }
            $totalamount_total_net = $totalamount_total;
            if ($tuimusementMarkup != 0) {
                $totalamount_total = $totalamount_total + (($totalamount_total * $tuimusementMarkup) / 100);
            }
            // Geo target markup
            if ($internalmarkup != 0) {
                $totalamount_total = $totalamount_total + (($totalamount_total * $internalmarkup) / 100);
            }
            // Agent markup
            if ($agent_markup > 0) {
                $totalamount_total = $totalamount_total + (($totalamount_total * $agent_markup) / 100);
            }
            // Fallback Markup
            if ($tuimusementMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                $totalamount_total = $totalamount_total + (($totalamount_total * $ActivitiesMarkupFallback) / 100);
            }
            $rate['totalplain'] = $totalamount_total;
            $rate['total'] = (float) $totalamount_total;
            array_push($activityRates, $rate);
    
            $availability[$i]["rates"] = $activityRates;
            $availability[$i]["uniqueIdentifier"] = $uniqueIdentifier;
            $availability[$i]["quoteid"] = md5(uniqid($session_id, true)) . "-" . $i . "-8";
        }
    }
    if (count($availability) > 0) {
        usort($availability, function ($a, $b) {
            return floatval($a['fromplain']) <=> floatval($b['fromplain']);
        });
    }
    $activity['availability'] = $availability;
    //
    // Store Session
    //
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_musementactivitiesdetails');
    $delete->where(array(
        'session_id' => $quoteid
    ));
    $statement = $sql->prepareStatementForSqlObject($delete);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('quote_session_musementactivitiesdetails');
    $insert->values(array(
        'session_id' => $quoteid,
        'xmlrequest' => (string) $raw,
        'xmlresult' => (string) $response,
        'data' => base64_encode(serialize($availability)),
        'searchsettings' => base64_encode(serialize($requestdata))
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}
// error_log("\r\nActivities Details - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>