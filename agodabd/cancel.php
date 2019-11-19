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
echo "COMECOU CANCEL";
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
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://sandbox-affiliateapisecure.agoda.com/xmlpartner/xmlbookservice/cancel_service';

$siteid = 1831338;
$apikey = "b57a754c-5e06-4cdd-ac0d-2ea58c48ef74";


$raw = '<?xml version="1.0" encoding="utf-8"?>
<CancellationRequestV2 siteid="' . $siteid . '" apikey="' . $apikey . '" xmlns="http://xml.agoda.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<BookingID>8283511</BookingID>
</CancellationRequestV2>';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'text/xml;charset=utf-8',
    'Authorization' => '1831338:b57a754c-5e06-4cdd-ac0d-2ea58c48ef74'
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

die();

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
$inputDoc->loadXML($response);
$CancellationResponseV2 = $inputDoc->getElementsByTagName("CancellationResponseV2");
$status = $ConfirmCancellationResponseV2->item(0)->getAttribute('status');

if ($status == "200") {
    $CancellationSummary = $CancellationResponseV2->item(0)->getElementsByTagName('CancellationSummary');
    if ($CancellationSummary->length > 0) {
        $BookingID = $CancellationSummary->item(0)->getElementsByTagName('BookingID');
        if ($BookingID->length > 0) {
            $BookingID = $BookingID->item(0)->nodeValue;
        } else {
            $BookingID = "";
        }
        $Reference = $CancellationSummary->item(0)->getElementsByTagName('Reference');
        if ($Reference->length > 0) {
            $Reference = $Reference->item(0)->nodeValue;
        } else {
            $Reference = "";
        }

        $canceltext = "";
        $Cancellation = $CancellationSummary->item(0)->getElementsByTagName('Cancellation');
        if ($Cancellation->length > 0) {
            $PolicyText = $Cancellation->item(0)->getElementsByTagName('PolicyText');
            if ($PolicyText->length > 0) {
                $language = $PolicyText->item(0)->getAttribute('language');
                $canceltext = $PolicyText->item(0)->nodeValue;
            } else {
                $canceltext = "";
            }
        }

        $paymenttext = "";
        $Payment = $CancellationSummary->item(0)->getElementsByTagName('Payment');
        if ($Payment->length > 0) {
            $PaymentRateInclusive = $Payment->item(0)->getElementsByTagName('PaymentRateInclusive');
            if ($PaymentRateInclusive->length > 0) {
                $currency = $PaymentRateInclusive->item(0)->getAttribute('currency');
                $canceltext = $PaymentRateInclusive->item(0)->nodeValue;
            } else {
                $paymenttext = "";
            }
        }

        $refundtext = "";
        $Refund = $CancellationSummary->item(0)->getElementsByTagName('Refund');
        if ($Refund->length > 0) {
            $RefundRateInclusive = $Refund->item(0)->getElementsByTagName('RefundRateInclusive');
            if ($RefundRateInclusive->length > 0) {
                $currency = $RefundRateInclusive->item(0)->getAttribute('currency');
                $refundtext = $RefundRateInclusive->item(0)->nodeValue;
            } else {
                $refundtext = "";
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