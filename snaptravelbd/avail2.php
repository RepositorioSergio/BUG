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
echo "COMECOU AVAIL2<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://b2b-api-staging.snaptravel.com/avail';

$raw = '{
    "hotelId": 119566,
    "sessionId": "k9O-nRmdIBbByBdd8%27mNr-0cIqYR8-B2DcX1I-Acr%3AbdncB%7DYIf%7DI-ycr%3AYR8gTd893NM%3A-c%3ALbj8yZ2DkvN%3A-yiyLbJncZjIIfMD%3AMjYrQ%7DN%7DB2LyvN1-ju%7D4-uQq7d1%7DNN8yJ1%3AyMMIZ1-M-O%7DI%7DMM131dDkju1vKMNABBY31RCLB%7DPBrNIc%7D2IvNi7-Zu1r8Nycyi%7DL1Mn%27Z2IY8MLcBByLviE%27U%7DPi%3ANr-ZcrvYMN-7j0gAdn%27AB%7DByU%7D%7DJ%7Dn%2771L%7DJ%7DY31MD%27U%7DrZzM%7Dcjj131dD%27Z%7D%3ALrNI-jj1q7i7%7Dj-yY%3ANy-Iuy4Y-D%27T%7DYAbMIcZBy4NiE%27U%7DPi%3ANr-ZcrvYMN-7j0gAdIAAB%7D%7DPJI4-iyZB1%3A1%3A-I3nMNBZ%7D%3ALCNIc%22j1311n%27UjI%3ArNI-yir%3A7i7%7DB%7Dr1QNy-pBYmQJDLB2D%27%7BNM%3AAiyLNiE%27U%7DPi%3ANr-ZcrvYMNd%3D",
    "arrivalDate": "06/28/2020",
    "departureDate": "06/30/2020",
    "room1": "2",
    "rateKey": "",
    "locale": "en_US",
    "currencyCode": "USD",
    "timeout": 5
}';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "x-api-key: 1Yr3v5xEXGqwB8MD2g1n3oma0r3blov3Exgo0r86",
    "Content-Type: application/json",
    "version: 3",
    "Content-Length: " . strlen($raw)
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

echo $response;

$response = json_decode($response, true); 
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$HotelRoomAvailabilityResponse = $response['HotelRoomAvailabilityResponse'];
if (count($HotelRoomAvailabilityResponse) > 0) {
    $hotelId = $HotelRoomAvailabilityResponse['hotelId'];
    $size = $HotelRoomAvailabilityResponse['@size'];
    $customerSessionId = $HotelRoomAvailabilityResponse['customerSessionId'];
    $HotelRoomResponse = $HotelRoomAvailabilityResponse['HotelRoomResponse'];
    if (count($HotelRoomResponse) > 0) {
        for ($j = 0; $j < count($HotelRoomResponse); $j ++) {
            $RoomImages = $HotelRoomResponse[$j]['RoomImages'];
            $ValueAdds = $HotelRoomResponse[$j]['ValueAdds'];
            $rateCode = $HotelRoomResponse[$j]['rateCode'];
            $rateDescription = $HotelRoomResponse['rateDescription'];
            $rateOccupancyPerRoom = $HotelRoomResponse[$j]['rateOccupancyPerRoom'];
            $roomTypeCode = $HotelRoomResponse[$j]['roomTypeCode'];
            $roomTypeDescription = $HotelRoomResponse[$j]['roomTypeDescription'];
            // BedTypes
            $BedTypes = $HotelRoomResponse[$j]['BedTypes'];
            if (count($BedTypes) > 0) {
                $size2 = $BedTypes['@size'];
                $BedType = $BedTypes['BedType'];
                if (count($BedType) > 0) {
                    for ($i = 0; $i < count($BedType); $i ++) {
                        $id = $BedType[$i]['@id'];
                        $description = $BedType[$i]['description'];
                    }
                }
            }
            // RateInfos
            $RateInfos = $HotelRoomResponse[$j]['RateInfos'];
            if (count($RateInfos) > 0) {
                $RateInfo = $RateInfos['RateInfo'];
                if (count($RateInfo) > 0) {
                    $nonRefundable = $RateInfo['nonRefundable'];
                    $ChargeableRateInfo = $RateInfo['ChargeableRateInfo'];
                    if (count($ChargeableRateInfo) > 0) {
                        $currencyCode2 = $ChargeableRateInfo['@currencyCode'];
                        $totalC2 = $ChargeableRateInfo['@total'];
                        $currencyCode = $ChargeableRateInfo['currencyCode'];
                        $totalC = $ChargeableRateInfo['total'];
                    }
                    $RoomGroup = $RateInfo['RoomGroup'];
                    if (count($RoomGroup) > 0) {
                        $Room = $RoomGroup['Room'];
                        if (count($Room) > 0) {
                            $rateKey = $Room['rateKey'];
                        }
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