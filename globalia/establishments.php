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
            $hoteltype = $data[0];
            $hoteldesc = $data[1];
            echo $hoteltype . "<br/>";
            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('establishments');
                $select->where(array(
                    'hoteltype' => $hoteltype
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $hoteltype = (string)$data['hoteltype'];
                    if ($hoteltype != "") {
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
                                'hoteldesc_ger' => $hoteldesc
                                );
                        } elseif (strpos($filename, 'ING') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'hoteldesc_en' => $hoteldesc
                                );
                        } elseif (strpos($filename, 'ITA') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'hoteldesc_it' => $hoteldesc
                                );
                        } elseif (strpos($filename, 'PTE') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'hoteldesc_pt' => $hoteldesc
                                );
                        } elseif (strpos($filename, 'ESP') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'hoteldesc_es' => $hoteldesc
                                );
                        } elseif (strpos($filename, 'FRA') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'hoteldesc_fr' => $hoteldesc
                                );
                        }
      
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('establishments');
                        $update->set($data);
                        $update->where(array('hoteltype' => $hoteltype));

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
                            'hoteltype' => $hoteltype,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'hoteldesc_pt' => $hoteldesc, 
                            'hoteldesc_es' => "", 
                            'hoteldesc_en' => "", 
                            'hoteldesc_it' => "", 
                            'hoteldesc_fr' => "", 
                            'hoteldesc_ger' => ""
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
                        'hoteltype' => $hoteltype,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'hoteldesc_pt' => $hoteldesc, 
                        'hoteldesc_es' => "", 
                        'hoteldesc_en' => "", 
                        'hoteldesc_it' => "", 
                        'hoteldesc_fr' => "", 
                        'hoteldesc_ger' => ""  
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

readCSV("aux/18062020_estPTE.csv");
readCSV("aux/18062020_estESP.csv");
readCSV("aux/18062020_estING.csv");
readCSV("aux/18062020_estFRA.csv");
readCSV("aux/18062020_estITA.csv");
readCSV("aux/18062020_estGER.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>