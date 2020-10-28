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
echo "COMECOU COTIZAR<br/>";
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
      <ser:cotizar soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
         <xml>
            <![CDATA[
<cotizacion>
<pais>510</pais>
<codigoAgencia>87819</codigoAgencia>
<numeroSucursal>0</numeroSucursal>
<cantidadDias>10</cantidadDias>
<fechaInicio>10/12/2021</fechaInicio>
<fechaFin>19/12/2021</fechaFin>
<planFamiliar>false</planFamiliar>
<destino>02</destino>
<clientes>
<clienteCotizacion>
<nombre>pablo</nombre>
<apellido>test</apellido>
<edad>30</edad>
<fechaNacimiento>01/01/1990</fechaNacimiento>
</clienteCotizacion>
</clientes>
</cotizacion>]]>
         </xml>
         <usuario xsi:type="xsd:string">WSTEST</usuario>
         <password xsi:type="xsd:string">123456</password>
      </ser:cotizar>
   </soapenv:Body>
</soapenv:Envelope>';

echo $raw;

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

echo '<br/>Done';
?>