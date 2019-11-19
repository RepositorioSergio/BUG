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
echo "COMECOU BOOKING LIST";
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

$url = 'https://sandbox-affiliateapisecure.agoda.com/xmlpartner/xmlbookservice/booklist_v2';

$siteid = 1831338;
$apikey = "b57a754c-5e06-4cdd-ac0d-2ea58c48ef74";
$fromdate = "2019-11-01";
$todate = "2019-11-02";


$raw = '<?xml version="1.0" encoding="utf-8"?>
<BookingListRequestV2 siteid="' . $siteid . '" apikey="' . $apikey . '" xmlns="http://xml.agoda.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<DateRange>
		<FromDate>' . $fromdate . '</FromDate>
		<ToDate>' . $todate . '</ToDate>
	</DateRange>
</BookingListRequestV2>';

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
$BookingListResponseV2 = $inputDoc->getElementsByTagName("BookingListResponseV2");
$status = $BookingListResponseV2->item(0)->getAttribute('status');

$Bookings = $BookingListResponseV2->item(0)->getElementsByTagName('Bookings');
if ($Bookings->length > 0) {
    $Booking = $Bookings->item(0)->getElementsByTagName('Booking');
    if ($Booking->length > 0) {
        for ($i=0; $i < $Booking->length; $i++) { 
            $id = $Booking->item($i)->getAttribute('id');
            $Bookingstatus = $Booking->item($i)->getAttribute('status');
            $selfservice = $Booking->item($i)->getAttribute('selfservice');
            $usdamount = $Booking->item($i)->getAttribute('usdamount');
            $departure = $Booking->item($i)->getAttribute('departure');
            $arrival = $Booking->item($i)->getAttribute('arrival');
            $lastmodified = $Booking->item($i)->getAttribute('lastmodified');
            $received = $Booking->item($i)->getAttribute('received');
            $cityname = $Booking->item($i)->getAttribute('cityname');
            $hotelname = $Booking->item($i)->getAttribute('hotelname');
            $hotelid = $Booking->item($i)->getAttribute('hotelid');
            $tag = $Booking->item($i)->getAttribute('tag');
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>