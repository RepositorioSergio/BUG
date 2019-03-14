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
echo "COMECOU RESERVA<br/>";
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

$raw = '<?xml version="1.0"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cir="http://circuito.serviceIncoming.dome.com/">
   <soapenv:Header/>
   <soapenv:Body>
      <cir:reservaAbrir>
        <arg0>
            <ideses>AGH#3281#251611587387945</ideses>
            <codtou>TST</codtou>  
            <locata>12347</locata> 
        </arg0>
      </cir:reservaAbrir>
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
$reservaAbrirRespuesta = $inputDoc->getElementsByTagName("reservaAbrirRespuesta");
$ideses = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("ideses");
if ($ideses->length > 0) {
    $ideses = $ideses->item(0)->nodeValue;
} else {
    $ideses = "";
}
$locata = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("locata");
if ($locata->length > 0) {
    $locata = $locata->item(0)->nodeValue;
} else {
    $locata = "";
}
$cupest = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("cupest");
if ($cupest->length > 0) {
    $cupest = $cupest->item(0)->nodeValue;
} else {
    $cupest = "";
}
$feccre = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("feccre");
if ($feccre->length > 0) {
    $feccre = $feccre->item(0)->nodeValue;
} else {
    $feccre = "";
}
$fecini = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("fecini");
if ($fecini->length > 0) {
    $fecini = $fecini->item(0)->nodeValue;
} else {
    $fecini = "";
}
$fecfin = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("fecfin");
if ($fecfin->length > 0) {
    $fecfin = $fecfin->item(0)->nodeValue;
} else {
    $fecfin = "";
}
$estado = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("estado");
if ($estado->length > 0) {
    $estado = $estado->item(0)->nodeValue;
} else {
    $estado = "";
}
$codtou = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("codtou");
if ($codtou->length > 0) {
    $codtou = $codtou->item(0)->nodeValue;
} else {
    $codtou = "";
}
$codpro = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("codpro");
if ($codpro->length > 0) {
    $codpro = $codpro->item(0)->nodeValue;
} else {
    $codpro = "";
}
$coddiv = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("coddiv");
if ($coddiv->length > 0) {
    $coddiv = $coddiv->item(0)->nodeValue;
} else {
    $coddiv = "";
}
$impcom = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("impcom");
if ($impcom->length > 0) {
    $impcom = $impcom->item(0)->nodeValue;
} else {
    $impcom = "";
}
$impnoc = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("impnoc");
if ($impnoc->length > 0) {
    $impnoc = $impnoc->item(0)->nodeValue;
} else {
    $impnoc = "";
}

