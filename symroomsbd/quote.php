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
echo "COMECOU QUOTE<br/>";
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

$raw = '{"query":"{\n hotelX {\n quote(criteria: {\n  optionRefId: \"81@01[200623[200625[1[14[0[EN[GB[en[EUR[300[0[1023[1[14[0[0[03011209[BARTEST#BARTEST@BARTEST#BARTEST[290#0#false#EUR##0#[1|30#30/30#30#5|1|2020-06-23|2|1215681|1215700|14|0|0[1325@1325[30#30[2|30#30/30#30#5|1|2020-06-23|2|1215681|1215700|14|0|0[1325@1325[30#30#5[[mercado@EN@ExpireDate@25/06/2020@RateRule@tarifaOpaca\"},\n  settings: {\n  client: \"Demo_Client\",\n  testMode: true,\n  context: \"HOTELTEST\"}) {\n  optionQuote {\n  optionRefId\n  status\n  price {\n  currency\n  binding\n  net\n gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  errors {\n  code\n  description\n  }\n  warnings {\n  code\n  description\n  }\n  }\n  }\n  }"}';

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
$excurrency = $exchange['currency'];
$exrate = $exchange['rate'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('quote');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'optionRefId' => $optionRefId,
        'status' => $status,
        'currency' => $currency,
        'binding' => $binding,
        'net' => $net,
        'gross' => $status,
        'excurrency' => $excurrency,
        'exrate' => $exrate
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