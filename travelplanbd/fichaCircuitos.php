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
$idFicha = "03291";
$idioma = "ESP";
$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.circuito.dtp">     <soapenv:Header/>     <soapenv:Body>        <typ:fichaCircuitosRequest>           <idClie>' . $globaliapackagesCustomerID . '</idClie>           <idFicha>' . $idFicha . '</idFicha>           <idioma>' . $idioma . '</idioma>        </typ:fichaCircuitosRequest>     </soapenv:Body>  </soapenv:Envelope>';

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
echo "<br/> PASSOU SERVIDOR";
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
echo "<br/> RESPONSE";
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
$fichaCircuitosResponse = $Body->item(0)->getElementsByTagName("fichaCircuitosResponse");
$fichaCircuitosResponseRow = $fichaCircuitosResponse->item(0)->getElementsByTagName("fichaCircuitosResponseRow");
$node = $fichaCircuitosResponseRow->item(0)->getElementsByTagName("itineraRow");
for ($iAux = 0; $iAux < $node->length; $iAux ++) {
    $itinera = $node->item($iAUX)->getElementsByTagName("itinera");
    if ($itinera->length > 0) {
        $itinera = $itinera->item(0)->nodeValue;
    } else {
        $itinera = "";
    }
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('fichaCircuitos_itineraRow');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'itinera' => $itinera
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
   
}

//hotelRow
$hotelRow = $fichaCircuitosResponseRow->item(0)->getElementsByTagName("hotelRow");
for ($jAux=0; $jAux < $hotelRow->length; $jAux++) { 
    $ciudad = $hotelRow->item($jAUX)->getElementsByTagName("ciudad");
    if ($ciudad->length > 0) {
        $ciudad = $ciudad->item(0)->nodeValue;
    } else {
        $ciudad = "";
    }
    $hotel = $hotelRow->item($jAUX)->getElementsByTagName("hotel");
    if ($hotel->length > 0) {
        $hotel = $hotel->item(0)->nodeValue;
    } else {
        $hotel = "";
    }
    $situacion = $hotelRow->item($jAUX)->getElementsByTagName("situacion");
    if ($situacion->length > 0) {
        $situacion = $situacion->item(0)->nodeValue;
    } else {
        $situacion = "";
    }
    $categoria = $hotelRow->item($jAUX)->getElementsByTagName("categoria");
    if ($categoria->length > 0) {
        $categoria = $categoria->item(0)->nodeValue;
    } else {
        $categoria = "";
    }
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('fichaCircuitos_hotelRow');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'ciudad' => $ciudad,
        'hotel' => $hotel,
        'situacion' => $situacion,
        'categoria' => $categoria
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}

//subInfoRow
$subInfoRow = $fichaCircuitosResponseRow->item(0)->getElementsByTagName("subInfoRow");
for ($kAux=0; $kAux < $subInfoRow->length; $kAux++) { 
    $subTit = $subInfoRow->item($kAUX)->getElementsByTagName("subTit");
    if ($subTit->length > 0) {
        $subTit = $subTit->item(0)->nodeValue;
    } else {
        $subTit = "";
    }
    $texto = $subInfoRow->item($kAUX)->getElementsByTagName("texto");
    if ($texto->length > 0) {
        $texto = $texto->item(0)->nodeValue;
    } else {
        $texto = "";
    }
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('fichaCircuitos_subInfoRow');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'subTit' => $subTit,
        'texto' => $texto
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>