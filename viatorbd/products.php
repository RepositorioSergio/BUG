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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = 'https://api.viator.com/partner/products/5010SYDNEY';

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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$status = $response['status'];
$productCode = $response['productCode'];
$language = $response['language'];
$createdAt = $response['createdAt'];
$lastUpdatedAt = $response['lastUpdatedAt'];
$title = $response['title'];
$timeZone = $response['timeZone'];
$description = $response['description'];
// ticketInfo
$ticketInfo = $response['ticketInfo'];
$ticketTypeDescription = $ticketInfo['ticketTypeDescription'];
$ticketsPerBooking = $ticketInfo['ticketsPerBooking'];
$ticketsPerBookingDescription = $ticketInfo['ticketsPerBookingDescription'];
$ticketTypes = $ticketInfo['ticketTypes'];
if (count($ticketTypes) > 0) {
    $ticketType = "";
    for ($iAux=0; $iAux < count($ticketTypes); $iAux++) { 
        $ticketType = $ticketTypes[$iAux];
    }
}
// pricingInfo
$pricingInfo = $response['pricingInfo'];
$type = $pricingInfo['type'];
$ageBands = $pricingInfo['ageBands'];
if (count($ageBands) > 0) {
    for ($iAux2=0; $iAux2 < count($ageBands); $iAux2++) { 
        $ageBand = $ageBands[$iAux2]['ageBand'];
        $startAge = $ageBands[$iAux2]['startAge'];
        $endAge = $ageBands[$iAux2]['endAge'];
        $minTravelersPerBooking = $ageBands[$iAux2]['minTravelersPerBooking'];
        $maxTravelersPerBooking = $ageBands[$iAux2]['maxTravelersPerBooking'];
    }
}
    // images
$images = $response['images'];
if (count($images) > 0) {
    for ($iAux3=0; $iAux3 < count($images); $iAux3++) { 
        $imageSource = $images[$iAux3]['imageSource'];
        $caption = $images[$iAux3]['caption'];
        $isCover = $images[$iAux3]['isCover'];
        $variants = $images[$iAux3]['variants'];
        if (count($variants) > 0) {
            for ($iAux4=0; $iAux4 < count($variants); $iAux4++) { 
                $height = $variants[$iAux4]['height'];
                $width = $variants[$iAux4]['width'];
                $url = $variants[$iAux4]['url'];

            }
        }
    }
}
//logistics
$logistics = $response['logistics'];
$redemption = $logistics['redemption'];
$redemptionType = $redemption['redemptionType'];
$specialInstructions = $redemption['specialInstructions'];
$travelerPickup = $logistics['travelerPickup'];
$pickupOptionType = $travelerPickup['pickupOptionType'];
$allowCustomTravelerPickup = $travelerPickup['allowCustomTravelerPickup'];
$minutesBeforeDepartureTimeForPickup = $travelerPickup['minutesBeforeDepartureTimeForPickup'];
$additionalInfo = $travelerPickup['additionalInfo'];
$locations = $travelerPickup['locations'];
if (count($locations) > 0) {
    for ($iAux5=0; $iAux5 < count($locations); $iAux5++) { 
        $location = $locations[$iAux5]['location'];
        $ref = $location['ref'];
        $pickupType = $locations[$iAux5]['pickupType'];

    }
}
// inclusions
$inclusions = $response['inclusions'];
if (count($inclusions) > 0) {
    for ($iAux6=0; $iAux6 < count($inclusions); $iAux6++) { 
        $category = $inclusions[$iAux6]['category'];
        $categoryDescription = $inclusions[$iAux6]['categoryDescription'];
        $type = $inclusions[$iAux6]['type'];
        $typeDescription = $inclusions[$iAux6]['typeDescription'];
        $otherDescription = $inclusions[$iAux6]['otherDescription'];

    }
}
// exclusions
$exclusions = $response['exclusions'];
if (count($exclusions) > 0) {
    for ($iAux6=0; $iAux6 < count($exclusions); $iAux6++) { 
        $category = $exclusions[$iAux6]['category'];
        $categoryDescription = $exclusions[$iAux6]['categoryDescription'];
        $type = $exclusions[$iAux6]['type'];
        $typeDescription = $exclusions[$iAux6]['typeDescription'];
        $otherDescription = $exclusions[$iAux6]['otherDescription'];

    }
}
// additionalInfo
$additionalInfo = $response['additionalInfo'];
if (count($additionalInfo) > 0) {
    for ($iAux7=0; $iAux7 < count($additionalInfo); $iAux7++) { 
        $type = $additionalInfo[$iAux7]['type'];
        $description = $additionalInfo[$iAux7]['description'];

    }
}
// cancellationPolicy
$cancellationPolicy = $response['cancellationPolicy'];
$cancellationPolicy_type = $cancellationPolicy['type'];
$cancellationPolicy_description = $cancellationPolicy['description'];
$cancelIfBadWeather = $cancellationPolicy['cancelIfBadWeather'];
$cancelIfInsufficientTravelers = $cancellationPolicy['cancelIfInsufficientTravelers'];
// bookingConfirmationSettings
$bookingConfirmationSettings = $response['bookingConfirmationSettings'];
$bookingCutoffType = $bookingConfirmationSettings['bookingCutoffType'];
$bookingCutoffInMinutes = $bookingConfirmationSettings['bookingCutoffInMinutes'];
$confirmationType = $bookingConfirmationSettings['confirmationType'];
// bookingRequirements
$bookingRequirements = $response['bookingRequirements'];
$minTravelersPerBooking = $bookingRequirements['minTravelersPerBooking'];
$maxTravelersPerBooking = $bookingRequirements['maxTravelersPerBooking'];
$requiresAdultForBooking = $bookingRequirements['requiresAdultForBooking'];
    // languageGuides
