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

$url = 'https://api-sandbox.rezserver.com/api/air/getFlightDepartures?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&sid=545b7d84c5b770a8a24c55a8857ea78720150703115359&adults=1&children=0&departure_date%5B%5D=2019-05-17&origin_airport_code%5B%5D=YWG&destination_airport_code%5B%5D=JFK';
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
    
$getAirFlightDepartures = $response['getAirFlightDepartures'];
$results = $getAirFlightDepartures['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$result = $results['result'];
$sid = $result['sid'];
$search_type = $result['search_type'];
$itinerary_count = $result['itinerary_count'];
$page_number = $result['page_number'];

$search_data = $result['search_data'];
if (count($search_data) > 0) {
    for ($i=0; $i < count($search_data); $i++) { 
        $origin = $search_data[$i]['origin'];
        $code = $origin['code'];
        $isAirport = $origin['isAirport'];
        $name = $origin['name'];
        $city = $origin['city'];
        $state = $origin['state'];
        $country = $origin['country'];

        $destination = $search_data[$i]['destination'];
        $code = $destination['code'];
        $isAirport = $destination['isAirport'];
        $name = $destination['name'];
        $city = $destination['city'];
        $state = $destination['state'];
        $country = $destination['country'];
        $departure_date = $search_data[$i]['departure_date'];
    }
}

$airport_data = $result['airport_data'];
if (count($airport_data) > 0) {
    for ($j=0; $j < count($airport_data); $j++) { 
        $name = $airport_data[$j]['name'];
        $code = $airport_data[$j]['code'];
        $city = $airport_data[$j]['city'];
        $state = $airport_data[$j]['state'];
        $country = $airport_data[$j]['country'];
        $flight_orig_count = $airport_data[$j]['flight_orig_count'];
        $flight_dest_count = $airport_data[$j]['flight_dest_count'];
        $geo = $airport_data[$j]['geo'];
        $latitude = $geo['latitude'];
        $longitude = $geo['longitude'];
    }
}

$airline_data = $result['airline_data'];
if (count($airline_data) > 0) {
    for ($k=0; $k < count($airline_data); $k++) { 
        $name = $airline_data[$k]['name'];
        $code = $airline_data[$k]['code'];
        $websiteUrl = $airline_data[$k]['websiteUrl'];
        $phoneNumber = $airline_data[$k]['phoneNumber'];
        $frequentFlyerProgram = $airline_data[$k]['frequentFlyerProgram'];
    }
}

$nearby_airports = $result['nearby_airports'];
if (count($nearby_airports) > 0) {
   for ($x=0; $x < count($nearby_airports); $x++) { 
        $code = $nearby_airports[$x]['code'];
        $name = $nearby_airports[$x]['name'];
        $geo = $nearby_airports[$x]['geo'];
        $distance = $geo['distance'];
        $latitude = $geo['latitude'];
        $longitude = $geo['longitude'];
   }
}

$itinerary_data = $result['itinerary_data'];
if (count($itinerary_data) > 0) {
    for ($y=0; $y < count($itinerary_data); $y++) { 
        $opaque = $itinerary_data[$y]['opaque'];
        $is_fused = $itinerary_data[$y]['is_fused'];
        $ppn_contract_bundle = $itinerary_data[$y]['ppn_contract_bundle'];
        $contract_page_url = $itinerary_data[$y]['contract_page_url'];
        $ppn_seat_bundle = $itinerary_data[$y]['ppn_seat_bundle'];

        $price_details = $itinerary_data[$y]['price_details'];
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

        $baggage_carrier = $itinerary_data[$y]['baggage_carrier'];
        $available = $baggage_carrier['available'];
        $departure = $baggage_carrier['departure'];
        $arrival = $baggage_carrier['arrival'];
        $airline = $baggage_carrier['airline'];
        $popup_url = $baggage_carrier['popup_url'];

        $displayable_airlines = $itinerary_data[$y]['displayable_airlines'];
        if (count($displayable_airlines) > 0) {
            $displayable_airline = "";
            for ($yAux2=0; $yAux2 < count($displayable_airlines); $yAux2++) { 
                $displayable_airline = $displayable_airlines[$yAux2];
            }
        }

        $slice_data = $itinerary_data[$y]['slice_data'];
        if (count($slice_data) > 0) {
            for ($yAux=0; $yAux < count($slice_data); $yAux++) { 
                $info = $slice_data[$yAux]['info'];
                $duration = $info['duration'];
                $max_duration = $info['max_duration'];
                $connection_count = $info['connection_count'];
                $max_connection_duration = $info['max_connection_duration'];
                $stop_count = $info['stop_count'];
                $max_stops = $info['max_stops'];

                $airline = $slice_data[$yAux]['airline'];
                $code = $airline['code'];
                $name = $airline['name'];
                $logo = $airline['logo'];

                $departure = $slice_data[$yAux]['departure'];
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

                $arrival = $slice_data[$yAux]['arrival'];
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

                $flight_data = $slice_data[$yAux]['flight_data'];
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
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>