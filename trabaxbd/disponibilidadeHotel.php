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
echo "COMECOU DISPONIBILIDADE HOTEL<br/>";
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
        <cir:disponibilidadHotel>
            <arg0>
                <ideses>AGH#3281#251611587387945</ideses>
                <codtou>TST</codtou>
                <tipdrd>RC</tipdrd>
                <conpgr>HT</conpgr>
                <distri id="1">
                    <numuni>1</numuni>
                    <numadl>2</numadl>
                    <numnin>0</numnin>
                    <numbeb>0</numbeb>
                </distri>
                <chkscm>N</chkscm>
                <slcpro>
                <codpro>EUF_I0PM</codpro>
                <codest>3</codest>
                <fecini>16/02/2013</fecini>
                <aerori>PMI</aerori>
                <blodis id="1">
                    <pasid>1</pasid>
                    <pasid>2</pasid>
                </blodis>
                </slcpro>
            </arg0>
        </cir:disponibilidadHotel>
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
    "Content-type: text/xml; charset=\"iso-8859-1\"",
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

die();
$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$cancelResResponse = $Body->item(0)->getElementsByTagName("cancelResResponse");
$cancelResResult = $cancelResResponse->item(0)->getElementsByTagName("cancelResResult");
$node = $cancelResResult->item(0)->getElementsByTagName("Reservation");
$ID = $node->item(0)->getAttribute("ID");
$UserPartnerID = $node->item(0)->getAttribute("UserPartnerID");
$CancelCost = $node->item(0)->getAttribute("CancelCost");

$CancelCostCurrency = $node->item(0)->getElementsByTagName("CancelCostCurrency");
if ($CancelCostCurrency->length > 0) {
    $Code = $CancelCostCurrency->item(0)->getAttribute("Code");
    $Name = $CancelCostCurrency->item(0)->getAttribute("Name");
} else {
    $Code = "";
    $Name = "";
}


try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('cancelar_reserva');
    $insert->values(array(
        'ID' => $ID,
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'UserPartnerID' => $UserPartnerID,
        'CancelCost' => $CancelCost,
        'Code' => $Code,
        'Name' => $Name
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


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>