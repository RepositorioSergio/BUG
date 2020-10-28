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
use SoapClient;
echo "COMECOU VOUCHER PAX<br/>";
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}

$url = 'https://wwwp.assistcard.net/ws/services/AssistCardService?wsdl';

$raw = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://services.ws.icard.com">
   <soapenv:Header/>
   <soapenv:Body>
      <ser:getVoucherPax soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
        <country>510</country> 
        <personalId>5555555TEST</personalId> 
        <user>WSTEST</user> 
        <password>123456</password>
      </ser:getVoucherPax>
   </soapenv:Body>
</soapenv:Envelope>';

$headers = array(
    "Content-type: text/xml;charset=UTF-8",
    "Accept: text/xml",
    "Accept-Encoding: gzip,deflate",
    "SOAPAction: ''",
    "User-Agent: Apache-HttpClient/4.1.1 (java 1.5)",
    "Connection: Keep-Alive",
    "Content-length: " . strlen($raw)
);

// POST https://www.assist-card.net/ws/services/AssistCardService HTTP/1.1
$url = 'https://wwwp.assist-card.net/ws/services/AssistCardService';
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $response;

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$respuesta = $inputDoc->getElementsByTagName("respuesta");
$cotizacionDolar = $respuesta->item(0)->getElementsByTagName('cotizacionDolar');
if ($cotizacionDolar->length > 0) {
    $cotizacionDolar = $cotizacionDolar->item(0)->nodeValue;
} else {
    $cotizacionDolar = "";
}


echo '<br/>Done';
?>