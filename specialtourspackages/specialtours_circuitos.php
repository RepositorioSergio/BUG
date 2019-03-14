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
$sql = "select value from settings where name='enablespecialtourspackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_specialtourspackages = $affiliate_id;
} else {
    $affiliate_id_specialtourspackages = 0;
}
$sql = "select value from settings where name='specialtourspackagesuser' and affiliate_id=$affiliate_id_specialtourspackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $specialtourspackagesuser = $row_settings['value'];
}
$sql = "select value from settings where name='specialtourspackagespassword' and affiliate_id=$affiliate_id_specialtourspackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $specialtourspackagespassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='specialtourspackagesserviceURL' and affiliate_id=$affiliate_id_specialtourspackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $specialtourspackagesserviceURL = $row['value'];
}
$sql = "select value from settings where name='specialtourspackagesagency' and affiliate_id=$affiliate_id_specialtourspackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $specialtourspackagesagency = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$raw = '{ "usuario" : "' . $specialtourspackagesuser . '", "password" : "' . $specialtourspackagespassword . '", "agencia" : "' . $specialtourspackagesagency . '" }';
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'X-Powered-By' => 'Zend Framework',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/x-www-form-urlencoded'
));
$url = $specialtourspackagesserviceURL . "v1/clientes/login";
$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
    $response = $response->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}
$response = json_decode($response, true);
$token = $response['token'];
echo $return;
echo $token;
echo $return;
$config = new \Zend\Config\Config(include '../config/autoload/global.specialtours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = $specialtourspackagesserviceURL . "v1/superbuscador/circuitos";
echo $return;
echo $url;
echo $return;
$raw = '{ "codigoGrupoMercado" : "AME", "codigoIdioma" : "es" }';
$client->setUri($url);
$client->setMethod('GET');
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'X-Powered-By' => 'Zend Framework',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/x-www-form-urlencoded',
    'Authorization: Bearer ' . $token
));
$response = $client->send();
if ($response->isSuccess()) {
    // $response = $response->getBody();
    include "/srv/www/htdocs/specialtours/specialtours/specialtours_circuitos_debug.php";
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}
echo $return;
echo $response;
echo $return;
$response = json_decode($response, true);
 /* echo "<xmp>";
var_dump($response);
echo "</xmp>";  */

