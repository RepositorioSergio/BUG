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
echo "COMECOU PRECO";
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

/* $sql = "SELECT ID_Viaje FROM viagens";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $ID_Viaje = $row->ID_Viaje; */


$user = 'CTMWS';
$pass = 'Ctmws123';
$ID_Viaje = 16539;

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <getPrecioIncluyeViaje xmlns="http://tempuri.org/">
      <userName>' . $user . '</userName>
      <userPassword>' . $pass . '</userPassword>
      <idViaje>' . $ID_Viaje . '</idViaje>
    </getPrecioIncluyeViaje>
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
die();
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
$getPrecioIncluyeViajeResponse = $Body->item(0)->getElementsByTagName("getPrecioIncluyeViajeResponse");
$getPrecioIncluyeViajeResult = $getPrecioIncluyeViajeResponse->item(0)->getElementsByTagName("getPrecioIncluyeViajeResult");

//diffgram
$diffgram = $getPrecioIncluyeViajeResult->item(0)->getElementsByTagName("diffgram");
if ($diffgram->length > 0) {
    $PrecioIncluye = $diffgram->item(0)->getElementsByTagName("PrecioIncluye");
    if ($PrecioIncluye->length > 0) {
        $Es = $PrecioIncluye->item(0)->getElementsByTagName("Es");
        if ($Es->length > 0) { 
            $TextoInicio = $Es->item(0)->getElementsByTagName("TextoInicio");
            if ($TextoInicio->length > 0) {
                $TextoInicio = $TextoInicio->item(0)->nodeValue;
            } else {
                $TextoInicio = "";
            }
            $TrasladoLlegada = $Es->item(0)->getElementsByTagName("TrasladoLlegada");
            if ($TrasladoLlegada->length > 0) {
                $TrasladoLlegada = $TrasladoLlegada->item(0)->nodeValue;
            } else {
                $TrasladoLlegada = "";
            }
            $TrasladoSalida = $Es->item(0)->getElementsByTagName("TrasladoSalida");
            if ($TrasladoSalida->length > 0) {
                $TrasladoSalida = $TrasladoSalida->item(0)->nodeValue;
            } else {
                $TrasladoSalida = "";
            }
            $Citytour = $Es->item(0)->getElementsByTagName("Citytour");
            if ($Citytour->length > 0) {
                $Citytour = $Citytour->item(0)->nodeValue;
            } else {
                $Citytour = "";
            }
            $Excusion = $Es->item(0)->getElementsByTagName("Excusion");
            if ($Excusion->length > 0) {
                $Excusion = $Excusion->item(0)->nodeValue;
            } else {
                $Excusion = "";
            }
            $Barco = $Es->item(0)->getElementsByTagName("Barco");
            if ($Barco->length > 0) {
                $Barco = $Barco->item(0)->nodeValue;
            } else {
                $Barco = "";
            }
            $TrasladoNoche = $Es->item(0)->getElementsByTagName("TrasladoNoche");
            if ($TrasladoNoche->length > 0) {
                $TrasladoNoche = $TrasladoNoche->item(0)->nodeValue;
            } else {
                $TrasladoNoche = "";
            }
            $Traslado = $Es->item(0)->getElementsByTagName("Traslado");
            if ($Traslado->length > 0) {
                $Traslado = $Traslado->item(0)->nodeValue;
            } else {
                $Traslado = "";
            }
            $Entradas = $Es->item(0)->getElementsByTagName("Entradas");
            if ($Entradas->length > 0) {
                $Entradas = $Entradas->item(0)->nodeValue;
            } else {
                $Entradas = "";
            }
            $PaseoTren = $Es->item(0)->getElementsByTagName("PaseoTren");
            if ($PaseoTren->length > 0) {
                $PaseoTren = $PaseoTren->item(0)->nodeValue;
            } else {
                $PaseoTren = "";
            }
            $Ferry = $Es->item(0)->getElementsByTagName("Ferry");
            if ($Ferry->length > 0) {
                $Ferry = $Ferry->item(0)->nodeValue;
            } else {
                $Ferry = "";
            }
            $Funicular = $Es->item(0)->getElementsByTagName("Funicular");
            if ($Funicular->length > 0) {
                $Funicular = $Funicular->item(0)->nodeValue;
            } else {
                $Funicular = "";
            }
            $TrenAltaVelocidad = $Es->item(0)->getElementsByTagName("TrenAltaVelocidad");
            if ($TrenAltaVelocidad->length > 0) {
                $TrenAltaVelocidad = $TrenAltaVelocidad->item(0)->nodeValue;
            } else {
                $TrenAltaVelocidad = "";
            }
            $VuelosIncluidos = $Es->item(0)->getElementsByTagName("VuelosIncluidos");
            if ($VuelosIncluidos->length > 0) {
                $VuelosIncluidos = $VuelosIncluidos->item(0)->nodeValue;
            } else {
                $VuelosIncluidos = "";
            }
            $Menu = $Es->item(0)->getElementsByTagName("Menu");
            if ($Menu->length > 0) {
                $Menu = $Menu->item(0)->nodeValue;
            } else {
                $Menu = "";
            }
            $MenuCena = $Es->item(0)->getElementsByTagName("MenuCena");
            if ($MenuCena->length > 0) {
                $MenuCena = $MenuCena->item(0)->nodeValue;
            } else {
                $MenuCena = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('PrecioIncluyeES');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'TextoInicio' => $TextoInicio,
                    'TrasladoLlegada' => $TrasladoLlegada,
                    'TrasladoSalida' => $TrasladoSalida,
                    'Citytour' => $Citytour,
                    'Excusion' => $Excusion,
                    'Barco' => $Barco,
                    'TrasladoNoche' => $TrasladoNoche,
                    'Traslado' => $Traslado,
                    'Entradas' => $Entradas,
                    'PaseoTren' => $PaseoTren,
                    'Ferry' => $Ferry,
                    'Funicular' => $Funicular,
                    'TrenAltaVelocidad' => $TrenAltaVelocidad,
                    'VuelosIncluidos' => $VuelosIncluidos,
                    'Menu' => $Menu,
                    'MenuCena' => $MenuCena
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