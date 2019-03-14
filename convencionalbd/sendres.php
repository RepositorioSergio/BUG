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
echo "COMECOU CANCEL ITEM RESERVA SIATAR<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xnet="http://xnetinfo.org/">
<soap:Header/>
<soap:Body>
   <xnet:sendRes>
      <xnet:aRequest EchoToken="123" TimeStamp="2019-02-28T17:43:25.315" Version="1.0">
         <xnet:POS>
            <xnet:Source>
               <xnet:RequestorID ID="a6dge3!tnsf2or" PartnerID="TEST" Username="xnet" Password="pctnx!!!"/>
            </xnet:Source>
         </xnet:POS>
         <xnet:Reservation ID="398" UserPartnerID="JOTA2X"/>
         <xnet:Titular>Alberto Neves</xnet:Titular>
    </xnet:aRequest>
    </xnet:sendRes>
</soap:Body>
</soap:Envelope>';
echo "<br/> RAW:" . $raw;
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));
$url = "http://xnetinfo.redirectme.net:8080/homologacao_webservice/Integration/ServerIntegration.asmx";
echo "<br/> PASSOU URL";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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
$sendResResponse = $Body->item(0)->getElementsByTagName("sendResResponse");
$sendResResult = $sendResResponse->item(0)->getElementsByTagName("sendResResult");
$ReservationsType = $sendResResult->item(0)->getElementsByTagName("ReservationsType");
$Reservations = $ReservationsType->item(0)->getElementsByTagName("Reservations");
$node = $Reservations->item(0)->getElementsByTagName("Reservation");
$ID = $node->item(0)->getAttribute("ID");
$UserPartnerID = $node->item(0)->getAttribute("UserPartnerID");

$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('enviar_reserva');
$insert->values(array(
    'ID' => $ID,
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'UserPartnerID' => $UserPartnerID
), $insert::VALUES_MERGE);
$statement = $sql->prepareStatementForSqlObject($insert);
$results = $statement->execute();
$db->getDriver()
    ->getConnection()
    ->disconnect();

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>