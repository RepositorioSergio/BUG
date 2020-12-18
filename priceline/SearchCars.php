<?php
error_log("\r\nSearchCars - Priceline\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
$vehicle = array();
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
$db = new \Laminas\Db\Adapter\Adapter($config);
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enablePricelinecars' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_carnect = $affiliate_id;
} else {
    $affiliate_id_carnect = 0;
}
$sql = "select value from settings where name='PricelinecarsAPIKey' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelinecarsAPIKey = $row_settings['value'];
}
$sql = "select value from settings where name='PricelinecarsID' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelinecarsID = $row_settings['value'];
}
$sql = "select value from settings where name='PricelinecarsServiceURL' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $PricelinecarsServiceURL = $row['value'];
}
$sql = "select value from settings where name='Pricelinecarsaffiliates_id' and affiliate_id=$affiliate_id_carnect";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Pricelinecarsaffiliates_id = (double) $row_settings["value"];
}
$sql = "select value from settings where name='Pricelinecarsbranches_id' and affiliate_id=$affiliate_id_carnect";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Pricelinecarsbranches_id = (double) $row_settings["value"];
}
$sql = "select value from settings where name='Pricelinecarsb2cMarkup' and affiliate_id=$affiliate_id_carnect";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Pricelinecarsb2cMarkup = (double) $row_settings["value"];
}
$sql = "select value from settings where name='PricelinecarsTestMode' and affiliate_id=$affiliate_id_carnect";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelinecarsTestMode = (double) $row_settings["value"];
}
$sql = "select value from settings where name='PricelinecarsMarkup' and affiliate_id=$affiliate_id_carnect";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelinecarsMarkup = (double) $row_settings["value"];
}
$sql = "select code, airport_code, name, city, latitude, longitude from carlocation where id=" . $pickup_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $pickup = $row_settings["airport_code"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
    $city = $row_settings["city"];
}
$sql = "select code, airport_code, name, city, latitude, longitude from carlocation where id=" . $dropoff_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $dropoff = $row_settings["airport_code"];
    $latitude2 = $row_settings["latitude"];
    $longitude2 = $row_settings["longitude"];
}
if ($dropoff == "") {
    $dropoff = $pickup;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

if ($PricelinecarsServiceURL != "") {
    $sid = bin2hex(random_bytes(16));
    $pickup_code = $pickup;
    $dropoff_code = $dropoff;
    $from = strftime("%Y-%m-%d", $from);
    $to = strftime("%Y-%m-%d", $to);
    $url = $PricelinecarsServiceURL . 'car/getResultsV3?format=json2&refid=' . $PricelinecarsID . '&api_key=' . $PricelinecarsAPIKey . '&sid=' . $sid . '&pickup_code=' . $pickup_code . '&dropoff_code=' . $dropoff_code . '&pickup_date=' . $from . '&pickup_time=' . $pickup_time . '&dropoff_date=' . $to . '&dropoff_time=' . $dropoff_time . '';
    error_log("\r\nPriceline URL: $url\r\n", 3, "/srv/www/htdocs/error_log");
    $headers = array(
        "Cache-Control: no-cache"
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    // error_log("\r\nPriceline RESPONSE $response \r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_priceline');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCars.php',
            'errorline' => 0,
            'errormessage' => '',
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
    if ($response != "") {
        $response = json_decode($response, true);
        $getCarResultsV3 = $response['getCarResultsV3'];
        $results = $getCarResultsV3['results'];
        $status = $results['status'];
        $status_code = $results['status_code'];
        $xml_cars = $results['xml_cars'];
        $xml_tweb_cars = $results['xml_tweb_cars'];
        $xml_rc_cars = $results['xml_rc_cars'];
        $result_list = $results['result_list'];
        if (count($result_list) > 0) {
            for ($j = 0; $j < count($result_list); $j ++) {
                $inventory = $result_list[$j]['inventory'];
                $creditCardRequired = $result_list[$j]['creditCardRequired'];
                $disclosure_required = $result_list[$j]['disclosure_required'];
                $driver_age_required = $result_list[$j]['driver_age_required'];
                $contract_page_url = $result_list[$j]['contract_page_url'];
                $car_reference_id = $result_list[$j]['car_reference_id'];
                // error_log("\r\nPriceline PPN: $car_reference_id\r\n", 3, "/srv/www/htdocs/error_log");
                $partner = $result_list[$j]['partner'];
                $code = $partner['code'];
                $name = $partner['name'];
                $logo = $partner['logo'];

                $car = $result_list[$j]['car'];
                $example = $car['example'];
                $description = $car['description'];
                $type = $car['type'];
                $type_name = $car['type_name'];
                $vehicle_code = $car['vehicle_code'];
                $passengers = $car['passengers'];
                $doors = $car['doors'];
                $bags = $car['bags'];
                $automatic_transmission = $car['automatic_transmission'];
                $air_conditioning = $car['air_conditioning'];
                $imageurl = $car['imageurl'];
                $images = $car['images'];
                $image = "";
                foreach ($images as $key => $value) {
                    $image = $value;
                }
                $partner_discounts = $car['partner_discounts'];

                $pickup = $result_list[$j]['pickup'];
                $pickup_location = $pickup['location'];
                $pickup_location_code = $pickup['location_code'];
                $latitude = $pickup['latitude'];
                $longitude = $pickup['longitude'];
                $city_center_distance = $pickup['city_center_distance'];
                $neighborhood = $pickup['neighborhood'];
                $location_information = $pickup['location_information'];

                $dropoff = $result_list[$j]['dropoff'];
                $dropoff_location = $dropoff['location'];
                $dropoff_location_code = $dropoff['location_code'];
                $latitude = $dropoff['latitude'];
                $longitude = $dropoff['longitude'];
                $city_center_distance = $dropoff['city_center_distance'];
                $neighborhood = $dropoff['neighborhood'];
                $location_information = $dropoff['location_information'];

                $price_details = $result_list[$j]['price_details'];
                $rate = $price_details['rate'];
                $base_price = $price_details['base_price'];
                $base_strikeout_price = $price_details['base_strikeout_price'];
                $base_type = $price_details['base_type'];
                $num_rental_days = $price_details['num_rental_days'];
                $sub_total = $price_details['sub_total'];
                $total_price = $price_details['total_price'];
                $total_strikeout_price = $price_details['total_strikeout_price'];
                $total_price_float = $price_details['total_price_float'];
                $currency = $price_details['currency'];
                $member_rate = $price_details['member_rate'];
                $campaign_rate = $price_details['campaign_rate'];
                $savings = $price_details['savings'];
                $display_price = $price_details['display_price'];
                $source_price = $price_details['source_price'];
                $baseline_price = $price_details['baseline_price'];
                $display_price_strikeout = $price_details['display_price_strikeout'];
                $source_price_strikeout = $price_details['source_price_strikeout'];
                $baseline_price_strikeout = $price_details['baseline_price_strikeout'];
                $display_sub_total = $price_details['display_sub_total'];
                $source_sub_total = $price_details['source_sub_total'];
                $baseline_sub_total = $price_details['baseline_sub_total'];
                $display_total = $price_details['display_total'];
                $source_total = $price_details['source_total'];
                $baseline_total = $price_details['baseline_total'];
                $display_total_strikeout = $price_details['display_total_strikeout'];
                $source_total_strikeout = $price_details['source_total_strikeout'];
                $baseline_total_strikeout = $price_details['baseline_total_strikeout'];
                $display_total_float = $price_details['display_total_float'];
                $source_total_float = $price_details['source_total_float'];
                $baseline_total_float = $price_details['baseline_total_float'];
                $display_currency = $price_details['display_currency'];
                $source_currency = $price_details['source_currency'];
                $baseline_currency = $price_details['baseline_currency'];
                $source_symbol = $price_details['source_symbol'];
                $baseline_symbol = $price_details['baseline_symbol'];
                $display_symbol = $price_details['display_symbol'];
                $pay_at_booking = $price_details['pay_at_booking'];
                $mileage = $price_details['mileage'];
                $free_cancellation = $price_details['free_cancellation'];
                $net_rate = $price_details['net_rate'];

                $priority = $result_list[$j]['priority'];
                $company = $priority['company'];

                if ($CodeContext != "") {
                    $CodeContext = $CodeContext;
                } else {
                    $CodeContext = $Code . "or similar";
                }

                $cars[$counter]['id'] = $counter;
                $cars[$counter]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-16-" . $counter;
                $cars[$counter]['vendorpicture'] = $logo;
                $cars[$counter]['vendorcode'] = $code;
                $cars[$counter]['vendor'] = $name;
                $cars[$counter]['vendorshortname'] = $example;
                $cars[$counter]['size'] = $passengers;
                $cars[$counter]['doors'] = $doors;
                $cars[$counter]['aircondition'] = $air_conditioning;
                $cars[$counter]['transmission'] = $transmission;
                $cars[$counter]['bags'] = $bags;
                $cars[$counter]['status'] = $Status;
                $cars[$counter]['from'] = $from;
                $cars[$counter]['to'] = $to;
                $cars[$counter]['pickup'] = ucwords(strtolower($pickup_location_code));
                $cars[$counter]['dropoff'] = ucwords(strtolower($dropoff_location_code));
                $cars[$counter]['class'] = $vehicle_code;
                $cars[$counter]['currency'] = $currency;
                $cars[$counter]['productId'] = $productId;
                $cars[$counter]['programId'] = $CarProgramId;
                $cars[$counter]['name'] = $example;
                $cars[$counter]['picture'] = $imageurl;
                $cars[$counter]['programname'] = $CarProgramName;
                $cars[$counter]['coverage'] = $coverage;
                $cars[$counter]['car_reference_id'] = $car_reference_id;
                $cars[$counter]['netcurrency'] = $currency;
                $cars[$counter]['netprice'] = $total_price;
                // Total including VAT in renting country currency
                /*
                 * if ($minPrice < $CarProgramPrice) {
                 * $minPrice = $CarProgramPrice;
                 * }
                 * $minPrice = number_format($minPrice, 2, ".", "");
                 * if ($carstouricoholidaysMarkup != 0) {
                 * $minPrice = $minPrice + (($minPrice * $carstouricoholidaysMarkup) / 100);
                 * }
                 * if ($agent_markup != 0) {
                 * $minPrice = $minPrice + (($minPrice * $agent_markup) / 100);
                 * }
                 * if ($CarProgramCurrency != "") {
                 * if ($CarProgramCurrency != $scurrency) {
                 * $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
                 * }
                 * } else {
                 * if ($currencyBase != "") {
                 * if ($currencyBase != $scurrency) {
                 * $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
                 * }
                 * }
                 * }
                 */
                $dailytotal = $total_price / $nights;
                $dailytotal = number_format($dailytotal, 2, ".", "");
                // $minPrice = number_format($minPrice, 2, ".", "");
                $cars[$counter]['currency'] = $scurrency;
                $cars[$counter]['total'] = $filter->filter($total_price);
                $cars[$counter]['dailytotal'] = $filter->filter($dailytotal);
                $cars[$counter]['dueatpickupplain'] = $DueAtPickup;
                $cars[$counter]['dueatpickup'] = $filter->filter($DueAtPickup);
                $cars[$counter]['dueatpickupcurrency'] = $filter->filter($currency);
                // Location
                // $cars[$counter]['special'] = 1;
                // $cars[$counter]['recommended'] = 1;
                $counter = $counter + 1;
            }
        }
        //
        // Store Session
        //
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_priceline');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_priceline');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $xmlresult,
                'data' => base64_encode(serialize($cars)),
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>