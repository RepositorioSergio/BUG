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

$url = 'https://api-sandbox.rezserver.com/api/air/getFlightContract?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&site_refid=1000&sid=abcEZas123&ppn_bundle=_eJwBAAH_f_fpcofB7xo3ysFyNskkGdZjM5LDKhNcRfjx_pP1iKqHKgyZvycPXUdTZSCgqnccVJzVYbLpufBpTNVVaXnacXv8';
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
$client->setMethod('GET');
//$client->setRawBody($raw);
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
    
$getAirFlightContract = $response['getAirFlightContract'];
$results = $getAirFlightContract['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$result = $results['result'];
$itinerary_data = $result['itinerary_data'];
$search_type = $itinerary_data['search_type'];
$opaque = $itinerary_data['opaque'];
$number_of_passengers = $itinerary_data['number_of_passengers'];
$adults = $itinerary_data['adults'];
$children = $itinerary_data['children'];
$lap_infants_allowed = $itinerary_data['lap_infants_allowed'];
$ppn_seat_bundle = $itinerary_data['ppn_seat_bundle'];
$seat_map_available = $itinerary_data['seat_map_available'];
$component_information_list = $itinerary_data['component_information_list'];
$ppn_book_bundle = $itinerary_data['ppn_book_bundle'];

$price_details = $itinerary_data['price_details'];
$display_base_fare = $price_details['display_base_fare'];
$display_taxes = $price_details['display_taxes'];
$display_fees = $price_details['display_fees'];
$display_taxes_and_fees = $price_details['display_taxes_and_fees'];
$display_pcln_fees = $price_details['display_pcln_fees'];
$display_ppn_fees = $price_details['display_ppn_fees'];
$display_taxes_and_ppn_fees = $price_details['display_taxes_and_ppn_fees'];
$display_total_fare_per_ticket = $price_details['display_total_fare_per_ticket'];
$display_total_fare = $price_details['display_total_fare'];
$display_currency = $price_details['display_currency'];
$display_symbol = $price_details['display_symbol'];
$source_base_fare = $price_details['source_base_fare'];
$source_taxes = $price_details['source_taxes'];
$source_fees = $price_details['source_fees'];
$source_taxes_and_fees = $price_details['source_taxes_and_fees'];
$source_pcln_fees = $price_details['source_pcln_fees'];
$source_ppn_fees = $price_details['source_ppn_fees'];
$source_taxes_and_ppn_fees = $price_details['source_taxes_and_ppn_fees'];
$source_total_fare_per_ticket = $price_details['source_total_fare_per_ticket'];
$source_total_fare = $price_details['source_total_fare'];
$source_currency = $price_details['source_currency'];
$baseline_base_fare = $price_details['baseline_base_fare'];
$baseline_taxes = $price_details['baseline_taxes'];
$baseline_fees = $price_details['baseline_fees'];
$baseline_taxes_and_fees = $price_details['baseline_taxes_and_fees'];
$baseline_pcln_fees = $price_details['baseline_pcln_fees'];
$baseline_ppn_fees = $price_details['baseline_ppn_fees'];
$baseline_taxes_and_ppn_fees = $price_details['baseline_taxes_and_ppn_fees'];
$baseline_total_fare_per_ticket = $price_details['baseline_total_fare_per_ticket'];
$baseline_total_fare = $price_details['baseline_total_fare'];
$baseline_currency = $price_details['baseline_currency'];
$baseline_symbol = $price_details['baseline_symbol'];
$component_itin_pricing_info = $price_details['component_itin_pricing_info'];

$customer_locations = $itinerary_data['customer_locations'];
$user_input = $customer_locations['user_input'];
$country_data = $customer_locations['country_data'];
if (count($country_data) > 0) {
    for ($i=0; $i < count($country_data); $i++) { 
        $name = $country_data[$i]['name'];
        $code = $country_data[$i]['code'];
        $region_data = $country_data[$i]['region_data'];
        if (count($region_data) > 0) {
            for ($iAux=0; $iAux < count($region_data); $iAux++) { 
                $name = $region_data[$iAux]['name'];
                $code = $region_data[$iAux]['code'];
            }
        }
    }
}

$baggage_carrier = $itinerary_data['baggage_carrier'];
$available = $baggage_carrier['available'];
$departure = $baggage_carrier['departure'];
$arrival = $baggage_carrier['arrival'];
$airline = $baggage_carrier['airline'];
$popup_url = $baggage_carrier['popup_url'];

$slice_data = $itinerary_data['slice_data'];
if (count($slice_data) > 0) {
    for ($j=0; $j < count($slice_data); $j++) { 
        $info = $slice_data[$j]['info'];
        $duration = $info['duration'];
        $max_duration = $info['max_duration'];
        $connection_count = $info['connection_count'];
        $max_connection_duration = $info['max_connection_duration'];
        $stop_count = $info['stop_count'];
        $max_stops = $info['max_stops'];

        $airline = $slice_data[$j]['airline'];
        $code = $airline['code'];
        $name = $airline['name'];
        $logo = $airline['logo'];

        $departure = $slice_data[$j]['departure'];
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

        $arrival = $slice_data[$j]['arrival'];
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

        $flight_data = $slice_data[$j]['flight_data'];
        if (count($flight_data) > 0) {
            for ($jAux=0; $jAux < count($flight_data); $jAux++) { 
                $info = $flight_data[$jAux]['info'];
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
                if (count($notes) > 0) {
                    for ($jAux2=0; $jAux2 < count($notes); $jAux2++) { 
                        $type = $notes[$jAux2]['type'];
                        $value = $notes[$jAux2]['value'];
                        $duration = $notes[$jAux2]['duration'];
                    }
                }

                $departure = $flight_data[$jAux]['departure'];
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

                $arrival = $flight_data[$jAux]['arrival'];
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
    }
}

$parent_contract_archive_id = $result['parent_contract_archive_id'];

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>