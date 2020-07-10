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
echo "COMECOU CANCEL<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.musement.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://wsi-test.americantours.com/ati-services/ATIInterface';

$username = '5m2z3i432';
$password = 'bmY9SWPELu';
$encode = $username . ":" . $password;
$auth = base64_encode("$encode");

$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
<soap:Header/>
<soap:Body>
    <ns1:OTA_CancelRQ Version="1.3">
        <ns1:UniqueID ID="6066776"/>
    </ns1:OTA_CancelRQ>
</soap:Body>
</soap:Envelope>';

$headers = array(
    "Content-type: text/xml;charset=UTF-8",
    "Accept-Encoding: gzip, deflate",
    "Authorization: Basic " . $auth,
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.musement.php');
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
if ($OTA_CancelRS->length > 0) {
    $Version = $OTA_CancelRS->item(0)->getAttribute("Version");
    $TimeStamp = $OTA_CancelRS->item(0)->getAttribute("TimeStamp");
    $CancelInfoRS = $OTA_CancelRS->item(0)->getElementsByTagName("CancelInfoRS");
    if ($CancelInfoRS->length > 0) {
        $UniqueID = $CancelInfoRS->item(0)->getElementsByTagName("UniqueID");
        if ($UniqueID->length > 0) {
            $ID = $UniqueID->item(0)->getAttribute("ID");
        }
    }
    $UniqueID = $OTA_CancelRS->item(0)->getElementsByTagName("UniqueID");
    if ($UniqueID->length > 0) {
        $ID = $UniqueID->item(0)->getAttribute("ID");
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>