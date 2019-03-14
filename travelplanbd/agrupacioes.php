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

$idClie = "CTMT0";
$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.circuito.dtp"><soapenv:Header/><soapenv:Body><typ:agrupacionesCircuitoRequest><idClie>' . $idClie . '</idClie></typ:agrupacionesCircuitoRequest></soapenv:Body></soapenv:Envelope>';

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
//include "/srv/www/htdocs/specialtours/travelplan/agrupaciones_debug.php";
/*
 * echo $return;
 * echo $response;
 * echo $return;
 */
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
$agrupacionesCircuitoResponse = $Body->item(0)->getElementsByTagName("agrupacionesCircuitoResponse");
$node = $agrupacionesCircuitoResponse->item(0)->getElementsByTagName("agrupacionesCircuitoResponseRow");
for ($iAux = 0; $iAux < $node->length; $iAux ++) {
    $codAgr = $node->item($iAUX)->getElementsByTagName("codAgr");
    if ($codAgr->length > 0) {
        $codAgr = $codAgr->item(0)->nodeValue;
    } else {
        $codAgr = "";
    }
    $descAgr = $node->item($iAUX)->getElementsByTagName("descAgr");
    if ($descAgr->length > 0) {
        $descAgr = $descAgr->item(0)->nodeValue;
    } else {
        $descAgr = "";
    }
    $imagen = $node->item($iAUX)->getElementsByTagName("imagen");
    if ($imagen->length > 0) {
        $imagen = $imagen->item(0)->nodeValue;
    } else {
        $imagen = "";
    }
    $swiVue = $node->item($iAUX)->getElementsByTagName("swiVue");
    if ($swiVue->length > 0) {
        $swiVue = $swiVue->item(0)->nodeValue;
    } else {
        $swiVue = "";
    }
    $seqItm = $node->item($iAUX)->getElementsByTagName("seqItm");
    if ($seqItm->length > 0) {
        $seqItm = $seqItm->item(0)->nodeValue;
    } else {
        $seqItm = "";
    }
    
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('agrupacion_arbolResponseRowTypeCont');
    $select->where(array(
        'codAgr' => $codAgr
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = $data['codAgr'];
        if (count($id) > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'codAgr' => $codAgr,
                'descAgr' => $descAgr,
                'imagen' => $imagen,
                'swiVue' => $swiVue,
                'seqItm' => $seqItm
            );
            $where['codAgr = ?'] = $codAgr;
            $update = $sql->update('agrupacion_arbolResponseRowTypeCont', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('agrupacion_arbolResponseRowTypeCont');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codAgr' => $codAgr,
                'descAgr' => $descAgr,
                'imagen' => $imagen,
                'swiVue' => $swiVue,
                'seqItm' => $seqItm
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
        $insert->into('agrupacion_arbolResponseRowTypeCont');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codAgr' => $codAgr,
            'descAgr' => $descAgr,
            'imagen' => $imagen,
            'swiVue' => $swiVue,
            'seqItm' => $seqItm
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
    
    $numNoc = $node->item($iAUX)->getElementsByTagName("numNoc");
    for ($jAux = 0; $jAux < $numNoc->length; $jAux ++) {
        $numNoc = $numNoc->item($jAux)->nodeValue;
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('agrupacion_numNoc');
        $select->where(array(
            'numNoc' => $numNoc
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
                    'numNoc' => $numNoc
                );
                $where['numNoc = ?'] = $numNoc;
                $update = $sql->update('agrupacion_numNoc', $data, $where);
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('agrupacion_numNoc');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
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
            $insert->into('agrupacion_numNoc');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'numNoc' => $numNoc
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    }
    
    $listaRga = $node->item($iAUX)->getElementsByTagName("listaRga");
    for ($dAux = 0; $dAux < $listaRga->length; $dAux ++) {
        
        $codRga = $listaRga->item($dAux)->getElementsByTagName("codRga");
        if ($codRga->length > 0) {
            $codRga = $codRga->item(0)->nodeValue;
        } else {
            $codRga = "";
        }
        $desRga = $listaRga->item($dAux)->getElementsByTagName("desRga");
        if ($desRga->length > 0) {
            $desRga = $desRga->item(0)->nodeValue;
        } else {
            $desRga = "";
        }
        
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('agrupacion_listaRga');
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
                    'desRga' => $desRga
                );
                $where['codRga = ?'] = $codRga;
                $update = $sql->update('agrupacion_listaRga', $data, $where);
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('agrupacion_listaRga');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'codRga' => $codRga,
                    'desRga' => $desRga
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
            $insert->into('agrupacion_listaRga');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codRga' => $codRga,
                'desRga' => $desRga
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    }
    
    $listaCat = $node->item($iAUX)->getElementsByTagName("listaCat");
    for ($eAux = 0; $eAux < $listaCat->length; $eAux ++) {
        
        $codCat = $listaCat->item($eAux)->getElementsByTagName("codCat");
        if ($codCat->length > 0) {
            $codCat = $codCat->item(0)->nodeValue;
        } else {
            $codCat = "";
        }
        $desCat = $listaCat->item($eAux)->getElementsByTagName("desCat");
        if ($desCat->length > 0) {
            $desCat = $desCat->item(0)->nodeValue;
        } else {
            $desCat = "";
        }
        
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('agrupacion_listaCat');
        $select->where(array(
            'codCat' => $codCat
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
                    'codCat' => $codCat,
                    'desCat' => $desCat
                );
                $where['codCat = ?'] = $codCat;
                $update = $sql->update('agrupacion_listaCat', $data, $where);
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('agrupacion_listaCat');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'codCat' => $codCat,
                    'desCat' => $desCat
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
            $insert->into('agrupacion_listaCat');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codCat' => $codCat,
                'desCat' => $desCat
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/> Done';
?>