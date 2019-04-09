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
echo "COMECOU PRE";
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
    "token": "' . $token . '", "language": "ES", "excursionList" : [{ "excursionID":"EXC017096840012965543", "hotel":"DIRECTTFS",
    "lobby":"DIRECTOS",
    "date":"20190701",
    "language": "ES"
    }
    ],
    "preBook" : "true" }';
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
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.coming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$bookingToken = $response['bookingToken'];
try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('prebooking');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'bookingToken' => $bookingToken
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

$excursionList = $response['excursionList'];
for ($i=0; $i < count($excursionList); $i++) { 
    $title = $excursionList[$i]['title'];
    $groupInfo = $excursionList[$i]['groupInfo'];
    $groupInfoName = $groupInfo['name'];
    $selectedDate = $excursionList[$i]['selectedDate'];
    $voucher = $excursionList[$i]['voucher'];

    $paxDistribution = $excursionList[$i]['paxDistribution'];
    $adults = $paxDistribution['adults'];
    $kids = $paxDistribution['kids'];
    $babies = $paxDistribution['babies'];

    $price2 = 0;
    $price = $excursionList[$i]['price'];
    $currency = $price['currency'];
    $currencyCode = $currency['code'];

    $netPrice = $price['netPrice'];
    $commission = $price['commission'];
    $adultsPrice = $price['adultsPrice'];
    $kidsPrice = $price['kidsPrice'];
    $babiesPrice = $price['babiesPrice'];
    $price2 = $price['price'];

    $meetPoint = $excursionList[$i]['meetPoint'];
    $meetPointCode = $meetPoint['code'];
    $latitude = $meetPoint['latitude'];
    $longitude = $meetPoint['longitude'];
    $hotelCode = $meetPoint['hotelCode'];
    $hotelName = $meetPoint['hotelName'];
    $lobbyCode = $meetPoint['lobbyCode'];
    $lobbyName = $meetPoint['lobbyName'];
    $time = $meetPoint['time'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('prebooking_excursionList');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'title' => $title,
            'groupInfoName' => $groupInfoName,
            'selectedDate' => $selectedDate,
            'voucher' => $voucher,
            'adults' => $adults,
            'kids' => $kids,
            'babies' => $babies,
            'currencyCode' => $currencyCode,
            'netPrice' => $netPrice,
            'commission' => $commission,
            'adultsPrice' => $adultsPrice,
            'kidsPrice' => $kidsPrice,
            'babiesPrice' => $babiesPrice,
            'price' => $price2,
            'meetPointCode' => $meetPointCode,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'hotelCode' => $hotelCode,
            'hotelName' => $hotelName,
            'lobbyCode' => $lobbyCode,
            'lobbyName' => $lobbyName,
            'time' => $time,
            'bookingToken' => $bookingToken
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

    $cancelFeeList = $excursionList[$i]['cancelFeeList'];
    for ($iAux=0; $iAux < count($cancelFeeList); $iAux++) { 
        $currency = $cancelFeeList[$iAux]['currency'];
        $currencyCode = $currency['code'];
        $price = $cancelFeeList[$iAux]['price'];
        $fromDate = $cancelFeeList[$iAux]['fromDate'];
        $toDate = $cancelFeeList[$iAux]['toDate'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('prebooking_cancelFeeList');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'currencyCode' => $currencyCode,
                'price' => $price,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'bookingToken' => $bookingToken
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