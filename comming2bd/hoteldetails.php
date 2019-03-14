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
echo "COMECOU HOTEL DETAILS";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.comming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT Code FROM hoteis";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $Code = $row->Code;

        $passuser = "COSTAMAR:COSTAMAR";
        $auth = base64_encode($passuser);

        $raw = '{
            "Language": "ESP",
            "HotelCode": "' . $Code . '"
        }';

        $client = new Client();
        $client->setOptions(array(
            'timeout' => 100,
            'sslverifypeer' => false,
            'sslverifyhost' => false
        ));
        $client->setHeaders(array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Basic " . $auth,
            "Content-length: " . strlen($raw)
        ));
        $client->setUri('http://services-pre.bedbank.coming2.com/hotel-api/api/Hotel/Details');
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

        $response = json_decode($response, true);


        echo "<xmp>";
        var_dump($response);
        echo "</xmp>";

        $config = new \Zend\Config\Config(include '../config/autoload/global.comming2.php');
        $config = [
            'driver' => $config->db->driver,
            'database' => $config->db->database,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'hostname' => $config->db->hostname
        ];
        $db = new \Zend\Db\Adapter\Adapter($config);

        for ($i=0; $i < count($response); $i++) { 
            $Code = $response[$i]['Code'];
            $Name = $response[$i]['Name'];
            $Description2 = $response[$i]['Description2'];
            $Latitude = $response[$i]['Latitude'];
            $Longitude = $response[$i]['Longitude'];
            $CountryISOCode = $response[$i]['CountryISOCode'];
            $Town = $response[$i]['Town'];
            $ZipCode = $response[$i]['ZipCode'];
            $HowToGet = $response[$i]['HowToGet'];
            $RoomDescription = $response[$i]['RoomDescription'];
            $SituationDescription = $response[$i]['SituationDescription'];
            $Address = $response[$i]['Address'];
            $AccommodationType = $response[$i]['AccommodationType'];
            $Category = $response[$i]['Category'];
            $Contact = $response[$i]['Contact'];
            $BookingContact = $response[$i]['BookingContact'];
            $CategoryName = $response[$i]['CategoryName'];
            $Description = $response[$i]['Description'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hoteldetails');
                $insert->values(array(
                    'Code' => $Code,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Name' => $Name,
                    'Description2' => $Description2,
                    'Latitude' => $Latitude,
                    'Longitude' => $Longitude,
                    'CountryISOCode' => $CountryISOCode,
                    'Town' => $Town,
                    'ZipCode' => $ZipCode,
                    'HowToGet' => $HowToGet,
                    'RoomDescription' => $RoomDescription,
                    'SituationDescription' => $SituationDescription,
                    'Address' => $Address,
                    'AccommodationType' => $AccommodationType,
                    'Category' => $Category,
                    'Contact' => $Contact,
                    'BookingContact' => $BookingContact,
                    'CategoryName' => $CategoryName,
                    'Description' => $Description
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error: " . $e;
                echo $return;
            }

            $Images = $response[$i]['Images'];
            for ($j=0; $j < count($Images); $j++) { 
                $Url = $Images[$j]['Url'];
                $Classification = $Images[$j]['Classification'];
                $Source = $Images[$j]['Source'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hoteldetails_Images');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Url' => $Url,
                        'Classification' => $Classification,
                        'Source' => $Source,
                        'Code' => $Code
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error IMG: " . $e;
                    echo $return;
                }
            }

            $Areas = $response[$i]['Areas'];
            for ($k=0; $k < count($Areas); $k++) { 
                $Type = $Areas[$k]['Type'];
                $CodeAreas = $Areas[$k]['Code'];
                $Name = $Areas[$k]['Name'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hoteldetails_Areas');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Code' => $CodeAreas,
                        'Type' => $Type,
                        'Name' => $Name,
                        'CodeHotel' => $Code
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error IMG: " . $e;
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