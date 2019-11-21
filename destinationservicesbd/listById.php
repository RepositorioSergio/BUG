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
echo "COMECOU LIST BY ID<br/>";
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


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
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
$path = "/activity.json/list-by-id?ids=35726&currency=USD&lang=EN";

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
    'Accept: application/json',
    'Content-Type: application/json;charset=UTF-8',
    'X-Bokun-Date: ' . $date,
    'X-Bokun-AccessKey: ' . $accessKey,
    'X-Bokun-Signature: ' . $signature,
    'Content-Length: ' . strlen($raw)
));
$client->setUri($url . '/activity.json/list-by-id?ids=35726&currency=USD&lang=EN');
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

echo $return;
echo $response;
echo $return;

$response = json_decode($response, true);
 
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$tookInMillis = $response['tookInMillis'];
$totalHits = $response['totalHits'];
$tagFilters = $response['tagFilters'];

//tagFacets
$tagFacets = $response['tagFacets'];
if (count($tagFacets) > 0) {
    for ($x=0; $x < count($tagFacets); $x++) { 
        $name = $tagFacets[$x]['name'];
        $title = $tagFacets[$x]['title'];
        $multipleSelection = $tagFacets[$x]['multipleSelection'];

        $entries = $tagFacets[$x]['entries'];
        for ($i=0; $i < count($entries); $i++) { 
            $title = $entries[$i]['title'];
            $term = $entries[$i]['term'];
            $count = $entries[$i]['count'];
            $flags = $entries[$i]['flags'];
        }


        $flags = $tagFacets[$x]['flags'];
        $sortedEntries = $tagFacets[$x]['sortedEntries'];
        for ($j=0; $j < count($sortedEntries); $j++) { 
            $title = $sortedEntries[$j]['title'];
            $term = $sortedEntries[$j]['term'];
            $count = $sortedEntries[$j]['count'];
            $flags = $sortedEntries[$j]['flags'];
        }
    }
}

//termFacets
$termFacets = $response['termFacets'];
$difficulty = $termFacets['difficulty'];
$name = $difficulty['name'];
$title = $difficulty['title'];
$multipleSelection = $difficulty['multipleSelection'];

$entries = $difficulty['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];
    }
}


$flags = $difficulty['flags'];
$sortedEntries = $difficulty['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];
    }
}


$country = $termFacets['country'];
$name = $country['name'];
$title = $country['title'];
$multipleSelection = $country['multipleSelection'];

$entries = $country['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];
    }
}


$flags = $country['flags'];
$sortedEntries = $country['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];
    }
}

$city = $termFacets['city'];
$name = $city['name'];
$title = $city['title'];
$multipleSelection = $city['multipleSelection'];

$entries = $city['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];
    }
}


$flags = $city['flags'];
$sortedEntries = $city['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];
    }
}

$supplier = $termFacets['supplier'];
$name = $supplier['name'];
$title = $supplier['title'];
$multipleSelection = $supplier['multipleSelection'];

$entries = $supplier['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];
    }
}


$flags = $supplier['flags'];
$sortedEntries = $supplier['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];
    }
}

$activityAttributes = $termFacets['activityAttributes'];
$name = $activityAttributes['name'];
$title = $activityAttributes['title'];
$multipleSelection = $activityAttributes['multipleSelection'];

$entries = $activityAttributes['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];
    }
}


$flags = $activityAttributes['flags'];
$sortedEntries = $activityAttributes['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];
    }
}

$guidanceLanguages = $termFacets['guidanceLanguages'];
$name = $guidanceLanguages['name'];
$title = $guidanceLanguages['title'];
$multipleSelection = $guidanceLanguages['multipleSelection'];

$entries = $guidanceLanguages['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];
    }
}


$flags = $guidanceLanguages['flags'];
$sortedEntries = $city['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];
    }
}

$activityType = $termFacets['activityType'];
$name = $activityType['name'];
$title = $activityType['title'];
$multipleSelection = $activityType['multipleSelection'];

$entries = $activityType['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];
    }
}


$flags = $activityType['flags'];
$sortedEntries = $activityType['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];
    }
}

$activityCategories = $termFacets['activityCategories'];
$name = $activityCategories['name'];
$title = $activityCategories['title'];
$multipleSelection = $activityCategories['multipleSelection'];

$entries = $activityCategories['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];
    }
}


$flags = $activityCategories['flags'];
$sortedEntries = $activityCategories['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];
    }
}

//tagFacetHierarchy
$tagFacetHierarchy = $response['tagFacetHierarchy'];

