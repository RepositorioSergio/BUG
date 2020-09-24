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
echo "COMECOU POST BOOKING POLICY";
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

$url = 'https://api-v3.rakutentravelxchange.com/booking_policy';
$raw = '{
    "search": {
        "hotel_id": "usg1",
        "check_in_date": "2020-11-10",
        "check_out_date": "2020-11-13",
        "room_count": 1,
        "adult_count": 2,
        "currency": "USD",
        "locale": "en-US",
        "source_market": "PT",
        "children": null
    },
    "package": {
        "hotel_id": "pbcs",
        "room_details": {
        "room_code": "",
        "rate_plan_code": "",
        "description": "Standard Room Queen Beds",
        "food": 1,
        "non_refundable": false,
        "room_type": "Standard",
        "room_view": "",
        "beds": {
            "queen": 2
        },
        "supplier_description": "Standard - 2 Queen Bed"
    },
    "booking_key": "a082c4df",
    "room_rate": 323.57,
    "room_rate_currency": "USD",
    "client_commission": 16.18,
    "client_commission_currency": "USD",
    "chargeable_rate": 339.75,
    "chargeable_rate_currency": "USD",
    "rate_type": "net"
    }
   }';

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
$booking_policy_id = $response['booking_policy_id'];
// cancelation policy
$cancellation_policy = $response['cancellation_policy'];
$remarks = $cancellation_policy['remarks'];
$cancellation_policies = $cancellation_policy['cancellation_policies'];
if (count($cancellation_policies) > 0) {
    for ($i=0; $i < count($cancellation_policies); $i++) { 
        $penalty_percentage = $cancellation_policies[$i]['penalty_percentage'];
        $date_from = $cancellation_policies[$i]['date_from'];
        $date_to = $cancellation_policies[$i]['date_to'];
    }
}
// package
$package = $response['package'];
$hotel_id = $package['hotel_id'];
$booking_key = $package['booking_key'];
$room_rate = $package['room_rate'];
$room_rate_currency = $package['room_rate_currency'];
$client_commission = $package['client_commission'];
$client_commission_currency = $package['client_commission_currency'];
$chargeable_rate = $package['chargeable_rate'];
$chargeable_rate_currency = $package['chargeable_rate_currency'];
$rate_type = $package['rate_type'];
$room_details = $package['room_details'];
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>