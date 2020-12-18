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

$url = 'https://api-sandbox.rezserver.com/api/shared/getBOF2.Downloads.Car.Cities?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&resume_key=_9jGq-Md2qbTmyYZxfgc6xpV88yc3Sr6rkiSogQMNkHGkjHqfDI6dVS8zMOeeINZXkVS41yxBFzwcBqu0UB2Ya6iReqI-6zERt2I5hcWJObg';

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
    
$getSharedBOF2_cities = $response['getSharedBOF2.Downloads.Car.Cities'];
$results = $getSharedBOF2_cities['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$resume_key = $results['resume_key'];
$total_records = $results['total_records'];
$total_file_size = $results['total_file_size'];
$csv = $results['csv'];
$time = $results['time'];
$cities = $results['cities'];
if (count($cities) > 0) {
    for ($i=0; $i < count($cities); $i++) { 
        $cityid_ppn = $cities[$i]['cityid_ppn'];
        $city = $cities[$i]['city'];
        $state_code = $cities[$i]['state_code'];
        $country_code = $cities[$i]['country_code'];
        $latitude = $cities[$i]['latitude'];
        $longitude = $cities[$i]['longitude'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cities');
            $insert->values(array(
                'datetime_updated' => time(),
                'cityid_ppn' => $cityid_ppn,
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