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
// Catalogo
$url = $specialtourspackagesserviceURL . "v1/superbuscador/ciudades";
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
    $response = $response->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo "<br/>" . $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}
echo $return;
$response = json_decode($response, true);
echo $return;
/* var_dump($response);
echo $return; */
/* $array = json_decode($response, true); */
$config = new \Zend\Config\Config(include '../config/autoload/global.specialtours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$id = 0;

$ciudades = $response['ciudades'];
/* echo '<xmp>';
var_dump($ciudades);
echo '</xmp>';*/
for ($i=0; $i < count($ciudades); $i++) { 
    $idCiudad = $ciudades[$i]['idCiudad'];
    $nombre = $ciudades[$i]['nombre'];
    $nombrePais = $ciudades[$i]['nombrePais'];
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('ciudades');
    $select->where(array(
        'idCiudad' => $idCiudad
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = (int) $data['idCiudad'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'idCiudad' => $idCiudad,
                'nombre' => $nombre,
                'nombrePais' => $nombrePais );
                $where['idCiudad = ?']  = $idCiudad;
            $update = $sql->update('ciudades', $data, $where);
            $db->getDriver()
            ->getConnection()
            ->disconnect();   
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('ciudades');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idCiudad' => $idCiudad,
                'nombre' => $nombre,
                'nombrePais' => $nombrePais
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
        $insert->into('ciudades');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'idCiudad' => $idCiudad,
            'nombre' => $nombre,
            'nombrePais' => $nombrePais
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    }

    $circuitos = $ciudades[$i]['circuitos'];
    for ($j=0; $j < count($circuitos); $j++) { 
        $idCiudad = $circuitos[$j]['idCiudad'];
        $idCircuito = $circuitos[$j]['idCircuito'];
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('ciudades_circuitos');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'idCiudad' => $idCiudad,
            'idCircuito' => $idCircuito
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    }

    $catalogos = $ciudades[$i]['catalogos'];
    for ($k=0; $k < count($catalogos); $k++) { 
        $idCiudad = $catalogos[$k]['idCiudad'];
        $idCatalogo = $catalogos[$k]['idCatalogo'];
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('ciudades_catalogos');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'idCiudad' => $idCiudad,
            'idCatalogo' => $idCatalogo
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

