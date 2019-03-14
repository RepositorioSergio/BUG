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
echo "COMECOU COUNTRIES";
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
$affiliate_id = 0;
$branch_filter = "";

$config = new \Zend\Config\Config(include '../config/autoload/global.getaroom.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$raw = '{
    "Language": "ES",
    "ParentCode": "AGR#MALLOPMI15#PMI"
}';
/* $startTime = microtime();
echo $return;
echo $url . $raw;
echo $return;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $AbreuHOTELAVAILABILITY);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "COSTAMAR:COSTAMAR");
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Authorization: Basic",
    "Content-length: " . strlen($raw)
));
$response = curl_exec($ch);
curl_close($ch);
$endTime = microtime(); */
$passuser = "COSTAMAR:COSTAMAR";
$auth = base64_encode($passuser);
echo $return;
echo $auth;
echo $return;
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Authorization: Basic " . $auth,
    "Content-length: " . strlen($raw)
));
echo "<br/>PASSOU<br/>";
$client->setUri('http://services.bedbank.coming2.com/hotel-api/Area/Zones');echo "<br/>PASSOU 2<br/>";
//$client->setAuth('COSTAMAR:COSTAMAR');
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

$response = json_decode($response, true);
echo $return;
echo "PASSOU";
echo $return;
if($response === false || $response === null){
    echo $return;
    echo "NOT DECODE";
    echo $return;
}


echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.getaroom.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

for ($i=0; $i < count($response); $i++) { 
    $Code = $response[$i]['Code'];
    $Name = $response[$i]['Name'];
    $ParentCode = $response[$i]['ParentCode'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('zones');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'Code' => $Code,
            'Name' => $Name,
            'ParentCode' => $ParentCode
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error: " . $e;
        echo $return;
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>