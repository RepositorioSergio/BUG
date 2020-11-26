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

$url = 'https://viatorapi.viator.com/service/search/freetext';

$raw = '{
    "destId": 684,
    "topX": "1-3",
    "currencyCode": "EUR",
    "text": "helicopter",
    "searchTypes": [
    "PRODUCT",
    "DESTINATION"
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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
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
        $searchType = $data[$i]['searchType'];
        $data2 = $data[$i]['data'];
        $sortOrder = $data2['sortOrder'];
        $webURL = $data2['webURL'];
        $pageUrlName = $data2['pageUrlName'];
        $primaryDestinationUrlName = $data2['primaryDestinationUrlName'];
        $publishedDate = $data2['publishedDate'];
        $panoramaCount = $data2['panoramaCount'];
        $userName = $data2['userName'];
        $seoType = $data2['seoType'];
        $pageTitle = $data2['pageTitle'];
        $tabTitle = $data2['tabTitle'];
        $descriptionIntro = $data2['descriptionIntro'];
        $keywords = $data2['keywords'];
        $reviewCount = $data2['reviewCount'];
        $editorsPick = $data2['editorsPick'];
        $descriptionText = $data2['descriptionText'];
        $keywordCount = $data2['keywordCount'];
        $overviewSummary = $data2['overviewSummary'];
        $pagePrimaryLanguage = $data2['pagePrimaryLanguage'];
        $showPhotos = $data2['showPhotos'];
        $showReviews = $data2['showReviews'];
        $attractionLatitude = $data2['attractionLatitude'];
        $attractionLongitude = $data2['attractionLongitude'];
        $attractionStreetAddress = $data2['attractionStreetAddress'];
        $attractionCity = $data2['attractionCity'];
        $attractionState = $data2['attractionState'];
        $destinationId = $data2['destinationId'];
        $thumbnailHiResURL = $data2['thumbnailHiResURL'];
        $primaryDestinationName = $data2['primaryDestinationName'];
        $thumbnailURL = $data2['thumbnailURL'];
        $photoCount = $data2['photoCount'];
        $primaryDestinationId = $data2['primaryDestinationId'];
        $seoId = $data2['seoId'];
        $productCount = $data2['productCount'];
        $rating = $data2['rating'];
        $title = $data2['title'];
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>