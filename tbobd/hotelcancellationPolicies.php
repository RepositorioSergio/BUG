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
echo "COMECOU CANCELPOLICIES<br/>";
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
    <wsa:Action>http://TekTravel/HotelBookingApi/HotelCancellationPolicy</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelCancellationPolicyRequest>
        <hot:ResultIndex>4</hot:ResultIndex>
        <hot:SessionId>9616db93-3fdf-40e4-9118-63441e133ccd</hot:SessionId>
        <hot:OptionsForBooking>
            <hot:FixedFormat>true</hot:FixedFormat>
            <hot:RoomCombination>
                <hot:RoomIndex>1</hot:RoomIndex>
                <hot:RoomIndex>2</hot:RoomIndex>
            </hot:RoomCombination>
        </hot:OptionsForBooking>
    </hot:HotelCancellationPolicyRequest>
</soap:Body>
</soap:Envelope>';

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
$HotelCancellationPolicyResponse = $Body->item(0)->getElementsByTagName("HotelCancellationPolicyResponse");

$CancelPolicies = $HotelCancellationPolicyResponse->item(0)->getElementsByTagName("CancelPolicies");
if ($CancelPolicies->length > 0) {
    $LastCancellationDeadline = $CancelPolicies->item(0)->getElementsByTagName("LastCancellationDeadline");
    if ($LastCancellationDeadline->length > 0) {
        $LastCancellationDeadline = $LastCancellationDeadline->item(0)->nodeValue;
    } else {
        $LastCancellationDeadline = "";
    }
    $DefaultPolicy = $CancelPolicies->item(0)->getElementsByTagName("DefaultPolicy");
    if ($DefaultPolicy->length > 0) {
        $DefaultPolicy = $DefaultPolicy->item(0)->nodeValue;
    } else {
        $DefaultPolicy = "";
    }
    $AutoCancellationText = $CancelPolicies->item(0)->getElementsByTagName("AutoCancellationText");
    if ($AutoCancellationText->length > 0) {
        $AutoCancellationText = $DefaultPolicy->item(0)->nodeValue;
    } else {
        $AutoCancellationText = "";
    }

    $NoShowlPolicy = $CancelPolicies->item(0)->getElementsByTagName("NoShowlPolicy");
    if ($NoShowlPolicy->length > 0) {
        $CurrencyNoShowl = $NoShowlPolicy->item(0)->getAttribute("Currency");
        $CancellationChargeNoShowl = $NoShowlPolicy->item(0)->getAttribute("CancellationCharge");
        $ChargeTypeNoShowl = $NoShowlPolicy->item(0)->getAttribute("ChargeType");
        $ToDateNoShowl = $NoShowlPolicy->item(0)->getAttribute("ToDate");
        $FromDateNoShowl = $NoShowlPolicy->item(0)->getAttribute("FromDate");
    } else {
        $CurrencyNoShowl = "";
        $CancellationChargeNoShowl = "";
        $ChargeTypeNoShowl = "";
        $ToDateNoShowl = "";
        $FromDateNoShowl = "";
    }

    $NoShowPolicy = $CancelPolicies->item(0)->getElementsByTagName("NoShowPolicy");
    if ($NoShowPolicy->length > 0) {
        $CurrencyNoShow = $NoShowPolicy->item(0)->getAttribute("Currency");
        $CancellationChargeNoShow = $NoShowPolicy->item(0)->getAttribute("CancellationCharge");
        $ChargeTypeNoShow = $NoShowPolicy->item(0)->getAttribute("ChargeType");
        $ToDateNoShow = $NoShowPolicy->item(0)->getAttribute("ToDate");
        $FromDateNoShow = $NoShowPolicy->item(0)->getAttribute("FromDate");
    } else {
        $CurrencyNoShow = "";
        $CancellationChargeNoShow = "";
        $ChargeTypeNoShow = "";
        $ToDateNoShow = "";
        $FromDateNoShow = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('HotelCancellationPolicy');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'LastCancellationDeadline' => $LastCancellationDeadline,
            'DefaultPolicy' => $DefaultPolicy,
            'AutoCancellationText' => $AutoCancellationText,
            'CurrencyNoShowl' => $CurrencyNoShowl,
            'CancellationChargeNoShowl' => $CancellationChargeNoShowl,
            'ChargeTypeNoShowl' => $ChargeTypeNoShowl,
            'ToDateNoShowl' => $ToDateNoShowl,
            'FromDateNoShowl' => $FromDateNoShowl,
            'CurrencyNoShow' => $CurrencyNoShow,
            'CancellationChargeNoShow' => $CancellationChargeNoShow,
            'ChargeTypeNoShow' => $ChargeTypeNoShow,
            'ToDateNoShow' => $ToDateNoShow,
            'FromDateNoShow' => $FromDateNoShow
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();

} catch (\Exception $e) {
    echo $return;
    echo "ERRO 1: " . $e;
    echo $return;
}
    

$CancelPolicy = $CancelPolicies->item(0)->getElementsByTagName("CancelPolicy");
if ($CancelPolicy->length > 0) {
    for ($i=0; $i < $CancelPolicy->length; $i++) { 
        $Currency = $CancelPolicy->item($i)->getAttribute("Currency");
        $CancellationCharge = $CancelPolicy->item($i)->getAttribute("CancellationCharge");
        $ChargeType = $CancelPolicy->item($i)->getAttribute("ChargeType");
        $ToDate = $CancelPolicy->item($i)->getAttribute("ToDate");
        $FromDate = $CancelPolicy->item($i)->getAttribute("FromDate");
        $RoomIndex = "";
        $RoomTypeName = "";


        try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('CancelPolicies_HCP');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Currency' => $Currency,
                    'CancellationCharge' => $CancellationCharge,
                    'ChargeType' => $ChargeType,
                    'ToDate' => $ToDate,
                    'FromDate' => $FromDate,
                    'RoomIndex' => $RoomIndex,
                    'RoomTypeName' => $RoomTypeName
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

    }
}
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>