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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.rakuten.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "region_list.csv";
$object = fopen($filename, 'r');
$line = 0;

while ($data = fgetcsv($object, 0, ",")) {
    if ($line > 0) {
        $id = $data[0];
        $region_type = $data[1];
        $name = $data[2];
        $name_long = $data[3];
        $parentcode = $data[4];
        $searchable = $data[5];

        $name = utf8_encode($name);
        $name_long = utf8_encode($name_long);

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('regions');
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
                        'datetime_updated' => time(),
                        'name' => $name, 
                        'name_long' => $name_long, 
                        'region_type' => $region_type, 
                        'parentcode' => $parentcode, 
                        'searchable' => $searchable 
                    );
  
                    $sql    = new Sql($dbUpdate);
                    $update = $sql->update();
                    $update->table('regions');
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
                    $insert->into('regions');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_updated' => time(),
                        'name' => $name, 
                        'name_long' => $name_long, 
                        'region_type' => $region_type, 
                        'parentcode' => $parentcode, 
                        'searchable' => $searchable 
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
                $insert->into('regions');
                $insert->values(array(
                    'id' => $id,
                    'datetime_updated' => time(),
                    'name' => $name, 
                    'name_long' => $name_long, 
                    'region_type' => $region_type, 
                    'parentcode' => $parentcode, 
                    'searchable' => $searchable  
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