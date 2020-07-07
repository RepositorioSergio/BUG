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
    $config = new \Zend\Config\Config(include '../config/autoload/global.goglobal.php');
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
        $id = $line;
        if ($line > 0) {
            $countryid = $data[0];
            $country = $data[1];
            $isocode = $data[2];
            $cityid = $data[3];
            $city = $data[4];
            $hotelid = $data[5];
            $name = $data[6];
            $address = $data[7];
            $phone = $data[8];
            $fax = $data[9];
            $stars = $data[10];
            $starsid = $data[11];
            $longitude = $data[12];
            $latitude = $data[13];
            $isapartment = $data[14];

            $country = str_replace('"', '', $country);
            $isocode = str_replace('"', '', $isocode);
            $cityid = str_replace('"', '', $cityid);
            $city = str_replace('"', '', $city);
            $hotelid = str_replace('"', '', $hotelid);
            $name = str_replace('"', '', $name);
            $address = str_replace('"', '', $address);
            $phone = str_replace('"', '', $phone);
            $fax = str_replace('"', '', $fax);
            $stars = str_replace('"', '', $stars);
            $starsid = str_replace('"', '', $starsid);
            $longitude = str_replace('"', '', $longitude);
            $latitude = str_replace('"', '', $latitude);
            $isapartment = str_replace('"', '', $isapartment);

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('goglobal');
                $select->where(array(
                    'id' => $id,
                    'hotelid' => $hotelid
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (string)$data['id'];
                    if ($id != "") {
                        $config = new \Zend\Config\Config(include '../config/autoload/global.goglobal.php');
                        $config = [
                            'driver' => $config->db->driver,
                            'database' => $config->db->database,
                            'username' => $config->db->username,
                            'password' => $config->db->password,
                            'hostname' => $config->db->hostname
                        ];
                        $dbUpdate = new \Zend\Db\Adapter\Adapter($config);

                        $data = array(
                            'countryid' => $countryid, 
                            'country' => $country, 
                            'isocode' => $isocode, 
                            'cityid' => $cityid, 
                            'city' => $city, 
                            'hotelid' => $hotelid,
                            'name' => $name, 
                            'address' => $address, 
                            'phone' => $phone, 
                            'fax' => $fax, 
                            'stars' => $stars, 
                            'starsid' => $starsid,
                            'longitude' => $longitude, 
                            'latitude' => $latitude, 
                            'isapartment' => $isapartment 
                            );
      
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('goglobal');
                        $update->set($data);
                        $update->where(array('id' => $id, 'hotelid' => $hotelid));

                        $statement = $sql->prepareStatementForSqlObject($update);
                        $results = $statement->execute();
                        $dbUpdate->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('goglobal');
                        $insert->values(array(
                            'countryid' => $countryid, 
                            'country' => $country, 
                            'isocode' => $isocode, 
                            'cityid' => $cityid, 
                            'city' => $city, 
                            'hotelid' => $hotelid,
                            'name' => $name, 
                            'address' => $address, 
                            'phone' => $phone, 
                            'fax' => $fax, 
                            'stars' => $stars, 
                            'starsid' => $starsid,
                            'longitude' => $longitude, 
                            'latitude' => $latitude, 
                            'isapartment' => $isapartment 
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
                    $insert->into('goglobal');
                    $insert->values(array(
                        'countryid' => $countryid, 
                        'country' => $country, 
                        'isocode' => $isocode, 
                        'cityid' => $cityid, 
                        'city' => $city, 
                        'hotelid' => $hotelid,
                        'name' => $name, 
                        'address' => $address, 
                        'phone' => $phone, 
                        'fax' => $fax, 
                        'stars' => $stars, 
                        'starsid' => $starsid,
                        'longitude' => $longitude, 
                        'latitude' => $latitude, 
                        'isapartment' => $isapartment 
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
        $line = $line + 1;
    }
    fclose($filename);
}

readCSV("Extended.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>