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
echo "COMECOU HOTELBEDS";
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
$config = new \Zend\Config\Config(include '../config/autoload/global.hotelbeds.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Api-key: qz8j9xgymx97tmd5srx94mru",
    "X-Signature: fc4f0c9a05bbeec19b4fb6001d2eae8919cc66d60be97bdf95d2f376bac02254",
    "Content-Type: application/json",
    "Accept: application/json",
    "Accept-Encoding: gzip"
));

$client->setUri('https://api.test.hotelbeds.com/transfer-api/1.0/availability/en/from/STATION/ATCH/to/ATLAS/4704/2019-11-28T12:15:11/2019-11-30T08:30:52/2/1/0');
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
if ($response === false || $response === null) {
    echo $return;
    echo "NOT DECODE";
    echo $return;
}

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

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.hotelbeds.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$services = $response['services'];

for ($i = 0; $i < count($services); $i ++) {
    $id = $services[$i]['id'];
    $direction = $services[$i]['direction'];
    $transferType = $services[$i]['transferType'];
    $minPaxCapacity = $services[$i]['minPaxCapacity'];
    $maxPaxCapacity = $services[$i]['maxPaxCapacity'];
    $rateKey = $services[$i]['rateKey'];
    $factsheetId = $services[$i]['factsheetId'];
    echo $return;
    echo "factsheetId: " . $factsheetId;
    echo $return;

    $vehicle = $services[$i]['vehicle'];
    $codevehicle = $vehicle['code'];
    $namevehicle = $vehicle['name'];

    $category = $services[$i]['category'];
    $codecategory = $vehicle['code'];
    $namecategory = $vehicle['name'];

    $pickupInformation = $services[$i]['pickupInformation'];
    $date = $pickupInformation['date'];
    $time = $pickupInformation['time'];
    $minPaxCapacityPickup = $pickupInformation['minPaxCapacity'];
    $maxPaxCapacityPickup = $pickupInformation['maxPaxCapacity'];
    //from
    $from = $pickupInformation['from'];
    $codefrom = $from['code'];
    $descriptionfrom = $from['description'];
    $typefrom = $from['type'];
    //to
    $to = $pickupInformation['to'];
    $codeto = $to['code'];
    $descriptionto = $to['description'];
    $typeto = $to['type'];
    //pickup
    $pickup = $pickupInformation['pickup'];
    $address = $pickup['address'];
    $number = $pickup['number'];
    $town = $pickup['town'];
    $zip = $pickup['zip'];
    $description = $pickup['description'];
    $altitude = $pickup['altitude'];
    $latitude = $pickup['latitude'];
    $longitude = $pickup['longitude'];
    $pickupId = $pickup['pickupId'];
    $stopName = $pickup['stopName'];
    $image = $pickup['image'];
    $checkPickup = $pickup['checkPickup'];
    $mustCheckPickupTime = $checkPickup['mustCheckPickupTime'];
    $url = $checkPickup['url'];
    $hoursBeforeConsulting = $checkPickup['hoursBeforeConsulting'];

    $price = $services[$i]['price'];
    $totalAmount = $price['totalAmount'];
    $netAmount = $price['netAmount'];
    $currencyId = $price['currencyId'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('services');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'direction' => $direction,
            'transferType' => $transferType,
            'minPaxCapacity' => $minPaxCapacity,
            'maxPaxCapacity' => $maxPaxCapacity,
            'rateKey' => $rateKey,
            'factsheetId' => $factsheetId,
            'codevehicle' => $codevehicle,
            'namevehicle' => $namevehicle,
            'codecategory' => $codecategory,
            'namecategory' => $namecategory,
            'date' => $date,
            'time' => $time,
            'minPaxCapacityPickup' => $minPaxCapacityPickup,
            'maxPaxCapacityPickup' => $maxPaxCapacityPickup,
            'codefrom' => $codefrom,
            'descriptionfrom' => $descriptionfrom,
            'typefrom' => $typefrom,
            'codeto' => $codeto,
            'descriptionto' => $descriptionto,
            'typeto' => $typeto,
            'address' => $address,
            'number' => $number,
            'town' => $town,
            'zip' => $zip,
            'description' => $description,
            'altitude' => $altitude,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'pickupId' => $pickupId,
            'stopName' => $stopName,
            'image' => $image,
            'mustCheckPickupTime' => $mustCheckPickupTime,
            'url' => $url,
            'hoursBeforeConsulting' => $hoursBeforeConsulting,
            'totalAmount' => $totalAmount,
            'netAmount' => $netAmount,
            'currencyId' => $currencyId
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error 1: " . $e;
        echo $return;
    }

    //content
    $content = $services[$i]['content'];
    //vehicle
    $vehicle = $content['vehicle'];
    $codevehicle = $vehicle['code'];
    $namevehicle = $vehicle['name'];
    //category
    $category = $content['category'];
    $codecategory = $category['code'];
    $namecategory = $category['name'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('content');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codevehicle' => $codevehicle,
            'namevehicle' => $namevehicle,
            'codecategory' => $codecategory,
            'namecategory' => $namecategory,
            'idServices' => $id
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error 2: " . $e;
        echo $return;
    }

    //images
    $images = $content['images'];
    for ($j=0; $j < count($images); $j++) { 
        $url = $images[$j]['url'];
        $type = $images[$j]['type'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('images');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'url' => $url,
                'type' => $type,
                'idServices' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 3: " . $e;
            echo $return;
        }
    }
    //transferDetailInfo
    $transferDetailInfo = $content['transferDetailInfo'];
    for ($k=0; $k < count($transferDetailInfo); $k++) { 
        $idtransfer = $transferDetailInfo[$k]['id'];
        $name = $transferDetailInfo[$k]['name'];
        $description = $transferDetailInfo[$k]['description'];
        $type = $transferDetailInfo[$k]['type'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('transferDetailInfo');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idtransfer' => $idtransfer,
                'name' => $name,
                'description' => $description,
                'type' => $type,
                'idServices' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 4: " . $e;
            echo $return;
        }
    }
    //customerTransferTimeInfo
    $customerTransferTimeInfo = $content['customerTransferTimeInfo'];
    for ($l=0; $l < count($customerTransferTimeInfo); $l++) { 
        $value = $customerTransferTimeInfo[$l]['value'];
        $type = $customerTransferTimeInfo[$l]['type'];
        $metric = $customerTransferTimeInfo[$l]['metric'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('customerTransferTimeInfo');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'value' => $value,
                'type' => $type,
                'metric' => $metric,
                'idServices' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 5: " . $e;
            echo $return;
        }
    }
    //supplierTransferTimeInfo
    $supplierTransferTimeInfo = $content['supplierTransferTimeInfo'];
    for ($m=0; $m < count($supplierTransferTimeInfo); $m++) { 
        $value = $supplierTransferTimeInfo[$m]['value'];
        $type = $supplierTransferTimeInfo[$m]['type'];
        $metric = $supplierTransferTimeInfo[$m]['metric'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('supplierTransferTimeInfo');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'value' => $value,
                'type' => $type,
                'metric' => $metric,
                'idServices' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 6: " . $e;
            echo $return;
        }
    }
    //transferRemarks
    $transferRemarks = $content['transferRemarks'];
    for ($r=0; $r < count($transferRemarks); $r++) { 
        $type = $transferRemarks[$r]['type'];
        $description = $transferRemarks[$r]['description'];
        $mandatory = $transferRemarks[$r]['mandatory'];
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('transferRemarks');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'type' => $type,
                'description' => $description,
                'mandatory' => $mandatory,
                'idServices' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 7: " . $e;
            echo $return;
        }
    }

    //cancellationPolicies
    $cancellationPolicies = $services[$i]['cancellationPolicies'];
    for ($s=0; $s < count($cancellationPolicies); $s++) { 
        $amount = $cancellationPolicies[$s]['amount'];
        $from = $cancellationPolicies[$s]['from'];
        $currencyId = $cancellationPolicies[$s]['currencyId'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cancellationPolicies');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'amount' => $amount,
                'from' => $from,
                'currencyId' => $currencyId,
                'idServices' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 8: " . $e;
            echo $return;
        }
    }
    //links
    $links = $services[$i]['links'];
    for ($x=0; $x < count($links); $x++) { 
        $rel = $links[$x]['rel'];
        $href = $links[$x]['href'];
        $method = $links[$x]['method'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('links');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'rel' => $rel,
                'href' => $href,
                'method' => $method,
                'idServices' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 9: " . $e;
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