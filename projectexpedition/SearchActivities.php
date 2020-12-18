<?php
error_log("\r\nSearch Activities - Project Expedition\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Laminas\Http\Client;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$failed = false;
$dbProjectExpedition = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id, packages_xml14, latitude, longitude from cities where id=" . $destination;
$statement2 = $dbProjectExpedition->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $packages_xml14 = $row_settings["packages_xml14"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $packages_xml14 = "";
    $latitude = 0;
    $longitude = 0;
}
if ($packages_xml14 != "" or ($latitude != 0 and $longitude != 0)) {
    $activities_array = array();
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
    $ch = curl_init();
    // TODO - Remove
    $package_code = "16350";
    // standard, child, elderly, student
    // Adults / Children (As defined in Pricing Rates)
    $url = $projectexpeditionwebserviceURL . "v1/return_availability?id=" . $package_code . "&standard=2&elderly=0&Adults=2&Children=0&date=" . strftime("%Y-%m-%d", $from) . "&range=15";
    error_log("\r\nActivities Search URL: $url\r\n", 3, "/srv/www/htdocs/error_log");
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
    
    if ($response != "") {
       //$response = json_decode($response, true);
       error_log("\r\nActivities Search: $response\r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($dbProjectExpedition);
            $insert = $sql->insert();
            $insert->into('log_projectexpeditionactivities');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchActivities.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $raw,
                'sqlcontext' => $response,
                'errcontext' => ''
            ), $insert::VALUES_MERGE);
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
                    error_log("\r\n net_price: $net_price\r\n", 3, "/srv/www/htdocs/error_log");
                } elseif ($key == 'currency') {
                    $currency = $value2;
                    error_log("\r\n currency: $currency\r\n", 3, "/srv/www/htdocs/error_log");
                }
            }
        }
        if ($scurrency != "" and $currency != $scurrency) {
            $net_price = $CurrencyConverter->convert($net_price, $currency, $scurrency);
        }
        if ($projectexpeditionMarkup != 0) {
            $net_price = $net_price + (($net_price * $projectexpeditionMarkup) / 100);
        }
        if ($internalmarkup != 0) {
            $net_price = $net_price + (($net_price * $internalmarkup) / 100);
        }
        // Agent markup
        if ($agent_markup > 0) {
            $net_price = $net_price + (($net_price * $agent_markup) / 100);
        }
        // Fallback Markup
        if ($projectexpeditionMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
            $net_price = $net_price + (($net_price * $ActivitiesMarkupFallback) / 100);
        }
        // Agent discount
        if ($agent_discount != 0) {
            $net_price = $net_price - (($net_price * $agent_discount) / 100);
        }
        $code = 1;
        $activities_array[$code]['fromplain'] = $net_price;
        $activities_array[$code]['from'] = $filter->filter($net_price);
        $activities_array[$code]['netcurrency'] = $currency;
        $activities_array[$code]['currency'] = $scurrency;
        $sfilter[] = "'$code'";


        error_log("\r\nX:" . print_r($activities_array, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        if (is_array($sfilter)) {
            $sfilter = implode(',', $sfilter);
            if ($sfilter != "") {
                $query = 'call xmlactivities("' . $sfilter . '")';
                error_log("\r\n$query - $session_id\r\n", 3, "/srv/www/htdocs/error_log");
                $supplier = 1;
                // Store Session
                try {
                    $sql = new Sql($dbProjectExpedition);
                    $delete = $sql->delete();
                    $delete->from('quote_session_projectexpeditionactivities');
                    $delete->where(array(
                        'session_id' => $session_id
                    ));
                    $statement = $sql->prepareStatementForSqlObject($delete);
                    $results = $statement->execute();
                    $sql = new Sql($dbProjectExpedition);
                    $insert = $sql->insert();
                    $insert->into('quote_session_projectexpeditionactivities');
                    $insert->values(array(
                        'session_id' => $session_id,
                        'xmlrequest' => (string) $raw,
                        'xmlresult' => (string) $response,
                        'data' => base64_encode(serialize($activities_array)),
                        'searchsettings' => base64_encode(serialize($requestdata))
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                } catch (\Exception $e) {
                    $logger = new Logger();
                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                }
            }
        }
    }
}
$dbProjectExpedition->getDriver()
    ->getConnection()
    ->disconnect();
?>