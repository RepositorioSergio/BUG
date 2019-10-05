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
echo "COMECOU MODIFICA DADOS PASSAGEIROa";
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
    <updateDatosPasajeros xmlns="http://tempuri.org/">
      <userName>' . $user . '</userName>
      <userPassword>' . $pass . '</userPassword>
      <coReserva>RM000173</coReserva>
      <Pasajeros>
        <ArrayOfString>
          <string>Alves</string>
          <string>Antonio</string>
        </ArrayOfString>
        <ArrayOfString>
          <string>Guia</string>
          <string>Roberto</string>
        </ArrayOfString>
      </Pasajeros>
    </updateDatosPasajeros>
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
$updateDatosPasajerosResponse = $Body->item(0)->getElementsByTagName("updateDatosPasajerosResponse");
$updateDatosPasajerosResult = $updateDatosPasajerosResponse->item(0)->getElementsByTagName("updateDatosPasajerosResult");

//diffgram
$diffgram = $updateDatosPasajerosResult->item(0)->getElementsByTagName("diffgram");
if ($diffgram->length > 0) {
    $DocumentElement = $diffgram->item(0)->getElementsByTagName("DocumentElement");
    if ($DocumentElement->length > 0) {
        $Resultados = $DocumentElement->item(0)->getElementsByTagName("Resultados");
        if ($Resultados->length > 0) {
            $ModificacionCompletada = $Resultados->item(0)->getElementsByTagName("ModificacionCompletada");
            if ($ModificacionCompletada->length > 0) {
                $ModificacionCompletada = $ModificacionCompletada->item(0)->nodeValue;
            } else {
                $ModificacionCompletada = "";
            }
            $Modificacion = $Resultados->item(0)->getElementsByTagName("Modificacion");
            if ($Modificacion->length > 0) {
                $Modificacion = $Modificacion->item(0)->nodeValue;
            } else {
                $Modificacion = "";
            }
            $FechaModificacion = $Resultados->item(0)->getElementsByTagName("FechaModificacion");
            if ($FechaModificacion->length > 0) {
                $FechaModificacion = $FechaModificacion->item(0)->nodeValue;
            } else {
                $FechaModificacion = "";
            }
            $IDAgenteModificacion = $Resultados->item(0)->getElementsByTagName("IDAgenteModificacion");
            if ($IDAgenteModificacion->length > 0) {
                $IDAgenteModificacion = $IDAgenteModificacion->item(0)->nodeValue;
            } else {
                $IDAgenteModificacion = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('updatePassageiro');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'ModificacionCompletada' => $ModificacionCompletada,
                    'Modificacion' => $Modificacion,
                    'FechaModificacion' => $FechaModificacion,
                    'IDAgenteModificacion' => $IDAgenteModificacion
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>