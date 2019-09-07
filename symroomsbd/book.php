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


$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = "https://api.travelgatex.com/";

$raw = '{"query":" mutation {\n hotelX {\n  book(input: {\n  optionRefId: \"71@11[191223[191224[1[1[1[EN[GB[en[EUR[0[1[1023[1[1[0[1[50#0#false#EUR##0#[1|30#30|1|2019-12-23|1|5456|5454|1|1|0[16[30#30[240#2#EUR#20@120#2#EUR#50[After@50@Before@50@ExpireDate@24/12/2019@mercado@EN@tgx_sess@790f17b7-8eb7-430a-8ca5-dc5249ce52c8\",\n  clientReference: \"BookTest1100\",\n  deltaPrice: {\n amount: 50,\n percent: 0,\n applyBoth: true },\n  paymentCard: {\n cardType: \"VI\",\n holder: {\n name: \"Test\",\n surname: \"Test\" },\n number: \"0123456789101112\",\n CVC: \"123\",\n expire: {\n month: 8,\n year: 21 }\n },\n  holder: {\n name: \"Test\",\n surname: \"Test\" },\n  rooms: [  {\n occupancyRefId: 1,\n paxes: [{\n name: \"Test1\",\n surname: \"Test1\",\n age: 26},\n {\n  name: \"Test2\",\n surname: \"Test2\",\n age: 30}\n]}\n]  },\n  settings: {\n  client: \"Demo_Client\",\n  testMode: true}) {\n  booking {\n  hotel {\n  hotelCode\n  hotelName\n  }\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  errors {\n  code\n  type\n  description\n  }\n  warnings {\n  code\n  description\n  }\n  }\n }}"}';

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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>'; 

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
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
$quote = $hotelX['quote'];

$optionQuote = $hotelX['optionQuote'];
$optionRefId = $optionQuote['optionRefId'];
$status = $optionQuote['status'];
//price
$price = $optionQuote['price'];
$currency = $price['currency'];
$binding = $price['binding'];
$net = $price['net'];
$gross = $price['gross'];
//exchange
$exchange = $price['exchange'];
$currency = $exchange['currency'];
$rate = $exchange['rate'];



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>