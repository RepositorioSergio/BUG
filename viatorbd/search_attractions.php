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

$url = 'https://viatorapi.viator.com/service/search/attractions';
$raw = '{
    "destId": 684,
    "topX": "1-3",
    "seoType": "ATTRACTION",
    "sortOrder": "SEO_PUBLISHED_DATE_D"
    }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'Accept' => 'application/json;version=2.0',
    'Accept-Language' => 'en-US',
    'exp-api-key' => '5364bbaf-e4f7-4727-9e91-317e794dfbaa'
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
        $webURL = $data[$i]['webURL'];
        $pageUrlName = $data[$i]['pageUrlName'];
        $primaryDestinationUrlName = $data[$i]['primaryDestinationUrlName'];
        $publishedDate = $data[$i]['publishedDate'];
        $panoramaCount = $data[$i]['panoramaCount'];
        $userName = $data[$i]['userName'];
        $keywordCount = $data[$i]['keywordCount'];
        $showReviews = $data[$i]['showReviews'];
        $tabTitle = $data[$i]['tabTitle'];
        $descriptionIntro = $data[$i]['descriptionIntro'];
        $reviewCount = $data[$i]['reviewCount'];
        $seoType = $data[$i]['seoType'];
        $pageTitle = $data[$i]['pageTitle'];
        $editorsPick = $data[$i]['editorsPick'];
        $showPhotos = $data[$i]['showPhotos'];
        $descriptionText = $data[$i]['descriptionText'];
        $overviewSummary = $data[$i]['overviewSummary'];
        $pagePrimaryLanguage = $data[$i]['pagePrimaryLanguage'];
        $attractionLatitude = $data[$i]['attractionLatitude'];
        $attractionLongitude = $data[$i]['attractionLongitude'];
        $attractionStreetAddress = $data[$i]['attractionStreetAddress'];
        $attractionCity = $data[$i]['attractionCity'];
        $attractionState = $data[$i]['attractionState'];
        $destinationId = $data[$i]['destinationId'];
        $thumbnailHiResURL = $data[$i]['thumbnailHiResURL'];
        $photoCount = $data[$i]['photoCount'];
        $primaryDestinationId = $data[$i]['primaryDestinationId'];
        $seoId = $data[$i]['seoId'];
        $productCount = $data[$i]['productCount'];
        $primaryDestinationName = $data[$i]['primaryDestinationName'];
        $thumbnailURL = $data[$i]['thumbnailURL'];
        $rating = $data[$i]['rating'];
        $title = $data[$i]['title'];
        $keywords = $data[$i]['keywords'];
        if (count($keywords) > 0) {
            for ($j=0; $j < count($keywords); $j++) { 
                $keywordId = $keywords[$j]['keywordId'];
                $keyword = $keywords[$j]['keyword'];
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