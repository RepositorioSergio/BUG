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
echo "COMECOU BOOK";
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

$url = 'https://book-sandbox.roomerapi.com/api/book';

$raw = '{
    "check_in":"2019-11-15",
    "check_out":"2019-11-17",
    "hotel_id":1142,
    "adults":2,
    "pos":"us",
    "room_code":"27191",
    "rate_code":"MnwjfDI3MTkxfGEzMmMxODlmLTNiNWQtNGI2YS1iZWM1LTU5ZjBhMTRjM2U2Ni01MDAxLTAyNHx8NTh8MjA5MDcyMDMxfCN8RkxFWElCTEU%3D",
    "total_price":759.38,
    "first_name":"Andrea",
    "last_name":"Doesit"
  }';

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


$data = $response['data'];
if (count($data) > 0) {
    $hotel_id = $data['hotel_id'];
    $partner_hotel_id = $data['partner_hotel_id'];
    $rate_code = $data['rate_code'];
    $room_code = $data['room_code'];
    $adults = $data['adults'];
    $children = $data['children'];
    $check_in = $data['check_in'];
    $check_out = $data['check_out'];
    $transaction_id = $data['transaction_id'];
    $itinerary = $data['itinerary'];
    $confirmation_number = $data['confirmation_number'];
    $reference_id = $data['reference_id'];
    $affiliate_id = $data['affiliate_id'];
    $fraud_id = $data['fraud_id'];
    $fraud_provider = $data['fraud_provider'];
    $guest_email = $data['guest_email'];

    $prices = $data['prices'];
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

    $cancellation_policy = $data['cancellation_policy'];
    $type = $cancellation_policy['type'];
    $details = $cancellation_policy['details'];
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
        $insert->into('book');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'hotel_id' => $hotel_id,
            'partner_hotel_id' => $partner_hotel_id,
            'room_code' => $room_code,
            'rate_code' => $rate_code,
            'adults' => $adults,
            'children' => $children,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'transaction_id' => $transaction_id,
            'itinerary' => $itinerary,
            'confirmation_number' => $confirmation_number,
            'reference_id' => $reference_id,
            'affiliate_id' => $affiliate_id,
            'fraud_id' => $fraud_id,
            'fraud_provider' => $fraud_provider,
            'guest_email' => $guest_email,
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

    $children_ages = $data['children_ages'];
    if (count($children_ages) > 0) {
        for ($i=0; $i < count($children_ages); $i++) { 
            $age = $children_ages[$i];
        }
    }

    $fees = $data['fees'];
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

}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
