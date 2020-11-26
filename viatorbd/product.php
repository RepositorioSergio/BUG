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

$url = 'https://viatorapi.viator.com/service/product';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'exp-api-key' => '5364bbaf-e4f7-4727-9e91-317e794dfbaa',
    'Accept-Language' => 'en-US',
    'Accept' => 'application/json;version=2.0'
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
$data = $response['data'];
$supplierName = $data['supplierName'];
$currencyCode = $data['currencyCode'];
$webURL = $data['webURL'];
$specialReservationDetails = $data['specialReservationDetails'];
$sslSupported = $data['sslSupported'];
$panoramaCount = $data['panoramaCount'];
$merchantCancellable = $data['merchantCancellable'];
$bookingEngineId = $data['bookingEngineId'];
$onRequestPeriod = $data['onRequestPeriod'];
$primaryGroupId = $data['primaryGroupId'];
$pas = $data['pas'];
$available = $data['available'];
$productUrlName = $data['productUrlName'];
$primaryDestinationUrlName = $data['primaryDestinationUrlName'];
$voucherRequirements = $data['voucherRequirements'];
$tourGradesAvailable = $data['tourGradesAvailable'];
$hotelPickup = $data['hotelPickup'];
$videos = $data['videos'];
$bookingQuestions = $data['bookingQuestions'];
$passengerAttributes = $data['passengerAttributes'];
$highlights = $data['highlights'];
$salesPoints = $data['salesPoints'];
$termsAndConditions = $data['termsAndConditions'];
$maxTravellerCount = $data['maxTravellerCount'];
$itinerary = $data['itinerary'];
$destinationId = $data['destinationId'];
$translationLevel = $data['translationLevel'];
$voucherOption = $data['voucherOption'];
$applePassSupported = $data['applePassSupported'];
$city = $data['city'];
$departureTime = $data['departureTime'];
$departureTimeComments = $data['departureTimeComments'];
$departurePoint = $data['departurePoint'];
$specialOffer = $data['specialOffer'];
$returnDetails = $data['returnDetails'];
$mapURL = $data['mapURL'];
$allTravellerNamesRequired = $data['allTravellerNamesRequired'];
$operates = $data['operates'];
$description = $data['description'];
$location = $data['location'];
$country = $data['country'];
$region = $data['region'];
$shortDescription = $data['shortDescription'];
$price = $data['price'];
$supplierCode = $data['supplierCode'];
$thumbnailHiResURL = $data['thumbnailHiResURL'];
$primaryDestinationName = $data['primaryDestinationName'];
$thumbnailURL = $data['thumbnailURL'];
$priceFormatted = $data['priceFormatted'];
$rrp = $data['rrp'];
$rrpformatted = $data['rrpformatted'];
$videoCount = $data['videoCount'];
$onSale = $data['onSale'];
$photoCount = $data['photoCount'];
$reviewCount = $data['reviewCount'];
$primaryDestinationId = $data['primaryDestinationId'];
$merchantNetPriceFrom = $data['merchantNetPriceFrom'];
$rating = $data['rating'];
$specialOfferAvailable = $data['specialOfferAvailable'];
$shortTitle = $data['shortTitle'];
$specialReservation = $data['specialReservation'];
$merchantNetPriceFromFormatted = $data['merchantNetPriceFromFormatted'];
$savingAmount = $data['savingAmount'];
$savingAmountFormated = $data['savingAmountFormated'];
$essential = $data['essential'];
$admission = $data['admission'];
$duration = $data['duration'];
$title = $data['title'];
$code = $data['code'];
$catIds = $data['catIds'];
if (count($catIds) > 0) {
    $catId = "";
    for ($j=0; $j < count($catIds); $j++) { 
        $catId = $catIds[$j];
    }
}
$subCatIds = $data['subCatIds'];
if (count($subCatIds) > 0) {
    $subCatId = "";
    for ($k=0; $k < count($subCatIds); $k++) { 
        $subCatId = $subCatIds[$k];
    }
}
$userPhotos = $data['userPhotos'];
if (count($userPhotos) > 0) {
    for ($i=0; $i < count($userPhotos); $i++) { 
        $sortOrder = $userPhotos[$i]['sortOrder'];
        $ownerName = $userPhotos[$i]['ownerName'];
        $ownerCountry = $userPhotos[$i]['ownerCountry'];
        $productTitle = $userPhotos[$i]['productTitle'];
        $productUrlName = $userPhotos[$i]['productUrlName'];
        $ownerAvatarURL = $userPhotos[$i]['ownerAvatarURL'];
        $sslSupported = $userPhotos[$i]['sslSupported'];
        $timeUploaded = $userPhotos[$i]['timeUploaded'];
        $productCode = $userPhotos[$i]['productCode'];
        $caption = $userPhotos[$i]['caption'];
        $thumbnailURL = $userPhotos[$i]['thumbnailURL'];
        $ownerId = $userPhotos[$i]['ownerId'];
        $editorsPick = $userPhotos[$i]['editorsPick'];
        $photoURL = $userPhotos[$i]['photoURL'];
        $photoId = $userPhotos[$i]['photoId'];
        $photoHiResURL = $userPhotos[$i]['photoHiResURL'];
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
        $reviewId = $reviews[$i]['reviewId'];
   }
}
$tourGrades = $data['tourGrades'];
if (count($tourGrades) > 0) {
    for ($i=0; $i < count($tourGrades); $i++) { 
        $sortOrder = $tourGrades[$i]['sortOrder'];
        $currencyCode = $tourGrades[$i]['currencyCode'];
        $gradeCode = $tourGrades[$i]['gradeCode'];
        $merchantNetPriceFrom = $tourGrades[$i]['merchantNetPriceFrom'];
        $priceFrom = $tourGrades[$i]['priceFrom'];
        $priceFromFormatted = $tourGrades[$i]['priceFromFormatted'];
        $merchantNetPriceFromFormatted = $tourGrades[$i]['merchantNetPriceFromFormatted'];
        $gradeTitle = $tourGrades[$i]['gradeTitle'];
        $gradeDescription = $tourGrades[$i]['gradeDescription'];
        $defaultLanguageCode = $tourGrades[$i]['defaultLanguageCode'];
        $gradeDepartureTime = $tourGrades[$i]['gradeDepartureTime'];
    }
}
$ageBands = $data['ageBands'];
if (count($ageBands) > 0) {
    for ($i=0; $i < count($ageBands); $i++) { 
        $sortOrder = $ageBands[$i]['sortOrder'];
        $ageFrom = $ageBands[$i]['ageFrom'];
        $ageTo = $ageBands[$i]['ageTo'];
        $adult = $ageBands[$i]['adult'];
        $bandId = $ageBands[$i]['bandId'];
        $pluralDescription = $ageBands[$i]['pluralDescription'];
        $treatAsAdult = $ageBands[$i]['treatAsAdult'];
        $description = $ageBands[$i]['description'];
        $count = $ageBands[$i]['count'];
    }
}
$productPhotos = $data['productPhotos'];
if (count($productPhotos) > 0) {
    for ($i=0; $i < count($productPhotos); $i++) { 
        $caption = $productPhotos[$i]['caption'];
        $photoURL = $productPhotos[$i]['photoURL'];
        $path = $productPhotos[$i]['path'];
        $supplier = $productPhotos[$i]['supplier'];
    }
}
$additionalInfo = $data['additionalInfo'];
if (count($additionalInfo) > 0) {
    $additionalInfor = "";
    for ($i=0; $i < count($additionalInfo); $i++) { 
        $additionalInfor = $additionalInfo[$i];
    }
}
$exclusions = $data['exclusions'];
if (count($exclusions) > 0) {
    $exclusion = "";
    for ($i=0; $i < count($exclusions); $i++) { 
        $exclusion = $exclusions[$i];
    }
}
$inclusions = $data['inclusions'];
if (count($inclusions) > 0) {
    $inclusion = "";
    for ($i=0; $i < count($inclusions); $i++) { 
        $inclusion = $inclusions[$i];
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>