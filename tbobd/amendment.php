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
echo "COMECOU AMEND<br/>";
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
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing" > 
    <hot:Credentials UserName="clubonehotelsTest" Password="Clu@28527768"> </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/Amendment</wsa:Action> 
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header> 
<soap:Body>
    <hot:AmendmentRequest>
        <!-- if price change information required set PriceChange="InformationRequired" -->
        <hot:Request Type="OfflineAmendment" PriceChange="Approved" Remarks="guest name amendment requested"/>
        <hot:BookingId>0</hot:BookingId>
        <hot:AmendInformation>
        <hot:CheckIn Date="2020-10-03"/> 
        <hot:CheckOut Date="2020-10-07"/>
        <hot:Rooms>
            <hot:RoomReq Amend="FirstRoom">
                <hot:Guest Action="Rename" ExistingName="Mr Ajayge testgea" GuestType="Adult" Title="Mr." FirstName="TestAbd" LastName="TestSrivastav" Age="29"/>
            </hot:RoomReq> 
        </hot:Rooms>
        </hot:AmendInformation> 
        <hot:ConfirmationNo>V55QI4</hot:ConfirmationNo>
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

$AmendmentRequested = $AmendmentResponse->item(0)->getElementsByTagName("AmendmentRequested");
if ($AmendmentRequested->length > 0) {
    $CheckIn = $AmendmentRequested->item(0)->getElementsByTagName("CheckIn");
    if ($CheckIn->length > 0) {
        $DateCheckIn = $CheckIn->item(0)->getAttribute("Date");
        $DateActionCheckIn = $CheckIn->item(0)->getAttribute("DateAction");
    } else {
        $DateCheckIn = "";
        $DateActionCheckIn = "";
    }
    $CheckOut = $AmendmentRequested->item(0)->getElementsByTagName("CheckOut");
    if ($CheckOut->length > 0) {
        $DateCheckOut = $CheckOut->item(0)->getAttribute("Date");
        $DateActionCheckOut = $CheckOut->item(0)->getAttribute("DateAction");
    } else {
        $DateCheckOut = "";
        $DateActionCheckOut = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('amendmentRequested');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'DateCheckIn' => $DateCheckIn,
            'DateActionCheckIn' => $DateActionCheckIn,
            'DateCheckOut' => $DateCheckOut,
            'DateActionCheckOut' => $DateActionCheckOut
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

    $Rooms = $AmendmentRequested->item(0)->getElementsByTagName("Rooms");
    if ($Rooms->length > 0) {
        $RoomRes = $Rooms->item(0)->getElementsByTagName("RoomRes");
        if ($RoomRes->length > 0) {
            $Amended = $RoomRes->item(0)->getAttribute("Amended");
            $Guest = $RoomRes->item(0)->getElementsByTagName("Guest");
            if ($Guest->length > 0) {
                for ($i=0; $i < $Guest->length; $i++) { 
                    $Title = $Guest->item($i)->getAttribute("Title");
                    $FirstName = $Guest->item($i)->getAttribute("FirstName");
                    $LastName = $Guest->item($i)->getAttribute("LastName");
                    $Age = $Guest->item($i)->getAttribute("Age");
                    $GuestType = $Guest->item($i)->getAttribute("GuestType");
                    $Action = $Guest->item($i)->getAttribute("Action");

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('guest_amendment');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Title' => $Title,
                            'FirstName' => $FirstName,
                            'LastName' => $LastName,
                            'Age' => $Age,
                            'GuestType' => $GuestType,
                            'Action' => $Action
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
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>