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
echo "COMECOU AVAIL";
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


$config = new \Zend\Config\Config(include '../config/autoload/global.coming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw2 = '{
    "user": "CTM",
    "password": "CTM9632"
    }';


$client2 = new Client();
$client2->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client2->setHeaders(array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Content-length: " . strlen($raw2)
));

$url = 'https://svcext.grupo-pinero.com/co2/pandora-v1';

$client2->setUri($url . '/login');
$client2->setMethod('POST');
$client2->setRawBody($raw2);
$response2 = $client2->send();
if ($response2->isSuccess()) {
$response2 = $response2->getBody();
} else {
$logger = new Logger();
$writer = new Writer\Stream('/srv/www/htdocs/error_log');
$logger->addWriter($writer);
$logger->info($client2->getUri());
$logger->info($response2->getStatusCode() . " - " . $response2->getReasonPhrase());
echo $return;
echo $response2->getStatusCode() . " - " . $response2->getReasonPhrase();
echo $return;
die();
}

$token = $response2;

$raw = '{
    "token":"' . $token . '",
    "language":"ES",
    "destination":"TCI",
    "adults":2,
    "kids":0,
    "babies":0,
    "initialDate":"20190701",
    "finalDate":"20190708"
}';


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Content-length: " . strlen($raw)
));

$url = 'https://svcext.grupo-pinero.com/co2/pandora-v1';

$client->setUri($url . '/excursion/availability');
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

