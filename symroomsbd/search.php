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

$raw = '{"query":"{\n  hotelX {\n    search( criteria: {\n                checkIn: \"2019-12-23\",\n                checkOut: \"2019-12-24\",\n                hotels: [\"1\"],\n                occupancies: [ {paxes: [{age: 30}, {age: 30}]}]},\n                settings: {\n                      client: \"Demo_Client\",\n                      testMode: true,\n                      context: \"HOTELTEST\"}) {\n      options {\n        id\n        supplierCode\n        hotelCode\n        hotelName\n        boardCode\n        price {\n          net\n          currency\n        }\n      }\n      errors {\n        code\n        type\n        description\n      }\n      warnings {\n        code\n        type\n        description\n      }\n    }\n  }\n}"}';

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
$search = $hotelX['search'];

//options
$options = $search['options'];
for ($i=0; $i < count($options); $i++) { 
    $id = $options[$i]['id'];
    $supplierCode = $options[$i]['supplierCode'];
    $hotelCode = $options[$i]['hotelCode'];
    $hotelName = $options[$i]['hotelName'];
    $boardCode = $options[$i]['boardCode'];
    $price = $options[$i]['price'];
    $net = $price['net'];
    $currency = $price['currency'];
}


//warnings
$warnings = $search['warnings'];
for ($j=0; $j < count($warnings); $j++) { 
    $code = $warnings[$j]['code'];
    $type = $warnings[$j]['type'];
    $description = $warnings[$j]['description'];
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>