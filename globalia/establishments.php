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

    while ($data = fgetcsv($object, 0, "|")) {
        if ($line > 0) {
            $id_hotel = $data[0];
            $id_category = $data[1];
            $type_hotel = $data[2];
            $id_cadhot = $data[3];
            $hotel = $data[4];
            $id_country = $data[5];
            $id_province = $data[6];
            $id_town = $data[7];
            $name_town = $data[8];
            $street = $data[9];
            $zipcode = $data[10];
            $latitude = $data[11];
            $longitude = $data[12];
            $phone = $data[13];
            $fax = $data[14];
            $checkin = $data[15];
            $checkout = $data[16];

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('establishments');
                $select->where(array(
                    'id_hotel' => $id_hotel
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id_hotel = (int)$data['id_hotel'];
                    if ($id_hotel > 0) {
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
                            'id_category' => $id_category, 
                            'type_hotel' => $type_hotel, 
                            'id_cadhot' => $id_cadhot, 
                            'hotel' => $hotel, 
                            'id_country' => $id_country, 
                            'id_province' => $id_province,
                            'id_town' => $id_town,
                            'name_town' => $name_town, 
                            'street' => $street, 
                            'zipcode' => $zipcode, 
                            'latitude' => $latitude, 
                            'longitude' => $longitude, 
                            'phone' => $phone,
                            'fax' => $fax, 
                            'checkin' => $checkin, 
                            'checkout' => $checkout 
                            );      
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('establishments');
                        $update->set($data);
                        $update->where(array('id_hotel' => $id_hotel));

                        $statement = $sql->prepareStatementForSqlObject($update);
                        $results = $statement->execute();
                        $dbUpdate->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('establishments');
                        $insert->values(array(
                            'id_hotel' => $id_hotel,
                            'id_category' => $id_category, 
                            'type_hotel' => $type_hotel, 
                            'id_cadhot' => $id_cadhot, 
                            'hotel' => $hotel, 
                            'id_country' => $id_country, 
                            'id_province' => $id_province,
                            'id_town' => $id_town,
                            'name_town' => $name_town, 
                            'street' => $street, 
                            'zipcode' => $zipcode, 
                            'latitude' => $latitude, 
                            'longitude' => $longitude, 
                            'phone' => $phone,
                            'fax' => $fax, 
                            'checkin' => $checkin, 
                            'checkout' => $checkout 
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
                    $insert->into('establishments');
                    $insert->values(array(
                        'id_hotel' => $id_hotel,
                        'id_category' => $id_category, 
                        'type_hotel' => $type_hotel, 
                        'id_cadhot' => $id_cadhot, 
                        'hotel' => $hotel, 
                        'id_country' => $id_country, 
                        'id_province' => $id_province,
                        'id_town' => $id_town,
                        'name_town' => $name_town, 
                        'street' => $street, 
                        'zipcode' => $zipcode, 
                        'latitude' => $latitude, 
                        'longitude' => $longitude, 
                        'phone' => $phone,
                        'fax' => $fax, 
                        'checkin' => $checkin, 
                        'checkout' => $checkout  
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

readCSV("establishments.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>