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
echo "COMECOU SEARC DEALS";
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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://b2b-sandbox.roomerapi.com/api/search_deals?platform=API&page_number=0&number_of_results=20';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Authorization: Token token=bfff17d3d81077f15d75abfcf115ed73',
    'Partner: paulo@club1hotels.com',
    'API-Version: 2.0'
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

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$age = "";
$amenity = "";

$data = $response['data'];
if (count($data) > 0) {
    $check_in = $data['check_in'];
    $check_out = $data['check_out'];
    $found_hotels_count = $data['found_hotels_count'];
    $currency = $data['currency'];
    $page = $data['page'];
    $total_pages = $data['total_pages'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('searchDeals');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'found_hotels_count' => $found_hotels_count,
            'currency' => $currency,
            'page' => $page,
            'total_pages' => $total_pages
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO 1: " . $e;
        echo $return;
    }

    $hotel_list = $data['hotel_list'];
    if (count($hotel_list) > 0) {
        for ($j=0; $j < count($hotel_list); $j++) { 
            $id = $hotel_list[$j]['id'];
            $name = $hotel_list[$j]['name'];
            $address = $hotel_list[$j]['address'];
            $city = $hotel_list[$j]['city'];
            $state = $hotel_list[$j]['state'];
            $country = $hotel_list[$j]['country'];
            $latitude = $hotel_list[$j]['latitude'];
            $longitude = $hotel_list[$j]['longitude'];
            $stars_rating = $hotel_list[$j]['stars_rating'];
            $zone_name = $hotel_list[$j]['zone_name'];
            $bed_choice_available = $hotel_list[$j]['bed_choice_available'];

            $amenities = $hotel_list[$j]['amenities'];
            $parking = $amenities['parking'];
            $wifi = $amenities['wifi'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotelList');
                $insert->values(array(
                    'id' => $id,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'partner_hotel_id' => $partner_hotel_id,
                    'name' => $name,
                    'address' => $address,
                    'city' => $city,
                    'country' => $country,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'stars_rating' => $stars_rating,
                    'zone_name' => $zone_name,
                    'bed_choice_available' => $bed_choice_available,
                    'parking' => $parking,
                    'wifi' => $wifi
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO 2: " . $e;
                echo $return;
            }

            $rooms = $hotel_list[$j]['rooms'];
            if (count($rooms) > 0) {
                for ($jAux2=0; $jAux2 < count($rooms); $jAux2++) { 
                    $check_in = $rooms[$jAux2]['check_in'];
                    $check_out = $rooms[$jAux2]['check_out'];
                    $adults = $rooms[$jAux2]['adults'];
                    $children = $rooms[$jAux2]['children'];
                    $room_code = $rooms[$jAux2]['room_code'];
                    $description = $rooms[$jAux2]['description'];
                    $room_type_name = $rooms[$jAux2]['room_type_name'];
                    $breakfast = $rooms[$jAux2]['breakfast'];
                    $board_name = $rooms[$jAux2]['board_name'];
                    $rate_code = $rooms[$jAux2]['rate_code'];

                    $prices = $rooms[$jAux2]['prices'];
                    $is_best_value = $prices['is_best_value'];
                    $recommended_price = $prices['recommended_price'];
                    $benchmark_price = $prices['benchmark_price'];
                    $b2c_rate = $prices['b2c_rate'];
                    $price = $b2c_rate['price'];
                    $tax = $b2c_rate['tax'];

                    $b2b_rate = $prices['b2b_rate'];
                    $pricebb = $b2b_rate['price'];
                    $taxbb = $b2b_rate['tax'];

                    $mobile_rate = $prices['mobile_rate'];
                    $pricemr = $mobile_rate['price'];
                    $taxmr = $mobile_rate['tax'];

                    $fenced_rate = $prices['fenced_rate'];
                    $pricefr = $fenced_rate['price'];
                    $taxfr = $fenced_rate['tax'];

                    $cancellation_policy = $rooms[$jAux2]['cancellation_policy'];
                    $type = $cancellation_policy['type'];
                    $details = $cancellation_policy['details'];
                    $details = $details['details'];
                    if (count($details) > 0) {
                        for ($j=0; $j < count($details); $j++) { 
                            $upto_date = $details[$j]['upto_date'];
                            $refund_amount = $details[$j]['refund_amount'];
                            $text = $details[$j]['text'];
                        }
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('rooms');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'room_code' => $room_code,
                            'description' => $description,
                            'room_type_name' => $room_type_name,
                            'breakfast' => $breakfast,
                            'board_name' => $board_name,
                            'rate_code' => $rate_code,
                            'is_best_value' => $is_best_value,
                            'recommended_price' => $recommended_price,
                            'benchmark_price' => $benchmark_price,
                            'price' => $price,
                            'tax' => $tax,
                            'pricebb' => $pricebb,
                            'taxbb' => $taxbb,
                            'pricemr' => $pricemr,
                            'taxmr' => $taxmr,
                            'pricefr' => $pricefr,
                            'taxfr' => $taxfr,
                            'typeCP' => $type,
                            'hotelID' => $id
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 3: " . $e;
                        echo $return;
                    }

                    $fees = $rooms[$jAux2]['fees'];
                    if (count($fees) > 0) {
                        $at_property = $transactions['at_property'];
                        if (count($at_property) > 0) {
                            for ($k=0; $k < count($at_property); $k++) { 
                                $name = $at_property[$k]['name'];
                                $amount = $at_property[$k]['amount'];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('property_fees');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'name' => $name,
                                        'amount' => $amount,
                                        'room_code' => $room_code
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 4: " . $e;
                                    echo $return;
                                }
                            }
                        }
                        $included = $transactions['included'];
                        if (count($included) > 0) {
                            for ($k=0; $k < count($included); $k++) { 
                                $name = $included[$k]['name'];
                                $amount = $included[$k]['amount'];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('included_fees');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'name' => $name,
                                        'amount' => $amount,
                                        'room_code' => $room_code
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 5: " . $e;
                                    echo $return;
                                }
                            }
                        }
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