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
echo "COMECOU PORTFOLIO<br/>";
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
   <ns:TransferPortfolio>
      <!--Optional:-->
      <ns:TransferPortfolioRQ Version="1.1" Language="en" Page="1" RecordsPerPage="500">
         <!--Optional:-->
         <ns:Login Password="' . $password . '" Email="' . $email . '"/>
      </ns:TransferPortfolioRQ>
   </ns:TransferPortfolio>
</soapenv:Body>
</soapenv:Envelope>';

$headers = array(
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "SOAPAction: http://www.juniper.es/webservice/2007/TransferPortfolio",
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
$TransferPortfolioResponse = $Body->item(0)->getElementsByTagName("TransferPortfolioResponse");
if ($TransferPortfolioResponse->length > 0) {
    $TransferPortfolioRS = $TransferPortfolioResponse->item(0)->getElementsByTagName("TransferPortfolioRS");
    if ($TransferPortfolioRS->length > 0) {
        $IntCode = $TransferPortfolioRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $TransferPortfolioRS->item(0)->getAttribute("TimeStamp");
        $Url = $TransferPortfolioRS->item(0)->getAttribute("Url");
        $ServicePortfolio = $TransferPortfolioRS->item(0)->getElementsByTagName("ServicePortfolio");
        if ($ServicePortfolio->length > 0) {
            $TotalRecords = $TransferPortfolioRS->item(0)->getAttribute("TotalRecords");
            $TotalPages = $TransferPortfolioRS->item(0)->getAttribute("TotalPages");
            $RecordsPerPage = $TransferPortfolioRS->item(0)->getAttribute("RecordsPerPage");
            $Page = $TransferPortfolioRS->item(0)->getAttribute("Page");
            $Service = $ServicePortfolio->item(0)->getElementsByTagName("Service");
            if ($Service->length > 0) {
                for ($i=0; $i < $Service->length; $i++) { 
                    $IntCode = $Service->item($i)->getAttribute("IntCode");
                    $Code = $Service->item($i)->getAttribute("Code");
                    $ServiceTypeCode = $Service->item($i)->getAttribute("ServiceTypeCode");
                    //Options
                    $Options = $Service->item($i)->getElementsByTagName("Options");
                    if ($Options->length > 0) {
                        $ServiceOption = $Options->item(0)->getElementsByTagName("ServiceOption");
                        if ($ServiceOption->length > 0) {
                            for ($iAux=0; $iAux < $ServiceOption->length; $iAux++) { 
                                $Code = $ServiceOption->item($iAux)->getAttribute("Code");
                                $NumberOfDays = $ServiceOption->item($iAux)->getAttribute("NumberOfDays");
                                $Order = $ServiceOption->item($iAux)->getAttribute("Order");
                            }
                        }
                    }
                    //Zones
                    $Zones = $Service->item($i)->getElementsByTagName("Zones");
                    if ($Zones->length > 0) {
                        $Zone = $Zones->item(0)->getElementsByTagName("Zone");
                        if ($Zone->length > 0) {
                            for ($iAux2=0; $iAux2 < $Zone->length; $iAux2++) { 
                                $Code = $Zone->item($iAux2)->getAttribute("Code");
                            }
                        }
                    }
                    //Origins
                    $Origins = $Service->item($i)->getElementsByTagName("Origins");
                    if ($Origins->length > 0) {
                        $Origin = $Origins->item(0)->getElementsByTagName("Origin");
                        if ($Origin->length > 0) {
                            for ($iAux3=0; $iAux3 < $Origin->length; $iAux3++) { 
                                $Code = $Origin->item($iAux3)->getAttribute("Code");
                            }
                        }
                    }
                    //Destinations
                    $Destinations = $Service->item($i)->getElementsByTagName("Destinations");
                    if ($Destinations->length > 0) {
                        $Destination = $Destinations->item(0)->getElementsByTagName("Destination");
                        if ($Destination->length > 0) {
                            for ($iAux4=0; $iAux4 < $Destination->length; $iAux4++) { 
                                $Code = $Destination->item($iAux4)->getAttribute("Code");
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