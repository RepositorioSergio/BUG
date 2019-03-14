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
$params = array(
    "claveEntidad" => $trapsaturpackagesEntityKey,
    "login" => $trapsaturpackagesLogin,
    "password" => $trapsaturpackagesPassword,
    "idioma" => "es"
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
    $client->__soapCall('tablasMaestras', array(
        $params
    ));
} catch (\Exception $e) {
    var_dump($e);
    die();
}

$xmlrequest = $client->__getLastRequest();
$xmlresult = $client->__getLastResponse();

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

$node2 = $inputDoc->getElementsByTagName("paises");
$node2 = $inputDoc->getElementsByTagName("multiRef");
for ($k=0; $k < $node2->length; $k++) { 
    $codigo = $node2->item($k)->getElementsByTagName('codigo');
    if ($codigo->length > 0) {
        $codigo = $codigo->item(0)->nodeValue;
    } else {
        $codigo = "";
    }
    $nombre = $node2->item($k)->getElementsByTagName('nombre');
    if ($nombre->length > 0) {
        $nombre = $nombre->item(0)->nodeValue;
    } else {
        $nombre = "";
    }
    $maestroCiudad = $node2->item($k)->getElementsByTagName('maestroCiudad');
    if ($maestroCiudad->length > 0) {
        $maestroCiudad = $maestroCiudad->item(0)->nodeValue;
    } else {
        $maestroCiudad = "";
    }
    echo $return;
    echo "maestroCiudad: " . $maestroCiudad;
    echo $return;

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('paises');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codigo' => $codigo,
            'nome' => $nombre,
            'maestroCidade' => $maestroCiudad
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


$node2 = $inputDoc->getElementsByTagName("ciudades");
$node2 = $inputDoc->getElementsByTagName("multiRef");
for ($k=0; $k < $node2->length; $k++) { 
    $codigo = $node2->item($k)->getElementsByTagName('codigo');
    if ($codigo->length > 0) {
        $codigo = $codigo->item(0)->nodeValue;
    } else {
        $codigo = "";
    }
    $nombre = $node2->item($k)->getElementsByTagName('nombre');
    if ($nombre->length > 0) {
        $nombre = $nombre->item(0)->nodeValue;
    } else {
        $nombre = "";
    }
    $codigoPais = $node2->item($k)->getElementsByTagName('codigoPais');
    if ($codigoPais->length > 0) {
        $codigoPais = $codigoPais->item(0)->nodeValue;
    } else {
        $codigoPais = "";
    }
    $nombrePais = $node2->item($k)->getElementsByTagName('nombrePais');
    if ($nombrePais->length > 0) {
        $nombrePais = $nombrePais->item(0)->nodeValue;
    } else {
        $nombrePais = "";
    }
    $dayNumber = $node2->item($k)->getElementsByTagName('dayNumber');
    if ($dayNumber->length > 0) {
        $dayNumber = $dayNumber->item(0)->nodeValue;
    } else {
        $dayNumber = "";
    }
    $salidaCircuitos = $node2->item($k)->getElementsByTagName('salidaCircuitos');
    if ($salidaCircuitos->length > 0) {
        $salidaCircuitos = $salidaCircuitos->item(0)->nodeValue;
    } else {
        $salidaCircuitos = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('cidades');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codigo' => $codigo,
            'nome' => $nombre,
            'codigoPais' => $codigoPais,
            'nomePais' => $nombrePais,
            'dia' => $dayNumber,
            'saidaCircuito' => $salidaCircuitos
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (Exception $e) {
        echo $return;
        echo "Exception2: " . $e;
        echo $return;
    }

} 

$node2 = $inputDoc->getElementsByTagName("regimenes");
$node2 = $inputDoc->getElementsByTagName("multiRef");
for ($k=0; $k < $node2->length; $k++) { 
    $codigo = $node2->item($k)->getElementsByTagName('codigo');
    if ($codigo->length > 0) {
        $codigo = $codigo->item(0)->nodeValue;
    } else {
        $codigo = "";
    }
    $descripcion = $node2->item($k)->getElementsByTagName('descripcion');
    if ($descripcion->length > 0) {
        $descripcion = $descripcion->item(0)->nodeValue;
    } else {
        $descripcion = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('regimenes_tablas');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codigo' => $codigoSub,
            'descricao' => $descripcion
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (Exception $e) {
        echo $return;
        echo "Exception2: " . $e;
        echo $return;
    }
    

}


$node = $inputDoc->getElementsByTagName("folletos");
$node = $inputDoc->getElementsByTagName("multiRef");
for ($i = 0; $i < $node->length; $i ++) {
    $codigo = $node->item($i)->getElementsByTagName('codigo');
    if ($codigo->length > 0) {
        $codigo = $codigo->item(0)->nodeValue;
    } else {
        $codigo = "";
    }
    $descripcion = $node->item($i)->getElementsByTagName('descripcion');
    if ($descripcion->length > 0) {
        $descripcion = $descripcion->item(0)->nodeValue;
    } else {
        $descripcion = "";
    }
    $fechaDesde = $node->item($i)->getElementsByTagName('fechaDesde');
    if ($fechaDesde->length > 0) {
        $fechaDesde = $fechaDesde->item(0)->nodeValue;
    } else {
        $fechaDesde = "";
    }
    $fechaHasta = $node->item($i)->getElementsByTagName('fechaHasta');
    if ($fechaHasta->length > 0) {
        $fechaHasta = $fechaHasta->item(0)->nodeValue;
    } else {
        $fechaHasta = "";
    }
    $descripcionPeriodo = $node->item($i)->getElementsByTagName('descripcionPeriodo');
    if ($descripcionPeriodo->length > 0) {
        $descripcionPeriodo = $descripcionPeriodo->item(0)->nodeValue;
    } else {
        $descripcionPeriodo = "";
    }
    $subtitulo = $node->item($i)->getElementsByTagName('subtitulo');
    if ($subtitulo->length > 0) {
        $subtitulo = $subtitulo->item(0)->nodeValue;
    } else {
        $subtitulo = "";
    }
    $pathImagen = $node->item($i)->getElementsByTagName('pathImagen');
    if ($pathImagen->length > 0) {
        $pathImagen = $pathImagen->item(0)->nodeValue;
    } else {
        $pathImagen = "";
    }
    $texto = $node->item($i)->getElementsByTagName('texto');
    if ($texto->length > 0) {
        $texto = $texto->item(0)->nodeValue;
    } else {
        $texto = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('folhetos');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codigo' => $codigo,
            'descripcion' => $descripcion,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'descripcionPeriodo' => $descripcionPeriodo,
            'subtitulo' => $subtitulo,
            'pathImagen' => $pathImagen,
            'texto' => $texto
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (Exception $e) {
        echo $return;
        echo "Exception2: " . $e;
        echo $return;
    }
    
    
    
    $subFolletos = $node->item($i)->getElementsByTagName('subFolletos');
    for ($iAux = 0; $iAux < $subFolletos->length; $iAux ++) {
        $codigoSub = $node->item($iAux)->getElementsByTagName('codigo');
        if ($codigoSub->length > 0) {
            $codigoSub = $codigoSub->item(0)->nodeValue;
        } else {
            $codigoSub = "";
        }
        $descripcionSub = $node->item($iAux)->getElementsByTagName('descripcion');
        if ($descripcionSub->length > 0) {
            $descripcionSub = $descripcionSub->item(0)->nodeValue;
        } else {
            $descripcionSub = "";
        }
        $codigoFolleto = $node->item($iAux)->getElementsByTagName('codigoFolleto');
        if ($codigoFolleto->length > 0) {
            $codigoFolleto = $codigoFolleto->item(0)->nodeValue;
        } else {
            $codigoFolleto = "";
        }
        $descripcionFolleto = $node->item($iAux)->getElementsByTagName('descripcionFolleto');
        if ($descripcionFolleto->length > 0) {
            $descripcionFolleto = $descripcionFolleto->item(0)->nodeValue;
        } else {
            $descripcionFolleto = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('subFolhetos');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codigo' => $codigoSub,
                'descripcion' => $descripcionSub,
                'codigoFolleto' => $codigoFolleto,
                'descripcionFolleto' => $descripcionFolleto
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (Exception $e) {
            echo $return;
            echo "Exception2: " . $e;
            echo $return;
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

