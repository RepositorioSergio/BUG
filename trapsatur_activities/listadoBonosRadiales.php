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
echo $return;
echo "activitiestrapsaturLogin: " . $activitiestrapsaturLogin;
echo $return;
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
echo $return;
echo "activitiestrapsaturServiceURL: " . $activitiestrapsaturServiceURL;
echo $return;
$sql = "select value from settings where name='activitiestrapsaturEntityKey' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $activitiestrapsaturEntityKey = $row_settings['value'];
}
echo $return;
echo "activitiestrapsaturEntityKey: " . $activitiestrapsaturEntityKey;
echo $return;
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
    //var_dump($client);
echo $return;
echo "ANTES PARAMS ";
echo $return;

$params = array(
    "claveEntidad" => $activitiestrapsaturEntityKey,
    "login" => $activitiestrapsaturLogin,
    "password" => $activitiestrapsaturPassword,
    "idioma" => "es",
    "fechaDesde" => "2019-06-01",
    "fechaHasta" => "2019-12-30",
    "tipoPeriodo" => "A"
);
try {
    $client->__soapCall('listadoBonos', array(
        $params
    ));
} catch (\Exception $e) {
        var_dump($e);
    die();
}
    //var_dump($client);
$xmlrequest = $client->__getLastRequest();
$xmlresult = $client->__getLastResponse();
echo $return;
echo "RESULTADO: ";
echo $return;
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
$node = $inputDoc->getElementsByTagName("bonoRadales");
$node = $inputDoc->getElementsByTagName("multiRef");

