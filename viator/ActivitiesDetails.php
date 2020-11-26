<?php
// error_log("\r\nStart Activities - Details\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Http\Client;
use Laminas\Filter\AbstractFilter;
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
$dbHotelbeds = new \Laminas\Db\Adapter\Adapter($config);
$affiliate_id_hotelbeds = 0;
$sql = "select value from settings where name='packagesviatorapikey' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $packagesviatorapikey = $row_settings['value'];
}
$sql = "select value from settings where name='packagesviatorServiceFeePerc' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $packagesviatorServiceFeePerc = $row_settings['value'];
}
$sql = "select value from settings where name='packagesviatorTimeout' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $packagesviatorTimeout = $row_settings['value'];
}
$sql = "select value from settings where name='packagesviatorerviceurl' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $packagesviatorerviceurl = $row_settings['value'];
}
$sql = "select value from settings where name='packagesviatorCurrency' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $packagesviatorCurrency = $row_settings['value'];
}
$sql = "select value from settings where name='packagesviatorb2cMarkup' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $packagesviatorb2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='packagesviatorMarkup' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $packagesviatorMarkup = $row_settings['value'];
}
if ($packagesviatorMarkup == "") {
    $packagesviatorMarkup = 0;
}
if (! is_numeric($packagesviatorMarkup)) {
    $packagesviatorMarkup = 0;
}

$afrom = strftime("%Y-%m-%d", $from);
$ato = strftime("%Y-%m-%d", $to);
$tmp = 0;
$raw = '{
    "productCode": "5010SYDNEY",
    "travelDate": "' . $afrom . '",
    "currency": "' . $packagesviatorCurrency . '",
    "paxMix": [
      {
        "ageBand": "ADULT",
        "numberOfTravelers": ' . $adults . '
      },
      {
        "ageBand": "CHILD",
        "numberOfTravelers": ' . $children . '
      }
    ]
  }';

$startTime = microtime();
$client = new Client();
if ($packagesviatorTimeout == 0) {
    $packagesviatorTimeout = 120;
}
$client->setOptions(array(
    'timeout' => $packagesviatorTimeout,
    'sslverifypeer' => false
));
$client->setRawBody($raw);
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'exp-api-key' => $packagesviatorapikey,
    'Accept-Language' => 'en-US',
    'Accept' => 'application/json;version=2.0'
));
$client->setUri($packagesviatorerviceurl . 'partner/availability/check');
$client->setMethod('POST');
$response = $client->send();
if ($response->isSuccess()) {
    $response = $response->getBody();
} else {
    $failed = true;
}
$endTime = microtime();
error_log("\r\n$response\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    $response = json_decode($response, true);
    $sql = new Sql($dbHotelbeds);
    $insert = $sql->insert();
    $insert->into('log_viatoractivitiesdetails');
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
    $currency = $response['currency'];
    $productCode = $response['productCode'];
    $travelDate = $response['travelDate'];
    $bookableItems = $response['bookableItems'];
    if (count($bookableItems) > 0) {
        for ($i=0; $i < count($bookableItems); $i++) { 
            $productOptionCode = $bookableItems[$i]['productOptionCode'];
            $available = $bookableItems[$i]['available'];
            $totalPrice = $bookableItems[$i]['totalPrice'];
            $price = $totalPrice['price'];
            $recommendedRetailPrice = $price['recommendedRetailPrice'];
            error_log("\r\n$recommendedRetailPrice\r\n", 3, "/srv/www/htdocs/error_log");
            $partnerNetPrice = $price['partnerNetPrice'];
            $bookingFee = $price['bookingFee'];
            $partnerTotalPrice = $price['partnerTotalPrice'];
            $lineItems = $bookableItems[$i]['lineItems'];
            if (count($lineItems) > 0) {
                for ($iAux=0; $iAux < count($lineItems); $iAux++) { 
                    $ageBand = $lineItems[$iAux]['ageBand'];
                    $numberOfTravelers = $lineItems[$iAux]['numberOfTravelers'];
                    $subtotalPrice = $lineItems[$iAux]['subtotalPrice'];
                    $price = $subtotalPrice['price'];
                    $recommendedRetailPrice = $price['recommendedRetailPrice'];
                    $partnerNetPrice = $price['partnerNetPrice'];
                }
            }
            $rate['totalplain'] = $partnerNetPrice;
            $rate['total'] = (float) $recommendedRetailPrice;
            array_push($activityRates, $rate);
            $availability[$i]["code"] = $productOptionCode;
            $availability[$i]["activity_code"] = $productCode;
            $availability[$i]["activity_activityCode"] = $productCode;
            $availability[$i]["name"] = $name;
            $availability[$i]["currency"] = $currency;

            if ($scurrency != "" and $packagesviatorCurrency != $scurrency) {
                $recommendedRetailPrice = $CurrencyConverter->convert($recommendedRetailPrice, $packagesviatorCurrency, $scurrency);
            }
            $recommendedRetailPrice_net = $recommendedRetailPrice;
            if ($packagesviatorMarkup != 0) {
                $recommendedRetailPrice = $recommendedRetailPrice + (($recommendedRetailPrice * $packagesviatorMarkup) / 100);
            }
            // Geo target markup
            if ($internalmarkup != 0) {
                $recommendedRetailPrice = $recommendedRetailPrice + (($recommendedRetailPrice * $internalmarkup) / 100);
            }
            // Agent markup
            if ($agent_markup > 0) {
                $recommendedRetailPrice = $recommendedRetailPrice + (($recommendedRetailPrice * $agent_markup) / 100);
            }
            // Fallback Markup
            if ($packagesviatorMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                $recommendedRetailPrice = $recommendedRetailPrice + (($recommendedRetailPrice * $ActivitiesMarkupFallback) / 100);
            }
            
            error_log("\r\n PASSOU1 $recommendedRetailPrice \r\n", 3, "/srv/www/htdocs/error_log");
            $availability[$i]["rates"] = $activityRates;
            $availability[$i]["quoteid"] = md5(uniqid($session_id, true)) . "-" . $i . "-4";
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
    $sql = new Sql($dbHotelbeds);
    $delete = $sql->delete();
    $delete->from('quote_session_viatoractivities');
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
    $sql = new Sql($dbHotelbeds);
    $insert = $sql->insert();
    $insert->into('quote_session_viatoractivities');
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
    $dbHotelbeds->getDriver()
        ->getConnection()
        ->disconnect();
}
error_log("\r\nActivities Details - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>