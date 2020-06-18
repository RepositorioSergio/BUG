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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.iterpec.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "data/cangooroo_data_cache_hotels_description_v2_es.csv";
$object = fopen($filename, 'r');
$line = 0;

while ($data = fgetcsv($object, 0, "|")) {
    if ($line > 0) {
        $id = $data[0];
        $description_pt = "";
        $description_es = $data[1];
        $description_en = "";

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('hotelsdescription');
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
                echo $id . "<br/>";
                if ($id > 0) {
                    echo "ENTROU<br/>";
                    $config = new \Zend\Config\Config(include '../config/autoload/global.iterpec.php');
                    $config = [
                        'driver' => $config->db->driver,
                        'database' => $config->db->database,
                        'username' => $config->db->username,
                        'password' => $config->db->password,
                        'hostname' => $config->db->hostname
                    ];
                    $dbUpdate = new \Zend\Db\Adapter\Adapter($config);
                    $sql = new Sql($dbUpdate);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'description_es' => $description_es
                        );
                        $where['id = ?']  = $id;
                    $update = $sql->update('hotelsdescription', $data, $where);
                    if ($update == 1) {
                        echo "UPDATE<br/>";
                    } else {
                        echo "NO UPDATE<br/>";
                    }
                    $dbUpdate->getDriver()
                    ->getConnection()
                    ->disconnect(); 
                } else {
                    echo "ENTROU 2<br/>";
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hotelsdescription');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'description_pt' => $description_pt,
                        'description_es' => $description_es,
                        'description_en' => $description_en   
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                }
            } else {
                echo "ENTROU 3<br/>";
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotelsdescription');
                $insert->values(array(
                    'id' => $id,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'description_pt' => $description_pt,
                    'description_es' => $description_es,
                    'description_en' => $description_en  
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