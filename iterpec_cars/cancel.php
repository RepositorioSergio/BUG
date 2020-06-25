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
echo "COMECOU CANCEL BOOKING<br/>";
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

$url = "https://ws-iterpec.cangooroo.net/ws/Rest/RentACar.svc/CancelBooking";
$username = "api.xl";
$password = "JNpWAfo%3d&";

$raw = '{
    "Credential": {
      "Username": "' . $username . '",
      "Password": "' . $password . '"
    },
    "ServiceId": 177
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
$BookingDescription = $Car['BookingDescription'];
$BookingId = $Car['BookingId'];
$CarModel = $Car['CarModel'];
$FinalCity = $Car['FinalCity'];
$FinalServiceDate = $Car['FinalServiceDate'];
$Luggages = $Car['Luggages'];
$NumberOfDays = $Car['NumberOfDays'];
$PaymentStatus = $Car['PaymentStatus'];
$PriceInformation = $Car['PriceInformation'];
$PrincipalPicture = $Car['PrincipalPicture'];
$ProviderCode = $Car['ProviderCode'];
$ReservationDate = $Car['ReservationDate'];
$ServiceDate = $Car['ServiceDate'];
$ServiceId = $Car['ServiceId'];
$SippCodes = $Car['SippCodes'];
$Status = $Car['Status'];
$UsedToken = $Car['UsedToken'];
$VendorName = $Car['VendorName'];
$features = $Car['features'];
$inicialCity = $Car['inicialCity'];
$Driver = $Car['Driver'];
$Age = $Driver['Age'];
$Cpf = $Driver['Cpf'];
$MainPax = $Driver['MainPax'];
$Name = $Driver['Name'];
$Surname = $Driver['Surname'];
$Title = $Driver['Title'];
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
?>