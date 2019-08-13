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
echo "COMECOU SEARCH RESERVATION<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$nrooms = 2;
$n = 5;

$user = 'wingstest';
$pass = 'Win@59491374';
$url = "https://api.tbotechnology.in/HotelAPI_V7/HotelService.svc";

$SessionId = '053f4524-2118-496a-802c-5027972549b7';
$ResultIndex = 1;
$hotelCode = 1198032;
$hotelName = 'Versailles';
$RoomTypeName = 'Standard';
$RatePlanCode = '2595124||35397c70-9f63-5cad-6054-d16f4bcaa2c9';
$RoomTypeCode = '3039749|35397c70-9f63-5cad-6054-d16f4bcaa2c9|1^^1^^2595124||35397c70-9f63-5cad-6054-d16f4bcaa2c9';
$RatePlanCode2 = '2595124||35397c70-9f63-5cad-6054-d16f4bcaa2c9';
$RoomTypeCode2 = '3039749|35397c70-9f63-5cad-6054-d16f4bcaa2c9|2^^2^^2595124||35397c70-9f63-5cad-6054-d16f4bcaa2c9';

// CANCELLATION POLICIES

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
    </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/HotelCancellationPolicy</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelCancellationPolicyRequest>
        <hot:ResultIndex>' . $ResultIndex . '</hot:ResultIndex>
        <hot:SessionId>' . $SessionId . '</hot:SessionId>
        <hot:OptionsForBooking>
            <hot:FixedFormat>false</hot:FixedFormat>
            <hot:RoomCombination>
                <hot:RoomIndex>1</hot:RoomIndex>
                <hot:RoomIndex>2</hot:RoomIndex>
            </hot:RoomCombination>
        </hot:OptionsForBooking>
    </hot:HotelCancellationPolicyRequest>
</soap:Body>
</soap:Envelope>';

$headers = array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$xmlresponse = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
// if ($response === false) {
// error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
// } else {
// error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
// }
echo '<xmp>';
var_dump($xmlresponse);
echo '</xmp>';

// AVAILABLE PRICING
$raw3 = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
<hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
</hot:Credentials>
<wsa:Action>http://TekTravel/HotelBookingApi/AvailabilityAndPricing</wsa:Action>
<wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
<hot:AvailabilityAndPricingRequest>
    <hot:ResultIndex>' . $ResultIndex . '</hot:ResultIndex>
    <hot:HotelCode></hot:HotelCode>
    <hot:SessionId>' . $SessionId . '</hot:SessionId>
    <hot:OptionsForBooking>
        <hot:FixedFormat>false</hot:FixedFormat>
        <hot:RoomCombination>
            <hot:RoomIndex>1</hot:RoomIndex>
            <hot:RoomIndex>2</hot:RoomIndex>
        </hot:RoomCombination>
    </hot:OptionsForBooking>
</hot:AvailabilityAndPricingRequest>
</soap:Body>
</soap:Envelope>';

$headers3 = array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Content-length: " . strlen($raw3)
);

$ch3 = curl_init();
curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch3, CURLOPT_URL, $url);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch3, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch3, CURLOPT_POST, true);
curl_setopt($ch3, CURLOPT_POSTFIELDS, $raw3);
curl_setopt($ch3, CURLOPT_HTTPHEADER, $headers3);
$xmlresponse3 = curl_exec($ch3);
$error = curl_error($ch3);
$headers = curl_getinfo($ch3);

echo '<xmp>';
var_dump($xmlresponse3);
echo '</xmp>';