$languageGuides = $response['languageGuides'];
if (count($languageGuides) > 0) {
    for ($iAux8=0; $iAux8 < count($languageGuides); $iAux8++) { 
        $type = $languageGuides[$iAux8]['type'];
        $language = $languageGuides[$iAux8]['language'];
        $legacyGuide = $languageGuides[$iAux8]['legacyGuide'];

    }
}
// bookingQuestions
$bookingQuestions = $response['bookingQuestions'];
if (count($bookingQuestions) > 0) {
    $bookingQuestion = "";
    for ($iAux9=0; $iAux9 < count($bookingQuestions); $iAux9++) { 
        $bookingQuestion = $bookingQuestions[$iAux9];

    }
}
// tags
$tags = $response['tags'];
if (count($tags) > 0) {
    $tag = "";
    for ($iAux10=0; $iAux10 < count($tags); $iAux10++) { 
        $tag = $tags[$iAux10];

    }
}
// destinations
$destinations = $response['destinations'];
if (count($destinations) > 0) {
    for ($iAux11=0; $iAux11 < count($destinations); $iAux11++) { 
        $ref = $destinations[$iAux11]['ref'];
        $primary = $destinations[$iAux11]['primary'];
    }
}
// itinerary
$itinerary = $response['itinerary'];
$itineraryType = $itinerary['itineraryType'];
$skipTheLine = $itinerary['skipTheLine'];
$privateTour = $itinerary['privateTour'];
$duration = $itinerary['duration'];
$fixedDurationInMinutes = $duration['fixedDurationInMinutes'];
$itineraryItems = $itinerary['itineraryItems'];
if (count($itineraryItems) > 0) {
    for ($iAux12=0; $iAux12 < count($itineraryItems); $iAux12++) { 
        $pointOfInterestLocation = $itineraryItems[$iAux12]['pointOfInterestLocation'];
        $location = $pointOfInterestLocation['location'];
        $ref = $location['ref'];
        $attractionId = $pointOfInterestLocation['attractionId'];
        $duration = $itineraryItems[$iAux12]['duration'];
        $fixedDurationInMinutes = $duration['fixedDurationInMinutes'];
        $passByWithoutStopping = $itineraryItems[$iAux12]['passByWithoutStopping'];
        $admissionIncluded = $itineraryItems[$iAux12]['admissionIncluded'];
        $description = $itineraryItems[$iAux12]['description'];
    }
}
// productOptions
$productOptions = $response['productOptions'];
if (count($productOptions) > 0) {
    for ($iAux13=0; $iAux13 < count($productOptions); $iAux13++) { 
        $productOptionCode = $productOptions[$iAux13]['productOptionCode'];
        $description = $productOptions[$iAux13]['description'];
        $title = $productOptions[$iAux13]['title'];
        $languageGuides = $productOptions[$iAux13]['languageGuides'];
        if (count($languageGuides) > 0) {
            for ($iAux14=0; $iAux14 < count($languageGuides); $iAux14++) { 
                $type = $languageGuides[$iAux14]['type'];
                $language = $languageGuides[$iAux14]['language'];
                $legacyGuide = $languageGuides[$iAux14]['legacyGuide'];

            }
        }
    }
}
// translationInfo
$translationInfo = $response['translationInfo'];
$containsMachineTranslatedText = $translationInfo['containsMachineTranslatedText'];
// supplier
$supplier = $response['supplier'];
$suppliername = $supplier['name'];



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>