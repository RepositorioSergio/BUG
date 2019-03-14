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
echo "COMECOU ENCERRAR RESERVA<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.trabax.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );

echo "COMECOU ENCERRAR RESERVA<br/>";
$raw = '<?xml version="1.0"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cir="http://circuito.serviceIncoming.dome.com/">
   <soapenv:Header/>
   <soapenv:Body>
      <cir:reservaCerrar>
        <arg0>
            <ideses>AGH#12345#123456789012345</ideses>  
            <codtou>TST</codtou>  
            <infpas id="1">   
                <nombre>Test1</nombre>   
                <priape>Test1</priape>  
                <pasapt>12345678W</pasapt>   
                <fecnac>10/06/1985</fecnac>  
            </infpas>  
            <infpas id="2">   
                <nombre>Test2</nombre>   
                <priape>Test2</priape>   
                <pasapt>12345678W</pasapt>   
                <fecnac>11/07/2003</fecnac>  
            </infpas>  
            <infpas id="3">   
                <nombre>Test3</nombre>   
                <priape>Test3</priape>   
                <pasapt>12345678W</pasapt>   
                <fecnac>11/07/2004</fecnac>  
            </infpas>  
            <percon id="1">   
                <nombre>Test</nombre>   
                <priape>Test</priape>   
                <tel>123456789</tel>   
                <mai>dome@dome.com</mai>  
            </percon>
        </arg0>
      </cir:reservaCerrar>
   </soapenv:Body>
</soapenv:Envelope>';
echo "<br/> RAW:" . $raw;


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml; charset=\"iso-8859-1\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));

$url = "http://cir-tbx.dome-consulting.com/circuito";

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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.trabax.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

die();
$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$ReservaCerrarRespuesta = $inputDoc->getElementsByTagName("ReservaCerrarRespuesta");
$ideses = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("ideses");
if ($ideses->length > 0) {
    $ideses = $ideses->item(0)->nodeValue;
} else {
    $ideses = "";
}
$locata = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("locata");
if ($locata->length > 0) {
    $locata = $locata->item(0)->nodeValue;
} else {
    $locata = "";
}
$cupest = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("cupest");
if ($cupest->length > 0) {
    $cupest = $cupest->item(0)->nodeValue;
} else {
    $cupest = "";
}
$feccre = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("feccre");
if ($feccre->length > 0) {
    $feccre = $feccre->item(0)->nodeValue;
} else {
    $feccre = "";
}
$porage = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("porage");
if ($porage->length > 0) {
    $porage = $porage->item(0)->nodeValue;
} else {
    $porage = "";
}
$codtou = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("codtou");
if ($codtou->length > 0) {
    $codtou = $codtou->item(0)->nodeValue;
} else {
    $codtou = "";
}
$codpro = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("codpro");
if ($codpro->length > 0) {
    $codpro = $codpro->item(0)->nodeValue;
} else {
    $codpro = "";
}
$coddiv = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("coddiv");
if ($coddiv->length > 0) {
    $coddiv = $coddiv->item(0)->nodeValue;
} else {
    $coddiv = "";
}
$impcom = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("impcom");
if ($impcom->length > 0) {
    $impcom = $impcom->item(0)->nodeValue;
} else {
    $impcom = "";
}
$impcag = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("impcag");
if ($impcag->length > 0) {
    $impcag = $impcag->item(0)->nodeValue;
} else {
    $impcag = "";
}

