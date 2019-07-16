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
echo "COMECOU HOTELBOOK<br/>";
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

$user = 'wingstest';
$pass = 'Win@59491374';

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
    </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/Amendment</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:AmendmentRequest>
        <hot:Request Type="CheckStatus" PriceChange="InformationRequired" Remarks="checking status amendment request" />
        <hot:BookingId>1729</hot:BookingId>
        <hot:ConfirmationNo> F15XU8</hot:ConfirmationNo>
    </hot:AmendmentRequest>
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
$url =  "https://api.tbotechnology.in/HotelAPI_V7/HotelService.svc";

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
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$hotelFacil = '';
$Attr = '';

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$AmendmentResponse = $Body->item(0)->getElementsByTagName("AmendmentResponse");

$BookingId = $AmendmentResponse->item(0)->getElementsByTagName("BookingId");
if ($BookingId->length > 0) {
    $BookingId = $BookingId->item(0)->nodeValue;
} else {
    $BookingId = "";
}
$AmendmentMessage = $AmendmentResponse->item(0)->getElementsByTagName("AmendmentMessage");
if ($AmendmentMessage->length > 0) {
    $AmendmentMessage = $AmendmentMessage->item(0)->nodeValue;
} else {
    $AmendmentMessage = "";
}
$RequestStatus = $AmendmentResponse->item(0)->getElementsByTagName("RequestStatus");
if ($RequestStatus->length > 0) {
    $RequestStatus = $RequestStatus->item(0)->nodeValue;
} else {
    $RequestStatus = "";
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('amendment');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'BookingId' => $BookingId,
        'AmendmentMessage' => $AmendmentMessage,
        'RequestStatus' => $RequestStatus
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();

} catch (\Exception $e) {
    echo $return;
    echo "ERRO: " . $e;
    echo $return;
}

$ApprovalInformation = $AmendmentResponse->item(0)->getElementsByTagName("ApprovalInformation");
if ($ApprovalInformation->length > 0) {
    $TotalPrice = $ApprovalInformation->item(0)->getElementsByTagName("TotalPrice");
    if ($TotalPrice->length > 0) {
        $BeforeAmendmentPrice = $TotalPrice->item(0)->getAttribute("BeforeAmendmentPrice");
        $AfterAmendmentPrice = $TotalPrice->item(0)->getAttribute("AfterAmendmentPrice");
    } else {
        $BeforeAmendmentPrice = "";
        $AfterAmendmentPrice = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('approvalInformation_amendment');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'BeforeAmendmentPrice' => $BeforeAmendmentPrice,
            'AfterAmendmentPrice' => $AfterAmendmentPrice,
            'BookingId' => $BookingId
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO 2: " . $e;
        echo $return;
    }

    $Rooms = $ApprovalInformation->item(0)->getElementsByTagName("Rooms");
    if ($Rooms->length > 0) {
        $Room = $Rooms->item(0)->getElementsByTagName("Room");
        if ($Room->length > 0) {
            for ($i=0; $i < $Room->length; $i++) { 
                $RoomIndex = $Room->item(0)->getAttribute("RoomIndex");
                $RoomName = $Room->item(0)->getAttribute("RoomName");
                $BeforeAmendmentPrice = $Room->item(0)->getAttribute("BeforeAmendmentPrice");
                $AfterAmendmentPrice = $Room->item(0)->getAttribute("AfterAmendmentPrice");

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('rooms_amendment');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'RoomIndex' => $RoomIndex,
                        'RoomName' => $RoomName,
                        'BeforeAmendmentPrice' => $BeforeAmendmentPrice,
                        'AfterAmendmentPrice' => $AfterAmendmentPrice,
                        'BookingId' => $BookingId,
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 3: " . $e;
                    echo $return;
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