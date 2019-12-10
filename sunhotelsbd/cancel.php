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
$affiliate_id = 0;
$branch_filter = "";


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

date_default_timezone_set('UTC');
//$date = date("Y-m-d H:i:s");
$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");

$url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx?op=CancelBooking";

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <CancelBooking xmlns="http://xml.sunhotels.net/15/">
      <userName>testagent</userName>
      <password>785623</password>
      <bookingID>SH9230299</bookingID>
      <language>en</language>
    </CancelBooking>
  </soap:Body>
</soap:Envelope>';

$headers = array(
    'Accept-Encoding: gzip,deflate',
    'Host: xml.sunhotels.net',
    'Content-Type: text/xml; charset=utf-8',
    'SOAPAction: http://xml.sunhotels.net/15/CancelBooking',
    'Content-Length: ' . strlen($raw)
); 

$ch = curl_init();
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip,deflate");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
echo $response;
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response2);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");

$CancelBookingResponse = $Body->item(0)->getElementsByTagName("CancelBookingResponse");
if ($CancelBookingResponse->length > 0) {
    $result = $CancelBookingResponse->item(0)->getElementsByTagName("result");
    if ($result->length > 0) {
        $Code = $result->item(0)->getElementsByTagName("Code");
        if ($Code->length > 0) {
            $Code = $Code->item(0)->nodeValue;
        } else {
            $Code = "";
        }
        $CancellationPaymentMethod = $result->item(0)->getElementsByTagName("CancellationPaymentMethod");
        if ($CancellationPaymentMethod->length > 0) {
            $id = $CancellationPaymentMethod->item(0)->getAttribute("id");
            $name = $CancellationPaymentMethod->item(0)->getAttribute("name");
            //cancellationfee
            $cancellationfee = $CancellationPaymentMethod->item(0)->getElementsByTagName("cancellationfee");
            if ($cancellationfee->length > 0) {
                $currency = $cancellationfee->item(0)->getAttribute("currency");
                $cancellationfee = $cancellationfee->item(0)->nodeValue;
            } else {
                $cancellationfee = "";
            }
            //cancellation
            $cancellation = $result->item(0)->getElementsByTagName("cancellation");
            if ($cancellation->length > 0) {
                $type = $cancellation->item(0)->getAttribute("type");
                $activecancellationpolicy = $cancellation->item(0)->getElementsByTagName("activecancellationpolicy");
                if ($activecancellationpolicy->length > 0) {
                    $deadline = $activecancellationpolicy->item(0)->getElementsByTagName("deadline");
                    if ($deadline->length > 0) {
                        $deadline = $deadline->item(0)->nodeValue;
                    } else {
                        $deadline = "";
                    }
                    $percentage = $activecancellationpolicy->item(0)->getElementsByTagName("percentage");
                    if ($percentage->length > 0) {
                        $percentage = $percentage->item(0)->nodeValue;
                    } else {
                        $percentage = "";
                    }
                    $text = $activecancellationpolicy->item(0)->getElementsByTagName("text");
                    if ($text->length > 0) {
                        $text = $text->item(0)->nodeValue;
                    } else {
                        $text = "";
                    }
                }
            }
        } else {
            $id = "";
            $name = "";
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>