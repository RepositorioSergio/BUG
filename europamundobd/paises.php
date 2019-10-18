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
echo "COMECOU PAISES";
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
    <getPaisesAll xmlns="http://tempuri.org/">
      <userName>' . $user . '</userName>
      <userPassword>' . $pass . '</userPassword>
    </getPaisesAll>
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
$getPaisesAllResponse = $Body->item(0)->getElementsByTagName("getPaisesAllResponse");
$getPaisesAllResult = $getPaisesAllResponse->item(0)->getElementsByTagName("getPaisesAllResult");

//diffgram
$diffgram = $getPaisesAllResult->item(0)->getElementsByTagName("diffgram");
if ($diffgram->length > 0) {
    $NewDataSet = $diffgram->item(0)->getElementsByTagName("NewDataSet");
    if ($NewDataSet->length > 0) {
        $Table = $NewDataSet->item(0)->getElementsByTagName("Table");
        if ($Table->length > 0) {
            for ($i=0; $i < $Table->length; $i++) { 
                $ID_PAIS = $Table->item($i)->getElementsByTagName("ID_PAIS");
                if ($ID_PAIS->length > 0) {
                    $ID_PAIS = $ID_PAIS->item(0)->nodeValue;
                } else {
                    $ID_PAIS = "";
                }
                $PAIS = $Table->item($i)->getElementsByTagName("PAIS");
                if ($PAIS->length > 0) {
                    $PAIS = $PAIS->item(0)->nodeValue;
                } else {
                    $PAIS = "";
                }
                $PREFIJO = $Table->item($i)->getElementsByTagName("PREFIJO");
                if ($PREFIJO->length > 0) {
                    $PREFIJO = $PREFIJO->item(0)->nodeValue;
                } else {
                    $PREFIJO = "";
                }
                $PAIS_PORTUGUES = $Table->item($i)->getElementsByTagName("PAIS_PORTUGUES");
                if ($PAIS_PORTUGUES->length > 0) {
                    $PAIS_PORTUGUES = $PAIS_PORTUGUES->item(0)->nodeValue;
                } else {
                    $PAIS_PORTUGUES = "";
                }
                $CODIGO_IATA = $Table->item($i)->getElementsByTagName("CODIGO_IATA");
                if ($CODIGO_IATA->length > 0) {
                    $CODIGO_IATA = $CODIGO_IATA->item(0)->nodeValue;
                } else {
                    $CODIGO_IATA = "";
                }
                $PAIS_INGLES = $Table->item($i)->getElementsByTagName("PAIS_INGLES");
                if ($PAIS_INGLES->length > 0) {
                    $PAIS_INGLES = $PAIS_INGLES->item(0)->nodeValue;
                } else {
                    $PAIS_INGLES = "";
                }
                $IDIOMA = $Table->item($i)->getElementsByTagName("IDIOMA");
                if ($IDIOMA->length > 0) {
                    $IDIOMA = $IDIOMA->item(0)->nodeValue;
                } else {
                    $IDIOMA = "";
                }
                $ID_HOTELSTON = $Table->item($i)->getElementsByTagName("ID_HOTELSTON");
                if ($ID_HOTELSTON->length > 0) {
                    $ID_HOTELSTON = $ID_HOTELSTON->item(0)->nodeValue;
                } else {
                    $ID_HOTELSTON = "";
                }
                $PAIS_RUSO = $Table->item($i)->getElementsByTagName("PAIS_RUSO");
                if ($PAIS_RUSO->length > 0) {
                    $PAIS_RUSO = $PAIS_RUSO->item(0)->nodeValue;
                } else {
                    $PAIS_RUSO = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('paises');
                    $insert->values(array(
                        'ID_PAIS' => $ID_PAIS,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'PAIS' => $PAIS,
                        'PREFIJO' => $PREFIJO,
                        'PAIS_PORTUGUES' => $PAIS_PORTUGUES,
                        'CODIGO_IATA' => $CODIGO_IATA,
                        'PAIS_INGLES' => $PAIS_INGLES,
                        'IDIOMA' => $IDIOMA,
                        'ID_HOTELSTON' => $ID_HOTELSTON,
                        'PAIS_RUSO' => $PAIS_RUSO
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