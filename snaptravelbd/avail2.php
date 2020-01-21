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
    "sessionId": "Ybf7-nACdrBbByBdd8%27mNr-0cIqYR8-B2DcX1I-Acr%3AbdncB%7DYLm%7DI-yiI%3Abj8gTd8%27sM%7D-A2YqYuP%27BjD-kN%7D-0iy4bRCLT%7D%3A%7DmMD4%22jI%3Ab-NgT%7DP3zMM%3Ay2Iv-u8c7d1LNN0gf%7DL-KuyyJ%7DILrN%3A-yB%7D4bdncB-yLrML-A2y44i7gZ%7DrIrNycy2LLn%7Dn-ZjLYrML-juY%3Ab-ncZiIr3Ny-yi1vsj8ABjLymNr-pJ%3AgCZrAyjp-%7D%7DMkP%7D%3AA%7D2Y%27-%7DLAP%7Dri3jYqENN%7DB%7DYIfNpI3jIvnMNAi%7DyAkMNAycyL1MD%27BjNHrMM4Ju1vbMD3BjL1fN1-ZcY%3A1%7DNBT%7DyLvNI-yBYqgu8L7d1%3ANN%3A%7Dz1yZy%7DI%3A-j%3AZy%7DnI3u1%3A1%7DD3B2Ir8NrifcrvY%7DDki%7DyAkMn4gcyLb%7DD%3AU2NgCNM4AirvsZCLZ2Di%3AMn4KcY%3A1%7DNBT%7DyLvNI-yBYqgu8L7d%7DyNN%3AZO1%3AB%7DNIZ1-M3T1Lif2%7D4YZP4U%7Dy1fNpI3jrvsRCLi%7DyA%7BMM4-cyLbZ8BZjNH3NI-Uj1q1NDkU2LZ4MD%3ABcY%3A1%7DNBT%7DyLvNI-yBYqgTNr%3D",
    "arrivalDate": "03/27/2020",
    "departureDate": "03/30/2020",
    "room1": "2",
    "rateKey": "al%7B2%7B%7Btzp_tclSGlYbf7-nACdrBbByBdd8%27mNr-0cIqYR8-B2DcX1I-Acr%3AbdncB%7DYLm%7DI-yiI%3Abj8gTd8%27sM%7D-A2YqYuP%27BjD-kN%7D-0iy4bRCLT%7D%3A%7DmMD4%22jI%3Ab-NgT%7DP3zMM%3Ay2Iv-u8c7d1LNN0gf%7DL-KuyyJ%7DILrN%3A-yB%7D4bdncB-yLrML-A2y44i7gZ%7DrIrNycy2LLn%7Dn-ZjLYrML-juY%3Ab-ncZiIr3Ny-yi1vsj8ABjLymNr-pJ%3AgCZrAyjp-%7D%7DMkP%7D%3AA%7D2Y%27-%7DLAP%7Dri3jYqENN%7DB%7DYIfNpI3jIvnMNAi%7DyAkMNAycyL1MD%27BjNHrMM4Ju1vbMD3BjL1fN1-ZcY%3A1%7DNBT%7DyLvNI-yBYqgu8L7d1%3ANN%3A%7Dz1yZy%7DI%3A-j%3AZy%7DnI3u1%3A1%7DD3B2Ir8NrifcrvY%7DDki%7DyAkMn4gcyLb%7DD%3AU2NgCNM4AirvsZCLZ2Di%3AMn4KcY%3A1%7DNBT%7DyLvNI-yBYqgu8L7d%7DyNN%3AZO1%3AB%7DNIZ1-M3T1Lif2%7D4YZP4U%7Dy1fNpI3jrvsRCLi%7DyA%7BMM4-cyLbZ8BZjNH3NI-Uj1q1NDkU2LZ4MD%3ABcY%3A1%7DNBT%7DyLvNI-yBYqgTNr%3Dl%2CGlKjk2_4KzkzlSGlO0R%227p-QRnZFuE%7DrjMq+RnAvYt-%27B8%2F%3AUN9XUO%3A%27i0%27%276NdvB89kUNLDBQr3U7YQi7H%3AU7HfRNYJ%7Dfz3Ot-DRXdCiN%2FCBtr3R8Hv6NdfiNIkUNctBOrrRM-0BXYXZNI3RDRJON%2FfiNRrB0Rmig%2FEitg3cM%7DLu%27z%7D1DqFuJgnjn-z%2F7%2F91n%7DLZMm9dD%7DI%229%2F%2FYO%2FOOH%2Fbgdz7cP3%27it1%22H%3A%7DN-g%2FUO91B4EO9--%2Fy7%7Ds4Yrdj79zy7j-fzY1Jgd%3A2b-BYU9%2FjHy%22%2FH1%7D41D%27U2II3N%3A-piIq7iM%27BjD%27zNy--B1vsZC-T%7DP3kNpIf2YqY-nLB-YY3Nr-gc%3AL1-Mydd8%27mNr-0cIqYR8-B2DcX1I-Acr%3AbdncB%7DYLm%7DI-yiI%3Abj8gTd8%27sM%7D-A2YqYuP%27BjD-kN%7D-0iy4bRCLT%7D%3A%7DmMD4%22jI%3Ab-NgT%7DP3zMM%3Ay2Iv-TNr%3Dl%2CGlx22lSGp+LL5",
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
    $size = $HotelRoomAvailabilityResponse['size'];
    $customerSessionId = $HotelRoomAvailabilityResponse['customerSessionId'];
    $HotelRoomResponse = $HotelRoomAvailabilityResponse['HotelRoomResponse'];
    if (count($HotelRoomResponse) > 0) {
        $RoomImages = $HotelRoomResponse['RoomImages'];
        $ValueAdds = $HotelRoomResponse['ValueAdds'];
        $rateCode = $HotelRoomResponse['rateCode'];
        $rateDescription = $HotelRoomResponse['rateDescription'];
        $rateOccupancyPerRoom = $HotelRoomResponse['rateOccupancyPerRoom'];
        $roomTypeCode = $HotelRoomResponse['roomTypeCode'];
        $roomTypeDescription = $HotelRoomResponse['roomTypeDescription'];
        //BedTypes
        $BedTypes = $HotelRoomResponse['BedTypes'];
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
        $RateInfos = $HotelRoomResponse['RateInfos'];
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
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>