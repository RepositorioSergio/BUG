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
echo "COMECOU BOOKING DETAILS<br/>";
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
$sql = "select value from settings where name='enableabreupackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_abreu = $affiliate_id;
} else {
    $affiliate_id_abreu = 0;
}
echo "<br/> affiliate_id_abreu " . $affiliate_id_abreu;
$sql = "select value from settings where name='abreupackagesuser' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagesuser = $row_settings['value'];
}
echo "<br/> abreupackagesuser " . $abreupackagesuser;
$sql = "select value from settings where name='abreupackagespassword' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagespassword = base64_decode($row_settings['value']);
}
echo "<br/> abreupackagespassword " . $abreupackagespassword;
$sql = "select value from settings where name='abreupackagesserviceURL' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagesserviceURL = $row_settings['value'];
}
echo "<br/> abreupackagesserviceURL " . $abreupackagesserviceURL;
$db->getDriver()
    ->getConnection()
    ->disconnect();
    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT bookingReferenceNumber FROM booking";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}


$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $result = $result->current();
    $bookingReferenceNumber = $result['bookingReferenceNumber'];
}
echo $return;
echo $bookingReferenceNumber;
echo $return;

$raw = '{   "username": "' . $abreupackagesuser . '",   "password": "' . $abreupackagespassword . '",   "language": "ES",   "bookingReferenceNumber": "' . $bookingReferenceNumber . '" }';

