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
echo "COMECOU CITY";
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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://affiliateapi7643.agoda.com/affiliateservice/lt_v1';

$raw = '{
        "criteria": {
        "additional": {
        "currency": "USD",
        "dailyRate": {
        "maximum": 10000,
        "minimum": 1
        },
        "discountOnly": false,
        "language": "en-us",
        "maxResult": 10,
        "minimumReviewScore": 0,
        "minimumStarRating": 0,
        "occupancy": {
        "numberOfAdult": 2,
        "numberOfChildren": 0
        },
        "sortBy": "PriceAsc"
        },
        "checkInDate": "2019-09-02",
        "checkOutDate": "2019-09-03",
        "cityId": 9395
        }
    }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/json;charset=utf-8'
));
$client->setUri($abreupackagesserviceURL . 'Booking/Book');
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

echo $return;
echo $response;
echo $return;

$response = json_decode($response, true);

echo '<xmp>';
var_dump($response);
echo '</xmp>';

die();

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$bookingReferenceNumber = $response['bookingReferenceNumber'];
if ($bookingReferenceNumber != "") {
    $bookingTotalAmount = $response['bookingTotalAmount'];
    $bookingCurrency = $response['bookingCurrency'];
    echo $return;
    echo $bookingCurrency;
    echo $return;

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('booking');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'bookingReferenceNumber' => $bookingReferenceNumber,
            'bookingTotalAmount' => $bookingTotalAmount,
            'bookingCurrency' => $bookingCurrency
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>