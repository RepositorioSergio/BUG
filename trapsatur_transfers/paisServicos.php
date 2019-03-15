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
    $TransferstrapsturPassword = base64_decode($row_settings['value']);
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
echo $return;
echo $return;
echo "Service URL:" . $TransferstrapsturServiceURL;
echo $return;
echo "Entity: " . $TransferstrapsturEntityKey;
echo $return;
echo "Login:" . $TransferstrapsturLogin;
echo $return;
echo $return;
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

// <element name="fecha" nillable="true" type="xsd:string"/>
// <element name="idioma" nillable="true" type="xsd:string"/>
// <element name="tipoServicio" nillable="true" type="xsd:string"/>
$tipoServicio = "TF";
// “TF” se obtendrán solo servicios de traslados.
// “VT” se obtendrán solo servicios de visitas guiadas.
$params = array(
    "codigoEntidad" => $TransferstrapsturEntityKey,
    "login" => $TransferstrapsturLogin,
    "password" => $TransferstrapsturPassword,
    "idioma" => "es",
    "tipoServicio" => $tipoServicio
);
try {
    $client->__soapCall('paisesServicios', array(
        $params
    ));
} catch (\Exception $e) {
    var_dump($e);
    die();
}
// var_dump($client);
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
$node = $inputDoc->getElementsByTagName("maestroPais");
$node = $inputDoc->getElementsByTagName("multiRef");

for ($j = 0; $j < $node->length; $j ++) {
    $codigo = $node->item($j)->getElementsByTagName('codigo');
    if ($codigo->length > 0) {
        $codigo = $codigo->item(0)->nodeValue;
    } else {
        $codigo = "";
    }
    echo $return;
    echo "codigo: " . $codigo;
    echo $return;
    $nombre = $node->item($j)->getElementsByTagName('nombre');
    if ($nombre->length > 0) {
        $nombre = $nombre->item(0)->nodeValue;
    } else {
        $nombre = "";
    }
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('paisesservicos');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codigo' => $codigo,
            'nombre' => $nombre
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
    
    $maestroCiudad = $node->item($j)->getElementsByTagName('maestroCiudad');
    for ($jAux = 0; $jAux < $maestroCiudad->length; $jAux ++) {
        $codigo = $maestroCiudad->item($jAux)->getElementsByTagName('codigo');
        if ($codigo->length > 0) {
            $codigo = $codigo->item(0)->nodeValue;
        } else {
            $codigo = "";
        }
        $nombre = $maestroCiudad->item($jAux)->getElementsByTagName('nombre');
        if ($nombre->length > 0) {
            $nombre = $nombre->item(0)->nodeValue;
        } else {
            $nombre = "";
        }
        $codigoPais = $maestroCiudad->item($jAux)->getElementsByTagName('codigoPais');
        if ($codigoPais->length > 0) {
            $codigoPais = $codigoPais->item(0)->nodeValue;
        } else {
            $codigoPais = "";
        }
        $nombrePais = $maestroCiudad->item($jAux)->getElementsByTagName('nombrePais');
        if ($nombrePais->length > 0) {
            $nombrePais = $nombrePais->item(0)->nodeValue;
        } else {
            $nombrePais = "";
        }
        $salidaCircuitos = $maestroCiudad->item($jAux)->getElementsByTagName('salidaCircuitos');
        if ($salidaCircuitos->length > 0) {
            $salidaCircuitos = $salidaCircuitos->item(0)->nodeValue;
        } else {
            $salidaCircuitos = "";
        }
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('maestroCiudad');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codigo' => $codigo,
                'nombre' => $nombre,
                'codigoPais' => $codigoPais,
                'nombrePais' => $nombrePais,
                'salidaCircuitos' => $salidaCircuitos
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
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>


