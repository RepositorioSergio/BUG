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

$url = 'https://api-sandbox.rezserver.com/api/air/getAutoComplete?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&string=New+York';
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
    
$getAirAutoComplete = $response['getAirAutoComplete'];
$results = $getAirAutoComplete['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$getSolr = $results['getSolr'];
$results2 = $getSolr['results'];
$status = $results2['status'];
$status_code = $results2['status_code'];
$query = $results2['query'];
$data = $results2['data'];
$city_data = $data['city_data'];
if (count($city_data) > 0) {
    for ($i=0; $i < count($city_data); $i++) { 
        $pet_count = $city_data[$i]['pet_count'];
        $state_code = $city_data[$i]['state_code'];
        $coordinate = $city_data[$i]['coordinate'];
        $state = $city_data[$i]['state'];
        $country_code = $city_data[$i]['country_code'];
        $city = $city_data[$i]['city'];
        $country = $city_data[$i]['country'];
        $rank = $city_data[$i]['rank'];
        $cityid_ppn = $city_data[$i]['cityid_ppn'];
        $longitude = $city_data[$i]['longitude'];
        $latitude = $city_data[$i]['latitude'];
        $hotel_count = $city_data[$i]['hotel_count'];
        $type = $city_data[$i]['type'];
        $score = $city_data[$i]['score'];
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>