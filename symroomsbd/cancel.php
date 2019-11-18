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
echo "COMECOU CANCEL<br/>";
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
$affiliate_id_palace = 0;
$branch_filter = "";


$config = new \Zend\Config\Config(include '../config/autoload/global.symrooms.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = "https://api.travelgatex.com/";

$raw = '{"query":" mutation {\n  hotelX {\n  cancel (\n  input: {\n  accessCode: \"422\",\n  language: \"es\",\n  hotelCode: \"1\",\n  reference: {\n  client: \"BookTest002\",\n  supplier: \"854731\"  }\n  },\n  settings:{\n  client: \"Demo_Client\",\n  testMode: true  }\n  ) {\n  cancellation {\n  reference {\n  client\n  supplier\n  }\n  cancelReference\n  status\n   price {\n  currency\n   binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  booking {\n  reference {\n  client\n  supplier\n  }\n  holder {\n  name\n  surname\n  }\n  hotel {\n  creationDate\n  checkIn\n  checkOut\n  hotelCode\n  hotelName\n  boardCode\n  occupancies {\n  id\n  paxes {\n  age\n  }\n  }\n  rooms {\n  occupancyRefId\n  code\n  description\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  }\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  cancelPolicy {\n  refundable\n  cancelPenalties {\n  hoursBefore\n  penaltyType\n  currency\n  value\n  }\n  }\n  remarks\n  status\n  payable\n  }\n  }\n  }\n  }\n  }"}';

 $headers = array(
    'Authorization: Apikey 64780338-49c8-4439-7c7d-d03c2033b145',
	'Accept-Encoding: gzip, deflate, br',
	'Content-Type: application/json',
	'Accept: application/json',
	'Connection: keep-alive',
	'DNT: 1',
	'Origin: https://api.travelgatex.com'
); 

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);

echo $response;

$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

$response = json_decode($response, true);
/* echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';  */

$config = new \Zend\Config\Config(include '../config/autoload/global.symrooms.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$data = $response['data'];
$hotelX = $data['hotelX'];

$cancel = $hotelX['cancel'];
$cancellation = $cancel['cancellation'];

$cancelReference = $cancellation['cancelReference'];
$status = $cancellation['status'];
//reference
$reference = $cancellation['reference'];
$client = $reference['client'];
$supplier = $reference['supplier'];
//price
$price = $cancellation['price'];
$currency = $price['currency'];
$binding = $price['binding'];
$net = $price['net'];
$gross = $price['gross'];

$exchange = $price['exchange'];
$excurrency = $exchange['currency'];
$exrate = $exchange['rate'];

//booking
$booking = $cancellation['booking'];
//reference
$bookingreference = $booking['reference'];
$referenceclient = $bookingreference['client'];
$referencesupplier = $bookingreference['supplier'];
//holder
$holder = $booking['holder'];
$name = $holder['name'];
$surname = $holder['surname'];
//hotel
$hotel = $booking['hotel'];
$hotelCode = $hotel['hotelCode'];
$hotelName = $hotel['hotelName'];
$boardCode = $hotel['boardCode'];
$occupancies = $hotel['occupancies'];
$creationDate = $hotel['creationDate'];
$checkIn = $hotel['checkIn'];
$checkOut = $hotel['checkOut'];

$rooms = $hotel['rooms'];
for ($j=0; $j < count($rooms); $j++) { 
    $code = $rooms[$j]['code'];
    $description = $rooms[$j]['description'];
    $occupancyRefId = $rooms[$j]['occupancyRefId'];
    $price = $rooms[$j]['price'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('roomsbooking_cancel');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'code' => $code,
            'description' => $description,
            'occupancyrefid' => $occupancyRefId,
            'price' => $price,
            'client' => $client
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $ex) {
        echo $return;
        echo "ERRO2: " . $ex;
        echo $return;
    }
}

//price booking
$price = $booking['price'];
$bookingcurrency = $price['currency'];
$bookingbinding = $price['binding'];
$bookingnet = $price['net'];
$bookinggross = $price['gross'];

$exchange = $price['exchange'];
$exbcurrency = $exchange['currency'];
$exbrate = $exchange['rate'];

//cancelPolicy
$cancelPolicy = $booking['cancelPolicy'];
$refundable = $cancelPolicy['refundable'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('cancel');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'cancelreference' => $cancelReference,
        'status' => $status,
        'client' => $client,
        'supplier' => $supplier,
        'currency' => $currency,
        'binding' => $binding,
        'net' => $net,
        'gross' => $gross,
        'excurrency' => $excurrency,
        'exrate' => $exrate,
        'referenceclient' => $referenceclient,
        'referencesupplier' => $referencesupplier,
        'name' => $name,
        'surname' => $surname,
        'hotelcode' => $hotelCode,
        'hotelname' => $hotelName,
        'boardcode' => $boardCode,
        'occupancies' => $occupancies,
        'creationdate' => $creationDate,
        'checkin' => $checkIn,
        'checkout' => $checkOut,
        'bookingcurrency' => $bookingcurrency,
        'bookingbinding' => $bookingbinding,
        'bookingnet' => $bookingnet,
        'bookinggross' => $bookinggross,
        'exbcurrency' => $exbcurrency,
        'exbrate' => $exbrate,
        'refundable' => $refundable
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
} catch (Exception $ex) {
    echo $return;
    echo "ERRO1: " . $ex;
    echo $return;
}

//cancelPenalties
$cancelPenalties = $cancelPolicy['cancelPenalties'];
for ($i=0; $i < count($cancelPenalties); $i++) { 
    $hoursBefore = $cancelPenalties[$i]['hoursBefore'];
    $penaltyType = $cancelPenalties[$i]['penaltyType'];
    $currency = $cancelPenalties[$i]['currency'];
    $value = $cancelPenalties[$i]['value'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('cancelpenalties_cancel');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'hoursbefore' => $hoursBefore,
            'penaltytype' => $penaltyType,
            'currency' => $currency,
            'value' => $value
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $ex) {
        echo $return;
        echo "ERRO3: " . $ex;
        echo $return;
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>