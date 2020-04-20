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
echo "COMECOU ACTIVITY ID<br/>";
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


$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$signature = "";
$word = "";
date_default_timezone_set('UTC');
//$date = date("Y-m-d H:i:s");
$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");
$accessKey = "709cc0c1189a46cca41796193c4f19af";
$secretKey = "7a846c68ec6b4a7ba964d3856307a54f";
$method = "GET";
$path = "/activity.json/22777";

$word = $date . "" . $accessKey . "" . $method . "" . $path;

$signature = hash_hmac("sha1", $word, $secretKey,true);
$signature = base64_encode($signature);


$url = "https://api.bokun.io";

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'X-Bokun-Date: ' . $date,
    'X-Bokun-AccessKey: ' . $accessKey,
    'X-Bokun-Signature: ' . $signature,
    'Accept: application/json',
    'Content-Type: application/json;charset=UTF-8',
    'Content-Length: ' . strlen($raw)
));
$client->setUri($url . '/activity.json/22777');
$client->setMethod('GET');
//$client->setRawBody($raw);
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

$response = json_decode($response, true);
 
$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

echo $return;
echo $response;
echo $return;

$id = $response['id'];
echo $return;
echo "ID: " . $id;
echo $return;
$externalId = $response['externalId'];
$productGroupId = $response['productGroupId'];
$productCategory = $response['productCategory'];
$box = $response['box'];
$inventoryLocal = $response['inventoryLocal'];
$inventorySupportsPricing = $response['inventorySupportsPricing'];
$inventorySupportsAvailability = $response['inventorySupportsAvailability'];
$creationDate = $response['creationDate'];
$lastModified = $response['lastModified'];
$lastPublished = $response['lastPublished'];
$published = $response['published'];
$title = $response['title'];
$description = $response['description'];
$excerpt = $response['excerpt'];
$overrideBarcodeFormat = $response['overrideBarcodeFormat'];
$barcodeType = $response['barcodeType'];
$timeZone = $response['timeZone'];
$slug = $response['slug'];
$baseLanguage = $response['baseLanguage'];
$boxedVendor = $response['boxedVendor'];
$storedExternally = $response['storedExternally'];
$pluginId = $response['pluginId'];
$reviewRating = $response['reviewRating'];
$reviewCount = $response['reviewCount'];
$activityType = $response['activityType'];
$bookingType = $response['bookingType'];
$scheduleType = $response['scheduleType'];
$capacityType = $response['capacityType'];
$passExpiryType = $response['passExpiryType'];
$fixedPassExpiryDate = $response['fixedPassExpiryDate'];
$privateActivity = $response['privateActivity'];
$passCapacity = $response['passCapacity'];
$passValidForDays = $response['passValidForDays'];
$passesAvailable = $response['passesAvailable'];
$dressCode = $response['dressCode'];
$passportRequired = $response['passportRequired'];
$included = $response['included'];
$excluded = $response['excluded'];
$requirements = $response['requirements'];
$resourceSlots = $response['resourceSlots'];
$bookingCutoffMinutes = $response['bookingCutoffMinutes'];
$bookingCutoffHours = $response['bookingCutoffHours'];
$bookingCutoffDays = $response['bookingCutoffDays'];
$bookingCutoffWeeks = $response['bookingCutoffWeeks'];
$requestDeadlineMinutes = $response['requestDeadlineMinutes'];
$requestDeadlineHours = $response['requestDeadlineHours'];
$requestDeadlineDays = $response['requestDeadlineDays'];
$requestDeadlineWeeks = $response['requestDeadlineWeeks'];
$boxedActivityId = $response['boxedActivityId'];
$comboActivity = $response['comboActivity'];
$comboParts = $response['comboParts'];
$ticketPerComboComponent = $response['ticketPerComboComponent'];
$ticketComboComponents = $response['ticketComboComponents'];
$pickupActivityId = $response['pickupActivityId'];
$allowCustomizedBookings = $response['allowCustomizedBookings'];
$dayBasedAvailability = $response['dayBasedAvailability'];
$selectFromDayOptions = $response['selectFromDayOptions'];
$defaultRateId = $response['defaultRateId'];
$ticketPerPerson = $response['ticketPerPerson'];
$durationType = $response['durationType'];
$duration = $response['duration'];
$durationMinutes = $response['durationMinutes'];
$durationHours = $response['durationHours'];
$durationDays = $response['durationDays'];
$durationWeeks = $response['durationWeeks'];
$durationText = $response['durationText'];
$minAge = $response['minAge'];
$nextDefaultPrice = $response['nextDefaultPrice'];
$pickupService = $response['pickupService'];
$pickupAllotment = $response['pickupAllotment'];
$pickupAllotmentType = $response['pickupAllotmentType'];
$useComponentPickupAllotments = $response['useComponentPickupAllotments'];
$customPickupAllowed = $response['customPickupAllowed'];
$pickupMinutesBefore = $response['pickupMinutesBefore'];
$noPickupMsg = $response['noPickupMsg'];
$ticketMsg = $response['ticketMsg'];
$showGlobalPickupMsg = $response['showGlobalPickupMsg'];
$showNoPickupMsg = $response['showNoPickupMsg'];
$dropoffService = $response['dropoffService'];
$customDropoffAllowed = $response['customDropoffAllowed'];
$useSameAsPickUpPlaces = $response['useSameAsPickUpPlaces'];
$difficultyLevel = $response['difficultyLevel'];
$hasOpeningHours = $response['hasOpeningHours'];
$defaultOpeningHours = $response['defaultOpeningHours'];
$hasBoxes = $response['hasBoxes'];
$requestDeadline = $response['requestDeadline'];
$bookingCutoff = $response['bookingCutoff'];
$actualId = $response['actualId'];
$nextDefaultPriceAsText = $response['nextDefaultPriceAsText'];