//items
$lang = "";
$items = $response['items'];
if (count($items) > 0) {
    for ($z=0; $z < count($items); $z++) { 
        $id = $items['id'];
        $externalId = $items['externalId'];
        $productGroupId = $items['productGroupId'];
        $title = $items['title'];
        $summary = $items['summary'];
        $excerpt = $items['excerpt'];
        $price = $items['price'];
        $box = $items['box'];
        $inventoryLocal = $items['inventoryLocal'];
        $boxedProductId = $items['boxedProductId'];
        $boxedSupplierId = $items['boxedSupplierId'];
        $reviewRating = $items['reviewRating'];
        $reviewCount = $items['reviewCount'];
        $baseLanguage = $items['baseLanguage'];

        $locationCode = $items['locationCode'];
        $country = $locationCode['country'];
        $location = $locationCode['location'];
        $name = $locationCode['name'];

        $googlePlace = $items['googlePlace'];
        $googlePlacecountry = $googlePlace['country'];
        $googlePlacecountryCode = $googlePlace['countryCode'];
        $googlePlacecity = $googlePlace['city'];
        $googlePlacecityCode = $googlePlace['cityCode'];
        $geoLocationCenter = $googlePlace['geoLocationCenter'];
        $lat = $geoLocationCenter['lat'];
        $lng = $geoLocationCenter['lng'];
        

        $vendor = $items['vendor'];
        $vendorid = $vendor['id'];
        $vendortitle = $vendor['title'];
        $vendorexternalId = $vendor['externalId'];

        $languages = $items['languages'];
        if (count($languages) > 0) {
            for ($i=0; $i < count($languages); $i++) { 
                $lang = $languages[$i];
            }
        }

        $customFields = $items['customFields'];
        if (count($customFields) > 0) {
            for ($j=0; $j < count($customFields); $j++) { 
                $type = $customFields[$j]['type'];
                $code = $customFields[$j]['code'];
                $title = $customFields[$j]['title'];
                $inputFieldId = $customFields[$j]['inputFieldId'];
                $value = $customFields[$j]['value'];//text
            }
        }

        $places = $items['places'];
        if (count($places) > 0) {
            for ($k=0; $k < count($places); $k++) { 
                $id = $places[$k]['id'];
                $title = $places[$k]['title'];

                $location = $places[$k]['location'];
                $address = $location['address'];
                $city = $location['city'];
                $countryCode = $location['countryCode'];
                $postCode = $location['postCode'];
                $latitude = $location['latitude'];
                $longitude = $location['longitude'];
                $zoomLevel = $location['zoomLevel'];
                $origin = $location['origin'];
                $originId = $location['originId'];
                $wholeAddress = $location['wholeAddress'];
            }
        }

        $keyPhoto = $items['keyPhoto'];
        $id = $keyPhoto['id'];
        $originalUrl = $keyPhoto['originalUrl'];
        $description = $keyPhoto['description'];
        $alternateText = $keyPhoto['alternateText'];
        $height = $keyPhoto['height'];
        $width = $keyPhoto['width'];
        $fileName = $keyPhoto['fileName'];
        $derived = $keyPhoto['derived'];
        if (count($derived) > 0) {
            for ($d=0; $d < count($derived); $d++) { 
                $name = $derived[$d]['name'];
                $url = $derived[$d]['url'];
                $cleanUrl = $derived[$d]['cleanUrl'];
            }
        }

        $photos = $items['photos'];
        if (count($photos) > 0) {
            for ($p=0; $p < count($photos); $p++) { 
                $id = $photos[$p]['id'];
                $originalUrl = $photos[$p]['originalUrl'];
                $description = $photos[$p]['description'];
                $alternateText = $photos[$p]['alternateText'];
                $height = $photos[$p]['height'];
                $width = $photos[$p]['width'];
                $fileName = $keyPhoto['fileName'];
                $derived = $photos[$p]['derived'];
                if (count($derived) > 0) {
                    for ($d=0; $d < count($derived); $d++) { 
                        $name = $derived[$d]['name'];
                        $url = $derived[$d]['url'];
                        $cleanUrl = $derived[$d]['cleanUrl'];
                    }
                }
            }
        }

        $fields = $items['fields'];
        $durationHours = $fields['durationHours'];
        $durationWeeks = $fields['durationWeeks'];
        $durationText = $fields['durationText'];
        $comboComponentsInventory = $fields['comboComponentsInventory'];
        $bookingCutoffDays = $fields['bookingCutoffDays'];
        $meetingType = $fields['meetingType'];
        $durationDays = $fields['durationDays'];
        $durationMinutes = $fields['durationMinutes'];
        $dayBasedAvailability = $fields['dayBasedAvailability'];
        $bookingCutoffMinutes = $fields['bookingCutoffMinutes'];
        $selectFromDayOptions = $fields['selectFromDayOptions'];
        $bookingCutoffHours = $fields['bookingCutoffHours'];
        $comboActivity = $fields['comboActivity'];
        $bookingCutoffWeeks = $fields['bookingCutoffWeeks'];
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>