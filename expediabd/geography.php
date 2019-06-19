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
$config = new \Zend\Config\Config(include '../config/autoload/global.expedia.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$apiKey = "503fvdcg1tm02jcebf6m5pqj8j";
$secret = "a7435jst471jn";
$timestamp = time();
$authorization = 'EAN APIKey=' . $apiKey . ',Signature=' . hash("sha512", $apiKey . $secret . $timestamp) . ',timestamp=' . time();
// echo $return;
// echo "authorization: " . $authorization;
// echo $return;
$ipaddress = '';
if ($_SERVER['HTTP_CLIENT_IP']) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
} else if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_X_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
} else if ($_SERVER['HTTP_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
} else if ($_SERVER['REMOTE_ADDR']) {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
} else {
    $ipaddress = 'UNKNOWN';
    $ipaddress = "142.44.216.144";
}

// echo $return;
// echo "IP: " . $ipaddress;
// echo $return;

$token = bin2hex(random_bytes(64));
// echo $return;
// echo "TOKEN: " . $token;
// echo $return;

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Accept: application/json",
    "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
    "Authorization: " . $authorization,
    "Content-Type: application/json",
    "Accept-Encoding: gzip",
    "Customer-Ip: " . $ipaddress
));
$url = 'https://test.ean.com/2.2/regions?language=en-US&include=details';
// echo $return;
// echo $url;
// echo $return;
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
echo $return;
echo $response;
echo $return;

/*
 * Tambem funciona:
 * $ch = curl_init();
 * curl_setopt($ch, CURLOPT_URL, $url);
 * curl_setopt($ch, CURLOPT_ENCODING, "gzip");
 * curl_setopt($ch, CURLOPT_HEADER, false);
 * curl_setopt($ch, CURLOPT_VERBOSE, true);
 * curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 * curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
 * curl_setopt($ch, CURLOPT_HTTPHEADER, array(
 * "Accept: application/json",
 * "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
 * "language: en-US",
 * "include: details",
 * "Authorization: " . $authorization,
 * "Customer-Ip: " . $ipaddress
 * ));
 * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 * $response = curl_exec($ch);
 * $error = curl_error($ch);
 * $headers = curl_getinfo($ch);
 *
 * if ($error != "") {
 * echo $return;
 * echo "ERRO: " . $error;
 * echo $return;
 * } else {
 * echo $return;
 * echo "NAO TEM ERROS.";
 * echo $return;
 * }
 * curl_close($ch);
 *
 * echo $return;
 * echo $response;
 * echo $return;
 */

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

// echo "<xmp>";
// var_dump($response);
// echo "</xmp>";


$config = new \Zend\Config\Config(include '../config/autoload/global.expedia.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$services = $response['services'];

for ($i = 0; $i < count($response); $i ++) {
    $id = $response[$i]['id'];
    $type = $response[$i]['type'];
    $name = $response[$i]['name'];
    $name_full = $response[$i]['name_full'];
    $country_code = $response[$i]['country_code'];
    echo $return;
    echo "country_code: " . $country_code;
    echo $return;
    //coordinates
    $coordinates = $response[$i]['coordinates'];
    $center_longitude = $coordinates['center_longitude'];
    $center_latitude = $coordinates['center_latitude'];
    //bounding_polygon
    $bounding_polygon = $coordinates['bounding_polygon'];
    $type_polygon = $bounding_polygon['type'];

    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('regions');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'type' => $type,
            'name' => $name,
            'name_full' => $name_full,
            'country_code' => $country_code,
            'center_longitude' => $center_longitude,
            'center_latitude' => $center_latitude,
            'type_polygon' => $type_polygon
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

    $coord = "";
    $coord2 = "";
    $coord3 = "";
    $coordinates2 = $bounding_polygon['coordinates'];
    for ($j=0; $j < count($coordinates2); $j++) { 
        $coord = $coordinates2[$j];
        
        for ($jAux=0; $jAux < count($coord); $jAux++) { 
            $coord2 = $coord[$jAux];

            for ($jAux2=0; $jAux2 < count($coord2); $jAux2++) { 
                $coord3 = $coord2[$jAux2];

                echo $return;
                echo "COORD: " . $coord3;
                echo $return;

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('coordinates_polygon');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'coordinates' => $coord3,
                        'id_region' => $id
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
            }
        }
    }

    $ancestors = $response[$i]['ancestors'];
    for ($x=0; $x < count($ancestors); $x++) { 
        $id_ancestors = $ancestors[$x]['id'];
        $type = $ancestors[$x]['type'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('ancestors');
            $insert->values(array(      
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'id_ancestors' => $id_ancestors,
                'type' => $type,
                'id_region' => $id
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

    $descendants = $response[$i]['descendants'];
    //province_state
    $province = "";
    $province_state = $descendants['province_state'];
    for ($ya=0; $ya < count($province_state); $ya++) { 
        $province = $province_state[$ya];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('province_state');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'province' => $province,
                'id_region' => $id
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

    //high_level_region
    $level_region = "";
    $high_level_region = $descendants['high_level_region'];
    for ($yb=0; $yb < count($high_level_region); $yb++) { 
        $level_region = $high_level_region[$yb];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('high_level_region');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'level_region' => $level_region,
                'id_region' => $id
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

    //city
    $cities = "";
    $city = $descendants['city'];
    for ($yc=0; $yc < count($city); $yc++) { 
        $cities = $city[$yc];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cities');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'city' => $cities,
                'id_region' => $id
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

    //point_of_interest
    $points = "";
    $point_of_interest = $descendants['point_of_interest'];
    for ($yd=0; $yd < count($point_of_interest); $yd++) { 
        $points = $point_of_interest[$yd];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('point_of_interest');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'points' => $points,
                'id_region' => $id
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

}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>