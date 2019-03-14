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

$url = $globaliapackagesserviceURL . 'b2c/services/wstReserva';

$ideSes = "52535383408122501180";
$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.reserva.wst">    <soapenv:Header/>    <soapenv:Body>       <typ:politicaCancelacionRequest>          <typ:idUsuario>' . $globaliapackagesCustomerID . '</typ:idUsuario>          <typ:ideSes>' . $ideSes . '</typ:ideSes>       </typ:politicaCancelacionRequest>    </soapenv:Body> </soapenv:Envelope>';

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
//include "/srv/www/htdocs/specialtours/travelplan/agrupaciones_debug.php";
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
$politicaCancelacionResponse = $Body->item(0)->getElementsByTagName("politicaCancelacionResponse");
$cancelaGastos = $politicaCancelacionResponse->item(0)->getElementsByTagName("cancelaGastos");
$tipGasto = $cancelaGastos->item(0)->getElementsByTagName("tipGasto");
if ($tipGasto->length > 0) {
    $tipGasto = $tipGasto->item(0)->nodeValue;
} else {
    $tipGasto = "";
}

$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('politicaCancelacion_tipGasto');
$insert->values(array(
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'tipGasto' => $tipGasto
), $insert::VALUES_MERGE);
$statement = $sql->prepareStatementForSqlObject($insert);
$results = $statement->execute();
$db->getDriver()
    ->getConnection()
    ->disconnect();

$node = $cancelaGastos->item(0)->getElementsByTagName("contenidoGasto");
for ($iAux = 0; $iAux < $node->length; $iAux ++) {
    $refSer = $node->item($iAUX)->getElementsByTagName("refSer");
    if ($refSer->length > 0) {
        $refSer = $refSer->item(0)->nodeValue;
    } else {
        $refSer = "";
    }
    $tipSer = $node->item($iAUX)->getElementsByTagName("tipSer");
    if ($tipSer->length > 0) {
        $tipSer = $tipSer->item(0)->nodeValue;
    } else {
        $tipSer = "";
    }
    $dscSer = $node->item($iAUX)->getElementsByTagName("dscSer");
    if ($dscSer->length > 0) {
        $dscSer = $dscSer->item(0)->nodeValue;
    } else {
        $dscSer = "";
    }   
    $fecIni = $node->item($iAUX)->getElementsByTagName("fecIni");
    if ($fecIni->length > 0) {
        $fecIni = $fecIni->item(0)->nodeValue;
    } else {
        $fecIni = "";
    }
    $codDiv = $node->item($iAUX)->getElementsByTagName("codDiv");
    if ($codDiv->length > 0) {
        $codDiv = $codDiv->item(0)->nodeValue;
    } else {
        $codDiv = "";
    }
    $tieneComi = $node->item($iAUX)->getElementsByTagName("tieneComi");
    if ($tieneComi->length > 0) {
        $tieneComi = $tieneComi->item(0)->nodeValue;
    } else {
        $tieneComi = "";
    }
    $porcentGastos = $node->item($iAUX)->getElementsByTagName("porcentGastos");
    if ($porcentGastos->length > 0) {
        $porcentGastos = $porcentGastos->item(0)->nodeValue;
    } else {
        $porcentGastos = "";
    }
    $importGastos = $node->item($iAUX)->getElementsByTagName("importGastos");
    if ($importGastos->length > 0) {
        $importGastos = $importGastos->item(0)->nodeValue;
    } else {
        $importGastos = "";
    }
    $aplRangoIni = $node->item($iAUX)->getElementsByTagName("aplRangoIni");
    if ($aplRangoIni->length > 0) {
        $aplRangoIni = $aplRangoIni->item(0)->nodeValue;
    } else {
        $aplRangoIni = "";
    }
    $aplRangoFin = $node->item($iAUX)->getElementsByTagName("aplRangoFin");
    if ($aplRangoFin->length > 0) {
        $aplRangoFin = $aplRangoFin->item(0)->nodeValue;
    } else {
        $aplRangoFin = "";
    }
    $fecSerIni = $node->item($iAUX)->getElementsByTagName("fecSerIni");
    if ($fecSerIni->length > 0) {
        $fecSerIni = $fecSerIni->item(0)->nodeValue;
    } else {
        $fecSerIni = "";
    }
    $fecSerFin = $node->item($iAUX)->getElementsByTagName("fecSerFin");
    if ($fecSerFin->length > 0) {
        $fecSerFin = $fecSerFin->item(0)->nodeValue;
    } else {
        $fecSerFin = "";
    }
    $gastPorConf = $node->item($iAUX)->getElementsByTagName("gastPorConf");
    if ($gastPorConf->length > 0) {
        $gastPorConf = $gastPorConf->item(0)->nodeValue;
    } else {
        $gastPorConf = "";
    }
    $textoGasto = $node->item($iAUX)->getElementsByTagName("textoGasto");
    if ($textoGasto->length > 0) {
        $textoGasto = $textoGasto->item(0)->nodeValue;
    } else {
        $textoGasto = "";
    }

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('politicaCancelacion_contenidoGasto');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'refSer' => $refSer,
        'tipSer' => $tipSer,
        'dscSer' => $dscSer,
        'fecIni' => $fecIni,
        'codDiv' => $codDiv,
        'tieneComi' => $tieneComi,
        'porcentGastos' => $porcentGastos,
        'importGastos' => $importGastos,
        'aplRangoIni' => $aplRangoIni,
        'aplRangoFin' => $aplRangoFin,
        'fecSerIni' => $fecSerIni,
        'fecSerFin' => $fecSerFin,
        'gastPorConf' => $gastPorConf,
        'textoGasto' => $textoGasto
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
    
    /* $sql = new Sql($db);
    $select = $sql->select();
    $select->from('politicaCancelacion_cancelaGastos');
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
        $insert->into('politicaCancelacion_cancelaGastos');
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
    } */ 
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>

