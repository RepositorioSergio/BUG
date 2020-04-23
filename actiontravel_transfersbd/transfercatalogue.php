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
echo "COMECOU CATALOGUE<br/>";
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

$url = 'http://xml-uat.bookingengine.es/webservice/JP/Operations/StaticDataTransactions.asmx';

$email = 'paulo@corp.bug-software.com';
$password = 'xA2d@a1X';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
   <ns:TransferCatalogueData>
      <!--Optional:-->
      <ns:TransferCatalogueDataRQ Version="1.1" Language="en">
         <!--Optional:-->
         <ns:Login Password="' . $password . '" Email="' . $email . '"/>
      </ns:TransferCatalogueDataRQ>
   </ns:TransferCatalogueData>
</soapenv:Body>
</soapenv:Envelope>';

$headers = array(
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "SOAPAction: http://www.juniper.es/webservice/2007/TransferCatalogueData",
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

echo $return;
echo $response;
echo $return;
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
$TransferCatalogueDataResponse = $Body->item(0)->getElementsByTagName("TransferCatalogueDataResponse");
if ($TransferCatalogueDataResponse->length > 0) {
    $CatalogueDataRS = $TransferCatalogueDataResponse->item(0)->getElementsByTagName("CatalogueDataRS");
    if ($CatalogueDataRS->length > 0) {
        $IntCode = $CatalogueDataRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $CatalogueDataRS->item(0)->getAttribute("TimeStamp");
        $Url = $CatalogueDataRS->item(0)->getAttribute("Url");
        $TransferStaticData = $CatalogueDataRS->item(0)->getElementsByTagName("TransferStaticData");
        if ($TransferStaticData->length > 0) {
            $TransferTypeList = $TransferStaticData->item(0)->getElementsByTagName("TransferTypeList");
            if ($TransferTypeList->length > 0) {
                $ServiceType = $TransferTypeList->item(0)->getElementsByTagName("ServiceType");
                if ($ServiceType->length > 0) {
                    for ($i=0; $i < $ServiceType->length; $i++) { 
                        $Code = $ServiceType->item($i)->getAttribute("Code");
                        $ParentCode = $ServiceType->item($i)->getAttribute("ParentCode");
                        $Name = $ServiceType->item($i)->getElementsByTagName("Name");
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
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>