$percon = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("percon");
if ($percon->length > 0) {
    $nombre = $percon->item(0)->getElementsByTagName("nombre");
    if ($nombre->length > 0) {
        $nombre = $nombre->item(0)->nodeValue;
    } else {
        $nombre = "";
    }
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('abrirreserva');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'locata' => $locata,
        'cupest' => $cupest,
        'estado' => $estado,
        'feccre' => $feccre,
        'fecini' => $fecini,
        'fecfin' => $fecfin,
        'codtou' => $codtou,
        'codpro' => $codpro,
        'coddiv' => $coddiv,
        'impcom' => $impcom,
        'impnoc' => $impnoc,
        'nombre' => $nombre
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

$respas = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("respas");
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
            $insert->into('abrirreserva_respas');
            $insert->values(array(
                'id' => $idRespas,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'nombre' => $nombre,
                'priape' => $priape,
                'sexo' => $sexo,
                'tippas' => $tippas,
                'locata' => $locata
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

$resaer = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("resaer");
if ($resaer->length > 0) {
    for ($j=0; $j < $resaer->length; $j++) { 
        $idResaer = $resaer->item($j)->getAttribute("id");
        $fecini = $resaer->item($j)->getElementsByTagName("fecini");
        if ($fecini->length > 0) {
            $fecini = $fecini->item(0)->nodeValue;
        } else {
            $fecini = "";
        }
        $fecfin = $resaer->item($j)->getElementsByTagName("fecfin");
        if ($fecfin->length > 0) {
            $fecfin = $fecfin->item(0)->nodeValue;
        } else {
            $fecfin = "";
        }
        $aerori = $resaer->item($j)->getElementsByTagName("aerori");
        if ($aerori->length > 0) {
            $aerori = $aerori->item(0)->nodeValue;
        } else {
            $aerori = "";
        }
        $aerdes = $resaer->item($j)->getElementsByTagName("aerdes");
        if ($aerdes->length > 0) {
            $aerdes = $aerdes->item(0)->nodeValue;
        } else {
            $aerdes = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('abrirreserva_resaer');
            $insert->values(array(
                'id' => $idResaer,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'fecini' => $fecini,
                'fecfin' => $fecfin,
                'aerori' => $aerori,
                'aerdes' => $aerdes,
                'locata' => $locata
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
            $numvueresseg = $resseg->item(0)->getElementsByTagName("numvue");
            if ($numvueresseg->length > 0) {
                $numvueresseg = $numvueresseg->item(0)->nodeValue;
            } else {
                $numvueresseg = "";
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
                        $insert->into('abrirreserva_pasis');
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
            $insert->into('abrirreserva_resseg');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'aeroriresseg' => $aeroriresseg,
                'aerdesresseg' => $aerdesresseg,
                'numvueresseg' => $numvueresseg,
                'idavue' => $idavue,
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

$resser = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("resser");
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
            $insert->into('abrirreserva_resser');
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
                'locata' => $locata
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
            $estado = $estsmo->item(0)->getElementsByTagName("estado");
            if ($estado->length > 0) {
                $estado = $estado->item(0)->nodeValue;
            } else {
                $estado = "";
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
                            $insert->into('cancelar_reserva_pasidestpas');
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
                $insert->into('abrirreserva_estsmo');
                $insert->values(array(
                    'id' => $idestsmo,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'codsmo' => $codsmo,
                    'codral' => $codral,
                    'cupest' => $cupest,
                    'numuni' => $numuni,
                    'estado' => $estado,
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


$ressup = $ReservaAbrirRespuesta->item(0)->getElementsByTagName("ressup");
if ($ressup->length > 0) {
    for ($r=0; $r < $ressup->length; $r++) { 
        $idressup = $ressup->item($r)->getAttribute("id");
        $codsup = $ressup->item($r)->getElementsByTagName("codsup");
        if ($codsup->length > 0) {
            $codsup = $codsup->item(0)->nodeValue;
        } else {
            $codsup = "";
        }
        $tipsup = $ressup->item($r)->getElementsByTagName("tipsup");
        if ($tipsup->length > 0) {
            $tipsup = $tipsup->item(0)->nodeValue;
        } else {
            $tipsup = "";
        }
        $numuni = $ressup->item($r)->getElementsByTagName("numuni");
        if ($numuni->length > 0) {
            $numuni = $numuni->item(0)->nodeValue;
        } else {
            $numuni = "";
        }
        $chkopc = $ressup->item($r)->getElementsByTagName("chkopc");
        if ($chkopc->length > 0) {
            $chkopc = $chkopc->item(0)->nodeValue;
        } else {
            $chkopc = "";
        }
        $impnoc = $ressup->item($r)->getElementsByTagName("impnoc");
        if ($impnoc->length > 0) {
            $impnoc = $impnoc->item(0)->nodeValue;
        } else {
            $impnoc = "";
        }
        $impcom = $ressup->item($r)->getElementsByTagName("impcom");
        if ($impcom->length > 0) {
            $impcom = $impcom->item(0)->nodeValue;
        } else {
            $impcom = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('abrirreserva_ressup');
            $insert->values(array(
                'id' => $idressup,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codsup' => $codsup,
                'tipsup' => $tipsup,
                'numuni' => $numuni,
                'chkopc' => $chkopc,
                'impnoc' => $impnoc,
                'impcom' => $impcom,
                'locata' => $locata
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
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>