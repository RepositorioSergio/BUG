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
echo "COMECOU SEARCH";
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

$url = "https://ws-iterpec.cangooroo.net/ws/Rest/Hotel.svc/Search";
$username = "api.xl";
$password = "JNpWAfo%3d&";

$raw = '{
    "Credential": {
      "Username": "' . $username . '",
      "Password": "' . $password . '"
    },
    "Criteria":{
      "DestinationId": 1003937,
      "NumNights": 1,
      "ReturnHotelStaticData": false,
      "ReturnOnRequestRooms": false,
      "CheckinDate": "2020-10-19",
      "MainPaxCountryCodeNationality": "BR",
      "SearchRooms": [{
         "NumAdults": 1,
         "Quantity": 1
      }],
      "Filters": {
         "MinPrice": 100.00,
         "MaxPrice": 1000.00,
         "CheapestRoomOnly": true
      }
    }
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

$Hotels = $response['Hotels'];
for ($i=0; $i < count($Hotels); $i++) { 
    $HotelId = $Hotels[$i]['HotelId'];
    $Name = $Hotels[$i]['Name'];
    $Longitude = $Hotels[$i]['Longitude'];
    $Latitude = $Hotels[$i]['Latitude'];
    $Category = $Hotels[$i]['Category'];
    $Address = $Hotels[$i]['Address'];
    $CustomFields = $Hotels[$i]['CustomFields'];
    for ($iAux=0; $iAux < count($CustomFields); $iAux++) { 
        $CustomFieldsName = $CustomFields[$iAux]['Name'];
        $CustomFieldsValue = $CustomFields[$iAux]['Value'];
    }
    $Rooms = $Hotels[$i]['Rooms'];
    for ($j=0; $j < count($Rooms); $j++) { 
        $Id = $Rooms[$j]['Id'];
        $BoardDescription = $Rooms[$j]['BoardDescription'];
        $CustomFields = $Rooms[$j]['CustomFields'];
        $HasBreakfast = $Rooms[$j]['HasBreakfast'];
        $IsAvailable = $Rooms[$j]['IsAvailable'];
        $IsNonRefundable = $Rooms[$j]['IsNonRefundable'];
        $IsPrePayment = $Rooms[$j]['IsPrePayment'];
        $MediaRoomId = $Rooms[$j]['MediaRoomId'];
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
        $ThumbUrl = $Rooms[$j]['ThumbUrl'];
        $CancellationPolicies = $Rooms[$j]['CancellationPolicies'];
        for ($k=0; $k < count($CancellationPolicies); $k++) { 
            $EndDate = $CancellationPolicies[$k]['EndDate'];
            $StartDate = $CancellationPolicies[$k]['StartDate'];
            $Value = $CancellationPolicies[$k]['Value'];
            $ValueCurrency = $Value['Currency'];
            $Value2 = $Value['Value'];
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>