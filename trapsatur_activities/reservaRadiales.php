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
    "folleto" => "RA7",
    "codigoSubFolleto" => "1",
    "codigoProducto" => "905",
    "fechaReserva" => "30/06/2019",
    "numeroAdultos" => 2,
    "numeroNiyos " => 0,
    "numeroAdultos65 " => 0,
    "pasajero" => "Alvaro Morato",
    "suRefernecia" => "localizador agencia"//alterar
);
try {
    $client->__soapCall('reservaMyA', array(
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
$node = $inputDoc->getElementsByTagName("reservaMyA");
$node = $inputDoc->getElementsByTagName("multiRef");

for ($j=0; $j < $node->length; $j++) { 
    $numeroExpediente = $node->item($j)->getElementsByTagName('numeroExpediente');
    if ($numeroExpediente->length > 0) {
        $numeroExpediente = $numeroExpediente->item(0)->nodeValue;
    } else {
        $numeroExpediente = "";
    }
    echo $return;
    echo "numeroExpediente: " . $numeroExpediente;
    echo $return;
    $moneda = $node->item($j)->getElementsByTagName('moneda');
    if ($moneda->length > 0) {
        $moneda = $moneda->item(0)->nodeValue;
    } else {
        $moneda = "";
    }
    echo $return;
    echo "moneda: " . $moneda;
    echo $return;
    $precio = $node->item($j)->getElementsByTagName('precio');
    if ($precio->length > 0) {
        $precio = $precio->item(0)->nodeValue;
    } else {
        $precio = "";
    }
    $cambioDia = $node->item($j)->getElementsByTagName('cambioDia');
    if ($cambioDia->length > 0) {
        $cambioDia = $cambioDia->item(0)->nodeValue;
    } else {
        $cambioDia = "";
    }
    $neto = $node->item($j)->getElementsByTagName('neto');
    if ($neto->length > 0) {
        $neto = $neto->item(0)->nodeValue;
    } else {
        $neto = "";
    }
    $lugarPresentacion = $node->item($j)->getElementsByTagName('lugarPresentacion');
    if ($lugarPresentacion->length > 0) {
        $lugarPresentacion = $lugarPresentacion->item(0)->nodeValue;
    } else {
        $lugarPresentacion = "";
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('reservaRadiales');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'numeroExpediente' => $numeroExpediente,
            'moneda' => $moneda,
            'precio' => $precio,
            'cambioDia' => $cambioDia,
            'neto' => $neto,
            'lugarPresentacion' => $lugarPresentacion
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


