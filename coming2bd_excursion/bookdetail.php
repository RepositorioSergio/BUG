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
echo "COMECOU BOOK DETAIL";
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
$sql = "select value from settings where name='enablecoming2' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_coming2 = $affiliate_id;
} else {
    $affiliate_id_coming2 = 0;
}
$sql = "select value from settings where name='coming2login' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2login = $row_settings['value'];
}
$sql = "select value from settings where name='coming2password' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2password = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='coming2ServiceURL' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2ServiceURL = $row_settings['value'];
}
echo $return;
echo $coming2ServiceURL;
echo $return;
$sql = "select value from settings where name='coming2Company' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2Company = $row_settings['value'];
}


$config = new \Zend\Config\Config(include '../config/autoload/global.comming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$raw = '{
    "token": "token",
    "language": "ES",
    "locata": "SDS161359618"
    }';

$passuser = "COSTAMAR:COSTAMAR";
$auth = base64_encode($passuser);
echo $return;
echo $auth;
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

$client->setUri($coming2ServiceURL . '/operacional/bookdetail');
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
$config = new \Zend\Config\Config(include '../config/autoload/global.comming2.php');
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
    $insert->into('booklist');
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
    $bookingDate = $bookLineList[$i]['bookingDate'];
    $serviceDate = $bookLineList[$i]['serviceDate'];
    $status = $bookLineList[$i]['status'];
    $destination = $bookLineList[$i]['destination'];
    $destinationName = $destination['name'];

    $meetPoint = $bookLineList[$i]['meetPoint'];
    $hotelCode = $meetPoint['hotelCode'];
    $hotelName = $meetPoint['hotelName'];
    $lobbyCode = $meetPoint['lobbyCode'];
    $lobbyName = $meetPoint['lobbyName'];
    $time = $meetPoint['time'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('booklist_bookLineList');
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
            'hotelCode' => $hotelCode,
            'hotelName' => $hotelName,
            'lobbyCode' => $lobbyCode,
            'lobbyName' => $lobbyName,
            'time' => $time
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
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>