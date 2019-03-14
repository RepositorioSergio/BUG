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
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = new Sql($db);
$select = $sql->select();
$select->from('circuitos');
// Campos
$select->columns([
    'id',
    'idCircuito'
]);
//
// Filtro (nao necessario)
//
// $select->where(array(
// 'hotel_id' => $row->id
// ));
//
// Sort (nao preciso)
//
// $select->order("sortorder");
$statement = $sql->prepareStatementForSqlObject($select);
$result = $statement->execute();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $id = $row->id;
        $idCircuito = $row->idCircuito;
        echo $return;
        echo $id;
        echo $return;
        $url = $specialtourspackagesserviceURL . "v1/superbuscador/circuitos/" . $idCircuito . "/recorrido";
        echo $return;
        echo $idCircuito;
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
        echo $return;
        $response = json_decode($response, true);
        echo $return;
        echo '<xmp>';
        var_dump($response);
        echo '</xmp>';
        
        
        // AQUI - > INSERT - UPDATE + PARSER
        $config = new \Zend\Config\Config(include '../config/autoload/global.specialtours.php');
        $config = [
            'driver' => $config->db->driver,
            'database' => $config->db->database,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'hostname' => $config->db->hostname
        ];
        $db = new \Zend\Db\Adapter\Adapter($config);

        $id = "";

        $grupos = $response['grupos'];
        for ($i=0; $i < count($grupos); $i++) { 
            $grupo = $grupos[$i]['grupo'];
            $idCircuito = $grupos[$i]['idCircuito'];
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('circuitos_recorrido_grupos');
            $select->where(array(
                'grupo' => $grupo
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $grupo = $data['grupo'];
                if ($grupo != "") {
            		$sql = new Sql($db);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'grupo' => $grupo,
                        'idCircuito' => $idCircuito
                    );
                        $where['grupo = ?']  = $grupo;
                    $update = $sql->update('circuitos_recorrido_grupos', $data, $where);
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();   
                } else {
                	$sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('circuitos_recorrido_grupos');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'grupo' => $grupo,
                        'idCircuito' => $idCircuito
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
                $insert->into('circuitos_recorrido_grupos');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'grupo' => $grupo,
                    'idCircuito' => $idCircuito
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            }

            
            $recorrido = $grupos[$i]['recorrido'];
            for ($j=0; $j < count($recorrido); $j++) { 
                $grupo = $recorrido[$j]['grupo'];
                $descripcion = $recorrido[$j]['descripcion'];
                $coordenadaX = $recorrido[$j]['coordenadaX'];
                $coordenadaY = $recorrido[$j]['coordenadaY'];
                $noches = $recorrido[$j]['noches'];
                $ruta = $recorrido[$j]['ruta'];
                $nombre = $recorrido[$j]['nombre'];
                $imagen = $recorrido[$j]['imagen'];
                $galeria = $recorrido[$j]['galeria'];
                $descripcionVisita = $recorrido[$j]['descripcionVisita'];
                echo "PASSOU 1 " . $imagen;
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('circuitos_grupos_recorrido');
                $select->where(array(
                    'grupo' => $grupo
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                echo "PASSOU 2";
                if ($result->valid()) {
                    $data = $result->current();
                    $grupo = $data['grupo'];
                    if ($grupo != "") {
                        $sql = new Sql($db);
                        $data = array(
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'grupo' => $grupo,
                            'descripcion' => $descripcion,
                            'coordenadaX' => $coordenadaX,
                            'coordenadaY' => $coordenadaY,
                            'noches' => $noches,
                            'ruta' => $ruta,
                            'imagen' => $imagen,
                            'galeria' => $galeria,
                            'descripcionVisita' => $descripcionVisita
                        );
                            $where['grupo = ?']  = $grupo;
                        $update = $sql->update('circuitos_grupos_recorrido', $data, $where);
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();   
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('circuitos_grupos_recorrido');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'grupo' => $grupo,
                            'descripcion' => $descripcion,
                            'coordenadaX' => $coordenadaX,
                            'coordenadaY' => $coordenadaY,
                            'noches' => $noches,
                            'ruta' => $ruta,
                            'imagen' => $imagen,
                            'galeria' => $galeria,
                            'descripcionVisita' => $descripcionVisita
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    }
                } else {
                    echo "PASSOU 3";
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('circuitos_grupos_recorrido');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'grupo' => $grupo,
                        'descripcion' => $descripcion,
                        'coordenadaX' => $coordenadaX,
                        'coordenadaY' => $coordenadaY,
                        'noches' => $noches,
                        'ruta' => $ruta,
                        'imagen' => $imagen,
                        'galeria' => $galeria,
                        'descripcionVisita' => $descripcionVisita
                    ), $insert::VALUES_MERGE);
                    echo "PASSOU 4";
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    echo "PASSOU 5";
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                }

                
                
            }

            $observaciones = $circuitos[$i]['observaciones'];
            $notas = $circuitos[$i]['notas'];
            for ($n=0; $n < count($notas); $n++) { 
                $valor = $notas[$n]['valor'];
                $idCircuito = $notas[$n]['idCircuito'];
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('circuitos_notas');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'valor' => $valor,
                    'idCircuito' => $idCircuito
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            }

            $categoriasProductos = $circuitos[$i]['categoriasProductos'];
            for ($c=0; $c < count($categoriasProductos); $c++) { 
                $nombre = $categoriasProductos[$c]['nombre'];
                $descripcion = $categoriasProductos[$c]['descripcion'];
                $idCircuito = $categoriasProductos[$c]['idCircuito'];
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('circuitos_categoriasProductos');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'idCircuito' => $idCircuito
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            }

            $serviciosIncluidos = $circuitos[$i]['serviciosIncluidos'];
            for ($si=0; $si < count($serviciosIncluidos); $si++) { 
                $detalle = $serviciosIncluidos[$si]['detalle'];
                $icono = $serviciosIncluidos[$si]['icono'];
                $idCircuito = $serviciosIncluidos[$si]['idCircuito'];
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('circuitos_serviciosIncluidos');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'detalle' => $detalle,
                    'icono' => $icono,
                    'idCircuito' => $idCircuito
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            }

            $tarifas = $circuitos[$i]['tarifas'];
            $paisesRecorrido = $circuitos[$i]['paisesRecorrido'];
            for ($p=0; $p < count($paisesRecorrido); $p++) { 
                $idCircuito = $paisesRecorrido[$p]['idCircuito'];
                $nombre = $paisesRecorrido[$p]['nombre'];
                $orden = $paisesRecorrido[$p]['orden'];
                $idPais = $paisesRecorrido[$p]['idPais'];
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('circuitos_paisesRecorrido');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'idCircuito' => $idCircuito,
                    'nombre' => $nombre,
                    'orden' => $orden,
                    'idPais' => $idPais
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();

                $ciudadesRecorrido = $paisesRecorrido[$p]['ciudadesRecorrido'];
                for ($pc=0; $pc < count($ciudadesRecorrido); $pc++) { 
                    $idPais = $paisesRecorrido[$pc]['idPais'];
                    $idCiudad = $paisesRecorrido[$pc]['idCiudad'];
                    $noches = $paisesRecorrido[$pc]['noches'];
                    $nombre = $paisesRecorrido[$pc]['nombre'];
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('circuitos_ciudadesRecorrido');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'idPais' => $idPais,
                        'idCiudad' => $idCiudad,
                        'noches' => $noches,
                        'nombre' => $nombre
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                }
            }

            $grupos = $circuitos[$i]['grupos'];
            for ($g=0; $g < count($grupos); $g++) { 
                $nombreGrupo = $grupos[$g]['nombreGrupo'];
                $idCircuito = $grupos[$g]['idCircuito'];
                $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('circuitos_grupos');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'nombreGrupo' => $nombreGrupo,
                        'idCircuito' => $idCircuito
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();

                $recorridos = $grupos[$g]['recorridos'];
                for ($gr=0; $gr < count($recorridos); $gr++) { 
                    $nombreGrupo = $recorridos[$gr]['nombreGrupo'];
                    $descripcion = $recorridos[$gr]['descripcion'];
                    $coordenadaX = $recorridos[$gr]['coordenadaX'];
                    $coordenadaY = $recorridos[$gr]['coordenadaY'];
                    $noches = $recorridos[$gr]['noches'];
                    $ruta = $recorridos[$gr]['ruta'];
                    $imagen = $recorridos[$gr]['imagen'];
                    $galeria = $recorridos[$gr]['galeria'];
                    $descripcionVisita = $recorridos[$gr]['descripcionVisita'];
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('circuitos_recorridos');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'nombreGrupo' => $nombreGrupo,
                        'descripcion' => $descripcion,
                        'coordenadaX' => $coordenadaX,
                        'coordenadaY' => $coordenadaY,
                        'noches' => $noches,
                        'ruta' => $ruta,
                        'imagen' => $imagen,
                        'galeria' => $galeria,
                        'descripcionVisita' => $descripcionVisita
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                }
            }

            $calendario = $circuitos[$i]['calendario'];
            $programaViajeSalidas = $circuitos[$i]['programaViajeSalidas'];
            $periodosValidez = $circuitos[$i]['periodosValidez'];
            $disponibilidad = $circuitos[$i]['disponibilidad'];
        }

        die();
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>

