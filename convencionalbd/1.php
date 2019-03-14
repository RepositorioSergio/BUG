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
echo "COMECOU CITIES SIATAR<br/>";
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

echo "<br/> RAW:" . $raw;
$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xnet="http://xnetinfo.org/">
   <soap:Header/>
   <soap:Body>
      <xnet:getCityList>
         <xnet:aRequest EchoToken="123" TimeStamp="2019-02-28T17:43:25.315" Version="1.0">
            <xnet:POS>
               <xnet:Source>
                  <xnet:RequestorID ID="a6dge3!tnsf2or" PartnerID="TEST" Username="xnet" Password="pctnx!!!"/>
               </xnet:Source>
            </xnet:POS>
            <xnet:CitySearchCriterion CountryCode="BRA"/>
         </xnet:aRequest>
      </xnet:getCityList>
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
die();
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
$XNET_CitySearchRS = $Body->item(0)->getElementsByTagName("XNET_CitySearchRS");
$Cities = $XNET_CitySearchRS->item(0)->getElementsByTagName("Cities");
$node = $Cities->item(0)->getElementsByTagName("City");
for ($i=0; $i < $node->lenght; $i++) { 
    $Code = $node->item($i)->getAttribute("Code");
    $Name = $node->item($i)->getAttribute("Name");
    $State = $node->item($i)->getAttribute("State");
    $CountryCode = $node->item($i)->getAttribute("CountryCode");
    echo $return;
    echo "CountryCode: " . $CountryCode;
    echo $return;

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('cities');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'Code' => $Code,
        'Name' => $Name,
        'State' => $State,
        'CountryCode' => $CountryCode
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
