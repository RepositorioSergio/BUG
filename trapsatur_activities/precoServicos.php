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
$sql = "select value from settings where name='enableactivitiestrapsatur' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_trapsaturpackages = $affiliate_id;
} else {
    $affiliate_id_trapsaturpackages = 0;
}
$sql = "select value from settings where name='activitiestrapsaturLogin' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $activitiestrapsaturLogin = $row_settings['value'];
}
$sql = "select value from settings where name='activitiestrapsaturPassword' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $activitiestrapsaturPassword = $row_settings['value'];
}
echo $return;
echo "activitiestrapsaturPassword: " . $activitiestrapsaturPassword;
echo $return;
$sql = "select value from settings where name='activitiestrapsaturServiceURL' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $activitiestrapsaturServiceURL = $row['value'];
}
$sql = "select value from settings where name='activitiestrapsaturEntityKey' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $activitiestrapsaturEntityKey = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

try {
    $client = new SoapClient($activitiestrapsaturServiceURL, array(
        'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        "trace" => 1,
        "exceptions" => true,
        'soap_version' => SOAP_1_1,
        "connection_timeout" => 100
    ));
} catch (\Exception $e) {
    var_dump($e);
    die();
}
    var_dump($client);

$params = array(
    "claveEntidad" => $activitiestrapsaturEntityKey,
    "login" => $activitiestrapsaturLogin,
    "password" => $activitiestrapsaturPassword,
    "idioma" => "es",
    "ciudad" => "es",//alterar
    "servicio" => "es",//alterar
    "fecha" => "2019-06-30",//alterar
    "plazas" => 2//alterar
);
try {
    $client->__soapCall('precioServicio', array(
        $params
    ));
} catch (\Exception $e) {
        var_dump($e);
    die();
}
    //var_dump($client);
$xmlrequest = $client->__getLastRequest();
$xmlresult = $client->__getLastResponse();

//echo $xmlresult;
$config = new \Zend\Config\Config(include '../config/autoload/global.trapsatur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult);
$node = $inputDoc->getElementsByTagName("precioServicio");
$node = $inputDoc->getElementsByTagName("multiRef");

for ($j=0; $j < $node->length; $j++) { 
    $precioFIT = $node->item($j)->getElementsByTagName('precioFIT');
    if ($precioFIT->length > 0) {
        $precioFIT = $precioFIT->item(0)->nodeValue;
    } else {
        $precioFIT = "";
    }
    echo $return;
    echo "precioFIT: " . $precioFIT;
    echo $return;
    $precioADI = $node->item($j)->getElementsByTagName('precioADI');
    if ($precioADI->length > 0) {
        $precioADI = $precioADI->item(0)->nodeValue;
    } else {
        $precioADI = "";
    }
    $moneda = $node->item($j)->getElementsByTagName('moneda');
    if ($moneda->length > 0) {
        $moneda = $moneda->item(0)->nodeValue;
    } else {
        $moneda = "";
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('precos_servicos');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'precioFIT' => $precioFIT,
            'precioADI' => $precioADI,
            'moneda' => $moneda
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (Exception $e) {
        echo $return;
        echo "Exception: " . $e;
        echo $return;
    }

}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>