$response = json_decode($response, true);

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.coming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$excursions = $response['excursions'];
for ($i=0; $i < count($excursions); $i++) { 
    $availableToken = $excursions[$i]['availableToken'];
    $code = $excursions[$i]['code'];
    $title = $excursions[$i]['title'];
    $subtitle = $excursions[$i]['subtitle'];
    $url = $excursions[$i]['url'];
    $description = $excursions[$i]['description'];
    $destination = $excursions[$i]['destination'];
    $destinationcode = $destination['code'];
    $delegation = $excursions[$i]['delegation'];
    $recomended = $excursions[$i]['recomended'];
    $paxDistribution = $excursions[$i]['paxDistribution'];
    $adults = $paxDistribution['adults'];
    $kids = $paxDistribution['kids'];
    $babies = $paxDistribution['babies'];

    $price2 = 0;
    $price = $excursions[$i]['price'];
    $currency = $price['currency'];
    $currencyCode = $currency['code'];

    $netPrice = $price['netPrice'];
    $commission = $price['commission'];
    $adultsPrice = $price['adultsPrice'];
    $kidsPrice = $price['kidsPrice'];
    $babiesPrice = $price['babiesPrice'];
    $price2 = $price['price'];

    $price3 = 0;
    $promoPrice = $excursions[$i]['promoPrice'];
    $currency = $promoPrice['currency'];
    $promoPricecurrencyCode = $currency['code'];

    $promoPricenetPrice = $promoPrice['netPrice'];
    $promoPricecommission = $promoPrice['commission'];
    $promoPriceadultsPrice = $promoPrice['adultsPrice'];
    $promoPricekidsPrice = $promoPrice['kidsPrice'];
    $promoPricebabiesPrice = $promoPrice['babiesPrice'];
    $price3 = $promoPrice['price'];

    $promoCode = $excursions[$i]['promoCode'];
    $promoCodecode = $promoCode['code'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('excursionList');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'availableToken' => $availableToken,
            'code' => $code,
            'title' => $title,
            'subtitle' => $subtitle,
            'url' => $url,
            'description' => $description,
            'destinationcode' => $destinationcode,
            'delegation' => $delegation,
            'recomended' => $recomended,
            'adults' => $adults,
            'kids' => $kids,
            'babies' => $babies,
            'currencyCode' => $currencyCode,
            'netPrice' => $netPrice,
            'commission' => $commission,
            'adultsPrice' => $adultsPrice,
            'kidsPrice' => $kidsPrice,
            'babiesPrice' => $babiesPrice,
            'price' => $price2,
            'promoPricecurrencyCode' => $promoPricecurrencyCode,
            'promoPricenet' => $promoPricenetPrice,
            'promoPricecommission' => $promoPricecommission,
            'promoPriceadults' => $promoPriceadultsPrice,
            'promoPricekids' => $promoPricekidsPrice,
            'promoPricebabies' => $promoPricebabiesPrice,
            'promoPrice' => $price3,
            'promoCode' => $promoCodecode
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error LIST: " . $e;
        echo $return;
    }

    $groupInfo = $excursions[$i]['groupInfo'];
    $groupInfocode = $groupInfo['code'];
    $title = $groupInfo['title'];
    $description = $groupInfo['description'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('excursionList_groupInfo');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'groupInfocode' => $groupInfocode,
            'title' => $title,
            'description' => $description,
            'codeExcursion' => $code
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error GROUP: " . $e;
        echo $return;
    }

    $themesList = $groupInfo['themesList'];
    for ($iAux=0; $iAux < count($themesList); $iAux++) { 
        $themesListcode = $themesList[$iAux]['code'];
        $name = $themesList[$iAux]['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('excursionList_themesList');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'code' => $themesListcode,
                'name' => $name,
                'codeExcursion' => $code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error TH: " . $e;
            echo $return;
        }
    }

    $featureList = $groupInfo['featureList'];
    for ($iAux=0; $iAux < count($featureList); $iAux++) { 
        $featureListcode = $featureList[$iAux]['code'];
        $name = $featureList[$iAux]['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('excursionList_featureList');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'code' => $featureListcode,
                'name' => $name,
                'codeExcursion' => $code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error FEA: " . $e;
            echo $return;
        }
    }

    $image = "";
    $imageList = $groupInfo['imageList'];
    for ($iAux=0; $iAux < count($imageList); $iAux++) { 
        $image = $imageList[$iAux];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('excursionList_imageList');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'image' => $image,
                'codeExcursion' => $code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error IMG: " . $e;
            echo $return;
        }
    }

    $datesByMarket = $excursions[$i]['datesByMarket'];
    $RU2 = "";
    $RU = $datesByMarket['RU'];
    if (count($RU) > 0) {
        for ($iAux2=0; $iAux2 < count($RU); $iAux2++) { 
            $RU2 = $RU[$iAux2];
    
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('excursionList_datesByMarketRU');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'RU' => $RU2,
                    'codeExcursion' => $code
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error RU: " . $e;
                echo $return;
            }
        }
    }


    $PT2 = "";
    $PT = $datesByMarket['PT'];
    for ($iAux2=0; $iAux2 < count($PT); $iAux2++) { 
        $PT2 = $PT[$iAux2];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('excursionList_datesByMarketPT');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'PT' => $PT2,
                'codeExcursion' => $code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error PT: " . $e;
            echo $return;
        }
    }

    $EN2 = "";
    $EN = $datesByMarket['EN'];
    for ($iAux2=0; $iAux2 < count($EN); $iAux2++) { 
        $EN2 = $EN[$iAux2];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('excursionList_datesByMarketEN');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'EN' => $EN2,
                'codeExcursion' => $code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error EN: " . $e;
            echo $return;
        }
    }

    $ES2 = "";
    $ES = $datesByMarket['ES'];
    for ($iAux2=0; $iAux2 < count($ES); $iAux2++) { 
        $ES2 = $ES[$iAux2];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('excursionList_datesByMarketES');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'ES' => $ES2,
                'codeExcursion' => $code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error EN: " . $e;
            echo $return;
        }
    }

    $IT2 = "";
    $IT = $datesByMarket['IT'];
    if (count($IT) > 0) {
        for ($iAux2=0; $iAux2 < count($IT); $iAux2++) { 
            $IT2 = $IT[$iAux2];
    
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('excursionList_datesByMarketIT');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'IT' => $IT2,
                    'codeExcursion' => $code
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error IT: " . $e;
                echo $return;
            }
        }
    }

    $FR2 = "";
    $FR = $datesByMarket['FR'];
    if (count($FR) > 0) {
        for ($iAux2=0; $iAux2 < count($FR); $iAux2++) { 
            $FR2 = $FR[$iAux2];
    
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('excursionList_datesByMarketFR');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'FR' => $FR2,
                    'codeExcursion' => $code
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error FR: " . $e;
                echo $return;
            }
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>