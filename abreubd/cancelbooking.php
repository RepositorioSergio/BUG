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
echo "COMECOU BOOKING CANCEL<br/>";
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
$sql = "select value from settings where name='enableabreupackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_abreu = $affiliate_id;
} else {
    $affiliate_id_abreu = 0;
}
echo "<br/> affiliate_id_abreu " . $affiliate_id_abreu;
$sql = "select value from settings where name='abreupackagesuser' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagesuser = $row_settings['value'];
}
echo "<br/> abreupackagesuser " . $abreupackagesuser;
$sql = "select value from settings where name='abreupackagespassword' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagespassword = base64_decode($row_settings['value']);
}
echo "<br/> abreupackagespassword " . $abreupackagespassword;
$sql = "select value from settings where name='abreupackagesserviceURL' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagesserviceURL = $row_settings['value'];
}
echo "<br/> abreupackagesserviceURL " . $abreupackagesserviceURL;
$db->getDriver()
    ->getConnection()
    ->disconnect();
    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT bookingReferenceNumber FROM booking";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}


$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $result = $result->current();
    $bookingReferenceNumber = $result['bookingReferenceNumber'];
}
echo $return;
echo $bookingReferenceNumber;
echo $return;

$raw = '{   "username": "' . $abreupackagesuser . '",   "password": "' . $abreupackagespassword . '",   "language": "ES",   "bookingReferenceNumber": "' . $bookingReferenceNumber . '" }';

$client = new Client();
$client->setOptions(array(
'timeout' => 100,
'sslverifypeer' => false,
'sslverifyhost' => false
));
$client->setHeaders(array(
'Accept-Encoding' => 'gzip,deflate',
'X-Powered-By' => 'Zend Framework',
'Content-Length' => strlen($raw),
'Content-Type' => 'application/json;charset=utf-8'
));
$client->setUri($abreupackagesserviceURL . 'Booking/Cancel');
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
//die();
}
$response = iconv('UTF-8', 'ASCII//TRANSLIT', $response);
echo $return;
echo $response;
echo $return;
$response = json_decode($response, true);
if ($response === false || $response === null) {
    echo $return;
    echo "NOT DECODE";
    echo $return;
}


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
'driver' => $config->db->driver,
'database' => $config->db->database,
'username' => $config->db->username,
'password' => $config->db->password,
'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$canceled = $response['canceled'];
$cancellationCharges = $response['cancellationCharges'];
$cancellationChargesCurrency = $response['cancellationChargesCurrency'];
if ($cancellationChargesCurrency != "") {
    $bookingReferenceNumber = $response['bookingReferenceNumber'];
echo $return;
echo $bookingReferenceNumber;
echo $return;

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('bookingCancel');
    $insert->values(array(
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'canceled' => $canceled,
    'cancellationCharges' => $cancellationCharges,
    'cancellationChargesCurrency' => $cancellationChargesCurrency,
    'bookingReferenceNumber' => $bookingReferenceNumber
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "ERRO BOOK: " . $e;
    echo $return;
}
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>