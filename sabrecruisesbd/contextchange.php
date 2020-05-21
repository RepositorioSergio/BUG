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
echo "COMECOU AVAIL<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

date_default_timezone_set("UTC");
$datetime2 = date('Y-m-d\TH:i:s');
$username = '7971';
$password = 'WS121414';
$CPAId = 'IA8H';

$url = 'https://sws-crt.cert.havail.sabre.com';

$raw2 = '<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsd="http://www.w3.org/1999/XMLSchema">
<soap-env:Header>
   <eb:MessageHeader soap-env:mustUnderstand="1" eb:version="1.0">
      <eb:From>
         <eb:PartyId />
      </eb:From>
      <eb:To>
         <eb:PartyId />
      </eb:To>
      <eb:CPAId>' . $CPAId . '</eb:CPAId>
      <eb:ConversationId>V1@0001@client</eb:ConversationId>
      <eb:Service>SessionCreateRQ</eb:Service>
      <eb:Action>SessionCreateRQ</eb:Action>
      <eb:MessageData>
         <eb:MessageId>mid:20001209-133003-2333@clientofsabre.com</eb:MessageId>
         <eb:Timestamp>' . $datetime2 . '</eb:Timestamp>
      </eb:MessageData>
   </eb:MessageHeader>
   <wsse:Security xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/12/secext" xmlns:wsu="http://schemas.xmlsoap.org/ws/2002/12/utility">
      <wsse:UsernameToken>
         <wsse:Username>' . $username . '</wsse:Username>
         <wsse:Password>' . $password . '</wsse:Password>
         <Organization>' . $CPAId . '</Organization>
         <Domain>AA</Domain>
      </wsse:UsernameToken>
   </wsse:Security>
</soap-env:Header>
<soap-env:Body>
   <eb:Manifest soap-env:mustUnderstand="1" eb:version="1.0">
      <eb:Reference xlink:href="cid:rootelement" xlink:type="simple" />
   </eb:Manifest>
   <SessionCreateRQ>
      <POS>
         <Source PseudoCityCode="' . $CPAId . '" />
      </POS>
   </SessionCreateRQ>
   <ns:SessionCreateRQ xmlns:ns="http://www.opentravel.org/OTA/2002/11" />
</soap-env:Body>
</soap-env:Envelope>';

$headers2 = array(
    "Content-Type: text/xml;charset=utf-8",
    "Accept-Encoding: gzip",
    "Content-length: " . strlen($raw2)
);
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_HEADER, false);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, $raw2);
curl_setopt($ch2, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch2, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
$response2 = curl_exec($ch2);
curl_close($ch2);
echo "<br/>RESPONSE2";
echo '<xmp>';
var_dump($response2);
echo '</xmp>';

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response2);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Header = $Envelope->item(0)->getElementsByTagName("Header");
$MessageHeader = $Header->item(0)->getElementsByTagName("MessageHeader");
if ($MessageHeader->length > 0) {
    $From = $MessageHeader->item(0)->getElementsByTagName("From");
    if ($From->length > 0) {
        $FromPartyId = $From->item(0)->getElementsByTagName("PartyId");
        if ($FromPartyId->length > 0) {
            $type = $FromPartyId->item(0)->getAttribute("type");
            $FromPartyId = $FromPartyId->item(0)->nodeValue;
        } else {
            $FromPartyId = "";
        }
    }
    $To = $MessageHeader->item(0)->getElementsByTagName("To");
    if ($To->length > 0) {
        $ToPartyId = $To->item(0)->getElementsByTagName("PartyId");
        if ($ToPartyId->length > 0) {
            $ToPartyId = $ToPartyId->item(0)->nodeValue;
        } else {
            $ToPartyId = "";
        }
    }
    $CPAId = $MessageHeader->item(0)->getElementsByTagName("CPAId");
    if ($CPAId->length > 0) {
        $CPAId = $CPAId->item(0)->nodeValue;
    } else {
        $CPAId = "";
    }
    $ConversationId = $MessageHeader->item(0)->getElementsByTagName("ConversationId");
    if ($ConversationId->length > 0) {
        $ConversationId = $ConversationId->item(0)->nodeValue;
    } else {
        $ConversationId = "";
    }
    $MessageData = $MessageHeader->item(0)->getElementsByTagName("MessageData");
    if ($MessageData->length > 0) {
        $MessageId = $MessageData->item(0)->getElementsByTagName("MessageId");
        if ($MessageId->length > 0) {
            $MessageId = $MessageId->item(0)->nodeValue;
        } else {
            $MessageId = "";
        }
        $RefToMessageId = $MessageData->item(0)->getElementsByTagName("RefToMessageId");
        if ($RefToMessageId->length > 0) {
            $RefToMessageId = $RefToMessageId->item(0)->nodeValue;
        } else {
            $RefToMessageId = "";
        }
    }
}
$Security = $Header->item(0)->getElementsByTagName("Security");
if ($Security->length > 0) {
    $BinarySecurityToken = $Security->item(0)->getElementsByTagName("BinarySecurityToken");
    if ($BinarySecurityToken->length > 0) {
        $BinarySecurityToken = $BinarySecurityToken->item(0)->nodeValue;
    } else {
        $BinarySecurityToken = "";
    }
}

date_default_timezone_set("UTC");
$datetime = date('Y-m-d\TH:i:s');

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Header>
   <eb:MessageHeader soapenv:mustUnderstand="0" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader">
       <eb:From>
         <eb:PartyId></eb:PartyId>
      </eb:From>
      <eb:To>
         <eb:PartyId></eb:PartyId>
      </eb:To>
      <eb:CPAId>' . $CPAId . '</eb:CPAId>
      <eb:ConversationId>V1@0001@client</eb:ConversationId>
      <eb:Service>ContextChangeLLSRQ</eb:Service>
      <eb:Action>ContextChangeLLSRQ</eb:Action>
      <eb:MessageData>
         <eb:MessageId>mid:20001209-133003-2333@clientofsabre.com</eb:MessageId>
         <eb:Timestamp>' . $datetime . '</eb:Timestamp>
      </eb:MessageData>
   </eb:MessageHeader>
   <eb:Security soapenv:mustUnderstand="0" xmlns:eb="http://schemas.xmlsoap.org/ws/2002/12/secext">
      <eb:BinarySecurityToken>' . $BinarySecurityToken . '</eb:BinarySecurityToken>
   </eb:Security>
</soapenv:Header>
<soapenv:Body>
    <ContextChangeRQ xmlns="http://webservices.sabre.com/sabreXML/2011/10" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ReturnHostCommand="false" TimeStamp="2013-08-12T09:30:00-06:00" Version="2.0.3">
        <ChangeAAA PseudoCityCode="BJ1G"/>
    </ContextChangeRQ>
</soapenv:Body>
</soapenv:Envelope>';
echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$headers = array(
    "Content-Type: text/xml;charset=utf-8",
    "Accept-Encoding: gzip",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);
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
$inputDoc->loadXML($response2);
$Envelope = $inputDoc->getElementsByTagName("Envelope");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
