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
echo "COMECOU MASTERS<br/>";
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
$sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_riu = $affiliate_id;
} else {
    $affiliate_id_riu = 0;
}
$sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $riuServiceURL = $row['value'];
}
echo $return;
echo $riuServiceURL;
echo $return;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
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

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:ser="http://services.enginexml.rumbonet.riu.com"
xmlns:dtos="http://dtos.enginexml.rumbonet.riu.com">
    <soapenv:Header/>
    <soapenv:Body>
        <ser:HotelMasters>
            <ser:in0>
                <!--Optional:-->
                <dtos:CountryCode>ES</dtos:CountryCode>
                <!--Optional:-->
                <dtos:Language>E</dtos:Language>
                <!--Optional:-->
                <dtos:tableName>HOTELS</dtos:tableName>
            </ser:in0>
        </ser:HotelMasters>
    </soapenv:Body>
</soapenv:Envelope>';


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


$client->setUri($riuServiceURL);
$client->setMethod('POST');
$client->setCookies(array(
    'JSESSIONID' => 'BEF9A4B92506B9E1926DA2BE5BDE4DEF'
));
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
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
$HotelMastersResponse = $Body->item(0)->getElementsByTagName("HotelMastersResponse");
$HotelMasterRS = $HotelMastersResponse->item(0)->getElementsByTagName("HotelMasterRS");
$maestrosL = $HotelMasterRS->item(0)->getElementsByTagName("maestrosL");
$node = $maestrosL->item(0)->getElementsByTagName("MasterQuery");
for ($i=0; $i < $node->length; $i++) {       
    $id = $node->item($i)->getElementsByTagName("id");
    if ($id->length > 0) {
        $id = $id->item(0)->nodeValue;
    } else {
        $id = "";
    }
    $code = $node->item($i)->getElementsByTagName("code");
    if ($code->length > 0) {
        $code = $code->item(0)->nodeValue;
    } else {
        $code = "";
    }
    $description = $node->item($i)->getElementsByTagName("description");
    if ($description->length > 0) {
        $description = $description->item(0)->nodeValue;
    } else {
        $description = "";
    }
    $tableName = $node->item($i)->getElementsByTagName("tableName");
    if ($tableName->length > 0) {
        $tableName = $tableName->item(0)->nodeValue;
    } else {
        $tableName = "";
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('hotelmasters');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'code' => $code,
            'description' => $description,
            'tableName' => $tableName
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO HOTEL: " . $e;
        echo $return;
    }

} 

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>