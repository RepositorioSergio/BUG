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
echo "COMECOU PING<br/>";
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
echo $return;
echo $auth;
echo $return;

$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
<soap:Header/>
<soap:Body>
<ns1:OTA_PingRQ Version="1.3">
<ns1:EchoData>Hello ATI!</ns1:EchoData>
</ns1:OTA_PingRQ>
</soap:Body>
</soap:Envelope>';

$headers = array(
    "Content-type: text/xml;charset=UTF-8",
    "Accept-Encoding: gzip, deflate",
    "SOAPAction: ",
    "Authorization: Basic " . $auth,
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
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

echo $return;
echo $error;
echo $return;
echo "<xmp>";
var_dump($response);
echo "</xmp>"; 
die();
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
                    $Searchable = $Zone->item($i)->getAttribute("Searchable");
                    $Code = $Zone->item($i)->getAttribute("Code");
                    $IATA = $Zone->item($i)->getAttribute("IATA");
                    $JPDCode = $Zone->item($i)->getAttribute("JPDCode");
                    $Name = $Zone->item($i)->getElementsByTagName("Name");
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('zonelist');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Code' => $Code,
                            'IATA' => $IATA,
                            'Name' => $Name
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO: " . $e;
                        echo $return;
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