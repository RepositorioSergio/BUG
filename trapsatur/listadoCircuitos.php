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
$db->getDriver()
    ->getConnection()
    ->disconnect();
$config = new \Zend\Config\Config(include '../config/autoload/global.trapsatur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT codigo FROM folhetos";
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
        echo $return;
        echo "codigo Folheto: " . $codigo;
        echo $return; 
        
        // fazer chamada aqui, tirar o die para fazer o loop
        if ($codigo != "") {
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
             var_dump($client);
            
            $params = array(
                "claveEntidad" => $trapsaturpackagesEntityKey,
                "login" => $trapsaturpackagesLogin,
                "password" => $trapsaturpackagesPassword,
                "idioma" => "es",
                "codigoFolleto" => $codigo
            );
            try {
                $client->__soapCall('listadoCircuitos', array(
                    $params
                ));
            } catch (\Exception $e) {
                 var_dump($e);
                die();
            }
             //var_dump($client);
            $xmlrequest = $client->__getLastRequest();
            $xmlresult = $client->__getLastResponse();
            
            //echo $xmlresult;
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
            $node = $inputDoc->getElementsByTagName("productos");
            $node = $inputDoc->getElementsByTagName("multiRef");
            
            for ($j=0; $j < $node->length; $j++) { 
                $codigo = $node->item($j)->getElementsByTagName('codigo');
                if ($codigo->length > 0) {
                    $codigo = $codigo->item(0)->nodeValue;
                } else {
                    $codigo = "";
                }
                echo $return;
                echo "codigo: " . $codigo;
                echo $return;
                $descripcion = $node->item($j)->getElementsByTagName('descripcion');
                if ($descripcion->length > 0) {
                    $descripcion = $descripcion->item(0)->nodeValue;
                } else {
                    $descripcion = "";
                }
                $serie = $node->item($j)->getElementsByTagName('serie');
                if ($serie->length > 0) {
                    $serie = $serie->item(0)->nodeValue;
                } else {
                    $serie = "";
                }
                $codigoPadre = $node->item($j)->getElementsByTagName('codigoPadre');
                if ($codigoPadre->length > 0) {
                    $codigoPadre = $codigoPadre->item(0)->nodeValue;
                } else {
                    $codigoPadre = "";
                }
                $codigoFolleto = $node->item($j)->getElementsByTagName('codigoFolleto');
                if ($codigoFolleto->length > 0) {
                    $codigoFolleto = $codigoFolleto->item(0)->nodeValue;
                } else {
                    $codigoFolleto = "";
                }
                $descripcionFolleto = $node->item($j)->getElementsByTagName('descripcionFolleto');
                if ($descriptionFolleto->length > 0) {
                    $descripcionFolleto = $descripcionFolleto->item(0)->nodeValue;
                } else {
                    $descripcionFolleto = "";
                }
                $periodoFolleto = $node->item($j)->getElementsByTagName('periodoFolleto');
                if ($periodoFolleto->length > 0) {
                    $periodoFolleto = $periodoFolleto->item(0)->nodeValue;
                } else {
                    $periodoFolleto = "";
                }
                $codigoSubfolleto = $node->item($j)->getElementsByTagName('codigoSubfolleto');
                if ($codigoSubfolleto->length > 0) {
                    $codigoSubfolleto = $codigoSubfolleto->item(0)->nodeValue;
                } else {
                    $codigoSubfolleto = "";
                }
                $codigoSubfolleto = $node->item($j)->getElementsByTagName('codigoSubfolleto');
                if ($codigoSubfolleto->length > 0) {
                    $codigoSubfolleto = $codigoSubfolleto->item(0)->nodeValue;
                } else {
                    $codigoSubfolleto = "";
                }
                $codigoSubfolleto = $node->item($j)->getElementsByTagName('codigoSubfolleto');
                if ($codigoSubfolleto->length > 0) {
                    $codigoSubfolleto = $codigoSubfolleto->item(0)->nodeValue;
                } else {
                    $codigoSubfolleto = "";
                }
                $codigoSubfolleto = $node->item($j)->getElementsByTagName('codigoSubfolleto');
                if ($codigoSubfolleto->length > 0) {
                    $codigoSubfolleto = $codigoSubfolleto->item(0)->nodeValue;
                } else {
                    $codigoSubfolleto = "";
                }
                $descripcionSubfolleto = $node->item($j)->getElementsByTagName('descripcionSubfolleto');
                if ($descripcionSubfolleto->length > 0) {
                    $descripcionSubfolleto = $descripcionSubfolleto->item(0)->nodeValue;
                } else {
                    $descripcionSubfolleto = "";
                }
                $zona = $node->item($j)->getElementsByTagName('zona');
                if ($zona->length > 0) {
                    $zona = $zona->item(0)->nodeValue;
                } else {
                    $zona = "";
                }
                $duracion = $node->item($j)->getElementsByTagName('duracion');
                if ($duracion->length > 0) {
                    $duracion = $duracion->item(0)->nodeValue;
                } else {
                    $duracion = "";
                }
                $textoIncluye = $node->item($j)->getElementsByTagName('textoIncluye');
                if ($textoIncluye->length > 0) {
                    $textoIncluye = $textoIncluye->item(0)->nodeValue;
                } else {
                    $textoIncluye = "";
                }
                $textoGeneral = $node->item($j)->getElementsByTagName('textoGeneral');
                if ($textoGeneral->length > 0) {
                    $textoGeneral = $textoGeneral->item(0)->nodeValue;
                } else {
                    $textoGeneral = "";
                }
                $urlFotografia = $node->item($j)->getElementsByTagName('urlFotografia');
                if ($urlFotografia->length > 0) {
                    $urlFotografia = $urlFotografia->item(0)->nodeValue;
                } else {
                    $urlFotografia = "";
                }
                $usrSeguro = $node->item($j)->getElementsByTagName('usrSeguro');
                if ($usrSeguro->length > 0) {
                    $usrSeguro = $usrSeguro->item(0)->nodeValue;
                } else {
                    $usrSeguro = "";
                }
                $urlMapa = $node->item($j)->getElementsByTagName('urlMapa');
                if ($urlMapa->length > 0) {
                    $urlMapa = $urlMapa->item(0)->nodeValue;
                } else {
                    $urlMapa = "";
                }
                $pasaporteRequerido = $node->item($j)->getElementsByTagName('pasaporteRequerido');
                if ($pasaporteRequerido->length > 0) {
                    $pasaporteRequerido = $pasaporteRequerido->item(0)->nodeValue;
                } else {
                    $pasaporteRequerido = "";
                }
                $precio = $node->item($j)->getElementsByTagName('precio');
                if ($precio->length > 0) {
                    $precio = $precio->item(0)->nodeValue;
                } else {
                    $precio = "";
                }
                $codigoCiudadOrigen = $node->item($j)->getElementsByTagName('codigoCiudadOrigen');
                if ($codigoCiudadOrigen->length > 0) {
                    $codigoCiudadOrigen = $codigoCiudadOrigen->item(0)->nodeValue;
                } else {
                    $codigoCiudadOrigen = "";
                }
                $descCiudadOrigen = $node->item($j)->getElementsByTagName('descCiudadOrigen');
                if ($descCiudadOrigen->length > 0) {
                    $descCiudadOrigen = $descCiudadOrigen->item(0)->nodeValue;
                } else {
                    $descCiudadOrigen = "";
                }
                $codigoCiudadDestino = $node->item($j)->getElementsByTagName('codigoCiudadDestino');
                if ($codigoCiudadDestino->length > 0) {
                    $codigoCiudadDestino = $codigoCiudadDestino->item(0)->nodeValue;
                } else {
                    $codigoCiudadDestino = "";
                }
                $descCiudadDestino = $node->item($j)->getElementsByTagName('descCiudadDestino');
                if ($descCiudadDestino->length > 0) {
                    $descCiudadDestino = $descCiudadDestino->item(0)->nodeValue;
                } else {
                    $descCiudadDestino = "";
                }
                $regBasico = $node->item($j)->getElementsByTagName('regBasico');
                if ($regBasico->length > 0) {
                    $regBasico = $regBasico->item(0)->nodeValue;
                } else {
                    $regBasico = "";
                }
                $regBasicoDescr = $node->item($j)->getElementsByTagName('regBasicoDescr');
                if ($regBasicoDescr->length > 0) {
                    $regBasicoDescr = $regBasicoDescr->item(0)->nodeValue;
                } else {
                    $regBasicoDescr = "";
                }
                $tasas = $node->item($j)->getElementsByTagName('tasas');
                if ($tasas->length > 0) {
                    $tasas = $tasas->item(0)->nodeValue;
                } else {
                    $tasas = "";
                }
                $moneda = $node->item($j)->getElementsByTagName('moneda');
                if ($moneda->length > 0) {
                    $moneda = $moneda->item(0)->nodeValue;
                } else {
                    $moneda = "";
                }
                $infoCircuito = $node->item($j)->getElementsByTagName('infoCircuito');
                if ($infoCircuito->length > 0) {
                    $infoCircuito = $infoCircuito->item(0)->nodeValue;
                } else {
                    $infoCircuito = "";
                }
                $opcionConSinAvion = $node->item($j)->getElementsByTagName('opcionConSinAvion');
                if ($opcionConSinAvion->length > 0) {
                    $opcionConSinAvion = $opcionConSinAvion->item(0)->nodeValue;
                } else {
                    $opcionConSinAvion = "";
                }
                $opcionNochesAdicionales = $node->item($j)->getElementsByTagName('opcionNochesAdicionales');
                if ($opcionNochesAdicionales->length > 0) {
                    $opcionNochesAdicionales = $opcionNochesAdicionales->item(0)->nodeValue;
                } else {
                    $opcionNochesAdicionales = "";
                }
                $tipoProducto = $node->item($j)->getElementsByTagName('tipoProducto');
                if ($tipoProducto->length > 0) {
                    $tipoProducto = $tipoProducto->item(0)->nodeValue;
                } else {
                    $tipoProducto = "";
                }
            
                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('productos');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'codigo' => $codigo,
                        'descricao' => $descripcion,
                        'serie' => $serie,
                        'codigoPai' => $codigoPadre,
                        'codigoFolheto' => $codigoFolleto,
                        'descricaoFolheto' => $descripcionFolleto,
                        'periodoFolheto' => $periodoFolleto,
                        'codigoSubFolheto' => $codigoSubfolleto,
                        'descricaoSubFolheto' => $descripcionSubfolleto,
                        'Zona' => $zona,
                        'duracao' => $duracion,
                        'textoIncluye' => $textoIncluye,
                        'textoGeral' => $textoGeneral,
                        'urlFotografia' => $urlFotografia,
                        'usrSeguro' => $usrSeguro,
                        'urlMapa' => $urlMapa,
                        'passaporte' => $pasaporteRequerido,
                        'precio' => $precio,
                        'codigoCidadeOrigem' => $codigoCiudadOrigen,
                        'descricaoCidadeOrigem' => $descCiudadOrigen,
                        'codigoCidadeDestino' => $codigoCiudadDestino,
                        'descCidadeDestino' => $descCiudadDestino,
                        'regBasico' => $regBasico,
                        'regBasicoDescricao' => $regBasicoDescr,
                        'taxas' => $tasas,
                        'moeda' => $moneda,
                        'infoCircuito' => $infoCircuito,
                        'opcaoComSemAviao' => $opcionConSinAvion,
                        'opcaoNoitesAdicionais' => $opcionNochesAdicionales,
                        'tipoProducto' => $tipoProducto
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
                echo $return;
                echo "PASSOU PRODUCTOS ";
                echo $return;
            
            
                $tramos = $node->item($j)->getElementsByTagName('tramos');
                if ($tramos->length > 0) {
                    for ($l=0; $l < $tramos->length; $l++) { 
                        $orden = $tramos->item($l)->getElementsByTagName('orden');
                        if ($orden->length > 0) {
                            $orden = $orden->item(0)->nodeValue;
                        } else {
                            $orden = "";
                        }
                        echo $return;
                        echo "orden: " . $orden;
                        echo $return;
                        $codigoTramo = $tramos->item($l)->getElementsByTagName('codigoTramo');
                        if ($codigoTramo->length > 0) {
                            $codigoTramo = $codigoTramo->item(0)->nodeValue;
                        } else {
                            $codigoTramo = "";
                        }
                        $descripcionTramo = $tramos->item($l)->getElementsByTagName('descripcionTramo');
                        if ($descripcionTramo->length > 0) {
                            $descripcionTramo = $descripcionTramo->item(0)->nodeValue;
                        } else {
                            $descripcionTramo = "";
                        }
                        $codigoCiudadOrigen = $tramos->item($l)->getElementsByTagName('codigoCiudadOrigen');
                        if ($codigoCiudadOrigen->length > 0) {
                            $codigoCiudadOrigen = $codigoCiudadOrigen->item(0)->nodeValue;
                        } else {
                            $codigoCiudadOrigen = "";
                        }
                        $descCiudadOrigen = $tramos->item($l)->getElementsByTagName('descCiudadOrigen');
                        if ($descCiudadOrigen->length > 0) {
                            $descCiudadOrigen = $descCiudadOrigen->item(0)->nodeValue;
                        } else {
                            $descCiudadOrigen = "";
                        }
                        $codigoCiudadDestino = $tramos->item($l)->getElementsByTagName('codigoCiudadDestino');
                        if ($codigoCiudadDestino->length > 0) {
                            $codigoCiudadDestino = $codigoCiudadDestino->item(0)->nodeValue;
                        } else {
                            $codigoCiudadDestino = "";
                        }
                        $descCiudadDestino = $tramos->item($l)->getElementsByTagName('descCiudadDestino');
                        if ($descCiudadDestino->length > 0) {
                            $descCiudadDestino = $descCiudadDestino->item(0)->nodeValue;
                        } else {
                            $descCiudadDestino = "";
                        }
                        $duracionDias = $tramos->item($l)->getElementsByTagName('duracionDias');
                        if ($duracionDias->length > 0) {
                            $duracionDias = $duracionDias->item(0)->nodeValue;
                        } else {
                            $duracionDias = "";
                        }
                        $diferenciaDiasBasico = $tramos->item($l)->getElementsByTagName('diferenciaDiasBasico');
                        if ($diferenciaDiasBasico->length > 0) {
                            $diferenciaDiasBasico = $diferenciaDiasBasico->item(0)->nodeValue;
                        } else {
                            $diferenciaDiasBasico = "";
                        }
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('tramos');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'ordem' => $orden,
                                'codigoTramo' => $codigoTramo,
                                'descricaoTramo' => $descripcionTramo,
                                'codigoCidadeOrigem' => $codigoCiudadOrigen,
                                'descCidadeOrigem' => $descCiudadOrigen,
                                'codigoCidadeDestino' => $codigoCiudadDestino,
                                'descCidadeDestino' => $descCiudadDestino,
                                'duracao' => $duracionDias,
                                'diferenca' => $diferenciaDiasBasico
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
                
                        $ciudades = $tramos->item($l)->getElementsByTagName('ciudades');
                        for ($lAux=0; $lAux < $ciudades->length; $lAux++) { 
                            $codigo = $ciudades->item($lAux)->getElementsByTagName('codigo');
                            if ($codigo->length > 0) {
                                $codigo = $codigo->item(0)->nodeValue;
                            } else {
                                $codigo = "";
                            }
                            echo $return;
                            echo "codigo: " . $codigo;
                            echo $return;
                            $nombre = $ciudades->item($lAux)->getElementsByTagName('nombre');
                            if ($nombre->length > 0) {
                                $nombre = $nombre->item(0)->nodeValue;
                            } else {
                                $nombre = "";
                            }
                            $codigoPais = $ciudades->item($lAux)->getElementsByTagName('codigoPais');
                            if ($codigoPais->length > 0) {
                                $codigoPais = $codigoPais->item(0)->nodeValue;
                            } else {
                                $codigoPais = "";
                            }
                            $nombrePais = $ciudades->item($lAux)->getElementsByTagName('nombrePais');
                            if ($nombrePais->length > 0) {
                                $nombrePais = $nombrePais->item(0)->nodeValue;
                            } else {
                                $nombrePais = "";
                            }
                            $dayNumber = $ciudades->item($lAux)->getElementsByTagName('dayNumber');
                            if ($dayNumber->length > 0) {
                                $dayNumber = $dayNumber->item(0)->nodeValue;
                            } else {
                                $dayNumber = "";
                            }
                
                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('tramos_cidades');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'codigo' => $codigo,
                                    'nome' => $nombre,
                                    'codigoPais' => $codigoPais,
                                    'nomePais' => $nombrePais,
                                    'dia' => $dayNumber
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
                
                        $hotelesCircuito = $tramos->item($l)->getElementsByTagName('hotelesCircuito');
                        for ($lAux2=0; $lAux2 < $hotelesCircuito->length; $lAux2++) { 
                            $ciudad = $hotelesCircuito->item($lAux2)->getElementsByTagName('ciudad');
                            if ($ciudad->length > 0) {
                                $ciudad = $ciudad->item(0)->nodeValue;
                            } else {
                                $ciudad = "";
                            }
                            echo $return;
                            echo "ciudad: " . $ciudad;
                            echo $return;
                            $hotel = $hotelesCircuito->item($lAux2)->getElementsByTagName('hotel');
                            if ($hotel->length > 0) {
                                $hotel = $hotel->item(0)->nodeValue;
                            } else {
                                $hotel = "";
                            }
                            $categoria = $hotelesCircuito->item($lAux2)->getElementsByTagName('categoria');
                            if ($categoria->length > 0) {
                                $categoria = $categoria->item(0)->nodeValue;
                            } else {
                                $categoria = "";
                            }
                            $claseCircuito = $hotelesCircuito->item($lAux2)->getElementsByTagName('claseCircuito');
                            if ($claseCircuito->length > 0) {
                                $claseCircuito = $claseCircuito->item(0)->nodeValue;
                            } else {
                                $claseCircuito = "";
                            }
                            $codigoCiudad = $hotelesCircuito->item($lAux2)->getElementsByTagName('codigoCiudad');
                            if ($codigoCiudad->length > 0) {
                                $codigoCiudad = $codigoCiudad->item(0)->nodeValue;
                            } else {
                                $codigoCiudad = "";
                            }
                
                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('tramos_hoteis');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'cidade' => $ciudad,
                                    'hotel' => $hotel,
                                    'categoria' => $categoria,
                                    'classeCircuito' => $claseCircuito,
                                    'codigoCidade' => $codigoCiudad
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
                }
                echo $return;
                echo "PASSOU TRAMOS ";
                echo $return;
            
                $categorias = $node->item($j)->getElementsByTagName('categorias');
                if ($categorias->length > 0) {
                    for ($m=0; $m < $categorias->length; $m++) { 
                        $identificador = $categorias->item($m)->getElementsByTagName('identificador');
                        if ($identificador->length > 0) {
                            $identificador = $identificador->item(0)->nodeValue;
                        } else {
                            $identificador = "";
                        }
                        echo $return;
                        echo "identificador: " . $identificador;
                        echo $return;
                        $descripcion = $categorias->item($m)->getElementsByTagName('descripcion');
                        if ($descripcion->length > 0) {
                            $descripcion = $descripcion->item(0)->nodeValue;
                        } else {
                            $descripcion = "";
                        }
                        $precio = $categorias->item($m)->getElementsByTagName('precio');
                        if ($precio->length > 0) {
                            $identificador = $precio->item(0)->nodeValue;
                        } else {
                            $precio = "";
                        }
                        $aclaraPrecio = $categorias->item($m)->getElementsByTagName('aclaraPrecio');
                        if ($aclaraPrecio->length > 0) {
                            $aclaraPrecio = $aclaraPrecio->item(0)->nodeValue;
                        } else {
                            $aclaraPrecio = "";
                        }
                        $percentDiscount3Pax = $categorias->item($m)->getElementsByTagName('percentDiscount3Pax');
                        if ($percentDiscount3Pax->length > 0) {
                            $percentDiscount3Pax = $percentDiscount3Pax->item(0)->nodeValue;
                        } else {
                            $percentDiscount3Pax = "";
                        }
                        $percentDiscountChild = $categorias->item($m)->getElementsByTagName('percentDiscountChild');
                        if ($percentDiscountChild->length > 0) {
                            $percentDiscountChild = $percentDiscountChild->item(0)->nodeValue;
                        } else {
                            $percentDiscountChild = "";
                        }
                        $percentDiscountChild_0to3 = $categorias->item($m)->getElementsByTagName('percentDiscountChild_0to3');
                        if ($percentDiscountChild_0to3->length > 0) {
                            $percentDiscountChild_0to3 = $percentDiscountChild_0to3->item(0)->nodeValue;
                        } else {
                            $percentDiscountChild_0to3 = "";
                        }
                        $percentDiscountChild_8to17 = $categorias->item($m)->getElementsByTagName('percentDiscountChild_8to17');
                        if ($percentDiscountChild_8to17->length > 0) {
                            $percentDiscountChild_8to17 = $percentDiscountChild_8to17->item(0)->nodeValue;
                        } else {
                            $percentDiscountChild_8to17 = "";
                        }
                        $percentDiscountMoreThan65 = $categorias->item($m)->getElementsByTagName('percentDiscountMoreThan65');
                        if ($percentDiscountMoreThan65->length > 0) {
                            $percentDiscountMoreThan65 = $percentDiscountMoreThan65->item(0)->nodeValue;
                        } else {
                            $percentDiscountMoreThan65 = "";
                        }
                        $lugarSalida = $categorias->item($m)->getElementsByTagName('lugarSalida');
                        if ($lugarSalida->length > 0) {
                            $lugarSalida = $lugarSalida->item(0)->nodeValue;
                        } else {
                            $lugarSalida = "";
                        }
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('categorias');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'identificador' => $identificador,
                                'descricao' => $descripcion,
                                'preco' => $precio,
                                'observacoes' => $aclaraPrecio,
                                'desconto3Pax' => $percentDiscount3Pax,
                                'descontoCrianca' => $percentDiscountChild,
                                'descontoCrianca0to3' => $percentDiscountChild_0to3,
                                'descontoCrianca8to17' => $percentDiscountChild_8to17,
                                'descontoMaiores65' => $percentDiscountMoreThan65,
                                'lugarSaida' => $lugarSalida
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
                echo $return;
                echo "PASSOU CATEGORIAS ";
                echo $return;
            
                $fechas = $node->item($j)->getElementsByTagName('fechas');
                if ($fechas->length > 0) {
                    for ($m=0; $m < $fechas->length; $m++) { 
                        $fecha = $fechas->item($m)->getElementsByTagName('fecha');
                        if ($fecha->length > 0) {
                            $fecha = $fecha->item(0)->nodeValue;
                        } else {
                            $fecha = "";
                        }
                        echo $return;
                        echo "fecha: " . $fecha;
                        echo $return;
                        $categoria = $fechas->item($m)->getElementsByTagName('categoria');
                        if ($categoria->length > 0) {
                            $categoria = $categoria->item(0)->nodeValue;
                        } else {
                            $categoria = "";
                        }
                        $cupo = $fechas->item($m)->getElementsByTagName('cupo');
                        if ($cupo->length > 0) {
                            $cupo = $cupo->item(0)->nodeValue;
                        } else {
                            $cupo = "";
                        }
                        $suplemento = $fechas->item($m)->getElementsByTagName('suplemento');
                        if ($suplemento->length > 0) {
                            $suplemento = $suplemento->item(0)->nodeValue;
                        } else {
                            $suplemento = "";
                        }
                        $descripcion = $fechas->item($m)->getElementsByTagName('descripcion');
                        if ($descripcion->length > 0) {
                            $descripcion = $descripcion->item(0)->nodeValue;
                        } else {
                            $descripcion = "";
                        }
                        $codigoTramo = $fechas->item($m)->getElementsByTagName('codigoTramo');
                        if ($codigoTramo->length > 0) {
                            $codigoTramo = $codigoTramo->item(0)->nodeValue;
                        } else {
                            $codigoTramo = "";
                        }
                        $descripcionTramo = $fechas->item($m)->getElementsByTagName('descripcionTramo');
                        if ($descripcionTramo->length > 0) {
                            $descripcionTramo = $descripcionTramo->item(0)->nodeValue;
                        } else {
                            $descripcionTramo = "";
                        }
                        $temporada = $fechas->item($m)->getElementsByTagName('temporada');
                        if ($temporada->length > 0) {
                            $temporada = $temporada->item(0)->nodeValue;
                        } else {
                            $temporada = "";
                        }
                        $colorTemporada = $fechas->item($m)->getElementsByTagName('colorTemporada');
                        if ($colorTemporada->length > 0) {
                            $colorTemporada = $colorTemporada->item(0)->nodeValue;
                        } else {
                            $colorTemporada = "";
                        }
                        $fechaCerrada = $fechas->item($m)->getElementsByTagName('fechaCerrada');
                        if ($fechaCerrada->length > 0) {
                            $fechaCerrada = $fechaCerrada->item(0)->nodeValue;
                        } else {
                            $fechaCerrada = "";
                        }
                        $motivoCierre = $fechas->item($m)->getElementsByTagName('motivoCierre');
                        if ($motivoCierre->length > 0) {
                            $motivoCierre = $motivoCierre->item(0)->nodeValue;
                        } else {
                            $motivoCierre = "";
                        }
                        $porcentajeComision = $fechas->item($m)->getElementsByTagName('porcentajeComision');
                        if ($porcentajeComision->length > 0) {
                            $porcentajeComision = $porcentajeComision->item(0)->nodeValue;
                        } else {
                            $porcentajeComision = "";
                        }
                
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('tramos_datas');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'data' => $fecha,
                                'categoria' => $categoria,
                                'cupo' => $cupo,
                                'suplemento' => $suplemento,
                                'descricao' => $descripcion,
                                'codigoTramo' => $codigoTramo,
                                'descTramo' => $descripcionTramo,
                                'temporada' => $temporada,
                                'corTemporada' => $colorTemporada,
                                'dataEncerramento' => $fechaCerrada,
                                'motivoEncerramento' => $motivoCierre,
                                'comissao' => $porcentajeComision
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
                
                
                        $trasladosCircuito = $fechas->item($m)->getElementsByTagName('trasladosCircuito');
                        for ($mAux=0; $mAux < $trasladosCircuito->length; $mAux++) { 
                            $codigoCiudad = $trasladosCircuito->item($mAux)->getElementsByTagName('codigoCiudad');
                            if ($codigoCiudad->length > 0) {
                                $codigoCiudad = $codigoCiudad->item(0)->nodeValue;
                            } else {
                                $codigoCiudad = "";
                            }
                            echo $return;
                            echo "codigoCiudad: " . $codigoCiudad;
                            echo $return;
                            $nombreCiudad = $trasladosCircuito->item($mAux)->getElementsByTagName('nombreCiudad');
                            if ($nombreCiudad->length > 0) {
                                $nombreCiudad = $nombreCiudad->item(0)->nodeValue;
                            } else {
                                $nombreCiudad = "";
                            }
                            $fecha = $trasladosCircuito->item($mAux)->getElementsByTagName('fecha');
                            if ($fecha->length > 0) {
                                $fecha = $fecha->item(0)->nodeValue;
                            } else {
                                $fecha = "";
                            }
                            $tipoTraslado = $trasladosCircuito->item($mAux)->getElementsByTagName('tipoTraslado');
                            if ($tipoTraslado->length > 0) {
                                $tipoTraslado = $tipoTraslado->item(0)->nodeValue;
                            } else {
                                $tipoTraslado = "";
                            }
                            $codigoServicio = $trasladosCircuito->item($mAux)->getElementsByTagName('codigoServicio');
                            if ($codigoServicio->length > 0) {
                                $codigoServicio = $codigoServicio->item(0)->nodeValue;
                            } else {
                                $codigoServicio = "";
                            }
                            $nombreServicio = $trasladosCircuito->item($mAux)->getElementsByTagName('nombreServicio');
                            if ($nombreServicio->length > 0) {
                                $nombreServicio = $nombreServicio->item(0)->nodeValue;
                            } else {
                                $nombreServicio = "";
                            }
                
                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('tramos_datas_trasladosCircuitos');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'codigoCidade' => $codigo,
                                    'nomeCidade' => $descripcion,
                                    'data' => $fechaDesde,
                                    'tipoTraslado' => $fechaHasta,
                                    'codigoServico' => $descripcionPeriodo,
                                    'nomeServico' => $subtitulo
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
                }
                
                echo $return;
                echo "PASSOU TRAMOS ";
                echo $return;
            
            
                $temporadas = $node->item($j)->getElementsByTagName('temporadas');
                if ($temporadas->length > 0) {
                    for ($m=0; $m < $temporadas->length; $m++) { 
                        $codigo = $temporadas->item($m)->getElementsByTagName('codigo');
                        if ($codigo->length > 0) {
                            $codigo = $codigo->item(0)->nodeValue;
                        } else {
                            $codigo = "";
                        }
                        echo $return;
                        echo "codigo: " . $codigo;
                        echo $return;
                        $categoria = $temporadas->item($m)->getElementsByTagName('categoria');
                        if ($categoria->length > 0) {
                            $categoria = $categoria->item(0)->nodeValue;
                        } else {
                            $categoria = "";
                        }
                        $suplemento = $temporadas->item($m)->getElementsByTagName('suplemento');
                        if ($suplemento->length > 0) {
                            $suplemento = $suplemento->item(0)->nodeValue;
                        } else {
                            $suplemento = "";
                        }
                        $descripcion = $temporadas->item($m)->getElementsByTagName('descripcion');
                        if ($descripcion->length > 0) {
                            $descripcion = $descripcion->item(0)->nodeValue;
                        } else {
                            $descripcion = "";
                        }
                        $colorTemporada = $temporadas->item($m)->getElementsByTagName('colorTemporada');
                        if ($colorTemporada->length > 0) {
                            $colorTemporada = $colorTemporada->item(0)->nodeValue;
                        } else {
                            $colorTemporada = "";
                        }
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('temporadas');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'codigo' => $codigo,
                                'categoria' => $categoria,
                                'suplemento' => $suplemento,
                                'descricao' => $descripcion,
                                'corTemporada' => $colorTemporada
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
                
                echo $return;
                echo "PASSOU TEMPORADAS ";
                echo $return;
            
            
                $regimenes = $node->item($j)->getElementsByTagName('regimenes');
                if ($regimenes->length > 0) {
                    for ($m=0; $m < $regimenes->length; $m++) { 
                        $codigoRegimen = $regimenes->item($m)->getElementsByTagName('codigoRegimen');
                        if ($codigoRegimen->length > 0) {
                            $codigoRegimen = $codigoRegimen->item(0)->nodeValue;
                        } else {
                            $codigoRegimen = "";
                        }
                        echo $return;
                        echo "codigoRegimen: " . $codigoRegimen;
                        echo $return;
                        $categoria = $regimenes->item($m)->getElementsByTagName('categoria');
                        if ($categoria->length > 0) {
                            $categoria = $categoria->item(0)->nodeValue;
                        } else {
                            $categoria = "";
                        }
                        $suplemento = $regimenes->item($m)->getElementsByTagName('suplemento');
                        if ($suplemento->length > 0) {
                            $suplemento = $suplemento->item(0)->nodeValue;
                        } else {
                            $suplemento = "";
                        }
                        $descripcion = $regimenes->item($m)->getElementsByTagName('descripcion');
                        if ($descripcion->length > 0) {
                            $descripcion = $descripcion->item(0)->nodeValue;
                        } else {
                            $descripcion = "";
                        }
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('regimenes');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'codigo' => $codigoRegimen,
                                'categoria' => $categoria,
                                'suplemento' => $suplemento,
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
                }
                
                echo $return;
                echo "PASSOU REGIMENES ";
                echo $return;
            
                $tiposHabitacion = $node->item($j)->getElementsByTagName('tiposHabitacion');
                if ($tiposHabitacion->length > 0) {
                    for ($m=0; $m < $tiposHabitacion->length; $m++) { 
                        $codigo = $tiposHabitacion->item($m)->getElementsByTagName('codigo');
                        if ($codigo->length > 0) {
                            $codigo = $codigo->item(0)->nodeValue;
                        } else {
                            $codigo = "";
                        }
                        echo $return;
                        echo "codigo: " . $codigo;
                        echo $return;
                        $categoria = $tiposHabitacion->item($m)->getElementsByTagName('categoria');
                        if ($categoria->length > 0) {
                            $categoria = $categoria->item(0)->nodeValue;
                        } else {
                            $categoria = "";
                        }
                        $suplemento = $tiposHabitacion->item($m)->getElementsByTagName('suplemento');
                        if ($suplemento->length > 0) {
                            $suplemento = $suplemento->item(0)->nodeValue;
                        } else {
                            $suplemento = "";
                        }
                        $descripcion = $tiposHabitacion->item($m)->getElementsByTagName('descripcion');
                        if ($descripcion->length > 0) {
                            $descripcion = $descripcion->item(0)->nodeValue;
                        } else {
                            $descripcion = "";
                        }
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('tiposhabitacao');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'codigo' => $codigo,
                                'categoria' => $categoria,
                                'suplemento' => $suplemento,
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
                }
                
                echo $return;
                echo "PASSOU HABITACAO ";
                echo $return;
            
                $idioma = $node->item($j)->getElementsByTagName('idioma');
                if ($idioma->length > 0) {
                    for ($m=0; $m < $idioma->length; $m++) { 
                        $codigo = $idioma->item($m)->getElementsByTagName('codigo');
                        if ($codigo->length > 0) {
                            $codigo = $codigo->item(0)->nodeValue;
                        } else {
                            $codigo = "";
                        }
                        echo $return;
                        echo "codigo: " . $codigo;
                        echo $return;
                        $descripcion = $idioma->item($m)->getElementsByTagName('descripcion');
                        if ($descripcion->length > 0) {
                            $descripcion = $descripcion->item(0)->nodeValue;
                        } else {
                            $descripcion = "";
                        }
                        $minimoPax = $idioma->item($m)->getElementsByTagName('minimoPax');
                        if ($minimoPax->length > 0) {
                            $minimoPax = $minimoPax->item(0)->nodeValue;
                        } else {
                            $minimoPax = "";
                        }
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('idiomas');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'codigo' => $codigo,
                                'descricao' => $descripcion,
                                'minimoPax' => $fechaDesde
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
                
                echo $return;
                echo "PASSOU IDIOMA ";
                echo $return;
            
                $ciudades = $node->item($j)->getElementsByTagName('ciudades');
                if ($ciudades->length > 0) {
                    for ($m=0; $m < $ciudades->length; $m++) { 
                        $codigo = $ciudades->item($m)->getElementsByTagName('codigo');
                        if ($codigo->length > 0) {
                            $codigo = $codigo->item(0)->nodeValue;
                        } else {
                            $codigo = "";
                        }
                        echo $return;
                        echo "codigo: " . $codigo;
                        echo $return;
                        $nombre = $ciudades->item($m)->getElementsByTagName('nombre');
                        if ($nombre->length > 0) {
                            $nombre = $nombre->item(0)->nodeValue;
                        } else {
                            $nombre = "";
                        }
                        $codigoPais = $ciudades->item($m)->getElementsByTagName('codigoPais');
                        if ($codigoPais->length > 0) {
                            $codigoPais = $codigoPais->item(0)->nodeValue;
                        } else {
                            $codigoPais = "";
                        }
                        $nombrePais = $ciudades->item($m)->getElementsByTagName('nombrePais');
                        if ($nombrePais->length > 0) {
                            $nombrePais = $nombrePais->item(0)->nodeValue;
                        } else {
                            $nombrePais = "";
                        }
                        $dayNumber = $ciudades->item($m)->getElementsByTagName('dayNumber');
                        if ($dayNumber->length > 0) {
                            $dayNumber = $dayNumber->item(0)->nodeValue;
                        } else {
                            $dayNumber = "";
                        }
                        $salidaCircuitos = $ciudades->item($m)->getElementsByTagName('salidaCircuitos');
                        if ($salidaCircuitos->length > 0) {
                            $salidaCircuitos = $salidaCircuitos->item(0)->nodeValue;
                        } else {
                            $salidaCircuitos = "";
                        }
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('productos_cidades');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'codigo' => $codigo,
                                'codigoPais' => $codigoPais,
                                'nome' => $nombre,
                                'nomePais' => $nombrePais,
                                'dia' => $dayNumber,
                                'saidaCircuitos' => $salidaCircuitos
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
                
                echo $return;
                echo "PASSOU PROD CIDADES ";
                echo $return;
            
                $hotelesCircuito = $node->item($j)->getElementsByTagName('hotelesCircuito');
                if ($hotelesCircuito->length > 0) {
                    for ($m=0; $m < $hotelesCircuito->length; $m++) { 
                        $ciudad = $hotelesCircuito->item($m)->getElementsByTagName('ciudad');
                        if ($ciudad->length > 0) {
                            $ciudad = $ciudad->item(0)->nodeValue;
                        } else {
                            $ciudad = "";
                        }
                        echo $return;
                        echo "ciudad: " . $ciudad;
                        echo $return;
                        $hotel = $hotelesCircuito->item($m)->getElementsByTagName('hotel');
                        if ($hotel->length > 0) {
                            $hotel = $hotel->item(0)->nodeValue;
                        } else {
                            $hotel = "";
                        }
                        $categoria = $hotelesCircuito->item($m)->getElementsByTagName('categoria');
                        if ($categoria->length > 0) {
                            $categoria = $categoria->item(0)->nodeValue;
                        } else {
                            $categoria = "";
                        }
                        $claseCircuito = $hotelesCircuito->item($m)->getElementsByTagName('claseCircuito');
                        if ($claseCircuito->length > 0) {
                            $claseCircuito = $claseCircuito->item(0)->nodeValue;
                        } else {
                            $claseCircuito = "";
                        }
                        $codigoCiudad = $hotelesCircuito->item($m)->getElementsByTagName('codigoCiudad');
                        if ($codigoCiudad->length > 0) {
                            $codigoCiudad = $codigoCiudad->item(0)->nodeValue;
                        } else {
                            $codigoCiudad = "";
                        }
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hoteis');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'cidade' => $codigo,
                                'nomehotel' => $hotel,
                                'categoria' => $categoria,
                                'classeCircuito' => $claseCircuito,
                                'codigoCidade' => $codigoCiudad
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
                
                echo $return;
                echo "PASSOU HOTEIS ";
                echo $return;
            
                $terminalesTte = $node->item($j)->getElementsByTagName('terminalesTte');
                if ($terminalesTte->length > 0) {
                    for ($m=0; $m < $terminalesTte->length; $m++) { 
                        $codigo = $terminalesTte->item($m)->getElementsByTagName('codigo');
                        if ($codigo->length > 0) {
                            $codigo = $codigo->item(0)->nodeValue;
                        } else {
                            $codigo = "";
                        }
                        echo $return;
                        echo "codigo: " . $codigo;
                        echo $return;
                        $descripcion = $terminalesTte->item($m)->getElementsByTagName('descripcion');
                        if ($descripcion->length > 0) {
                            $descripcion = $descripcion->item(0)->nodeValue;
                        } else {
                            $descripcion = "";
                        }
                        $codigoCiudad = $terminalesTte->item($m)->getElementsByTagName('codigoCiudad');
                        if ($codigoCiudad->length > 0) {
                            $codigoCiudad = $codigoCiudad->item(0)->nodeValue;
                        } else {
                            $codigoCiudad = "";
                        }
                        $descripcionCiudad = $terminalesTte->item($m)->getElementsByTagName('descripcionCiudad');
                        if ($descripcionCiudad->length > 0) {
                            $descripcionCiudad = $descripcionCiudad->item(0)->nodeValue;
                        } else {
                            $descripcionCiudad = "";
                        }
                        $tipo = $terminalesTte->item($m)->getElementsByTagName('tipo');
                        if ($tipo->length > 0) {
                            $tipo = $tipo->item(0)->nodeValue;
                        } else {
                            $tipo = "";
                        }
                
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('terminaiste');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'codigo' => $codigo,
                                'descripcion' => $descripcion,
                                'codigoCidade' => $codigoCiudad,
                                'descCidade' => $descripcionCiudad,
                                'tipo' => $tipo
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

