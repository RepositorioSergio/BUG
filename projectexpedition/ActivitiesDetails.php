<?php
error_log("\r\nActivities Details - Project Expedition\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Http\Client;
use Laminas\Filter\AbstractFilter;
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
$dbProjectExpedition = new \Laminas\Db\Adapter\Adapter($config);
$affiliate_id_projectexpedition = 0;
$sql = "select value from settings where name='projectexpeditionEmail' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionEmail = $row_settings['value'];
}
$sql = "select value from settings where name='projectexpeditionaccesstoken' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionaccesstoken = $row_settings['value'];
}
$sql = "select value from settings where name='projectexpeditionTimeout' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionTimeout = $row_settings['value'];
}
$sql = "select value from settings where name='projectexpeditionaccesstoken' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionaccesstoken = $row_settings['value'];
}
error_log("\r\nprojectexpeditionaccesstoken: $projectexpeditionaccesstoken\r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='projectexpeditionwebserviceURL' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionwebserviceURL = $row_settings['value'];
}
$sql = "select value from settings where name='projectexpeditionSearchSortorder' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionSearchSortorder = $row_settings['value'];
}
$sql = "select value from settings where name='projectexpeditionaffiliates_id' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionaffiliates_id = $row_settings['value'];
}
$sql = "select value from settings where name='projectexpeditionPhone' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionPhone = $row_settings['value'];
}
$sql = "select value from settings where name='projectexpeditionipaddress' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionipaddress = $row_settings['value'];
}
$sql = "select value from settings where name='projectexpeditionb2cMarkup' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionb2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='projectexpeditionMarkup' and affiliate_id=$affiliate_id_projectexpedition";
$statement = $dbProjectExpedition->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $projectexpeditionMarkup = $row_settings['value'];
}
if ($projectexpeditionMarkup == "") {
    $projectexpeditionMarkup = 0;
}
if (! is_numeric($projectexpeditionMarkup)) {
    $projectexpeditionMarkup = 0;
}

$afrom = strftime("%Y-%m-%d", $from);
$ato = strftime("%Y-%m-%d", $to);
$tmp = 0;
if ($lang == "") {
    $lang = $hotelbedsTicketslanguage;
}
$package_code = "16350";
$url = $projectexpeditionwebserviceURL . "v1/return_availability?id=" . $package_code . "&standard=2&elderly=0&Adults=2&Children=0&date=" . strftime("%Y-%m-%d", $from) . "&range=15";
error_log("\r\n url: $url\r\n", 3, "/srv/www/htdocs/error_log");
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_TIMEOUT, 600000);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
$headers = array();
$headers[] = 'access-token:' . $projectexpeditionaccesstoken;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$data = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);
$response = json_decode($data, true);

error_log("\r\n" . print_r($response, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
 error_log("\r\n$response\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    
    $sql = new Sql($dbProjectExpedition);
    $insert = $sql->insert();
    $insert->into('log_hotelbedsactivitiesdetails');
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
    $net_price =0;
        $currency = "";
        foreach ($response as $key => $value) {
            foreach ($value as $key => $value2) {
                if ($key == 'net_price') {
                    $net_price = $value2;
                    $amount = $net_price;
                    error_log("\r\n net_price: $net_price\r\n", 3, "/srv/www/htdocs/error_log");
                } elseif ($key == 'currency') {
                    $currency = $value2;
                    error_log("\r\n currency: $currency\r\n", 3, "/srv/www/htdocs/error_log");
                    if ($scurrency != "" and $currency != $scurrency) {
                        $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                    }
                    if ($projectexpeditionMarkup != 0) {
                        $amount = $amount + (($amount * $projectexpeditionMarkup) / 100);
                    }
                    // Geo target markup
                    if ($internalmarkup != 0) {
                        $amount = $amount + (($amount * $internalmarkup) / 100);
                    }
                    // Agent markup
                    if ($agent_markup > 0) {
                        $amount = $amount + (($amount * $agent_markup) / 100);
                    }
                    // Fallback Markup
                    if ($projectexpeditionMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                        $amount = $amount + (($amount * $ActivitiesMarkupFallback) / 100);
                    }
                    $amounts['amount'] = $filter->filter($amount);
                    $amounts['amountplain'] = $amount;
                    array_push($actvitiesPaxAmounts, $amounts);
                }
            }
        }
 

            $availability[$xmodalities]["uniqueIdentifier"] = $uniqueIdentifier;
            $availability[$xmodalities]["quoteid"] = md5(uniqid($session_id, true)) . "-" . $xmodalities . "-1";

    if (count($availability) > 0) {
        usort($availability, function ($a, $b) {
            return floatval($a['fromplain']) <=> floatval($b['fromplain']);
        });
    }
    $activity['availability'] = $availability;
    //
    // Store Session
    //
    $sql = new Sql($dbProjectExpedition);
    $delete = $sql->delete();
    $delete->from('quote_session_projectexpeditionactivitiesdetails');
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
    $sql = new Sql($dbProjectExpedition);
    $insert = $sql->insert();
    $insert->into('quote_session_projectexpeditionactivitiesdetails');
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
    $dbProjectExpedition->getDriver()
        ->getConnection()
        ->disconnect();
}
?>