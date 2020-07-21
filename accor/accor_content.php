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
echo "COMECOU READ XML<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.accor.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'content.xml';
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$repositoryContent = $inputDoc->getElementsByTagName("repositoryContent");
$hotels = $repositoryContent->item(0)->getElementsByTagName('hotels');
if ($hotels->length > 0) {
    $hotel = $hotels->item(0)->getElementsByTagName('hotel');
    if ($hotel->length > 0) {
        for ($i=0; $i < $hotel->length; $i++) { 
            $hotelCode = $hotel->item($i)->getElementsByTagName('hotelCode');
            if ($hotelCode->length > 0) {
                $hotelCode = $hotelCode->item(0)->nodeValue;
            } else {
                $hotelCode = "";
            }
            $hotelName = $hotel->item($i)->getElementsByTagName('hotelName');
            if ($hotelName->length > 0) {
                $hotelName = $hotelName->item(0)->nodeValue;
            } else {
                $hotelName = "";
            }
            $file = $hotel->item($i)->getElementsByTagName('file');
            if ($file->length > 0) {
                $file = $file->item(0)->nodeValue;
            } else {
                $file = "";
            }
            $lastModified = $hotel->item($i)->getElementsByTagName('lastModified');
            if ($lastModified->length > 0) {
                $lastModified = $lastModified->item(0)->nodeValue;
            } else {
                $lastModified = "";
            }
            $creationTime = $hotel->item($i)->getElementsByTagName('creationTime');
            if ($creationTime->length > 0) {
                $creationTime = $creationTime->item(0)->nodeValue;
            } else {
                $creationTime = "";
            }
            $latitude = $hotel->item($i)->getElementsByTagName('latitude');
            if ($latitude->length > 0) {
                $latitude = $latitude->item(0)->nodeValue;
            } else {
                $latitude = "";
            }
            $longitude = $hotel->item($i)->getElementsByTagName('longitude');
            if ($longitude->length > 0) {
                $longitude = $longitude->item(0)->nodeValue;
            } else {
                $longitude = "";
            }
            $brandCode = $hotel->item($i)->getElementsByTagName('brandCode');
            if ($brandCode->length > 0) {
                $brandCode = $brandCode->item(0)->nodeValue;
            } else {
                $brandCode = "";
            }
            $brandName = $hotel->item($i)->getElementsByTagName('brandName');
            if ($brandName->length > 0) {
                $brandName = $brandName->item(0)->nodeValue;
            } else {
                $brandName = "";
            }
            $cityName = $hotel->item($i)->getElementsByTagName('cityName');
            if ($cityName->length > 0) {
                $cityName = $cityName->item(0)->nodeValue;
            } else {
                $cityName = "";
            }
            $countryCode = $hotel->item($i)->getElementsByTagName('countryCode');
            if ($countryCode->length > 0) {
                $countryCode = $countryCode->item(0)->nodeValue;
            } else {
                $countryCode = "";
            }
            $countryName = $hotel->item($i)->getElementsByTagName('countryName');
            if ($countryName->length > 0) {
                $countryName = $countryName->item(0)->nodeValue;
            } else {
                $countryName = "";
            }
            $hotelStatus = $hotel->item($i)->getElementsByTagName('hotelStatus');
            if ($hotelStatus->length > 0) {
                $hotelStatus = $hotelStatus->item(0)->nodeValue;
            } else {
                $hotelStatus = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('accor_hotels');
                $insert->values(array(
                    'id' => $hotelCode,
                    'name' => $hotelName,
                    'file' => $file,
                    'lastmodified' => $lastModified,
                    'creationtime' => $creationTime,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'brandcode' => $brandCode,
                    'brandname' => $brandName,
                    'cityname' => $cityName,
                    'countrycode' => $countryCode,
                    'countryname' => $countryName,
                    'hotelstatus' => $hotelStatus
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
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
