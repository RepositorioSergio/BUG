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
echo "COMECOU BOOKING";
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

$url = 'https://api-sandbox.rezserver.com/api/air/getFlightLookUp';

$raw = '{
	"booking_id": 10232444329,
	"email": "zordon@powerRangers.com"
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
    
$getAirFlightLookUp = $response['getAirFlightLookUp'];
$results = $getAirFlightLookUp['results'];
$status_code = $results['status_code'];
$status = $results['status'];
$Success = $status['Success'];
$summary = $Success['summary'];
$value = $Success['value'];
$Error = $status['Error'];
$summary = $Error['summary'];
$value = $Error['value'];
$results2 = $results['results'];
$booking_id = $results2['booking_id'];
$book_status = $results2['book_status'];
$search_type = $results2['search_type'];
$is_cancelable = $results2['is_cancelable'];
$is_fused = $results2['is_fused'];
$last_changed_dt = $results2['last_changed_dt'];
$void_window_close = $results2['void_window_close'];
$pnr_locator = $results2['pnr_locator'];
$lap_infant_allowed = $results2['lap_infant_allowed'];
$component_offer_info_list = $results2['component_offer_info_list'];
$post_book_paid_seats = $results2['post_book_paid_seats'];
$ppn_seat_bundle = $results2['ppn_seat_bundle'];
$email_offer_id = $results2['email_offer_id'];

$customer = $results2['customer'];
$phone_number = $customer['phone_number'];
$email = $customer['email'];
$name = $customer['name'];
$address = $customer['address'];
$location = $customer['location'];
$city = $customer['city'];
$state = $customer['state'];
$country = $customer['country'];
$postal_code = $customer['postal_code'];

$price_details = $results2['price_details'];
$num_travellers = $price_details['num_travellers'];
$display_base_fare = $price_details['display_base_fare'];
$display_taxes = $price_details['display_taxes'];
$display_fees = $price_details['display_fees'];
$display_taxes_and_fees = $price_details['display_taxes_and_fees'];
$display_travel_insurance = $price_details['display_travel_insurance'];
$display_total_fare_per_ticket = $price_details['display_total_fare_per_ticket'];
$display_total_fare = $price_details['display_total_fare'];
$display_currency = $price_details['display_currency'];
$display_symbol = $price_details['display_symbol'];
$source_base_fare = $price_details['source_base_fare'];
$source_taxes = $price_details['source_taxes'];
$source_fees = $price_details['source_fees'];
$source_taxes_and_fees = $price_details['source_taxes_and_fees'];
$source_travel_insurance = $price_details['source_travel_insurance'];
$source_total_fare_per_ticket = $price_details['source_total_fare_per_ticket'];
$source_total_fare = $price_details['source_total_fare'];
$source_currency = $price_details['source_currency'];
$baseline_base_fare = $price_details['baseline_base_fare'];
$baseline_taxes = $price_details['baseline_taxes'];
$baseline_fees = $price_details['baseline_fees'];
$baseline_taxes_and_fees = $price_details['baseline_taxes_and_fees'];
$baseline_travel_insurance = $price_details['baseline_travel_insurance'];
$baseline_total_fare_per_ticket = $price_details['baseline_total_fare_per_ticket'];
$baseline_total_fare = $price_details['baseline_total_fare'];
$baseline_currency = $price_details['baseline_currency'];
$baseline_symbol = $price_details['baseline_symbol'];
$currency_conversion_rate = $price_details['currency_conversion_rate'];
$total_seat_cost = $price_details['total_seat_cost'];

$adjustments = $results2['adjustments'];
$refundable_amount = $adjustments['refundable_amount'];
$charges = $adjustments['charges'];

$baggage_carrier = $results2['baggage_carrier'];
$available = $baggage_carrier['available'];
$airline = $baggage_carrier['airline'];
$popup_url = $baggage_carrier['popup_url'];

$airline_data = $results2['airline_data'];
foreach ($airline_data as $key => $value) {
    $code = $value['code'];
    $name = $value['name'];
    $websiteUrl = $value['websiteUrl'];
    $phoneNumber = $value['phoneNumber'];
    $frequentFlyerProgram = $value['frequentFlyerProgram'];
    $carrierLocator = $value['carrierLocator'];
}

