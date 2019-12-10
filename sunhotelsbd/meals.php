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
echo "COMECOU MEALS<br/>";
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

$url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx?op=GetMeals";

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetMeals xmlns="http://xml.sunhotels.net/15/">
      <userName>testagent</userName>
      <password>785623</password>
      <language>en</language>
    </GetMeals>
  </soap:Body>
</soap:Envelope>';

$headers = array(
    'Accept-Encoding: gzip,deflate',
    'Host: xml.sunhotels.net',
    'Content-Type: text/xml; charset=utf-8',
    'SOAPAction: http://xml.sunhotels.net/15/GetMeals',
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

$GetMealsResponse = $Body->item(0)->getElementsByTagName("GetMealsResponse");
if ($GetMealsResponse->length > 0) {
    $getMealsResult = $GetMealsResponse->item(0)->getElementsByTagName("getMealsResult");
    if ($getMealsResult->length > 0) {
        $meals = $getMealsResult->item(0)->getElementsByTagName("meals");
        if ($meals->length > 0) {
            $meal = $meals->item(0)->getElementsByTagName("meal");
            if ($meal->length > 0) {
                for ($i=0; $i < $meal->length; $i++) { 
                    $id = $meal->item($i)->getElementsByTagName("id");
                    if ($id->length > 0) {
                        $id = $id->item(0)->nodeValue;
                    } else {
                        $id = 0;
                    }
                    $name = $meal->item($i)->getElementsByTagName("name");
                    if ($name->length > 0) {
                        $name = $name->item(0)->nodeValue;
                    } else {
                        $name = "";
                    }
                    $labels = $meal->item($i)->getElementsByTagName("labels");
                    if ($labels->length > 0) {
                        $label = $labels->item(0)->getElementsByTagName("label");
                        if ($label->length > 0) {
                            for ($j=0; $j < $label->length; $j++) { 
                                $id = $label->item($j)->getElementsByTagName("id");
                                if ($id->length > 0) {
                                    $id = $id->item(0)->nodeValue;
                                } else {
                                    $id = 0;
                                }
                                $text = $label->item($j)->getElementsByTagName("text");
                                if ($text->length > 0) {
                                    $text = $text->item(0)->nodeValue;
                                } else {
                                    $text = "";
                                }
                            }
                        }
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