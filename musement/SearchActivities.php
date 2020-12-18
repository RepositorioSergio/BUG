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
$db = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id, packages_xml14, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
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
    // error_log("\r\n$apiKey - $hotelbedsTicketslanguage - $sharedSecret - $endpoint - $hotelbedsTicketCurrencyCode - $hotelbedsTicketsMarkup \r\n", 3, "/srv/www/htdocs/error_log");

    $afrom = strftime("%Y-%m-%d", $from);
    $ato = strftime("%Y-%m-%d", $to);
    // error_log("\r\nFrom: $afrom / To: $ato\r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\nSignature: $signature\r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\napiKey: $apiKey\r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\nsharedSecret: $sharedSecret\r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\nActivities Search: $raw\r\n", 3, "/srv/www/htdocs/error_log");
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
    $client->setUri($tuimusementendpoint . "activities");
    $client->setMethod('GET');
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
    error_log("\r\nActivities Search: $response\r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();
    if ($response != "") {
        $response = json_decode($response,true);
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_musementactivities');
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
        $meta = $response['meta'];
        $count = $meta['count'];
        $data = $response['data'];
        if (count($data) > 0) {
            for ($i=0; $i < count($data); $i++) { 
                $operational_days = $data[$i]['operational_days'];
                $max_confirmation_time = $data[$i]['max_confirmation_time'];
                $cutoff_time = $data[$i]['cutoff_time'];
                $booking_type = $data[$i]['booking_type'];
                $uuid = $data[$i]['uuid'];
                $code = $uuid;
                $saves = $data[$i]['saves'];
                $title = $data[$i]['title'];
                $relevance = $data[$i]['relevance'];
                $relevance_venue = $data[$i]['relevance_venue'];
                $must_see = $data[$i]['must_see'];
                $last_chance = $data[$i]['last_chance'];
                $top_seller = $data[$i]['top_seller'];
                $voucher_access_usage = $data[$i]['voucher_access_usage'];
                $temporary = $data[$i]['temporary'];
                $description = $data[$i]['description'];
                $about = $data[$i]['about'];
                $meeting_point = $data[$i]['meeting_point'];
                // city
                $city = $data[$i]['city'];
                $cityid = $city['id'];
                $cityname = $city['name'];
                $cover_image_url = $city['cover_image_url'];
                $url = $city['url'];
                $time_zone = $city['time_zone'];
                $country = $city['country'];
                $countryid = $country['id'];
                $countryname = $country['name'];
                $iso_code = $country['iso_code'];

                $duration_range = $data[$i]['duration_range'];
                $duration_range_max = $duration_range['max'];

                $validity = $data[$i]['validity'];
                $has_price_info_on_date = $data[$i]['has_price_info_on_date'];
                $open = $data[$i]['open'];
                $ticket_not_included = $data[$i]['ticket_not_included'];
                $likely_to_sell_out = $data[$i]['likely_to_sell_out'];
                $special_offer = $data[$i]['special_offer'];
                $exclusive = $data[$i]['exclusive'];
                $best_price = $data[$i]['best_price'];
                $daily = $data[$i]['daily'];
                $is_available_today = $data[$i]['is_available_today'];
                $is_available_tomorrow = $data[$i]['is_available_tomorrow'];
                $cover_image_url = $data[$i]['cover_image_url'];
                $discount = $data[$i]['discount'];
                $reviews_number = $data[$i]['reviews_number'];
                $reviews_avg = $data[$i]['reviews_avg'];
                $url = $data[$i]['url'];
                $giftable = $data[$i]['giftable'];
                $buy_multiplier = $data[$i]['buy_multiplier'];
                $ticket = $data[$i]['ticket'];
                $free_cancellation = $data[$i]['free_cancellation'];

                $service_fee = $data[$i]['service_fee'];
                $currency = $service_fee['currency'];
                $value = $service_fee['value'];
                $formatted_value = $service_fee['formatted_value'];
                $formatted_iso_value = $service_fee['formatted_iso_value'];

                $retail_price = $data[$i]['retail_price'];
                $currency = $retail_price['currency'];
                $value = $retail_price['value'];
                $total = $value;
                $formatted_value = $retail_price['formatted_value'];
                $formatted_iso_value = $retail_price['formatted_iso_value'];

                $retail_price_without_service_fee = $data[$i]['retail_price_without_service_fee'];
                $currency = $retail_price_without_service_fee['currency'];
                $value = $retail_price_without_service_fee['value'];
                $formatted_value = $retail_price_without_service_fee['formatted_value'];
                $formatted_iso_value = $retail_price_without_service_fee['formatted_iso_value'];

                $original_retail_price_without_service_fee = $data[$i]['original_retail_price_without_service_fee'];
                $currency = $original_retail_price_without_service_fee['currency'];
                $value = $original_retail_price_without_service_fee['value'];
                $formatted_value = $original_retail_price_without_service_fee['formatted_value'];
                $formatted_iso_value = $original_retail_price_without_service_fee['formatted_iso_value'];

                $original_retail_price = $data[$i]['original_retail_price'];
                $currency = $original_retail_price['currency'];
                $value = $original_retail_price['value'];
                $formatted_value = $original_retail_price['formatted_value'];
                $formatted_iso_value = $original_retail_price['formatted_iso_value'];

                $languages = $data[$i]['languages'];
                if (count($languages) > 0) {
                    for ($j=0; $j < count($languages); $j++) { 
                        $languages_code = $languages[$j]['code'];
                        $name = $languages[$j]['name'];
                    }
                }

                $group_size = $data[$i]['group_size'];
                if (count($group_size) > 0) {
                    for ($k=0; $k < count($group_size); $k++) { 
                        $group_size_code = $group_size[$k]['code'];
                        $name = $group_size[$k]['name'];
                    }
                }

                $features = $data[$i]['features'];
                if (count($features) > 0) {
                    for ($l=0; $l < count($features); $l++) { 
                        $featurescode = $features[$l]['code'];
                        $name = $features[$l]['name'];
                    }
                }

                $categories = $data[$i]['categories'];
                if (count($categories) > 0) {
                    for ($r=0; $r < count($categories); $r++) { 
                        $id = $categories[$r]['id'];
                        $name = $categories[$r]['name'];
                        $level = $categories[$r]['level'];
                        $code = $categories[$r]['code'];
                        $event_image_url = $categories[$r]['event_image_url'];
                        $cover_image_url = $categories[$r]['cover_image_url'];
                        $url = $categories[$r]['url'];
                    }
                }

                $flavours = $data[$i]['flavours'];
                if (count($flavours) > 0) {
                    for ($x=0; $x < count($flavours); $x++) { 
                        $id = $flavours[$x]['id'];
                        $name = $flavours[$x]['name'];
                        $active = $flavours[$x]['active'];
                        $slug = $flavours[$x]['slug'];
                    }
                }

                $verticals = $data[$i]['verticals'];
                if (count($verticals) > 0) {
                    for ($y=0; $y < count($verticals); $y++) { 
                        $id = $verticals[$y]['id'];
                        $name = $verticals[$y]['name'];
                        $active = $verticals[$y]['active'];
                        $verticalscode = $verticals[$y]['code'];
                        $slug = $verticals[$y]['slug'];
                        $url = $verticals[$y]['url'];
                        $meta_title = $verticals[$y]['meta_title'];
                        $meta_description = $verticals[$y]['meta_description'];
                        $cover_image_url = $verticals[$y]['cover_image_url'];
                        $relevance = $verticals[$y]['relevance'];
                    }
                }

                if ($scurrency != "" and $CurrencyCode != $scurrency) {
                    $total = $CurrencyConverter->convert($total, $CurrencyCode, $scurrency);
                }
                if ($tuimusementMarkup != 0) {
                    $total = $total + (($total * $tuimusementMarkup) / 100);
                }
                if ($internalmarkup != 0) {
                    $total = $total + (($total * $internalmarkup) / 100);
                }
                // Agent markup
                if ($agent_markup > 0) {
                    $total = $total + (($total * $agent_markup) / 100);
                }
                // Fallback Markup
                if ($tuimusementMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                    $total = $total + (($total * $ActivitiesMarkupFallback) / 100);
                }
                // Agent discount
                if ($agent_discount != 0) {
                    $total = $total - (($total * $agent_discount) / 100);
                }
                $activities_array[$code]['fromplain'] = $total;
                $activities_array[$code]['from'] = $filter->filter($total);
                $activities_array[$code]['netcurrency'] = $currency;
                $activities_array[$code]['currency'] = $scurrency;
                $sfilter[] = "'$code'";
            }
        }
        // error_log("\r\nX:" . print_r($activities_array, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        if (is_array($sfilter)) {
            $sfilter = implode(',', $sfilter);
            if ($sfilter != "") {
                $query = 'call xmlactivities("' . $sfilter . '")';
                error_log("\r\n$query - $session_id\r\n", 3, "/srv/www/htdocs/error_log");
                $supplier = 8;
                // Store Session
                try {
                    $sql = new Sql($db);
                    $delete = $sql->delete();
                    $delete->from('quote_session_musementactivities');
                    $delete->where(array(
                        'session_id' => $session_id
                    ));
                    $statement = $sql->prepareStatementForSqlObject($delete);
                    $results = $statement->execute();
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('quote_session_musementactivities');
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>