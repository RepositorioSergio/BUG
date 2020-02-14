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
echo "COMECOU SERVICE AVAILABILITY";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/staticdatatransactions.asmx';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
  <ZoneList>
    <ZoneListRQ Version="1.1" Language="en">
      <Login Password="' . $password . '" Email="' . $email . '"/>
      <ZoneListRequest ProductType="TKT"/>
    </ZoneListRQ>
  </ZoneList>
</soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/xml",
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$xmlresult = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
var_dump($xmlresult);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$ZoneListResponse = $Body->item(0)->getElementsByTagName("ZoneListResponse");
if ($ZoneListResponse->length > 0) {
    $ZoneListRS = $ZoneListResponse->item(0)->getElementsByTagName("ZoneListRS");
    if ($ZoneListRS->length > 0) {
        $IntCode = $ZoneListRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $ZoneListRS->item(0)->getAttribute("TimeStamp");
        $Url = $ZoneListRS->item(0)->getAttribute("Url");
        $ZoneList = $ZoneListRS->item(0)->getElementsByTagName("ZoneList");
        if ($ZoneList->length > 0) {
            $Zone = $ZoneList->item(0)->getElementsByTagName("Zone");
            if ($Zone->length > 0) {
                for ($i=0; $i < $Zone->length; $i++) { 
                    $Code = $Zone->item($i)->getAttribute("Code");
                    $Name = $Zone->item($i)->getElementsByTagName("Name");
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
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
