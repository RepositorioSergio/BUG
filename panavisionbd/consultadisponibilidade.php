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

$duracao = array();

$sql = "SELECT duracion FROM descricaocircuitos";
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
        $intervalo = $row->duracion;

        if (in_array($intervalo, $duracao) == false) {
            $duracao[] = $intervalo;
        }

    }
}
sort($duracao);

$count = 0;

$sql = "SELECT idcircuito, idzona, origen FROM circuitos";
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
        $idcircuito = $row->idcircuito;
        $destino = $row->idzona;
        $origen = $row->origen;

        foreach ($duracao as $key => $value) {
            $intervalo = $value;

            $adults = 2;
            $children = 0;
            $jubilados = 0;
            $noivos = 0;
            $ind = 1;
            $dbl = 0;
            $tpl = 0;

            $sql = "SELECT fsalida FROM circuitos_datasaida WHERE idcircuito='$idcircuito'";
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
                    $fsalida = $row->fsalida;

                    $raw = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tms="TmsApi">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <tms:consultaDisponibilidad soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                            <xml xsi:type="xsd:string">?</xml>
                        </tms:consultaDisponibilidad>
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
                    $client->setUri('http://wwwaps.panavision-tours.es/integracionAme/TmsApi?invoke=consultaDisponibilidad&xml=<consulta><origen>' . $origen . '</origen><destino>' . $destino . '</destino><idcircuito>' . $idcircuito . '</idcircuito><fecha_salida>' . $fsalida . '</fecha_salida><intervalo>' . $intervalo . '</intervalo><n_adultos>' . $adults . '</n_adultos><n_jubilados>' . $jubilados . '</n_jubilados><n_ninos>' . $children . '</n_ninos><n_novios>' . $noivos . '</n_novios><n_dbl>' . $dbl . '</n_dbl><n_tpl>' . $tpl . '</n_tpl><n_ind>' . $ind . '</n_ind></consulta>');
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

                    /* echo $return;
                    echo $response;
                    echo $return; */
                    $response = htmlspecialchars_decode($response);
                    /* echo $return;
                    echo $response;
                    echo $return; */
                    $start = strpos($response, "<respuesta>");
                    $end = strpos($response, "</respuesta>");
                    $response = substr($response, $start, $end - $start + 12);

                    $inputDoc = new DOMDocument();
                    $inputDoc->loadXML($response);
                    $alternativa = $inputDoc->getElementsByTagName("alternativa");
                    for ($i=0; $i < $alternativa->length; $i++) { 
                        $idfolleto = $alternativa->item($i)->getElementsByTagName("idfolleto");
                        if ($idfolleto->length > 0) {
                            $idfolleto = $idfolleto->item(0)->nodeValue;
                        } else {
                            $idfolleto = "";
                        }
                        echo $return;
                        echo $idfolleto;
                        echo $return;
                        $idcircuito = $alternativa->item($i)->getElementsByTagName("idcircuito");
                        if ($idcircuito->length > 0) {
                            $idcircuito = $idcircuito->item(0)->nodeValue;
                        } else {
                            $idcircuito = "";
                        }
                        $nombre = $alternativa->item($i)->getElementsByTagName("nombre");
                        if ($nombre->length > 0) {
                            $nombre = $nombre->item(0)->nodeValue;
                        } else {
                            $nombre = "";
                        }
                        $URL_img_detalle = $alternativa->item($i)->getElementsByTagName("URL_img_detalle");
                        if ($URL_img_detalle->length > 0) {
                            $URL_img_detalle = $URL_img_detalle->item(0)->nodeValue;
                        } else {
                            $URL_img_detalle = "";
                        }
                        $texto = $alternativa->item($i)->getElementsByTagName("texto");
                        if ($texto->length > 0) {
                            $texto = $texto->item(0)->nodeValue;
                        } else {
                            $texto = "";
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('desponibilidade_circuitos');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'idfolleto' => $idfolleto,
                                'idcircuito' => $idcircuito,
                                'nombre' => $nombre,
                                'URL_img_detalle' => $URL_img_detalle,
                                'texto' => $texto
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

                        $fecha = $alternativa->item($i)->getElementsByTagName("fecha");
                        if ($fecha->length > 0) {
                            for ($j=0; $j < $fecha->length; $j++) { 
                                $fsalida = $fecha->item($j)->getElementsByTagName("fsalida");
                                if ($fsalida->length > 0) {
                                    $fsalida = $fsalida->item(0)->nodeValue;
                                } else {
                                    $fsalida = "";
                                }
                                echo $return;
                                echo $fsalida;
                                echo $return;
                                $idopcion = $fecha->item($j)->getElementsByTagName("idopcion");
                                if ($idopcion->length > 0) {
                                    $idopcion = $idopcion->item(0)->nodeValue;
                                } else {
                                    $idopcion = "";
                                }
                                $descopcion = $fecha->item($j)->getElementsByTagName("descopcion");
                                if ($descopcion->length > 0) {
                                    $descopcion = $descopcion->item(0)->nodeValue;
                                } else {
                                    $descopcion = "";
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('datas_desponibilidade');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'fsalida' => $fsalida,
                                        'idopcion' => $idopcion,
                                        'descopcion' => $descopcion
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

                                $desglose = $fecha->item($j)->getElementsByTagName("desglose");
                                if ($desglose->length > 0) {
                                    $precio = $desglose->item(0)->getElementsByTagName("precio");
                                    if ($precio->length > 0) {
                                        for ($k=0; $k < $precio->length; $k++) { 
                                            $type = $precio->item($k)->getAttribute("type");
                                            $concepto = $precio->item($k)->getElementsByTagName("concepto");
                                            if ($concepto->length > 0) {
                                                $concepto = $concepto->item(0)->nodeValue;
                                            } else {
                                                $concepto = "";
                                            }
                                            $valor = $precio->item($k)->getElementsByTagName("valor");
                                            if ($valor->length > 0) {
                                                $valor = $valor->item(0)->nodeValue;
                                            } else {
                                                $valor = "";
                                            }
                                            echo $return;
                                            echo $valor;
                                            echo $return;

                                            try {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('preco_data');
                                                $insert->values(array(
                                                    'datetime_created' => time(),
                                                    'datetime_updated' => 0,
                                                    'type' => $type,
                                                    'concepto' => $concepto,
                                                    'valor' => $valor
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

                    }    
                    $count = $count + 1;
                
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
