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
// Start
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enableabreupackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_abreu = $affiliate_id;
} else {
    $affiliate_id_abreu = 0;
}
// echo "<br/> affiliate_id_abreu " . $affiliate_id_abreu;
$sql = "select value from settings where name='abreupackagesuser' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagesuser = $row_settings['value'];
}
// echo "<br/> abreupackagesuser " . $abreupackagesuser;
$sql = "select value from settings where name='abreupackagespassword' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagespassword = base64_decode($row_settings['value']);
}
// echo "<br/> abreupackagespassword " . $abreupackagespassword;
$sql = "select value from settings where name='abreupackagesserviceURL' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagesserviceURL = $row_settings['value'];
}
// echo "<br/> abreupackagesserviceURL " . $abreupackagesserviceURL;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$raw = '{      "username":"' . $abreupackagesuser . '",   "password": "' . $abreupackagespassword . '",        "language": "PT" }';
echo $return;
echo $raw;
echo $return;

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'X-Powered-By' => 'Zend Framework',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/json'
));
$client->setUri($abreupackagesserviceURL . 'CircuitDetails/GetStaticCache');
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
$response = substr($response, strpos($response, "["));
echo $return;
echo $response;
echo $return;

$response = json_decode($response, true);

if ($response === false || $response === null) {
    echo $return;
    echo "NOT DECODE";
    echo $return;
}
die();
if (json_last_error() == 0) {
    echo '- Nao houve erro! O parsing foi perfeito';
} else {
    echo 'Erro!<br/>';
    switch (json_last_error()) {
        
        case JSON_ERROR_DEPTH:
            echo ' - profundidade maxima excedida';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - state mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Caracter de controle encontrado';
            break;
        case JSON_ERROR_SYNTAX:
            echo ' - Erro de sintaxe! String JSON mal-formada!';
            break;
        case JSON_ERROR_UTF8:
            echo ' - Erro na codificação UTF-8';
            break;
        default:
            echo ' – Erro desconhecido';
            break;
    }
}

/*
 * echo "<xmp>";
 * var_dump($response);
 * echo "</xmp>";
 */

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

