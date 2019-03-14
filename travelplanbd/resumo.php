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

if (!$_SERVER['DOCUMENT_ROOT']) {
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
$sql = "select value from settings where name='enableglobaliapackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_globaliapackages = $affiliate_id;
} else {
    $affiliate_id_globaliapackages = 0;
}
$sql = "select value from settings where name='globaliapackagesCustomerID' and affiliate_id=$affiliate_id_globaliapackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $globaliapackagesCustomerID = $row_settings['value'];
}
$sql = "select value from settings where name='globaliapackagesserviceURL' and affiliate_id=$affiliate_id_globaliapackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $globaliapackagesserviceURL = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = $globaliapackagesserviceURL . 'b2c/services/wstReserva';

$ideSes = "52535383408122501180";
$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.reserva.wst"><soapenv:Header/><soapenv:Body><typ:resumenReservaV2Request><typ:idUsuario>' . $globaliapackagesCustomerID . '</typ:idUsuario><typ:ideSes>' . $ideSes . '</typ:ideSes></typ:resumenReservaV2Request></soapenv:Body></soapenv:Envelope>';

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
echo "<br/> RESPONSE <br/>";
/*  echo $return;
 echo $response;
 echo $return; */

echo '<xmp>';
var_dump($response);
echo '</xmp>';
$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$resumenReservaV2Response = $Body->item(0)->getElementsByTagName("resumenReservaV2Response");
$datosReserva = $resumenReservaV2Response->item(0)->getElementsByTagName("datosReserva");
$brutoCom = $datosReserva->item(0)->getElementsByTagName("brutoCom");
if ($brutoCom->length > 0) {
    $brutoCom = $brutoCom->item(0)->nodeValue;
} else {
    $brutoCom = "";
}
$brutonCo = $datosReserva->item(0)->getElementsByTagName("brutonCo");
if ($brutonCo->length > 0) {
    $brutonCo = $brutonCo->item(0)->nodeValue;
} else {
    $brutonCo = "";
}
$divRes = $datosReserva->item(0)->getElementsByTagName("divRes");
if ($divRes->length > 0) {
    $divRes = $divRes->item(0)->nodeValue;
} else {
    $divRes = "";
}
$estres = $datosReserva->item(0)->getElementsByTagName("estres");
if ($estres->length > 0) {
    $estres = $estres->item(0)->nodeValue;
} else {
    $estres = "";
}
$fecFin = $datosReserva->item(0)->getElementsByTagName("fecFin");
if ($fecFin->length > 0) {
    $fecFin = $fecFin->item(0)->nodeValue;
} else {
    $fecFin = "";
}
$fecIni = $datosReserva->item(0)->getElementsByTagName("fecIni");
if ($fecIni->length > 0) {
    $fecIni = $fecIni->item(0)->nodeValue;
} else {
    $fecIni = "";
}
$nomPro = $datosReserva->item(0)->getElementsByTagName("nomPro");
if ($nomPro->length > 0) {
    $nomPro = $nomPro->item(0)->nodeValue;
} else {
    $nomPro = "";
}
$numAdl = $datosReserva->item(0)->getElementsByTagName("numAdl");
if ($numAdl->length > 0) {
    $numAdl = $numAdl->item(0)->nodeValue;
} else {
    $numAdl = "";
}
$numChl = $datosReserva->item(0)->getElementsByTagName("numChl");
if ($numChl->length > 0) {
    $numChl = $numChl->item(0)->nodeValue;
} else {
    $numChl = "";
}
$numInf = $datosReserva->item(0)->getElementsByTagName("numInf");
if ($numInf->length > 0) {
    $numInf = $numInf->item(0)->nodeValue;
} else {
    $numInf = "";
}
$pvpRes = $datosReserva->item(0)->getElementsByTagName("pvpRes");
if ($pvpRes->length > 0) {
    $pvpRes = $pvpRes->item(0)->nodeValue;
} else {
    $pvpRes = "";
}
$refPro = $datosReserva->item(0)->getElementsByTagName("refPro");
if ($refPro->length > 0) {
    $refPro = $refPro->item(0)->nodeValue;
} else {
    $refPro = "";
}
$tasas = $datosReserva->item(0)->getElementsByTagName("tasas");
if ($tasas->length > 0) {
    $tasas = $tasas->item(0)->nodeValue;
} else {
    $tasas = "";
}
$netRes = $datosReserva->item(0)->getElementsByTagName("netRes");
if ($netRes->length > 0) {
    $netRes = $netRes->item(0)->nodeValue;
} else {
    $netRes = "";
}
$pvpTot = $datosReserva->item(0)->getElementsByTagName("pvpTot");
if ($pvpTot->length > 0) {
    $pvpTot = $pvpTot->item(0)->nodeValue;
} else {
    $pvpTot = "";
}
$porAge = $datosReserva->item(0)->getElementsByTagName("porAge");
if ($porAge->length > 0) {
    $porAge = $porAge->item(0)->nodeValue;
} else {
    $porAge = "";
}

