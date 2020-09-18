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
echo "COMECOU HOTELS";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.rakuten.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://api-v3.rakutentravelxchange.com/hotel_list?check_in_date=2020-09-29&check_out_date=2020-09-30&adult_count=2&room_count=1&currency=USD&source_market=US&hotel_id_list=fst1%2Cfst2%2C%2CreFn%2CTJRf%2CKQQR%2CSvBX&locale=en-US';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'accept-encoding' => 'gzip',
    'Content-Type' => 'application/json',
    'x-api-key' => 'c58781b0-ab9b-488b-8e93-57c2c1627f5a'
));
$client->setUri($url);
$client->setMethod('GET');
// $client->setRawBody($raw);
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

$config = new \Zend\Config\Config(include '../config/autoload/global.rakuten.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$session_id = $response['session_id'];
$event_id = $response['event_id'];
$status = $response['status'];
$search = $response['search'];
$check_in_date = $search['check_in_date'];
$check_out_date = $search['check_out_date'];
$source_market = $search['source_market'];
$room_count = $search['room_count'];
$adult_count = $search['adult_count'];
$currency = $search['currency'];
$locale = $search['locale'];
$children = $search['children'];
$hotel_id_list = $search['hotel_id_list'];
if (count($hotel_id_list) > 0) {
    $hotel_id = "";
    for ($i=0; $i < count($hotel_id_list); $i++) { 
        $hotel_id = $hotel_id_list[$i];
    }
}

$hotels = $response['hotels'];
if (count($hotels) > 0) {
    for ($j=0; $j < count($hotels); $j++) { 
        $id = $hotels[$j]['id'];
        $rates = $hotels[$j]['rates'];
        $packages = $rates['packages'];
        if (count($packages) > 0) {
            for ($jAux=0; $jAux < count($packages); $jAux++) { 
                $hotel_id = $hotels[$j]['hotel_id'];
                $booking_key = $hotels[$j]['booking_key'];
                $room_rate = $hotels[$j]['room_rate'];
                $room_rate_currency = $hotels[$j]['room_rate_currency'];
                $client_commission = $hotels[$j]['client_commission'];
                $client_commission_currency = $hotels[$j]['client_commission_currency'];
                $chargeable_rate = $hotels[$j]['chargeable_rate'];
                $chargeable_rate_currency = $hotels[$j]['chargeable_rate_currency'];
                $rate_type = $hotels[$j]['rate_type'];
                $room_details = $hotels[$j]['room_details'];
                $room_code = $room_details['room_code'];
                $rate_plan_code = $room_details['rate_plan_code'];
                $rate_plan_description = $room_details['rate_plan_description'];
                $description = $room_details['description'];
                $food = $room_details['food'];
                $non_refundable = $room_details['non_refundable'];
                $room_type = $room_details['room_type'];
                $room_view = $room_details['room_view'];
                $supplier_description = $room_details['supplier_description'];
                $non_smoking = $room_details['non_smoking'];
                $room_gender = $room_details['room_gender'];
                $benefits = $room_details['benefits'];
                $floor = $room_details['floor'];
                $amenitites = $room_details['amenitites'];
                $beds = $room_details['beds'];
                $queen = $beds['queen'];
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