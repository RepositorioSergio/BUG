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

$url = 'https://api-sandbox.rezserver.com/api/air/getFlightVoidRequest?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&email=test%40test.com&booking_id=10012515350&sid=545b7d84c5b770a8a24c55a8857ea78720150703115359';
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
    
$getAirFlightVoidRequest = $response['getAirFlightVoidRequest'];
$results = $getAirFlightVoidRequest['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$result = $results['result'];
$booking_status = $result['booking_status'];
$point_of_sale = $result['point_of_sale'];
$currency = $point_of_sale['currency'];
$country = $point_of_sale['country'];

$component_air_void_info_list = $result['component_air_void_info_list'];
if (count($component_air_void_info_list) > 0) {
    for ($i=0; $i < count($component_air_void_info_list); $i++) { 
        $bookingReferenceId = $component_air_void_info_list[$i]['bookingReferenceId'];
        $bookingStatus = $component_air_void_info_list[$i]['bookingStatus'];
        $statusCode = $bookingStatus['statusCode'];
        $reasonCode = $bookingStatus['reasonCode'];
    }
}

$charges = $result['charges'];
$refundable_amount = $charges['refundable_amount'];
$charges2 = $charges['charges'];

$passengers = $result['passengers'];
if (count($passengers) > 0) {
    for ($j=0; $j < count($passengers); $j++) { 
        $id = $passengers[$j]['id'];
        $refNum = $passengers[$j]['refNum'];
        $first_name = $passengers[$j]['first_name'];
        $middle_name = $passengers[$j]['middle_name'];
        $last_name = $passengers[$j]['last_name'];
        $ticket_info = $passengers[$j]['ticket_info'];
        $number = $ticket_info['number'];
        $status = $ticket_info['status'];
        $exchange_ticket = $ticket_info['exchange_ticket'];
        $penalty = $ticket_info['penalty'];
        $conjuction_ticket = $ticket_info['conjuction_ticket'];
    }
}




// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>