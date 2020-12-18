<?php
require '../vendor/autoload.php';
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Config;
use Zend\Log\Logger;
use Zend\Log\Writer;
echo "COMECOU ";
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}
$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://api-sandbox.rezserver.com/api/car/getLookUp';

$raw = '
{
	"sid": "test",
	"booking_id": 10023422476,
	"email": "john.doe@example.com"
}';
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/json;charset=utf-8'
));
$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
    $response = $response->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}

echo $return;
echo $response;
echo $return; 
$response = json_decode($response, true);


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$getCarLookUp = $response['getCarLookUp'];
$results = $getCarLookUp['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$inventory = $results['inventory'];
$lookup_status = $results['lookup_status'];
$booking_id = $results['booking_id'];
$offer_token = $results['offer_token'];
$booking_status = $results['booking_status'];
$booking_status_code = $results['booking_status_code'];
$is_chg_cxl_allowed = $results['is_chg_cxl_allowed'];
$is_cancellation_allowed = $results['is_cancellation_allowed'];
$rate_type = $results['rate_type'];
$cancellation_method = $results['cancellation_method'];
$cancellation_rules = $results['cancellation_rules'];
$reservation_changed = $results['reservation_changed'];
$partner_confirmation_number = $results['partner_confirmation_number'];
$partner_logo = $results['partner_logo'];
$partner_name = $results['partner_name'];
$partner_code = $results['partner_code'];
$partner_phone = $results['partner_phone'];
$pickup_code = $results['pickup_code'];
$statuspickup_city_id = $results['pickup_city_id'];
$pickup_name = $results['pickup_name'];
$pickup_address = $results['pickup_address'];
$pickup_city = $results['pickup_city'];
$pickup_state = $results['pickup_state'];
$pickup_country = $results['pickup_country'];
$pickup_zip = $results['pickup_zip'];
$pickup_latitude = $results['pickup_latitude'];
$pickup_longitude = $results['pickup_longitude'];
$pickup_location_code = $results['pickup_location_code'];
$pickup_location_info = $results['pickup_location_info'];
$dropoff_code = $results['dropoff_code'];
$dropoff_city_id = $results['dropoff_city_id'];
$dropoff_name = $results['dropoff_name'];
$dropoff_address = $results['dropoff_address'];
$dropoff_city = $results['dropoff_city'];
$dropoff_state = $results['dropoff_state'];
$dropoff_country = $results['dropoff_country'];
$dropoff_zip = $results['dropoff_zip'];
$dropoff_latitude = $results['dropoff_latitude'];
$dropoff_longitude = $results['dropoff_longitude'];
$dropoff_location_code = $results['dropoff_location_code'];
$dropoff_location_info = $results['dropoff_location_info'];
$pickup_time_text = $results['pickup_time_text'];
$dropoff_time_text = $results['dropoff_time_text'];
$pickup_date = $results['pickup_date'];
$pickup_time = $results['pickup_time'];
$dropoff_date = $results['dropoff_date'];
$dropoff_time = $results['dropoff_time'];
$pickup_timezone = $results['pickup_timezone'];
$dropoff_timezone = $results['dropoff_timezone'];
$flight_airline = $results['flight_airline'];
$flight_number = $results['flight_number'];
$shuttle_text = $results['shuttle_text'];
$reservation_url = $results['reservation_url'];
$rcc_booking_id = $results['rcc_booking_id'];
$rcc_phone_number = $results['rcc_phone_number'];
$version = $results['version'];

$driver = $results['driver'];
$title = $driver['title'];
$first_name = $driver['first_name'];
$last_name = $driver['last_name'];

$customer = $results['customer'];
$email = $customer['email'];
$phone = $customer['phone'];

$car_info = $results['car_info'];
$code = $car_info['code'];
$description = $car_info['description'];
$example = $car_info['example'];
$image = $car_info['image'];
$mileage = $car_info['mileage'];
$passengers = $car_info['passengers'];
$bags = $car_info['bags'];
$automatic_transmission = $car_info['automatic_transmission'];
$air_conditioning = $car_info['air_conditioning'];
$images = $car_info['images'];
if (count($images) > 0) {
    $image = "";
    for ($i=0; $i < count($images); $i++) { 
        $image = $images[$i];
    }
}

$pricing = $results['pricing'];
$currency = $pricing['currency'];
$display_currency = $pricing['display_currency'];
$baseline_currency = $pricing['baseline_currency'];
$symbol = $pricing['symbol'];
$display_symbol = $pricing['display_symbol'];
$baseline_symbol = $pricing['baseline_symbol'];
$subtotal = $pricing['subtotal'];
$display_subtotal = $pricing['display_subtotal'];
$baseline_subtotal = $pricing['baseline_subtotal'];
$total = $pricing['total'];
$display_total = $pricing['display_total'];
$baseline_total = $pricing['baseline_total'];
$est_commission = $pricing['est_commission'];
$display_est_commission = $pricing['display_est_commission'];
$baseline_est_commission = $pricing['baseline_est_commission'];
$ti_est_commission = $pricing['ti_est_commission'];
$ti_display_est_commission = $pricing['ti_display_est_commission'];
$ti_baseline_est_commission = $pricing['ti_baseline_est_commission'];
$combined_est_commission = $pricing['combined_est_commission'];
$combined_display_est_commission = $pricing['combined_display_est_commission'];
$combined_baseline_est_commission = $pricing['combined_baseline_est_commission'];
$prepaid_at_booking = $pricing['prepaid_at_booking'];
$prepaid_at_counter = $pricing['prepaid_at_counter'];
$prepaid_at_booking_display = $pricing['prepaid_at_booking_display'];
$prepaid_at_counter_display = $pricing['prepaid_at_counter_display'];
$prepaid_at_booking_baseline = $pricing['prepaid_at_booking_baseline'];
$prepaid_at_counter_baseline = $pricing['prepaid_at_counter_baseline'];
$total_label = $pricing['total_label'];
$breakdown_data = $pricing['breakdown_data'];
if (count($breakdown_data) > 0) {
    for ($j=0; $j < count($breakdown_data); $j++) { 
        $type = $breakdown_data[$j]['type'];
        $factor = $breakdown_data[$j]['factor'];
        $price = $breakdown_data[$j]['price'];
        $display_price = $breakdown_data[$j]['display_price'];
        $baseline_price = $breakdown_data[$j]['baseline_price'];
        $total = $breakdown_data[$j]['total'];
        $display_total = $breakdown_data[$j]['display_total'];
        $baseline_total = $breakdown_data[$j]['baseline_total'];
    }
}
$taxes_and_fees = $pricing['taxes_and_fees'];
$total = $taxes_and_fees['total'];
$display_price = $taxes_and_fees['display_price'];
$baseline_price = $taxes_and_fees['baseline_price'];
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

$cdw_upsell = $results['cdw_upsell'];
$display = $cdw_upsell['display'];

$postsale_insurance = $results['postsale_insurance'];
$display = $postsale_insurance['display'];
$text = $postsale_insurance['text'];
$details_data = $postsale_insurance['details_data'];
$html = $postsale_insurance['html'];
$display_currency = $postsale_insurance['display_currency'];
$source_currency = $postsale_insurance['source_currency'];
$baseline_currency = $postsale_insurance['baseline_currency'];
$display_symbol = $postsale_insurance['display_symbol'];
$source_symbol = $postsale_insurance['source_symbol'];
$baseline_symbol = $postsale_insurance['baseline_symbol'];
$insurance_bundle = $postsale_insurance['insurance_bundle'];
$customer_locations = $postsale_insurance['customer_locations'];
$price_per_day = $postsale_insurance['price_per_day'];
$all = $price_per_day['all'];
$ny = $price_per_day['ny'];
$display_all = $price_per_day['display_all'];
$source_all = $price_per_day['source_all'];
$baseline_all = $price_per_day['baseline_all'];
$display_ny = $price_per_day['display_ny'];
$source_ny = $price_per_day['source_ny'];
$baseline_ny = $price_per_day['baseline_ny'];
$total = $postsale_insurance['total'];
$all = $total['all'];
$ny = $total['ny'];
$display_all = $total['display_all'];
$source_all = $total['source_all'];
$baseline_all = $total['baseline_all'];
$display_ny = $total['display_ny'];
$source_ny = $total['source_ny'];
$baseline_ny = $total['baseline_ny'];
$text_block = $postsale_insurance['text_block'];
if (count($text_block) > 0) {
    $text = "";
    for ($k=0; $k < count($text_block); $k++) { 
        $text = $text_block[$k];
    }
}

$cdw = $results['cdw'];
$activated = $cdw['activated'];

$flight_airline = $results['flight_airline'];
$flight_number = $results['flight_number'];

$important_information = $results['important_information'];
if (count($important_information) > 0) {
    for ($l=0; $l < count($important_information); $l++) { 
        $title = $important_information[$l]['title'];
        $text = $important_information[$l]['text'];
    }
}

$car_policy_data = $results['car_policy_data'];
if (count($car_policy_data) > 0) {
    for ($m=0; $m < count($car_policy_data); $m++) { 
        $title = $car_policy_data[$m]['title'];
        $description = $car_policy_data[$m]['description'];
    }
}

$package = $results['package'];
$parent = $package['parent'];
$product = $parent['product'];
$tripid = $parent['tripid'];
$email = $parent['email'];



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>