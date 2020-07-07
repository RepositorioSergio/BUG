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
            $cityid = $data[0];
            $city = $data[1];
            $countryid = $data[2];
            $country = $data[3];
            $isocode2 = $data[4];
            $target_city = "";
            $target_city_id = 0;
            $mapped = 0;

            $city = str_replace('"', '', $city);
            $countryid = str_replace('"', '', $countryid);
            $country = str_replace('"', '', $country);
            $isocode2 = str_replace('"', '', $isocode2);
            // $target_city = str_replace('"', '', $target_city);
            // $target_city_id = str_replace('"', '', $target_city_id);
            // $mapped = str_replace('"', '', $mapped);

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('goglobal_destinations');
                $select->where(array(
                    'id' => $id,
                    'cityid' => $cityid,
                    'countryid' => $countryid
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
                            'cityid' => $cityid, 
                            'city' => $city, 
                            'countryid' => $countryid, 
                            'country' => $country, 
                            'isocode2' => $isocode2
                            );
      
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('goglobal_destinations');
                        $update->set($data);
                        $update->where(array(
                            'id' => $id,
                            'cityid' => $cityid,
                            'countryid' => $countryid
                        ));

                        $statement = $sql->prepareStatementForSqlObject($update);
                        $results = $statement->execute();
                        $dbUpdate->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('goglobal_destinations');
                        $insert->values(array(
                            'cityid' => $cityid, 
                            'city' => $city, 
                            'countryid' => $countryid, 
                            'country' => $country, 
                            'isocode2' => $isocode2, 
                            'target_city' => $target_city,
                            'target_city_id' => $target_city_id, 
                            'mapped' => $mapped
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
                    $insert->into('goglobal_destinations');
                    $insert->values(array(
                        'cityid' => $cityid, 
                        'city' => $city, 
                        'countryid' => $countryid, 
                        'country' => $country, 
                        'isocode2' => $isocode2, 
                        'target_city' => $target_city,
                        'target_city_id' => $target_city_id, 
                        'mapped' => $mapped
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

readCSV("Destinations.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>