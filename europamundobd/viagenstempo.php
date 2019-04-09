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
echo "COMECOU VIAGENS TEMPO";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.europamundo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$user = 'CTMWS';
$pass = 'Ctmws123';

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <getViajesTempo xmlns="http://tempuri.org/">
      <strTemporada>2019</strTemporada>
      <userName>' . $user . '</userName>
      <userPassword>' . $pass . '</userPassword>
    </getViajesTempo>
  </soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: text/xml",
    "Accept: text/xml",
    "Content-length: " . strlen($raw)
));

$client->setUri('http://desarrollo.selfip.com/webserv/ServiceDatos.asmx');
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

echo "<xmp>";
var_dump($response);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.europamundo.php');
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
$getViajesTempoResponse = $Body->item(0)->getElementsByTagName("getViajesTempoResponse");
$getViajesTempoResult = $getViajesTempoResponse->item(0)->getElementsByTagName("getViajesTempoResult");

//diffgram
$diffgram = $getViajesTempoResult->item(0)->getElementsByTagName("diffgram");
if ($diffgram->length > 0) {
    $conexion = $diffgram->item(0)->getElementsByTagName("conexion");
    if ($conexion->length > 0) {
        $Table = $conexion->item(0)->getElementsByTagName("Table");
        if ($Table->length > 0) {
            for ($i=0; $i < $Table->length; $i++) { 
                $ID_Viaje = $Table->item($i)->getElementsByTagName("ID_Viaje");
                if ($ID_Viaje->length > 0) {
                    $ID_Viaje = $ID_Viaje->item(0)->nodeValue;
                } else {
                    $ID_Viaje = "";
                }
                $Nombre_Viaje = $Table->item($i)->getElementsByTagName("Nombre_Viaje");
                if ($Nombre_Viaje->length > 0) {
                    $Nombre_Viaje = $Nombre_Viaje->item(0)->nodeValue;
                } else {
                    $Nombre_Viaje = "";
                }
                $NOMBRE_VIAJE_PORTUGUES = $Table->item($i)->getElementsByTagName("NOMBRE_VIAJE_PORTUGUES");
                if ($NOMBRE_VIAJE_PORTUGUES->length > 0) {
                    $NOMBRE_VIAJE_PORTUGUES = $NOMBRE_VIAJE_PORTUGUES->item(0)->nodeValue;
                } else {
                    $NOMBRE_VIAJE_PORTUGUES = "";
                }
                $Serie = $Table->item($i)->getElementsByTagName("Serie");
                if ($Serie->length > 0) {
                    $Serie = $Serie->item(0)->nodeValue;
                } else {
                    $Serie = "";
                }
                $Temporada = $Table->item($i)->getElementsByTagName("Temporada");
                if ($Temporada->length > 0) {
                    $Temporada = $Temporada->item(0)->nodeValue;
                } else {
                    $Temporada = "";
                }
                $Color_viaje = $Table->item($i)->getElementsByTagName("Color_viaje");
                if ($Color_viaje->length > 0) {
                    $Color_viaje = $Color_viaje->item(0)->nodeValue;
                } else {
                    $Color_viaje = "";
                }
                $NOMBRE_VIAJE_ENGLISH = $Table->item($i)->getElementsByTagName("NOMBRE_VIAJE_ENGLISH");
                if ($NOMBRE_VIAJE_ENGLISH->length > 0) {
                    $NOMBRE_VIAJE_ENGLISH = $NOMBRE_VIAJE_ENGLISH->item(0)->nodeValue;
                } else {
                    $NOMBRE_VIAJE_ENGLISH = "";
                }
                $NOMBRE_VIAJE_RUSO = $Table->item($i)->getElementsByTagName("NOMBRE_VIAJE_RUSO");
                if ($NOMBRE_VIAJE_RUSO->length > 0) {
                    $NOMBRE_VIAJE_RUSO = $NOMBRE_VIAJE_RUSO->item(0)->nodeValue;
                } else {
                    $NOMBRE_VIAJE_RUSO = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viagensTempo');
                    $insert->values(array(
                        'ID_Viaje' => $ID_Viaje,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Nombre_Viaje' => $Nombre_Viaje,
                        'NOMBRE_VIAJE_PORTUGUES' => $NOMBRE_VIAJE_PORTUGUES,
                        'Serie' => $Serie,
                        'Temporada' => $Temporada,
                        'Color_viaje' => $Color_viaje,
                        'NOMBRE_VIAJE_ENGLISH' => $NOMBRE_VIAJE_ENGLISH,
                        'NOMBRE_VIAJE_RUSO' => $NOMBRE_VIAJE_RUSO
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error: " . $e;
                    echo $return;
                }
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>