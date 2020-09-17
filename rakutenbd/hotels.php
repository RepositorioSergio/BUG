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
echo "COMECOU HOTELS";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.rakuten.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://data.rakutentravelxchange.com/hotels';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json'
));
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
$response = json_decode($response, true);

$config = new \Zend\Config\Config(include '../config/autoload/global.rakuten.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$data = $response['data'];
$items = $data['items'];
if (count($items) > 0) {
    for ($i=0; $i < count($items); $i++) { 
        $id = $items[$i]['id'];
        $name = $items[$i]['name'];
        $original_name = $items[$i]['original_name'];
        $address = $items[$i]['address'];
        $approximate_cost = $items[$i]['approximate_cost'];
        $category_id = $items[$i]['category_id'];
        $chain_code = $items[$i]['chain_code'];
        $city = $items[$i]['city'];
        $country = $items[$i]['country'];
        $country_code = $items[$i]['country_code'];
        $latitude = $items[$i]['latitude'];
        $longitude = $items[$i]['longitude'];
        $rating = $items[$i]['rating'];
        $trip_advisor_rating = $items[$i]['trip_advisor_rating'];
        $trip_advisor_review_count = $items[$i]['trip_advisor_review_count'];

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('hotels');
            $select->where(array(
                'id' => $id
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (string)$data['id'];
                if ($id != "") {
                    $config = new \Zend\Config\Config(include '../config/autoload/global.rakuten.php');
                    $config = [
                        'driver' => $config->db->driver,
                        'database' => $config->db->database,
                        'username' => $config->db->username,
                        'password' => $config->db->password,
                        'hostname' => $config->db->hostname
                    ];
                    $dbUpdate = new \Zend\Db\Adapter\Adapter($config);

                    $data = array(
                        'datetime_updated' => time(),
                        'name' => $name, 
                        'original_name' => $original_name, 
                        'address' => $address, 
                        'approximate_cost' => $approximate_cost, 
                        'category_id' => $category_id, 
                        'chain_code' => $chain_code, 
                        'city' => $city, 
                        'country' => $country, 
                        'country_code' => $country_code, 
                        'latitude' => $latitude, 
                        'longitude' => $longitude, 
                        'rating' => $rating, 
                        'trip_advisor_rating' => $trip_advisor_rating, 
                        'trip_advisor_review_count' => $trip_advisor_review_count
                    );
  
                    $sql    = new Sql($dbUpdate);
                    $update = $sql->update();
                    $update->table('hotels');
                    $update->set($data);
                    $update->where(array('id' => $id));

                    $statement = $sql->prepareStatementForSqlObject($update);
                    $results = $statement->execute();
                    $dbUpdate->getDriver()
                    ->getConnection()
                    ->disconnect(); 
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hotels');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_updated' => time(),
                        'name' => $name, 
                        'original_name' => $original_name, 
                        'address' => $address, 
                        'approximate_cost' => $approximate_cost, 
                        'category_id' => $category_id, 
                        'chain_code' => $chain_code, 
                        'city' => $city, 
                        'country' => $country, 
                        'country_code' => $country_code, 
                        'latitude' => $latitude, 
                        'longitude' => $longitude, 
                        'rating' => $rating, 
                        'trip_advisor_rating' => $trip_advisor_rating, 
                        'trip_advisor_review_count' => $trip_advisor_review_count
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
                $insert->into('hotels');
                $insert->values(array(
                    'id' => $id,
                    'datetime_updated' => time(),
                    'name' => $name, 
                    'original_name' => $original_name, 
                    'address' => $address, 
                    'approximate_cost' => $approximate_cost, 
                    'category_id' => $category_id, 
                    'chain_code' => $chain_code, 
                    'city' => $city, 
                    'country' => $country, 
                    'country_code' => $country_code, 
                    'latitude' => $latitude, 
                    'longitude' => $longitude, 
                    'rating' => $rating, 
                    'trip_advisor_rating' => $trip_advisor_rating, 
                    'trip_advisor_review_count' => $trip_advisor_review_count  
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            }
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO: ". $e;
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