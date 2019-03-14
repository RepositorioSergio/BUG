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
$sql = "select value from settings where name='enablepanavisionpackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_panavisionpackages = $affiliate_id;
} else {
    $affiliate_id_panavisionpackages = 0;
}
$sql = "select value from settings where name='panavisionpackagesuser' and affiliate_id=$affiliate_id_panavisionpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panavisionpackagesuser = $row_settings['value'];
}
$sql = "select value from settings where name='panavisionpackagespassword' and affiliate_id=$affiliate_id_panavisionpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panavisionpackagespassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='panavisionpackagesserviceURL' and affiliate_id=$affiliate_id_panavisionpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $panavisionpackagesserviceURL = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.panavision.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

/*
 * $sql = "SELECT idfolleto, idcrucero FROM cruceros";
 * $statement = $db->createStatement($sql);
 * try {
 * $statement->prepare();
 * } catch (\Exception $e) {
 * echo $return;
 * echo $e->getMessage();
 * echo $return;
 * die();
 * }
 */

/*
 * $result = $statement->execute();
 * $result->buffer();
 * if ($result instanceof ResultInterface && $result->isQueryResult()) {
 * $resultSet = new ResultSet();
 * $resultSet->initialize($result);
 * foreach ($resultSet as $row) {
 * $idfolleto = $row->idfolleto;
 * $idcrucero = $row->idcrucero;
 */

