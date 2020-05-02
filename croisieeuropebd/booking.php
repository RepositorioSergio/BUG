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
echo "COMECOU BOOK";
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
    "token": "' . $token . '",
    "language": "ES",
    "name" : "Paulo",
    "surname" : "Andrade",
    "preBook" : "false",
    "agencyRef" : "Test",
    "bookingToken" : "BLQ017096874142792431#1"
    }';
echo $return;
echo "RAW: " . $raw;
echo $return;


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

$client->setUri($url . '/excursion/booking');
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

$booking = $response['booking'];
$file = $booking['file'];
$agencyRef = $booking['agencyRef'];
$client = $booking['client'];
$clientCode = $client['code'];
$operator = $client['operator'];
$agency = $client['agency'];
$agencyCode = $agency['code'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('booking');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'file' => $file,
        'agencyRef' => $agencyRef,
        'clientCode' => $clientCode,
        'operator' => $operator,
        'agencyCode' => $agencyCode
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "Error BOOK: " . $e;
    echo $return;
}

$bookLineList = $booking['bookLineList'];
for ($i=0; $i < count($bookLineList); $i++) { 
    $locata = $bookLineList[$i]['locata'];
    $name = $bookLineList[$i]['name'];
    $holder = $bookLineList[$i]['holder'];

    $paxDistribution = $bookLineList[$i]['paxDistribution'];
    $adults = $paxDistribution['adults'];
    $kids = $paxDistribution['kids'];
    $babies = $paxDistribution['babies'];
    $frees = $paxDistribution['frees'];

    $price2 = 0;
    $price = $bookLineList[$i]['price'];
    $currency = $price['currency'];
    $currencyCode = $currency['code'];

    $netPrice = $price['netPrice'];
    $commission = $price['commission'];
    $discount = $price['discount'];
    $refund = $price['refund'];
    $tax = $price['tax'];
    $price2 = $price['price'];

    $bookingDate = $bookLineList[$i]['bookingDate'];
    $serviceDate = $bookLineList[$i]['serviceDate'];
    $productType = $bookLineList[$i]['productType'];
    $status = $bookLineList[$i]['status'];
    $destination = $bookLineList[$i]['destination'];
    $destinationcode = $destination['code'];
    $destinationName = $destination['name'];

    $meetPoint = $bookLineList[$i]['meetPoint'];
    $hotelCode = $meetPoint['hotelCode'];
    $hotelName = $meetPoint['hotelName'];
    $lobbyCode = $meetPoint['lobbyCode'];
    $lobbyName = $meetPoint['lobbyName'];
    $time = $meetPoint['time'];

    $tourDescription = $bookLineList[$i]['tourDescription'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('booking_bookLineList');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'locata' => $locata,
            'name' => $name,
            'holder' => $holder,
            'adults' => $adults,
            'kids' => $kids,
            'babies' => $babies,
            'frees' => $frees,
            'currencyCode' => $currencyCode,
            'netPrice' => $netPrice,
            'commission' => $commission,
            'discount' => $discount,
            'refund' => $refund,
            'tax' => $tax,
            'price' => $price2,
            'bookingDate' => $bookingDate,
            'serviceDate' => $serviceDate,
            'productType' => $productType,
            'status' => $status,
            'destinationcode' => $destinationcode,
            'destinationName' => $destinationName,
            'hotelCode' => $hotelCode,
            'hotelName' => $hotelName,
            'lobbyCode' => $lobbyCode,
            'lobbyName' => $lobbyName,
            'time' => $time,
            'tourDescription' => $tourDescription
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

    $cancelFeeList = $bookLineList[$i]['cancelFeeList'];
    for ($iAux=0; $iAux < count($cancelFeeList); $iAux++) { 
        $currency = $cancelFeeList[$iAux]['currency'];
        $currencyCode = $currency['code'];
        $price = $cancelFeeList[$iAux]['price'];
        $fromDate = $cancelFeeList[$iAux]['fromDate'];
        $toDate = $cancelFeeList[$iAux]['toDate'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('booking_cancelFeeList');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'currencyCode' => $currencyCode,
                'price' => $price,
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error TH: " . $e;
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