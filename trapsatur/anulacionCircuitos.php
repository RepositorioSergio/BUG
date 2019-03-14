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
$sql = "select value from settings where name='enabletrapsaturpackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_trapsaturpackages = $affiliate_id;
} else {
    $affiliate_id_trapsaturpackages = 0;
}
$sql = "select value from settings where name='trapsaturpackagesLogin' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $trapsaturpackagesLogin = $row_settings['value'];
}
$sql = "select value from settings where name='trapsaturpackagesPassword' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $trapsaturpackagesPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='trapsaturpackagesServiceURL' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $trapsaturpackagesServiceURL = $row['value'];
}
$sql = "select value from settings where name='trapsaturpackagesEntityKey' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $trapsaturpackagesEntityKey = $row_settings['value'];
}


$config = new \Zend\Config\Config(include '../config/autoload/global.trapsatur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT numeroExpediente FROM reservaCircuitos";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
/* $result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $numeroExpediente = $row->numeroExpediente;
        echo $return;
        echo "numeroExpediente: " . $numeroExpediente;
        echo $return;

        if ($numeroExpediente != "") { */
            $numeroExpediente = "";
            $params = array(
                "claveEntidad" => $trapsaturpackagesEntityKey,
                "login" => $trapsaturpackagesLogin,
                "password" => $trapsaturpackagesPassword,
                "tipoAnulacion" => "E",
                "localizador" => $numeroExpediente
            );
            try {
                $client = new SoapClient($trapsaturpackagesServiceURL, array(
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
            // var_dump($client);
            try {
                $client->__soapCall('anulacion', array(
                    $params
                ));
            } catch (\Exception $e) {
                var_dump($e);
                die();
            }
            
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
            
            $node2 = $inputDoc->getElementsByTagName("anulacion");
            $node2 = $inputDoc->getElementsByTagName("multiRef");
            if ($node2->length > 3) {
                for ($k=0; $k < $node2->length; $k++) { 
                    $codigo = $node2->item($k)->getElementsByTagName('codigo');
                    if ($codigo->length > 0) {
                        $codigo = $codigo->item(0)->nodeValue;
                    } else {
                        $codigo = "";
                    }
                    echo $return;
                    echo "codigo: " . $codigo;
                    echo $return;
                    $descripcion = $node2->item($k)->getElementsByTagName('descripcion');
                    if ($descripcion->length > 0) {
                        $descripcion = $descripcion->item(0)->nodeValue;
                    } else {
                        $descripcion = "";
                    }
                    echo $return;
                    echo "descripcion: " . $descripcion;
                    echo $return;
                    $observaciones = $node2->item($k)->getElementsByTagName('observaciones');
                    if ($observaciones->length > 0) {
                        $observaciones = $observaciones->item(0)->nodeValue;
                    } else {
                        $observaciones = "";
                    }
                    echo $return;
                    echo "observaciones: " . $observaciones;
                    echo $return;
                    $fechaTope = $node2->item($k)->getElementsByTagName('fechaTope');
                    if ($fechaTope->length > 0) {
                        $fechaTope = $fechaTope->item(0)->nodeValue;
                    } else {
                        $fechaTope = "";
                    }
                    $locAnulacion = $node2->item($k)->getElementsByTagName('locAnulacion');
                    if ($locAnulacion->length > 0) {
                        $locAnulacion = $locAnulacion->item(0)->nodeValue;
                    } else {
                        $locAnulacion = "";
                    }
                                   
                
                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('anulacionCircuitos');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'codigo' => $codigo,
                            'descripcion' => $descripcion,
                            'observaciones' => $observaciones,
                            'fechaTope' => $fechaTope,
                            'locAnulacion' => $locAnulacion
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
/*         }
    }
} */
//die();

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>



