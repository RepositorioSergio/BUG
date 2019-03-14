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
echo $return;
echo $url;
echo $return;
$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.circuito.dtp">    <soapenv:Header/>    <soapenv:Body>       <typ:buscadorCircuitosRequest>          <idClie>' . $globaliapackagesCustomerID . '</idClie>       </typ:buscadorCircuitosRequest>    </soapenv:Body> </soapenv:Envelope>';
echo $raw;
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
//include "/srv/www/htdocs/specialtours/travelplan/circuitos_debug.php";
echo "RESPONSE";
/* echo $return;
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
$buscadorCircuitosResponse = $Body->item(0)->getElementsByTagName("buscadorCircuitosResponse");
//arbolDestinos
$arbolDestinos = $buscadorCircuitosResponse->item(0)->getElementsByTagName("arbolDestinos");
$node = $arbolDestinos->item(0)->getElementsByTagName("arbolResponseRowType");
for ($iAux = 0; $iAux < $node->length; $iAux ++) {
    $tipo = $node->item($iAUX)->getElementsByTagName("tipo");
    if ($tipo->length > 0) {
        $tipo = $tipo->item(0)->nodeValue;
    } else {
        $tipo = "";
    }
    $codigo = $node->item($iAUX)->getElementsByTagName("codigo");
    if ($codigo->length > 0) {
        $codigo = $codigo->item(0)->nodeValue;
    } else {
        $codigo = "";
    }
    $desc = $node->item($iAUX)->getElementsByTagName("desc");
    if ($desc->length > 0) {
        $desc = $desc->item(0)->nodeValue;
    } else {
        $desc = "";
    }
    $codZona = $node->item($iAUX)->getElementsByTagName("codZona");
    if ($codZona->length > 0) {
        $codZona = $codZona->item(0)->nodeValue;
    } else {
        $codZona = "";
    }
    $descZona = $node->item($iAUX)->getElementsByTagName("descZona");
    if ($descZona->length > 0) {
        $descZona = $descZona->item(0)->nodeValue;
    } else {
        $descZona = "";
    }
    $cadenaDestino = $node->item($iAUX)->getElementsByTagName("cadenaDestino");
    if ($cadenaDestino->length > 0) {
        $cadenaDestino = $cadenaDestino->item(0)->nodeValue;
    } else {
        $cadenaDestino = "";
    }
    $residente = $node->item($iAUX)->getElementsByTagName("residente");
    if ($residente->length > 0) {
        $residente = $residente->item(0)->nodeValue;
    } else {
        $residente = "";
    }

    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('travelplan_arbolResponseRowTypeCont');
    $select->where(array(
        'codigo' => $codigo
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = $data['codigo'];
        if (count($id) > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'tipo' => $tipo,
                'codigo' => $codigo,
                'descr' => $desc,
                'codZona' => $codZona,
                'descZona' => $descZona,
                'cadenaDestino' => $cadenaDestino,
                'residente' => $residente
            );
            $where['codigo = ?'] = $codigo;
            $update = $sql->update('travelplan_arbolResponseRowTypeCont', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('travelplan_arbolResponseRowTypeCont');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'tipo' => $tipo,
                'codigo' => $codigo,
                'descr' => $desc,
                'codZona' => $codZona,
                'descZona' => $descZona,
                'cadenaDestino' => $cadenaDestino,
                'residente' => $residente
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
        $insert->into('travelplan_arbolResponseRowTypeCont');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'tipo' => $tipo,
            'codigo' => $codigo,
            'descr' => $desc,
            'codZona' => $codZona,
            'descZona' => $descZona,
            'cadenaDestino' => $cadenaDestino,
            'residente' => $residente
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
    
    $arbolDestinoResponseRowType = $node->item($iAUX)->getElementsByTagName("arbolDestinoResponseRowType");
    for ($kAux=0; $kAux < $arbolDestinoResponseRowType->length; $kAux++) { 
  
        $tipoB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("tipo");
        if ($tipoB->length > 0) {
            $tipoB = $tipoB->item(0)->nodeValue;
        } else {
            $tipoB = "";
        }
        $codigoB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("codigo");
        if ($codigoB->length > 0) {
            $codigoB = $codigoB->item(0)->nodeValue;
        } else {
            $codigoB = "";
        }
        $descB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("desc");
        if ($descB->length > 0) {
            $descB = $descB->item(0)->nodeValue;
        } else {
            $descB = "";
        }
        $descZonaB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("descZona");
        if ($descZonaB->length > 0) {
            $descZonaB = $descZonaB->item(0)->nodeValue;
        } else {
            $descZonaB = "";
        }
        $cadenaDestinoB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("cadenaDestino");
        if ($cadenaDestinoB->length > 0) {
            $cadenaDestinoB = $cadenaDestinoB->item(0)->nodeValue;
        } else {
            $cadenaDestinoB = "";
        }
        $residenteB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("residente");
        if ($residenteB->length > 0) {
            $residenteB = $residenteB->item(0)->nodeValue;
        } else {
            $residenteB = "";
        }
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('travelplan_arbolResponseRowTypePais');
        $select->where(array(
            'codigo' => $codigoB
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $result->buffer();
        $customers = array();
        if ($result->valid()) {
            $data = $result->current();
            $id = $data['codigo'];
            if (count($id) > 0) {
                $sql = new Sql($db);
                $data = array(
                    'datetime_created' => time(),
                    'datetime_updated' => 1,
                    'tipo' => $tipoB,
                    'codigo' => $codigoB,
                    'codigoCont' => $codigo,
                    'descr' => $descB,
                    'codZona' => $codZonaB,
                    'descZona' => $descZonaB,
                    'cadenaDestino' => $cadenaDestinoB,
                    'residente' => $residenteB
                );
                $where['codigo = ?'] = $codigoB;
                $update = $sql->update('travelplan_arbolResponseRowTypePais', $data, $where);
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('travelplan_arbolResponseRowTypePais');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'tipo' => $tipoB,
                    'codigo' => $codigoB,
                    'codigoCont' => $codigo,
                    'descr' => $descB,
                    'codZona' => $codZonaB,
                    'descZona' => $descZonaB,
                    'cadenaDestino' => $cadenaDestinoB,
                    'residente' => $residenteB
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
            $insert->into('travelplan_arbolResponseRowTypePais');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'tipo' => $tipoB,
                'codigo' => $codigoB,
                'codigoCont' => $codigo,
                'descr' => $descB,
                'codZona' => $codZonaB,
                'descZona' => $descZonaB,
                'cadenaDestino' => $cadenaDestinoB,
                'residente' => $residenteB
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    
        $arbolDestinoResponseRowTypeB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("arbolDestinoResponseRowType");
        for ($jAux = 0; $jAux < $arbolDestinoResponseRowTypeB->length; $jAux ++) {
            $tipoC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("tipo");
            if ($tipoC->length > 0) {
                $tipoC = $tipoC->item(0)->nodeValue;
            } else {
                $tipoC = "";
            }
            $codigoC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("codigo");
            if ($codigoC->length > 0) {
                $codigoC = $codigoC->item(0)->nodeValue;
            } else {
                $codigoC = "";
            }
            $descC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("desc");
            if ($descC->length > 0) {
                $descC = $descC->item(0)->nodeValue;
            } else {
                $descC = "";
            }
            $descZonaC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("descZona");
            if ($descZonaC->length > 0) {
                $descZonaC = $descZonaC->item(0)->nodeValue;
            } else {
                $descZonaC = "";
            }
            $cadenaDestinoC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("cadenaDestino");
            if ($cadenaDestinoC->length > 0) {
                $cadenaDestinoC = $cadenaDestinoC->item(0)->nodeValue;
            } else {
                $cadenaDestinoC = "";
            }
            $residenteC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("residente");
            if ($residenteC->length > 0) {
                $residenteC = $residenteC->item(0)->nodeValue;
            } else {
                $residenteC = "";
            }
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('travelplan_arbolResponseRowTypePoblacion');
            $select->where(array(
                'codigo' => $codigoC
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = $data['codigo'];
                if (count($id) > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'tipo' => $tipoC,
                        'codigo' => $codigoC,
                        'codigoPais' => $codigoB,
                        'descr' => $descC,
                        'codZona' => $codZonaC,
                        'descZona' => $descZonaC,
                        'cadenaDestino' => $cadenaDestinoC,
                        'residente' => $residenteC
                    );
                    $where['codigo = ?'] = $codigoC;
                    $update = $sql->update('travelplan_arbolResponseRowTypePoblacion', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('travelplan_arbolResponseRowTypePoblacion');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'tipo' => $tipoC,
                        'codigo' => $codigoC,
                        'codigoPais' => $codigoB,
                        'descr' => $descC,
                        'codZona' => $codZonaC,
                        'descZona' => $descZonaC,
                        'cadenaDestino' => $cadenaDestinoC,
                        'residente' => $residenteC
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
                $insert->into('travelplan_arbolResponseRowTypePoblacion');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'tipo' => $tipoC,
                    'codigo' => $codigoC,
                    'codigoPais' => $codigoB,
                    'descr' => $descC,
                    'codZona' => $codZonaC,
                    'descZona' => $descZonaC,
                    'cadenaDestino' => $cadenaDestinoC,
                    'residente' => $residenteC
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
echo "<br/> PASSOU AQUI AGRUPACION";
//agrupacion
$agrupacion = $buscadorCircuitosResponse->item(0)->getElementsByTagName("agrupacion");
$node = $agrupacion->item(0)->getElementsByTagName("arbolResponseRowType");
for ($iAux = 0; $iAux < $node->length; $iAux++) {
    $tipo = $node->item($iAUX)->getElementsByTagName("tipo");
    if ($tipo->length > 0) {
        $tipo = $tipo->item(0)->nodeValue;
    } else {
        $tipo = "";
    }
    $codigo = $node->item($iAUX)->getElementsByTagName("codigo");
    if ($codigo->length > 0) {
        $codigo = $codigo->item(0)->nodeValue;
    } else {
        $codigo = "";
    }
    $desc = $node->item($iAUX)->getElementsByTagName("desc");
    if ($desc->length > 0) {
        $desc = $desc->item(0)->nodeValue;
    } else {
        $desc = "";
    }
    $codZona = $node->item($iAUX)->getElementsByTagName("codZona");
    if ($codZona->length > 0) {
        $codZona = $codZona->item(0)->nodeValue;
    } else {
        $codZona = "";
    }
    $descZona = $node->item($iAUX)->getElementsByTagName("descZona");
    if ($descZona->length > 0) {
        $descZona = $descZona->item(0)->nodeValue;
    } else {
        $descZona = "";
    }
    $cadenaDestino = $node->item($iAUX)->getElementsByTagName("cadenaDestino");
    if ($cadenaDestino->length > 0) {
        $cadenaDestino = $cadenaDestino->item(0)->nodeValue;
    } else {
        $cadenaDestino = "";
    }
    $residente = $node->item($iAUX)->getElementsByTagName("residente");
    if ($residente->length > 0) {
        $residente = $residente->item(0)->nodeValue;
    } else {
        $residente = "";
    }

    try{
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('travelplan_agruparbolResponseRowTypeCont');
        $select->where(array(
            'codigo' => $codigo
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $result->buffer();
        $customers = array();
        if ($result->valid()) {
            $data = $result->current();
            $id = $data['codigo'];
            if (count($id) > 0) {
                $sql = new Sql($db);
                $data = array(
                    'datetime_created' => time(),
                    'datetime_updated' => 1,
                    'tipo' => $tipo,
                    'codigo' => $codigo,
                    'descr' => $desc,
                    'codZona' => $codZona,
                    'descZona' => $descZona,
                    'cadenaDestino' => $cadenaDestino,
                    'residente' => $residente
                );
                $where['codigo = ?'] = $codigo;
                $update = $sql->update('travelplan_agruparbolResponseRowTypeCont', $data, $where);
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('travelplan_agruparbolResponseRowTypeCont');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'tipo' => $tipo,
                    'codigo' => $codigo,
                    'descr' => $desc,
                    'codZona' => $codZona,
                    'descZona' => $descZona,
                    'cadenaDestino' => $cadenaDestino,
                    'residente' => $residente
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
            $insert->into('travelplan_agruparbolResponseRowTypeCont');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'tipo' => $tipo,
                'codigo' => $codigo,
                'descr' => $desc,
                'codZona' => $codZona,
                'descZona' => $descZona,
                'cadenaDestino' => $cadenaDestino,
                'residente' => $residente
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    }catch(Exception $e){
        echo "<br/> Erro " . $e->getMessage();
    }
    
    
    $arbolDestinoResponseRowType = $node->item($iAUX)->getElementsByTagName("arbolDestinoResponseRowType");
    for ($kAux=0; $kAux < $arbolDestinoResponseRowType->length; $kAux++) { 
  
        $tipoB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("tipo");
        if ($tipoB->length > 0) {
            $tipoB = $tipoB->item(0)->nodeValue;
        } else {
            $tipoB = "";
        }
        $codigoB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("codigo");
        if ($codigoB->length > 0) {
            $codigoB = $codigoB->item(0)->nodeValue;
        } else {
            $codigoB = "";
        }
        $descB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("desc");
        if ($descB->length > 0) {
            $descB = $descB->item(0)->nodeValue;
        } else {
            $descB = "";
        }
        $descZonaB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("descZona");
        if ($descZonaB->length > 0) {
            $descZonaB = $descZonaB->item(0)->nodeValue;
        } else {
            $descZonaB = "";
        }
        $cadenaDestinoB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("cadenaDestino");
        if ($cadenaDestinoB->length > 0) {
            $cadenaDestinoB = $cadenaDestinoB->item(0)->nodeValue;
        } else {
            $cadenaDestinoB = "";
        }
        $residenteB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("residente");
        if ($residenteB->length > 0) {
            $residenteB = $residenteB->item(0)->nodeValue;
        } else {
            $residenteB = "";
        }

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('travelplan_agruparbolDestinoResponseRowTypePais');
            $select->where(array(
                'codigo' => $codigoB
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = $data['codigo'];
                if (count($id) > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'tipo' => $tipoB,
                        'codigo' => $codigoB,
                        'codigoCont' => $codigo,
                        'descr' => $descB,
                        'codZona' => $codZonaB,
                        'descZona' => $descZonaB,
                        'cadenaDestino' => $cadenaDestinoB,
                        'residente' => $residenteB
                    );
                    $where['codigo = ?'] = $codigoB;
                    $update = $sql->update('travelplan_agruparbolDestinoResponseRowTypePais', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('travelplan_agruparbolDestinoResponseRowTypePais');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'tipo' => $tipoB,
                        'codigo' => $codigoB,
                        'codigoCont' => $codigo,
                        'descr' => $descB,
                        'codZona' => $codZonaB,
                        'descZona' => $descZonaB,
                        'cadenaDestino' => $cadenaDestinoB,
                        'residente' => $residenteB
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
                $insert->into('travelplan_agruparbolDestinoResponseRowTypePais');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'tipo' => $tipoB,
                    'codigo' => $codigoB,
                    'codigoCont' => $codigo,
                    'descr' => $descB,
                    'codZona' => $codZonaB,
                    'descZona' => $descZonaB,
                    'cadenaDestino' => $cadenaDestinoB,
                    'residente' => $residenteB
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            }
        } catch (Exception $e) {
            echo "<br/> Erro " . $e->getMessage();
        }
        
    
        $arbolDestinoResponseRowTypeB = $arbolDestinoResponseRowType->item($kAux)->getElementsByTagName("arbolDestinoResponseRowType");
        for ($jAux = 0; $jAux < $arbolDestinoResponseRowTypeB->length; $jAux ++) {
            $tipoC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("tipo");
            if ($tipoC->length > 0) {
                $tipoC = $tipoC->item(0)->nodeValue;
            } else {
                $tipoC = "";
            }
            $codigoC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("codigo");
            if ($codigoC->length > 0) {
                $codigoC = $codigoC->item(0)->nodeValue;
            } else {
                $codigoC = "";
            }
            $descC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("desc");
            if ($descC->length > 0) {
                $descC = $descC->item(0)->nodeValue;
            } else {
                $descC = "";
            }
            $descZonaC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("descZona");
            if ($descZonaC->length > 0) {
                $descZonaC = $descZonaC->item(0)->nodeValue;
            } else {
                $descZonaC = "";
            }
            $cadenaDestinoC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("cadenaDestino");
            if ($cadenaDestinoC->length > 0) {
                $cadenaDestinoC = $cadenaDestinoC->item(0)->nodeValue;
            } else {
                $cadenaDestinoC = "";
            }
            $residenteC = $arbolDestinoResponseRowTypeB->item($jAux)->getElementsByTagName("residente");
            if ($residenteC->length > 0) {
                $residenteC = $residenteC->item(0)->nodeValue;
            } else {
                $residenteC = "";
            }

            try {
                $sql = new Sql($db);
            $select = $sql->select();
            $select->from('travelplan_agruparbolDestinoResponseRowTypePoblacion');
            $select->where(array(
                'codigo' => $codigoC
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = $data['codigo'];
                if (count($id) > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'tipo' => $tipoC,
                        'codigo' => $codigoC,
                        'codigoPais' => $codigoB,
                        'descr' => $descC,
                        'codZona' => $codZonaC,
                        'descZona' => $descZonaC,
                        'cadenaDestino' => $cadenaDestinoC,
                        'residente' => $residenteC
                    );
                    $where['codigo = ?'] = $codigoC;
                    $update = $sql->update('travelplan_agruparbolDestinoResponseRowTypePoblacion', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('travelplan_agruparbolDestinoResponseRowTypePoblacion');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'tipo' => $tipoC,
                        'codigo' => $codigoC,
                        'codigoPais' => $codigoB,
                        'descr' => $descC,
                        'codZona' => $codZonaC,
                        'descZona' => $descZonaC,
                        'cadenaDestino' => $cadenaDestinoC,
                        'residente' => $residenteC
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                }
            } else {
                try{
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('travelplan_agruparbolDestinoResponseRowTypePoblacion');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'tipo' => $tipoC,
                        'codigo' => $codigoC,
                        'codigoPais' => $codigoB,
                        'descr' => $descC,
                        'codZona' => $codZonaC,
                        'descZona' => $descZonaC,
                        'cadenaDestino' => $cadenaDestinoC,
                        'residente' => $residenteC
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    }catch(Exception $e){
                        echo "Erro " . $e->getMessage();  
                    }
                    
                }
            } catch (Exception $e) {
                echo "<br/> Erro " . $e->getMessage();
            }
            
        }
    }
} 
//meses
$meses = $buscadorCircuitosResponse->item(0)->getElementsByTagName("meses");
$mes = $meses->item(0)->getElementsByTagName("mes");
for ($yAux=0; $yAux < $mes->length; $yAux++) { 
    $anomes = $mes->item(0)->nodeValue;
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('travelplan_meses');
    $select->where(array(
        'mes' => $anomes
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = $data['mes'];
        if (count($id) > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'mes' => $anomes
            );
            $where['mes = ?'] = $anomes;
            $update = $sql->update('travelplan_meses', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('travelplan_meses');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'mes' => $anomes
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
        $insert->into('travelplan_meses');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'mes' => $anomes
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
}
//noches
$noches = $buscadorCircuitosResponse->item(0)->getElementsByTagName("noches");
$numNoc = $noches->item(0)->getElementsByTagName("numNoc");
for ($zAux=0; $zAux < $numNoc->length; $zAux++) { 
    $numNoc2 = $numNoc->item($zAux)->nodeValue;
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('travelplan_noches');
    $select->where(array(
        'numNoc' => $numNoc2
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = (int) $data['numNoc'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'numNoc' => $numNoc2
            );
            $where['numNoc = ?'] = $numNoc2;
            $update = $sql->update('travelplan_noches', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('travelplan_noches');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'numNoc' => $numNoc2
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
        $insert->into('travelplan_noches');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'numNoc' => $numNoc2
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
}

//categorias
$categorias = $buscadorCircuitosResponse->item(0)->getElementsByTagName("categorias");
$codCat = $categorias->item(0)->getElementsByTagName("codCat");
for ($aAux=0; $aAux < $codCat->length; $aAux++) { 
    $codCat2 = $codCat->item($aAux)->nodeValue;
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('travelplan_categories');
    $select->where(array(
        'codCat' => $codCat2
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = $data['codCat'];
        if (count($id) > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'codCat' => $codCat2
            );
            $where['codCat = ?'] = $codCat2;
            $update = $sql->update('travelplan_categories', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('travelplan_categories');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codCat' => $codCat2
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
        $insert->into('travelplan_categories');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codCat' => $codCat2
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
}


//regimenes
$regimenes = $buscadorCircuitosResponse->item(0)->getElementsByTagName("regimenes");
$codRga = $regimenes->item(0)->getElementsByTagName("codRga");
for ($bAux=0; $bAux < $codRga->length; $bAux++) { 
    $codRga2 = $codRga->item($bAux)->nodeValue;

    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('travelplan_regimenes');
    $select->where(array(
        'codRga' => $codRga2
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
                'codRga' => $codRga2
            );
            $where['codRga = ?'] = $codRga2;
            $update = $sql->update('travelplan_regimenes', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('travelplan_regimenes');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codRga' => $codRga2
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
        $insert->into('travelplan_regimenes');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codRga' => $codRga2
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
}


//ciudadesInicio
$ciudadesInicio = $buscadorCircuitosResponse->item(0)->getElementsByTagName("ciudadesInicio");
$ciudadInicio = $ciudadesInicio->item(0)->getElementsByTagName("ciudadInicio");
for ($cAux=0; $cAux < $ciudadInicio->length; $cAux++) { 
    $codInicio = $ciudadInicio->item($cAux)->getElementsByTagName("codInicio");
    if ($codInicio->length > 0) {
        $codInicio = $codInicio->item(0)->nodeValue;
    } else {
        $codInicio = "";
    }
    $descInicio = $ciudadInicio->item($cAux)->getElementsByTagName("descInicio");
    if ($descInicio->length > 0) {
        $descInicio = $descInicio->item(0)->nodeValue;
    } else {
        $descInicio = "";
    }

    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('travelplan_ciudadesInicio');
    $select->where(array(
        'codInicio' => $codInicio
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = $data['codInicio'];
        if (count($id) > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'codInicio' => $codInicio,
                'descInicio' => $descInicio
            );
            $where['codInicio = ?'] = $codInicio;
            $update = $sql->update('travelplan_ciudadesInicio', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('travelplan_ciudadesInicio');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codInicio' => $codInicio,
                'descInicio' => $descInicio
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
        $insert->into('travelplan_ciudadesInicio');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codInicio' => $codInicio,
            'descInicio' => $descInicio
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