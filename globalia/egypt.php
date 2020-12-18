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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.egypt.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "Livro1.csv";
$object = fopen($filename, 'r');
$line = 0;

while ($data = fgetcsv($object, 0, ";")) {
    if ($line > 0) {
        $id = $data[0];
        $hotel = utf8_encode($data[1]);
        $address = utf8_encode($data[2]);
        $code1 = $data[3];
        $city = $data[4];
        $syszone = $data[5];
        $code2 = $data[6];
        $state = $data[7];
        $code3 = $data[8];
        $country = $data[9];

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('xmlhotels');
            $select->where(array(
                'id' => $id
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (int)$data['id'];
                if ($id > 0) {
                    $config = new \Zend\Config\Config(include '../config/autoload/global.egypt.php');
                    $config = [
                        'driver' => $config->db->driver,
                        'database' => $config->db->database,
                        'username' => $config->db->username,
                        'password' => $config->db->password,
                        'hostname' => $config->db->hostname
                    ];
                    $dbUpdate = new \Zend\Db\Adapter\Adapter($config);

                    $data = array(
                        'datetime_updated' => time(),
                        'hotel' => $hotel,
                        'address' => $address,
                        'code1' => $code1,
                        'city' => $city,
                        'syszone' => $syszone,
                        'code2' => $code2,
                        'state' => $state,
                        'code3' => $code3,
                        'country' => $country
                        );      
                    $sql    = new Sql($dbUpdate);
                    $update = $sql->update();
                    $update->table('xmlhotels');
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
                    $insert->into('xmlhotels');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_updated' => time(),
                        'hotel' => $hotel,
                        'address' => $address,
                        'code1' => $code1,
                        'city' => $city,
                        'syszone' => $syszone,
                        'code2' => $code2,
                        'state' => $state,
                        'code3' => $code3,
                        'country' => $country
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
                $insert->into('xmlhotels');
                $insert->values(array(
                    'id' => $id,
                    'datetime_updated' => time(),
                    'hotel' => $hotel,
                    'address' => $address,
                    'code1' => $code1,
                    'city' => $city,
                    'syszone' => $syszone,
                    'code2' => $code2,
                    'state' => $state,
                    'code3' => $code3,
                    'country' => $country
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>