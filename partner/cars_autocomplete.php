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

$url = 'https://api-sandbox.rezserver.com/api/car/getAutoComplete?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&string=New+York';

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
    
$getCarAutoComplete = $response['getCarAutoComplete'];
$results = $getCarAutoComplete['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$airport_data = $results['airport_data'];
if (count($airport_data) > 0) {
    for ($i=0; $i < count($airport_data); $i++) { 
        $airport_code = $airport_data[$i]['airport_code'];
        $airport_name = $airport_data[$i]['airport_name'];
    }
}
$city_data = $results['city_data'];
if (count($city_data) > 0) {
   for ($j=0; $j < count($city_data); $j++) { 
        $car_cityid_tweb = $city_data[$j]['car_cityid_tweb'];
        $ppn_car_cityid = $city_data[$j]['ppn_car_cityid'];
        $city_code = $city_data[$j]['city_code'];
        $city = $city_data[$j]['city'];
        $rc_city = $city_data[$j]['rc_city'];
        $state_code = $city_data[$j]['state_code'];
        $country = $city_data[$j]['country'];
        $rc_country = $city_data[$j]['rc_country'];
        $country_code = $city_data[$j]['country_code'];
        $latitude = $city_data[$j]['latitude'];
        $longitude = $city_data[$j]['longitude'];
        $airport_data = $city_data[$j]['airport_data'];
        if (count($airport_data) > 0) {
            for ($jAux=0; $jAux < count($airport_data); $jAux++) { 
                $airport_code = $airport_data[$jAux]['airport_code'];
                $airport_name = $airport_data[$jAux]['airport_name'];
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