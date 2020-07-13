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
echo "COMECOU CANCEL FEES<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.infinitastravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: application/xml",
    "Content-length: ".strlen($raw)
));

$url = "https://xmlsandbox.rentalcars.com/service/ServiceRequest.do";

$raw = '<CancelFeesRQ>
<Credentials username="club1hot944" password="club1hot944" remoteIp="91.151.7.6"/>
<Booking id="123456789"></Booking>
<Email>mrsmith@rentalcars.com</Email>
</CancelFeesRQ>';

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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.infinitastravel.php');
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
$CancelFeesRS = $inputDoc->getElementsByTagName("CancelFeesRS");
if ($CancelFeesRS->length > 0) {
    $Breakdown = $CancelFeesRS->item(0)->getElementsByTagName("Breakdown");
    if ($Breakdown->length > 0) {
        $Total = $Breakdown->item(0)->getElementsByTagName("Total");
        if ($Total->length > 0) {
            $Total_amount = $Total->item(0)->getAttribute("amount");
            $Total_currency = $Total->item(0)->getAttribute("currency");
        }
        $Paid = $Breakdown->item(0)->getElementsByTagName("Paid");
        if ($Paid->length > 0) {
            $Paid_amount = $Paid->item(0)->getAttribute("amount");
            $Paid_currency = $Paid->item(0)->getAttribute("currency");
        }
        $Refundable = $Breakdown->item(0)->getElementsByTagName("Refundable");
        if ($Refundable->length > 0) {
            $Refundable_amount = $Refundable->item(0)->getAttribute("amount");
            $Refundable_currency = $Refundable->item(0)->getAttribute("currency");
        }
        $Fees = $Breakdown->item(0)->getElementsByTagName("Fees");
        if ($Fees->length > 0) {
            $Fee = $Fees->item(0)->getElementsByTagName("Fee");
            if ($Fee->length > 0) {
                for ($i=0; $i < $Fee->length; $i++) { 
                    $Fee_amount = $Fee->item($i)->getAttribute("amount");
                    $Fee_currency = $Fee->item($i)->getAttribute("currency");
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