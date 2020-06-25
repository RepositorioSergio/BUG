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
echo "COMECOU PAYMENT POLICIES<br/>";
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

$url = "https://ws-iterpec.cangooroo.net/ws/Rest/RentACar.svc/GetPaymentPolicies";
$username = "api.xl";
$password = "JNpWAfo%3d&";

$raw = '{
    "Credential": {
      "Username": "api.xl",
      "Password": "JNpWAfo%3d&"
    },
    "Token": "0883bae1-00f3-4215-8eb5-6ab11eaf53b6",
    "CarId": 514
  }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'X-Agent-Email' => 'gzip,deflate',
    'X-Agent-Token' => 'Zend Framework',
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
$TotalTime = $response['TotalTime'];
$Car = $response['Car'];
if ($Car != null) {
    $AirConditioning = $Car['AirConditioning'];
    $BaggageQuantity = $Car['BaggageQuantity'];
    $CarModel = $Car['CarModel'];
    $Currency = $Car['Currency'];
    $IsAvailable = $Car['IsAvailable'];
    $NumberOfDoors = $Car['NumberOfDoors'];
    $PassengerQuantity = $Car['PassengerQuantity'];
    $ResponseId = $Car['ResponseId'];
    $TransmissionType = $Car['TransmissionType'];
    $DropOffLocationDetail = $Car['DropOffLocationDetail'];
    $DropOffLocationDetail_Address = $DropOffLocationDetail['Address'];
    $DropOffLocationDetail_Code = $DropOffLocationDetail['Code'];
    $DropOffLocationDetail_Latitude = $DropOffLocationDetail['Latitude'];
    $DropOffLocationDetail_Longitude = $DropOffLocationDetail['Longitude'];
    $DropOffLocationDetail_Phone = $DropOffLocationDetail['Phone'];
    $DropOffLocationDetail_ProviderId = $DropOffLocationDetail['ProviderId'];
    $DropOffLocationDetail_StoreId = $DropOffLocationDetail['StoreId'];
    $Integration = $Car['Integration'];
    $IntegrationId = $Integration['IntegrationId'];
    $IntegrationName = $Integration['IntegrationName'];
    $SippCode = $Integration['SippCode'];
    $PickUpLocationDetail = $Car['PickUpLocationDetail'];
    $PickUpLocationDetail_Address = $PickUpLocationDetail['Address'];
    $PickUpLocationDetail_Code = $PickUpLocationDetail['Code'];
    $PickUpLocationDetail_Latitude = $PickUpLocationDetail['Latitude'];
    $PickUpLocationDetail_Longitude = $PickUpLocationDetail['Longitude'];
    $PickUpLocationDetail_Phone = $PickUpLocationDetail['Phone'];
    $PickUpLocationDetail_ProviderId = $PickUpLocationDetail['ProviderId'];
    $PickUpLocationDetail_StoreId = $PickUpLocationDetail['StoreId'];
    $PriceInformation = $Car['PriceInformation'];
    $PaymentAtDestination = $PriceInformation['PaymentAtDestination'];
    $PaymentAtDestinationCurrency = $PaymentAtDestination['Currency'];
    $PaymentAtDestinationValue = $PaymentAtDestination['Value'];
    $PrePayment = $PriceInformation['PrePayment'];
    $PrePaymentCurrency = $PrePayment['Currency'];
    $PrePaymentValue = $PrePayment['Value'];
    $TotalPrice = $PriceInformation['TotalPrice'];
    $Currency = $TotalPrice['Currency'];
    $Value = $TotalPrice['Value'];
    $Rental = $Car['Rental'];
    $GroupName = $Rental['GroupName'];
    $IdGroup = $Rental['IdGroup'];
    $ProviderGroup = $Rental['ProviderGroup'];
    $RateCode = $Rental['RateCode'];
    $RentalCode = $Rental['RentalCode'];
    $RentalLogoUrl = $Rental['RentalLogoUrl'];
    $RentalName = $Rental['RentalName'];
    $Images = $Car['Images'];
    if (count($Images) > 0) {
        $image = "";
        for ($j=0; $j < count($Images); $j++) { 
            $image = $Images[$j];
        }
    }
    $CancellationPolicies = $Car['CancellationPolicies'];
    if (count($CancellationPolicies) > 0) {
        for ($k=0; $k < count($CancellationPolicies); $k++) { 
            $EndDate = $CancellationPolicies[$k]['EndDate'];
            $StartDate = $CancellationPolicies[$k]['StartDate'];
            $Value = $CancellationPolicies[$k]['Value'];
            $CancellationPolicies_Currency = $Value['Currency'];
            $CancellationPolicies_Value = $Value['Value'];
        }
    }
    $Features = $Car['Features'];
    if (count($Features) > 0) {
        for ($x=0; $x < count($Features); $x++) { 
            $EnglishDescription = $Features[$x]['EnglishDescription'];
            $PortugueseDescription = $Features[$x]['PortugueseDescription'];
            $SpanishDescription = $Features[$x]['SpanishDescription'];
        }
    }
}
?>