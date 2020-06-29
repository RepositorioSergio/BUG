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
            $id = $data[0];
            $carcompany = $data[1];
            $carcompanyid = $data[2];
            $stationname = $data[3];
            $address = $data[4];
            $city = $data[5];
            $destinationid = $data[6];
            $zipcode = $data[7];
            $statecode = $data[8];
            $countrycode = $data[9];
            $phone = $data[10];
            $latitude = $data[11];
            $longitude = $data[12];
            $airportcode = $data[13];
            echo $id . "ID<br/>";

            try {               
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('carstations');
                $insert->values(array(
                    'id' => $id,
                    'carcompany' => $carcompany,
                    'carcompanyid' => $carcompanyid,
                    'stationname' => $stationname,
                    'address' => $address,
                    'city' => $city,
                    'destinationid' => $destinationid,
                    'zipcode' => $zipcode,
                    'statecode' => $statecode,
                    'countrycode' => $countrycode,
                    'phone' => $phone,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'airportcode' => $airportcode 
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

readCSV("PDS2_CarStationsList.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>