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
            $jpcode = $data[0];
            $description = $data[1];
            $category = $data[2];
            $categorydescription = $data[3];
            $address = $data[4];
            $latitude = $data[5];
            $longitude = $data[6];
            $codecountry = $data[7];
            $country = $data[8];
            $codestate = $data[9];
            $state = $data[10];
            $codecity = $data[11];
            $city = $data[12];

            $description = utf8_encode($description);
            $address = utf8_encode($address);
            $city = utf8_encode($city);
            if ($category === "") {
                $category = 0;
            }

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('w2m_hotels');
                $select->where(array(
                    'jpcode' => $jpcode
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $jpcode = (string)$data['jpcode'];
                    if ($id_hotel != "") {
                        $config = new \Zend\Config\Config(include '../config/autoload/global.globalia.php');
                        $config = [
                            'driver' => $config->db->driver,
                            'database' => $config->db->database,
                            'username' => $config->db->username,
                            'password' => $config->db->password,
                            'hostname' => $config->db->hostname
                        ];
                        $dbUpdate = new \Zend\Db\Adapter\Adapter($config);
                        echo $jpcode . "<br/>";

                        $data = array(
                            'jpcode' => $jpcode
                            );      
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('w2m_hotels');
                        $update->set($data);
                        $update->where(array('jpcode' => $jpcode));

                        $statement = $sql->prepareStatementForSqlObject($update);
                        $results = $statement->execute();
                        $dbUpdate->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('w2m_hotels');
                        $insert->values(array(
                            'description' => $description,
                            'code' => $jpcode, 
                            'jpdcode' => "", 
                            'jpcode' => $jpcode, 
                            'stars' => $category, 
                            'address_1' => $address, 
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'city_name' => $city, 
                            'zipcode' => "", 
                            'phone' => "", 
                            'xmldata' => "", 
                            'long_description' => "",
                            'additional_description' => "", 
                            'short_description' => "", 
                            'email' => "",
                            'checkin' => "",
                            'checkout' => "",
                            'chainname' => "", 
                            'areaid' => 0, 
                            'accommodationtype' => "", 
                            'accommodationtype_id' => 0,
                            'datetime_created' => time(), 
                            'datetime_updated' => time() 
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
                    $insert->into('w2m_hotels');
                    $insert->values(array(
                        'description' => $description,
                        'code' => $jpcode, 
                        'jpdcode' => "", 
                        'jpcode' => $jpcode, 
                        'stars' => $category, 
                        'address_1' => $address, 
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'city_name' => $city, 
                        'zipcode' => "", 
                        'phone' => "", 
                        'xmldata' => "", 
                        'long_description' => "",
                        'additional_description' => "", 
                        'short_description' => "", 
                        'email' => "",
                        'checkin' => "",
                        'checkout' => "",
                        'chainname' => "", 
                        'areaid' => 0, 
                        'accommodationtype' => "", 
                        'accommodationtype_id' => 0,
                        'datetime_created' => time(), 
                        'datetime_updated' => time()  
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

readCSV("Plantilla_Maxim1.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>