$slice_data = $results2['slice_data'];
foreach ($slice_data as $key => $value2) {
    $info = $value2['info'];
    $duration = $info['duration'];
    $connection_count = $info['connection_count'];
    $stop_count = $info['stop_count'];

    $airline = $value2['airline'];
    $code = $airline['code'];
    $name = $airline['name'];
    $logo = $airline['logo'];

    $departure = $value2['departure'];
    $airport = $departure['airport'];
    $airportcode = $airport['code'];
    $airportcity_id = $airport['city_id'];
    $airportname = $airport['name'];
    $airportcity = $airport['city'];
    $airportstate= $airport['state'];
    $airportcountry = $airport['country'];

    $datetime = $departure['datetime'];
    $date = $datetime['date'];
    $date_display = $datetime['date_display'];
    $time_24h = $datetime['time_24h'];
    $time_12h = $datetime['time_12h'];
    $date_time = $datetime['date_time'];

    $arrival = $value2['arrival'];
    $airport = $arrival['airport'];
    $airportcode = $airport['code'];
    $airportcity_id = $airport['city_id'];
    $airportname = $airport['name'];
    $airportcity = $airport['city'];
    $airportstate= $airport['state'];
    $airportcountry = $airport['country'];

    $datetime = $arrival['datetime'];
    $date = $datetime['date'];
    $date_display = $datetime['date_display'];
    $time_24h = $datetime['time_24h'];
    $time_12h = $datetime['time_12h'];
    $date_time = $datetime['date_time'];

    $flight_data = $value2['flight_data'];
    foreach ($flight_data as $key => $value3) {
        $info = $value3['info'];
        $id = $info['id'];
        $bkg_class = $info['bkg_class'];
        $cabin_class = $info['cabin_class'];
        $cabin_name = $info['cabin_name'];
        $marketing_airline = $info['marketing_airline'];
        $marketing_airline_code = $info['marketing_airline_code'];
        $operating_airline = $info['operating_airline'];
        $operating_airline_code = $info['operating_airline_code'];
        $flight_number = $info['flight_number'];
        $stop_count = $info['stop_count'];
        $aircraft = $info['aircraft'];
        $aircraft_type = $info['aircraft_type'];
        $duration = $info['duration'];
        $carrier_locator = $info['carrier_locator'];
        $disinsection = $info['disinsection'];
        $carrier_locator = $info['carrier_locator'];
        $seat_map_available = $info['seat_map_available'];
        $seat_selection_allowed = $info['seat_selection_allowed'];
        $seat_free_assignment = $info['seat_free_assignment'];
        $premium_seating_flag = $info['premium_seating_flag'];
        $notes = $info['notes'];
        foreach ($notes as $key => $value4) {
            $type = $value4['type'];
            $notesvalue = $value4['value'];
            $duration = $value4['duration'];
        }
        $departure = $value3['departure'];
        $airport = $departure['airport'];
        $airportcode = $airport['code'];
        $airportcity_id = $airport['city_id'];
        $airportname = $airport['name'];
        $airportcity = $airport['city'];
        $airportstate= $airport['state'];
        $airportcountry = $airport['country'];

        $datetime = $departure['datetime'];
        $date = $datetime['date'];
        $date_display = $datetime['date_display'];
        $time_24h = $datetime['time_24h'];
        $time_12h = $datetime['time_12h'];
        $date_time = $datetime['date_time'];

        $arrival = $value3['arrival'];
        $airport = $arrival['airport'];
        $airportcode = $airport['code'];
        $airportcity_id = $airport['city_id'];
        $airportname = $airport['name'];
        $airportcity = $airport['city'];
        $airportstate= $airport['state'];
        $airportcountry = $airport['country'];
        $datetime = $arrival['datetime'];
        $date = $datetime['date'];
        $date_display = $datetime['date_display'];
        $time_24h = $datetime['time_24h'];
        $time_12h = $datetime['time_12h'];
        $date_time = $datetime['date_time'];
    }
}

$fare_rules_data = $results2['fare_rules_data'];
foreach ($fare_rules_data as $key => $value5) {
    $title = $value5['title'];
    $text = $value5['text'];
}

$popup_data = $results2['popup_data'];

$flight_schedule = $popup_data['flight_schedule'];
$title = $flight_schedule['title'];
$paragraph_data = $flight_schedule['paragraph_data'];
$paragraph = "";
foreach ($paragraph_data as $key => $value6) {
    $paragraph = $value6;
}

