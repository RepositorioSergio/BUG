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
echo "COMECOU ADMIN<br/>";
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

$raw = '{"query":"{\n  admin {\n clients{\n edges{\n node{\n code\n clientData{\n code\n name\n }\n }\n }\n }\n accesses {\n edges {\n     node {\n code\n accessData{\n code\n supplier{\n code\n supplierData{\n context\n }\n }\n name\n }\n error {\n code\n type\n description\n }\n createdAt\n updatedAt\n }\n }\n }\n }\n }"}';

/* $client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Host: api.palaceresorts.com",
    "Content-length: " . strlen($raw)
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
}  */

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
$admin = $data['admin'];
//clients
$clients = $admin['clients'];
$edges = $clients['edges'];
for ($i=0; $i < count($edges); $i++) { 
    $node = $edges[$i]['node'];
    $code = $node['code'];

    $clientData = $node['clientData'];
    $clientDatacode = $clientData['code'];
    $name = $clientData['name'];
}


//accesses
$accesses = $admin['accesses'];
$edges = $accesses['edges'];
for ($j=0; $j < count($edges); $j++) { 
    $node = $edges[$j]['node'];
    $code = $node['code'];
    $createdAt = $node['createdAt'];
    $updatedAt = $node['updatedAt'];

    $accessData = $node['accessData'];
    $accessDatacode = $accessData['code'];
    $name = $accessData['name'];

    $supplier = $accessData['supplier'];
    $suppliercode = $supplier['code'];
    $supplierData = $supplier['supplierData'];
    $context = $supplierData['context'];
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>