$client = new Client();
$client->setOptions(array(
'timeout' => 100,
'sslverifypeer' => false,
'sslverifyhost' => false
));
$client->setHeaders(array(
'Accept-Encoding' => 'gzip,deflate',
'X-Powered-By' => 'Zend Framework',
'Content-Length' => strlen($raw),
'Content-Type' => 'application/json;charset=utf-8'
));
$client->setUri($abreupackagesserviceURL . 'Booking/Details');
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
//die();
}
$response = iconv('UTF-8', 'ASCII//TRANSLIT', $response);
echo $return;
echo $response;
echo $return;
$response = json_decode($response, true);
if ($response === false || $response === null) {
    echo $return;
    echo "NOT DECODE";
    echo $return;
}


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
'driver' => $config->db->driver,
'database' => $config->db->database,
'username' => $config->db->username,
'password' => $config->db->password,
'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$amount = $response['amount'];
$bookingStatus = $response['bookingStatus'];
$packageName = $response['packageName'];
if ($packageName != "") {
    $agentBookingReference = $response['agentBookingReference'];
$leadPassengerName = $response['leadPassengerName'];
$bookingStartDate = $response['bookingStartDate'];
$bookingEndDate = $response['bookingEndDate'];
echo $return;
echo $bookingEndDate;
echo $return;

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('bookingdetails');
    $insert->values(array(
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'amount' => $amount,
    'bookingStatus' => $bookingStatus,
    'packageName' => $packageName,
    'agentBookingReference' => $agentBookingReference,
    'leadPassengerName' => $leadPassengerName,
    'bookingStartDate' => $bookingStartDate,
    'bookingEndDate' => $bookingEndDate
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "ERRO BOOK: " . $e;
    echo $return;
}

//bookedRooms
$bookedRooms = $response['bookedRooms'];
for ($i=0; $i < count($bookedRooms); $i++) { 
    $bookedRoomNumber = $bookedRooms[$i]['bookedRoomNumber'];
    $bookingCode = $bookedRooms[$i]['bookingCode'];
    $elementName = $bookedRooms[$i]['elementName'];
    $numberOfAdults = $bookedRooms[$i]['numberOfAdults'];
    $numberOfChildren = $bookedRooms[$i]['numberOfChildren'];
    $elementTotalPrice = $bookedRooms[$i]['elementTotalPrice'];
    $elementPricePerAdult = $bookedRooms[$i]['elementPricePerAdult'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('details_bookedRooms');
        $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'bookedRoomNumber' => $bookedRoomNumber,
        'bookingCode' => $bookingCode,
        'elementName' => $elementName,
        'numberOfAdults' => $numberOfAdults,
        'numberOfChildren' => $numberOfChildren,
        'elementTotalPrice' => $elementTotalPrice,
        'elementPricePerAdult' => $elementPricePerAdult
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO BOOKROOMS: " . $e;
        echo $return;
    }

    //elementPricePerChildren
    $elementPricePerChildren = $bookedRooms[$i]['elementPricePerChildren'];
    for ($iAux=0; $iAux < count($elementPricePerChildren); $iAux++) { 
        $childrenAge = $elementPricePerChildren[$iAux]['childrenAge'];
        $pricePerChildren = $elementPricePerChildren[$iAux]['pricePerChildren'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('details_elementPricePerChildren');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'childrenAge' => $childrenAge,
                'pricePerChildren' => $pricePerChildren
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO ELEM: " . $e;
            echo $return;
        }
    }

    //bookedPassengers
    $bookedPassengers = $bookedRooms[$i]['bookedPassengers'];
    for ($iAux2=0; $iAux2 < count($bookedPassengers); $iAux2++) { 
        $passengerId = $bookedPassengers[$iAux2]['passengerId'];
        $title = $bookedPassengers[$iAux2]['title'];
        $firstName = $bookedPassengers[$iAux2]['firstName'];
        $lastName = $bookedPassengers[$iAux2]['lastName'];
        $passengerType = $bookedPassengers[$iAux2]['passengerType'];
        $leadPassenger = $bookedPassengers[$iAux2]['leadPassenger'];
        echo $return;
        echo $leadPassenger;
        echo $return;
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('details_bookedPassengers');
            $insert->values(array(
                'passengerId' => $passengerId,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'title' => $title,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'passengerType' => $passengerType,
                'leadPassenger' => $leadPassenger
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO BP: " . $e;
            echo $return;
        }
    }
}


//bookedServices
$bookedServices = $response['bookedServices'];
for ($j=0; $j < count($bookedServices); $j++) { 
    $serviceName = $bookedServices[$j]['serviceName'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('details_bookedServices');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'serviceName' => $serviceName
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO BOOKROOMS: " . $e;
        echo $return;
    }

    $bookedOptions = $bookedServices[$j]['bookedOptions'];
    for ($jAux=0; $jAux < count($bookedOptions); $jAux++) { 
        $quantity = $bookedOptions[$jAux]['quantity'];
        $nAdults = $bookedOptions[$jAux]['nAdults'];
        $nChildren = $bookedOptions[$jAux]['nChildren'];
        $fromDate = $bookedOptions[$jAux]['fromDate'];
        $toDate = $bookedOptions[$jAux]['toDate'];
        $amount = $bookedOptions[$jAux]['amount'];
        $currencyCode = $bookedOptions[$jAux]['currencyCode'];
        $status = $bookedOptions[$jAux]['status'];
        $optionName = $bookedOptions[$jAux]['optionName'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('details_bookedOptions');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'quantity' => $quantity,
                'nAdults' => $nAdults,
                'nChildren' => $nChildren,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'amount' => $amount,
                'currencyCode' => $currencyCode,
                'status' => $status,
                'optionName' => $optionName
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO BOOKROOMS: " . $e;
            echo $return;
        }
    }
}

//cancellationCharges
$cancellationCharges = $response['cancellationCharges'];
for ($k=0; $k < count($cancellationCharges); $k++) { 
    $cancellationCharges2 = $cancellationCharges[$k]['cancellationCharges'];
    $cancellationChargesCurrency = $cancellationCharges[$k]['cancellationChargesCurrency'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('details_cancellationCharges');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'cancellationCharges' => $cancellationCharges2,
            'cancellationChargesCurrency' => $cancellationChargesCurrency
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO CANCEL: " . $e;
        echo $return;
    }
}
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>