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
$url = 'https://test.ean.com/2.2/properties/content?language=en-US';
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
die();

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

$content = array();
$gener = array();
$pet = array();
$amenitie = array();
$amenities2 = array();
$link = array();
$types2 = array();
$room = array();
$rate = array();
$bed = array();
$statistic = array();
$themes2 = array();

foreach ($response as $key => $value) {
    $content = $response[$key];

    $property_id = $content['property_id'];
    $name = $content['name'];
    $phone = $content['phone'];
    $fax = $content['fax'];
    $rank = $content['rank'];
    $registry_number = $content['registry_number'];
    //address
    $address = $content['address'];
    $line_1 = $address['line_1'];
    $city = $address['city'];
    $state_province_code = $address['state_province_code'];
    $state_province_name = $address['state_province_name'];
    $postal_code = $address['postal_code'];
    $country_code = $address['country_code'];

    //ratings
    $ratings = $content['ratings'];
    $property = $ratings['property'];
    $rating = $property['rating'];
    $type = $property['type'];

    //location
    $location = $content['location'];
    $coordinates = $location['coordinates'];
    $latitude = $coordinates['latitude'];
    $longitude = $coordinates['longitude'];

    //category
    $category = $content['category'];
    $categoryid = $category['id'];
    $categoryname = $category['name'];

    //business_model
    $business_model = $content['business_model'];
    $expedia_collect = $business_model['expedia_collect'];
    $property_collect = $content['property_collect'];

    //checkin
    $checkin = $content['checkin'];
    $hour24 = $checkin['24_hour'];
    $begin_time = $checkin['begin_time'];
    $end_time = $checkin['end_time'];
    $instructions = $checkin['instructions'];
    $special_instructions = $checkin['special_instructions'];
    $min_age = $checkin['min_age'];

    //checkout
    $checkout = $content['checkout'];
    $time = $checkout['time'];

    //fees
    $fees = $content['fees'];
    $mandatory = $fees['mandatory'];
    $optional = $fees['optional'];

    //policies
    $policies = $content['policies'];
    $know_before_you_go = $policies['know_before_you_go'];

    //dates
    $dates = $content['dates'];
    $added = $dates['added'];
    $updated = $dates['updated'];

    //descriptions
    $descriptions = $content['descriptions'];
    $amenities = $descriptions['amenities'];
    $dining = $descriptions['dining'];
    $renovations = $descriptions['renovations'];
    $national_ratings = $descriptions['national_ratings'];
    $business_amenities = $descriptions['business_amenities'];
    $rooms = $descriptions['rooms'];
    $attractions = $descriptions['attractions'];
    $location = $descriptions['location'];
    $headline = $descriptions['headline'];

    //airports
    $airports = $content['airports'];
    $preferred = $airports['preferred'];
    $iata_airport_code = $preferred['iata_airport_code'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('content');
        $insert->values(array(
            'property_id' => $property_id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'name' => $name,
            'phone' => $phone,
            'fax' => $fax,
            'rank' => $rank,
            'registry_number' => $registry_number,
            'line_1' => $line_1,
            'city' => $city,
            'state_province_code' => $state_province_code,
            'state_province_name' => $state_province_name,
            'postal_code' => $postal_code,
            'country_code' => $country_code,
            'rating' => $rating,
            'type' => $type,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'categoryid' => $categoryid,
            'categoryname' => $categoryname,
            'expedia_collect' => $expedia_collect,
            'property_collect' => $property_collect,
            'hour24' => $hour24,
            'begin_time' => $begin_time,
            'end_time' => $end_time,
            'instructions' => $instructions,
            'special_instructions' => $special_instructions,
            'min_age' => $min_age,
            'time' => $time,
            'mandatory' => $mandatory,
            'optional' => $optional,
            'know_before_you_go' => $know_before_you_go,
            'added' => $added,
            'updated' => $updated,
            'amenities' => $amenities,
            'dining' => $dining,
            'renovations' => $renovations,
            'national_ratings' => $national_ratings,
            'business_amenities' => $business_amenities,
            'rooms' => $rooms,
            'attractions' => $attractions,
            'location' => $location,
            'headline' => $headline,
            'iata_airport_code' => $iata_airport_code
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

    //attributes
    $attributes = $content['attributes'];
    $general = $attributes['general'];
    foreach ($general as $key2 => $value2) {
        $gener = $general[$key2];

        $id = $gener['id'];
        $name = $gener['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('attributes');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idAttributes' => $id,
                'name' => $name,
                'property_id' => $property_id
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
    $pets = $attributes['pets'];
    foreach ($pets as $key3 => $value3) {
        $pet = $pets[$key3];

        $id = $pet['id'];
        $name = $pet['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('pets');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idPets' => $id,
                'name' => $name,
                'property_id' => $property_id
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

    //amenities
    $amenities = $content['amenities'];
    foreach ($amenities as $key4 => $value4) {
        $amenitie = $amenities[$key4];

        $id = $amenitie['id'];
        $name = $amenitie['name'];
        $value = $amenitie['value'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('amenities_content');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idAC' => $id,
                'name' => $name,
                'value' => $value,
                'property_id' => $property_id
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

    //images
    $images = $content['images'];
    for ($i=0; $i < count($images); $i++) { 
        $caption = $images[$i]['caption'];
        $hero_image = $images[$i]['hero_image'];
        $category = $images[$i]['category'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('images');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'caption' => $caption,
                'hero_image' => $hero_image,
                'category' => $category,
                'property_id' => $property_id
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

        $links = $images[$i]['links'];
        foreach ($links as $key5 => $value5) {
            $link = $links[$key5];
            $method = $link['method'];
            $href = $link['href'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('links');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'link' => $link,
                    'method' => $method,
                    'href' => $href,
                    'property_id' => $property_id
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

    //onsite_payments
    $onsite_payments = $content['onsite_payments'];
    $currency = $onsite_payments['currency'];
    $types = $onsite_payments['types'];
    foreach ($types as $key6 => $value6) {
        $types2 = $types[$key6];
        $id = $types2['id'];
        $name = $types2['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('currency');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idCurrency' => $id,
                'name' => $name,
                'property_id' => $property_id
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

    //rooms
    $rooms = $content['rooms'];
    foreach ($rooms as $key7 => $value7) {
        $room = $rooms[$key7];
        $id = $room['id'];
        $name = $room['name'];
        $descriptions = $room['descriptions'];
        $overview = $descriptions['overview'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('rooms');
            $insert->values(array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'name' => $name,
                'overview' => $overview,
                'property_id' => $property_id
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

        $amenities = $room['amenities'];
        foreach ($amenities as $keyamenities => $valueamenities) {
            $amenities2 = $amenities[$keyamenities];
            $id = $amenities2['id'];
            $name = $amenities2['name'];
            $value = $amenities2['value'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('amenities_rooms');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'idAR' => $id,
                    'name' => $name,
                    'value' => $value,
                    'property_id' => $property_id
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
        $bed_groups = $room['bed_groups'];
        foreach ($bed_groups as $keybed => $valuebed) {
            $bed = $bed_groups[$keybed];
            $id = $bed['id'];
            $description = $bed['description'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('bed_groups');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'idBed' => $id,
                    'description' => $name,
                    'property_id' => $property_id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 10: " . $e;
                echo $return;
            }

            $configuration = $bed['configuration'];
            for ($j=0; $j < count($configuration); $j++) { 
                $type = $configuration[$j]['type'];
                $size = $configuration[$j]['size'];
                $quantity = $configuration[$j]['quantity'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('configuration');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'type' => $type,
                        'size' => $size,
                        'quantity' => $quantity,
                        'property_id' => $property_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 11: " . $e;
                    echo $return;
                }
            }
        }
    }

    //rates
    $rates = $content['rates'];
    foreach ($rates as $key8 => $value8) {
        $rate = $rates[$key8];
        $idRate = $rate['id'];
        $amenities = $rate['amenities'];
        foreach ($amenities as $keyamenities => $valueamenities) {
            $amenities2 = $amenities[$keyamenities];
            $id = $amenities2['id'];
            $name = $amenities2['name'];
            $value = $amenities2['value'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('amenities_rates');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'idARATES' => $id,
                    'name' => $name,
                    'value' => $value,
                    'idRate' => $idRate,
                    'property_id' => $property_id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 12: " . $e;
                echo $return;
            }
        }
    }

    //statistics
    $statistics = $content['statistics'];
    foreach ($statistics as $key9 => $value9) {
        $statistic = $statistics[$key9];
        $id = $statistic['id'];
        $name = $statistic['name'];
        $value = $statistic['value'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('statistics');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idStatistics' => $id,
                'name' => $name,
                'value' => $value,
                'property_id' => $property_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 13: " . $e;
            echo $return;
        }
    }

    //themes
    $themes = $content['themes'];
    foreach ($themes as $key10 => $value10) {
        $themes2 = $themes[$key10];
        $id = $themes2['id'];
        $name = $themes2['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('themes');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idThemes' => $id,
                'name' => $name,
                'property_id' => $property_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 14: " . $e;
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