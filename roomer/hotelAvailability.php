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
echo "COMECOU HOTEL AVAILABILITY";
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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://b2b-sandbox.roomerapi.com/api/hotel_availability?check_in=2019-11-15&check_out=2019-11-17&hotel_id=1142&adults=2&children=3&children_ages=2,4,7&platform=API&pos=us';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Authorization: Token token=bfff17d3d81077f15d75abfcf115ed73',
    'Partner: paulo@club1hotels.com',
    'API-Version: 2.0'
)); 
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

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$important = "";
$taxes = "";
$parag = "";

$rooms = $response['rooms'];
if (count($rooms) > 0) {
    for ($jAux2=0; $jAux2 < count($rooms); $jAux2++) {
        $rate_code = $rooms[$jAux2]['rate_code']; 
        $room_code = $rooms[$jAux2]['room_code'];
        $description = $rooms[$jAux2]['description'];
        $bed_type = $rooms[$jAux2]['bed_type'];
        $board_name = $rooms[$jAux2]['board_name'];
        $room_type_name = $rooms[$jAux2]['room_type_name'];
        $breakfast = $rooms[$jAux2]['breakfast'];
        $check_in_instruction = $rooms[$jAux2]['check_in_instruction'];
        $occupancy_limit = $rooms[$jAux2]['occupancy_limit'];
        $zone_name = $rooms[$jAux2]['zone_name'];

        $prices = $rooms[$jAux2]['prices'];
        $is_best_value = $prices['is_best_value'];
        $recommended_price = $prices['recommended_price'];
        $benchmark_price = $prices['benchmark_price'];

        $b2c_rate = $prices['b2c_rate'];
        $price = $b2c_rate['price'];
        $tax = $b2c_rate['tax'];

        $b2b_rate = $prices['b2b_rate'];
        $pricebb = $b2b_rate['price'];
        $taxbb = $b2b_rate['tax'];

        $mobile_rate = $prices['mobile_rate'];
        $pricemr = $mobile_rate['price'];
        $taxmr = $mobile_rate['tax'];

        $fenced_rate = $prices['fenced_rate'];
        $pricefr = $fenced_rate['price'];
        $taxfr = $fenced_rate['tax'];

        $cancellation_policy = $rooms[$jAux2]['cancellation_policy'];
        $type = $cancellation_policy['type'];
        $details = $cancellation_policy['details'];
        $details = $details['details'];
        if (count($details) > 0) {
            for ($j=0; $j < count($details); $j++) { 
                $upto_date = $details[$j]['upto_date'];
                $refund_amount = $details[$j]['refund_amount'];
                $text = $details[$j]['text'];
            }
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('rooms_hotelAvailability');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'room_code' => $room_code,
                'rate_code' => $rate_code,
                'description' => $description,
                'bed_type' => $bed_type,
                'breakfast' => $breakfast,
                'board_name' => $board_name,
                'room_type_name' => $room_type_name,
                'check_in_instruction' => $check_in_instruction,
                'zone_name' => $zone_name,
                'occupancy_limit' => $occupancy_limit,
                'is_best_value' => $is_best_value,
                'recommended_price' => $recommended_price,
                'benchmark_price' => $benchmark_price,
                'price' => $price,
                'tax' => $tax,
                'pricebb' => $pricebb,
                'taxbb' => $taxbb,
                'pricemr' => $pricemr,
                'taxmr' => $taxmr,
                'pricefr' => $pricefr,
                'taxfr' => $taxfr,
                'typeCP' => $type
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO 1: " . $e;
            echo $return;
        }

        $fees = $rooms[$jAux2]['fees'];
        if (count($fees) > 0) {
            $at_property = $transactions['at_property'];
            if (count($at_property) > 0) {
                for ($k=0; $k < count($at_property); $k++) { 
                    $name = $at_property[$k]['name'];
                    $amount = $at_property[$k]['amount'];

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('property_fees');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'name' => $name,
                            'amount' => $amount,
                            'room_code' => $room_code
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 4: " . $e;
                        echo $return;
                    }
                }
            }
            $included = $transactions['included'];
            if (count($included) > 0) {
                for ($k=0; $k < count($included); $k++) { 
                    $name = $included[$k]['name'];
                    $amount = $included[$k]['amount'];

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('included_fees');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'name' => $name,
                            'amount' => $amount,
                            'room_code' => $room_code
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 5: " . $e;
                        echo $return;
                    }
                }
            }
        }

        $bedding_data = $rooms[$jAux2]['bedding_data'];
        if (count($bedding_data) > 0) {
            for ($i=0; $i < count($bedding_data); $i++) { 
                $bed_count = $bedding_data[$i]['bed_count'];
                $bed_type = $bedding_data[$i]['bed_type'];
            }
        }

        $important_information = $rooms[$jAux2]['important_information'];
        if (count($important_information) > 0) {
            for ($k=0; $k < count($important_information); $k++) { 
                $important = $important_information[$k];
            }
        }

        $taxes_and_fees = $rooms[$jAux2]['taxes_and_fees'];
        if (count($taxes_and_fees) > 0) {
            for ($k=0; $k < count($taxes_and_fees); $k++) { 
                $taxes = $taxes_and_fees[$k];
            }
        }

        $policy_data = $rooms[$jAux2]['policy_data'];
        if (count($policy_data) > 0) {
            for ($k=0; $k < count($policy_data); $k++) { 
                $title = $policy_data[$k]['title'];
                $paragraph_data = $policy_data[$k]['paragraph_data'];
                if (count($paragraph_data) > 0) {
                    for ($kAux=0; $kAux < count($paragraph_data); $kAux++) { 
                        $parag = $paragraph_data[$kAux];
                    }
                }
            }
        }

        $allowed_cards_data = $rooms[$jAux2]['allowed_cards_data'];
        if (count($allowed_cards_data) > 0) {
            for ($k=0; $k < count($allowed_cards_data); $k++) { 
                $card_type = $allowed_cards_data[$k]['card_type'];
                $name = $allowed_cards_data[$k]['name'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('allowedCardsData');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'card_type' => $card_type,
                        'name' => $name
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 2: " . $e;
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
echo '<br/>Done';
?>