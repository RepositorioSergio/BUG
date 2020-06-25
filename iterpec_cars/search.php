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
echo "COMECOU SEARCH<br/>";
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

$url = "https://ws-iterpec.cangooroo.net/ws/Rest/RentACar.svc/Search";
$username = "api.xl";
$password = "JNpWAfo%3d&";

$raw = '{
    "Credential": {
      "Username": "api.xl",
      "Password": "JNpWAfo%3d&"
     },
    "Criteria":{
      "Pickup": {
      "Date": "2020-08-14",
      "Hour": 12,
      "Minutes": 00,
      "LocationCode": "MCO",
      "LocationType": "Airport"
     },
     "Dropoff": {
      "Date": "2020-08-16",
      "Hour": 12,
      "Minutes": 00,
      "LocationCode": "MIA",
      "LocationType": "Airport"
     }
    }
   }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'Content-Length' => strlen($raw)
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
$TotalCarResults = $response['TotalCarResults'];
$Cars = $response['Cars'];
if (count($Cars) > 0) {
    for ($i=0; $i < count($Cars); $i++) { 
        $AirConditioning = $Cars[$i]['AirConditioning'];
        $BaggageQuantity = $Cars[$i]['BaggageQuantity'];
        $CarModel = $Cars[$i]['CarModel'];
        $Currency = $Cars[$i]['Currency'];
        $IsAvailable = $Cars[$i]['IsAvailable'];
        $NumberOfDoors = $Cars[$i]['NumberOfDoors'];
        $PassengerQuantity = $Cars[$i]['PassengerQuantity'];
        $ResponseId = $Cars[$i]['ResponseId'];
        $TransmissionType = $Cars[$i]['TransmissionType'];
        $DropOffLocationDetail = $Cars[$i]['DropOffLocationDetail'];
        $DropOffLocationDetail_Address = $DropOffLocationDetail['Address'];
        $DropOffLocationDetail_Code = $DropOffLocationDetail['Code'];
        $DropOffLocationDetail_Latitude = $DropOffLocationDetail['Latitude'];
        $DropOffLocationDetail_Longitude = $DropOffLocationDetail['Longitude'];
        $DropOffLocationDetail_Phone = $DropOffLocationDetail['Phone'];
        $DropOffLocationDetail_ProviderId = $DropOffLocationDetail['ProviderId'];
        $DropOffLocationDetail_StoreId = $DropOffLocationDetail['StoreId'];
        $Integration = $Cars[$i]['Integration'];
        $IntegrationId = $Integration['IntegrationId'];
        $IntegrationName = $Integration['IntegrationName'];
        $SippCode = $Integration['SippCode'];
        $PickUpLocationDetail = $Cars[$i]['PickUpLocationDetail'];
        $PickUpLocationDetail_Address = $PickUpLocationDetail['Address'];
        $PickUpLocationDetail_Code = $PickUpLocationDetail['Code'];
        $PickUpLocationDetail_Latitude = $PickUpLocationDetail['Latitude'];
        $PickUpLocationDetail_Longitude = $PickUpLocationDetail['Longitude'];
        $PickUpLocationDetail_Phone = $PickUpLocationDetail['Phone'];
        $PickUpLocationDetail_ProviderId = $PickUpLocationDetail['ProviderId'];
        $PickUpLocationDetail_StoreId = $PickUpLocationDetail['StoreId'];
        $PriceInformation = $Cars[$i]['PriceInformation'];
        $TotalPrice = $PriceInformation['TotalPrice'];
        $Currency = $TotalPrice['Currency'];
        $Value = $TotalPrice['Value'];
        $Rental = $Cars[$i]['Rental'];
        $GroupName = $Rental['GroupName'];
        $IdGroup = $Rental['IdGroup'];
        $ProviderGroup = $Rental['ProviderGroup'];
        $RateCode = $Rental['RateCode'];
        $RentalCode = $Rental['RentalCode'];
        $RentalLogoUrl = $Rental['RentalLogoUrl'];
        $RentalName = $Rental['RentalName'];
        $Images = $Cars[$i]['Images'];
        if (count($Images) > 0) {
            $image = "";
            for ($j=0; $j < count($Images); $j++) { 
                $image = $Images[$j];
            }
        }
        $CancellationPolicies = $Cars[$i]['CancellationPolicies'];
        if (count($CancellationPolicies) > 0) {
            for ($k=0; $k < count($CancellationPolicies); $k++) { 
                $EndDate = $CancellationPolicies[$k]['EndDate'];
                $StartDate = $CancellationPolicies[$k]['StartDate'];
                $Value = $CancellationPolicies[$k]['Value'];
                $CancellationPolicies_Currency = $Value['Currency'];
                $CancellationPolicies_Value = $Value['Value'];
            }
        }
        $Features = $Cars[$i]['Features'];
        if (count($Features) > 0) {
            for ($x=0; $x < count($Features); $x++) { 
                $EnglishDescription = $Features[$x]['EnglishDescription'];
                $PortugueseDescription = $Features[$x]['PortugueseDescription'];
                $SpanishDescription = $Features[$x]['SpanishDescription'];
            }
        }
    }
}
?>