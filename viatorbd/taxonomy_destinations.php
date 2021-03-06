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
// echo "COMECOU CITIES";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = 'https://viatorapi.viator.com/service/taxonomy/destinations';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'Accept' => 'application/json;version=2.0',
    'Accept-Language' => 'en-US',
    'exp-api-key' => '5364bbaf-e4f7-4727-9e91-317e794dfbaa'
));
$client->setUri($url);
$client->setMethod('GET');
// $client->setRawBody($raw);
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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$errorReference = $response['errorReference'];
$dateStamp = $response['dateStamp'];
$errorType = $response['errorType'];
$errorCodes = $response['errorCodes'];
$errorMessage = $response['errorMessage'];
$errorName = $response['errorName'];
$extraInfo = $response['extraInfo'];
$extraObject = $response['extraObject'];
$success = $response['success'];
$totalCount = $response['totalCount'];
$errorMessageText = $response['errorMessageText'];
$vmid = $response['vmid'];
$data = $response['data'];
if (count($data) > 0) {
    for ($i=0; $i < count($data); $i++) { 
        $sortOrder = $data[$i]['sortOrder'];
        $selectable = $data[$i]['selectable'];
        $destinationUrlName = $data[$i]['destinationUrlName'];
        $defaultCurrencyCode = $data[$i]['defaultCurrencyCode'];
        $lookupId = $data[$i]['lookupId'];
        $parentId = $data[$i]['parentId'];
        $timeZone = $data[$i]['timeZone'];
        $iataCode = $data[$i]['iataCode'];
        $destinationName = $data[$i]['destinationName'];
        $destinationType = $data[$i]['destinationType'];
        $destinationId = $data[$i]['destinationId'];
        $latitude = $data[$i]['latitude'];
        $longitude = $data[$i]['longitude'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('viator_taxonomydestinations');
            $insert->values(array(
                'datetime_updated' => time(),
                'sortorder' => $sortOrder,
                'selectable' => $selectable,
                'destinationurlname' => $destinationUrlName,
                'defaultcurrencycode' => $defaultCurrencyCode,
                'lookupid' => $lookupId,
                'parentid' => $parentId,
                'timezone' => $timeZone,
                'iatacode' => $iataCode,
                'destinationname' => $destinationName,
                'destinationtype' => $destinationType,
                'destinationid' => $destinationId,
                'latitude' => $latitude,
                'longitude' => $longitude
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $LastGeneratedValue = $db->getDriver()->getLastGeneratedValue("id");
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 1: " . $e;
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