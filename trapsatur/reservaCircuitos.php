
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
echo $return;
echo "trapsaturpackagesServiceURL: " . $trapsaturpackagesServiceURL;
echo $return;
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
            $params = array(
                "claveEntidad" => $trapsaturpackagesEntityKey,
                "login" => $trapsaturpackagesLogin,
                "password" => $trapsaturpackagesPassword,
                "codigoFolleto" => $codigoFolheto,
                "coodigoSubFolleto" => $codigoSubFolheto,
                "codigoProducto" => $codigo,
                "fechaReserva" => "22/03/2019",
                "regimen" => "",
                "categoria" => "",
                "idiomaGuia" => "",
                "numeroHabitacionesSingle" => 1,
                "numeroHabitacionesDoble" => 0,
                "numeroHabitacionesTriple" => 0,
                "numeroHabitacionesTripleNinyo" => 0,
                "suReferencia" => 0,
                "nombrePasajeroTitular" => "Alvaro Morato",
                "observacionesAlojamiento" => "",
                "trasladoLlegada" => "",
                "trasladoSalida" => "",
                "numeroHabitacionesTripleNinyo" => 0,
                "suReferencia" => 0,
                "habitaciones" => 1,
                "numNochesPreTour" => 1,
                "numNochesPostTour" => 1,
                "mailConfirmacion1" => "mail1@gmail.com",
                "mailConfirmacion2" => "mail2@gmail.com",
                "expediente" => "",
                "conAvion" => "N",
                "lugarSalida" => "Lisboa",
                "paisResidencia" => "Portugal",
                "seguroOpcional" => "N"
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
                $client->__soapCall('reservaCircuito', array(
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
            
            $node2 = $inputDoc->getElementsByTagName("reservaCircuito");
            $node2 = $inputDoc->getElementsByTagName("multiRef");
            if ($node2->length > 3) {
                for ($k=0; $k < $node2->length; $k++) { 
                    $numeroExpediente = $node2->item($k)->getElementsByTagName('numeroExpediente');
                    if ($numeroExpediente->length > 0) {
                        $numeroExpediente = $numeroExpediente->item(0)->nodeValue;
                    } else {
                        $numeroExpediente = "";
                    }
                    echo $return;
                    echo "numeroExpediente: " . $numeroExpediente;
                    echo $return;
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
                    $neto = $node2->item($k)->getElementsByTagName('neto');
                    if ($neto->length > 0) {
                        $neto = $neto->item(0)->nodeValue;
                    } else {
                        $neto = "";
                    }
                    $cambioDia = $node2->item($k)->getElementsByTagName('cambioDia');
                    if ($cambioDia->length > 0) {
                        $cambioDia = $cambioDia->item(0)->nodeValue;
                    } else {
                        $cambioDia = "";
                    }
                    $cambioTemporada = $node2->item($k)->getElementsByTagName('cambioTemporada');
                    if ($cambioTemporada->length > 0) {
                        $cambioTemporada = $cambioTemporada->item(0)->nodeValue;
                    } else {
                        $cambioTemporada = "";
                    }
                    $descripcionProducto = $node2->item($k)->getElementsByTagName('descripcionProducto');
                    if ($descripcionProducto->length > 0) {
                        $descripcionProducto = $descripcionProducto->item(0)->nodeValue;
                    } else {
                        $descripcionProducto = "";
                    }
                    $lugarPresentacion = $node2->item($k)->getElementsByTagName('lugarPresentacion');
                    if ($lugarPresentacion->length > 0) {
                        $lugarPresentacion = $lugarPresentacion->item(0)->nodeValue;
                    } else {
                        $lugarPresentacion = "";
                    }
                    $literalLugarSalida = $node2->item($k)->getElementsByTagName('literalLugarSalida');
                    if ($literalLugarSalida->length > 0) {
                        $literalLugarSalida = $literalLugarSalida->item(0)->nodeValue;
                    } else {
                        $literalLugarSalida = "";
                    }
                    $literalHoraPresentacion = $node2->item($k)->getElementsByTagName('literalHoraPresentacion');
                    if ($literalHoraPresentacion->length > 0) {
                        $literalHoraPresentacion = $literalHoraPresentacion->item(0)->nodeValue;
                    } else {
                        $literalHoraPresentacion = "";
                    }
                    $literalFechaSalida = $node2->item($k)->getElementsByTagName('literalFechaSalida');
                    if ($literalFechaSalida->length > 0) {
                        $literalFechaSalida = $literalFechaSalida->item(0)->nodeValue;
                    } else {
                        $literalFechaSalida = "";
                    }
                    $literalFechaHoy = $node2->item($k)->getElementsByTagName('literalFechaHoy');
                    if ($literalFechaHoy->length > 0) {
                        $literalFechaHoy = $literalFechaHoy->item(0)->nodeValue;
                    } else {
                        $literalFechaHoy = "";
                    }
                    $resultadoNochesPreTour = $node2->item($k)->getElementsByTagName('resultadoNochesPreTour');
                    if ($resultadoNochesPreTour->length > 0) {
                        $resultadoNochesPreTour = $resultadoNochesPreTour->item(0)->nodeValue;
                    } else {
                        $resultadoNochesPreTour = "";
                    }
                    $resultadoNochesPostTour = $node2->item($k)->getElementsByTagName('resultadoNochesPostTour');
                    if ($resultadoNochesPostTour->length > 0) {
                        $resultadoNochesPostTour = $resultadoNochesPostTour->item(0)->nodeValue;
                    } else {
                        $resultadoNochesPostTour = "";
                    }
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
                
                
                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('reservaCircuitos');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'numeroExpediente' => $numeroExpediente,
                            'moneda' => $moneda,
                            'precio' => $precio,
                            'neto' => $neto,
                            'cambioDia' => $cambioDia,
                            'cambioTemporada' => $cambioTemporada,
                            'descripcionProducto' => $descripcionProducto,
                            'lugarPresentacion' => $lugarPresentacion,
                            'literalLugarSalida' => $literalLugarSalida,
                            'literalHoraPresentacion' => $literalHoraPresentacion,
                            'literalFechaSalida' => $literalFechaSalida,
                            'literalFechaHoy' => $literalFechaHoy,
                            'resultadoNochesPreTour' => $resultadoNochesPreTour,
                            'resultadoNochesPostTour' => $resultadoNochesPostTour,
                            'precioNochePre' => $precioNochePre,
                            'literalNochePre' => $literalNochePre,
                            'precioNochePost' => $precioNochePost,
                            'literalNochePost' => $literalNochePost
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


