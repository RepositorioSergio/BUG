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
echo "COMECOU READ CSV<br/>";
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
    
function readCSV(string $filename){
    $config = new \Zend\Config\Config(include '../config/autoload/global.tourico.php');
    $config = [
        'driver' => $config->db->driver,
        'database' => $config->db->database,
        'username' => $config->db->username,
        'password' => $config->db->password,
        'hostname' => $config->db->hostname
    ];
    $db = new \Zend\Db\Adapter\Adapter($config);

    $object = fopen($filename, 'r');
    $line = 0;

    while ($data = fgetcsv($object, 0, "|")) {
        if ($line > 0) {
            $activityid = $data[0];
            $activityname = $data[1];
            $categoryid = $data[2];
            $categoryname = $data[3];
            $thumbnailpath = $data[4];
            $shortdescription = $data[5];
            $hotelcurrency = $data[6];
            $address = $data[7];
            $addresszip = $data[8];
            $addresscity = $data[9];
            $statecode = $data[10];
            $state = $data[11];
            $countrycode = $data[12];
            $countryname = $data[13];
            $longitude = $data[14];
            $latitude = $data[15];
            $destination = $data[16];
            $cityname = $data[17];
            $destinationid = $data[18];
            $timestamp = $data[19];
            $productstatus = $data[20];
            $rescount = $data[21];
            $priority = $data[22];

            $activityname = str_replace('"', '', $activityname);
            $activityname = mb_convert_encoding($activityname, "UTF-8");
            $categoryid = str_replace('"', '', $categoryid);
            $categoryid = mb_convert_encoding($categoryid, "UTF-8");
            $categoryname = str_replace('"', '', $categoryname);
            $categoryname = mb_convert_encoding($categoryname, "UTF-8");
            $thumbnailpath = str_replace('"', '', $thumbnailpath);
            $thumbnailpath = mb_convert_encoding($thumbnailpath, "UTF-8");
            $shortdescription = str_replace('"', '', $shortdescription);
            $shortdescription = mb_convert_encoding($shortdescription, "UTF-8");
            $hotelcurrency = str_replace('"', '', $hotelcurrency);
            $hotelcurrency = mb_convert_encoding($hotelcurrency, "UTF-8");
            $address = str_replace('"', '', $address);
            $address = mb_convert_encoding($address, "UTF-8");
            $addresszip = str_replace('"', '', $addresszip);
            $addresszip = mb_convert_encoding($addresszip, "UTF-8");
            $addresscity = str_replace('"', '', $addresscity);
            $addresscity = mb_convert_encoding($addresscity, "UTF-8");
            $statecode = str_replace('"', '', $statecode);
            $statecode = mb_convert_encoding($statecode, "UTF-8");
            $state = str_replace('"', '', $state);
            $state = mb_convert_encoding($state, "UTF-8");
            $countrycode = str_replace('"', '', $countrycode);
            $countrycode = mb_convert_encoding($countrycode, "UTF-8");
            $countryname = str_replace('"', '', $countryname);
            $countryname = mb_convert_encoding($countryname, "UTF-8");
            $longitude = str_replace('"', '', $longitude);
            $longitude = mb_convert_encoding($longitude, "UTF-8");
            $latitude = str_replace('"', '', $latitude);
            $latitude = mb_convert_encoding($latitude, "UTF-8");
            $destination = str_replace('"', '', $destination);
            $destination = mb_convert_encoding($destination, "UTF-8");
            $cityname = str_replace('"', '', $cityname);
            $cityname = mb_convert_encoding($cityname, "UTF-8");
            $destinationid = str_replace('"', '', $destinationid);
            $destinationid = mb_convert_encoding($destinationid, "UTF-8");
            $timestamp = str_replace('"', '', $timestamp);
            $timestamp = mb_convert_encoding($timestamp, "UTF-8");
            $productstatus = str_replace('"', '', $productstatus);
            $productstatus = mb_convert_encoding($productstatus, "UTF-8");
            $rescount = str_replace('"', '', $rescount);
            $rescount = mb_convert_encoding($rescount, "UTF-8");
            $priority = str_replace('"', '', $priority);
            $priority = mb_convert_encoding($priority, "UTF-8");

            try {               
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('activityinfo');
                $insert->values(array(
                    'activityid' => $activityid,
                    'activityname' => $activityname,
                    'categoryid' => $categoryid,
                    'categoryname' => $categoryname,
                    'thumbnailpath' => $thumbnailpath,
                    'shortdescription' => $shortdescription,
                    'hotelcurrency' => $hotelcurrency,
                    'address' => $address,
                    'addresszip' => $addresszip,
                    'addresscity' => $addresscity,
                    'statecode' => $statecode,
                    'state' => $state,
                    'countrycode' => $countrycode,
                    'countryname' => $countryname,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'destination' => $destination,
                    'cityname' => $cityname,
                    'destinationid' => $destinationid,
                    'timestamp' => $timestamp,
                    'productstatus' => $productstatus,
                    'rescount' => $rescount,
                    'priority' => $priority
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO: ". $e;
                echo $return;
            }
        }
        $line = $line + 1;
    }
    fclose($filename);
}

readCSV("PDS2_ActivityInfo_THF.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>