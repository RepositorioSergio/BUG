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
echo "COMECOU AVAILABILITY<br/>";
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

$url = 'https://xtest.wamos.com/packageTravelXml';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pac="http://packages.servicePackage.dome.com/">
<soapenv:Header/>
<soapenv:Body>
   <pac:availability>
      <arg0>
       <!--El campo "BeginDate" es obligatorio-->
         <beginDate>2020-02-01</beginDate>
       <!--endDate>2019-02-02</endDate-->
         <categoryCode>T</categoryCode>
         <!--El campo "distribution" es obligatorio -->
         <distribution>
            <distributionId>1</distributionId>
            <pax>
               <age>30</age>
               <documentNumber></documentNumber>
               <firstName>NOMBRE1 TEST</firstName>
               <lastName>APELLIDOS1 TEST</lastName>
               <paxId>1</paxId>
               <phone></phone>
            </pax>
            <pax>
               <age>35</age>
               <documentNumber></documentNumber>
               <firstName>NOMBRE2 TEST</firstName>
               <lastName>APELLIDOS2 TEST</lastName>
               <paxId>2</paxId>
               <phone></phone>
            </pax>    
         </distribution>
         <!-- El campo "fareCode" es obligatorio -->
         <fareCode>NORMAL</fareCode>
         <!--ideses></ideses-->
         <login>
            <clientId>${#Project#clientId}</clientId>
            <password>${#Project#clave}</password>
            <system>${#Project#system}</system>
            <user>${#Project#user}</user>
         </login>
         <!-- El campo "packageCode" es obligatorio -->
         <packageCode>IT19T-27819</packageCode>
         <showOptionals>Y</showOptionals>
         <stayCode>7</stayCode>
      </arg0>
   </pac:availability>
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
    "Content-length: ".strlen($raw)
));


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
$HotelAvailResponse = $Body->item(0)->getElementsByTagName("HotelAvailResponse");
$HotelAvailResponse2 = $HotelAvailResponse->item(0)->getElementsByTagName("HotelAvailResponse");
$availabilityList = $HotelAvailResponse2->item(0)->getElementsByTagName("availabilityList");
$node = $availabilityList->item(0)->getElementsByTagName("AvailabilityGroup");


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>