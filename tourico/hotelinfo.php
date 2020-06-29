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
            $hotelid = $data[0];
            $hotelname = $data[1];
            $exclusivedeal = $data[2];
            $checkinhour = $data[3];
            $checkouthour = $data[4];
            $totalroomsinhotel = $data[5];
            $thumbnailpath = $data[6];
            $shortdescription = $data[7];
            $hotelcurrency = $data[8];
            $stars = $data[9];
            $address = $data[10];
            $addresszip = $data[11];
            $addresscity = $data[12];
            $location = $data[13];
            $statecode = $data[14];
            $state = $data[15];
            $countrycode = $data[16];
            $countryname = $data[17];
            $longitude = $data[18];
            $latitude = $data[19];
            $sdestination = $data[20];
            $shotelcityname = $data[21];
            $destinationid = $data[22];
            $provider = $data[23];
            $phone = $data[24];
            $fax = $data[25];
            $nearestairportiatacode = $data[26];
            $refdirection = $data[27];
            $refpointdist = $data[28];
            $distunit = $data[29];
            $timestamp = $data[30];
            $productstatus = $data[31];

            $hotelname = str_replace('"', '', $hotelname);
            $hotelname = mb_convert_encoding($hotelname, "UTF-8");
            $exclusivedeal = str_replace('"', '', $exclusivedeal);
            $checkinhour = str_replace('"', '', $checkinhour);
            $checkouthour = str_replace('"', '', $checkouthour);
            $totalroomsinhotel = str_replace('"', '', $totalroomsinhotel);
            $thumbnailpath = str_replace('"', '', $thumbnailpath);
            $shortdescription = str_replace('"', '', $shortdescription);
            $shortdescription = mb_convert_encoding($shortdescription, "UTF-8");
            $hotelcurrency = str_replace('"', '', $hotelcurrency);
            $stars = str_replace('"', '', $stars);
            $address = str_replace('"', '', $address);
            $address = mb_convert_encoding($address, "UTF-8");
            $addresszip = str_replace('"', '', $addresszip);
            $addresszip = mb_convert_encoding($addresszip, "UTF-8");
            $addresscity = str_replace('"', '', $addresscity);
            $addresscity = mb_convert_encoding($addresscity, "UTF-8");
            $location = str_replace('"', '', $location);
            $location = mb_convert_encoding($location, "UTF-8");
            $statecode = str_replace('"', '', $statecode);
            $state = str_replace('"', '', $state);
            $countrycode = str_replace('"', '', $countrycode);
            $countryname = str_replace('"', '', $countryname);
            $longitude = str_replace('"', '', $longitude);
            $latitude = str_replace('"', '', $latitude);
            $sdestination = str_replace('"', '', $sdestination);
            $shotelcityname = str_replace('"', '', $shotelcityname);
            $destinationid = str_replace('"', '', $destinationid);
            $provider = str_replace('"', '', $provider);
            $phone = str_replace('"', '', $phone);
            $fax = str_replace('"', '', $fax);
            $nearestairportiatacode = str_replace('"', '', $nearestairportiatacode);
            $refdirection = str_replace('"', '', $refdirection);
            $refpointdist = str_replace('"', '', $refpointdist);
            $distunit = str_replace('"', '', $distunit);
            $timestamp = str_replace('"', '', $timestamp);
            $productstatus = str_replace('"', '', $productstatus);

            try {               
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotelinfo');
                $insert->values(array(
                    'hotelid' => $hotelid,
                    'hotelname' => $hotelname,
                    'exclusivedeal' => $exclusivedeal,
                    'checkinhour' => $checkinhour,
                    'checkouthour' => $checkouthour,
                    'totalroomsinhotel' => $totalroomsinhotel,
                    'thumbnailpath' => $thumbnailpath,
                    'shortdescription' => $shortdescription,
                    'hotelcurrency' => $hotelcurrency,
                    'stars' => $stars,
                    'address' => $address,
                    'addresszip' => $addresszip,
                    'addresscity' => $addresscity,
                    'location' => $location,
                    'statecode' => $statecode,
                    'state' => $state,
                    'countrycode' => $countrycode,
                    'countryname' => $countryname,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'sdestination' => $sdestination,
                    'shotelcityname' => $shotelcityname,
                    'destinationid' => $destinationid,
                    'provider' => $provider,
                    'phone' => $phone,
                    'fax' => $fax,
                    'nearestairportiatacode' => $nearestairportiatacode,
                    'refdirection' => $refdirection,
                    'refpointdist' => $refpointdist,
                    'distunit' => $distunit,
                    'timestamp' => $timestamp,
                    'productstatus' => $productstatus  
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

readCSV("HotelInfo/PDS2_HotelInfo_THF.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>