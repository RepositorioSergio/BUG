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

$url = 'https://api-sandbox.rezserver.com/api/air/getFlightBook';

$raw = '{
	"Standard": {
		"refclickid": "john.doe@example.com",
		"sid": "0",
		"ppn_bundle": "",
		"passenger[1][name_prefix]": "Mr.",
		"passenger[1][first_name]": "Michael",
		"passenger[1][last_name]": "Keaton",
		"passenger[1][gender]": "M",
		"passenger[1][birthday]": "",
		"initials": "HO",
		"phone_number": "9437885",
		"phone_area_code": "204",
		"phone_country_code": "1",
		"email": "test@test.com",
		"address": "115 Banatyne Ave",
		"city": "Winnipeg",
		"state_code": "MB",
		"country_code": "CA",
		"postal_code": "R3E2H2&",
		"cc_name": "HotelsbycityOperations",
		"cc_type": "VI",
		"cc_number": "4552720412345677",
		"cc_code": "999",
		"cc_exp_mo": "12",
		"cc_exp_year": "2022"
	}
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
    
$results = $response['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$result = $results['result'];
$booking_status = $result['booking_status'];
$trip_number = $result['trip_number'];
$cs_number = $result['cs_number'];
$est_commission = $result['est_commission'];
$baseline_est_commission = $result['baseline_est_commission'];
$ppn_est_commission = $result['ppn_est_commission'];
$ppn_baseline_est_commission = $result['ppn_baseline_est_commission'];
$insurance = $result['insurance'];
$ti_result = $insurance['ti_result'];
$selected = $ti_result['selected'];
$status = $ti_result['status'];

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>