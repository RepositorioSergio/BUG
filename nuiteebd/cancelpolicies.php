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

$config = new \Zend\Config\Config(include '../config/autoload/global.nuitee.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://ws.nuitee.com/nuitee/Nuitee?WSDL';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:nuit="http://www.nuitee.ma">
<soapenv:Header/>
<soapenv:Body>
   <nuit:performGetCancellationPolicy>
      <getCancellationPolicyReq>
         <sessionId>20200420|20200427|en|MA|USD|2A0C|1091|9xGqt|1583513870770kwfU</sessionId>
         <hotelCode>215547</hotelCode>
         <rateDetailCode>7-215547|NRFN|20200305|9|57101</rateDetailCode>
         <timeout>1600</timeout>
      </getCancellationPolicyReq>
   </nuit:performGetCancellationPolicy>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type: text/xml; charset=utf-8',
    'Accept: text/xml',
    'Content-Length: ' . strlen($raw)
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
}

echo '<xmp>';
var_dump($response);
echo '</xmp>';
$config = new \Zend\Config\Config(include '../config/autoload/global.nuitee.php');
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
$Envelope = $inputDoc->getElementsByTagName('Envelope');
$Body = $Envelope->item(0)->getElementsByTagName('Body');
$performGetCancellationPolicyResponse = $Body->item(0)->getElementsByTagName('performGetCancellationPolicyResponse');
if ($performGetCancellationPolicyResponse->length > 0) {
    $cancelPoliciesInfos = $performGetCancellationPolicyResponse->item(0)->getElementsByTagName('cancelPoliciesInfos');
    if ($cancelPoliciesInfos->length > 0) {
        $refundableTag = $cancelPoliciesInfos->item(0)->getElementsByTagName('refundableTag');
        if ($refundableTag->length > 0) {
            $refundableTag = $refundableTag->item(0)->nodeValue;
        } else {
            $refundableTag = "";
        }
        $defaultPolicy = $cancelPoliciesInfos->item(0)->getElementsByTagName('defaultPolicy');
        if ($defaultPolicy->length > 0) {
            $defaultPolicy = $defaultPolicy->item(0)->nodeValue;
        } else {
            $defaultPolicy = "";
        }
        $cancelPolicyInfos = $cancelPoliciesInfos->item(0)->getElementsByTagName('cancelPolicyInfos');
        if ($cancelPolicyInfos->length > 0) {
            $amount = $cancelPolicyInfos->item(0)->getElementsByTagName('amount');
            if ($amount->length > 0) {
                $amount = $amount->item(0)->nodeValue;
            } else {
                $amount = "";
            }
            $cancelTime = $cancelPolicyInfos->item(0)->getElementsByTagName('cancelTime');
            if ($amount->length > 0) {
                $cancelTime = $cancelTime->item(0)->nodeValue;
            } else {
                $cancelTime = "";
            }
            $type = $cancelPolicyInfos->item(0)->getElementsByTagName('type');
            if ($type->length > 0) {
                $type = $type->item(0)->nodeValue;
            } else {
                $type = "";
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