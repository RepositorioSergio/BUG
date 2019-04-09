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

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
    <getPrecioViaje xmlns="http://tempuri.org/">
        <Id_Viaje>17480</Id_Viaje>
        <Temporada>2019</Temporada>
        <userName>CTMWS</userName>
        <userPassword>Ctmws123</userPassword>
        <Pais></Pais>
    </getPrecioViaje>
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
$getPrecioViajeResponse = $Body->item(0)->getElementsByTagName("getPrecioViajeResponse");
$getPrecioViajeResult = $getPrecioViajeResponse->item(0)->getElementsByTagName("getPrecioViajeResult");

//diffgram
$diffgram = $getPrecioViajeResult->item(0)->getElementsByTagName("diffgram");
if ($diffgram->length > 0) {
    $DocumentElement = $diffgram->item(0)->getElementsByTagName("DocumentElement");
    if ($DocumentElement->length > 0) {
        $PreciosViajes = $DocumentElement->item(0)->getElementsByTagName("PreciosViajes");
        if ($PreciosViajes->length > 0) {
            for ($i=0; $i < $PreciosViajes->length; $i++) { 
                $Anio = $PreciosViajes->item($i)->getElementsByTagName("Anio");
                if ($Anio->length > 0) {
                    $Anio = $Anio->item(0)->nodeValue;
                } else {
                    $Anio = "";
                }
                $Temporada = $PreciosViajes->item($i)->getElementsByTagName("Temporada");
                if ($Temporada->length > 0) {
                    $Temporada = $Temporada->item(0)->nodeValue;
                } else {
                    $Temporada = "";
                }
                $Id_Viaje = $PreciosViajes->item($i)->getElementsByTagName("Id_Viaje");
                if ($Id_Viaje->length > 0) {
                    $Id_Viaje = $Id_Viaje->item(0)->nodeValue;
                } else {
                    $Id_Viaje = "";
                }
                $Nombre_Viaje = $PreciosViajes->item($i)->getElementsByTagName("Nombre_Viaje");
                if ($Nombre_Viaje->length > 0) {
                    $Nombre_Viaje = $Nombre_Viaje->item(0)->nodeValue;
                } else {
                    $Nombre_Viaje = "";
                }
                $Precio = $PreciosViajes->item($i)->getElementsByTagName("Precio");
                if ($Precio->length > 0) {
                    $Precio = $Precio->item(0)->nodeValue;
                } else {
                    $Precio = "";
                }
                $SuplementoSG = $PreciosViajes->item($i)->getElementsByTagName("SuplementoSG");
                if ($SuplementoSG->length > 0) {
                    $SuplementoSG = $SuplementoSG->item(0)->nodeValue;
                } else {
                    $SuplementoSG = "";
                }
                $SuplementoMP = $PreciosViajes->item($i)->getElementsByTagName("SuplementoMP");
                if ($SuplementoMP->length > 0) {
                    $SuplementoMP = $SuplementoMP->item(0)->nodeValue;
                } else {
                    $SuplementoMP = "";
                }
                $PrecioTR = $PreciosViajes->item($i)->getElementsByTagName("PrecioTR");
                if ($PrecioTR->length > 0) {
                    $PrecioTR = $PrecioTR->item(0)->nodeValue;
                } else {
                    $PrecioTR = "";
                }
                $PrecioCD = $PreciosViajes->item($i)->getElementsByTagName("PrecioCD");
                if ($PrecioCD->length > 0) {
                    $PrecioCD = $PrecioCD->item(0)->nodeValue;
                } else {
                    $PrecioCD = "";
                }
                $Moneda = $PreciosViajes->item($i)->getElementsByTagName("Moneda");
                if ($Moneda->length > 0) {
                    $Moneda = $Moneda->item(0)->nodeValue;
                } else {
                    $Moneda = "";
                }
                $SuplementoComp = $PreciosViajes->item($i)->getElementsByTagName("SuplementoComp");
                if ($SuplementoComp->length > 0) {
                    $SuplementoComp = $SuplementoComp->item(0)->nodeValue;
                } else {
                    $SuplementoComp = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('precoViagens');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Anio' => $Anio,
                        'Temporada' => $Temporada,
                        'Id_Viaje' => $Id_Viaje,
                        'Nombre_Viaje' => $Nombre_Viaje,
                        'Precio' => $Precio,
                        'SuplementoSG' => $SuplementoSG,
                        'SuplementoMP' => $SuplementoMP,
                        'PrecioTR' => $PrecioTR,
                        'PrecioCD' => $PrecioCD,
                        'Moneda' => $Moneda,
                        'SuplementoComp' => $SuplementoComp
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