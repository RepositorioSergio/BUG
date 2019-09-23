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
echo "COMECOU TRANSACTION";
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

$url = 'http://b2b-sandbox.roomerapi.com/api/data/transactions';

$raw = '{"transaction_ids":[5013124626,5013124627],"reference_ids":[],"from":"2019-11-10","to":"2019-11-30","check_in":"2019-11-01","check_out":"2019-11-20","modification_date":"2019-12-17","itineraries":["342718859","342719155"]}';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type:application/json',
    'Authorization: Token token=bfff17d3d81077f15d75abfcf115ed73',
    'Partner: paulo@club1hotels.com',
    'API-Version: 2.0'
)); 
$client->setUri($url);
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

$age = "";


$transactions = $response['transactions'];
if (count($transactions) > 0) {
    for ($i=0; $i < count($transactions); $i++) { 
        $hotel_id = $transactions[$i]['hotel_id'];
        $guest_first_name = $transactions[$i]['guest_first_name'];
        $guest_last_name = $transactions[$i]['guest_last_name'];
        $created_at = $transactions[$i]['created_at'];
        $reference_id = $transactions[$i]['reference_id'];
        $special_requests = $transactions[$i]['special_requests'];
        $platform = $transactions[$i]['platform'];
        $cancellation_code = $transactions[$i]['cancellation_code'];
        $cancellation_reason = $transactions[$i]['cancellation_reason'];
        $refunded = $transactions[$i]['refunded'];
        $check_in = $transactions[$i]['check_in'];
        $check_out = $transactions[$i]['check_out'];
        $status = $transactions[$i]['status'];
        $total_price = $transactions[$i]['total_price'];
        $transaction_id = $transactions[$i]['transaction_id'];
        $adults = $transactions[$i]['adults'];
        $children = $transactions[$i]['children'];
        $itinerary = $transactions[$i]['itinerary'];
        $confirmation_number = $transactions[$i]['confirmation_number'];
        
        //full_request
        $full_request = $transactions[$i]['full_request'];
        $check_inFR = $full_request['check_in'];
        $check_outFR = $full_request['check_out'];
        $hotel_idFR = $full_request['hotel_id'];
        $adultsFR = $full_request['adults'];
        $childrenFR = $full_request['children'];
        $platformFR = $full_request['platform'];
        $posFR = $full_request['pos'];
        $room_codeFR = $full_request['room_code'];
        $rate_codeFR = $full_request['rate_code'];
        $total_priceFR = $full_request['total_price'];
        $first_nameFR = $full_request['first_name'];
        $last_nameFR = $full_request['last_name'];
        $special_requestsFR = $full_request['special_requests'];
        $reference_idFR = $full_request['reference_id'];
        $affiliate_idFR = $full_request['affiliate_id'];
        $fraud_idFR = $full_request['fraud_id'];
        $fraud_providerFR = $full_request['fraud_provider'];
        $amountFR = $full_request['amount'];
        $currencyFR = $full_request['currency'];
        $reservation_idFR = $full_request['reservation_id'];

         //prices
         $prices = $transactions[$i]['prices'];
         if (count($prices) > 0) {
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
         }

        //cancellation_policy
        $cancellation_policy = $transactions[$i]['cancellation_policy'];
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
            $insert->into('transactions');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'hotel_id' => $hotel_id,
                'guest_first_name' => $guest_first_name,
                'guest_last_name' => $guest_last_name,
                'created_at' => $created_at,
                'reference_id' => $reference_id,
                'special_requests' => $special_requests,
                'platform' => $platform,
                'cancellation_code' => $cancellation_code,
                'cancellation_reason' => $cancellation_reason,
                'refunded' => $refunded,
                'check_in' => $check_in,
                'check_out' => $check_out,
                'status' => $status,
                'total_price' => $total_price,
                'transaction_id' => $transaction_id,
                'adults' => $adults,
                'children' => $children,
                'itinerary' => $itinerary,
                'confirmation_number' => $confirmation_number,
                'occupancy_limit' => $occupancy_limit,
                'check_inFR' => $check_inFR,
                'check_outFR' => $check_outFR,
                'hotel_idFR' => $hotel_idFR,
                'adultsFR' => $adultsFR,
                'childrenFR' => $childrenFR,
                'platformFR' => $platformFR,
                'posFR' => $posFR,
                'room_codeFR' => $room_codeFR,
                'rate_codeFR' => $rate_codeFR,
                'total_priceFR' => $total_priceFR,
                'first_nameFR' => $first_nameFR,
                'last_nameFR' => $last_nameFR,
                'special_requestsFR' => $special_requestsFR,
                'reference_idFR' => $reference_idFR,
                'affiliate_idFR' => $affiliate_idFR,
                'fraud_idFR' => $fraud_idFR,
                'fraud_providerFR' => $fraud_providerFR,
                'amountFR' => $amountFR,
                'currencyFR' => $currencyFR,
                'reservation_idFR' => $reservation_idFR,
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
            echo "ERRO: " . $e;
            echo $return;
        }

        //fees
        $fees = $transactions[$i]['fees'];
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
                            'room_code' => $room_codeFR
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
                            'room_code' => $room_codeFR
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

        //allowed_cards_data
        $allowed_cards_data = $transactions[$i]['allowed_cards_data'];
        if (count($allowed_cards_data) > 0) {
            for ($k=0; $k < count($allowed_cards_data); $k++) { 
                $card_type = $allowed_cards_data[$k]['card_type'];
                $name = $allowed_cards_data[$k]['name'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('allowedCardsData_transactions');
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
echo 'Done';
?>