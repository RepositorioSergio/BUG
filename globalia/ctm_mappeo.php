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
        $giata = $data[0];
        $hotel_id = $data[1];
        $category_id = $data[2];
        $type_hotel = $data[3];
        $cadhot_id = $data[4];
        $name_hotel = $data[5];
        $country_id = $data[6];
        $province_id = $data[7];
        $town_id = $data[8];
        $name_town = $data[9];
        $street = $data[10];
        $zipcode = $data[11];
        $latitude = $data[12];
        $longitude = $data[13];
        $phone = $data[14];
        $fax = $data[15];
        $checkin = $data[16];
        $checkout = $data[17];

        $name_hotel = utf8_encode($name_hotel);
        $street = utf8_encode($street);
        $zipcode = utf8_encode($zipcode);
        $name_town = utf8_encode($name_town);

        echo $return;
        echo "ID: ". $hotel_id . "<br/>";
        echo $return;

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('ctm_mappeo');
            $select->where(array(
                'hotel_id' => $hotel_id
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $hotel_id = (int)$data['hotel_id'];
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
                        'giata' => $giata,
                        'category_id' => $category_id, 
                        'type_hotel' => $type_hotel, 
                        'cadhot_id' => $cadhot_id, 
                        'name_hotel' => $name_hotel, 
                        'country_id' => $country_id, 
                        'province_id' => $province_id,
                        'town_id' => $town_id,
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
                    $update->table('ctm_mappeo');
                    $update->set($data);
                    $update->where(array('hotel_id' => $hotel_id));

                    $statement = $sql->prepareStatementForSqlObject($update);
                    $results = $statement->execute();
                    $dbUpdate->getDriver()
                    ->getConnection()
                    ->disconnect(); 
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('ctm_mappeo');
                    $insert->values(array(
                        'hotel_id' => $hotel_id,
                        'giata' => $giata,
                        'category_id' => $category_id, 
                        'type_hotel' => $type_hotel, 
                        'cadhot_id' => $cadhot_id, 
                        'name_hotel' => $name_hotel, 
                        'country_id' => $country_id, 
                        'province_id' => $province_id,
                        'town_id' => $town_id,
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
                $insert->into('ctm_mappeo');
                $insert->values(array(
                    'hotel_id' => $hotel_id,
                    'giata' => $giata,
                    'category_id' => $category_id, 
                    'type_hotel' => $type_hotel, 
                    'cadhot_id' => $cadhot_id, 
                    'name_hotel' => $name_hotel, 
                    'country_id' => $country_id, 
                    'province_id' => $province_id,
                    'town_id' => $town_id,
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
        echo $return;
        echo "line: ". $line . "<br/>";
        echo $return;

        $line = $line + 1;
    }
    fclose($filename);
}

readCSV("Livro1.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>