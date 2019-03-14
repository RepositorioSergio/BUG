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

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.reserva.wst"><soapenv:Header/><soapenv:Body><typ:crearSesionRequest><typ:idUsuario>' . $globaliapackagesCustomerID . '</typ:idUsuario><typ:codIdi>ESP</typ:codIdi><typ:codAge/><typ:codSag/></typ:crearSesionRequest></soapenv:Body></soapenv:Envelope>';
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
//include "/srv/www/htdocs/specialtours/travelplan/criarsessao_debug.php";
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
$crearSesionResponse = $Body->item(0)->getElementsByTagName("crearSesionResponse");
$ideSes = $crearSesionResponse->item(0)->getElementsByTagName("ideSes");
if ($ideSes->length > 0) {
    $ideSes = $ideSes->item(0)->nodeValue;
} else {
    $ideSes = "";
}

$sql = new Sql($db);
$select = $sql->select();
$select->from('criar_sessao');
$select->where(array(
    'ideSes' => $ideSes
));
$statement = $sql->prepareStatementForSqlObject($select);
$result = $statement->execute();
$result->buffer();
$customers = array();
if ($result->valid()) {
    $data = $result->current();
    $id = $data['ideSes'];
    if (count($id) > 0) {
        $sql = new Sql($db);
        $data = array(
            'datetime_created' => time(),
            'datetime_updated' => 1,
            'ideSes' => $ideSes
        );
        $where['ideSes = ?'] = $ideSes;
        $update = $sql->update('criar_sessao', $data, $where);
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } else {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('criar_sessao');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'ideSes' => $ideSes
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
    $insert->into('criar_sessao');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'ideSes' => $ideSes
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
echo '<br/> Done';
?>
