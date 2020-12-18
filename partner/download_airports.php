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

$url = 'https://api-sandbox.rezserver.com/api/shared/getBOF2.Downloads.Air.Airports?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&resume_key=_S4x67CbkS1RI9EMomeUyb9mBuw0nipnRLkz21r57GUFUOsWNhnkMqX-KCfRPlC8uQw2S03zjmBzdrugzLo3FEg';

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
    
$getSharedBOF2_Airports = $response['getSharedBOF2.Downloads.Air.Airports'];
$results = $getSharedBOF2_Airports['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$resume_key = $results['resume_key'];
$total_records = $results['total_records'];
$total_file_size = $results['total_file_size'];
$csv = $results['csv'];
$time = $results['time'];
$airports = $results['airports'];
if (count($airports) > 0) {
    for ($i=0; $i < count($airports); $i++) { 
        $iata = $airports[$i]['iata'];
        $airport = $airports[$i]['airport'];
        $cityid_ppn = $airports[$i]['cityid_ppn'];
        $state_code = $airports[$i]['state_code'];
        $country_code = $airports[$i]['country_code'];
        $latitude = $airports[$i]['latitude'];
        $longitude = $airports[$i]['longitude'];
        $city_name = $airports[$i]['city_name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('airports');
            $insert->values(array(
                'datetime_updated' => time(),
                'iata' => $iata,
                'airport' => $airport,
                'cityid_ppn' => $cityid_ppn,
                'state_code' => $state_code,
                'country_code' => $country_code,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'city_name' => $city_name
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