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

$url = 'https://api-sandbox.rezserver.com/api/car/getCancelRequest?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&Standard%5Bemail%5D=john.doe%40example.com&Standard%5Bbooking_id%5D=12345421322';

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
    
$getCarCancel = $response['getCarCancel'];
$results = $getCarCancel['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$getCarCancelRequest = $results['getCarCancelRequest'];
$results2 = $getCarCancelRequest['results'];
$status = $results2['status'];
$status_code = $results2['status_code'];
$inventory = $results2['inventory'];
$cancel_cs_text = $results2['cancel_cs_text'];
$cancel_id = $results2['cancel_id'];
$cancel_request_date_time = $results2['cancel_request_date_time'];
$cancel_status = $results2['cancel_status'];
$confirmation_number = $results2['confirmation_number'];
$partner_confirmation_number = $results2['partner_confirmation_number'];
$partner_name = $results2['partner_name'];
$telephone_number = $results2['telephone_number'];
$time = $results2['time'];




// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>