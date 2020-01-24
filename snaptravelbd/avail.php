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
echo "COMECOU AVAIL<br/>";
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
    "hotelId": 105485,
    "sessionId": "Yk9O-nRmdIBbByBdd8%27mNr-0cIqYR8-B2DcX1I-Acr%3AbdncB%7DYIf%7DI-yi1vY-nLB2N93N0ABcrv1Z8AU%7Dy%7DmN%3A-BiYqYNnLT%7D%3AI%3ANrcPuYvn-ncj-Ph8NM%3AMjIvCuQq7d1%7DNN8yJ1%3AyMMIZ1-M-O%7DI%7DMM131-ncZ%7Dy%7DmM%7Dc%22jI31%7DNyB-%3A%7DrNIcZBY%3AviMLj-Y%3AvNy-Uu1vn-N%7DU%7DP%27bNn4ZB1vNiE%27U%7DPi%3ANr-pcrvYZC47j0gAdn%27AB%7DByU%7D%7DJ%7Dn%2771L%7DJ%7DY31-D4BjI%3ACNL-Ju131RCLZ2DkrNIcAiI%3ANiMLBjL1QNycyi%3AIQ%7DNBZ2I%7DmML-giYv4iE%27U%7DPi%3ANr-pcrvYZC47j0gAdIAAB%7D%7DPJI4-iyZB1%3A1%3A-I3n%7Dncj%7DYvkMD%3AAiI3n-Dkj-%3AyrNI-BiY%3A4iM%27T%7Drv%7BNy-A2I%3AnNnLZ2D%27bM%7D--iIv7iE%27U%7DPi%3ANr-pcrvYZCz%3D",
    "arrivalDate": "05/28/2020",
    "departureDate": "05/29/2020",
    "room1": "2,8",
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
    $size = $HotelRoomAvailabilityResponse['size'];
    $customerSessionId = $HotelRoomAvailabilityResponse['customerSessionId'];
    $HotelRoomResponse = $HotelRoomAvailabilityResponse['HotelRoomResponse'];
    if (count($HotelRoomResponse) > 0) {
        for ($j=0; $j < count($HotelRoomResponse); $j++) { 
            $RoomImages = $HotelRoomResponse[$j]['RoomImages'];
            $ValueAdds = $HotelRoomResponse[$j]['ValueAdds'];
            $rateCode = $HotelRoomResponse[$j]['rateCode'];
            $rateDescription = $HotelRoomResponse['rateDescription'];
            $rateOccupancyPerRoom = $HotelRoomResponse[$j]['rateOccupancyPerRoom'];
            $roomTypeCode = $HotelRoomResponse[$j]['roomTypeCode'];
            $roomTypeDescription = $HotelRoomResponse[$j]['roomTypeDescription'];
            //BedTypes
            $BedTypes = $HotelRoomResponse[$j]['BedTypes'];
            if (count($BedTypes) > 0) {
                $size = $BedTypes['size'];
                $BedType = $BedTypes['BedType'];
                if (count($BedType) > 0) {
                    for ($i=0; $i < count($BedType); $i++) { 
                        $id = $BedType[$i]['id'];
                        $description = $BedType[$i]['description'];
                    }
                }
            }
            //RateInfos
            $RateInfos = $HotelRoomResponse[$j]['RateInfos'];
            if (count($RateInfos) > 0) {
                $RateInfo = $RateInfos['RateInfo'];
                if (count($RateInfo) > 0) {
                    $nonRefundable = $RateInfo['nonRefundable'];
                    $ChargeableRateInfo = $RateInfo['ChargeableRateInfo'];
                    if (count($ChargeableRateInfo) > 0) {
                        $currencyCode = $ChargeableRateInfo['currencyCode'];
                        $total = $ChargeableRateInfo['total'];
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
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('snaptravelbd');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'rateKey' => $rateKey,
                'rateCode' => $rateCode
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>