$locationCode = $response['locationCode'];
$country = $locationCode['country'];
$location = $locationCode['location'];
$name = $locationCode['name'];

$googlePlace = $response['googlePlace'];
$googlePlacecountry = $googlePlace['country'];
$googlePlacecountryCode = $googlePlace['countryCode'];
$googlePlacecity = $googlePlace['city'];
$googlePlacecityCode = $googlePlace['cityCode'];
$geoLocationCenter = $googlePlace['geoLocationCenter'];
$lat = $geoLocationCenter['lat'];
$lng = $geoLocationCenter['lng'];

$nextDefaultPriceMoney = $response['nextDefaultPriceMoney'];
$amount = $nextDefaultPriceMoney['amount'];
$currency = $nextDefaultPriceMoney['currency'];

$vendor = $response['vendor'];
$vendorid = $vendor['id'];
$vendortitle = $vendor['title'];
$vendorcurrencyCode = $vendor['currencyCode'];
$vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
$vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
$vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
$vendorcompanyEmailIsDefault = $vendor['companyEmailIsDefault'];

$actualVendor = $response['actualVendor'];
$actualVendorid = $actualVendor['id'];
$actualVendortitle = $actualVendor['title'];
$actualVendorcurrencyCode = $actualVendor['currencyCode'];
$showInvoiceIdOnTicket = $actualVendor['showInvoiceIdOnTicket'];
$showAgentDetailsOnTicket = $actualVendor['showAgentDetailsOnTicket'];
$showPaymentsOnInvoice = $actualVendor['showPaymentsOnInvoice'];
$companyEmailIsDefault = $actualVendor['companyEmailIsDefault'];

$route = $response['route'];
$mapZoomLevel = $route['mapZoomLevel'];
$center = $route['center'];
$centerlat = $center['lat'];
$centerlng = $center['lng'];
$start = $route['start'];
$startlat = $start['lat'];
$startlng = $start['lng'];
$end = $route['end'];
$endlat = $end['lat'];
$endlng = $end['lng'];

$cancellationPolicy = $response['cancellationPolicy'];
$cancellationPolicyid = $cancellationPolicy['id'];
$cancellationPolicytitle = $cancellationPolicy['title'];
$cancellationPolicytax = $cancellationPolicy['tax'];
$defaultPolicy = $cancellationPolicy['defaultPolicy'];
$penaltyRules = $cancellationPolicy['penaltyRules'];
if (count($penaltyRules) > 0) {
    for ($i=0; $i < count($penaltyRules); $i++) { 
        $id = $penaltyRules[$i]['id'];
        $chargeType = $penaltyRules[$i]['chargeType'];
        $charge = $penaltyRules[$i]['charge'];
        $cutoffHours = $penaltyRules[$i]['cutoffHours'];
    }
}

$displaySettings = $response['displaySettings'];
$showPickupPlaces = $displaySettings['showPickupPlaces'];
$showRouteMap = $displaySettings['showRouteMap'];
$selectRateBasedOnStartTime = $displaySettings['selectRateBasedOnStartTime'];
$customFields = $displaySettings['customFields'];
if (count($customFields) > 0) {
    for ($j=0; $j < count($customFields); $j++) { 
        $type = $customFields[$j]['title'];
        $inputFieldId = $customFields[$j]['inputFieldId'];
        $value = $customFields[$j]['value'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('customfields');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'type' => $type,
                'inputfieldid' => $inputFieldId,
                'value' => $value,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 25: " . $e;
            echo $return;
        }
    }
}

$keyPhoto = $response['keyPhoto'];
$keyPhotoid = $keyPhoto['id'];
$keyPhotooriginalUrl = $keyPhoto['originalUrl'];
$keyPhotodescription = $keyPhoto['description'];
$keyPhotoalternateText = $keyPhoto['alternateText'];
$keyPhotoheight = $keyPhoto['height'];
$keyPhotowidth = $keyPhoto['width'];
$keyPhotofileName = $keyPhoto['fileName'];
$derived = $keyPhoto['derived'];
if (count($derived) > 0) {
    for ($d=0; $d < count($derived); $d++) { 
        $name = $derived[$d]['name'];
        $url = $derived[$d]['url'];
        $cleanUrl = $derived[$d]['cleanUrl'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('derived_keyPhoto');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'name' => $name,
                'url' => $url,
                'cleanurl' => $cleanUrl,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 26: " . $e;
            echo $return;
        }
    }
}

