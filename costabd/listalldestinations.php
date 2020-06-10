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
echo "COMECOU DESTINATIONS<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.costa.php');
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
  <soap:Header>
    <Agency xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Code>38290196</Code>
    </Agency>
    <Partner xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Name>Costamar</Name>
      <Password>C0sT2m2R</Password>
    </Partner>
  </soap:Header>
  <soap:Body>
    <ListAllDestinations xmlns="http://schemas.costacrociere.com/WebAffiliation" />
  </soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/ListAllDestinations",
    "Content-length: ".strlen($raw)
));
$url = "https://training.costaclick.net/WAWS_1_9/Availability.asmx";

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
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.costa.php');
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
$ListAllDestinationsResponse = $Body->item(0)->getElementsByTagName("ListAllDestinationsResponse");
if ($ListAllDestinationsResponse->length > 0) {
    $ListAllDestinationsResult = $ListAllDestinationsResponse->item(0)->getElementsByTagName("ListAllDestinationsResult");
    if ($ListAllDestinationsResult->length > 0) {
        $Destination = $ListAllDestinationsResult->item(0)->getElementsByTagName("Destination");
        if ($Destination->length > 0) {
            for ($i=0; $i < $Destination->length; $i++) { 
                $Code = $Destination->item($i)->getElementsByTagName("Code");
                if ($Code->length > 0) {
                    $Code = $Code->item(0)->nodeValue;
                } else {
                    $Code = "";
                }
                $Description = $Destination->item($i)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    $Description = $Description->item(0)->nodeValue;
                } else {
                    $Description = "";
                }
                $AdditionalInfo = $Destination->item($i)->getElementsByTagName("AdditionalInfo");
                if ($AdditionalInfo->length > 0) {
                    $AdditionalInfo = $AdditionalInfo->item(0)->nodeValue;
                } else {
                    $AdditionalInfo = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('destinations');
                    $insert->values(array(
                        'code' => $Code,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'description' => $Description
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 1: " . $e;
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
echo '<br/>Done';
?>