$config = new \Zend\Config\Config(include '../config/autoload/global.specialtours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$circuitos = $response['circuitos'];

$count = count($circuitos);
for ($i=0; $i < $count; $i++) { 
    $idCatalogo = $circuitos[$i]['idCatalogo'];
    $codigoCircuito = $circuitos[$i]['codigoCircuito'];
    $nombreCircuito = $circuitos[$i]['nombreCircuito'];
    $duracion = $circuitos[$i]['duracion'];
    $nombreCiudadInicio = $circuitos[$i]['nombreCiudadInicio'];
    $idCircuito = $circuitos[$i]['idCircuito'];
    $comidas = $circuitos[$i]['comidas'];
    $descripcionRecorrido = $circuitos[$i]['descripcionRecorrido'];
    $mesInicio = $circuitos[$i]['mesInicio'];
    $anoInicio = $circuitos[$i]['anoInicio'];
    $mesFin = $circuitos[$i]['mesFin'];
    $anoFin = $circuitos[$i]['anoFin'];
    $precioVentaDbl = $circuitos[$i]['precioVentaDbl'];
    $codigoMoneda = $circuitos[$i]['codigoMoneda'];
    $diasSalidas = $circuitos[$i]['diasSalidas'];

    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('circuitos');
    $select->where(array(
        'idCircuito' => $idCircuito
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = (int) $data['idCircuito'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'idCatalogo' => $idCatalogo,
                'codigoCircuito' => $codigoCircuito,
                'nombreCircuito' => $nombreCircuito,
                'duracion' => $duracion,
                'nombreCiudadInicio' => $nombreCiudadInicio,
                'idCircuito' => $idCircuito,
                'comidas' => $comidas,
                'descripcionRecorrido' => $descripcionRecorrido,
                'mesInicio' => $mesInicio,
                'anoInicio' => $anoInicio,
                'mesFin' => $mesFin,
                'anoFin' => $anoFin,
                'precioVentaDbl' => $precioVentaDbl,
                'codigoMoneda' => $codigoMoneda,
                'diasSalidas' => $diasSalidas );
                $where['idCircuito = ?']  = $idCircuito;
            $update = $sql->update('circuitos', $data, $where);
            $db->getDriver()
            ->getConnection()
            ->disconnect();   
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('circuitos');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idCatalogo' => $idCatalogo,
                'codigoCircuito' => $codigoCircuito,
                'nombreCircuito' => $nombreCircuito,
                'duracion' => $duracion,
                'nombreCiudadInicio' => $nombreCiudadInicio,
                'idCircuito' => $idCircuito,
                'comidas' => $comidas,
                'descripcionRecorrido' => $descripcionRecorrido,
                'mesInicio' => $mesInicio,
                'anoInicio' => $anoInicio,
                'mesFin' => $mesFin,
                'anoFin' => $anoFin,
                'precioVentaDbl' => $precioVentaDbl,
                'codigoMoneda' => $codigoMoneda,
                'diasSalidas' => $diasSalidas
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        }
    } else {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('circuitos');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'idCatalogo' => $idCatalogo,
            'codigoCircuito' => $codigoCircuito,
            'nombreCircuito' => $nombreCircuito,
            'duracion' => $duracion,
            'nombreCiudadInicio' => $nombreCiudadInicio,
            'idCircuito' => $idCircuito,
            'comidas' => $comidas,
            'descripcionRecorrido' => $descripcionRecorrido,
            'mesInicio' => $mesInicio,
            'anoInicio' => $anoInicio,
            'mesFin' => $mesFin,
            'anoFin' => $anoFin,
            'precioVentaDbl' => $precioVentaDbl,
            'codigoMoneda' => $codigoMoneda,
            'diasSalidas' => $diasSalidas
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    }

    /* $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('circuitos');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'idCatalogo' => $idCatalogo,
        'codigoCircuito' => $codigoCircuito,
        'nombreCircuito' => $nombreCircuito,
        'duracion' => $duracion,
        'nombreCiudadInicio' => $nombreCiudadInicio,
        'idCircuito' => $idCircuito,
        'comidas' => $comidas,
        'descripcionRecorrido' => $descripcionRecorrido,
        'mesInicio' => $mesInicio,
        'anoInicio' => $anoInicio,
        'mesFin' => $mesFin,
        'anoFin' => $anoFin,
        'precioVentaDbl' => $precioVentaDbl,
        'codigoMoneda' => $codigoMoneda,
        'diasSalidas' => $diasSalidas
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
     $db->getDriver()
    ->getConnection()
    ->disconnect(); */

    $paisesRecorrido = $circuitos[$i]['paisesRecorrido'];
    for ($j=0; $j < count($paisesRecorrido); $j++) { 
        $nombre = $paisesRecorrido[$j]['nombre'];
        $idCircuito = $paisesRecorrido[$j]['idCircuito'];
        $idPais = $paisesRecorrido[$j]['idPais'];
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('circuitos_paisesRecorrido');
        $insert->values(array(
            'nombre' => $nombre,
            'idCircuito' => $idCircuito,
            'idPais' => $idPais
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    }

    $ciudadesRecorrido = $circuitos[$i]['ciudadesRecorrido'];
    for ($k=0; $k < count($ciudadesRecorrido); $k++) { 
        $idCircuito = $ciudadesRecorrido[$k]['idCircuito'];
        $nombre = $ciudadesRecorrido[$k]['nombre'];
        $idCiudad = $ciudadesRecorrido[$k]['idCiudad'];
        $idPais = $ciudadesRecorrido[$k]['idPais'];
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('circuitos_ciudadesRecorrido');
        $insert->values(array(
            'idCircuito' => $idCircuito,
            'nombre' => $nombre,
            'idCiudad' => $idCiudad,
            'idPais' => $idPais
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    }

    $zonas = $circuitos[$i]['zonas'];
    for ($l=0; $l < count($zonas); $l++) { 
        $idZona = $zonas[$l]['idZona'];
        $idCircuito = $zonas[$l]['idCircuito'];
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('circuitos_zonas');
        $insert->values(array(
            'idZona' => $idZona,
            'idCircuito' => $idCircuito
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    }

    $productos = $circuitos[$i]['productos'];
    for ($m=0; $m < count($productos); $m++) { 
        $nombre = $productos[$m]['nombre'];
        $idCircuito = $productos[$m]['idCircuito'];
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('circuitos_productos');
        $insert->values(array(
            'nombre' => $nombre,
            'idCircuito' => $idCircuito
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    }

    $categorias = $circuitos[$i]['categorias'];
    for ($n=0; $n < count($categorias); $n++) { 
        $codigo = $categorias[$n]['codigo'];
        $idCircuito = $categorias[$n]['idCircuito'];
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('circuitos_categorias');
        $insert->values(array(
            'codigo' => $codigo,
            'idCircuito' => $idCircuito
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } 
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>
