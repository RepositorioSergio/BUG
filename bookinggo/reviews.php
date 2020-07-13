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
echo "COMECOU REVIEWS<br/>";
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

$raw = '<ReviewsRQ version="1.1" preflang="en">
<Credentials username="club1hot944" password="club1hot944" remoteIp="127.0.0.0"/> 
<Location id="3806"/>
</ReviewsRQ>';

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
$ReviewsRS = $inputDoc->getElementsByTagName("ReviewsRS");
$Reviews = $ReviewsRS->item(0)->getElementsByTagName("Reviews");
if ($Reviews->length > 0) {
    $Review = $Reviews->item(0)->getElementsByTagName("Review");
    if ($Review->length > 0) {
        $Name = $Review->item(0)->getElementsByTagName("Name");
        if ($Name->length > 0) {
            $Name = $Name->item(0)->nodeValue;
        } else {
            $Name = "";
        }
        $Type = $Review->item(0)->getElementsByTagName("Type");
        if ($Type->length > 0) {
            $Type = $Type->item(0)->nodeValue;
        } else {
            $Type = "";
        }
        $Rating = $Review->item(0)->getElementsByTagName("Rating");
        if ($Rating->length > 0) {
            $Rating = $Rating->item(0)->nodeValue;
        } else {
            $Rating = "";
        }
        $Negative = $Review->item(0)->getElementsByTagName("Negative");
        if ($Negative->length > 0) {
            $Negative = $Negative->item(0)->nodeValue;
        } else {
            $Negative = "";
        }
        $Positive = $Review->item(0)->getElementsByTagName("Positive");
        if ($Positive->length > 0) {
            $Positive = $Positive->item(0)->nodeValue;
        } else {
            $Positive = "";
        }
        $Date = $Review->item(0)->getElementsByTagName("Date");
        if ($Date->length > 0) {
            $Date = $Date->item(0)->nodeValue;
        } else {
            $Date = "";
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>