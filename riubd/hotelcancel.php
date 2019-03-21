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
echo "COMECOU CANCEL<br/>";
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
$sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_riu = $affiliate_id;
} else {
    $affiliate_id_riu = 0;
}
$sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $riuServiceURL = $row['value'];
}
echo $return;
echo $riuServiceURL;
echo $return;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<soap:Body>
    <HotelResCancel xmlns="http://services.enginexml.rumbonet.riu.com">
        <in0 xmlns="http://services.enginexml.rumbonet.riu.com">
            <CountryCode xmlns="http://dtos.enginexml.rumbonet.riu.com">PE</CountryCode>
            <CustomerReservationCode xmlns="http://dtos.enginexml.rumbonet.riu.com" xsi:nil="true" />
            <hotelReservationCode xmlns="http://dtos.enginexml.rumbonet.riu.com">RNT2XPQM</hotelReservationCode>
            <hotelReservationID xmlns="http://dtos.enginexml.rumbonet.riu.com">1</hotelReservationID>
            <Language xmlns="http://dtos.enginexml.rumbonet.riu.com">E</Language>
        </in0>
    </HotelResCancel>
</soap:Body>
</soap:Envelope>';


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));


$client->setUri($riuServiceURL);
$client->setMethod('POST');
$client->setCookies(array(
    'JSESSIONID' => '589DAE9DCDA61EBE7E3F439C8987F987'
));
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
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
$HotelResCancelResponse = $Body->item(0)->getElementsByTagName("HotelResCancelResponse");
$HotelResCancelRS = $HotelResCancelResponse->item(0)->getElementsByTagName("HotelResCancelRS");
if ($HotelResCancelRS->length > 0) {
    $adultsCount = $HotelResCancelRS->item(0)->getElementsByTagName("adultsCount");
    if ($adultsCount->length > 0) {
        $adultsCount = $adultsCount->item(0)->nodeValue;
    } else {
        $adultsCount = "";
    }
    $bookingAmount = $HotelResCancelRS->item(0)->getElementsByTagName("bookingAmount");
    if ($bookingAmount->length > 0) {
        $bookingAmount = $bookingAmount->item(0)->nodeValue;
    } else {
        $bookingAmount = "";
    }
    $bookingState = $HotelResCancelRS->item(0)->getElementsByTagName("bookingState");
    if ($bookingState->length > 0) {
        $bookingState = $bookingState->item(0)->nodeValue;
    } else {
        $bookingState = "";
    }
    $childCount = $HotelResCancelRS->item(0)->getElementsByTagName("childCount");
    if ($childCount->length > 0) {
        $childCount = $childCount->item(0)->nodeValue;
    } else {
        $childCount = "";
    }
    $creationDate = $HotelResCancelRS->item(0)->getElementsByTagName("creationDate");
    if ($creationDate->length > 0) {
        $creationDate = $creationDate->item(0)->nodeValue;
    } else {
        $creationDate = "";
    }
    $currencyCode = $HotelResCancelRS->item(0)->getElementsByTagName("currencyCode");
    if ($currencyCode->length > 0) {
        $currencyCode = $currencyCode->item(0)->nodeValue;
    } else {
        $currencyCode = "";
    }
    $CustomerReservationCode = $HotelResCancelRS->item(0)->getElementsByTagName("CustomerReservationCode");
    if ($CustomerReservationCode->length > 0) {
        $CustomerReservationCode = $CustomerReservationCode->item(0)->nodeValue;
    } else {
        $CustomerReservationCode = "";
    }
    $errors = $HotelResCancelRS->item(0)->getElementsByTagName("errors");
    if ($errors->length > 0) {
        $errors = $errors->item(0)->nodeValue;
    } else {
        $errors = "";
    }
    $hotelID = $HotelResCancelRS->item(0)->getElementsByTagName("hotelID");
    if ($hotelID->length > 0) {
        $hotelID = $hotelID->item(0)->nodeValue;
    } else {
        $hotelID = "";
    }
    $hotelReservationCode = $HotelResCancelRS->item(0)->getElementsByTagName("hotelReservationCode");
    if ($hotelReservationCode->length > 0) {
        $hotelReservationCode = $hotelReservationCode->item(0)->nodeValue;
    } else {
        $hotelReservationCode = "";
    }
    $hotelReservationID = $HotelResCancelRS->item(0)->getElementsByTagName("hotelReservationID");
    if ($hotelReservationID->length > 0) {
        $hotelReservationID = $hotelReservationID->item(0)->nodeValue;
    } else {
        $hotelReservationID = "";
    }
    $impPromocode = $HotelResCancelRS->item(0)->getElementsByTagName("impPromocode");
    if ($impPromocode->length > 0) {
        $impPromocode = $impPromocode->item(0)->nodeValue;
    } else {
        $impPromocode = "";
    }
    $infantsCount = $HotelResCancelRS->item(0)->getElementsByTagName("infantsCount");
    if ($infantsCount->length > 0) {
        $infantsCount = $infantsCount->item(0)->nodeValue;
    } else {
        $infantsCount = "";
    }
    $mealPlan = $HotelResCancelRS->item(0)->getElementsByTagName("mealPlan");
    if ($mealPlan->length > 0) {
        $mealPlan = $mealPlan->item(0)->nodeValue;
    } else {
        $mealPlan = "";
    }
    $numDays = $HotelResCancelRS->item(0)->getElementsByTagName("numDays");
    if ($numDays->length > 0) {
        $numDays = $numDays->item(0)->nodeValue;
    } else {
        $numDays = "";
    }
    $rateType = $HotelResCancelRS->item(0)->getElementsByTagName("rateType");
    if ($rateType->length > 0) {
        $rateType = $rateType->item(0)->nodeValue;
    } else {
        $rateType = "";
    }
    $roomsCount = $HotelResCancelRS->item(0)->getElementsByTagName("roomsCount");
    if ($roomsCount->length > 0) {
        $roomsCount = $roomsCount->item(0)->nodeValue;
    } else {
        $roomsCount = "";
    }
    $state = $HotelResCancelRS->item(0)->getElementsByTagName("state");
    if ($state->length > 0) {
        $state = $state->item(0)->nodeValue;
    } else {
        $state = "";
    }
    $stayDateEnd = $HotelResCancelRS->item(0)->getElementsByTagName("stayDateEnd");
    if ($stayDateEnd->length > 0) {
        $stayDateEnd = $stayDateEnd->item(0)->nodeValue;
    } else {
        $stayDateEnd = "";
    }
    $stayDateStart = $HotelResCancelRS->item(0)->getElementsByTagName("stayDateStart");
    if ($stayDateStart->length > 0) {
        $stayDateStart = $stayDateStart->item(0)->nodeValue;
    } else {
        $stayDateStart = "";
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('hotelcancel');
        $insert->values(array(
            'CustomerReservationCode' => $CustomerReservationCode,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'hotelID' => $hotelID,
            'bookingAmount' => $bookingAmount,
            'bookingState' => $bookingState,
            'creationDate' => $creationDate,
            'currencyCode' => $currencyCode,
            'currentAccount' => $currentAccount,
            'errors' => $errors,
            'hotelReservationCode' => $hotelReservationCode,
            'hotelReservationID' => $hotelReservationID,
            'mealPlan' => $mealPlan,
            'numDays' => $numDays,
            'rateType' => $rateType,
            'AdultsCount' => $AdultsCount,
            'ChildCount' => $ChildCount,
            'InfantsCount' => $InfantsCount,
            'roomsCount' => $roomsCount,
            'state' => $state,
            'stayDateEnd' => $stayDateEnd,
            'stayDateStart' => $stayDateStart
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO HOTEL: " . $e;
        echo $return;
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>