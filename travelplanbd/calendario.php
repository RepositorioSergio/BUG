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

$url = $globaliapackagesserviceURL . 'services/wstCircuito';

$ideClie = "CTMT0";
$aptDep = "MAD";
$numNoc = 7;
$mes = "2019-04";
$codAgr = "PAR56";
$edad = "30";
$codIdi = "ESP";
$numCat = "CL";
$codRga = "TI";
// Removed <aptDep>' . $aptDep . '</aptDep>
$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.circuito.dtp"><soapenv:Header/><soapenv:Body><typ:calendarioCircuitosRequest><ideClie>' . $ideClie . '</ideClie><codIdi>' . $codIdi . '</codIdi><codAgr>' . $codAgr . '</codAgr><mes>' . $mes . '</mes><numCat>' . $numCat . '</numCat><codRga>' . $codRga . '</codRga><numNoc>' . $numNoc . '</numNoc><habitaciones><edad>' . $edad . '</edad></habitaciones></typ:calendarioCircuitosRequest></soapenv:Body></soapenv:Envelope> ';

$client = new Client();
$client->setOptions(array(
    'timeout' => 10,
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
$calendarioCircuitosResponse = $Body->item(0)->getElementsByTagName("calendarioCircuitosResponse");
$node = $calendarioCircuitosResponse->item(0)->getElementsByTagName("calendarioCircuitosResponseRow");
if ($node->length > 0) {
    for ($iAux = 0; $iAux < $node->length; $iAux ++) {
        $fecSal = $node->item($iAUX)->getElementsByTagName("fecSal");
        if ($fecSal->length > 0) {
            $fecSal = $fecSal->item(0)->nodeValue;
        } else {
            $fecSal = "";
        }
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('calendario_CircuitosResponseRow');
        $select->where(array(
            'fecSal' => $fecSal
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $result->buffer();
        $customers = array();
        if ($result->valid()) {
            $data = $result->current();
            $id = $data['fecSal'];
            if (count($id) > 0) {
                $sql = new Sql($db);
                $data = array(
                    'datetime_created' => time(),
                    'datetime_updated' => 1,
                    'fecSal' => $fecSal
                );
                $where['fecSal = ?'] = $fecSal;
                $update = $sql->update('calendario_CircuitosResponseRow', $data, $where);
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('calendario_CircuitosResponseRow');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'fecSal' => $fecSal
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
            $insert->into('calendario_CircuitosResponseRow');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'fecSal' => $fecSal
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
        
        $serviciosCircuito = $node->item($iAUX)->getElementsByTagName("serviciosCircuito");
        for ($jAux = 0; $jAux < $serviciosCircuito->length; $jAux ++) {
            $refSer = $serviciosCircuito->item($jAUX)->getElementsByTagName("refSer");
            if ($refSer->length > 0) {
                $refSer = $refSer->item(0)->nodeValue;
            } else {
                $refSer = "";
            }
            $ideSer = $serviciosCircuito->item($jAUX)->getElementsByTagName("ideSer");
            if ($ideSer->length > 0) {
                $ideSer = $ideSer->item(0)->nodeValue;
            } else {
                $ideSer = "";
            }
            $nomSer = $serviciosCircuito->item($jAUX)->getElementsByTagName("nomSer");
            if ($nomSer->length > 0) {
                $nomSer = $nomSer->item(0)->nodeValue;
            } else {
                $nomSer = "";
            }
            $codCsc = $serviciosCircuito->item($jAUX)->getElementsByTagName("codCsc");
            if ($codCsc->length > 0) {
                $codCsc = $codCsc->item(0)->nodeValue;
            } else {
                $codCsc = "";
            }
            $codRga = $serviciosCircuito->item($jAUX)->getElementsByTagName("codRga");
            if ($codRga->length > 0) {
                $codRga = $codRga->item(0)->nodeValue;
            } else {
                $codRga = "";
            }
            $numNoc = $serviciosCircuito->item($jAUX)->getElementsByTagName("numNoc");
            if ($numNoc->length > 0) {
                $numNoc = $numNoc->item(0)->nodeValue;
            } else {
                $numNoc = "";
            }
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('calendario_serviciosCircuito');
            $select->where(array(
                'refSer' => $refSer
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (int) $data['refSer'];
                if ($id > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'refSer' => $refSer,
                        'ideSer' => $ideSer,
                        'nomSer' => $nomSer,
                        'codCsc' => $codCsc,
                        'codRga' => $codRga,
                        'numNoc' => $numNoc
                    );
                    $where['refSer = ?'] = $refSer;
                    $update = $sql->update('calendario_serviciosCircuito', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('calendario_serviciosCircuito');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'refSer' => $refSer,
                        'ideSer' => $ideSer,
                        'nomSer' => $nomSer,
                        'codCsc' => $codCsc,
                        'codRga' => $codRga,
                        'numNoc' => $numNoc
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
                $insert->into('calendario_serviciosCircuito');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'refSer' => $refSer,
                    'ideSer' => $ideSer,
                    'nomSer' => $nomSer,
                    'codCsc' => $codCsc,
                    'codRga' => $codRga,
                    'numNoc' => $numNoc
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            }
            
            // productosCircuito
            $productosCircuito = $serviciosCircuito->item($jAUX)->getElementsByTagName("productosCircuito");
            $refPro = $productosCircuito->item(0)->getElementsByTagName("refPro");
            if ($refPro->length > 0) {
                $refPro = $refPro->item(0)->nodeValue;
            } else {
                $refPro = "";
            }
            $SuplAerIda = $productosCircuito->item(0)->getElementsByTagName("SuplAerIda");
            if ($SuplAerIda->length > 0) {
                $SuplAerIda = $SuplAerIda->item(0)->nodeValue;
            } else {
                $SuplAerIda = "";
            }
            $SuplAerVue = $productosCircuito->item(0)->getElementsByTagName("SuplAerVue");
            if ($SuplAerVue->length > 0) {
                $SuplAerVue = $SuplAerVue->item(0)->nodeValue;
            } else {
                $SuplAerVue = "";
            }
            $idePro = $productosCircuito->item(0)->getElementsByTagName("idePro");
            if ($idePro->length > 0) {
                $idePro = $idePro->item(0)->nodeValue;
            } else {
                $idePro = "";
            }
            $idGrup = $productosCircuito->item(0)->getElementsByTagName("idGrup");
            if ($idGrup->length > 0) {
                $idGrup = $idGrup->item(0)->nodeValue;
            } else {
                $idGrup = "";
            }
            $nomLar = $productosCircuito->item(0)->getElementsByTagName("nomLar");
            if ($nomLar->length > 0) {
                $nomLar = $nomLar->item(0)->nodeValue;
            } else {
                $nomLar = "";
            }
            $fecUltReg = $productosCircuito->item(0)->getElementsByTagName("fecUltReg");
            if ($fecUltReg->length > 0) {
                $fecUltReg = $fecUltReg->item(0)->nodeValue;
            } else {
                $fecUltReg = "";
            }
            
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('calendario_productosCircuito');
            $select->where(array(
                'refPro' => $refPro
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (int) $data['refSer'];
                if ($id > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'refPro' => $refPro,
                        'SuplAerIda' => $SuplAerIda,
                        'SuplAerVue' => $SuplAerVue,
                        'idePro' => $idePro,
                        'idGrup' => $idGrup,
                        'nomLar' => $nomLar,
                        'fecUltReg' => $fecUltReg
                    );
                    $where['refPro = ?'] = $refPro;
                    $update = $sql->update('calendario_productosCircuito', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('calendario_productosCircuito');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'refPro' => $refPro,
                        'SuplAerIda' => $SuplAerIda,
                        'SuplAerVue' => $SuplAerVue,
                        'idePro' => $idePro,
                        'idGrup' => $idGrup,
                        'nomLar' => $nomLar,
                        'fecUltReg' => $fecUltReg
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
                $insert->into('calendario_productosCircuito');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'refPro' => $refPro,
                    'SuplAerIda' => $SuplAerIda,
                    'SuplAerVue' => $SuplAerVue,
                    'idePro' => $idePro,
                    'idGrup' => $idGrup,
                    'nomLar' => $nomLar,
                    'fecUltReg' => $fecUltReg
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            }
            
            // preciosRegimen
            $preciosRegimen = $productosCircuito->item(0)->getElementsByTagName("preciosRegimen");
            $codRga = $preciosRegimen->item(0)->getElementsByTagName("codRga");
            if ($codRga->length > 0) {
                $codRga = $codRga->item(0)->nodeValue;
            } else {
                $codRga = "";
            }
            $pvp = $preciosRegimen->item(0)->getElementsByTagName("pvp");
            if ($pvp->length > 0) {
                $pvp = $pvp->item(0)->nodeValue;
            } else {
                $pvp = "";
            }
            $cadRes = $preciosRegimen->item(0)->getElementsByTagName("codRes");
            if ($cadRes->length > 0) {
                $cadRes = $cadRes->item(0)->nodeValue;
            } else {
                $cadRes = "";
            }
            $status = $preciosRegimen->item(0)->getElementsByTagName("status");
            if ($status->length > 0) {
                $status = $status->item(0)->nodeValue;
            } else {
                $status = "";
            }
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('calendario_preciosRegimen');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codRga' => $codRga,
                'pvp' => $pvp,
                'cadRes' => $cadRes,
                'status' => $status
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();

            /* $sql = new Sql($db);
            $select = $sql->select();
            $select->from('calendario_preciosRegimen');
            $select->where(array(
                'codRga' => $codRga
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = $data['codRga'];
                if (count($id) > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'codRga' => $codRga,
                        'pvp' => $pvp,
                        'cadRes' => $cadRes,
                        'status' => $status
                    );
                    $where['codRga = ?'] = $codRga;
                    $update = $sql->update('calendario_preciosRegimen', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('calendario_preciosRegimen');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'codRga' => $codRga,
                        'pvp' => $pvp,
                        'cadRes' => $cadRes,
                        'status' => $status
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
                $insert->into('calendario_preciosRegimen');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'codRga' => $codRga,
                    'pvp' => $pvp,
                    'cadRes' => $cadRes,
                    'status' => $status
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } */
            
            $tiposHabitacion = $preciosRegimen->item(0)->getElementsByTagName("tiposHabitacion");
            $pvpHab = $tiposHabitacion->item(0)->getElementsByTagName("pvpHab");
            if ($pvpHab->length > 0) {
                $pvpHab = $pvpHab->item(0)->nodeValue;
            } else {
                $pvpHab = "";
            }
            $codGtr = $tiposHabitacion->item(0)->getElementsByTagName("codGtr");
            if ($codGtr->length > 0) {
                $codGtr = $codGtr->item(0)->nodeValue;
            } else {
                $codGtr = "";
            }
            $modDet = $tiposHabitacion->item(0)->getElementsByTagName("modDet");
            if ($modDet->length > 0) {
                $modDet = $modDet->item(0)->nodeValue;
            } else {
                $modDet = "";
            }
            $dscDet = $tiposHabitacion->item(0)->getElementsByTagName("dscDet");
            if ($dscDet->length > 0) {
                $dscDet = $dscDet->item(0)->nodeValue;
            } else {
                $dscDet = "";
            }
            $codCha = $tiposHabitacion->item(0)->getElementsByTagName("codCha");
            if ($codCha->length > 0) {
                $codCha = $codCha->item(0)->nodeValue;
            } else {
                $codCha = "";
            }
            $dscCha = $tiposHabitacion->item(0)->getElementsByTagName("dscCha");
            if ($dscCha->length > 0) {
                $dscCha = $dscCha->item(0)->nodeValue;
            } else {
                $dscCha = "";
            }
            $paxMin = $tiposHabitacion->item(0)->getElementsByTagName("paxMin");
            if ($paxMin->length > 0) {
                $paxMin = $paxMin->item(0)->nodeValue;
            } else {
                $paxMin = "";
            }
            $paxMax = $tiposHabitacion->item(0)->getElementsByTagName("paxMax");
            if ($paxMax->length > 0) {
                $paxMax = $paxMax->item(0)->nodeValue;
            } else {
                $paxMax = "";
            }
            $paxMax = $tiposHabitacion->item(0)->getElementsByTagName("paxMax");
            if ($paxMax->length > 0) {
                $paxMax = $paxMax->item(0)->nodeValue;
            } else {
                $paxMax = "";
            }
            $chlMin = $tiposHabitacion->item(0)->getElementsByTagName("chlMin");
            if ($chlMin->length > 0) {
                $chlMin = $chlMin->item(0)->nodeValue;
            } else {
                $chlMin = "";
            }
            $chlMax = $tiposHabitacion->item(0)->getElementsByTagName("chlMax");
            if ($chlMax->length > 0) {
                $chlMax = $chlMax->item(0)->nodeValue;
            } else {
                $chlMax = "";
            }
            $paxTot = $tiposHabitacion->item(0)->getElementsByTagName("paxTot");
            if ($paxTot->length > 0) {
                $paxTot = $paxTot->item(0)->nodeValue;
            } else {
                $paxTot = "";
            }
            $paxTotMax = $tiposHabitacion->item(0)->getElementsByTagName("paxTotMax");
            if ($paxTotMax->length > 0) {
                $paxTotMax = $paxTotMax->item(0)->nodeValue;
            } else {
                $paxTotMax = "";
            }
            $itmPrd = $tiposHabitacion->item(0)->getElementsByTagName("itmPrd");
            if ($itmPrd->length > 0) {
                $itmPrd = $itmPrd->item(0)->nodeValue;
            } else {
                $itmPrd = "";
            }
            
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('calendario_tiposHabitacion');
            $select->where(array(
                'modDet' => $modDet
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = $data['modDet'];
                if (count($id) > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'pvpHab' => $pvpHab,
                        'codGtr' => $codGtr,
                        'modDet' => $modDet,
                        'dscDet' => $dscDet,
                        'codCha' => $codCha,
                        'dscCha' => $dscCha,
                        'paxMin' => $paxMin,
                        'paxMax' => $paxMax,
                        'chlMin' => $chlMin,
                        'chlMax' => $chlMax,
                        'paxTot' => $paxTot,
                        'paxTotMax' => $paxTotMax,
                        'itmPrd' => $itmPrd
                    );
                    $where['modDet = ?'] = $modDet;
                    $update = $sql->update('calendario_tiposHabitacion', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('calendario_tiposHabitacion');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'pvpHab' => $pvpHab,
                        'codGtr' => $codGtr,
                        'modDet' => $modDet,
                        'dscDet' => $dscDet,
                        'codCha' => $codCha,
                        'dscCha' => $dscCha,
                        'paxMin' => $paxMin,
                        'paxMax' => $paxMax,
                        'chlMin' => $chlMin,
                        'chlMax' => $chlMax,
                        'paxTot' => $paxTot,
                        'paxTotMax' => $paxTotMax,
                        'itmPrd' => $itmPrd
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
                $insert->into('calendario_tiposHabitacion');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'pvpHab' => $pvpHab,
                    'codGtr' => $codGtr,
                    'modDet' => $modDet,
                    'dscDet' => $dscDet,
                    'codCha' => $codCha,
                    'dscCha' => $dscCha,
                    'paxMin' => $paxMin,
                    'paxMax' => $paxMax,
                    'chlMin' => $chlMin,
                    'chlMax' => $chlMax,
                    'paxTot' => $paxTot,
                    'paxTotMax' => $paxTotMax,
                    'itmPrd' => $itmPrd
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/> Done';
?>
