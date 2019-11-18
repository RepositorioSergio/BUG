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
echo "COMECOU BOOK<br/>";
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

$raw = '{"query":" mutation {\n  hotelX {\n  book (\n  input: {\n  optionRefId: \"81@11[191223[191224[1[14[1[EN[GB[en[EUR[300[0[422[1[14[1[0[18111033[DPR@DPR[50#0#false#EUR##0#[1|30#30|1|2019-12-23|1|143250|143278|14|0|0[1@1[30#30[[After@50@Before@50@ExpireDate@24/12/2019@mercado@EN@tgx_sess@0d56a883-756e-4fab-a4a6-37574096a402\",\n  clientReference: \"BookTest002\",\n  deltaPrice: {\n  amount: 50,\n  percent: 0,\n  applyBoth: true\n  },\n  paymentCard: {\n  cardType: \"VI\",\n holder: {\n name: \"Test\",\n surname: \"Test\" },\n number: \"0123456789101112\",\n CVC: \"123\",\n expire: {\n month: 8,\n year: 21\n }\n },\n  holder: {\n  name: \"Test\",\n  surname: \"surnameTest\"\n  },\n  rooms: { \n  occupancyRefId: 1,\n  paxes: [{\n  name: \"Pax1 name\",\n  surname: \"Pax1 surname\",\n  age: 30\n  },\n  {\n  name: \"Pax2 name\",\n  surname: \"Pax2 surname\",\n  age: 30\n  }]\n  }\n  },\n  settings: {\n  client: \"Demo_Client\",\n  testMode: true\n  }) {\n  booking {\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  status\n  remarks\n  reference {\n  client\n  supplier\n  }\n  holder {\n  name\n  surname\n  }\n  hotel {\n  creationDate\n  checkIn\n  checkOut\n  hotelCode\n  hotelName\n  boardCode\n  occupancies {\n  id\n  paxes {\n  age\n}\n  }\n  rooms {\n  code\n  description\n  occupancyRefId\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  }\n  }\n  errors {\n  code\n  type\n  description\n  }\n  warnings {\n  code\n  type\n  description\n  }\n  }\n  }\n  }"}';

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
$book = $hotelX['book'];

$booking = $book['booking'];

//price
$price = $booking['price'];
$currency = $price['currency'];
$binding = $price['binding'];
$net = $price['net'];
$gross = $price['gross'];

$status = $booking['status'];
$remarks = $booking['remarks'];
$holder = $booking['holder'];
$hotel = $booking['hotel'];

$reference = $booking['reference'];
$client = $reference['client'];
$supplier = $reference['supplier'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('book');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'currency' => $currency,
        'binding' => $binding,
        'net' => $net,
        'gross' => $gross,
        'status' => $status,
        'remarks' => $remarks,
        'holder' => $holder,
        'hotel' => $hotel,
        'client' => $client,
        'supplier' => $supplier
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


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>