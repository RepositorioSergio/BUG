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
    $config = new \Zend\Config\Config(include '../config/autoload/global.globalia.php');
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

    while ($data = fgetcsv($object, 0, ";")) {
        if ($line > 0) {
            $id = $data[0];
            $name = $data[1];
            $active = $data[2];
            $cityid = $data[3];
            $cityname = $data[4];
            $destinationid = $data[5];
            $destinationname = $data[6];
            $category = $data[7];
            $address = $data[8];
            $latitude = $data[9];
            $longitude = $data[10];
            $countrycode = $data[11];
            $iata = $data[12];

            $name = utf8_encode($name);
            $cityid = utf8_encode($cityid);
            $cityname = utf8_encode($cityname);
            $destinationname = utf8_encode($destinationname);
            $address = utf8_encode($address);


            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('hdo_hotels');
                $select->where(array(
                    'id' => $id
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (string)$data['id'];
                    if ($id != "") {
                        $config = new \Zend\Config\Config(include '../config/autoload/global.globalia.php');
                        $config = [
                            'driver' => $config->db->driver,
                            'database' => $config->db->database,
                            'username' => $config->db->username,
                            'password' => $config->db->password,
                            'hostname' => $config->db->hostname
                        ];
                        $dbUpdate = new \Zend\Db\Adapter\Adapter($config);

                        $data = array(
                            'name' => $name,  
                            'active' => $active,
                            'cityid' => $cityid, 
                            'cityname' => $cityname,
                            'destinationid' => $destinationid, 
                            'destinationname' => $destinationname,
                            'category' => $category, 
                            'address' => $address,
                            'latitude' => $latitude, 
                            'longitude' => $longitude,
                            'countrycode' => $countrycode, 
                            'iata' => $iata
                        );
      
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('hdo_hotels');
                        $update->set($data);
                        $update->where(array('id' => $id));

                        $statement = $sql->prepareStatementForSqlObject($update);
                        $results = $statement->execute();
                        $dbUpdate->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hdo_hotels');
                        $insert->values(array(
                            'id' => $id,
                            'name' => $name,  
                            'active' => $active,
                            'cityid' => $cityid, 
                            'cityname' => $cityname,
                            'destinationid' => $destinationid, 
                            'destinationname' => $destinationname,
                            'category' => $category, 
                            'address' => $address,
                            'latitude' => $latitude, 
                            'longitude' => $longitude,
                            'countrycode' => $countrycode, 
                            'iata' => $iata
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
                    $insert->into('hdo_hotels');
                    $insert->values(array(
                        'id' => $id,
                        'name' => $name,  
                        'active' => $active,
                        'cityid' => $cityid, 
                        'cityname' => $cityname,
                        'destinationid' => $destinationid, 
                        'destinationname' => $destinationname,
                        'category' => $category, 
                        'address' => $address,
                        'latitude' => $latitude, 
                        'longitude' => $longitude,
                        'countrycode' => $countrycode, 
                        'iata' => $iata
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

readCSV("HDO_Inventory.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>