echo $return;
echo "PASSOU AQUI " . count($response);
echo $return;
for ($k = 0; $k < count($response); $k ++) {
    $circuitDetailsId = $response[$k]['circuitDetailsId'];
    echo $return;
    echo $circuitDetailsId;
    echo $return;
    $circuitCode = $response[$k]['circuitCode'];
    $circuitType = $response[$k]['circuitType'];
    $name = $response[$k]['name'];
    $thumbnail = $response[$k]['thumbnail'];
    $description = $response[$k]['description'];
    $details = $response[$k]['details'];
    $circuitPromotionCode = $response[$k]['circuitPromotionCode'];
    $duration = $response[$k]['duration'];
    $included = $response[$k]['included'];
    $notIncluded = $response[$k]['notIncluded'];
    $flightsInfo = $response[$k]['flightsInfo'];
    $salesConditions = $response[$k]['salesConditions'];
    $archives = $response[$k]['archives'];
    
    try {
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('cache');
        $select->where(array(
            'circuitDetailsId' => $circuitDetailsId
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $result->buffer();
        $customers = array();
        if ($result->valid()) {
            $data = $result->current();
            $id = (int) $data['circuitDetailsId'];
            if ($id > 0) {
                $sql = new Sql($db);
                $data = array(
                    'circuitDetailsId' => $circuitDetailsId,
                    'datetime_created' => time(),
                    'datetime_updated' => 1,
                    'circuitCode' => $circuitCode,
                    'circuitType' => $circuitType,
                    'name' => $name,
                    'thumbnail' => $thumbnail,
                    'description' => $description,
                    'details' => $details,
                    'circuitPromotionCode' => $circuitPromotionCode,
                    'duration' => $duration,
                    'included' => $included,
                    'notIncluded' => $notIncluded,
                    'flightsInfo' => $flightsInfo,
                    'salesConditions' => $salesConditions,
                    'archives' => $archives
                );
                $where['circuitDetailsId = ?'] = $circuitDetailsId;
                $update = $sql->update('cache', $data, $where);
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('cache');
                $insert->values(array(
                    'circuitDetailsId' => $circuitDetailsId,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'circuitCode' => $circuitCode,
                    'circuitType' => $circuitType,
                    'name' => $name,
                    'thumbnail' => $thumbnail,
                    'description' => $description,
                    'details' => $details,
                    'circuitPromotionCode' => $circuitPromotionCode,
                    'duration' => $duration,
                    'included' => $included,
                    'notIncluded' => $notIncluded,
                    'flightsInfo' => $flightsInfo,
                    'salesConditions' => $salesConditions,
                    'archives' => $archives
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            }
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cache');
            $insert->values(array(
                'circuitDetailsId' => $circuitDetailsId,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'circuitCode' => $circuitCode,
                'circuitType' => $circuitType,
                'name' => $name,
                'thumbnail' => $thumbnail,
                'description' => $description,
                'details' => $details,
                'circuitPromotionCode' => $circuitPromotionCode,
                'duration' => $duration,
                'included' => $included,
                'notIncluded' => $notIncluded,
                'flightsInfo' => $flightsInfo,
                'salesConditions' => $salesConditions,
                'archives' => $archives
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    } catch (\Exception $e) {
        echo $return;
        echo "ERROR CACHE: " . $e;
        echo $return;
    }
    
    // cities
    $cities = $response[$k]['cities'];
    for ($i = 0; $i < count($cities); $i ++) {
        $cityId = $cities[$i]['cityId'];
        $name = $cities[$i]['name'];
        
        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('cache_cities');
            $select->where(array(
                'cityId' => $cityId
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (int) $data['cityId'];
                if ($id > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'cityId' => $cityId,
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'name' => $name,
                        'circuitDetailsId' => $circuitDetailsId
                    );
                    $where['cityId = ?'] = $cityId;
                    $update = $sql->update('cache_cities', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('cache_cities');
                    $insert->values(array(
                        'cityId' => $cityId,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name' => $name,
                        'circuitDetailsId' => $circuitDetailsId
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                }
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('cache_cities');
                $insert->values(array(
                    'cityId' => $cityId,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'name' => $name,
                    'circuitDetailsId' => $circuitDetailsId
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            }
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR CITIES: " . $e;
            echo $return;
        }
    }
    
    // fromPrices
    $fromPrices = $response[$k]['fromPrices'];
    for ($i = 0; $i < count($fromPrices); $i ++) {
        $optionName = $fromPrices[$i]['optionName'];
        $optionFromPrice = $fromPrices[$i]['optionFromPrice'];
        $optionFromPriceCurrency = $fromPrices[$i]['optionFromPriceCurrency'];
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cache_fromPrices');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'optionName' => $optionName,
                'optionFromPrice' => $optionFromPrice,
                'optionFromPriceCurrency' => $optionFromPriceCurrency,
                'circuitDetailsId' => $circuitDetailsId
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR PRICES: " . $e;
            echo $return;
        }
    }
    
    // circuitDepartures
    $circuitDepartures = $response[$k]['circuitDepartures'];
    for ($i = 0; $i < count($circuitDepartures); $i ++) {
        $cityId = $circuitDepartures[$i]['cityId'];
        $cityName = $circuitDepartures[$i]['cityName'];
        $departureDate = $circuitDepartures[$i]['departureDate'];
        $departureEndDate = $circuitDepartures[$i]['departureEndDate'];
        
        try {
            
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cache_circuitDepartures');
            $insert->values(array(
                'cityId' => $cityId,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'cityName' => $cityName,
                'departureDate' => $departureDate,
                'departureEndDate' => $departureEndDate,
                'circuitDetailsId' => $circuitDetailsId
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR circuitDepartures: " . $e;
            echo $return;
        }
    }
    
    // itinerary
    $itinerary = $response[$k]['itinerary'];
    for ($i = 0; $i < count($itinerary); $i ++) {
        $title = $itinerary[$i]['title'];
        $description = $itinerary[$i]['description'];
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cache_itinerary');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'title' => $title,
                'description' => $description,
                'circuitDetailsId' => $circuitDetailsId
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR itinerary: " . $e;
            echo $return;
        }
    }
    
    // expectedHotels
    $hotel = "";
    $expectedHotels = $response[$k]['expectedHotels'];
    for ($i = 0; $i < count($expectedHotels); $i ++) {
        $hotel = $expectedHotels[$i];
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cache_expectedHotels');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'hotel' => $hotel,
                'circuitDetailsId' => $circuitDetailsId
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR expectedHotels: " . $e;
            echo $return;
        }
    }
    
    // images
    $image = "";
    $images = $response[$k]['images'];
    for ($i = 0; $i < count($images); $i ++) {
        $image = $images[$i];
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cache_images');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'image' => $image,
                'circuitDetailsId' => $circuitDetailsId
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERROR IMAGES: " . $e;
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