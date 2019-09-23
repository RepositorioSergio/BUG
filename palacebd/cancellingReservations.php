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
// Start
$affiliate_id_palace = 0;
$branch_filter = "";


$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = "https://api.palaceresorts.com/EnterpriseServiceInterface/ServiceInterface.asmx";

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <soap:Body>
  <CancellingReservationsArg xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <cancellationRequest>
    <Data xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationRequest.xsd">
      <cancellationList>
        <Hotel xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">MPG</Hotel>
        <Folio xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">12279717</Folio>
        <Code xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">TLAV15I</Code>
        <Reason xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">TEST_CANCEL</Reason>
        <Ent_User xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">CTM-PERU</Ent_User>
        <Ent_Term xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">CTM-PERU</Ent_Term>
        <Cancelled xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">false</Cancelled>
        <ErrDescription xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">TEST</ErrDescription>
      </cancellationList>
    </Data>
    <Tag></Tag>
    <AuthInfo xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationRequest.xsd">
      <Recnum xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd">0</Recnum>
      <Ent_User xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd">CTM-PERU</Ent_User>
      <Ent_Pass xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd">x4Mg82k9WS</Ent_Pass>
      <Ent_Term xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd">CTM-PERU</Ent_Term>
    </AuthInfo>
  </cancellationRequest>
</CancellingReservationsArg>
  </soap:Body>
</soap:Envelope>';


$raw2 = '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd" xmlns:ns2="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationRequest.xsd" xmlns:ns3="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd" xmlns:ns4="http://localhost/xmlschemas/enterpriseservice/16-07-2009/">
  <SOAP-ENV:Body>
  <ns4:CancellingReservationsArg>
  <ns2:cancellationRequest>
    <ns2:Data>
      <ns2:cancellationList>
        <ns1:Hotel>MPG</ns1:Hotel>
        <ns1:Folio>12279717</ns1:Folio>
        <ns1:Code>TLAV15I</ns1:Code>
        <ns1:Reason>TEST_CANCEL</ns1:Reason>
        <ns1:Ent_User>CTM-PERU</ns1:Ent_User>
        <ns1:Ent_Term>CTM-PERU</ns1:Ent_Term>
        <ns1:Cancelled>false</ns1:Cancelled>
        <ns1:ErrDescription>TEST</ns1:ErrDescription>
      </ns2:cancellationList>
    </ns2:Data>
    <ns2:AuthInfo>
        <ns3:Recnum>0</ns3:Recnum>
        <ns3:Ent_User>CTM-PERU</ns3:Ent_User>
        <ns3:Ent_Pass>x4Mg82k9WS</ns3:Ent_Pass>
        <ns3:Ent_Term>CTM-PERU</ns3:Ent_Term>
    </ns2:AuthInfo>
  </ns2:cancellationRequest>
</ns4:CancellingReservationsArg>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

$raw3 = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Header>
    <RequestHeader xmlns="http://localhost/xmlschemas/enterpriseservice/16-07-2009/">
      <Headers>
        <anyType />
        <anyType />
      </Headers>
    </RequestHeader>
  </soap12:Header>
  <soap12:Body>
    <CancellingReservationsArg xmlns="http://localhost/xmlschemas/enterpriseservice/16-07-2009/">
      <cancellationRequest xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationRequest.xsd">
        <Data>
          <cancellationList>
            <Hotel xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">MPG</Hotel>
            <Folio xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">12279717</Folio>
            <Code xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">TLAV15I</Code>
            <Reason xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">TEST_CANCEL</Reason>
            <Ent_User xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">CTM-PERU</Ent_User>
            <Ent_Term xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">CTM-PERU</Ent_Term>
            <Cancelled xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">false</Cancelled>
            <ErrDescription xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">TEST</ErrDescription>
          </cancellationList>
          <cancellationList>
            <Hotel xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">MPG</Hotel>
            <Folio xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">12279717</Folio>
            <Code xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">TLAV15I</Code>
            <Reason xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">TEST_CANCEL</Reason>
            <Ent_User xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">CTM-PERU</Ent_User>
            <Ent_Term xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">CTM-PERU</Ent_Term>
            <Cancelled xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">false</Cancelled>
            <ErrDescription xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/cancellationList.xsd">TEST</ErrDescription>
          </cancellationList>
        </Data>
        <Tag></Tag>
        <AuthInfo>
          <Recnum xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd">0</Recnum>
          <Ent_User xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd">CTM-PERU</Ent_User>
          <Ent_Pass xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd">x4Mg82k9WS</Ent_Pass>
          <Ent_Term xmlns="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd">CTM-PERU</Ent_Term>
        </AuthInfo>
      </cancellationRequest>
    </CancellingReservationsArg>
  </soap12:Body>
</soap12:Envelope>';

