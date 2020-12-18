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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.priceline.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://api-sandbox.rezserver.com/api/shared/getBOF2.Downloads.Car.Locations?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&resume_key=_nDNOwJ1bOwthqU3RJ2H9-Rd2fkZOYa1umDl9i-pUOa6NbJqN78E9gIvumLnTIuIZ3RT7iMd2Pm6XoBXY1S5KzrpBQNoyyP2vqQHBUO0bEz0';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Cache-Control' => 'no-cache'
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

$config = new \Zend\Config\Config(include '../config/autoload/global.priceline.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$getSharedBOF2_locations = $response['getSharedBOF2.Downloads.Car.Locations'];
$results = $getSharedBOF2_locations['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$resume_key = $results['resume_key'];
$total_records = $results['total_records'];
$total_file_size = $results['total_file_size'];
$csv = $results['csv'];
$time = $results['time'];
$locations = $results['locations'];
if (count($locations) > 0) {
    for ($i=0; $i < count($locations); $i++) { 
        $cityid_ppn = $locations[$i]['cityid_ppn'];
        $partner_code = $locations[$i]['partner_code'];
        $partner_location_code = $locations[$i]['partner_location_code'];
        $is_airport = $locations[$i]['is_airport'];
        $address = $locations[$i]['address'];
        $city = $locations[$i]['city'];
        $state_code = $locations[$i]['state_code'];
        $country_code = $locations[$i]['country_code'];
        $latitude = $locations[$i]['latitude'];
        $longitude = $locations[$i]['longitude'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('locations');
            $insert->values(array(
                'datetime_updated' => time(),
                'cityid_ppn' => $cityid_ppn,
                'partner_code' => $partner_code,
                'partner_location_code' => $partner_location_code,
                'is_airport' => $is_airport,
                'address' => $address,
                'city' => $city,
                'state_code' => $state_code,
                'country_code' => $country_code,
                'latitude' => $latitude,
                'longitude' => $longitude
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO: " . $e;
            echo $return; 
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>