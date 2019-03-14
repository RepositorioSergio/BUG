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
$sql = "select value from settings where name='enabletrapsaturTransfers' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_trapsaturpackages = $affiliate_id;
} else {
    $affiliate_id_trapsaturpackages = 0;
}
$sql = "select value from settings where name='TransferstrapsturLogin' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TransferstrapsturLogin = $row_settings['value'];
}
echo $return;
echo "TransferstrapsturLogin: " . $TransferstrapsturLogin;
echo $return;
$sql = "select value from settings where name='TransferstrapsturPassword' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TransferstrapsturPassword = $row_settings['value'];
}
echo $return;
echo "TransferstrapsturPassword: " . $TransferstrapsturPassword;
echo $return;
$sql = "select value from settings where name='TransferstrapsturServiceURL' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $TransferstrapsturServiceURL = $row['value'];
}
$sql = "select value from settings where name='TransferstrapsturEntityKey' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TransferstrapsturEntityKey = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

try {
    $client = new SoapClient($TransferstrapsturServiceURL, array(
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
    "codigoEntidad" => $TransferstrapsturEntityKey,
    "login" => $TransferstrapsturLogin,
    "password" => $TransferstrapsturPassword,
    "idioma" => "es",
    "ciudad" => "es",//alterar p baixo
    "servicio" => "es",
    "fecha" => "2019-06-30",
    "plazas" => "2",
    "nombrePasajero" => "es",
    "codigoterminalTte" => "es",//alterar
    "tipotransfer" => "IN",//alterar
    "numeroVuelo" => "IB5566",//alterar
    "horaVuelo" => "11:00"
);
try {
    $client->__soapCall('reservaServicio', array(
        $params
    ));
} catch (\Exception $e) {
        var_dump($e);
    die();
}
    //var_dump($client);
$xmlrequest = $client->__getLastRequest();
$xmlresult = $client->__getLastResponse();
echo $xmlresult;

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
$node = $inputDoc->getElementsByTagName("reservaServicio");
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
    $localizadorServicio = $node->item($j)->getElementsByTagName('localizadorServicio');
    if ($localizadorServicio->length > 0) {
        $localizadorServicio = $localizadorServicio->item(0)->nodeValue;
    } else {
        $localizadorServicio = "";
    }
    $precio = $node->item($j)->getElementsByTagName('precio');
    if ($precio->length > 0) {
        $precio = $precio->item(0)->nodeValue;
    } else {
        $precio = "";
    }
    $moneda = $node->item($j)->getElementsByTagName('moneda');
    if ($moneda->length > 0) {
        $moneda = $moneda->item(0)->nodeValue;
    } else {
        $moneda = "";
    }
    $fechaRecogidaHotel = $node->item($j)->getElementsByTagName('fechaRecogidaHotel');
    if ($fechaRecogidaHotel->length > 0) {
        $fechaRecogidaHotel = $fechaRecogidaHotel->item(0)->nodeValue;
    } else {
        $fechaRecogidaHotel = "";
    }
    $horaRecogidaHotel = $node->item($j)->getElementsByTagName('horaRecogidaHotel');
    if ($horaRecogidaHotel->length > 0) {
        $horaRecogidaHotel = $horaRecogidaHotel->item(0)->nodeValue;
    } else {
        $horaRecogidaHotel = "";
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('reserva_servicos');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'numeroExpediente' => $numeroExpediente,
            'localizadorServicio' => $localizadorServicio,
            'precio' => $precio,
            'moneda' => $moneda,
            'fechaRecogidaHotel' => $fechaRecogidaHotel,
            'horaRecogidaHotel' => $horaRecogidaHotel
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