$sql = "SELECT idfolleto, idcrucero FROM cruzeiros";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$url = 'https://supply.integration2.testaroom.com/';
$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $idfolleto = $row->idfolleto;
        $idcrucero = $row->idcrucero;

        $raw = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tms="TmsApi">
        <soapenv:Header/>
        <soapenv:Body>
            <tms:descripcionCrucero soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                <xml xsi:type="xsd:string">?</xml>
            </tms:descripcioncrucero>
        </soapenv:Body>
        </soapenv:Envelope>';

        $client = new Client();
        $client->setOptions(array(
            'timeout' => 100,
            'sslverifypeer' => false,
            'sslverifyhost' => false
        ));
        $client->setAuth($panavisionpackagesuser, $panavisionpackagespassword);
        $client->setHeaders(array(
            'Accept-Encoding' => 'gzip,deflate',
            'X-Powered-By' => 'Zend Framework',
            'Content-Length' => strlen($raw),
            'Content-Type' => 'text/xml'
        ));
        echo $return;
        echo $return;
        echo $panavisionpackagesserviceURL;
        echo $return;
        echo $return;
        echo $raw;
        echo $return;
        echo $return;
        $client->setUri('http://wwwaps.panavision-tours.es/integracionAme/TmsApi?invoke=descripcionCrucero&xml=<consulta><idfolleto>' . $idfolleto . '</idfolleto><idcrucero>' . $idcrucero . '</idcrucero></consulta>');
        $client->setMethod('GET');
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

        $config = new \Zend\Config\Config(include '../config/autoload/global.panavision.php');
        $config = [
            'driver' => $config->db->driver,
            'database' => $config->db->database,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'hostname' => $config->db->hostname
        ];
        $db = new \Zend\Db\Adapter\Adapter($config);

        $response = htmlspecialchars_decode($response);
        /* echo $return;
        echo $response;
        echo $return; */
        $start = strpos($response, "<crucero>");
        $end = strpos($response, "</crucero>");
        $response = substr($response, $start, $end - $start + 10);

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $crucero = $inputDoc->getElementsByTagName("crucero");
        $titulo = $crucero->item(0)->getElementsByTagName("titulo");
        if ($titulo->length > 0) {
            $titulo = $titulo->item(0)->nodeValue;
        } else {
            $titulo = "";
        }
        echo $return;
        echo $titulo;
        echo $return;
        $subtitulo = $crucero->item(0)->getElementsByTagName("subtitulo");
        if ($subtitulo->length > 0) {
            $subtitulo = $subtitulo->item(0)->nodeValue;
        } else {
            $subtitulo = "";
        }
        $idbarco = $crucero->item(0)->getElementsByTagName("idbarco");
        if ($idbarco->length > 0) {
            $idbarco = $idbarco->item(0)->nodeValue;
        } else {
            $idbarco = "";
        }
        $nombrebarco = $crucero->item(0)->getElementsByTagName("nombrebarco");
        if ($nombrebarco->length > 0) {
            $nombrebarco = $nombrebarco->item(0)->nodeValue;
        } else {
            $nombrebarco = "";
        }
        $duracion = $crucero->item(0)->getElementsByTagName("duracion");
        if ($duracion->length > 0) {
            $duracion = $duracion->item(0)->nodeValue;
        } else {
            $duracion = "";
        }
        $precio_desde = $crucero->item(0)->getElementsByTagName("precio_desde");
        if ($precio_desde->length > 0) {
            $precio_desde = $precio_desde->item(0)->nodeValue;
        } else {
            $precio_desde = "";
        }
        $regimen = $crucero->item(0)->getElementsByTagName("regimen");
        if ($regimen->length > 0) {
            $regimen = $regimen->item(0)->nodeValue;
        } else {
            $regimen = "";
        }
        $incluye = $crucero->item(0)->getElementsByTagName("incluye");
        if ($incluye->length > 0) {
            $incluye = $incluye->item(0)->nodeValue;
        } else {
            $incluye = "";
        }
        $incluido = $crucero->item(0)->getElementsByTagName("incluido");
        if ($incluido->length > 0) {
            $incluido = $incluido->item(0)->nodeValue;
        } else {
            $incluido = "";
        }
        $pdf = $crucero->item(0)->getElementsByTagName("pdf");
        if ($pdf->length > 0) {
            $pdf = $pdf->item(0)->nodeValue;
        } else {
            $pdf = "";
        }
        echo $return;
        echo $pdf;
        echo $return;

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('descricaocruzeiros');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'titulo' => $titulo,
                'subtitulo' => $subtitulo,
                'duracion' => $duracion,
                'precio_desde' => $precio_desde,
                'regimen' => $regimen,
                'incluye' => $incluye,
                'incluido' => $incluido,
                'pdf' => $pdf
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error: " . $e;
            echo $return;
        }


        //itinerario
        $itinerario = $crucero->item(0)->getElementsByTagName("itinerario");
        if ($itinerario->length > 0) {
            echo $return;
            echo "TAM: " . $itinerario->length;
            echo $return;
            for ($i=0; $i < $itinerario->length; $i++) { 
                $ciudades = $itinerario->item($i)->getElementsByTagName("ciudades");
                if ($ciudades->length > 0) {
                    $ciudadAlojamiento = $ciudades->item(0)->getAttribute("ciudadAlojamiento");
                    $ciudades = $ciudades->item(0)->nodeValue;
                } else {
                    $ciudades = "";
                }
                echo $return;
                echo $ciudades;
                echo $return;
                $dia = $itinerario->item($i)->getElementsByTagName("dia");
                if ($dia->length > 0) {
                    $dia = $dia->item(0)->nodeValue;
                } else {
                    $dia = "";
                }
                $descripcion = $itinerario->item($i)->getElementsByTagName("descripcion");
                if ($descripcion->length > 0) {
                    $descripcion = $descripcion->item(0)->nodeValue;
                } else {
                    $descripcion = "";
                }
                echo $return;
                echo $descripcion;
                echo $return;
                $destino = $itinerario->item($i)->getElementsByTagName("destino");
                if ($destino->length > 0) {
                    $nombreDestino = $destino->item(0)->getElementsByTagName("nombreDestino");
                    if ($nombreDestino->length > 0) {
                        $nombreDestino = $nombreDestino->item(0)->nodeValue;
                    } else {
                        $nombreDestino = "";
                    }
                    $codigoDestino = $destino->item(0)->getElementsByTagName("codigoDestino");
                    if ($codigoDestino->length > 0) {
                        $codigoDestino = $codigoDestino->item(0)->nodeValue;
                    } else {
                        $codigoDestino = "";
                    }
                    $codigoPais = $destino->item(0)->getElementsByTagName("codigoPais");
                    if ($codigoPais->length > 0) {
                        $codigoPais = $codigoPais->item(0)->nodeValue;
                    } else {
                        $codigoPais = "";
                    }
                    $ciudadInicio = $destino->item(0)->getElementsByTagName("ciudadInicio");
                    if ($ciudadInicio->length > 0) {
                        $ciudadInicio = $ciudadInicio->item(0)->nodeValue;
                    } else {
                        $ciudadInicio = "";
                    }
                    echo $return;
                    echo $ciudadInicio;
                    echo $return;
                    //coordenadas
                    $coordenadas = $destino->item(0)->getElementsByTagName("coordenadas");
                    if ($coordenadas->length > 0) {
                        $latitud = $coordenadas->item(0)->getElementsByTagName("latitud");
                        if ($latitud->length > 0) {
                            $latitud = $latitud->item(0)->nodeValue;
                        } else {
                            $latitud = "";
                        }
                        $longitud = $coordenadas->item(0)->getElementsByTagName("longitud");
                        if ($longitud->length > 0) {
                            $longitud = $longitud->item(0)->nodeValue;
                        } else {
                            $longitud = "";
                        }
                        echo $return;
                        echo $longitud;
                        echo $return;
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('itinerarios_cruzeiros');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'ciudadAlojamiento' => $ciudadAlojamiento,
                            'ciudades' => $ciudades,
                            'dia' => $dia,
                            'descripcion' => $descripcion,
                            'nombreDestino' => $nombreDestino,
                            'codigoDestino' => $codigoDestino,
                            'codigoPais' => $codigoPais,
                            'ciudadInicio' => $ciudadInicio,
                            'latitud' => $latitud,
                            'longitud' => $longitud
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error: " . $e;
                        echo $return;
                    }

                    //hotelesPrevistos
                    $hotelesPrevistos = $destino->item(0)->getElementsByTagName("hotelesPrevistos");
                    if ($hotelesPrevistos->length > 0) {
                        for ($k=0; $k < $hotelesPrevistos->length; $k++) { 
                            $hotel = $hotelesPrevistos->item($k)->getElementsByTagName("hotel");
                            if ($hotel->length > 0) {
                                $hotel = $hotel->item(0)->nodeValue;
                            } else {
                                $hotel = "";
                            }

                            echo $return;
                            echo $hotel;
                            echo $return;
                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('hoteisprevistos');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'hotel' => $hotel
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error: " . $e;
                                echo $return;
                            } 

                        }
                    }
                }
            }
        }

        //hoteles
        $hoteles = $crucero->item(0)->getElementsByTagName("hoteles");
        if ($hoteles->length > 0) {
            for ($l=0; $l < $hoteles->length; $l++) { 
                $ciudad = $hoteles->item($l)->getElementsByTagName("ciudad");
                if ($ciudad->length > 0) {
                    $ciudad = $ciudad->item(0)->nodeValue;
                } else {
                    $ciudad = "";
                }
                $nombre = $hoteles->item($l)->getElementsByTagName("nombre");
                if ($nombre->length > 0) {
                    $nombre = $nombre->item(0)->nodeValue;
                } else {
                    $nombre = "";
                }
                $categoria = $hoteles->item($l)->getElementsByTagName("categoria");
                if ($categoria->length > 0) {
                    $categoria = $categoria->item(0)->nodeValue;
                } else {
                    $categoria = "";
                }
                echo $return;
                echo $categoria;
                echo $return;

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hoteis');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'ciudad' => $ciudad,
                        'nombre' => $nombre,
                        'categoria' => $categoria
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error: " . $e;
                    echo $return;
                } 

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
