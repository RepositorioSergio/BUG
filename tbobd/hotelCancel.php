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
echo "COMECOU HOTEL CANCEL<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$user = 'clubonehotelsTest';
$pass = 'Clu@28527768';
$confirmationno = '8IVYXV';

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
    </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/HotelCancel</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelCancelRequest>
        <hot:ConfirmationNo>' . $confirmationno . '</hot:ConfirmationNo>
        <hot:RequestType>HotelCancel</hot:RequestType>
        <hot:Remarks>test cancelhotel</hot:Remarks>
    </hot:HotelCancelRequest>
</soap:Body>
</soap:Envelope>';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Content-length: ".strlen($raw)
));
$url =  "https://api.tbotechnology.in/HotelAPI_V7/HotelService.svc";

$client->setUri($url);
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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$HotelCancelResponse = $Body->item(0)->getElementsByTagName("HotelCancelResponse");

$BookingId = $HotelCancelResponse->item(0)->getElementsByTagName("BookingId");
if ($BookingId->length > 0) {
    $BookingId = $BookingId->item(0)->nodeValue;
} else {
    $BookingId = "";
}
$CancellationCharge = $HotelCancelResponse->item(0)->getElementsByTagName("CancellationCharge");
if ($CancellationCharge->length > 0) {
    $CancellationCharge = $CancellationCharge->item(0)->nodeValue;
} else {
    $CancellationCharge = "";
}
$RefundAmount = $HotelCancelResponse->item(0)->getElementsByTagName("RefundAmount");
if ($RefundAmount->length > 0) {
    $RefundAmount = $RefundAmount->item(0)->nodeValue;
} else {
    $RefundAmount = "";
}
$RequestStatus = $HotelCancelResponse->item(0)->getElementsByTagName("RequestStatus");
if ($RequestStatus->length > 0) {
    $RequestStatus = $RequestStatus->item(0)->nodeValue;
} else {
    $RequestStatus = "";
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('hotelCancel');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'BookingId' => $BookingId,
        'CancellationCharge' => $CancellationCharge,
        'RefundAmount' => $RefundAmount,
        'RequestStatus' => $RequestStatus
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>