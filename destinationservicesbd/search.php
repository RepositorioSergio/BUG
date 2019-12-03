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
echo "COMECOU SEARCH<br/>";
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
$method = "POST";
$path = "/activity.json/search?currency=USD&lang=EN";

$word = $date . "" . $accessKey . "" . $method . "" . $path;
$signature = hash_hmac("sha1", $word, $secretKey,true);
$signature = base64_encode($signature);

$raw = '{
    "page": 1,
    "pageSize": 20
  }
  ';

$url = "https://api.bokun.io";

$headers = array(
    'Accept: application/json',
    'Content-Type: application/json;charset=UTF-8',
    'X-Bokun-Date: ' . $date,
    'X-Bokun-AccessKey: ' . $accessKey,
    'X-Bokun-Signature: ' . $signature,
    'Content-Length: ' . strlen($raw)
); 

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url . '/activity.json/search?currency=USD&lang=EN');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $return;
echo $response;
echo $return;

$response = json_decode($response, true);

//die();
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

//tagFacets
$tagFacets = $response['tagFacets'];
$tagFacetsname = $tagFacets['name'];
$tagFacetstitle = $tagFacets['title'];
$tagFacetsmultipleSelection = $tagFacets['multipleSelection'];

$entries = $tagFacets['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('entries_tagFacets');
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


$flags = $tagFacets['flags'];
$sortedEntries = $tagFacets['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('sortedEntries_tagFacets');
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

//termFacets
$termFacets = $response['termFacets'];
$difficulty = $termFacets['difficulty'];
$difficultyname = $difficulty['name'];
$difficultytitle = $difficulty['title'];
$difficultymultipleSelection = $difficulty['multipleSelection'];

$entries = $difficulty['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('entries_difficulty');
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
            echo "ERROR 4: " . $e;
            echo $return;
        }
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

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('sortedEntries_difficulty');
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
            echo "ERROR 5: " . $e;
            echo $return;
        }
    }
}


$country = $termFacets['country'];
$countryname = $country['name'];
$countrytitle = $country['title'];
$countrymultipleSelection = $country['multipleSelection'];

$entries = $country['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('entries_country');
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
            echo "ERROR 6: " . $e;
            echo $return;
        }
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

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('sortedEntries_country');
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
            echo "ERROR 7: " . $e;
            echo $return;
        }
    }
}

$city = $termFacets['city'];
$cityname = $city['name'];
$citytitle = $city['title'];
$citymultipleSelection = $city['multipleSelection'];

$entries = $city['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('entries_city');
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
            echo "ERROR 8: " . $e;
            echo $return;
        }
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

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('sortedEntries_city');
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
            echo "ERROR 9: " . $e;
            echo $return;
        }
    }
}

$supplier = $termFacets['supplier'];
$suppliername = $supplier['name'];
$suppliertitle = $supplier['title'];
$suppliermultipleSelection = $supplier['multipleSelection'];

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
            $insert->into('entries_supplier');
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
            $insert->into('sortedEntries_supplier');
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

$activityAttributes = $termFacets['activityAttributes'];
$aaname = $activityAttributes['name'];
$aatitle = $activityAttributes['title'];
$aamultipleSelection = $activityAttributes['multipleSelection'];

$entries = $activityAttributes['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('entries_activityAttributes');
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
            echo "ERROR 12: " . $e;
            echo $return;
        }
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

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('sortedEntries_activityAttributes');
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
            echo "ERROR 13: " . $e;
            echo $return;
        }
    }
}

$guidanceLanguages = $termFacets['guidanceLanguages'];
$glname = $guidanceLanguages['name'];
$gltitle = $guidanceLanguages['title'];
$glmultipleSelection = $guidanceLanguages['multipleSelection'];

$entries = $guidanceLanguages['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('entries_guidanceLanguages');
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
            echo "ERROR 14: " . $e;
            echo $return;
        }
    }
}


$flags = $guidanceLanguages['flags'];
$sortedEntries = $guidanceLanguages['sortedEntries'];
if (count($sortedEntries) > 0) {
    for ($j=0; $j < count($sortedEntries); $j++) { 
        $title = $sortedEntries[$j]['title'];
        $term = $sortedEntries[$j]['term'];
        $count = $sortedEntries[$j]['count'];
        $flags = $sortedEntries[$j]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('sortedEntries_guidanceLanguages');
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
            echo "ERROR 15: " . $e;
            echo $return;
        }
    }
}

$activityType = $termFacets['activityType'];
$atname = $activityType['name'];
$attitle = $activityType['title'];
$atmultipleSelection = $activityType['multipleSelection'];

$entries = $activityType['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('entries_activityType');
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
            echo "ERROR 16: " . $e;
            echo $return;
        }
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

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('sortedEntries_activityType');
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
            echo "ERROR 17: " . $e;
            echo $return;
        }
    }
}

