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
echo "COMECOU CIDADES";
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


$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <getCiudades xmlns="http://tempuri.org/">
      <userName>CTMWS</userName>
      <userPassword>Ctmws123</userPassword>
    </getCiudades>
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
//schema
$schema = $getCiudadesResult->item(0)->getElementsByTagName("schema");
if ($schema->length > 0) {
    $id = $schema->item(0)->getAttribute("id");
    $element = $schema->item(0)->getElementsByTagName("element");
    if ($element->length > 0) {
        $UseCurrentLocale = $element->item(0)->getAttribute("msdata:UseCurrentLocale");
        $IsDataSet = $element->item(0)->getAttribute("msdata:IsDataSet");
        $name = $element->item(0)->getAttribute("name");
        $complexType = $element->item(0)->getElementsByTagName("complexType");
        if ($complexType->length > 0) {
            $choice = $complexType->item(0)->getElementsByTagName("choice");
            if ($choice->length > 0) {
                $maxOccurs = $choice->item(0)->getAttribute("maxOccurs");
                $minOccurs = $choice->item(0)->getAttribute("minOccurs");
                $element2 = $choice->item(0)->getElementsByTagName("element");
                if ($element2->length > 0) {
                    $elementname = $element2->item(0)->getAttribute("name");
                    $complexType2 = $element2->item(0)->getElementsByTagName("complexType");
                    if ($complexType2->length > 0) {
                        $sequence = $complexType2->item(0)->getElementsByTagName("sequence");
                        if ($sequence->length > 0) {
                            $element3 = $sequence->item(0)->getElementsByTagName("element");
                            if ($element3->length > 0) {
                                $type = $element3->item(0)->getAttribute("type");
                                $element3name = $element3->item(0)->getAttribute("name");
                                $element3minOccurs = $element3->item(0)->getAttribute("minOccurs");
                            }
                        }
                    }
                }
            }
        }
    }
}


//diffgram
$diffgram = $getCiudadesResult->item(0)->getElementsByTagName("diffgram");
if ($diffgram->length > 0) {
    $NewDataSet = $diffgram->item(0)->getElementsByTagName("NewDataSet");
    if ($NewDataSet->length > 0) {
        $Table = $NewDataSet->item(0)->getElementsByTagName("Table");
        if ($Table->length > 0) {
            for ($i=0; $i < $Table->length; $i++) { 
                $id = $Table->item($i)->getAttribute("diffgr:id");
                $rowOrder = $Table->item($i)->getAttribute("msdata:rowOrder");
                $Ciudades = $Table->item($i)->getElementsByTagName("Ciudades");
                if ($Ciudades->length > 0) {
                    $Ciudades = $Ciudades->item(0)->nodeValue;
                } else {
                    $Ciudades = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('cidades');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Ciudades' => $Ciudades
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