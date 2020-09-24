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
echo "COMECOU PREBOOK";
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

$url = 'https://api-v3.rakutentravelxchange.com/pre_book';
$raw = '{
    "booking_policy_id": "9936899c-fe78-11ea-bbd9-564ef3c0029b",
    "client_reference": "aaaa_0005",
    "room_lead_guests": [
    {
        "first_name": "Test",
        "last_name": "Smith",
        "nationality": "US"
    }
    ],
    "contact_person": {
    "salutation": "Mr.",
    "first_name": "Test",
    "last_name": "Smith",
    "contact_no": "1112313123"
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
$booking_id = $response['booking_id'];
$client_reference = $response['client_reference'];
$status = $response['status'];
$status_payment = $response['status_payment'];
$requested_at = $response['requested_at'];
$bundled_booking = $response['bundled_booking'];
$locale = $response['locale'];
$source_market = $response['source_market'];
$is_custom = $response['is_custom'];
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
// Guest
$guest = $response['guest'];
$salutation = $guest['salutation'];
$first_name = $guest['first_name'];
$last_name = $guest['last_name'];
$email = $guest['email'];
$city = $guest['city'];
$state = $guest['state'];
$street = $guest['street'];
$postal_code = $guest['postal_code'];
$country = $guest['country'];
$remarks = $guest['remarks'];
$nationality = $guest['nationality'];
$contact_no = $guest['contact_no'];
//Pricing
$pricing = $response['pricing'];
$zumata = $pricing['zumata'];
$zumata_currency = $zumata['currency'];
$zumata_value = $zumata['value'];
$client = $pricing['client'];
$client_currency = $client['currency'];
$client_value = $client['value'];
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
// Booking Details
$booking_details = $response['booking_details'];
$customer_reference = $booking_details['customer_reference'];
$customer_booking_id = $booking_details['customer_booking_id'];
// Contact Person
$contact_person = $response['contact_person'];
$salutation = $contact_person['salutation'];
$first_name = $contact_person['first_name'];
$last_name = $contact_person['last_name'];
$email = $contact_person['email'];
$city = $contact_person['city'];
$state = $contact_person['state'];
$street = $contact_person['street'];
$postal_code = $contact_person['postal_code'];
$country = $contact_person['country'];
$remarks = $contact_person['remarks'];
$nationality = $contact_person['nationality'];
$contact_no = $contact_person['contact_no'];
// Room lead
$room_lead_guests = $response['room_lead_guests'];
$salutation = $room_lead_guests['salutation'];
$first_name = $room_lead_guests['first_name'];
$last_name = $room_lead_guests['last_name'];
$nationality = $room_lead_guests['nationality'];
$remarks = $room_lead_guests['remarks'];

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>