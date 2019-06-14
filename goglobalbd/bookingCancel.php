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
echo "COMECOU BOOKING CANCEL TARDE";
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
$config = new \Zend\Config\Config(include '../config/autoload/global.goglobal.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:gog="http://www.goglobal.travel/">
   <soapenv:Header/>
   <soapenv:Body>
      <gog:MakeRequest>
         <gog:requestType>3</gog:requestType>
         <gog:xmlRequest><![CDATA[
            <Root>
                <Header>
                    <Agency>1521636</Agency>
                    <User>CLUB1XML</User>
                    <Password>andrade1998</Password>
                    <Operation>BOOKING_CANCEL_REQUEST</Operation>
                    <OperationType>Request</OperationType>
                </Header>
                <Main>
                    <GoBookingCode>500003345</GoBookingCode>
                </Main>
            </Root>
         ]]></gog:xmlRequest>
      </gog:MakeRequest>
   </soapenv:Body>
</soapenv:Envelope>';

/* $client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: text/xml; charset=utf-8",
    "Content-length: " . strlen($raw)
));

$client->setUri('http://xml.qa.goglobal.travel/XMLWebService.asmx');
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
} */

$GoGlobalServiceURL = 'http://xml.qa.goglobal.travel/XMLWebService.asmx';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $GoGlobalServiceURL);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: text/xml; charset=utf-8",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);

if ($error != "") {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
} else {
    echo $return;
    echo "NAO TEM ERROS.";
    echo $return;
}


curl_close($ch);
echo $return;
echo $response;
echo $return;

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$MakeRequestResult = $inputDoc->getElementsByTagName("MakeRequestResult");
if ($MakeRequestResult->length > 0) {
    $response = $MakeRequestResult->item(0)->nodeValue;
} else {
    $response = "";
}

$config = new \Zend\Config\Config(include '../config/autoload/global.goglobal.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc2 = new DOMDocument();
$inputDoc2->loadXML($response);
$Root = $inputDoc2->getElementsByTagName("Root");
if ($Root->length > 0) {
    $Main = $Root->item(0)->getElementsByTagName("Main");
    if ($Main->length > 0) {
        $GoBookingCode = $Main->item(0)->getElementsByTagName("GoBookingCode");
        if ($GoBookingCode->length > 0) {
            $GoBookingCode = $GoBookingCode->item(0)->nodeValue;
        } else {
            $GoBookingCode = "";
        }
        $BookingStatus = $Main->item(0)->getElementsByTagName("BookingStatus");
        if ($BookingStatus->length > 0) {
            $BookingStatus = $BookingStatus->item(0)->nodeValue;
        } else {
            $BookingStatus = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('bookingCancel');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'GoBookingCode' => $GoBookingCode,
                'BookingStatus' => $BookingStatus
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error: " . $e;
            echo $return;
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>