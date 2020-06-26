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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.globalia.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "18062020_destinationsPTE.csv";
$object = fopen($filename, 'r');
$line = 0;

while ($data = fgetcsv($object, 0, "|")) {
    if ($line > 0) {
        $countryid = $data[0];
        $countryname = $data[1];
        $provinceid = $data[2];
        $provincename = $data[3];
        $id = $data[4];
        $name = $data[5];

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('destinations');
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
                echo $id . "<br/>";
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
                    if (strpos($filename, 'GER') !== false) {
                        $data = array(
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'name_ger' => $name,
                            'provinceid_ger' => $provinceid,
                            'provincename_ger' => $provincename,
                            'countryid_ger' => $countryid,
                            'countryname_ger' => $countryname 
                            );
                    } elseif (strpos($filename, 'ING') !== false) {
                        $data = array(
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'name_en' => $name,
                            'provinceid_en' => $provinceid,
                            'provincename_en' => $provincename,
                            'countryid_en' => $countryid,
                            'countryname_en' => $countryname 
                            );
                    } elseif (strpos($filename, 'ITA') !== false) {
                        $data = array(
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'name_it' => $name,
                            'provinceid_it' => $provinceid,
                            'provincename_it' => $provincename,
                            'countryid_it' => $countryid,
                            'countryname_it' => $countryname 
                            );
                    } elseif (strpos($filename, 'PTE') !== false) {
                        $data = array(
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'name_pt' => $name,
                            'provinceid_pt' => $provinceid,
                            'provincename_pt' => $provincename,
                            'countryid_pt' => $countryid,
                            'countryname_pt' => $countryname 
                            );
                    }
                    
                    $sql    = new Sql($dbUpdate);
                    $update = $sql->update();
                    $update->table('destinations');
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
                    $insert->into('destinations');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name_es' => $name,
                        'name_fr' => "",
                        'name_ger' => "",
                        'name_en' => "",
                        'name_it' => "",
                        'name_pt' => "",
                        'provinceid_es' => $provinceid,
                        'provinceid_fr' => "",
                        'provinceid_ger' => "",
                        'provinceid_en' => "",
                        'provinceid_it' => "",
                        'provinceid_pt' => "",
                        'provincename_es' => $provincename,
                        'provincename_fr' => "",
                        'provincename_ger' => "",
                        'provincename_en' => "",
                        'provincename_it' => "",
                        'provincename_pt' => "",
                        'countryid_es' => $countryid,
                        'countryid_fr' => "",
                        'countryid_ger' => "",
                        'countryid_en' => "",
                        'countryid_it' => "",
                        'countryid_pt' => "",
                        'countryname_es' => $countryname,
                        'countryname_fr' => "",
                        'countryname_ger' => "",
                        'countryname_en' => "",
                        'countryname_it' => "",
                        'countryname_pt' => ""  
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
                $insert->into('destinations');
                $insert->values(array(
                    'id' => $id,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'name_es' => $name,
                    'name_fr' => "",
                    'name_ger' => "",
                    'name_en' => "",
                    'name_it' => "",
                    'name_pt' => "",
                    'provinceid_es' => $provinceid,
                    'provinceid_fr' => "",
                    'provinceid_ger' => "",
                    'provinceid_en' => "",
                    'provinceid_it' => "",
                    'provinceid_pt' => "",
                    'provincename_es' => $provincename,
                    'provincename_fr' => "",
                    'provincename_ger' => "",
                    'provincename_en' => "",
                    'provincename_it' => "",
                    'provincename_pt' => "",
                    'countryid_es' => $countryid,
                    'countryid_fr' => "",
                    'countryid_ger' => "",
                    'countryid_en' => "",
                    'countryid_it' => "",
                    'countryid_pt' => "",
                    'countryname_es' => $countryname,
                    'countryname_fr' => "",
                    'countryname_ger' => "",
                    'countryname_en' => "",
                    'countryname_it' => "",
                    'countryname_pt' => ""    
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