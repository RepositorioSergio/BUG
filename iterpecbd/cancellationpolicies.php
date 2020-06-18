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
echo "COMECOU CANCELLATION POLICIES<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = "https://ws-iterpec.cangooroo.net/ws/Rest/Hotel.svc/GetCancellationPolicies";
$username = "api.xl";
$password = "JNpWAfo%3d&";

$raw = '{
    "Credential": {
      "Username": "api.xl",
      "Password": "JNpWAfo%3d&"
    },
      "Token": "8133c39b-d7dd-4b51-8764-c383c8a3c80b",
      "HotelId": 2110305,
      "RoomIds": ["3160001"]
  }';
echo $return;
echo $raw;
echo $return;
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
    'Content-Type' => 'application/x-www-form-urlencoded'
));
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
echo $return;
echo $response;
echo $return;
$response = json_decode($response, true);
echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$TimeSpan = $response['TimeSpan'];
$Token = $response['Token'];
$TotalTime = $response['TotalTime'];

$Rooms = $response['Rooms'];
for ($j=0; $j < count($Rooms); $j++) { 
    $Id = $Rooms[$j]['Id'];
    $BoardDescription = $Rooms[$j]['BoardDescription'];
    $CustomFields = $Rooms[$j]['CustomFields'];
    $HasBreakfast = $Rooms[$j]['HasBreakfast'];
    $IsAvailable = $Rooms[$j]['IsAvailable'];
    $IsNonRefundable = $Rooms[$j]['IsNonRefundable'];
    $IsPrePayment = $Rooms[$j]['IsPrePayment'];
    $MediaRoomId = $Rooms[$j]['MediaRoomId'];
    $MoreInformation = $Rooms[$j]['MoreInformation'];
    $NumAdults = $Rooms[$j]['NumAdults'];
    $PayDirectToHotel = $Rooms[$j]['PayDirectToHotel'];
    $Quantity = $Rooms[$j]['Quantity'];
    $RoomDescription = $Rooms[$j]['RoomDescription'];
    $SellingPricePerRoom = $Rooms[$j]['SellingPricePerRoom'];
    $SellingPriceCurrency = $SellingPricePerRoom['Currency'];
    $SellingPriceValue = $SellingPricePerRoom['Value'];
    $TotalSellingPrice = $Rooms[$j]['TotalSellingPrice'];
    $Currency = $TotalSellingPrice['Currency'];
    $Value = $TotalSellingPrice['Value'];
    $SupplierHotelAddress = $Rooms[$j]['SupplierHotelAddress'];
    $SupplierHotelName = $Rooms[$j]['SupplierHotelName'];
    $CancellationPolicies = $Rooms[$j]['CancellationPolicies'];
    for ($k=0; $k < count($CancellationPolicies); $k++) { 
        $EndDate = $CancellationPolicies[$k]['EndDate'];
        $StartDate = $CancellationPolicies[$k]['StartDate'];
        $Value = $CancellationPolicies[$k]['Value'];
        $ValueCurrency = $Value['Currency'];
        $Value2 = $Value['Value'];
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>