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

$url = 'https://viatorapi.sandbox.viator.com/service/attraction';

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
$client->setMethod('GET');
// $client->setRawBody($raw);
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
//
$data = $response['data'];
$webURL = $data['webURL'];
$pageUrlName = $data['pageUrlName'];
$primaryDestinationUrlName = $data['primaryDestinationUrlName'];
$publishedDate = $data['publishedDate'];
$panoramaCount = $data['panoramaCount'];
$userName = $data['userName'];
$infoPageOverviewTitle1 = $data['infoPageOverviewTitle1'];
$infoPageOverviewTitle2 = $data['infoPageOverviewTitle2'];
$infoPageOverview1 = $data['infoPageOverview1'];
$infoPageOverview2 = $data['infoPageOverview2'];
$attractionAdmission = $data['attractionAdmission'];
$attractionTransit = $data['attractionTransit'];
$attractionOpenHours = $data['attractionOpenHours'];
$keywordCount = $data['keywordCount'];
$showReviews = $data['showReviews'];
$tabTitle = $data['tabTitle'];
$descriptionIntro = $data['descriptionIntro'];
$reviewCount = $data['reviewCount'];
$seoType = $data['seoType'];
$pageTitle = $data['pageTitle'];
$editorsPick = $data['editorsPick'];
$showPhotos = $data['showPhotos'];
$descriptionText = $data['descriptionText'];
$overviewSummary = $data['overviewSummary'];
$pagePrimaryLanguage = $data['pagePrimaryLanguage'];
$attractionLatitude = $data['attractionLatitude'];
$attractionLongitude = $data['attractionLongitude'];
$attractionStreetAddress = $data['attractionStreetAddress'];
$attractionCity = $data['attractionCity'];
$attractionState = $data['attractionState'];
$destinationId = $data['destinationId'];
$thumbnailHiResURL = $data['thumbnailHiResURL'];
$photoCount = $data['photoCount'];
$primaryDestinationId = $data['primaryDestinationId'];
$seoId = $data['seoId'];
$productCount = $data['productCount'];
$primaryDestinationName = $data['primaryDestinationName'];
$thumbnailURL = $data['thumbnailURL'];
$rating = $data['rating'];
$title = $data['title'];
$keywords = $data['keywords'];
if (count($keywords) > 0) {
    for ($j=0; $j < count($keywords); $j++) { 
        $keywordId = $keywords[$j]['keywordId'];
        $keyword = $keywords[$j]['keyword'];
    }
}
$userPhotos = $data['userPhotos'];
if (count($userPhotos) > 0) {
    for ($i=0; $i < count($userPhotos); $i++) { 
        $sortOrder = $userPhotos[$i]['sortOrder'];
        $ownerCountry = $userPhotos[$i]['ownerCountry'];
        $productTitle = $userPhotos[$i]['productTitle'];
        $productUrlName = $userPhotos[$i]['productUrlName'];
        $ownerAvatarURL = $userPhotos[$i]['ownerAvatarURL'];
        $sslSupported = $userPhotos[$i]['sslSupported'];
        $photoHiResURL = $userPhotos[$i]['photoHiResURL'];
        $productCode = $userPhotos[$i]['productCode'];
        $caption = $userPhotos[$i]['caption'];
        $thumbnailURL = $userPhotos[$i]['thumbnailURL'];
        $ownerId = $userPhotos[$i]['ownerId'];
        $timeUploaded = $userPhotos[$i]['timeUploaded'];
        $editorsPick = $userPhotos[$i]['editorsPick'];
        $photoURL = $userPhotos[$i]['photoURL'];
        $photoId = $userPhotos[$i]['photoId'];
        $photoMediumResURL = $userPhotos[$i]['photoMediumResURL'];
        $title = $userPhotos[$i]['title'];
    }
}
$reviews = $data['reviews'];
if (count($reviews) > 0) {
    for ($i=0; $i < count($reviews); $i++) { 
        $sortOrder = $reviews[$i]['sortOrder'];
        $ownerName = $reviews[$i]['ownerName'];
        $ownerCountry = $reviews[$i]['ownerCountry'];
        $productTitle = $reviews[$i]['productTitle'];
        $productUrlName = $reviews[$i]['productUrlName'];
        $ownerAvatarURL = $reviews[$i]['ownerAvatarURL'];
        $sslSupported = $reviews[$i]['sslSupported'];
        $productCode = $reviews[$i]['productCode'];
        $submissionDate = $reviews[$i]['submissionDate'];
        $rating = $reviews[$i]['rating'];
        $review = $reviews[$i]['review'];
        $publishedDate = $reviews[$i]['publishedDate'];
        $ownerId = $reviews[$i]['ownerId'];
        $viatorFeedback = $reviews[$i]['viatorFeedback'];
        $viatorNotes = $reviews[$i]['viatorNotes'];
        $reviewId = $reviews[$i]['reviewId'];
    }
}
$products = $data['products'];
if (count($products) > 0) {
    for ($i=0; $i < count($products); $i++) { 
        $sortOrder = $products[$i]['sortOrder'];
        $supplierName = $products[$i]['supplierName'];
        $currencyCode = $products[$i]['currencyCode'];
        $catIds = $products[$i]['catIds'];
        $subCatIds = $products[$i]['subCatIds'];
        $webURL = $products[$i]['webURL'];
        $specialReservationDetails = $products[$i]['specialReservationDetails'];
        $sslSupported = $products[$i]['sslSupported'];
        $panoramaCount = $products[$i]['panoramaCount'];
        $merchantCancellable = $products[$i]['merchantCancellable'];
        $bookingEngineId = $products[$i]['bookingEngineId'];
        $onRequestPeriod = $products[$i]['onRequestPeriod'];
        $primaryGroupId = $products[$i]['primaryGroupId'];
        $pas = $products[$i]['pas'];
        $available = $products[$i]['available'];
        $productUrlName = $products[$i]['productUrlName'];
        $primaryDestinationUrlName = $products[$i]['primaryDestinationUrlName'];
        $pashortDescriptions = $products[$i]['shortDescription'];
        $price = $products[$i]['price'];
        $supplierCode = $products[$i]['supplierCode'];
        $translationLevel = $products[$i]['translationLevel'];
        $thumbnailHiResURL = $products[$i]['thumbnailHiResURL'];
        $onSale = $products[$i]['onSale'];
        $photoCount = $products[$i]['photoCount'];
        $reviewCount = $products[$i]['reviewCount'];
        $primaryDestinationId = $products[$i]['primaryDestinationId'];
        $merchantNetPriceFrom = $products[$i]['merchantNetPriceFrom'];
        $primaryDestinationName = $products[$i]['primaryDestinationName'];
        $thumbnailURL = $products[$i]['thumbnailURL'];
        $priceFormatted = $products[$i]['priceFormatted'];
        $rrp = $products[$i]['rrp'];
        $rrpformatted = $products[$i]['rrpformatted'];
        $videoCount = $products[$i]['videoCount'];
        $rating = $products[$i]['rating'];
        $essential = $products[$i]['essential'];
        $admission = $products[$i]['admission'];
        $uniqueShortDescription = $products[$i]['uniqueShortDescription'];
        $merchantNetPriceFromFormatted = $products[$i]['merchantNetPriceFromFormatted'];
        $savingAmount = $products[$i]['savingAmount'];
        $savingAmountFormated = $products[$i]['savingAmountFormated'];
        $specialReservation = $products[$i]['specialReservation'];
        $shortTitle = $products[$i]['shortTitle'];
        $specialOfferAvailable = $products[$i]['specialOfferAvailable'];
        $duration = $products[$i]['duration'];
        $title = $products[$i]['title'];
        $code = $products[$i]['code'];
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>