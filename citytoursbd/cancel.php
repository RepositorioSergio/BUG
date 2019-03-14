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
echo "COMECOU SEARCH<br/>";
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
$sql = "select value from settings where name='enablecitytourspackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_citytours = $affiliate_id;
} else {
    $affiliate_id_citytours = 0;
}
$sql = "select value from settings where name='citytourspackagesuser' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagesuser = $row_settings['value'];
}
echo "<br/>citytourspackagesuser: " . $citytourspackagesuser;
$sql = "select value from settings where name='citytourspackagespassword' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagespassword = base64_decode($row_settings['value']);
}
echo "<br/>citytourspackagespassword: " . $citytourspackagespassword;
$sql = "select value from settings where name='citytourspackagesserviceURL' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagesserviceURL = $row_settings['value'];
}
echo "<br/>citytourspackagesserviceURL: " . $citytourspackagesserviceURL;
$sql = "select value from settings where name='citytourspackagesagency' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagesagency = $row_settings['value'];
}
echo "<br/>citytourspackagesagency: " . $citytourspackagesagency;
$sql = "select value from settings where name='citytourspackagesSystem' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytourspackagesSystem = $row_settings['value'];
}
echo "<br/>citytourspackagesSystem: " . $citytourspackagesSystem;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.citytours.php');
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

$raw = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
  <ServiceReservationCancel xmlns="http://tempuri.org/">
<OTA_CancelRQ CancelType="Commit" PrimaryLangID="en-us" Target="Test" TimeStamp="' . $timestamp . '" Version="3.0" xmlns="http://www.opentravel.org/OTA/2003/05">
<POS>
  <Source PseudoCityCode="NONE">
    <RequestorID ID="TESTID" Type="TD"/>
    <TPA_Extensions>
      <Provider>
        <System>' . $citytourspackagesSystem . '</System>
        <Userid>' . $citytourspackagesuser . '</Userid>
         <Password>' . $citytourspackagespassword . '</Password>        
      </Provider>
    </TPA_Extensions>
  </Source>
</POS>
<UniqueID ID="1121" Type="15" BrokerCode="7" />
</OTA_CancelRQ>
</ServiceReservationCancel>
  </soap:Body>
</soap:Envelope>';
echo $raw;
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
    "SOAPAction: http://tempuri.org/ServiceReservationCancel",
    "Content-length: ".strlen($raw)
));
$client->setUri($majesticusaServiceURL);
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


$config = new \Zend\Config\Config(include '../config/autoload/global.citytours.php');
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
$OTA_CancelRS = $Body->item(0)->getElementsByTagName("OTA_CancelRS");
$Status = $OTA_CancelRS->item(0)->getAttribute("Status");
$node = $OTA_CancelRS->item(0)->getElementsByTagName("CancelInfoRS");
$UniqueID = $node->item(0)->getElementsByTagName("UniqueID");
if ($UniqueID->length > 0) {
    $Type = $UniqueID->item(0)->getAttribute("Type");
    $ID = $UniqueID->item(0)->getAttribute("ID");
} else {
    $Type = "";
    $ID = "";
}
$CancelRules = $node->item(0)->getElementsByTagName("CancelRules");
if ($CancelRules->length > 0) {
    $CancelRule = $CancelRules->item(0)->getElementsByTagName("CancelRule");
    if ($CancelRule->length > 0) {
        $DecimalPlaces = $CancelRule->item(0)->getAttribute("DecimalPlaces");
        $Amount = $CancelRule->item(0)->getAttribute("Amount");
        $TypeCancel = $CancelRule->item(0)->getAttribute("Type");
    } else {
        $DecimalPlaces = "";
        $Amount = "";
        $TypeCancel = "";
    }
}

$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('cancel');
$insert->values(array(
    'ID' => $ID,
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'Type' => $Type,
    'DecimalPlaces' => $DecimalPlaces,
    'Amount' => $Amount,
    'TypeCancel' => $TypeCancel
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