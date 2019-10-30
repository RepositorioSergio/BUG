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
echo "COMECOU DESC";
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
$ID_Viaje = 16539;

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <getDescripcion xmlns="http://tempuri.org/">
      <Id_Viaje>' . $ID_Viaje . '</Id_Viaje>
      <userName>' . $user . '</userName>
      <userPassword>' . $pass . '</userPassword>
    </getDescripcion>
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
$getCiudadesResponse = $Body->item(0)->getElementsByTagName("getCiudadesResponse");
$getCiudadesResult = $getCiudadesResponse->item(0)->getElementsByTagName("getCiudadesResult");

//diffgram
$diffgram = $getCiudadesResult->item(0)->getElementsByTagName("diffgram");
if ($diffgram->length > 0) {
    $NewDataSet = $diffgram->item(0)->getElementsByTagName("NewDataSet");
    if ($NewDataSet->length > 0) {
        $Table = $NewDataSet->item(0)->getElementsByTagName("Table");
        if ($Table->length > 0) {
            for ($i=0; $i < $Table->length; $i++) { 
                $ID_Ciudad = $Table->item($i)->getElementsByTagName("ID_Ciudad");
                if ($ID_Ciudad->length > 0) {
                    $ID_Ciudad = $ID_Ciudad->item(0)->nodeValue;
                } else {
                    $ID_Ciudad = "";
                }
                $Ciudad = $Table->item($i)->getElementsByTagName("Ciudad");
                if ($Ciudad->length > 0) {
                    $Ciudad = $Ciudad->item(0)->nodeValue;
                } else {
                    $Ciudad = "";
                }
                $CODIGO_IATA = $Table->item($i)->getElementsByTagName("CODIGO_IATA");
                if ($CODIGO_IATA->length > 0) {
                    $CODIGO_IATA = $CODIGO_IATA->item(0)->nodeValue;
                } else {
                    $CODIGO_IATA = "";
                }
                $GuiaPdf = $Table->item($i)->getElementsByTagName("GuiaPdf");
                if ($GuiaPdf->length > 0) {
                    $GuiaPdf = $GuiaPdf->item(0)->nodeValue;
                } else {
                    $GuiaPdf = "";
                }
                $ID_PAIS = $Table->item($i)->getElementsByTagName("ID_PAIS");
                if ($ID_PAIS->length > 0) {
                    $ID_PAIS = $ID_PAIS->item(0)->nodeValue;
                } else {
                    $ID_PAIS = "";
                }
                $CIUDAD_PORTUGUES = $Table->item($i)->getElementsByTagName("CIUDAD_PORTUGUES");
                if ($CIUDAD_PORTUGUES->length > 0) {
                    $CIUDAD_PORTUGUES = $CIUDAD_PORTUGUES->item(0)->nodeValue;
                } else {
                    $CIUDAD_PORTUGUES = "";
                }
                $CIUDAD_INGLES = $Table->item($i)->getElementsByTagName("CIUDAD_INGLES");
                if ($CIUDAD_INGLES->length > 0) {
                    $CIUDAD_INGLES = $CIUDAD_INGLES->item(0)->nodeValue;
                } else {
                    $CIUDAD_INGLES = "";
                }
                $CIUDAD_RUSO = $Table->item($i)->getElementsByTagName("CIUDAD_RUSO");
                if ($CIUDAD_RUSO->length > 0) {
                    $CIUDAD_RUSO = $CIUDAD_RUSO->item(0)->nodeValue;
                } else {
                    $CIUDAD_RUSO = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('cidadesCod');
                    $insert->values(array(
                        'ID_Ciudad' => $ID_Ciudad,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Ciudad' => $Ciudad,
                        'CODIGO_IATA' => $CODIGO_IATA,
                        'GuiaPdf' => $GuiaPdf,
                        'ID_PAIS' => $ID_PAIS,
                        'CIUDAD_PORTUGUES' => $CIUDAD_PORTUGUES,
                        'CIUDAD_INGLES' => $CIUDAD_INGLES,
                        'CIUDAD_RUSO' => $CIUDAD_RUSO
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