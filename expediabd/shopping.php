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
$url = 'https://test.ean.com/2.2/properties/availability?checkin=2020-11-15&checkout=2020-11-17&currency=USD&language=en-US&country_code=US&occupancy=2&sales_channel=website&sales_environment=hotel_package&sort_type=preferred&property_id=24051641';


//
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

$night = array();
$occupancie = array();

for ($i = 0; $i < count($response); $i ++) {
    $property_id = $response[$i]['property_id'];
    $score = $response[$i]['score'];
    echo $return;
    echo "property_id: " . $property_id;
    echo $return;
    // links
    $links = $response[$i]['links'];
    $additional_rates = $links['additional_rates'];
    $method = $additional_rates['method'];
    $href = $additional_rates['href'];
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('shopping');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'property_id' => $property_id,
            'score' => $score,
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

    // rooms
    $rooms = $response[$i]['rooms'];
    for ($j=0; $j < count($rooms); $j++) { 
        $id = $rooms[$j]['id'];
        $room_name = $rooms[$j]['room_name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('rooms_shopping');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'id_room' => $id,
                'room_name' => $room_name,
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

        $rates = $rooms[$j]['rates'];
        for ($k=0; $k < count($rates); $k++) { 
            $id_rates = $rates[$k]['id'];
            $available_rooms = $rates[$k]['available_rooms'];
            $refundable = $rates[$k]['refundable'];
            $fenced_deal = $rates[$k]['fenced_deal'];
            $fenced_deal_available = $rates[$k]['fenced_deal_available'];
            $deposit_required = $rates[$k]['deposit_required'];
            $merchant_of_record = $rates[$k]['merchant_of_record'];
            $promo_id = $rates[$k]['promo_id'];
            //links
            $links = $rates[$k]['links'];
            $payment_options = $links['payment_options'];
            $method = $payment_options['method'];
            $href = $payment_options['href'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('rates_shopping');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'id_rates' => $id_rates,
                    'available_rooms' => $available_rooms,
                    'refundable' => $refundable,
                    'fenced_deal' => $fenced_deal,
                    'fenced_deal_available' => $fenced_deal_available,
                    'deposit_required' => $deposit_required,
                    'merchant_of_record' => $merchant_of_record,
                    'promo_id' => $promo_id,
                    'method' => $method,
                    'href' => $href,
                    'id_room' => $id
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

            //amenities
            $amenities = $rates[$k]['amenities'];
            for ($kAux=0; $kAux < count($amenities); $kAux++) { 
                $id_amenities = $amenities[$kAux]['id'];
                $name = $amenities[$kAux]['name'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('amenities_shopping');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'id_amenities' => $id_amenities,
                        'name' => $name,
                        'id_rates' => $id_rates
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

            //bed_groups
            $bed_groups = $rates[$k]['bed_groups'];
            for ($kAux2=0; $kAux2 < count($bed_groups); $kAux2++) { 
                $links = $bed_groups[$kAux2]['links'];
                $price_check = $links['price_check'];
                $method = $price_check['method'];
                $href = $price_check['href'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('bed_groups_shopping');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'method' => $method,
                        'href' => $href,
                        'id_rates' => $id_rates
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

                $configuration = $bed_groups[$kAux2]['configuration'];
                for ($kAux3=0; $kAux3 < count($configuration); $kAux3++) { 
                    $type = $configuration[$kAux3]['type'];
                    $size = $configuration[$kAux3]['size'];
                    $quantity = $configuration[$kAux3]['quantity'];

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('configuration_shopping');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'type' => $type,
                            'size' => $size,
                            'quantity' => $quantity,
                            'id_rates' => $id_rates
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

            
            $occupancies = $rates[$k]['occupancies'];
            foreach ($occupancies as $key => $value) {
                $occupancie = $occupancies[$key];
                $nightly = $occupancie['nightly'];
                for ($kA=0; $kA < count($nightly); $kA++) { 
                    $night = $nightly[$kA];
                    for ($kB=0; $kB < count($night); $kB++) { 
                        $type = $night[$kB]['type'];
                        $value = $night[$kB]['value'];
                        $currency = $night[$kB]['currency'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('nightly');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'type' => $type,
                                'value' => $value,
                                'currency' => $currency,
                                'id_rates' => $id_rates
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

                $stay = $occupancie['stay'];
                for ($x=0; $x < count($stay); $x++) { 
                    $type = $stay[$x]['type'];
                    $value = $stay[$x]['value'];
                    $currency = $stay[$x]['currency'];

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('stay');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'type' => $type,
                            'value' => $value,
                            'currency' => $currency,
                            'id_rates' => $id_rates
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

                $fees = $occupancie['fees'];
                $mandatory_fee = $fees['mandatory_fee'];
                $billable_currency = $mandatory_fee['billable_currency'];
                $value = $billable_currency['value'];
                $currency = $billable_currency['currency'];
                $request_currency = $mandatory_fee['request_currency'];
                $valueRC = $request_currency['value'];
                $currencyRC = $request_currency['currency'];

                $totals = $occupancie['totals'];
                $inclusive = $totals['inclusive'];
                $billable_currency = $inclusive['billable_currency'];
                $valueBInclusive = $billable_currency['value'];
                $currencyBInclusive = $billable_currency['currency'];
                $request_currency = $inclusive['request_currency'];
                $valueRInclusive = $request_currency['value'];
                $currencyRInclusive = $request_currency['currency'];

                $exclusive = $totals['exclusive'];
                $billable_currency = $exclusive['billable_currency'];
                $valueBExclusive = $billable_currency['value'];
                $currencyBExclusive  = $billable_currency['currency'];
                $request_currency = $exclusive['request_currency'];
                $valueRExclusive = $request_currency['value'];
                $currencyRExclusive = $request_currency['currency'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('fees');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'value' => $value,
                        'currency' => $currency,
                        'valueRC' => $valueRC,
                        'currencyRC' => $currencyRC,
                        'valueBInclusive' => $valueBInclusive,
                        'currencyBInclusive' => $currencyBInclusive,
                        'valueRInclusive' => $valueRInclusive,
                        'currencyRInclusive' => $currencyRInclusive,
                        'valueBExclusive' => $valueBExclusive,
                        'currencyBExclusive' => $currencyBExclusive,
                        'valueRExclusive' => $valueRExclusive,
                        'currencyRExclusive' => $currencyRExclusive,
                        'id_rates' => $id_rates
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
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>