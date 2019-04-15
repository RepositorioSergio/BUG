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
echo "COMECOU PRICE";
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

$url = $didatravelserviceurl . "/api/rate/PriceConfirm?\$format=json";
echo $return;
echo $url;
echo $return;
$raw = '{
    "PreBook": true,
    "CheckOutDate": "2019-09-30",
    "CheckInDate": "2019-09-26",
    "NumOfRooms": 1,
    "HotelID": 5982,
    "Header": {
        "LicenseKey": "' . $didatravellicensekey . '",
        "ClientID": "' . $didatravelclientid . '"
    },
    "OccupancyDetails": [
        {
            "ChildCount": 0,
            "AdultCount": 2,
            "RoomNum": 1
        }
    ],
    "Currency": "CNY",
    "Nationality": "CN",
    "RatePlanID": "1:42$86067:DBL-E10:EY:BB:R"
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
echo "RESPONSE";
$response = $client->send();
if ($response->isSuccess()) {
    echo "ENTROU IF";
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
$PriceDetails = $Success['PriceDetails'];
$CheckOutDate = $PriceDetails['CheckOutDate'];
$CheckInDate = $PriceDetails['CheckInDate'];
$ReferenceNo = $PriceDetails['ReferenceNo'];
$HotelList = $PriceDetails['HotelList'];

$count = count($HotelList);
for ($i=0; $i < $count; $i++) { 
    $HotelID = $HotelList[$i]['HotelID'];
    $HotelName = $HotelList[$i]['HotelName'];
    $Destination = $HotelList[$i]['Destination'];
    $CityCode = $Destination['CityCode'];
    $TotalPrice = $HotelList[$i]['TotalPrice'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('priceconfirm');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'HotelID' => $HotelID,
            'HotelName' => $HotelName,
            'CityCode' => $CityCode,
            'TotalPrice' => $TotalPrice,
            'CheckOutDate' => $CheckOutDate,
            'CheckInDate' => $CheckInDate,
            'ReferenceNo' => $ReferenceNo
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

    $RatePlanList = $HotelList[$i]['RatePlanList'];
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
        $StandardOccupancy = $RatePlanList[$j]['StandardOccupancy'];
        $InventoryCount = $RatePlanList[$j]['InventoryCount'];
        $MaxOccupancy = $RatePlanList[$j]['MaxOccupancy'];
        $Currency = $RatePlanList[$j]['Currency'];
        $TotalPrice = $RatePlanList[$j]['TotalPrice'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('priceconfirm_rateplan');
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
                'StandardOccupancy' => $StandardOccupancy,
                'InventoryCount' => $InventoryCount,
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
                $insert->into('priceconfirm_price');
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

    $CancellationPolicyList = $HotelList[$i]['CancellationPolicyList'];
    for ($jAux2=0; $jAux2 < count($CancellationPolicyList); $jAux2++) { 
        $Amount = $CancellationPolicyList[$jAux2]['Amount'];
        $FromDate = $CancellationPolicyList[$jAux2]['FromDate'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('priceconfirm_cancelpolicies');
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
 
} 

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>