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
echo "COMECOU ACTIVE LOCATIONS<br/>";
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
    "Content-type: text/xml",
    "Accept: text/xml",
    "Content-length: ".strlen($raw)
));

$url = "https://xml.rentalcars.com/service/ServiceRequest.do";

$raw = '<ActiveLocationsRQ version="1.1" preflang="en"> 
    <Credentials username="club1hot944" password="club1hot944"/>
</ActiveLocationsRQ>';

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
$ActiveLocationsRS = $inputDoc->getElementsByTagName("ActiveLocationsRS");
$ActiveLocation = $ActiveLocationsRS->item(0)->getElementsByTagName("ActiveLocation");
if ($ActiveLocation->length > 0) {
    for ($i=0; $i < $ActiveLocation->length; $i++) { 
        $Country = $ActiveLocation->item($i)->getElementsByTagName("Country");
        if ($Country->length > 0) {
            $Countryid = $Country->item(0)->getAttribute("id");
            $Countryname = $Country->item(0)->getAttribute("name");
            $City = $Country->item(0)->getElementsByTagName("City");
            if ($City->length > 0) {
                $Cityid = $City->item(0)->getAttribute("id");
                $Cityname = $City->item(0)->getAttribute("name");
                $local = "";
                $Location = $City->item(0)->getElementsByTagName("Location");
                if ($Location->length > 0) {
                    $Locationid = $Location->item(0)->getAttribute("id");
                    $type = $Location->item(0)->getAttribute("type");
                    $iata = $Location->item(0)->getAttribute("iata");
                    $longitude = $Location->item(0)->getAttribute("longitude");
                    $latitude = $Location->item(0)->getAttribute("latitude");
                    $local = $Location->item(0)->nodeValue;
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