try {

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('activitiesbyid');
    $insert->values(array(
        'id' => $id,
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'externalid' => $externalId,
        'productgroupid' => $productGroupId,
        'productcategory' => $productCategory,
        'box' => $box,
        'inventorylocal' => $inventoryLocal,
        'inventorysupportspricing' => $inventorySupportsPricing,
        'inventorysupportsavailability' => $inventorySupportsAvailability,
        'creationdate' => $creationDate,
        'lastmodified' => $lastModified,
        'lastpublished' => $lastPublished,
        'published' => $published,
        'title' => $title,
        'description' => $description,
        'excerpt' => $excerpt,
        'overridebarcodeformat' => $overrideBarcodeFormat,
        'barcodetype' => $barcodeType,
        'timezone' => $timeZone,
        'slug' => $slug,
        'baselanguage' => $baseLanguage,
        'boxedvendor' => $boxedVendor,
        'storedexternally' => $storedExternally,
        'pluginid' => $pluginId,
        'reviewrating' => $reviewRating,
        'reviewcount' => $reviewCount,
        'activitytype' => $activityType,
        'bookingtype' => $bookingType,
        'scheduletype' => $scheduleType,
        'capacitytype' => $capacityType,
        'passexpirytype' => $passExpiryType,
        'fixedpassexpirydate' => $fixedPassExpiryDate,
        'privateactivity' => $privateActivity,
        'passcapacity' => $passCapacity,
        'passvalidfordays' => $passValidForDays,
        'passesavailable' => $passesAvailable,
        'dresscode' => $dressCode,
        'passportrequired' => $passportRequired,
        'included' => $included,
        'excluded' => $excluded,
        'requirements' => $requirements,
        'resourceslots' => $resourceSlots,
        'bookingcutoffminutes' => $bookingCutoffMinutes,
        'bookingcutoffhours' => $bookingCutoffHours,
        'bookingcutoffdays' => $bookingCutoffDays,
        'bookingcutoffweeks' => $bookingCutoffWeeks,
        'requestdeadlineminutes' => $requestDeadlineMinutes,
        'requestdeadlinehours' => $requestDeadlineHours,
        'requestdeadlinedays' => $requestDeadlineDays,
        'boxedactivityid' => $boxedActivityId,
        'comboactivity' => $comboActivity,
        'comboparts' => $comboParts,
        'ticketpercombocomponent' => $ticketPerComboComponent,
        'ticketcombocomponents' => $ticketComboComponents,
        'pickupactivityid' => $pickupActivityId,
        'allowcustomizedbookings' => $allowCustomizedBookings,
        'daybasedavailability' => $dayBasedAvailability,
        'selectfromdayoptions' => $selectFromDayOptions,
        'defaultrateid' => $defaultRateId,
        'ticketperperson' => $ticketPerPerson,
        'durationtype' => $durationType,
        'duration' => $duration,
        'durationminutes' => $durationMinutes,
        'durationhours' => $durationHours,
        'durationdays' => $durationDays,
        'durationweeks' => $durationWeeks,
        'durationtext' => $durationText,
        'minage' => $minAge,
        'nextdefaultprice' => $nextDefaultPrice,
        'pickupservice' => $pickupService,
        'pickupallotment' => $pickupAllotment,
        'pickupallotmenttype' => $pickupAllotmentType,
        'usecomponentpickupallotments' => $useComponentPickupAllotments,
        'custompickupallowed' => $customPickupAllowed,
        'pickupminutesbefore' => $pickupMinutesBefore,
        'nopickupmsg' => $noPickupMsg,
        'ticketmsg' => $ticketMsg,
        'showglobalpickupmsg' => $showGlobalPickupMsg,
        'shownopickupmsg' => $showNoPickupMsg,
        'dropoffservice' => $dropoffService,
        'customdropoffallowed' => $customDropoffAllowed,
        'usesameaspickUpplaces' => $useSameAsPickUpPlaces,
        'difficultylevel' => $difficultyLevel,
        'hasopeninghours' => $hasOpeningHours,
        'defaultopeninghours' => $defaultOpeningHours,
        'hasboxes' => $hasBoxes,
        'requestdeadline' => $requestDeadline,
        'bookingcutoff' => $bookingCutoff,
        'actualid' => $actualId,
        'nextdefaultpriceastext' => $nextDefaultPriceAsText,
        'country' => $country,
        'location' => $location,
        'name' => $name,
        'googleplacecountry' => $googlePlacecountry,
        'googleplacecountrycode' => $googlePlacecountryCode,
        'googleplacecity' => $googlePlacecity,
        'googleplacecitycode' => $googlePlacecityCode,
        'lat' => $lat,
        'lng' => $lng,
        'amount' => $amount,
        'currency' => $currency,
        'vendorid' => $vendorid,
        'vendortitle' => $vendortitle,
        'vendorcurrencycode' => $vendorcurrencyCode,
        'vendorshowinvoiceidonticket' => $vendorshowInvoiceIdOnTicket,
        'vendorshowagentdetailsonticket' => $vendorshowAgentDetailsOnTicket,
        'vendorshowpaymentsoninvoice' => $vendorshowPaymentsOnInvoice,
        'vendorcompanyemailisdefault' => $vendorcompanyEmailIsDefault,
        'actualvendorid' => $actualVendorid,
        'actualvendortitle' => $actualVendortitle,
        'actualvendorcurrencycode' => $actualVendorcurrencyCode,
        'showinvoiceidonticket' => $showInvoiceIdOnTicket,
        'showagentdetailsonticket' => $showAgentDetailsOnTicket,
        'showpaymentsoninvoice' => $showPaymentsOnInvoice,
        'companyemailisdefault' => $companyEmailIsDefault,
        'mapzoomlevel' => $mapZoomLevel,
        'centerlat' => $centerlat,
        'centerlng' => $centerlng,
        'startlat' => $startlat,
        'startlng' => $startlng,
        'endlat' => $endlat,
        'endlng' => $endlng,
        'cancellationpolicyid' => $cancellationPolicyid,
        'cancellationpolicytitle' => $cancellationPolicytitle,
        'cancellationpolicytax' => $cancellationPolicytax,
        'defaultpolicy' => $defaultPolicy,
        'showpickupplaces' => $showPickupPlaces,
        'showroutemap' => $showRouteMap,
        'selectratebasedonstarttime' => $selectRateBasedOnStartTime,
        'keyphotoid' => $keyPhotoid,
        'keyphotooriginalurl' => $keyPhotooriginalUrl,
        'keyphotodescription' => $keyPhotodescription,
        'keyphotoalternatetext' => $keyPhotoalternateText,
        'keyphotoheight' => $keyPhotoheight,
        'keyphotowidth' => $keyPhotowidth,
        'keyphotofilename' => $keyPhotofileName
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect(); 

} catch (\Exception $e) {
    echo $return;
    echo "ERROR 1: " . $e;
    echo $return;
} 

$photos = $response['photos'];
if (count($photos) > 0) {
    for ($f=0; $f < count($photos); $f++) { 
        $photosid = $photos[$f]['id'];
        $photosoriginalUrl = $photos[$f]['originalUrl'];
        $photosdescription = $photos[$f]['description'];
        $photosalternateText = $photos[$f]['alternateText'];
        $photosheight = $photos[$f]['height'];
        $photoswidth = $photos[$f]['width'];
        $photosfileName = $photos[$f]['fileName'];
        $derived = $photos[$f]['derived'];
        if (count($derived) > 0) {
            for ($d=0; $d < count($derived); $d++) { 
                $name = $derived[$d]['name'];
                $url = $derived[$d]['url'];
                $cleanUrl = $derived[$d]['cleanUrl'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('derived_photos');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name' => $name,
                        'url' => $url,
                        'cleanurl' => $cleanUrl,
                        'photosid' => $photosid,
                        'activityid' => $id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERROR 24: " . $e;
                    echo $return;
                }
            }
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('photos');
            $insert->values(array(
                'photosid' => $photosid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'filename' => $photosfileName,
                'description' => $photosdescription,
                'alternatetext' => $photosalternateText,
                'height' => $photosheight,
                'width' => $photoswidth,
                'originalurl' => $photosoriginalUrl,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 2: " . $e;
            echo $return;
        } 
    }
}

$mainContactFields = $response['mainContactFields'];
if (count($mainContactFields) > 0) {
    for ($k=0; $k < count($mainContactFields); $k++) { 
        $field = $mainContactFields[$k]['field'];
        $required = $mainContactFields[$k]['required'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('maincontactfields');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'field' => $field,
                'required' => $required,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 3: " . $e;
            echo $return;
        }
    }
}

$requiredCustomerFields = $response['requiredCustomerFields'];
if (count($requiredCustomerFields) > 0) {
    $customerfields = "";
    for ($l=0; $l < count($requiredCustomerFields); $l++) { 
        $customfields = $requiredCustomerFields[$l];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('requiredcustomerfields');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'customfields' => $customfields,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 4: " . $e;
            echo $return;
        }
    }
}

$languages = $response['languages'];
if (count($languages) > 0) {
    $language = "";
    for ($l=0; $l < count($languages); $l++) { 
        $language = $languages[$l];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('languages');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'language' => $language,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 5: " . $e;
            echo $return;
        }
    }
}

$startPoints = $response['startPoints'];
if (count($startPoints) > 0) {
    for ($c=0; $c < count($startPoints); $c++) { 
        $startpointsid = $startPoints[$c]['id'];
        $type = $startPoints[$c]['type'];
        $title = $startPoints[$c]['title'];
        $code = $startPoints[$c]['code'];
        $pickupTicketDescription = $startPoints[$c]['pickupTicketDescription'];
        $dropoffTicketDescription = $startPoints[$c]['dropoffTicketDescription'];
        $address = $startPoints[$c]['address'];
        $addressid = $address['id'];
        $addressLine1 = $address['addressLine1'];
        $addressLine2 = $address['addressLine2'];
        $addressLine3 = $address['addressLine3'];
        $city = $address['city'];
        $state = $address['state'];
        $postalCode = $address['postalCode'];
        $countryCode = $address['countryCode'];
        $mapZoomLevel = $address['mapZoomLevel'];
        $origin = $address['origin'];
        $originId = $address['originId'];
        $geoPoint = $address['geoPoint'];
        $latitude = $geoPoint['latitude'];
        $longitude = $geoPoint['longitude'];
        $unLocode = $address['unLocode'];
        $unlocodecountry = $unLocode['country'];
        $unlocodecity = $unLocode['city'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('startpoints');
            $insert->values(array(
                'startpointsid' => $startpointsid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'type' => $type,
                'title' => $title,
                'code' => $code,
                'pickupticketdescription' => $pickupTicketDescription,
                'dropoffticketdescription' => $dropoffTicketDescription,
                'addressid' => $addressid,
                'addressline1' => $addressLine1,
                'addressline2' => $addressLine2,
                'addressline3' => $addressLine3,
                'city' => $city,
                'state' => $state,
                'postalcode' => $postalCode,
                'countrycode' => $countryCode,
                'mapzoomlevel' => $mapZoomLevel,
                'origin' => $origin,
                'originid' => $originId,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'unlocodecountry' => $unlocodecountry,
                'unlocodecity' => $unlocodecity,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 6: " . $e;
            echo $return;
        } 

        $created = $address['created'];
        if (count($created) > 0) {
            for ($cAux=0; $cAux < count($created); $cAux++) { 
                $created = $created[$cAux];
            }
        }

        $created = $startPoints[$c]['created'];
        if (count($created) > 0) {
            for ($cAux=0; $cAux < count($created); $cAux++) { 
                $created = $created[$cAux];
            }
        }
    }
}

$bookingQuestions = $response['bookingQuestions'];
if (count($bookingQuestions) > 0) {
    for ($x=0; $x < count($bookingQuestions); $x++) { 
        $bookingquestionsid = $bookingQuestions[$x]['id'];
        $personalData = $bookingQuestions[$x]['personalData'];
        $questionCode = $bookingQuestions[$x]['questionCode'];
        $label = $bookingQuestions[$x]['label'];
        $help = $bookingQuestions[$x]['help'];
        $placeholder = $bookingQuestions[$x]['placeholder'];
        $required = $bookingQuestions[$x]['required'];
        $defaultValue = $bookingQuestions[$x]['defaultValue'];
        $dataType = $bookingQuestions[$x]['dataType'];
        $selectFromOptions = $bookingQuestions[$x]['selectFromOptions'];
        $selectMultiple = $bookingQuestions[$x]['selectMultiple'];
        $context = $bookingQuestions[$x]['context'];
        $pricingCategoryTriggerSelection = $bookingQuestions[$x]['pricingCategoryTriggerSelection'];
        $rateTriggerSelection = $bookingQuestions[$x]['rateTriggerSelection'];
        $extraTriggerSelection = $bookingQuestions[$x]['extraTriggerSelection'];
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('bookingquestions');
            $insert->values(array(
                'bookingquestionsid' => $bookingquestionsid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'personaldata' => $personalData,
                'questioncode' => $questionCode,
                'label' => $label,
                'help' => $help,
                'placeholder' => $placeholder,
                'required' => $required,
                'defaultvalue' => $defaultValue,
                'datatype' => $dataType,
                'selectfromoptions' => $selectFromOptions,
                'selectmultiple' => $selectMultiple,
                'context' => $context,
                'pricingcategorytriggerselection' => $pricingCategoryTriggerSelection,
                'ratetriggerselection' => $rateTriggerSelection,
                'extratriggerselection' => $extraTriggerSelection,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 7: " . $e;
            echo $return;
        } 

        $options = $bookingQuestions[$x]['options'];
        if (count($options) > 0) {
            for ($xAux=0; $xAux < count($options); $xAux++) { 
                $name = $options[$xAux]['name'];
                $value = $options[$xAux]['value'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('options_bookingquestions');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name' => $name,
                        'value' => $value,
                        'activityid' => $id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERROR 8: " . $e;
                    echo $return;
                }
            }
        }
    }
}

$passengerFields = $response['passengerFields'];
if (count($passengerFields) > 0) {
    for ($y=0; $y < count($passengerFields); $y++) { 
        $field = $passengerFields[$y]['field'];
        $required = $passengerFields[$y]['required'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('passengerFields');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'field' => $field,
                'required' => $required,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 9: " . $e;
            echo $return;
        }
    }
}

$activityCategories = $response['activityCategories'];
if (count($activityCategories) > 0) {
    $activity = "";
    for ($w=0; $w < count($activityCategories); $w++) { 
        $activity = $activityCategories[$w];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('activitycategories');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'activity' => $activity,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 10: " . $e;
            echo $return;
        }
    }
}

$guidanceTypes = $response['guidanceTypes'];
if (count($guidanceTypes) > 0) {
    for ($s=0; $s < count($guidanceTypes); $s++) { 
        $guidancetypesid = $guidanceTypes[$s]['id'];
        $guidanceType = $guidanceTypes[$s]['guidanceType'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('guidancetypes');
            $insert->values(array(
                'guidancetypesid' => $guidancetypesid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'guidancetype' => $guidanceType,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 11: " . $e;
            echo $return;
        }

        $created = $guidanceTypes[$s]['created'];
        if (count($created) > 0) {
            for ($cAux=0; $cAux < count($created); $cAux++) { 
                $created = $created[$cAux];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('created_guidancetypes');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'created' => $created,
                        'guidancetypesid' => $guidancetypesid
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERROR 12: " . $e;
                    echo $return;
                }
            }
        }
        $languages = $guidanceTypes[$s]['languages'];
        if (count($languages) > 0) {
            $language = "";
            for ($cAux=0; $cAux < count($languages); $cAux++) { 
                $language = $languages[$cAux];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('languages_guidancetypes');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'language' => $language,
                        'guidancetypesid' => $guidancetypesid
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERROR 13: " . $e;
                    echo $return;
                }
            }
        }
    }
}

$rates = $response['rates'];
if (count($rates) > 0) {
    for ($r=0; $r < count($rates); $r++) { 
        $ratesid = $rates[$r]['id'];
        $title = $rates[$r]['title'];
        $description = $rates[$r]['description'];
        $index = $rates[$r]['index'];
        $rateCode = $rates[$r]['rateCode'];
        $pricedPerPerson = $rates[$r]['pricedPerPerson'];
        $minPerBooking = $rates[$r]['minPerBooking'];
        $maxPerBooking = $rates[$r]['maxPerBooking'];
        $fixedPassExpiryDate = $rates[$r]['fixedPassExpiryDate'];
        $passValidForDays = $rates[$r]['passValidForDays'];
        $pickupSelectionType = $rates[$r]['pickupSelectionType'];
        $pickupPricingType = $rates[$r]['pickupPricingType'];
        $pickupPricedPerPerson = $rates[$r]['pickupPricedPerPerson'];
        $dropoffSelectionType = $rates[$r]['dropoffSelectionType'];
        $dropoffPricingType = $rates[$r]['dropoffPricingType'];
        $dropoffPricedPerPerson = $rates[$r]['dropoffPricedPerPerson'];
        $allStartTimes = $rates[$r]['allStartTimes'];
        $tieredPricingEnabled = $rates[$r]['tieredPricingEnabled'];
        $allPricingCategories = $rates[$r]['allPricingCategories'];
        $cancellationPolicy = $rates[$r]['cancellationPolicy'];
        $cancellationPolicyid = $cancellationPolicy['id'];
        $cancellationPolicytitle = $cancellationPolicy['title'];
        $cancellationPolicytax = $cancellationPolicy['tax'];
        $defaultPolicy = $cancellationPolicy['defaultPolicy'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('rates');
            $insert->values(array(
                'ratesid' => $ratesid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'title' => $title,
                'description' => $description,
                'index' => $index,
                'ratecode' => $rateCode,
                'pricedperperson' => $pricedPerPerson,
                'minperbooking' => $minPerBooking,
                'maxperbooking' => $maxPerBooking,
                'fixedpassexpirydate' => $fixedPassExpiryDate,
                'passvalidfordays' => $passValidForDays,
                'pickupselectiontype' => $pickupSelectionType,
                'pickuppricingtype' => $pickupPricingType,
                'pickuppricedperperson' => $pickupPricedPerPerson,
                'dropoffselectiontype' => $dropoffSelectionType,
                'dropoffpricingtype' => $dropoffPricingType,
                'dropoffpricedperperson' => $dropoffPricedPerPerson,
                'allstarttimes' => $allStartTimes,
                'cancellationpolicyid' => $cancellationPolicyid,
                'cancellationpolicytitle' => $cancellationPolicytitle,
                'cancellationpolicytax' => $cancellationPolicytax,
                'defaultpolicy' => $defaultPolicy,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 14: " . $e;
            echo $return;
        } 

        $penaltyRules = $cancellationPolicy['penaltyRules'];
        if (count($penaltyRules) > 0) {
            for ($i=0; $i < count($penaltyRules); $i++) { 
                $penaltyrulesid = $penaltyRules[$i]['id'];
                $chargeType = $penaltyRules[$i]['chargeType'];
                $charge = $penaltyRules[$i]['charge'];
                $cutoffHours = $penaltyRules[$i]['cutoffHours'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('penaltyrules');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'penaltyrulesid' => $penaltyrulesid,
                        'chargetype' => $chargeType,
                        'charge' => $charge,
                        'cutoffhours' => $cutoffHours,
                        'ratesid' => $ratesid
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERROR 15: " . $e;
                    echo $return;
                }
            }
        }
        $extraConfigs = $rates[$r]['extraConfigs'];
        if (count($extraConfigs) > 0) {
            for ($z=0; $z < count($extraConfigs); $z++) { 
                $extraconfigsid = $extraConfigs[$z]['id'];
                $activityExtraId = $extraConfigs[$z]['activityExtraId'];
                $selectionType = $extraConfigs[$z]['selectionType'];
                $pricingType = $extraConfigs[$z]['pricingType'];
                $pricedPerPerson = $extraConfigs[$z]['pricedPerPerson'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('extraconfigs_rules');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'extraconfigsid' => $extraconfigsid,
                        'activityextraid' => $activityExtraId,
                        'selectiontype' => $selectionType,
                        'pricingtype' => $pricingType,
                        'pricedperperson' => $pricedPerPerson,
                        'ratesid' => $ratesid
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERROR 16: " . $e;
                    echo $return;
                }

                $created = $extraConfigs[$z]['created'];
                if (count($created) > 0) {
                    for ($cAux=0; $cAux < count($created); $cAux++) { 
                        $created = $created[$cAux];
                    }
                }
            }
        }
        $startTimeIds = $rates[$r]['startTimeIds'];
        if (count($startTimeIds) > 0) {
            $start = "";
            for ($st=0; $st < count($startTimeIds); $st++) { 
                $start = $startTimeIds[$st];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('starttimeids_rules');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'start' => $start,
                        'ratesid' => $ratesid
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERROR 17: " . $e;
                    echo $return;
                }
            }
        }
        $pricingCategoryIds = $rates[$r]['pricingCategoryIds'];
        if (count($pricingCategoryIds) > 0) {
            $pricing = "";
            for ($p=0; $p < count($pricingCategoryIds) ; $p++) { 
                $pricing = $pricingCategoryIds[$p];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('pricingcategoryids_rules');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'pricing' => $pricing,
                        'ratesid' => $ratesid
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERROR 18: " . $e;
                    echo $return;
                }
            }
        }
    }
}

$pricingCategories = $response['pricingCategories'];
if (count($pricingCategories) > 0) {
    for ($p=0; $p < count($pricingCategories); $p++) { 
        $pricingcategoriesid = $pricingCategories[$p]['id'];
        $title = $pricingCategories[$p]['title'];
        $ticketCategory = $pricingCategories[$p]['ticketCategory'];
        $occupancy = $pricingCategories[$p]['occupancy'];
        $groupSize = $pricingCategories[$p]['groupSize'];
        $ageQualified = $pricingCategories[$p]['ageQualified'];
        $minAge = $pricingCategories[$p]['minAge'];
        $maxAge = $pricingCategories[$p]['maxAge'];
        $dependent = $pricingCategories[$p]['dependent'];
        $masterCategoryId = $pricingCategories[$p]['masterCategoryId'];
        $maxPerMaster = $pricingCategories[$p]['maxPerMaster'];
        $sumDependentCategories = $pricingCategories[$p]['sumDependentCategories'];
        $maxDependentSum = $pricingCategories[$p]['maxDependentSum'];
        $internalUseOnly = $pricingCategories[$p]['internalUseOnly'];
        $defaultCategory = $pricingCategories[$p]['defaultCategory'];
        $fullTitle = $pricingCategories[$p]['fullTitle'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('pricingcategories');
            $insert->values(array(
                'pricingcategoriesid' => $pricingcategoriesid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'title' => $title,
                'ticketcategory' => $ticketCategory,
                'occupancy' => $occupancy,
                'groupsize' => $groupSize,
                'agequalified' => $ageQualified,
                'minage' => $minAge,
                'maxage' => $maxAge,
                'dependent' => $dependent,
                'mastercategoryid' => $masterCategoryId,
                'maxpermaster' => $maxPerMaster,
                'sumdependentcategories' => $sumDependentCategories,
                'maxdependentsum' => $maxDependentSum,
                'internaluseonly' => $internalUseOnly,
                'defaultcategory' => $defaultCategory,
                'fulltitle' => $fullTitle,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 19: " . $e;
            echo $return;
        }
    }
}

$agendaItems = $response['agendaItems'];
if (count($agendaItems)  > 0) {
    for ($a=0; $a < count($agendaItems); $a++) { 
        $agendaitemsid = $agendaItems[$a]['id'];
        $index = $agendaItems[$a]['index'];
        $title = $agendaItems[$a]['title'];
        $excerpt = $agendaItems[$a]['excerpt'];
        $body = $agendaItems[$a]['body'];
        $day = $agendaItems[$a]['day'];
        $address = $agendaItems[$a]['address'];
        $keyPhoto = $agendaItems[$a]['keyPhoto'];
        $location = $agendaItems[$a]['location'];
        $locationaddress = $location['address'];
        $city = $location['city'];
        $countryCode = $location['countryCode'];
        $postCode = $location['postCode'];
        $latitude = $location['latitude'];
        $longitude = $location['longitude'];
        $zoomLevel = $location['zoomLevel'];
        $origin = $location['origin'];
        $originId = $location['originId'];
        $wholeAddress = $location['wholeAddress'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('agendaitems');
            $insert->values(array(
                'agendaitemsid' => $agendaitemsid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'title' => $title,
                'index' => $index,
                'excerpt' => $excerpt,
                'body' => $body,
                'day' => $day,
                'address' => $address,
                'keyphoto' => $keyPhoto,
                'locationaddress' => $locationaddress,
                'city' => $city,
                'countrycode' => $countryCode,
                'postcode' => $postCode,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'zoomlevel' => $zoomLevel,
                'origin' => $origin,
                'originid' => $originId,
                'wholeaddress' => $wholeAddress,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 20: " . $e;
            echo $return;
        }
    }
}

$startTimes = $response['startTimes'];
if (count($startTimes) > 0) {
    for ($s=0; $s < count($startTimes); $s++) { 
        $starttimesid = $startTimes[$s]['id'];
        $label = $startTimes[$s]['label'];
        $hour = $startTimes[$s]['hour'];
        $minute = $startTimes[$s]['minute'];
        $overrideTimeWhenPickup = $startTimes[$s]['overrideTimeWhenPickup'];
        $pickupHour = $startTimes[$s]['pickupHour'];
        $pickupMinute = $startTimes[$s]['pickupMinute'];
        $durationType = $startTimes[$s]['durationType'];
        $voucherPickupMsg = $startTimes[$s]['voucherPickupMsg'];
        $externalId = $startTimes[$s]['externalId'];
        $duration = $startTimes[$s]['duration'];
        $durationMinutes = $startTimes[$s]['durationMinutes'];
        $durationHours = $startTimes[$s]['durationHours'];
        $durationDays = $startTimes[$s]['durationDays'];
        $durationWeeks = $startTimes[$s]['durationWeeks'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('starttimes');
            $insert->values(array(
                'starttimesid' => $starttimesid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'label' => $label,
                'hour' => $hour,
                'minute' => $minute,
                'overridetimewhenpickup' => $overrideTimeWhenPickup,
                'pickuphour' => $pickupHour,
                'pickupminute' => $pickupMinute,
                'durationtype' => $durationType,
                'voucherpickupmsg' => $voucherPickupMsg,
                'externalid' => $externalId,
                'duration' => $duration,
                'durationminutes' => $durationMinutes,
                'durationhours' => $durationHours,
                'durationdays' => $durationDays,
                'durationweeks' => $durationWeeks,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 21: " . $e;
            echo $return;
        }

        $flags = $startTimes[$s]['flags'];
        if (count($flags) > 0) {
            for ($sAux=0; $sAux < count($flags); $sAux++) { 
                $flags = $flags[$sAux];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('flags_starttimes');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'pricing' => $pricing,
                        'starttimesid' => $starttimesid
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERROR 22: " . $e;
                    echo $return;
                }
            }
        }
    }
}

$bookableExtras = $response['bookableExtras'];
if (count($bookableExtras) > 0) {
    for ($b=0; $b < count($bookableExtras); $b++) { 
        $bookableextrasid = $bookableExtras[$b]['id'];
        $externalId = $bookableExtras[$b]['externalId'];
        $title = $bookableExtras[$b]['title'];
        $information = $bookableExtras[$b]['information'];
        $included = $bookableExtras[$b]['included'];
        $free = $bookableExtras[$b]['free'];
        $productGroupId = $bookableExtras[$b]['productGroupId'];
        $pricingType = $bookableExtras[$b]['pricingType'];
        $pricingTypeLabel = $bookableExtras[$b]['pricingTypeLabel'];
        $price = $bookableExtras[$b]['price'];
        $increasesCapacity = $bookableExtras[$b]['increasesCapacity'];
        $maxPerBooking = $bookableExtras[$b]['maxPerBooking'];
        $limitByPax = $bookableExtras[$b]['limitByPax'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('bookableextras');
            $insert->values(array(
                'bookableextrasid' => $bookableextrasid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'externalid' => $externalId,
                'title' => $title,
                'information' => $information,
                'included' => $included,
                'free' => $free,
                'productgroupid' => $productGroupId,
                'pricingtype' => $pricingType,
                'pricingtypelabel' => $pricingTypeLabel,
                'price' => $price,
                'increasescpacity' => $increasesCapacity,
                'maxperbooking' => $maxPerBooking,
                'limitbypax' => $limitByPax,
                'activityid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 23: " . $e;
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