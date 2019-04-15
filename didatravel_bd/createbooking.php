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
echo "COMECOU CREATE BOOK";
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
$sql = "select value from settings where name='enabledidatravel' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_didatravel = $affiliate_id;
} else {
    $affiliate_id_didatravel = 0;
}
echo "<br/> affiliate_id_didatravel " . $affiliate_id_didatravel;
$sql = "select value from settings where name='didatravelclientid' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravelclientid = $row_settings['value'];
}
echo "<br/> didatravelclientid " . $didatravelclientid;
$sql = "select value from settings where name='didatravellicensekey' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravellicensekey = $row_settings['value'];
}
echo "<br/> didatravellicensekey " . $didatravellicensekey;
$sql = "select value from settings where name='didatravelserviceurl' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $didatravelserviceurl = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = $didatravelserviceurl . "/api/booking/HotelBookingConfirm?\$format=json";
echo $return;
echo $url;
echo $return;
$raw = '{
    "CheckOutDate": "2019-09-30",
    "CheckInDate": "2019-09-26",
    "NumOfRooms": 1,
    "Header": {
        "LicenseKey": "' . $didatravellicensekey . '",
        "ClientID": "' . $didatravelclientid . '"
    },
    "GuestList": [
        {
            "RoomNum": 1,
            "GuestInfo": [
                {
                    "IsAdult": true,
                    "Name": {
                        "Last": "Joao",
                        "First": "Alves"
                    }
                }
            ]
        }
    ],
    "Contact": {
        "Name": {
            "Last": "Joao",
            "First": "Alves"
        },
        "Email": "joaoalves@gmail.com",
        "Phone": "15812345678"
    },
    "ClientReference": "",
    "ReferenceNo": "DHB190412221512822"
}';
echo $return;
echo $raw;
echo $return;
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
    'Content-Type' => 'application/x-www-form-urlencoded'
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
echo $return;
echo $response;
echo $return;
$response = json_decode($response, true);
echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$Success = $response['Success'];
$BookingDetails = $Success['BookingDetails'];
$TotalPrice = $BookingDetails['TotalPrice'];
$OrderDate = $BookingDetails['OrderDate'];
$CheckOutDate = $BookingDetails['CheckOutDate'];
$CheckInDate = $BookingDetails['CheckInDate'];
$NumOfRooms = $BookingDetails['NumOfRooms'];
$Status = $BookingDetails['Status'];
$ClientReference = $BookingDetails['ClientReference'];
$CustomerRequest = $BookingDetails['CustomerRequest'];
$BookingID = $BookingDetails['BookingID'];

$Contact = $BookingDetails['Contact'];
$Name = $Contact['Name'];
$Last = $Name['Last'];
$First = $Name['First'];
$Email = $Contact['Email'];
$Phone = $Contact['Phone'];


try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('booking');
    $insert->values(array(
        'BookingID' => $BookingID,
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'TotalPrice' => $TotalPrice,
        'OrderDate' => $OrderDate,
        'CheckOutDate' => $CheckOutDate,
        'CheckInDate' => $CheckInDate,
        'NumOfRooms' => $NumOfRooms,
        'Status' => $Status,
        'ClientReference' => $ClientReference,
        'CustomerRequest' => $CustomerRequest,
        'Last' => $Last,
        'First' => $First,
        'Email' => $Email,
        'Phone' => $Phone
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "ERRO S: " . $e;
    echo $return;
}

$GuestList = $BookingDetails['GuestList'];
$count = count($GuestList);
for ($i=0; $i < $count; $i++) { 
    $RoomNum = $GuestList[$i]['RoomNum'];

    $GuestInfo = $GuestList[$i]['GuestInfo'];

    for ($iAux=0; $iAux < count($GuestInfo); $iAux++) { 
        $Age = $GuestInfo[$iAux]['Age'];
        $IsAdult = $GuestInfo[$iAux]['IsAdult'];
        $Name = $GuestInfo[$iAux]['Name'];
        $Last = $Name['Last'];
        $First = $Name['First'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('booking_GuestInfo');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'RoomNum' => $RoomNum,
                'Age' => $Age,
                'IsAdult' => $IsAdult,
                'Last' => $Last,
                'First' => $First,
                'BookingID' => $BookingID
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO S: " . $e;
            echo $return;
        }
    }
}

$Hotel = $BookingDetails['Hotel'];
$HotelID = $Hotel['HotelID'];
$HotelName = $Hotel['HotelName'];
$Destination = $Hotel['Destination'];
$CityCode = $Destination['CityCode'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('booking_hotel');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'HotelID' => $HotelID,
        'HotelName' => $HotelName,
        'CityCode' => $CityCode
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "ERRO S: " . $e;
    echo $return;
}

$RatePlanList = $Hotel['RatePlanList'];
for ($j=0; $j < count($RatePlanList); $j++) { 
    $RatePlanID = $RatePlanList[$j]['RatePlanID'];
    $RatePlanName = $RatePlanList[$j]['RatePlanName'];
    $RoomStatus = $RatePlanList[$j]['RoomStatus'];
    $BreakfastType = $RatePlanList[$j]['BreakfastType'];
    $BedType = $RatePlanList[$j]['BedType'];
    $RoomOccupancy = $RatePlanList[$j]['RoomOccupancy'];
    $ChildCount = $RoomOccupancy['ChildCount'];
    $AdultCount = $RoomOccupancy['AdultCount'];
    $RoomNum = $RoomOccupancy['RoomNum'];
    $MaxOccupancy = $RatePlanList[$j]['MaxOccupancy'];
    $Currency = $RatePlanList[$j]['Currency'];
    $TotalPrice = $RatePlanList[$j]['TotalPrice'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('booking_rateplan');
        $insert->values(array(
            'RatePlanID' => $RatePlanID,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'RatePlanName' => $RatePlanName,
            'RoomStatus' => $RoomStatus,
            'BreakfastType' => $BreakfastType,
            'BedType' => $BedType,
            'ChildCount' => $ChildCount,
            'AdultCount' => $AdultCount,
            'RoomNum' => $RoomNum,
            'MaxOccupancy' => $MaxOccupancy,
            'Currency' => $Currency,
            'TotalPrice' => $TotalPrice
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO RATE: " . $e;
        echo $return;
    }
    
    $PriceList = $RatePlanList[$j]['PriceList'];
    for ($jAux=0; $jAux < count($PriceList); $jAux++) { 
        $StayDate = $PriceList[$jAux]['StayDate'];
        $Price = $PriceList[$jAux]['Price'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('booking_price');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'StayDate' => $StayDate,
                'Price' => $Price,
                'RatePlanID' => $RatePlanID
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO PR: " . $e;
            echo $return;
        }
    }
}

$CancellationPolicyList = $Hotel['CancellationPolicyList'];
for ($jAux2=0; $jAux2 < count($CancellationPolicyList); $jAux2++) { 
    $Amount = $CancellationPolicyList[$jAux2]['Amount'];
    $FromDate = $CancellationPolicyList[$jAux2]['FromDate'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('booking_cancelpolicies');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'Amount' => $Amount,
            'FromDate' => $FromDate,
            'HotelID' => $HotelID
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO PL: " . $e;
        echo $return;
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>