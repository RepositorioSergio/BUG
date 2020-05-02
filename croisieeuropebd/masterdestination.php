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
echo "COMECOU DESTINATION";
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
// Start
$affiliate_id = 0;
$branch_filter = "";


$config = new \Zend\Config\Config(include '../config/autoload/global.coming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw2 = '{
    "user": "CTM",
    "password": "CTM9632"
    }';


$client2 = new Client();
$client2->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client2->setHeaders(array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Content-length: " . strlen($raw2)
));

$url = 'https://svcext.grupo-pinero.com/co2/pandora-v1';

$client2->setUri($url . '/login');
$client2->setMethod('POST');
$client2->setRawBody($raw2);
$response2 = $client2->send();
if ($response2->isSuccess()) {
$response2 = $response2->getBody();
} else {
$logger = new Logger();
$writer = new Writer\Stream('/srv/www/htdocs/error_log');
$logger->addWriter($writer);
$logger->info($client2->getUri());
$logger->info($response2->getStatusCode() . " - " . $response2->getReasonPhrase());
echo $return;
echo $response2->getStatusCode() . " - " . $response2->getReasonPhrase();
echo $return;
die();
}

$token = $response2;


$raw = '{
    "token":"' . $token . '",
    "language": "EN",
    "productType" : "EXCURSION"
    }';


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Content-length: " . strlen($raw)
));

$url = 'https://svcext.grupo-pinero.com/co2/pandora-v1';

$client->setUri($url . '/master/destinations');
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
echo $response;
$response = json_decode($response, true);

echo "<xmp>";
var_dump($response);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.coming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$countryList = $response['countryList'];
for ($i=0; $i < count($countryList); $i++) { 
    $code = $countryList[$i]['code'];
    $name = $countryList[$i]['name'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('countries');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'code' => $code,
            'name' => $name
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error LIST: " . $e;
        echo $return;
    }

    $provinceList = $countryList[$i]['provinceList'];
    for ($j=0; $j < count($provinceList); $j++) { 
        $provinceCode = $provinceList[$j]['code'];
        $provinceName = $provinceList[$j]['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('provinces');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'provinceCode' => $provinceCode,
                'provinceName' => $provinceName,
                'countryCode' => $code,
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error PR: " . $e;
            echo $return;
        }

        $destinationList = $provinceList[$j]['destinationList'];
        for ($k=0; $k < count($destinationList); $k++) { 
            $destinationCode = $destinationList[$k]['code'];
            $destinationName = $destinationList[$k]['name'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('country_destinations');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'destinationCode' => $destinationCode,
                    'destinationName' => $destinationName,
                    'countryCode' => $code,
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error DEST: " . $e;
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