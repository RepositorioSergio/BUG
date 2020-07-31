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
echo "COMECOU EVENTS<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://test.xtravelsystem.com/public/v1_0rc1/basketHandler';

$email = 'paulo@corp.bug-software.com';
$password = 'xA2d@a1X';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Body>
      <tns:GetEventsByArea xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:tns="http://xtravelsystem.com/v1_0rc1/common/types" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
         <GetEventsByAreaRQ_1>
            <agencyCode>613</agencyCode>
            <brandCode>1</brandCode>
            <pointOfSaleId>1</pointOfSaleId>
            <areaId>384</areaId>
            <from>2005-09-12T15:10:00.000Z</from>
            <language>en</language>
            <to>2006-11-12T15:10:00.000Z</to>
         </GetEventsByAreaRQ_1>
      </tns:GetEventsByArea>
   </soapenv:Body>
</soapenv:Envelope>';

$headers = array(
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

echo $return;
echo $error;
echo $return;
echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
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
$GetEventsByAreaResponse = $Body->item(0)->getElementsByTagName("GetEventsByAreaResponse");
if ($GetEventsByAreaResponse->length > 0) {
    $result = $GetEventsByAreaResponse->item(0)->getElementsByTagName("result");
    if ($result->length > 0) {
        $events = $result->item(0)->getElementsByTagName("events");
        if ($events->length > 0) {
            for ($i=0; $i < $events->length; $i++) { 
                $description = $events->item($i)->getElementsByTagName("description");
                if ($description->length > 0) {
                    $description = $description->item(0)->nodeValue;
                } else {
                    $description = "";
                }
                $from = $events->item($i)->getElementsByTagName("from");
                if ($from->length > 0) {
                    $from = $from->item(0)->nodeValue;
                } else {
                    $from = "";
                }
                $name = $events->item($i)->getElementsByTagName("name");
                if ($name->length > 0) {
                    $name = $name->item(0)->nodeValue;
                } else {
                    $name = "";
                }
                $to = $events->item($i)->getElementsByTagName("to");
                if ($to->length > 0) {
                    $to = $to->item(0)->nodeValue;
                } else {
                    $to = "";
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