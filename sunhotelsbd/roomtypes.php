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
echo "COMECOU ROOMTYPES<br/>";
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


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

date_default_timezone_set('UTC');
//$date = date("Y-m-d H:i:s");
$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");

$url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx?op=GetRoomTypes";

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetRoomTypes xmlns="http://xml.sunhotels.net/15/">
      <userName>testagent</userName>
      <password>785623</password>
      <language>en</language>
    </GetRoomTypes>
  </soap:Body>
</soap:Envelope>';

$headers = array(
    'Accept-Encoding: gzip,deflate',
    'Host: xml.sunhotels.net',
    'Content-Type: text/xml; charset=utf-8',
    'SOAPAction: http://xml.sunhotels.net/15/GetRoomTypes',
    'Content-Length: ' . strlen($raw)
); 

$ch = curl_init();
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip,deflate");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $response;

echo "<xmp>";
echo $response;
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
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
$Body = $Envelope->item(0)->getElementsByTagName("Body");

$GetRoomTypesResponse = $Body->item(0)->getElementsByTagName("GetRoomTypesResponse");
if ($GetRoomTypesResponse->length > 0) {
    $getRoomTypesResult = $GetRoomTypesResponse->item(0)->getElementsByTagName("getRoomTypesResult");
    if ($getRoomTypesResult->length > 0) {
        $roomTypes = $getRoomTypesResult->item(0)->getElementsByTagName("roomTypes");
        if ($roomTypes->length > 0) {
            $roomType = $roomTypes->item(0)->getElementsByTagName("roomType");
            if ($roomType->length > 0) {
                for ($i=0; $i < $roomType->length; $i++) { 
                    $id = $roomType->item($i)->getElementsByTagName("id");
                    if ($id->length > 0) {
                        $id = $id->item(0)->nodeValue;
                    } else {
                        $id = 0;
                    }
                    $name = $roomType->item($i)->getElementsByTagName("name");
                    if ($name->length > 0) {
                        $name = $name->item(0)->nodeValue;
                    } else {
                        $name = "";
                    }
                    $sharedRoom = $roomType->item($i)->getElementsByTagName("sharedRoom");
                    if ($sharedRoom->length > 0) {
                        $sharedRoom = $sharedRoom->item(0)->nodeValue;
                    } else {
                        $sharedRoom = "";
                    }
                    $sharedFacilities = $roomType->item($i)->getElementsByTagName("sharedFacilities");
                    if ($sharedFacilities->length > 0) {
                        $sharedFacilities = $sharedFacilities->item(0)->nodeValue;
                    } else {
                        $sharedFacilities = "";
                    }
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