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
echo "COMECOU ROOMS ACCOMMODATION<br/>";
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
$path = "/accommodation.json/380/rooms";

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
$client->setUri($url . '/accommodation.json/380/rooms');
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
$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
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
//$tagFilters = $response['tagFilters'];

try {

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('accommodation');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'tookinmillis' => $tookInMillis,
        'totalhits' => $totalHits
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

//tagFacets
$tagFacets = $response['tagFacets'];
for ($t=0; $t < count($tagFacets); $t++) { 
    $tagFacetsname = $tagFacets[$t]['name'];
    $tagFacetstitle = $tagFacets[$t]['title'];
    $tagFacetsmultipleSelection = $tagFacets[$t]['multipleSelection'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('tagFacets_accommodation');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'tagfacetsname' => $tagFacetsname,
            'tagfacetstitle' => $tagFacetstitle,
            'tagfacetsmultipleselection' => $tagFacetsmultipleSelection
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect(); 
    
    } catch (\Exception $e) {
        echo $return;
        echo "ERROR 1-1: " . $e;
        echo $return;
    }

    $entries = $tagFacets[$t]['entries'];
    if (count($entries) > 0) {
        for ($i=0; $i < count($entries); $i++) { 
            $title = $entries[$i]['title'];
            $term = $entries[$i]['term'];
            $count = $entries[$i]['count'];
            $flags = $entries[$i]['flags'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('entries_tagFacetsAcc');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'title' => $title,
                    'term' => $term,
                    'count' => $count
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


    $flags = $tagFacets[$t]['flags'];
    $sortedEntries = $tagFacets[$t]['sortedEntries'];
    if (count($sortedEntries) > 0) {
        for ($j=0; $j < count($sortedEntries); $j++) { 
            $title = $sortedEntries[$j]['title'];
            $term = $sortedEntries[$j]['term'];
            $count = $sortedEntries[$j]['count'];
            $flags = $sortedEntries[$j]['flags'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('sortedEntries_tagFacetsAcc');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'title' => $title,
                    'term' => $term,
                    'count' => $count
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
}

//termFacets
$termFacets = $response['termFacets'];

$supplier = $termFacets['supplier'];
$suppliername = $supplier['name'];
$suppliertitle = $supplier['title'];
$suppliermultipleSelection = $supplier['multipleSelection'];

try {

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('supplier_accommodation');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'suppliername' => $suppliername,
        'suppliertitle' => $suppliertitle,
        'suppliermultipleSelection' => $suppliermultipleSelection
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

$entries = $supplier['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('entries_supplierAcc');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'title' => $title,
                'term' => $term,
                'count' => $count
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


$flags = $supplier['flags'];
$sortedEntries = $supplier['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('sortedEntries_supplierAcc');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'title' => $title,
                'term' => $term,
                'count' => $count
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
    }
}

//tagFacetHierarchy
$tagFacetHierarchy = $response['tagFacetHierarchy'];

//items
$lang = "";
$items = $response['items'];
if (count($items) > 0) {
    for ($z=0; $z < count($items); $z++) { 
        $id = $items[$z]['id'];
        $externalId = $items[$z]['externalId'];
        $productGroupId = $items[$z]['productGroupId'];
        $title = $items[$z]['title'];
        $summary = $items[$z]['summary'];
        $excerpt = $items[$z]['excerpt'];
        $box = $items[$z]['box'];
        $inventoryLocal = $items[$z]['inventoryLocal'];
        $boxedProductId = $items[$z]['boxedProductId'];
        $boxedSupplierId = $items['boxedSupplierId'];
        $reviewRating = $items[$z]['reviewRating'];
        $reviewCount = $items[$z]['reviewCount'];
        $baseLanguage = $items[$z]['baseLanguage'];

        $location = $items[$z]['location'];
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
        

        $vendor = $items[$z]['vendor'];
        $vendorid = $vendor['id'];
        $vendortitle = $vendor['title'];
        $vendorexternalId = $vendor['externalId'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('items_accommodation');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'itemsid' => $id,
                'externalid' => $externalId,
                'productgroupid' => $productGroupId,
                'title' => $title,
                'summary' => $summary,
                'excerpt' => $excerpt,
                'box' => $box,
                'inventorylocal' => $inventoryLocal,
                'boxedproductid' => $boxedProductId,
                'boxedsupplierid' => $boxedSupplierId,
                'reviewrating' => $reviewRating,
                'reviewcount' => $reviewCount,
                'baselanguage' => $baseLanguage,
                'address' => $address,
                'city' => $city,
                'countrycode' => $countryCode,
                'postcode' => $postCode,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'zoomLevel' => $zoomLevel,
                'origin' => $origin,
                'originid' => $originId,
                'wholeaddress' => $wholeAddress,
                'vendorid' => $vendorid,
                'vendortitle' => $vendortitle,
                'vendorexternalid' => $vendorexternalId
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


        $languages = $items[$z]['languages'];
        if (count($languages) > 0) {
            for ($i=0; $i < count($languages); $i++) { 
                $lang = $languages[$i];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('languages_accommodation');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'language' => $lang
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
            }
        }

        $customFields = $items[$z]['customFields'];
        if (count($customFields) > 0) {
            for ($j=0; $j < count($customFields); $j++) { 
                $type = $customFields[$j]['type'];
                $code = $customFields[$j]['code'];
                $title = $customFields[$j]['title'];
                $inputFieldId = $customFields[$j]['inputFieldId'];
                $value = $customFields[$j]['value'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('customFields_accommodation');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'type' => $type,
                        'code' => $code,
                        'title' => $title,
                        'inputfieldid' => $inputFieldId,
                        'value' => $value
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

        $places = $items[$z]['places'];
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

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('places_accommodation');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'title' => $title,
                        'address' => $address,
                        'city' => $city,
                        'countrycode' => $countryCode,
                        'postcode' => $postCode,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'zoomlevel' => $zoomLevel,
                        'origin' => $origin,
                        'originid' => $originId,
                        'wholeaddress' => $wholeAddress
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

        $photos = $items[$z]['photos'];
        if (count($photos) > 0) {
            for ($p=0; $p < count($photos); $p++) { 
                $id = $photos[$p]['id'];
                $originalUrl = $photos[$p]['originalUrl'];
                $description = $photos[$p]['description'];
                $alternateText = $photos[$p]['alternateText'];
                $height = $photos[$p]['height'];
                $width = $photos[$p]['width'];
                $fileName = $keyPhoto['fileName'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('photos_accommodation');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'originalurl' => $originalUrl,
                        'description' => $description,
                        'alternatetext' => $alternateText,
                        'height' => $height,
                        'width' => $width,
                        'filename' => $fileName
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

                $derived = $photos[$p]['derived'];
                if (count($derived) > 0) {
                    for ($d=0; $d < count($derived); $d++) { 
                        $name = $derived[$d]['name'];
                        $url = $derived[$d]['url'];
                        $cleanUrl = $derived[$d]['cleanUrl'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('derived_photosAcc');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $name,
                                'url' => $url,
                                'cleanurl' => $cleanUrl
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect(); 
                        
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERROR 27: " . $e;
                            echo $return;
                        }
                    }
                }
            }
        }

        $fields = $items[$z]['fields'];
        $rating = $fields['rating'];
        $distanceFromLocation = $fields['distanceFromLocation'];
        $name = $distanceFromLocation['name'];
        $metadataField = $distanceFromLocation['metadataField'];
        $value = $distanceFromLocation['value'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('fields_accommodation');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'rating' => $rating,
                'name' => $name,
                'metadatafield' => $metadataField,
                'value' => $value
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect(); 
        
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR 28: " . $e;
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