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

$url = 'https://api.viator.com/partner/products/bulk';

$raw = '{
    "productCodes": [
      "10175P18",
      "5010SYDNEY"
    ]
  }';

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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$products = $response['products'];
if (count($products) > 0) {
   for ($i=0; $i < count($products); $i++) { 
        $status = $products[$i]['status'];
        $productCode = $products[$i]['productCode'];
        $language = $products[$i]['language'];
        $createdAt = $products[$i]['createdAt'];
        $lastUpdatedAt = $products[$i]['lastUpdatedAt'];
        $title = $products[$i]['title'];
        $timeZone = $products[$i]['timeZone'];
        $description = $products[$i]['description'];
        // ticketInfo
        $ticketInfo = $products[$i]['ticketInfo'];
        $ticketTypeDescription = $ticketInfo['ticketTypeDescription'];
        $ticketsPerBooking = $ticketInfo['ticketsPerBooking'];
        $ticketsPerBookingDescription = $ticketInfo['ticketsPerBookingDescription'];
        $ticketTypes = $ticketInfo['ticketTypes'];
        if (count($ticketTypes) > 0) {
            $ticketType = "";
            for ($iAux=0; $iAux < count($ticketTypes); $iAux++) { 
                $ticketType = $ticketTypes[$iAux];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_tickettypes');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'tickettype' => $ticketType
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 2: " . $e;
                    echo $return;
                } */
            }
        }
        // pricingInfo
        $pricingInfo = $products[$i]['pricingInfo'];
        $type = $pricingInfo['type'];
        $ageBands = $pricingInfo['ageBands'];
        if (count($ageBands) > 0) {
            for ($iAux2=0; $iAux2 < count($ageBands); $iAux2++) { 
                $ageBand = $ageBands[$iAux2]['ageBand'];
                $startAge = $ageBands[$iAux2]['startAge'];
                $endAge = $ageBands[$iAux2]['endAge'];
                $minTravelersPerBooking = $ageBands[$iAux2]['minTravelersPerBooking'];
                $maxTravelersPerBooking = $ageBands[$iAux2]['maxTravelersPerBooking'];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_agebands');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'ageband' => $ageBand,
                        'startage' => $startAge,
                        'endage' => $endAge,
                        'mintravelersperbooking' => $minTravelersPerBooking,
                        'maxtravelersperbooking' => $maxTravelersPerBooking
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 3: " . $e;
                    echo $return;
                } */
            }
        }
         // images
        $images = $products[$i]['images'];
        if (count($images) > 0) {
            for ($iAux3=0; $iAux3 < count($images); $iAux3++) { 
                $imageSource = $images[$iAux3]['imageSource'];
                $caption = $images[$iAux3]['caption'];
                $isCover = $images[$iAux3]['isCover'];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_images');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'imagesource' => $imageSource,
                        'caption' => $caption,
                        'iscover' => $isCover
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 4: " . $e;
                    echo $return;
                } */
                $variants = $images[$iAux3]['variants'];
                if (count($variants) > 0) {
                    for ($iAux4=0; $iAux4 < count($variants); $iAux4++) { 
                        $height = $variants[$iAux4]['height'];
                        $width = $variants[$iAux4]['width'];
                        $url = $variants[$iAux4]['url'];

                        /* try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('viator_products_images_variants');
                            $insert->values(array(
                                'datetime_updated' => time(),
                                'height' => $height,
                                'width' => $width,
                                'url' => $url
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 5: " . $e;
                            echo $return;
                        } */
                    }
                }
            }
        }
        //logistics
        $logistics = $products[$i]['logistics'];
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

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_logistics_locations');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'location' => $location,
                        'ref' => $ref,
                        'pickuptype' => $pickupType
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 6: " . $e;
                    echo $return;
                } */
            }
        }
        // inclusions
        $inclusions = $products[$i]['inclusions'];
        if (count($inclusions) > 0) {
            for ($iAux6=0; $iAux6 < count($inclusions); $iAux6++) { 
                $category = $inclusions[$iAux6]['category'];
                $categoryDescription = $inclusions[$iAux6]['categoryDescription'];
                $type = $inclusions[$iAux6]['type'];
                $typeDescription = $inclusions[$iAux6]['typeDescription'];
                $otherDescription = $inclusions[$iAux6]['otherDescription'];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_inclusions');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'category' => $category,
                        'categorydescription' => $categoryDescription,
                        'type' => $type,
                        'typedescription' => $typeDescription,
                        'otherdescription' => $otherDescription
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 7: " . $e;
                    echo $return;
                } */
            }
        }
        // exclusions
        $exclusions = $products[$i]['exclusions'];
        if (count($exclusions) > 0) {
            for ($iAux6=0; $iAux6 < count($exclusions); $iAux6++) { 
                $category = $exclusions[$iAux6]['category'];
                $categoryDescription = $exclusions[$iAux6]['categoryDescription'];
                $type = $exclusions[$iAux6]['type'];
                $typeDescription = $exclusions[$iAux6]['typeDescription'];
                $otherDescription = $exclusions[$iAux6]['otherDescription'];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_exclusions');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'category' => $category,
                        'categorydescription' => $categoryDescription,
                        'type' => $type,
                        'typedescription' => $typeDescription,
                        'otherdescription' => $otherDescription
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 8: " . $e;
                    echo $return;
                } */
            }
        }
        // additionalInfo
        $additionalInfo = $products[$i]['additionalInfo'];
        if (count($additionalInfo) > 0) {
            for ($iAux7=0; $iAux7 < count($additionalInfo); $iAux7++) { 
                $type = $additionalInfo[$iAux7]['type'];
                $description = $additionalInfo[$iAux7]['description'];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_additionalinfo');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'type' => $type,
                        'description' => $description
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 9: " . $e;
                    echo $return;
                } */
            }
        }
        // cancellationPolicy
        $cancellationPolicy = $products[$i]['cancellationPolicy'];
        $cancellationPolicy_type = $cancellationPolicy['type'];
        $cancellationPolicy_description = $cancellationPolicy['description'];
        $cancelIfBadWeather = $cancellationPolicy['cancelIfBadWeather'];
        $cancelIfInsufficientTravelers = $cancellationPolicy['cancelIfInsufficientTravelers'];
        // bookingConfirmationSettings
        $bookingConfirmationSettings = $products[$i]['bookingConfirmationSettings'];
        $bookingCutoffType = $bookingConfirmationSettings['bookingCutoffType'];
        $bookingCutoffInMinutes = $bookingConfirmationSettings['bookingCutoffInMinutes'];
        $confirmationType = $bookingConfirmationSettings['confirmationType'];
        // bookingRequirements
        $bookingRequirements = $products[$i]['bookingRequirements'];
        $minTravelersPerBooking = $bookingRequirements['minTravelersPerBooking'];
        $maxTravelersPerBooking = $bookingRequirements['maxTravelersPerBooking'];
        $requiresAdultForBooking = $bookingRequirements['requiresAdultForBooking'];
         // languageGuides
        $languageGuides = $products[$i]['languageGuides'];
        if (count($languageGuides) > 0) {
            for ($iAux8=0; $iAux8 < count($languageGuides); $iAux8++) { 
                $type = $languageGuides[$iAux8]['type'];
                $language = $languageGuides[$iAux8]['language'];
                $legacyGuide = $languageGuides[$iAux8]['legacyGuide'];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_languageguides');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'type' => $type,
                        'language' => $language,
                        'legacyguide' => $legacyGuide
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 10: " . $e;
                    echo $return;
                } */
            }
        }
        // bookingQuestions
        $bookingQuestions = $products[$i]['bookingQuestions'];
        if (count($bookingQuestions) > 0) {
            $bookingQuestion = "";
            for ($iAux9=0; $iAux9 < count($bookingQuestions); $iAux9++) { 
                $bookingQuestion = $bookingQuestions[$iAux9];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_bookingquestions');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'bookingquestion' => $bookingQuestion
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 11: " . $e;
                    echo $return;
                } */
            }
        }
        // tags
        $tags = $products[$i]['tags'];
        if (count($tags) > 0) {
            $tag = "";
            for ($iAux10=0; $iAux10 < count($tags); $iAux10++) { 
                $tag = $tags[$iAux10];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_tags');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'tag' => $tag
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 12: " . $e;
                    echo $return;
                } */
            }
        }
        // destinations
        $destinations = $products[$i]['destinations'];
        if (count($destinations) > 0) {
            for ($iAux11=0; $iAux11 < count($destinations); $iAux11++) { 
                $ref = $destinations[$iAux11]['ref'];
                $primary = $destinations[$iAux11]['primary'];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_destinations');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'ref' => $ref,
                        'primary' => $primary
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 13: " . $e;
                    echo $return;
                } */
            }
        }
         // itinerary
         $itinerary = $products[$i]['itinerary'];
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

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_itineraryitems');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'location' => $location,
                        'ref' => $ref,
                        'fixeddurationinminutes' => $fixedDurationInMinutes,
                        'passbywithoutstopping' => $passByWithoutStopping,
                        'admissionincluded' => $admissionIncluded,
                        'description' => $description
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 14: " . $e;
                    echo $return;
                } */
            }
         }
          // productOptions
        $productOptions = $products[$i]['productOptions'];
        if (count($productOptions) > 0) {
            for ($iAux13=0; $iAux13 < count($productOptions); $iAux13++) { 
                $productOptionCode = $productOptions[$iAux13]['productOptionCode'];
                $description = $productOptions[$iAux13]['description'];
                $title = $productOptions[$iAux13]['title'];

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_products_productoptions');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'productoptioncode' => $productOptionCode,
                        'description' => $description,
                        'title' => $title
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 15: " . $e;
                    echo $return;
                } */
                $languageGuides = $productOptions[$iAux13]['languageGuides'];
                if (count($languageGuides) > 0) {
                    for ($iAux14=0; $iAux14 < count($languageGuides); $iAux14++) { 
                        $type = $languageGuides[$iAux14]['type'];
                        $language = $languageGuides[$iAux14]['language'];
                        $legacyGuide = $languageGuides[$iAux14]['legacyGuide'];

                        /* try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('viator_products_productoptions_languageguides');
                            $insert->values(array(
                                'datetime_updated' => time(),
                                'type' => $type,
                                'language' => $language,
                                'legacyguide' => $legacyGuide
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 16: " . $e;
                            echo $return;
                        } */
                    }
                }
            }
        }
         // translationInfo
         $translationInfo = $products[$i]['translationInfo'];
         $containsMachineTranslatedText = $translationInfo['containsMachineTranslatedText'];
          // supplier
        $supplier = $products[$i]['supplier'];
        $suppliername = $supplier['name'];

        /* try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('viator_products');
            $insert->values(array(
                'datetime_updated' => time(),
                'status' => $status,
                'productcode' => $productCode,
                'language' => $language,
                'createdat' => $createdAt,
                'lastupdatedat' => $lastUpdatedAt,
                'title' => $title,
                'description' => $description,
                'tickettypedescription' => $ticketTypeDescription,
                'ticketsperbooking' => $ticketsPerBooking,
                'ticketsperbookingdescription' => $ticketsPerBookingDescription,
                'type' => $type,
                'redemptiontype' => $redemptionType,
                'specialinstructions' => $specialInstructions,
                'pickupoptiontype' => $pickupOptionType,
                'allowcustomtravelerpickup' => $allowCustomTravelerPickup,
                'minutesbeforedeparturetimeforpickup' => $minutesBeforeDepartureTimeForPickup,
                'additionalinfo' => $additionalInfo,
                'cancellationpolicy_type' => $cancellationPolicy_type,
                'cancellationpolicy_description' => $cancellationPolicy_description,
                'cancelifbadweather' => $cancelIfBadWeather,
                'cancelifinsufficienttravelers' => $cancelIfInsufficientTravelers,
                'bookingcutofftype' => $bookingCutoffType,
                'confirmationtype' => $confirmationType,
                'mintravelersperbooking' => $minTravelersPerBooking,
                'maxtravelersperbooking' => $maxTravelersPerBooking,
                'requiresadultforbooking' => $requiresAdultForBooking,
                'itinerarytype' => $itineraryType,
                'skiptheline' => $skipTheLine,
                'privatetour' => $privateTour,
                'fixeddurationinminutes' => $fixedDurationInMinutes,
                'containsmachinetranslatedtext' => $containsMachineTranslatedText,
                'suppliername' => $suppliername
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 1: " . $e;
            echo $return;
        } */
   }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>