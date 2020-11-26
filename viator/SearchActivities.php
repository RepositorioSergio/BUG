<?php
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
$dbHotelbeds = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id, packages_xml04, latitude, longitude from cities where id=" . $destination;
$statement2 = $dbHotelbeds->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $packages_xml04 = $row_settings["packages_xml04"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $packages_xml04 = "";
    $latitude = 0;
    $longitude = 0;
}
if ($packages_xml04 != "" or ($latitude != 0 and $longitude != 0)) {
    $activities_array = array();
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
    error_log("\r\n  packagesviatorerviceurl: $packagesviatorerviceurl \r\n", 3, "/srv/www/htdocs/error_log");
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
    // error_log("\r\n$apiKey - $hotelbedsTicketslanguage - $sharedSecret - $endpoint - $hotelbedsTicketCurrencyCode - $hotelbedsTicketsMarkup \r\n", 3, "/srv/www/htdocs/error_log");
    $afrom = strftime("%Y-%m-%d", $from);
    $ato = strftime("%Y-%m-%d", $to);
    // error_log("\r\nFrom: $afrom / To: $ato\r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\nSignature: $signature\r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\napiKey: $apiKey\r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\nsharedSecret: $sharedSecret\r\n", 3, "/srv/www/htdocs/error_log");
    $raw = '{
        "productCodes": [
          "265910P1",
          "5010SYDNEY"
        ]
      }';

    // error_log("\r\nActivities Search: $raw\r\n", 3, "/srv/www/htdocs/error_log");
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
    $client->setUri($packagesviatorerviceurl . 'partner/availability/schedules/bulk');
    $client->setMethod('POST');
    try {
        $response = $client->send();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if ($response->isSuccess()) {
        $response = $response->getBody();
    } else {
        $failed = true;
    }
    // error_log("\r\nActivities Response: $response\r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();
    if ($response != "") {
        $response = json_decode($response, true);
        try {
            $sql = new Sql($dbHotelbeds);
            $insert = $sql->insert();
            $insert->into('log_viatoractivities');
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
        $availabilitySchedules = $response['availabilitySchedules'];
        if (count($availabilitySchedules) > 0) {
            for ($j=0; $j < count($availabilitySchedules); $j++) { 
                $productCode = $availabilitySchedules[$j]['productCode'];
                $currency = $availabilitySchedules[$j]['currency'];
                $bookableItems = $availabilitySchedules[$j]['bookableItems'];
                if (count($bookableItems) > 0) {
                    for ($i=0; $i < count($bookableItems); $i++) { 
                        $productOptionCode = $bookableItems[$i]['productOptionCode'];
                        $code = $productOptionCode;
                        $seasons = $bookableItems[$i]['seasons'];
                        if (count($seasons) > 0) {
                            for ($iAux=0; $iAux < count($seasons); $iAux++) { 
                                $startDate = $seasons[$iAux]['startDate'];
                                $endDate = $seasons[$iAux]['endDate'];
                                $pricingRecords = $seasons[$iAux]['pricingRecords'];
                                if (count($pricingRecords) > 0) {
                                    for ($iAux2=0; $iAux2 < count($pricingRecords); $iAux2++) { 
                                            $daysOfWeek = $pricingRecords[$iAux2]['daysOfWeek'];
                                            if (count($daysOfWeek) > 0) {
                                                $dayOfWeek = "";
                                                for ($iAux3=0; $iAux3 < count($daysOfWeek); $iAux3++) { 
                                                    $dayOfWeek = $daysOfWeek[$iAux3];
                                                }
                                            }
                                            $pricingDetails = $pricingRecords[$iAux2]['pricingDetails'];
                                            if (count($pricingDetails) > 0) {
                                                for ($iAux4=0; $iAux4 < count($pricingDetails); $iAux4++) { 
                                                    $pricingPackageType = $pricingDetails[$iAux4]['pricingPackageType'];
                                                    $minTravelers = $pricingDetails[$iAux4]['minTravelers'];
                                                    $ageBand = $pricingDetails[$iAux4]['ageBand'];
                                                    $price = $pricingDetails[$iAux4]['price'];
                                                    $original = $price['original'];
                                                    $recommendedRetailPrice = $original['recommendedRetailPrice'];
                                                    $partnerNetPrice = $original['partnerNetPrice'];
                                                    $bookingFee = $original['bookingFee'];
                                                    $partnerTotalPrice = $original['partnerTotalPrice'];
                                                    if ($scurrency != "" and $CurrencyCode != $scurrency) {
                                                        $recommendedRetailPrice = $CurrencyConverter->convert($recommendedRetailPrice, $CurrencyCode, $scurrency);
                                                    }
                                                    if ($packagesviatorMarkup != 0) {
                                                        $recommendedRetailPrice = $recommendedRetailPrice + (($recommendedRetailPrice * $packagesviatorMarkup) / 100);
                                                    }
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
                                                    // Agent discount
                                                    if ($agent_discount != 0) {
                                                        $recommendedRetailPrice = $recommendedRetailPrice - (($recommendedRetailPrice * $agent_discount) / 100);
                                                    }
                                                    $activities_array[$code]['fromplain'] = $recommendedRetailPrice;
                                                    $activities_array[$code]['from'] = $filter->filter($recommendedRetailPrice);
                                                    $activities_array[$code]['netcurrency'] = $currency;
                                                    $activities_array[$code]['currency'] = $scurrency;
                                                    $sfilter[] = "'$code'";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        error_log("\r\nX:" . print_r($activities_array, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        if (is_array($sfilter)) {
            $sfilter = implode(',', $sfilter);
            if ($sfilter != "") {
                $query = 'call xmlactivities("' . $sfilter . '")';
                error_log("\r\n$query - $session_id\r\n", 3, "/srv/www/htdocs/error_log");
                $supplier = 4;
                // Store Session
                try {
                    $sql = new Sql($dbHotelbeds);
                    $delete = $sql->delete();
                    $delete->from('quote_session_viatoractivities');
                    $delete->where(array(
                        'session_id' => $session_id
                    ));
                    $statement = $sql->prepareStatementForSqlObject($delete);
                    $results = $statement->execute();
                    $sql = new Sql($dbHotelbeds);
                    $insert = $sql->insert();
                    $insert->into('quote_session_viatoractivities');
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
$dbHotelbeds->getDriver()
    ->getConnection()
    ->disconnect();
?>