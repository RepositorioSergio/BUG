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
            $roomtypename = $data[3];
            $hotelroomtypeid = $data[4];
            $fromdate = $data[5];
            $todate = $data[6];
            $promotiontype = $data[7];
            $value = $data[8];
            $remarks = $data[9];
            $hotelcurrency = $data[10];
            $stars = $data[11];
            $address = $data[12];
            $addresszip = $data[13];
            $addresscity = $data[14];
            $location = $data[15];
            $statecode = $data[16];
            $state = $data[17];
            $countrycode = $data[18];
            $countryname = $data[19];
            $longitude = $data[20];
            $latitude = $data[21];
            $sdestination = $data[22];
            $shotelcityname = $data[23];
            $destinationid = $data[24];
            $provider = $data[25];

            $hotelname = str_replace('"', '', $hotelname);
            $hotelname = mb_convert_encoding($hotelname, "UTF-8");
            $exclusivedeal = str_replace('"', '', $exclusivedeal);
            $roomtypename = str_replace('"', '', $roomtypename);
            $hotelroomtypeid = str_replace('"', '', $hotelroomtypeid);
            $fromdate = str_replace('"', '', $fromdate);
            $todate = str_replace('"', '', $todate);
            $promotiontype = str_replace('"', '', $promotiontype);
            $value = str_replace('"', '', $value);
            $remarks = str_replace('"', '', $remarks);
            $remarks = mb_convert_encoding($remarks, "UTF-8");
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

            try {               
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('promotionlist');
                $insert->values(array(
                    'hotelid' => $hotelid,
                    'hotelname' => $hotelname,
                    'exclusivedeal' => $exclusivedeal,
                    'roomtypename' => $roomtypename,
                    'hotelroomtypeid' => $hotelroomtypeid,
                    'fromdate' => $fromdate,
                    'todate' => $todate,
                    'promotiontype' => $promotiontype,
                    'value' => $value,
                    'remarks' => $remarks,
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
                    'provider' => $provider    
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

readCSV("HotelInfo/THF_PromotionList.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>