for ($j=0; $j < $node->length; $j++) { 
    $localizador = $node->item($j)->getElementsByTagName('localizador');
    if ($localizador->length > 0) {
        $localizador = $localizador->item(0)->nodeValue;
    } else {
        $localizador = "";
    }
    echo $return;
    echo "localizador: " . $localizador;
    echo $return;
    $sureferencia = $node->item($j)->getElementsByTagName('sureferencia');
    if ($sureferencia->length > 0) {
        $sureferencia = $sureferencia->item(0)->nodeValue;
    } else {
        $sureferencia = "";
    }
    echo $return;
    echo "sureferencia: " . $sureferencia;
    echo $return;
    $codigoRadial = $node->item($j)->getElementsByTagName('codigoRadial');
    if ($codigoRadial->length > 0) {
        $codigoRadial = $codigoRadial->item(0)->nodeValue;
    } else {
        $codigoRadial = "";
    }
    $nombreRadial = $node->item($j)->getElementsByTagName('nombreRadial');
    if ($nombreRadial->length > 0) {
        $nombreRadial = $nombreRadial->item(0)->nodeValue;
    } else {
        $nombreRadial = "";
    }
    $fechaRadial = $node->item($j)->getElementsByTagName('fechaRadial');
    if ($fechaRadial->length > 0) {
        $fechaRadial = $fechaRadial->item(0)->nodeValue;
    } else {
        $fechaRadial = "";
    }
    $fechaAlta = $node->item($j)->getElementsByTagName('fechaAlta');
    if ($fechaAlta->length > 0) {
        $fechaAlta = $fechaAlta->item(0)->nodeValue;
    } else {
        $fechaAlta = "";
    }
    $pasajero = $node->item($j)->getElementsByTagName('pasajero');
    if ($pasajero->length > 0) {
        $pasajero = $pasajero->item(0)->nodeValue;
    } else {
        $pasajero = "";
    }
    $plazasAdulto = $node->item($j)->getElementsByTagName('plazasAdulto');
    if ($plazasAdulto->length > 0) {
        $plazasAdulto = $plazasAdulto->item(0)->nodeValue;
    } else {
        $plazasAdulto = "";
    }
    $plazasNinyo = $node->item($j)->getElementsByTagName('plazasNinyo');
    if ($plazasNinyo->length > 0) {
        $plazasNinyo = $plazasNinyo->item(0)->nodeValue;
    } else {
        $plazasNinyo = "";
    }
    $plazasAdulto65 = $node->item($j)->getElementsByTagName('plazasAdulto65');
    if ($plazasAdulto65->length > 0) {
        $plazasAdulto65 = $plazasAdulto65->item(0)->nodeValue;
    } else {
        $plazasAdulto65 = "";
    }
    $fechaBaja = $node->item($j)->getElementsByTagName('fechaBaja');
    if ($fechaBaja->length > 0) {
        $fechaBaja = $fechaBaja->item(0)->nodeValue;
    } else {
        $fechaBaja = "";
    }
    $localizadorAnulacion = $node->item($j)->getElementsByTagName('localizadorAnulacion');
    if ($localizadorAnulacion->length > 0) {
        $localizadorAnulacion = $localizadorAnulacion->item(0)->nodeValue;
    } else {
        $localizadorAnulacion = "";
    }
    $contactoAgencia = $node->item($j)->getElementsByTagName('contactoAgencia');
    if ($contactoAgencia->length > 0) {
        $contactoAgencia = $contactoAgencia->item(0)->nodeValue;
    } else {
        $contactoAgencia = "";
    }
    $observaciones = $node->item($j)->getElementsByTagName('observaciones');
    if ($observaciones->length > 0) {
        $observaciones = $observaciones->item(0)->nodeValue;
    } else {
        $observaciones = "";
    }
    $observacionesRec = $node->item($j)->getElementsByTagName('observacionesRec');
    if ($observacionesRec->length > 0) {
        $observacionesRec = $observacionesRec->item(0)->nodeValue;
    } else {
        $observacionesRec = "";
    }
    $importeBruto = $node->item($j)->getElementsByTagName('importeBruto');
    if ($importeBruto->length > 0) {
        $importeBruto = $importeBruto->item(0)->nodeValue;
    } else {
        $importeBruto = "";
    }
    $importeNeto = $node->item($j)->getElementsByTagName('importeNeto');
    if ($importeNeto->length > 0) {
        $importeNeto = $importeNeto->item(0)->nodeValue;
    } else {
        $importeNeto = "";
    }
    $estado = $node->item($j)->getElementsByTagName('estado');
    if ($estado->length > 0) {
        $estado = $estado->item(0)->nodeValue;
    } else {
        $estado = "";
    }
    $nombreBonoPDF = $node->item($j)->getElementsByTagName('nombreBonoPDF');
    if ($nombreBonoPDF->length > 0) {
        $nombreBonoPDF = $nombreBonoPDF->item(0)->nodeValue;
    } else {
        $nombreBonoPDF = "";
    }
    $numeroConserje = $node->item($j)->getElementsByTagName('numeroConserje');
    if ($numeroConserje->length > 0) {
        $numeroConserje = $numeroConserje->item(0)->nodeValue;
    } else {
        $numeroConserje = "";
    }
    $nombreConserje = $node->item($j)->getElementsByTagName('nombreConserje');
    if ($nombreConserje->length > 0) {
        $nombreConserje = $nombreConserje->item(0)->nodeValue;
    } else {
        $nombreConserje = "";
    }
    $user = $node->item($j)->getElementsByTagName('user');
    if ($user->length > 0) {
        $user = $user->item(0)->nodeValue;
    } else {
        $user = "";
    }
    $codigoAgencia = $node->item($j)->getElementsByTagName('codigoAgencia');
    if ($codigoAgencia->length > 0) {
        $codigoAgencia = $codigoAgencia->item(0)->nodeValue;
    } else {
        $codigoAgencia = "";
    }
    $nombreAgencia = $node->item($j)->getElementsByTagName('nombreAgencia');
    if ($nombreAgencia->length > 0) {
        $nombreAgencia = $nombreAgencia->item(0)->nodeValue;
    } else {
        $nombreAgencia = "";
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('bonosRadiales');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'localizador' => $localizador,
            'sureferencia' => $sureferencia,
            'codigoRadial' => $codigoRadial,
            'nombreRadial' => $nombreRadial,
            'fechaRadial' => $fechaRadial,
            'fechaAlta' => $fechaAlta,
            'pasajero' => $pasajero,
            'plazasAdulto' => $plazasAdulto,
            'plazasNinyo' => $plazasNinyo,
            'plazasAdulto65' => $plazasAdulto65,
            'localizadorAnulacion' => $localizadorAnulacion,
            'contactoAgencia' => $contactoAgencia,
            'observaciones' => $observaciones,
            'observacionesRec' => $observacionesRec,
            'importeBruto' => $importeBruto,
            'importeNeto' => $importeNeto,
            'estado' => $estado,
            'nombreBonoPDF' => $nombreBonoPDF,
            'numeroConserje' => $numeroConserje,
            'nombreConserje' => $nombreConserje,
            'user' => $user,
            'codigoAgencia' => $codigoAgencia,
            'nombreAgencia' => $nombreAgencia
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

