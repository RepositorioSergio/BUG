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
echo "COMECOU";
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
$url = 'https://api-sandbox.rezserver.com/api/car/getResultsV3?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&sid=euubv1k1qd8fdbovjv3jl8m9d7&pickup_airport_code=JFK&pickup_date=2021-01-02&pickup_time=10%3A00%3A00&dropoff_date=2021-01-05&dropoff_time=10%3A00%3A00';
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
    
$getCarResultsRequest = $response['getCarResultsRequest'];
$results = $getCarResultsRequest['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$rc_inventory_count = $results['rc_inventory_count'];
$returned_inv_count = $results['returned_inv_count'];
$rc_inventory = $results['rc_inventory'];
$car_companies = $results['car_companies'];
if (count($car_companies) > 0) {
    for ($i=0; $i < count($car_companies); $i++) { 
        $code = $car_companies[$i]['code'];
        $name = $car_companies[$i]['name'];
    }
}
$results_list = $results['results_list'];
if (count($results_list) > 0) {
    for ($j=0; $j < count($results_list); $j++) { 
        $num_rental_days = $results_list[$j]['num_rental_days'];
        $inventory = $results_list[$j]['inventory'];
        $opaque = $results_list[$j]['opaque'];
        $payType = $results_list[$j]['payType'];
        $net_rate = $results_list[$j]['net_rate'];
        $creditCardRequired = $results_list[$j]['creditCardRequired'];
        $postpaid_contract_bundle = $results_list[$j]['postpaid_contract_bundle'];
        $contract_page_url = $results_list[$j]['contract_page_url'];
        $partner = $results_list[$j]['partner'];
        $code = $partner['code'];
        $name = $partner['name'];
        $logo = $partner['logo'];
        $car = $results_list[$j]['car'];
        $example = $car['example'];
        $description = $car['description'];
        $type = $car['type'];
        $type_name = $car['type_name'];
        $vehicle_code = $car['vehicle_code'];
        $passengers = $car['passengers'];
        $doors = $car['doors'];
        $bags = $car['bags'];
        $transmission = $car['transmission'];
        $air_conditioning = $car['air_conditioning'];
        $free_cancellation = $car['free_cancellation'];
        $pay_at_booking = $car['pay_at_booking'];
        $mileage = $car['mileage'];
        $imageURL = $car['imageURL'];
        $campaign_rate = $car['campaign_rate'];
        $discount = $car['discount'];
        $images = $car['images'];
        $image = "";
        foreach ($images as $key => $value) {
            $image = $value;
        }

        $pickup = $results_list[$j]['pickup'];
        $location = $pickup['location'];
        $location_code = $pickup['location_code'];
        $latitude = $pickup['latitude'];
        $longitude = $pickup['longitude'];
        $city_center_distance = $pickup['city_center_distance'];
        $neighborhood = $pickup['neighborhood'];
        $location_information = $pickup['location_information'];

        $dropoff = $results_list[$j]['dropoff'];
        $location = $dropoff['location'];
        $location_code = $dropoff['location_code'];
        $latitude = $dropoff['latitude'];
        $longitude = $dropoff['longitude'];
        $city_center_distance = $dropoff['city_center_distance'];
        $neighborhood = $dropoff['neighborhood'];
        $location_information = $dropoff['location_information'];

        $price_details = $results_list[$j]['price_details'];
        $base = $price_details['base'];
        $price = $base['price'];
        $strikeout_price = $base['strikeout_price'];
        $rate_unit = $base['rate_unit'];
        $sub_total_price = $base['sub_total_price'];
        $total_price = $base['total_price'];
        $total_price_float = $base['total_price_float'];
        $total_strikeout_price = $base['total_strikeout_price'];
        $currency = $base['currency'];
        $symbol = $base['symbol'];

        $source = $price_details['source'];
        $price = $source['price'];
        $strikeout_price = $source['strikeout_price'];
        $rate_unit = $source['rate_unit'];
        $sub_total_price = $source['sub_total_price'];
        $total_price = $source['total_price'];
        $total_price_float = $source['total_price_float'];
        $total_strikeout_price = $source['total_strikeout_price'];
        $currency = $source['currency'];
        $symbol = $source['symbol'];

        $display = $price_details['display'];
        $price = $display['price'];
        $strikeout_price = $display['strikeout_price'];
        $rate_unit = $display['rate_unit'];
        $sub_total_price = $display['sub_total_price'];
        $total_price = $display['total_price'];
        $total_price_float = $display['total_price_float'];
        $total_strikeout_price = $display['total_strikeout_price'];
        $currency = $display['currency'];
        $symbol = $display['symbol'];
    }
}



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>