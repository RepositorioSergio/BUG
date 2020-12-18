<?php
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
error_log("\r\nPolicies Cars - Priceline\r\n", 3, "/srv/www/htdocs/error_log");
$db = new \Laminas\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_priceline where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $xmlrequest = $row_settings["xmlrequest"];
    $xmlresult = $row_settings["xmlresult"];
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $from = $searchsettings['pickup_from'];
    error_log("\r\nDate from : " . $from . "\r\n", 3, "/srv/www/htdocs/error_log");
    $to = $searchsettings['dropoff_to'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    error_log("\r\nAgent_id : " . $agent_id . "\r\n", 3, "/srv/www/htdocs/error_log");
    $response['result'] = $data[$row];
    $vendor = $total + $response['result']['vendor'];
    error_log("\r\nVendor : " . $vendor . "\r\n", 3, "/srv/www/htdocs/error_log");
    $total = $total + $response['result']['total'];
    error_log("\r\nTotal : " . $total . "\r\n", 3, "/srv/www/htdocs/error_log");
    $ppn = $response['result']['car_reference_id'];
    error_log("\r\nPPN : " . $ppn . "\r\n", 3, "/srv/www/htdocs/error_log");
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enablePricelinecars' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_priceline = $affiliate_id;
} else {
    $affiliate_id_priceline = 0;
}
$sql = "select value from settings where name='PricelinecarsAPIKey' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelinecarsAPIKey = $row_settings['value'];
}
$sql = "select value from settings where name='PricelinecarsID' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelinecarsID = $row_settings['value'];
}
$sql = "select value from settings where name='PricelinecarsServiceURL' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $PricelinecarsServiceURL = $row['value'];
}
$sql = "select value from settings where name='Pricelinecarsaffiliates_id' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Pricelinecarsaffiliates_id = (double) $row_settings["value"];
}
$sql = "select value from settings where name='Pricelinecarsbranches_id' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Pricelinecarsbranches_id = (double) $row_settings["value"];
}
$sql = "select value from settings where name='Pricelinecarsb2cMarkup' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Pricelinecarsb2cMarkup = (double) $row_settings["value"];
}
$sql = "select value from settings where name='PricelinecarsTestMode' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelinecarsTestMode = (double) $row_settings["value"];
}
$sql = "select value from settings where name='PricelinecarsMarkup' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelinecarsMarkup = (double) $row_settings["value"];
}
$url = 'https://api-sandbox.rezserver.com/api/car/getContractRequest?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&sid=abcdefghijk&ppn_bundle=' . $ppn;
error_log("\r\nPriceline Request: $url\r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\nPriceline RESPONSE: $response \r\n", 3, "/srv/www/htdocs/error_log");
try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('log_priceline');
    $insert->values(array(
        'datetime_created' => time(),
        'filename' => 'Policies.php',
        'errorline' => "",
        'errormessage' => $PricelinecarsServiceURL,
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

$response = json_decode($response, true);

$getCarContractRequest = $response['getCarContractRequest'];
$results = $getCarContractRequest['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$inventory = $results['inventory'];
$contract_status = $results['contract_status'];
$partner_logo = $results['partner_logo'];
$partner_code = $results['partner_code'];
$partner_name = $results['partner_name'];
$PickUpCode = $results['PickUpCode'];
$pickup_code = $results['pickup_code'];
$pickup_cityid = $results['pickup_cityid'];
$pickup_name = $results['pickup_name'];
$pickup_airport_name = $results['pickup_airport_name'];
$pickup_airport_code = $results['pickup_airport_code'];
$pickup_address = $results['pickup_address'];
$pickup_city = $results['pickup_city'];
$pickup_state = $results['pickup_state'];
$pickup_country = $results['pickup_country'];
$pickup_zip = $results['pickup_zip'];
$pickup_latitude = $results['pickup_latitude'];
$pickup_longitude = $results['pickup_longitude'];
$pickup_location_information = $results['pickup_location_information'];
$DropOffCode = $results['DropOffCode'];
$dropoff_code = $results['dropoff_code'];
$dropoff_cityid = $results['dropoff_cityid'];
$dropoff_airport_name = $results['dropoff_airport_name'];
$dropoff_airport_code = $results['dropoff_airport_code'];
$dropoff_address = $results['dropoff_address'];
$dropoff_city = $results['dropoff_city'];
$dropoff_state = $results['dropoff_state'];
$dropoff_country = $results['dropoff_country'];
$dropoff_zip = $results['dropoff_zip'];
$dropoff_latitude = $results['dropoff_latitude'];
$dropoff_longitude = $results['dropoff_longitude'];
$dropoff_name = $results['dropoff_name'];
$dropoff_location_information = $results['dropoff_location_information'];
$pickup_time_text = $results['pickup_time_text'];
$dropoff_time_text = $results['dropoff_time_text'];
$pickup_date = $results['pickup_date'];
$pickup_time = $results['pickup_time'];
$dropoff_date = $results['dropoff_date'];
$dropoff_time = $results['dropoff_time'];
$driver_age_required = $results['driver_age_required'];
$guarantee_necessary = $results['guarantee_necessary'];
$shuttle_text = $results['shuttle_text'];
$car_book_bundle = $results['car_book_bundle'];
$output_version	 = $results['output_version	'];
$time = $results['time'];
$pickup_hours_data = $results['pickup_hours_data'];
if (count($pickup_hours_data) > 0) {
    for ($i=0; $i < count($pickup_hours_data); $i++) { 
        $open = $pickup_hours_data[$i]['open'];
        $close = $pickup_hours_data[$i]['close'];
    }
}
$dropoff_hours_data = $results['dropoff_hours_data'];
if (count($dropoff_hours_data) > 0) {
    for ($i=0; $i < count($dropoff_hours_data); $i++) { 
        $open = $dropoff_hours_data[$i]['open'];
        $close = $dropoff_hours_data[$i]['close'];
    }
}

$car_info = $results['car_info'];
$car_type = $car_info['car_type'];
$description = $car_info['description'];
$example = $car_info['example'];
$image = $car_info['image'];
$passengers = $car_info['passengers'];
$doors = $car_info['doors'];
$bags = $car_info['bags'];
$mileage = $car_info['mileage'];
$automatic_transmission = $car_info['automatic_transmission'];
$air_conditioning = $car_info['air_conditioning'];
$images = $car_info['images'];
$image = "";
foreach ($images as $key => $value) {
    $image = $value;
}

$pricing = $results['pricing'];
$display_price_strikeout = $pricing['display_price_strikeout'];
$source_price_strikeout = $pricing['source_price_strikeout'];
$baseline_price_strikeout = $pricing['baseline_price_strikeout'];
$subtotal = $pricing['subtotal'];
$display_subtotal = $pricing['display_subtotal'];
$source_subtotal = $pricing['source_subtotal'];
$baseline_subtotal = $pricing['baseline_subtotal'];
$total = $pricing['total'];
$display_total = $pricing['display_total'];
$source_total = $pricing['source_total'];
$baseline_total = $pricing['baseline_total'];
$total_strikeout = $pricing['total_strikeout'];
$display_total_strikeout = $pricing['display_total_strikeout'];
$source_total_strikeout = $pricing['source_total_strikeout'];
$baseline_total_strikeout = $pricing['baseline_total_strikeout'];
$baseline_savings_data = $pricing['baseline_savings_data'];
$source_savings_data = $pricing['source_savings_data'];
$display_savings_data = $pricing['display_savings_data'];
$member_rate = $pricing['member_rate'];
$prepaid_rate = $pricing['prepaid_rate'];
$prepaid_at_booking = $pricing['prepaid_at_booking'];
$prepaid_at_counter = $pricing['prepaid_at_counter'];
$prepaid_at_booking_display = $pricing['prepaid_at_booking_display'];
$prepaid_at_counter_display = $pricing['prepaid_at_counter_display'];
$total_label = $pricing['total_label'];
$currency = $pricing['currency'];
$display_currency = $pricing['display_currency'];
$source_currency = $pricing['source_currency'];
$baseline_currency = $pricing['baseline_currency'];
$display_symbol = $pricing['display_symbol'];
$source_symbol = $pricing['source_symbol'];
$baseline_symbol = $pricing['baseline_symbol'];
$breakdown_data = $pricing['breakdown_data'];
if (count($breakdown_data) > 0) {
    for ($j=0; $j < count($breakdown_data); $j++) { 
        $type = $breakdown_data[$j]['type'];
        $factor = $breakdown_data[$j]['factor'];
        $price = $breakdown_data[$j]['price'];
        $total = $breakdown_data[$j]['total'];
        $display_price = $breakdown_data[$j]['display_price'];
        $source_price = $breakdown_data[$j]['source_price'];
        $baseline_price = $breakdown_data[$j]['baseline_price'];
        $display_total = $breakdown_data[$j]['display_total'];
        $source_total = $breakdown_data[$j]['source_total'];
        $baseline_total = $breakdown_data[$j]['baseline_total'];
    }
}
$taxes_and_fees = $pricing['taxes_and_fees'];
$total = $taxes_and_fees['total'];
$display_total = $taxes_and_fees['display_total'];
$source_total = $taxes_and_fees['source_total'];
$baseline_total = $taxes_and_fees['baseline_total'];
$breakdown_data = $taxes_and_fees['breakdown_data'];
if (count($breakdown_data) > 0) {
    for ($jAux=0; $jAux < count($breakdown_data); $jAux++) { 
        $title = $breakdown_data[$jAux]['title'];
        $price = $breakdown_data[$jAux]['price'];
        $display_price = $breakdown_data[$jAux]['display_price'];
        $source_price = $breakdown_data[$jAux]['source_price'];
        $baseline_price = $breakdown_data[$jAux]['baseline_price'];
    }
}

$disclosure_data = $results['disclosure_data'];
if (count($disclosure_data) > 0) {
    for ($k=0; $k < count($disclosure_data); $k++) { 
        $id = $disclosure_data[$k]['id'];
        $name = $disclosure_data[$k]['name'];
    }
}

$special_request_data = $results['special_request_data'];
if (count($special_request_data) > 0) {
    for ($i=0; $i < count($special_request_data); $i++) { 
        $name = $special_request_data[$i]['name'];
        $code = $special_request_data[$i]['code'];
        $count = $special_request_data[$i]['count'];
        $description = $special_request_data[$i]['description'];
    }
}

$discount_codes = $results['discount_codes'];
$corp_discount = $discount_codes['corp_discount'];
$promo_code = $discount_codes['promo_code'];
$rate_code = $discount_codes['rate_code'];

$vendor_program_data = $results['vendor_program_data'];
if (count($vendor_program_data) > 0) {
    for ($l=0; $l < count($vendor_program_data); $l++) { 
        $name = $vendor_program_data[$l]['name'];
        $code = $vendor_program_data[$l]['code'];
        $value = $vendor_program_data[$l]['value'];
        $disable = $vendor_program_data[$l]['disable'];
    }
}

$partner_program_data = $results['partner_program_data'];
if (count($partner_program_data) > 0) {
    for ($l=0; $l < count($partner_program_data); $l++) { 
        $name = $partner_program_data[$l]['name'];
        $code = $partner_program_data[$l]['code'];
        $value = $partner_program_data[$l]['value'];
        $disable = $partner_program_data[$l]['disable'];
    }
}

$cdw = $results['cdw'];
$activated = $cdw['activated'];
$insurance_allowed = $cdw['insurance_allowed'];
$text = $cdw['text'];
$html = $cdw['html'];
$text_block = $cdw['text_block'];
$display_currency = $cdw['display_currency'];
$source_currency = $cdw['source_currency'];
$baseline_currency = $cdw['baseline_currency'];
$display_symbol = $cdw['display_symbol'];
$source_symbol = $cdw['source_symbol'];
$baseline_symbol = $cdw['baseline_symbol'];
$price_per_day = $cdw['price_per_day'];
$all = $price_per_day['all'];
$ny = $price_per_day['ny'];
$display_all = $price_per_day['display_all'];
$source_all = $price_per_day['source_all'];
$baseline_all = $price_per_day['baseline_all'];
$display_ny = $price_per_day['display_ny'];
$source_ny = $price_per_day['source_ny'];
$baseline_ny = $price_per_day['baseline_ny'];
$total = $cdw['total'];
$all = $total['all'];
$ny = $total['ny'];
$display_all = $total['display_all'];
$source_all = $total['source_all'];
$baseline_all = $total['baseline_all'];
$display_ny = $total['display_ny'];
$source_ny = $total['source_ny'];
$baseline_ny = $total['baseline_ny'];

$payment_method_data = $results['payment_method_data'];
if (count($payment_method_data) > 0) {
    for ($p=0; $p < count($payment_method_data); $p++) { 
        $name = $payment_method_data[$p]['name'];
        $code = $payment_method_data[$p]['code'];
    }
}

$customer_locations = $results['customer_locations'];
$user_input = $customer_locations['user_input'];
$country_data = $customer_locations['country_data'];
if (count($country_data)) {
    for ($r=0; $r < count($country_data); $r++) { 
        $code = $country_data[$r]['code'];
        $name = $country_data[$r]['name'];
        $region_data = $country_data[$r]['region_data'];
        if (count($region_data) > 0) {
            for ($rAux=0; $rAux < count($region_data); $rAux++) { 
                $name = $region_data[$rAux]['name'];
                $code = $region_data[$rAux]['code'];
            }
        }
    }
}

$booking_conditions = $results['booking_conditions'];
if (count($booking_conditions) > 0) {
   for ($s=0; $s < count($booking_conditions); $s++) { 
        $text = $booking_conditions[$s]['text'];
   }
}

$important_information = $results['important_information'];
if (count($important_information) > 0) {
    for ($t=0; $t < count($important_information); $t++) { 
        $title = $important_information[$t]['title'];
        $text = $important_information[$t]['text'];
    }
}

$car_policy_data = $results['car_policy_data'];
if (count($car_policy_data) > 0) {
    for ($x=0; $x < count($car_policy_data); $x++) { 
        $title = $car_policy_data[$x]['title'];
        $description = $car_policy_data[$x]['description'];
    }
}

$book_form_details = $results['book_form_details'];
$newsletter = $book_form_details['newsletter'];
?>