try{
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('resumoReserva_dadosReserva');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'brutoCom' => $brutoCom,
        'brutonCo' => $brutonCo,
        'divRes' => $divRes,
        'estres' => $estres,
        'fecFin' => $fecFin,
        'fecIni' => $fecIni,
        'nomPro' => $nomPro,
        'numAdl' => $numAdl,
        'numChl' => $numChl,
        'numInf' => $numInf,
        'pvpRes' => $pvpRes,
        'refPro' => $refPro,
        'tasas' => $tasas,
        'netRes' => $netRes,
        'pvpTot' => $pvpTot,
        'porAge' => $porAge
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}catch(Exception $e){
    echo "<br/> Erro " . $e->getMessage();
}

$node = $datosReserva->item(0)->getElementsByTagName("incluye");
for ($iAux = 0; $iAux < $node->length; $iAux++) {

    $incluye = $node->item($iAux)->nodeValue;

    try{
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('resumoReserva_dadosReservaIncluye');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'incluye' => $incluye
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }catch(Exception $e){
        echo "<br/> Erro1 " . $e->getMessage();
    }

}

//serviciosReserva
$serviciosReserva = $resumenReservaV2Response->item(0)->getElementsByTagName("serviciosReserva");
for ($jAux = 0; $jAux < $serviciosReserva->length; $jAux++) {
    $dscAptDep = $serviciosReserva->item($jAux)->getElementsByTagName("dscAptDep");
    if ($dscAptDep->length > 0) {
        $dscAptDep = $dscAptDep->item(0)->nodeValue;
    } else {
        $dscAptDep = "";
    }
    $dscAptArr = $serviciosReserva->item($jAux)->getElementsByTagName("dscAptArr");
    if ($dscAptArr->length > 0) {
        $dscAptArr = $dscAptArr->item(0)->nodeValue;
    } else {
        $dscAptArr = "";
    }
    $codAptDep = $serviciosReserva->item($jAux)->getElementsByTagName("codAptDep");
    if ($codAptDep->length > 0) {
        $codAptDep = $codAptDep->item(0)->nodeValue;
    } else {
        $codAptDep = "";
    }
    $codAptArr = $serviciosReserva->item($jAux)->getElementsByTagName("codAptArr");
    if ($codAptArr->length > 0) {
        $codAptArr = $codAptArr->item(0)->nodeValue;
    } else {
        $codAptArr = "";
    }
    $codCia = $serviciosReserva->item($jAux)->getElementsByTagName("codCia");
    if ($codCia->length > 0) {
        $codCia = $codCia->item(0)->nodeValue;
    } else {
        $codCia = "";
    }
    $dscCia = $serviciosReserva->item($jAux)->getElementsByTagName("dscCia");
    if ($dscCia->length > 0) {
        $dscCia = $dscCia->item(0)->nodeValue;
    } else {
        $dscCia = "";
    }
    $codCiaOpe = $serviciosReserva->item($jAux)->getElementsByTagName("codCiaOpe");
    if ($codCiaOpe->length > 0) {
        $codCiaOpe = $codCiaOpe->item(0)->nodeValue;
    } else {
        $codCiaOpe = "";
    }
    $dscCiaOpe = $serviciosReserva->item($jAux)->getElementsByTagName("dscCiaOpe");
    if ($dscCiaOpe->length > 0) {
        $dscCiaOpe = $dscCiaOpe->item(0)->nodeValue;
    } else {
        $dscCiaOpe = "";
    }
    $codCla = $serviciosReserva->item($jAux)->getElementsByTagName("codCla");
    if ($codCla->length > 0) {
        $codCla = $codCla->item(0)->nodeValue;
    } else {
        $codCla = "";
    }
    $dscCla = $serviciosReserva->item($jAux)->getElementsByTagName("dscCla");
    if ($dscCla->length > 0) {
        $dscCla = $dscCla->item(0)->nodeValue;
    } else {
        $dscCla = "";
    }
    $codRga = $serviciosReserva->item($jAux)->getElementsByTagName("codRga");
    if ($codRga->length > 0) {
        $codRga = $codRga->item(0)->nodeValue;
    } else {
        $codRga = "";
    }
    $dscCha = $serviciosReserva->item($jAux)->getElementsByTagName("dscCha ");
    if ($dscCha->length > 0) {
        $dscCha = $dscCha->item(0)->nodeValue;
    } else {
        $dscCha = "";
    }
    $estSer = $serviciosReserva->item($jAux)->getElementsByTagName("estSer");
    if ($estSer->length > 0) {
        $estSer = $estSer->item(0)->nodeValue;
    } else {
        $estSer = "";
    }
    $fecFin = $serviciosReserva->item($jAux)->getElementsByTagName("fecFin");
    if ($fecFin->length > 0) {
        $fecFin = $fecFin->item(0)->nodeValue;
    } else {
        $fecFin = "";
    }
    $fecIni = $serviciosReserva->item($jAux)->getElementsByTagName("fecIni");
    if ($fecIni->length > 0) {
        $fecIni = $fecIni->item(0)->nodeValue;
    } else {
        $fecIni = "";
    }
    $horArr = $serviciosReserva->item($jAux)->getElementsByTagName("horArr");
    if ($horArr->length > 0) {
        $horArr = $horArr->item(0)->nodeValue;
    } else {
        $horArr = "";
    }
    $horDep = $serviciosReserva->item($jAux)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $nomSer = $serviciosReserva->item($jAux)->getElementsByTagName("nomSer");
    if ($nomSer->length > 0) {
        $nomSer = $nomSer->item(0)->nodeValue;
    } else {
        $nomSer = "";
    }
    $numVue = $serviciosReserva->item($jAux)->getElementsByTagName("numVue");
    if ($numVue->length > 0) {
        $numVue = $numVue->item(0)->nodeValue;
    } else {
        $numVue = "";
    }
    $paxSer = $serviciosReserva->item($jAux)->getElementsByTagName("paxSer");
    if ($paxSer->length > 0) {
        $paxSer = $paxSer->item(0)->nodeValue;
    } else {
        $paxSer = "";
    }
    $refgrp = $serviciosReserva->item($jAux)->getElementsByTagName("refgrp");
    if ($refgrp->length > 0) {
        $refgrp = $refgrp->item(0)->nodeValue;
    } else {
        $refgrp = "";
    }
    $refSer = $serviciosReserva->item($jAux)->getElementsByTagName("refSer");
    if ($refSer->length > 0) {
        $refSer = $refSer->item(0)->nodeValue;
    } else {
        $refSer = "";
    }
    $tipSer = $serviciosReserva->item($jAux)->getElementsByTagName("tipSer");
    if ($tipSer->length > 0) {
        $tipSer = $tipSer->item(0)->nodeValue;
    } else {
        $tipSer = "";
    }
    $uniSer = $serviciosReserva->item($jAux)->getElementsByTagName("uniSer");
    if ($uniSer->length > 0) {
        $uniSer = $uniSer->item(0)->nodeValue;
    } else {
        $uniSer = "";
    }
    $dscTso = $serviciosReserva->item($jAux)->getElementsByTagName("dscTso");
    if ($dscTso->length > 0) {
        $dscTso = $dscTso->item(0)->nodeValue;
    } else {
        $dscTso = "";
    }
    $codTot = $serviciosReserva->item($jAux)->getElementsByTagName("codTot");
    if ($codTot->length > 0) {
        $codTot = $codTot->item(0)->nodeValue;
    } else {
        $codTot = "";
    }
    $codCha = $serviciosReserva->item($jAux)->getElementsByTagName("codCha");
    if ($codCha->length > 0) {
        $codCha = $codCha->item(0)->nodeValue;
    } else {
        $codCha = "";
    }
    $codTha = $serviciosReserva->item($jAux)->getElementsByTagName("codTha");
    if ($codTha->length > 0) {
        $codTha = $codTha->item(0)->nodeValue;
    } else {
        $codTha = "";
    }

    try{
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('resumoReserva_servicosReserva');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'dscAptDep' => $dscAptDep,
            'dscAptArr' => $dscAptArr,
            'codAptDep' => $codAptDep,
            'codAptArr' => $codAptArr,
            'codCia' => $codCia,
            'dscCia' => $dscCia,
            'codCiaOpe' => $codCiaOpe,
            'dscCiaOpe' => $dscCiaOpe,
            'codCla' => $codCla,
            'dscCla' => $dscCla,
            'codRga' => $codRga,
            'dscCha' => $dscCha,
            'estSer' => $estSer,
            'fecFin' => $fecFin,
            'fecIni' => $fecIni,
            'horArr' => $horArr,
            'horDep' => $horDep,
            'nomSer' => $nomSer,
            'numVue' => $numVue,
            'paxSer' => $paxSer,
            'refgrp' => $refgrp,
            'refSer' => $refSer,
            'tipSer' => $tipSer,
            'uniSer' => $uniSer,
            'dscTso' => $dscTso,
            'codTot' => $codTot,
            'codCha' => $codCha,
            'codTha' => $codTha
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }catch(Exception $e){
        echo "<br/> Erro " . $e->getMessage();
    }

}
//pasajerosReserva
$pasajerosReserva = $resumenReservaV2Response->item(0)->getElementsByTagName("pasajerosReserva");
$nomPax = $pasajerosReserva->item(0)->getElementsByTagName("nomPax");
if ($nomPax->length > 0) {
    $nomPax = $nomPax->item(0)->nodeValue;
} else {
    $nomPax = "";
}
$apePax = $pasajerosReserva->item(0)->getElementsByTagName("apePax");
if ($apePax->length > 0) {
    $apePax = $nomPax->item(0)->nodeValue;
} else {
    $apePax = "";
}
$agePax = $pasajerosReserva->item(0)->getElementsByTagName("agePax");
if ($agePax->length > 0) {
    $agePax = $agePax->item(0)->nodeValue;
} else {
    $agePax = "";
}
$tipPax = $pasajerosReserva->item(0)->getElementsByTagName("tipPax");
if ($tipPax->length > 0) {
    $tipPax = $tipPax->item(0)->nodeValue;
} else {
    $tipPax = "";
}
try{
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('resumoReserva_pasajerosReserva');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'nomPax' => $nomPax,
        'apePax' => $apePax,
        'agePax' => $agePax,
        'tipPax' => $tipPax
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}catch(Exception $e){
    echo "<br/> Erro " . $e->getMessage();
}

