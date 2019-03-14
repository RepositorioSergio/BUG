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
echo "COMECOU DISPONIBILIDADE DESTINO<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.trabax.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );

$raw = '<?xml version="1.0"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cir="http://circuito.serviceIncoming.dome.com/">
   <soapenv:Header/>
   <soapenv:Body>
      <cir:disponibilidadDestino>
      <arg0>
        <ideses>AGH#3281#220137409382829</ideses>
        <codtou>TST</codtou>
        <tiparb>ZPQ</tiparb>
        </arg0>
      </cir:disponibilidadDestino>
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

$config = new \Zend\Config\Config(include '../config/autoload/global.trabax.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$response = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $response);//CONVERT ISO-8859-1 TO UTF-8

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$disponibilidadDestinoResponse = $Body->item(0)->getElementsByTagName("disponibilidadDestinoResponse");
$return2 = $disponibilidadDestinoResponse->item(0)->getElementsByTagName("return");
$ideses = $return2->item(0)->getElementsByTagName("ideses");
if ($ideses->length > 0) {
    $ideses = $ideses->item(0)->nodeValue;
} else {
    $ideses = "";
}
echo $return;
echo "ideses: " . $ideses;
echo $return;
$codtou = $return2->item(0)->getElementsByTagName("codtou");
if ($codtou->length > 0) {
    $codtou = $codtou->item(0)->nodeValue;
} else {
    $codtou = "";
}

/* try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('disponibilidadedestino');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'ideses' => $ideses
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
} */

$infzge = $return2->item(0)->getElementsByTagName("infzge");
if ($infzge->length > 0) {
    for ($i=0; $i < $infzge->length; $i++) { 
        $codzge = $infzge->item($i)->getElementsByTagName("codzge");
        if ($codzge->length > 0) {
            $codzge = $codzge->item(0)->nodeValue;
        } else {
            $codzge = "";
        }
        $zgesup = $infzge->item($i)->getElementsByTagName("zgesup");
        if ($zgesup->length > 0) {
            $zgesup = $zgesup->item(0)->nodeValue;
        } else {
            $zgesup = "";
        }
        $tipzge = $infzge->item($i)->getElementsByTagName("tipzge");
        if ($tipzge->length > 0) {
            $tipzge = $tipzge->item(0)->nodeValue;
        } else {
            $tipzge = "";
        }
        $nomzge = $infzge->item($i)->getElementsByTagName("nomzge");
        if ($nomzge->length > 0) {
            $nomzge = $nomzge->item(0)->nodeValue;
        } else {
            $nomzge = "";
        }
        $nivinf = $infzge->item($i)->getElementsByTagName("nivinf");
        if ($nivinf->length > 0) {
            $nivinf = $nivinf->item(0)->nodeValue;
        } else {
            $nivinf = "";
        }
        $chkfin = $infzge->item($i)->getElementsByTagName("chkfin");
        if ($chkfin->length > 0) {
            $chkfin = $chkfin->item(0)->nodeValue;
        } else {
            $chkfin = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('disponibilidadeDestino');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codzge' => $codzge,
                'zgesup' => $zgesup,
                'tipzge' => $tipzge,
                'nomzge' => $nomzge,
                'nivinf' => $nivinf,
                'chkfin' => $chkfin
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