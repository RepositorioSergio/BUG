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
echo "COMECOU BOOK";
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

$url = 'https://sandbox-affiliateapisecure.agoda.com/xmlpartner/xmlbookservice/book_v3';

$siteid = 1831338;
$apikey = "b57a754c-5e06-4cdd-ac0d-2ea58c48ef74";
$searchid = 604183064886580000;
$tag = "00000000-0000-0000-0000-000000000000";
$checkin = "2020-01-25";
$checkout = "2020-01-27";
$hotelID = 12133;
$roomId = 3094730;
$promotionid = 164998561;
$name = "Studio";
$lineitemid = 1;
$rateplan = "B2B";
$ratetype = "B2B";
$currency = "USD";
$model = "Merchant";
$ratecategoryid = 377586;
$blockid = "548d30b1-7a9d-d62e-1efe-32d229c618c9";
$count = 1;
$adults = 2;
$children = 0;
$rateExclusive = 53.20;
$rateInclusive = 62.62;
$rateTax = 4.63;
$rateFees = 4.79;

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<BookingRequestV3 siteid="' . $siteid . '" apikey="' . $apikey . '" delaybooking="true" 
    xmlns="http://xml.agoda.com" 
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://xml.agoda.com BookingRequestV3.xsd">
    <BookingDetails searchid="' . $searchid . '" tag="00000000-0000-0000-0000-000000000000" AllowDuplication="true" CheckIn="' . $checkin . '" CheckOut="' . $checkout . '" UserCountry="TH">
        <Hotel id="' . $hotelID . '">
            <Rooms>
                <Room id="' . $roomId . '" promotionid="' . $promotionid . '" name="' . $name . '" lineitemid="' . $lineitemid . '" rateplan="' . $rateplan . '" ratetype="' . $ratetype . '" currency="USD" model="' . $model . '" ratecategoryid="' . $ratecategoryid . '" blockid="' . $blockid . '" count="' . $count . '" adults="' . $adults . '" children="' . $children . '">
                    <Rate exclusive="' . $rateExclusive . '" tax="' . $rateTax . '" fees="' . $rateFees . '" inclusive="' . $rateInclusive . '"/>
                    <Surcharges>
                        <Surcharge id="0">
                            <Rate exclusive="0" tax="0" fees="0" inclusive="0" />
                        </Surcharge>
                    </Surcharges>
                    <GuestDetails>
                        <GuestDetail Primary="true">
                            <Title>Mr.</Title>
                            <FirstName>John</FirstName>
                            <LastName>Doe</LastName>
                            <CountryOfPassport>GB</CountryOfPassport>
                            <Gender>Male</Gender>
                            <Age>30</Age>
                        </GuestDetail>
                        <GuestDetail Primary="false">
                            <Title>Mrs.</Title>
                            <FirstName>Alexa</FirstName>
                            <LastName>Doe</LastName>
                            <CountryOfPassport>GB</CountryOfPassport>
                            <Gender>Female</Gender>
                            <Age>32</Age>
                        </GuestDetail>
                    </GuestDetails>
                    <SpecialRequest>high floor please</SpecialRequest><!--optional-->
                </Room>
            </Rooms>
        </Hotel>
    </BookingDetails>
    <CustomerDetail>
        <Language>en-us</Language>
        <Title>Mr.</Title>
        <FirstName>John</FirstName>
        <LastName>Doe</LastName>
        <Email>johndoe@mail.com</Email>
        <Phone>
            <CountryCode>^66</CountryCode>
            <AreaCode>^2</AreaCode>
            <Number>6629231024</Number>
        </Phone>
        <Newsletter>true</Newsletter>
        <IpAddress>127.0.0.1</IpAddress>
    </CustomerDetail>
    <PaymentDetails>
        <CreditCardInfo>
            <Cardtype>Visa</Cardtype>
            <Number>1234567891012345</Number>
            <ExpiryDate>092022</ExpiryDate>
            <Cvc>123</Cvc>
            <HolderName>John Doe</HolderName>
            <CountryOfIssue>TH</CountryOfIssue>
            <IssuingBank>Siam Commercial Bank</IssuingBank>
        </CreditCardInfo>
    </PaymentDetails>
</BookingRequestV3>';

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
$BookingResponseV3 = $inputDoc->getElementsByTagName("BookingResponseV3");
$status = $BookingResponseV3->item(0)->getAttribute('status');

$BookingDetails = $BookingResponseV3->item(0)->getElementsByTagName('BookingDetails');
if ($BookingDetails->length > 0) {
    $Booking = $BookingDetails->item(0)->getElementsByTagName('Booking');
    if ($Booking->length > 0) {
        $id = $Booking->item(0)->getAttribute('id');
        $selfservice = $Booking->item(0)->getAttribute('selfservice');
        $ItineraryID = $Booking->item(0)->getAttribute('ItineraryID');
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>