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
        echo "codigo Folheto: " . $codigo;
        echo $return;

        if ($codigo != "" and $codigoFolheto != "" and $codigoSubFolheto != "") {

            $pasajeros = array(
                "name" => "Alvaro",
                "surName" => "Morato",
                "passport" => "G1234567",
                "passportEmitido" => "2018-11-20",
                "passportCaduca" => "2020-10-30",
                "discount" => ""
            );

            $habitaciones = array(
                "tipoHabitacion" => "D",
                "pasajeros" => $pasajeros
            );

            $params = array(
                "claveEntidad" => $trapsaturpackagesEntityKey,
                "login" => $trapsaturpackagesLogin,
                "password" => $trapsaturpackagesPassword,
                "idioma" => "es",
                "codigoFolleto" => $codigoFolheto,
                "coodigoSubFolleto" => $codigoSubFolheto,
                "codigoProducto" => $codigo,
                "fechaReserva" => "30/03/2019",
                "regimen" => "",
                "categoria" => "",
                "numeroHabitacionesSingle" => 1,
                "numeroHabitacionesDoble" => 0,
                "numeroHabitacionesTriple" => 0,
                "numeroHabitacionesTripleNinyo" => 0,
                "habitaciones" => $habitaciones
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
                $client->__soapCall('precioCircuito', array(
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
            
            $node2 = $inputDoc->getElementsByTagName("precioCircuito");
            $node2 = $inputDoc->getElementsByTagName("multiRef");
            if ($node2->length > 3) {
                for ($k=0; $k < $node2->length; $k++) { 
                    $moneda = $node2->item($k)->getElementsByTagName('moneda');
                    if ($moneda->length > 0) {
                        $moneda = $moneda->item(0)->nodeValue;
                    } else {
                        $moneda = "";
                    }
                    echo $return;
                    echo "moneda: " . $moneda;
                    echo $return;
                    $precio = $node2->item($k)->getElementsByTagName('precio');
                    if ($precio->length > 0) {
                        $precio = $precio->item(0)->nodeValue;
                    } else {
                        $precio = "";
                    }
                    echo $return;
                    echo "precio: " . $precio;
                    echo $return;
                    $precioNochePre = $node2->item($k)->getElementsByTagName('precioNochePre');
                    if ($precioNochePre->length > 0) {
                        $precioNochePre = $precioNochePre->item(0)->nodeValue;
                    } else {
                        $precioNochePre = "";
                    }
                    echo $return;
                    echo "precioNochePre: " . $precioNochePre;
                    echo $return;
                    $literalNochePre = $node2->item($k)->getElementsByTagName('literalNochePre');
                    if ($literalNochePre->length > 0) {
                        $literalNochePre = $literalNochePre->item(0)->nodeValue;
                    } else {
                        $literalNochePre = "";
                    }
                    echo $return;
                    echo "literalNochePre: " . $literalNochePre;
                    echo $return;
                    $precioNochePost = $node2->item($k)->getElementsByTagName('precioNochePost');
                    if ($precioNochePost->length > 0) {
                        $precioNochePost = $precioNochePost->item(0)->nodeValue;
                    } else {
                        $precioNochePost = "";
                    }
                    echo $return;
                    echo "precioNochePost: " . $precioNochePost;
                    echo $return;
                    $literalNochePost = $node2->item($k)->getElementsByTagName('literalNochePost');
                    if ($literalNochePost->length > 0) {
                        $literalNochePost = $literalNochePost->item(0)->nodeValue;
                    } else {
                        $literalNochePost = "";
                    }
                    echo $return;
                    echo "literalNochePost: " . $literalNochePost;
                    echo $return;
                    $porcentajeComision = $node2->item($k)->getElementsByTagName('porcentajeComision');
                    if ($porcentajeComision->length > 0) {
                        $porcentajeComision = $porcentajeComision->item(0)->nodeValue;
                    } else {
                        $porcentajeComision = "";
                    }
                    echo $return;
                    echo "porcentajeComision: " . $porcentajeComision;
                    echo $return;
                
                
                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('precoCircuitos');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'moneda' => $moneda,
                            'precio' => $precio,
                            'precioNochePre' => $precioNochePre,
                            'literalNochePre' => $literalNochePre,
                            'precioNochePost' => $precioNochePost,
                            'literalNochePost' => $literalNochePost,
                            'porcentajeComision' => $porcentajeComision
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

                    $aclararCoste = $node2->item($k)->getElementsByTagName('aclararCoste');
                    for ($kAux=0; $kAux < $aclararCoste->length; $kAux++) { 
                        $descripcion = $aclararCoste->item($kAux)->getElementsByTagName('descripcion');
                        if ($descripcion->length > 0) {
                            $descripcion = $descripcion->item(0)->nodeValue;
                        } else {
                            $descripcion = "";
                        }
                        echo $return;
                        echo "descripcion: " . $descripcion;
                        echo $return;
                        $pax = $aclararCoste->item($kAux)->getElementsByTagName('pax');
                        if ($pax->length > 0) {
                            $pax = $pax->item(0)->nodeValue;
                        } else {
                            $pax = "";
                        }
                        echo $return;
                        echo "pax: " . $pax;
                        echo $return;
                        $precio = $aclararCoste->item($kAux)->getElementsByTagName('precio');
                        if ($precio->length > 0) {
                            $precio = $precio->item(0)->nodeValue;
                        } else {
                            $precio = "";
                        }
                        echo $return;
                        echo "precio: " . $precio;
                        echo $return;
                        $tipoPrecio = $aclararCoste->item($kAux)->getElementsByTagName('tipoPrecio');
                        if ($tipoPrecio->length > 0) {
                            $tipoPrecio = $tipoPrecio->item(0)->nodeValue;
                        } else {
                            $tipoPrecio = "";
                        }
                        echo $return;
                        echo "tipoPrecio: " . $tipoPrecio;
                        echo $return;
                        $importe = $aclararCoste->item($kAux)->getElementsByTagName('importe');
                        if ($importe->length > 0) {
                            $importe = $importe->item(0)->nodeValue;
                        } else {
                            $importe = "";
                        }
                        echo $return;
                        echo "importe: " . $importe;
                        echo $return;

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('precoCircuitos_aclararCoste');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'descripcion' => $descripcion,
                                'pax' => $pax,
                                'precio' => $precio,
                                'tipoPrecio' => $tipoPrecio,
                                'importe' => $importe
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
}
//die();

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>


