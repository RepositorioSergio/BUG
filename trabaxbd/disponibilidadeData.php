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
echo "COMECOU DISPONIBILIDADE DATA<br/>";
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

/* $config = new \Zend\Config\Config(include '../config/autoload/global.trabax.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config); */

$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );


$raw = '<?xml version="1.0"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cir="http://circuito.serviceIncoming.dome.com/">
   <soapenv:Header/>
   <soapenv:Body>
      <cir:disponibilidadFecha>
        <arg0>
            <ideses>AGH#3281#792912285472517</ideses>
            <codtou>TST</codtou>
            <zgeori>MAD</zgeori>
            <zgedes>PMI</zgedes>
            <numnoc>4</numnoc>
            <fecini>01/04/2019</fecini>
            <fecfin>30/04/2019</fecfin>
        </arg0>
      </cir:disponibilidadFecha>
   </soapenv:Body>
</soapenv:Envelope>';
echo "<br/> RAW:" . $raw;


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));

$url = "http://cir-tbx.dome-consulting.com/circuito";

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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.trabax.php');
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
$disponibilidadFechaRespuesta = $Body->item(0)->getElementsByTagName("disponibilidadFechaRespuesta");
$return2 = $disponibilidadFechaRespuesta->item(0)->getElementsByTagName("return");
$ideses = $return2->item(0)->getElementsByTagName("ideses");
if ($ideses->length > 0) {
    $ideses = $ideses->item(0)->nodeValue;
} else {
    $ideses = "";
}

$infimp = $return2->item(0)->getElementsByTagName("infimp");
if ($infimp->length > 0) {
    for ($j=0; $j < $infimp->length; $j++) { 
        $id = $infimp->item($j)->getAttribute("id");
        $codser = $infimp->item($j)->getElementsByTagName("codser");
        if ($codser->length > 0) {
            $codser = $codser->item(0)->nodeValue;
        } else {
            $codser = "";
        }
        $aerori = $infimp->item($j)->getElementsByTagName("aerori");
        if ($aerori->length > 0) {
            $aerori = $aerori->item(0)->nodeValue;
        } else {
            $aerori = "";
        }
        $impdia = $infimp->item($j)->getElementsByTagName("impdia");
        if ($impdia->length > 0) {
            $refdia = $impdia->item(0)->getAttribute("refdia");
            $imptot = $impdia->item(0)->getElementsByTagName("imptot");
            if ($imptot->length > 0) {
                $imptot = $imptot->item(0)->nodeValue;
            } else {
                $imptot = "";
            }
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('disponibilidadedata');
            $insert->values(array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codser' => $codser,
                'aerori' => $aerori,
                'refdia' => $refdia,
                'imptot' => $imptot
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO: " . $e;
            echo $return;
        }
    }
}

$data = "";
$fecdia = $return2->item(0)->getElementsByTagName("fecdia");
if ($fecdia->length > 0) {
    for ($i=0; $i < $fecdia->length; $i++) { 
        $data = $fecdia->item($i)->nodeValue;

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('disponibilidadedata');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'data' => $data,
                'idDisponibilidade' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO: " . $e;
            echo $return;
        }
    }
}



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
