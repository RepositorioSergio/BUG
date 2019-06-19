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

//TOKEN vem do currentprice

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
$url = 'https://test.ean.com/2.2/itineraries?affiliate_reference_id=4480ABC&email=smith@example.com';
// echo $return;
// echo $url;
// echo $return;
$client->setUri($url);
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

$amenities2 = "";
$night = array();

$itinerary_id = $response['itinerary_id'];
$property_id = $response['property_id'];
$creation_date_time = $response['creation_date_time'];
$affiliate_reference_id = $response['affiliate_reference_id'];
$affiliate_metadata = $response['affiliate_metadata'];

$billing_contact = $response['billing_contact'];
$email = $billing_contact['email'];
$given_name = $billing_contact['given_name'];
$family_name = $billing_contact['family_name'];
$phone = $billing_contact['phone'];
$address = $billing_contact['address'];
$line_1 = $address['line_1'];
$line_2 = $address['line_2'];
$line_3 = $address['line_3'];
$city = $address['city'];
$state_province_code = $address['state_province_code'];
$postal_code = $address['postal_code'];
$country_code = $address['country_code'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('retrievebooking');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'itinerary_id' => $itinerary_id,
        'property_id' => $property_id,
        'creation_date_time' => $creation_date_time,
        'affiliate_reference_id' => $affiliate_reference_id,
        'affiliate_metadata' => $affiliate_metadata,
        'email' => $email,
        'given_name' => $given_name,
        'family_name' => $family_name,
        'phone' => $phone,
        'line_1' => $line_1,
        'line_2' => $line_2,
        'line_3' => $line_3,
        'city' => $city,
        'state_province_code' => $state_province_code,
        'postal_code' => $postal_code,
        'country_code' => $country_code
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

$rooms = $response['rooms'];
for ($i=0; $i < count($rooms); $i++) { 
    $id_rooms = $rooms[$i]['id'];
    $bed_group_id = $rooms[$i]['bed_group_id'];
    $checkin = $rooms[$i]['checkin'];
    $checkout = $rooms[$i]['checkout'];
    $number_of_adults = $rooms[$i]['number_of_adults'];
    $given_name = $rooms[$i]['given_name'];
    $family_name = $rooms[$i]['family_name'];
    $status = $rooms[$i]['status'];
    $special_request = $rooms[$i]['special_request'];
    $smoking = $rooms[$i]['smoking'];
    $confirmation_id = $rooms[$i]['confirmation_id'];
    $expedia = $confirmation_id['expedia'];
    //links
    $links = $rooms[$i]['links'];
    $cancel = $links['cancel'];
    $methodcancel = $cancel['method'];
    $hrefcancel = $cancel['href'];
    $change = $links['change'];
    $methodchange = $change['method'];
    $hrefchange = $change['href'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('rooms_retbooking');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'id_rooms' => $id_rooms,
            'bed_group_id' => $bed_group_id,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'number_of_adults' => $number_of_adults,
            'given_name' => $given_name,
            'family_name' => $family_name,
            'status' => $status,
            'special_request' => $special_request,
            'smoking' => $smoking,
            'expedia' => $expedia,
            'methodcancel' => $methodcancel,
            'hrefcancel' => $hrefcancel,
            'methodchange' => $methodchange,
            'hrefchange' => $hrefchange,
            'itinerary_id' => $itinerary_id
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

    $rate = $rooms[$i]['rate'];
    $id_rate = $rate['id'];
    $merchant_of_record = $rate['merchant_of_record'];
    $refundable = $rate['refundable'];
    $cancel_refund = $rate['cancel_refund'];
    $amount = $cancel_refund['amount'];
    $currency = $cancel_refund['currency'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('rates_retbooking');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'id_rate' => $id_rate,
            'merchant_of_record' => $merchant_of_record,
            'refundable' => $refundable,
            'amount' => $amount,
            'currency' => $currency,
            'id_rooms' => $id_rooms
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

    $nightly = $rate['nightly'];
    for ($kA=0; $kA < count($nightly); $kA++) { 
        $night = $nightly[$kA];
        for ($kB=0; $kB < count($night); $kB++) { 
            $type = $night[$kB]['type'];
            $value = $night[$kB]['value'];
            $currency = $night[$kB]['currency'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('nightly_retbooking');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'type' => $type,
                    'value' => $value,
                    'currency' => $currency,
                    'id_rate' => $id_rate
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
    }

    $stay = $rate['stay'];
    for ($x=0; $x < count($stay); $x++) { 
        $type = $stay[$x]['type'];
        $value = $stay[$x]['value'];
        $currency = $stay[$x]['currency'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('stay_retbooking');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'type' => $type,
                'value' => $value,
                'currency' => $currency,
                'id_rate' => $id_rate
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
    //fees
    $fees = $rate['fees'];
    for ($s=0; $s < count($fees); $s++) { 
        $type = $fees[$s]['type'];
        $value = $fees[$s]['value'];
        $scope = $fees[$s]['scope'];
        $frequency = $fees[$s]['frequency'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('fees_retbooking');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'type' => $type,
                'value' => $value,
                'scope' => $scope,
                'frequency' => $frequency,
                'id_rate' => $id_rate
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

    $amenities = $rate['amenities'];
    for ($z=0; $z < count($amenities); $z++) { 
        $amenities2 = $amenities[$z];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('amenities_retbooking');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'type' => $type,
                'id_rate' => $id_rate
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
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>