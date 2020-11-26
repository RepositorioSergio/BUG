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

$url = 'https://viatorapi.viator.com/service/products/photos';
$start = 1;
$end = 100;
$available = true;
while ($available == true) {
$url = 'https://api.viator.com/partner/v1/product/photos?code=5010SYDNEY&topX=' . $start . '-' . $end;
$available = false;
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

/* echo $return;
echo $response;
echo $return; */

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
    for ($i = 0; $i < count($data); $i ++) {
        $sortOrder = $data[$i]['sortOrder'];
        $ownerName = $data[$i]['ownerName'];
        $ownerCountry = (string) $data[$i]['ownerCountry'];
        $productTitle = $data[$i]['productTitle'];
        $productUrlName = $data[$i]['productUrlName'];
        $ownerAvatarURL = (string) $data[$i]['ownerAvatarURL'];
        $sslSupported = $data[$i]['sslSupported'] ? 'true' : 'false';
        $timeUploaded = $data[$i]['timeUploaded'];
        $productCode = $data[$i]['productCode'];
        $caption = $data[$i]['caption'];
        $thumbnailURL = $data[$i]['thumbnailURL'];
        $ownerId = $data[$i]['ownerId'];
        $editorsPick = $data[$i]['editorsPick'];
        $photoURL = $data[$i]['photoURL'];
        $photoId = $data[$i]['photoId'];
        $photoHiResURL = $data[$i]['photoHiResURL'];
        $photoMediumResURL = $data[$i]['photoMediumResURL'];
        $title = $data[$i]['title'];
        $available = true;
        echo $return;
        echo $i . " -> " . $photoId;

        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('viator_product_photos');
        $select->where(array(
            'photoid' => $photoId
        ));
        try {
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO 1 : " . $e;
            echo $return;
            echo $select->getSqlString();
            echo $return;
            die();
        }
        $result->buffer();
        $customers = array();
        if ($result->valid()) {
            $dataTmp = $result->current();
            $id = (int) $dataTmp['id'];
            if ($id > 0) {
                $dbUpdate = new \Zend\Db\Adapter\Adapter($config);
                $sql = new Sql($db);
                $select = $sql->update();
                $select->table('viator_product_photos');
                $select->where(array(
                    'id' => $id
                ));
                $select->set(array(
                    'datetime_updated' => time(),
                    'sortorder' => $sortOrder, 
                    'ownername' => $ownerName, 
                    'ownercountry' => $ownerCountry, 
                    'producttitle' => $productTitle, 
                    'producturlname' => $productUrlName, 
                    'owneravatarurl' => $ownerAvatarURL,
                    'sslsupported' => $sslSupported, 
                    'timeuploaded' => $timeUploaded, 
                    'productcode' => $productCode, 
                    'caption' => $caption,
                    'thumbnailurl' => $thumbnailURL, 
                    'ownerid' => $ownerId, 
                    'editorspick' => $editorsPick, 
                    'photourl' => $photoURL,
                    'photohiresurl' => $photoHiResURL, 
                    'photomediumresurl' => $photoMediumResURL, 
                    'title' => $title 
                ));
                try {
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $results = $statement->execute();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 2 : " . $e;
                    echo $return;
                    echo $select->getSqlString();
                    echo $return;
                    die();
                }
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('viator_product_photos');
                $insert->values(array(
                    'photoid' => $photoId,
                    'datetime_updated' => time(),
                    'sortorder' => $sortOrder, 
                    'ownername' => $ownerName, 
                    'ownercountry' => $ownerCountry, 
                    'producttitle' => $productTitle, 
                    'producturlname' => $productUrlName, 
                    'owneravatarurl' => $ownerAvatarURL,
                    'sslsupported' => $sslSupported, 
                    'timeuploaded' => $timeUploaded, 
                    'productcode' => $productCode, 
                    'caption' => $caption,
                    'thumbnailurl' => $thumbnailURL, 
                    'ownerid' => $ownerId, 
                    'editorspick' => $editorsPick, 
                    'photourl' => $photoURL,
                    'photohiresurl' => $photoHiResURL, 
                    'photomediumresurl' => $photoMediumResURL, 
                    'title' => $title 
                ), $insert::VALUES_MERGE);
                try {
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 3 : " . $e;
                    echo $return;
                    echo $insert->getSqlString();
                    echo $return;
                    die();
                }
            }
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('viator_product_photos');
            $insert->values(array(
                'photoid' => $photoId,
                'datetime_updated' => time(),
                'sortorder' => $sortOrder, 
                'ownername' => $ownerName, 
                'ownercountry' => $ownerCountry, 
                'producttitle' => $productTitle, 
                'producturlname' => $productUrlName, 
                'owneravatarurl' => $ownerAvatarURL,
                'sslsupported' => $sslSupported, 
                'timeuploaded' => $timeUploaded, 
                'productcode' => $productCode, 
                'caption' => $caption,
                'thumbnailurl' => $thumbnailURL, 
                'ownerid' => $ownerId, 
                'editorspick' => $editorsPick, 
                'photourl' => $photoURL,
                'photohiresurl' => $photoHiResURL, 
                'photomediumresurl' => $photoMediumResURL, 
                'title' => $title 
            ), $insert::VALUES_MERGE);
            try {
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO 4 : " . $e;
                echo $return;
                echo $insert->getSqlString();
                echo $return;
                var_dump($data);
                die();
            }
        }
    }
}
    $start = $start + 100;
    $end = $end + 100;
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br>Done';
?>