$dgpReserva = $pasajerosReserva->item(0)->getElementsByTagName("dgpReserva");
if ($dgpReserva->length > 0) {
    for ($dgAux=0; $dgAux < $dgpReserva->length; $dgAux++) { 
        $codCam = $dgpReserva->item($dgAux)->getElementsByTagName("codCam");
        if ($codCam->length > 0) {
            $codCam = $codCam->item(0)->nodeValue;
        } else {
            $codCam = "";
        }
        $ordGrp = $dgpReserva->item($dgAux)->getElementsByTagName("ordGrp");
        if ($ordGrp->length > 0) {
            $ordGrp = $ordGrp->item(0)->nodeValue;
        } else {
            $ordGrp = "";
        }
        $dscGrp = $dgpReserva->item($dgAux)->getElementsByTagName("dscGrp");
        if ($dscGrp->length > 0) {
            $dscGrp = $dscGrp->item(0)->nodeValue;
        } else {
            $dscGrp = "";
        }
        $codTcd = $dgpReserva->item($dgAux)->getElementsByTagName("codTcd");
        if ($codTcd->length > 0) {
            $codTcd = $codTcd->item(0)->nodeValue;
        } else {
            $codTcd = "";
        }
        $tipAux = $dgpReserva->item($dgAux)->getElementsByTagName("tipAux");
        if ($tipAux->length > 0) {
            $tipAux = $tipAux->item(0)->nodeValue;
        } else {
            $tipAux = "";
        }
        $dscTcd = $dgpReserva->item($dgAux)->getElementsByTagName("dscTcd");
        if ($dscTcd->length > 0) {
            $dscTcd = $dscTcd->item(0)->nodeValue;
        } else {
            $dscTcd = "";
        }
        $dscAux = $dgpReserva->item($dgAux)->getElementsByTagName("dscAux");
        if ($dscAux->length > 0) {
            $dscAux = $dscAux->item(0)->nodeValue;
        } else {
            $dscAux = "";
        }
        $numUni = $dgpReserva->item($dgAux)->getElementsByTagName("numUni");
        if ($numUni->length > 0) {
            $numUni = $numUni->item(0)->nodeValue;
        } else {
            $numUni = "";
        }
        $impBru = $dgpReserva->item($dgAux)->getElementsByTagName("impBru");
        if ($impBru->length > 0) {
            $impBru = $impBru->item(0)->nodeValue;
        } else {
            $impBru = "";
        }
        $bruTot = $dgpReserva->item($dgAux)->getElementsByTagName("bruTot");
        if ($bruTot->length > 0) {
            $bruTot = $bruTot->item(0)->nodeValue;
        } else {
            $bruTot = "";
        }
        $swiCom = $dgpReserva->item($dgAux)->getElementsByTagName("swiCom");
        if ($swiCom->length > 0) {
            $swiCom = $swiCom->item(0)->nodeValue;
        } else {
            $swiCom = "";
        }
        $swiOfe = $dgpReserva->item($dgAux)->getElementsByTagName("swiOfe");
        if ($swiOfe->length > 0) {
            $swiOfe = $swiOfe->item(0)->nodeValue;
        } else {
            $swiOfe = "";
        }
        $fecBaj = $dgpReserva->item($dgAux)->getElementsByTagName("fecBaj");
        if ($fecBaj->length > 0) {
            $fecBaj = $fecBaj->item(0)->nodeValue;
        } else {
            $fecBaj = "";
        }
        $seqItm = $dgpReserva->item($dgAux)->getElementsByTagName("seqItm");
        if ($seqItm->length > 0) {
            $seqItm = $seqItm->item(0)->nodeValue;
        } else {
            $seqItm = "";
        }
        $refSerAux = $dgpReserva->item($dgAux)->getElementsByTagName("refSerAux");
        if ($refSerAux->length > 0) {
            $refSerAux = $refSerAux->item(0)->nodeValue;
        } else {
            $refSerAux = "";
        }
        $refGrp = $dgpReserva->item($dgAux)->getElementsByTagName("refGrp");
        if ($refGrp->length > 0) {
            $refGrp = $refGrp->item(0)->nodeValue;
        } else {
            $refGrp = "";
        }
        $tipSer = $dgpReserva->item($dgAux)->getElementsByTagName("tipSer");
        if ($tipSer->length > 0) {
            $tipSer = $tipSer->item(0)->nodeValue;
        } else {
            $tipSer = "";
        }
        $fecIni = $dgpReserva->item($dgAux)->getElementsByTagName("fecIni");
        if ($fecIni->length > 0) {
            $fecIni = $fecIni->item(0)->nodeValue;
        } else {
            $fecIni = "";
        }
        $fecFin = $dgpReserva->item($dgAux)->getElementsByTagName("fecFin");
        if ($fecFin->length > 0) {
            $fecFin = $fecFin->item(0)->nodeValue;
        } else {
            $fecFin = "";
        }
        $ageIni = $dgpReserva->item($dgAux)->getElementsByTagName("ageIni");
        if ($ageIni->length > 0) {
            $ageIni = $ageIni->item(0)->nodeValue;
        } else {
            $ageIni = "";
        }
        $ageFin = $dgpReserva->item($dgAux)->getElementsByTagName("ageFin");
        if ($ageFin->length > 0) {
            $ageFin = $ageFin->item(0)->nodeValue;
        } else {
            $ageFin = "";
        }
        $dscSer = $dgpReserva->item($dgAux)->getElementsByTagName("dscSer");
        if ($dscSer->length > 0) {
            $dscSer = $dscSer->item(0)->nodeValue;
        } else {
            $dscSer = "";
        }

        try{
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('resumoReserva_PRdgpReserva');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codCam' => $codCam,
                'ordGrp' => $ordGrp,
                'dscGrp' => $dscGrp,
                'codTcd' => $codTcd,
                'tipAux' => $tipAux,
                'dscTcd' => $dscTcd,
                'dscAux' => $dscAux,
                'numUni' => $numUni,
                'impBru' => $impBru,
                'bruTot' => $bruTot,
                'swiCom' => $swiCom,
                'swiOfe' => $swiOfe,
                'fecBaj' => $fecBaj,
                'seqItm' => $seqItm,
                'refSerAux' => $refSerAux,
                'refGrp' => $refGrp,
                'tipSer' => $tipSer,
                'fecIni' => $fecIni,
                'fecFin' => $fecFin,
                'ageIni' => $ageIni,
                'ageFin' => $ageFin,
                'dscSer' => $dscSer
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }catch(Exception $e){
            echo "<br/> Erro2 " . $e->getMessage();
        }

    }
}

//datosCampanyas
$datosCampanyas = $resumenReservaV2Response->item(0)->getElementsByTagName("datosCampanyas");
if ($datosCampanyas->length > 0) {
    $datosCampanyas = $datosCampanyas->item(0)->nodeValue;
} else {
    $datosCampanyas = "";
}

//residente
$residente = $resumenReservaV2Response->item(0)->getElementsByTagName("residente");
if ($residente->length > 0) {
    $residente = $residente->item(0)->nodeValue;
} else {
    $residente = "";
}

//visado 
$visado = $resumenReservaV2Response->item(0)->getElementsByTagName("visado");
if ($visado->length > 0) {
    $visado = $visado->item(0)->nodeValue;
} else {
    $visado = "";
}
try{
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('resumoReserva');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'datosCampanyas' => $datosCampanyas,
        'residente' => $residente,
        'visado' => $visado
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}catch(Exception $e){
    echo "<br/> Erro " . $e->getMessage();
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>