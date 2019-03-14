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
echo "COMECOU DISPONIBILIDADE ELEMENTO<br/>";
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
      <cir:disponibilidadElemento>
        <arg0>
            <ideses>AGH#3281#220137409382829</ideses>
            <codtou>TST</codtou>
            <codidi>SPA</codidi>
            <codele>codsmo</codele>
        </arg0>
      </cir:disponibilidadElemento>
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
echo $return;
echo "PASSOU";
echo $return;
$Body = $Envelope->item(0)->getElementsByTagName("Body");
echo $return;
echo "PASSOU 1";
echo $return;
$disponibilidadElementoResponse = $Body->item(0)->getElementsByTagName("disponibilidadElementoResponse");
$return2 = $disponibilidadElementoResponse->item(0)->getElementsByTagName("return");
echo $return;
echo "PASSOU 2";
echo $return;
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
$codele = $return2->item(0)->getElementsByTagName("codele");
if ($codele->length > 0) {
    $codele = $codele->item(0)->nodeValue;
} else {
    $codele = "";
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('disponibilidadeElemento');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'codtou' => $codtou,
        'codele' => $codele
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

$infele = $return2->item(0)->getElementsByTagName("infele");
if ($infele->length > 0) {
    for ($i=0; $i < $infele->length; $i++) { 
        $valele = $infele->item($i)->getElementsByTagName("valele");
        if ($valele->length > 0) {
            $valele = $valele->item(0)->nodeValue;
        } else {
            $valele = "";
        }
        $desele = $infele->item($i)->getElementsByTagName("desele");
        if ($desele->length > 0) {
            $desele = $desele->item(0)->nodeValue;
        } else {
            $desele = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('disponibilidadeElemento_infele');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'valele' => $valele,
                'desele' => $desele,
                'codtou' => $codtou
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