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
echo "COMECOU BOOKING<br/>";
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

$url = "https://ws-iterpec.cangooroo.net/ws/Rest/RentACar.svc/DoBooking";
$username = "api.xl";
$password = "JNpWAfo%3d&";

$raw = '{
    "Credential": {
       "Username": "api.xl",
       "Password": "JNpWAfo%3d&"
    },
    "Token": "0883bae1-00f3-4215-8eb5-6ab11eaf53b6",
    "CarId": 514,
    "Driver":{
       "Name": "Test",
       "Surname": "Test2",
       "Age": 25,
       "Cpf": "40028922080",
       "Title": "Mr"
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
$Cars = $response['Cars'];
if (count($Cars) > 0) {
    for ($i=0; $i < count($Cars); $i++) { 
        $BookingDescription = $Cars[$i]['BookingDescription'];
        $BookingId = $Cars[$i]['BookingId'];
        $CarModel = $Cars[$i]['CarModel'];
        $FinalCity = $Cars[$i]['FinalCity'];
        $FinalServiceDate = $Cars[$i]['FinalServiceDate'];
        $Luggages = $Cars[$i]['Luggages'];
        $NumberOfDays = $Cars[$i]['NumberOfDays'];
        $PaymentStatus = $Cars[$i]['PaymentStatus'];
        $PrincipalPicture = $Cars[$i]['PrincipalPicture'];
        $ProviderCode = $Cars[$i]['ProviderCode'];
        $ReservationDate = $Cars[$i]['ReservationDate'];
        $ServiceDate = $Cars[$i]['ServiceDate'];
        $ServiceId = $Cars[$i]['ServiceId'];
        $SippCodes = $Cars[$i]['SippCodes'];
        $Status = $Cars[$i]['Status'];
        $UsedToken = $Cars[$i]['UsedToken'];
        $VendorName = $Cars[$i]['VendorName'];
        $features = $Cars[$i]['features'];
        $inicialCity = $Cars[$i]['inicialCity'];
        $Driver = $Cars[$i]['Driver'];
        $Age = $Driver['Age'];
        $Cpf = $Driver['Cpf'];
        $MainPax = $Driver['MainPax'];
        $Name = $Driver['Name'];
        $Surname = $Driver['Surname'];
        $Title = $Driver['Title'];
        $PriceInformation = $Cars[$i]['PriceInformation'];
        $PaymentAtDestination = $PriceInformation['PaymentAtDestination'];
        $PaymentAtDestinationCurrency = $PaymentAtDestination['Currency'];
        $PaymentAtDestinationValue = $PaymentAtDestination['Value'];
        $PrePayment = $PriceInformation['PrePayment'];
        $PrePaymentCurrency = $PrePayment['Currency'];
        $PrePaymentValue = $PrePayment['Value'];
        $TotalPrice = $PriceInformation['TotalPrice'];
        $TotalPriceCurrency = $TotalPrice['Currency'];
        $TotalPriceValue = $TotalPrice['Value'];
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
    }
}
?>