$percon = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("percon");
if ($percon->length > 0) {
    $id = $percon->item(0)->getAttribute("id");
    $nombre = $percon->item(0)->getElementsByTagName("nombre");
    if ($nombre->length > 0) {
        $nombre = $nombre->item(0)->nodeValue;
    } else {
        $nombre = "";
    }
    $priape = $percon->item(0)->getElementsByTagName("priape");
    if ($priape->length > 0) {
        $priape = $priape->item(0)->nodeValue;
    } else {
        $priape = "";
    }
    $tel = $percon->item(0)->getElementsByTagName("tel");
    if ($tel->length > 0) {
        $tel = $tel->item(0)->nodeValue;
    } else {
        $tel = "";
    }
    $mai = $percon->item(0)->getElementsByTagName("mai");
    if ($mai->length > 0) {
        $mai = $mai->item(0)->nodeValue;
    } else {
        $mai = "";
    }
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('encerrar_reserva');
    $insert->values(array(
        'id' => $id,
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'locata' => $locata,
        'cupest' => $cupest,
        'feccre' => $feccre,
        'tipres' => $tipres,
        'porage' => $porage,
        'codtou' => $codtou,
        'codpro' => $codpro,
        'coddiv' => $coddiv,
        'impcom' => $impcom,
        'impcag' => $impcag,
        'nombre' => $nombre,
        'priape' => $priape,
        'tel' => $tel,
        'mai' => $mai
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "ERRO: " . $e;
    echo $return;
}

$respas = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("respas");
if ($respas->length > 0) {
    for ($i=0; $i < $respas->length; $i++) { 
        $idRespas = $respas->item($i)->getAttribute("id");
        $nombre = $respas->item($i)->getElementsByTagName("nombre");
        if ($nombre->length > 0) {
            $nombre = $nombre->item(0)->nodeValue;
        } else {
            $nombre = "";
        }
        $priape = $respas->item($i)->getElementsByTagName("priape");
        if ($priape->length > 0) {
            $priape = $priape->item(0)->nodeValue;
        } else {
            $priape = "";
        }
        $segape = $respas->item($i)->getElementsByTagName("segape");
        if ($segape->length > 0) {
            $segape = $segape->item(0)->nodeValue;
        } else {
            $segape = "";
        }
        $fecnac = $respas->item($i)->getElementsByTagName("fecnac");
        if ($fecnac->length > 0) {
            $fecnac = $fecnac->item(0)->nodeValue;
        } else {
            $fecnac = "";
        }
        $tipdoc = $respas->item($i)->getElementsByTagName("tipdoc");
        if ($tipdoc->length > 0) {
            $tipdoc = $tipdoc->item(0)->nodeValue;
        } else {
            $tipdoc = "";
        }
        $pasapt = $respas->item($i)->getElementsByTagName("pasapt");
        if ($pasapt->length > 0) {
            $pasapt = $pasapt->item(0)->nodeValue;
        } else {
            $pasapt = "";
        }
        $sexo = $respas->item($i)->getElementsByTagName("sexo");
        if ($sexo->length > 0) {
            $sexo = $sexo->item(0)->nodeValue;
        } else {
            $sexo = "";
        }
        $tippas = $respas->item($i)->getElementsByTagName("tippas");
        if ($tippas->length > 0) {
            $tippas = $tippas->item(0)->nodeValue;
        } else {
            $tippas = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('encerrar_reserva_respas');
            $insert->values(array(
                'id' => $idRespas,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'nombre' => $nombre,
                'priape' => $priape,
                'segape' => $segape,
                'fecnac' => $fecnac,
                'tipdoc' => $tipdoc,
                'pasapt' => $pasapt,
                'sexo' => $sexo,
                'idCancelar' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO PAS: " . $e;
            echo $return;
        }
    }
}

$resaer = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("resaer");
if ($resaer->length > 0) {
    for ($j=0; $j < $resaer->length; $j++) { 
        $idResaer = $resaer->item($j)->getAttribute("id");
        $fecini = $resaer->item($j)->getElementsByTagName("fecini");
        if ($fecini->length > 0) {
            $fecini = $fecini->item(0)->nodeValue;
        } else {
            $fecini = "";
        }
        $codpgr = $resaer->item($j)->getElementsByTagName("codpgr");
        if ($codpgr->length > 0) {
            $codpgr = $codpgr->item(0)->nodeValue;
        } else {
            $codpgr = "";
        }
        $codser = $resaer->item($j)->getElementsByTagName("codser");
        if ($codser->length > 0) {
            $codser = $codser->item(0)->nodeValue;
        } else {
            $codser = "";
        }
        $cupest = $resaer->item($j)->getElementsByTagName("cupest");
        if ($cupest->length > 0) {
            $cupest = $cupest->item(0)->nodeValue;
        } else {
            $cupest = "";
        }
        $estado = $resaer->item($j)->getElementsByTagName("estado");
        if ($estado->length > 0) {
            $estado = $estado->item(0)->nodeValue;
        } else {
            $estado = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('encerrar_reserva_resaer');
            $insert->values(array(
                'id' => $idResaer,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'fecini' => $fecini,
                'codpgr' => $codpgr,
                'codser' => $codser,
                'cupest' => $cupest,
                'estado' => $estado,
                'idCancelar' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO RESAER: " . $e;
            echo $return;
        }

        $resseg = $resaer->item($j)->getElementsByTagName("resseg");
        if ($resseg->length > 0) {
            $idresseg = $resseg->item(0)->getAttribute("id");
            $feciniresseg = $resseg->item(0)->getElementsByTagName("fecini");
            if ($feciniresseg->length > 0) {
                $feciniresseg = $feciniresseg->item(0)->nodeValue;
            } else {
                $feciniresseg = "";
            }
            $fecfinresseg = $resseg->item(0)->getElementsByTagName("fecfin");
            if ($fecfinresseg->length > 0) {
                $fecfinresseg = $fecfinresseg->item(0)->nodeValue;
            } else {
                $fecfinresseg = "";
            }
            $aeroriresseg = $resseg->item(0)->getElementsByTagName("aerori");
            if ($aeroriresseg->length > 0) {
                $aeroriresseg = $aeroriresseg->item(0)->nodeValue;
            } else {
                $aeroriresseg = "";
            }
            $aerdesresseg = $resseg->item(0)->getElementsByTagName("aerdes");
            if ($aerdesresseg->length > 0) {
                $aerdesresseg = $aerdesresseg->item(0)->nodeValue;
            } else {
                $aerdesresseg = "";
            }
            $codciaresseg = $resseg->item(0)->getElementsByTagName("codcia");
            if ($codciaresseg->length > 0) {
                $codciaresseg = $codciaresseg->item(0)->nodeValue;
            } else {
                $codciaresseg = "";
            }
            $numvueresseg = $resseg->item(0)->getElementsByTagName("numvue");
            if ($numvueresseg->length > 0) {
                $numvueresseg = $numvueresseg->item(0)->nodeValue;
            } else {
                $numvueresseg = "";
            }
            $codclaresseg = $resseg->item(0)->getElementsByTagName("codcla");
            if ($codclaresseg->length > 0) {
                $codclaresseg = $codclaresseg->item(0)->nodeValue;
            } else {
                $codclaresseg = "";
            }
            $codcabresseg = $resseg->item(0)->getElementsByTagName("codcab");
            if ($codcabresseg->length > 0) {
                $codcabresseg = $codcabresseg->item(0)->nodeValue;
            } else {
                $codcabresseg = "";
            }
            $cupestresseg = $resseg->item(0)->getElementsByTagName("cupest");
            if ($cupestresseg->length > 0) {
                $cupestresseg = $cupestresseg->item(0)->nodeValue;
            } else {
                $cupestresseg = "";
            }
            $estadoresseg = $resseg->item(0)->getElementsByTagName("estado");
            if ($estadoresseg->length > 0) {
                $estadoresseg = $estadoresseg->item(0)->nodeValue;
            } else {
                $estadoresseg = "";
            }
            $idavue = $resseg->item(0)->getElementsByTagName("idavue");
            if ($idavue->length > 0) {
                $idavue = $idavue->item(0)->nodeValue;
            } else {
                $idavue = "";
            }
        }

        $estpas = $resaer->item($j)->getElementsByTagName("estpas");
        if ($estpas->length > 0) {
            $cupestestpas = $estpas->item(0)->getElementsByTagName("cupest");
            if ($cupestestpas->length > 0) {
                $cupestestpas = $cupestestpas->item(0)->nodeValue;
            } else {
                $cupestestpas = "";
            }

            $pasid2 = "";
            $pasid = $estpas->item(0)->getElementsByTagName("pasid");
            if ($pasid->length > 0) {
                for ($jAux=0; $jAux < $pasid->length; $jAux++) { 
                    $pasid2 = $pasid->item(0)->nodeValue;

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('encerrar_reserva_pasis');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'pasid' => $pasid2,
                            'idResseg' => $idresseg
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO PAS: " . $e;
                        echo $return;
                    }
                }
            }
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('encerrar_reserva_resseg');
            $insert->values(array(
                'id' => $idresseg,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'fecini' => $feciniresseg,
                'fecfinresseg' => $fecfinresseg,
                'aeroriresseg' => $aeroriresseg,
                'aerdesresseg' => $aerdesresseg,
                'codciaresseg' => $codciaresseg,
                'numvueresseg' => $numvueresseg,
                'codclaresseg' => $codclaresseg,
                'codcabresseg' => $codcabresseg,
                'cupestresseg' => $cupestresseg,
                'estadoresseg' => $estadoresseg,
                'idavue' => $idavue,
                'cupestestpas' => $cupestestpas,
                'idResaer' => $idResaer
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO RESAER: " . $e;
            echo $return;
        }
    }
}

$resser = $ReservaCerrarRespuesta->item(0)->getElementsByTagName("resser");
if ($resser->length > 0) {
    for ($k=0; $k < $resser->length; $k++) { 
        $idresser = $resser->item($k)->getAttribute("id");
        $fecini = $resser->item($k)->getElementsByTagName("fecini");
        if ($fecini->length > 0) {
            $fecini = $fecini->item(0)->nodeValue;
        } else {
            $fecini = "";
        }
        $fecfin = $resser->item($k)->getElementsByTagName("fecfin");
        if ($fecfin->length > 0) {
            $fecfin = $fecfin->item(0)->nodeValue;
        } else {
            $fecfin = "";
        }
        $nomser = $resser->item($k)->getElementsByTagName("nomser");
        if ($nomser->length > 0) {
            $nomser = $nomser->item(0)->nodeValue;
        } else {
            $nomser = "";
        }
        $codsca = $resser->item($k)->getElementsByTagName("codsca");
        if ($codsca->length > 0) {
            $codsca = $codsca->item(0)->nodeValue;
        } else {
            $codsca = "";
        }
        $codzge = $resser->item($k)->getElementsByTagName("codzge");
        if ($codzge->length > 0) {
            $codzge = $codzge->item(0)->nodeValue;
        } else {
            $codzge = "";
        }
        $codtse = $resser->item($k)->getElementsByTagName("codtse");
        if ($codtse->length > 0) {
            $codtse = $codtse->item(0)->nodeValue;
        } else {
            $codtse = "";
        }
        $codser = $resser->item($k)->getElementsByTagName("codser");
        if ($codser->length > 0) {
            $codser = $codser->item(0)->nodeValue;
        } else {
            $codser = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('encerrar_reserva_resser');
            $insert->values(array(
                'id' => $idresser,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'fecini' => $fecini,
                'fecfin' => $fecfin,
                'nomser' => $nomser,
                'codsca' => $codsca,
                'codzge' => $codzge,
                'codtse' => $codtse,
                'codser' => $codser,
                'idCancelar' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO RESSER: " . $e;
            echo $return;
        }

        //estsmo
        $estsmo = $resser->item($k)->getElementsByTagName("estsmo");
        if ($estsmo->length > 0) {
            $idestsmo = $estsmo->item(0)->getAttribute("id");
            $codsmo = $estsmo->item(0)->getElementsByTagName("codsmo");
            if ($codsmo->length > 0) {
                $codsmo = $codsmo->item(0)->nodeValue;
            } else {
                $codsmo = "";
            }
            $codral = $estsmo->item(0)->getElementsByTagName("codral");
            if ($codral->length > 0) {
                $codral = $codral->item(0)->nodeValue;
            } else {
                $codral = "";
            }
            $cupest = $estsmo->item(0)->getElementsByTagName("cupest");
            if ($cupest->length > 0) {
                $cupest = $cupest->item(0)->nodeValue;
            } else {
                $cupest = "";
            }
            $numuni = $estsmo->item(0)->getElementsByTagName("numuni");
            if ($numuni->length > 0) {
                $numuni = $numuni->item(0)->nodeValue;
            } else {
                $numuni = "";
            }
            $impcom = $estsmo->item(0)->getElementsByTagName("impcom");
            if ($impcom->length > 0) {
                $impcom = $impcom->item(0)->nodeValue;
            } else {
                $impcom = "";
            }

            $estpas = $estsmo->item(0)->getElementsByTagName("estpas");
            if ($estpas->length > 0) {
                $cupestestpas = $estpas->item(0)->getElementsByTagName("cupest");
                if ($cupestestpas->length > 0) {
                    $cupestestpas = $cupestestpas->item(0)->nodeValue;
                } else {
                    $cupestestpas = "";
                }
                $pasidd = "";
                $pasid = $estpas->item(0)->getElementsByTagName("pasid");
                if ($pasid->length > 0) {
                    for ($kAux=0; $kAux < $pasid->length; $kAux++) { 
                        $pasidd = $pasid->item(0)->nodeValue;

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('encerrar_reserva_pasidestpas');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'pasid' => $pasidd,
                                'idestsmo' => $idestsmo
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO PAS: " . $e;
                            echo $return;
                        }
                    }
                }
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('encerrar_reserva_estsmo');
                $insert->values(array(
                    'id' => $idestsmo,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'codsmo' => $codsmo,
                    'codral' => $codral,
                    'cupest' => $cupest,
                    'numuni' => $numuni,
                    'impcom' => $impcom,
                    'idresser' => $idresser
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO RESSER: " . $e;
                echo $return;
            }
        } else {
            $idestsmo = "";
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>