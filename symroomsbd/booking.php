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
echo "COMECOU BOOKING<br/>";
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
$affiliate_id_palace = 0;
$branch_filter = "";


$config = new \Zend\Config\Config(include '../config/autoload/global.symrooms.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = "https://api.travelgatex.com/";

$raw = '{"query":" query {\n  hotelX {\n booking(criteria: {\n accessCode: \"422\",\n  language: \"en\",\n  references: {\n  currency: \"EUR\",\n hotelCode: \"1\",\n  references: [\n  {\n  client: \"Demo_Client\",\n  supplier: \"HOTELTEST\"  }\n  ]\n },\n  typeSearch: REFERENCES},\n  settings: {\n client:\"Demo_Client\",\n testMode: true}\n) {\n errors{\n code\n  type\n  description\n  }\n  warnings {\n  code\n  description\n  type\n  }\n  bookings {\n  reference {\n  client\n  supplier\n  }\n  hotel {\n  bookingDate\n  start\n  end\n  hotelCode\n  hotelName\n  boardCode\n  occupancies {\n  id\n  paxes {\n  age\n  }\n  }\n  rooms {\n  occupancyRefId\n  code\n  description\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  }\n  }\n  }\n  }"}';


 $headers = array(
    'Authorization: Apikey 64780338-49c8-4439-7c7d-d03c2033b145',
	'Accept-Encoding: gzip, deflate, br',
	'Content-Type: application/json',
	'Accept: application/json',
	'Connection: keep-alive',
	'DNT: 1',
	'Origin: https://api.travelgatex.com'
); 

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);

echo $response;

$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

$response = json_decode($response, true);
echo "<br/>RESPONSE";
/* echo '<xmp>';
var_dump($response);
echo '</xmp>';  */

$config = new \Zend\Config\Config(include '../config/autoload/global.symrooms.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$data = $response['data'];
$hotelX = $data['hotelX'];
$booking = $hotelX['booking'];

$bookings = $booking['bookings'];
for ($i=0; $i < count($bookings); $i++) { 
    $reference = $bookings[$i]['reference'];
    $client = $reference['client'];
    $supplier = $reference['supplier'];

    $hotel = $bookings[$i]['hotel'];
    $hotelCode = $hotel['hotelCode'];
    $hotelName = $hotel['hotelName'];
    $bookingDate = $hotel['bookingDate'];
    $start = $hotel['start'];
    $end = $hotel['end'];
    $boardCode = $hotel['boardCode'];
    $occupancies = $hotel['occupancies'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('booking');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'client' => $client,
            'supplier' => $supplier,
            'hotelcode' => $hotelCode,
            'hotelname' => $hotelName,
            'bookingdate' => $bookingDate,
            'start' => $start,
            'end' => $end,
            'boardCode' => $boardCode,
            'occupancies' => $occupancies
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $ex) {
        echo $return;
        echo "ERRO1: " . $ex;
        echo $return;
    }

    $rooms = $hotel['rooms'];
    for ($j=0; $j < count($rooms); $j++) { 
        $code = $rooms[$j]['code'];
        $description = $rooms[$j]['description'];
        $occupancyRefId = $rooms[$j]['occupancyRefId'];
        $price = $rooms[$j]['price'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('rooms_booking');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'code' => $code,
                'description' => $description,
                'occupancyrefid' => $occupancyRefId,
                'price' => $price,
                'client' => $client
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (Exception $ex) {
            echo $return;
            echo "ERRO2: " . $ex;
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