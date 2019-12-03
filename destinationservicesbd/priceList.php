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
echo "COMECOU PRICE LIST<br/>";
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
$path = "/activity.json/35726/price-list";

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
$client->setUri($url . '/activity.json/35726/price-list');
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
 
$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$activityId = $response['activityId'];
$isPriceConverted = $response['isPriceConverted'];
$defaultCurrency = $response['defaultCurrency'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('pricelist');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'activityid' => $activityId,
        'ispriceconverted' => $isPriceConverted,
        'defaultcurrency' => $defaultCurrency
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

$pricesByDateRange = $response['pricesByDateRange'];
for ($k = 0; $k < count($pricesByDateRange); $k ++) {
    $from = $pricesByDateRange[$k]['from'];
    $to = $pricesByDateRange[$k]['to'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('pricesByDateRange_pricelist');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'from' => $from,
            'to' => $to,
            'activityid' => $activityId
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

    $rates = $pricesByDateRange[$k]['rates'];
    for ($i=0; $i < count($rates); $i++) { 
        $rateId = $rates[$i]['rateId'];
        $title = $rates[$i]['title'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('rates_pricelist');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'rateid' => $rateId,
                'title' => $title,
                'activityid' => $activityId
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

        $passengers = $rates[$i]['passengers'];
        for ($j=0; $j < count($passengers); $j++) { 
            $pricingCategoryId = $passengers[$j]['pricingCategoryId'];
            $title = $passengers[$j]['title'];
            $ticketCategory = $passengers[$j]['ticketCategory'];

            $price = $passengers[$j]['price'];
            $currency = $price['currency'];
            $amount = $price['amount'];
            $ofWhichTax = $price['ofWhichTax'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('passengers_pricelist');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'pricingcategoryid' => $pricingCategoryId,
                    'title' => $title,
                    'ticketcategory' => $ticketCategory,
                    'currency' => $currency,
                    'amount' => $amount,
                    'ofwhichtax' => $ofWhichTax,
                    'rateid' => $rateId
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
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>