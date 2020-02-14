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
echo "COMECOU CANCEL BOOKING<br/>";
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

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/BookTransactions.asmx';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';
$reservationlocator = "NV655K";

$raw= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
   <CancelBooking>
      <CancelRQ Version="1.1" Language="en">
             <Login Password="' . $password . '" Email="' . $email . '"/>
         <CancelRequest ReservationLocator="' . $reservationlocator . '"/>
         <AdvancedOptions>
            <ShowBreakdownPrice>true</ShowBreakdownPrice>
         </AdvancedOptions>
      </CancelRQ>
   </CancelBooking>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Accept: application/xml",
    "Content-type: text/xml;charset=UTF-8",
    "Accept-Encoding: gzip, deflate",
    "SOAPAction: http://www.juniper.es/webservice/2007/CancelBooking",
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
}

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();

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
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$CancelBookingResponse = $Body->item(0)->getElementsByTagName("CancelBookingResponse");
if ($CancelBookingResponse->length > 0) {
    $BookingRS = $CancelBookingResponse->item(0)->getElementsByTagName("BookingRS");
    if ($BookingRS->length > 0) {
        $IntCode = $BookingRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $BookingRS->item(0)->getAttribute("TimeStamp");
        $Url = $BookingRS->item(0)->getAttribute("Url");
        $Warnings = $BookingRS->item(0)->getElementsByTagName("Warnings");
        if ($Warnings->length > 0) {
            $Warning = $Warnings->item(0)->getElementsByTagName("Warning");
            if ($Warning->length > 0) {
                $Code = $Warning->item(0)->getAttribute("Code");
                $Text = $Warning->item(0)->getAttribute("Text");
            }
            $CancelInfo = $Warnings->item(0)->getElementsByTagName("CancelInfo");
            if ($CancelInfo->length > 0) {
                $BookingCodeState = $CancelInfo->item(0)->getElementsByTagName("BookingCodeState");
                if ($BookingCodeState->length > 0) {
                    $BookingCodeState = $BookingCodeState->item(0)->nodeValue;
                } else {
                    $BookingCodeState = "";
                }
                $BookingCancelCost = $CancelInfo->item(0)->getElementsByTagName("BookingCancelCost");
                if ($BookingCancelCost->length > 0) {
                    $BookingCodeState = $BookingCancelCost->item(0)->nodeValue;
                } else {
                    $BookingCancelCost = "";
                }
                $BookingCancelCostCurrency = $CancelInfo->item(0)->getElementsByTagName("BookingCancelCostCurrency");
                if ($BookingCancelCostCurrency->length > 0) {
                    $BookingCancelCostCurrency = $BookingCancelCostCurrency->item(0)->nodeValue;
                } else {
                    $BookingCancelCostCurrency = "";
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
