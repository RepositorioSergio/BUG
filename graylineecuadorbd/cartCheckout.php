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
echo "COMECOU CART CHECK<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.graylineecuador.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));

$url = "http://demo.gl-tours.com/web_service_cart_check_out.php?UserRand=user1780375154&fName=David&lName=Neves&MrA=Mr&email=davidneves@gmail.com&mNumber=986520178&hName=Reconquista Garden&hAddress=Esmeralda 675, Centro&Country=BR&Pax=2&adultName1=Gloria Neves&MrA1=Mrs&PassporA1=BR21d4100&NacionalityA1=Brasil&BirthDateA1=1981-05-21&User=TEST&Pass=1234";

$client->setUri($url);
$client->setMethod('POST');
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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.graylineecuador.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$response2 = $inputDoc->getElementsByTagName("response");
$process = $response2->item(0)->getElementsByTagName("process");
if ($process->length > 0) {
    $process = $process->item(0)->nodeValue;
} else {
    $process = "";
}
$cartNumber = $response2->item(0)->getElementsByTagName("cartNumber");
if ($cartNumber->length > 0) {
    $cartNumber = $cartNumber->item(0)->nodeValue;
} else {
    $cartNumber = "";
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('cartCheckout');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'cartNumber' => $cartNumber,
        'process' => $process
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "ERRO: " . $e;
    echo $return;
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>