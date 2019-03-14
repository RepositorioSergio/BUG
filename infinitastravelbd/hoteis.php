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
echo "COMECOU HOTEIS<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.infinitastravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT Code FROM cities";
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
        echo $return;
        echo $Code;
        echo $return;

        $client = new Client();
        $client->setOptions(array(
            'timeout' => 100,
            'sslverifypeer' => false,
            'sslverifyhost' => false
        ));

        $url = "http://infinitash.redirectme.net/hubserver/hotel/list?partner_id=XCTM&user_name=ctmtour&password=ctm01&target=1&version=1.3&language=0&city=$Code";
        echo $return;
        echo $url;
        echo $return;

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
        echo "<br/>RESPONSE";
        echo '<xmp>';
        var_dump($response);
        echo '</xmp>';

        $config = new \Zend\Config\Config(include '../config/autoload/global.infinitastravel.php');
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
        $HUB_HotelSearchRS = $inputDoc->getElementsByTagName("HUB_HotelSearchRS");
        $Hotels = $HUB_HotelSearchRS->item(0)->getElementsByTagName("Hotels");
        $node = $Hotels->item(0)->getElementsByTagName("Hotel");
        for ($i=0; $i < $node->length; $i++) { 
            $Code = $node->item($i)->getAttribute("Code");
            $Name = $node->item($i)->getAttribute("Name");
            $StarRating = $node->item($i)->getAttribute("StarRating");
            $Description = $node->item($i)->getElementsByTagName("Description");
            if ($Description->length > 0) {
                $Description = $Description->item(0)->nodeValue;
            } else {
                $Description = "";
            }
            $MainPhoto = $node->item($i)->getElementsByTagName("MainPhoto");
            if ($MainPhoto->length > 0) {
                $MainPhoto = $MainPhoto->item(0)->nodeValue;
            } else {
                $MainPhoto = "";
            }

            $Address = $node->item($i)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $Latitude = $Address->item(0)->getAttribute("Latitude");
                $Longitude = $Address->item(0)->getAttribute("Longitude");

                $City = $Address->item(0)->getElementsByTagName("City");
                if ($City->length > 0) {
                    $CityCode = $City->item(0)->getAttribute("Code");
                    $CityName = $City->item(0)->getAttribute("Name");
                    $CountryCode = $City->item(0)->getAttribute("CountryCode");
                } else {
                    $Code = "";
                    $Name = "";
                    $CountryCode = "";
                }
                $Address2 = $Address->item(0)->getElementsByTagName("Address");
                if ($Address2->length > 0) {
                    $Address2 = $Address2->item(0)->nodeValue;
                } else {
                    $Address2 = "";
                }
            }

            $PhoneNumbers = $node->item($i)->getElementsByTagName("PhoneNumbers");
            if ($PhoneNumbers->length > 0) {
                $PhoneNumber = $PhoneNumbers->item(0)->getElementsByTagName("PhoneNumber");
                if ($PhoneNumber->length > 0) {
                    $LineNumber = $PhoneNumber->item(0)->getAttribute("LineNumber");
                    $Prefix = $PhoneNumber->item(0)->getAttribute("Prefix");
                    $CountryAccessCode = $PhoneNumber->item(0)->getAttribute("CountryAccessCode");
                    $AreaCityCode = $PhoneNumber->item(0)->getAttribute("AreaCityCode");
                } else {
                    $LineNumber = "";
                    $Prefix = "";
                    $CountryAccessCode = "";
                    $AreaCityCode = "";
                }
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hoteis');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Code' => $Code,
                    'Name' => $Name,
                    'StarRating' => $StarRating,
                    'Description' => $Description,
                    'MainPhoto' => $MainPhoto,
                    'Latitude' => $Latitude,
                    'Longitude' => $Longitude,
                    'CityCode' => $CityCode,
                    'CityName' => $CityName,
                    'CountryCode' => $CountryCode,
                    'Address' => $Address2,
                    'LineNumber' => $LineNumber,
                    'Prefix' => $Prefix,
                    'CountryAccessCode' => $CountryAccessCode,
                    'AreaCityCode' => $AreaCityCode
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