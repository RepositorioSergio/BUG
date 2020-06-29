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
            $hotelcode = $data[0];
            $attributecode = $data[1];
            $attributedescription = $data[2];
            $attributeservice = $data[3];
            $attributedetailcode = $data[4];
            $attributetaildescription = $data[5];
            $attributefree = $data[6];

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('attributes');
                $select->where(array(
                    'attributedetailcode' => $attributedetailcode,
                    'hotelcode' => $hotelcode
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $attributedetailcode = (string)$data['attributedetailcode'];
                    if ($attributedetailcode != "") {
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
                                'attributetaildescription_ger' => $attributetaildescription,
                                'attributedescription_ger' => $attributedescription,
                                'attributeservice_ger' => $attributeservice,
                                'attributefree_ger' => $attributefree
                                );
                        } elseif (strpos($filename, 'ING') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'attributetaildescription_en' => $attributetaildescription,
                                'attributedescription_en' => $attributedescription,
                                'attributeservice_en' => $attributeservice,
                                'attributefree_en' => $attributefree  
                                );
                        } elseif (strpos($filename, 'ITA') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'attributetaildescription_it' => $attributetaildescription,
                                'attributedescription_it' => $attributedescription,
                                'attributeservice_it' => $attributeservice,
                                'attributefree_it' => $attributefree 
                                );
                        } elseif (strpos($filename, 'PTE') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'attributetaildescription_pt' => $attributetaildescription,
                                'attributedescription_pt' => $attributedescription,
                                'attributeservice_pt' => $attributeservice,
                                'attributefree_pt' => $attributefree 
                                );
                        } elseif (strpos($filename, 'ESP') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'attributetaildescription_es' => $attributetaildescription,
                                'attributedescription_es' => $attributedescription,
                                'attributeservice_es' => $attributeservice,
                                'attributefree_es' => $attributefree 
                                );
                        } elseif (strpos($filename, 'FRA') !== false) {
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'attributetaildescription_fr' => $attributetaildescription,
                                'attributedescription_fr' => $attributedescription,
                                'attributeservice_fr' => $attributeservice,
                                'attributefree_fr' => $attributefree
                                );
                        }
                        
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('attributes');
                        $update->set($data);
                        $update->where(array(
                            'hotelcode' => $hotelcode,
                            'attributedetailcode' => $attributedetailcode
                        ));

                        $statement = $sql->prepareStatementForSqlObject($update);
                        $results = $statement->execute();
                        $dbUpdate->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('attributes');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'attributedetailcode' => $attributedetailcode,
                            'attributetaildescription_es' => $attributetaildescription,
                            'attributetaildescription_ger' => "",
                            'attributetaildescription_fr' => "",
                            'attributetaildescription_en' => "",
                            'attributetaildescription_pt' => "",
                            'hotelcode' => $hotelcode,
                            'attributecode' => $attributecode,
                            'attributedescription_es' => $attributedescription,
                            'attributedescription_ger' => "",
                            'attributedescription_fr' => "",
                            'attributedescription_en' => "",
                            'attributedescription_pt' => "",
                            'attributeservice_es' => $attributeservice,
                            'attributeservice_ger' => "",
                            'attributeservice_fr' => "",
                            'attributeservice_en' => "",
                            'attributeservice_pt' => "",
                            'attributefree_es' => $attributefree,
                            'attributefree_ger' => "",
                            'attributefree_fr' => "",
                            'attributefree_en' => "",
                            'attributefree_pt' => "" 
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
                    $insert->into('attributes');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'attributedetailcode' => $attributedetailcode,
                        'attributetaildescription_es' => $attributetaildescription,
                        'attributetaildescription_ger' => "",
                        'attributetaildescription_fr' => "",
                        'attributetaildescription_en' => "",
                        'attributetaildescription_pt' => "",
                        'hotelcode' => $hotelcode,
                        'attributecode' => $attributecode,
                        'attributedescription_es' => $attributedescription,
                        'attributedescription_ger' => "",
                        'attributedescription_fr' => "",
                        'attributedescription_en' => "",
                        'attributedescription_pt' => "",
                        'attributeservice_es' => $attributeservice,
                        'attributeservice_ger' => "",
                        'attributeservice_fr' => "",
                        'attributeservice_en' => "",
                        'attributeservice_pt' => "",
                        'attributefree_es' => $attributefree,
                        'attributefree_ger' => "",
                        'attributefree_fr' => "",
                        'attributefree_en' => "",
                        'attributefree_pt' => ""   
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

readCSV("aux/18062020_atrESP.csv");
readCSV("aux/08082019_atrPTE.csv");
readCSV("aux/18062020_atrFRA.csv");
readCSV("aux/18062020_atrGER.csv");
readCSV("aux/18062020_atrING.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>