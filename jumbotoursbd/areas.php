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
echo "COMECOU AREAS<br/>";
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
    <tns:getAreas xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:tns="http://xtravelsystem.com/v1_0rc1/common/types" xmlns:xs="http://www.w3.org/2001/XMLSchema">
        <GetAreasRQ_1>
            <agencyCode>613</agencyCode>
            <brandCode>1</brandCode>
            <pointOfSaleId>1</pointOfSaleId>
            <areasTreeCode>1</areasTreeCode>
            <language>EN</language>
        </GetAreasRQ_1>
    </tns:getAreas>
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
$getAreasResponse = $Body->item(0)->getElementsByTagName("getAreasResponse");
if ($getAreasResponse->length > 0) {
    $result = $getAreasResponse->item(0)->getElementsByTagName("result");
    if ($result->length > 0) {
        $areas = $result->item(0)->getElementsByTagName("areas");
        if ($areas->length > 0) {
            $code = $areas->item(0)->getElementsByTagName("code");
            if ($code->length > 0) {
                $code = $code->item(0)->nodeValue;
            } else {
                $code = 0;
            }
            $name = $areas->item(0)->getElementsByTagName("name");
            if ($name->length > 0) {
                $name = $name->item(0)->nodeValue;
            } else {
                $name = "";
            }
            $areascity = $areas->item(0)->getElementsByTagName("areas");
            if ($areascity->length > 0) {
                $areascity_code = $areascity->item(0)->getElementsByTagName("code");
                if ($areascity_code->length > 0) {
                    $areascity_code = $areascity_code->item(0)->nodeValue;
                } else {
                    $areascity_code = 0;
                }
                $areascity_name = $areas->item(0)->getElementsByTagName("name");
                if ($areascity_name->length > 0) {
                    $areascity_name = $areascity_name->item(0)->nodeValue;
                } else {
                    $areascity_name = "";
                }
            }
            $areas3 = $areas->item(0)->getElementsByTagName("areas");
            if ($areas3->length > 0) {
                for ($i=0; $i < $areas3->length; $i++) { 
                    $code = $areas3->item($i)->getElementsByTagName("code");
                    if ($code->length > 0) {
                        $code = $code->item(0)->nodeValue;
                    } else {
                        $code = 0;
                    }
                    $name = $areas3->item($i)->getElementsByTagName("name");
                    if ($name->length > 0) {
                        $name = $name->item(0)->nodeValue;
                    } else {
                        $name = "";
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
echo '<br/>Done';
?>