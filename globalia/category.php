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
            $id = $data[0];
            $descriptionid = $data[1];

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('categories');
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

                        if (strpos($filename, 'GER') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'descriptionid_ger' => $descriptionid
                                );
                        } elseif (strpos($filename, 'ING') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'descriptionid_en' => $descriptionid
                                );
                        } elseif (strpos($filename, 'ITA') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'descriptionid_it' => $descriptionid
                                );
                        } elseif (strpos($filename, 'PTE') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'descriptionid_pt' => $descriptionid
                                );
                        } elseif (strpos($filename, 'ESP') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'descriptionid_es' => $descriptionid
                                );
                        } elseif (strpos($filename, 'FRA') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'descriptionid_fr' => $descriptionid
                                );
                        }
      
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('categories');
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
                        $insert->into('categories');
                        $insert->values(array(
                            'id' => $id,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'descriptionid_pt' => $descriptionid, 
                            'descriptionid_es' => "", 
                            'descriptionid_en' => "", 
                            'descriptionid_it' => "", 
                            'descriptionid_fr' => "", 
                            'descriptionid_ger' => ""
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
                    $insert->into('categories');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'descriptionid_pt' => $descriptionid, 
                        'descriptionid_es' => "", 
                        'descriptionid_en' => "", 
                        'descriptionid_it' => "", 
                        'descriptionid_fr' => "", 
                        'descriptionid_ger' => ""  
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

readCSV("aux/18062020_catPTE.csv");
readCSV("aux/18062020_catESP.csv");
readCSV("aux/18062020_catING.csv");
readCSV("aux/18062020_catFRA.csv");
readCSV("aux/18062020_catITA.csv");
readCSV("aux/18062020_catGER.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>