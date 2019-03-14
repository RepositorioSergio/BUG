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

$url = $globaliapackagesserviceURL . 'b2c/services/wstGetDatosFacturacion';
echo $return;
echo $url;
echo $return;
$ideClie = "CTMT0";
$ideSes = "52535383408125230044";
$localizador = "N4ZVUG";
$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.DatosFacturacion.wst">     <soapenv:Header/>     <soapenv:Body>        <typ:datosFacturacionRequest>           <idUsuario>' . $ideClie . '</idUsuario>           <localizador>' . $localizador . '</localizador>           <sesion>' . $ideSes . '</sesion>        </typ:datosFacturacionRequest>     </soapenv:Body>  </soapenv:Envelope>';
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
echo "<br/> PASSOU 3";

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
$datosFacturacionResponse = $Body->item(0)->getElementsByTagName("datosFacturacionResponse");
$nom = $datosFacturacionResponse->item(0)->getElementsByTagName("nom");
if ($nom->length > 0) {
    $nom = $nom->item(0)->nodeValue;
} else {
    $nom = "";
}
$apellido1 = $datosFacturacionResponse->item(0)->getElementsByTagName("apellido1");
if ($apellido1->length > 0) {
    $apellido1 = $apellido1->item(0)->nodeValue;
} else {
    $apellido1 = "";
}
$nif = $datosFacturacionResponse->item(0)->getElementsByTagName("nif");
if ($nif->length > 0) {
    $nif = $nif->item(0)->nodeValue;
} else {
    $nif = "";
}
$direccion = $datosFacturacionResponse->item(0)->getElementsByTagName("direccion");
if ($direccion->length > 0) {
    $direccion = $direccion->item(0)->nodeValue;
} else {
    $direccion = "";
}
$poblacion = $datosFacturacionResponse->item(0)->getElementsByTagName("poblacion");
if ($poblacion->length > 0) {
    $poblacion = $poblacion->item(0)->nodeValue;
} else {
    $poblacion = "";
}
$ciudad = $datosFacturacionResponse->item(0)->getElementsByTagName("ciudad");
if ($ciudad->length > 0) {
    $ciudad = $ciudad->item(0)->nodeValue;
} else {
    $ciudad = "";
}
$provincia = $datosFacturacionResponse->item(0)->getElementsByTagName("provincia");
if ($provincia->length > 0) {
    $provincia = $provincia->item(0)->nodeValue;
} else {
    $provincia = "";
}
$codPostal = $datosFacturacionResponse->item(0)->getElementsByTagName("codPostal");
if ($codPostal->length > 0) {
    $codPostal = $codPostal->item(0)->nodeValue;
} else {
    $codPostal = "";
}
$pais = $datosFacturacionResponse->item(0)->getElementsByTagName("pais");
if ($pais->length > 0) {
    $pais = $pais->item(0)->nodeValue;
} else {
    $pais = "";
}
$sexo = $datosFacturacionResponse->item(0)->getElementsByTagName("sexo");
if ($sexo->length > 0) {
    $sexo = $sexo->item(0)->nodeValue;
} else {
    $sexo = "";
}
$email = $datosFacturacionResponse->item(0)->getElementsByTagName("email");
if ($email->length > 0) {
    $email = $email->item(0)->nodeValue;
} else {
    $email = "";
}

$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('dadosFacturacao');
$insert->values(array(
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'nom' => $nom,
    'apellido1' => $apellido1,
    'nif' => $nif,
    'direccion' => $direccion,
    'poblacion' => $poblacion,
    'ciudad' => $ciudad,
    'provincia' => $provincia,
    'codPostal' => $codPostal,
    'pais' => $pais,
    'sexo' => $sexo,
    'email' => $email
), $insert::VALUES_MERGE);
$statement = $sql->prepareStatementForSqlObject($insert);
$results = $statement->execute();
$db->getDriver()
    ->getConnection()
    ->disconnect();

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>