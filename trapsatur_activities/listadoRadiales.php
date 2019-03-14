
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
    "conInfo" => "S"
);
try {
    $client->__soapCall('listadoMyA', array(
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
$node = $inputDoc->getElementsByTagName("radiales");
$node = $inputDoc->getElementsByTagName("multiRef");

for ($j=0; $j < $node->length; $j++) { 
    $cod_folleto = $node->item($j)->getElementsByTagName('cod_folleto');
    if ($cod_folleto->length > 0) {
        $cod_folleto = $cod_folleto->item(0)->nodeValue;
    } else {
        $cod_folleto = "";
    }
    echo $return;
    echo "cod_folleto: " . $cod_folleto;
    echo $return;
    $des_folleto = $node->item($j)->getElementsByTagName('des_folleto');
    if ($des_folleto->length > 0) {
        $des_folleto = $des_folleto->item(0)->nodeValue;
    } else {
        $des_folleto = "";
    }
    echo $return;
    echo "des_folleto: " . $des_folleto;
    echo $return;
    $cod_subfolleto = $node->item($j)->getElementsByTagName('cod_subfolleto');
    if ($cod_subfolleto->length > 0) {
        $cod_subfolleto = $cod_subfolleto->item(0)->nodeValue;
    } else {
        $cod_subfolleto = "";
    }
    echo $return;
    echo "cod_subfolleto: " . $cod_subfolleto;
    echo $return;
    $des_subfolleto = $node->item($j)->getElementsByTagName('des_subfolleto');
    if ($des_subfolleto->length > 0) {
        $des_subfolleto = $des_subfolleto->item(0)->nodeValue;
    } else {
        $des_subfolleto = "";
    }
    $cod_producto = $node->item($j)->getElementsByTagName('cod_producto');
    if ($cod_producto->length > 0) {
        $cod_producto = $cod_producto->item(0)->nodeValue;
    } else {
        $cod_producto = "";
    }
    $des_producto = $node->item($j)->getElementsByTagName('des_producto');
    if ($des_producto->length > 0) {
        $des_producto = $des_producto->item(0)->nodeValue;
    } else {
        $des_producto = "";
    }
    $des_general = $node->item($j)->getElementsByTagName('des_general');
    if ($des_general->length > 0) {
        $des_general = $des_general->item(0)->nodeValue;
    } else {
        $des_general = "";
    }
    echo $return;
    echo "des_general: " . $des_general;
    echo $return;
    $costeAdulto = $node->item($j)->getElementsByTagName('costeAdulto');
    if ($costeAdulto->length > 0) {
        $costeAdulto = $costeAdulto->item(0)->nodeValue;
    } else {
        $costeAdulto = "";
    }
    $costeNiyo = $node->item($j)->getElementsByTagName('costeNiyo');
    if ($costeNiyo->length > 0) {
        $costeNiyo = $costeNiyo->item(0)->nodeValue;
    } else {
        $costeNiyo = "";
    }
    echo $return;
    echo "costeNiyo: " . $costeNiyo;
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
    $fechas = $node->item($j)->getElementsByTagName('fechas');
    if ($fechas->length > 0) {
        $fechas = $fechas->item(0)->nodeValue;
    } else {
        $fechas = "";
    }
    echo $return;
    echo "fechas: " . $fechas;
    echo $return;
    $infSalida = $node->item($j)->getElementsByTagName('infSalida');
    if ($infSalida->length > 0) {
        $infSalida = $infSalida->item(0)->nodeValue;
    } else {
        $infSalida = "";
    }
    $urlImagen = $node->item($j)->getElementsByTagName('urlImagen');
    if ($urlImagen->length > 0) {
        $urlImagen = $urlImagen->item(0)->nodeValue;
    } else {
        $urlImagen = "";
    }
    echo $return;
    echo "urlImagen: " . $urlImagen;
    echo $return;
    $infoMyA = $node->item($j)->getElementsByTagName('infoMyA');
    if ($infoMyA->length > 0) {
        $infoMyA = $infoMyA->item(0)->nodeValue;
    } else {
        $infoMyA = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('radiales');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'cod_folleto' => $cod_folleto,
            'des_folleto' => $des_folleto,
            'cod_subfolleto' => $cod_subfolleto,
            'des_subfolleto' => $des_subfolleto,
            'cod_producto' => $cod_producto,
            'des_producto' => $des_producto,
            'des_general' => $des_general,
            'costeAdulto' => $costeAdulto,
            'costeNiyo' => $costeNiyo,
            'moneda' => $moneda,
            'fechas' => $fechas,
            'infSalida' => $infSalida,
            'urlImagen' => $urlImagen,
            'infoMyA' => $infoMyA
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


    $hotelesRecogida = $node->item($j)->getElementsByTagName('hotelesRecogida');
    if ($hotelesRecogida->length > 0) {
        for ($jAux=0; $jAux < $hotelesRecogida->length; $jAux++) { 
            $grupo = $hotelesRecogida->item($jAux)->getElementsByTagName('grupo');
            if ($grupo->length > 0) {
                $grupo = $grupo->item(0)->nodeValue;
            } else {
                $grupo = "";
            }
            echo $return;
            echo "grupo: " . $grupo;
            echo $return;
            $progresivo = $hotelesRecogida->item($jAux)->getElementsByTagName('progresivo');
            if ($progresivo->length > 0) {
                $progresivo = $progresivo->item(0)->nodeValue;
            } else {
                $progresivo = "";
            }
            $sucursal = $hotelesRecogida->item($jAux)->getElementsByTagName('sucursal');
            if ($sucursal->length > 0) {
                $sucursal = $sucursal->item(0)->nodeValue;
            } else {
                $sucursal = "";
            }
            $nombre = $hotelesRecogida->item($jAux)->getElementsByTagName('nombre');
            if ($nombre->length > 0) {
                $nombre = $nombre->item(0)->nodeValue;
            } else {
                $nombre = "";
            }
            $recogida = $hotelesRecogida->item($jAux)->getElementsByTagName('recogida');
            if ($recogida->length > 0) {
                $recogida = $recogida->item(0)->nodeValue;
            } else {
                $recogida = "";
            }
            echo $return;
            echo "recogida: " . $recogida;
            echo $return;
            $ruta = $hotelesRecogida->item($jAux)->getElementsByTagName('ruta');
            if ($ruta->length > 0) {
                $ruta = $ruta->item(0)->nodeValue;
            } else {
                $ruta = "";
            }
            $horas = $hotelesRecogida->item($jAux)->getElementsByTagName('horas');
            if ($horas->length > 0) {
                $horas = $horas->item(0)->nodeValue;
            } else {
                $horas = "";
            }
            $minutos = $hotelesRecogida->item($jAux)->getElementsByTagName('minutos');
            if ($minutos->length > 0) {
                $minutos = $minutos->item(0)->nodeValue;
            } else {
                $minutos = "";
            }
            $codigo = $hotelesRecogida->item($jAux)->getElementsByTagName('codigo');
            if ($codigo->length > 0) {
                $codigo = $codigo->item(0)->nodeValue;
            } else {
                $codigo = "";
            }
    
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('radiales_hoteis');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'grupo' => $grupo,
                    'progresivo' => $progresivo,
                    'sucursal' => $sucursal,
                    'nombre' => $nombre,
                    'recogida' => $recogida,
                    'ruta' => $ruta,
                    'horas' => $horas,
                    'minutos' => $minutos,
                    'codigo' => $codigo
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>


