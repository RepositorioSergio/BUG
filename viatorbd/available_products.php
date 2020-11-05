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
// echo "COMECOU CITIES";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = 'https://viatorapi.sandbox.viator.com/service/available/products';
$raw = '{
    "currencyCode": "USD",
    "startDate": "2020-12-21",
    "endDate": "2020-12-31",
    "numAdults": 1,
    "productCodes": [
    "5010SYDNEY",
    "2280SUN",
    "9169P50"
    ]
    }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json'
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

/*
 * echo "<xmp>";
 * var_dump($response);
 * echo "</xmp>";
 */

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$errorReference = $response['errorReference'];
$dateStamp = $response['dateStamp'];
$errorType = $response['errorType'];
$errorCodes = $response['errorCodes'];
$errorMessage = $response['errorMessage'];
$errorName = $response['errorName'];
$extraInfo = $response['extraInfo'];
$extraObject = $response['extraObject'];
$success = $response['success'];
$totalCount = $response['totalCount'];
$errorMessageText = $response['errorMessageText'];
$vmid = $response['vmid'];
$data = $response['data'];
if (count($data) > 0) {
    for ($i=0; $i < count($data); $i++) { 
        $sortOrder = $data[$i]['sortOrder'];
        $supplierName = $data[$i]['supplierName'];
        $currencyCode = $data[$i]['currencyCode'];
        $webURL = $data[$i]['webURL'];
        $specialReservationDetails = $data[$i]['specialReservationDetails'];
        $sslSupported = $data[$i]['sslSupported'];
        $panoramaCount = $data[$i]['panoramaCount'];
        $merchantCancellable = $data[$i]['merchantCancellable'];
        $bookingEngineId = $data[$i]['bookingEngineId'];
        $onRequestPeriod = $data[$i]['onRequestPeriod'];
        $primaryGroupId = $data[$i]['primaryGroupId'];
        $available = $data[$i]['available'];
        $productUrlName = $data[$i]['productUrlName'];
        $primaryDestinationUrlName = $data[$i]['primaryDestinationUrlName'];
        $shortDescription = $data[$i]['shortDescription'];
        $price = $data[$i]['price'];
        $supplierCode = $data[$i]['supplierCode'];
        $translationLevel = $data[$i]['translationLevel'];
        $thumbnailHiResURL = $data[$i]['thumbnailHiResURL'];
        $primaryDestinationName = $data[$i]['primaryDestinationName'];
        $thumbnailURL = $data[$i]['thumbnailURL'];
        $priceFormatted = $data[$i]['priceFormatted'];
        $rrp = $data[$i]['rrp'];
        $rrpformatted = $data[$i]['rrpformatted'];
        $videoCount = $data[$i]['videoCount'];
        $onSale = $data[$i]['onSale'];
        $photoCount = $data[$i]['photoCount'];
        $reviewCount = $data[$i]['reviewCount'];
        $primaryDestinationId = $data[$i]['primaryDestinationId'];
        $merchantNetPriceFrom = $data[$i]['merchantNetPriceFrom'];
        $rating = $data[$i]['rating'];
        $specialOfferAvailable = $data[$i]['specialOfferAvailable'];
        $shortTitle = $data[$i]['shortTitle'];
        $specialReservation = $data[$i]['specialReservation'];
        $uniqueShortDescription = $data[$i]['uniqueShortDescription'];
        $merchantNetPriceFromFormatted = $data[$i]['merchantNetPriceFromFormatted'];
        $savingAmount = $data[$i]['savingAmount'];
        $savingAmountFormated = $data[$i]['savingAmountFormated'];
        $essential = $data[$i]['essential'];
        $admission = $data[$i]['admission'];
        $duration = $data[$i]['duration'];
        $title = $data[$i]['title'];
        $code = $data[$i]['code'];
        $pas = $data[$i]['pas'];
        $productCode = $pas['productCode'];
        $travellerMix = $pas['travellerMix'];
        $incompleteQuote = $pas['incompleteQuote'];
        $removedChildAges = $pas['removedChildAges'];
        $tourGrades = $pas['tourGrades'];
        //
        $SUNSET_ASTAR = $tourGrades['SUNSET_ASTAR'];
        $tourGradeCode = $SUNSET_ASTAR['tourGradeCode'];
        $title = $SUNSET_ASTAR['title'];
        $description = $SUNSET_ASTAR['description'];
        $bookingEngine = $SUNSET_ASTAR['bookingEngine'];
        $sapi = $SUNSET_ASTAR['sapi'];
        $availDates = $SUNSET_EC130['availDates'];
        $dateList = $availDates['dateList'];
        $priceFrom = $availDates['priceFrom'];
        $priceQuote = $availDates['priceQuote'];
        $retailPrice = $priceQuote['retailPrice'];
        $generalRetailPrice = $priceQuote['generalRetailPrice'];
        $priceSource = $priceQuote['priceSource'];
        //
        $SUNSET_EC130 = $tourGrades['SUNSET_EC130'];
        $tourGradeCode = $SUNSET_EC130['tourGradeCode'];
        $title = $SUNSET_EC130['title'];
        $description = $SUNSET_EC130['description'];
        $bookingEngine = $SUNSET_EC130['bookingEngine'];
        $sapi = $SUNSET_EC130['sapi'];
        $availDates = $SUNSET_EC130['availDates'];
        $dateList = $availDates['dateList'];
        $priceFrom = $availDates['priceFrom'];
        $priceQuote = $availDates['priceQuote'];
        $retailPrice = $priceQuote['retailPrice'];
        $generalRetailPrice = $priceQuote['generalRetailPrice'];
        $priceSource = $priceQuote['priceSource'];

        $catIds = $data[$i]['catIds'];
        if (count($catIds) > 0) {
            $catId = "";
            for ($j=0; $j < count($catIds); $j++) { 
                $catId = $catIds[$j];
            }
        }
        $subCatIds = $data[$i]['subCatIds'];
        if (count($subCatIds) > 0) {
            $subCatId = "";
            for ($k=0; $k < count($subCatIds); $k++) { 
                $subCatId = $subCatIds[$k];
            }
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>