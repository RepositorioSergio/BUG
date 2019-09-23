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
echo "COMECOU CANCEL";
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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://b2b-sandbox.roomerapi.com/api/cancel?itinerary=342722173&transaction_id=5013124629&platform=API&reason=NotTravelinganymore';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type:application/json',
    'Authorization: Token token=bfff17d3d81077f15d75abfcf115ed73',
    'Partner: paulo@club1hotels.com',
    'API-Version: 2.0'
));
$client->setUri($url);
$client->setMethod('POST');
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

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$age = "";


$data = $response['data'];
if (count($data) > 0) {
    $itinerary = $data['itinerary'];
    $cancellation_code = $data['cancellation_code'];
    $cancellation_policy = $data['cancellation_policy'];
    $type = $cancellation_policy['type'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('cancellation');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'itinerary' => $itinerary,
            'cancellation_code' => $cancellation_code,
            'typeCP' => $type
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

    $details = $cancellation_policy['details'];
    if (count($details) > 0) {
        for ($j=0; $j < count($details); $j++) { 
            $upto_date = $details[$j]['upto_date'];
            $refund_amount = $details[$j]['refund_amount'];
            $text = $details[$j]['text'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('details_cancellation');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'upto_date' => $upto_date,
                    'refund_amount' => $refund_amount,
                    'text' => $text
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO 2: " . $e;
                echo $return;
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