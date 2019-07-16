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
echo "COMECOU GIATA FALSE<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$user = 'wingstest';
$pass = 'Win@59491374';

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
<hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
</hot:Credentials>
<wsa:Action>http://TekTravel/HotelBookingApi/GiataHotelCodes</wsa:Action>
<wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
<hot:GiataHotelCodesRequest>
<hot:CityCode>115936</hot:CityCode>
<hot:IsDetailedResponse>false</hot:IsDetailedResponse>
</hot:GiataHotelCodesRequest>
</soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Content-length: ".strlen($raw)
));
$url =  "https://api.tbotechnology.in/HotelAPI_V7/HotelService.svc";

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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$GiataHotelCodesResponse = $Body->item(0)->getElementsByTagName("GiataHotelCodesResponse");

$Hotels = $GiataHotelCodesResponse->item(0)->getElementsByTagName("Hotels");
if ($Hotels->length > 0) {
    $Hotel = $Hotels->item(0)->getElementsByTagName("Hotel");
    if ($Hotel->length > 0) {
        for ($i=0; $i < $Hotel->length; $i++) { 
            $HotelCode = $Hotel->item($i)->getAttribute("HotelCode");
            $HotelName = $Hotel->item($i)->getAttribute("HotelName");
            $HotelAddress = $Hotel->item($i)->getAttribute("HotelAddress");
            $Latitude = $Hotel->item($i)->getAttribute("Latitude");
            $Longitude = $Hotel->item($i)->getAttribute("Longitude");
            $StarRating = $Hotel->item($i)->getAttribute("StarRating");
            $CountryName = $Hotel->item($i)->getAttribute("CountryName");
            $CityName = $Hotel->item($i)->getAttribute("CityName");

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('GiataHotelCodesFalse');
                $insert->values(array(
                    'HotelCode' => $HotelCode,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'HotelName' => $HotelName,
                    'HotelAddress' => $HotelAddress,
                    'Latitude' => $Latitude,
                    'Longitude' => $Longitude,
                    'StarRating' => $StarRating,
                    'CountryName' => $CountryName,
                    'CityName' => $CityName
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
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>