$activityCategories = $termFacets['activityCategories'];
$acname = $activityCategories['name'];
$actitle = $activityCategories['title'];
$acmultipleSelection = $activityCategories['multipleSelection'];

$entries = $activityCategories['entries'];
if (count($entries) > 0) {
    for ($i=0; $i < count($entries); $i++) { 
        $title = $entries[$i]['title'];
        $term = $entries[$i]['term'];
        $count = $entries[$i]['count'];
        $flags = $entries[$i]['flags'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('entries_activityCategories');
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
            echo "ERROR 18: " . $e;
            echo $return;
        }
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

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('sortedEntries_activityCategories');
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
            echo "ERROR 19: " . $e;
            echo $return;
        }
    }
}

try {

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('activities');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'tookInmillis' => $tookInMillis,
        'totalhits' => $totalHits,
        'tagfacetsname' => $tagFacetsname,
        'tagfacetstitle' => $tagFacetstitle,
        'tagfacetsmultipleselection' => $tagFacetsmultipleSelection,
        'difficultyname' => $difficultyname,
        'difficultytitle' => $difficultytitle,
        'difficultymultipleSelection' => $difficultymultipleSelection,
        'countryname' => $countryname,
        'countrytitle' => $countrytitle,
        'countrymultipleSelection' => $countrymultipleSelection,
        'cityname' => $cityname,
        'citytitle' => $citytitle,
        'citymultipleSelection' => $citymultipleSelection,
        'suppliername' => $suppliername,
        'suppliertitle' => $suppliertitle,
        'suppliermultipleSelection' => $suppliermultipleSelection,
        'aaname' => $aaname,
        'aatitle' => $aatitle,
        'aamultipleSelection' => $aamultipleSelection,
        'glname' => $glname,
        'gltitle' => $gltitle,
        'glmultipleSelection' => $glmultipleSelection,
        'atname' => $atname,
        'attitle' => $attitle,
        'atmultipleSelection' => $atmultipleSelection,
        'acname' => $acname,
        'actitle' => $actitle,
        'acmultipleSelection' => $acmultipleSelection
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

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('items_activities');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'itemsid' => $id,
                'externalid' => $externalId,
                'productgroupId' => $productGroupId,
                'title' => $title,
                'summary' => $summary,
                'excerpt' => $excerpt,
                'price' => $price,
                'box' => $box,
                'inventorylocal' => $inventoryLocal,
                'boxedproductid' => $boxedProductId,
                'boxedsupplierid' => $boxedSupplierId,
                'reviewrating' => $reviewRating,
                'reviewcount' => $reviewCount,
                'baselanguage' => $baseLanguage,
                'country' => $country,
                'name' => $name,
                'googleplacecountry' => $googlePlacecountry,
                'googleplacecountrycode' => $googlePlacecountryCode,
                'googleplacecity' => $googlePlacecity,
                'googleplacecitycode' => $googlePlacecityCode,
                'lat' => $lat,
                'lng' => $lng,
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


        $languages = $items['languages'];
        if (count($languages) > 0) {
            for ($i=0; $i < count($languages); $i++) { 
                $lang = $languages[$i];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('languages_activities');
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

        $customFields = $items['customFields'];
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
                    $insert->into('customFields_activities');
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

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('places_activities');
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

        $keyPhoto = $items['keyPhoto'];
        $id = $keyPhoto['id'];
        $originalUrl = $keyPhoto['originalUrl'];
        $description = $keyPhoto['description'];
        $alternateText = $keyPhoto['alternateText'];
        $height = $keyPhoto['height'];
        $width = $keyPhoto['width'];
        $fileName = $keyPhoto['fileName'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('keyPhoto_activities');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'keyphotoid' => $id,
                'originalurl' => $originalUrl,
                'description' => $description,
                'alternatetext' => $alternateText,
                'height' => $height,
                'width' => $width,
                'fileName' => $fileName
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
                        'cleanurl' => $cleanUrl
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

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('photos_activities');
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
                            $insert->into('derived_photos');
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

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('fields_activities');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'durationhours' => $durationHours,
                'durationweeks' => $durationWeeks,
                'durationtext' => $durationText,
                'combocomponentsinventory' => $comboComponentsInventory,
                'bookingcutoffdays' => $bookingCutoffDays,
                'meetingtype' => $meetingType,
                'durationdays' => $durationDays,
                'durationminutes' => $durationMinutes,
                'daybasedavailability' => $dayBasedAvailability,
                'bookingcutoffminutes' => $bookingCutoffMinutes,
                'selectfromdayoptions' => $selectFromDayOptions,
                'bookingcutoffhours' => $bookingCutoffHours,
                'comboactivity' => $comboActivity,
                'bookingcutoffweeks' => $bookingCutoffWeeks
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