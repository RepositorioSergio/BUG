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

$sql = "SELECT codigo,codigoFolheto,codigoSubFolheto FROM productos";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $codigo = $row->codigo;
        $codigoFolheto = $row->codigoFolheto;
        $codigoSubFolheto = $row->codigoSubFolheto;
        echo $return;
        echo "codigo Producto: " . $codigo;
        echo $return;
        echo $return;
        echo "codigo Folheto: " . $codigoFolheto;
        echo $return;
        echo $return;
        echo "codigo Sub: " . $codigoSubFolheto;
        echo $return;

        if ($codigo != "" and $codigoFolheto != "" and $codigoSubFolheto != "") {
            $params = array(
                "claveEntidad" => $trapsaturpackagesEntityKey,
                "login" => $trapsaturpackagesLogin,
                "password" => $trapsaturpackagesPassword,
                "codigoFolleto" => $codigoFolheto,
                "codigoSubFolleto" => $codigoSubFolheto,
                "codigoProducto" => $codigo
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
                $client->__soapCall('disponibilidadCircuito', array(
                    $params
                ));
            } catch (\Exception $e) {
                var_dump($e);
                die();
            }
            
            $xmlrequest = $client->__getLastRequest();
            $xmlresult = $client->__getLastResponse();
            echo $return;
            echo "RESULTADO2: ";
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
            
            $node2 = $inputDoc->getElementsByTagName("fechasDisponibilidad");
            $node2 = $inputDoc->getElementsByTagName("multiRef");
            if ($node2->length > 2) {
                for ($k=0; $k < $node2->length; $k++) { 
                    $fechaString = $node2->item($k)->getElementsByTagName('fechaString');
                    if ($fechaString->length > 0) {
                        $fechaString = $fechaString->item(0)->nodeValue;
                    } else {
                        $fechaString = "";
                    }
                    echo $return;
                    echo "fechaString: " . $fechaString;
                    echo $return;
                    $codigoTramo = $node2->item($k)->getElementsByTagName('codigoTramo');
                    if ($codigoTramo->length > 0) {
                        $codigoTramo = $codigoTramo->item(0)->nodeValue;
                    } else {
                        $codigoTramo = "";
                    }
                    $descripcionTramo = $node2->item($k)->getElementsByTagName('descripcionTramo');
                    if ($descripcionTramo->length > 0) {
                        $descripcionTramo = $descripcionTramo->item(0)->nodeValue;
                    } else {
                        $descripcionTramo = "";
                    }
                    echo $return;
                    echo "descripcionTramo: " . $descripcionTramo;
                    echo $return;
                    $numeroPlazas = $node2->item($k)->getElementsByTagName('numeroPlazas');
                    if ($numeroPlazas->length > 0) {
                        $numeroPlazas = $numeroPlazas->item(0)->nodeValue;
                    } else {
                        $numeroPlazas = "";
                    }
                    $fechaCerrada = $node2->item($k)->getElementsByTagName('fechaCerrada');
                    if ($fechaCerrada->length > 0) {
                        $fechaCerrada = $fechaCerrada->item(0)->nodeValue;
                    } else {
                        $fechaCerrada = "";
                    }
                    $motivoCierre = $node2->item($k)->getElementsByTagName('motivoCierre');
                    if ($motivoCierre->length > 0) {
                        $motivoCierre = $motivoCierre->item(0)->nodeValue;
                    } else {
                        $motivoCierre = "";
                    }
                    echo $return;
                    echo "motivoCierre: " . $motivoCierre;
                    echo $return;
                
                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('disponibilidadeCircuitos');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'fechaString' => $fechaString,
                            'codigoTramo' => $codigoTramo,
                            'descripcionTramo' => $descripcionTramo,
                            'numeroPlazas' => $numeroPlazas,
                            'fechaCerrada' => $fechaCerrada,
                            'motivoCierre' => $motivoCierre
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
        }
    }
}

//die();

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>



