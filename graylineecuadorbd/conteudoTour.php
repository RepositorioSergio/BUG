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
echo "COMECOU CONTEUDO TOUR<br/>";
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

$url = "http://demo.gl-tours.com/web_service_Cart.php?userCart=user189142933&Lang=eng&User=TEST&Pass=1234";

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
die();
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

$Cart = $response2->item(0)->getElementsByTagName("Cart");
if ($Cart->length > 0) {
    for ($i=0; $i < $Cart->length; $i++) { 
        $id = $Cart->item($i)->getElementsByTagName("id");
        if ($id->length > 0) {
            $id = $id->item(0)->nodeValue;
        } else {
            $id = "";
        }
        $Date = $Cart->item($i)->getElementsByTagName("Date");
        if ($Date->length > 0) {
            $Date = $Date->item(0)->nodeValue;
        } else {
            $Date = "";
        }
        $Qty = $Cart->item($i)->getElementsByTagName("Qty");
        if ($Qty->length > 0) {
            $Qty = $Qty->item(0)->nodeValue;
        } else {
            $Qty = "";
        }
        $Price = $Cart->item($i)->getElementsByTagName("Price");
        if ($Price->length > 0) {
            $Price = $Price->item(0)->nodeValue;
        } else {
            $Price = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('conteudoCarroCompras');
            $insert->values(array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Date' => $Date,
                'Qty' => $Qty,
                'Price' => $Price,
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
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>