/* $client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Host: api.palaceresorts.com",
    "Content-length: " . strlen($raw)
));

$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
$response = $response->getBody();
} else {
$logger = new Logger();
$writer = new Writer\Stream('/srv/www/htdocs/error_log');
$logger->addWriter($writer);
$logger->info($client->getUri());
$logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
echo $return;
echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
echo $return;
die();
}  */
$headers = array(
    "Content-type: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Host: api.palaceresorts.com",
    "SOAPAction: http://localhost/xmlschemas/enterpriseservice/16-07-2009/CancellingReservations",
    "Content-length: " . strlen($raw2)
); // SOAPAction: your op URL

$headers2 = array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Host: api.palaceresorts.com",
    "SOAPAction: http://localhost/xmlschemas/enterpriseservice/16-07-2009/CancellingReservationsArg",
    "Content-length: " . strlen($raw3)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);

echo $response;

$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>'; 
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
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

$GetAvailabilityResponse = $Body->item(0)->getElementsByTagName("GetAvailabilityResponse");
if ($GetAvailabilityResponse->length > 0) {
    $roomAvailabilityResponse = $GetAvailabilityResponse->item(0)->getElementsByTagName("roomAvailabilityResponse");
    if ($roomAvailabilityResponse->length > 0) {
        $Hotel = $roomAvailabilityResponse->item(0)->getElementsByTagName('Hotel');
        if ($Hotel->length > 0) {
            $Hotel = $Hotel->item(0)->nodeValue;
        } else {
            $Hotel = "";
        }
        $shid = $Hotel;
        $sfilter[] = " sid='$Hotel' ";
        $TotalAmount = $roomAvailabilityResponse->item(0)->getElementsByTagName('TotalAmount');
        if ($TotalAmount->length > 0) {
            $TotalAmount = $TotalAmount->item(0)->nodeValue;
        } else {
            $TotalAmount = "";
        }
        $Moneda = $roomAvailabilityResponse->item(0)->getElementsByTagName('Moneda');
        if ($Moneda->length > 0) {
            $Moneda = $Moneda->item(0)->nodeValue;
        } else {
            $Moneda = "";
        }
        $TipoCambio = $roomAvailabilityResponse->item(0)->getElementsByTagName('TipoCambio');
        if ($TipoCambio->length > 0) {
            $TipoCambio = $TipoCambio->item(0)->nodeValue;
        } else {
            $TipoCambio = "";
        }
        $Tarifa1raNoche = $roomAvailabilityResponse->item(0)->getElementsByTagName('Tarifa1raNoche');
        if ($Tarifa1raNoche->length > 0) {
            $Tarifa1raNoche = $Tarifa1raNoche->item(0)->nodeValue;
        } else {
            $Tarifa1raNoche = "";
        }
        $RateCode = $roomAvailabilityResponse->item(0)->getElementsByTagName('RateCode');
        if ($RateCode->length > 0) {
            $RateCode = $RateCode->item(0)->nodeValue;
        } else {
            $RateCode = "";
        }
        $DescripcionTarifa = $roomAvailabilityResponse->item(0)->getElementsByTagName('DescripcionTarifa');
        if ($DescripcionTarifa->length > 0) {
            $DescripcionTarifa = $DescripcionTarifa->item(0)->nodeValue;
        } else {
            $DescripcionTarifa = "";
        }
        
        $Data = $roomAvailabilityResponse->item(0)->getElementsByTagName('Data');
        if ($Data->length > 0) {
            $Availability = $Data->item(0)->getElementsByTagName('Availability');
            if ($Availability->length > 0) {
                $dayAvailable = $Availability->item(0)->getElementsByTagName('dayAvailable');
                if ($dayAvailable->length > 0) {
                    for ($i = 0; $i < $dayAvailable->length; $i ++) {
                        $Day = $dayAvailable->item($i)->getElementsByTagName('Day');
                        if ($Day->length > 0) {
                            $Day = $Day->item(0)->nodeValue;
                        } else {
                            $Day = "";
                        }
                        $Available = $dayAvailable->item($i)->getElementsByTagName('Available');
                        if ($Available->length > 0) {
                            $Available = $Available->item(0)->nodeValue;
                        } else {
                            $Available = "";
                        }
                        $Rate = $dayAvailable->item($i)->getElementsByTagName('Rate');
                        if ($Rate->length > 0) {
                            $Rate = $Rate->item(0)->nodeValue;
                        } else {
                            $Rate = "";
                        }
                        $RateCode = $dayAvailable->item($i)->getElementsByTagName('RateCode');
                        if ($RateCode->length > 0) {
                            $RateCode = $RateCode->item(0)->nodeValue;
                        } else {
                            $RateCode = "";
                        }
                        $RateCodeDescription = $dayAvailable->item($i)->getElementsByTagName('RateCodeDescription');
                        if ($Day->length > 0) {
                            $RateCodeDescription = $RateCodeDescription->item(0)->nodeValue;
                        } else {
                            $RateCodeDescription = "";
                        }
                        $Currency = $dayAvailable->item($i)->getElementsByTagName('Currency');
                        if ($Currency->length > 0) {
                            $Currency = $Currency->item(0)->nodeValue;
                        } else {
                            $Currency = "";
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
echo '<br/>Done';
?>