$bag_spec_requirements = $popup_data['bag_spec_requirements'];
$title = $bag_spec_requirements['title'];
$paragraph_data = $bag_spec_requirements['paragraph_data'];
$paragraph2 = "";
foreach ($paragraph_data as $key => $value7) {
    $paragraph2 = $value7;
}

$notice_of_incorp = $popup_data['notice_of_incorp'];
$title = $notice_of_incorp['title'];
$paragraph_data = $notice_of_incorp['paragraph_data'];
$paragraph3 = "";
foreach ($paragraph_data as $key => $value8) {
    $paragraph3 = $value8;
}

$frequent_flyer = $popup_data['frequent_flyer'];
$title = $frequent_flyer['title'];
$paragraph_data = $frequent_flyer['paragraph_data'];
$paragraph4 = "";
foreach ($paragraph_data as $key => $value9) {
    $paragraph4 = $value9;
}

$check_in = $popup_data['check_in'];
$title = $check_in['title'];
$paragraph_data = $check_in['paragraph_data'];
$paragraph5 = "";
foreach ($paragraph_data as $key => $value10) {
    $paragraph5 = $value10;
}

$internation_travel = $popup_data['internation_travel'];
$title = $internation_travel['title'];
$paragraph_data = $internation_travel['paragraph_data'];
$paragraph6 = "";
foreach ($paragraph_data as $key => $value11) {
    $paragraph6 = $value11;
}

$seat_assignments = $popup_data['seat_assignments'];
$title = $seat_assignments['title'];
$paragraph_data = $seat_assignments['paragraph_data'];
$paragraph7 = "";
foreach ($paragraph_data as $key => $value12) {
    $paragraph7 = $value12;
}

$commonly_used_terms = $popup_data['commonly_used_terms'];
$title = $commonly_used_terms['title'];
$paragraph_data = $commonly_used_terms['paragraph_data'];
$paragraph8 = "";
foreach ($paragraph_data as $key => $value13) {
    $paragraph8 = $value13;
}

$baggage_fees = $popup_data['baggage_fees'];
$title = $baggage_fees['title'];
$paragraph_data = $baggage_fees['paragraph_data'];
$paragraph9 = "";
foreach ($paragraph_data as $key => $value14) {
    $paragraph9 = $value14;
}

$taxes_and_fees = $popup_data['taxes_and_fees'];
$title = $taxes_and_fees['title'];
$paragraph_data = $taxes_and_fees['paragraph_data'];
$paragraph10 = "";
foreach ($paragraph_data as $key => $value15) {
    $paragraph10 = $value15;
}

$disinsection = $popup_data['disinsection'];
$title = $disinsection['title'];
$paragraph_data = $disinsection['paragraph_data'];
$paragraph11 = "";
foreach ($paragraph_data as $key => $value16) {
    $paragraph11 = $value16;
}

$valid_passport = $popup_data['valid_passport'];
$title = $valid_passport['title'];
$paragraph_data = $valid_passport['paragraph_data'];
$paragraph12 = "";
foreach ($paragraph_data as $key => $value17) {
    $paragraph12 = $value17;
}

$important_info_data = $results2['important_info_data'];

$important_information = $important_info_data['important_information'];
$title = $important_information['title'];
$paragraph_data = $important_information['paragraph_data'];
$paragraph13 = "";
foreach ($paragraph_data as $key => $value18) {
    $paragraph13 = $value18;
}

$need_change = $important_info_data['need_change'];
$title = $need_change['title'];
$paragraph_data = $need_change['paragraph_data'];
$paragraph14 = "";
foreach ($paragraph_data as $key => $value19) {
    $paragraph14 = $value19;
}

$customer_service = $important_info_data['customer_service'];
$title = $customer_service['title'];
$paragraph_data = $customer_service['paragraph_data'];
$paragraph15 = "";
foreach ($paragraph_data as $key => $value20) {
    $paragraph15 = $value20;
}

$package = $results2['package'];
$parent = $package['parent'];
$product = $parent['product'];
$tripid = $parent['tripid'];
$email = $parent['email'];

$postsale_insurance = $results2['postsale_insurance'];
$display = $postsale_insurance['display'];


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>