$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
<hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
</hot:Credentials>
<wsa:Action>http://TekTravel/HotelBookingApi/HotelBook</wsa:Action>
<wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelBookRequest>
        <hot:ClientReferenceNumber>210314135855789#gapa</hot:ClientReferenceNumber>
        <hot:GuestNationality>AE</hot:GuestNationality>
        <hot:Guests>
            <hot:Guest LeadGuest="true" GuestType="Adult" GuestInRoom="1">
                <hot:Title>Mr</hot:Title>
                <hot:FirstName>Ajay</hot:FirstName>
                <hot:LastName>testgea</hot:LastName>
                <hot:Age>20</hot:Age>
            </hot:Guest>
            <hot:Guest LeadGuest="false" GuestType="Child" GuestInRoom="1">
                <hot:Title>Mr</hot:Title>
                <hot:FirstName>Mahi</hot:FirstName>
                <hot:LastName>test</hot:LastName>
                <hot:Age>3</hot:Age>
            </hot:Guest>
            <hot:Guest LeadGuest="false" GuestType="Child" GuestInRoom="1">
                <hot:Title>Mr</hot:Title>
                <hot:FirstName>Mary</hot:FirstName>
                <hot:LastName>test</hot:LastName>
                <hot:Age>5</hot:Age>
            </hot:Guest>
            <hot:Guest LeadGuest="false" GuestType="Adult" GuestInRoom="2">
                <hot:Title>Mr</hot:Title>
                <hot:FirstName>Maya</hot:FirstName>
                <hot:LastName>testgea</hot:LastName>
                <hot:Age>22</hot:Age>
            </hot:Guest>
            <hot:Guest LeadGuest="false" GuestType="Adult" GuestInRoom="2">
                <hot:Title>Mr</hot:Title>
                <hot:FirstName>Andreas</hot:FirstName>
                <hot:LastName>testgea</hot:LastName>
                <hot:Age>32</hot:Age>
            </hot:Guest>
        </hot:Guests>
        <hot:AddressInfo>
            <hot:AddressLine1>testadd1</hot:AddressLine1>
            <hot:AddressLine2>testadd2</hot:AddressLine2>
            <hot:CountryCode>91</hot:CountryCode>
            <hot:AreaCode>11</hot:AreaCode>
            <hot:PhoneNo>25869696</hot:PhoneNo>
            <hot:Email>abc@gmail.com</hot:Email>
            <hot:City>Delhi</hot:City>
            <hot:State>Delhi</hot:State>
            <hot:Country>India</hot:Country>
            <hot:ZipCode>256525</hot:ZipCode>
        </hot:AddressInfo>
        <!-- VoucherBooking-true Booking will be Vouchered -->
        <hot:PaymentInfo VoucherBooking="true" PaymentModeType="Limit">
        </hot:PaymentInfo>
        <hot:SessionId>' . $SessionId . '</hot:SessionId>
        <hot:NoOfRooms>2</hot:NoOfRooms>
        <hot:ResultIndex>' . $ResultIndex . '</hot:ResultIndex>
        <hot:HotelCode>' . $hotelCode . '</hot:HotelCode>
        <hot:HotelName>' . $hotelName . '</hot:HotelName>
        <hot:HotelRooms>
            <hot:HotelRoom>
                <hot:RoomIndex>1</hot:RoomIndex>
                <hot:RoomTypeName>' . $RoomTypeName . '</hot:RoomTypeName>
                <hot:RoomTypeCode>' . $RoomTypeCode . '</hot:RoomTypeCode>
                <hot:RatePlanCode>' . $RatePlanCode . '</hot:RatePlanCode>
                <hot:RoomRate RoomFare="48.88" Currency="USD" AgentMarkUp="0.00" RoomTax="11.00" TotalFare="59.88"/>
                <hot:Supplements>
                    <hot:SuppInfo SuppID="1" SuppChargeType="AtProperty" Price="16.32" SuppIsSelected="false"/>
                </hot:Supplements>
            </hot:HotelRoom>
            <hot:HotelRoom>
                <hot:RoomIndex>2</hot:RoomIndex>
                <hot:RoomTypeName>' . $RoomTypeName . '</hot:RoomTypeName>
                <hot:RoomTypeCode>' . $RoomTypeCode2 . '</hot:RoomTypeCode>
                <hot:RatePlanCode>' . $RatePlanCode2 . '</hot:RatePlanCode>
                <hot:RoomRate RoomFare="48.88" Currency="USD" AgentMarkUp="0.00" RoomTax="11.00" TotalFare="59.88"/>
                <hot:Supplements>
                    <hot:SuppInfo SuppID="1" SuppChargeType="AtProperty" Price="16.32" SuppIsSelected="false"/>
                </hot:Supplements>
            </hot:HotelRoom>
        </hot:HotelRooms>
    </hot:HotelBookRequest>
</soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Content-length: ".strlen($raw)
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

echo "<br/>RESPONSE 4";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>